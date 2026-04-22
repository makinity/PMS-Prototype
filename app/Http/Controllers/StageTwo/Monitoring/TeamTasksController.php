<?php

namespace App\Http\Controllers\StageTwo\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\OrsEntry;
use App\Models\User;
use Illuminate\Http\Request;

class TeamTasksController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = $this->authorizedSupervisor();

        $teamEmployees = User::query()
            ->select(['id', 'name'])
            ->where('office_id', $supervisor->office_id)
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        $entriesQuery = OrsEntry::query()
            ->with([
                'employee:id,name,office_id',
                'ipcrItem',
                'monitoring',
            ])
            ->withCount('evidences')
            ->whereHas('employee', function ($query) use ($supervisor) {
                $query->where('office_id', $supervisor->office_id);
            })
            ->orderByDesc('work_date')
            ->orderByDesc('id');

        $status = trim((string) $request->query('status', ''));
        if ($status !== '' && strtolower($status) !== 'all') {
            $entriesQuery->where('status', $status);
        }

        $employeeId = $request->query('employee_id');
        if (!empty($employeeId)) {
            $entriesQuery->where('employee_id', (int) $employeeId);
        }

        $entries = $entriesQuery->paginate(15)->withQueryString();

        return view('supervisor.team-tasks', compact('entries', 'teamEmployees'));
    }

    private function authorizedSupervisor(): User
    {
        $user = auth()->user();
        abort_if(!$user || $user->role !== 'supervisor', 403);

        return $user;
    }
}
