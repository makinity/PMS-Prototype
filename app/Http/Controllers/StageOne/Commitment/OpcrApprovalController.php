<?php

namespace App\Http\Controllers\StageOne\Commitment;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Opcr;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpcrApprovalController extends Controller
{
    // ✅ Dept Head screens: Stage I → OPCR Approval
    // Approving OPCR triggers IPCR generation (system rule)

    public function index(Request $request)
    {
        $this->ensureRole($request->user()->role, ['dept-head']);

        $opcrs = Opcr::with(['unitWorkPlan.office', 'unitWorkPlan.performancePeriod'])
            ->where('status', Opcr::STATUS_FOR_REVIEW)
            ->orderByDesc('id')
            ->paginate(10);

        return view('stages.stage1.opcr_approval.index', compact('opcrs'));
    }

    public function show(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['dept-head']);

        $opcr = Opcr::with([
            'unitWorkPlan.office',
            'unitWorkPlan.performancePeriod',
            'unitWorkPlan.mfos.successIndicators',
        ])->findOrFail($id);

        return view('stages.stage1.opcr_approval.show', compact('opcr'));
    }

    public function approve(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['dept-head']);

        $opcr = Opcr::with(['unitWorkPlan.uwpFunctions.mfos.successIndicators.assignments.employee'])->findOrFail($id);

        if ($opcr->status !== Opcr::STATUS_FOR_REVIEW) {
            return back()->with('error', 'Only OPCR for review can be approved.');
        }

        $uwp = UnitWorkPlan::with(['uwpFunctions.mfos.successIndicators.assignments.employee'])->findOrFail($opcr->unit_work_plan_id);

        if ($uwp->status !== UnitWorkPlan::STATUS_PMT_APPROVED) {
            return back()->with('error', 'OPCR source UWP must be PMT approved.');
        }

        $createdCount = 0;

        DB::transaction(function () use ($opcr, $uwp, $request, &$createdCount) {
            // Approve OPCR
            $opcr->update([
                'status' => Opcr::STATUS_APPROVED,
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
                'locked_at' => now(),
            ]);

            // Generate IPCR per assigned employee (idempotent due to unique index)
            foreach ($uwp->assignments as $assignment) {
                $ipcr = Ipcr::firstOrCreate(
                    [
                        'opcr_id' => $opcr->id,
                        'employee_id' => $assignment->employee_id,
                    ],
                    [
                        'unit_work_plan_id' => $uwp->id,
                        'status' => Ipcr::STATUS_GENERATED,
                    ]
                );

                if ($ipcr->wasRecentlyCreated) {
                    $createdCount++;
                }
            }
        });

        return redirect()
            ->route('stage1.opcr_approval.index')
            ->with('success', "OPCR approved. IPCR generated for {$createdCount} employee(s).");
    }

    private function ensureRole(string $role, array $allowed): void
    {
        if (!in_array($role, $allowed, true)) {
            abort(403, 'Unauthorized.');
        }
    }
}
