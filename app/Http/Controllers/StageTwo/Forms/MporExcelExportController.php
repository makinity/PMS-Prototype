<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Exports\StageTwo\MporExcelExport;
use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class MporExcelExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $ipcr = $this->resolveEmployeeIpcr($request);
        $month = trim((string) $request->input('month', ''));
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = now()->format('Y-m');
        }

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $catalog = [
            'core' => [],
            'support' => [],
        ];

        foreach ($ipcr->items as $item) {
            $outputTitle = trim((string) ($item->output_title ?? ''));
            if ($outputTitle === '') {
                continue;
            }

            $functionType = strtolower(trim((string) ($item->function_type ?? '')));
            $section = str_contains($functionType, 'support') ? 'support' : 'core';
            $outputKey = $this->normalizeOutputKey($outputTitle);

            if (!isset($catalog[$section][$outputKey])) {
                $catalog[$section][$outputKey] = [
                    'label' => $outputTitle,
                    'weeks' => [
                        1 => ['qty' => 0, 'q_points' => 0, 't_points' => 0],
                        2 => ['qty' => 0, 'q_points' => 0, 't_points' => 0],
                        3 => ['qty' => 0, 'q_points' => 0, 't_points' => 0],
                        4 => ['qty' => 0, 'q_points' => 0, 't_points' => 0],
                    ],
                ];
            }
        }

        $entries = OrsEntry::query()
            ->with([
                'ipcrItem:id,output_title,function_type',
                'monitoring:ors_entry_id,quality_rating,timeliness_rating',
            ])
            ->where('employee_id', $ipcr->employee_id)
            ->where('ipcr_id', $ipcr->id)
            ->where('status', 'rated')
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('monitoring', function ($q) {
                $q->whereNotNull('quality_rating')
                    ->whereNotNull('timeliness_rating');
            })
            ->where('quantity', '>', 0)
            ->orderBy('work_date')
            ->get();

        foreach ($entries as $entry) {
            $outputTitle = trim((string) data_get($entry, 'ipcrItem.output_title', ''));
            if ($outputTitle === '') {
                continue;
            }

            $functionType = strtolower(trim((string) data_get($entry, 'ipcrItem.function_type', '')));
            $section = str_contains($functionType, 'support') ? 'support' : 'core';
            $outputKey = $this->normalizeOutputKey($outputTitle);

            if (!isset($catalog[$section][$outputKey])) {
                continue;
            }

            $day = Carbon::parse((string) $entry->work_date)->day;
            $week = $day <= 7 ? 1 : ($day <= 14 ? 2 : ($day <= 21 ? 3 : 4));

            $qty = is_numeric($entry->quantity) ? (float) $entry->quantity : 0.0;
            if ($qty <= 0) {
                continue;
            }

            $qRating = (float) data_get($entry, 'monitoring.quality_rating', 0);
            $tRating = (float) data_get($entry, 'monitoring.timeliness_rating', 0);

            $catalog[$section][$outputKey]['weeks'][$week]['qty'] += $qty;
            $catalog[$section][$outputKey]['weeks'][$week]['q_points'] += $qty * $qRating;
            $catalog[$section][$outputKey]['weeks'][$week]['t_points'] += $qty * $tRating;
        }

        $catalog = [
            'core' => array_filter($catalog['core'], function (array $row): bool {
                return collect($row['weeks'] ?? [])->sum('qty') > 0;
            }),
            'support' => array_filter($catalog['support'], function (array $row): bool {
                return collect($row['weeks'] ?? [])->sum('qty') > 0;
            }),
        ];

        $monthYear = trim((string) $request->input('month_year', ''));
        if ($monthYear === '') {
            $monthYear = $startDate->format('F Y');
        }

        $payload = [
            'employee' => (string) ($ipcr->employee?->name ?? 'Employee'),
            'office' => (string) ($ipcr->office?->name ?? 'Office'),
            'month_year' => $monthYear,
            'supervisor' => 'Supervisor',
            'core' => array_values($catalog['core']),
            'support' => array_values($catalog['support']),
            'attendance' => [
                'absence' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
                'tardiness' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 'total' => 0],
            ],
        ];

        return Excel::download(
            new MporExcelExport($payload),
            $this->buildFilename($payload)
        );
    }

    private function resolveEmployeeIpcr(Request $request): Ipcr
    {
        $user = $request->user();
        abort_unless($user, 403);

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $query = Ipcr::query()
            ->with([
                'employee:id,name,office_id',
                'office:id,name',
                'performancePeriod:id,name,start_date,end_date',
                'items:id,ipcr_id,output_title,function_type,indicator_text',
            ])
            ->where('employee_id', $user->id)
            ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT]);

        if ($activePeriod) {
            $query->where('performance_period_id', $activePeriod->id);
        }

        $ipcr = $query
            ->orderByDesc('generated_at')
            ->orderByDesc('id')
            ->first();

        if (!$ipcr && $activePeriod) {
            $ipcr = Ipcr::query()
                ->with([
                    'employee:id,name,office_id',
                    'office:id,name',
                    'performancePeriod:id,name,start_date,end_date',
                    'items:id,ipcr_id,output_title,function_type,indicator_text',
                ])
                ->where('employee_id', $user->id)
                ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT])
                ->orderByDesc('generated_at')
                ->orderByDesc('id')
                ->first();
        }

        abort_if(!$ipcr, 404, 'No IPCR found for export.');

        return $ipcr;
    }

    private function buildFilename(array $payload): string
    {
        $employee = Str::slug((string) ($payload['employee'] ?? 'Employee'), '_');
        $office = Str::slug((string) ($payload['office'] ?? 'Office'), '_');
        $monthYear = Str::slug((string) ($payload['month_year'] ?? 'Month_Year'), '_');

        return "MPOR_{$employee}_{$office}_{$monthYear}.xlsx";
    }

    private function normalizeOutputKey(string $outputTitle): string
    {
        return mb_strtolower(
            trim((string) preg_replace('/\s+/', ' ', $outputTitle))
        );
    }
}
