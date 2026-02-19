<?php

namespace App\Http\Controllers\StageTwo\Commitement;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\IpcrItem;
use App\Models\OrsEntry;
use App\Models\OrsEntryEvidence;
use App\Models\PerformancePeriod;
use App\Services\MyTaskSyncService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

        if (!$activePeriod) {
            $orsGate['blocked'] = true;
            $orsGate['reason'] = 'No active performance period found.';
        } elseif (!$ipcr) {
            $orsGate['blocked'] = true;
            $orsGate['reason'] = 'IPCR not committed for active period.';
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
            $hasOutputType = Schema::hasColumn($orsTable, 'output_type');
            $hasNotes = Schema::hasColumn($orsTable, 'notes');
            $hasQuantity = Schema::hasColumn($orsTable, 'quantity');
            $hasSubmittedAt = Schema::hasColumn($orsTable, 'submitted_at');
            $hasTotalSeconds = Schema::hasColumn($orsTable, 'total_seconds');
            $hasClientRequestId = Schema::hasColumn($orsTable, 'client_request_id');
            $hasRequestId = Schema::hasColumn($orsTable, 'request_id');

            $entries = collect();

            if (class_exists(OrsEntry::class)) {
                $query = OrsEntry::query();

                if ($hasIpcrItemId && Schema::hasTable('ipcr_items')) {
                    $query->with(['ipcrItem:id,output_title,indicator_text']);
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
                $hasRequestId,
                $hasClientRequestId,
                $hasOutputType,
                $hasNotes,
                $hasQuantity,
                $hasSubmittedAt,
                $hasTotalSeconds,
                $evidenceCounts
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
                $requestId = $hasRequestId
                    ? data_get($entry, 'request_id')
                    : ($hasClientRequestId ? data_get($entry, 'client_request_id') : null);

                return [
                    'id' => $entryId,
                    'title' => $indicatorText !== '' ? $indicatorText : 'Untitled Activity',
                    'date' => $workDate,
                    'state' => $hasStatus ? (string) (data_get($entry, 'status') ?? 'draft') : 'draft',
                    'uwpOutputLabel' => $outputTitle !== '' ? $outputTitle : 'Untitled Output',
                    'requestId' => $requestId,
                    'output' => $hasOutputType ? data_get($entry, 'output_type') : null,
                    'notes' => $hasNotes ? data_get($entry, 'notes') : null,
                    'quantity' => $hasQuantity ? data_get($entry, 'quantity') : null,
                    'submittedAt' => $hasSubmittedAt ? data_get($entry, 'submitted_at') : null,
                    'durationSeconds' => $hasTotalSeconds ? (int) (data_get($entry, 'total_seconds') ?? 0) : 0,
                    'evidenceAttached' => (int) ($evidenceCounts[$entryId] ?? 0) > 0,
                ];
            })->values()->all();
        }

        $officeName = $ipcr?->office?->name ?? '';
        $periodName = $ipcr?->performancePeriod?->name ?? ($activePeriod?->name ?? '');

        return view('employee.ors', [
            'activePeriod' => $activePeriod,
            'ipcr' => $ipcr,
            'orsGate' => $orsGate,
            'orsOptions' => $orsOptions,
            'orsEntries' => $orsEntries,
            'employeeName' => $user->name ?? '',
            'officeName' => $officeName,
            'periodName' => $periodName,
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
            'client_request_id' => ['nullable', 'string', 'max:100'],
            'output_type' => [
                'required',
                'string',
                Rule::in(['bsf_01', 'official_receipt', 'scanned_doc', 'records_checklist']),
            ],
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
            'output_type' => $validated['output_type'],
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

        if (Schema::hasColumn('ors_entries', 'client_request_id')) {
            $entryData['client_request_id'] = $validated['client_request_id'] ?? null;
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

        $this->syncMyTask($orsEntry);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $orsEntry->id,
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

        try {
            $updated = DB::transaction(function () use ($orsEntry, $user) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('This ORS entry is already submitted/locked.');
                }

                if (strtolower((string) $entry->status) !== 'draft') {
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

        $this->syncMyTask($updated);

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

        try {
            $updated = DB::transaction(function () use ($orsEntry) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('This ORS entry is already submitted/locked.');
                }

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

        $this->syncMyTask($updated);

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

        try {
            $updated = DB::transaction(function () use ($orsEntry, $user) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('This ORS entry is already submitted/locked.');
                }

                if (strtolower((string) $entry->status) !== 'paused') {
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

        $this->syncMyTask($updated);

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

        try {
            $updated = DB::transaction(function () use ($orsEntry) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('This ORS entry is already submitted/locked.');
                }

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

        $this->syncMyTask($updated);

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

        $validated = $request->validate([
            'quantity' => ['required', 'string', 'max:255'],
            'evidence' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xlsx'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $result = DB::transaction(function () use ($orsEntry, $validated, $request) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('This ORS entry is already submitted/locked.');
                }

                $now = now();
                $file = $request->file('evidence');
                $evidencePayload = null;

                if ($file) {
                    $evidencePayload = $this->storeEvidenceForEntry($entry, $file, $now);
                } elseif (!$entry->evidences()->exists()) {
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

                $this->syncMyTask($entry);

                return [
                    'entry' => $entry->fresh(),
                    'evidence' => $evidencePayload,
                ];
            });
        } catch (\RuntimeException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 422)
                : back()->withErrors(['entry' => $e->getMessage()]);
        }

        $updated = $result['entry'];
        $evidence = $result['evidence'];

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $updated->id,
                'status' => (string) $updated->status,
                'submitted_at' => $updated->submitted_at?->toIso8601String(),
                'locked_at' => $updated->locked_at?->toIso8601String(),
                'total_seconds' => (int) ($updated->total_seconds ?? 0),
                'evidence' => $evidence,
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

        $validated = $request->validate([
            'evidence' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xlsx'],
        ]);

        try {
            $result = DB::transaction(function () use ($orsEntry, $validated) {
                $entry = OrsEntry::query()->lockForUpdate()->findOrFail($orsEntry->id);

                if ($this->isEntryLocked($entry)) {
                    throw new \RuntimeException('Cannot upload evidence to a submitted/locked ORS entry.');
                }

                $now = now();
                $payload = $this->storeEvidenceForEntry($entry, $validated['evidence'], $now);
                $this->syncMyTask($entry);

                return ['entry' => $entry->fresh(), 'evidence' => $payload];
            });
        } catch (\RuntimeException $e) {
            return $request->expectsJson()
                ? $this->jsonError($e->getMessage(), 422)
                : back()->withErrors(['evidence' => $e->getMessage()]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $result['entry']->id,
                'status' => (string) $result['entry']->status,
                'has_evidence' => true,
                'evidence' => $result['evidence'],
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

                $evidenceRow = OrsEntryEvidence::query()->lockForUpdate()->findOrFail($evidence->id);
                if ((int) $evidenceRow->ors_entry_id !== (int) $entry->id) {
                    throw new \RuntimeException('Evidence does not belong to this ORS entry.');
                }

                if (!empty($evidenceRow->file_path)) {
                    Storage::disk('public')->delete($evidenceRow->file_path);
                }
                $evidenceRow->delete();

                $this->syncMyTask($entry);

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
        $dir = 'ors-evidence/' . $entry->employee_id . '/' . $entry->id;
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

    private function syncMyTask(OrsEntry $entry): void
    {
        if (!Schema::hasTable('my_tasks')) {
            return;
        }

        app(MyTaskSyncService::class)->syncFromOrsEntry($entry->fresh());
    }

    private function isEntryLocked(OrsEntry $orsEntry): bool
    {
        return strtolower((string) $orsEntry->status) === 'submitted'
            || !is_null($orsEntry->locked_at);
    }

    private function jsonError(string $message, int $status = 422)
    {
        return response()->json([
            'ok' => false,
            'message' => $message,
        ], $status);
    }

    private function jsonEntry(OrsEntry $orsEntry)
    {
        return response()->json([
            'ok' => true,
            'entry' => [
                'id' => $orsEntry->id,
                'status' => (string) $orsEntry->status,
                'started_at' => $orsEntry->started_at?->toIso8601String(),
                'stopped_at' => $orsEntry->stopped_at?->toIso8601String(),
                'total_seconds' => (int) ($orsEntry->total_seconds ?? 0),
            ],
        ]);
    }
}
