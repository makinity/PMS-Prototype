<?php

namespace App\Http\Controllers\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\AccomplishmentSubmission;
use App\Models\Ipcr;
use App\Models\PerformancePeriod;
use App\Support\ResolvesIpcrTargetScores;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccomplishmentReviewController extends Controller
{
    use ResolvesIpcrTargetScores;

    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 403);
        $statusFilter = strtolower(trim((string) $request->query('status', '')));
        $allowedStatuses = [
            'draft',
            'submitted_to_supervisor',
            'supervisor_endorsed',
            'dept_head_endorsed',
            'recommended_by_pmt',
            'pmt_approved',
            'released_by_pmt',
            'returned_to_employee',
        ];
        $statusFilter = in_array($statusFilter, $allowedStatuses, true) ? $statusFilter : '';

        $period = PerformancePeriod::query()
            ->where('is_active', 1)
            ->first();

        $periodLabel = '--';
        $rows = [];
        $submissionPayloads = [];
        $infoMessage = null;

        if (!$period) {
            $infoMessage = 'No active performance period is configured.';
            return view('dept-head.accomplishment-review', compact(
                'periodLabel',
                'rows',
                'submissionPayloads',
                'infoMessage'
            ));
        }

        $periodLabel = $this->buildPeriodLabel($period);
        $monthLabels = $this->buildMonthLabels($period);

        // Dept Head scope (demo): same office/unit as the employee
        $submissionQuery = AccomplishmentSubmission::query()
            ->where('performance_period_id', $period->id)
            ->whereHas('employee', function ($q) use ($user) {
                $q->where('office_id', $user->office_id);
            })
            ->with([
                'employee:id,name,office_id',
                'employee.office:id,name',
                'mpors:id,employee_id,office_id,month,status',
                'ipcr:id,unit_work_plan_id,performance_period_id,office_id,employee_id',
                'ipcr.unitWorkPlan.uwpFunctions.mfos:id,uwp_function_id,title,target_quantity,target_timeline',
                'ipcr.items:id,ipcr_id,uwp_function_id,uwp_success_indicator_id,output_title,function_type,indicator_text,target_quantity,target_timeline,target_summary,standards_payload',
            ])
            ->orderByDesc('submitted_at')
            ->orderByDesc('id');

        if ($statusFilter !== '') {
            $submissionQuery->where('status', $statusFilter);
        }

        $submissions = $submissionQuery->get();

        if ($submissions->isEmpty()) {
            $infoMessage = $statusFilter !== ''
                ? 'No submissions found for the selected status in your office/unit.'
                : 'No submissions found for your office/unit.';
        }

        foreach ($submissions as $submission) {
            $employeeName = (string) ($submission->employee?->name ?? '--');
            $officeName = (string) ($submission->employee?->office?->name ?? '--');
            $status = strtolower(trim((string) ($submission->status ?? 'draft')));

            $submittedAtLabel = $submission->submitted_at
                ? $submission->submitted_at->format('M d, Y h:i A')
                : '--';

            $supervisorActionLabel = $submission->supervisor_action_at
                ? $submission->supervisor_action_at->format('M d, Y h:i A')
                : '--';

            $attachments = $this->buildAttachmentPayload($submission->attachments ?? []);

            // Same SMPOR/IPCR snapshot logic as Supervisor
            [$smporSections, $ratingsByOutput, $ratingsByIndicator] = $this->buildSmporSectionsFromSnapshot(
                $submission->mpors ?? collect(),
                $monthLabels,
                $submission->ipcr
            );

            $source = strtolower((string) ($submission->dataset_source ?? ''));
            $smporModeLabel = $source === 'qar_official'
                ? 'Official (Submitted Snapshot)'
                : 'Preview (Submitted Snapshot)';
            $smporSourceLabel = $source === 'qar_official'
                ? 'QAR-linked MPORs (snapshot)'
                : 'Submitted MPORs (snapshot)';

            $ipcrSections = $this->buildIpcrSections(
                $submission->ipcr,
                $periodLabel,
                $ratingsByOutput,
                $ratingsByIndicator
            );

            $payload = [
                'id' => (int) $submission->id,
                'employee_name' => $employeeName,
                'office_name' => $officeName,
                'period_label' => $periodLabel,
                'status' => $status,
                'status_label' => $this->formatStatusLabel($status),
                'submitted_at_label' => $submittedAtLabel,
                'supervisor_action_at_label' => $supervisorActionLabel,
                'remarks' => (string) ($submission->employee_remarks ?? ''),
                'attachments' => $attachments,
                'smporMonths' => $monthLabels,
                'smporSections' => $smporSections,
                'smporSourceLabel' => $smporSourceLabel,
                'smporModeLabel' => $smporModeLabel,
                'ipcrSections' => $ipcrSections,
                'supervisor_remarks' => (string) ($submission->supervisor_remarks ?? ''),
            ];

            $ratingService = app(\App\Services\PerformanceRatingService::class);
            if ($submission->ipcr) {
                $submission->ipcr->refresh();
                [$computedScore, $computedRating] = $ratingService->getResolvedScoreAndRating($submission->ipcr);
            } else {
                $computedScore = 0.00;
                $computedRating = '--';
            }

            $payload['computed_score'] = $computedScore;
            $payload['computed_rating'] = $computedRating;

            $rows[] = [
                'id' => (int) $submission->id,
                'employee_name' => $employeeName,
                'office_name' => $officeName,
                'period_label' => $periodLabel,
                'status' => $status,
                'status_label' => $this->formatStatusLabel($status),
                'submitted_at_label' => $submittedAtLabel,
                'supervisor_action_at_label' => $supervisorActionLabel,
                'computed_score' => $computedScore,
            ];

            $submissionPayloads[(string) $submission->id] = $payload;
        }

        return view('dept-head.accomplishment-review', compact(
            'periodLabel',
            'rows',
            'submissionPayloads',
            'infoMessage'
        ));
    }

    private function buildPeriodLabel(PerformancePeriod $period): string
    {
        if ($period->start_date && $period->end_date) {
            $start = Carbon::parse($period->start_date);
            $end = Carbon::parse($period->end_date);
            return $start->format('F Y') . ' - ' . $end->format('F Y');
        }

        return (string) ($period->name ?? '--');
    }

    private function buildMonthLabels(PerformancePeriod $period): array
    {
        if (!$period->start_date || !$period->end_date) {
            return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        }

        $start = Carbon::parse($period->start_date)->startOfMonth();
        $end = Carbon::parse($period->end_date)->startOfMonth();
        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $labels = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $labels[] = $cursor->format('M');
            $cursor->addMonth();
        }

        return !empty($labels) ? $labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    }

    private function buildAttachmentPayload(array $attachments): array
    {
        return collect($attachments)
            ->map(function ($item): array {
                $name = trim((string) data_get($item, 'original_name', ''));
                $path = trim((string) data_get($item, 'path', ''));

                return [
                    'name' => $name !== '' ? $name : 'Attachment',
                    'url' => $path !== '' ? Storage::disk('public')->url($path) : null,
                ];
            })
            ->values()
            ->all();
    }

    private function buildSmporSectionsFromSnapshot(Collection $mpors, array $monthLabels, ?Ipcr $ipcr = null): array
    {
        $targetQuantityByOutput = $this->buildTargetQuantityByOutput($ipcr);
        $sections = [];
        $ratingsByOutput = [];
        $ratingsByIndicator = [];
        $indicatorTotals = [];
        $sectionBuckets = [];
        $ratingsTotals = [];

        foreach ($mpors as $mpor) {
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

                try {
                    $workDate = !empty($entry->work_date) ? Carbon::parse($entry->work_date) : null;
                } catch (\Throwable) {
                    $workDate = null;
                }

                if (!$workDate) {
                    continue;
                }

                $monthLabel = $workDate->format('M');
                if (!in_array($monthLabel, $monthLabels, true)) {
                    continue;
                }

                $expectedOutput = trim((string) ($entry->ipcrItem?->output_title ?? ''));
                if ($expectedOutput === '') {
                    $expectedOutput = 'Unassigned Output';
                }

                $functionType = $this->normalizeFunctionType((string) ($entry->ipcrItem?->function_type ?? 'support'));
                if (!isset($sectionBuckets[$functionType][$expectedOutput])) {
                    $sectionBuckets[$functionType][$expectedOutput] = [
                        'expected_output' => $expectedOutput,
                        'quantity' => $this->initializeMonthMap($monthLabels),
                        'quality' => $this->initializeMonthMap($monthLabels),
                        'timeliness' => $this->initializeMonthMap($monthLabels),
                        'quantity_total' => 0.0,
                        'quality_total' => 0.0,
                        'timeliness_total' => 0.0,
                    ];
                }

                $qualityPoints = $quantity * (float) $monitoring->quality_rating;
                $timelinessPoints = $quantity * (float) $monitoring->timeliness_rating;

                $indicatorText = trim((string) ($entry->ipcrItem?->indicator_text ?? ''));
                if ($indicatorText !== '') {
                    $indicatorLookupKey = $this->buildIndicatorRatingLookupKey($expectedOutput, $indicatorText);

                    if (!isset($indicatorTotals[$indicatorLookupKey])) {
                        $indicatorTotals[$indicatorLookupKey] = [
                            'output' => $expectedOutput,
                            'indicator_text' => $indicatorText,
                            'qty' => 0.0,
                            'q_points' => 0.0,
                            't_points' => 0.0,
                        ];
                    }

                    $indicatorTotals[$indicatorLookupKey]['qty'] += $quantity;
                    $indicatorTotals[$indicatorLookupKey]['q_points'] += $qualityPoints;
                    $indicatorTotals[$indicatorLookupKey]['t_points'] += $timelinessPoints;
                }

                $sectionBuckets[$functionType][$expectedOutput]['quantity'][$monthLabel] += $quantity;
                $sectionBuckets[$functionType][$expectedOutput]['quality'][$monthLabel] += $qualityPoints;
                $sectionBuckets[$functionType][$expectedOutput]['timeliness'][$monthLabel] += $timelinessPoints;
                $sectionBuckets[$functionType][$expectedOutput]['quantity_total'] += $quantity;
                $sectionBuckets[$functionType][$expectedOutput]['quality_total'] += $qualityPoints;
                $sectionBuckets[$functionType][$expectedOutput]['timeliness_total'] += $timelinessPoints;

                if (!isset($ratingsTotals[$expectedOutput])) {
                    $ratingsTotals[$expectedOutput] = [
                        'qty' => 0.0,
                        'q_points' => 0.0,
                        't_points' => 0.0,
                    ];
                }

                $ratingsTotals[$expectedOutput]['qty'] += $quantity;
                $ratingsTotals[$expectedOutput]['q_points'] += $qualityPoints;
                $ratingsTotals[$expectedOutput]['t_points'] += $timelinessPoints;
            }
        }

        [$ratingsByOutput, $ratingsByIndicator] = $this->buildRatedIpcrPerformanceMaps($ipcr);

        $sectionTypes = array_keys($sectionBuckets);
        usort($sectionTypes, function (string $left, string $right): int {
            $order = ['core' => 1, 'support' => 2];
            $leftOrder = $order[$left] ?? 99;
            $rightOrder = $order[$right] ?? 99;

            if ($leftOrder === $rightOrder) {
                return strnatcasecmp($left, $right);
            }

            return $leftOrder <=> $rightOrder;
        });

        foreach ($sectionTypes as $sectionType) {
            $rowsMap = $sectionBuckets[$sectionType] ?? [];
            ksort($rowsMap, SORT_NATURAL | SORT_FLAG_CASE);

            $rows = [];
            $totals = [
                'quantity_total' => 0.0,
                'quality_total' => 0.0,
                'quality_avg' => 0.0,
                'timeliness_total' => 0.0,
                'timeliness_avg' => 0.0,
            ];

            foreach ($rowsMap as $row) {
                $qty = (float) ($row['quantity_total'] ?? 0);
                $qualityTotal = (float) ($row['quality_total'] ?? 0);
                $timelinessTotal = (float) ($row['timeliness_total'] ?? 0);

                $rows[] = [
                    'expected_output' => $row['expected_output'] ?? 'Unassigned Output',
                    'quantity' => $this->normalizeMonthMap($row['quantity'] ?? [], $monthLabels),
                    'quality' => $this->normalizeMonthMap($row['quality'] ?? [], $monthLabels),
                    'timeliness' => $this->normalizeMonthMap($row['timeliness'] ?? [], $monthLabels),
                    'quantity_total' => $qty,
                    'quality_total' => $qualityTotal,
                    'quality_avg' => $qty > 0 ? $qualityTotal / $qty : 0.0,
                    'timeliness_total' => $timelinessTotal,
                    'timeliness_avg' => $qty > 0 ? $timelinessTotal / $qty : 0.0,
                ];

                $totals['quantity_total'] += $qty;
                $totals['quality_total'] += $qualityTotal;
                $totals['timeliness_total'] += $timelinessTotal;
            }

            if ($totals['quantity_total'] > 0) {
                $totals['quality_avg'] = $totals['quality_total'] / $totals['quantity_total'];
                $totals['timeliness_avg'] = $totals['timeliness_total'] / $totals['quantity_total'];
            }

            $sections[] = [
                'title' => $sectionType === 'support' ? 'SUPPORT FUNCTIONS' : 'CORE FUNCTIONS',
                'function_type' => $sectionType,
                'rows' => $rows,
                'totals' => $totals,
            ];
        }

        return [$sections, $ratingsByOutput, $ratingsByIndicator];
    }

    private function buildIpcrSections(?Ipcr $ipcr, string $periodLabel, array $ratingsByOutput, array $ratingsByIndicator): array
    {
        if (!$ipcr) {
            return [];
        }

        $items = $ipcr->items ?? collect();
        if ($items->isEmpty()) {
            return [];
        }

        $targetSummaryByFunctionAndOutput = $this->buildTargetSummaryByFunctionAndOutput($ipcr);
        $sections = [];
        $itemsByFunctionAndOutput = $items->groupBy(function ($item): string {
            $functionType = $this->normalizeFunctionType((string) ($item->function_type ?? 'support'));
            $output = trim((string) ($item->output_title ?? ''));
            if ($output === '') {
                $output = 'Unassigned Output';
            }

            return $functionType . '||' . $output;
        });

        foreach ($itemsByFunctionAndOutput as $groupKey => $groupItems) {
            [$functionType, $majorOutput] = explode('||', (string) $groupKey, 2);
            $fallbackTargetSummary = trim((string) ($groupItems->first(function ($row) {
                return trim((string) ($row->target_summary ?? '')) !== '';
            })?->target_summary ?? ''));
            $firstMatchingItem = $groupItems->first();
            $targetMapKey = (int) ($firstMatchingItem?->uwp_function_id ?? 0) . '||' . trim((string) $majorOutput);
            $targetSummary = trim((string) ($targetSummaryByFunctionAndOutput[$targetMapKey] ?? $fallbackTargetSummary));

            if ($targetSummary === '') {
                $targetSummary = '--';
            }

            $indicators = $groupItems->map(function ($item) use ($ratingsByIndicator, $majorOutput): array {
                $standardsPayload = $item->standards_payload;
                if (is_string($standardsPayload)) {
                    $decoded = json_decode($standardsPayload, true);
                    $standardsPayload = is_array($decoded) ? $decoded : [];
                } elseif (!is_array($standardsPayload)) {
                    $standardsPayload = [];
                }

                $indicatorText = trim((string) ($item->indicator_text ?? '')) ?: '--';
                $lookupKey = $indicatorText !== '--' ? $indicatorText : '';
                $indicatorLookupKey = $lookupKey !== ''
                    ? $this->buildIndicatorRatingLookupKey($majorOutput, $lookupKey)
                    : '';
                $indicatorRatings = $indicatorLookupKey !== '' ? ($ratingsByIndicator[$indicatorLookupKey] ?? null) : null;

                return [
                    'indicator_text' => $indicatorText,
                    'standards_payload' => $standardsPayload,
                    'q' => $indicatorRatings ? (float) ($indicatorRatings['q'] ?? 0) : null,
                    'e' => $indicatorRatings ? (float) ($indicatorRatings['e'] ?? 0) : null,
                    't' => $indicatorRatings ? (float) ($indicatorRatings['t'] ?? 0) : null,
                    'a' => $indicatorRatings ? (float) ($indicatorRatings['a'] ?? 0) : null,
                    'rated_qty' => $indicatorRatings ? (float) ($indicatorRatings['qty'] ?? 0) : null,
                ];
            })->values()->all();

            $ratings = $ratingsByOutput[$majorOutput] ?? null;

            if (!isset($sections[$functionType])) {
                $sections[$functionType] = [
                    'function_type' => $functionType,
                    'title' => $functionType === 'support' ? 'Support Functions' : 'Core Functions',
                    'weight_percent' => null,
                    'rows' => [],
                ];
            }

            $sections[$functionType]['rows'][] = [
                'major_output' => $majorOutput,
                'target_summary' => $targetSummary,
                'timeline' => $periodLabel,
                'indicators_count' => count($indicators),
                'indicators' => $indicators,
                'q' => $ratings ? (float) $ratings['q'] : null,
                'e' => $ratings ? (float) $ratings['e'] : null,
                't' => $ratings ? (float) $ratings['t'] : null,
                'a' => $ratings ? (float) $ratings['a'] : null,
                'rated_qty' => $ratings ? (float) $ratings['qty'] : null,
            ];
        }

        $ordered = [];
        foreach (['core', 'support'] as $type) {
            if (!isset($sections[$type])) {
                continue;
            }

            usort($sections[$type]['rows'], static function (array $a, array $b): int {
                return strnatcasecmp((string) ($a['major_output'] ?? ''), (string) ($b['major_output'] ?? ''));
            });

            $ordered[] = $sections[$type];
            unset($sections[$type]);
        }

        if (!empty($sections)) {
            foreach ($sections as $section) {
                usort($section['rows'], static function (array $a, array $b): int {
                    return strnatcasecmp((string) ($a['major_output'] ?? ''), (string) ($b['major_output'] ?? ''));
                });
                $ordered[] = $section;
            }
        }

        return $ordered;
    }

    private function formatStatusLabel(string $status): string
    {
        return match ($status) {
            'submitted_to_supervisor' => 'Submitted to Supervisor',
            'supervisor_endorsed' => 'Supervisor Endorsed',
            'dept_head_endorsed' => 'Awaiting PMT Recommendation',
            'recommended_by_pmt', 'pmt_approved' => 'Recommended by PMT',
            'approved_by_pmt', 'adjusted_by_pmt' => 'Calibrated by PMT',
            'released_by_pmt' => 'Officially Released',
            'returned_to_employee' => 'Returned to Employee',
            default => 'Draft',
        };
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

    public function endorseToPmt($id)
    {
        $submission = AccomplishmentSubmission::findOrFail($id);
        if ($submission->status !== 'supervisor_endorsed') {
            return back()->with('error', 'Only submissions endorsed by supervisor can be endorsed to pmt.');
        }

        $submission->update([
            'status' => 'dept_head_endorsed',
            'dept_head_id' => Auth::id(),
            'dept_head_action_at' => now(),
        ]);

        if ($submission->ipcr) {
            $submission->ipcr->update([
                'status' => \App\Models\Ipcr::STATUS_PENDING_PMT_CALIBRATION,
            ]);
        }

        return back()->with('success', 'Submission successfully endorsed to PMT.');
    }
}
