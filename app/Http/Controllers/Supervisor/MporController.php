<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Mpor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MporController extends Controller
{
    public function index(Request $request)
    {
        $supervisor = $request->user();
        abort_unless($supervisor && $supervisor->role === 'supervisor', 403);

        $selectedEmployeeId = (int) $request->query('employee_id', 0);
        $month = (string) $request->query('month', now()->format('Y-m'));
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        try {
            $monthCarbon = Carbon::createFromFormat('Y-m', $month);
            $monthLabel = $monthCarbon->format('F Y');
            $startDate = $monthCarbon->copy()->startOfMonth()->toDateString();
            $endDate = $monthCarbon->copy()->endOfMonth()->toDateString();
        } catch (\Throwable $e) {
            $monthLabel = $month;
        }

        $query = Mpor::query()
            ->select('mpors.*')
            ->with(['employee:id,name,office_id', 'employee.office:id,name'])
            ->selectSub(function ($subQuery) use ($startDate, $endDate) {
                $subQuery->from('ors_entries')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('ors_entries.employee_id', 'mpors.employee_id')
                    ->where('ors_entries.status', 'rated')
                    ->where('ors_entries.quantity', '>', 0)
                    ->whereBetween('ors_entries.work_date', [$startDate, $endDate])
                    ->whereExists(function ($exists) {
                        $exists->select(DB::raw(1))
                            ->from('ors_entry_monitorings')
                            ->whereColumn('ors_entry_monitorings.ors_entry_id', 'ors_entries.id')
                            ->whereNotNull('ors_entry_monitorings.quality_rating')
                            ->whereNotNull('ors_entry_monitorings.timeliness_rating');
                    });
            }, 'rated_ors_entries_count')
            ->whereNotNull('employee_id')
            ->where('month', $month)
            ->whereExists(function ($q) use ($supervisor) {
                $q->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'mpors.employee_id')
                    ->where('users.role', 'employee')
                    ->where('users.office_id', $supervisor->office_id);
            })
            // sensible scope: supervisor sees MPORs in same office
            ->whereHas('employee', function ($q) use ($supervisor) {
                $q->where('office_id', $supervisor->office_id)
                  ->where('role', 'employee');
            })
            // show only meaningful statuses (adjust if you want draft visible)
            ->whereIn('status', ['submitted', 'approved', 'endorsed'])
            ->orderByRaw("FIELD(status,'submitted','approved','endorsed')")
            ->orderByDesc('generated_at')
            ->orderByDesc('id');

        if ($selectedEmployeeId > 0) {
            $query->where('mpors.employee_id', $selectedEmployeeId);
        }

        $mpors = $query->get();

        return view('supervisor.mpor', compact(
            'mpors',
            'selectedEmployeeId',
            'month',
            'monthLabel'
        ));
    }

    /**
     * JSON endpoint: modal preview loads rated entries + summary.
     */
    public function show(Request $request, Mpor $mpor)
    {
        $supervisor = $request->user();
        abort_unless($supervisor && $supervisor->role === 'supervisor', 403);

        $mpor->load(['employee.office']);
        abort_unless($mpor->employee, 404);

        // security: only allow MPORs within supervisor office
        abort_unless((int) ($mpor->employee?->office_id ?? 0) === (int) $supervisor->office_id, 403);

        // month parsing
        $month = (string) $mpor->month;
        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid MPOR month.'], 422);
        }
        $end = $start->copy()->endOfMonth();

        $mporMonthYear = $start->format('F Y');
        $employeeName = $mpor->employee?->name ?? '--';
        $officeName = $mpor->employee?->office?->name ?? '--';

        $sectionLabels = [
            'core' => 'Core Functions (80%)',
            'support' => 'Support Functions (20%)',
        ];

        // Fetch rated entries INCLUDED in MPOR computation:
        // - status rated
        // - quantity > 0
        // - date range
        // - monitoring ratings exist
        // - must have ipcrItem (output_title + function_type)
        $ratedEntries = \App\Models\OrsEntry::query()
            ->with([
                'ipcrItem:id,output_title,function_type,indicator_text',
                'monitoring:ors_entry_id,quality_rating,timeliness_rating',
            ])
            ->where('employee_id', $mpor->employee_id)
            ->where('status', 'rated')
            ->where('quantity', '>', 0)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('ipcrItem', function ($q) {
                $q->whereNotNull('output_title');
            })
            ->whereHas('monitoring', function ($q) {
                $q->whereNotNull('quality_rating')->whereNotNull('timeliness_rating');
            })
            ->orderBy('work_date')
            ->get();

        // Excluded entries (for KPI): count all month ORS that could have been included but aren't
        $allMonthEntriesCount = \App\Models\OrsEntry::query()
            ->where('employee_id', $mpor->employee_id)
            ->where('quantity', '>', 0)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->count();

        $includedCount = $ratedEntries->count();
        $excludedCount = max($allMonthEntriesCount - $includedCount, 0);

        // Normalize output key (same style as your employee controller)
        $normalizeOutputKey = static function (string $outputTitle): string {
            return mb_strtolower(
                trim((string) preg_replace('/\s+/', ' ', $outputTitle))
            );
        };

        // Build MPOR rows per output_title
        $mporRows = [];

        foreach ($ratedEntries as $entry) {
            $outputTitle = trim((string) data_get($entry, 'ipcrItem.output_title', ''));
            if ($outputTitle === '') {
                continue;
            }

            $outputKey = $normalizeOutputKey($outputTitle);
            if ($outputKey === '') {
                continue;
            }

            $functionType = strtolower(trim((string) data_get($entry, 'ipcrItem.function_type', '')));
            $section = str_contains($functionType, 'support') ? 'support' : 'core';

            if (!isset($mporRows[$outputKey])) {
                $mporRows[$outputKey] = [
                    'id' => $outputKey,
                    'label' => $outputTitle,
                    'section' => $section,
                    'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'qtyTotal' => 0,
                    'qualTotal' => 0,
                    'timeTotal' => 0,
                ];
            }

            $day = (int) Carbon::parse((string) $entry->work_date)->format('j');
            $week = $day <= 7 ? 1 : ($day <= 14 ? 2 : ($day <= 21 ? 3 : 4));

            $qty = is_numeric($entry->quantity) ? (float) $entry->quantity : 0;
            if ($qty <= 0) {
                continue;
            }

            $qRating = (float) data_get($entry, 'monitoring.quality_rating', 0);
            $tRating = (float) data_get($entry, 'monitoring.timeliness_rating', 0);

            // MPOR logic: points = qty * rating (Q/T)
            $mporRows[$outputKey]['qty'][$week] += $qty;
            $mporRows[$outputKey]['qual'][$week] += ($qty * $qRating);
            $mporRows[$outputKey]['time'][$week] += ($qty * $tRating);
        }

        $sectionRows = ['core' => [], 'support' => []];

        foreach (array_values($mporRows) as $row) {
            $row['qtyTotal'] = array_sum($row['qty']);
            $row['qualTotal'] = array_sum($row['qual']);
            $row['timeTotal'] = array_sum($row['time']);

            if ($row['qtyTotal'] <= 0) {
                continue;
            }

            $sectionRows[$row['section'] === 'support' ? 'support' : 'core'][] = $row;
        }

        // Grand totals
        $grandTotals = [
            'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'qtyTotal' => 0,
            'qualTotal' => 0,
            'timeTotal' => 0,
        ];

        foreach ($sectionRows as $rows) {
            foreach ($rows as $row) {
                foreach ([1, 2, 3, 4] as $week) {
                    $grandTotals['qty'][$week] += (float) $row['qty'][$week];
                    $grandTotals['qual'][$week] += (float) $row['qual'][$week];
                    $grandTotals['time'][$week] += (float) $row['time'][$week];
                }
            }
        }

        $grandTotals['qtyTotal'] = array_sum($grandTotals['qty']);
        $grandTotals['qualTotal'] = array_sum($grandTotals['qual']);
        $grandTotals['timeTotal'] = array_sum($grandTotals['time']);

        return response()->json([
            'meta' => [
                'mpor_id' => $mpor->id,
                'status' => $mpor->status,
                'month' => $mpor->month,
                'monthLabel' => $mporMonthYear,
                'employeeName' => $employeeName,
                'officeName' => $officeName,
                'supervisorName' => $supervisor->name ?? '--',
            ],
            'sectionLabels' => $sectionLabels,
            'sectionRows' => $sectionRows,
            'grandTotals' => $grandTotals,
            'kpis' => [
                'includedRated' => $includedCount,
                'excluded' => $excludedCount,
            ],
        ]);
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

        return back();
    }
}
