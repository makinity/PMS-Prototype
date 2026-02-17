@extends('layouts.employee')

@section('main-content')
    @php
        $mporMonthYear = 'January 2026';
        $employeeName = 'Ramon Reyes';
        $officeName = 'Revenue Collection Unit';

        $mporStatusKey = 'mpor_status_' . (auth()->id() ?? 'guest') . '_' . \Illuminate\Support\Str::slug($mporMonthYear, '_');
        $mporStatus = (string) data_get(session($mporStatusKey, []), 'status', 'draft');
        $isMporLocked = in_array($mporStatus, ['submitted', 'endorsed'], true);

        $orsTasks = [
            [
                'id' => 'task-jan-04',
                'date' => '2026-01-04',
                'uwpOutputId' => 'ebank_scanning',
                'uwpOutputLabel' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'quantityValue' => 1,
                'quantityLabel' => '1 daily batch',
                'state' => 'submitted',
                'supervisorQuality' => 5,
                'supervisorTimeliness' => 5,
            ],
            [
                'id' => 'task-jan-02',
                'date' => '2026-01-02',
                'uwpOutputId' => 'otc_processing',
                'uwpOutputLabel' => 'Processing of Over-the-Counter Revenue Transactions',
                'quantityValue' => 12,
                'quantityLabel' => '12 transactions',
                'state' => 'submitted',
                'supervisorQuality' => 5,
                'supervisorTimeliness' => 5,
            ],
            [
                'id' => 'task-jan-05',
                'date' => '2026-01-05',
                'uwpOutputId' => 'otc_processing',
                'uwpOutputLabel' => 'Processing of Over-the-Counter Revenue Transactions',
                'quantityValue' => 6,
                'quantityLabel' => '6 receipts validated',
                'state' => 'recording',
                'supervisorQuality' => null,
                'supervisorTimeliness' => null,
            ],
            [
                'id' => 'task-jan-06',
                'date' => '2026-01-06',
                'uwpOutputId' => 'records_maintenance',
                'uwpOutputLabel' => 'Maintenance of Revenue Records Filing System',
                'quantityValue' => 0,
                'quantityLabel' => '--',
                'state' => 'missing',
                'supervisorQuality' => null,
                'supervisorTimeliness' => null,
            ],
            [
                'id' => 'task-jan-08',
                'date' => '2026-01-08',
                'uwpOutputId' => 'records_maintenance',
                'uwpOutputLabel' => 'Maintenance of Revenue Records Filing System',
                'quantityValue' => 3,
                'quantityLabel' => '3 retrieval logs',
                'state' => 'submitted',
                'supervisorQuality' => null,
                'supervisorTimeliness' => null,
            ],
        ];

        $mfoCatalog = [
            'core' => [
                ['id' => 'ebank_scanning', 'label' => 'E-Bank Scanning and Encoding of Revenue Transactions'],
                ['id' => 'otc_processing', 'label' => 'Processing of Over-the-Counter Revenue Transactions'],
            ],
            'support' => [
                ['id' => 'records_maintenance', 'label' => 'Maintenance of Revenue Records Filing System'],
            ],
        ];

        $mporRows = [];
        foreach ($mfoCatalog as $sectionKey => $items) {
            foreach ($items as $item) {
                $mporRows[$item['id']] = [
                    'id' => $item['id'],
                    'label' => $item['label'],
                    'section' => $sectionKey,
                    'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                    'qtyTotal' => 0,
                    'qualTotal' => 0,
                    'timeTotal' => 0,
                ];
            }
        }

        $includedRatedTasks = [];

        foreach ($orsTasks as $task) {
            $isIncluded = ($task['state'] ?? null) === 'submitted'
                && is_numeric($task['supervisorQuality'] ?? null)
                && is_numeric($task['supervisorTimeliness'] ?? null);

            if (! $isIncluded) {
                continue;
            }

            $rowId = $task['uwpOutputId'] ?? null;
            if (! $rowId || ! isset($mporRows[$rowId])) {
                continue;
            }

            $day = (int) \Carbon\Carbon::parse($task['date'])->format('j');
            $week = $day <= 7 ? 1 : ($day <= 14 ? 2 : ($day <= 21 ? 3 : 4));

            $qty = (float) ($task['quantityValue'] ?? 0);
            $qualRating = (float) ($task['supervisorQuality'] ?? 0);
            $timeRating = (float) ($task['supervisorTimeliness'] ?? 0);

            $mporRows[$rowId]['qty'][$week] += $qty;
            $mporRows[$rowId]['qual'][$week] += ($qty * $qualRating);
            $mporRows[$rowId]['time'][$week] += ($qty * $timeRating);

            $includedRatedTasks[] = $task;
        }

        foreach ($mporRows as $rowId => $row) {
            $mporRows[$rowId]['qtyTotal'] = array_sum($row['qty']);
            $mporRows[$rowId]['qualTotal'] = array_sum($row['qual']);
            $mporRows[$rowId]['timeTotal'] = array_sum($row['time']);
        }

        $sectionRows = [
            'core' => array_values(array_filter($mporRows, fn ($row) => $row['section'] === 'core')),
            'support' => array_values(array_filter($mporRows, fn ($row) => $row['section'] === 'support')),
        ];

        $grandTotals = [
            'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'qtyTotal' => 0,
            'qualTotal' => 0,
            'timeTotal' => 0,
        ];

        foreach ($mporRows as $row) {
            foreach ([1, 2, 3, 4] as $week) {
                $grandTotals['qty'][$week] += $row['qty'][$week];
                $grandTotals['qual'][$week] += $row['qual'][$week];
                $grandTotals['time'][$week] += $row['time'][$week];
            }
        }

        $grandTotals['qtyTotal'] = array_sum($grandTotals['qty']);
        $grandTotals['qualTotal'] = array_sum($grandTotals['qual']);
        $grandTotals['timeTotal'] = array_sum($grandTotals['time']);

        $sectionLabels = [
            'core' => 'Core Functions (80%)',
            'support' => 'Support Functions (20%)',
        ];
    @endphp

    <section class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="rounded-xl border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-200">
                {{ session('info') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Monthly Performance Output Report</p>
                <h1 class="mt-1 text-2xl font-bold text-white md:text-3xl">MONTHLY PERFORMANCE OUTPUT REPORT</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Read-only mirror of locked ORS entries with supervisor ratings.
                </p>

                <div class="mt-4 grid gap-3 text-xs uppercase tracking-[0.3em] text-white sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-3">
                        <p class="text-slate-400">NAME</p>
                        <p class="mt-1 font-semibold normal-case tracking-normal">{{ $employeeName }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-3">
                        <p class="text-slate-400">OFFICE / DIVISION</p>
                        <p class="mt-1 font-semibold normal-case tracking-normal">{{ $officeName }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-3">
                        <p class="text-slate-400">MONTH</p>
                        <p class="mt-1 font-semibold normal-case tracking-normal">{{ $mporMonthYear }}</p>
                    </div>
                </div>
            </div>

            <div class="flex w-full flex-row gap-2 md:w-auto md:items-center">
                @if (! $isMporLocked)
                    <button type="button" data-modal-target="mporSubmitConfirmModal" data-modal-toggle="mporSubmitConfirmModal"
                        class="flex-1 rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-center text-xs font-semibold text-white transition hover:bg-slate-700 md:flex-none">
                        Submit MPOR
                    </button>
                @else
                    <button type="button" disabled
                        class="flex-1 cursor-not-allowed rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-center text-xs font-semibold text-emerald-200 opacity-80 md:flex-none">
                        Submitted
                    </button>
                @endif
                <a href="{{ route('employee.mpor.export.excel') }}"
                    class="flex-1 rounded-lg border border-slate-700 px-4 py-2 text-center text-xs text-slate-300 hover:bg-slate-800 md:flex-none">
                    Export PDF
                </a>
            </div>
        </div>

        <div class="space-y-4 md:hidden">
            @foreach ($sectionLabels as $sectionKey => $sectionLabel)
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-400">{{ $sectionLabel }}</p>

                    @foreach ($sectionRows[$sectionKey] as $row)
                        <div class="mt-3 rounded-xl border border-slate-800 bg-slate-900/50 p-3">
                            <p class="text-sm font-semibold text-white">{{ $row['label'] }}</p>
                            <div class="mt-3 grid gap-3">
                                @foreach (['qty' => 'Efficiency / Quantity', 'qual' => 'Quality / Effectiveness', 'time' => 'Timeliness'] as $metricKey => $metricLabel)
                                    <div class="rounded-lg border border-slate-800 bg-slate-900/40 p-3">
                                        <p class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">{{ $metricLabel }}</p>
                                        <div class="mt-2 grid grid-cols-5 gap-2 text-right">
                                            @for ($week = 1; $week <= 4; $week++)
                                                <div>
                                                    <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">W{{ $week }}</p>
                                                    <p class="text-sm font-semibold text-white">{{ number_format($row[$metricKey][$week], 0) }}</p>
                                                </div>
                                            @endfor
                                            <div>
                                                <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p>
                                                <p class="text-sm font-semibold text-white">{{ number_format($row[$metricKey . 'Total'], 0) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="hidden md:block">
            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-[0.75rem] text-slate-200">
                        <thead>
                            <tr class="text-left text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                                <th class="whitespace-nowrap px-3 py-3 align-bottom" rowspan="2">Output / Task</th>
                                <th class="border-l border-slate-800 px-3 py-3 text-center" colspan="5">Efficiency / Quantity</th>
                                <th class="border-l border-slate-800 px-3 py-3 text-center" colspan="5">Quality / Effectiveness</th>
                                <th class="border-l border-slate-800 px-3 py-3 text-center" colspan="5">Timeliness</th>
                            </tr>
                            <tr class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">
                                <th class="border-l border-slate-800 px-2 py-2 text-right">W1</th>
                                <th class="px-2 py-2 text-right">W2</th>
                                <th class="px-2 py-2 text-right">W3</th>
                                <th class="px-2 py-2 text-right">W4</th>
                                <th class="px-2 py-2 text-right font-semibold">Total</th>
                                <th class="border-l border-slate-800 px-2 py-2 text-right">W1</th>
                                <th class="px-2 py-2 text-right">W2</th>
                                <th class="px-2 py-2 text-right">W3</th>
                                <th class="px-2 py-2 text-right">W4</th>
                                <th class="px-2 py-2 text-right font-semibold">Total</th>
                                <th class="border-l border-slate-800 px-2 py-2 text-right">W1</th>
                                <th class="px-2 py-2 text-right">W2</th>
                                <th class="px-2 py-2 text-right">W3</th>
                                <th class="px-2 py-2 text-right">W4</th>
                                <th class="px-2 py-2 text-right font-semibold">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-[0.75rem]">
                            <tr class="bg-slate-800/40 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                <td class="px-3 py-2 font-semibold text-slate-200" colspan="16">Core Functions (80%)</td>
                            </tr>
                            @foreach ($sectionRows['core'] as $row)
                                <tr>
                                    <td class="px-3 py-3 font-semibold text-white">{{ $row['label'] }}</td>
                                    <td class="border-l border-slate-800 px-2 py-3 text-right tabular-nums">{{ number_format($row['qty'][1], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qty'][2], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qty'][3], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qty'][4], 0) }}</td>
                                    <td class="px-2 py-3 text-right font-semibold tabular-nums">{{ number_format($row['qtyTotal'], 0) }}</td>
                                    <td class="border-l border-slate-800 px-2 py-3 text-right tabular-nums">{{ number_format($row['qual'][1], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qual'][2], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qual'][3], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qual'][4], 0) }}</td>
                                    <td class="px-2 py-3 text-right font-semibold tabular-nums">{{ number_format($row['qualTotal'], 0) }}</td>
                                    <td class="border-l border-slate-800 px-2 py-3 text-right tabular-nums">{{ number_format($row['time'][1], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['time'][2], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['time'][3], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['time'][4], 0) }}</td>
                                    <td class="px-2 py-3 text-right font-semibold tabular-nums">{{ number_format($row['timeTotal'], 0) }}</td>
                                </tr>
                            @endforeach

                            <tr class="bg-slate-800/40 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                <td class="px-3 py-2 font-semibold text-slate-200" colspan="16">Support Functions (20%)</td>
                            </tr>
                            @foreach ($sectionRows['support'] as $row)
                                <tr>
                                    <td class="px-3 py-3 font-semibold text-white">{{ $row['label'] }}</td>
                                    <td class="border-l border-slate-800 px-2 py-3 text-right tabular-nums">{{ number_format($row['qty'][1], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qty'][2], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qty'][3], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qty'][4], 0) }}</td>
                                    <td class="px-2 py-3 text-right font-semibold tabular-nums">{{ number_format($row['qtyTotal'], 0) }}</td>
                                    <td class="border-l border-slate-800 px-2 py-3 text-right tabular-nums">{{ number_format($row['qual'][1], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qual'][2], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qual'][3], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['qual'][4], 0) }}</td>
                                    <td class="px-2 py-3 text-right font-semibold tabular-nums">{{ number_format($row['qualTotal'], 0) }}</td>
                                    <td class="border-l border-slate-800 px-2 py-3 text-right tabular-nums">{{ number_format($row['time'][1], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['time'][2], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['time'][3], 0) }}</td>
                                    <td class="px-2 py-3 text-right tabular-nums">{{ number_format($row['time'][4], 0) }}</td>
                                    <td class="px-2 py-3 text-right font-semibold tabular-nums">{{ number_format($row['timeTotal'], 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-400">
            Stage II demo: MPOR points = Quantity &times; Supervisor Rating (Q/T). Only submitted ORS entries with supervisor ratings are included.
        </p>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-xs uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-[0.6rem] tracking-[0.3em] text-slate-500">
                    <span>Week 1</span><span>Week 2</span><span>Week 3</span><span>Week 4</span><span>Total</span>
                </div>
                <div class="mt-2 grid grid-cols-5 text-center text-sm font-semibold text-white">
                    <span>{{ number_format($grandTotals['qty'][1], 0) }}</span>
                    <span>{{ number_format($grandTotals['qty'][2], 0) }}</span>
                    <span>{{ number_format($grandTotals['qty'][3], 0) }}</span>
                    <span>{{ number_format($grandTotals['qty'][4], 0) }}</span>
                    <span>{{ number_format($grandTotals['qtyTotal'], 0) }}</span>
                </div>
                <div class="my-5 border-t border-slate-700/70"></div>
                <div class="mt-3 space-y-2 text-[0.65rem] tracking-[0.2em] text-slate-500">
                    <div class="flex items-center justify-between gap-3">
                        <span class="min-w-0">Included ORS entries (rated)</span>
                        <span class="shrink-0 font-semibold text-white">{{ count($includedRatedTasks) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="min-w-0">Excluded entries (unrated/draft/missing)</span>
                        <span class="shrink-0 font-semibold text-white">{{ count($orsTasks) - count($includedRatedTasks) }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-sm font-semibold text-white">
                    <span>Confirmed:</span>
                    <span class="text-slate-500">Stage II</span>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Supervisor</p>
                        <p class="text-sm font-semibold text-white normal-case tracking-normal">Carlo D. Beray</p>
                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                    </div>
                    <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Employee</p>
                        <p class="text-sm font-semibold text-white normal-case tracking-normal">{{ $employeeName }}</p>
                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="mporSubmitConfirmModal" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                    <div class="flex items-start justify-between border-b border-slate-800 p-5">
                        <h3 class="text-lg font-semibold text-white">Submit MPOR</h3>
                        <button type="button" data-modal-hide="mporSubmitConfirmModal"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                            <span class="sr-only">Close modal</span>
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <div class="space-y-3 p-5 text-sm text-slate-300">
                        <p>You are about to submit your Monthly Performance Output Report (MPOR) for {{ $mporMonthYear }}.</p>
                        <p>Once submitted, this report will be locked and forwarded for supervisor/department review.</p>
                        <p class="font-medium text-white">Proceed?</p>
                    </div>

                    <form id="mporSubmitForm" method="POST" action="{{ route('employee.mpor.submit') }}"
                        class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                        @csrf
                        <input type="hidden" name="month_year" value="{{ $mporMonthYear }}">

                        <button type="button" data-modal-hide="mporSubmitConfirmModal"
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                            Cancel
                        </button>

                        <button type="submit" id="mporProceedSubmissionBtn"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                            <span data-button-spinner
                                class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                            <span data-button-label>Proceed Submission</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submitForm = document.getElementById('mporSubmitForm');
            const proceedButton = document.getElementById('mporProceedSubmissionBtn');

            if (!submitForm || !proceedButton) {
                return;
            }

            submitForm.addEventListener('submit', function() {
                const label = proceedButton.querySelector('[data-button-label]');
                const spinner = proceedButton.querySelector('[data-button-spinner]');

                proceedButton.disabled = true;
                proceedButton.classList.add('cursor-not-allowed', 'opacity-80');

                if (label) {
                    label.textContent = 'Submitting...';
                }

                if (spinner) {
                    spinner.classList.remove('hidden');
                }
            });
        });
    </script>
@endpush
