<?php

namespace App\Http\Controllers\StageOne\Planning;

use App\Http\Controllers\Controller;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UwpDeptHeadReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
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
                'uwpFunctions' => function ($q) {
                    $q->orderBy('sort_order')
                      ->with([
                          'mfos' => function ($mq) {
                              $mq->orderBy('sort_order')
                                 ->with([
                                     'successIndicators' => function ($iq) {
                                         $iq->orderBy('sort_order')
                                            ->with([
                                                'qetStandards',
                                                'assignments.employee',
                                            ]);
                                     }
                                 ]);
                          }
                      ]);
                },
            ])
            ->whereHas('office.head', fn ($q) => $q->whereKey($user->id));

        if (!$status) {
            $uwpsQuery->where('status', UnitWorkPlan::STATUS_SUBMITTED);
        } elseif (in_array($status, $allowedStatuses, true)) {
            $uwpsQuery->where('status', $status);
        } else {
            $uwpsQuery->where('status', UnitWorkPlan::STATUS_SUBMITTED);
        }

        if ($activePeriod) {
            $uwpsQuery->where('performance_period_id', $activePeriod->id);
        }

        $uwps = $uwpsQuery
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get();

        return view('dept-head.uwp', [
            'uwps' => $uwps,
            'activePeriod' => $activePeriod,
        ]);
    }

    /**
     * POST action from modal: endorse to PMT OR return to supervisor
     */
    public function review(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'unit_work_plan_id' => ['required', 'integer', 'exists:unit_work_plans,id'],
            'action' => ['required', Rule::in(['endorse', 'return'])],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        return DB::transaction(function () use ($validated, $user) {
            /** @var UnitWorkPlan $uwp */
            $uwp = UnitWorkPlan::query()
                ->where('id', $validated['unit_work_plan_id'])
                ->whereHas('office.head', fn ($q) => $q->whereKey($user->id))
                ->lockForUpdate()
                ->firstOrFail();

            // Only allow actions when UWP is currently submitted
            if ($uwp->status !== UnitWorkPlan::STATUS_SUBMITTED) {
                return back()->with('error', 'This UWP is not in a reviewable status.');
            }

            if ($validated['action'] === 'endorse') {
                $uwp->forceFill([
                    'status' => UnitWorkPlan::STATUS_ENDORSED,
                    'endorsed_at' => now(),
                    // optional: lock after endorse if your flow requires
                    // 'locked_at' => now(),
                ])->save();

                // Optional: persist remarks to audit log or separate table
                // e.g. UwpReviewLog::create([...])

                return back()->with('success', 'UWP endorsed and forwarded to PMT.');
            }

            // action === return
            $remarks = trim((string) ($validated['remarks'] ?? ''));

            if ($remarks === '') {
                return back()->with('error', 'Remarks are required when returning the UWP.');
            }

            // If you have a STATUS_RETURNED constant, use it.
            // If not, keep 'returned' as string or add constant in model.
            $uwp->forceFill([
                'status' => UnitWorkPlan::STATUS_RETURNED,
                'endorsed_at' => null,
                'locked_at' => null,
            ])->save();

            // Optional: store remarks somewhere (recommended)
            // e.g. UwpReviewLog::create([
            //   'unit_work_plan_id' => $uwp->id,
            //   'reviewed_by' => $user->id,
            //   'action' => 'returned',
            //   'remarks' => $remarks,
            // ]);

            return back()->with('success', 'UWP returned to supervisor for revision.');
        });
    }
}
