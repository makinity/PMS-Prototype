<?php

namespace App\Http\Controllers\StageOne\Forms;

use App\Exports\StageOne\UwpExcelExport;
use App\Http\Controllers\Controller;
use App\Models\UnitWorkPlan;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UwpExcelExportController extends Controller
{
    public function excelExport(int $uwpId)
    {
        $uwp = UnitWorkPlan::query()
            ->with([
                'office.head',
                'performancePeriod',
                'creator',
                'uwpFunctions.mfos.successIndicators.qetStandards',
            ])
            ->where('id', $uwpId)
            ->firstOrFail();

        if ($uwp->status !== UnitWorkPlan::STATUS_PMT_APPROVED) {
            abort(403, 'Only PMT approved UWP can be exported.');
        }

        $uwpData = [
            'office' => $uwp->office?->name,
            'supervisor' => $uwp->creator?->name,
            'dept_head' => $uwp->office?->head?->name,
            'period' => $uwp->performancePeriod?->name,
            'outputs' => [],
        ];

        $standards = [];

        foreach ($uwp->uwpFunctions as $fn) {
            foreach ($fn->mfos as $mfo) {
                $indicators = $mfo->successIndicators->map(function ($si) {
                    return [
                        'text' => (string) ($si->indicator_text ?? ''),
                        'target_quantity' => is_numeric($si->target_quantity ?? null) ? (int) $si->target_quantity : null,
                        'target_timeline' => trim((string) ($si->target_timeline ?? '')),
                        'target_summary' => $this->buildTargetSummary(
                            $si->target_quantity,
                            (string) ($si->target_timeline ?? '')
                        ),
                    ];
                })->filter(fn (array $indicator) => trim((string) ($indicator['text'] ?? '')) !== '')
                    ->values()
                    ->all();

                $indicatorTargetQuantity = collect($indicators)
                    ->sum(fn (array $indicator) => is_numeric($indicator['target_quantity'] ?? null) ? (int) $indicator['target_quantity'] : 0);
                $indicatorTimelines = collect($indicators)
                    ->pluck('target_timeline')
                    ->filter(fn ($value) => trim((string) $value) !== '')
                    ->unique()
                    ->values()
                    ->all();

                $uwpData['outputs'][] = [
                    'mfo' => $mfo->title,
                    'success_indicators' => $indicators,
                    'target_quantity' => $indicatorTargetQuantity > 0
                        ? $indicatorTargetQuantity
                        : (is_numeric($mfo->target_quantity ?? null) ? (int) $mfo->target_quantity : null),
                    'target' => count($indicatorTimelines) === 1
                        ? $indicatorTimelines[0]
                        : (count($indicatorTimelines) > 1 ? 'Multiple indicator targets' : trim((string) ($mfo->target_timeline ?? ''))),
                    'function' => ucfirst((string) $fn->function_type),
                    'function_type' => $fn->function_type,
                ];

                foreach ($mfo->successIndicators as $si) {
                    $indicator = (string) $si->indicator_text;

                    if (!isset($standards[$indicator])) {
                        $standards[$indicator] = [];
                    }

                    foreach ([5, 4, 3, 2, 1] as $rating) {
                        $standards[$indicator][$rating] = [
                            'q' => $standards[$indicator][$rating]['q'] ?? [],
                            'e' => $standards[$indicator][$rating]['e'] ?? [],
                            't' => $standards[$indicator][$rating]['t'] ?? [],
                        ];
                    }

                    foreach ($si->qetStandards as $st) {
                        $rating = (int) $st->rating;
                        if (!in_array($rating, [1, 2, 3, 4, 5], true)) {
                            continue;
                        }

                        $dimension = strtolower((string) $st->dimension);
                        $dimension = match ($dimension) {
                            'quality', 'q' => 'q',
                            'efficiency', 'e' => 'e',
                            'timeliness', 't' => 't',
                            default => null,
                        };

                        if ($dimension === null) {
                            continue;
                        }

                        $standards[$indicator][$rating][$dimension][] = (string) $st->standard_text;
                    }
                }
            }
        }

        return Excel::download(
            new UwpExcelExport($uwpData, $standards),
            'UWP_' . Str::slug($uwp->office?->name ?? 'Office') . '_' .
            Str::slug($uwp->performancePeriod?->name ?? 'Period') . '.xlsx'
        );
    }

    private function buildTargetSummary(mixed $targetQuantity, ?string $targetTimeline): string
    {
        $quantityText = is_numeric($targetQuantity) ? trim((string) $targetQuantity) : '';
        $timelineText = trim((string) ($targetTimeline ?? ''));

        return trim($quantityText . ' ' . $timelineText);
    }
}
