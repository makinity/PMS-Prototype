<?php

namespace App\Http\Controllers\Employee;

use App\Exports\StageTwo\IpcrExcelExport;
use App\Exports\StageTwo\SmporExcelExport;
use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Mpor;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
use App\Models\QarHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

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
        $ipcrSections = [];
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
                'ipcrSections',
                'smporMonths',
                'smporSections',
                'smporSourceLabel',
                'smporModeLabel',
            ));
        }

        $periodLabel = (string) ($period->name ?? '-');
        $monthKeys = [];

        if (!empty($period->start_date) && !empty($period->end_date)) {
            $start = Carbon::parse($period->start_date);
            $end = Carbon::parse($period->end_date);

            if ($end->lt($start)) {
                [$start, $end] = [$end, $start];
            }

            $periodLabel = $start->format('F Y') . ' - ' . $end->format('F Y');
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

        $ipcr = null;
        if ($user?->id) {
            $ipcr = Ipcr::query()
                ->where('employee_id', $user->id)
                ->where('performance_period_id', $period->id)
                ->with(['items'])
                ->first();
        }

        if ($ipcr) {
            $ipcr->load([
                'unitWorkPlan.uwpFunctions' => function ($query): void {
                    $query->orderBy('sort_order');
                },
            ]);

            $timelineLabel = (string) ($periodLabel ?? '—');
            $itemsByOutput = ($ipcr->items ?? collect())->groupBy(function ($item): string {
                $outputTitle = trim((string) ($item->output_title ?? ''));
                return $outputTitle !== '' ? $outputTitle : '—';
            });

            $functions = $ipcr->unitWorkPlan?->uwpFunctions ?? collect();
            foreach ($functions as $function) {
                $functionType = strtolower(trim((string) ($function->function_type ?? '')));
                if ($functionType === '') {
                    $functionType = 'support';
                }

                $sectionRows = [];
                foreach ($itemsByOutput as $majorOutput => $outputItems) {
                    $matchingItems = $outputItems->filter(function ($item) use ($functionType): bool {
                        return strtolower(trim((string) ($item->function_type ?? 'support'))) === $functionType;
                    })->values();

                    if ($matchingItems->isEmpty()) {
                        continue;
                    }

                    $targetSummary = '—';
                    foreach ($matchingItems as $item) {
                        $candidateTarget = trim((string) ($item->target_summary ?? ''));
                        if ($candidateTarget !== '') {
                            $targetSummary = $candidateTarget;
                            break;
                        }
                    }

                    $indicators = $matchingItems->map(function ($item): array {
                        $standardsPayload = $item->standards_payload;
                        if (is_string($standardsPayload)) {
                            $decoded = json_decode($standardsPayload, true);
                            $standardsPayload = is_array($decoded) ? $decoded : [];
                        } elseif (!is_array($standardsPayload)) {
                            $standardsPayload = [];
                        }

                        return [
                            'indicator_text' => trim((string) ($item->indicator_text ?? '')) ?: '—',
                            'standards_payload' => $standardsPayload,
                        ];
                    })->values()->all();

                    $sectionRows[] = [
                        'major_output' => (string) $majorOutput,
                        'target_summary' => $targetSummary,
                        'timeline' => $timelineLabel,
                        'indicators_count' => count($indicators),
                        'indicators' => $indicators,
                    ];
                }

                if (empty($sectionRows)) {
                    continue;
                }

                usort($sectionRows, static function (array $left, array $right): int {
                    return strnatcasecmp((string) ($left['major_output'] ?? ''), (string) ($right['major_output'] ?? ''));
                });

                $ipcrSections[] = [
                    'function_type' => $functionType,
                    'title' => trim((string) ($function->name ?? '')) ?: '—',
                    'weight_percent' => isset($function->weight_percent) ? (float) $function->weight_percent : null,
                    'rows' => $sectionRows,
                ];
            }
        }

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

                if (!empty($monthKeys)) {
                    $officialMporQuery->where(function ($query) use ($monthKeys): void {
                        foreach ($monthKeys as $yearMonth) {
                            $query->orWhere('month', 'like', $yearMonth . '%');
                        }
                    });
                }

                $selectedMpors = $officialMporQuery
                    ->orderBy('month')
                    ->get();

                if ($selectedMpors->isNotEmpty()) {
                    $usingOfficialDataset = true;
                    $smporModeLabel = 'Official (PMT-approved QAR)';
                    $smporSourceLabel = 'QAR-linked MPORs';
                }
            }
        }

        if (!$usingOfficialDataset && $user?->id && $user?->office_id) {
            $previewMporQuery = Mpor::query()
                ->where('employee_id', $user->id)
                ->where('office_id', $user->office_id)
                ->whereIn('status', ['submitted']);

            if (!empty($monthKeys)) {
                $previewMporQuery->where(function ($query) use ($monthKeys): void {
                    foreach ($monthKeys as $yearMonth) {
                        $query->orWhere('month', 'like', $yearMonth . '%');
                    }
                });
            }

            $selectedMpors = $previewMporQuery
                ->orderBy('month')
                ->get();
        }


        $aggregatesBySection = [];
        $aggregatesByOutput = [];
        $ipcrRatingsTotalsByOutput = [];

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

                if (!isset($ipcrRatingsTotalsByOutput[$expectedOutput])) {
                    $ipcrRatingsTotalsByOutput[$expectedOutput] = [
                        'qty' => 0.0,
                        'q_points' => 0.0,
                        't_points' => 0.0,
                    ];
                }

                $ipcrRatingsTotalsByOutput[$expectedOutput]['qty'] += $quantity;
                $ipcrRatingsTotalsByOutput[$expectedOutput]['q_points'] += $qualityPoints;
                $ipcrRatingsTotalsByOutput[$expectedOutput]['t_points'] += $timelinessPoints;
            }
        }

        $ipcrRatingsAvgByOutput = [];
        foreach ($ipcrRatingsTotalsByOutput as $outputTitle => $totals) {
            $ratedQty = (float) ($totals['qty'] ?? 0);
            if ($ratedQty <= 0) {
                continue;
            }

            $q = round(((float) ($totals['q_points'] ?? 0)) / $ratedQty, 2);
            $t = round(((float) ($totals['t_points'] ?? 0)) / $ratedQty, 2);

            $ipcrRatingsAvgByOutput[$outputTitle] = [
                'qty' => $ratedQty,
                'q' => $q,
                'e' => $q,
                't' => $t,
            ];
        }

        if (!empty($ipcrSections)) {
            foreach ($ipcrSections as $sectionIndex => $section) {
                $rows = is_array($section['rows'] ?? null) ? $section['rows'] : [];

                foreach ($rows as $rowIndex => $row) {
                    $majorOutput = trim((string) ($row['major_output'] ?? ''));
                    $lookupOutput = preg_match('/[\pL\pN]/u', $majorOutput)
                        ? $majorOutput
                        : 'Unassigned Output';

                    $ratings = $ipcrRatingsAvgByOutput[$lookupOutput] ?? null;

                    $ipcrSections[$sectionIndex]['rows'][$rowIndex]['q'] = $ratings ? (float) $ratings['q'] : null;
                    $ipcrSections[$sectionIndex]['rows'][$rowIndex]['e'] = $ratings ? (float) $ratings['e'] : null;
                    $ipcrSections[$sectionIndex]['rows'][$rowIndex]['t'] = $ratings ? (float) $ratings['t'] : null;
                    $ipcrSections[$sectionIndex]['rows'][$rowIndex]['rated_qty'] = $ratings ? (float) $ratings['qty'] : null;
                }
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
            'ipcrSections',
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

        $start = $period->start_date
            ? Carbon::parse($period->start_date)->startOfMonth()
            : Carbon::now()->startOfYear();
        $end = $period->end_date
            ? Carbon::parse($period->end_date)->startOfMonth()
            : $start->copy()->addMonths(5);

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $rangeMonthMap = [];
        $exportMonthKeys = ['jan', 'feb', 'mar', 'apr', 'may', 'jun'];
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
        $supervisorName = (string) ($office?->employees
            ?->firstWhere('role', 'supervisor')
            ?->name ?? '—');
        $departmentHeadName = (string) (
            $office?->head?->name
            ?? $office?->employees?->firstWhere('role', 'dept-head')?->name
            ?? '—'
        );

        $ipcr = Ipcr::query()
            ->where('employee_id', $user->id)
            ->where('performance_period_id', $period->id)
            ->with([
                'items',
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
        $selectedMpors = collect();
        $usingOfficialDataset = false;
        $pmtApprovedStatus = defined(QarHeader::class . '::STATUS_PMT_APPROVED')
            ? constant(QarHeader::class . '::STATUS_PMT_APPROVED')
            : 'pmt_approved';

        if ($user->office_id && $rangeStartMonth && $rangeEndMonth) {
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

                $selectedMpors = $officialMporQuery
                    ->whereBetween(DB::raw('LEFT(month, 7)'), [$rangeStartMonth, $rangeEndMonth])
                    ->orderBy('month')
                    ->get();

                $usingOfficialDataset = $selectedMpors->isNotEmpty();
            }
        }

        if (!$usingOfficialDataset && $user?->id && $user?->office_id && $rangeStartMonth && $rangeEndMonth) {
            $selectedMpors = Mpor::query()
                ->where('employee_id', $user->id)
                ->where('office_id', $user->office_id)
                ->whereIn('status', ['submitted'])
                ->whereBetween(DB::raw('LEFT(month, 7)'), [$rangeStartMonth, $rangeEndMonth])
                ->orderBy('month')
                ->get();

        }

        $totalRatedEntries = 0;
        $sampleLogged = false;

        foreach ($selectedMpors as $mpor) {
            $rawMonth = trim((string) $mpor->month);
            $monthKey = substr($rawMonth, 0, 7);

            try {
                $parsedYm = Carbon::createFromFormat('Y-m', $monthKey)->format('Y-m');
            } catch (\Throwable) {
                try {
                    $parsedYm = Carbon::parse($rawMonth)->format('Y-m');
                } catch (\Throwable) {
                    continue;
                }
            }

            $monthSlotKey = $rangeMonthMap[$parsedYm] ?? null;

            if (!$monthSlotKey) {
                continue;
            }

            $ratedEntries = $mpor->ratedOrsEntriesForMonth()
                ->with(['monitoring', 'ipcrItem'])
                ->get();
            $totalRatedEntries += $ratedEntries->count();

            $ratedSample = $ratedEntries->take(3)->map(static function ($entry): array {
                return [
                    'id' => $entry->id,
                    'status' => $entry->status,
                    'quantity' => $entry->quantity,
                    'work_date' => $entry->work_date,
                    'ipcr_item_id' => $entry->ipcr_item_id ?? $entry->ipcrItem?->id,
                    'monitoring_id' => $entry->monitoring->id ?? null,
                    'monitoring_exists' => (bool) $entry->monitoring,
                    'quality_rating' => $entry->monitoring->quality_rating ?? null,
                    'timeliness_rating' => $entry->monitoring->timeliness_rating ?? null,
                ];
            })->values()->all();

            try {
                $monthStart = Carbon::createFromFormat('Y-m', $parsedYm)->startOfMonth()->toDateString();
                $monthEnd = Carbon::createFromFormat('Y-m', $parsedYm)->endOfMonth()->toDateString();

                $rawOrsSamples = OrsEntry::query()
                    ->where('employee_id', (int) ($mpor->employee_id ?: $user->id))
                    ->whereBetween('work_date', [$monthStart, $monthEnd])
                    ->take(3)
                    ->get(['id', 'status', 'quantity', 'work_date', 'monitoring_id', 'ipcr_item_id'])
                    ->toArray();

            } catch (\Throwable $exception) {
                logger()->info('SMPOR EXPORT DEBUG RAW ORS SAMPLE FAILED', [
                    'mpor_id' => $mpor->id,
                    'message' => $exception->getMessage(),
                ]);
            }

            if ($ratedEntries->isEmpty() && !$sampleLogged) {
                try {
                    $monthStart = Carbon::createFromFormat('Y-m', $parsedYm)->startOfMonth()->toDateString();
                    $monthEnd = Carbon::createFromFormat('Y-m', $parsedYm)->endOfMonth()->toDateString();

                    $orsSamples = OrsEntry::query()
                        ->where('employee_id', (int) ($mpor->employee_id ?: $user->id))
                        ->whereBetween('work_date', [$monthStart, $monthEnd])
                        ->take(3)
                        ->get(['id', 'status', 'quantity', 'work_date'])
                        ->toArray();
                    $sampleLogged = true;
                } catch (\Throwable $exception) {
                    logger()->info('SMPOR EXPORT ORS SAMPLE FAILED', [
                        'mpor_id' => $mpor->id,
                        'message' => $exception->getMessage(),
                    ]);
                }
            }

            foreach ($ratedEntries as $entry) {
                $monitoring = $entry->monitoring;
                $quantity = (float) ($entry->quantity ?? 0);
                $qualityRating = (float) ($monitoring?->quality_rating ?? 0);
                $timelinessRating = (float) ($monitoring?->timeliness_rating ?? 0);

                if (!$monitoring || is_null($monitoring->quality_rating) || is_null($monitoring->timeliness_rating) || $quantity <= 0) {
                    continue;
                }

                $label = trim((string) (
                    $entry->ipcrItem?->output_title
                    ?? ($entry->mfo_title ?? null)
                    ?? ($entry->mfo ?? null)
                    ?? ''
                ));
                if ($label === '') {
                    $label = 'Unassigned Output';
                }

                if (!isset($aggregateMap[$label])) {
                    $aggregateMap[$label] = $initializeMonthlyBuckets();
                }

                $functionType = $this->normalizeFunctionType((string) ($entry->ipcrItem?->function_type ?? 'support'));
                if (!isset($labelGroupMap[$label])) {
                    $labelGroupMap[$label] = $functionType;
                }

                $aggregateMap[$label][$monthSlotKey]['qty'] += $quantity;
                $aggregateMap[$label][$monthSlotKey]['q_points'] += $quantity * $qualityRating;
                $aggregateMap[$label][$monthSlotKey]['t_points'] += $quantity * $timelinessRating;
            }
        }

        if ($totalRatedEntries === 0 && !$sampleLogged && $selectedMpors->isNotEmpty()) {
            $firstMpor = $selectedMpors->first();
            $fallbackRawMonth = trim((string) ($firstMpor->month ?? ''));
            try {
                $fallbackYm = Carbon::parse($fallbackRawMonth)->format('Y-m');
                $monthStart = Carbon::createFromFormat('Y-m', $fallbackYm)->startOfMonth()->toDateString();
                $monthEnd = Carbon::createFromFormat('Y-m', $fallbackYm)->endOfMonth()->toDateString();

                $orsSamples = OrsEntry::query()
                    ->where('employee_id', (int) ($firstMpor->employee_id ?: $user->id))
                    ->whereBetween('work_date', [$monthStart, $monthEnd])
                    ->take(3)
                    ->get(['id', 'status', 'quantity', 'work_date'])
                    ->toArray();

            } catch (\Throwable $exception) {
                logger()->info('SMPOR EXPORT ORS SAMPLE FAILED', [
                    'mpor_id' => $firstMpor->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        foreach ($aggregateMap as $label => $months) {
            $groupType = $labelGroupMap[$label] ?? 'support';
            if ($groupType === 'support') {
                $addExpectedOutput($supportLabels, $label);
            } else {
                $addExpectedOutput($coreLabels, $label);
            }
        }

        $coreRows = [];
        foreach ($coreLabels as $label) {
            $coreRows[] = $this->makeOutputRow($label, $aggregateMap[$label] ?? []);
        }

        $supportRows = [];
        foreach ($supportLabels as $label) {
            $supportRows[] = $this->makeOutputRow($label, $aggregateMap[$label] ?? []);
        }

        $aggregatePreview = collect($aggregateMap)
            ->take(5)
            ->map(static function (array $monthBuckets, string $label): array {
                return [
                    'label' => $label,
                    'months' => $monthBuckets,
                ];
            })
            ->values()
            ->all();

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

    private function buildFilename(array $payload, bool $preview): string
    {
        $office = Str::slug((string) ($payload['office'] ?? 'Office'), '_');
        $period = Str::slug((string) ($payload['semestral_period'] ?? 'Semestral_Period'), '_');
        $suffix = $preview ? '_Preview' : '';

        return "SMPOR_{$office}_{$period}{$suffix}.xlsx";
    }

    public function exportIpcrExcel(Request $request)
    {
        $ipcrModel = $this->resolveEmployeeIpcr($request);
        $ipcr = $this->buildIpcr($ipcrModel);
        $standards = $this->buildStandardsFromIpcrOrFail($ipcrModel);
        $valuesByIndicator = $this->buildValuesByIndicator($ipcrModel);
        $meta = $this->buildMeta($request, $ipcrModel);

        return Excel::download(
            new IpcrExcelExport($ipcr, $standards, $valuesByIndicator),
            $this->buildIpcrFilename($meta, false)
        );
    }

    public function previewExcel(Request $request)
    {
        $ipcrModel = $this->resolveEmployeeIpcr($request);
        $ipcr = $this->buildIpcr($ipcrModel);
        $standards = $this->buildStandardsFromIpcrOrFail($ipcrModel);
        $valuesByIndicator = $this->buildValuesByIndicator($ipcrModel);
        $meta = $this->buildMeta($request, $ipcrModel);

        return Excel::download(
            new IpcrExcelExport($ipcr, $standards, $valuesByIndicator),
            $this->buildFilename($meta, true)
        );
    }

    private function buildMeta(Request $request, Ipcr $ipcr): array
    {
        $user = $request->user();
        $office = (string) ($ipcr->office?->name ?? $user?->office?->name ?? 'Office');

        $periodLabel = 'Period';
        if ($ipcr->performancePeriod) {
            $periodName = trim((string) ($ipcr->performancePeriod->name ?? ''));
            if ($periodName !== '') {
                $periodLabel = $periodName;
            } elseif ($ipcr->performancePeriod->start_date && $ipcr->performancePeriod->end_date) {
                $start = Carbon::parse($ipcr->performancePeriod->start_date)->format('M d, Y');
                $end = Carbon::parse($ipcr->performancePeriod->end_date)->format('M d, Y');
                $periodLabel = "{$start} - {$end}";
            }
        }

        return [
            'employee' => (string) ($ipcr->employee?->name ?? $user?->name ?? 'Employee'),
            'office' => $office,
            'period' => $periodLabel,
        ];
    }

    private function buildIpcr(Ipcr $ipcr): array
    {
        $sections = [
            'core' => [],
            'support' => [],
        ];

        foreach ($ipcr->items as $item) {
            $output = trim((string) ($item->output_title ?? ''));
            $indicator = trim((string) ($item->indicator_text ?? ''));
            if ($output === '' || $indicator === '') {
                continue;
            }

            $functionType = strtolower(trim((string) ($item->function_type ?? '')));
            $section = str_contains($functionType, 'support') ? 'support' : 'core';

            if (!isset($sections[$section][$output])) {
                $sections[$section][$output] = [
                    'output' => $output,
                    'indicators' => [],
                ];
            }

            if (!in_array($indicator, $sections[$section][$output]['indicators'], true)) {
                $sections[$section][$output]['indicators'][] = $indicator;
            }
        }

        return [
            'core' => array_values($sections['core']),
            'support' => array_values($sections['support']),
        ];
    }

    private function buildValuesByIndicator(Ipcr $ipcr): array
    {
        [$startDate, $endDate] = $this->resolvePeriodWindow($ipcr);

        $entries = OrsEntry::query()
            ->with([
                'ipcrItem:id,output_title,function_type,indicator_text',
                'monitoring:ors_entry_id,quality_rating,timeliness_rating,supervisor_id',
            ])
            ->where('employee_id', $ipcr->employee_id)
            ->where('ipcr_id', $ipcr->id)
            ->where('status', 'rated')
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('monitoring', function ($q) {
                $q->whereNotNull('quality_rating')
                    ->whereNotNull('timeliness_rating');
            })
            ->orderBy('work_date')
            ->get();

        $aggregated = [];
        foreach ($entries as $entry) {
            $indicator = trim((string) data_get($entry, 'ipcrItem.indicator_text', ''));
            if ($indicator === '') {
                continue;
            }

            $quantity = is_numeric($entry->quantity) ? (float) $entry->quantity : 0.0;
            if ($quantity <= 0) {
                continue;
            }

            $qualityRating = (float) data_get($entry, 'monitoring.quality_rating', 0);
            $timelinessRating = (float) data_get($entry, 'monitoring.timeliness_rating', 0);

            if (!isset($aggregated[$indicator])) {
                $aggregated[$indicator] = [
                    'total_qty' => 0.0,
                    'sum_q_points' => 0.0,
                    'sum_t_points' => 0.0,
                ];
            }

            $aggregated[$indicator]['total_qty'] += $quantity;
            $aggregated[$indicator]['sum_q_points'] += ($quantity * $qualityRating);
            $aggregated[$indicator]['sum_t_points'] += ($quantity * $timelinessRating);
        }

        $valuesByIndicator = [];
        foreach ($aggregated as $indicator => $totals) {
            $totalQty = (float) ($totals['total_qty'] ?? 0.0);
            if ($totalQty <= 0) {
                continue;
            }

            $q = round(((float) $totals['sum_q_points']) / $totalQty, 2);
            $t = round(((float) $totals['sum_t_points']) / $totalQty, 2);

            $valuesByIndicator[$indicator] = [
                'accomplishment' => 'Completed ' . $this->formatQuantity($totalQty) . ' output(s) for the period based on rated ORS totals.',
                'q' => $q,
                'e' => $q,
                't' => $t,
                'remarks' => 'Derived from rated ORS entries; supervisor ratings applied (Stage II).',
            ];
        }

        return $valuesByIndicator;
    }

    private function buildStandardsFromIpcrOrFail(Ipcr $ipcr): array
    {
        $allIndicators = [];
        foreach ($ipcr->items as $item) {
            $indicator = trim((string) ($item->indicator_text ?? ''));
            if ($indicator !== '') {
                $allIndicators[$indicator] = true;
            }
        }

        $standards = [];

        foreach ($ipcr->items as $item) {
            $indicator = trim((string) ($item->indicator_text ?? ''));
            if ($indicator === '' || isset($standards[$indicator])) {
                continue;
            }

            $rawPayload = $item->standards_payload;
            $payload = null;

            if (is_string($rawPayload)) {
                $decoded = json_decode($rawPayload, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $payload = $decoded;
                }
            } elseif (is_array($rawPayload)) {
                $payload = $rawPayload;
            }

            if (!is_array($payload) || empty($payload)) {
                continue;
            }

            $normalized = [];
            foreach ([5, 4, 3, 2, 1] as $rating) {
                $bucket = $payload[(string) $rating] ?? $payload[$rating] ?? [];
                if (!is_array($bucket)) {
                    $bucket = [];
                }

                $bucketUpper = [];
                foreach ($bucket as $key => $value) {
                    $bucketUpper[strtoupper((string) $key)] = $value;
                }

                $normalized[$rating] = [
                    'q' => $this->normalizeStandardsDimension($bucketUpper['Q'] ?? null),
                    'e' => $this->normalizeStandardsDimension($bucketUpper['E'] ?? null),
                    't' => $this->normalizeStandardsDimension($bucketUpper['T'] ?? null),
                ];
            }

            $standards[$indicator] = $normalized;
        }

        $missingIndicators = [];
        foreach (array_keys($allIndicators) as $indicator) {
            if (!isset($standards[$indicator])) {
                $missingIndicators[] = $indicator;
            }
        }

        if (!empty($missingIndicators)) {
            abort(422, 'IPCR export requires standards_payload for all indicators. Missing/invalid for: ' . implode(', ', $missingIndicators));
        }

        if (empty($standards)) {
            abort(422, 'IPCR export requires standards_payload for all indicators. Missing/invalid for: (no indicators)');
        }

        return $standards;
    }

    private function normalizeStandardsDimension(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(
                fn ($v) => trim((string) $v),
                $value
            ), fn ($v) => $v !== ''));
        }

        $text = trim((string) $value);
        if ($text === '') {
            return [];
        }

        return [$text];
    }

    private function buildIpcrFilename(array $meta, bool $preview): string
    {
        $employee = Str::slug((string) ($meta['employee'] ?? 'Employee'), '_');
        $office = Str::slug((string) ($meta['office'] ?? 'Office'), '_');
        $period = Str::slug((string) ($meta['period'] ?? 'Period'), '_');
        $suffix = $preview ? '_Preview' : '';

        return "IPCR_{$employee}_{$office}_{$period}{$suffix}.xlsx";
    }

    private function resolveEmployeeIpcr(Request $request): Ipcr
    {
        $user = $request->user();
        abort_unless($user, 403);

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $query = Ipcr::query()
            ->with([
                'employee:id,name,office_id',
                'office:id,name',
                'performancePeriod:id,name,start_date,end_date',
                'items:id,ipcr_id,output_title,function_type,indicator_text,target_summary,standards_payload',
            ])
            ->where('employee_id', $user->id)
            ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT]);

        if ($activePeriod) {
            $query->where('performance_period_id', $activePeriod->id);
        }

        $ipcr = $query
            ->orderByDesc('generated_at')
            ->orderByDesc('id')
            ->first();

        if (!$ipcr && $activePeriod) {
            $ipcr = Ipcr::query()
                ->with([
                    'employee:id,name,office_id',
                    'office:id,name',
                    'performancePeriod:id,name,start_date,end_date',
                    'items:id,ipcr_id,output_title,function_type,indicator_text,target_summary,standards_payload',
                ])
                ->where('employee_id', $user->id)
                ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT])
                ->orderByDesc('generated_at')
                ->orderByDesc('id')
                ->first();
        }

        abort_if(!$ipcr, 404, 'No IPCR found for export.');

        return $ipcr;
    }

    private function resolvePeriodWindow(Ipcr $ipcr): array
    {
        $start = $ipcr->performancePeriod?->start_date
            ? Carbon::parse($ipcr->performancePeriod->start_date)
            : null;
        $end = $ipcr->performancePeriod?->end_date
            ? Carbon::parse($ipcr->performancePeriod->end_date)
            : null;

        if (!$start || !$end) {
            $fallback = PerformancePeriod::query()
                ->whereKey($ipcr->performance_period_id)
                ->orWhere('is_active', true)
                ->orderByDesc('start_date')
                ->first();

            if ($fallback?->start_date && $fallback?->end_date) {
                $start = Carbon::parse($fallback->start_date);
                $end = Carbon::parse($fallback->end_date);
            }
        }

        if (!$start || !$end) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        }

        return [$start->copy()->startOfDay(), $end->copy()->endOfDay()];
    }

    private function formatQuantity(float $quantity): string
    {
        if (fmod($quantity, 1.0) === 0.0) {
            return (string) (int) $quantity;
        }

        return rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.');
    }
}
