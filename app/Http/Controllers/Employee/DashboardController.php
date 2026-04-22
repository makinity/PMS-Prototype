<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $today = Carbon::today();

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();

        $currentIpcr = null;
        if ($activePeriod) {
            $currentIpcr = Ipcr::query()
                ->withCount('items')
                ->where('employee_id', $user->id)
                ->where('performance_period_id', $activePeriod->id)
                ->latest('id')
                ->first();
        }

        $orsPeriodBase = OrsEntry::query()
            ->with(['ipcrItem', 'monitoring'])
            ->withCount('evidences')
            ->where('employee_id', $user->id);

        if ($activePeriod) {
            $orsPeriodBase->where('performance_period_id', $activePeriod->id);
        }

        // Recent (today)
        $orsToday = (clone $orsPeriodBase)
            ->whereDate('work_date', $today)
            ->orderByDesc('started_at')
            ->limit(20)
            ->get();

        $runningEntry = (clone $orsPeriodBase)
            ->whereNotNull('started_at')
            ->whereNull('stopped_at')
            ->orderByDesc('started_at')
            ->first();

        $todayTotalSeconds = (clone $orsPeriodBase)
            ->whereDate('work_date', $today)
            ->sum('total_seconds');

        $todayTasksCount = (clone $orsPeriodBase)
            ->whereDate('work_date', $today)
            ->count();

        $todaySubmittedCount = (clone $orsPeriodBase)
            ->whereDate('work_date', $today)
            ->whereIn('status', ['submitted', 'rated'])
            ->count();

        $todayWithEvidenceCount = (clone $orsPeriodBase)
            ->whereDate('work_date', $today)
            ->whereHas('evidences')
            ->count();

        // Period stats
        $periodTotalSeconds = (clone $orsPeriodBase)->sum('total_seconds');
        $periodDraftCount = (clone $orsPeriodBase)->where('status', 'draft')->count();
        $periodSubmittedCount = (clone $orsPeriodBase)->where('status', 'submitted')->count();
        $periodRatedCount = (clone $orsPeriodBase)->where('status', 'rated')->count();

        $periodWithEvidenceCount = (clone $orsPeriodBase)
            ->whereHas('evidences')
            ->count();

        // Monitoring
        $needsRatingCount = (clone $orsPeriodBase)
            ->where('status', 'submitted')
            ->whereDoesntHave('monitoring')
            ->count();

        $monitoredCount = (clone $orsPeriodBase)
            ->whereHas('monitoring')
            ->count();

        // IPCR progress
        $distinctWorkedIpcrItems = 0;
        $ipcrItemsTotal = $currentIpcr?->items_count ?? 0;

        if ($currentIpcr) {
            $distinctWorkedIpcrItems = (clone $orsPeriodBase)
                ->whereNotNull('ipcr_item_id')
                ->distinct('ipcr_item_id')
                ->count('ipcr_item_id');
        }

        $ipcrProgressPct = $ipcrItemsTotal > 0
            ? (int) round(($distinctWorkedIpcrItems / $ipcrItemsTotal) * 100)
            : 0;

        // Integrity checks
        $integrity = [
            'has_running_timer' => (bool) $runningEntry,
            'running_timer_started_at' => $runningEntry?->started_at,
            'running_timer_age_seconds' => $runningEntry && $runningEntry->started_at
                ? Carbon::parse($runningEntry->started_at)->diffInSeconds(now())
                : 0,
            'has_negative_total_seconds' => (clone $orsPeriodBase)->where('total_seconds', '<', 0)->exists(),
            'has_started_but_no_total' => (clone $orsPeriodBase)
                ->whereNotNull('started_at')
                ->whereNotNull('stopped_at')
                ->where(function ($q) {
                    $q->whereNull('total_seconds')->orWhere('total_seconds', '=', 0);
                })
                ->exists(),
        ];

        /**
         * Chart datasets
         * 1) Today status counts
         * 2) Period status counts
         * 3) Last 7 days logged hours (active period scope)
         */
        $todayDraftCount = (clone $orsPeriodBase)
            ->whereDate('work_date', $today)
            ->where('status', 'draft')
            ->count();

        $todayRatedCount = (clone $orsPeriodBase)
            ->whereDate('work_date', $today)
            ->where('status', 'rated')
            ->count();

        // Last 7 days range (including today)
        $start7 = (clone $today)->subDays(6);

        $weeklyQuery = OrsEntry::query()
            ->where('employee_id', $user->id);

        if ($activePeriod) {
            $weeklyQuery->where('performance_period_id', $activePeriod->id);
        }

        $weeklyMap = $weeklyQuery
            ->whereDate('work_date', '>=', $start7)
            ->whereDate('work_date', '<=', $today)
            ->selectRaw('DATE(work_date) as d, COALESCE(SUM(total_seconds), 0) as secs')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('secs', 'd');

        $weeklyLabels = [];
        $weeklyHours = [];

        for ($i = 0; $i < 7; $i++) {
            $date = (clone $start7)->addDays($i);
            $key = $date->toDateString();
            $weeklyLabels[] = $date->format('M d');
            $secs = (int) ($weeklyMap[$key] ?? 0);
            $weeklyHours[] = round($secs / 3600, 2);
        }

        return view('employee.dashboard', [
            'user' => $user,
            'today' => $today,
            'activePeriod' => $activePeriod,
            'currentIpcr' => $currentIpcr,

            'orsToday' => $orsToday,
            'runningEntry' => $runningEntry,

            'todayTotalSeconds' => $todayTotalSeconds,
            'todayTasksCount' => $todayTasksCount,
            'todaySubmittedCount' => $todaySubmittedCount,
            'todayWithEvidenceCount' => $todayWithEvidenceCount,

            'periodTotalSeconds' => $periodTotalSeconds,
            'periodDraftCount' => $periodDraftCount,
            'periodSubmittedCount' => $periodSubmittedCount,
            'periodRatedCount' => $periodRatedCount,
            'periodWithEvidenceCount' => $periodWithEvidenceCount,

            'needsRatingCount' => $needsRatingCount,
            'monitoredCount' => $monitoredCount,

            'distinctWorkedIpcrItems' => $distinctWorkedIpcrItems,
            'ipcrItemsTotal' => $ipcrItemsTotal,
            'ipcrProgressPct' => $ipcrProgressPct,

            'integrity' => $integrity,

            // Chart data
            'todayDraftCount' => $todayDraftCount,
            'todayRatedCount' => $todayRatedCount,
            'weeklyLabels' => $weeklyLabels,
            'weeklyHours' => $weeklyHours,
        ]);
    }
}
