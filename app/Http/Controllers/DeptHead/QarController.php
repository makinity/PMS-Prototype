<?php

namespace App\Http\Controllers\DeptHead;

use App\Http\Controllers\Controller;
use App\Models\Ipcr;
use App\Models\Mpor;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;
use App\Models\QarHeader;
use App\Models\QarMporLink;
use App\Models\QarRow;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QarController extends Controller
{
    private const QAR_SESSION_KEY = 'stage2_dept_head_qar_state';

    public function index(Request $request)
    {
        $deptHead = $request->user();
        $deptHeadName = (string) ($deptHead?->name ?? '--');
        $office = $deptHead?->office?->name ?? 'Office';
        $officeId = (int) ($deptHead?->office_id ?? 0);

        $period = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();

        $quarterContext = $this->resolveQuarterContext($request, $period);
        $period = $quarterContext['period'];
        $quarterNumber = $quarterContext['quarterNumber'];
        $quarterKey = $quarterContext['quarterKey'];
        $quarterLabel = $quarterContext['quarterLabel'];
        $quarterMonths = $quarterContext['quarterMonths'];
        $allowedQuarterNumbers = $quarterContext['allowedQuarterNumbers'];
        $allowedQuarterOptions = $quarterContext['allowedQuarterOptions'];
        $selectedQuarterNumber = $quarterContext['selectedQuarterNumber'];

        $incomingMporModels = Mpor::query()
            ->with(['employee:id,name,office_id', 'employee.office:id,name'])
            ->when($officeId > 0, function ($query) use ($officeId) {
                $query->where('office_id', $officeId);
            })
            ->whereIn(DB::raw('LOWER(status)'), ['approved'])
            ->whereIn('month', $quarterMonths)
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->get();

        $formatMonthLabel = static function (?string $month): string {
            if (!is_string($month) || trim($month) === '') {
                return '-';
            }

            try {
                return Carbon::createFromFormat('Y-m', $month)->format('M Y');
            } catch (\Throwable) {
                return $month;
            }
        };

        $incomingMpors = $incomingMporModels
            ->map(function (Mpor $mpor) use ($formatMonthLabel): array {
                return [
                    'id' => $mpor->id,
                    'employee' => $mpor->employee?->name ?? '-',
                    'month' => $formatMonthLabel($mpor->month),
                    'status' => (string) ($mpor->status ?? '-'),
                ];
            })
            ->values()
            ->all();

        $consolidatedMpors = $incomingMpors;
        $generatedAt = $incomingMporModels
            ->map(fn (Mpor $mpor) => $mpor->submitted_at ?? $mpor->generated_at)
            ->filter()
            ->sortBy(fn ($dt) => $dt instanceof Carbon ? $dt->getTimestamp() : 0)
            ->last();

        $sessionKey = self::QAR_SESSION_KEY . ':' . $quarterKey;
        $state = $request->session()->get($sessionKey, []);
        if (!is_array($state)) {
            $state = [];
        }

        $status = (string) ($state['status'] ?? QarHeader::STATUS_DRAFT);
        $status = str_replace('-', '_', $status);
        if ($status === 'endorsed') {
            $status = QarHeader::STATUS_DEPT_HEAD_ENDORSED;
        }
        if (!in_array($status, [QarHeader::STATUS_DRAFT, QarHeader::STATUS_DEPT_HEAD_ENDORSED, QarHeader::STATUS_PMT_APPROVED], true)) {
            $status = QarHeader::STATUS_DRAFT;
        }
        $approvedAt = $state['approved_at'] ?? null;

        $state['seeded'] = true;
        $state['incoming_mpors'] = [];
        $state['consolidated_mpors'] = $consolidatedMpors;
        $state['qar_rows'] = [];
        $state['generated_at'] = $generatedAt
            ? Carbon::parse($generatedAt)->toDateTimeString()
            : ($state['generated_at'] ?? null);
        $state['status'] = $status;
        $state['approved_at'] = $approvedAt;
        $pmtStatusLabel = 'Pending validation';

        $annexRowsMap = [];
        foreach ($incomingMporModels as $mpor) {
            $ratedEntriesForAnnex = $mpor->ratedOrsEntriesForMonth()
                ->with('ipcrItem')
                ->get();

            foreach ($ratedEntriesForAnnex as $entry) {
                $ipcrItemId = (int) ($entry->ipcr_item_id ?? 0);
                if ($ipcrItemId <= 0) {
                    continue;
                }

                $item = $entry->ipcrItem;
                if (!$item) {
                    continue;
                }

                if (!isset($annexRowsMap[$ipcrItemId])) {
                    $annexRowsMap[$ipcrItemId] = [
                        'ppa_code' => (string) $ipcrItemId,
                        'mfo' => (string) ($item->output_title ?? '-'),
                        'indicator' => (string) ($item->indicator_text ?? '-'),
                        'target_timeline' => (string) ($item->target_summary ?? '-'),
                        'actual_performance' => 0.0,
                        'remarks' => 'Derived from rated ORS entries',
                    ];
                }

                $annexRowsMap[$ipcrItemId]['actual_performance'] += (float) ($entry->quantity ?? 0);
            }
        }

        $rows = collect($annexRowsMap)
            ->sortKeys()
            ->map(function (array $row): array {
                $row['actual_performance'] = round((float) $row['actual_performance'], 2);

                return $row;
            })
            ->values()
            ->all();

        $state['qar_rows'] = $rows;
        $request->session()->put($sessionKey, $state);

        $uwpTargetTimelineMap = [];

        $includedMporCount = count($consolidatedMpors);
        $includedEmployeeCount = collect($consolidatedMpors)
            ->pluck('employee')
            ->filter(fn ($name) => is_string($name) && trim($name) !== '' && $name !== '-')
            ->unique()
            ->count();
        $includedMonthsTotal = 3;
        $includedMonthsCount = collect($consolidatedMpors)
            ->pluck('month')
            ->filter(fn ($monthLabel) => is_string($monthLabel) && trim($monthLabel) !== '' && $monthLabel !== '-')
            ->unique()
            ->count();

        $requestedMporId = (int) $request->query('mpor_id', 0);
        $selectedMpor = $requestedMporId > 0
            ? $incomingMporModels->firstWhere('id', $requestedMporId)
            : null;
        if (!$selectedMpor) {
            $selectedMpor = $incomingMporModels->first();
        }

        $selectedMporDetail = [
            'employee_name' => '--',
            'office_division' => $office,
            'month_label' => '--',
            'status' => 'Approved (Locked)',
            'submitted_at' => '-',
            'groups' => [],
            'summary' => [
                'week1_total' => 0,
                'week2_total' => 0,
                'week3_total' => 0,
                'week4_total' => 0,
                'grand_total' => 0,
                'included_entries' => 0,
                'excluded_entries' => 0,
            ],
            'confirmed' => [
                'supervisor_name' => '--',
                'employee_name' => '--',
            ],
        ];

        if ($selectedMpor && !empty($selectedMpor->employee_id)) {
            try {
                $monthStart = Carbon::createFromFormat('Y-m', (string) $selectedMpor->month)->startOfMonth();
            } catch (\Throwable) {
                $monthStart = Carbon::now()->startOfMonth();
            }
            $monthEnd = $monthStart->copy()->endOfMonth();

            $ipcrQuery = Ipcr::query()
                ->with([
                    'items:id,ipcr_id,output_title,function_type,indicator_text,standards_payload',
                ])
                ->where('employee_id', $selectedMpor->employee_id)
                ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT]);

            if ($period) {
                $ipcrQuery->where('performance_period_id', $period->id);
            }

            $ipcr = $ipcrQuery
                ->orderByDesc('generated_at')
                ->orderByDesc('id')
                ->first();

            if (!$ipcr) {
                $ipcr = Ipcr::query()
                    ->with([
                        'items:id,ipcr_id,output_title,function_type,indicator_text,standards_payload',
                    ])
                    ->where('employee_id', $selectedMpor->employee_id)
                    ->whereIn('status', [Ipcr::STATUS_COMMITTED, Ipcr::STATUS_FOR_COMMITMENT])
                    ->orderByDesc('generated_at')
                    ->orderByDesc('id')
                    ->first();
            }

            $normalizeOutputKey = static function (string $outputTitle): string {
                return mb_strtolower(
                    trim((string) preg_replace('/\s+/', ' ', $outputTitle))
                );
            };

            $mporRows = [];

            if ($ipcr) {
                foreach ($ipcr->items as $item) {
                    $outputTitle = trim((string) ($item->output_title ?? ''));
                    if ($outputTitle === '') {
                        continue;
                    }

                    $outputKey = $normalizeOutputKey($outputTitle);
                    if ($outputKey === '') {
                        continue;
                    }

                    $functionType = strtolower(trim((string) ($item->function_type ?? '')));
                    $section = str_contains($functionType, 'support') ? 'support' : 'core';

                    if (!isset($mporRows[$outputKey])) {
                        $mporRows[$outputKey] = [
                            'id' => $outputKey,
                            'label' => $outputTitle,
                            'section' => $section,
                            'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                            'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                            'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                            'qtyTotal' => 0,
                            'qualTotal' => 0,
                            'timeTotal' => 0,
                        ];
                    }
                }
            }

            $ratedEntries = collect();

            if ($ipcr) {
                $ratedEntries = OrsEntry::query()
                    ->with([
                        'ipcrItem:id,output_title,function_type',
                        'monitoring:ors_entry_id,quality_rating,timeliness_rating,supervisor_id',
                        'monitoring.supervisor:id,name',
                    ])
                    ->where('employee_id', $selectedMpor->employee_id)
                    ->where('ipcr_id', $ipcr->id)
                    ->where('status', 'rated')
                    ->where('quantity', '>', 0)
                    ->whereBetween('work_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                    ->whereHas('monitoring', function ($q) {
                        $q->whereNotNull('quality_rating')
                            ->whereNotNull('timeliness_rating');
                    })
                    ->orderBy('work_date')
                    ->get();
            }

            foreach ($ratedEntries as $entry) {
                $outputTitle = trim((string) data_get($entry, 'ipcrItem.output_title', ''));
                $outputKey = $normalizeOutputKey($outputTitle);

                if ($outputKey === '' || !isset($mporRows[$outputKey])) {
                    continue;
                }

                $day = (int) Carbon::parse((string) $entry->work_date)->format('j');
                $week = $day <= 7 ? 1 : ($day <= 14 ? 2 : ($day <= 21 ? 3 : 4));

                $qty = (float) ($entry->quantity ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $qualityRating = (float) data_get($entry, 'monitoring.quality_rating', 0);
                $timelinessRating = (float) data_get($entry, 'monitoring.timeliness_rating', 0);

                $mporRows[$outputKey]['qty'][$week] += $qty;
                $mporRows[$outputKey]['qual'][$week] += ($qty * $qualityRating);
                $mporRows[$outputKey]['time'][$week] += ($qty * $timelinessRating);
            }

            $sectionRows = [
                'core' => [],
                'support' => [],
            ];

            foreach (array_values($mporRows) as $row) {
                $row['qtyTotal'] = array_sum($row['qty']);
                $row['qualTotal'] = array_sum($row['qual']);
                $row['timeTotal'] = array_sum($row['time']);

                if ($row['qtyTotal'] <= 0) {
                    continue;
                }

                $sectionRows[$row['section'] === 'support' ? 'support' : 'core'][] = $row;
            }

            foreach (['core', 'support'] as $sectionKey) {
                $sectionRows[$sectionKey] = collect($sectionRows[$sectionKey])
                    ->sortBy(fn (array $row) => strtolower((string) ($row['label'] ?? '')))
                    ->values()
                    ->all();
            }

            $grandTotals = [
                'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                'qtyTotal' => 0,
                'qualTotal' => 0,
                'timeTotal' => 0,
            ];

            foreach ($sectionRows as $rowsBySection) {
                foreach ($rowsBySection as $row) {
                    foreach ([1, 2, 3, 4] as $week) {
                        $grandTotals['qty'][$week] += $row['qty'][$week];
                        $grandTotals['qual'][$week] += $row['qual'][$week];
                        $grandTotals['time'][$week] += $row['time'][$week];
                    }
                }
            }

            $grandTotals['qtyTotal'] = array_sum($grandTotals['qty']);
            $grandTotals['qualTotal'] = array_sum($grandTotals['qual']);
            $grandTotals['timeTotal'] = array_sum($grandTotals['time']);

            $toModalRows = static function (array $rowsBySection): array {
                return collect($rowsBySection)
                    ->map(function (array $row): array {
                        return [
                            'task_title' => (string) ($row['label'] ?? '-'),
                            'eff' => [
                                'w1' => (int) round((float) ($row['qty'][1] ?? 0)),
                                'w2' => (int) round((float) ($row['qty'][2] ?? 0)),
                                'w3' => (int) round((float) ($row['qty'][3] ?? 0)),
                                'w4' => (int) round((float) ($row['qty'][4] ?? 0)),
                                'total' => (int) round((float) ($row['qtyTotal'] ?? 0)),
                            ],
                            'qual' => [
                                'w1' => (int) round((float) ($row['qual'][1] ?? 0)),
                                'w2' => (int) round((float) ($row['qual'][2] ?? 0)),
                                'w3' => (int) round((float) ($row['qual'][3] ?? 0)),
                                'w4' => (int) round((float) ($row['qual'][4] ?? 0)),
                                'total' => (int) round((float) ($row['qualTotal'] ?? 0)),
                            ],
                            'time' => [
                                'w1' => (int) round((float) ($row['time'][1] ?? 0)),
                                'w2' => (int) round((float) ($row['time'][2] ?? 0)),
                                'w3' => (int) round((float) ($row['time'][3] ?? 0)),
                                'w4' => (int) round((float) ($row['time'][4] ?? 0)),
                                'total' => (int) round((float) ($row['timeTotal'] ?? 0)),
                            ],
                        ];
                    })
                    ->values()
                    ->all();
            };

            $groups = [];
            if (!empty($sectionRows['core'])) {
                $groups[] = [
                    'label' => 'CORE FUNCTIONS',
                    'weight_label' => '80%',
                    'rows' => $toModalRows($sectionRows['core']),
                ];
            }
            if (!empty($sectionRows['support'])) {
                $groups[] = [
                    'label' => 'SUPPORT FUNCTIONS',
                    'weight_label' => '20%',
                    'rows' => $toModalRows($sectionRows['support']),
                ];
            }

            $supervisorName = trim((string) data_get($ratedEntries->first(), 'monitoring.supervisor.name', ''));
            if ($supervisorName === '') {
                $supervisorName = '--';
            }

            $submittedAt = $selectedMpor->submitted_at ?? $selectedMpor->generated_at;

            $selectedMporDetail = [
                'employee_name' => $selectedMpor->employee?->name ?? '--',
                'office_division' => $office,
                'month_label' => $formatMonthLabel($selectedMpor->month),
                'status' => 'Approved (Locked)',
                'submitted_at' => $submittedAt
                    ? Carbon::parse($submittedAt)->format('M d, Y g:i A')
                    : '-',
                'groups' => $groups,
                'summary' => [
                    'week1_total' => (int) round((float) ($grandTotals['qty'][1] ?? 0)),
                    'week2_total' => (int) round((float) ($grandTotals['qty'][2] ?? 0)),
                    'week3_total' => (int) round((float) ($grandTotals['qty'][3] ?? 0)),
                    'week4_total' => (int) round((float) ($grandTotals['qty'][4] ?? 0)),
                    'grand_total' => (int) round((float) ($grandTotals['qtyTotal'] ?? 0)),
                    'included_entries' => $ratedEntries->count(),
                    'excluded_entries' => 0,
                ],
                'confirmed' => [
                    'supervisor_name' => $supervisorName,
                    'employee_name' => $selectedMpor->employee?->name ?? '--',
                ],
            ];
        }

        // If requested via AJAX as JSON, return only the selected MPOR detail
        if ($request->wantsJson()) {
            return response()->json($selectedMporDetail);
        }

        return view('dept-head.qar', compact(
            'deptHeadName',
            'office',
            'officeId',
            'period',
            'quarterNumber',
            'quarterKey',
            'quarterLabel',
            'allowedQuarterNumbers',
            'allowedQuarterOptions',
            'selectedQuarterNumber',
            'incomingMpors',
            'consolidatedMpors',
            'generatedAt',
            'status',
            'approvedAt',
            'pmtStatusLabel',
            'rows',
            'uwpTargetTimelineMap',
            'includedMporCount',
            'includedEmployeeCount',
            'includedMonthsTotal',
            'includedMonthsCount',
            'selectedMporDetail'
        ));
    }

    public function endorse(Request $request)
    {
        $deptHead = $request->user();
        $officeId = (int) ($deptHead?->office_id ?? 0);
        if ($officeId <= 0) {
            return redirect()
                ->route('dept-head.qar')
                ->with('info', 'Dept Head office is required.');
        }

        $period = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();
        if (!$period) {
            return redirect()
                ->route('dept-head.qar')
                ->with('info', 'No active performance period found.');
        }

        $quarterContext = $this->resolveQuarterContext($request, $period);
        $quarterNumber = $quarterContext['quarterNumber'];
        $quarterKey = $quarterContext['quarterKey'];
        $quarterMonths = $quarterContext['quarterMonths'];

        $incomingMporModels = Mpor::query()
            ->with(['employee:id,name,office_id'])
            ->where('office_id', $officeId)
            ->whereIn(DB::raw('LOWER(status)'), ['approved'])
            ->whereIn('month', $quarterMonths)
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->get();

        $generatedAt = $incomingMporModels
            ->map(fn (Mpor $mpor) => $mpor->submitted_at ?? $mpor->generated_at)
            ->filter()
            ->sortBy(fn ($dt) => $dt instanceof Carbon ? $dt->getTimestamp() : 0)
            ->last();
        $generatedAt = $generatedAt ? Carbon::parse($generatedAt) : now();

        $annexRowsMap = [];
        foreach ($incomingMporModels as $mpor) {
            $ratedEntriesForAnnex = $mpor->ratedOrsEntriesForMonth()
                ->with('ipcrItem')
                ->get();

            foreach ($ratedEntriesForAnnex as $entry) {
                $ipcrItemId = (int) ($entry->ipcr_item_id ?? 0);
                if ($ipcrItemId <= 0) {
                    continue;
                }

                $item = $entry->ipcrItem;
                if (!$item) {
                    continue;
                }

                if (!isset($annexRowsMap[$ipcrItemId])) {
                    $annexRowsMap[$ipcrItemId] = [
                        'ppa_code' => (string) $ipcrItemId,
                        'mfo' => (string) ($item->output_title ?? '-'),
                        'indicator' => (string) ($item->indicator_text ?? '-'),
                        'target_timeline' => (string) ($item->target_summary ?? '-'),
                        'actual_performance' => 0.0,
                        'remarks' => 'Derived from rated ORS entries',
                    ];
                }

                $annexRowsMap[$ipcrItemId]['actual_performance'] += (float) ($entry->quantity ?? 0);
            }
        }

        $rows = collect($annexRowsMap)
            ->sortKeys()
            ->map(function (array $row): array {
                $row['actual_performance'] = round((float) $row['actual_performance'], 2);

                return $row;
            })
            ->values()
            ->all();

        $existingHeader = QarHeader::query()
            ->where('office_id', $officeId)
            ->where('performance_period_id', $period->id)
            ->where('quarter_key', $quarterKey)
            ->first();

        if (
            $existingHeader
            && in_array((string) $existingHeader->status, [QarHeader::STATUS_DEPT_HEAD_ENDORSED, QarHeader::STATUS_PMT_APPROVED], true)
        ) {
            return redirect()
                ->route('dept-head.qar', ['q' => $quarterNumber])
                ->with('info', 'QAR is already endorsed.');
        }

        $approvedAt = now();
        $header = null;

        DB::transaction(function () use (
            &$header,
            $officeId,
            $period,
            $quarterKey,
            $generatedAt,
            $approvedAt,
            $deptHead,
            $rows,
            $incomingMporModels
        ) {
            $header = QarHeader::query()->updateOrCreate(
                [
                    'office_id' => $officeId,
                    'performance_period_id' => $period->id,
                    'quarter_key' => $quarterKey,
                ],
                [
                    'status' => QarHeader::STATUS_DEPT_HEAD_ENDORSED,
                    'generated_at' => $generatedAt,
                    'generated_by' => $deptHead?->id,
                    'approved_at' => $approvedAt,
                    'approved_by' => $deptHead?->id,
                    'pmt_status' => QarHeader::PMT_PENDING,
                ]
            );

            if (empty($header->pmt_status)) {
                $header->pmt_status = QarHeader::PMT_PENDING;
                $header->save();
            }

            QarRow::query()
                ->where('qar_header_id', $header->id)
                ->delete();

            foreach ($rows as $index => $row) {
                QarRow::query()->create([
                    'qar_header_id' => $header->id,
                    'ppa_code' => (string) ($row['ppa_code'] ?? ''),
                    'mfo_title' => (string) ($row['mfo'] ?? ''),
                    'indicator_text' => (string) ($row['indicator'] ?? ''),
                    'target_timeline' => (string) ($row['target_timeline'] ?? '-'),
                    'actual_performance' => (float) ($row['actual_performance'] ?? 0),
                    'remarks' => (string) ($row['remarks'] ?? ''),
                    'sort_order' => $index + 1,
                ]);
            }

            QarMporLink::query()
                ->where('qar_header_id', $header->id)
                ->delete();

            foreach ($incomingMporModels as $mpor) {
                $monthLabel = '-';
                if (is_string($mpor->month) && trim($mpor->month) !== '') {
                    try {
                        $monthLabel = Carbon::createFromFormat('Y-m', $mpor->month)->format('M Y');
                    } catch (\Throwable) {
                        $monthLabel = (string) $mpor->month;
                    }
                }

                QarMporLink::query()->create([
                    'qar_header_id' => $header->id,
                    'mpor_id' => $mpor->id,
                    'employee_name' => $mpor->employee?->name ?? '-',
                    'month_label' => $monthLabel,
                    'status_label' => (string) ($mpor->status ?? '-'),
                ]);
            }
        });

        $sessionKey = self::QAR_SESSION_KEY . ':' . $quarterKey;
        $state = $request->session()->get($sessionKey, []);
        if (!is_array($state)) {
            $state = [];
        }
        $state['status'] = QarHeader::STATUS_DEPT_HEAD_ENDORSED;
        $state['approved_at'] = $approvedAt->toDateTimeString();
        $state['generated_at'] = Carbon::parse($header?->generated_at ?? $generatedAt)->toDateTimeString();
        $request->session()->put($sessionKey, $state);

        return redirect()
            ->route('dept-head.qar', ['q' => $quarterNumber])
            ->with('success', 'QAR endorsed and saved');
    }

    public function generate(Request $request)
    {
        $period = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();
        $quarterContext = $this->resolveQuarterContext($request, $period);

        return redirect()
            ->route('dept-head.qar', ['q' => $quarterContext['selectedQuarterNumber']])
            ->with('info', 'Consolidation is automatic. Approved MPORs are now used directly.');
    }

    public function reset(Request $request)
    {
        $period = PerformancePeriod::query()
            ->where('is_active', true)
            ->first();
        $quarterContext = $this->resolveQuarterContext($request, $period);
        $quarterKey = $quarterContext['quarterKey'];
        $quarterNumber = $quarterContext['selectedQuarterNumber'];

        $request->session()->forget(self::QAR_SESSION_KEY);
        $request->session()->forget(self::QAR_SESSION_KEY . ':' . $quarterKey);

        return redirect()
            ->route('dept-head.qar', ['q' => $quarterNumber])
            ->with('success', 'Prototype reset.');
    }

    private function resolveQuarterContext(Request $request, ?PerformancePeriod $period = null): array
    {
        $period = $period ?: PerformancePeriod::query()
            ->where('is_active', true)
            ->first();

        $quarterData = $this->buildAllowedQuarterData($period);
        $allowedQuarterNumbers = $quarterData['allowedQuarterNumbers'];
        $allowedQuarterOptions = $quarterData['allowedQuarterOptions'];
        $yearForQuarterKey = $quarterData['yearForQuarterKey'];

        $requestedQ = (int) $request->input('q', 0);
        $currentQ = (int) ceil(now()->month / 3);

        if (in_array($requestedQ, $allowedQuarterNumbers, true)) {
            $selectedQuarterNumber = $requestedQ;
        } elseif (in_array($currentQ, $allowedQuarterNumbers, true)) {
            $selectedQuarterNumber = $currentQ;
        } else {
            $selectedQuarterNumber = (int) ($allowedQuarterNumbers[0] ?? $currentQ);
        }

        $quarterNumber = $selectedQuarterNumber;
        $quarterKey = sprintf('%d-Q%d', $yearForQuarterKey, $quarterNumber);
        $quarterLabel = sprintf('Q%d %d', $quarterNumber, $yearForQuarterKey);

        $quarterStartMonth = (($quarterNumber - 1) * 3) + 1;
        $quarterMonths = collect(range($quarterStartMonth, $quarterStartMonth + 2))
            ->map(fn (int $month): string => sprintf('%d-%02d', $yearForQuarterKey, $month))
            ->all();

        return [
            'period' => $period,
            'year' => $yearForQuarterKey,
            'quarterNumber' => $quarterNumber,
            'quarterKey' => $quarterKey,
            'quarterLabel' => $quarterLabel,
            'quarterMonths' => $quarterMonths,
            'allowedQuarterNumbers' => $allowedQuarterNumbers,
            'allowedQuarterOptions' => $allowedQuarterOptions,
            'selectedQuarterNumber' => $selectedQuarterNumber,
        ];
    }

    private function buildAllowedQuarterData(?PerformancePeriod $period): array
    {
        $now = now();
        $currentQuarter = (int) ceil($now->month / 3);
        $yearForQuarterKey = (int) $now->year;

        if (!$period || empty($period->start_date) || empty($period->end_date)) {
            return [
                'allowedQuarterNumbers' => [$currentQuarter],
                'allowedQuarterOptions' => [
                    ['value' => $currentQuarter, 'label' => 'Q' . $currentQuarter],
                ],
                'yearForQuarterKey' => $yearForQuarterKey,
            ];
        }

        try {
            $start = Carbon::parse($period->start_date)->startOfMonth();
            $end = Carbon::parse($period->end_date)->startOfMonth();
        } catch (\Throwable) {
            return [
                'allowedQuarterNumbers' => [$currentQuarter],
                'allowedQuarterOptions' => [
                    ['value' => $currentQuarter, 'label' => 'Q' . $currentQuarter],
                ],
                'yearForQuarterKey' => $yearForQuarterKey,
            ];
        }

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $yearForQuarterKey = (int) $start->year;

        $quarters = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $quarters[] = (int) ceil($cursor->month / 3);
            $cursor->addMonthNoOverflow();
        }

        $allowedQuarterNumbers = collect($quarters)
            ->filter(fn ($q) => $q >= 1 && $q <= 4)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (empty($allowedQuarterNumbers)) {
            $allowedQuarterNumbers = [$currentQuarter];
        }

        $allowedQuarterOptions = collect($allowedQuarterNumbers)
            ->map(fn (int $q) => ['value' => $q, 'label' => 'Q' . $q])
            ->values()
            ->all();

        return [
            'allowedQuarterNumbers' => $allowedQuarterNumbers,
            'allowedQuarterOptions' => $allowedQuarterOptions,
            'yearForQuarterKey' => $yearForQuarterKey,
        ];
    }
}
