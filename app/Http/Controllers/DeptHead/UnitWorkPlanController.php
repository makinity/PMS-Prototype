<?php

namespace App\Http\Controllers\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\UwpConsolidationSignature;
use Illuminate\Http\Request;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use App\Services\UwpConsolidationSignatureService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UnitWorkPlanController extends Controller
{
    public function __construct(
        private readonly UwpConsolidationSignatureService $signatureService,
    ) {
    }

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
            UnitWorkPlan::STATUS_CONSOLIDATED,
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
                'signature' => [
                    'nullable',
                    'string',
                    function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                        if (empty($value)) {
                            return;
                        }

                        try {
                            $this->signatureService->decodeSignatureDataUrl((string) $value);
                        } catch (\InvalidArgumentException $e) {
                            $fail($e->getMessage());
                        }
                    },
                ],
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

        $signedArtifact = [];
        $reviewedUwp = $uwp;

        try {
            DB::transaction(function () use ($request, $uwp, $validated, $user, &$signedArtifact, &$reviewedUwp) {
                if ($validated['action'] === 'endorse') {
                    $lockedUwp = UnitWorkPlan::query()
                        ->with($this->signatureRelations())
                        ->whereKey($uwp->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (!$lockedUwp->office || !$lockedUwp->office->head || (int) $lockedUwp->office->head->id !== (int) $user->id) {
                        throw ValidationException::withMessages([
                            'unit_work_plan_id' => 'You are not authorized to review this Unit Work Plan.',
                        ]);
                    }

                    if ($lockedUwp->status !== UnitWorkPlan::STATUS_SUBMITTED) {
                        throw ValidationException::withMessages([
                            'unit_work_plan_id' => 'Only submitted Unit Work Plans can be endorsed.',
                        ]);
                    }

                    if (!empty($validated['signature'])) {
                        $signedArtifact = $this->signatureService->createSignedArtifact(
                            $lockedUwp,
                            (string) ($validated['signature'] ?? '')
                        );

                        $signatureRecord = UwpConsolidationSignature::query()->create([
                            'unit_work_plan_id' => $lockedUwp->id,
                            'opcr_id' => null,
                            'signed_by' => $user->id,
                            'signature_image_path' => $signedArtifact['signature_image_path'],
                            'signed_excel_path' => $signedArtifact['signed_excel_path'],
                            'signature_hash' => $signedArtifact['signature_hash'],
                            'signed_at' => now(),
                            'metadata' => [
                                'office_id' => $lockedUwp->office_id,
                                'performance_period_id' => $lockedUwp->performance_period_id,
                                'ip_address' => $request->ip(),
                                'user_agent' => $request->userAgent(),
                            ],
                        ]);

                        $opcr = $this->consolidateSubmittedUwpsFor($lockedUwp, $user);

                        $signatureRecord->forceFill([
                            'opcr_id' => $opcr->id,
                        ])->save();
                    } else {
                        $this->consolidateSubmittedUwpsFor($lockedUwp, $user);
                    }

                    $lockedUwp->refresh();
                    $reviewedUwp = $lockedUwp;
                    return;
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

                if ($validated['action'] !== 'endorse') {
                    $uwp->save();
                }
            });
        } catch (ValidationException $e) {
            if (!empty($signedArtifact)) {
                $this->signatureService->cleanupArtifact($signedArtifact);
            }
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $e->validator->errors()->first() ?: 'Unable to consolidate Unit Work Plans.',
                ], 422);
            }

            throw $e;
        } catch (\Throwable $e) {
            if (!empty($signedArtifact)) {
                $this->signatureService->cleanupArtifact($signedArtifact);
            }
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
                'uwp_id' => (int) $reviewedUwp->id,
                'status' => $validated['action'] === 'endorse' ? UnitWorkPlan::STATUS_CONSOLIDATED : (string) $reviewedUwp->status,
                'endorsed_at' => optional($reviewedUwp->endorsed_at)->toDateTimeString(),
                'returned_at' => optional($reviewedUwp->returned_at)->toDateTimeString(),
                'returned_by_role' => $reviewedUwp->returned_by_role,
                'return_remarks' => $reviewedUwp->return_remarks,
            ]);
        }

        return redirect()
            ->route($validated['action'] === 'endorse' ? 'dept-head.opcr.index' : 'dept-head.uwp.index', $request->only('status'))
            ->with('success', $validated['action'] === 'endorse'
                ? 'Submitted UWPs consolidated into an OPCR draft.'
                : 'Unit Work Plan successfully reviewed.');
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
                    'message' => 'Unable to review Unit Work Plan.',
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

    private function consolidateSubmittedUwpsFor(UnitWorkPlan $seedUwp, $user): Opcr
    {
        $submittedUwps = UnitWorkPlan::query()
            ->where('office_id', $seedUwp->office_id)
            ->where('performance_period_id', $seedUwp->performance_period_id)
            ->where('status', UnitWorkPlan::STATUS_SUBMITTED)
            ->lockForUpdate()
            ->get();

        if ($submittedUwps->isEmpty()) {
            throw ValidationException::withMessages([
                'unit_work_plan_id' => 'No submitted Unit Work Plans are available for consolidation.',
            ]);
        }

        /** @var Opcr $opcr */
        $opcr = Opcr::query()
            ->where('office_id', $seedUwp->office_id)
            ->where('performance_period_id', $seedUwp->performance_period_id)
            ->lockForUpdate()
            ->first();

        if (!$opcr) {
            $opcr = Opcr::query()->create([
                'unit_work_plan_id' => $submittedUwps->first()->id,
                'office_id' => $seedUwp->office_id,
                'performance_period_id' => $seedUwp->performance_period_id,
                'generated_by' => $user->id,
                'status' => Opcr::STATUS_DRAFT,
                'submitted_at' => null,
                'approved_by' => null,
                'approved_at' => null,
                'returned_at' => null,
                'remarks' => null,
                'locked_at' => null,
            ]);
        } elseif ($opcr->isApproved()) {
            throw ValidationException::withMessages([
                'unit_work_plan_id' => 'The OPCR for this office and period is already approved.',
            ]);
        } elseif (in_array(strtolower((string) $opcr->status), [Opcr::STATUS_ENDORSED, Opcr::STATUS_SUBMITTED], true)) {
            throw ValidationException::withMessages([
                'unit_work_plan_id' => 'The OPCR for this office and period is already submitted to PMT.',
            ]);
        } else {
            $opcr->forceFill([
                'unit_work_plan_id' => $opcr->unit_work_plan_id ?: $submittedUwps->first()->id,
                'generated_by' => $user->id,
                'status' => Opcr::STATUS_DRAFT,
                'submitted_at' => null,
                'approved_by' => null,
                'approved_at' => null,
                'returned_at' => null,
                'remarks' => null,
                'locked_at' => null,
            ])->save();
        }

        $existingSourceIds = $opcr->unitWorkPlans()
            ->where('unit_work_plans.status', UnitWorkPlan::STATUS_CONSOLIDATED)
            ->pluck('unit_work_plans.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $sourceIds = $submittedUwps->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->merge($existingSourceIds)
            ->unique()
            ->values()
            ->all();
        $opcr->unitWorkPlans()->sync($sourceIds);

        UnitWorkPlan::query()
            ->whereIn('id', $sourceIds)
            ->update([
                'status' => UnitWorkPlan::STATUS_CONSOLIDATED,
                'endorsed_at' => now(),
                'locked_at' => now(),
                'returned_at' => null,
                'returned_by' => null,
                'returned_by_role' => null,
                'return_remarks' => null,
            ]);

        return $opcr;
    }

    private function signatureRelations(): array
    {
        return [
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
                                            ->with(['qetStandards']);
                                    },
                                ]);
                        },
                    ]);
            },
        ];
    }
}
