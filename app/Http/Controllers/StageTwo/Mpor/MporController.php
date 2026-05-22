<?php

namespace App\Http\Controllers\StageTwo\Mpor;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Mpor;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MporController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && $user->role === 'employee', 403);

        $month = $request->query('month');
        if (!is_string($month) || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = now()->format('Y-m');
        }

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $mporMonthYear = $start->format('F Y');
        $employeeName = $user->name;
        $officeName = optional($user->office)->name ?? '--';

        $mpor = Mpor::query()
            ->where('employee_id', $user->id)
            ->where('month', $month)
            ->first();

        $mporStatus = $mpor?->status ?? 'draft';
        $isMporLocked = in_array($mporStatus, ['submitted', 'endorsed', 'approved'], true);

        $functionWeights = [];
        $mporEmptyReason = null;

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $committedIpcrQuery = Ipcr::query()
            ->with([
                'office:id,name',
                'employee:id,name,office_id',
                'performancePeriod:id,name,start_date,end_date',
                'items:id,ipcr_id,output_title,function_type,indicator_text,standards_payload,uwp_function_id',
                'items.uwpFunction:id,function_type,weight_percent',
            ])
            ->where('employee_id', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('locked_at')
                    ->orWhereIn('status', [
                        Ipcr::STATUS_COMMITTED,
                        Ipcr::STATUS_PENDING_PMT_CALIBRATION,
                        Ipcr::STATUS_RETURNED_BY_PMT,
                        Ipcr::STATUS_APPROVED_BY_PMT,
                        Ipcr::STATUS_ADJUSTED_BY_PMT,
                        Ipcr::STATUS_RELEASED_BY_PMT,
                    ]);
            });

        if ($activePeriod) {
            $committedIpcrQuery->where('performance_period_id', $activePeriod->id);
        }

        $committedIpcr = $committedIpcrQuery
            ->orderByDesc('generated_at')
            ->orderByDesc('id')
            ->first();

        if (!$committedIpcr) {
            $committedIpcr = Ipcr::query()
                ->with([
                    'office:id,name',
                    'employee:id,name,office_id',
                    'performancePeriod:id,name,start_date,end_date',
                    'items:id,ipcr_id,output_title,function_type,indicator_text,standards_payload,uwp_function_id',
                    'items.uwpFunction:id,function_type,weight_percent',
                ])
                ->where('employee_id', $user->id)
                ->where(function ($q) {
                    $q->whereNotNull('locked_at')
                        ->orWhereIn('status', [
                            Ipcr::STATUS_COMMITTED,
                            Ipcr::STATUS_PENDING_PMT_CALIBRATION,
                            Ipcr::STATUS_RETURNED_BY_PMT,
                            Ipcr::STATUS_APPROVED_BY_PMT,
                            Ipcr::STATUS_ADJUSTED_BY_PMT,
                            Ipcr::STATUS_RELEASED_BY_PMT,
                        ]);
                })
                ->orderByDesc('generated_at')
                ->orderByDesc('id')
                ->first();
        }

        $normalizeOutputKey = static function (string $outputTitle): string {
            return mb_strtolower(
                trim((string) preg_replace('/\s+/', ' ', $outputTitle))
            );
        };

        $mporRows = [];
        $sectionOrder = ['core', 'support'];
        $sectionLabels = [];

        if ($committedIpcr) {
            $functionWeights = $this->resolveFunctionWeights($committedIpcr);
            $detectedTypes = $committedIpcr->items
                ->map(fn ($item) => $this->normalizeFunctionType((string) ($item->function_type ?? '')))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $orderedTypes = [];
            foreach ($sectionOrder as $preferred) {
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
                $sectionLabels[$type] = $this->formatFunctionLabel(
                    $type,
                    $functionWeights[$type] ?? null
                );
            }

            foreach ($committedIpcr->items as $item) {
                $outputTitle = trim((string) ($item->output_title ?? ''));
                if ($outputTitle === '') {
                    continue;
                }

                $outputKey = $normalizeOutputKey($outputTitle);
                if ($outputKey === '') {
                    continue;
                }

                $section = $this->normalizeFunctionType((string) ($item->function_type ?? ''));

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
            }

            if (empty($mporRows)) {
                $mporEmptyReason = 'Committed IPCR targets are not available for this period.';
            }
        } else {
            $mporEmptyReason = 'No committed/locked IPCR targets found. Commit your IPCR targets first to populate MPOR rows.';
        }

        $ratedEntries = collect();

        $ratedEntries = OrsEntry::query()
            ->with([
                'ipcrItem:id,output_title,function_type',
                'monitoring:ors_entry_id,quality_rating,timeliness_rating',
            ])
            ->where('employee_id', $user->id)
            ->where('status', 'rated')
            ->where('quantity', '>', 0)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('monitoring', function ($q) {
                $q->whereNotNull('quality_rating')
                    ->whereNotNull('timeliness_rating');
            })
            ->orderBy('work_date')
            ->get();

        $orsTasks = $ratedEntries->map(function (OrsEntry $entry) use ($normalizeOutputKey) {
            $outputTitle = trim((string) data_get($entry, 'ipcrItem.output_title', ''));
            $outputKey = $normalizeOutputKey($outputTitle);

            return [
                'id' => 'ors-' . $entry->id,
                'date' => (string) $entry->work_date,
                'uwpOutputId' => $outputKey,
                'uwpOutputLabel' => $outputTitle === '' ? '--' : $outputTitle,
                'quantityValue' => is_numeric($entry->quantity) ? (float) $entry->quantity : 0,
                'quantityLabel' => is_numeric($entry->quantity) ? (string) $entry->quantity : '--',
                'state' => 'rated',
                'supervisorQuality' => data_get($entry, 'monitoring.quality_rating'),
                'supervisorTimeliness' => data_get($entry, 'monitoring.timeliness_rating'),
                'functionType' => (string) data_get($entry, 'ipcrItem.function_type', ''),
                'outputKey' => $outputKey,
            ];
        })->values()->all();

        $includedRatedTasks = $orsTasks;

        foreach ($includedRatedTasks as $task) {
            $outputKey = (string) ($task['outputKey'] ?? '');
            if ($outputKey === '') {
                continue;
            }

            if (!isset($mporRows[$outputKey]) && $committedIpcr) {
                $taskLabel = trim((string) ($task['uwpOutputLabel'] ?? ''));
                if ($taskLabel === '' || $taskLabel === '--') {
                    continue;
                }

                $rawSection = (string) ($task['functionType'] ?? '');
                $section = $this->normalizeFunctionType($rawSection);
                if (!isset($sectionLabels[$section])) {
                    $sectionLabels[$section] = $this->formatFunctionLabel(
                        $section,
                        $functionWeights[$section] ?? null
                    );
                }

                $mporRows[$outputKey] = [
                    'id' => $outputKey,
                    'label' => $taskLabel,
                    'section' => $section,
                    'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'qtyTotal' => 0,
                    'qualTotal' => 0,
                    'timeTotal' => 0,
                ];
            }

            if (!isset($mporRows[$outputKey])) {
                continue;
            }

            $day = (int) Carbon::parse((string) $task['date'])->format('j');
            $week = $day <= 7 ? 1 : ($day <= 14 ? 2 : ($day <= 21 ? 3 : 4));

            $qty = (float) ($task['quantityValue'] ?? 0);
            if ($qty <= 0) {
                continue;
            }

            $qualityRating = (float) ($task['supervisorQuality'] ?? 0);
            $timelinessRating = (float) ($task['supervisorTimeliness'] ?? 0);

            $mporRows[$outputKey]['qty'][$week] += $qty;
            $mporRows[$outputKey]['qual'][$week] += ($qty * $qualityRating);
            $mporRows[$outputKey]['time'][$week] += ($qty * $timelinessRating);
        }

        if (empty($sectionLabels)) {
            $defaultWeights = $this->defaultFunctionWeights();
            $sectionLabels = [
                'core' => $this->formatFunctionLabel('core', $defaultWeights['core'] ?? null),
                'support' => $this->formatFunctionLabel('support', $defaultWeights['support'] ?? null),
            ];
        }

        $sectionRows = [];
        foreach (array_keys($sectionLabels) as $sectionKey) {
            $sectionRows[$sectionKey] = [];
        }

        foreach (array_values($mporRows) as $row) {
            $row['qtyTotal'] = array_sum($row['qty']);
            $row['qualTotal'] = array_sum($row['qual']);
            $row['timeTotal'] = array_sum($row['time']);

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
                    $grandTotals['qty'][$week] += $row['qty'][$week];
                    $grandTotals['qual'][$week] += $row['qual'][$week];
                    $grandTotals['time'][$week] += $row['time'][$week];
                }
            }
        }

        $grandTotals['qtyTotal'] = array_sum($grandTotals['qty']);
        $grandTotals['qualTotal'] = array_sum($grandTotals['qual']);
        $grandTotals['timeTotal'] = array_sum($grandTotals['time']);

        return view('employee.mpor', compact(
            'month',
            'start',
            'end',
            'mpor',
            'mporMonthYear',
            'employeeName',
            'officeName',
            'mporStatus',
            'isMporLocked',
            'orsTasks',
            'includedRatedTasks',
            'sectionRows',
            'grandTotals',
            'sectionLabels',
            'mporEmptyReason'
        ));
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

    private function defaultFunctionWeights(): array
    {
        return [
            'core' => 80.0,
            'support' => 20.0,
        ];
    }

    private function resolveFunctionWeights(Ipcr $ipcr): array
    {
        $weights = [];

        $uniqueFunctions = $ipcr->items
            ->filter(fn ($item) => !is_null($item->uwp_function_id))
            ->unique('uwp_function_id')
            ->values();

        foreach ($uniqueFunctions as $item) {
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

    private function formatWeightPercent(float $value): string
    {
        $rounded = round($value, 2);
        if (abs($rounded - round($rounded)) < 0.01) {
            return (string) (int) round($rounded);
        }

        $formatted = number_format($rounded, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }

    public function submitMpor(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && $user->role === 'employee', 403);

        $month = $request->input('month');

        if (!is_string($month) || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'type' => 'info',
                    'message' => 'Invalid month selected.',
                ], 422);
            }

            return redirect()
                ->route('employee.mpor.index')
                ->with('info', 'Invalid month selected.');
        }

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // Get active IPCR (same logic as index)
        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $ipcrQuery = Ipcr::query()
            ->where('employee_id', $user->id)
            ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT]);

        if ($activePeriod) {
            $ipcrQuery->where('performance_period_id', $activePeriod->id);
        }

        $ipcr = $ipcrQuery
            ->orderByDesc('generated_at')
            ->orderByDesc('id')
            ->first();

        if (!$ipcr) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'type' => 'info',
                    'message' => 'No active IPCR found.',
                ], 422);
            }

            return redirect()
                ->route('employee.mpor.index', ['month' => $month])
                ->with('info', 'No active IPCR found.');
        }

        // Ensure there are rated ORS entries for this month
        $hasRatedEntries = OrsEntry::query()
            ->where('employee_id', $user->id)
            ->where('ipcr_id', $ipcr->id)
            ->where('status', 'rated')
            ->where('quantity', '>', 0)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('monitoring', function ($q) {
                $q->whereNotNull('quality_rating')
                ->whereNotNull('timeliness_rating');
            })
            ->exists();

        if (! $hasRatedEntries) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'type' => 'info',
                    'message' => 'No rated ORS entries found for this month.',
                ], 422);
            }

            return redirect()
                ->route('employee.mpor.index', ['month' => $month])
                ->with('info', 'No rated ORS entries found for this month.');
        }

        $mpor = Mpor::query()
            ->where('employee_id', $user->id)
            ->where('month', $month)
            ->first();

        if ($mpor && in_array($mpor->status, ['submitted', 'endorsed', 'approved'], true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'type' => 'info',
                    'message' => 'MPOR already submitted.',
                ], 422);
            }

            return redirect()
                ->route('employee.mpor.index', ['month' => $month])
                ->with('info', 'MPOR already submitted.');
        }

        if (! $mpor) {
            $mpor = new Mpor();
            $mpor->employee_id = $user->id;
            $mpor->office_id = $user->office_id;
            $mpor->month = $month;
            $mpor->generated_at = now();
            $mpor->created_by = $user->id;
        }

        $mpor->status = 'submitted';
        $mpor->submitted_at = now();
        $mpor->approved_at = null;
        $mpor->approved_by = null;
        $mpor->endorsed_at = null;
        $mpor->endorsed_by = null;
        $mpor->returned_at = null;
        $mpor->returned_by = null;
        $mpor->return_remarks = null;
        $mpor->save();

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'type' => 'success',
                'message' => 'MPOR successfully submitted.',
                'status' => 'submitted',
                'month' => $month,
            ]);
        }

        return redirect()
            ->route('employee.mpor', ['month' => $month])
            ->with('success', 'MPOR successfully submitted.');
    }
}
