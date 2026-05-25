<?php

namespace App\Http\Controllers\Pmt;

use App\Exports\StageOne\OpcrExcelExport;
use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Models\UnitWorkPlan;
use App\Notifications\WorkflowEventNotification;
use App\Services\IpcrGeneratorService;
use App\Services\WorkflowNotificationDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class OpcrController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'pmt') {
            abort(403, 'Unauthorized.');
        }

        $activePeriod = PerformancePeriod::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        $status = strtolower(trim($request->string('status')->toString()));
        $search = trim($request->string('search')->toString());
        $allowedStatuses = [
            Opcr::STATUS_ENDORSED,
            'for_pmt_review',
            Opcr::STATUS_APPROVED,
            Opcr::STATUS_RETURNED,
            Opcr::STATUS_SUBMITTED,
        ];
        $selectedStatus = $status;
        $forPmtReviewStatuses = [Opcr::STATUS_ENDORSED, 'for_pmt_review'];

        $opcrsQuery = Opcr::query()
            ->with($this->opcrRelations())
            ->when($activePeriod, function ($query) use ($activePeriod) {
                $query->where(function ($q) use ($activePeriod) {
                    $q->where('performance_period_id', $activePeriod->id)
                        ->orWhereHas('unitWorkPlan', fn ($uwpQuery) => $uwpQuery->where('performance_period_id', $activePeriod->id))
                        ->orWhereHas('unitWorkPlans', fn ($uwpQuery) => $uwpQuery->where('performance_period_id', $activePeriod->id));
                });
            });

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
            $opcrsQuery->where(function ($query) use ($like) {
                $query->whereHas('office', fn ($officeQuery) => $officeQuery->where('name', 'like', $like))
                    ->orWhereHas('performancePeriod', fn ($periodQuery) => $periodQuery->where('name', 'like', $like))
                    ->orWhereHas('unitWorkPlan.office', fn ($officeQuery) => $officeQuery->where('name', 'like', $like))
                    ->orWhereHas('unitWorkPlan.performancePeriod', fn ($periodQuery) => $periodQuery->where('name', 'like', $like))
                    ->orWhereHas('unitWorkPlans.office', fn ($officeQuery) => $officeQuery->where('name', 'like', $like))
                    ->orWhereHas('unitWorkPlans.performancePeriod', fn ($periodQuery) => $periodQuery->where('name', 'like', $like))
                    ->orWhere('id', 'like', $like)
                    ->orWhereRaw('LOWER(status) like ?', [strtolower($like)]);
            });
        }

        if ($status !== '' && in_array($status, $allowedStatuses, true)) {
            if ($status === Opcr::STATUS_ENDORSED || $status === 'for_pmt_review') {
                $opcrsQuery->whereIn('status', $forPmtReviewStatuses);
                $selectedStatus = Opcr::STATUS_ENDORSED;
            } elseif ($status === Opcr::STATUS_SUBMITTED) {
                $opcrsQuery->whereIn('status', [Opcr::STATUS_SUBMITTED, Opcr::STATUS_FOR_REVIEW]);
            } else {
                $opcrsQuery->where('status', $status);
            }
        } elseif ($status !== '') {
            $selectedStatus = Opcr::STATUS_ENDORSED;
            $opcrsQuery->whereIn('status', $forPmtReviewStatuses);
        }

        $opcrs = $opcrsQuery
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        $opcrPayloads = $opcrs->mapWithKeys(fn (Opcr $opcr) => [
            $opcr->id => $this->buildPayload($opcr),
        ]);

        return view('pmt.opcr-review', [
            'activePeriod' => $activePeriod,
            'opcrs' => $opcrs,
            'opcrPayloads' => $opcrPayloads,
            'selectedStatus' => $selectedStatus,
            'searchTerm' => $search,
        ]);
    }

    public function review(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'pmt') {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'opcr_id' => ['required', 'integer', 'exists:opcrs,id'],
            'action' => ['required', Rule::in(['approve', 'return'])],
            'remarks' => ['required_if:action,return', 'nullable', 'string', 'min:3'],
        ]);

        $redirectToList = (bool) $request->boolean('redirect_to_list');
        $wantsJson = $request->expectsJson() || $request->ajax();
        $redirect = function (string $type, string $message) use ($redirectToList, $wantsJson, $request) {
            if ($wantsJson) {
                return response()->json(['status' => $type, 'message' => $message], $type === 'error' ? 422 : 200);
            }
            if ($redirectToList) {
                return redirect()->route('pmt.opcr.review.index')->with($type, $message);
            }
            return back()->with($type, $message);
        };

        return DB::transaction(function () use ($validated, $user, $redirect) {
            /** @var Opcr $opcr */
            $opcr = Opcr::query()
                ->lockForUpdate()
                ->findOrFail($validated['opcr_id']);

            if ($opcr->status === Opcr::STATUS_APPROVED) {
                return $redirect('error', 'OPCR already final approved.');
            }

            $reviewableStatuses = [Opcr::STATUS_ENDORSED, 'for_pmt_review'];
            if (!in_array($opcr->status, $reviewableStatuses, true)) {
                return $redirect('error', 'OPCR is not ready for PMT final review.');
            }

            if ($validated['action'] === 'approve') {
                $opcr->forceFill([
                    'status' => Opcr::STATUS_APPROVED,
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'returned_at' => null,
                    'remarks' => null,
                    'locked_at' => now(),
                ])->save();

                app(IpcrGeneratorService::class)->generateFromOpcr($opcr);

                $opcr->loadMissing(['performancePeriod']);
                $ipcrs = Ipcr::query()
                    ->with('employee')
                    ->where('opcr_id', $opcr->id)
                    ->where('performance_period_id', $opcr->performance_period_id)
                    ->get();

                $notifier = app(WorkflowNotificationDispatcher::class);
                foreach ($ipcrs as $ipcr) {
                    if (!$ipcr->employee) {
                        continue;
                    }

                    $notifier->notifyUser(
                        $ipcr->employee,
                        new WorkflowEventNotification(
                            title: 'IPCR Target Ready',
                            body: 'Your IPCR target for the current period is now ready for commitment.',
                            url: route('employee.ipcr-target'),
                            type: 'success',
                            meta: [
                                'event' => 'ipcr.ready_for_commitment',
                                'ipcr_id' => $ipcr->id,
                                'opcr_id' => $opcr->id,
                                'office_id' => $opcr->office_id,
                                'performance_period_id' => $opcr->performance_period_id,
                                'status' => (string) $ipcr->status,
                                'source_role' => 'pmt',
                            ],
                        )
                    );
                }

                // Notify Dept Head that OPCR was approved
                $opcr->loadMissing('office.head');
                $deptHead = $opcr->office?->head;
                if ($deptHead) {
                    $notifier->notifyUser(
                        $deptHead,
                        new WorkflowEventNotification(
                            title: 'OPCR Approved by PMT',
                            body: "{$user->name} approved your office OPCR.",
                            url: route('dept-head.opcr'),
                            type: 'success',
                            meta: [
                                'event' => 'opcr.pmt_approved',
                                'opcr_id' => $opcr->id,
                                'office_id' => $opcr->office_id,
                                'performance_period_id' => $opcr->performance_period_id,
                                'source_role' => 'pmt',
                            ],
                        )
                    );
                }

                return $redirect('success', 'OPCR final approved.');
            }

            $remarks = trim((string) ($validated['remarks'] ?? ''));
            $opcr->forceFill([
                'status' => Opcr::STATUS_RETURNED,
                'approved_by' => null,
                'approved_at' => null,
                'returned_at' => now(),
                'remarks' => $remarks,
                'locked_at' => null,
            ])->save();

            $sourceIds = $opcr->unitWorkPlans()->pluck('unit_work_plans.id');
            if ($sourceIds->isEmpty() && $opcr->unit_work_plan_id) {
                $sourceIds = collect([(int) $opcr->unit_work_plan_id]);
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
                    'returned_by_role' => 'pmt',
                    'return_remarks' => $remarks,
                ]);

            // Notify Dept Head that OPCR was returned
            $opcr->loadMissing('office.head');
            $deptHead = $opcr->office?->head;
            if ($deptHead) {
                app(WorkflowNotificationDispatcher::class)->notifyUser(
                    $deptHead,
                    new WorkflowEventNotification(
                        title: 'OPCR Returned by PMT',
                        body: "{$user->name} returned your office OPCR for revision.",
                        url: route('dept-head.opcr'),
                        type: 'alert',
                        meta: [
                            'event' => 'opcr.pmt_returned',
                            'opcr_id' => $opcr->id,
                            'office_id' => $opcr->office_id,
                            'performance_period_id' => $opcr->performance_period_id,
                            'source_role' => 'pmt',
                        ],
                    )
                );
            }

            return $redirect('success', 'OPCR returned to Supervisors for UWP correction.');
        });
    }

    public function show(Opcr $opcr)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'pmt') {
            abort(403, 'Unauthorized.');
        }

        $opcr->load($this->opcrRelations());

        $payload = $this->buildPayload($opcr);
        $statusKey = strtolower((string) $opcr->status);
        $isReviewable = in_array($statusKey, [Opcr::STATUS_ENDORSED, 'for_pmt_review'], true);

        return view('pmt.opcr-review-show', [
            'opcr' => $opcr,
            'payload' => $payload,
            'isReviewable' => $isReviewable,
        ]);
    }

    public function export(Opcr $opcr)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'pmt') {
            abort(403, 'Unauthorized.');
        }

        $opcr->load($this->opcrRelations());

        $source = $opcr->sourceUnitWorkPlans()->first();
        $office = Str::slug((string) ($opcr->office?->name ?? $source?->office?->name ?? 'Office'), '_');
        $period = Str::slug((string) ($opcr->performancePeriod?->name ?? $source?->performancePeriod?->name ?? 'Period'), '_');
        
        $latestSignature = \App\Models\UwpConsolidationSignature::query()
            ->where('opcr_id', $opcr->id)
            ->orderByDesc('signed_at')
            ->first();

        if ($latestSignature && $latestSignature->signed_excel_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($latestSignature->signed_excel_path)) {
            $filename = "OPCR_{$office}_{$period}_SIGNED.xlsx";
            return \Illuminate\Support\Facades\Storage::disk('public')->download(
                $latestSignature->signed_excel_path,
                $filename
            );
        }

        $filename = "OPCR_{$office}_{$period}.xlsx";
        return Excel::download(new OpcrExcelExport($opcr), $filename);
    }

    private function buildPayload(Opcr $opcr): array
    {
        $sources = $opcr->sourceUnitWorkPlans();
        $fallbackUwp = $sources->first() ?: $opcr->unitWorkPlan;
        $outputs = [];

        foreach ($sources as $uwp) {
            $uwp->loadMissing([
                'office',
                'performancePeriod',
                'uwpFunctions.mfos.successIndicators.qetStandards',
                'uwpFunctions.mfos.successIndicators.assignments.employee',
            ]);

            foreach ($uwp->uwpFunctions as $function) {
                foreach ($function->mfos as $mfo) {
                    $successIndicators = [];
                    $outputTargetQuantity = 0;
                    $outputTargetTimelines = [];

                    foreach ($mfo->successIndicators as $indicator) {
                        $standardsByRating = [];
                        foreach ([5, 4, 3, 2, 1] as $rating) {
                            $standardsByRating[(string) $rating] = ['Q' => [], 'E' => [], 'T' => []];
                        }

                        foreach ($indicator->qetStandards as $standard) {
                            $rating = (string) $standard->rating;
                            if (!isset($standardsByRating[$rating])) {
                                continue;
                            }

                            $dimension = strtolower((string) $standard->dimension);
                            if (in_array($dimension, ['q', 'quality'], true)) {
                                $standardsByRating[$rating]['Q'][] = (string) $standard->standard_text;
                            } elseif (in_array($dimension, ['e', 'efficiency'], true)) {
                                $standardsByRating[$rating]['E'][] = (string) $standard->standard_text;
                            } elseif (in_array($dimension, ['t', 'timeliness'], true)) {
                                $standardsByRating[$rating]['T'][] = (string) $standard->standard_text;
                            }
                        }

                        $assignees = $indicator->assignments
                            ->map(fn ($assignment) => $assignment->employee?->name)
                            ->filter()
                            ->values()
                            ->all();

                        $targetQuantity = is_numeric($indicator->target_quantity ?? null) ? (int) $indicator->target_quantity : null;
                        $targetTimeline = trim((string) ($indicator->target_timeline ?? ''));

                        if ($targetQuantity !== null && $targetQuantity > 0) {
                            $outputTargetQuantity += $targetQuantity;
                        }

                        if ($targetTimeline !== '' && !in_array($targetTimeline, $outputTargetTimelines, true)) {
                            $outputTargetTimelines[] = $targetTimeline;
                        }

                        $successIndicators[] = [
                            'indicator_text' => (string) ($indicator->indicator_text ?? ''),
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
                        'title' => (string) ($mfo->title ?? ''),
                        'source_uwp_id' => $uwp->id,
                        'target_quantity' => $outputTargetQuantity > 0
                            ? $outputTargetQuantity
                            : (is_numeric($mfo->target_quantity ?? null) ? (int) $mfo->target_quantity : null),
                        'target_summary' => $outputTargetSummary,
                        'weight_percent' => $mfo->weight_percent ?? $function->weight_percent ?? '',
                        'function_type' => strtolower((string) ($function->function_type ?? '')),
                        'success_indicators' => $successIndicators,
                    ];
                }
            }
        }

        return [
            'opcr' => [
                'id' => $opcr->id,
                'status' => (string) $opcr->status,
                'office' => [
                    'name' => $opcr->office?->name ?? $fallbackUwp?->office?->name ?? '',
                ],
                'period' => [
                    'name' => $opcr->performancePeriod?->name ?? $fallbackUwp?->performancePeriod?->name ?? '',
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
        $uwpTree = function ($query) {
            $query->orderBy('sort_order')->with([
                'mfos' => function ($mfoQuery) {
                    $mfoQuery->orderBy('sort_order')->with([
                        'successIndicators' => function ($indicatorQuery) {
                            $indicatorQuery->orderBy('sort_order')->with([
                                'qetStandards',
                                'assignments.employee',
                            ]);
                        },
                    ]);
                },
            ]);
        };

        return [
            'office',
            'performancePeriod',
            'unitWorkPlan.office',
            'unitWorkPlan.performancePeriod',
            'unitWorkPlan.uwpFunctions' => $uwpTree,
            'unitWorkPlans.office',
            'unitWorkPlans.performancePeriod',
            'unitWorkPlans.uwpFunctions' => $uwpTree,
        ];
    }
}
