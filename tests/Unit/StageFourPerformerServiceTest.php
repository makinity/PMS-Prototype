<?php

namespace Tests\Unit;

use App\Models\Ipcr;
use App\Models\Office;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Models\User;
use App\Services\StageFourPerformerService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class StageFourPerformerServiceTest extends TestCase
{
    private StageFourPerformerService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new StageFourPerformerService();
    }

    public function test_it_resolves_employee_rows_using_computed_values(): void
    {
        $period = new PerformancePeriod(['name' => 'Jan - Jun 2026']);
        $office = new Office(['name' => 'HRMO']);
        $employee = new User(['name' => 'Alice Employee']);
        $employee->setRelation('office', $office);

        $ipcr = new Ipcr([
            'final_score' => 4.70,
            'adjectival_rating' => 'Outstanding',
            'released_at' => Carbon::parse('2026-05-04 10:00:00'),
        ]);
        $ipcr->id = 10;
        $ipcr->setRelation('employee', $employee);
        $ipcr->setRelation('office', $office);
        $ipcr->setRelation('performancePeriod', $period);

        $row = $this->service->resolveEmployeeRow($ipcr);

        $this->assertSame(10, $row['id']);
        $this->assertSame('Alice Employee', $row['employee_name']);
        $this->assertSame('HRMO', $row['office_name']);
        $this->assertSame('Jan - Jun 2026', $row['period_name']);
        $this->assertSame(4.70, $row['official_score']);
        $this->assertSame('Outstanding', $row['official_rating']);
    }

    public function test_it_prefers_adjusted_employee_values_when_present(): void
    {
        $ipcr = new Ipcr([
            'final_score' => 2.00,
            'adjectival_rating' => 'Unsatisfactory',
            'pmt_adjusted_score' => 4.40,
            'pmt_adjusted_rating' => 'Very Satisfactory',
        ]);
        $ipcr->id = 11;
        $ipcr->setRelation('employee', new User(['name' => '--']));
        $ipcr->setRelation('office', new Office(['name' => '--']));
        $ipcr->setRelation('performancePeriod', new PerformancePeriod(['name' => '--']));

        $row = $this->service->resolveEmployeeRow($ipcr);

        $this->assertSame(4.40, $row['official_score']);
        $this->assertSame('Very Satisfactory', $row['official_rating']);
    }

    public function test_it_resolves_office_rows_and_department_head_name(): void
    {
        $period = new PerformancePeriod(['name' => 'Jan - Jun 2026']);
        $head = new User(['name' => 'Dept Head']);
        $office = new Office(['name' => 'Budget Office']);
        $office->setRelation('head', $head);

        $opcr = new Opcr([
            'final_score' => 1.30,
            'adjectival_rating' => 'Poor',
        ]);
        $opcr->id = 21;
        $opcr->setRelation('office', $office);
        $opcr->setRelation('performancePeriod', $period);

        $row = $this->service->resolveOfficeRow($opcr);

        $this->assertSame(21, $row['id']);
        $this->assertSame('Budget Office', $row['office_name']);
        $this->assertSame('Dept Head', $row['department_head_name']);
        $this->assertSame(1.30, $row['official_score']);
        $this->assertSame('Poor', $row['official_rating']);
    }

    public function test_it_groups_top_and_low_rows_and_excludes_satisfactory(): void
    {
        $employees = new Collection([
            ['id' => 1, 'official_score' => 4.20, 'official_rating' => 'Very Satisfactory'],
            ['id' => 2, 'official_score' => 1.20, 'official_rating' => 'Poor'],
            ['id' => 3, 'official_score' => 3.00, 'official_rating' => 'Satisfactory'],
            ['id' => 4, 'official_score' => 4.70, 'official_rating' => 'Outstanding'],
        ]);

        $offices = new Collection([
            ['id' => 5, 'official_score' => 3.60, 'official_rating' => 'Very Satisfactory'],
            ['id' => 6, 'official_score' => 1.30, 'official_rating' => 'Poor'],
            ['id' => 7, 'official_score' => 2.50, 'official_rating' => 'Satisfactory'],
            ['id' => 8, 'official_score' => 1.50, 'official_rating' => 'Unsatisfactory'],
        ]);

        $data = $this->service->groupPerformerRows($employees, $offices);

        $this->assertSame([4, 1], $data['top_employees']->pluck('id')->all());
        $this->assertSame([2], $data['low_employees']->pluck('id')->all());
        $this->assertSame([5], $data['top_offices']->pluck('id')->all());
        $this->assertSame([6, 8], $data['low_offices']->pluck('id')->all());
        $this->assertSame(2, $data['summary_counts']['top_employees']);
        $this->assertSame(1, $data['summary_counts']['top_offices']);
        $this->assertSame(1, $data['summary_counts']['low_employees']);
        $this->assertSame(2, $data['summary_counts']['low_offices']);
    }

    public function test_it_returns_null_when_official_values_are_missing(): void
    {
        $ipcr = new Ipcr();
        $ipcr->setRelation('employee', new User(['name' => '--']));
        $ipcr->setRelation('office', new Office(['name' => '--']));
        $ipcr->setRelation('performancePeriod', new PerformancePeriod(['name' => '--']));

        $opcr = new Opcr();
        $opcr->setRelation('office', new Office(['name' => '--']));
        $opcr->setRelation('performancePeriod', new PerformancePeriod(['name' => '--']));

        $this->assertNull($this->service->resolveEmployeeRow($ipcr));
        $this->assertNull($this->service->resolveOfficeRow($opcr));
    }
}
