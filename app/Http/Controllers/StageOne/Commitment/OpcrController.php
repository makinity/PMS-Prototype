<?php

namespace App\Http\Controllers\StageOne\Commitment;

use App\Http\Controllers\Controller;
use App\Models\Opcr;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpcrController extends Controller
{
    // ✅ Admin screens: Stage I → OPCR (Generate)

    public function index(Request $request)
    {
        $this->ensureRole($request->user()->role, ['admin', 'dept-head']);

        $opcrs = Opcr::with(['unitWorkPlan.office', 'unitWorkPlan.performancePeriod', 'generator'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('stages.stage1.opcr.index', compact('opcrs'));
    }

    public function create(Request $request)
    {
        $this->ensureRole($request->user()->role, ['admin']);

        // Only PMT-approved UWPs eligible
        $approvedUwps = UnitWorkPlan::with(['office', 'performancePeriod'])
            ->where('status', UnitWorkPlan::STATUS_PMT_APPROVED)
            ->orderByDesc('approved_at')
            ->get();

        return view('stages.stage1.opcr.create', compact('approvedUwps'));
    }

    public function store(Request $request)
    {
        $this->ensureRole($request->user()->role, ['admin']);

        $data = $request->validate([
            'unit_work_plan_id' => ['required', 'integer', 'exists:unit_work_plans,id'],
        ]);

        $uwp = UnitWorkPlan::with(['office', 'performancePeriod'])->findOrFail($data['unit_work_plan_id']);

        if ($uwp->status !== UnitWorkPlan::STATUS_PMT_APPROVED) {
            return back()->with('error', 'Selected UWP is not PMT approved.');
        }

        // Prevent duplicates (unique on unit_work_plan_id)
        $existing = Opcr::where('unit_work_plan_id', $uwp->id)->first();
        if ($existing) {
            return redirect()
                ->route('stage1.opcr.show', $existing->id)
                ->with('info', 'OPCR already exists for this UWP.');
        }

        $opcr = DB::transaction(function () use ($uwp, $request) {
            return Opcr::create([
                'unit_work_plan_id' => $uwp->id,
                'generated_by' => $request->user()->id,
                'status' => Opcr::STATUS_FOR_REVIEW,
            ]);
        });

        return redirect()
            ->route('stage1.opcr.show', $opcr->id)
            ->with('success', 'OPCR generated and set to For Department Head Review.');
    }

    public function show(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['admin', 'dept-head', 'supervisor', 'pmt']);

        $opcr = Opcr::with([
            'unitWorkPlan.office',
            'unitWorkPlan.performancePeriod',
            'unitWorkPlan.mfos.successIndicators',
            'generator',
            'approver',
        ])->findOrFail($id);

        return view('stages.stage1.opcr.show', compact('opcr'));
    }

    private function ensureRole(string $role, array $allowed): void
    {
        if (!in_array($role, $allowed, true)) {
            abort(403, 'Unauthorized.');
        }
    }
}
