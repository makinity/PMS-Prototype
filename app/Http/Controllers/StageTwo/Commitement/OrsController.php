<?php

namespace App\Http\Controllers\StageTwo\Commitement;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\IpcrItem;
use App\Models\Mpor;
use App\Models\OrsEntry;
use App\Models\OrsEntryEvidence;
use App\Models\PerformancePeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            abort(403, 'Unauthorized.');
        }

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd = Carbon::now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $orsStats = [
            'thisWeek' => 0,
            'drafts' => 0,
            'submitted' => 0,
            'validated' => 0,
        ];
        if (Schema::hasTable('ors_entries')) {
            $statsBaseQuery = OrsEntry::query();
            if (Schema::hasColumn('ors_entries', 'employee_id')) {
                $statsBaseQuery->where('employee_id', $user->id);
            } elseif (Schema::hasColumn('ors_entries', 'user_id')) {
                $statsBaseQuery->where('user_id', $user->id);
            } else {
                $statsBaseQuery->whereRaw('1 = 0');
            }

            if ($activePeriod && Schema::hasColumn('ors_entries', 'performance_period_id')) {
                $statsBaseQuery->where('performance_period_id', $activePeriod->id);
            }

            if (Schema::hasColumn('ors_entries', 'work_date')) {
                $orsStats['thisWeek'] = (clone $statsBaseQuery)
                    ->whereBetween('work_date', [$weekStart, $weekEnd])
                    ->count();
            }

            if (Schema::hasColumn('ors_entries', 'status')) {
                $orsStats['drafts'] = (clone $statsBaseQuery)
                    ->where('status', 'draft')
                    ->count();
                $orsStats['submitted'] = (clone $statsBaseQuery)
                    ->where('status', 'submitted')
                    ->count();
                $orsStats['validated'] = (clone $statsBaseQuery)
                    ->whereIn('status', ['validated', 'locked', 'rated'])
                    ->count();
            }
        }

        $committedStatus = defined(Ipcr::class . '::STATUS_COMMITTED')
            ? Ipcr::STATUS_COMMITTED
            : 'committed';

        $ipcr = null;
        if ($activePeriod) {
            $ipcr = Ipcr::query()
                ->with(['ipcrItems', 'office', 'performancePeriod', 'employee'])
                ->where('employee_id', $user->id)
                ->where('status', $committedStatus)
                ->where('performance_period_id', $activePeriod->id)
                ->orderByDesc('id')
                ->first();
        }

        $orsGate = [
            'blocked' => false,
            'reason' => null,
            'ipcr_status' => $ipcr?->status,
            'ipcr_id' => $ipcr?->id,
        ];

        $mporMonthLocks = $this->resolveLockedMporMonthsForEmployee(
            (int) $user->id,
            $activePeriod
        );
        $currentMporLock = $this->resolveMporMonthLock($mporMonthLocks, now());

        if (!$activePeriod) {
            $orsGate['blocked'] = true;
            $orsGate['reason'] = 'No active performance period found.';
        } elseif (!$ipcr) {
            $orsGate['blocked'] = true;
            $orsGate['reason'] = 'IPCR not committed for active period.';
        }

        $supervisorOfficeId = $ipcr?->office_id ?: $user->office_id;
        $supervisors = [];
        if (Schema::hasTable('users')) {
            $supervisorsQuery = User::query()
                ->select(['id', 'name', 'office_id'])
                ->with('office:id,name')
                ->where('role', 'supervisor');

            if (Schema::hasColumn('users', 'is_active')) {
                $supervisorsQuery->where('is_active', true);
            }

            $supervisors = $supervisorsQuery
                ->orderBy('name')
                ->get()
                ->map(static fn (User $supervisor): array => [
                    'id' => (int) $supervisor->id,
                    'name' => (string) $supervisor->name . ($supervisor->office ? ' - ' . $supervisor->office->name : ''),
                ])
                ->values()
                ->all();
        }

        $orsOptions = [];
        if ($ipcr) {
            $grouped = [];

            foreach ($ipcr->ipcrItems as $item) {
                $outputTitle = trim((string) ($item->output_title ?? ''));
                $outputTitle = $outputTitle !== '' ? $outputTitle : 'Untitled Output';
                $groupKey = mb_strtolower($outputTitle);

                if (!isset($grouped[$groupKey])) {
                    $grouped[$groupKey] = [
                        'output_key' => 'mfo_' . substr(md5($groupKey), 0, 12),
                        'output_title' => $outputTitle,
                        'indicators' => [],
                    ];
                }

                $grouped[$groupKey]['indicators'][] = [
                    'ipcr_item_id' => $item->id,
                    'indicator_text' => (string) ($item->indicator_text ?? ''),
                    'target_summary' => (string) ($item->target_summary ?? ''),
                    'standards_payload' => is_array($item->standards_payload) ? $item->standards_payload : null,
                ];
            }

            $orsOptions = array_values($grouped);

            usort($orsOptions, function (array $a, array $b): int {
                return strcasecmp((string) ($a['output_title'] ?? ''), (string) ($b['output_title'] ?? ''));
            });

            foreach ($orsOptions as &$option) {
                usort($option['indicators'], function (array $a, array $b): int {
                    return strcasecmp((string) ($a['indicator_text'] ?? ''), (string) ($b['indicator_text'] ?? ''));
                });
            }
            unset($option);
        }

        $orsEntries = [];
        if (!$orsGate['blocked'] && Schema::hasTable('ors_entries')) {
            $orsTable = 'ors_entries';
            $hasEmployeeId = Schema::hasColumn($orsTable, 'employee_id');
            $hasUserId = Schema::hasColumn($orsTable, 'user_id');
            $hasPerformancePeriodId = Schema::hasColumn($orsTable, 'performance_period_id');
            $hasIpcrId = Schema::hasColumn($orsTable, 'ipcr_id');
            $hasIpcrItemId = Schema::hasColumn($orsTable, 'ipcr_item_id');
            $hasWorkDate = Schema::hasColumn($orsTable, 'work_date');
            $hasStatus = Schema::hasColumn($orsTable, 'status');
            $hasNotes = Schema::hasColumn($orsTable, 'notes');
            $hasQuantity = Schema::hasColumn($orsTable, 'quantity');
            $hasStartedAt = Schema::hasColumn($orsTable, 'started_at');
            $hasStoppedAt = Schema::hasColumn($orsTable, 'stopped_at');
            $hasSubmittedAt = Schema::hasColumn($orsTable, 'submitted_at');
            $hasTotalSeconds = Schema::hasColumn($orsTable, 'total_seconds');
            $hasSupervisorId = Schema::hasColumn($orsTable, 'supervisor_id');
            $hasUsersTable = Schema::hasTable('users');

            $entries = collect();

            if (class_exists(OrsEntry::class)) {
                $query = OrsEntry::query();

                $with = [];
                if ($hasIpcrItemId && Schema::hasTable('ipcr_items')) {
                    $with[] = 'ipcrItem:id,output_title,indicator_text';
                }
                if ($hasSupervisorId && $hasUsersTable && method_exists(OrsEntry::class, 'supervisor')) {
                    $with[] = 'supervisor:id,name';
                }
                if (!empty($with)) {
                    $query->with($with);
                }

                if ($hasEmployeeId) {
                    $query->where('employee_id', $user->id);
                } elseif ($hasUserId) {
                    $query->where('user_id', $user->id);
                } else {
                    $query->whereRaw('1 = 0');
                }

                if ($activePeriod && $hasPerformancePeriodId) {
                    $query->where('performance_period_id', $activePeriod->id);
                } elseif ($ipcr && $hasIpcrId) {
                    $query->where('ipcr_id', $ipcr->id);
                }

                if ($hasWorkDate) {
                    $query->orderByDesc('work_date');
                }
                $query->orderByDesc('id');

                $entries = $query->get();
            } else {
                $query = DB::table('ors_entries as oe');

                if ($hasIpcrItemId && Schema::hasTable('ipcr_items')) {
                    $query->leftJoin('ipcr_items as ii', 'ii.id', '=', 'oe.ipcr_item_id');
                }
                if ($hasSupervisorId && $hasUsersTable) {
                    $query->leftJoin('users as su', 'su.id', '=', 'oe.supervisor_id');
                }

                if ($hasEmployeeId) {
                    $query->where('oe.employee_id', $user->id);
                } elseif ($hasUserId) {
                    $query->where('oe.user_id', $user->id);
                } else {
                    $query->whereRaw('1 = 0');
                }

                if ($activePeriod && $hasPerformancePeriodId) {
                    $query->where('oe.performance_period_id', $activePeriod->id);
                } elseif ($ipcr && $hasIpcrId) {
                    $query->where('oe.ipcr_id', $ipcr->id);
                }

                $query->select('oe.*');
                if ($hasIpcrItemId && Schema::hasTable('ipcr_items')) {
                    $query->addSelect([
                        'ii.output_title as joined_output_title',
                        'ii.indicator_text as joined_indicator_text',
                    ]);
                }
                if ($hasSupervisorId && $hasUsersTable) {
                    $query->addSelect([
                        'su.name as joined_supervisor_name',
                    ]);
                }

                if ($hasWorkDate) {
                    $query->orderByDesc('oe.work_date');
                }
                $query->orderByDesc('oe.id');

                $entries = $query->get();
            }

            $evidenceCounts = collect();
            if (Schema::hasTable('ors_entry_evidences') && Schema::hasColumn('ors_entry_evidences', 'ors_entry_id')) {
                $entryIds = $entries->pluck('id')->filter()->values();
                if ($entryIds->isNotEmpty()) {
                    $evidenceCounts = DB::table('ors_entry_evidences')
                        ->whereIn('ors_entry_id', $entryIds)
                        ->select('ors_entry_id', DB::raw('COUNT(*) as aggregate'))
                        ->groupBy('ors_entry_id')
                        ->pluck('aggregate', 'ors_entry_id');
                }
            }

            $orsEntries = $entries->map(function ($entry) use (
                $hasWorkDate,
                $hasStatus,
                $hasNotes,
                $hasQuantity,
                $hasStartedAt,
                $hasStoppedAt,
                $hasSubmittedAt,
                $hasTotalSeconds,
                $hasSupervisorId,
                $evidenceCounts,
                $mporMonthLocks
            ) {
                $workDateRaw = $hasWorkDate ? data_get($entry, 'work_date') : null;
                $workDate = null;
                if ($workDateRaw instanceof \DateTimeInterface) {
                    $workDate = $workDateRaw->format('Y-m-d');
                } elseif (!is_null($workDateRaw) && (string) $workDateRaw !== '') {
                    $workDate = substr((string) $workDateRaw, 0, 10);
                }

                $indicatorText = (string) (
                    data_get($entry, 'ipcrItem.indicator_text')
                    ?? data_get($entry, 'joined_indicator_text')
                    ?? ''
                );

                $outputTitle = (string) (
                    data_get($entry, 'ipcrItem.output_title')
                    ?? data_get($entry, 'joined_output_title')
                    ?? ''
                );

                $entryId = (int) (data_get($entry, 'id') ?? 0);
                $supervisorIdRaw = $hasSupervisorId ? data_get($entry, 'supervisor_id') : null;
                $supervisorName = $hasSupervisorId
                    ? (string) (
                        data_get($entry, 'supervisor.name')
                        ?? data_get($entry, 'joined_supervisor_name')
                        ?? ''
                    )
                    : '';

                return [
                    'id' => $entryId,
                    'title' => $indicatorText !== '' ? $indicatorText : 'Untitled Activity',
                    'date' => $workDate,
                    'state' => $hasStatus ? (string) (data_get($entry, 'status') ?? 'draft') : 'draft',
                    'uwpOutputLabel' => $outputTitle !== '' ? $outputTitle : 'Untitled Output',
                    'notes' => $hasNotes ? data_get($entry, 'notes') : null,
                    'quantity' => $hasQuantity ? data_get($entry, 'quantity') : null,
                    'submittedAt' => $hasSubmittedAt ? data_get($entry, 'submitted_at') : null,
                    'startedAt' => $hasStartedAt ? data_get($entry, 'started_at') : null,
                    'stoppedAt' => $hasStoppedAt ? data_get($entry, 'stopped_at') : null,
                    'totalSeconds' => $hasTotalSeconds ? (int) (data_get($entry, 'total_seconds') ?? 0) : 0,
                    'durationSeconds' => $hasTotalSeconds ? (int) (data_get($entry, 'total_seconds') ?? 0) : 0,
                    'evidenceCount' => (int) ($evidenceCounts[$entryId] ?? 0),
                    'evidenceAttached' => (int) ($evidenceCounts[$entryId] ?? 0) > 0,
                    'supervisorId' => is_numeric($supervisorIdRaw) ? (int) $supervisorIdRaw : null,
                    'supervisorName' => $supervisorName !== '' ? $supervisorName : null,
                    'monthLocked' => !is_null($workDate) && !is_null($this->resolveMporMonthLock($mporMonthLocks, $workDate)),
                    'mporLockReason' => $workDate ? ($this->resolveMporMonthLock($mporMonthLocks, $workDate)['reason'] ?? null) : null,
                ];
            })->values()->all();
        }

        $officeName = $ipcr?->office?->name ?? '';
        $periodName = $ipcr?->performancePeriod?->name ?? ($activePeriod?->name ?? '');

        return view('employee.ors', [
            'activePeriod' => $activePeriod,
            'ipcr' => $ipcr,
            'orsGate' => $orsGate,
            'orsStats' => $orsStats,
            'orsOptions' => $orsOptions,
            'orsEntries' => $orsEntries,
            'supervisors' => $supervisors,
            'employeeName' => $user->name ?? '',
            'officeName' => $officeName,
            'periodName' => $periodName,
            'mporMonthLocks' => $mporMonthLocks,
            'currentMporLock' => $currentMporLock,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'work_date' => ['required', 'date'],
            'ipcr_item_id' => ['required', 'integer', 'exists:ipcr_items,id'],
            'supervisor_id' => ['required', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $committedStatus = defined(Ipcr::class . '::STATUS_COMMITTED')
            ? Ipcr::STATUS_COMMITTED
            : 'committed';

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $committedIpcrQuery = Ipcr::query()
            ->with('performancePeriod')
            ->where('employee_id', $user->id)
            ->where('status', $committedStatus)
            ->orderByDesc('id');

        if ($activePeriod) {
            $committedIpcrQuery->where('performance_period_id', $activePeriod->id);
        }

        $ipcr = $committedIpcrQuery->first();

        if (!$ipcr && !$activePeriod) {
            $ipcr = Ipcr::query()
                ->with('performancePeriod')
                ->where('employee_id', $user->id)
                ->where('status', $committedStatus)
                ->orderByDesc('id')
                ->first();
        }

        if (!$ipcr) {
            throw ValidationException::withMessages([
                'ipcr' => ['No committed IPCR found for the required period.'],
            ]);
        }

        if ((int) $ipcr->employee_id !== (int) $user->id || strtolower((string) $ipcr->status) !== strtolower((string) $committedStatus)) {
            throw ValidationException::withMessages([
                'ipcr' => ['You are not allowed to log tasks for this IPCR.'],
            ]);
        }

        $selectedSupervisorId = $validated['supervisor_id'] ?? null;
        $resolvedOfficeId = $ipcr->office_id ?? $user->office_id ?? null;

        if (!is_null($selectedSupervisorId)) {
            $supervisor = User::query()
                ->select(['id', 'role', 'office_id'])
                ->find($selectedSupervisorId);

            if (!$supervisor || strtolower(trim((string) $supervisor->role)) !== 'supervisor') {
                throw ValidationException::withMessages([
                    'supervisor_id' => ['Selected supervisor is invalid.'],
                ]);
            }
        }

        $ipcrItem = IpcrItem::query()
            ->whereKey($validated['ipcr_item_id'])
            ->where('ipcr_id', $ipcr->id)
            ->first();

        if (!$ipcrItem) {
            throw ValidationException::withMessages([
                'ipcr_item_id' => ['Selected task/activity does not belong to your committed IPCR.'],
            ]);
        }

        $workDate = Carbon::parse($validated['work_date'])->startOfDay();
        $this->assertMporMonthUnlocked($user, $workDate);

        $period = $ipcr->performancePeriod;
        if ($period) {
            $periodStart = !empty($period->start_date) ? Carbon::parse($period->start_date)->startOfDay() : null;
            $periodEnd = !empty($period->end_date) ? Carbon::parse($period->end_date)->endOfDay() : null;

            if ($periodStart && $workDate->lt($periodStart)) {
                throw ValidationException::withMessages([
                    'work_date' => ['Work date is before the IPCR performance period start date.'],
                ]);
            }

            if ($periodEnd && $workDate->gt($periodEnd)) {
                throw ValidationException::withMessages([
                    'work_date' => ['Work date is after the IPCR performance period end date.'],
                ]);
            }
        }

        $entryData = [
            'ipcr_id' => $ipcr->id,
            'ipcr_item_id' => $ipcrItem->id,
            'work_date' => $workDate->toDateString(),
            'notes' => $validated['notes'] ?? null,
            'status' => 'draft',
            'started_at' => null,
            'stopped_at' => null,
            'submitted_at' => null,
        ];

        if (Schema::hasColumn('ors_entries', 'employee_id')) {
            $entryData['employee_id'] = $user->id;
        } elseif (Schema::hasColumn('ors_entries', 'user_id')) {
            $entryData['user_id'] = $user->id;
        }

        if (Schema::hasColumn('ors_entries', 'office_id')) {
            $entryData['office_id'] = $ipcr->office_id;
        }

        if (Schema::hasColumn('ors_entries', 'performance_period_id')) {
            $entryData['performance_period_id'] = $ipcr->performance_period_id;
        }

        if (Schema::hasColumn('ors_entries', 'supervisor_id')) {
            $entryData['supervisor_id'] = !is_null($selectedSupervisorId) ? (int) $selectedSupervisorId : null;
        }

        if (Schema::hasColumn('ors_entries', 'duration_seconds')) {
            $entryData['duration_seconds'] = 0;
        } elseif (Schema::hasColumn('ors_entries', 'total_seconds')) {
            $entryData['total_seconds'] = 0;
        }

        if (Schema::hasColumn('ors_entries', 'locked_at')) {
            $entryData['locked_at'] = null;
        }

        $orsEntry = DB::transaction(function () use ($entryData) {
            return OrsEntry::query()->create($entryData);
        });

        if ($request->expectsJson()) {
            $freshEntry = $orsEntry->fresh() ?? $orsEntry;

            return response()->json([
                'ok' => true,
                'id' => $orsEntry->id,
                'entry' => $this->entryPayload($freshEntry),
                'message' => 'ORS task logged as draft.',
            ], 201);
        }

        Log::info('ORS STORE HIT', $request->all());

        return back()->with('success', 'ORS task logged as draft.');
    }

    public function start(Request $request, OrsEntry $orsEntry)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            return $this->jsonError('Unauthorized.', 403);
        }

        if ((int) ($orsEntry->employee_id ?? 0) !== (int) $user->id) {
            return $this->jsonError('You are not allowed to modify this ORS entry.', 403);
        }

        if ($this->isEntryLocked($orsEntry)) {
            return $this->jsonError('This ORS entry is already submitted/locked.', 422);
        }

        $lockedMonthMessage = $this->mporMonthLockMessageForEntry($user, $orsEntry);
        if ($lockedMonthMessage) {
            return $this->jsonError($lockedMonthMessage, 422);
        }

        try {
            $updated = DB::transaction(function () use ($orsEntry, $user) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('This ORS entry is already submitted/locked.');
                }

                $this->assertAuthenticatedEmployeeMporMonthUnlocked($entry);

                $status = strtolower((string) $entry->status);
                if ($status === 'recording') {
                    return $entry->fresh();
                }

                if ($status !== 'draft') {
                    throw new \RuntimeException('Only draft entries can be started.');
                }

                $hasOtherRecording = OrsEntry::query()
                    ->where('employee_id', $user->id)
                    ->where('status', 'recording')
                    ->where('id', '!=', $entry->id)
                    ->lockForUpdate()
                    ->exists();

                if ($hasOtherRecording) {
                    throw new \RuntimeException('Another ORS entry is currently recording.');
                }

                $entry->status = 'recording';
                $entry->started_at = now();
                $entry->stopped_at = null;
                $entry->save();

                return $entry->fresh();
            });
        } catch (\RuntimeException $e) {
            return $this->jsonError($e->getMessage(), 422);
        }

        return $this->jsonEntry($updated);
    }

    public function pause(Request $request, OrsEntry $orsEntry)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            return $this->jsonError('Unauthorized.', 403);
        }

        if ((int) ($orsEntry->employee_id ?? 0) !== (int) $user->id) {
            return $this->jsonError('You are not allowed to modify this ORS entry.', 403);
        }

        if ($this->isEntryLocked($orsEntry)) {
            return $this->jsonError('This ORS entry is already submitted/locked.', 422);
        }

        $lockedMonthMessage = $this->mporMonthLockMessageForEntry($user, $orsEntry);
        if ($lockedMonthMessage) {
            return $this->jsonError($lockedMonthMessage, 422);
        }

        try {
            $updated = DB::transaction(function () use ($orsEntry) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('This ORS entry is already submitted/locked.');
                }

                $this->assertAuthenticatedEmployeeMporMonthUnlocked($entry);

                if (strtolower((string) $entry->status) !== 'recording' || !$entry->started_at) {
                    throw new \RuntimeException('Only recording entries can be paused.');
                }

                $now = now();
                $elapsed = 0;
                if ($entry->started_at) {
                    $startedAt = $entry->started_at instanceof Carbon
                        ? $entry->started_at
                        : Carbon::parse($entry->started_at);
                    $elapsed = $startedAt->diffInSeconds($now);
                }

                $elapsed = max(0, (int) $elapsed);
                $total = (int) ($entry->total_seconds ?? 0);
                $total = $total + $elapsed;
                if ($total < 0) {
                    $total = 0;
                }

                $entry->total_seconds = $total;
                $entry->stopped_at = $now;
                $entry->started_at = null;
                $entry->status = 'paused';
                $entry->save();

                return $entry->fresh();
            });
        } catch (\RuntimeException $e) {
            return $this->jsonError($e->getMessage(), 422);
        }

        return $this->jsonEntry($updated);
    }

    public function resume(Request $request, OrsEntry $orsEntry)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            return $this->jsonError('Unauthorized.', 403);
        }

        if ((int) ($orsEntry->employee_id ?? 0) !== (int) $user->id) {
            return $this->jsonError('You are not allowed to modify this ORS entry.', 403);
        }

        if ($this->isEntryLocked($orsEntry)) {
            return $this->jsonError('This ORS entry is already submitted/locked.', 422);
        }

        $lockedMonthMessage = $this->mporMonthLockMessageForEntry($user, $orsEntry);
        if ($lockedMonthMessage) {
            return $this->jsonError($lockedMonthMessage, 422);
        }

        try {
            $updated = DB::transaction(function () use ($orsEntry, $user) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('This ORS entry is already submitted/locked.');
                }

                $this->assertAuthenticatedEmployeeMporMonthUnlocked($entry);

                $status = strtolower((string) $entry->status);
                if ($status === 'recording') {
                    return $entry->fresh();
                }

                if ($status !== 'paused') {
                    throw new \RuntimeException('Only paused entries can be resumed.');
                }

                $hasOtherRecording = OrsEntry::query()
                    ->where('employee_id', $user->id)
                    ->where('status', 'recording')
                    ->where('id', '!=', $entry->id)
                    ->lockForUpdate()
                    ->exists();

                if ($hasOtherRecording) {
                    throw new \RuntimeException('Another ORS entry is currently recording.');
                }

                $entry->started_at = now();
                $entry->stopped_at = null;
                $entry->status = 'recording';
                $entry->save();

                return $entry->fresh();
            });
        } catch (\RuntimeException $e) {
            return $this->jsonError($e->getMessage(), 422);
        }

        return $this->jsonEntry($updated);
    }

    public function stop(Request $request, OrsEntry $orsEntry)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            return $this->jsonError('Unauthorized.', 403);
        }

        if ((int) ($orsEntry->employee_id ?? 0) !== (int) $user->id) {
            return $this->jsonError('You are not allowed to modify this ORS entry.', 403);
        }

        if ($this->isEntryLocked($orsEntry)) {
            return $this->jsonError('This ORS entry is already submitted/locked.', 422);
        }

        $lockedMonthMessage = $this->mporMonthLockMessageForEntry($user, $orsEntry);
        if ($lockedMonthMessage) {
            return $this->jsonError($lockedMonthMessage, 422);
        }

        try {
            $updated = DB::transaction(function () use ($orsEntry) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('This ORS entry is already submitted/locked.');
                }

                $this->assertAuthenticatedEmployeeMporMonthUnlocked($entry);

                $status = strtolower((string) $entry->status);
                if (!in_array($status, ['recording', 'paused'], true)) {
                    throw new \RuntimeException('Only recording or paused entries can be stopped.');
                }

                $now = now();
                $elapsed = 0;
                if ($status === 'recording' && $entry->started_at) {
                    $startedAt = $entry->started_at instanceof Carbon
                        ? $entry->started_at
                        : Carbon::parse($entry->started_at);
                    $elapsed = $startedAt->diffInSeconds($now);
                }

                $elapsed = max(0, (int) $elapsed);
                $total = (int) ($entry->total_seconds ?? 0);
                $total = $total + $elapsed;
                if ($total < 0) {
                    $total = 0;
                }

                $entry->total_seconds = $total;
                $entry->status = 'draft';
                $entry->started_at = null;
                $entry->stopped_at = $now;
                $entry->save();

                return $entry->fresh();
            });
        } catch (\RuntimeException $e) {
            return $this->jsonError($e->getMessage(), 422);
        }

        return $this->jsonEntry($updated);
    }

    public function submit(Request $request, OrsEntry $orsEntry)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            return $request->expectsJson()
                ? $this->jsonError('Unauthorized.', 403)
                : abort(403, 'Unauthorized.');
        }

        if ((int) ($orsEntry->employee_id ?? 0) !== (int) $user->id) {
            return $request->expectsJson()
                ? $this->jsonError('You are not allowed to modify this ORS entry.', 403)
                : abort(403, 'Unauthorized.');
        }

        if ($this->isEntryLocked($orsEntry)) {
            return $request->expectsJson()
                ? $this->jsonError('This ORS entry is already submitted/locked.', 422)
                : back()->withErrors(['entry' => 'This ORS entry is already submitted/locked.']);
        }

        $lockedMonthMessage = $this->mporMonthLockMessageForEntry($user, $orsEntry);
        if ($lockedMonthMessage) {
            return $request->expectsJson()
                ? $this->jsonError($lockedMonthMessage, 422)
                : back()->withErrors(['entry' => $lockedMonthMessage]);
        }

        if (strtolower((string) $orsEntry->status) !== 'draft') {
            return $request->expectsJson()
                ? $this->jsonError('Only draft entries can be submitted for review.', 422)
                : back()->withErrors(['entry' => 'Only draft entries can be submitted for review.']);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0'],
            'evidence' => ['nullable', 'array'],
            'evidence.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xlsx'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $result = DB::transaction(function () use ($orsEntry, $validated, $request) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('This ORS entry is already submitted/locked.');
                }
                $this->assertAuthenticatedEmployeeMporMonthUnlocked($entry);
                if (strtolower((string) $entry->status) !== 'draft') {
                    throw new \RuntimeException('Only draft entries can be submitted for review.');
                }

                $now = now();
                $files = $request->file('evidence', []);
                if (!is_array($files)) {
                    $files = $files ? [$files] : [];
                }

                $uploadedEvidences = [];
                foreach ($files as $file) {
                    if (!$file) {
                        continue;
                    }
                    $uploadedEvidences[] = $this->storeEvidenceForEntry($entry, $file, $now);
                }

                if (!$entry->evidences()->exists()) {
                    throw new \RuntimeException('Evidence is required before submitting this ORS entry.');
                }

                if ($entry->started_at) {
                    $startedAt = $entry->started_at instanceof Carbon
                        ? $entry->started_at
                        : Carbon::parse($entry->started_at);

                    $elapsedSeconds = max(0, (int) $startedAt->diffInSeconds($now));
                    $total = (int) ($entry->total_seconds ?? 0);
                    $entry->total_seconds = min(4294967295, max(0, $total + $elapsedSeconds));

                    $entry->stopped_at = $now;
                    $entry->started_at = null;
                }

                $entry->quantity = $validated['quantity'];
                if (array_key_exists('notes', $validated)) {
                    $entry->notes = $validated['notes'];
                }
                $entry->status = 'submitted';
                $entry->submitted_at = $now;
                $entry->locked_at = $now;
                $entry->save();

                $latestEvidence = OrsEntryEvidence::query()
                    ->where('ors_entry_id', $entry->id)
                    ->orderByDesc('uploaded_at')
                    ->orderByDesc('id')
                    ->first();

                return [
                    'entry' => $entry->fresh(),
                    'latestEvidence' => $latestEvidence,
                    'uploadedEvidences' => $uploadedEvidences,
                ];
            });
        } catch (\RuntimeException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 422)
                : back()->withErrors(['entry' => $e->getMessage()]);
        }

        $updated = $result['entry'];
        $latestEvidence = $result['latestEvidence'];
        $uploadedEvidences = $result['uploadedEvidences'] ?? [];
        $latestEvidencePayload = $latestEvidence
            ? [
                'file_name' => $latestEvidence->file_name,
                'file_path' => $latestEvidence->file_path,
                'uploaded_at' => $latestEvidence->uploaded_at?->toIso8601String(),
            ]
            : null;

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $updated->id,
                'status' => (string) $updated->status,
                'submitted_at' => $updated->submitted_at?->toIso8601String(),
                'locked_at' => $updated->locked_at?->toIso8601String(),
                'total_seconds' => (int) ($updated->total_seconds ?? 0),
                'evidence_count' => $updated->evidences()->count(),
                'evidence' => $latestEvidencePayload,
                'uploaded_evidences' => $uploadedEvidences,
                'entry' => $this->entryPayload($updated->fresh() ?? $updated),
            ]);
        }

        return back()->with('success', 'ORS entry submitted for review.');
    }

    public function uploadEvidence(Request $request, OrsEntry $orsEntry)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            return $request->expectsJson()
                ? $this->jsonError('Unauthorized.', 403)
                : abort(403, 'Unauthorized.');
        }

        if ((int) ($orsEntry->employee_id ?? 0) !== (int) $user->id) {
            return $request->expectsJson()
                ? $this->jsonError('You are not allowed to modify this ORS entry.', 403)
                : abort(403, 'Unauthorized.');
        }

        $lockedMonthMessage = $this->mporMonthLockMessageForEntry($user, $orsEntry);
        if ($lockedMonthMessage) {
            return $request->expectsJson()
                ? $this->jsonError($lockedMonthMessage, 422)
                : back()->withErrors(['evidence' => $lockedMonthMessage]);
        }

        $validated = $request->validate([
            'evidence' => ['required', 'array', 'min:1'],
            'evidence.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xlsx'],
        ]);

        try {
            $result = DB::transaction(function () use ($orsEntry, $validated) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('Cannot upload evidence to a submitted/locked ORS entry.');
                }

                $this->assertAuthenticatedEmployeeMporMonthUnlocked($entry);

                $now = now();
                $files = $validated['evidence'] ?? [];
                if (!is_array($files)) {
                    $files = $files ? [$files] : [];
                }

                $payloads = [];
                foreach ($files as $file) {
                    if (!$file) {
                        continue;
                    }
                    $payloads[] = $this->storeEvidenceForEntry($entry, $file, $now);
                }

                if (count($payloads) === 0) {
                    throw new \RuntimeException('Please select at least one evidence file to upload.');
                }

                return ['entry' => $entry->fresh(), 'evidences' => $payloads];
            });
        } catch (\RuntimeException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 422)
                : back()->withErrors(['evidence' => $e->getMessage()]);
        }

        if ($request->expectsJson()) {
            $uploadedEvidences = $result['evidences'] ?? [];
            $lastUploaded = !empty($uploadedEvidences) ? end($uploadedEvidences) : null;

            return response()->json([
                'ok' => true,
                'id' => $result['entry']->id,
                'status' => (string) $result['entry']->status,
                'has_evidence' => $result['entry']->evidences()->exists(),
                'evidence_count' => $result['entry']->evidences()->count(),
                'uploaded_evidences' => $uploadedEvidences,
                'evidence' => $lastUploaded,
            ]);
        }

        return back()->with('success', 'Evidence uploaded successfully.');
    }

    public function destroyEvidence(Request $request, OrsEntry $orsEntry, OrsEntryEvidence $evidence)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            return $request->expectsJson()
                ? $this->jsonError('Unauthorized.', 403)
                : abort(403, 'Unauthorized.');
        }

        if ((int) ($orsEntry->employee_id ?? 0) !== (int) $user->id) {
            return $request->expectsJson()
                ? $this->jsonError('You are not allowed to modify this ORS entry.', 403)
                : abort(403, 'Unauthorized.');
        }

        $lockedMonthMessage = $this->mporMonthLockMessageForEntry($user, $orsEntry);
        if ($lockedMonthMessage) {
            return $request->expectsJson()
                ? $this->jsonError($lockedMonthMessage, 422)
                : back()->withErrors(['evidence' => $lockedMonthMessage]);
        }

        if ((int) $evidence->ors_entry_id !== (int) $orsEntry->id) {
            return $request->expectsJson()
                ? $this->jsonError('Evidence does not belong to this ORS entry.', 422)
                : back()->withErrors(['evidence' => 'Evidence does not belong to this ORS entry.']);
        }

        try {
            $updated = DB::transaction(function () use ($orsEntry, $evidence) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);
                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('Cannot remove evidence from a submitted/locked ORS entry.');
                }

                $this->assertAuthenticatedEmployeeMporMonthUnlocked($entry);

                $evidenceRow = OrsEntryEvidence::query()->lockForUpdate()->findOrFail($evidence->id);
                if ((int) $evidenceRow->ors_entry_id !== (int) $entry->id) {
                    throw new \RuntimeException('Evidence does not belong to this ORS entry.');
                }

                if (!empty($evidenceRow->file_path)) {
                    Storage::disk('public')->delete($evidenceRow->file_path);
                }
                $evidenceRow->delete();

                return $entry->fresh();
            });
        } catch (\RuntimeException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 422)
                : back()->withErrors(['evidence' => $e->getMessage()]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $updated->id,
                'status' => (string) $updated->status,
                'has_evidence' => $updated->evidences()->exists(),
            ]);
        }

        return back()->with('success', 'Evidence removed successfully.');
    }

    private function storeEvidenceForEntry(OrsEntry $entry, $file, Carbon $now): array
    {
        $original = $file->getClientOriginalName();
        $safeBase = Str::slug(pathinfo($original, PATHINFO_FILENAME));
        if ($safeBase === '') {
            $safeBase = 'evidence';
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $finalName = $safeBase . '-' . $now->format('YmdHis') . '-' . Str::random(6) . '.' . $ext;
        $dir = 'ors_evidences/' . $entry->employee_id . '/' . $entry->id;
        $path = Storage::disk('public')->putFileAs($dir, $file, $finalName);

        if (!$path) {
            throw new \RuntimeException('Failed to upload evidence file.');
        }

        OrsEntryEvidence::query()->create([
            'ors_entry_id' => $entry->id,
            'file_name' => $original,
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_at' => $now,
        ]);

        return [
            'file_name' => $original,
            'file_path' => $path,
            'uploaded_at' => $now->toIso8601String(),
        ];
    }

    private function isEntryLocked(OrsEntry $orsEntry): bool
    {
        return in_array(strtolower((string) $orsEntry->status), ['submitted', 'rated'], true)
            || !is_null($orsEntry->locked_at);
    }

    private function resolveLockedMporMonthsForEmployee(int $employeeId, ?PerformancePeriod $activePeriod = null): array
    {
        $query = Mpor::query()
            ->select(['month', 'status'])
            ->where('employee_id', $employeeId)
            ->whereIn('status', ['submitted', 'approved', 'endorsed']);

        if ($activePeriod && !empty($activePeriod->start_date) && !empty($activePeriod->end_date)) {
            $startMonth = Carbon::parse($activePeriod->start_date)->format('Y-m');
            $endMonth = Carbon::parse($activePeriod->end_date)->format('Y-m');
            $query->whereBetween('month', [$startMonth, $endMonth]);
        }

        return $query
            ->get()
            ->mapWithKeys(function (Mpor $mpor): array {
                $monthKey = trim((string) $mpor->month);
                if ($monthKey === '') {
                    return [];
                }

                $status = strtolower(trim((string) $mpor->status));
                $monthLabel = Carbon::createFromFormat('Y-m', substr($monthKey, 0, 7))->format('F Y');

                return [
                    $monthKey => [
                        'month' => $monthKey,
                        'status' => $status,
                        'label' => $monthLabel,
                        'reason' => sprintf(
                            'ORS is locked for %s because the MPOR is already %s.',
                            $monthLabel,
                            $status
                        ),
                    ],
                ];
            })
            ->all();
    }

    private function resolveMporMonthLock(array $monthLocks, $date): ?array
    {
        if (empty($monthLocks) || empty($date)) {
            return null;
        }

        try {
            $monthKey = Carbon::parse($date)->format('Y-m');
        } catch (\Throwable $e) {
            return null;
        }

        return $monthLocks[$monthKey] ?? null;
    }

    private function assertMporMonthUnlocked(User $user, $date): void
    {
        $monthLock = $this->resolveMporMonthLock(
            $this->resolveLockedMporMonthsForEmployee((int) $user->id),
            $date
        );

        if ($monthLock) {
            throw ValidationException::withMessages([
                'work_date' => [$monthLock['reason'] ?? 'ORS is locked because the MPOR is already submitted.'],
            ]);
        }
    }

    private function assertAuthenticatedEmployeeMporMonthUnlocked(OrsEntry $orsEntry): void
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            throw new \RuntimeException('Unauthorized.');
        }

        $monthLock = $this->resolveMporMonthLock(
            $this->resolveLockedMporMonthsForEmployee((int) $user->id),
            $orsEntry->work_date
        );

        if ($monthLock) {
            throw new \RuntimeException($monthLock['reason'] ?? 'ORS is locked because the MPOR is already submitted.');
        }
    }

    private function mporMonthLockMessageForEntry(User $user, OrsEntry $orsEntry): ?string
    {
        $monthLock = $this->resolveMporMonthLock(
            $this->resolveLockedMporMonthsForEmployee((int) $user->id),
            $orsEntry->work_date
        );

        return $monthLock['reason'] ?? null;
    }

    private function jsonError(string $message, int $status = 422)
    {
        return response()->json([
            'ok' => false,
            'message' => $message,
        ], $status);
    }

    private function entryPayload(OrsEntry $entry): array
    {
        $orsTable = 'ors_entries';
        $hasOrsTable = Schema::hasTable($orsTable);
        $hasNotes = $hasOrsTable && Schema::hasColumn($orsTable, 'notes');
        $hasQuantity = $hasOrsTable && Schema::hasColumn($orsTable, 'quantity');
        $hasSubmittedAt = $hasOrsTable && Schema::hasColumn($orsTable, 'submitted_at');
        $hasStartedAt = $hasOrsTable && Schema::hasColumn($orsTable, 'started_at');
        $hasStoppedAt = $hasOrsTable && Schema::hasColumn($orsTable, 'stopped_at');
        $hasTotalSeconds = $hasOrsTable && Schema::hasColumn($orsTable, 'total_seconds');
        $hasDurationSeconds = $hasOrsTable && Schema::hasColumn($orsTable, 'duration_seconds');
        $hasIpcrItemId = $hasOrsTable && Schema::hasColumn($orsTable, 'ipcr_item_id');
        $hasIpcrItemsTable = Schema::hasTable('ipcr_items');

        if ($hasIpcrItemsTable && $hasIpcrItemId) {
            $entry->loadMissing(['ipcrItem:id,output_title,indicator_text']);
        }

        $workDateRaw = data_get($entry, 'work_date');
        $workDate = null;
        if ($workDateRaw instanceof \DateTimeInterface) {
            $workDate = $workDateRaw->format('Y-m-d');
        } elseif (!is_null($workDateRaw) && (string) $workDateRaw !== '') {
            $workDate = substr((string) $workDateRaw, 0, 10);
        }

        $indicatorText = trim((string) (data_get($entry, 'ipcrItem.indicator_text') ?? ''));
        $outputTitle = trim((string) (data_get($entry, 'ipcrItem.output_title') ?? ''));

        $submittedAt = $hasSubmittedAt ? data_get($entry, 'submitted_at') : null;
        $startedAt = $hasStartedAt ? data_get($entry, 'started_at') : null;
        $stoppedAt = $hasStoppedAt ? data_get($entry, 'stopped_at') : null;

        $totalSeconds = 0;
        if ($hasTotalSeconds) {
            $totalSeconds = (int) (data_get($entry, 'total_seconds') ?? 0);
        } elseif ($hasDurationSeconds) {
            $totalSeconds = (int) (data_get($entry, 'duration_seconds') ?? 0);
        }

        $evidenceCount = 0;
        if (Schema::hasTable('ors_entry_evidences') && Schema::hasColumn('ors_entry_evidences', 'ors_entry_id') && method_exists($entry, 'evidences')) {
            if (array_key_exists('evidences_count', $entry->getAttributes())) {
                $evidenceCount = (int) ($entry->getAttribute('evidences_count') ?? 0);
            } else {
                $evidenceCount = (int) $entry->evidences()->count();
            }
        }

        return [
            'id' => (int) (data_get($entry, 'id') ?? 0),
            'title' => $indicatorText !== '' ? $indicatorText : 'Untitled Activity',
            'date' => $workDate,
            'state' => (string) (data_get($entry, 'status') ?? 'draft'),
            'uwpOutputLabel' => $outputTitle !== '' ? $outputTitle : 'Untitled Output',
            'notes' => $hasNotes ? data_get($entry, 'notes') : null,
            'quantity' => $hasQuantity ? data_get($entry, 'quantity') : null,
            'submittedAt' => $submittedAt instanceof \DateTimeInterface ? $submittedAt->toIso8601String() : (!empty($submittedAt) ? Carbon::parse($submittedAt)->toIso8601String() : null),
            'startedAt' => $startedAt instanceof \DateTimeInterface ? $startedAt->toIso8601String() : (!empty($startedAt) ? Carbon::parse($startedAt)->toIso8601String() : null),
            'stoppedAt' => $stoppedAt instanceof \DateTimeInterface ? $stoppedAt->toIso8601String() : (!empty($stoppedAt) ? Carbon::parse($stoppedAt)->toIso8601String() : null),
            'totalSeconds' => $totalSeconds,
            'durationSeconds' => $totalSeconds,
            'evidenceCount' => $evidenceCount,
            'evidenceAttached' => $evidenceCount > 0,
            'monthLocked' => !is_null($this->resolveMporMonthLock(
                $this->resolveLockedMporMonthsForEmployee((int) ($entry->employee_id ?? 0)),
                $workDate
            )),
            'mporLockReason' => $this->resolveMporMonthLock(
                $this->resolveLockedMporMonthsForEmployee((int) ($entry->employee_id ?? 0)),
                $workDate
            )['reason'] ?? null,
        ];
    }

    private function jsonEntry(OrsEntry $orsEntry)
    {
        return response()->json([
            'ok' => true,
            'entry' => $this->entryPayload($orsEntry->fresh() ?? $orsEntry),
        ]);
    }
}
