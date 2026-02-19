<?php

namespace App\Http\Controllers\StageTwo\Commitement;

use App\Http\Controllers\Controller;
use App\Models\MyTask;
use App\Models\PerformancePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyTasksController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'employee') {
            abort(403, 'Unauthorized.');
        }

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $myTasksQuery = MyTask::query()
            ->with(['ipcrItem:id,output_title,indicator_text'])
            ->where('employee_id', $user->id)
            ->orderByDesc('work_date')
            ->orderByDesc('id');

        if ($activePeriod) {
            $myTasksQuery->where('performance_period_id', $activePeriod->id);
        }

        $myTasks = $myTasksQuery->get();

        return view('employee.my-task', [
            'myTasks' => $myTasks,
            'activePeriod' => $activePeriod,
            'employeeName' => $user->name,
        ]);
    }
}
