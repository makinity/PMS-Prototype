@extends('layouts.dept-head')

@section('main-content')
    @php
        $statusMeta = [
            'draft' => [
                'label' => 'Draft',
                'badge' => 'border-violet-500/40 bg-violet-500/10 text-violet-200',
            ],
            'dept_head_endorsed' => [
                'label' => 'Dept Head Endorsed',
                'badge' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200',
            ],
            'pmt_approved' => [
                'label' => 'PMT Approved',
                'badge' => 'border-sky-500/40 bg-sky-500/10 text-sky-200',
            ],
        ];

        $currentStatusMeta = $statusMeta[$status] ?? $statusMeta['draft'];

        $incomingMporsSafe = $incomingMpors ?? [];
        $consolidatedMporsSafe = $consolidatedMpors ?? [];
        $rowsSafe = $rows ?? [];

        $isEndorsed = in_array($status, ['dept_head_endorsed', 'pmt_approved'], true);
        $hasIncoming = !empty($incomingMporsSafe);
        $hasConsolidated = !empty($consolidatedMporsSafe);

        $canEndorse = ($status === 'draft') && $hasConsolidated;

        $approvedDateLabel = $isEndorsed && !empty($approvedAt)
            ? \Illuminate\Support\Carbon::parse($approvedAt)->format('M d, Y g:i A')
            : '-';

        $generatedDateLabel = !empty($generatedAt)
            ? \Illuminate\Support\Carbon::parse($generatedAt)->format('M d, Y g:i A')
            : '-';

        $uwpTargetTimelineMapSafe = $uwpTargetTimelineMap ?? [];
        $selectedMporDetailSafe = $selectedMporDetail ?? [];
        $selectedGroups = is_array($selectedMporDetailSafe['groups'] ?? null) ? $selectedMporDetailSafe['groups'] : [];
        $selectedSummary = is_array($selectedMporDetailSafe['summary'] ?? null) ? $selectedMporDetailSafe['summary'] : [];
        $selectedConfirmed = is_array($selectedMporDetailSafe['confirmed'] ?? null) ? $selectedMporDetailSafe['confirmed'] : [];
        $selectedEmployeeName = $selectedMporDetailSafe['employee_name'] ?? '--';
        $selectedOfficeDivision = $selectedMporDetailSafe['office_division'] ?? $office;
        $selectedMonthLabel = $selectedMporDetailSafe['month_label'] ?? '--';
        $selectedStatusLabel = $selectedMporDetailSafe['status'] ?? 'Submitted (Locked)';
        $selectedSubmittedAt = $selectedMporDetailSafe['submitted_at'] ?? '-';
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
                    Approved MPORs automatically populate this QAR snapshot for PMT validation.
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
                        <p class="mt-1 text-sm font-semibold text-white">{{ $quarterLabel ?? '-' }}</p>
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
                    <p class="text-xs text-slate-400">Approved MPORs received for automatic QAR population.</p>
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
                                <td class="px-4 py-3 text-slate-300">Auto-populated to QAR</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('dept-head.qar', ['mpor_id' => $mpor['id'] ?? 0]) }}"
                                            class="rounded-lg border border-slate-700 bg-slate-900/50 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-800">
                                            View
                                        </a>
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
                    <h2 class="text-lg font-semibold text-white">B) QAR Summary</h2>
                    <p class="text-xs text-slate-400">Snapshot auto-populated from approved MPORs.</p>
                </div>
                <div class="text-right text-xs text-slate-500">
                    <p>Last updated: {{ $generatedDateLabel }}</p>
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
                    <p class="mt-1 text-sm font-semibold text-white">Approved MPOR snapshot</p>
                </div>
            </div>

            <div class="mt-4 rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Approved MPOR Records</p>
                @if ($hasConsolidated)
                    <ul class="mt-2 space-y-1 text-sm text-slate-300">
                        @foreach ($consolidatedMporsSafe as $mpor)
                            <li>- {{ $mpor['employee'] ?? '-' }} - {{ $mpor['month'] ?? '-' }} - {{ $mpor['status'] ?? '-' }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-2 text-sm text-slate-400">No approved MPORs yet for this quarter.</p>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">C) Annex I QAR</h2>
                    <p class="text-xs text-slate-400">Rows are auto-populated from approved MPORs.</p>
                </div>
                <button type="button"
                    data-modal-target="qarApproveConfirmModal"
                    data-modal-toggle="qarApproveConfirmModal"
                    class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-500/20 disabled:cursor-not-allowed disabled:opacity-60">
                    Endorse QAR
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
                                    QAR rows are empty. No approved MPOR data found.
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

        <div id="qarApproveConfirmModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-lg p-4">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between border-b border-slate-800 p-5">
                    <h3 class="text-lg font-semibold text-white">Endorse QAR</h3>
                    <button type="button" data-modal-hide="qarApproveConfirmModal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <span class="sr-only">Close modal</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="space-y-3 p-5 text-sm text-slate-300">
                    <p>Endorse this QAR for PMT validation?</p>
                    <p>Once endorsed, QAR becomes read-only at Dept Head level.</p>
                </div>
                <form id="qarEndorseForm" method="POST" action="{{ route('dept-head.qar.endorse') }}"
                    class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                    @csrf
                    <button type="button" data-modal-hide="qarApproveConfirmModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" id="qarApproveProceedBtn"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        <span data-button-label>Proceed Endorse</span>
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
                            <span class="text-slate-300">{{ $selectedSubmittedAt }}</span>
                        </p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="inline-flex rounded-full border border-slate-700 bg-slate-950/40 px-2 py-1 text-xs font-semibold text-slate-200">
                            {{ $selectedStatusLabel }}
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
                            <p class="mt-1 text-sm font-semibold text-white">{{ $selectedEmployeeName }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Office / Division</p>
                            <p class="mt-1 text-sm font-semibold text-white">{{ $selectedOfficeDivision }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Month</p>
                            <p class="mt-1 text-sm font-semibold text-white">{{ $selectedMonthLabel }}</p>
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
                                    <tbody class="divide-y divide-slate-800">
                                        @forelse ($selectedGroups as $group)
                                            @php
                                                $groupRows = is_array($group['rows'] ?? null) ? $group['rows'] : [];
                                            @endphp
                                            <tr class="bg-slate-900/60 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                                <td class="px-3 py-2 font-semibold text-slate-200" colspan="16">
                                                    {{ ($group['label'] ?? 'GROUP') . (!empty($group['weight_label']) ? ' (' . $group['weight_label'] . ')' : '') }}
                                                </td>
                                            </tr>

                                            @forelse ($groupRows as $row)
                                                @php
                                                    $eff = is_array($row['eff'] ?? null) ? $row['eff'] : [];
                                                    $qual = is_array($row['qual'] ?? null) ? $row['qual'] : [];
                                                    $time = is_array($row['time'] ?? null) ? $row['time'] : [];
                                                @endphp
                                                <tr class="text-slate-200">
                                                    <td class="px-3 py-2 font-medium text-white">{{ $row['task_title'] ?? '-' }}</td>

                                                    <td class="border-l border-slate-800 px-2 py-2 text-right tabular-nums">{{ $eff['w1'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right tabular-nums">{{ $eff['w2'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right tabular-nums">{{ $eff['w3'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right tabular-nums">{{ $eff['w4'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right font-semibold text-white tabular-nums">{{ $eff['total'] ?? 0 }}</td>

                                                    <td class="border-l border-slate-800 px-2 py-2 text-right tabular-nums">{{ $qual['w1'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right tabular-nums">{{ $qual['w2'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right tabular-nums">{{ $qual['w3'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right tabular-nums">{{ $qual['w4'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right font-semibold text-white tabular-nums">{{ $qual['total'] ?? 0 }}</td>

                                                    <td class="border-l border-slate-800 px-2 py-2 text-right tabular-nums">{{ $time['w1'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right tabular-nums">{{ $time['w2'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right tabular-nums">{{ $time['w3'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right tabular-nums">{{ $time['w4'] ?? 0 }}</td>
                                                    <td class="px-2 py-2 text-right font-semibold text-white tabular-nums">{{ $time['total'] ?? 0 }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="16" class="px-3 py-2 text-sm text-slate-400">No rows found for this group.</td>
                                                </tr>
                                            @endforelse
                                        @empty
                                            <tr>
                                                <td colspan="16" class="px-4 py-5 text-center text-sm text-slate-400">
                                                    No MPOR grid rows available.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <p class="mt-3 text-xs text-slate-400">
                                Stage II: MPOR points = Quantity &times; Supervisor Rating (Q/T). Derived from rated ORS entries.
                            </p>

                            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                                <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4 text-xs uppercase tracking-[0.3em] text-slate-400">
                                    <div class="flex items-center justify-between text-[0.6rem] tracking-[0.3em] text-slate-500">
                                        <span>Week 1</span><span>Week 2</span><span>Week 3</span><span>Week 4</span><span>Total</span>
                                    </div>
                                    <div class="mt-2 grid grid-cols-5 text-center text-sm font-semibold text-white">
                                        <span>{{ $selectedSummary['week1_total'] ?? 0 }}</span>
                                        <span>{{ $selectedSummary['week2_total'] ?? 0 }}</span>
                                        <span>{{ $selectedSummary['week3_total'] ?? 0 }}</span>
                                        <span>{{ $selectedSummary['week4_total'] ?? 0 }}</span>
                                        <span>{{ $selectedSummary['grand_total'] ?? 0 }}</span>
                                    </div>
                                    <div class="my-5 border-t border-slate-700/70"></div>
                                    <div class="space-y-2 text-[0.65rem] tracking-[0.2em] text-slate-500">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="min-w-0">Included ORS Entries (Rated)</span>
                                            <span class="shrink-0 font-semibold text-white">{{ $selectedSummary['included_entries'] ?? 0 }}</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="min-w-0">Excluded Entries (Unrated/Draft/Missing)</span>
                                            <span class="shrink-0 font-semibold text-white">{{ $selectedSummary['excluded_entries'] ?? 0 }}</span>
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
                                            <p class="text-sm font-semibold text-white normal-case tracking-normal">{{ $selectedConfirmed['supervisor_name'] ?? '--' }}</p>
                                            <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                                        </div>
                                        <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                                            <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Employee</p>
                                            <p class="text-sm font-semibold text-white normal-case tracking-normal">{{ $selectedConfirmed['employee_name'] ?? '--' }}</p>
                                            <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end border-t border-slate-800 p-5">
                    <button type="button" data-modal-hide="qarViewMporModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (request()->filled('mpor_id') && $hasIncoming)
        <button type="button"
            id="qarAutoOpenViewModal"
            class="hidden"
            data-modal-target="qarViewMporModal"
            data-modal-toggle="qarViewMporModal">
            Open MPOR View
        </button>
    @endif

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

                bindLoadingSubmit('qarEndorseForm', 'qarApproveProceedBtn', 'Endorsing...');

                const autoOpenViewButton = document.getElementById('qarAutoOpenViewModal');
                if (autoOpenViewButton) {
                    window.setTimeout(() => {
                        autoOpenViewButton.click();
                    }, 80);
                }
            });
        </script>
    @endpush
@endsection
