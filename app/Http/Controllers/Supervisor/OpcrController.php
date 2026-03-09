<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OpcrController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'supervisor') {
            abort(403, 'Unauthorized.');
        }

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $approvedUwps = UnitWorkPlan::query()
            ->with([
                'office',
                'performancePeriod',
                'creator',
                'uwpFunctions' => function ($q) {
                    $q->orderBy('sort_order')->with([
                        'mfos' => function ($mq) {
                            $mq->orderBy('sort_order')->with([
                                'successIndicators' => function ($iq) {
                                    $iq->orderBy('sort_order')->with([
                                        'qetStandards',
                                        'assignments.employee.office',
                                    ]);
                                },
                            ]);
                        },
                    ]);
                },
            ])
            ->where('status', UnitWorkPlan::STATUS_PMT_APPROVED)
            ->when($activePeriod, fn ($q) => $q->where('performance_period_id', $activePeriod->id))
            ->where(function ($q) use ($user) {
                $q->where('created_by', $user->id);
                if (!empty($user->office_id)) {
                    $q->orWhere('office_id', $user->office_id);
                }
            })
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->get();

        $approvedUwpPayloads = $approvedUwps->map(function (UnitWorkPlan $uwp) {
            $payload = $this->buildUwpPayload($uwp);
            return [
                'id' => $uwp->id,
                'label' => trim(sprintf(
                    '%s - %s (PMT Approved)',
                    $uwp->office?->name ?? 'Office',
                    $uwp->performancePeriod?->name ?? 'Period'
                )),
                'payload' => $payload,
            ];
        });

        $opcrs = Opcr::query()
            ->with([
                'unitWorkPlan.office',
                'unitWorkPlan.performancePeriod',
                'generator',
            ])
            ->whereHas('unitWorkPlan', function ($q) use ($user, $activePeriod) {
                $q->where(function ($sq) use ($user) {
                    $sq->where('created_by', $user->id);
                    if (!empty($user->office_id)) {
                        $sq->orWhere('office_id', $user->office_id);
                    }
                });

                if ($activePeriod) {
                    $q->where('performance_period_id', $activePeriod->id);
                }
            })
            ->orderByDesc('id')
            ->get();

        $opcrPayloads = $opcrs->mapWithKeys(function (Opcr $opcr) use ($approvedUwps) {
            $uwp = $approvedUwps->firstWhere('id', $opcr->unit_work_plan_id);
            if (!$uwp && $opcr->relationLoaded('unitWorkPlan') && $opcr->unitWorkPlan) {
                $uwp = UnitWorkPlan::query()
                    ->with([
                        'office',
                        'performancePeriod',
                        'creator',
                        'uwpFunctions.mfos.successIndicators.qetStandards',
                        'uwpFunctions.mfos.successIndicators.assignments.employee.office',
                    ])
                    ->find($opcr->unit_work_plan_id);
            }

            if (!$uwp) {
                return [$opcr->id => null];
            }

            $payload = $this->buildUwpPayload($uwp);
            $payload['opcr_id'] = $opcr->id;
            $payload['opcr_status'] = strtolower((string) $opcr->status);
            $payload['submitted_at'] = optional($opcr->submitted_at)->toDateTimeString();

            return [$opcr->id => $payload];
        });

        return view('supervisor.opcr', [
            'activePeriod' => $activePeriod,
            'approvedUwps' => $approvedUwps,
            'approvedUwpPayloads' => $approvedUwpPayloads,
            'opcrs' => $opcrs,
            'opcrPayloads' => $opcrPayloads,
        ]);
    }

    public function generate(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'supervisor') {
            abort(403, 'Unauthorized.');
        }

        $data = $request->validate([
            'unit_work_plan_id' => ['required', 'integer', 'exists:unit_work_plans,id'],
        ]);

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $uwp = UnitWorkPlan::query()
            ->whereKey($data['unit_work_plan_id'])
            ->where('status', UnitWorkPlan::STATUS_PMT_APPROVED)
            ->when($activePeriod, fn ($q) => $q->where('performance_period_id', $activePeriod->id))
            ->where(function ($q) use ($user) {
                $q->where('created_by', $user->id);
                if (!empty($user->office_id)) {
                    $q->orWhere('office_id', $user->office_id);
                }
            })
            ->first();

        if (!$uwp) {
            return back()->with('error', 'Selected UWP is not eligible for OPCR generation.');
        }

        try {
            $result = DB::transaction(function () use ($uwp, $user) {
                /** @var Opcr|null $existing */
                $existing = Opcr::query()
                    ->where('unit_work_plan_id', $uwp->id)
                    ->lockForUpdate()
                    ->first();

                if (!$existing) {
                    $created = Opcr::query()->create([
                        'unit_work_plan_id' => $uwp->id,
                        'office_id' => $uwp->office_id,
                        'performance_period_id' => $uwp->performance_period_id,
                        'generated_by' => $user->id,
                        'status' => Opcr::STATUS_DRAFT,
                        'submitted_at' => null,
                        'approved_at' => null,
                        'returned_at' => null,
                        'remarks' => null,
                        'locked_at' => null,
                    ]);

                    return ['opcr' => $created, 'message' => 'OPCR generated as Draft.'];
                }

                $currentStatus = strtolower((string) $existing->status);
                if (in_array($currentStatus, [Opcr::STATUS_SUBMITTED, Opcr::STATUS_APPROVED], true)) {
                    throw ValidationException::withMessages([
                        'unit_work_plan_id' => 'Only draft/returned OPCR can be regenerated.',
                    ]);
                }

                if ($currentStatus === Opcr::STATUS_RETURNED || $existing->isLocked()) {
                    $existing->forceFill([
                        'status' => Opcr::STATUS_DRAFT,
                        'submitted_at' => null,
                        'approved_at' => null,
                        'returned_at' => null,
                        'remarks' => null,
                        'locked_at' => null,
                        'generated_by' => $user->id,
                        'office_id' => $uwp->office_id,
                        'performance_period_id' => $uwp->performance_period_id,
                    ])->save();

                    return ['opcr' => $existing, 'message' => 'Returned OPCR regenerated to Draft.'];
                }

                return ['opcr' => $existing, 'message' => 'OPCR is already in Draft.'];
            });
        } catch (ValidationException $e) {
            $message = (string) ($e->validator->errors()->first() ?? 'Invalid OPCR transition.');
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        return back()->with('success', (string) $result['message']);
    }

    public function submit(Request $request, int $opcr)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'supervisor') {
            abort(403, 'Unauthorized.');
        }

        /** @var Opcr|null $opcrModel */
        $opcrModel = Opcr::query()
            ->whereKey($opcr)
            ->whereHas('unitWorkPlan', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->first();

        if (!$opcrModel) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'OPCR not found.'], 404);
            }

            return back()->with('error', 'OPCR not found.');
        }

        $status = strtolower((string) $opcrModel->status);
        if ($opcrModel->isLocked() || $status !== Opcr::STATUS_DRAFT) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Only draft OPCR can be submitted.',
                ], 422);
            }

            return back()->with('error', 'Only draft OPCR can be submitted.');
        }

        $opcrModel->forceFill([
            'status' => Opcr::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'approved_at' => null,
            'returned_at' => null,
            'remarks' => null,
            'locked_at' => now(),
        ])->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'OPCR submitted to Department Head.',
            ]);
        }

        return back()->with('success', 'OPCR submitted to Department Head.');
    }

    private function buildUwpPayload(UnitWorkPlan $uwp): array
    {
        $outputs = [];

        foreach ($uwp->uwpFunctions as $function) {
            foreach ($function->mfos as $mfo) {
                $successIndicators = [];

                foreach ($mfo->successIndicators as $indicator) {
                    $standardsByRating = [];
                    foreach ([5, 4, 3, 2, 1] as $rating) {
                        $standardsByRating[$rating] = [
                            'q' => [],
                            'e' => [],
                            't' => [],
                        ];
                    }

                    foreach ($indicator->qetStandards as $standard) {
                        $rating = (int) $standard->rating;
                        if (!isset($standardsByRating[$rating])) {
                            continue;
                        }

                        $dimension = strtolower((string) $standard->dimension);
                        $dimension = match ($dimension) {
                            'quality' => 'q',
                            'efficiency' => 'e',
                            'timeliness' => 't',
                            'q', 'e', 't' => $dimension,
                            default => null,
                        };

                        if (!$dimension) {
                            continue;
                        }

                        if (!empty($standard->standard_text)) {
                            $standardsByRating[$rating][$dimension][] = $standard->standard_text;
                        }
                    }

                    $assignees = $indicator->assignments
                        ->map(fn ($assignment) => [
                            'name' => $assignment->employee?->name,
                            'office' => $assignment->employee?->office?->name,
                        ])
                        ->filter(fn ($row) => !empty($row['name']))
                        ->values()
                        ->all();

                    $successIndicators[] = [
                        'indicator_text' => $indicator->indicator_text,
                        'standards_by_rating' => $standardsByRating,
                        'assignees' => $assignees,
                    ];
                }

                $outputs[] = [
                    'mfo_title' => $mfo->title,
                    'target_quantity' => $mfo->target_quantity,
                    'target_timeline' => $mfo->target_timeline,
                    'weight_percent' => $mfo->weight_percent ?? $function->weight_percent,
                    'function_type' => strtolower((string) $function->function_type),
                    'success_indicators' => $successIndicators,
                ];
            }
        }

        return [
            'id' => $uwp->id,
            'status' => $uwp->status,
            'office' => [
                'name' => $uwp->office?->name,
            ],
            'period' => [
                'name' => $uwp->performancePeriod?->name,
            ],
            'derived_outputs' => $outputs,
        ];
    }
}
