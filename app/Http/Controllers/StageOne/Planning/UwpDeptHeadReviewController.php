<?php

namespace App\Http\Controllers\StageOne\Planning;

use App\Http\Controllers\Controller;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            UnitWorkPlan::STATUS_SUBMITTED,
            UnitWorkPlan::STATUS_ENDORSED,
            UnitWorkPlan::STATUS_PMT_APPROVED,
            UnitWorkPlan::STATUS_RETURNED,
        ];
        $hasValidStatus = $status !== '' && in_array($status, $allowedStatuses, true);

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

        $uwpsQuery->where('status', '!=', UnitWorkPlan::STATUS_DRAFT);

        if ($hasValidStatus) {
            $uwpsQuery->where('status', $status);
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
            'selectedStatus' => $hasValidStatus ? $status : '',
        ]);
    }

    /**
     * POST action from modal: endorse to PMT OR return to supervisor
     */
    public function review(Request $request)
    {
        $expectsJson = $request->expectsJson() || $request->ajax();

        $user = Auth::user();
        if (!$user) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 403);
            }

            abort(403, 'Unauthorized.');
        }

        try {
            $validated = validator($request->all(), [
                'unit_work_plan_id' => ['required', 'exists:unit_work_plans,id'],
                'action' => ['required', Rule::in(['endorse', 'return'])],
                'remarks' => ['nullable', 'string'],
            ])->validate();
        } catch (ValidationException $e) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $e->validator->errors()->first() ?: 'Invalid review request.',
                ], 422);
            }

            throw $e;
        }

        $uwp = UnitWorkPlan::with('office.head')
            ->findOrFail($validated['unit_work_plan_id']);

        if (!$uwp->office || !$uwp->office->head || (int) $uwp->office->head->id !== (int) $user->id) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to review this Unit Work Plan.',
                ], 403);
            }

            return back()->with('error', 'You are not authorized to review this Unit Work Plan.');
        }

        $reviewable = in_array($uwp->status, [
            UnitWorkPlan::STATUS_SUBMITTED,
            UnitWorkPlan::STATUS_ENDORSED,
        ], true);

        if (!$reviewable) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only submitted or endorsed Unit Work Plans can be reviewed.',
                ], 422);
            }

            return back()->with('error', 'Only submitted or endorsed Unit Work Plans can be reviewed.');
        }

        if ($validated['action'] === 'endorse' && $uwp->status !== UnitWorkPlan::STATUS_SUBMITTED) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only submitted Unit Work Plans can be endorsed.',
                ], 422);
            }

            return back()->with('error', 'Only submitted Unit Work Plans can be endorsed.');
        }

        if ($validated['action'] === 'return' && empty(trim((string) ($validated['remarks'] ?? '')))) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Remarks are required when returning a Unit Work Plan.',
                ], 422);
            }

            return back()->with('error', 'Remarks are required when returning a Unit Work Plan.');
        }

        try {
            DB::transaction(function () use ($uwp, $validated, $user) {
                if ($validated['action'] === 'endorse') {
                    $uwp->status = UnitWorkPlan::STATUS_ENDORSED;
                    $uwp->endorsed_at = now();
                    $uwp->returned_at = null;
                    $uwp->returned_by = null;
                    $uwp->returned_by_role = null;
                    $uwp->return_remarks = null;
                }

                if ($validated['action'] === 'return') {
                    $uwp->status = UnitWorkPlan::STATUS_RETURNED;
                    $uwp->returned_at = now();
                    $uwp->returned_by = $user->id;
                    $uwp->returned_by_role = 'dept-head';
                    $uwp->return_remarks = trim((string) ($validated['remarks'] ?? ''));
                    $uwp->endorsed_at = null;
                    $uwp->approved_at = null;
                    $uwp->locked_at = null;
                    $uwp->submitted_at = null;
                }

                $uwp->save();
            });
        } catch (\Throwable $e) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to review Unit Work Plan.',
                ], 500);
            }

            throw $e;
        }

        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'uwp_id' => (int) $uwp->id,
                'status' => (string) $uwp->status,
                'endorsed_at' => optional($uwp->endorsed_at)->toDateTimeString(),
                'returned_at' => optional($uwp->returned_at)->toDateTimeString(),
                'returned_by_role' => $uwp->returned_by_role,
                'return_remarks' => $uwp->return_remarks,
            ]);
        }

        return redirect()
            ->route('dept-head.uwp.index', $request->only('status'))
            ->with('success', 'Unit Work Plan successfully reviewed.');
    }

    public function returnUwp(Request $request)
    {
        $expectsJson = $request->expectsJson() || $request->ajax();

        $user = Auth::user();
        if (!$user) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 403);
            }

            abort(403, 'Unauthorized.');
        }

        try {
            $validated = validator($request->all(), [
                'unit_work_plan_id' => ['required', 'integer', 'exists:unit_work_plans,id'],
                'remarks' => ['required', 'string', 'max:5000'],
            ])->validate();
        } catch (ValidationException $e) {
            if ($expectsJson) {
                $message = $e->validator->errors()->first() ?: 'Invalid return request.';

                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            throw $e;
        }

        $remarks = trim((string) $validated['remarks']);
        if ($remarks === '') {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Remarks are required when returning a Unit Work Plan.',
                ], 422);
            }

            return back()->with('error', 'Remarks are required when returning a Unit Work Plan.');
        }

        try {
            $result = DB::transaction(function () use ($validated, $user, $remarks) {
                $uwp = UnitWorkPlan::query()
                    ->with('office.head')
                    ->whereKey($validated['unit_work_plan_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$uwp->office || !$uwp->office->head || (int) $uwp->office->head->id !== (int) $user->id) {
                    return [
                        'ok' => false,
                        'status_code' => 403,
                        'message' => 'You are not authorized to review this Unit Work Plan.',
                    ];
                }

                if ($uwp->status !== UnitWorkPlan::STATUS_SUBMITTED) {
                    return [
                        'ok' => false,
                        'status_code' => 422,
                        'message' => 'Only submitted Unit Work Plans can be returned.',
                    ];
                }

                $returnedAt = now();

                $uwp->forceFill([
                    'status' => UnitWorkPlan::STATUS_RETURNED,
                    'returned_at' => $returnedAt,
                    'returned_by' => $user->id,
                    'returned_by_role' => 'dept-head',
                    'return_remarks' => $remarks,
                    'submitted_at' => null,
                    'endorsed_at' => null,
                    'approved_at' => null,
                    'locked_at' => null,
                ])->save();

                return [
                    'ok' => true,
                    'uwp_id' => (int) $uwp->id,
                    'status' => UnitWorkPlan::STATUS_RETURNED,
                    'returned_at' => $returnedAt->toDateTimeString(),
                    'returned_by_role' => 'dept-head',
                    'return_remarks' => $remarks,
                ];
            });
        } catch (\Throwable $e) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to return Unit Work Plan.',
                ], 500);
            }

            throw $e;
        }

        if (!($result['ok'] ?? false)) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Unable to return Unit Work Plan.',
                ], (int) ($result['status_code'] ?? 422));
            }

            return back()->with('error', $result['message'] ?? 'Unable to return Unit Work Plan.');
        }

        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'status' => UnitWorkPlan::STATUS_RETURNED,
                'returned_at' => $result['returned_at'] ?? now()->toDateTimeString(),
                'returned_by_role' => 'dept-head',
                'return_remarks' => $result['return_remarks'] ?? $remarks,
                'uwp_id' => (int) ($result['uwp_id'] ?? $validated['unit_work_plan_id']),
            ]);
        }

        return redirect()
            ->route('dept-head.uwp.index', $request->only('status'))
            ->with('success', 'Unit Work Plan returned to Supervisor.');
    }
}
