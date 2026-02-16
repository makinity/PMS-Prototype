@extends('layouts.dept-head')

@section('main-content')
    @php
        $statusMeta = [
            'draft' => [
                'label' => 'Draft',
                'badge' => 'border-violet-500/40 bg-violet-500/10 text-violet-200',
            ],
            'dept_head_approved' => [
                'label' => 'Dept Head Approved',
                'badge' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200',
            ],
        ];

        $currentStatusMeta = $statusMeta[$status] ?? $statusMeta['draft'];

        $incomingMporsSafe = $incomingMpors ?? [];
        $consolidatedMporsSafe = $consolidatedMpors ?? [];
        $rowsSafe = $rows ?? [];

        $isApproved = $status === 'dept_head_approved';
        $hasIncoming = !empty($incomingMporsSafe);
        $hasConsolidated = !empty($consolidatedMporsSafe);

        $canGenerate = ! $isApproved;
        $canApprove = ! $isApproved && !empty($generatedAt) && $hasConsolidated;

        $approvedDateLabel = $isApproved && !empty($approvedAt)
            ? \Illuminate\Support\Carbon::parse($approvedAt)->format('M d, Y g:i A')
            : '-';

        $generatedDateLabel = !empty($generatedAt)
            ? \Illuminate\Support\Carbon::parse($generatedAt)->format('M d, Y g:i A')
            : '-';

        $uwpTargetTimelineMapSafe = $uwpTargetTimelineMap ?? [];
        $mporDummyDetailsSafe = $mporDummyDetails ?? [];
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

        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Annex I - Office Quarterly Accomplishment Report</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Office Quarterly Accomplishment Report (QAR)</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Review incoming submitted MPORs, consolidate into QAR snapshot, then approve for PMT validation.
                </p>
            </div>

            <div class="w-full space-y-2 lg:w-auto">
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-3">
                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Office</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $office }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-3">
                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Quarter</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $quarter }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-3">
                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Status</p>
                        <span class="{{ $currentStatusMeta['badge'] }} mt-1 inline-flex rounded-full border px-2 py-1 text-xs font-semibold">
                            {{ $currentStatusMeta['label'] }}
                        </span>
                    </div>
                </div>

                <div class="flex justify-end">
                    <form id="qarResetForm" method="POST" action="{{ route('dept-head.qar.reset') }}">
                        @csrf
                        <button type="submit"
                            id="qarResetBtn"
                            class="inline-flex items-center gap-2 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs font-semibold text-rose-200 transition hover:bg-rose-500/20">
                            <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                            <span data-button-label>Reset Prototype</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">A) Incoming MPORs</h2>
                    <p class="text-xs text-slate-400">Received submitted MPORs waiting for QAR consolidation.</p>
                </div>
            </div>

            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead>
                        <tr class="bg-slate-950/60 text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3">Month</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Notes</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse ($incomingMporsSafe as $mpor)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-white">{{ $mpor['employee'] ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $mpor['month'] ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-slate-700 bg-slate-950/40 px-2 py-1 text-xs font-semibold text-slate-200">
                                        {{ $mpor['status'] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-300">Ready for consolidation</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button"
                                            data-modal-target="qarViewMporModal"
                                            data-modal-toggle="qarViewMporModal"
                                            data-mpor-view-trigger="1"
                                            data-mpor-key="{{ \Illuminate\Support\Str::slug(($mpor['employee'] ?? 'unknown') . '-' . ($mpor['month'] ?? 'unknown')) }}"
                                            class="rounded-lg border border-slate-700 bg-slate-900/50 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-800">
                                            View
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-400">
                                    No incoming MPORs yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">B) Consolidation Summary</h2>
                    <p class="text-xs text-slate-400">Snapshot based on consolidated MPORs only.</p>
                </div>
                <div class="text-right text-xs text-slate-500">
                    <p>Last consolidated: {{ $generatedDateLabel }}</p>
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-4">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Included MPORs</p>
                    <p class="mt-1 text-base font-semibold text-white">{{ $includedMporCount }}</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Included Employees</p>
                    <p class="mt-1 text-base font-semibold text-white">{{ $includedEmployeeCount }}</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Included Months in Quarter</p>
                    <p class="mt-1 text-base font-semibold text-white">{{ $includedMonthsCount }}/{{ $includedMonthsTotal }}</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Data Source</p>
                    <p class="mt-1 text-sm font-semibold text-white">Consolidated MPOR snapshot</p>
                </div>
            </div>

            <div class="mt-4 rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Consolidated MPOR Records</p>
                @if ($hasConsolidated)
                    <ul class="mt-2 space-y-1 text-sm text-slate-300">
                        @foreach ($consolidatedMporsSafe as $mpor)
                            <li>- {{ $mpor['employee'] ?? '-' }} - {{ $mpor['month'] ?? '-' }} - {{ $mpor['status'] ?? '-' }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-2 text-sm text-slate-400">No consolidated snapshot yet. Consolidate incoming MPORs first.</p>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">C) Annex I Consolidated QAR</h2>
                    <p class="text-xs text-slate-400">Rows appear only after consolidation.</p>
                </div>
                <button type="button"
                    data-modal-target="qarApproveConfirmModal"
                    data-modal-toggle="qarApproveConfirmModal"
                    @disabled(!$canApprove)
                    class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-500/20 disabled:cursor-not-allowed disabled:opacity-60">
                    Approve QAR
                </button>
            </div>

            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead>
                        <tr class="bg-slate-950/60 text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                            <th class="px-4 py-3">PPA Code</th>
                            <th class="px-4 py-3">MFO/PPA</th>
                            <th class="px-4 py-3">Performance Indicator</th>
                            <th class="px-4 py-3 text-center">Target / Timeline</th>
                            <th class="px-4 py-3 text-center">Actual Performance</th>
                            <th class="px-4 py-3">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse ($rowsSafe as $row)
                            @php
                                $code = (string) ($row['ppa_code'] ?? '');
                                $targetTimeline = $uwpTargetTimelineMapSafe[$code] ?? ($row['target_timeline'] ?? '-');
                            @endphp
                            <tr>
                                <td class="px-4 py-3">{{ $row['ppa_code'] }}</td>
                                <td class="px-4 py-3">{{ $row['mfo'] }}</td>
                                <td class="px-4 py-3">{{ $row['indicator'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <p class="text-sm text-slate-200">{{ $targetTimeline }}</p>
                                </td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $row['actual_performance'] }}</td>
                                <td class="px-4 py-3">{{ $row['remarks'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-400">
                                    QAR rows are empty. Consolidate incoming MPORs first.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Prepared/Approved by</p>
                    <p class="mt-2 text-sm font-semibold text-white">{{ $deptHeadName }}</p>
                    <p class="mt-2 text-xs text-slate-500">Date:</p>
                    <p class="text-sm text-slate-300">{{ $approvedDateLabel }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Validated by</p>
                    <p class="mt-2 text-sm font-semibold text-white">PMT</p>
                    <p class="mt-2 text-sm text-amber-200">{{ $pmtStatusLabel }}</p>
                </div>
            </div>
        </div>
    </section>

    <div id="qarGenerateConfirmModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-lg p-4">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between border-b border-slate-800 p-5">
                    <h3 class="text-lg font-semibold text-white">Consolidate to QAR</h3>
                    <button type="button" data-modal-hide="qarGenerateConfirmModal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <span class="sr-only">Close modal</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="space-y-3 p-5 text-sm text-slate-300">
                    <p>
                        Consolidate incoming MPORs for <span class="font-semibold text-white">{{ $quarter }}</span> -
                        <span class="font-semibold text-white">{{ $office }}</span>?
                    </p>
                    <p>
                        This will build a consolidated QAR snapshot from the current incoming MPOR list.
                    </p>
                </div>
                <form id="qarGenerateForm" method="POST" action="{{ route('dept-head.qar.generate') }}"
                    class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                    @csrf
                    <button type="button" data-modal-hide="qarGenerateConfirmModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" id="qarGenerateProceedBtn"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        <span data-button-label>Proceed Consolidate</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

        <div id="qarApproveConfirmModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-lg p-4">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between border-b border-slate-800 p-5">
                    <h3 class="text-lg font-semibold text-white">Approve QAR</h3>
                    <button type="button" data-modal-hide="qarApproveConfirmModal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <span class="sr-only">Close modal</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="space-y-3 p-5 text-sm text-slate-300">
                    <p>Approve this consolidated QAR for PMT validation?</p>
                    <p>Once approved, QAR becomes read-only at Dept Head level.</p>
                </div>
                <form id="qarApproveForm" method="POST" action="{{ route('dept-head.qar.approve') }}"
                    class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                    @csrf
                    <button type="button" data-modal-hide="qarApproveConfirmModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" id="qarApproveProceedBtn"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        <span data-button-label>Proceed Approve</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="qarViewMporModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-7xl p-4">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between gap-3 border-b border-slate-800 p-5">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Monthly Performance Output Report</h3>
                        <p class="mt-1 text-xs text-slate-400">Read-only mirror of locked ORS entries with supervisor ratings.</p>
                        <p class="mt-2 text-xs text-slate-500">
                            Submitted at:
                            <span data-bind="submitted_at" class="text-slate-300">--</span>
                        </p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span data-bind="status"
                            class="inline-flex rounded-full border border-slate-700 bg-slate-950/40 px-2 py-1 text-xs font-semibold text-slate-200">
                            Submitted (Locked)
                        </span>
                        <button type="button" data-modal-hide="qarViewMporModal"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                            <span class="sr-only">Close modal</span>
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-5 p-5 text-sm text-slate-300">
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Name</p>
                            <p data-bind="employee_name" class="mt-1 text-sm font-semibold text-white">--</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Office / Division</p>
                            <p data-bind="office_division" class="mt-1 text-sm font-semibold text-white">--</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Month</p>
                            <p data-bind="month_label" class="mt-1 text-sm font-semibold text-white">--</p>
                        </div>
                    </div>

                    <div>
                        <div>
                            <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-950/60">
                                <table class="min-w-full text-[0.75rem] text-slate-200">
                                    <thead>
                                        <tr class="text-left text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                                            <th class="whitespace-nowrap px-3 py-2 align-bottom" rowspan="2">Output / Task</th>
                                            <th class="border-l border-slate-800 px-3 py-2 text-center" colspan="5">Efficiency / Quantity</th>
                                            <th class="border-l border-slate-800 px-3 py-2 text-center" colspan="5">Quality / Effectiveness</th>
                                            <th class="border-l border-slate-800 px-3 py-2 text-center" colspan="5">Timeliness</th>
                                        </tr>
                                        <tr class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">
                                            <th class="border-l border-slate-800 px-2 py-1 text-right">W1</th>
                                            <th class="px-2 py-1 text-right">W2</th>
                                            <th class="px-2 py-1 text-right">W3</th>
                                            <th class="px-2 py-1 text-right">W4</th>
                                            <th class="px-2 py-1 text-right font-semibold">Total</th>
                                            <th class="border-l border-slate-800 px-2 py-1 text-right">W1</th>
                                            <th class="px-2 py-1 text-right">W2</th>
                                            <th class="px-2 py-1 text-right">W3</th>
                                            <th class="px-2 py-1 text-right">W4</th>
                                            <th class="px-2 py-1 text-right font-semibold">Total</th>
                                            <th class="border-l border-slate-800 px-2 py-1 text-right">W1</th>
                                            <th class="px-2 py-1 text-right">W2</th>
                                            <th class="px-2 py-1 text-right">W3</th>
                                            <th class="px-2 py-1 text-right">W4</th>
                                            <th class="px-2 py-1 text-right font-semibold">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="mporGridTbody" class="divide-y divide-slate-800"></tbody>
                                </table>
                            </div>

                            <p class="mt-3 text-xs text-slate-400">
                                Stage II demo: MPOR points = Quantity &times; Supervisor Rating (Q/T). Batch quantities are treated as single units.
                            </p>

                            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                                <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4 text-xs uppercase tracking-[0.3em] text-slate-400">
                                    <div class="flex items-center justify-between text-[0.6rem] tracking-[0.3em] text-slate-500">
                                        <span>Week 1</span><span>Week 2</span><span>Week 3</span><span>Week 4</span><span>Total</span>
                                    </div>
                                    <div class="mt-2 grid grid-cols-5 text-center text-sm font-semibold text-white">
                                        <span id="mporWeek1">0</span>
                                        <span id="mporWeek2">0</span>
                                        <span id="mporWeek3">0</span>
                                        <span id="mporWeek4">0</span>
                                        <span id="mporGrand">0</span>
                                    </div>
                                    <div class="my-5 border-t border-slate-700/70"></div>
                                    <div class="space-y-2 text-[0.65rem] tracking-[0.2em] text-slate-500">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="min-w-0">Included ORS Entries (Rated)</span>
                                            <span id="mporIncluded" class="shrink-0 font-semibold text-white">0</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="min-w-0">Excluded Entries (Unrated/Draft/Missing)</span>
                                            <span id="mporExcluded" class="shrink-0 font-semibold text-white">0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                    <div class="flex items-center justify-between text-sm font-semibold text-white">
                                        <span>Confirmed:</span>
                                        <span class="text-slate-500">Stage II</span>
                                    </div>
                                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                        <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                                            <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Supervisor</p>
                                            <p id="mporSupervisorName" class="text-sm font-semibold text-white normal-case tracking-normal">--</p>
                                            <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                                        </div>
                                        <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                                            <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Employee</p>
                                            <p id="mporEmployeeConfirmName" class="text-sm font-semibold text-white normal-case tracking-normal">--</p>
                                            <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end border-t border-slate-800 p-5">
                    <button type="button"
                        data-modal-hide="qarViewMporModal"
                        data-modal-target="qarGenerateConfirmModal"
                        data-modal-toggle="qarGenerateConfirmModal"
                        @disabled(!$canGenerate)
                        class="mr-2 rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60">
                        Consolidate to QAR
                    </button>
                    <button type="button" data-modal-hide="qarViewMporModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="mporDummyJson">{!! json_encode($mporDummyDetailsSafe) !!}</script>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const bindLoadingSubmit = (formId, buttonId, loadingLabel) => {
                    const form = document.getElementById(formId);
                    const button = document.getElementById(buttonId);
                    if (!form || !button) {
                        return;
                    }

                    const spinner = button.querySelector('[data-button-spinner]');
                    const label = button.querySelector('[data-button-label]');

                    form.addEventListener('submit', function() {
                        button.disabled = true;
                        button.classList.add('cursor-not-allowed', 'opacity-80');

                        if (spinner) {
                            spinner.classList.remove('hidden');
                        }

                        if (label) {
                            label.textContent = loadingLabel;
                        }
                    });
                };

                bindLoadingSubmit('qarGenerateForm', 'qarGenerateProceedBtn', 'Consolidating...');
                bindLoadingSubmit('qarApproveForm', 'qarApproveProceedBtn', 'Approving...');
                bindLoadingSubmit('qarResetForm', 'qarResetBtn', 'Resetting...');

                const mporJsonEl = document.getElementById('mporDummyJson');
                let mporData = {};

                try {
                    mporData = JSON.parse(mporJsonEl?.textContent || '{}');
                } catch (error) {
                    mporData = {};
                }

                const mporGridTbody = document.getElementById('mporGridTbody');
                const mporWeek1 = document.getElementById('mporWeek1');
                const mporWeek2 = document.getElementById('mporWeek2');
                const mporWeek3 = document.getElementById('mporWeek3');
                const mporWeek4 = document.getElementById('mporWeek4');
                const mporGrand = document.getElementById('mporGrand');
                const mporIncluded = document.getElementById('mporIncluded');
                const mporExcluded = document.getElementById('mporExcluded');
                const mporSupervisorName = document.getElementById('mporSupervisorName');
                const mporEmployeeConfirmName = document.getElementById('mporEmployeeConfirmName');

                const escapeHtml = (value) => String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');

                const setBoundText = (key, value) => {
                    document.querySelectorAll(`[data-bind="${key}"]`).forEach((el) => {
                        el.textContent = value ?? '-';
                    });
                };

                const buildMetricCells = (metric, withLeftBorder) => {
                    const safeMetric = metric || {};
                    const keys = ['w1', 'w2', 'w3', 'w4', 'total'];

                    return keys.map((key, index) => {
                        const classes = ['px-2', 'py-2', 'text-right', 'tabular-nums'];
                        if (index === 0 && withLeftBorder) {
                            classes.push('border-l', 'border-slate-800');
                        }
                        if (key === 'total') {
                            classes.push('font-semibold', 'text-white');
                        } else {
                            classes.push('text-slate-200');
                        }

                        return `<td class="${classes.join(' ')}">${escapeHtml(safeMetric[key] ?? 0)}</td>`;
                    }).join('');
                };

                const renderMporGrid = (groups) => {
                    if (!mporGridTbody) {
                        return;
                    }

                    if (!Array.isArray(groups) || groups.length === 0) {
                        mporGridTbody.innerHTML = `
                            <tr>
                                <td colspan="16" class="px-4 py-5 text-center text-sm text-slate-400">
                                    No MPOR grid rows available.
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    const html = [];

                    groups.forEach((group) => {
                        const groupLabel = `${group?.label || 'GROUP'}${group?.weight_label ? ` (${group.weight_label})` : ''}`;
                        html.push(`
                            <tr class="bg-slate-900/60 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                <td class="px-3 py-2 font-semibold text-slate-200" colspan="16">${escapeHtml(groupLabel)}</td>
                            </tr>
                        `);

                        const rows = Array.isArray(group?.rows) ? group.rows : [];
                        if (!rows.length) {
                            html.push(`
                                <tr>
                                    <td colspan="16" class="px-3 py-2 text-sm text-slate-400">No rows found for this group.</td>
                                </tr>
                            `);
                            return;
                        }

                        rows.forEach((row) => {
                            html.push(`
                                <tr class="text-slate-200">
                                    <td class="px-3 py-2 font-medium text-white">${escapeHtml(row?.task_title || '-')}</td>
                                    ${buildMetricCells(row?.eff, true)}
                                    ${buildMetricCells(row?.qual, true)}
                                    ${buildMetricCells(row?.time, true)}
                                </tr>
                            `);
                        });
                    });

                    mporGridTbody.innerHTML = html.join('');
                };

                const fillSummary = (summary) => {
                    if (mporWeek1) {
                        mporWeek1.textContent = String(summary?.week1_total ?? 0);
                    }
                    if (mporWeek2) {
                        mporWeek2.textContent = String(summary?.week2_total ?? 0);
                    }
                    if (mporWeek3) {
                        mporWeek3.textContent = String(summary?.week3_total ?? 0);
                    }
                    if (mporWeek4) {
                        mporWeek4.textContent = String(summary?.week4_total ?? 0);
                    }
                    if (mporGrand) {
                        mporGrand.textContent = String(summary?.grand_total ?? 0);
                    }
                    if (mporIncluded) {
                        mporIncluded.textContent = String(summary?.included_entries ?? 0);
                    }
                    if (mporExcluded) {
                        mporExcluded.textContent = String(summary?.excluded_entries ?? 0);
                    }
                };

                const fillConfirmed = (confirmed) => {
                    if (mporSupervisorName) {
                        mporSupervisorName.textContent = confirmed?.supervisor_name || '--';
                    }
                    if (mporEmployeeConfirmName) {
                        mporEmployeeConfirmName.textContent = confirmed?.employee_name || '--';
                    }
                };

                const fillViewModal = (detail) => {
                    const safe = {
                        employee_name: detail?.employee_name || '-',
                        office_division: detail?.office_division || '-',
                        month_label: detail?.month_label || '-',
                        status: detail?.status || 'Submitted (Locked)',
                        submitted_at: detail?.submitted_at || '-',
                        groups: Array.isArray(detail?.groups) ? detail.groups : [],
                        summary: detail?.summary || {},
                        confirmed: detail?.confirmed || {},
                    };

                    setBoundText('employee_name', safe.employee_name);
                    setBoundText('office_division', safe.office_division);
                    setBoundText('month_label', safe.month_label);
                    setBoundText('status', safe.status);
                    setBoundText('submitted_at', safe.submitted_at);

                    renderMporGrid(safe.groups);
                    fillSummary(safe.summary);
                    fillConfirmed(safe.confirmed);
                };

                const viewButtons = document.querySelectorAll('[data-mpor-view-trigger][data-mpor-key]');
                viewButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const key = button.getAttribute('data-mpor-key') || '';
                        const detail = mporData[key] || Object.values(mporData)[0] || null;
                        if (!detail) {
                            return;
                        }
                        fillViewModal(detail);
                    });
                });
            });
        </script>
    @endpush
@endsection
