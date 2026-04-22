<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Office;
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
        $data = self::buildDashboardData();

        return view('admin.dashboard', compact('data'));
    }

    public static function buildDashboardData(): array
    {
        $period = PerformancePeriod::query()->where('is_active', true)->first();
        if (!$period) {
            $period = PerformancePeriod::query()->orderByDesc('start_date')->first();
        }

        $roles = ['admin', 'pmt', 'dept-head', 'supervisor', 'employee'];
        $roleCountsRaw = User::query()
            ->selectRaw('role, COUNT(*) as total')
            ->whereIn('role', $roles)
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();
        $roleCounts = collect($roles)
            ->mapWithKeys(fn (string $role) => [$role => (int) ($roleCountsRaw[$role] ?? 0)])
            ->toArray();

        $kpis = [
            'totalUsers' => User::query()->count(),
            'activeUsers' => User::query()->where('is_active', true)->count(),
            'totalOffices' => Office::query()->count(),
            'roles' => $roleCounts,
        ];

        $uwpStatusCounts = [];
        $opcrStatusCounts = [];
        $ipcrStatusCounts = [];
        $uwpTotal = 0;
        $opcrTotal = 0;
        $ipcrTotal = 0;
        $orsTotal = 0;
        $orsDraftCount = 0;
        $orsSubmittedCount = 0;
        $orsTrendLabels = [];
        $orsTrendCounts = [];

        if ($period) {
            $periodId = (int) $period->id;

            $uwpTotal = UnitWorkPlan::query()
                ->where('performance_period_id', $periodId)
                ->count();
            $uwpStatusCounts = UnitWorkPlan::query()
                ->where('performance_period_id', $periodId)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $opcrTotal = Opcr::query()
                ->where('performance_period_id', $periodId)
                ->count();
            $opcrStatusCounts = Opcr::query()
                ->where('performance_period_id', $periodId)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $ipcrTotal = Ipcr::query()
                ->where('performance_period_id', $periodId)
                ->count();
            $ipcrStatusCounts = Ipcr::query()
                ->where('performance_period_id', $periodId)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $orsPeriodBase = OrsEntry::query()->where('performance_period_id', $periodId);
            $orsTotal = (clone $orsPeriodBase)->count();
            $orsDraftCount = (clone $orsPeriodBase)
                ->whereRaw('LOWER(status) = ?', ['draft'])
                ->count();
            $orsSubmittedCount = (clone $orsPeriodBase)
                ->whereIn(DB::raw('LOWER(status)'), ['submitted', 'rated'])
                ->count();

            $today = Carbon::today();
            $start14 = $today->copy()->subDays(13);
            $trendRaw = (clone $orsPeriodBase)
                ->whereNotNull('work_date')
                ->whereBetween('work_date', [$start14->toDateString(), $today->toDateString()])
                ->selectRaw('DATE(work_date) as work_day, COUNT(*) as total')
                ->groupBy('work_day')
                ->orderBy('work_day')
                ->pluck('total', 'work_day')
                ->toArray();

            for ($cursor = $start14->copy(); $cursor->lte($today); $cursor->addDay()) {
                $key = $cursor->toDateString();
                $orsTrendLabels[] = $cursor->format('M d');
                $orsTrendCounts[] = (int) ($trendRaw[$key] ?? 0);
            }
        }

        return [
            'period' => $period,
            'kpis' => $kpis,
            'uwpStatusCounts' => $uwpStatusCounts,
            'opcrStatusCounts' => $opcrStatusCounts,
            'ipcrStatusCounts' => $ipcrStatusCounts,
            'uwpTotal' => $uwpTotal,
            'opcrTotal' => $opcrTotal,
            'ipcrTotal' => $ipcrTotal,
            'orsTotal' => $orsTotal,
            'orsDraftCount' => $orsDraftCount,
            'orsSubmittedCount' => $orsSubmittedCount,
            'orsTrendLabels' => $orsTrendLabels,
            'orsTrendCounts' => $orsTrendCounts,
        ];
    }
}
