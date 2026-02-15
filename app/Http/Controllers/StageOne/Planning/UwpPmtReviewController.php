<?php

namespace App\Http\Controllers\StageOne\Planning;

use App\Http\Controllers\Controller;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UwpPmtReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }
        if ($user->role !== 'pmt') {
            abort(403, 'Unauthorized.');
        }

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $status = strtolower(trim($request->string('status')->toString()));
        $allowedStatuses = [
            UnitWorkPlan::STATUS_DRAFT,
            UnitWorkPlan::STATUS_SUBMITTED,
            UnitWorkPlan::STATUS_ENDORSED,
            UnitWorkPlan::STATUS_PMT_APPROVED,
            UnitWorkPlan::STATUS_RETURNED,
        ];

        $uwpsQuery = UnitWorkPlan::query()
            ->with([
                'office.head',
                'performancePeriod',
                'creator',
                'uwpFunctions' => function ($query) {
                    $query->orderBy('sort_order')
                        ->with([
                            'mfos' => function ($mfoQuery) {
                                $mfoQuery->orderBy('sort_order')
                                    ->with([
                                        'successIndicators' => function ($indicatorQuery) {
                                            $indicatorQuery->orderBy('sort_order')
                                                ->with([
                                                    'qetStandards',
                                                    'assignments.employee',
                                                ]);
                                        },
                                    ]);
                            },
                        ]);
                },
            ]);

        if (!$status) {
            $uwpsQuery->where('status', UnitWorkPlan::STATUS_ENDORSED);
        } elseif (in_array($status, $allowedStatuses, true)) {
            $uwpsQuery->where('status', $status);
        } else {
            $uwpsQuery->where('status', UnitWorkPlan::STATUS_ENDORSED);
        }

        if ($activePeriod) {
            $uwpsQuery->where('performance_period_id', $activePeriod->id);
        }

        $uwps = $uwpsQuery
            ->orderByDesc('endorsed_at')
            ->orderByDesc('id')
            ->get();

        return view('pmt.uwp', compact('uwps', 'activePeriod'));
    }

    public function approve(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }
        if ($user->role !== 'pmt') {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'unit_work_plan_id' => ['required', 'integer', 'exists:unit_work_plans,id'],
        ]);

        $result = DB::transaction(function () use ($validated) {
            $uwp = UnitWorkPlan::query()
                ->where('id', $validated['unit_work_plan_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($uwp->status !== UnitWorkPlan::STATUS_ENDORSED) {
                return [
                    'ok' => false,
                    'message' => 'Only Endorsed UWPs can be PMT approved.',
                ];
            }

            $uwp->forceFill([
                'status' => UnitWorkPlan::STATUS_PMT_APPROVED,
                'approved_at' => now(),
                'locked_at' => now(),
            ])->save();

            return [
                'ok' => true,
                'message' => 'UWP approved by PMT. Status is now PMT Approved.',
            ];
        });

        if (!$result['ok']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }
}
