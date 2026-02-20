<?php

namespace App\Http\Controllers\StageTwo\Forms;

use App\Exports\StageTwo\IpcrExcelExport;
use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class IpcrExcelExportController extends Controller
{
    public function exportExcel(Request $request)
    {
        $ipcrModel = $this->resolveEmployeeIpcr($request);
        $ipcr = $this->buildIpcr($ipcrModel);
        $standards = $this->buildStandardsFromIpcrOrFail($ipcrModel);
        $valuesByIndicator = $this->buildValuesByIndicator($ipcrModel);
        $meta = $this->buildMeta($request, $ipcrModel);

        return Excel::download(
            new IpcrExcelExport($ipcr, $standards, $valuesByIndicator),
            $this->buildFilename($meta, false)
        );
    }

    public function previewExcel(Request $request)
    {
        $ipcrModel = $this->resolveEmployeeIpcr($request);
        $ipcr = $this->buildIpcr($ipcrModel);
        $standards = $this->buildStandardsFromIpcrOrFail($ipcrModel);
        $valuesByIndicator = $this->buildValuesByIndicator($ipcrModel);
        $meta = $this->buildMeta($request, $ipcrModel);

        return Excel::download(
            new IpcrExcelExport($ipcr, $standards, $valuesByIndicator),
            $this->buildFilename($meta, true)
        );
    }

    private function buildMeta(Request $request, Ipcr $ipcr): array
    {
        $user = $request->user();
        $office = (string) ($ipcr->office?->name ?? $user?->office?->name ?? 'Office');

        $periodLabel = 'Period';
        if ($ipcr->performancePeriod) {
            $periodName = trim((string) ($ipcr->performancePeriod->name ?? ''));
            if ($periodName !== '') {
                $periodLabel = $periodName;
            } elseif ($ipcr->performancePeriod->start_date && $ipcr->performancePeriod->end_date) {
                $start = Carbon::parse($ipcr->performancePeriod->start_date)->format('M d, Y');
                $end = Carbon::parse($ipcr->performancePeriod->end_date)->format('M d, Y');
                $periodLabel = "{$start} - {$end}";
            }
        }

        return [
            'employee' => (string) ($ipcr->employee?->name ?? $user?->name ?? 'Employee'),
            'office' => $office,
            'period' => $periodLabel,
        ];
    }

    private function buildIpcr(Ipcr $ipcr): array
    {
        $sections = [
            'core' => [],
            'support' => [],
        ];

        foreach ($ipcr->items as $item) {
            $output = trim((string) ($item->output_title ?? ''));
            $indicator = trim((string) ($item->indicator_text ?? ''));
            if ($output === '' || $indicator === '') {
                continue;
            }

            $functionType = strtolower(trim((string) ($item->function_type ?? '')));
            $section = str_contains($functionType, 'support') ? 'support' : 'core';

            if (!isset($sections[$section][$output])) {
                $sections[$section][$output] = [
                    'output' => $output,
                    'indicators' => [],
                ];
            }

            if (!in_array($indicator, $sections[$section][$output]['indicators'], true)) {
                $sections[$section][$output]['indicators'][] = $indicator;
            }
        }

        return [
            'core' => array_values($sections['core']),
            'support' => array_values($sections['support']),
        ];
    }

    private function buildValuesByIndicator(Ipcr $ipcr): array
    {
        [$startDate, $endDate] = $this->resolvePeriodWindow($ipcr);

        $entries = OrsEntry::query()
            ->with([
                'ipcrItem:id,output_title,function_type,indicator_text',
                'monitoring:ors_entry_id,quality_rating,timeliness_rating,supervisor_id',
            ])
            ->where('employee_id', $ipcr->employee_id)
            ->where('ipcr_id', $ipcr->id)
            ->where('status', 'rated')
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('monitoring', function ($q) {
                $q->whereNotNull('quality_rating')
                    ->whereNotNull('timeliness_rating');
            })
            ->orderBy('work_date')
            ->get();

        $aggregated = [];
        foreach ($entries as $entry) {
            $indicator = trim((string) data_get($entry, 'ipcrItem.indicator_text', ''));
            if ($indicator === '') {
                continue;
            }

            $quantity = is_numeric($entry->quantity) ? (float) $entry->quantity : 0.0;
            if ($quantity <= 0) {
                continue;
            }

            $qualityRating = (float) data_get($entry, 'monitoring.quality_rating', 0);
            $timelinessRating = (float) data_get($entry, 'monitoring.timeliness_rating', 0);

            if (!isset($aggregated[$indicator])) {
                $aggregated[$indicator] = [
                    'total_qty' => 0.0,
                    'sum_q_points' => 0.0,
                    'sum_t_points' => 0.0,
                ];
            }

            $aggregated[$indicator]['total_qty'] += $quantity;
            $aggregated[$indicator]['sum_q_points'] += ($quantity * $qualityRating);
            $aggregated[$indicator]['sum_t_points'] += ($quantity * $timelinessRating);
        }

        $valuesByIndicator = [];
        foreach ($aggregated as $indicator => $totals) {
            $totalQty = (float) ($totals['total_qty'] ?? 0.0);
            if ($totalQty <= 0) {
                continue;
            }

            $q = round(((float) $totals['sum_q_points']) / $totalQty, 2);
            $t = round(((float) $totals['sum_t_points']) / $totalQty, 2);

            $valuesByIndicator[$indicator] = [
                'accomplishment' => 'Completed ' . $this->formatQuantity($totalQty) . ' output(s) for the period based on rated ORS totals.',
                'q' => $q,
                'e' => $q,
                't' => $t,
                'remarks' => 'Derived from rated ORS entries; supervisor ratings applied (Stage II).',
            ];
        }

        return $valuesByIndicator;
    }

    private function buildStandardsFromIpcrOrFail(Ipcr $ipcr): array
    {
        $allIndicators = [];
        foreach ($ipcr->items as $item) {
            $indicator = trim((string) ($item->indicator_text ?? ''));
            if ($indicator !== '') {
                $allIndicators[$indicator] = true;
            }
        }

        $standards = [];

        foreach ($ipcr->items as $item) {
            $indicator = trim((string) ($item->indicator_text ?? ''));
            if ($indicator === '' || isset($standards[$indicator])) {
                continue;
            }

            $rawPayload = $item->standards_payload;
            $payload = null;

            if (is_string($rawPayload)) {
                $decoded = json_decode($rawPayload, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $payload = $decoded;
                }
            } elseif (is_array($rawPayload)) {
                $payload = $rawPayload;
            }

            if (!is_array($payload) || empty($payload)) {
                continue;
            }

            $normalized = [];
            foreach ([5, 4, 3, 2, 1] as $rating) {
                $bucket = $payload[(string) $rating] ?? $payload[$rating] ?? [];
                if (!is_array($bucket)) {
                    $bucket = [];
                }

                $bucketUpper = [];
                foreach ($bucket as $key => $value) {
                    $bucketUpper[strtoupper((string) $key)] = $value;
                }

                $normalized[$rating] = [
                    'q' => $this->normalizeStandardsDimension($bucketUpper['Q'] ?? null),
                    'e' => $this->normalizeStandardsDimension($bucketUpper['E'] ?? null),
                    't' => $this->normalizeStandardsDimension($bucketUpper['T'] ?? null),
                ];
            }

            $standards[$indicator] = $normalized;
        }

        $missingIndicators = [];
        foreach (array_keys($allIndicators) as $indicator) {
            if (!isset($standards[$indicator])) {
                $missingIndicators[] = $indicator;
            }
        }

        if (!empty($missingIndicators)) {
            abort(422, 'IPCR export requires standards_payload for all indicators. Missing/invalid for: ' . implode(', ', $missingIndicators));
        }

        if (empty($standards)) {
            abort(422, 'IPCR export requires standards_payload for all indicators. Missing/invalid for: (no indicators)');
        }

        return $standards;
    }

    private function normalizeStandardsDimension(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(
                fn ($v) => trim((string) $v),
                $value
            ), fn ($v) => $v !== ''));
        }

        $text = trim((string) $value);
        if ($text === '') {
            return [];
        }

        return [$text];
    }

    private function buildFilename(array $meta, bool $preview): string
    {
        $employee = Str::slug((string) ($meta['employee'] ?? 'Employee'), '_');
        $office = Str::slug((string) ($meta['office'] ?? 'Office'), '_');
        $period = Str::slug((string) ($meta['period'] ?? 'Period'), '_');
        $suffix = $preview ? '_Preview' : '';

        return "IPCR_{$employee}_{$office}_{$period}{$suffix}.xlsx";
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
                'items:id,ipcr_id,output_title,function_type,indicator_text,target_summary,standards_payload',
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
                    'items:id,ipcr_id,output_title,function_type,indicator_text,target_summary,standards_payload',
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

    private function resolvePeriodWindow(Ipcr $ipcr): array
    {
        $start = $ipcr->performancePeriod?->start_date
            ? Carbon::parse($ipcr->performancePeriod->start_date)
            : null;
        $end = $ipcr->performancePeriod?->end_date
            ? Carbon::parse($ipcr->performancePeriod->end_date)
            : null;

        if (!$start || !$end) {
            $fallback = PerformancePeriod::query()
                ->whereKey($ipcr->performance_period_id)
                ->orWhere('is_active', true)
                ->orderByDesc('start_date')
                ->first();

            if ($fallback?->start_date && $fallback?->end_date) {
                $start = Carbon::parse($fallback->start_date);
                $end = Carbon::parse($fallback->end_date);
            }
        }

        if (!$start || !$end) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        }

        return [$start->copy()->startOfDay(), $end->copy()->endOfDay()];
    }

    private function formatQuantity(float $quantity): string
    {
        if (fmod($quantity, 1.0) === 0.0) {
            return (string) (int) $quantity;
        }

        return rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.');
    }

}
