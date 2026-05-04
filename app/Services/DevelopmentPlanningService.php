<?php

namespace App\Services;

use App\Models\DevelopmentPlan;
use App\Models\Ipcr;
use App\Models\PerformancePeriod;
use Illuminate\Support\Collection;

class DevelopmentPlanningService
{
    private const LOW_RATINGS = ['Unsatisfactory', 'Poor'];

    public function __construct(
        private readonly StageFourPerformerService $performerService
    ) {
    }

    public function getLowPerformerCandidates(?PerformancePeriod $period): Collection
    {
        $query = Ipcr::query()
            ->with([
                'employee:id,name,office_id,position',
                'employee.office:id,name',
                'office:id,name',
                'performancePeriod:id,name',
            ])
            ->where('status', Ipcr::STATUS_RELEASED_BY_PMT);

        if ($period) {
            $query->where('performance_period_id', $period->id);
        }

        $ipcrs = $query->get();

        $plans = DevelopmentPlan::query()
            ->when($period, fn ($q) => $q->where('performance_period_id', $period->id))
            ->get()
            ->keyBy('ipcr_id');

        return $this->buildCandidateRows($ipcrs, $plans);
    }

    public function buildCandidateRows(Collection $ipcrs, Collection $plans): Collection
    {
        return $ipcrs
            ->map(function (Ipcr $ipcr) use ($plans) {
                $row = $this->performerService->resolveEmployeeRow($ipcr);
                if (!$row || !in_array($row['official_rating'], self::LOW_RATINGS, true)) {
                    return null;
                }

                $plan = $plans->get($ipcr->id);

                return [
                    'ipcr_id' => (int) $ipcr->id,
                    'employee_id' => (int) ($ipcr->employee_id ?? 0),
                    'employee_name' => $row['employee_name'],
                    'office_name' => $row['office_name'],
                    'position' => (string) ($ipcr->employee?->position ?? '--'),
                    'period_name' => $row['period_name'],
                    'official_score' => $row['official_score'],
                    'official_rating' => $row['official_rating'],
                    'released_at' => $row['released_at'],
                    'development_plan_id' => $plan?->id,
                    'development_plan_status' => (string) ($plan?->status ?? ''),
                    'development_plan_status_label' => $this->formatStatusLabel((string) ($plan?->status ?? '')),
                ];
            })
            ->filter()
            ->sortBy('official_score')
            ->values();
    }

    public function summaryCounts(Collection $candidates): array
    {
        return [
            'low_performers' => $candidates->count(),
            'drafts_created' => $candidates->filter(fn (array $row) => ($row['development_plan_status'] ?? '') === DevelopmentPlan::STATUS_DRAFT)->count(),
            'pending_details' => $candidates->filter(fn (array $row) => ($row['development_plan_status'] ?? '') === DevelopmentPlan::STATUS_PENDING_DETAILS)->count(),
        ];
    }

    public function formatStatusLabel(string $status): string
    {
        return match ($status) {
            DevelopmentPlan::STATUS_DRAFT => 'Draft',
            DevelopmentPlan::STATUS_PENDING_DETAILS => 'Pending Details',
            DevelopmentPlan::STATUS_SUBMITTED_TO_LD => 'Submitted to L&D',
            default => 'No Draft Yet',
        };
    }
}
