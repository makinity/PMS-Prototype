<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntegrationSetting;
use App\Services\HmsEmployeeSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HrisIntegrationController extends Controller
{
    private const SETTING_BASE_URL = 'hms.base_url';
    private const SETTING_TOKEN = 'hms.bearer_token';
    private const SETTING_ENABLED = 'hms.enabled';
    private const SETTING_LAST_TESTED_AT = 'hms.last_tested_at';
    private const SETTING_LAST_TEST_STATUS = 'hms.last_test_status';
    private const SETTING_LAST_TEST_MESSAGE = 'hms.last_test_message';
    private const SETTING_LAST_TEST_COUNT = 'hms.last_test_count';

    public function __construct(
        private readonly HmsEmployeeSyncService $employeeSyncService,
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureAdminAccess($request);

        $settings = [
            'base_url' => IntegrationSetting::getValue(self::SETTING_BASE_URL, ''),
            'bearer_token' => IntegrationSetting::getValue(self::SETTING_TOKEN, ''),
            'enabled' => IntegrationSetting::getValue(self::SETTING_ENABLED, '1') === '1',
            'last_tested_at' => IntegrationSetting::getValue(self::SETTING_LAST_TESTED_AT),
            'last_test_status' => IntegrationSetting::getValue(self::SETTING_LAST_TEST_STATUS, 'unknown'),
            'last_test_message' => IntegrationSetting::getValue(self::SETTING_LAST_TEST_MESSAGE),
            'last_test_count' => IntegrationSetting::getValue(self::SETTING_LAST_TEST_COUNT),
        ];

        $sampleEndpoints = $this->buildSampleEndpoints($settings['base_url']);

        return view('admin.hris', compact('settings', 'sampleEndpoints'));
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

    private function buildSampleEndpoints(string $baseUrl): array
    {
        $normalized = rtrim(trim($baseUrl), '/');
        if ($normalized === '') {
            return [];
        }

        return [
            $normalized . '/offices',
            $normalized . '/employees?per_page=15&updated_since=' . now()->subDay()->toIso8601String(),
            $normalized . '/employees/1',
        ];
    }

    private function ensureAdminAccess(Request $request): void
    {
        $user = $request->user();

        abort_if(!$user || strtolower((string) $user->role) !== 'admin', 403, 'Unauthorized.');
    }
}
