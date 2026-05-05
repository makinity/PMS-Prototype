<?php

namespace App\Http\Middleware;

use App\Models\IntegrationSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidPmsApiToken
{
    private const SETTING_PMS_API_ENABLED = 'pms_api.enabled';
    private const SETTING_PMS_API_TOKEN = 'pms_api.token';

    public function handle(Request $request, Closure $next): Response
    {
        $enabled = IntegrationSetting::getValue(self::SETTING_PMS_API_ENABLED, '1') === '1';
        if (! $enabled) {
            return response()->json([
                'message' => 'PMS API is currently disabled.',
            ], 503);
        }

        $expectedToken = trim((string) IntegrationSetting::getValue(self::SETTING_PMS_API_TOKEN, ''));
        $providedToken = trim((string) $request->bearerToken());

        if ($expectedToken === '' || $providedToken === '' || ! hash_equals($expectedToken, $providedToken)) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
