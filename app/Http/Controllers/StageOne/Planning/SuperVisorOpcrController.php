<?php

namespace App\Http\Controllers\StageOne\Planning;

use App\Http\Controllers\Controller;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperVisorOpcrController extends Controller
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
            $payload['opcr_status'] = $opcr->status;

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

        DB::transaction(function () use ($uwp, $user) {
            Opcr::query()->firstOrCreate(
                ['unit_work_plan_id' => $uwp->id],
                [
                    'generated_by' => $user->id,
                    'status' => Opcr::STATUS_FOR_REVIEW,
                ]
            );
        });

        return back()->with('success', 'OPCR generated from PMT-approved UWP.');
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
