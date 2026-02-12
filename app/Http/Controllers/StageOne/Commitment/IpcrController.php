<?php

namespace App\Http\Controllers\StageOne\Commitment;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IpcrController extends Controller
{
    // ✅ Employee screens: Stage I → My IPCR

    public function myIpcr(Request $request)
    {
        $this->ensureRole($request->user()->role, ['employee']);

        $ipcr = Ipcr::with([
            'unitWorkPlan.office',
            'unitWorkPlan.performancePeriod',
            'unitWorkPlan.mfos.successIndicators',
            'opcr',
        ])
            ->where('employee_id', $request->user()->id)
            ->orderByDesc('id')
            ->first();

        return view('stages.stage1.ipcr.my', compact('ipcr'));
    }

    public function commit(Request $request, int $id)
    {
        $this->ensureRole($request->user()->role, ['employee']);

        $ipcr = Ipcr::findOrFail($id);

        if ($ipcr->employee_id !== $request->user()->id) {
            abort(403, 'Unauthorized.');
        }

        if ($ipcr->status !== Ipcr::STATUS_GENERATED) {
            return back()->with('error', 'Only Generated IPCR can be committed.');
        }

        DB::transaction(function () use ($ipcr) {
            $ipcr->update([
                'status' => Ipcr::STATUS_COMMITTED,
                'committed_at' => now(),
                'locked_at' => now(),
            ]);
        });

        return redirect()
            ->route('stage1.ipcr.my')
            ->with('success', 'IPCR committed. Stage I completed for you.');
    }

    private function ensureRole(string $role, array $allowed): void
    {
        if (!in_array($role, $allowed, true)) {
            abort(403, 'Unauthorized.');
        }
    }
}
