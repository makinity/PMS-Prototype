<?php

namespace Tests\Unit;

use App\Models\DevelopmentPlan;
use App\Models\Ipcr;
use App\Models\Office;
use App\Models\PerformancePeriod;
use App\Models\User;
use App\Services\DevelopmentPlanningService;
use App\Services\StageFourPerformerService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class DevelopmentPlanningServiceTest extends TestCase
{
    private DevelopmentPlanningService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DevelopmentPlanningService(new StageFourPerformerService());
    }

    public function test_it_builds_candidate_rows_for_low_performers_only(): void
    {
        $period = new PerformancePeriod(['name' => 'Jan - Jun 2026']);
        $office = new Office(['name' => 'HRMO']);
        $employeeA = new User(['name' => 'Low One', 'position' => 'Staff']);
        $employeeA->setRelation('office', $office);
        $employeeB = new User(['name' => 'Top One', 'position' => 'Officer']);
        $employeeB->setRelation('office', $office);

        $lowIpcr = new Ipcr([
            'final_score' => 1.20,
            'adjectival_rating' => 'Poor',
        ]);
        $lowIpcr->id = 7;
        $lowIpcr->employee_id = 101;
        $lowIpcr->setRelation('employee', $employeeA);
        $lowIpcr->setRelation('office', $office);
        $lowIpcr->setRelation('performancePeriod', $period);

        $topIpcr = new Ipcr([
            'final_score' => 4.50,
            'adjectival_rating' => 'Outstanding',
        ]);
        $topIpcr->id = 8;
        $topIpcr->employee_id = 102;
        $topIpcr->setRelation('employee', $employeeB);
        $topIpcr->setRelation('office', $office);
        $topIpcr->setRelation('performancePeriod', $period);

        $rows = $this->service->buildCandidateRows(
            new Collection([$lowIpcr, $topIpcr]),
            new Collection()
        );

        $this->assertCount(1, $rows);
        $this->assertSame(7, $rows->first()['ipcr_id']);
        $this->assertSame('Low One', $rows->first()['employee_name']);
        $this->assertSame('Poor', $rows->first()['official_rating']);
        $this->assertSame('No Draft Yet', $rows->first()['development_plan_status_label']);
    }

    public function test_it_prefers_adjusted_values_and_merges_existing_draft_status(): void
    {
        $period = new PerformancePeriod(['name' => 'Jan - Jun 2026']);
        $office = new Office(['name' => 'Planning']);
        $employee = new User(['name' => 'Adjusted Low', 'position' => 'Analyst']);
        $employee->setRelation('office', $office);

        $ipcr = new Ipcr([
            'final_score' => 2.50,
            'adjectival_rating' => 'Satisfactory',
            'pmt_adjusted_score' => 1.50,
            'pmt_adjusted_rating' => 'Unsatisfactory',
        ]);
        $ipcr->id = 9;
        $ipcr->employee_id = 103;
        $ipcr->setRelation('employee', $employee);
        $ipcr->setRelation('office', $office);
        $ipcr->setRelation('performancePeriod', $period);

        $plan = new DevelopmentPlan([
            'status' => DevelopmentPlan::STATUS_PENDING_DETAILS,
        ]);
        $plan->id = 55;
        $plan->ipcr_id = 9;

        $rows = $this->service->buildCandidateRows(
            new Collection([$ipcr]),
            new Collection([9 => $plan])
        );

        $this->assertCount(1, $rows);
        $this->assertSame(1.50, $rows->first()['official_score']);
        $this->assertSame('Unsatisfactory', $rows->first()['official_rating']);
        $this->assertSame(55, $rows->first()['development_plan_id']);
        $this->assertSame(DevelopmentPlan::STATUS_PENDING_DETAILS, $rows->first()['development_plan_status']);
        $this->assertSame('Pending Details', $rows->first()['development_plan_status_label']);
    }

    public function test_it_returns_expected_summary_counts(): void
    {
        $counts = $this->service->summaryCounts(new Collection([
            ['development_plan_status' => DevelopmentPlan::STATUS_DRAFT],
            ['development_plan_status' => DevelopmentPlan::STATUS_PENDING_DETAILS],
            ['development_plan_status' => ''],
        ]));

        $this->assertSame(3, $counts['low_performers']);
        $this->assertSame(1, $counts['drafts_created']);
        $this->assertSame(1, $counts['pending_details']);
    }
}
