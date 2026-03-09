<?php

namespace App\Http\Controllers\Pmt;

use App\Http\Controllers\Controller;
use App\Exports\StageOne\OpcrExcelExport;
use App\Models\Opcr;
use App\Models\PerformancePeriod;
use App\Services\IpcrGeneratorService;
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
        $allowedStatuses = [
            Opcr::STATUS_ENDORSED,
            'for_pmt_review',
            Opcr::STATUS_APPROVED,
            Opcr::STATUS_RETURNED,
            Opcr::STATUS_SUBMITTED,
        ];
        $selectedStatus = $status;
        $forPmtReviewStatuses = [
            Opcr::STATUS_ENDORSED,
            'for_pmt_review',
        ];

        $opcrsQuery = Opcr::query()
            ->with([
                'unitWorkPlan.office',
                'unitWorkPlan.performancePeriod',
                'unitWorkPlan.uwpFunctions' => function ($query) {
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
                },
            ])
            ->when($activePeriod, function ($query) use ($activePeriod) {
                $query->whereHas('unitWorkPlan', fn ($uwpQuery) => $uwpQuery->where('performance_period_id', $activePeriod->id));
            });

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

        $opcrPayloads = $opcrs->mapWithKeys(function (Opcr $opcr) {
            return [$opcr->id => $this->buildPayload($opcr)];
        });

        return view('pmt.opcr-review', [
            'activePeriod' => $activePeriod,
            'opcrs' => $opcrs,
            'opcrPayloads' => $opcrPayloads,
            'selectedStatus' => $selectedStatus,
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

        return DB::transaction(function () use ($validated) {
            /** @var Opcr $opcr */
            $opcr = Opcr::query()
                ->lockForUpdate()
                ->findOrFail($validated['opcr_id']);

            if ($opcr->status === Opcr::STATUS_APPROVED) {
                return back()->with('error', 'OPCR already final approved.');
            }

            $reviewableStatuses = [Opcr::STATUS_ENDORSED, 'for_pmt_review'];
            if (!in_array($opcr->status, $reviewableStatuses, true)) {
                return back()->with('error', 'OPCR is not ready for PMT final review.');
            }

            if ($validated['action'] === 'approve') {
                $wasApproved = ($opcr->status === Opcr::STATUS_APPROVED);

                $opcr->status = Opcr::STATUS_APPROVED;

                if (array_key_exists('remarks', $opcr->getAttributes())) {
                    $opcr->remarks = null;
                }

                $opcr->save();

                if (!$wasApproved) {
                    app(IpcrGeneratorService::class)->generateFromOpcr($opcr);
                }

                return back()->with('success', 'OPCR final approved.');
            }

            $opcr->status = Opcr::STATUS_RETURNED;

            if (array_key_exists('remarks', $opcr->getAttributes())) {
                $opcr->remarks = trim((string) ($validated['remarks'] ?? ''));
            }

            $opcr->save();

            return back()->with('success', 'OPCR returned for correction.');
        });
    }

    public function export(Opcr $opcr)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'pmt') {
            abort(403, 'Unauthorized.');
        }

        $opcr->load([
            'unitWorkPlan.office.head',
            'unitWorkPlan.performancePeriod',
            'unitWorkPlan.creator',
            'unitWorkPlan.uwpFunctions' => function ($query) {
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
            },
        ]);

        $office = Str::slug((string) ($opcr->unitWorkPlan?->office?->name ?? 'Office'), '_');
        $period = Str::slug((string) ($opcr->unitWorkPlan?->performancePeriod?->name ?? 'Period'), '_');
        $filename = "OPCR_{$office}_{$period}.xlsx";

        return Excel::download(new OpcrExcelExport($opcr), $filename);
    }

    private function buildPayload(Opcr $opcr): array
    {
        $uwp = $opcr->unitWorkPlan;
        $outputs = [];

        if ($uwp) {
            foreach ($uwp->uwpFunctions as $function) {
                foreach ($function->mfos as $mfo) {
                    $successIndicators = [];

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

                        $successIndicators[] = [
                            'indicator_text' => (string) ($indicator->indicator_text ?? ''),
                            'standards_by_rating' => $standardsByRating,
                            'assignees' => $assignees,
                        ];
                    }

                    $outputs[] = [
                        'title' => (string) ($mfo->title ?? ''),
                        'target_quantity' => $mfo->target_quantity,
                        'target_summary' => (string) ($mfo->target_timeline ?? ''),
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
                    'name' => $uwp?->office?->name ?? '',
                ],
                'period' => [
                    'name' => $uwp?->performancePeriod?->name ?? '',
                ],
                'source_uwp' => [
                    'id' => $uwp?->id ?? '',
                    'status' => (string) ($uwp?->status ?? ''),
                ],
            ],
            'outputs' => $outputs,
        ];
    }
}
