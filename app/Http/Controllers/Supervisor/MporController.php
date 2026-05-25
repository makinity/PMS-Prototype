<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Mpor;
use App\Notifications\WorkflowEventNotification;
use App\Services\WorkflowNotificationDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MporController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = $request->user();
        abort_unless($supervisor && $supervisor->role === 'supervisor', 403);

        $search = trim((string) $request->query('search', ''));
        $selectedEmployeeId = (int) $request->query('employee_id', 0);
        $month = (string) $request->query('month', now()->format('Y-m'));
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        try {
            $monthCarbon = Carbon::createFromFormat('Y-m', $month);
            $monthLabel = $monthCarbon->format('F Y');
            $startDate = $monthCarbon->copy()->startOfMonth()->toDateString();
            $endDate = $monthCarbon->copy()->endOfMonth()->toDateString();
        } catch (\Throwable $e) {
            $monthLabel = $month;
        }

        $query = Mpor::query()
            ->select('mpors.*')
            ->with(['employee:id,name,office_id', 'employee.office:id,name'])
            ->selectSub(function ($subQuery) use ($startDate, $endDate) {
                $subQuery->from('ors_entries')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('ors_entries.employee_id', 'mpors.employee_id')
                    ->where('ors_entries.status', 'rated')
                    ->where('ors_entries.quantity', '>', 0)
                    ->whereBetween('ors_entries.work_date', [$startDate, $endDate])
                    ->whereExists(function ($exists) {
                        $exists->select(DB::raw(1))
                            ->from('ors_entry_monitorings')
                            ->whereColumn('ors_entry_monitorings.ors_entry_id', 'ors_entries.id')
                            ->whereNotNull('ors_entry_monitorings.quality_rating')
                            ->whereNotNull('ors_entry_monitorings.timeliness_rating');
                    });
            }, 'rated_ors_entries_count')
            ->whereNotNull('employee_id')
            ->where('month', $month)
            ->whereExists(function ($q) use ($supervisor) {
                $q->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'mpors.employee_id')
                    ->where('users.role', 'employee')
                    ->where('users.office_id', $supervisor->office_id);
            })
            // sensible scope: supervisor sees MPORs in same office
            ->whereHas('employee', function ($q) use ($supervisor) {
                $q->where('office_id', $supervisor->office_id)
                  ->where('role', 'employee');
            })
            ->whereIn('status', ['submitted', 'approved', 'endorsed'])
            ->orderByRaw("FIELD(status,'submitted','approved','endorsed')")
            ->orderByDesc('generated_at')
            ->orderByDesc('id');

        if ($selectedEmployeeId > 0) {
            $query->where('mpors.employee_id', $selectedEmployeeId);
        }

        if ($search !== '') {
            $query->whereHas('employee', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $mpors = $query->get();

        if ($request->wantsJson()) {
            return response()->json(['html' => view('supervisor._mpor-table-body', compact('mpors'))->render()]);
        }

        return view('supervisor.mpor', compact(
            'mpors',
            'selectedEmployeeId',
            'month',
            'monthLabel',
            'search'
        ));
    }

    public function show(Request $request, Mpor $mpor)
    {
        $payload = $this->buildMporPayload($request, $mpor);

        return view('supervisor.mpor-show', [
            'mpor' => $mpor,
            'meta' => $payload['meta'],
            'sectionLabels' => $payload['sectionLabels'],
            'sectionRows' => $payload['sectionRows'],
            'grandTotals' => $payload['grandTotals'],
            'kpis' => $payload['kpis'],
        ]);
    }

    public function previewJson(Request $request, Mpor $mpor)
    {
        return response()->json($this->buildMporPayload($request, $mpor));
    }

    private function buildMporPayload(Request $request, Mpor $mpor): array
    {
        $supervisor = $request->user();
        abort_unless($supervisor && $supervisor->role === 'supervisor', 403);

        $mpor->load(['employee.office']);
        abort_unless($mpor->employee, 404);

        // security: only allow MPORs within supervisor office
        abort_unless((int) ($mpor->employee?->office_id ?? 0) === (int) $supervisor->office_id, 403);

        // month parsing
        $month = (string) $mpor->month;
        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable $e) {
            abort(422, 'Invalid MPOR month.');
        }
        $end = $start->copy()->endOfMonth();

        $mporMonthYear = $start->format('F Y');
        $employeeName = $mpor->employee?->name ?? '--';
        $officeName = $mpor->employee?->office?->name ?? '--';

        $sectionLabels = [];
        $functionWeights = [];

        // Fetch rated entries INCLUDED in MPOR computation:
        // - status rated
        // - quantity > 0
        // - date range
        // - monitoring ratings exist
        // - must have ipcrItem (output_title + function_type)
        $ratedEntries = \App\Models\OrsEntry::query()
            ->with([
                'ipcrItem:id,output_title,function_type,indicator_text,uwp_function_id',
                'ipcrItem.uwpFunction:id,function_type,weight_percent',
                'monitoring:ors_entry_id,quality_rating,timeliness_rating',
            ])
            ->where('employee_id', $mpor->employee_id)
            ->where('status', 'rated')
            ->where('quantity', '>', 0)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('ipcrItem', function ($q) {
                $q->whereNotNull('output_title');
            })
            ->whereHas('monitoring', function ($q) {
                $q->whereNotNull('quality_rating')->whereNotNull('timeliness_rating');
            })
            ->orderBy('work_date')
            ->get();

        // Excluded entries (for KPI): count all month ORS that could have been included but aren't
        $allMonthEntriesCount = \App\Models\OrsEntry::query()
            ->where('employee_id', $mpor->employee_id)
            ->where('quantity', '>', 0)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->count();

        $includedCount = $ratedEntries->count();
        $excludedCount = max($allMonthEntriesCount - $includedCount, 0);

        // Normalize output key (same style as your employee controller)
        $normalizeOutputKey = static function (string $outputTitle): string {
            return mb_strtolower(
                trim((string) preg_replace('/\s+/', ' ', $outputTitle))
            );
        };

        $functionWeights = $this->resolveFunctionWeights($ratedEntries);

        $detectedTypes = $ratedEntries
            ->map(fn ($entry) => $this->normalizeFunctionType((string) data_get($entry, 'ipcrItem.function_type', '')))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $preferredOrder = ['core', 'support'];
        $orderedTypes = [];
        foreach ($preferredOrder as $preferred) {
            if (in_array($preferred, $detectedTypes, true)) {
                $orderedTypes[] = $preferred;
            }
        }
        foreach ($detectedTypes as $type) {
            if (!in_array($type, $orderedTypes, true)) {
                $orderedTypes[] = $type;
            }
        }

        foreach ($orderedTypes as $type) {
            $sectionLabels[$type] = $this->formatFunctionLabel($type, $functionWeights[$type] ?? null);
        }

        if (empty($sectionLabels)) {
            $defaultWeights = $this->defaultFunctionWeights();
            $sectionLabels = [
                'core' => $this->formatFunctionLabel('core', $defaultWeights['core'] ?? null),
                'support' => $this->formatFunctionLabel('support', $defaultWeights['support'] ?? null),
            ];
        }

        // Build MPOR rows per output_title
        $mporRows = [];

        foreach ($ratedEntries as $entry) {
            $outputTitle = trim((string) data_get($entry, 'ipcrItem.output_title', ''));
            if ($outputTitle === '') {
                continue;
            }

            $outputKey = $normalizeOutputKey($outputTitle);
            if ($outputKey === '') {
                continue;
            }

            $functionType = (string) data_get($entry, 'ipcrItem.function_type', '');
            $section = $this->normalizeFunctionType($functionType);

            if (!isset($mporRows[$outputKey])) {
                $mporRows[$outputKey] = [
                    'id' => $outputKey,
                    'label' => $outputTitle,
                    'section' => $section,
                    'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'qtyTotal' => 0,
                    'qualTotal' => 0,
                    'timeTotal' => 0,
                ];
            }

            $day = (int) Carbon::parse((string) $entry->work_date)->format('j');
            $week = $day <= 7 ? 1 : ($day <= 14 ? 2 : ($day <= 21 ? 3 : 4));

            $qty = is_numeric($entry->quantity) ? (float) $entry->quantity : 0;
            if ($qty <= 0) {
                continue;
            }

            $qRating = (float) data_get($entry, 'monitoring.quality_rating', 0);
            $tRating = (float) data_get($entry, 'monitoring.timeliness_rating', 0);

            // MPOR logic: points = qty * rating (Q/T)
            $mporRows[$outputKey]['qty'][$week] += $qty;
            $mporRows[$outputKey]['qual'][$week] += ($qty * $qRating);
            $mporRows[$outputKey]['time'][$week] += ($qty * $tRating);
        }

        $sectionRows = [];
        foreach (array_keys($sectionLabels) as $sectionKey) {
            $sectionRows[$sectionKey] = [];
        }

        foreach (array_values($mporRows) as $row) {
            $row['qtyTotal'] = array_sum($row['qty']);
            $row['qualTotal'] = array_sum($row['qual']);
            $row['timeTotal'] = array_sum($row['time']);

            if ($row['qtyTotal'] <= 0) {
                continue;
            }

            $sectionKey = (string) ($row['section'] ?? 'core');
            if (!isset($sectionRows[$sectionKey])) {
                $sectionRows[$sectionKey] = [];
                $sectionLabels[$sectionKey] = $this->formatFunctionLabel(
                    $sectionKey,
                    $functionWeights[$sectionKey] ?? null
                );
            }
            $sectionRows[$sectionKey][] = $row;
        }

        // Grand totals
        $grandTotals = [
            'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'qtyTotal' => 0,
            'qualTotal' => 0,
            'timeTotal' => 0,
        ];

        foreach ($sectionRows as $rows) {
            foreach ($rows as $row) {
                foreach ([1, 2, 3, 4] as $week) {
                    $grandTotals['qty'][$week] += (float) $row['qty'][$week];
                    $grandTotals['qual'][$week] += (float) $row['qual'][$week];
                    $grandTotals['time'][$week] += (float) $row['time'][$week];
                }
            }
        }

        $grandTotals['qtyTotal'] = array_sum($grandTotals['qty']);
        $grandTotals['qualTotal'] = array_sum($grandTotals['qual']);
        $grandTotals['timeTotal'] = array_sum($grandTotals['time']);

        return [
            'meta' => [
                'mpor_id' => $mpor->id,
                'status' => $mpor->status,
                'month' => $mpor->month,
                'monthLabel' => $mporMonthYear,
                'employeeName' => $employeeName,
                'officeName' => $officeName,
                'supervisorName' => $supervisor->name ?? '--',
                'returnRemarks' => (string) ($mpor->return_remarks ?? ''),
            ],
            'sectionLabels' => $sectionLabels,
            'sectionRows' => $sectionRows,
            'grandTotals' => $grandTotals,
            'kpis' => [
                'includedRated' => $includedCount,
                'excluded' => $excludedCount,
            ],
        ];
    }

    private function normalizeFunctionType(?string $type): string
    {
        $normalized = strtolower(trim((string) $type));

        if ($normalized === '') {
            return 'custom';
        }

        if (in_array($normalized, ['core', 'support', 'custom'], true)) {
            return $normalized;
        }

        if (str_contains($normalized, 'support')) {
            return 'support';
        }

        if (str_contains($normalized, 'core')) {
            return 'core';
        }

        return $normalized;
    }

    private function formatFunctionLabel(string $type, ?float $weight = null): string
    {
        $base = match ($type) {
            'core' => 'Core Functions',
            'support' => 'Support Functions',
            'custom' => 'Custom Functions',
            default => ucwords(str_replace('_', ' ', $type)) . ' Functions',
        };

        if ($weight === null) {
            return $base;
        }

        return sprintf('%s (%s%%)', $base, $this->formatWeightPercent($weight));
    }

    private function resolveFunctionWeights($entries): array
    {
        $weights = [];

        $uniqueItems = $entries
            ->pluck('ipcrItem')
            ->filter()
            ->unique('uwp_function_id')
            ->values();

        foreach ($uniqueItems as $item) {
            $function = $item->uwpFunction;
            $rawType = (string) ($function?->function_type ?? $item->function_type ?? '');
            $type = $this->normalizeFunctionType($rawType);
            if ($type === '') {
                continue;
            }
            $weights[$type] = ($weights[$type] ?? 0) + (float) ($function?->weight_percent ?? 0);
        }

        if (empty($weights)) {
            return $this->defaultFunctionWeights();
        }

        return $weights;
    }

    private function defaultFunctionWeights(): array
    {
        return [
            'core' => 80.0,
            'support' => 20.0,
        ];
    }

    private function formatWeightPercent(float $value): string
    {
        $rounded = round($value, 2);
        if (abs($rounded - round($rounded)) < 0.01) {
            return (string) (int) round($rounded);
        }

        $formatted = number_format($rounded, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }

    public function endorse(Mpor $mpor)
    {
        if ($mpor->status !== 'approved') {
            if (request()->wantsJson()) return response()->json(['message' => 'MPOR must be approved first.'], 422);
            return back()->with('error', 'MPOR must be approved first.');
        }

        $mpor->update([
            'status' => 'endorsed',
            'endorsed_at' => now(),
            'endorsed_by' => auth()->id(),
        ]);

        $mpor->loadMissing(['employee:id,name,office_id', 'employee.office.head']);
        $deptHead = $mpor->employee?->office?->head;
        if ($deptHead) {
            $supervisor = auth()->user();
            app(WorkflowNotificationDispatcher::class)->notifyUser(
                $deptHead,
                new WorkflowEventNotification(
                    title: 'MPOR Endorsed by Supervisor',
                    body: ($supervisor->name ?? 'Supervisor') . " endorsed an MPOR for {$mpor->employee->name} ({$mpor->month}).",
                    url: route('dept-head.qar'),
                    type: 'info',
                    meta: [
                        'event' => 'mpor.endorsed_to_dept_head',
                        'mpor_id' => $mpor->id,
                        'office_id' => $mpor->office_id,
                        'source_role' => 'supervisor',
                    ],
                )
            );
        }

        if (request()->wantsJson()) return response()->json(['status' => 'endorsed']);
        return back()->with('success', 'MPOR endorsed to Department Head.');
    }

    public function approve(Mpor $mpor)
    {
        if ($mpor->status !== 'submitted') {
            if (request()->wantsJson()) return response()->json(['message' => 'MPOR cannot be approved.'], 422);
            return back()->with('error', 'MPOR cannot be approved.');
        }

        $mpor->update([
            'status' => 'approved',
            'submitted_at' => $mpor->submitted_at ?? now(),
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'endorsed_at' => null,
            'endorsed_by' => null,
            'returned_at' => null,
            'returned_by' => null,
            'return_remarks' => null,
        ]);

        if (request()->wantsJson()) return response()->json(['status' => 'approved']);
        return back()->with('success', 'MPOR approved.');
    }

    public function return(Request $request, Mpor $mpor)
    {
        $supervisor = $request->user();
        abort_unless($supervisor && $supervisor->role === 'supervisor', 403);

        $mpor->load('employee:id,office_id');
        abort_unless((int) ($mpor->employee?->office_id ?? 0) === (int) $supervisor->office_id, 403);

        if ($mpor->status !== 'submitted') {
            if ($request->wantsJson()) return response()->json(['message' => 'Only submitted MPOR can be returned.'], 422);
            return back()->with('info', 'Only submitted MPOR can be returned to employee.');
        }

        $validated = $request->validate([
            'return_remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $mpor->update([
            'status' => 'returned',
            'submitted_at' => null,
            'approved_at' => null,
            'approved_by' => null,
            'endorsed_at' => null,
            'endorsed_by' => null,
            'returned_at' => now(),
            'returned_by' => $supervisor->id,
            'return_remarks' => trim((string) ($validated['return_remarks'] ?? '')) ?: null,
        ]);

        $mpor->loadMissing('employee:id,name');
        if ($mpor->employee) {
            app(WorkflowNotificationDispatcher::class)->notifyUser(
                $mpor->employee,
                new WorkflowEventNotification(
                    title: 'MPOR Returned',
                    body: ($supervisor->name ?? 'Your supervisor') . " returned your MPOR for {$mpor->month}.",
                    url: route('employee.mpor', ['month' => $mpor->month]),
                    type: 'alert',
                    meta: [
                        'event' => 'mpor.returned_to_employee',
                        'mpor_id' => $mpor->id,
                        'employee_id' => $mpor->employee_id,
                        'supervisor_id' => $supervisor->id,
                        'office_id' => $mpor->office_id,
                        'month' => $mpor->month,
                        'status' => (string) $mpor->status,
                        'source_role' => 'supervisor',
                    ],
                )
            );
        }

        if ($request->wantsJson()) return response()->json(['status' => 'returned']);
        return back()->with('success', 'MPOR returned to employee.');
    }
}
