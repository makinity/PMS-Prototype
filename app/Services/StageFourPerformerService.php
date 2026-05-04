<?php

namespace App\Services;

use App\Models\Ipcr;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use Illuminate\Support\Collection;

class StageFourPerformerService
{
    private const TOP_RATINGS = ['Outstanding', 'Very Satisfactory'];
    private const LOW_RATINGS = ['Unsatisfactory', 'Poor'];

    public function getTopAndLowPerformers(?PerformancePeriod $period): array
    {
        return $this->groupPerformerRows(
            $this->buildEmployeeRows($period),
            $this->buildOfficeRows($period)
        );
    }

    public function groupPerformerRows(Collection $employeeRows, Collection $officeRows): array
    {
        $topEmployees = $employeeRows
            ->filter(fn (array $row) => in_array($row['official_rating'], self::TOP_RATINGS, true))
            ->sortByDesc('official_score')
            ->values();

        $lowEmployees = $employeeRows
            ->filter(fn (array $row) => in_array($row['official_rating'], self::LOW_RATINGS, true))
            ->sortBy('official_score')
            ->values();

        $topOffices = $officeRows
            ->filter(fn (array $row) => in_array($row['official_rating'], self::TOP_RATINGS, true))
            ->sortByDesc('official_score')
            ->values();

        $lowOffices = $officeRows
            ->filter(fn (array $row) => in_array($row['official_rating'], self::LOW_RATINGS, true))
            ->sortBy('official_score')
            ->values();

        return [
            'top_employees' => $topEmployees,
            'top_offices' => $topOffices,
            'low_employees' => $lowEmployees,
            'low_offices' => $lowOffices,
            'summary_counts' => [
                'top_employees' => $topEmployees->count(),
                'top_offices' => $topOffices->count(),
                'low_employees' => $lowEmployees->count(),
                'low_offices' => $lowOffices->count(),
            ],
        ];
    }

    public function resolveEmployeeRow(Ipcr $ipcr): ?array
    {
        $officialScore = $ipcr->pmt_adjusted_score !== null
            ? (float) $ipcr->pmt_adjusted_score
            : (is_numeric($ipcr->final_score) ? (float) $ipcr->final_score : null);
        $officialRating = trim((string) ($ipcr->pmt_adjusted_rating ?: $ipcr->adjectival_rating));

        if ($officialScore === null || $officialRating === '') {
            return null;
        }

        return [
            'id' => (int) $ipcr->id,
            'employee_name' => (string) ($ipcr->employee?->name ?? '--'),
            'office_name' => (string) ($ipcr->employee?->office?->name ?? $ipcr->office?->name ?? '--'),
            'period_name' => (string) ($ipcr->performancePeriod?->name ?? '--'),
            'official_score' => round($officialScore, 2),
            'official_rating' => $officialRating,
            'released_at' => $ipcr->released_at,
        ];
    }

    public function resolveOfficeRow(Opcr $opcr): ?array
    {
        $officialScore = $opcr->pmt_adjusted_score !== null
            ? (float) $opcr->pmt_adjusted_score
            : (is_numeric($opcr->final_score) ? (float) $opcr->final_score : null);
        $officialRating = trim((string) ($opcr->pmt_adjusted_rating ?: $opcr->adjectival_rating));

        if ($officialScore === null || $officialRating === '') {
            return null;
        }

        return [
            'id' => (int) $opcr->id,
            'office_name' => (string) ($opcr->office?->name ?? '--'),
            'department_head_name' => (string) ($opcr->office?->head?->name ?? '--'),
            'period_name' => (string) ($opcr->performancePeriod?->name ?? '--'),
            'official_score' => round($officialScore, 2),
            'official_rating' => $officialRating,
            'released_at' => $opcr->released_at,
        ];
    }

    private function buildEmployeeRows(?PerformancePeriod $period): Collection
    {
        $query = Ipcr::query()
            ->with([
                'employee:id,name,office_id',
                'employee.office:id,name',
                'performancePeriod:id,name',
            ])
            ->where('status', Ipcr::STATUS_RELEASED_BY_PMT);

        if ($period) {
            $query->where('performance_period_id', $period->id);
        }

        return $query->get()
            ->map(fn (Ipcr $ipcr) => $this->resolveEmployeeRow($ipcr))
            ->filter()
            ->values();
    }

    private function buildOfficeRows(?PerformancePeriod $period): Collection
    {
        $query = Opcr::query()
            ->with([
                'office.head:id,name',
                'performancePeriod:id,name',
            ])
            ->where('status', Opcr::STATUS_RELEASED_BY_PMT);

        if ($period) {
            $query->where('performance_period_id', $period->id);
        }

        return $query->get()
            ->map(fn (Opcr $opcr) => $this->resolveOfficeRow($opcr))
            ->filter()
            ->values();
    }
}
