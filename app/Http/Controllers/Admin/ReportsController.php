<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\PerformancePeriod;
use App\Services\AdminReportException;
use App\Services\AdminReportService;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function __construct(
        private readonly AdminReportService $reports,
        private readonly AuditLogService $auditLogs,
    ) {
    }

    public function index(Request $request): View
    {
        self::ensureAdmin($request);

        return view('admin.reports', [
            'reports' => $this->reports->definitions(),
            'offices' => Office::query()->orderBy('name')->get(['id', 'name']),
            'periods' => PerformancePeriod::query()->orderByDesc('start_date')->get(['id', 'name', 'start_date', 'end_date']),
            'filters' => [
                'report' => trim((string) $request->query('report', '')),
                'performance_period_id' => trim((string) $request->query('performance_period_id', '')),
                'office_id' => trim((string) $request->query('office_id', '')),
            ],
        ]);
    }

    public function preview(Request $request, string $report)
    {
        self::ensureAdmin($request);

        try {
            [$period, $office] = $this->resolveScope($request, $report);
            $response = $this->reports->makePreviewResponse($report, $period, $office);
            $this->logReportAction($request, 'preview_report', $report, $period, $office);

            return $response;
        } catch (AdminReportException $e) {
            return $this->redirectWithError($request, $report, $e->getMessage());
        }
    }

    public function download(Request $request, string $report)
    {
        self::ensureAdmin($request);

        try {
            [$period, $office] = $this->resolveScope($request, $report);
            $response = $this->reports->makeDownloadResponse($report, $period, $office);
            $this->logReportAction($request, 'download_report', $report, $period, $office);

            return $response;
        } catch (AdminReportException $e) {
            return $this->redirectWithError($request, $report, $e->getMessage());
        }
    }

    private function resolveScope(Request $request, string $report): array
    {
        abort_unless($this->reports->exists($report), 404);

        $validated = $request->validate([
            'performance_period_id' => ['required', 'integer', 'exists:performance_periods,id'],
            'office_id' => ['required', 'integer', 'exists:offices,id'],
        ]);

        $period = PerformancePeriod::query()->findOrFail((int) $validated['performance_period_id']);
        $office = Office::query()->findOrFail((int) $validated['office_id']);

        return [$period, $office];
    }

    private function redirectWithError(Request $request, string $report, string $message): RedirectResponse
    {
        return redirect()
            ->route('admin.reports', [
                'report' => $report,
                'performance_period_id' => $request->query('performance_period_id'),
                'office_id' => $request->query('office_id'),
            ])
            ->with('error', $message);
    }

    private function logReportAction(
        Request $request,
        string $actionKey,
        string $report,
        PerformancePeriod $period,
        Office $office
    ): void {
        $actor = $request->user();

        $this->auditLogs->log([
            'actor_user_id' => $actor?->id,
            'actor_name' => $actor?->name,
            'actor_role' => $actor?->role,
            'action_key' => $actionKey,
            'module_key' => 'admin.reports',
            'target_type' => 'report',
            'target_id' => $report,
            'route_name' => $request->route()?->getName(),
            'http_method' => strtoupper($request->method()),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success',
            'summary' => ucfirst(str_replace('_', ' ', $actionKey)) . ' ' . strtoupper($report) . ' report',
            'metadata' => [
                'report' => $report,
                'report_label' => $this->reports->label($report),
                'performance_period_id' => $period->id,
                'performance_period_name' => $period->name,
                'office_id' => $office->id,
                'office_name' => $office->name,
            ],
        ]);
    }

    private static function ensureAdmin(Request $request): void
    {
        $actor = $request->user();
        abort_if(!$actor || strtolower((string) $actor->role) !== 'admin', 403, 'Unauthorized.');
    }
}
