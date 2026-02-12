@extends('layouts.dept-head')

@section('main-content')
    @php
        /**
         * STAGE II — QAR (Dept Head)
         * - Read-only
         * - Derived from SUBMITTED + MONITORED (rated) ORS entries
         * - Consolidation layer for quarterly monitoring summaries (NO evaluation)
         */

        // -------------------------
        // LOCKED DEMO CONTEXT
        // -------------------------
        $performancePeriodLabel = 'January – June 2026';
        $officeUnit = 'Revenue Collection Unit';

        // -------------------------
        // DUMMY SUBMITTED + RATED ORS (SOURCE)
        // (Alignment target from your provided submitted ORS samples)
        // -------------------------
        $orsSubmittedRated = [
            [
                'employee' => 'Ramon Reyes',
                'office' => $officeUnit,
                'date_submitted' => 'January 4, 2026',
                'month_key' => '2026-01',
                'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'function' => 'core',
                'output_type' => 'Bank Statement Form (BSF-01)',
                'indicator' => 'All e-bank transactions scanned and encoded daily',
                'request_id' => 'REQ-2026-004',
                'duration_label' => '1h 30m',
                'evidence' => 'Evidence attached',
                'quantity_label' => '1 Daily Batch',
                'quantity_value' => 1, // numeric for point computation
                'status' => 'Submitted (Locked)',
                'quality_rating' => 5,
                'timeliness_rating' => 5,
                'remarks' => 'Goods',
            ],
            [
                'employee' => 'Ramon Reyes',
                'office' => $officeUnit,
                'date_submitted' => 'January 2, 2026',
                'month_key' => '2026-01',
                'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                'function' => 'core',
                'output_type' => 'Official Receipt (OR)',
                'indicator' => 'Same-day verification of OTC transactions',
                'request_id' => 'REQ-2026-002',
                'duration_label' => '2h 00m',
                'evidence' => 'Evidence attached',
                'quantity_label' => '12 transactions',
                'quantity_value' => 12,
                'status' => 'Submitted (Locked)',
                'quality_rating' => 5,
                'timeliness_rating' => 5,
                'remarks' => 'Goods',
            ],
        ];

        // -------------------------
        // QAR PERIOD (Quarterly)
        // -------------------------
        $qarPeriod = [
            'key' => 'Q1-2026',
            'label' => 'January – March 2026 (Q1 2026)',
            'months' => [
                ['key' => '2026-01', 'label' => 'January'],
                ['key' => '2026-02', 'label' => 'February'],
                ['key' => '2026-03', 'label' => 'March'],
            ],
        ];

        // -------------------------
        // LOCKED MFO LIST (for consistent row order + includes support row)
        // -------------------------
        $mfos = [
            [
                'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'function' => 'core',
            ],
            [
                'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                'function' => 'core',
            ],
            [
                'mfo' => 'Maintenance of Revenue Records Filing System',
                'function' => 'support',
            ],
        ];

        // -------------------------
        // AGGREGATION — Derived from ORS -> monthly rollup (MPOR-like) -> quarterly view (QAR)
        // NOTE: QAR is a consolidation layer. We show Quantity + Quality Points + Timeliness Points.
        // -------------------------
        $initMonthBucket = function () use ($qarPeriod) {
            $bucket = [];
            foreach ($qarPeriod['months'] as $m) {
                $bucket[$m['key']] = [
                    'qty' => 0,
                    'q_points' => 0,
                    't_points' => 0,
                ];
            }
            return $bucket;
        };

        // Per MFO aggregates by month
        $mfoAgg = [];
        foreach ($mfos as $row) {
            $mfoAgg[$row['mfo']] = [
                'function' => $row['function'],
                'months' => $initMonthBucket(),
                'totals' => ['qty' => 0, 'q_points' => 0, 't_points' => 0],
            ];
        }

        // Global aggregates
        $totalEntries = count($orsSubmittedRated);
        $monthsWithActivity = [];
        $totalQty = 0;
        $totalQPoints = 0;
        $totalTPoints = 0;

        foreach ($orsSubmittedRated as $e) {
            $mfo = $e['mfo'];
            $month = $e['month_key'];

            // Only aggregate if within selected quarter months
            $monthKeys = array_map(fn($x) => $x['key'], $qarPeriod['months']);
            if (!in_array($month, $monthKeys, true)) {
                continue;
            }

            $qty = (int) ($e['quantity_value'] ?? 0);
            $qRating = (int) ($e['quality_rating'] ?? 0);
            $tRating = (int) ($e['timeliness_rating'] ?? 0);

            $qPoints = $qty * $qRating;
            $tPoints = $qty * $tRating;

            if (!isset($mfoAgg[$mfo])) {
                // ignore unexpected rows (prototype safety)
                continue;
            }

            $mfoAgg[$mfo]['months'][$month]['qty'] += $qty;
            $mfoAgg[$mfo]['months'][$month]['q_points'] += $qPoints;
            $mfoAgg[$mfo]['months'][$month]['t_points'] += $tPoints;

            $mfoAgg[$mfo]['totals']['qty'] += $qty;
            $mfoAgg[$mfo]['totals']['q_points'] += $qPoints;
            $mfoAgg[$mfo]['totals']['t_points'] += $tPoints;

            $monthsWithActivity[$month] = true;

            $totalQty += $qty;
            $totalQPoints += $qPoints;
            $totalTPoints += $tPoints;
        }

        $monthsWithActivityLabels = [];
        foreach ($qarPeriod['months'] as $m) {
            if (!empty($monthsWithActivity[$m['key']])) {
                $monthsWithActivityLabels[] = $m['label'] . ' 2026';
            }
        }
        $monthsWithActivityText = count($monthsWithActivityLabels) ? implode(', ', $monthsWithActivityLabels) : '—';

        // QAR list (single item demo)
        $qarEntries = [
            [
                'period' => $qarPeriod['label'],
                'office' => $officeUnit,
                'source' => 'Derived from submitted + monitored ORS (Demo)',
                'status' => 'Monitoring',
            ],
        ];
    @endphp

    <section class="space-y-6">

        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Quarterly Accomplishment Report (QAR)
                </p>
                <h1 class="text-2xl font-bold text-white">QAR Monitoring</h1>
                <p class="text-sm text-slate-400">
                    Quarterly consolidation of monitoring totals derived from submitted + monitored ORS (via MPOR rules). No validation or performance rating occurs in Stage II.
                </p>
                <p class="text-[11px] text-slate-500 mt-1">
                    Read-only | Monitoring copy | Stage III handles evaluation and calibration
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button"
                        disabled
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-500 bg-slate-800/60 cursor-not-allowed">
                    Export QAR (Monitoring Copy)
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">QAR List</h2>
                    <p class="text-xs text-slate-400">Stage II – Quarterly monitoring summary (Dept Head view only)</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Period</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Source</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach ($qarEntries as $qar)
                            <tr class="hover:bg-slate-900">
                                <td class="px-4 py-3">{{ $qar['period'] }}</td>
                                <td class="px-4 py-3 text-slate-200">{{ $qar['office'] }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $qar['source'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full border border-blue-500/40 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                                        Monitoring
                                    </span>
                                    <span class="rounded-full border border-slate-500/40 bg-slate-900/60 px-2 py-1 text-[11px] font-semibold text-slate-300 ml-1">
                                        Read-only
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        data-qar-view
                                        data-period="{{ $qar['period'] }}"
                                        data-office="{{ $qar['office'] }}"
                                        data-source="{{ $qar['source'] }}"
                                        class="text-blue-400 hover:text-blue-300 text-xs font-semibold">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <div id="qar-view-modal"
         class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Quarterly Accomplishment Report (QAR)</p>
                    <h3 class="text-lg font-semibold text-white">Stage II – Performance Monitoring (Read-only)</h3>
                    <p class="text-[11px] text-slate-500 mt-1">
                        Derived from submitted ORS with monitoring ratings (Quality & Timeliness). No validation. No performance evaluation.
                    </p>
                </div>
                <button type="button" id="qar-modal-close" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-4 text-sm text-slate-200">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Office / Unit</p>
                        <p id="qar-office" class="mt-1 font-semibold">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Quarter Covered</p>
                        <p id="qar-period" class="mt-1 font-semibold">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Performance Period</p>
                        <p class="mt-1 font-semibold">{{ $performancePeriodLabel }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Source</p>
                        <p id="qar-source" class="mt-1 font-semibold">--</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-5 text-sm text-slate-200">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Submitted + Rated ORS Entries</p>
                        <p class="mt-1 font-semibold">{{ $totalEntries }}</p>
                        <p class="text-[11px] text-slate-500 mt-1">Submitted (Locked) entries only</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Core Outputs Logged</p>
                        <p class="mt-1 font-semibold">
                            {{ collect($orsSubmittedRated)->where('function', 'core')->count() }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Support Outputs Logged</p>
                        <p class="mt-1 font-semibold">
                            {{ collect($orsSubmittedRated)->where('function', 'support')->count() }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Months with Activity</p>
                        <p class="mt-1 font-semibold">{{ $monthsWithActivityText }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Quarter Totals (Monitoring)</p>
                        <p class="mt-1 font-semibold text-white">Qty: {{ $totalQty }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Quality Points: {{ $totalQPoints }} • Timeliness Points: {{ $totalTPoints }}
                        </p>
                    </div>
                </div>

                {{-- CORE FUNCTIONS --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-white">Core Functions (80%)</h4>
                        <p class="text-[11px] text-slate-500">Monitoring totals derived from MPOR rules (Qty × Monitoring Rating)</p>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-800">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-950/50 text-slate-200">
                                <tr>
                                    <th class="px-3 py-2 text-left" rowspan="2">Major Output</th>
                                    @foreach ($qarPeriod['months'] as $m)
                                        <th class="px-3 py-2 text-center" colspan="3">{{ $m['label'] }}</th>
                                    @endforeach
                                    <th class="px-3 py-2 text-center" colspan="3">Quarter Total</th>
                                </tr>
                                <tr class="border-t border-slate-800">
                                    @foreach ($qarPeriod['months'] as $m)
                                        <th class="px-3 py-2 text-center text-[11px] text-slate-400">Qty</th>
                                        <th class="px-3 py-2 text-center text-[11px] text-slate-400">Q Pts</th>
                                        <th class="px-3 py-2 text-center text-[11px] text-slate-400">T Pts</th>
                                    @endforeach
                                    <th class="px-3 py-2 text-center text-[11px] text-slate-400">Qty</th>
                                    <th class="px-3 py-2 text-center text-[11px] text-slate-400">Q Pts</th>
                                    <th class="px-3 py-2 text-center text-[11px] text-slate-400">T Pts</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach ($mfos as $row)
                                    @continue($row['function'] !== 'core')
                                    @php
                                        $a = $mfoAgg[$row['mfo']] ?? null;
                                    @endphp
                                    <tr class="hover:bg-slate-900/40">
                                        <td class="px-3 py-2 text-slate-200">
                                            {{ $row['mfo'] }}
                                        </td>

                                        @foreach ($qarPeriod['months'] as $m)
                                            @php
                                                $mm = $a ? ($a['months'][$m['key']] ?? ['qty'=>0,'q_points'=>0,'t_points'=>0]) : ['qty'=>0,'q_points'=>0,'t_points'=>0];
                                            @endphp
                                            <td class="px-3 py-2 text-center">{{ $mm['qty'] }}</td>
                                            <td class="px-3 py-2 text-center">{{ $mm['q_points'] }}</td>
                                            <td class="px-3 py-2 text-center">{{ $mm['t_points'] }}</td>
                                        @endforeach

                                        <td class="px-3 py-2 text-center font-semibold">{{ $a['totals']['qty'] ?? 0 }}</td>
                                        <td class="px-3 py-2 text-center font-semibold">{{ $a['totals']['q_points'] ?? 0 }}</td>
                                        <td class="px-3 py-2 text-center font-semibold">{{ $a['totals']['t_points'] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t border-slate-800 bg-slate-950/40">
                                @php
                                    $coreTotalsByMonth = [];
                                    foreach ($qarPeriod['months'] as $m) {
                                        $coreTotalsByMonth[$m['key']] = ['qty'=>0,'q_points'=>0,'t_points'=>0];
                                    }
                                    $coreGrand = ['qty'=>0,'q_points'=>0,'t_points'=>0];

                                    foreach ($mfos as $row) {
                                        if ($row['function'] !== 'core') continue;
                                        $a = $mfoAgg[$row['mfo']] ?? null;
                                        if (!$a) continue;

                                        foreach ($qarPeriod['months'] as $m) {
                                            $coreTotalsByMonth[$m['key']]['qty'] += $a['months'][$m['key']]['qty'] ?? 0;
                                            $coreTotalsByMonth[$m['key']]['q_points'] += $a['months'][$m['key']]['q_points'] ?? 0;
                                            $coreTotalsByMonth[$m['key']]['t_points'] += $a['months'][$m['key']]['t_points'] ?? 0;
                                        }

                                        $coreGrand['qty'] += $a['totals']['qty'] ?? 0;
                                        $coreGrand['q_points'] += $a['totals']['q_points'] ?? 0;
                                        $coreGrand['t_points'] += $a['totals']['t_points'] ?? 0;
                                    }
                                @endphp
                                <tr>
                                    <td class="px-3 py-2 text-left font-semibold text-slate-200">Core Totals</td>
                                    @foreach ($qarPeriod['months'] as $m)
                                        <td class="px-3 py-2 text-center font-semibold">{{ $coreTotalsByMonth[$m['key']]['qty'] }}</td>
                                        <td class="px-3 py-2 text-center font-semibold">{{ $coreTotalsByMonth[$m['key']]['q_points'] }}</td>
                                        <td class="px-3 py-2 text-center font-semibold">{{ $coreTotalsByMonth[$m['key']]['t_points'] }}</td>
                                    @endforeach
                                    <td class="px-3 py-2 text-center font-semibold text-white">{{ $coreGrand['qty'] }}</td>
                                    <td class="px-3 py-2 text-center font-semibold text-white">{{ $coreGrand['q_points'] }}</td>
                                    <td class="px-3 py-2 text-center font-semibold text-white">{{ $coreGrand['t_points'] }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <p class="text-[11px] text-slate-500">
                        Note: Quantity is employee-declared at ORS submission and is locked. Supervisor does not rate Quantity. Quality & Timeliness are monitoring ratings only.
                    </p>
                </div>

                {{-- SUPPORT FUNCTIONS --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-white">Support Functions (20%)</h4>
                        <p class="text-[11px] text-slate-500">Included for completeness (may be zero for the quarter)</p>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-800">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-950/50 text-slate-200">
                                <tr>
                                    <th class="px-3 py-2 text-left" rowspan="2">Major Output</th>
                                    @foreach ($qarPeriod['months'] as $m)
                                        <th class="px-3 py-2 text-center" colspan="3">{{ $m['label'] }}</th>
                                    @endforeach
                                    <th class="px-3 py-2 text-center" colspan="3">Quarter Total</th>
                                </tr>
                                <tr class="border-t border-slate-800">
                                    @foreach ($qarPeriod['months'] as $m)
                                        <th class="px-3 py-2 text-center text-[11px] text-slate-400">Qty</th>
                                        <th class="px-3 py-2 text-center text-[11px] text-slate-400">Q Pts</th>
                                        <th class="px-3 py-2 text-center text-[11px] text-slate-400">T Pts</th>
                                    @endforeach
                                    <th class="px-3 py-2 text-center text-[11px] text-slate-400">Qty</th>
                                    <th class="px-3 py-2 text-center text-[11px] text-slate-400">Q Pts</th>
                                    <th class="px-3 py-2 text-center text-[11px] text-slate-400">T Pts</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach ($mfos as $row)
                                    @continue($row['function'] !== 'support')
                                    @php
                                        $a = $mfoAgg[$row['mfo']] ?? null;
                                    @endphp
                                    <tr class="hover:bg-slate-900/40">
                                        <td class="px-3 py-2 text-slate-200">
                                            {{ $row['mfo'] }}
                                        </td>

                                        @foreach ($qarPeriod['months'] as $m)
                                            @php
                                                $mm = $a ? ($a['months'][$m['key']] ?? ['qty'=>0,'q_points'=>0,'t_points'=>0]) : ['qty'=>0,'q_points'=>0,'t_points'=>0];
                                            @endphp
                                            <td class="px-3 py-2 text-center">{{ $mm['qty'] }}</td>
                                            <td class="px-3 py-2 text-center">{{ $mm['q_points'] }}</td>
                                            <td class="px-3 py-2 text-center">{{ $mm['t_points'] }}</td>
                                        @endforeach

                                        <td class="px-3 py-2 text-center font-semibold">{{ $a['totals']['qty'] ?? 0 }}</td>
                                        <td class="px-3 py-2 text-center font-semibold">{{ $a['totals']['q_points'] ?? 0 }}</td>
                                        <td class="px-3 py-2 text-center font-semibold">{{ $a['totals']['t_points'] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SOURCE SNAPSHOT (read-only) --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-white">Source Snapshot: Submitted + Rated ORS (Read-only)</h4>
                        <p class="text-[11px] text-slate-500">For traceability only (not validation, not approval)</p>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-800">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-950/50 text-slate-200">
                                <tr>
                                    <th class="px-3 py-2 text-left">Date Submitted</th>
                                    <th class="px-3 py-2 text-left">Employee</th>
                                    <th class="px-3 py-2 text-left">Major Output (MFO)</th>
                                    <th class="px-3 py-2 text-left">Success Indicator</th>
                                    <th class="px-3 py-2 text-left">ORS Ref</th>
                                    <th class="px-3 py-2 text-left">Quantity (Locked)</th>
                                    <th class="px-3 py-2 text-center">Quality</th>
                                    <th class="px-3 py-2 text-center">Timeliness</th>
                                    <th class="px-3 py-2 text-left">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach ($orsSubmittedRated as $e)
                                    <tr class="hover:bg-slate-900/40">
                                        <td class="px-3 py-2">{{ $e['date_submitted'] }}</td>
                                        <td class="px-3 py-2 text-slate-200">{{ $e['employee'] }}</td>
                                        <td class="px-3 py-2">{{ $e['mfo'] }}</td>
                                        <td class="px-3 py-2 text-slate-300">{{ $e['indicator'] }}</td>
                                        <td class="px-3 py-2">{{ $e['request_id'] }}</td>
                                        <td class="px-3 py-2">
                                            <span class="rounded-md border border-slate-700 bg-slate-950/40 px-2 py-1 text-[11px] text-slate-200">
                                                {{ $e['quantity_label'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-center font-semibold">{{ $e['quality_rating'] }}</td>
                                        <td class="px-3 py-2 text-center font-semibold">{{ $e['timeliness_rating'] }}</td>
                                        <td class="px-3 py-2 text-slate-300">{{ $e['remarks'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="text-xs text-slate-400">
                        This report is a monitoring-only consolidation for Stage II. Validation, SMPOR/IPCR accomplishment review, and performance rating occur in Stage III.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <a href="#"
                   class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                    Export QAR (Monitoring Copy)
                </a>

                <button type="button"
                        id="qar-modal-close-bottom"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('qar-view-modal');
                const closeButtons = [document.getElementById('qar-modal-close'), document.getElementById('qar-modal-close-bottom')];
                const periodEl = document.getElementById('qar-period');
                const officeEl = document.getElementById('qar-office');
                const sourceEl = document.getElementById('qar-source');

                function toggleModal(show) {
                    if (!modal) return;
                    if (show) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        document.body.classList.add('overflow-hidden');
                    } else {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.body.classList.remove('overflow-hidden');
                    }
                }

                document.querySelectorAll('[data-qar-view]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        if (periodEl) periodEl.textContent = btn.dataset.period || '--';
                        if (officeEl) officeEl.textContent = btn.dataset.office || '--';
                        if (sourceEl) sourceEl.textContent = btn.dataset.source || '--';
                        toggleModal(true);
                    });
                });

                closeButtons.forEach((btn) => {
                    if (!btn) return;
                    btn.addEventListener('click', () => toggleModal(false));
                });

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        toggleModal(false);
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        toggleModal(false);
                    }
                });
            });
        </script>
    @endpush
@endsection
