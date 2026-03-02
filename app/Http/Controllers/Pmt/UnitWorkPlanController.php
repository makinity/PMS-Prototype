<?php

namespace App\Http\Controllers\Pmt;

use App\Http\Controllers\Controller;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UnitWorkPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'pmt') abort(403, 'Unauthorized.');

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $hasStatusParam = $request->exists('status');

        $statusRaw = $request->query('status', null);
        $status = strtolower(trim((string) $statusRaw));

        $allowedStatuses = [
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
                                                ->with(['qetStandards', 'assignments.employee']);
                                        },
                                    ]);
                            },
                        ]);
                },
            ])
            ->where('status', '!=', UnitWorkPlan::STATUS_DRAFT);

        if ($hasStatusParam) {
            if ($status === UnitWorkPlan::STATUS_DRAFT) {
                $uwpsQuery->where('status', UnitWorkPlan::STATUS_ENDORSED);
            } elseif ($status !== '' && in_array($status, $allowedStatuses, true)) {
                $uwpsQuery->where('status', $status);
            }
        }

        if ($activePeriod) {
            $uwpsQuery->where('performance_period_id', $activePeriod->id);
        }

        $uwps = $uwpsQuery
            ->orderByDesc('endorsed_at')
            ->orderByDesc('id')
            ->get();

        return view('pmt.uwp', [
            'uwps' => $uwps,
            'activePeriod' => $activePeriod,
            'selectedStatus' => ($hasStatusParam && $status !== '' && in_array($status, $allowedStatuses, true)) ? $status : '',
        ]);
    }

    public function review(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'pmt') {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'unit_work_plan_id' => ['required', 'integer', 'exists:unit_work_plans,id'],
            'action' => ['required', Rule::in(['approve', 'return'])],
            'remarks' => ['nullable', 'string'],
        ]);

        $result = DB::transaction(function () use ($validated) {
            $uwp = UnitWorkPlan::query()
                ->where('id', $validated['unit_work_plan_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($uwp->status !== UnitWorkPlan::STATUS_ENDORSED) {
                return ['ok' => false, 'message' => 'Only Endorsed UWPs can be reviewed by PMT.'];
            }

            if ($validated['action'] === 'return') {
                $remarks = trim((string) ($validated['remarks'] ?? ''));
                if ($remarks === '') {
                    return ['ok' => false, 'message' => 'Remarks are required when returning a UWP.'];
                }

                $uwp->forceFill([
                    'status' => UnitWorkPlan::STATUS_RETURNED,
                    'endorsed_at' => null,
                    'approved_at' => null,
                    'locked_at' => null,
                    'submitted_at' => null,
                ])->save();

                return ['ok' => true, 'message' => 'UWP returned to Supervisor for revision.'];
            }

            $uwp->forceFill([
                'status' => UnitWorkPlan::STATUS_PMT_APPROVED,
                'approved_at' => now(),
                'locked_at' => now(),
            ])->save();

            return ['ok' => true, 'message' => 'UWP approved by PMT. Status is now PMT Approved.'];
        });

        if (!$result['ok']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function approve(Request $request)
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
        if ($user->role !== 'pmt') {
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
            ])->validate();
        } catch (ValidationException $e) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $e->validator->errors()->first() ?: 'Invalid approval request.',
                ], 422);
            }

            throw $e;
        }

        try {
            $result = DB::transaction(function () use ($validated) {
                $uwp = UnitWorkPlan::query()
                    ->where('id', $validated['unit_work_plan_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($uwp->status !== UnitWorkPlan::STATUS_ENDORSED) {
                    return [
                        'ok' => false,
                        'status_code' => 422,
                        'message' => 'Only Endorsed UWPs can be PMT approved.',
                    ];
                }

                $approvedAt = now();
                $lockedAt = now();

                $uwp->forceFill([
                    'status' => UnitWorkPlan::STATUS_PMT_APPROVED,
                    'approved_at' => $approvedAt,
                    'locked_at' => $lockedAt,
                ])->save();

                return [
                    'ok' => true,
                    'uwp_id' => (int) $uwp->id,
                    'status' => UnitWorkPlan::STATUS_PMT_APPROVED,
                    'approved_at' => $approvedAt->toDateTimeString(),
                    'locked_at' => $lockedAt->toDateTimeString(),
                    'message' => 'UWP approved by PMT. Status is now PMT Approved.',
                ];
            });
        } catch (\Throwable $e) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to approve UWP right now.',
                ], 500);
            }

            throw $e;
        }

        if (!$result['ok']) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Unable to approve UWP.',
                ], (int) ($result['status_code'] ?? 422));
            }

            return back()->with('error', $result['message']);
        }

        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'uwp_id' => (int) ($result['uwp_id'] ?? $validated['unit_work_plan_id']),
                'status' => UnitWorkPlan::STATUS_PMT_APPROVED,
                'approved_at' => $result['approved_at'] ?? now()->toDateTimeString(),
                'locked_at' => $result['locked_at'] ?? now()->toDateTimeString(),
                'message' => $result['message'] ?? 'UWP approved by PMT.',
            ]);
        }

        return back()->with('success', $result['message']);
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
        if ($user->role !== 'pmt') {
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
                return response()->json([
                    'success' => false,
                    'message' => $e->validator->errors()->first() ?: 'Invalid return request.',
                ], 422);
            }

            throw $e;
        }

        $remarks = trim((string) $validated['remarks']);
        if ($remarks === '') {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Remarks are required when returning a UWP.',
                ], 422);
            }

            return back()->with('error', 'Remarks are required when returning a UWP.');
        }

        try {
            $result = DB::transaction(function () use ($validated, $user, $remarks) {
                $uwp = UnitWorkPlan::query()
                    ->whereKey($validated['unit_work_plan_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($uwp->status !== UnitWorkPlan::STATUS_ENDORSED) {
                    return [
                        'ok' => false,
                        'status_code' => 422,
                        'message' => 'Only Endorsed UWPs can be returned by PMT.',
                    ];
                }

                $returnedAt = now();

                $uwp->forceFill([
                    'status' => UnitWorkPlan::STATUS_RETURNED,
                    'returned_at' => $returnedAt,
                    'returned_by' => $user->id,
                    'returned_by_role' => 'pmt',
                    'return_remarks' => $remarks,
                    'approved_at' => null,
                    'locked_at' => null,
                    'submitted_at' => null,
                ])->save();

                return [
                    'ok' => true,
                    'uwp_id' => (int) $uwp->id,
                    'status' => UnitWorkPlan::STATUS_RETURNED,
                    'returned_at' => $returnedAt->toDateTimeString(),
                    'returned_by_role' => 'pmt',
                    'return_remarks' => $remarks,
                    'message' => 'UWP returned to Supervisor for revision.',
                ];
            });
        } catch (\Throwable $e) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to return UWP right now.',
                ], 500);
            }

            throw $e;
        }

        if (!$result['ok']) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Unable to return UWP.',
                ], (int) ($result['status_code'] ?? 422));
            }

            return back()->with('error', $result['message']);
        }

        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'uwp_id' => (int) ($result['uwp_id'] ?? $validated['unit_work_plan_id']),
                'status' => UnitWorkPlan::STATUS_RETURNED,
                'returned_at' => $result['returned_at'] ?? now()->toDateTimeString(),
                'returned_by_role' => 'pmt',
                'return_remarks' => $result['return_remarks'] ?? $remarks,
                'message' => $result['message'] ?? 'UWP returned to Supervisor for revision.',
            ]);
        }

        return back()->with('success', $result['message']);
    }
}
