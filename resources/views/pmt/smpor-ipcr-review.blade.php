@extends('layouts.pmt')

@section('main-content')
    @php
        $performancePeriod = 'January–June 2026';
        $officeUnit = 'Revenue Collection Unit';
        $supervisorName = 'Carlo D. Beray';

        $submissions = [
            [
                'employee' => 'Ramon Reyes',
                'office' => $officeUnit,
                'submitted_at' => 'June 30, 2026 4:15 PM',
                'smpor_status' => 'Submitted',
                'ipcr_status' => 'Submitted',
                'remarks' => 'Completed all assigned monitoring outputs. Supporting docs attached.',
            ],
        ];

        $smporSummary = [
            [
                'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'qty' => 1,
                'q_pts' => 5,
                't_pts' => 5,
                'function' => 'Core',
            ],
            [
                'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                'qty' => 12,
                'q_pts' => 60,
                't_pts' => 60,
                'function' => 'Core',
            ],
            [
                'mfo' => 'Maintenance of Revenue Records Filing System',
                'qty' => 0,
                'q_pts' => 0,
                't_pts' => 0,
                'function' => 'Support',
            ],
        ];

        $smporTotals = [
            'qty' => array_sum(array_map(fn($r) => $r['qty'], $smporSummary)),
            'q_pts' => array_sum(array_map(fn($r) => $r['q_pts'], $smporSummary)),
            't_pts' => array_sum(array_map(fn($r) => $r['t_pts'], $smporSummary)),
        ];

        $ipcrAccomplishments = [
            [
                'mfo' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'summary' => 'Monitoring-derived accomplishments based on submitted + rated ORS entries. Evidence is attached in ORS records.',
                'evidence' => 'Attached (reference)',
                'function' => 'Core',
            ],
            [
                'mfo' => 'Processing of Over-the-Counter Revenue Transactions',
                'summary' => 'Monitoring-derived accomplishments based on submitted + rated ORS entries. Evidence is attached in ORS records.',
                'evidence' => 'Attached (reference)',
                'function' => 'Core',
            ],
            [
                'mfo' => 'Maintenance of Revenue Records Filing System',
                'summary' => 'No monitoring entries recorded for the period in this demo dataset.',
                'evidence' => '—',
                'function' => 'Support',
            ],
        ];
    @endphp

    <section class="space-y-6">

        <!-- Page Header -->
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Stage II — Monitoring Visibility
                </p>
                <h1 class="text-2xl font-bold text-white">SMPOR &amp; IPCR Accomplishment Review</h1>
                <p class="text-sm text-slate-400">
                    View-only list of end-of-period submissions. No approvals or ratings occur in Stage II.
                </p>
                <p class="text-xs text-slate-500 mt-1">
                    Performance Period: {{ $performancePeriod }} • Supervisor: {{ $supervisorName }} • Office/Unit: {{ $officeUnit }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-slate-900/60 px-3 py-1 text-xs font-semibold text-slate-200 border border-slate-700">
                    Read-only
                </span>
                <span class="inline-flex items-center rounded-full bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200 border border-blue-500/40">
                    Monitoring copy
                </span>
            </div>
        </div>

        <!-- Submissions List -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">Submitted Accomplishments</h2>
                    <p class="text-xs text-slate-400">
                        Entries appear only after Employee submits SMPOR &amp; IPCR accomplishments (locked for Stage III).
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button"
                            disabled
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-500 bg-slate-800/60 cursor-not-allowed">
                        Export List (Disabled)
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Submitted At</th>
                            <th class="px-4 py-3 text-left">SMPOR</th>
                            <th class="px-4 py-3 text-left">IPCR Accomplishment</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($submissions as $row)
                            <tr class="hover:bg-slate-900/40">
                                <td class="px-4 py-3 text-slate-200 font-semibold">{{ $row['employee'] }}</td>
                                <td class="px-4 py-3">{{ $row['office'] }}</td>
                                <td class="px-4 py-3">{{ $row['submitted_at'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200 border border-emerald-500/40">
                                        {{ $row['smpor_status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200 border border-emerald-500/40">
                                        {{ $row['ipcr_status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            data-open-modal="review-modal"
                                            data-employee="{{ $row['employee'] }}"
                                            data-office="{{ $row['office'] }}"
                                            data-submitted="{{ $row['submitted_at'] }}"
                                            data-remarks="{{ $row['remarks'] }}"
                                            class="text-blue-400 hover:text-blue-300 text-xs font-semibold">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                    No submissions found for this period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-[11px] text-slate-500">
                Note: This screen is visibility-only. Stage III begins after submissions are locked.
            </p>
        </div>

    </section>

    <!-- REVIEW MODAL -->
    <div id="review-modal"
         class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Submitted Accomplishments (Read-only)</p>
                    <h3 class="text-lg font-semibold text-white">SMPOR &amp; IPCR Review — Stage II</h3>
                    <p class="text-[11px] text-slate-500 mt-1">
                        Monitoring-derived documents. No approval. No rating. Final IPCR rating occurs in Stage III.
                    </p>
                </div>
                <button type="button" data-close-modal="review-modal" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto">

                <!-- Context -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-4 text-sm text-slate-200">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Employee</p>
                        <p id="rv-employee" class="mt-1 font-semibold">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Office / Unit</p>
                        <p id="rv-office" class="mt-1 font-semibold">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Performance Period</p>
                        <p class="mt-1 font-semibold">{{ $performancePeriod }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Submitted At</p>
                        <p id="rv-submitted" class="mt-1 font-semibold">--</p>
                    </div>
                </div>

                <!-- Employee Remarks -->
                <div class="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">Employee Remarks (Submitted)</h4>
                            <p class="text-[11px] text-slate-500 mt-1">Read-only. Included for traceability.</p>
                        </div>
                    </div>
                    <p id="rv-remarks" class="mt-3 text-sm text-slate-200 whitespace-pre-line">--</p>
                </div>

                <!-- SMPOR Preview -->
                <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">SMPOR</p>
                            <h4 class="text-sm font-semibold text-white">Monitoring Summary (Read-only)</h4>
                            <p class="text-[11px] text-slate-500 mt-1">Derived from MPOR (submitted + rated ORS). No scoring.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    data-open-modal="smpor-preview-modal"
                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800 transition">
                                View SMPOR
                            </button>
                            <button type="button"
                                    disabled
                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-500 bg-slate-800/60 cursor-not-allowed">
                                Export PDF (Disabled)
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-slate-200">
                        <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase text-slate-500">Total Quantity (Monitoring)</p>
                            <p class="mt-1 font-semibold">{{ $smporTotals['qty'] }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase text-slate-500">Total Quality Points</p>
                            <p class="mt-1 font-semibold">{{ $smporTotals['q_pts'] }}</p>
                            <p class="text-[11px] text-slate-500 mt-1">Qty × Quality Rating</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase text-slate-500">Total Timeliness Points</p>
                            <p class="mt-1 font-semibold">{{ $smporTotals['t_pts'] }}</p>
                            <p class="text-[11px] text-slate-500 mt-1">Qty × Timeliness Rating</p>
                        </div>
                    </div>
                </div>

                <!-- IPCR Accomplishment Preview -->
                <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">IPCR</p>
                            <h4 class="text-sm font-semibold text-white">Accomplishment Report (Read-only)</h4>
                            <p class="text-[11px] text-slate-500 mt-1">System-generated accomplishments derived from SMPOR consolidation.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    data-open-modal="ipcr-preview-modal"
                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800 transition">
                                View IPCR Accomplishment
                            </button>
                            <button type="button"
                                    disabled
                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-500 bg-slate-800/60 cursor-not-allowed">
                                Export PDF (Disabled)
                            </button>
                        </div>
                    </div>

                    <p class="text-xs text-slate-400">
                        Reminder: Final IPCR rating is completed in Stage III. This Stage II view is monitoring-derived and read-only.
                    </p>
                </div>

                <div class="text-xs text-slate-400">
                    This Stage II screen does not approve or rate accomplishments. Submissions are locked and used as input for Stage III evaluation.
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <button type="button"
                        data-close-modal="review-modal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>

                <button
                    type="button"
                    id="endorseBtn"
                    class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition"
                >
                    Approve
                </button>
            </div>
        </div>
    </div>

    <!-- SMPOR PREVIEW MODAL -->
    <div id="smpor-preview-modal"
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">SMPOR (Monitoring Summary)</p>
                    <h3 class="text-lg font-semibold text-white">SMPOR Preview — {{ $performancePeriod }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">
                        System-generated, monitoring-only. Derived from MPOR (submitted + rated ORS).
                    </p>
                </div>
                <button type="button" data-close-modal="smpor-preview-modal" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-4 text-sm text-slate-200">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Employee</p>
                        <p id="sp-employee" class="mt-1 font-semibold">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Office / Unit</p>
                        <p id="sp-office" class="mt-1 font-semibold">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Period</p>
                        <p class="mt-1 font-semibold">{{ $performancePeriod }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Source</p>
                        <p class="mt-1 font-semibold">MPOR (from submitted + rated ORS)</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-slate-950/50 text-slate-200">
                            <tr>
                                <th class="px-3 py-2 text-left">Major Output (MFO)</th>
                                <th class="px-3 py-2 text-left">Function</th>
                                <th class="px-3 py-2 text-center">Total Quantity (Monitoring)</th>
                                <th class="px-3 py-2 text-center">Total Quality Points</th>
                                <th class="px-3 py-2 text-center">Total Timeliness Points</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach ($smporSummary as $r)
                                <tr class="hover:bg-slate-900/40">
                                    <td class="px-3 py-2 text-slate-200">{{ $r['mfo'] }}</td>
                                    <td class="px-3 py-2 text-slate-300">{{ $r['function'] }}</td>
                                    <td class="px-3 py-2 text-center font-semibold">{{ $r['qty'] }}</td>
                                    <td class="px-3 py-2 text-center font-semibold">{{ $r['q_pts'] }}</td>
                                    <td class="px-3 py-2 text-center font-semibold">{{ $r['t_pts'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-slate-800 bg-slate-950/40">
                            <tr>
                                <td class="px-3 py-2 text-left font-semibold text-slate-200" colspan="2">Grand Totals</td>
                                <td class="px-3 py-2 text-center font-semibold text-white">{{ $smporTotals['qty'] }}</td>
                                <td class="px-3 py-2 text-center font-semibold text-white">{{ $smporTotals['q_pts'] }}</td>
                                <td class="px-3 py-2 text-center font-semibold text-white">{{ $smporTotals['t_pts'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p class="text-xs text-slate-400">
                    Computation basis (Stage II only): Quality Points = Quantity × Supervisor Quality (Monitoring) • Timeliness Points = Quantity × Supervisor Timeliness (Monitoring).
                    This is monitoring-only and does not represent final performance ratings.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <button type="button"
                        data-close-modal="smpor-preview-modal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- IPCR PREVIEW MODAL -->
    <div id="ipcr-preview-modal"
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR Accomplishment Report</p>
                    <h3 class="text-lg font-semibold text-white">IPCR Accomplishment Preview — {{ $performancePeriod }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">
                        System-generated accomplishments derived from SMPOR consolidation (Stage II). Read-only.
                    </p>
                </div>
                <button type="button" data-close-modal="ipcr-preview-modal" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-4 text-sm text-slate-200">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Employee</p>
                        <p id="ip-employee" class="mt-1 font-semibold">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Office / Unit</p>
                        <p id="ip-office" class="mt-1 font-semibold">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Period</p>
                        <p class="mt-1 font-semibold">{{ $performancePeriod }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Source</p>
                        <p class="mt-1 font-semibold">SMPOR (monitoring consolidation)</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-slate-950/50 text-slate-200">
                            <tr>
                                <th class="px-3 py-2 text-left">Major Output (MFO)</th>
                                <th class="px-3 py-2 text-left">Function</th>
                                <th class="px-3 py-2 text-left">Accomplishment Summary (Monitoring)</th>
                                <th class="px-3 py-2 text-left">Evidence</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach ($ipcrAccomplishments as $r)
                                <tr class="hover:bg-slate-900/40">
                                    <td class="px-3 py-2 text-slate-200">{{ $r['mfo'] }}</td>
                                    <td class="px-3 py-2 text-slate-300">{{ $r['function'] }}</td>
                                    <td class="px-3 py-2 text-slate-200">{{ $r['summary'] }}</td>
                                    <td class="px-3 py-2">
                                        <span class="rounded-md border border-slate-700 bg-slate-950/40 px-2 py-1 text-[11px] text-slate-200">
                                            {{ $r['evidence'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="text-xs text-slate-400">
                    Final IPCR rating is completed in Stage III. This preview is monitoring-derived and read-only.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <button type="button"
                        data-close-modal="ipcr-preview-modal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const body = document.body;

                const reviewModal = document.getElementById('review-modal');
                const smporModal = document.getElementById('smpor-preview-modal');
                const ipcrModal = document.getElementById('ipcr-preview-modal');

                const rvEmployee = document.getElementById('rv-employee');
                const rvOffice = document.getElementById('rv-office');
                const rvSubmitted = document.getElementById('rv-submitted');
                const rvRemarks = document.getElementById('rv-remarks');

                const spEmployee = document.getElementById('sp-employee');
                const spOffice = document.getElementById('sp-office');

                const ipEmployee = document.getElementById('ip-employee');
                const ipOffice = document.getElementById('ip-office');

                function openModal(modalEl) {
                    if (!modalEl) return;
                    modalEl.classList.remove('hidden');
                    modalEl.classList.add('flex');
                    body.classList.add('overflow-hidden');
                }

                function closeModal(modalEl) {
                    if (!modalEl) return;
                    modalEl.classList.add('hidden');
                    modalEl.classList.remove('flex');

                    // Only remove overflow lock if no other modal is open
                    const anyOpen = [reviewModal, smporModal, ipcrModal].some(m => m && !m.classList.contains('hidden'));
                    if (!anyOpen) body.classList.remove('overflow-hidden');
                }

                // Open handlers
                document.querySelectorAll('[data-open-modal]').forEach((btn) => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();

                        const modalId = btn.getAttribute('data-open-modal');
                        const modalEl = document.getElementById(modalId);
                        if (!modalEl) return;

                        // When opening review modal, hydrate context
                        if (modalId === 'review-modal') {
                            const employee = btn.getAttribute('data-employee') || '--';
                            const office = btn.getAttribute('data-office') || '--';
                            const submitted = btn.getAttribute('data-submitted') || '--';
                            const remarks = btn.getAttribute('data-remarks') || '—';

                            if (rvEmployee) rvEmployee.textContent = employee;
                            if (rvOffice) rvOffice.textContent = office;
                            if (rvSubmitted) rvSubmitted.textContent = submitted;
                            if (rvRemarks) rvRemarks.textContent = remarks;

                            // Propagate to preview modals too (so user can open previews from inside review)
                            if (spEmployee) spEmployee.textContent = employee;
                            if (spOffice) spOffice.textContent = office;
                            if (ipEmployee) ipEmployee.textContent = employee;
                            if (ipOffice) ipOffice.textContent = office;
                        }

                        // If opening preview modals directly, reuse last known values from review context
                        if (modalId === 'smpor-preview-modal') {
                            if (spEmployee && spEmployee.textContent.trim() === '--' && rvEmployee) spEmployee.textContent = rvEmployee.textContent || '--';
                            if (spOffice && spOffice.textContent.trim() === '--' && rvOffice) spOffice.textContent = rvOffice.textContent || '--';
                        }
                        if (modalId === 'ipcr-preview-modal') {
                            if (ipEmployee && ipEmployee.textContent.trim() === '--' && rvEmployee) ipEmployee.textContent = rvEmployee.textContent || '--';
                            if (ipOffice && ipOffice.textContent.trim() === '--' && rvOffice) ipOffice.textContent = rvOffice.textContent || '--';
                        }

                        openModal(modalEl);
                    });
                });

                // Close buttons
                document.querySelectorAll('[data-close-modal]').forEach((btn) => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const modalId = btn.getAttribute('data-close-modal');
                        closeModal(document.getElementById(modalId));
                    });
                });

                // Backdrop click close
                [reviewModal, smporModal, ipcrModal].forEach((m) => {
                    m?.addEventListener('click', (e) => {
                        if (e.target === m) closeModal(m);
                    });
                });

                // ESC closes top-most open modal (preview first, then review)
                document.addEventListener('keydown', (e) => {
                    if (e.key !== 'Escape') return;

                    if (ipcrModal && !ipcrModal.classList.contains('hidden')) return closeModal(ipcrModal);
                    if (smporModal && !smporModal.classList.contains('hidden')) return closeModal(smporModal);
                    if (reviewModal && !reviewModal.classList.contains('hidden')) return closeModal(reviewModal);
                });
            });

            document.getElementById('endorseBtn').addEventListener('click', function () {

            const button = this;

            // Prevent double click
            if (button.classList.contains('loading')) return;

            button.classList.add('loading');
            button.disabled = true;

            // Save original text
            button.dataset.originalText = button.innerHTML;

            // Replace with spinner + text
            button.innerHTML = `
                <div class="flex items-center gap-2 justify-center">
                    <svg class="animate-spin h-4 w-4 text-slate-200"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8v8z">
                        </path>
                    </svg>
                    Processing...
                </div>
            `;

            // 🔹 Simulate request (remove this in production)
            setTimeout(() => {
                button.innerHTML = button.dataset.originalText;
                button.disabled = false;
                button.classList.remove('loading');
            }, 2000);

        });
        </script>
    @endpush
@endsection
