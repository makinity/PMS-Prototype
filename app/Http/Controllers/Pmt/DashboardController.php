<?php

namespace App\Http\Controllers\Pmt;

use App\Http\Controllers\Controller;
use App\Models\AccomplishmentSubmission;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = PerformancePeriod::query()->where('is_active', true)->first()
            ?? PerformancePeriod::query()->orderByDesc('start_date')->first();

        $periodId = $period?->id;

        $uwpBase = UnitWorkPlan::query()->when($periodId, fn ($q) => $q->where('performance_period_id', $periodId));
        $opcrBase = Opcr::query()->when($periodId, fn ($q) => $q->where('performance_period_id', $periodId));
        $submissionBase = AccomplishmentSubmission::query()->when($periodId, fn ($q) => $q->where('performance_period_id', $periodId));

        $pendingUwp = (clone $uwpBase)->where('status', UnitWorkPlan::STATUS_ENDORSED)->count();
        $pendingOpcr = (clone $opcrBase)->whereIn('status', [Opcr::STATUS_ENDORSED, 'for_pmt_review'])->count();
        $pendingAcc = (clone $submissionBase)->where('status', AccomplishmentSubmission::STATUS_DEPT_HEAD_ENDORSED)->count();

        $returnedCount = (clone $uwpBase)->where('status', UnitWorkPlan::STATUS_RETURNED)->count()
            + (clone $opcrBase)->whereIn('status', [Opcr::STATUS_RETURNED, Opcr::STATUS_RETURNED_BY_PMT])->count()
            + (clone $submissionBase)->where('status', AccomplishmentSubmission::STATUS_RETURNED_TO_EMPLOYEE)->count();

        $finalizedCount = (clone $uwpBase)->where('status', UnitWorkPlan::STATUS_PMT_APPROVED)->count()
            + (clone $opcrBase)->whereIn('status', [Opcr::STATUS_APPROVED_BY_PMT, Opcr::STATUS_ADJUSTED_BY_PMT, Opcr::STATUS_RELEASED_BY_PMT])->count()
            + (clone $submissionBase)->whereIn('status', [
                AccomplishmentSubmission::STATUS_RECOMMENDED_BY_PMT,
                AccomplishmentSubmission::STATUS_RELEASED_BY_PMT,
            ])->count();

        $kpis = [
            'pendingActions' => $pendingUwp + $pendingOpcr + $pendingAcc,
            'returnedEscalated' => $returnedCount,
            'finalizedApprovals' => $finalizedCount,
            'queueItems' => $pendingUwp + $pendingOpcr + $pendingAcc + $returnedCount,
        ];

        $queueCounts = [
            'uwp' => $pendingUwp,
            'opcr' => $pendingOpcr,
            'accomplishment' => $pendingAcc,
            'returned' => $returnedCount,
        ];

        $today = Carbon::today();
        $start = $today->copy()->subDays(13);

        $uwpTrendRaw = (clone $uwpBase)
            ->whereDate('updated_at', '>=', $start)
            ->whereDate('updated_at', '<=', $today)
            ->selectRaw('DATE(updated_at) as d')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved_count', [UnitWorkPlan::STATUS_PMT_APPROVED])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as returned_count', [UnitWorkPlan::STATUS_RETURNED])
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $opcrTrendRaw = (clone $opcrBase)
            ->whereDate('updated_at', '>=', $start)
            ->whereDate('updated_at', '<=', $today)
            ->selectRaw('DATE(updated_at) as d')
            ->selectRaw('SUM(CASE WHEN status IN (?, ?, ?) THEN 1 ELSE 0 END) as approved_count', [
                Opcr::STATUS_APPROVED_BY_PMT,
                Opcr::STATUS_ADJUSTED_BY_PMT,
                Opcr::STATUS_RELEASED_BY_PMT,
            ])
            ->selectRaw('SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as returned_count', [
                Opcr::STATUS_RETURNED,
                Opcr::STATUS_RETURNED_BY_PMT,
            ])
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $labels = [];
        $approvedSeries = [];
        $returnedSeries = [];

        for ($cursor = $start->copy(); $cursor->lte($today); $cursor->addDay()) {
            $key = $cursor->toDateString();
            $uwpRow = $uwpTrendRaw->get($key);
            $opcrRow = $opcrTrendRaw->get($key);

            $labels[] = $cursor->format('M d');
            $approvedSeries[] = (int) ($uwpRow->approved_count ?? 0) + (int) ($opcrRow->approved_count ?? 0);
            $returnedSeries[] = (int) ($uwpRow->returned_count ?? 0) + (int) ($opcrRow->returned_count ?? 0);
        }

        $approvalQueue = (clone $opcrBase)
            ->with(['office:id,name'])
            ->whereIn('status', [Opcr::STATUS_ENDORSED, 'for_pmt_review', Opcr::STATUS_SUBMITTED])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(fn (Opcr $opcr) => [
                'office' => $opcr->office?->name ?? 'Unknown Office',
                'status' => strtolower((string) $opcr->status),
                'updated_at' => optional($opcr->updated_at)?->format('Y-m-d H:i'),
            ])
            ->values()
            ->all();

        $recentActions = (clone $submissionBase)
            ->with(['office:id,name'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(fn (AccomplishmentSubmission $submission) => [
                'office' => $submission->office?->name ?? 'Unknown Office',
                'status' => strtolower((string) $submission->status),
                'updated_at' => optional($submission->updated_at)?->format('Y-m-d H:i'),
            ])
            ->values()
            ->all();

        return view('pmt.dashboard', [
            'period' => $period,
            'kpis' => $kpis,
            'queueCounts' => $queueCounts,
            'trend' => [
                'labels' => $labels,
                'series' => [
                    'approved' => $approvedSeries,
                    'returned' => $returnedSeries,
                ],
            ],
            'approvalQueue' => $approvalQueue,
            'recentActions' => $recentActions,
        ]);
    }
}

