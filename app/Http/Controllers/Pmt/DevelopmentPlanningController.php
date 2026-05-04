<?php

namespace App\Http\Controllers\Pmt;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentPlan;
use App\Models\Ipcr;
use App\Models\PerformancePeriod;
use App\Services\DevelopmentPlanningService;
use Illuminate\Http\Request;

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
        $summaryCounts = $planningService->summaryCounts($candidates);

        $infoMessage = null;
        if (!$activePeriod) {
            $infoMessage = 'No active performance period is configured.';
        } elseif ($candidates->isEmpty()) {
            $infoMessage = 'No officially released low-performing employee results found for the active period.';
        }

        return view('pmt.development-planning.index', [
            'activePeriod' => $activePeriod,
            'candidates' => $candidates,
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
}
