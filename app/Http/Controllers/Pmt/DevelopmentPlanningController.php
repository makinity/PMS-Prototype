<?php

namespace App\Http\Controllers\Pmt;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentPlan;
use App\Models\Ipcr;
use App\Models\PerformancePeriod;
use App\Services\DevelopmentPlanningService;
use App\Services\LndHandoffService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DevelopmentPlanningController extends Controller
{
    public function index(Request $request, DevelopmentPlanningService $planningService)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pmt', 403);

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $candidates = $planningService->getLowPerformerCandidates($activePeriod);
        
        $performerData = app(\App\Services\StageFourPerformerService::class)->getTopAndLowPerformers($activePeriod);
        $lowOffices = $performerData['low_offices'] ?? collect();

        $summaryCounts = $planningService->summaryCounts($candidates, $lowOffices);

        $infoMessage = null;
        if (!$activePeriod) {
            $infoMessage = 'No active performance period is configured.';
        } elseif ($candidates->isEmpty() && $lowOffices->isEmpty()) {
            $infoMessage = 'No officially released low-performing results found for the active period.';
        }

        return view('pmt.development-planning.index', [
            'activePeriod' => $activePeriod,
            'candidates' => $candidates,
            'lowOffices' => $lowOffices,
            'summaryCounts' => $summaryCounts,
            'infoMessage' => $infoMessage,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pmt', 403);

        $validated = $request->validate([
            'ipcr_id' => ['required', 'integer', 'exists:ipcrs,id'],
        ]);

        $ipcr = Ipcr::query()
            ->with(['employee.office', 'performancePeriod'])
            ->whereKey($validated['ipcr_id'])
            ->where('status', Ipcr::STATUS_RELEASED_BY_PMT)
            ->firstOrFail();

        $row = app(\App\Services\StageFourPerformerService::class)->resolveEmployeeRow($ipcr);
        abort_unless($row && in_array($row['official_rating'], ['Unsatisfactory', 'Poor'], true), 422);

        DevelopmentPlan::query()->firstOrCreate(
            [
                'ipcr_id' => $ipcr->id,
                'performance_period_id' => $ipcr->performance_period_id,
            ],
            [
                'employee_id' => $ipcr->employee_id,
                'office_id' => $ipcr->office_id,
                'source_score' => $row['official_score'],
                'source_rating' => $row['official_rating'],
                'status' => DevelopmentPlan::STATUS_DRAFT,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]
        );

        return redirect()->route('pmt.development-planning.index')
            ->with('success', 'Development planning draft created.');
    }

    public function show(Request $request, DevelopmentPlan $developmentPlan, DevelopmentPlanningService $planningService)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pmt', 403);

        $developmentPlan->load([
            'employee.office',
            'performancePeriod',
            'ipcr',
            'creator',
            'updater',
        ]);

        return view('pmt.development-planning.show', [
            'developmentPlan' => $developmentPlan,
            'statusLabel' => $planningService->formatStatusLabel((string) $developmentPlan->status),
        ]);
    }

    public function updateStatus(Request $request, DevelopmentPlan $developmentPlan)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pmt', 403);

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', [
                DevelopmentPlan::STATUS_DRAFT,
                DevelopmentPlan::STATUS_PENDING_DETAILS,
            ])],
            'pmt_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $developmentPlan->update([
            'status' => $validated['status'],
            'pmt_remarks' => $validated['pmt_remarks'] ?? null,
            'updated_by' => $user->id,
        ]);

        return redirect()
            ->route('pmt.development-planning.show', $developmentPlan)
            ->with('success', 'Development planning status updated.');
    }

    public function sendToLnd(Request $request, DevelopmentPlan $developmentPlan, LndHandoffService $handoffService)
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'pmt', 403);

        $developmentPlan->loadMissing(['employee.office', 'performancePeriod', 'ipcr']);

        try {
            $result = $handoffService->sendDevelopmentPlan($developmentPlan);

            $syncStatus = strtolower(trim((string) ($result['status'] ?? 'sent')));
            if (!in_array($syncStatus, [
                DevelopmentPlan::LND_SYNC_SENT,
                DevelopmentPlan::LND_SYNC_ACKNOWLEDGED,
            ], true)) {
                $syncStatus = DevelopmentPlan::LND_SYNC_SENT;
            }

            $developmentPlan->update([
                'lnd_sync_status' => $syncStatus,
                'lnd_reference_id' => $result['lnd_reference_id'] ?? null,
                'lnd_synced_at' => now(),
                'lnd_last_error' => null,
                'submitted_to_ld_at' => now(),
                'updated_by' => $user->id,
            ]);

            return response()->json([
                'ok' => true,
                'status' => $syncStatus,
                'label' => $this->formatLndSyncStatusLabel($syncStatus),
                'lnd_reference_id' => $developmentPlan->lnd_reference_id,
                'synced_at' => optional($developmentPlan->lnd_synced_at)->toISOString(),
                'message' => $result['message'] ?? 'Development plan sent to LND.',
            ]);
        } catch (\Throwable $e) {
            Log::warning('LND handoff failed', [
                'development_plan_id' => $developmentPlan->id,
                'error' => $e->getMessage(),
            ]);

            $developmentPlan->update([
                'lnd_sync_status' => DevelopmentPlan::LND_SYNC_FAILED,
                'lnd_last_error' => mb_substr((string) $e->getMessage(), 0, 1000),
                'updated_by' => $user->id,
            ]);

            return response()->json([
                'ok' => false,
                'status' => DevelopmentPlan::LND_SYNC_FAILED,
                'label' => $this->formatLndSyncStatusLabel(DevelopmentPlan::LND_SYNC_FAILED),
                'message' => $e->getMessage() !== '' ? $e->getMessage() : 'Failed to send development plan to LND.',
            ], 422);
        }
    }

    private function formatLndSyncStatusLabel(string $status): string
    {
        return match (strtolower(trim($status))) {
            DevelopmentPlan::LND_SYNC_SENT => 'Sent',
            DevelopmentPlan::LND_SYNC_ACKNOWLEDGED => 'Acknowledged',
            DevelopmentPlan::LND_SYNC_FAILED => 'Failed',
            default => 'Not Sent',
        };
    }
}
