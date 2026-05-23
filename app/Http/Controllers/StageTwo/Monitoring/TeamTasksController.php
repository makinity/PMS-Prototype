<?php

namespace App\Http\Controllers\StageTwo\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\OrsEntry;
use App\Models\User;
use App\Notifications\TaskReminderNotification;
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
                'employee:id,name,office_id,profile_photo_path',
                'ipcrItem',
                'monitoring.supervisor.office',
                'supervisor.office',
            ])
            ->withCount('evidences')
            ->whereHas('employee', function ($query) use ($supervisor) {
                $query->where('office_id', $supervisor->office_id);
            })
            ->orderByDesc('work_date')
            ->orderByDesc('id');

        $entries = $entriesQuery->paginate(12)->withQueryString();

        return view('supervisor.team-tasks', compact('entries', 'teamEmployees', 'supervisor'));
    }

    public function notify(OrsEntry $orsEntry)
    {
        $supervisor = $this->authorizedSupervisor();

        abort_if($orsEntry->supervisor_id !== $supervisor->id, 403);
        abort_if(in_array($orsEntry->status, ['submitted', 'rated']), 422);

        $orsEntry->load('employee');
        $orsEntry->employee->notify(new TaskReminderNotification($orsEntry, $supervisor->name));

        return response()->json(['message' => 'Notification sent to ' . $orsEntry->employee->name]);
    }

    private function authorizedSupervisor(): User
    {
        $user = auth()->user();
        abort_if(!$user || $user->role !== 'supervisor', 403);

        return $user;
    }
}
