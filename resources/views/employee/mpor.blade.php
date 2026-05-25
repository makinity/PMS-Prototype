@extends('layouts.employee')

@section('main-content')
    @php
        /**
         * All MPOR data must come from controller.
         * Keep this small guard only (no hardcoded defaults, no computations).
         */
        $mporMonthYear = $mporMonthYear ?? now()->format('F Y');
        $employeeName = $employeeName ?? (auth()->user()->name ?? '—');
        $officeName = $officeName ?? (optional(auth()->user()->office)->name ?? '—');

        $mporStatus = $mporStatus ?? 'draft';
        $isMporLocked = $isMporLocked ?? in_array($mporStatus, ['submitted', 'endorsed'], true);

        $sectionRows = $sectionRows ?? ['core' => [], 'support' => []];
        $grandTotals = $grandTotals ?? [
            'qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'qual' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'time' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'qtyTotal' => 0,
            'qualTotal' => 0,
            'timeTotal' => 0,
        ];

        $sectionLabels = $sectionLabels ?? [
            'core' => 'Core Functions (80%)',
            'support' => 'Support Functions (20%)',
        ];

        $orsTasks = $orsTasks ?? [];
        $includedRatedTasks = $includedRatedTasks ?? [];
        $mporEmptyReason = trim((string) ($mporEmptyReason ?? ''));
        $isReturned = strtolower((string) $mporStatus) === 'returned';
        $returnRemarks = trim((string) ($mpor?->return_remarks ?? ''));
    @endphp

    <section class="space-y-6">
        @if ($mporEmptyReason !== '')
            <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                {{ $mporEmptyReason }}
            </div>
        @endif

        @if ($isReturned)
            <div id="mporReturnedBanner" class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
                <p class="font-semibold">MPOR returned by Supervisor</p>
                <p class="mt-1 text-rose-100/90">
                    You may continue logging ORS tasks for {{ $mporMonthYear }} and then resubmit the MPOR.
                </p>
                @if ($returnRemarks !== '')
                    <p class="mt-2 text-rose-100/90">
                        Remarks: {{ $returnRemarks }}
                    </p>
                @endif
            </div>
        @endif

        <div class="flex flex-col gap-3 md:gap-4 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                <h1 class="mt-1 text-xl font-bold leading-tight text-white sm:text-2xl md:text-3xl">MONTHLY PERFORMANCE OUTPUT REPORT</h1>

                <div class="mt-3 grid grid-cols-2 gap-2 text-xs uppercase tracking-[0.2em] text-white sm:grid-cols-3">
                    <div class="rounded-lg border border-gray-700 bg-slate-900/40 px-3 py-2">
                        <p class="text-[0.6rem] text-slate-500">NAME</p>
                        <p class="mt-0.5 truncate text-sm font-semibold normal-case tracking-normal">{{ $employeeName }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-700 bg-slate-900/40 px-3 py-2">
                        <p class="text-[0.6rem] text-slate-500">OFFICE / DIVISION</p>
                        <p class="mt-0.5 truncate text-sm font-semibold normal-case tracking-normal">{{ $officeName }}</p>
                    </div>
                    <div class="col-span-2 rounded-lg border border-gray-700 bg-slate-900/40 px-3 py-2 sm:col-span-1">
                        <p class="text-[0.6rem] text-slate-500">MONTH</p>
                        <p class="mt-0.5 truncate text-sm font-semibold normal-case tracking-normal">{{ $mporMonthYear }}</p>
                    </div>
                </div>
            </div>

            <div id="mporActionButtons" class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:flex-row md:items-center">
                @if (! $isMporLocked)
                    <button type="button" data-modal-target="mporSubmitConfirmModal" data-modal-toggle="mporSubmitConfirmModal"
                        class="group inline-flex items-center justify-center gap-2 rounded-lg border border-blue-500/50 bg-gradient-to-b from-blue-500 to-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-[0_8px_20px_-12px_rgba(59,130,246,0.9)] transition hover:from-blue-400 hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/60 focus:ring-offset-2 focus:ring-offset-slate-950 sm:min-w-[10.5rem] sm:flex-none">
                        <i class="fa-solid fa-paper-plane text-xs text-blue-100 transition group-hover:translate-x-0.5"></i>
                        {{ $isReturned ? 'Resubmit MPOR' : 'Submit MPOR' }}
                    </button>
                @else
                    <button type="button" disabled
                        class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-center text-sm font-semibold text-emerald-200 opacity-80 sm:min-w-[10.5rem] sm:flex-none">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        Submitted
                    </button>
                @endif

                <a href="{{ route('employee.mpor.export.excel') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-2 text-center text-sm font-medium text-slate-200 transition hover:border-slate-500 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400/40 focus:ring-offset-2 focus:ring-offset-slate-950 sm:min-w-[8.75rem] sm:flex-none">
                    <i class="fa-solid fa-file-arrow-down text-xs text-slate-300"></i>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- Mobile: Hybrid tabs + output drawer --}}
        <div class="space-y-4 lg:hidden" id="mporMobileWorkspace">
            <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-400">Output Metrics</p>
                </div>

                <div class="mt-3 grid grid-cols-3 gap-2" role="tablist" aria-label="MPOR metrics">
                    @php
                        $mobileMetricTabs = [
                            'qty' => 'Quantity',
                            'qual' => 'Quality',
                            'time' => 'Timeliness',
                        ];
                    @endphp
                    @foreach ($mobileMetricTabs as $metricKey => $metricLabel)
                        <button type="button" data-mpor-metric-tab="{{ $metricKey }}" role="tab"
                            aria-selected="{{ $metricKey === 'qty' ? 'true' : 'false' }}"
                            class="{{ $metricKey === 'qty' ? 'bg-blue-600 text-white border-blue-500' : 'border-slate-700 text-slate-300' }} rounded-lg border px-2 py-2 text-xs font-semibold transition">
                            {{ $metricLabel }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-3">
                    <p class="mb-1 block text-[0.6rem] font-semibold uppercase tracking-[0.25em] text-slate-500">Function</p>
                    <div class="relative" id="mporSectionDropdown" data-mpor-section-dropdown>
                        <button type="button" id="mporSectionDropdownButton" data-mpor-section-trigger aria-haspopup="listbox" aria-expanded="false"
                            class="flex w-full items-center justify-between rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-200 transition hover:border-slate-600 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span data-mpor-section-label>All Functions</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                        </button>
                        <div id="mporSectionDropdownList" data-mpor-section-list
                            class="absolute z-30 mt-1 hidden w-full overflow-hidden rounded-lg border border-slate-700 bg-slate-900 shadow-xl">
                            <button type="button" data-mpor-section-option="all" role="option" aria-selected="true"
                                class="block w-full px-3 py-2 text-left text-sm text-slate-100 transition hover:bg-slate-800">All Functions</button>
                            <button type="button" data-mpor-section-option="core" role="option" aria-selected="false"
                                class="block w-full px-3 py-2 text-left text-sm text-slate-100 transition hover:bg-slate-800">Core Functions (80%)</button>
                            <button type="button" data-mpor-section-option="support" role="option" aria-selected="false"
                                class="block w-full px-3 py-2 text-left text-sm text-slate-100 transition hover:bg-slate-800">Support Functions (20%)</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3" id="mporMobileOutputList">
                @forelse ($sectionRows as $sectionKey => $rows)
                    @foreach ($rows as $row)
                        @php
                            $rowLabel = (string) data_get($row, 'label', 'Untitled output');
                            $rowId = $sectionKey . '-' . md5($rowLabel . '|' . $loop->index);
                        @endphp
                        <article data-mpor-mobile-row data-section="{{ $sectionKey }}"
                            class="overflow-hidden rounded-2xl border border-gray-700 bg-slate-900/40">
                            <button type="button"
                                class="flex w-full items-start justify-between gap-3 px-4 py-3 text-left"
                                data-mpor-row-toggle="{{ $rowId }}" aria-expanded="false" aria-controls="mpor-row-panel-{{ $rowId }}">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white" title="{{ $rowLabel }}">{{ $rowLabel }}</p>
                                    <span class="mt-1 inline-flex rounded-full border border-slate-700 px-2 py-0.5 text-[0.6rem] uppercase tracking-[0.2em] text-slate-400">
                                        {{ $sectionKey === 'core' ? 'Core' : 'Support' }}
                                    </span>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Total</p>
                                    <p data-mpor-row-total
                                        data-qty="{{ number_format((float) data_get($row, 'qtyTotal', 0), 0, '.', '') }}"
                                        data-qual="{{ number_format((float) data_get($row, 'qualTotal', 0), 0, '.', '') }}"
                                        data-time="{{ number_format((float) data_get($row, 'timeTotal', 0), 0, '.', '') }}"
                                        class="mt-1 text-base font-bold text-cyan-200 tabular-nums">
                                        {{ number_format(data_get($row, 'qtyTotal', 0), 0) }}
                                    </p>
                                </div>
                            </button>

                            <div id="mpor-row-panel-{{ $rowId }}" class="hidden border-t border-gray-700 px-4 pb-4 pt-3"
                                data-mpor-row-panel>
                                <p class="mb-2 text-[0.6rem] uppercase tracking-[0.25em] text-slate-500">Weekly breakdown</p>
                                <div class="grid grid-cols-5 gap-2">
                                    @for ($week = 1; $week <= 4; $week++)
                                        <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-2 text-center">
                                            <p class="text-[0.55rem] uppercase tracking-[0.25em] text-slate-500">W{{ $week }}</p>
                                            <p data-mpor-week-value data-qty="{{ number_format((float) data_get($row, "qty.{$week}", 0), 0, '.', '') }}"
                                                data-qual="{{ number_format((float) data_get($row, "qual.{$week}", 0), 0, '.', '') }}"
                                                data-time="{{ number_format((float) data_get($row, "time.{$week}", 0), 0, '.', '') }}"
                                                class="mt-1 text-sm font-semibold text-white tabular-nums">
                                                {{ number_format(data_get($row, "qty.{$week}", 0), 0) }}
                                            </p>
                                        </div>
                                    @endfor
                                    <div class="rounded-lg border border-cyan-500/30 bg-cyan-500/10 p-2 text-center">
                                        <p class="text-[0.55rem] uppercase tracking-[0.25em] text-cyan-200">Total</p>
                                        <p data-mpor-total-value data-qty="{{ number_format((float) data_get($row, 'qtyTotal', 0), 0, '.', '') }}"
                                            data-qual="{{ number_format((float) data_get($row, 'qualTotal', 0), 0, '.', '') }}"
                                            data-time="{{ number_format((float) data_get($row, 'timeTotal', 0), 0, '.', '') }}"
                                            class="mt-1 text-sm font-bold text-cyan-100 tabular-nums">
                                            {{ number_format(data_get($row, 'qtyTotal', 0), 0) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @empty
                    <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4 text-sm text-slate-500">
                        No entries available.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Desktop table --}}
        <div class="hidden lg:block">
            <div class="overflow-hidden rounded-2xl border border-gray-700 bg-slate-900/40">
                <div class="overflow-x-auto max-h-[38rem]">
                    <table class="min-w-full text-[0.75rem] text-slate-200">
                        <thead class="sticky top-0 z-20 bg-slate-900/95 backdrop-blur-sm">
                            <tr class="text-left text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                                <th class="sticky left-0 z-30 whitespace-nowrap px-3 py-3 align-bottom bg-slate-900/95" rowspan="2">Output / Task</th>
                                <th class="border-l border-gray-700 px-3 py-3 text-center" colspan="5">Efficiency / Quantity</th>
                                <th class="border-l border-gray-700 px-3 py-3 text-center" colspan="5">Quality / Effectiveness</th>
                                <th class="border-l border-gray-700 px-3 py-3 text-center" colspan="5">Timeliness</th>
                            </tr>
                            <tr class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">
                                @for ($i = 0; $i < 3; $i++)
                                    <th class="{{ $i === 0 ? 'border-l border-gray-700' : '' }} px-2 py-2 text-right">W1</th>
                                    <th class="px-2 py-2 text-right">W2</th>
                                    <th class="px-2 py-2 text-right">W3</th>
                                    <th class="px-2 py-2 text-right">W4</th>
                                    <th class="px-2 py-2 text-right font-semibold">Total</th>
                                @endfor
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-800 text-[0.75rem]">
                            @foreach ($sectionLabels as $sectionKey => $sectionLabel)
                                <tr class="bg-slate-800/40 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                    <td class="sticky left-0 z-10 border-r border-gray-700 bg-slate-800/80 px-3 py-2 font-semibold text-slate-100" colspan="16">
                                        {{ $sectionLabel }}
                                    </td>
                                </tr>

                                @forelse ($sectionRows[$sectionKey] ?? [] as $row)
                                    <tr>
                                        <td class="sticky left-0 z-10 max-w-[20rem] border-r border-gray-700 bg-slate-900/95 px-3 py-3 font-semibold text-white">
                                            <span class="block truncate" title="{{ $row['label'] }}">{{ $row['label'] }}</span>
                                        </td>

                                        {{-- qty --}}
                                        <td class="border-l border-gray-700 px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'qty.1', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'qty.2', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'qty.3', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'qty.4', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right font-semibold tabular-nums">{{ number_format(data_get($row, 'qtyTotal', 0), 0) }}</td>

                                        {{-- qual --}}
                                        <td class="border-l border-gray-700 px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'qual.1', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'qual.2', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'qual.3', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'qual.4', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right font-semibold tabular-nums">{{ number_format(data_get($row, 'qualTotal', 0), 0) }}</td>

                                        {{-- time --}}
                                        <td class="border-l border-gray-700 px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'time.1', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'time.2', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'time.3', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, 'time.4', 0), 0) }}</td>
                                        <td class="px-2 py-3 text-right font-semibold tabular-nums text-cyan-100">{{ number_format(data_get($row, 'timeTotal', 0), 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="16" class="px-3 py-6 text-center text-sm text-slate-500">No entries available.</td>
                                    </tr>
                                @endforelse
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4 text-xs uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-[0.6rem] tracking-[0.3em] text-slate-500">
                    <span>Week 1</span><span>Week 2</span><span>Week 3</span><span>Week 4</span><span>Total</span>
                </div>
                <div class="mt-2 grid grid-cols-5 text-center text-sm font-semibold text-white">
                    <span>{{ number_format(data_get($grandTotals, 'qty.1', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qty.2', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qty.3', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qty.4', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qtyTotal', 0), 0) }}</span>
                </div>
                <div class="my-5 border-t border-slate-700/70"></div>
                <div class="mt-3 space-y-2 text-[0.65rem] tracking-[0.2em] text-slate-500">
                    <div class="flex items-center justify-between gap-3">
                        <span class="min-w-0">Included ORS entries (rated)</span>
                        <span class="shrink-0 font-semibold text-white">{{ count($includedRatedTasks) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="min-w-0">Excluded entries (unrated/draft/missing)</span>
                        <span class="shrink-0 font-semibold text-white">{{ max(count($orsTasks) - count($includedRatedTasks), 0) }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-sm font-semibold text-white">
                    <span>Confirmed:</span>
                    <span class="text-slate-500">Stage II</span>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1 rounded-xl border border-gray-700 bg-slate-900/40 p-3 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Supervisor</p>
                        <p class="text-sm font-semibold text-white normal-case tracking-normal">Carlo D. Beray</p>
                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                    </div>
                    <div class="space-y-1 rounded-xl border border-gray-700 bg-slate-900/40 p-3 text-center">
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
                <div class="relative rounded-2xl border border-gray-700 bg-slate-900 shadow-lg">
                    <div class="flex items-start justify-between border-b border-gray-700 p-5">
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

                    <form id="mporSubmitForm" method="POST" action="{{ route('employee.mpor.submit') }}" data-snackbar-ignore="true"
                        class="flex items-center justify-end gap-2 border-t border-gray-700 p-5">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">

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
            const mobileWorkspace = document.getElementById('mporMobileWorkspace');
            if (mobileWorkspace) {
                let activeMetric = 'qty';
                let activeSection = 'all';

                const metricTabs = Array.from(mobileWorkspace.querySelectorAll('[data-mpor-metric-tab]'));
                const sectionDropdown = mobileWorkspace.querySelector('[data-mpor-section-dropdown]');
                const sectionTrigger = mobileWorkspace.querySelector('[data-mpor-section-trigger]');
                const sectionLabel = mobileWorkspace.querySelector('[data-mpor-section-label]');
                const sectionList = mobileWorkspace.querySelector('[data-mpor-section-list]');
                const sectionOptions = Array.from(mobileWorkspace.querySelectorAll('[data-mpor-section-option]'));
                const rows = Array.from(mobileWorkspace.querySelectorAll('[data-mpor-mobile-row]'));
                const sectionLabelMap = {
                    all: 'All Functions',
                    core: 'Core Functions (80%)',
                    support: 'Support Functions (20%)',
                };

                const formatValue = (value) => Number(value || 0).toLocaleString();

                const paintMetricValues = () => {
                    rows.forEach((row) => {
                        row.querySelectorAll('[data-mpor-week-value], [data-mpor-total-value], [data-mpor-row-total]').forEach((el) => {
                            el.textContent = formatValue(el.dataset[activeMetric]);
                        });
                    });
                };

                const paintMetricTabs = () => {
                    metricTabs.forEach((tab) => {
                        const selected = tab.dataset.mporMetricTab === activeMetric;
                        tab.setAttribute('aria-selected', selected ? 'true' : 'false');
                        tab.classList.toggle('bg-blue-600', selected);
                        tab.classList.toggle('text-white', selected);
                        tab.classList.toggle('border-blue-500', selected);
                        tab.classList.toggle('border-slate-700', !selected);
                        tab.classList.toggle('text-slate-300', !selected);
                    });
                };

                const paintRowsBySection = () => {
                    rows.forEach((row) => {
                        const visible = activeSection === 'all' || row.dataset.section === activeSection;
                        row.classList.toggle('hidden', !visible);
                    });
                };

                const setRowExpanded = (row, expand) => {
                    const toggle = row.querySelector('[data-mpor-row-toggle]');
                    const panel = row.querySelector('[data-mpor-row-panel]');
                    if (!toggle || !panel) {
                        return;
                    }
                    toggle.setAttribute('aria-expanded', expand ? 'true' : 'false');
                    panel.classList.toggle('hidden', !expand);
                    row.classList.toggle('ring-1', expand);
                    row.classList.toggle('ring-cyan-500/20', expand);
                };

                const getVisibleRows = () => rows.filter((row) => !row.classList.contains('hidden'));

                const collapseAllRows = () => {
                    rows.forEach((row) => setRowExpanded(row, false));
                };

                const ensureAtLeastOneExpanded = () => {
                    const visibleRows = getVisibleRows();
                    if (!visibleRows.length) {
                        return;
                    }
                    const hasExpanded = visibleRows.some((row) => row.querySelector('[data-mpor-row-toggle]')?.getAttribute('aria-expanded') === 'true');
                    if (!hasExpanded) {
                        setRowExpanded(visibleRows[0], true);
                    }
                };

                metricTabs.forEach((tab) => {
                    tab.addEventListener('click', () => {
                        activeMetric = tab.dataset.mporMetricTab || 'qty';
                        paintMetricTabs();
                        paintMetricValues();
                    });
                });

                const setSection = (value) => {
                    activeSection = ['all', 'core', 'support'].includes(value) ? value : 'all';
                    if (sectionLabel) {
                        sectionLabel.textContent = sectionLabelMap[activeSection] || sectionLabelMap.all;
                    }
                    sectionOptions.forEach((option) => {
                        const selected = option.dataset.mporSectionOption === activeSection;
                        option.setAttribute('aria-selected', selected ? 'true' : 'false');
                        option.classList.toggle('bg-slate-800', selected);
                        option.classList.toggle('text-cyan-200', selected);
                    });
                };

                if (sectionTrigger && sectionList) {
                    sectionTrigger.addEventListener('click', () => {
                        const expanded = sectionTrigger.getAttribute('aria-expanded') === 'true';
                        sectionTrigger.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                        sectionList.classList.toggle('hidden', expanded);
                    });

                    document.addEventListener('click', (event) => {
                        if (!sectionDropdown || sectionDropdown.contains(event.target)) {
                            return;
                        }
                        sectionTrigger.setAttribute('aria-expanded', 'false');
                        sectionList.classList.add('hidden');
                    });
                }

                if (sectionOptions.length) {
                    sectionOptions.forEach((option) => {
                        option.addEventListener('click', () => {
                            setSection(option.dataset.mporSectionOption || 'all');
                            paintRowsBySection();
                            collapseAllRows();
                            ensureAtLeastOneExpanded();
                            if (sectionTrigger && sectionList) {
                                sectionTrigger.setAttribute('aria-expanded', 'false');
                                sectionList.classList.add('hidden');
                            }
                        });
                    });
                } else {
                    // Fallback in case options are not rendered
                    activeSection = 'all';
                }

                if (sectionOptions.length) {
                    setSection('all');
                }

                rows.forEach((row) => {
                    const toggle = row.querySelector('[data-mpor-row-toggle]');
                    if (!toggle) {
                        return;
                    }
                    toggle.addEventListener('click', () => {
                        const expanded = toggle.getAttribute('aria-expanded') === 'true';
                        setRowExpanded(row, !expanded);
                    });
                });

                paintMetricTabs();
                paintRowsBySection();
                paintMetricValues();
                collapseAllRows();
                ensureAtLeastOneExpanded();
            }

            const submitForm = document.getElementById('mporSubmitForm');
            const proceedButton = document.getElementById('mporProceedSubmissionBtn');
            const actionButtons = document.getElementById('mporActionButtons');
            const returnedBanner = document.getElementById('mporReturnedBanner');
            const label = proceedButton?.querySelector('[data-button-label]');
            const spinner = proceedButton?.querySelector('[data-button-spinner]');
            const originalLabel = label?.textContent?.trim() || 'Proceed Submission';
            const modalId = 'mporSubmitConfirmModal';

            if (!submitForm || !proceedButton) {
                return;
            }

            const clearAlert = () => {};

            const renderAlert = (type, message) => {
                if (!message) {
                    return;
                }

                if (window.PMSnackbar) {
                    window.PMSnackbar.clear();
                    window.PMSnackbar.show({
                        type: String(type || 'info').toLowerCase(),
                        message: String(message),
                    });
                }
            };

            const setLoading = (isLoading) => {
                proceedButton.disabled = true;
                proceedButton.classList.add('cursor-not-allowed', 'opacity-80');

                if (label) {
                    label.textContent = isLoading ? 'Submitting...' : originalLabel;
                }

                if (spinner) {
                    spinner.classList.toggle('hidden', !isLoading);
                }

                if (!isLoading) {
                    proceedButton.disabled = false;
                    proceedButton.classList.remove('cursor-not-allowed', 'opacity-80');
                    delete proceedButton.dataset.loadingActive;
                }
            };

            const closeSubmitModal = () => {
                const hideButton = document.querySelector(`[data-modal-hide="${modalId}"]`);
                if (hideButton) {
                    hideButton.click();
                    return;
                }

                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            };

            const markSubmittedActionButton = () => {
                if (!actionButtons) {
                    return;
                }

                const submitMporButton = actionButtons.querySelector('[data-modal-target="mporSubmitConfirmModal"]');
                if (!submitMporButton) {
                    return;
                }

                const submittedButton = document.createElement('button');
                submittedButton.type = 'button';
                submittedButton.disabled = true;
                submittedButton.className = 'flex-1 cursor-not-allowed rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-center text-xs font-semibold text-emerald-200 opacity-80 md:flex-none';
                submittedButton.textContent = 'Submitted';
                submitMporButton.replaceWith(submittedButton);
            };

            const clearReturnedBanner = () => {
                if (!returnedBanner) {
                    return;
                }

                returnedBanner.remove();
            };

            submitForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                if (proceedButton.dataset.loadingActive === 'true') {
                    return;
                }

                clearAlert();
                proceedButton.dataset.loadingActive = 'true';
                setLoading(true);

                const token = submitForm.querySelector('input[name="_token"]')?.value
                    || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || '';

                try {
                    const response = await fetch(submitForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: new FormData(submitForm),
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (response.ok && payload?.ok === true) {
                        renderAlert('success', payload?.message || 'MPOR successfully submitted.');
                        closeSubmitModal();
                        markSubmittedActionButton();
                        clearReturnedBanner();
                        return;
                    }

                    const message = payload?.message || 'Unable to submit MPOR.';
                    if (response.status === 422 || payload?.type === 'info' || payload?.ok === false) {
                        renderAlert('info', message);
                    } else {
                        renderAlert('error', message);
                    }
                    setLoading(false);
                } catch (error) {
                    renderAlert('error', 'Something went wrong while submitting MPOR. Please try again.');
                    setLoading(false);
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        #mporSectionFilter option {
            background-color: #020617;
            color: #e2e8f0;
        }
    </style>
@endpush
