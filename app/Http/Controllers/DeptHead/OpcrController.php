<?php

namespace App\Http\Controllers\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use App\Services\OpcrOfficeRatingService;
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

        $office = $user->supervisedOffice()->first();
        $currentOpcr = null;
        $linkedSourceIds = collect();

        if ($office) {
            $currentOpcr = Opcr::query()
                ->with($this->opcrRelations())
                ->where(function ($query) use ($user, $office) {
                    $query->where('office_id', $office->id)
                        ->orWhereHas('unitWorkPlan.office.head', fn ($q) => $q->whereKey($user->id))
                        ->orWhereHas('unitWorkPlans.office.head', fn ($q) => $q->whereKey($user->id));
                })
                ->when($activePeriod, function ($q) use ($activePeriod) {
                    $q->where(function ($periodQuery) use ($activePeriod) {
                        $periodQuery->where('performance_period_id', $activePeriod->id)
                            ->orWhereHas('unitWorkPlan', fn ($uq) => $uq->where('performance_period_id', $activePeriod->id))
                            ->orWhereHas('unitWorkPlans', fn ($uq) => $uq->where('performance_period_id', $activePeriod->id));
                    });
                })
                ->latest('id')
                ->first();

            if ($currentOpcr) {
                $linkedSourceIds = $currentOpcr->sourceUnitWorkPlans()
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id);
            }
        }

        $sourceUwps = collect();
        if ($office) {
            $sourceUwps = UnitWorkPlan::query()
                ->with($this->sourceUwpRelations())
                ->where('office_id', $office->id)
                ->when($activePeriod, fn ($q) => $q->where('performance_period_id', $activePeriod->id))
                ->where(function ($sourceQuery) use ($linkedSourceIds) {
                    $sourceQuery->where('status', UnitWorkPlan::STATUS_SUBMITTED);
                    if ($linkedSourceIds->isNotEmpty()) {
                        $sourceQuery->orWhereIn('id', $linkedSourceIds->all());
                    }
                })
                ->orderByRaw(
                    "case 
                        when status = ? then 0
                        when status = ? then 1
                        when status = ? then 2
                        else 3
                    end",
                    [
                        UnitWorkPlan::STATUS_SUBMITTED,
                        UnitWorkPlan::STATUS_CONSOLIDATED,
                        UnitWorkPlan::STATUS_RETURNED,
                    ]
                )
                ->orderBy('created_by')
                ->orderByDesc('id')
                ->get();
        }

        $sourceUwpPayloads = $sourceUwps->mapWithKeys(fn (UnitWorkPlan $uwp) => [
            $uwp->id => $this->buildSourceUwpPayload($uwp),
        ]);

        $currentOpcrPayload = $currentOpcr ? $this->buildPayload($currentOpcr) : null;
        $submittedSeedUwpId = $sourceUwps
            ->firstWhere('status', UnitWorkPlan::STATUS_SUBMITTED)?->id;

        // Add missing filter variables for the view
        $selectedStatus = $request->query('status', 'all');
        $searchTerm = $request->query('search', '');

        return view('dept-head.opcr', [
            'activePeriod' => $activePeriod,
            'currentOpcr' => $currentOpcr,
            'currentOpcrPayload' => $currentOpcrPayload,
            'sourceUwps' => $sourceUwps,
            'sourceUwpPayloads' => $sourceUwpPayloads,
            'submittedSeedUwpId' => $submittedSeedUwpId,
            'selectedStatus' => $selectedStatus,
            'searchTerm' => $searchTerm,
        ]);
    }

    public function accomplishment(Request $request)
    {
        $activePeriod = PerformancePeriod::where('is_active', true)->first();
        $officeId = auth()->user()->office_id;

        $currentOpcr = Opcr::where('office_id', $officeId)
            ->where('performance_period_id', $activePeriod?->id)
            ->first();

        $currentOpcrPayload = $currentOpcr ? $this->buildPayload($currentOpcr) : null;

        // Stage-based logic: Only allow calibration submission if we are in the rating phase
        $hasRatings = false;
        if ($currentOpcr) {
            $hasRatings = \App\Models\Ipcr::where('opcr_id', $currentOpcr->id)->whereNotNull('final_score')->exists();
        }

        return view('dept-head.opcr-accomplishment', [
            'activePeriod' => $activePeriod,
            'currentOpcr' => $currentOpcr,
            'currentOpcrPayload' => $currentOpcrPayload,
            'hasRatings' => $hasRatings,
            'opcrStatus' => strtolower((string) ($currentOpcr?->status ?? '')),
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

    public function endorse(Request $request, int $opcr, \App\Services\UwpConsolidationSignatureService $signatureService)
    {
        $signatureInput = $request->input('signature');

        $validated = $request->validate([
            'signature' => ['nullable', 'string'],
        ]);
        $user = Auth::user();
        if (!$user || $user->role !== 'dept-head') {
            abort(403, 'Unauthorized.');
        }

        /** @var Opcr|null $model */
        $model = Opcr::query()
            ->with(['office.head', 'unitWorkPlan.office.head', 'unitWorkPlans.office.head'])
            ->whereKey($opcr)
            ->lockForUpdate()
            ->first();

        if (!$model || !$this->canManageOpcr($model, $user)) {
            return $this->notFoundResponse($request);
        }

        $status = strtolower((string) $model->status);
        if (!in_array($status, [Opcr::STATUS_DRAFT, Opcr::STATUS_RETURNED, Opcr::STATUS_SUBMITTED, Opcr::STATUS_APPROVED], true)) {
            return $this->invalidResponse($request, 'Only draft, returned, submitted, or already approved OPCR can be submitted to PMT.');
        }

        DB::transaction(function () use ($model, $user, $signatureInput, $signatureService, $request) {
            $model->forceFill([
                'status' => Opcr::STATUS_ENDORSED,
                'submitted_at' => now(),
                'approved_by' => $user->id,
                'approved_at' => null,
                'returned_at' => null,
                'remarks' => null,
                'locked_at' => now(),
            ])->save();

            if (!empty($signatureInput)) {
                $signedArtifact = $signatureService->createSignedOpcrArtifact(
                    $model,
                    $signatureInput
                );

                \App\Models\UwpConsolidationSignature::query()->create([
                    'unit_work_plan_id' => $model->unit_work_plan_id ?: $model->unitWorkPlans()->first()?->id,
                    'opcr_id' => $model->id,
                    'signed_by' => $user->id,
                    'signature_image_path' => $signedArtifact['signature_image_path'],
                    'signed_excel_path' => $signedArtifact['signed_excel_path'],
                    'signature_hash' => $signedArtifact['signature_hash'],
                    'signed_at' => now(),
                    'metadata' => [
                        'action' => 'dept_head_endorse_opcr',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                ]);
            }
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'OPCR submitted to PMT.']);
        }

        return back()->with('success', 'OPCR submitted to PMT.');
    }

    public function submitCalibration(Request $request, int $opcr)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'dept-head') {
            abort(403, 'Unauthorized.');
        }

        /** @var Opcr|null $model */
        $model = Opcr::query()
            ->with(['office.head', 'unitWorkPlan.office.head', 'unitWorkPlans.office.head'])
            ->whereKey($opcr)
            ->lockForUpdate()
            ->first();

        if (!$model || !$this->canManageOpcr($model, $user)) {
            return $this->notFoundResponse($request);
        }

        if ($model->status !== Opcr::STATUS_APPROVED) {
            return $this->invalidResponse($request, 'Only approved OPCRs can be submitted for final calibration.');
        }

        // Additional guard: Ensure we are not in the planning stage.
        // We consider it a rating stage only if at least one IPCR has been finalized or has a score.
        $hasAnyScores = \App\Models\Ipcr::where('opcr_id', $model->id)
            ->whereIn('status', [\App\Models\Ipcr::STATUS_APPROVED_BY_PMT, \App\Models\Ipcr::STATUS_ADJUSTED_BY_PMT, \App\Models\Ipcr::STATUS_RELEASED_BY_PMT])
            ->whereNotNull('final_score')
            ->exists();
        if (!$hasAnyScores) {
            return $this->invalidResponse($request, 'Cannot submit for calibration. This OPCR is still in the Planning Stage (no accomplishments/ratings found).');
        }

        $ipcrs = \App\Models\Ipcr::where('opcr_id', $model->id)
            ->whereIn('status', [\App\Models\Ipcr::STATUS_APPROVED_BY_PMT, \App\Models\Ipcr::STATUS_ADJUSTED_BY_PMT, \App\Models\Ipcr::STATUS_RELEASED_BY_PMT])
            ->get();

        if ($ipcrs->isEmpty()) {
            return $this->invalidResponse($request, 'Cannot submit OPCR for final calibration. No IPCR accomplishments found for this office period.');
        }

        $validScoresCount = $ipcrs->filter(fn($ipcr) => $ipcr->final_score !== null)->count();

        if ($validScoresCount > 0) {
            $uncalibratedCount = $ipcrs->filter(function ($ipcr) {
                return !in_array($ipcr->status, [\App\Models\Ipcr::STATUS_APPROVED_BY_PMT, \App\Models\Ipcr::STATUS_ADJUSTED_BY_PMT, \App\Models\Ipcr::STATUS_RELEASED_BY_PMT], true);
            })->count();

            if ($uncalibratedCount > 0) {
                return $this->invalidResponse($request, "Cannot submit OPCR for calibration. There are {$uncalibratedCount} IPCR(s) in this office that have not yet been calibrated by the PMT.");
            }
        }

        $payload = $this->buildPayload($model);
        $computedSummary = $payload['computed_summary'] ?? app(OpcrOfficeRatingService::class)->calculate($model, $payload['outputs'] ?? []);

        DB::transaction(function () use ($model, $computedSummary) {
            $model->forceFill([
                'status' => Opcr::STATUS_PENDING_PMT_CALIBRATION,
                'final_score' => $computedSummary['is_ready'] ? $computedSummary['overall_score'] : null,
                'adjectival_rating' => $computedSummary['is_ready'] ? $computedSummary['adjectival_rating'] : null,
            ])->save();
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Final OPCR submitted to PMT for Calibration.']);
        }

        return back()->with('success', 'Final OPCR submitted to PMT for Calibration.');
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
            ->with(['office.head', 'unitWorkPlan.office.head', 'unitWorkPlans.office.head'])
            ->whereKey($opcr)
            ->lockForUpdate()
            ->first();

        if (!$model || !$this->canManageOpcr($model, $user)) {
            return $this->notFoundResponse($request);
        }

        $status = strtolower((string) $model->status);
        if (!in_array($status, [Opcr::STATUS_DRAFT, Opcr::STATUS_SUBMITTED, Opcr::STATUS_ENDORSED], true)) {
            return $this->invalidResponse($request, 'Only draft, submitted, or PMT-bound OPCR can be returned.');
        }

        DB::transaction(function () use ($model, $validated, $user) {
            $remarks = trim((string) $validated['remarks']);

            $model->forceFill([
                'status' => Opcr::STATUS_RETURNED,
                'approved_by' => null,
                'approved_at' => null,
                'returned_at' => now(),
                'remarks' => $remarks,
                'locked_at' => null,
            ])->save();

            $sourceIds = $model->unitWorkPlans()->pluck('unit_work_plans.id');
            if ($sourceIds->isEmpty() && $model->unit_work_plan_id) {
                $sourceIds = collect([(int) $model->unit_work_plan_id]);
            }

            UnitWorkPlan::query()
                ->whereIn('id', $sourceIds->all())
                ->update([
                    'status' => UnitWorkPlan::STATUS_RETURNED,
                    'submitted_at' => null,
                    'endorsed_at' => null,
                    'approved_at' => null,
                    'locked_at' => null,
                    'returned_at' => now(),
                    'returned_by' => $user->id,
                    'returned_by_role' => 'dept-head',
                    'return_remarks' => $remarks,
                ]);
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'OPCR returned to Supervisors.']);
        }

        return back()->with('success', 'OPCR returned to Supervisors.');
    }

    private function buildPayload(Opcr $opcr): array
    {
        $sources = $opcr->sourceUnitWorkPlans();
        $fallbackUwp = $sources->first() ?: $opcr->unitWorkPlan;
        $outputs = [];

        // AGGREGATE ACCOMPLISHMENTS FROM IPCRs
        // Only aggregate IPCRs that are officially Calibrated (Approved or Adjusted)
        $ipcrAccomplishments = [];
        $officeIpcrs = \App\Models\Ipcr::where('opcr_id', $opcr->id)
            ->whereIn('status', [\App\Models\Ipcr::STATUS_APPROVED_BY_PMT, \App\Models\Ipcr::STATUS_ADJUSTED_BY_PMT, \App\Models\Ipcr::STATUS_RELEASED_BY_PMT])
            ->get();
        
        $ratingService = app(\App\Services\PerformanceRatingService::class);
        
        foreach ($officeIpcrs as $ipcr) {
            [$ratingsByOutput, $ratingsByIndicator] = $ratingService->buildRatedIpcrPerformanceMaps($ipcr);
            
            foreach ($ratingsByOutput as $title => $ratings) {
                if (!isset($ipcrAccomplishments[$title])) {
                    $ipcrAccomplishments[$title] = [
                        'qty'      => 0.0,
                        'q_sum'    => 0.0, // sum of per-employee Q averages (1-5 scale)
                        'e_points' => 0.0, // qty-weighted efficiency for office total
                        't_sum'    => 0.0, // sum of per-employee T averages (1-5 scale)
                        'count'    => 0,   // number of employees contributing
                    ];
                }
                $ipcrAccomplishments[$title]['qty']      += (float) ($ratings['qty'] ?? 0);
                $ipcrAccomplishments[$title]['q_sum']    += (float) ($ratings['q'] ?? 0);
                $ipcrAccomplishments[$title]['e_points'] += (float) (($ratings['e'] ?? 0) * ($ratings['qty'] ?? 0));
                $ipcrAccomplishments[$title]['t_sum']    += (float) ($ratings['t'] ?? 0);
                $ipcrAccomplishments[$title]['count']++;
            }
        }

        foreach ($sources as $uwp) {
            $uwp->loadMissing([
                'office',
                'performancePeriod',
                'creator',
                'uwpFunctions.mfos.successIndicators.qetStandards',
                'uwpFunctions.mfos.successIndicators.assignments.employee.office',
            ]);

            foreach ($uwp->uwpFunctions as $function) {
                foreach ($function->mfos as $mfo) {
                    $indicators = [];
                    $outputTargetQuantity = 0;
                    $outputTargetTimelines = [];

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

                        $targetQuantity = is_numeric($si->target_quantity ?? null) ? (int) $si->target_quantity : null;
                        $targetTimeline = trim((string) ($si->target_timeline ?? ''));

                        if ($targetQuantity !== null && $targetQuantity > 0) {
                            $outputTargetQuantity += $targetQuantity;
                        }

                        if ($targetTimeline !== '' && !in_array($targetTimeline, $outputTargetTimelines, true)) {
                            $outputTargetTimelines[] = $targetTimeline;
                        }

                        $indicators[] = [
                            'indicator_text' => $si->indicator_text,
                            'target_quantity' => $targetQuantity,
                            'target_timeline' => $targetTimeline,
                            'standards_by_rating' => $standardsByRating,
                            'assignees' => $assignees,
                        ];
                    }

                    $outputTargetSummary = count($outputTargetTimelines) === 1
                        ? $outputTargetTimelines[0]
                        : (count($outputTargetTimelines) > 1
                            ? 'Multiple indicator targets'
                            : trim((string) ($mfo->target_timeline ?? '')));

                    $outputTitle = trim((string) ($mfo->title ?? ''));
                    $acc = $ipcrAccomplishments[$outputTitle] ?? null;
                    
                    $actualQty = $acc ? (float) $acc['qty'] : 0.0;
                    $empCount  = $acc ? (int) $acc['count'] : 0;
                    // Q and T: average of per-employee ratings (already 1–5 scale)
                    $actualQ = ($acc && $empCount > 0) ? round($acc['q_sum'] / $empCount, 2) : 0.0;
                    // E: quantity-weighted efficiency ratio
                    $actualE = ($acc && $actualQty > 0) ? round($acc['e_points'] / $actualQty, 2) : 0.0;
                    $actualT = ($acc && $empCount > 0) ? round($acc['t_sum'] / $empCount, 2) : 0.0;
                    $actualAvg = ($actualQ > 0 || $actualE > 0 || $actualT > 0)
                        ? round(($actualQ + $actualE + $actualT) / 3, 2)
                        : 0.0;

                    $outputs[] = [
                        'title' => $mfo->title,
                        'source_uwp_id' => $uwp->id,
                        'source_supervisor' => $uwp->creator?->name,
                        'target_quantity' => $outputTargetQuantity > 0
                            ? $outputTargetQuantity
                            : (is_numeric($mfo->target_quantity ?? null) ? (int) $mfo->target_quantity : null),
                        'target_summary' => $outputTargetSummary,
                        'weight_percent' => $mfo->weight_percent ?? $function->weight_percent,
                        'function_type' => strtolower((string) $function->function_type),
                        'success_indicators' => $indicators,
                        // NEW: Actual Accomplishments
                        'actual_quantity' => $actualQty,
                        'actual_q' => $actualQ,
                        'actual_e' => $actualE,
                        'actual_t' => $actualT,
                        'actual_avg' => $actualAvg,
                    ];
                }
            }
        }

        $computedSummary = app(OpcrOfficeRatingService::class)->calculate($opcr, $outputs);

        return [
            'opcr' => [
                'id' => $opcr->id,
                'status' => $opcr->status,
                'final_score' => $opcr->final_score,
                'adjectival_rating' => $opcr->adjectival_rating,
                'office' => [
                    'id' => $opcr->office?->id ?? $fallbackUwp?->office?->id,
                    'name' => $opcr->office?->name ?? $fallbackUwp?->office?->name,
                ],
                'period' => [
                    'id' => $opcr->performancePeriod?->id ?? $fallbackUwp?->performancePeriod?->id,
                    'name' => $opcr->performancePeriod?->name ?? $fallbackUwp?->performancePeriod?->name,
                ],
                'source_uwp' => [
                    'id' => $sources->pluck('id')->implode(', '),
                    'status' => $sources->pluck('status')->unique()->implode(', '),
                ],
            ],
            'outputs' => $outputs,
            'computed_summary' => $computedSummary,
        ];
    }

    private function opcrRelations(): array
    {
        $uwpTree = function ($q) {
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
        };

        return [
            'office.head',
            'performancePeriod',
            'unitWorkPlan.office.head',
            'unitWorkPlan.performancePeriod',
            'unitWorkPlan.creator',
            'unitWorkPlan.uwpFunctions' => $uwpTree,
            'unitWorkPlans.office.head',
            'unitWorkPlans.performancePeriod',
            'unitWorkPlans.creator',
            'unitWorkPlans.uwpFunctions' => $uwpTree,
        ];
    }

    private function sourceUwpRelations(): array
    {
        return [
            'office.head',
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
        ];
    }

    private function buildSourceUwpPayload(UnitWorkPlan $uwp): array
    {
        return [
            'id' => $uwp->id,
            'status' => $uwp->status,
            'return_remarks' => (string) ($uwp->return_remarks ?? ''),
            'returned_at' => optional($uwp->returned_at)->toDateTimeString(),
            'returned_by_role' => (string) ($uwp->returned_by_role ?? ''),
            'office' => [
                'id' => $uwp->office?->id,
                'name' => $uwp->office?->name,
            ],
            'period' => [
                'id' => $uwp->performancePeriod?->id,
                'name' => $uwp->performancePeriod?->name,
            ],
            'supervisor' => [
                'id' => $uwp->creator?->id,
                'name' => $uwp->creator?->name,
            ],
            'department_head' => [
                'id' => $uwp->office?->head?->id,
                'name' => $uwp->office?->head?->name,
            ],
            'functions' => $uwp->uwpFunctions->map(function ($function) {
                return [
                    'id' => $function->id,
                    'name' => $function->name,
                    'function_type' => $function->function_type,
                    'weight_percent' => (string) ($function->weight_percent ?? ''),
                    'mfos' => $function->mfos->map(function ($mfo) {
                        return [
                            'id' => $mfo->id,
                            'title' => $mfo->title,
                            'target_quantity' => $mfo->target_quantity,
                            'target_timeline' => $mfo->target_timeline,
                            'weight_percent' => (string) ($mfo->weight_percent ?? ''),
                            'success_indicators' => $mfo->successIndicators->map(function ($indicator) {
                                $standardsByRating = [];
                                foreach ([5, 4, 3, 2, 1] as $rating) {
                                    $standardsByRating[(string) $rating] = ['Q' => [], 'E' => [], 'T' => []];
                                }

                                foreach ($indicator->qetStandards as $standard) {
                                    $dimension = strtolower((string) $standard->dimension);
                                    $rating = (string) $standard->rating;
                                    if (!isset($standardsByRating[$rating])) {
                                        continue;
                                    }

                                    if (in_array($dimension, ['q', 'quality'], true)) {
                                        $standardsByRating[$rating]['Q'][] = $standard->standard_text;
                                    } elseif (in_array($dimension, ['e', 'efficiency'], true)) {
                                        $standardsByRating[$rating]['E'][] = $standard->standard_text;
                                    } elseif (in_array($dimension, ['t', 'timeliness'], true)) {
                                        $standardsByRating[$rating]['T'][] = $standard->standard_text;
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

                                return [
                                    'id' => $indicator->id,
                                    'indicator_text' => $indicator->indicator_text,
                                    'target_quantity' => $indicator->target_quantity,
                                    'target_timeline' => $indicator->target_timeline,
                                    'assignees' => $assignees,
                                    'standards_by_rating' => $standardsByRating,
                                ];
                            })->values()->all(),
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
        ];
    }

    private function canManageOpcr(Opcr $opcr, $user): bool
    {
        if ($opcr->office?->head && (int) $opcr->office->head->id === (int) $user->id) {
            return true;
        }

        foreach ($opcr->sourceUnitWorkPlans() as $uwp) {
            if ($uwp->office?->head && (int) $uwp->office->head->id === (int) $user->id) {
                return true;
            }
        }

        return false;
    }

    private function notFoundResponse(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'OPCR not found.'], 404);
        }

        return back()->with('error', 'OPCR not found.');
    }

    private function invalidResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->with('error', $message);
    }
}
