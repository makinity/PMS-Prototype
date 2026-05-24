<?php

namespace App\Http\Controllers\Pmt;

use App\Http\Controllers\Controller;
use App\Models\AccomplishmentSubmission;
use App\Models\Ipcr;
use App\Models\PerformancePeriod;
use App\Notifications\WorkflowEventNotification;
use App\Services\WorkflowNotificationDispatcher;
use App\Support\ResolvesIpcrTargetScores;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EmployeeCalibrationController extends Controller
{
    use ResolvesIpcrTargetScores;

    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 403);
        $search = trim($request->string('search')->toString());

        $period = PerformancePeriod::query()
            ->where('is_active', 1)
            ->first();

        $periodLabel = '--';
        $rows = [];
        $submissionPayloads = [];
        $infoMessage = null;

        if (!$period) {
            $infoMessage = 'No active performance period is configured.';

            return view('pmt.employee-calibration.index', compact(
                'periodLabel',
                'rows',
                'submissionPayloads',
                'infoMessage'
            ));
        }

        $periodLabel = $this->buildPeriodLabel($period);
        $monthLabels = $this->buildMonthLabels($period);

        // Fetch IPCRs that are in calibration states AND have accomplishment endorsed by dept head
        $ipcrsQuery = Ipcr::query()
            ->where('performance_period_id', $period->id)
            ->whereIn('status', [
                Ipcr::STATUS_PENDING_PMT_CALIBRATION,
                Ipcr::STATUS_APPROVED_BY_PMT,
                Ipcr::STATUS_ADJUSTED_BY_PMT,
                Ipcr::STATUS_RELEASED_BY_PMT,
            ])
            ->whereHas('accomplishmentSubmission', function ($q) {
                $q->whereIn('status', [
                    AccomplishmentSubmission::STATUS_RECOMMENDED_BY_PMT,
                    AccomplishmentSubmission::STATUS_RELEASED_BY_PMT,
                ]);
            })
            ->with([
                'employee:id,name,office_id',
                'employee.office:id,name',
                'unitWorkPlan.uwpFunctions.mfos:id,uwp_function_id,title,target_quantity,target_timeline',
                'items:id,ipcr_id,uwp_function_id,uwp_success_indicator_id,output_title,function_type,indicator_text,target_quantity,target_timeline,target_summary,standards_payload',
            ]);

        if ($search !== '') {
            $normalizedSearch = mb_strtolower($search);
            $ipcrsQuery->where(function ($query) use ($search, $normalizedSearch) {
                $query->whereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('employee.office', function ($officeQuery) use ($search) {
                    $officeQuery->where('name', 'like', '%' . $search . '%');
                })->orWhereRaw('LOWER(status) like ?', ['%' . $normalizedSearch . '%']);
            });
        }

        $ipcrs = $ipcrsQuery->orderByDesc('updated_at')->get();

        if ($ipcrs->isEmpty()) {
            $infoMessage = 'No IPCRs pending calibration, calibrated, or released found for the active period.';
        }

        // Get associated Accomplishment Submissions for evidence (SMPORs)
        $ipcrIds = $ipcrs->pluck('id')->toArray();
        $submissions = AccomplishmentSubmission::whereIn('ipcr_id', $ipcrIds)
            ->with(['mpors:id,employee_id,office_id,month,status'])
            ->get()
            ->keyBy('ipcr_id');

        foreach ($ipcrs as $ipcr) {
            $employeeName = (string) ($ipcr->employee?->name ?? '--');
            $officeName = (string) ($ipcr->employee?->office?->name ?? '--');
            $status = strtolower(trim((string) ($ipcr->status ?? '')));
            
            $submission = $submissions->get($ipcr->id);

            $attachments = $submission ? $this->buildAttachmentPayload($submission->attachments ?? []) : [];

            [$smporSections, $ratingsByOutput, $ratingsByIndicator] = $this->buildSmporSectionsFromSnapshot(
                $submission ? ($submission->mpors ?? collect()) : collect(),
                $monthLabels,
                $ipcr
            );

            $source = $submission ? strtolower((string) ($submission->dataset_source ?? '')) : '';
            $smporModeLabel = $source === 'qar_official'
                ? 'Official (Submitted Snapshot)'
                : 'Preview (Submitted Snapshot)';
            $smporSourceLabel = $source === 'qar_official'
                ? 'QAR-linked MPORs (snapshot)'
                : 'Submitted MPORs (snapshot)';

            $ipcrSections = $this->buildIpcrSections(
                $ipcr,
                $periodLabel,
                $ratingsByOutput,
                $ratingsByIndicator
            );

            $ratingService = app(\App\Services\PerformanceRatingService::class);
            $computedScore = (float) ($ipcr->final_score ?? 0);
            if ($computedScore <= 0) {
                $computedScore = $ratingService->calculateComputedScore($ipcr);
            }

            $payload = [
                'id' => (int) $ipcr->id,
                'employee_name' => $employeeName,
                'office_name' => $officeName,
                'period_label' => $periodLabel,
                'status' => $status,
                'status_label' => $this->formatStatusLabel($status),
                'computed_score' => $computedScore,
                'computed_rating' => $ipcr->adjectival_rating ?: $ratingService->resolveAdjectivalRating($computedScore),
                'adjusted_score' => $ipcr->pmt_adjusted_score,
                'adjusted_rating' => $ipcr->pmt_adjusted_rating,
                'adjustment_reason' => $ipcr->pmt_adjustment_reason,
                'pmt_remarks' => $ipcr->pmt_remarks,
                'released_at' => $ipcr->released_at?->format('M d, Y h:i A'),
                'attachments' => $attachments,
                'smporMonths' => $monthLabels,
                'smporSections' => $smporSections,
                'smporSourceLabel' => $smporSourceLabel,
                'smporModeLabel' => $smporModeLabel,
                'ipcrSections' => $ipcrSections,
            ];

            $rows[] = [
                'id' => (int) $ipcr->id,
                'employee_name' => $employeeName,
                'office_name' => $officeName,
                'period_label' => $periodLabel,
                'status' => $status,
                'status_label' => $this->formatStatusLabel($status),
                'computed_score' => $computedScore,
                'adjusted_score' => $ipcr->pmt_adjusted_score,
            ];

            $submissionPayloads[(string) $ipcr->id] = $payload;
        }

        return view('pmt.employee-calibration.index', compact(
            'periodLabel',
            'rows',
            'submissionPayloads',
            'infoMessage',
            'search'
        ));
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $ipcr = Ipcr::with([
                'employee:id,name,office_id',
                'employee.office:id,name',
                'unitWorkPlan.uwpFunctions.mfos:id,uwp_function_id,title,target_quantity,target_timeline',
                'items:id,ipcr_id,uwp_function_id,uwp_success_indicator_id,output_title,function_type,indicator_text,target_quantity,target_timeline,target_summary,standards_payload',
            ])
            ->findOrFail($id);

        $period = PerformancePeriod::find($ipcr->performance_period_id);
        $periodLabel = $period ? $this->buildPeriodLabel($period) : '--';
        $monthLabels = $period ? $this->buildMonthLabels($period) : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];

        $submission = AccomplishmentSubmission::where('ipcr_id', $ipcr->id)
            ->with(['mpors:id,employee_id,office_id,month,status'])
            ->first();

        $employeeName = (string) ($ipcr->employee?->name ?? '--');
        $officeName = (string) ($ipcr->employee?->office?->name ?? '--');
        $status = strtolower(trim((string) ($ipcr->status ?? '')));
        
        $attachments = $submission ? $this->buildAttachmentPayload($submission->attachments ?? []) : [];

        [$smporSections, $ratingsByOutput, $ratingsByIndicator] = $this->buildSmporSectionsFromSnapshot(
            $submission ? ($submission->mpors ?? collect()) : collect(),
            $monthLabels,
            $ipcr
        );

        $source = $submission ? strtolower((string) ($submission->dataset_source ?? '')) : '';
        $smporModeLabel = $source === 'qar_official'
            ? 'Official (Submitted Snapshot)'
            : 'Preview (Submitted Snapshot)';
        $smporSourceLabel = $source === 'qar_official'
            ? 'QAR-linked MPORs (snapshot)'
            : 'Submitted MPORs (snapshot)';

        $ipcrSections = $this->buildIpcrSections(
            $ipcr,
            $periodLabel,
            $ratingsByOutput,
            $ratingsByIndicator
        );

        $ratingService = app(\App\Services\PerformanceRatingService::class);
        $computedScore = (float) ($ipcr->final_score ?? 0);
        if ($computedScore <= 0) {
            $computedScore = $ratingService->calculateComputedScore($ipcr);
        }

        $payload = [
            'id' => (int) $ipcr->id,
            'employee_name' => $employeeName,
            'office_name' => $officeName,
            'period_label' => $periodLabel,
            'status' => $status,
            'status_label' => $this->formatStatusLabel($status),
            'computed_score' => $computedScore,
            'computed_rating' => $ipcr->adjectival_rating ?: $ratingService->resolveAdjectivalRating($computedScore),
            'adjusted_score' => $ipcr->pmt_adjusted_score,
            'adjusted_rating' => $ipcr->pmt_adjusted_rating,
            'adjustment_reason' => $ipcr->pmt_adjustment_reason,
            'pmt_remarks' => $ipcr->pmt_remarks,
            'released_at' => $ipcr->released_at?->format('M d, Y h:i A'),
            'attachments' => $attachments,
            'smporMonths' => $monthLabels,
            'smporSections' => $smporSections,
            'smporSourceLabel' => $smporSourceLabel,
            'smporModeLabel' => $smporModeLabel,
            'ipcrSections' => $ipcrSections,
        ];

        return view('pmt.employee-calibration.show', [
            'ipcr' => $ipcr,
            'payload' => $payload,
        ]);
    }

    public function adjust(Request $request, $id)
    {
        $request->validate([
            'adjusted_score' => 'required|numeric|min:1|max:5',
            'adjusted_rating' => 'required|string|max:255',
            'adjustment_reason' => 'required|string|max:1000',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $ipcr = Ipcr::findOrFail($id);
        
        // Remove strict isLocked check to allow PMT to adjust scores during evaluation phase.
        // if ($ipcr->isLocked()) {
        //     return back()->with('error', 'IPCR is finalized and locked. Calibration is no longer allowed.');
        // }

        if (!in_array($ipcr->status, [Ipcr::STATUS_PENDING_PMT_CALIBRATION, Ipcr::STATUS_APPROVED_BY_PMT, Ipcr::STATUS_ADJUSTED_BY_PMT])) {
            return back()->with('error', 'Invalid IPCR status for calibration.');
        }

        $ipcr->update([
            'status' => Ipcr::STATUS_ADJUSTED_BY_PMT,
            'pmt_adjusted_score' => $request->adjusted_score,
            'pmt_adjusted_rating' => $request->adjusted_rating,
            'pmt_adjustment_reason' => $request->adjustment_reason,
            'pmt_remarks' => $request->remarks,
            'pmt_reviewed_by' => Auth::id(),
            'pmt_reviewed_at' => now(),
            'released_by' => null,
            'released_at' => null,
        ]);

        $ratingService = app(\App\Services\PerformanceRatingService::class);
        $ratingService->calculateAndSaveFinalScore($ipcr);
        $this->recalculateOpcrScore($ipcr);

        if ($request->wantsJson()) return response()->json(['status' => 'adjusted', 'score' => $ipcr->pmt_adjusted_score, 'rating' => $ipcr->pmt_adjusted_rating]);
        return back()->with('success', 'IPCR rating adjusted and calibrated successfully.');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $ipcr = Ipcr::findOrFail($id);
        
        // Remove strict isLocked check to allow PMT to approve scores during evaluation phase.
        // if ($ipcr->isLocked()) {
        //     return back()->with('error', 'IPCR is already finalized and locked.');
        // }

        if (!in_array($ipcr->status, [Ipcr::STATUS_PENDING_PMT_CALIBRATION, Ipcr::STATUS_APPROVED_BY_PMT, Ipcr::STATUS_ADJUSTED_BY_PMT])) {
            return back()->with('error', 'Invalid IPCR status for approval.');
        }

        $ipcr->update([
            'status' => Ipcr::STATUS_APPROVED_BY_PMT,
            'pmt_remarks' => $request->remarks,
            'pmt_reviewed_by' => Auth::id(),
            'pmt_reviewed_at' => now(),
            'released_by' => null,
            'released_at' => null,
        ]);

        $ratingService = app(\App\Services\PerformanceRatingService::class);
        $ratingService->calculateAndSaveFinalScore($ipcr);
        $this->recalculateOpcrScore($ipcr);

        if ($request->wantsJson()) return response()->json(['status' => 'approved']);
        return back()->with('success', 'IPCR rating approved and calibrated successfully.');
    }

    public function release(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $ipcr = Ipcr::findOrFail($id);

        if (!in_array($ipcr->status, [Ipcr::STATUS_APPROVED_BY_PMT, Ipcr::STATUS_ADJUSTED_BY_PMT], true)) {
            return back()->with('error', 'Only calibrated IPCR ratings can be released.');
        }

        $previousStatus = (string) $ipcr->status;

        DB::transaction(function () use ($ipcr, $request) {
            $ipcr->update([
                'status' => Ipcr::STATUS_RELEASED_BY_PMT,
                'pmt_remarks' => $request->remarks,
                'pmt_reviewed_by' => Auth::id(),
                'pmt_reviewed_at' => now(),
                'released_by' => Auth::id(),
                'released_at' => now(),
                'finalized_at' => now(),
                'locked_at' => now(),
            ]);

            $submission = AccomplishmentSubmission::where('ipcr_id', $ipcr->id)->first();
            if ($submission) {
                $submission->update([
                    'status' => AccomplishmentSubmission::STATUS_RELEASED_BY_PMT,
                    'pmt_remarks' => $request->remarks,
                    'pmt_id' => Auth::id(),
                    'pmt_action_at' => now(),
                ]);
            }
        });

        $this->recalculateOpcrScore($ipcr);
        $this->autoReleaseOpcrIfAllReleased($ipcr);

        if ($previousStatus !== Ipcr::STATUS_RELEASED_BY_PMT) {
            $notifier = app(WorkflowNotificationDispatcher::class);
            $meta = [
                'event' => 'stage4.data_updated',
                'source' => 'ipcr_release',
                'ipcr_id' => $ipcr->id,
                'office_id' => $ipcr->office_id,
                'performance_period_id' => $ipcr->performance_period_id,
                'status' => Ipcr::STATUS_RELEASED_BY_PMT,
            ];

            $notifier->notifyRole(
                'pmt',
                new WorkflowEventNotification(
                    title: 'Stage IV Data Updated',
                    body: 'Released IPCR results are ready for Top Performers review.',
                    url: route('pmt.top-performers.index'),
                    type: 'info',
                    meta: $meta
                )
            );
            $notifier->notifyRole(
                'pmt',
                new WorkflowEventNotification(
                    title: 'Stage IV Data Updated',
                    body: 'Released IPCR results are ready for Development Planning review.',
                    url: route('pmt.development-planning.index'),
                    type: 'info',
                    meta: $meta
                )
            );
        }

        if ($request->wantsJson()) return response()->json(['status' => 'released']);
        return back()->with('success', 'IPCR official rating released to the office and employee.');
    }

    public function returnIpcr(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        $ipcr = Ipcr::findOrFail($id);
        
        if (!in_array($ipcr->status, [Ipcr::STATUS_PENDING_PMT_CALIBRATION, Ipcr::STATUS_APPROVED_BY_PMT, Ipcr::STATUS_ADJUSTED_BY_PMT])) {
            return back()->with('error', 'Invalid IPCR status for return.');
        }

        DB::transaction(function() use ($ipcr, $request) {
            $ipcr->update([
                'status' => Ipcr::STATUS_RETURNED_BY_PMT,
                'pmt_remarks' => $request->remarks,
                'pmt_reviewed_by' => Auth::id(),
                'pmt_reviewed_at' => now(),
                'released_by' => null,
                'released_at' => null,
                'locked_at' => null,
                'finalized_at' => null,
            ]);

            // We must also return the accomplishment submission so the flow goes back
            $submission = AccomplishmentSubmission::where('ipcr_id', $ipcr->id)->first();
            if ($submission) {
                $submission->update([
                    'status' => AccomplishmentSubmission::STATUS_RETURNED_TO_EMPLOYEE,
                    'dept_head_remarks' => $request->remarks,
                    'pmt_remarks' => $request->remarks,
                    'pmt_id' => Auth::id(),
                    'pmt_action_at' => now(),
                ]);
            }
        });

        if ($request->wantsJson()) return response()->json(['status' => 'returned']);
        return back()->with('success', 'IPCR returned successfully.');
    }

    // Helper functions (same as AccomplishmentReviewController)
    
    private function recalculateOpcrScore(Ipcr $ipcr): void
    {
        $officeId = $ipcr->office_id;
        $periodId = $ipcr->performance_period_id;
        if (!$officeId || !$periodId) return;

        $scores = Ipcr::where('office_id', $officeId)
            ->where('performance_period_id', $periodId)
            ->whereNotNull('final_score')
            ->where('final_score', '>', 0)
            ->pluck('final_score');

        if ($scores->isEmpty()) return;

        $avg = round($scores->avg(), 2);
        $ratingService = app(\App\Services\PerformanceRatingService::class);

        \App\Models\Opcr::where('office_id', $officeId)
            ->where('performance_period_id', $periodId)
            ->update([
                'final_score' => $avg,
                'adjectival_rating' => $ratingService->resolveAdjectivalRating($avg),
            ]);
    }

    private function autoReleaseOpcrIfAllReleased(Ipcr $ipcr): void
    {
        $officeId = $ipcr->office_id;
        $periodId = $ipcr->performance_period_id;
        if (!$officeId || !$periodId) return;

        $totalIpcrs = Ipcr::where('office_id', $officeId)
            ->where('performance_period_id', $periodId)
            ->count();

        $releasedIpcrs = Ipcr::where('office_id', $officeId)
            ->where('performance_period_id', $periodId)
            ->where('status', Ipcr::STATUS_RELEASED_BY_PMT)
            ->count();

        if ($totalIpcrs > 0 && $totalIpcrs === $releasedIpcrs) {
            \App\Models\Opcr::where('office_id', $officeId)
                ->where('performance_period_id', $periodId)
                ->update([
                    'status' => \App\Models\Opcr::STATUS_RELEASED_BY_PMT,
                    'released_by' => Auth::id(),
                    'released_at' => now(),
                    'locked_at' => now(),
                ]);
        }
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
            })->values()->all();
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
                if ($quantity <= 0) continue;

                try {
                    $workDate = !empty($entry->work_date) ? Carbon::parse($entry->work_date) : null;
                } catch (\Throwable) {
                    $workDate = null;
                }
                if (!$workDate) continue;

                $monthLabel = $workDate->format('M');
                if (!in_array($monthLabel, $monthLabels, true)) continue;

                $expectedOutput = trim((string) ($entry->ipcrItem?->output_title ?? ''));
                if ($expectedOutput === '') $expectedOutput = 'Unassigned Output';

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
                        $indicatorTotals[$indicatorLookupKey] = ['output' => $expectedOutput, 'indicator_text' => $indicatorText, 'qty' => 0.0, 'q_points' => 0.0, 't_points' => 0.0];
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
            }
        }

        [$ratingsByOutput, $ratingsByIndicator] = $this->buildRatedIpcrPerformanceMaps($ipcr);

        $sectionTypes = array_keys($sectionBuckets);
        usort($sectionTypes, function (string $left, string $right): int {
            $order = ['core' => 1, 'support' => 2];
            return ($order[$left] ?? 99) <=> ($order[$right] ?? 99);
        });

        foreach ($sectionTypes as $sectionType) {
            $rowsMap = $sectionBuckets[$sectionType] ?? [];
            ksort($rowsMap, SORT_NATURAL | SORT_FLAG_CASE);

            $rows = [];
            $totals = ['quantity_total' => 0.0, 'quality_total' => 0.0, 'quality_avg' => 0.0, 'timeliness_total' => 0.0, 'timeliness_avg' => 0.0];

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
        if (!$ipcr) return [];
        $items = $ipcr->items ?? collect();
        if ($items->isEmpty()) return [];

        $targetSummaryByFunctionAndOutput = $this->buildTargetSummaryByFunctionAndOutput($ipcr);
        $sections = [];
        $itemsByFunctionAndOutput = $items->groupBy(function ($item): string {
            $functionType = $this->normalizeFunctionType((string) ($item->function_type ?? 'support'));
            $output = trim((string) ($item->output_title ?? ''));
            return $functionType . '||' . ($output === '' ? 'Unassigned Output' : $output);
        });

        foreach ($itemsByFunctionAndOutput as $groupKey => $groupItems) {
            [$functionType, $majorOutput] = explode('||', (string) $groupKey, 2);
            $fallbackTargetSummary = trim((string) ($groupItems->first(fn($row) => trim((string) ($row->target_summary ?? '')) !== '')?->target_summary ?? ''));
            $targetMapKey = (int) ($groupItems->first()?->uwp_function_id ?? 0) . '||' . trim((string) $majorOutput);
            $targetSummary = trim((string) ($targetSummaryByFunctionAndOutput[$targetMapKey] ?? $fallbackTargetSummary));
            if ($targetSummary === '') $targetSummary = '--';

            $indicators = $groupItems->map(function ($item) use ($ratingsByIndicator, $majorOutput): array {
                $standardsPayload = is_string($item->standards_payload) ? json_decode($item->standards_payload, true) : (is_array($item->standards_payload) ? $item->standards_payload : []);
                $indicatorText = trim((string) ($item->indicator_text ?? '')) ?: '--';
                $indicatorLookupKey = $indicatorText !== '--' ? $this->buildIndicatorRatingLookupKey($majorOutput, $indicatorText) : '';
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
                $sections[$functionType] = ['function_type' => $functionType, 'title' => $functionType === 'support' ? 'Support Functions' : 'Core Functions', 'rows' => []];
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
            if (isset($sections[$type])) {
                usort($sections[$type]['rows'], fn($a, $b) => strnatcasecmp($a['major_output'], $b['major_output']));
                $ordered[] = $sections[$type];
            }
        }
        return $ordered;
    }

    private function formatStatusLabel(string $status): string
    {
        return match ($status) {
            Ipcr::STATUS_PENDING_PMT_CALIBRATION => 'Pending Calibration',
            Ipcr::STATUS_APPROVED_BY_PMT => 'Calibrated (Approved)',
            Ipcr::STATUS_ADJUSTED_BY_PMT => 'Calibrated (Adjusted)',
            Ipcr::STATUS_RELEASED_BY_PMT => 'Officially Released',
            Ipcr::STATUS_RETURNED_BY_PMT => 'Returned by PMT',
            default => str_replace('_', ' ', ucfirst($status)),
        };
    }

    private function normalizeFunctionType(string $functionType): string
    {
        $value = strtolower(trim($functionType));
        return in_array($value, ['core', 'strategic'], true) ? 'core' : 'support';
    }

    private function initializeMonthMap(array $monthLabels): array
    {
        return array_fill_keys($monthLabels, 0.0);
    }

    private function normalizeMonthMap(array $values, array $monthLabels): array
    {
        $normalized = $this->initializeMonthMap($monthLabels);
        foreach ($values as $label => $value) {
            if (array_key_exists($label, $normalized)) $normalized[$label] = (float) $value;
        }
        return $normalized;
    }
}
