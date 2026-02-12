<?php

namespace App\Http\Controllers\StageOne\Planning;

use App\Http\Controllers\Controller;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitWorkPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = UnitWorkPlan::query()
            ->with(['office', 'performancePeriod'])
            ->orderByDesc('id');

        if ($user->role === 'supervisor') {
            $query->where('created_by', $user->id);
        }

        $uwps = $query->paginate(10);

        return view('stages.stage1.uwp.index', compact('uwps'));
    }

    public function create(Request $request)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        // Your blade will likely load offices/periods via dropdown
        return view('stages.stage1.uwp.create');
    }

    public function store(Request $request)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        $data = $request->validate([
            'office_id' => ['required', 'integer', 'exists:offices,id'],
            'performance_period_id' => ['required', 'integer', 'exists:performance_periods,id'],
        ]);

        $user = $request->user();

        $uwp = UnitWorkPlan::create([
            'office_id' => $data['office_id'],
            'performance_period_id' => $data['performance_period_id'],
            'created_by' => $user->id,
            'status' => UnitWorkPlan::STATUS_DRAFT,
        ]);

        return redirect()
            ->route('stage1.uwp.show', $uwp->id)
            ->with('success', 'UWP created (Draft).');
    }

    public function show(Request $request, int $id)
    {
        $uwp = UnitWorkPlan::with([
            'office',
            'performancePeriod',
            'creator',
            'mfos.successIndicators.qetStandards',
            'assignments.employee',
            'opcr',
        ])->findOrFail($id);

        $this->ensureCanViewUwp($request->user(), $uwp);

        return view('stages.stage1.uwp.show', compact('uwp'));
    }

    public function edit(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        $uwp = UnitWorkPlan::findOrFail($id);
        $this->ensureCanViewUwp($request->user(), $uwp);

        if (!$uwp->isDraft()) {
            return back()->with('error', 'UWP is read-only once submitted.');
        }

        return view('stages.stage1.uwp.edit', compact('uwp'));
    }

    public function update(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        $uwp = UnitWorkPlan::findOrFail($id);
        $this->ensureCanViewUwp($request->user(), $uwp);

        if (!$uwp->isDraft()) {
            return back()->with('error', 'UWP is read-only once submitted.');
        }

        $data = $request->validate([
            'office_id' => ['required', 'integer', 'exists:offices,id'],
            'performance_period_id' => ['required', 'integer', 'exists:performance_periods,id'],
        ]);

        $uwp->update($data);

        return redirect()
            ->route('stage1.uwp.show', $uwp->id)
            ->with('success', 'UWP updated.');
    }

    public function submit(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['supervisor']);

        $uwp = UnitWorkPlan::with(['mfos.successIndicators', 'assignments'])->findOrFail($id);
        $this->ensureCanViewUwp($request->user(), $uwp);

        if (!$uwp->isDraft()) {
            return back()->with('error', 'Only Draft UWP can be submitted.');
        }

        if ($uwp->mfos()->count() === 0) {
            return back()->with('error', 'Cannot submit: no MFOs found.');
        }
        if ($uwp->assignments()->count() === 0) {
            return back()->with('error', 'Cannot submit: no assigned employees.');
        }

        DB::transaction(function () use ($uwp) {
            $uwp->update([
                'status' => UnitWorkPlan::STATUS_SUBMITTED,
                'submitted_at' => now(),
                'locked_at' => now(),
            ]);
        });

        return redirect()
            ->route('stage1.uwp.show', $uwp->id)
            ->with('success', 'UWP submitted. Now read-only.');
    }

    private function ensureRole(string $role, array $allowed): void
    {
        if (!in_array($role, $allowed, true)) {
            abort(403, 'Unauthorized.');
        }
    }

    private function ensureCanViewUwp($user, UnitWorkPlan $uwp): void
    {
        if (in_array($user->role, ['admin', 'pmt', 'dept-head'], true)) {
            return;
        }

        if ($user->role === 'supervisor' && $uwp->created_by === $user->id) {
            return;
        }

        abort(403, 'Unauthorized.');
    }
}
