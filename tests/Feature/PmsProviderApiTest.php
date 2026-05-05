<?php

namespace Tests\Feature;

use App\Models\IntegrationSetting;
use App\Models\Office;
use App\Models\PerformancePeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PmsProviderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_hris_page_generates_and_displays_pms_api_token_details(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.hris'));

        $response->assertOk();
        $response->assertSee('View PMS API Details');
        $response->assertSee('Performance Management System API Details');

        $this->assertNotSame('', (string) IntegrationSetting::getValue('pms_api.token', ''));
        $this->assertSame('1', IntegrationSetting::getValue('pms_api.enabled'));
    }

    public function test_pms_api_requires_valid_token_and_regeneration_revokes_old_token(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $office = Office::query()->create([
            'name' => 'Revenue Collection Unit',
            'code' => 'RCU',
        ]);

        User::factory()->create([
            'name' => 'Mark Juntilla',
            'office_id' => $office->id,
            'position' => 'Revenue Officer',
            'is_active' => true,
        ]);

        PerformancePeriod::query()->create([
            'name' => 'Jan-Jun 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get(route('admin.hris'))->assertOk();

        $originalToken = (string) IntegrationSetting::getValue('pms_api.token', '');

        $this->getJson('/api/pms/v1/employees')->assertStatus(401);
        $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/pms/v1/employees')
            ->assertStatus(401);

        $this->withHeader('Authorization', 'Bearer ' . $originalToken)
            ->getJson('/api/pms/v1/employees')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'employee_id', 'name', 'office_id', 'office_name', 'position'],
                ],
            ]);

        $this->withHeader('Authorization', 'Bearer ' . $originalToken)
            ->getJson('/api/pms/v1/offices')
            ->assertOk();

        $this->withHeader('Authorization', 'Bearer ' . $originalToken)
            ->getJson('/api/pms/v1/performance-periods')
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.hris.pms-api.regenerate'))
            ->assertRedirect();

        $newToken = (string) IntegrationSetting::getValue('pms_api.token', '');

        $this->assertNotSame($originalToken, $newToken);

        $this->withHeader('Authorization', 'Bearer ' . $originalToken)
            ->getJson('/api/pms/v1/employees')
            ->assertStatus(401);

        $this->withHeader('Authorization', 'Bearer ' . $newToken)
            ->getJson('/api/pms/v1/employees')
            ->assertOk();
    }
}
