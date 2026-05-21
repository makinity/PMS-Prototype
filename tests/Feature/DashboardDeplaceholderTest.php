<?php

namespace Tests\Feature;

use App\Models\Office;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardDeplaceholderTest extends TestCase
{
    use RefreshDatabase;

    public function test_dept_head_dashboard_renders_with_expected_payload_keys(): void
    {
        $period = PerformancePeriod::query()->create([
            'name' => 'Jan-Jun 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $deptHead = User::factory()->create([
            'role' => 'dept-head',
            'is_active' => true,
        ]);

        $office = Office::query()->create([
            'name' => 'Planning Office',
            'head_id' => $deptHead->id,
        ]);

        $deptHead->update(['office_id' => $office->id]);

        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'is_active' => true,
            'office_id' => $office->id,
        ]);

        UnitWorkPlan::query()->create([
            'office_id' => $office->id,
            'performance_period_id' => $period->id,
            'created_by' => $supervisor->id,
            'status' => UnitWorkPlan::STATUS_SUBMITTED,
        ]);

        $response = $this->actingAs($deptHead)->get(route('dept-head.dashboard'));

        $response->assertOk();
        $response->assertViewIs('dept-head.dashboard');
        $response->assertViewHasAll(['period', 'kpis', 'statusCounts', 'trend', 'recentActivity']);
    }

    public function test_pmt_dashboard_renders_with_expected_payload_keys(): void
    {
        $period = PerformancePeriod::query()->create([
            'name' => 'Jan-Jun 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $pmt = User::factory()->create([
            'role' => 'pmt',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $office = Office::query()->create(['name' => 'HRMO']);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'is_active' => true,
            'office_id' => $office->id,
        ]);

        $uwp = UnitWorkPlan::query()->create([
            'office_id' => $office->id,
            'performance_period_id' => $period->id,
            'created_by' => $supervisor->id,
            'status' => UnitWorkPlan::STATUS_ENDORSED,
        ]);

        Opcr::query()->create([
            'unit_work_plan_id' => $uwp->id,
            'office_id' => $office->id,
            'performance_period_id' => $period->id,
            'generated_by' => $admin->id,
            'status' => Opcr::STATUS_ENDORSED,
        ]);

        $response = $this->actingAs($pmt)->get(route('pmt.dashboard'));

        $response->assertOk();
        $response->assertViewIs('pmt.dashboard');
        $response->assertViewHasAll(['period', 'kpis', 'queueCounts', 'trend', 'recentActions', 'approvalQueue']);
    }

    public function test_dashboard_period_falls_back_to_latest_when_no_active_period(): void
    {
        PerformancePeriod::query()->create([
            'name' => 'Old Period',
            'start_date' => '2025-01-01',
            'end_date' => '2025-06-30',
            'is_active' => false,
        ]);
        $latest = PerformancePeriod::query()->create([
            'name' => 'Latest Period',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);

        $deptHead = User::factory()->create(['role' => 'dept-head', 'is_active' => true]);
        $office = Office::query()->create(['name' => 'Operations', 'head_id' => $deptHead->id]);
        $deptHead->update(['office_id' => $office->id]);

        $response = $this->actingAs($deptHead)->get(route('dept-head.dashboard'));
        $response->assertOk();
        $response->assertViewHas('period', fn ($period) => (int) $period->id === (int) $latest->id);
    }

    public function test_dashboard_trend_shape_is_14_days(): void
    {
        $period = PerformancePeriod::query()->create([
            'name' => 'Jan-Jun 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $pmt = User::factory()->create(['role' => 'pmt', 'is_active' => true]);
        $response = $this->actingAs($pmt)->get(route('pmt.dashboard'));
        $response->assertOk();

        $trend = $response->viewData('trend');
        $this->assertCount(14, $trend['labels'] ?? []);
        $this->assertCount(14, $trend['series']['approved'] ?? []);
        $this->assertCount(14, $trend['series']['returned'] ?? []);
    }
}

