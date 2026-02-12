<?php

namespace App\Http\Controllers\StageOne\Planning;

use App\Http\Controllers\Controller;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UwpEndorsementController extends Controller
{
    // ✅ Dept Head screens: Stage I → UWP Endorsement

    public function index(Request $request)
    {
        $this->ensureRole($request->user()->role, ['dept-head']);

        $uwps = UnitWorkPlan::with(['office', 'performancePeriod', 'creator'])
            ->where('status', UnitWorkPlan::STATUS_SUBMITTED)
            ->orderByDesc('submitted_at')
            ->paginate(10);

        return view('stages.stage1.uwp_endorsement.index', compact('uwps'));
    }

    public function show(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['dept-head']);

        $uwp = UnitWorkPlan::with([
            'office',
            'performancePeriod',
            'creator',
            'uwpFunctions.mfos.successIndicators.qetStandards',
            'uwpFunctions.mfos.successIndicators.assignments.employee',
        ])->findOrFail($id);

        return view('stages.stage1.uwp_endorsement.show', compact('uwp'));
    }

    public function endorse(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['dept-head']);

        $uwp = UnitWorkPlan::findOrFail($id);

        if ($uwp->status !== UnitWorkPlan::STATUS_SUBMITTED) {
            return back()->with('error', 'Only Submitted UWPs can be endorsed.');
        }

        DB::transaction(function () use ($uwp) {
            $uwp->update([
                'status' => UnitWorkPlan::STATUS_ENDORSED,
                'endorsed_at' => now(),
            ]);
        });

        return redirect()
            ->route('stage1.uwp_endorsement.index')
            ->with('success', 'UWP endorsed and forwarded to PMT.');
    }

    private function ensureRole(string $role, array $allowed): void
    {
        if (!in_array($role, $allowed, true)) {
            abort(403, 'Unauthorized.');
        }
    }
}
