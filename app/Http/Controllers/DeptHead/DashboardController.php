<?php

namespace App\Http\Controllers\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\AccomplishmentSubmission;
use App\Models\Opcr;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $officeId = $user?->supervisedOffice?->id ?? $user?->office_id;

        $period = PerformancePeriod::query()->where('is_active', true)->first()
            ?? PerformancePeriod::query()->orderByDesc('start_date')->first();

        $periodId = $period?->id;

        $teamMembers = User::query()
            ->where('office_id', $officeId)
            ->where('role', 'employee')
            ->where('is_active', true);

        $uwpBase = UnitWorkPlan::query()
            ->where('office_id', $officeId)
            ->when($periodId, fn ($q) => $q->where('performance_period_id', $periodId));

        $opcrBase = Opcr::query()
            ->where('office_id', $officeId)
            ->when($periodId, fn ($q) => $q->where('performance_period_id', $periodId));

        $submissionBase = AccomplishmentSubmission::query()
            ->where('office_id', $officeId)
            ->when($periodId, fn ($q) => $q->where('performance_period_id', $periodId));

        $orsBase = OrsEntry::query()
            ->where('office_id', $officeId)
            ->when($periodId, fn ($q) => $q->where('performance_period_id', $periodId));

        $pendingReviews = (clone $uwpBase)->where('status', UnitWorkPlan::STATUS_SUBMITTED)->count()
            + (clone $opcrBase)->whereIn('status', [Opcr::STATUS_SUBMITTED, Opcr::STATUS_ENDORSED])->count()
            + (clone $submissionBase)->where('status', AccomplishmentSubmission::STATUS_SUPERVISOR_ENDORSED)->count();

        $atRisk = (clone $uwpBase)->where('status', UnitWorkPlan::STATUS_RETURNED)->count()
            + (clone $opcrBase)->whereIn('status', [Opcr::STATUS_RETURNED, Opcr::STATUS_RETURNED_BY_PMT])->count()
            + (clone $submissionBase)->where('status', AccomplishmentSubmission::STATUS_RETURNED_TO_EMPLOYEE)->count();

        $completedEndorsed = (clone $uwpBase)->whereIn('status', [UnitWorkPlan::STATUS_ENDORSED, UnitWorkPlan::STATUS_PMT_APPROVED])->count()
            + (clone $opcrBase)->whereIn('status', [Opcr::STATUS_APPROVED, Opcr::STATUS_APPROVED_BY_PMT, Opcr::STATUS_RELEASED_BY_PMT])->count()
            + (clone $submissionBase)->whereIn('status', [
                AccomplishmentSubmission::STATUS_DEPT_HEAD_ENDORSED,
                AccomplishmentSubmission::STATUS_RECOMMENDED_BY_PMT,
                AccomplishmentSubmission::STATUS_RELEASED_BY_PMT,
            ])->count();

        $kpis = [
            'teamMembers' => (clone $teamMembers)->count(),
            'pendingReviews' => $pendingReviews,
            'atRisk' => $atRisk,
            'completedEndorsed' => $completedEndorsed,
        ];

        $statusCounts = [
            'draft' => (clone $uwpBase)->where('status', UnitWorkPlan::STATUS_DRAFT)->count()
                + (clone $opcrBase)->where('status', Opcr::STATUS_DRAFT)->count()
                + (clone $submissionBase)->where('status', AccomplishmentSubmission::STATUS_DRAFT)->count(),
            'submitted' => (clone $uwpBase)->where('status', UnitWorkPlan::STATUS_SUBMITTED)->count()
                + (clone $opcrBase)->where('status', Opcr::STATUS_SUBMITTED)->count()
                + (clone $submissionBase)->where('status', AccomplishmentSubmission::STATUS_SUBMITTED_TO_SUPERVISOR)->count(),
            'endorsed' => (clone $uwpBase)->where('status', UnitWorkPlan::STATUS_ENDORSED)->count()
                + (clone $opcrBase)->where('status', Opcr::STATUS_ENDORSED)->count()
                + (clone $submissionBase)->whereIn('status', [
                    AccomplishmentSubmission::STATUS_SUPERVISOR_ENDORSED,
                    AccomplishmentSubmission::STATUS_DEPT_HEAD_ENDORSED,
                ])->count(),
            'returned' => (clone $uwpBase)->where('status', UnitWorkPlan::STATUS_RETURNED)->count()
                + (clone $opcrBase)->whereIn('status', [Opcr::STATUS_RETURNED, Opcr::STATUS_RETURNED_BY_PMT])->count()
                + (clone $submissionBase)->where('status', AccomplishmentSubmission::STATUS_RETURNED_TO_EMPLOYEE)->count(),
            'completed' => (clone $opcrBase)->whereIn('status', [Opcr::STATUS_APPROVED, Opcr::STATUS_APPROVED_BY_PMT, Opcr::STATUS_RELEASED_BY_PMT])->count()
                + (clone $submissionBase)->whereIn('status', [
                    AccomplishmentSubmission::STATUS_RECOMMENDED_BY_PMT,
                    AccomplishmentSubmission::STATUS_RELEASED_BY_PMT,
                ])->count(),
        ];

        $today = Carbon::today();
        $start = $today->copy()->subDays(13);
        $trendRaw = (clone $orsBase)
            ->whereNotNull('work_date')
            ->whereBetween('work_date', [$start->toDateString(), $today->toDateString()])
            ->selectRaw('DATE(work_date) as d')
            ->selectRaw('SUM(CASE WHEN LOWER(status) IN (?, ?) THEN 1 ELSE 0 END) as submitted_count', ['submitted', 'rated'])
            ->selectRaw('SUM(CASE WHEN LOWER(status) = ? THEN 1 ELSE 0 END) as rated_count', ['rated'])
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $trendLabels = [];
        $trendSubmitted = [];
        $trendRated = [];

        for ($cursor = $start->copy(); $cursor->lte($today); $cursor->addDay()) {
            $key = $cursor->toDateString();
            $row = $trendRaw->get($key);
            $trendLabels[] = $cursor->format('M d');
            $trendSubmitted[] = (int) ($row->submitted_count ?? 0);
            $trendRated[] = (int) ($row->rated_count ?? 0);
        }

        $recentActivity = (clone $orsBase)
            ->with(['employee:id,name', 'ipcrItem:id,output_title'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get()
            ->map(fn (OrsEntry $entry) => [
                'employee' => $entry->employee?->name ?? 'Unknown',
                'task' => $entry->ipcrItem?->output_title ?? 'Unlinked Output',
                'status' => strtolower((string) $entry->status),
                'work_date' => optional($entry->work_date)->format('Y-m-d'),
                'updated_at' => optional($entry->updated_at)?->format('Y-m-d H:i'),
            ])
            ->values()
            ->all();

        return view('dept-head.dashboard', [
            'period' => $period,
            'kpis' => $kpis,
            'statusCounts' => $statusCounts,
            'trend' => [
                'labels' => $trendLabels,
                'series' => [
                    'submitted' => $trendSubmitted,
                    'rated' => $trendRated,
                ],
            ],
            'recentActivity' => $recentActivity,
        ]);
    }
}

