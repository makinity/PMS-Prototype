<?php

namespace App\Http\Controllers\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpcrController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'dept-head') {
            abort(403, 'Unauthorized.');
        }

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $allowedStatuses = [
            Opcr::STATUS_SUBMITTED,
            Opcr::STATUS_ENDORSED,
            Opcr::STATUS_APPROVED,
            Opcr::STATUS_RETURNED,
        ];

        $rawStatus = strtolower(trim($request->string('status')->toString()));
        $isAll = ($rawStatus === 'all');

        $status = in_array($rawStatus, $allowedStatuses, true)
            ? $rawStatus
            : Opcr::STATUS_SUBMITTED;

        $opcrs = Opcr::query()
            ->with([
                'unitWorkPlan.office.head',
                'unitWorkPlan.performancePeriod',
                'unitWorkPlan.creator',
                'unitWorkPlan.uwpFunctions' => function ($q) {
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
            ->whereHas('unitWorkPlan.office.head', fn ($q) => $q->whereKey($user->id))
            ->when($activePeriod, function ($q) use ($activePeriod) {
                $q->whereHas('unitWorkPlan', fn ($uq) => $uq->where('performance_period_id', $activePeriod->id));
            })
            ->when(!$isAll, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderByDesc('id')
            ->get();

        $opcrPayloads = $opcrs->mapWithKeys(function (Opcr $opcr) {
            return [$opcr->id => $this->buildPayload($opcr)];
        });

        return view('dept-head.opcr', [
            'activePeriod' => $activePeriod,
            'opcrs' => $opcrs,
            'opcrPayloads' => $opcrPayloads,
            'selectedStatus' => $isAll ? 'all' : $status,
        ]);
    }

    public function review(Request $request)
    {
        $validated = $request->validate([
            'opcr_id' => ['required', 'integer', 'exists:opcrs,id'],
            'action' => ['required', 'in:endorse,return'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($validated['action'] === 'endorse') {
            return $this->endorse($request, (int) $validated['opcr_id']);
        }

        $request->merge(['remarks' => $validated['remarks'] ?? null]);
        return $this->returnOpcr($request, (int) $validated['opcr_id']);
    }

    public function endorse(Request $request, int $opcr)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'dept-head') {
            abort(403, 'Unauthorized.');
        }

        /** @var Opcr|null $model */
        $model = Opcr::query()
            ->whereKey($opcr)
            ->whereHas('unitWorkPlan.office.head', fn ($q) => $q->whereKey($user->id))
            ->lockForUpdate()
            ->first();

        if (!$model) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'OPCR not found.'], 404);
            }

            return back()->with('error', 'OPCR not found.');
        }

        $status = strtolower((string) $model->status);
        if ($status !== Opcr::STATUS_SUBMITTED) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Only submitted OPCR can be endorsed.'], 422);
            }

            return back()->with('error', 'Only submitted OPCR can be endorsed.');
        }

        DB::transaction(function () use ($model, $user) {
            $model->forceFill([
                'status' => Opcr::STATUS_ENDORSED,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'returned_at' => null,
                'remarks' => null,
                'locked_at' => now(),
            ])->save();
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'OPCR endorsed.']);
        }

        return back()->with('success', 'OPCR endorsed.');
    }

    public function returnOpcr(Request $request, int $opcr)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'dept-head') {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:5000'],
        ]);

        /** @var Opcr|null $model */
        $model = Opcr::query()
            ->whereKey($opcr)
            ->whereHas('unitWorkPlan.office.head', fn ($q) => $q->whereKey($user->id))
            ->lockForUpdate()
            ->first();

        if (!$model) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'OPCR not found.'], 404);
            }

            return back()->with('error', 'OPCR not found.');
        }

        $status = strtolower((string) $model->status);
        if ($status !== Opcr::STATUS_SUBMITTED) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Only submitted OPCR can be returned.'], 422);
            }

            return back()->with('error', 'Only submitted OPCR can be returned.');
        }

        DB::transaction(function () use ($model, $validated) {
            $model->forceFill([
                'status' => Opcr::STATUS_RETURNED,
                'approved_by' => null,
                'approved_at' => null,
                'returned_at' => now(),
                'remarks' => trim((string) $validated['remarks']),
                'locked_at' => null,
            ])->save();

            $sourceUwp = $model->unitWorkPlan()->lockForUpdate()->first();
            if ($sourceUwp) {
                $sourceUwp->forceFill([
                    'status' => UnitWorkPlan::STATUS_RETURNED,
                    'locked_at' => null,
                ])->save();
            }
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'OPCR returned to Supervisor.']);
        }

        return back()->with('success', 'OPCR returned to Supervisor.');
    }

    private function buildPayload(Opcr $opcr): array
    {
        $uwp = $opcr->unitWorkPlan;
        $outputs = [];

        if ($uwp) {
            foreach ($uwp->uwpFunctions as $function) {
                foreach ($function->mfos as $mfo) {
                    $indicators = [];

                    foreach ($mfo->successIndicators as $si) {
                        $standardsByRating = [];
                        foreach ([5, 4, 3, 2, 1] as $rating) {
                            $standardsByRating[(string) $rating] = ['Q' => [], 'E' => [], 'T' => []];
                        }

                        foreach ($si->qetStandards as $standard) {
                            $rating = (string) $standard->rating;
                            if (!isset($standardsByRating[$rating])) {
                                continue;
                            }

                            $dimension = strtolower((string) $standard->dimension);
                            if (in_array($dimension, ['q', 'quality'], true)) {
                                $standardsByRating[$rating]['Q'][] = $standard->standard_text;
                            } elseif (in_array($dimension, ['e', 'efficiency'], true)) {
                                $standardsByRating[$rating]['E'][] = $standard->standard_text;
                            } elseif (in_array($dimension, ['t', 'timeliness'], true)) {
                                $standardsByRating[$rating]['T'][] = $standard->standard_text;
                            }
                        }

                        $assignees = $si->assignments
                            ->map(fn ($assignment) => $assignment->employee?->name)
                            ->filter()
                            ->values()
                            ->all();

                        $indicators[] = [
                            'indicator_text' => $si->indicator_text,
                            'standards_by_rating' => $standardsByRating,
                            'assignees' => $assignees,
                        ];
                    }

                    $outputs[] = [
                        'title' => $mfo->title,
                        'target_summary' => $mfo->target_timeline,
                        'weight_percent' => $mfo->weight_percent ?? $function->weight_percent,
                        'function_type' => strtolower((string) $function->function_type),
                        'success_indicators' => $indicators,
                    ];
                }
            }
        }

        return [
            'opcr' => [
                'id' => $opcr->id,
                'status' => $opcr->status,
                'office' => [
                    'id' => $uwp?->office?->id,
                    'name' => $uwp?->office?->name,
                ],
                'period' => [
                    'id' => $uwp?->performancePeriod?->id,
                    'name' => $uwp?->performancePeriod?->name,
                ],
                'source_uwp' => [
                    'id' => $uwp?->id,
                    'status' => $uwp?->status,
                ],
            ],
            'outputs' => $outputs,
        ];
    }
}
