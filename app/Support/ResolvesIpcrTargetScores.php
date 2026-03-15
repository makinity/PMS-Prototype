<?php

namespace App\Support;

use App\Models\Ipcr;
use App\Models\OrsEntry;
use Illuminate\Support\Carbon;

trait ResolvesIpcrTargetScores
{
    private function formatTargetSummary(mixed $targetQuantity, ?string $targetTimeline): string
    {
        $quantityText = $targetQuantity === null ? '' : trim((string) $targetQuantity);
        $timelineText = trim((string) ($targetTimeline ?? ''));

        return trim($quantityText . ' ' . $timelineText);
    }

    private function buildTargetSummaryByFunctionAndOutput(?Ipcr $ipcr): array
    {
        if (!$ipcr) {
            return [];
        }

        $ipcr->loadMissing('unitWorkPlan.uwpFunctions.mfos');

        $targetSummaries = [];
        foreach ($ipcr->unitWorkPlan?->uwpFunctions ?? [] as $function) {
            $functionId = (int) ($function->id ?? 0);

            foreach ($function->mfos ?? [] as $mfo) {
                $outputTitle = trim((string) ($mfo->title ?? ''));
                if ($outputTitle === '') {
                    continue;
                }

                $targetSummaries[$functionId . '||' . $outputTitle] = $this->formatTargetSummary(
                    $mfo->target_quantity,
                    $mfo->target_timeline
                );
            }
        }

        return $targetSummaries;
    }

    private function buildTargetQuantityByOutput(?Ipcr $ipcr): array
    {
        if (!$ipcr) {
            return [];
        }

        $ipcr->loadMissing('unitWorkPlan.uwpFunctions.mfos');

        $targetQuantities = [];
        foreach ($ipcr->unitWorkPlan?->uwpFunctions ?? [] as $function) {
            foreach ($function->mfos ?? [] as $mfo) {
                $outputTitle = trim((string) ($mfo->title ?? ''));
                if ($outputTitle === '') {
                    continue;
                }

                $targetQuantities[$outputTitle] = is_numeric($mfo->target_quantity)
                    ? (float) $mfo->target_quantity
                    : null;
            }
        }

        return $targetQuantities;
    }

    private function buildIndicatorRatingLookupKey(string $outputTitle, string $indicatorText): string
    {
        return trim($outputTitle) . '||' . trim($indicatorText);
    }

    private function resolveScoringPeriodWindow(Ipcr $ipcr): array
    {
        $start = $ipcr->performancePeriod?->start_date
            ? Carbon::parse($ipcr->performancePeriod->start_date)
            : null;
        $end = $ipcr->performancePeriod?->end_date
            ? Carbon::parse($ipcr->performancePeriod->end_date)
            : null;

        if (!$start || !$end) {
            $fallback = Ipcr::query()
                ->with('performancePeriod:id,start_date,end_date')
                ->find($ipcr->id);

            if ($fallback?->performancePeriod?->start_date && $fallback?->performancePeriod?->end_date) {
                $start = Carbon::parse($fallback->performancePeriod->start_date);
                $end = Carbon::parse($fallback->performancePeriod->end_date);
            }
        }

        if (!$start || !$end) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        }

        return [$start->copy()->startOfDay(), $end->copy()->endOfDay()];
    }

    private function buildRatedIpcrPerformanceMaps(?Ipcr $ipcr, ?int $employeeId = null): array
    {
        if (!$ipcr) {
            return [[], []];
        }

        [$startDate, $endDate] = $this->resolveScoringPeriodWindow($ipcr);
        $targetQuantityByOutput = $this->buildTargetQuantityByOutput($ipcr);
        $resolvedEmployeeId = $employeeId ?: (int) ($ipcr->employee_id ?? 0);

        if ($resolvedEmployeeId <= 0) {
            return [[], []];
        }

        $entries = OrsEntry::query()
            ->with([
                'monitoring:ors_entry_id,quality_rating,timeliness_rating',
                'ipcrItem:id,output_title,indicator_text',
            ])
            ->where('employee_id', $resolvedEmployeeId)
            ->where('ipcr_id', $ipcr->id)
            ->where('status', 'rated')
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('monitoring', function ($query) {
                $query->whereNotNull('quality_rating')
                    ->whereNotNull('timeliness_rating');
            })
            ->get();

        $totalsByOutput = [];
        $totalsByIndicator = [];

        foreach ($entries as $entry) {
            $monitoring = $entry->monitoring;
            if (!$monitoring) {
                continue;
            }

            $quantity = (float) ($entry->quantity ?? 0);
            if ($quantity <= 0) {
                continue;
            }

            $outputTitle = trim((string) ($entry->ipcrItem?->output_title ?? ''));
            if ($outputTitle === '') {
                $outputTitle = 'Unassigned Output';
            }

            $qualityPoints = $quantity * (float) $monitoring->quality_rating;
            $timelinessPoints = $quantity * (float) $monitoring->timeliness_rating;

            if (!isset($totalsByOutput[$outputTitle])) {
                $totalsByOutput[$outputTitle] = [
                    'qty' => 0.0,
                    'q_points' => 0.0,
                    't_points' => 0.0,
                ];
            }

            $totalsByOutput[$outputTitle]['qty'] += $quantity;
            $totalsByOutput[$outputTitle]['q_points'] += $qualityPoints;
            $totalsByOutput[$outputTitle]['t_points'] += $timelinessPoints;

            $indicatorText = trim((string) ($entry->ipcrItem?->indicator_text ?? ''));
            if ($indicatorText === '') {
                continue;
            }

            $indicatorLookupKey = $this->buildIndicatorRatingLookupKey($outputTitle, $indicatorText);
            if (!isset($totalsByIndicator[$indicatorLookupKey])) {
                $totalsByIndicator[$indicatorLookupKey] = [
                    'output' => $outputTitle,
                    'qty' => 0.0,
                    'q_points' => 0.0,
                    't_points' => 0.0,
                ];
            }

            $totalsByIndicator[$indicatorLookupKey]['qty'] += $quantity;
            $totalsByIndicator[$indicatorLookupKey]['q_points'] += $qualityPoints;
            $totalsByIndicator[$indicatorLookupKey]['t_points'] += $timelinessPoints;
        }

        $ratingsByOutput = [];
        foreach ($totalsByOutput as $outputTitle => $totals) {
            $ratings = $this->buildPerformanceRatings(
                (float) ($totals['qty'] ?? 0),
                (float) ($totals['q_points'] ?? 0),
                (float) ($totals['t_points'] ?? 0),
                $targetQuantityByOutput[$outputTitle] ?? null
            );

            if ($ratings !== null) {
                $ratingsByOutput[$outputTitle] = $ratings;
            }
        }

        $ratingsByIndicator = [];
        foreach ($totalsByIndicator as $indicatorLookupKey => $totals) {
            $outputTitle = trim((string) ($totals['output'] ?? ''));
            $ratings = $this->buildPerformanceRatings(
                (float) ($totals['qty'] ?? 0),
                (float) ($totals['q_points'] ?? 0),
                (float) ($totals['t_points'] ?? 0),
                $targetQuantityByOutput[$outputTitle] ?? null
            );

            if ($ratings !== null) {
                $ratingsByIndicator[$indicatorLookupKey] = $ratings;
            }
        }

        return [$ratingsByOutput, $ratingsByIndicator];
    }

    private function calculateQuantityScore(float $actualQuantity, mixed $targetQuantity): ?float
    {
        $resolvedTarget = is_numeric($targetQuantity) ? (float) $targetQuantity : 0.0;
        if ($actualQuantity <= 0 || $resolvedTarget <= 0) {
            return null;
        }

        return round(min(5.0, 5.0 * ($actualQuantity / $resolvedTarget)), 2);
    }

    private function calculateAverageScore(?float $q, ?float $e, ?float $t): ?float
    {
        if ($e === null || $t === null) {
            return null;
        }

        if ($q === null) {
            return round(($e + $t) / 2, 2);
        }

        return round(($q + $e + $t) / 3, 2);
    }

    private function buildPerformanceRatings(
        float $actualQuantity,
        float $qualityPoints,
        float $timelinessPoints,
        mixed $targetQuantity
    ): ?array {
        if ($actualQuantity <= 0) {
            return null;
        }

        $q = $this->calculateQuantityScore($actualQuantity, $targetQuantity);
        $e = round($qualityPoints / $actualQuantity, 2);
        $t = round($timelinessPoints / $actualQuantity, 2);

        return [
            'qty' => $actualQuantity,
            'q' => $q,
            'e' => $e,
            't' => $t,
            'a' => $this->calculateAverageScore($q, $e, $t),
        ];
    }
}
