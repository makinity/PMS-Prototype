<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntegrationSetting;
use App\Services\HmsEmployeeSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HrisIntegrationController extends Controller
{
    private const SETTING_BASE_URL = 'hms.base_url';
    private const SETTING_TOKEN = 'hms.bearer_token';
    private const SETTING_ENABLED = 'hms.enabled';
    private const SETTING_LAST_TESTED_AT = 'hms.last_tested_at';
    private const SETTING_LAST_TEST_STATUS = 'hms.last_test_status';
    private const SETTING_LAST_TEST_MESSAGE = 'hms.last_test_message';
    private const SETTING_LAST_TEST_COUNT = 'hms.last_test_count';
    private const SETTING_PMS_API_ENABLED = 'pms_api.enabled';
    private const SETTING_PMS_API_TOKEN = 'pms_api.token';
    private const SETTING_PMS_API_TOKEN_GENERATED_AT = 'pms_api.token_generated_at';
    private const SETTING_PMS_API_TOKEN_REGENERATED_BY = 'pms_api.token_regenerated_by';

    public function __construct(
        private readonly HmsEmployeeSyncService $employeeSyncService,
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureAdminAccess($request);

        $this->ensurePmsApiToken($request->user()?->name);

        $settings = [
            'base_url' => IntegrationSetting::getValue(self::SETTING_BASE_URL, ''),
            'bearer_token' => IntegrationSetting::getValue(self::SETTING_TOKEN, ''),
            'enabled' => IntegrationSetting::getValue(self::SETTING_ENABLED, '1') === '1',
            'last_tested_at' => IntegrationSetting::getValue(self::SETTING_LAST_TESTED_AT),
            'last_test_status' => IntegrationSetting::getValue(self::SETTING_LAST_TEST_STATUS, 'unknown'),
            'last_test_message' => IntegrationSetting::getValue(self::SETTING_LAST_TEST_MESSAGE),
            'last_test_count' => IntegrationSetting::getValue(self::SETTING_LAST_TEST_COUNT),
        ];

        $pmsApi = $this->buildPmsApiDetails();

        return view('admin.hris', compact('settings', 'pmsApi'));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess($request);

        $validated = $request->validate([
            'base_url' => ['required', 'url', 'max:255'],
            'bearer_token' => ['required', 'string', 'max:1000'],
            'enabled' => ['nullable', 'boolean'],
        ]);

        $normalizedBaseUrl = rtrim(trim((string) $validated['base_url']), '/');
        $normalizedToken = trim((string) $validated['bearer_token']);

        IntegrationSetting::setValue(self::SETTING_BASE_URL, $normalizedBaseUrl);
        IntegrationSetting::setValue(self::SETTING_TOKEN, $normalizedToken);
        IntegrationSetting::setValue(self::SETTING_ENABLED, $request->boolean('enabled', true) ? '1' : '0');

        return back()->with('success', 'HMS connection settings saved.');
    }

    public function testConnection(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess($request);

        $baseUrl = rtrim(trim((string) IntegrationSetting::getValue(self::SETTING_BASE_URL, '')), '/');
        $token = trim((string) IntegrationSetting::getValue(self::SETTING_TOKEN, ''));

        if ($baseUrl === '' || $token === '') {
            return back()->with('error', 'Save the HMS base URL and bearer token before testing the connection.');
        }

        $testedAt = now();
        $status = 'failed';
        $message = 'Unable to connect to HMS.';
        $count = null;

        try {
            $response = Http::acceptJson()
                ->withToken($token)
                ->timeout(12)
                ->get($baseUrl . '/employees', [
                    'per_page' => 1,
                ]);

            if ($response->successful()) {
                $payload = $response->json();
                $countValue = data_get($payload, 'meta.total');
                if (is_array($countValue) || is_object($countValue)) {
                    $countValue = data_get($payload, 'meta.total_count')
                        ?? data_get($payload, 'meta.pagination.total')
                        ?? data_get($payload, 'total')
                        ?? null;
                }

                $count = is_scalar($countValue) ? $countValue : null;
                $status = 'connected';
                $message = 'HMS connection successful.';
            } else {
                $message = sprintf(
                    'HMS connection failed with HTTP %s%s',
                    $response->status(),
                    $response->body() !== '' ? '. Check token, URL, or API response.' : '.'
                );
            }
        } catch (\Throwable $e) {
            $message = 'HMS connection failed: ' . $e->getMessage();
        }

        IntegrationSetting::setValue(self::SETTING_LAST_TESTED_AT, $testedAt->toDateTimeString());
        IntegrationSetting::setValue(self::SETTING_LAST_TEST_STATUS, $status);
        IntegrationSetting::setValue(self::SETTING_LAST_TEST_MESSAGE, $message);
        IntegrationSetting::setValue(self::SETTING_LAST_TEST_COUNT, is_null($count) ? null : (string) $count);

        return back()->with($status === 'connected' ? 'success' : 'error', $message);
    }

    public function syncEmployees(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess($request);

        $baseUrl = rtrim(trim((string) IntegrationSetting::getValue(self::SETTING_BASE_URL, '')), '/');
        $token = trim((string) IntegrationSetting::getValue(self::SETTING_TOKEN, ''));
        $enabled = IntegrationSetting::getValue(self::SETTING_ENABLED, '1') === '1';

        if (! $enabled) {
            return back()->with('error', 'Enable HMS integration before syncing employees.');
        }

        if ($baseUrl === '' || $token === '') {
            return back()->with('error', 'Save the HMS base URL and bearer token before syncing employees.');
        }

        try {
            $summary = $this->employeeSyncService->sync($baseUrl, $token);
        } catch (\Throwable $e) {
            return back()->with('error', 'HMS employee sync failed: ' . $e->getMessage());
        }

        $message = sprintf(
            'Employee sync complete. Fetched: %d, Created: %d, Updated: %d, Skipped: %d, Failed: %d.',
            (int) ($summary['fetched'] ?? 0),
            (int) ($summary['created'] ?? 0),
            (int) ($summary['updated'] ?? 0),
            (int) ($summary['skipped'] ?? 0),
            (int) ($summary['failed'] ?? 0),
        );

        return back()
            ->with('success', $message)
            ->with('hms_sync_summary', $summary);
    }

    public function regeneratePmsApiToken(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess($request);

        $this->generateAndPersistPmsApiToken($request->user()?->name);

        return back()
            ->with('success', 'PMS API token regenerated. Previously shared credentials are now invalid.')
            ->with('open_pms_api_modal', true);
    }

    private function buildPmsApiDetails(): array
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $apiBaseUrl = $baseUrl !== '' ? $baseUrl . '/api/pms/v1' : '/api/pms/v1';

        return [
            'enabled' => IntegrationSetting::getValue(self::SETTING_PMS_API_ENABLED, '1') === '1',
            'base_url' => $apiBaseUrl,
            'token' => (string) IntegrationSetting::getValue(self::SETTING_PMS_API_TOKEN, ''),
            'generated_at' => IntegrationSetting::getValue(self::SETTING_PMS_API_TOKEN_GENERATED_AT),
            'regenerated_by' => IntegrationSetting::getValue(self::SETTING_PMS_API_TOKEN_REGENERATED_BY),
            'available_data' => [
                'Employees',
                'Offices',
                'Performance Periods',
                'Top Performers',
                'IDP List',
            ],
            'sample_endpoints' => [
                $apiBaseUrl . '/employees',
                $apiBaseUrl . '/offices',
                $apiBaseUrl . '/performance-periods',
                $apiBaseUrl . '/top-performers',
                $apiBaseUrl . '/idp-list',
            ],
        ];
    }

    private function ensurePmsApiToken(?string $actorName = null): void
    {
        $existingToken = trim((string) IntegrationSetting::getValue(self::SETTING_PMS_API_TOKEN, ''));
        if ($existingToken !== '') {
            return;
        }

        $this->generateAndPersistPmsApiToken($actorName);
    }

    private function generateAndPersistPmsApiToken(?string $actorName = null): string
    {
        $token = 'pms_' . Str::lower(bin2hex(random_bytes(32)));

        IntegrationSetting::setValue(self::SETTING_PMS_API_ENABLED, '1');
        IntegrationSetting::setValue(self::SETTING_PMS_API_TOKEN, $token);
        IntegrationSetting::setValue(self::SETTING_PMS_API_TOKEN_GENERATED_AT, now()->toDateTimeString());
        IntegrationSetting::setValue(
            self::SETTING_PMS_API_TOKEN_REGENERATED_BY,
            trim((string) ($actorName ?? 'System'))
        );

        return $token;
    }

    private function ensureAdminAccess(Request $request): void
    {
        $user = $request->user();

        abort_if(!$user || strtolower((string) $user->role) !== 'admin', 403, 'Unauthorized.');
    }
}
