<?php

namespace App\Http\Controllers\StageTwo\Mpor;

use App\Http\Controllers\Controller;
use App\Models\Mpor;
use App\Models\OrsEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MporController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && $user->role === 'employee', 403);

        $month = $request->query('month');
        if (!is_string($month) || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = now()->format('Y-m');
        }

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $mporMonthYear = $start->format('F Y');
        $employeeName = $user->name;
        $officeName = optional($user->office)->name ?? '—';

        $mpor = Mpor::query()
            ->where('employee_id', $user->id)
            ->where('month', $month)
            ->first();

        $mporStatus = $mpor?->status ?? 'draft';
        $isMporLocked = in_array($mporStatus, ['submitted', 'endorsed', 'approved'], true);

        $sectionLabels = [
            'core' => 'Core Functions (80%)',
            'support' => 'Support Functions (20%)',
        ];

        $ratedEntries = OrsEntry::query()
            ->with(['monitoring', 'ipcrItem'])
            ->where('employee_id', $user->id)
            ->where('status', 'rated')
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('monitoring', function ($q) {
                $q->whereNotNull('quality_rating')
                    ->whereNotNull('timeliness_rating');
            })
            ->orderBy('work_date')
            ->get();

        $orsTasks = $ratedEntries->map(function (OrsEntry $entry) {
            return [
                'id' => 'ors-' . $entry->id,
                'date' => (string) $entry->work_date,
                'uwpOutputId' => (int) $entry->ipcr_item_id,
                'uwpOutputLabel' => (string) ($entry->ipcrItem?->output_title ?? '—'),
                'quantityValue' => is_numeric($entry->quantity) ? (float) $entry->quantity : 0,
                'quantityLabel' => is_numeric($entry->quantity) ? (string) $entry->quantity : '--',
                'state' => 'rated',
                'supervisorQuality' => data_get($entry, 'monitoring.quality_rating'),
                'supervisorTimeliness' => data_get($entry, 'monitoring.timeliness_rating'),
                'functionType' => (string) ($entry->ipcrItem?->function_type ?? ''),
            ];
        })->values()->all();

        $includedRatedTasks = $orsTasks;

        $mporRowsByItem = [];
        foreach ($includedRatedTasks as $task) {
            $rowId = (int) ($task['uwpOutputId'] ?? 0);
            if ($rowId <= 0) {
                continue;
            }

            if (!isset($mporRowsByItem[$rowId])) {
                $functionType = strtolower(trim((string) ($task['functionType'] ?? '')));
                $section = str_contains($functionType, 'support') ? 'support' : 'core';

                $mporRowsByItem[$rowId] = [
                    'id' => $rowId,
                    'label' => (string) ($task['uwpOutputLabel'] ?? '—'),
                    'section' => $section,
                    'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'qtyTotal' => 0,
                    'qualTotal' => 0,
                    'timeTotal' => 0,
                ];
            }

            $day = (int) Carbon::parse($task['date'])->format('j');
            $week = $day <= 7 ? 1 : ($day <= 14 ? 2 : ($day <= 21 ? 3 : 4));

            $qty = (float) ($task['quantityValue'] ?? 0);
            $qualityRating = (float) ($task['supervisorQuality'] ?? 0);
            $timelinessRating = (float) ($task['supervisorTimeliness'] ?? 0);

            $mporRowsByItem[$rowId]['qty'][$week] += $qty;
            $mporRowsByItem[$rowId]['qual'][$week] += ($qty * $qualityRating);
            $mporRowsByItem[$rowId]['time'][$week] += ($qty * $timelinessRating);
        }

        $sectionRows = [
            'core' => [],
            'support' => [],
        ];

        foreach (array_values($mporRowsByItem) as $row) {
            $row['qtyTotal'] = array_sum($row['qty']);
            $row['qualTotal'] = array_sum($row['qual']);
            $row['timeTotal'] = array_sum($row['time']);

            $sectionRows[$row['section'] === 'support' ? 'support' : 'core'][] = $row;
        }

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
                    $grandTotals['qty'][$week] += $row['qty'][$week];
                    $grandTotals['qual'][$week] += $row['qual'][$week];
                    $grandTotals['time'][$week] += $row['time'][$week];
                }
            }
        }

        $grandTotals['qtyTotal'] = array_sum($grandTotals['qty']);
        $grandTotals['qualTotal'] = array_sum($grandTotals['qual']);
        $grandTotals['timeTotal'] = array_sum($grandTotals['time']);

        return view('employee.mpor.index', compact(
            'month',
            'start',
            'end',
            'mpor',
            'mporMonthYear',
            'employeeName',
            'officeName',
            'mporStatus',
            'isMporLocked',
            'orsTasks',
            'includedRatedTasks',
            'sectionRows',
            'grandTotals',
            'sectionLabels'
        ));
    }
}
