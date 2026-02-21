<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mpor;
use App\Models\User;
use Illuminate\Support\Carbon;

class MporController extends Controller
{
    function index(Request $request)
    {
        $supervisor = $request->user();

        // Filters
        $selectedEmployeeId = (int) $request->query('employee_id', 0);
        $month = (string) $request->query('month', now()->format('Y-m'));

        // Month label
        try {
            $monthLabel = Carbon::createFromFormat('Y-m', $month)->format('F Y');
        } catch (\Throwable $e) {
            $monthLabel = $month;
        }

        // Team employees (adjust your team logic here)
        // If you don’t have supervisor_id, swap this to your real selection logic.
        $teamEmployees = User::query()
            ->where('office_id', $supervisor->office_id) // sensible default
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        $mpor = null;
        $officeLabel = '—';

        $summary = [
            'entry_count' => 0,
            'days_count' => 0,
            'sum_quantity' => 0,
            'avg_quality' => null,
            'avg_timeliness' => null,
        ];

        $attachedEntries = collect();

        if ($selectedEmployeeId > 0) {
            $mpor = Mpor::query()
                ->with([
                    'employee.office',
                    // load rated entries with their needed relations
                    'ratedOrsEntries.ipcrItem',
                    'ratedOrsEntries.monitoring',
                ])
                ->withCount([
                    // counts evidences per ORS entry
                    'ratedOrsEntries as rated_entries_count',
                ])
                ->where('employee_id', $selectedEmployeeId)
                ->where('month', $month)
                ->first();

            if ($mpor) {
                $officeLabel =
                    $mpor->employee?->office?->name
                    ?? ($mpor->employee?->office_id ? ('Office #' . $mpor->employee->office_id) : 'Unassigned Office');

                // IMPORTANT: also eager-load evidence counts
                $orsEntries = $mpor->ratedOrsEntries()
                    ->with(['ipcrItem', 'monitoring'])
                    ->withCount('evidences')
                    ->orderBy('work_date')
                    ->get();

                // Format rows for Blade (display-ready)
                $attachedEntries = $orsEntries->map(function ($entry) {
                    $workDateLabel = '—';
                    if (!empty($entry->work_date)) {
                        try {
                            $workDateLabel = Carbon::parse($entry->work_date)->format('M d, Y');
                        } catch (\Throwable $e) {
                            $workDateLabel = (string) $entry->work_date;
                        }
                    }

                    $outputTitle = $entry->ipcrItem?->output_title ?? '—';
                    $indicator = $entry->ipcrItem?->indicator_text ?? '';
                    $taskText = trim($outputTitle . ($indicator ? ' — ' . $indicator : ''));

                    return [
                        'id' => $entry->id,
                        'work_date_label' => $workDateLabel,
                        'task_text' => $taskText,
                        'quantity_label' => is_null($entry->quantity) ? '—' : (string) $entry->quantity,
                        'quality_label' => $entry->monitoring?->quality_rating ?? '--',
                        'timeliness_label' => $entry->monitoring?->timeliness_rating ?? '--',
                        'evidence_count' => (int) ($entry->evidences_count ?? 0),
                    ];
                });

                // Summary (derived from rated entries)
                $summary['entry_count'] = $orsEntries->count();

                $summary['days_count'] = $orsEntries
                    ->pluck('work_date')
                    ->filter()
                    ->map(fn ($d) => Carbon::parse($d)->toDateString())
                    ->unique()
                    ->count();

                $summary['sum_quantity'] = $orsEntries
                    ->pluck('quantity')
                    ->filter(fn ($q) => is_numeric($q))
                    ->sum();

                $summary['avg_quality'] = $orsEntries
                    ->map(fn ($e) => $e->monitoring?->quality_rating)
                    ->filter(fn ($v) => is_numeric($v))
                    ->avg();

                $summary['avg_timeliness'] = $orsEntries
                    ->map(fn ($e) => $e->monitoring?->timeliness_rating)
                    ->filter(fn ($v) => is_numeric($v))
                    ->avg();
            }
        }

        return view('supervisor.mpor.index', compact(
            'teamEmployees',
            'selectedEmployeeId',
            'month',
            'monthLabel',
            'mpor',
            'officeLabel',
            'summary',
            'attachedEntries'
        ));
    }

    public function approve(Mpor $mpor)
    {
        if ($mpor->status !== 'submitted') {
            return back()->with('error', 'MPOR cannot be approved.');
        }

        $mpor->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'MPOR approved successfully.');
    }

    public function endorse(Mpor $mpor)
    {
        if ($mpor->status !== 'approved') {
            return back()->with('error', 'MPOR must be approved first.');
        }

        $mpor->update([
            'status' => 'endorsed',
            'endorsed_at' => now(),
            'endorsed_by' => auth()->id(),
        ]);

        return back()->with('success', 'MPOR endorsed to Department Head.');
    }
}
