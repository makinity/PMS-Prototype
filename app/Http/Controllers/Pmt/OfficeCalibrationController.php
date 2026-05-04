<?php

namespace App\Http\Controllers\Pmt;

use App\Http\Controllers\Controller;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficeCalibrationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 403);
        $search = trim($request->string('search')->toString());

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();

        $infoMessage = null;

        if (!$activePeriod) {
            $infoMessage = 'No active performance period is configured.';
            return view('pmt.office-calibration.index', [
                'activePeriod' => $activePeriod,
                'opcrs' => collect(),
                'infoMessage' => $infoMessage,
                'search' => $search
            ]);
        }

        $query = Opcr::query()
            ->with(['office.head'])
            ->where(function ($q) use ($activePeriod) {
                $q->where('performance_period_id', $activePeriod->id)
                  ->orWhereHas('unitWorkPlan', fn ($uq) => $uq->where('performance_period_id', $activePeriod->id))
                  ->orWhereHas('unitWorkPlans', fn ($uq) => $uq->where('performance_period_id', $activePeriod->id));
            })
            ->whereIn('status', [
                Opcr::STATUS_PENDING_PMT_CALIBRATION,
                Opcr::STATUS_APPROVED_BY_PMT,
                Opcr::STATUS_ADJUSTED_BY_PMT,
            ]);

        if ($search !== '') {
            $normalizedSearch = mb_strtolower($search);
            $query->where(function ($q) use ($search, $normalizedSearch) {
                $q->whereHas('office', function ($oq) use ($search) {
                    $oq->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('office.head', function ($hq) use ($search) {
                    $hq->where('name', 'like', '%' . $search . '%');
                })->orWhereRaw('LOWER(status) like ?', ['%' . $normalizedSearch . '%']);
            });
        }

        $opcrs = $query->orderByDesc('updated_at')->get();

        if ($opcrs->isEmpty()) {
            $infoMessage = 'No OPCRs pending calibration or calibrated found for the active period.';
        }

        return view('pmt.office-calibration.index', compact('activePeriod', 'opcrs', 'infoMessage', 'search'));
    }

    public function show($id)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $opcr = Opcr::query()
            ->with($this->opcrRelations())
            ->findOrFail($id);

        if (!in_array($opcr->status, [Opcr::STATUS_PENDING_PMT_CALIBRATION, Opcr::STATUS_APPROVED_BY_PMT, Opcr::STATUS_ADJUSTED_BY_PMT])) {
            return redirect()->route('pmt.office-calibration.index')->with('error', 'Invalid OPCR status for calibration.');
        }

        $payload = $this->buildPayload($opcr);

        return view('pmt.office-calibration.show', compact('opcr', 'payload'));
    }

    public function adjust(Request $request, $id)
    {
        $request->validate([
            'adjusted_score' => 'required|numeric|min:1|max:5',
            'adjusted_rating' => 'required|string|max:255',
            'adjustment_reason' => 'required|string|max:1000',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $opcr = Opcr::findOrFail($id);
        
        // Remove strict locked_at check to allow PMT to adjust office scores during evaluation.
        // if (!is_null($opcr->locked_at)) {
        //     return back()->with('error', 'OPCR is finalized and locked. Calibration is no longer allowed.');
        // }

        if (!in_array($opcr->status, [Opcr::STATUS_PENDING_PMT_CALIBRATION, Opcr::STATUS_APPROVED_BY_PMT, Opcr::STATUS_ADJUSTED_BY_PMT])) {
            return back()->with('error', 'Invalid OPCR status for calibration.');
        }

        $opcr->update([
            'status' => Opcr::STATUS_ADJUSTED_BY_PMT,
            'pmt_adjusted_score' => $request->adjusted_score,
            'pmt_adjusted_rating' => $request->adjusted_rating,
            'pmt_adjustment_reason' => $request->adjustment_reason,
            'pmt_remarks' => $request->remarks,
            'pmt_reviewed_by' => Auth::id(),
            'pmt_reviewed_at' => now(),
        ]);

        return back()->with('success', 'OPCR rating adjusted and calibrated successfully.');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $opcr = Opcr::findOrFail($id);
        
        // Remove strict locked_at check to allow PMT to approve office scores during evaluation.
        // if (!is_null($opcr->locked_at)) {
        //     return back()->with('error', 'OPCR is already finalized and locked.');
        // }

        if (!in_array($opcr->status, [Opcr::STATUS_PENDING_PMT_CALIBRATION, Opcr::STATUS_APPROVED_BY_PMT, Opcr::STATUS_ADJUSTED_BY_PMT])) {
            return back()->with('error', 'Invalid OPCR status for approval.');
        }

        $opcr->update([
            'status' => Opcr::STATUS_APPROVED_BY_PMT,
            'pmt_remarks' => $request->remarks,
            'pmt_reviewed_by' => Auth::id(),
            'pmt_reviewed_at' => now(),
        ]);

        return back()->with('success', 'OPCR rating approved and calibrated successfully.');
    }

    public function returnOpcr(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        $opcr = Opcr::findOrFail($id);
        
        if (!in_array($opcr->status, [Opcr::STATUS_PENDING_PMT_CALIBRATION, Opcr::STATUS_APPROVED_BY_PMT, Opcr::STATUS_ADJUSTED_BY_PMT])) {
            return back()->with('error', 'Invalid OPCR status for return.');
        }

        $opcr->update([
            'status' => Opcr::STATUS_RETURNED_BY_PMT,
            'pmt_remarks' => $request->remarks,
            'pmt_reviewed_by' => Auth::id(),
            'pmt_reviewed_at' => now(),
        ]);

        return back()->with('success', 'OPCR returned successfully.');
    }

    // Reuse payload building logic from DeptHeadOpcrController
    private function buildPayload(Opcr $opcr): array
    {
        $sources = $opcr->sourceUnitWorkPlans();
        $fallbackUwp = $sources->first() ?: $opcr->unitWorkPlan;
        $outputs = [];

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
                    ];
                }
            }
        }

        return [
            'opcr' => [
                'id' => $opcr->id,
                'status' => $opcr->status,
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
}
