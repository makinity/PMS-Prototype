<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Active performance period (Stage II scope)
        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();

        // Determine supervisor’s office scope:
        // Prefer supervisedOffice (head_id), otherwise fallback to user's office_id.
        $officeId = $user->supervisedOffice?->id ?? $user->office_id;

        // Team = employees in the supervisor's office (exclude supervisor)
        $teamMembersQuery = User::query()
            ->where('office_id', $officeId)
            ->where('role', 'employee')
            ->where('is_active', true);

        $teamMemberIds = $teamMembersQuery->pluck('id')->all();
        $teamMembersCount = count($teamMemberIds);

        // Base ORS scope for team (active period, office)
        $orsTeamBase = OrsEntry::query()
            ->whereIn('employee_id', $teamMemberIds)
            ->when($officeId, fn ($q) => $q->where('office_id', $officeId))
            ->when($activePeriod, fn ($q) => $q->where('performance_period_id', $activePeriod->id));

        // Definitions (simple + consistent):
        // - Completed = status rated
        // - Pending validation = status submitted + no monitoring
        // - Overdue = status draft with work_date < today
        // - In progress = draft but not overdue (work_date >= today)
        $completedCount = (clone $orsTeamBase)->where('status', 'rated')->count();

        $pendingValidationCount = (clone $orsTeamBase)
            ->where('status', 'submitted')
            ->whereDoesntHave('monitoring')
            ->count();

        $overdueCount = (clone $orsTeamBase)
            ->where('status', 'draft')
            ->whereDate('work_date', '<', $today)
            ->count();

        $inProgressCount = (clone $orsTeamBase)
            ->where('status', 'draft')
            ->whereDate('work_date', '>=', $today)
            ->count();

        // "Active Tasks" = draft + submitted (regardless of monitoring)
        $activeTasksCount = (clone $orsTeamBase)
            ->whereIn('status', ['draft', 'submitted'])
            ->count();

        // Weekly team output: last 7 days (including today)
        // Metric = count of submitted/rated entries per day
        $start7 = (clone $today)->subDays(6);

        $weeklyRaw = OrsEntry::query()
            ->whereIn('employee_id', $teamMemberIds)
            ->when($officeId, fn ($q) => $q->where('office_id', $officeId))
            ->when($activePeriod, fn ($q) => $q->where('performance_period_id', $activePeriod->id))
            ->whereDate('work_date', '>=', $start7)
            ->whereDate('work_date', '<=', $today)
            ->whereIn('status', ['submitted', 'rated'])
            ->selectRaw('DATE(work_date) as d, COUNT(*) as c')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd'); // [YYYY-MM-DD => count]

        $weeklyLabels = [];
        $weeklyCounts = [];

        for ($i = 0; $i < 7; $i++) {
            $date = (clone $start7)->addDays($i);
            $key = $date->toDateString();

            $weeklyLabels[] = $date->format('D'); // Mon, Tue, ...
            $weeklyCounts[] = (int) ($weeklyRaw[$key] ?? 0);
        }

        // Chart 1: Task Status Distribution (Completed / In Progress / Pending / Overdue)
        $taskStatusLabels = ['Completed', 'In Progress', 'Pending', 'Overdue'];
        $taskStatusData = [$completedCount, $inProgressCount, $pendingValidationCount, $overdueCount];

        return view('supervisor.dashboard', [
            'user' => $user,
            'today' => $today,
            'activePeriod' => $activePeriod,

            'officeId' => $officeId,
            'teamMembersCount' => $teamMembersCount,
            'activeTasksCount' => $activeTasksCount,
            'pendingValidationCount' => $pendingValidationCount,
            'overdueCount' => $overdueCount,

            'taskStatusLabels' => $taskStatusLabels,
            'taskStatusData' => $taskStatusData,

            'weeklyLabels' => $weeklyLabels,
            'weeklyCounts' => $weeklyCounts,
        ]);
    }
}
