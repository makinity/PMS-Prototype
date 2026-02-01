@extends('layouts.supervisor')

@section('main-content')
    <style>
        #ors-calendar .fc-col-header-cell { background-color: rgba(15, 23, 42, 0.85); }
        #ors-calendar .fc-col-header-cell-cushion,
        #ors-calendar .fc-daygrid-day-number { color: #e2e8f0; }

        .status-chip{
            display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .55rem;border-radius:9999px;border-width:1px;
            font-size:.75rem;font-weight:600
        }
        .status-dot{width:.55rem;height:.55rem;border-radius:9999px}
    </style>

    <section class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-white">Daily ORS Monitoring</h1>
            <p class="text-sm text-slate-400">
                Supervisors monitor submitted daily outputs using the Output Rating Sheet (ORS).
                <span class="block">Only <span class="font-semibold text-slate-200">Submitted (Locked)</span> ORS entries appear here and can be rated.</span>
            </p>
        </div>

        <!-- Legend (submitted + missing only, aligned with manual ORS) -->
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-slate-400">ORS Monitoring Legend</p>
                    <p class="text-[11px] text-slate-500">
                        Visibility: submitted outputs only · Rating: submitted only
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
                    <span class="status-chip border-blue-500/60 bg-blue-500/10 text-blue-100">
                        <span class="status-dot bg-blue-500"></span>
                        Submitted (Locked)
                    </span>

                    <span class="status-chip border-red-500/60 bg-red-500/10 text-red-100">
                        <span class="status-dot bg-red-500"></span>
                        Missing / Overdue
                    </span>
                </div>
            </div>

            <p class="mt-3 text-[11px] text-slate-400">
                This screen mirrors the manual ORS process: the supervisor reviews outputs only after the employee submits the ORS entry.
                Draft / in-progress work remains visible only to the employee in ORS.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="mb-3 flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">ORS Calendar</h2>
                    <p class="text-sm text-slate-400">
                        Click an entry to view details and record monitoring rating. Missing / Overdue indicates no submitted ORS entry for the day.
                    </p>
                </div>
            </div>
            <div id="ors-calendar"></div>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 text-xs text-slate-400">
            ORS Monitoring is daily coaching and documentation. Final IPCR evaluation occurs in Stage III.
        </div>

        <!-- Monitoring Detail Modal -->
        <div id="ors-monitoring-modal"
             class="ors-modal fixed inset-0 z-[60] hidden items-center justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6"
             role="dialog"
             aria-modal="true">
            <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 shadow-xl overflow-hidden">
                <div class="flex max-h-[86vh] flex-col">
                    <div class="border-b border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-white">ORS Monitoring Detail</h2>
                                <p class="text-xs text-slate-400">
                                    Supervisor review (manual ORS equivalent). Rating applies only to Submitted (Locked) entries.
                                </p>
                            </div>
                            <button type="button"
                                    onclick="closeOrsModal('ors-monitoring-modal')"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white">
                                x
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-5">
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                            <!-- LEFT -->
                            <div class="space-y-5">
                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div>
                                            <p class="text-xs text-slate-400">Ratee (Employee)</p>
                                            <p id="monitoringEmployee" class="font-semibold text-white">--</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400">Office / Unit</p>
                                            <p id="monitoringOffice" class="text-white">--</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400">Date Submitted</p>
                                            <p id="monitoringDate" class="text-white">--</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3 text-sm text-slate-200">
                                    <div>
                                        <p class="text-xs text-slate-400">Major Output (MFO)</p>
                                        <p id="monitoringMajorOutput" class="text-white">--</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400">Output Type</p>
                                        <p id="monitoringOutput">--</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400">Actual Accomplishment</p>
                                        <p id="monitoringAccomplishment" class="text-slate-100">--</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                        <p class="text-xs text-slate-400">ORS Reference / Request ID</p>
                                        <p id="monitoringRequestId" class="text-slate-100 mt-1">--</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                        <p class="text-xs text-slate-400">Time Spent</p>
                                        <p id="monitoringDuration" class="text-slate-100 mt-1">--</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 sm:col-span-2">
                                        <p class="text-xs text-slate-400">Evidence Attached</p>
                                        <p id="monitoringEvidence" class="text-emerald-300 font-semibold mt-1">--</p>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Quantity (employee-declared)</p>
                                    <p id="monitoringQuantity" class="mt-1 text-base font-semibold text-white">--</p>
                                    <p class="mt-2 text-[11px] text-slate-400">
                                        Declared by employee during ORS submission. Supervisor does not rate Quantity.
                                    </p>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Status</p>
                                    <div class="mt-2 inline-flex flex-col gap-1">
                                        <span id="monitoringStatus" class="status-chip border border-slate-700 bg-slate-800 text-slate-200"></span>
                                        <span id="monitoringStatusDetail" class="text-xs text-slate-300"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT -->
                            <div class="space-y-5">
                                <!-- Rating Basis (compact + opens sub-modal) -->
                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3 text-xs text-slate-200">
                                    <p class="text-[11px] uppercase text-slate-400">Rating basis</p>
                                    <p class="text-[11px] text-slate-400">
                                        Based on Stage I standards for this success indicator. Employee declares Quantity; supervisor rates Quality & Timeliness.
                                    </p>

                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <p id="ratingBasisIndicator" class="text-sm font-semibold text-white">--</p>

                                        <button id="openRatingBasisBtn"
                                                type="button"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-700 bg-slate-900/50 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                            View basis for rating
                                        </button>
                                    </div>

                                    <p class="text-[11px] text-slate-500">
                                        The standards below are reference-only and come from Stage I approval.
                                    </p>
                                </div>

                                <!-- Monitoring Rating -->
                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3 text-xs text-slate-200">
                                    <p class="text-[11px] uppercase text-slate-400">Monitoring Rating (ORS Format)</p>

                                    <div id="rating-locked-note" class="mt-2 text-[11px] text-slate-400">
                                        Rating is available only for Submitted (Locked) ORS entries.
                                    </div>

                                    <!-- NOTE: Efficiency rating REMOVED (Supervisor rates Quality & Timeliness only) -->
                                    <div id="rating-controls" class="mt-4 hidden space-y-4">
                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            <div>
                                                <label class="text-xs text-slate-300">Quality</label>
                                                <select id="ratingQual"
                                                        class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm text-slate-200">
                                                    <option value="">--</option>
                                                    <option>5</option><option>4</option><option>3</option><option>2</option><option>1</option>
                                                </select>
                                                <p class="mt-2 text-[11px] text-slate-500">Rate against Stage I Quality standards.</p>
                                            </div>
                                            <div>
                                                <label class="text-xs text-slate-300">Timeliness</label>
                                                <select id="ratingTime"
                                                        class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm text-slate-200">
                                                    <option value="">--</option>
                                                    <option>5</option><option>4</option><option>3</option><option>2</option><option>1</option>
                                                </select>
                                                <p class="mt-2 text-[11px] text-slate-500">Rate against Stage I Timeliness standards.</p>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="text-xs text-slate-300">Remarks</label>
                                            <textarea id="ratingRemarks"
                                                      rows="4"
                                                      class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm text-slate-200"
                                                      placeholder="Coaching notes / observations..."></textarea>
                                            <p class="mt-2 text-[11px] text-slate-400">
                                                For monitoring & coaching only. Final IPCR rating is completed in Stage III.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 text-xs text-slate-400">
                                    Tip: Use remarks for coaching notes. This ORS rating supports monitoring documentation only.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('supervisor.ors.export.pdf') }}"
                               class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                                Export PDF
                            </a>

                            <!-- Close removed here; Save is primary. Close still available via "x", ESC, and backdrop click -->
                            <button id="saveMonitoringRatingBtn"
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                                <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                <span data-button-label>Save Rating</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Basis Sub-Modal (centered + filter: Quality/Efficiency/Timeliness) -->
        <div id="ratingBasisModal"
             class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/70 px-4 py-6"
             role="dialog"
             aria-modal="true">
            <div class="w-full max-w-4xl overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
                <div class="flex max-h-[75vh] flex-col">
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-800 px-6 py-4">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Rating Basis</h3>
                            <p id="basisModalIndicator" class="mt-1 text-xs text-slate-400">--</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-[11px] uppercase tracking-wide text-slate-400" for="basisFilter">Show</label>
                            <select id="basisFilter"
                                    style="background:#0f172a;color:#e5e7eb;"
                                    class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm text-slate-200">
                                <option value="qual">Quality (Q)</option>
                                <option value="eff">Efficiency (E)</option>
                                <option value="time">Timeliness (T)</option>
                            </select>
                        </div>
                        <button type="button"
                                id="closeRatingBasisBtn"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white">
                            <span class="sr-only">Close rating basis</span>
                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                            </svg>
                        </button>
                    </div>
                    <div id="basisModalBody" class="flex-1 overflow-y-auto px-6 py-5 text-sm text-slate-200"></div>
                    <div class="border-t border-slate-800 bg-slate-900/80 px-6 py-3 text-[11px] text-slate-400">
                        <div class="flex items-center justify-between gap-3">
                            <p>Stage I standards are reference-only. Efficiency is advisory; supervisors rate Quality & Timeliness.</p>
                            <button type="button"
                                    id="basisDoneBtn"
                                    class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-200 hover:bg-slate-800"
                                    onclick="closeBasis();">
                                Done
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('ors-monitoring-modal');
                const employeeEl = document.getElementById('monitoringEmployee');
                const officeEl = document.getElementById('monitoringOffice');
                const dateEl = document.getElementById('monitoringDate');
                const majorOutputEl = document.getElementById('monitoringMajorOutput');
                const outputEl = document.getElementById('monitoringOutput');
                const accomplishmentEl = document.getElementById('monitoringAccomplishment');
                const requestIdEl = document.getElementById('monitoringRequestId');
                const durationEl = document.getElementById('monitoringDuration');
                const evidenceEl = document.getElementById('monitoringEvidence');
                const statusEl = document.getElementById('monitoringStatus');
                const statusDetailEl = document.getElementById('monitoringStatusDetail');
                const quantityEl = document.getElementById('monitoringQuantity');

                const ratingBasisIndicatorEl = document.getElementById('ratingBasisIndicator');

                const ratingLockedNote = document.getElementById('rating-locked-note');
                const ratingControls = document.getElementById('rating-controls');

                const saveBtn = document.getElementById('saveMonitoringRatingBtn');

                // Basis sub-modal
                const basisModal = document.getElementById('ratingBasisModal');
                const basisBody = document.getElementById('basisModalBody');
                const basisIndicator = document.getElementById('basisModalIndicator');
                const basisFilter = document.getElementById('basisFilter');
                const openBasisBtn = document.getElementById('openRatingBasisBtn');
                const closeBasisBtn = document.getElementById('closeRatingBasisBtn');
                const basisDoneBtn = document.getElementById('basisDoneBtn');

                const STATUS_META = {
                    submitted: {
                        label: 'Submitted (Locked)',
                        detail: 'Submitted output is ready for monitoring rating.',
                        color: '#3b82f6',
                        badge: 'border-blue-500/60 bg-blue-500/10 text-blue-100'
                    },
                    missing: {
                        label: 'Missing / Overdue',
                        detail: 'No submitted ORS entry for this date.',
                        color: '#ef4444',
                        badge: 'border-red-500/60 bg-red-500/10 text-red-100',
                        muted: true
                    }
                };

                // Stage I standards (Q/E/T reference). Supervisor rates Q + T only (Efficiency advisory only).
                const INDICATOR_STANDARDS = {
                    'Same-day verification of OTC transactions': {
                        qual: {
                            5: 'No errors / no rework',
                            4: 'Minor errors, no rework needed',
                            3: 'Some errors, minimal rework',
                            2: 'Frequent errors, rework required',
                            1: 'Major errors, output unacceptable'
                        },
                        eff: {
                            5: 'Finished within expected duration; smooth workflow',
                            4: 'Minor delays but within expected duration',
                            3: 'Some delays; slightly exceeds expected duration',
                            2: 'Frequent delays; exceeds expected duration',
                            1: 'Severe delays; far beyond expected duration'
                        },
                        time: {
                            5: 'Completed within same working day',
                            4: 'Completed same day with minor delay',
                            3: 'Completed end-of-day / near cutoff',
                            2: 'Completed next working day',
                            1: 'Beyond next working day'
                        }
                    },
                    'All e-bank transactions scanned and encoded daily': {
                        qual: {
                            5: 'Clear scans + accurate encoding + complete indexing',
                            4: 'Minor scan/encoding issues, still acceptable',
                            3: 'Some missing/unclear pages, needs minor correction',
                            2: 'Frequent missing/unclear pages, rework required',
                            1: 'Poor quality, cannot be validated'
                        },
                        eff: {
                            5: 'Finished within expected duration; smooth workflow',
                            4: 'Minor delays but within expected duration',
                            3: 'Some delays; slightly exceeds expected duration',
                            2: 'Frequent delays; exceeds expected duration',
                            1: 'Severe delays; far beyond expected duration'
                        },
                        time: {
                            5: 'Completed same working day',
                            4: 'Completed same day with minor delay',
                            3: 'Completed end-of-day / near cutoff',
                            2: 'Completed next working day',
                            1: 'Beyond next working day'
                        }
                    }
                };

                /**
                 * DEMO LOCKED DATASET (Stage II) — Supervisor sees SUBMITTED only
                 * Employee: Ramon Reyes
                 * - Jan 2, 2026: Submitted (Locked) — Same-day verification of OTC transactions
                 * - Jan 4, 2026: Submitted (Locked) — All e-bank transactions scanned and encoded daily
                 */
                const tasks = [
                    {
                        id: 'task-jan-02',
                        date: '2026-01-02',
                        dateLabel: 'January 2, 2026',
                        employee: 'Ramon Reyes',
                        office: 'Revenue Collection Unit',
                        output: 'Official Receipt (OR)',
                        uwpOutput: 'Processing of Over-the-Counter Revenue Transactions',
                        accomplishment: 'Same-day verification of OTC transactions',
                        duration: '2h 00m',
                        evidence: true,
                        requestId: 'REQ-2026-002',
                        quantity: '18 transactions',
                        status: 'submitted'
                    },
                    {
                        id: 'task-jan-04',
                        date: '2026-01-04',
                        dateLabel: 'January 4, 2026',
                        employee: 'Ramon Reyes',
                        office: 'Revenue Collection Unit',
                        output: 'Bank Statement Form (BSF-01)',
                        uwpOutput: 'E-Bank Scanning and Encoding of Revenue Transactions',
                        accomplishment: 'All e-bank transactions scanned and encoded daily',
                        duration: '1h 30m',
                        evidence: true,
                        requestId: 'REQ-2026-004',
                        quantity: '35 transactions',
                        status: 'submitted'
                    }
                ];

                function setButtonLoading(button, isLoading, loadingText) {
                    if (!button) return;
                    const label = button.querySelector('[data-button-label]');
                    const spinner = button.querySelector('[data-button-spinner]');
                    if (label && !button.dataset.originalLabel) {
                        button.dataset.originalLabel = label.textContent.trim();
                    }

                    if (isLoading) {
                        button.disabled = true;
                        button.classList.add('opacity-80', 'cursor-wait');
                        spinner?.classList.remove('hidden');
                        if (label && loadingText) label.textContent = loadingText;
                    } else {
                        button.disabled = false;
                        button.classList.remove('opacity-80', 'cursor-wait');
                        spinner?.classList.add('hidden');
                        if (label && button.dataset.originalLabel) label.textContent = button.dataset.originalLabel;
                    }
                }

                // Calendar
                const calendarEl = document.getElementById('ors-calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    initialDate: '2026-01-01',
                    height: 'auto',
                    headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                    dayMaxEventRows: 3,
                    editable: false,
                    selectable: true,
                    events: tasks.map((task) => {
                        const meta = STATUS_META[task.status] || STATUS_META.submitted;
                        return {
                            id: task.id,
                            title: task.accomplishment,
                            start: task.date,
                            color: meta.color,
                            extendedProps: { ...task, label: meta.label, detail: meta.detail, badge: meta.badge }
                        };
                    }),
                    eventContent(arg) {
                        const meta = arg.event.extendedProps;
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('text-[11px]', 'leading-tight', 'px-1', 'py-[2px]');
                        const title = (arg.event.title || '').length > 34 ? arg.event.title.substring(0, 31) + '...' : arg.event.title;

                        wrapper.innerHTML = `
                            <div class="flex flex-col gap-1">
                                <span class="text-slate-100 font-semibold">${title}</span>
                                <span class="rounded-full border px-2 py-[1px] text-[10px]" style="color:${meta.color}; border-color:${meta.color};">${meta.label}</span>
                            </div>
                        `;
                        return { domNodes: [wrapper] };
                    },
                    dateClick(info) {
                        const found = tasks.find((t) => t.date === info.dateStr);
                        if (found) {
                            openMonitoringModal(found);
                            return;
                        }

                        openMonitoringModal({
                            employee: 'Ramon Reyes',
                            office: 'Revenue Collection Unit',
                            date: info.dateStr,
                            dateLabel: info.dateStr,
                            uwpOutput: '--',
                            output: 'No submitted ORS entry',
                            accomplishment: 'No submitted ORS entry for this date.',
                            duration: '--',
                            evidence: false,
                            requestId: '--',
                            quantity: '--',
                            status: 'missing'
                        });
                    },
                    eventClick(info) {
                        openMonitoringModal(info.event.extendedProps);
                    }
                });
                calendar.render();

                // Basis rendering
                const BASIS_FILTERS = {
                    qual: { label: 'QUALITY (Q)', hint: 'Supervisor rates Quality.' },
                    eff:  { label: 'EFFICIENCY (E)', hint: 'Advisory reference only.' },
                    time: { label: 'TIMELINESS (T)', hint: 'Supervisor rates Timeliness.' }
                };

                function renderBasisRows(entries) {
                    return Object.entries(entries || {})
                        .sort((a, b) => Number(b[0]) - Number(a[0]))
                        .map(([score, desc]) => `
                            <div class="flex items-start gap-3 rounded-lg border border-slate-800 bg-slate-950/40 p-3">
                                <span class="min-w-[28px] h-7 inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-950/60 text-xs font-semibold text-white">${score}</span>
                                <p class="text-sm leading-relaxed text-slate-300">${desc}</p>
                            </div>
                        `).join('');
                }

                function renderBasisSingle(indicatorText, filterKey) {
                    const standards = INDICATOR_STANDARDS[indicatorText];
                    if (!standards) {
                        return `<p class="text-sm text-slate-500">No Stage I standards found for this indicator in the demo dataset.</p>`;
                    }

                    const entries = standards[filterKey];
                    const meta = BASIS_FILTERS[filterKey] || { label: 'Standards', hint: '' };
                    if (!entries) {
                        return `<p class="text-sm text-slate-500">${meta.label} standards are not available for this indicator.</p>`;
                    }

                    const rows = renderBasisRows(entries);
                    return `
                        <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                            <div class="mb-3">
                                <p class="text-[11px] uppercase tracking-wide text-slate-400">${meta.label}</p>
                                <p class="mt-1 text-[11px] text-slate-500">${meta.hint}</p>
                            </div>
                            <div class="space-y-3">
                                ${rows}
                            </div>
                        </div>
                    `;
                }

                let currentModalData = null;

                function updateRatingBasis(data) {
                    const indicator = data.status === 'submitted'
                        ? (data.accomplishment || 'No submitted ORS entry')
                        : 'No submitted ORS entry';

                    ratingBasisIndicatorEl.textContent = indicator;

                    const hasStandards = data.status === 'submitted' && !!INDICATOR_STANDARDS[indicator];
                    if (openBasisBtn) {
                        openBasisBtn.disabled = !hasStandards;
                        openBasisBtn.classList.toggle('opacity-60', !hasStandards);
                        openBasisBtn.classList.toggle('cursor-not-allowed', !hasStandards);
                    }
                }

                function openMonitoringModal(data) {
                    if (!modal) return;
                    currentModalData = data;

                    employeeEl.textContent = data.employee || 'Ramon Reyes';
                    officeEl.textContent = data.office || 'Revenue Collection Unit';
                    dateEl.textContent = data.dateLabel || data.date || '--';

                    majorOutputEl.textContent = data.uwpOutput || '--';
                    outputEl.textContent = data.output || '--';
                    accomplishmentEl.textContent = data.accomplishment || '--';

                    requestIdEl.textContent = data.requestId || '--';
                    durationEl.textContent = data.duration || '--';
                    evidenceEl.textContent = data.evidence ? 'Evidence attached' : 'No evidence (read-only)';
                    quantityEl.textContent = data.quantity || '--';

                    const meta = STATUS_META[data.status] || STATUS_META.missing;
                    statusEl.textContent = meta.label;
                    statusEl.className = `status-chip ${meta.badge}`;
                    statusDetailEl.textContent = meta.detail || '';

                    updateRatingBasis(data);

                    // Rateable only if submitted
                    const rateable = data.status === 'submitted';
                    ratingLockedNote.classList.toggle('hidden', rateable);
                    ratingControls.classList.toggle('hidden', !rateable);

                    // Clear inputs when switching entries (demo clean)
                    document.getElementById('ratingQual').value = '';
                    document.getElementById('ratingTime').value = '';
                    document.getElementById('ratingRemarks').value = '';

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                window.closeOrsModal = (modalId) => {
                    const m = document.getElementById(modalId);
                    if (!m) return;
                    m.classList.add('hidden');
                    m.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                };

                // Backdrop close (monitoring)
                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) closeOrsModal('ors-monitoring-modal');
                });

                // ESC close (monitoring + basis)
                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') return;

                    if (basisModal && !basisModal.classList.contains('hidden')) {
                        closeBasis();
                        return;
                    }

                    if (!modal.classList.contains('hidden')) {
                        closeOrsModal('ors-monitoring-modal');
                    }
                });

                function renderBasis(indicatorText, filterKey = 'qual') {
                    basisBody.innerHTML = renderBasisSingle(indicatorText, filterKey);
                }

                function openBasis() {
                    if (!currentModalData) return;
                    const indicator = currentModalData.accomplishment || '';
                    if (!INDICATOR_STANDARDS[indicator]) return;

                    basisIndicator.textContent = indicator;
                    if (basisFilter) basisFilter.value = 'qual';
                    renderBasis(indicator, 'qual');

                    basisModal?.classList.remove('hidden');
                    basisModal?.classList.add('flex');
                }

                function closeBasis() {
                    basisModal?.classList.add('hidden');
                    basisModal?.classList.remove('flex');
                }

                openBasisBtn?.addEventListener('click', openBasis);
                closeBasisBtn?.addEventListener('click', closeBasis);
                basisDoneBtn?.addEventListener('click', closeBasis);

                basisModal?.addEventListener('click', (event) => {
                    if (event.target === basisModal) closeBasis();
                });

                basisFilter?.addEventListener('change', () => {
                    const indicator = basisIndicator.textContent || '';
                    renderBasis(indicator, basisFilter.value);
                });

                // Demo-only: Save rating (blue, with loading spinner)
                saveBtn?.addEventListener('click', () => {
                    if (!currentModalData || currentModalData.status !== 'submitted') {
                        alert('Rating is available only for Submitted (Locked) entries.');
                        return;
                    }

                    const q = document.getElementById('ratingQual').value;
                    const t = document.getElementById('ratingTime').value;

                    if (!q || !t) {
                        alert('Select Quality and Timeliness ratings.');
                        return;
                    }

                    setButtonLoading(saveBtn, true, 'Saving...');
                    setTimeout(() => {
                        setButtonLoading(saveBtn, false);
                        alert('Demo: Monitoring rating saved.');
                    }, 600);
                });
            });
        </script>
    @endpush
@endsection
