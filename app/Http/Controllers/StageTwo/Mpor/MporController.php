<?php

namespace App\Http\Controllers\StageTwo\Mpor;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Mpor;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
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
        $officeName = optional($user->office)->name ?? '--';

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

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $ipcrQuery = Ipcr::query()
            ->with([
                'office:id,name',
                'employee:id,name,office_id',
                'performancePeriod:id,name,start_date,end_date',
                'items:id,ipcr_id,output_title,function_type,indicator_text,standards_payload',
            ])
            ->where('employee_id', $user->id)
            ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT]);

        if ($activePeriod) {
            $ipcrQuery->where('performance_period_id', $activePeriod->id);
        }

        $ipcr = $ipcrQuery
            ->orderByDesc('generated_at')
            ->orderByDesc('id')
            ->first();

        if (!$ipcr) {
            $ipcr = Ipcr::query()
                ->with([
                    'office:id,name',
                    'employee:id,name,office_id',
                    'performancePeriod:id,name,start_date,end_date',
                    'items:id,ipcr_id,output_title,function_type,indicator_text,standards_payload',
                ])
                ->where('employee_id', $user->id)
                ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT])
                ->orderByDesc('generated_at')
                ->orderByDesc('id')
                ->first();
        }

        $normalizeOutputKey = static function (string $outputTitle): string {
            return mb_strtolower(
                trim((string) preg_replace('/\s+/', ' ', $outputTitle))
            );
        };

        $mporRows = [];

        if ($ipcr) {
            foreach ($ipcr->items as $item) {
                $outputTitle = trim((string) ($item->output_title ?? ''));
                if ($outputTitle === '') {
                    continue;
                }

                $outputKey = $normalizeOutputKey($outputTitle);
                if ($outputKey === '') {
                    continue;
                }

                $functionType = strtolower(trim((string) ($item->function_type ?? '')));
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
            }
        }

        $ratedEntries = collect();

        if ($ipcr) {
            $ratedEntries = OrsEntry::query()
                ->with([
                    'ipcrItem:id,output_title,function_type',
                    'monitoring:ors_entry_id,quality_rating,timeliness_rating',
                ])
                ->where('employee_id', $user->id)
                ->where('ipcr_id', $ipcr->id)
                ->where('status', 'rated')
                ->where('quantity', '>', 0)
                ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                ->whereHas('monitoring', function ($q) {
                    $q->whereNotNull('quality_rating')
                        ->whereNotNull('timeliness_rating');
                })
                ->orderBy('work_date')
                ->get();
        }

        $orsTasks = $ratedEntries->map(function (OrsEntry $entry) use ($normalizeOutputKey) {
            $outputTitle = trim((string) data_get($entry, 'ipcrItem.output_title', ''));
            $outputKey = $normalizeOutputKey($outputTitle);

            return [
                'id' => 'ors-' . $entry->id,
                'date' => (string) $entry->work_date,
                'uwpOutputId' => $outputKey,
                'uwpOutputLabel' => $outputTitle === '' ? '--' : $outputTitle,
                'quantityValue' => is_numeric($entry->quantity) ? (float) $entry->quantity : 0,
                'quantityLabel' => is_numeric($entry->quantity) ? (string) $entry->quantity : '--',
                'state' => 'rated',
                'supervisorQuality' => data_get($entry, 'monitoring.quality_rating'),
                'supervisorTimeliness' => data_get($entry, 'monitoring.timeliness_rating'),
                'functionType' => (string) data_get($entry, 'ipcrItem.function_type', ''),
                'outputKey' => $outputKey,
            ];
        })->values()->all();

        $includedRatedTasks = $orsTasks;

        foreach ($includedRatedTasks as $task) {
            $outputKey = (string) ($task['outputKey'] ?? '');
            if ($outputKey === '' || !isset($mporRows[$outputKey])) {
                continue;
            }

            $day = (int) Carbon::parse((string) $task['date'])->format('j');
            $week = $day <= 7 ? 1 : ($day <= 14 ? 2 : ($day <= 21 ? 3 : 4));

            $qty = (float) ($task['quantityValue'] ?? 0);
            if ($qty <= 0) {
                continue;
            }

            $qualityRating = (float) ($task['supervisorQuality'] ?? 0);
            $timelinessRating = (float) ($task['supervisorTimeliness'] ?? 0);

            $mporRows[$outputKey]['qty'][$week] += $qty;
            $mporRows[$outputKey]['qual'][$week] += ($qty * $qualityRating);
            $mporRows[$outputKey]['time'][$week] += ($qty * $timelinessRating);
        }

        $sectionRows = [
            'core' => [],
            'support' => [],
        ];

        foreach (array_values($mporRows) as $row) {
            $row['qtyTotal'] = array_sum($row['qty']);
            $row['qualTotal'] = array_sum($row['qual']);
            $row['timeTotal'] = array_sum($row['time']);

            if ($row['qtyTotal'] <= 0) {
                continue;
            }

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

        return view('employee.mpor', compact(
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

    public function submitMpor(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && $user->role === 'employee', 403);

        $month = $request->input('month');

        if (!is_string($month) || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            return redirect()
                ->route('employee.mpor.index')
                ->with('info', 'Invalid month selected.');
        }

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // Get active IPCR (same logic as index)
        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $ipcrQuery = Ipcr::query()
            ->where('employee_id', $user->id)
            ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT]);

        if ($activePeriod) {
            $ipcrQuery->where('performance_period_id', $activePeriod->id);
        }

        $ipcr = $ipcrQuery
            ->orderByDesc('generated_at')
            ->orderByDesc('id')
            ->first();

        if (!$ipcr) {
            return redirect()
                ->route('employee.mpor.index', ['month' => $month])
                ->with('info', 'No active IPCR found.');
        }

        // Ensure there are rated ORS entries for this month
        $hasRatedEntries = OrsEntry::query()
            ->where('employee_id', $user->id)
            ->where('ipcr_id', $ipcr->id)
            ->where('status', 'rated')
            ->where('quantity', '>', 0)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('monitoring', function ($q) {
                $q->whereNotNull('quality_rating')
                ->whereNotNull('timeliness_rating');
            })
            ->exists();

        if (! $hasRatedEntries) {
            return redirect()
                ->route('employee.mpor.index', ['month' => $month])
                ->with('info', 'No rated ORS entries found for this month.');
        }

        $mpor = Mpor::query()
            ->where('employee_id', $user->id)
            ->where('month', $month)
            ->first();

        if ($mpor && in_array($mpor->status, ['submitted', 'endorsed', 'approved'], true)) {
            return redirect()
                ->route('employee.mpor.index', ['month' => $month])
                ->with('info', 'MPOR already submitted.');
        }

        if (! $mpor) {
            $mpor = new Mpor();
            $mpor->employee_id = $user->id;
            $mpor->office_id = $user->office_id;
            $mpor->month = $month;
            $mpor->generated_at = now();
            $mpor->created_by = $user->id;
        }

        $mpor->status = 'submitted';
        $mpor->submitted_at = now();
        $mpor->save();

        return redirect()
            ->route('employee.mpor', ['month' => $month])
            ->with('success', 'MPOR successfully submitted.');
    }
}
