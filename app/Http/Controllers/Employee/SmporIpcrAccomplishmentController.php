<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Mpor;
use App\Models\PerformancePeriod;
use App\Models\QarHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SmporIpcrAccomplishmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $employeeName = (string) ($user?->name ?? '-');
        $officeName = (string) ($user?->office?->name ?? '-');

        $period = PerformancePeriod::query()
            ->where('is_active', 1)
            ->first();

        $submissionStatus = 'draft';
        $submittedAtLabel = '-';
        $attachmentNames = [];
        $remarksValue = '';
        $smporRows = [];
        $smporMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $smporTotals = [
            'quantity' => 0,
            'quality_points' => 0,
            'timeliness_points' => 0,
            'monthly_quantity' => array_fill_keys($smporMonths, 0.0),
            'monthly_quality_points' => array_fill_keys($smporMonths, 0.0),
            'monthly_timeliness_points' => array_fill_keys($smporMonths, 0.0),
        ];
        $ipcrRows = [];
        $smporSections = [];
        $smporSourceLabel = 'Submitted MPORs';
        $smporModeLabel = 'Preview (monitoring-only)';

        if (!$period) {
            $periodLabel = '-';
            $request->session()->flash('info', 'No active performance period is configured.');

            return view('employee.accomplishment-submission', compact(
                'employeeName',
                'officeName',
                'periodLabel',
                'submissionStatus',
                'submittedAtLabel',
                'attachmentNames',
                'remarksValue',
                'smporRows',
                'smporTotals',
                'ipcrRows',
                'smporMonths',
                'smporSections',
                'smporSourceLabel',
                'smporModeLabel',
            ));
        }

        $periodLabel = (string) ($period->name ?? '-');
        $rangeStartMonth = null;
        $rangeEndMonth = null;
        $monthKeys = [];

        if (!empty($period->start_date) && !empty($period->end_date)) {
            $start = Carbon::parse($period->start_date);
            $end = Carbon::parse($period->end_date);

            if ($end->lt($start)) {
                [$start, $end] = [$end, $start];
            }

            $periodLabel = $start->format('F Y') . ' - ' . $end->format('F Y');
            $rangeStartMonth = $start->format('Y-m');
            $rangeEndMonth = $end->format('Y-m');
            $smporMonths = [];

            $cursor = $start->copy()->startOfMonth();
            $lastMonth = $end->copy()->startOfMonth();

            while ($cursor->lte($lastMonth)) {
                $monthKeys[] = $cursor->format('Y-m');
                $smporMonths[] = $cursor->format('M');
                $cursor->addMonth();
            }
        }

        $smporTotals['monthly_quantity'] = array_fill_keys($smporMonths, 0.0);
        $smporTotals['monthly_quality_points'] = array_fill_keys($smporMonths, 0.0);
        $smporTotals['monthly_timeliness_points'] = array_fill_keys($smporMonths, 0.0);

        $selectedMpors = collect();
        $usingOfficialDataset = false;
        $pmtApprovedStatus = defined(QarHeader::class . '::STATUS_PMT_APPROVED')
            ? constant(QarHeader::class . '::STATUS_PMT_APPROVED')
            : 'pmt_approved';

        if ($user?->office_id) {
            $qar = QarHeader::query()
                ->where('office_id', $user->office_id)
                ->where('performance_period_id', $period->id)
                ->where('status', $pmtApprovedStatus)
                ->with(['mporLinks:id,qar_header_id,mpor_id'])
                ->orderByDesc('approved_at')
                ->orderByDesc('id')
                ->first();

            $officialMporIds = $qar?->mporLinks
                ? $qar->mporLinks->pluck('mpor_id')->filter()->unique()->values()
                : collect();

            if ($officialMporIds->isNotEmpty()) {
                $officialMporQuery = Mpor::query()
                    ->whereIn('id', $officialMporIds)
                    ->where('office_id', $user->office_id);

                if ($user?->id) {
                    $officialMporQuery->where('employee_id', $user->id);
                }

                if ($rangeStartMonth && $rangeEndMonth) {
                    $officialMporQuery->whereBetween('month', [$rangeStartMonth, $rangeEndMonth]);
                }

                $selectedMpors = $officialMporQuery
                    ->orderBy('month')
                    ->get();

                if ($selectedMpors->isNotEmpty()) {
                    $usingOfficialDataset = true;
                    $smporModeLabel = 'Official (PMT-approved QAR)';
                    $smporSourceLabel = 'QAR-linked MPORs';
                    $request->session()->flash('info', 'Showing official SMPOR/IPCR derived from PMT-approved QAR.');
                }
            }
        }

        if (!$usingOfficialDataset && $user?->id && $user?->office_id) {
            $previewMporQuery = Mpor::query()
                ->where('employee_id', $user->id)
                ->where('office_id', $user->office_id)
                ->whereIn('status', ['submitted']);

            if ($rangeStartMonth && $rangeEndMonth) {
                $previewMporQuery->whereBetween('month', [$rangeStartMonth, $rangeEndMonth]);
            }

            $selectedMpors = $previewMporQuery
                ->orderBy('month')
                ->get();
        }

        $aggregatesBySection = [];
        $aggregatesByOutput = [];

        foreach ($selectedMpors as $mpor) {
            $ratedEntries = $mpor->ratedOrsEntriesForMonth()
                ->with(['monitoring', 'ipcrItem'])
                ->get();

            foreach ($ratedEntries as $entry) {
                $monitoring = $entry->monitoring;
                if (!$monitoring || is_null($monitoring->quality_rating) || is_null($monitoring->timeliness_rating)) {
                    continue;
                }

                $quantity = (float) ($entry->quantity ?? 0);
                if ($quantity <= 0) {
                    continue;
                }

                $workDate = !empty($entry->work_date) ? Carbon::parse($entry->work_date) : null;
                if (!$workDate) {
                    continue;
                }

                $monthKey = $workDate->format('Y-m');
                if (!empty($monthKeys) && !in_array($monthKey, $monthKeys, true)) {
                    continue;
                }

                $monthLabel = $workDate->format('M');
                if (!in_array($monthLabel, $smporMonths, true)) {
                    continue;
                }

                $expectedOutput = trim((string) ($entry->ipcrItem?->output_title ?? ''));
                if ($expectedOutput === '') {
                    $expectedOutput = 'Unassigned Output';
                }

                $functionType = $this->normalizeFunctionType((string) ($entry->ipcrItem?->function_type ?? 'support'));

                if (!isset($aggregatesBySection[$functionType][$expectedOutput])) {
                    $aggregatesBySection[$functionType][$expectedOutput] = [
                        'expected_output' => $expectedOutput,
                        'quantity' => $this->initializeMonthMap($smporMonths),
                        'quality' => $this->initializeMonthMap($smporMonths),
                        'timeliness' => $this->initializeMonthMap($smporMonths),
                        'quantity_total' => 0.0,
                        'quality_total' => 0.0,
                        'timeliness_total' => 0.0,
                    ];
                }

                if (!isset($aggregatesByOutput[$expectedOutput])) {
                    $aggregatesByOutput[$expectedOutput] = [
                        'mfo' => $expectedOutput,
                        'total_quantity' => 0.0,
                        'total_quality_points' => 0.0,
                        'total_timeliness_points' => 0.0,
                        'monthly_quantity' => $this->initializeMonthMap($smporMonths),
                        'monthly_quality_points' => $this->initializeMonthMap($smporMonths),
                        'monthly_timeliness_points' => $this->initializeMonthMap($smporMonths),
                    ];
                }

                $qualityPoints = $quantity * (float) $monitoring->quality_rating;
                $timelinessPoints = $quantity * (float) $monitoring->timeliness_rating;

                $aggregatesBySection[$functionType][$expectedOutput]['quantity'][$monthLabel] += $quantity;
                $aggregatesBySection[$functionType][$expectedOutput]['quality'][$monthLabel] += $qualityPoints;
                $aggregatesBySection[$functionType][$expectedOutput]['timeliness'][$monthLabel] += $timelinessPoints;
                $aggregatesBySection[$functionType][$expectedOutput]['quantity_total'] += $quantity;
                $aggregatesBySection[$functionType][$expectedOutput]['quality_total'] += $qualityPoints;
                $aggregatesBySection[$functionType][$expectedOutput]['timeliness_total'] += $timelinessPoints;

                $aggregatesByOutput[$expectedOutput]['total_quantity'] += $quantity;
                $aggregatesByOutput[$expectedOutput]['total_quality_points'] += $qualityPoints;
                $aggregatesByOutput[$expectedOutput]['total_timeliness_points'] += $timelinessPoints;
                $aggregatesByOutput[$expectedOutput]['monthly_quantity'][$monthLabel] += $quantity;
                $aggregatesByOutput[$expectedOutput]['monthly_quality_points'][$monthLabel] += $qualityPoints;
                $aggregatesByOutput[$expectedOutput]['monthly_timeliness_points'][$monthLabel] += $timelinessPoints;

                $smporTotals['quantity'] += $quantity;
                $smporTotals['quality_points'] += $qualityPoints;
                $smporTotals['timeliness_points'] += $timelinessPoints;
                $smporTotals['monthly_quantity'][$monthLabel] += $quantity;
                $smporTotals['monthly_quality_points'][$monthLabel] += $qualityPoints;
                $smporTotals['monthly_timeliness_points'][$monthLabel] += $timelinessPoints;
            }
        }

        $sectionDefinitions = $this->buildSectionDefinitions($user?->id, $period->id);
        $sectionTypes = array_values(array_unique(array_merge(
            array_keys($sectionDefinitions),
            array_keys($aggregatesBySection)
        )));

        usort($sectionTypes, function (string $left, string $right) use ($sectionDefinitions): int {
            $leftOrder = $sectionDefinitions[$left]['sort_order']
                ?? ($left === 'core' ? 10 : ($left === 'support' ? 20 : 30));
            $rightOrder = $sectionDefinitions[$right]['sort_order']
                ?? ($right === 'core' ? 10 : ($right === 'support' ? 20 : 30));

            if ($leftOrder === $rightOrder) {
                return strnatcasecmp($left, $right);
            }

            return $leftOrder <=> $rightOrder;
        });

        foreach ($sectionTypes as $sectionType) {
            $rowsMap = $aggregatesBySection[$sectionType] ?? [];
            $sectionDefinition = $sectionDefinitions[$sectionType] ?? null;

            $orderedOutputs = [];
            if (!empty($sectionDefinition['output_order']) && is_array($sectionDefinition['output_order'])) {
                foreach ($sectionDefinition['output_order'] as $outputTitle) {
                    if (isset($rowsMap[$outputTitle])) {
                        $orderedOutputs[] = $outputTitle;
                    }
                }
            }

            $remainingOutputs = array_values(array_diff(array_keys($rowsMap), $orderedOutputs));
            usort($remainingOutputs, static fn (string $a, string $b): int => strnatcasecmp($a, $b));
            $orderedOutputs = array_merge($orderedOutputs, $remainingOutputs);

            $sectionRows = [];
            $sectionTotals = [
                'quantity_total' => 0.0,
                'quality_total' => 0.0,
                'quality_avg' => 0.0,
                'timeliness_total' => 0.0,
                'timeliness_avg' => 0.0,
            ];

            foreach ($orderedOutputs as $outputTitle) {
                $row = $rowsMap[$outputTitle];
                $quantityTotal = (float) ($row['quantity_total'] ?? 0);
                $qualityTotal = (float) ($row['quality_total'] ?? 0);
                $timelinessTotal = (float) ($row['timeliness_total'] ?? 0);

                $qualityAvg = $quantityTotal > 0 ? $qualityTotal / $quantityTotal : 0.0;
                $timelinessAvg = $quantityTotal > 0 ? $timelinessTotal / $quantityTotal : 0.0;

                $sectionRows[] = [
                    'expected_output' => $row['expected_output'],
                    'quantity' => $this->normalizeMonthMap($row['quantity'] ?? [], $smporMonths),
                    'quality' => $this->normalizeMonthMap($row['quality'] ?? [], $smporMonths),
                    'timeliness' => $this->normalizeMonthMap($row['timeliness'] ?? [], $smporMonths),
                    'quantity_total' => $quantityTotal,
                    'quality_total' => $qualityTotal,
                    'quality_avg' => $qualityAvg,
                    'timeliness_total' => $timelinessTotal,
                    'timeliness_avg' => $timelinessAvg,
                ];

                $sectionTotals['quantity_total'] += $quantityTotal;
                $sectionTotals['quality_total'] += $qualityTotal;
                $sectionTotals['timeliness_total'] += $timelinessTotal;
            }

            if ($sectionTotals['quantity_total'] > 0) {
                $sectionTotals['quality_avg'] = $sectionTotals['quality_total'] / $sectionTotals['quantity_total'];
                $sectionTotals['timeliness_avg'] = $sectionTotals['timeliness_total'] / $sectionTotals['quantity_total'];
            }

            $baseTitle = $sectionDefinition['title']
                ?? ($sectionType === 'core' ? 'CORE FUNCTION' : 'SUPPORT FUNCTION');
            $weightPercent = $sectionDefinition['weight_percent']
                ?? ($sectionType === 'core' ? 80.0 : ($sectionType === 'support' ? 20.0 : 0.0));

            $title = $baseTitle;
            if ((float) $weightPercent > 0) {
                $weightLabel = rtrim(rtrim(number_format((float) $weightPercent, 2, '.', ''), '0'), '.');
                $title = $baseTitle . ' (' . $weightLabel . '%)';
            }

            $smporSections[] = [
                'title' => $title,
                'function_type' => $sectionType,
                'weight_percent' => (float) $weightPercent,
                'rows' => $sectionRows,
                'totals' => $sectionTotals,
            ];
        }

        if (!empty($aggregatesByOutput)) {
            ksort($aggregatesByOutput, SORT_NATURAL | SORT_FLAG_CASE);
            $smporRows = array_values($aggregatesByOutput);

            foreach ($smporRows as $smporRow) {
                $totalQuantity = (float) ($smporRow['total_quantity'] ?? 0);
                $totalQuantityLabel = fmod($totalQuantity, 1.0) === 0.0
                    ? (string) (int) $totalQuantity
                    : rtrim(rtrim(number_format($totalQuantity, 2, '.', ''), '0'), '.');

                $ipcrRows[] = [
                    'mfo' => (string) ($smporRow['mfo'] ?? 'Unassigned MFO'),
                    'accomplishment_summary' => "Completed {$totalQuantityLabel} output(s) for the period based on submitted MPOR totals.",
                    'evidence_label' => $totalQuantity > 0 ? 'Attached (reference)' : '-',
                ];
            }
        }

        return view('employee.accomplishment-submission', compact(
            'employeeName',
            'officeName',
            'periodLabel',
            'submissionStatus',
            'submittedAtLabel',
            'attachmentNames',
            'remarksValue',
            'smporRows',
            'smporTotals',
            'ipcrRows',
            'smporMonths',
            'smporSections',
            'smporSourceLabel',
            'smporModeLabel',
        ));
    }

    private function buildSectionDefinitions(?int $employeeId, ?int $periodId): array
    {
        if (!$employeeId || !$periodId) {
            return [];
        }

        $ipcr = Ipcr::query()
            ->where('employee_id', $employeeId)
            ->where('performance_period_id', $periodId)
            ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT])
            ->with([
                'unitWorkPlan.uwpFunctions' => function ($functionQuery): void {
                    $functionQuery
                        ->orderBy('sort_order')
                        ->with([
                            'mfos' => function ($mfoQuery): void {
                                $mfoQuery->orderBy('sort_order');
                            },
                        ]);
                },
            ])
            ->orderByRaw(
                "CASE
                    WHEN status = ? THEN 0
                    WHEN status = ? THEN 1
                    ELSE 2
                END",
                [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT]
            )
            ->orderByDesc('id')
            ->first();

        if (!$ipcr?->unitWorkPlan) {
            return [];
        }

        $definitions = [];
        $fallbackSortOrder = 100;

        foreach ($ipcr->unitWorkPlan->uwpFunctions as $function) {
            $functionType = $this->normalizeFunctionType((string) ($function->function_type ?? 'support'));
            $defaultTitle = $functionType === 'core' ? 'CORE FUNCTION' : 'SUPPORT FUNCTION';
            $candidateTitle = trim((string) ($function->name ?? ''));
            $title = $candidateTitle !== '' ? mb_strtoupper($candidateTitle) : $defaultTitle;

            if (!isset($definitions[$functionType])) {
                $definitions[$functionType] = [
                    'title' => $title,
                    'weight_percent' => 0.0,
                    'sort_order' => is_null($function->sort_order) ? $fallbackSortOrder++ : (int) $function->sort_order,
                    'output_order' => [],
                ];
            }

            $definitions[$functionType]['weight_percent'] += (float) ($function->weight_percent ?? 0);

            foreach ($function->mfos as $mfo) {
                $outputTitle = trim((string) ($mfo->title ?? ''));
                if ($outputTitle === '') {
                    continue;
                }

                if (!in_array($outputTitle, $definitions[$functionType]['output_order'], true)) {
                    $definitions[$functionType]['output_order'][] = $outputTitle;
                }
            }
        }

        return $definitions;
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

    private function initializeMonthMap(array $monthLabels): array
    {
        $map = [];
        foreach ($monthLabels as $label) {
            $map[$label] = 0.0;
        }

        return $map;
    }

    private function normalizeMonthMap(array $values, array $monthLabels): array
    {
        $normalized = $this->initializeMonthMap($monthLabels);
        foreach ($values as $label => $value) {
            if (array_key_exists($label, $normalized)) {
                $normalized[$label] = (float) $value;
            }
        }

        return $normalized;
    }
}
