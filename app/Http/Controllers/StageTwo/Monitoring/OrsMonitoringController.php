<?php

namespace App\Http\Controllers\StageTwo\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\OrsEntry;
use App\Models\OrsEntryMonitoring;
use App\Models\User;
use Illuminate\Http\Request;

class OrsMonitoringController extends Controller
{
    public function index()
    {
        $supervisor = $this->authorizedSupervisor();
        $submittedEntries = $this->submittedEntriesForSupervisor($supervisor);

        return view('supervisor.ors-monitoring', compact('submittedEntries'));
    }

    public function show(OrsEntry $orsEntry)
    {
        $supervisor = $this->authorizedSupervisor();

        $orsEntry->load([
            'employee:id,name,office_id',
            'employee.office:id,name',
            'ipcrItem:id,output_title,indicator_text,standards_payload',
            'evidences',
            'monitoring' => fn ($q) => $q->where('supervisor_id', $supervisor->id),
        ]);

        abort_unless(
            $orsEntry->employee && (int) $orsEntry->employee->office_id === (int) $supervisor->office_id,
            403
        );

        $isSubmitted = method_exists($orsEntry, 'isSubmitted')
            ? $orsEntry->isSubmitted()
            : strtolower((string) $orsEntry->status) === 'submitted';

        $submittedEntries = $this->submittedEntriesForSupervisor($supervisor);

        return view('supervisor.ors-monitoring', compact('orsEntry', 'isSubmitted', 'submittedEntries'));
    }

    public function store(Request $request, OrsEntry $orsEntry)
    {
        $supervisor = $this->authorizedSupervisor();

        $orsEntry->load('employee');
        abort_unless(
            $orsEntry->employee && (int) $orsEntry->employee->office_id === (int) $supervisor->office_id,
            403
        );

        $status = strtolower((string) $orsEntry->status);
        $rateable = in_array($status, ['submitted', 'rated'], true);

        if (!$rateable) {
            return back()->with('error', 'Rating is allowed only for submitted OR rated ORS entries.');
        }

        $validated = $request->validate([
            'quality_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'timeliness_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        OrsEntryMonitoring::query()->updateOrCreate(
            [
                'ors_entry_id' => $orsEntry->id,
                'supervisor_id' => $supervisor->id,
            ],
            [
                'quality_rating' => (int) $validated['quality_rating'],
                'timeliness_rating' => (int) $validated['timeliness_rating'],
                'remarks' => $validated['remarks'] ?? null,

                'rated_at' => now(),
            ]
        );

        if ($orsEntry->status === 'submitted') {
            $orsEntry->update([
                'status' => 'rated',
            ]);
        }

        return back()->with('success', 'Rating saved successfully.');
    }

    private function authorizedSupervisor(): User
    {
        $user = auth()->user();
        abort_if(!$user || $user->role !== 'supervisor', 403);

        return $user;
    }

    private function submittedEntriesForSupervisor(User $supervisor)
    {
        return OrsEntry::query()
            ->with([
                'employee:id,name,office_id',
                'employee.office:id,name',
                'ipcrItem:id,output_title,indicator_text,standards_payload',
                'monitoring' => fn ($q) => $q->where('supervisor_id', $supervisor->id),
            ])
            ->withCount('evidences')
            ->whereIn('status', ['submitted', 'rated'])
            ->whereHas('employee', function ($query) use ($supervisor) {
                $query->where('office_id', $supervisor->office_id);
            })
            ->orderByDesc('work_date')
            ->orderByDesc('id')
            ->get();
    }
}
