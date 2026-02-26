<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Exports\StageTwo\SmporExcelExport;
use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Mpor;
use App\Models\PerformancePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SmporExcelExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $payload = $this->buildPayload();
        if (is_null($payload)) {
            return redirect()->back();
        }

        return Excel::download(
            new SmporExcelExport($payload),
            $this->buildFilename($payload, false)
        );
    }

    public function previewExcel(Request $request)
    {
        $payload = $this->buildPayload();
        if (is_null($payload)) {
            return redirect()->back();
        }

        return Excel::download(
            new SmporExcelExport($payload),
            $this->buildFilename($payload, true)
        );
    }

    private function buildPayload(): ?array
    {
        $user = auth()->user();
        if (!$user) {
            session()->flash('info', 'Unable to resolve employee account for SMPOR export.');
            return null;
        }

        $period = PerformancePeriod::query()
            ->where('is_active', 1)
            ->first();

        if (!$period) {
            session()->flash('info', 'No active performance period is configured.');
            return null;
        }

        $start = !empty($period->start_date)
            ? Carbon::parse($period->start_date)->startOfMonth()
            : Carbon::now()->startOfYear();
        $end = !empty($period->end_date)
            ? Carbon::parse($period->end_date)->startOfMonth()
            : $start->copy()->addMonths(5);

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $exportMonthKeys = ['jan', 'feb', 'mar', 'apr', 'may', 'jun'];
        $rangeMonthMap = [];
        $cursor = $start->copy();

        while ($cursor->lte($end) && count($rangeMonthMap) < 6) {
            $slotKey = $exportMonthKeys[count($rangeMonthMap)];
            $rangeMonthMap[$cursor->format('Y-m')] = $slotKey;
            $cursor->addMonth();
        }

        if (empty($rangeMonthMap)) {
            $cursor = $start->copy();
            while (count($rangeMonthMap) < 6) {
                $slotKey = $exportMonthKeys[count($rangeMonthMap)];
                $rangeMonthMap[$cursor->format('Y-m')] = $slotKey;
                $cursor->addMonth();
            }
        }

        $rangeStartMonth = array_key_first($rangeMonthMap);
        $rangeEndMonth = array_key_last($rangeMonthMap);

        $semestralPeriodLabel = $start->year === $end->year
            ? $start->format('F') . '-' . $end->format('F Y')
            : $start->format('F Y') . '-' . $end->format('F Y');

        $office = $user->office()->with(['head:id,name', 'employees:id,name,role,office_id'])->first();
        $officeName = (string) ($office?->name ?? '—');
        $employeeName = (string) ($user->name ?? '—');
        $supervisorName = (string) ($office?->employees?->firstWhere('role', 'supervisor')?->name ?? '—');
        $departmentHeadName = (string) (
            $office?->head?->name
            ?? $office?->employees?->firstWhere('role', 'dept-head')?->name
            ?? '—'
        );

        $ipcr = Ipcr::query()
            ->where('employee_id', $user->id)
            ->where('performance_period_id', $period->id)
            ->with([
                'items:id,ipcr_id,output_title,function_type',
                'unitWorkPlan.uwpFunctions' => function ($query): void {
                    $query
                        ->orderBy('sort_order')
                        ->with([
                            'mfos' => function ($mfoQuery): void {
                                $mfoQuery->orderBy('sort_order');
                            },
                        ]);
                },
            ])
            ->orderByDesc('id')
            ->first();

        $coreLabels = [];
        $supportLabels = [];

        $addExpectedOutput = static function (array &$labels, string $label): void {
            $normalized = trim($label);
            if ($normalized === '') {
                return;
            }

            if (!in_array($normalized, $labels, true)) {
                $labels[] = $normalized;
            }
        };

        if ($ipcr) {
            foreach ($ipcr->items as $item) {
                $label = (string) ($item->output_title ?? '');
                $functionType = $this->normalizeFunctionType((string) ($item->function_type ?? 'support'));

                if ($functionType === 'support') {
                    $addExpectedOutput($supportLabels, $label);
                } else {
                    $addExpectedOutput($coreLabels, $label);
                }
            }

            foreach ($ipcr->unitWorkPlan?->uwpFunctions ?? collect() as $function) {
                $functionType = $this->normalizeFunctionType((string) ($function->function_type ?? 'support'));

                foreach ($function->mfos ?? [] as $mfo) {
                    $label = (string) ($mfo->title ?? '');
                    if ($functionType === 'support') {
                        $addExpectedOutput($supportLabels, $label);
                    } else {
                        $addExpectedOutput($coreLabels, $label);
                    }
                }
            }
        }

        $initializeMonthlyBuckets = static function () use ($exportMonthKeys): array {
            $months = [];
            foreach ($exportMonthKeys as $monthKey) {
                $months[$monthKey] = [
                    'qty' => 0.0,
                    'q_points' => 0.0,
                    't_points' => 0.0,
                ];
            }

            return $months;
        };

        $aggregateMap = [];
        $labelGroupMap = [];

        if ($user->office_id && $rangeStartMonth && $rangeEndMonth) {
            $mpors = Mpor::query()
                ->where('employee_id', $user->id)
                ->where('office_id', $user->office_id)
                ->whereIn('status', ['submitted']) // keep submitted for current Stage II flow
                ->whereBetween('month', [$rangeStartMonth, $rangeEndMonth])
                ->orderBy('month')
                ->get();

            foreach ($mpors as $mpor) {
                $monthSlotKey = $rangeMonthMap[(string) $mpor->month] ?? null;
                if (!$monthSlotKey) {
                    continue;
                }

                $entries = $mpor->ratedOrsEntriesForMonth()
                    ->with(['monitoring', 'ipcrItem'])
                    ->get();

                foreach ($entries as $entry) {
                    $monitoring = $entry->monitoring;
                    $quantity = (float) ($entry->quantity ?? 0);

                    if (
                        !$monitoring
                        || is_null($monitoring->quality_rating)
                        || is_null($monitoring->timeliness_rating)
                        || $quantity <= 0
                    ) {
                        continue;
                    }

                    $label = trim((string) (
                        $entry->ipcrItem?->output_title
                        ?? ($entry->mfo_title ?? null)
                        ?? ($entry->mfo ?? null)
                        ?? ''
                    ));
                    if ($label === '') {
                        $label = 'Unassigned MFO';
                    }

                    if (!isset($aggregateMap[$label])) {
                        $aggregateMap[$label] = $initializeMonthlyBuckets();
                    }

                    $functionType = $this->normalizeFunctionType((string) ($entry->ipcrItem?->function_type ?? 'support'));
                    if (!isset($labelGroupMap[$label])) {
                        $labelGroupMap[$label] = $functionType;
                    }

                    $qualityPoints = $quantity * (float) $monitoring->quality_rating;
                    $timelinessPoints = $quantity * (float) $monitoring->timeliness_rating;

                    $aggregateMap[$label][$monthSlotKey]['qty'] += $quantity;
                    $aggregateMap[$label][$monthSlotKey]['q_points'] += $qualityPoints;
                    $aggregateMap[$label][$monthSlotKey]['t_points'] += $timelinessPoints;
                }
            }
        }

        foreach ($aggregateMap as $label => $months) {
            $functionType = $labelGroupMap[$label] ?? 'support';
            if ($functionType === 'support') {
                $addExpectedOutput($supportLabels, $label);
            } else {
                $addExpectedOutput($coreLabels, $label);
            }
        }

        usort($coreLabels, static fn (string $a, string $b): int => strnatcasecmp($a, $b));
        usort($supportLabels, static fn (string $a, string $b): int => strnatcasecmp($a, $b));

        $coreRows = [];
        foreach ($coreLabels as $label) {
            $coreRows[] = $this->makeOutputRow($label, $aggregateMap[$label] ?? []);
        }

        $supportRows = [];
        foreach ($supportLabels as $label) {
            $supportRows[] = $this->makeOutputRow($label, $aggregateMap[$label] ?? []);
        }

        return [
            'name' => $employeeName,
            'office' => $officeName,
            'semestral_period' => $semestralPeriodLabel,
            'supervisor' => $supervisorName,
            'department_head' => $departmentHeadName,
            'employee' => $employeeName,
            'core' => $coreRows,
            'support' => $supportRows,
            'attendance' => [
                'absence' => [
                    'jan' => 0,
                    'feb' => 0,
                    'mar' => 0,
                    'apr' => 0,
                    'may' => 0,
                    'jun' => 0,
                    'total' => 0,
                ],
                'tardiness' => [
                    'jan' => 0,
                    'feb' => 0,
                    'mar' => 0,
                    'apr' => 0,
                    'may' => 0,
                    'jun' => 0,
                    'total' => 0,
                ],
            ],
        ];
    }

    /**
     * $monthValues supports either:
     *  - ['jan' => 12]  (qty only)
     *  - ['jan' => ['qty'=>12,'q_points'=>60,'t_points'=>60]] (explicit per-band values)
     */
    private function makeOutputRow(string $label, array $monthValues): array
    {
        $months = [];
        $keys = ['jan', 'feb', 'mar', 'apr', 'may', 'jun'];

        foreach ($keys as $key) {
            $value = $monthValues[$key] ?? 0;

            if (is_array($value)) {
                $qty = (int) round((float) ($value['qty'] ?? 0));
                $qPoints = (int) round((float) ($value['q_points'] ?? 0));
                $tPoints = (int) round((float) ($value['t_points'] ?? 0));
            } else {
                $qty = (int) round((float) $value);
                $qPoints = 0;
                $tPoints = 0;
            }

            $months[$key] = [
                'qty' => $qty,
                'q_points' => $qPoints,
                't_points' => $tPoints,
            ];
        }

        return [
            'label' => $label,
            'months' => $months,
        ];
    }

    private function normalizeFunctionType(string $functionType): string
    {
        $value = strtolower(trim($functionType));

        if ($value === 'support') {
            return 'support';
        }

        if ($value === 'core' || $value === 'strategic') {
            return 'core';
        }

        return 'support';
    }

    private function buildFilename(array $payload, bool $preview): string
    {
        $office = Str::slug((string) ($payload['office'] ?? 'Office'), '_');
        $period = Str::slug((string) ($payload['semestral_period'] ?? 'Semestral_Period'), '_');
        $suffix = $preview ? '_Preview' : '';

        return "SMPOR_{$office}_{$period}{$suffix}.xlsx";
    }
}
