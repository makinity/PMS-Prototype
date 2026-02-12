<?php

namespace App\Http\Controllers\StageOne\Planning;

use App\Http\Controllers\Controller;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UwpPmtReviewController extends Controller
{
    // ✅ PMT screens: Stage I → PMT Review

    public function index(Request $request)
    {
        $this->ensureRole($request->user()->role, ['pmt']);

        $uwps = UnitWorkPlan::with(['office', 'performancePeriod', 'creator'])
            ->where('status', UnitWorkPlan::STATUS_ENDORSED)
            ->orderByDesc('endorsed_at')
            ->paginate(10);

        return view('stages.stage1.pmt_review.index', compact('uwps'));
    }

    public function show(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['pmt']);

        $uwp = UnitWorkPlan::with([
            'office',
            'performancePeriod',
            'creator',
            'mfos.successIndicators.qetStandards',
            'assignments.employee',
        ])->findOrFail($id);

        return view('stages.stage1.pmt_review.show', compact('uwp'));
    }

    public function approve(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['pmt']);

        $uwp = UnitWorkPlan::findOrFail($id);

        if ($uwp->status !== UnitWorkPlan::STATUS_ENDORSED) {
            return back()->with('error', 'Only Endorsed UWPs can be PMT approved.');
        }

        DB::transaction(function () use ($uwp) {
            $uwp->update([
                'status' => UnitWorkPlan::STATUS_PMT_APPROVED,
                'approved_at' => now(),
                'locked_at' => $uwp->locked_at ?? now(),
            ]);
        });

        return redirect()
            ->route('stage1.pmt_review.index')
            ->with('success', 'UWP approved by PMT. Eligible for OPCR generation.');
    }

    private function ensureRole(string $role, array $allowed): void
    {
        if (!in_array($role, $allowed, true)) {
            abort(403, 'Unauthorized.');
        }
    }
}
