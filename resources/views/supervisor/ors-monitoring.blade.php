@extends('layouts.supervisor')

@section('main-content')
    @php
        $submittedEntries = $submittedEntries ?? collect();

        $calendarTasks = $submittedEntries
            ->filter(fn ($entry) => strtolower((string) ($entry->status ?? '')) === 'submitted')
            ->map(function ($entry) {
                $workDate = $entry->work_date;
                $dateValue = null;
                $dateLabel = '--';

                if ($workDate instanceof \Carbon\CarbonInterface) {
                    $dateValue = $workDate->format('Y-m-d');
                    $dateLabel = $workDate->format('F j, Y');
                } elseif (is_string($workDate) && trim($workDate) !== '') {
                    $trimmed = trim($workDate);
                    $dateValue = substr($trimmed, 0, 10);
                    $dateLabel = $trimmed;
                }

                $officeName = optional(optional($entry->employee)->office)->name;
                $officeValue = $officeName ?: (optional($entry->employee)->office_id ? ('Office #' . optional($entry->employee)->office_id) : '--');

                return [
                    'id' => $entry->id,
                    'date' => $dateValue,
                    'dateLabel' => $dateLabel,
                    'employee' => optional($entry->employee)->name ?? '—',
                    'office' => $officeValue,
                    'output' => optional($entry->ipcrItem)->output_title ?? (optional($entry->ipcrItem)->output_type ?? '--'),
                    'uwpOutput' => optional($entry->ipcrItem)->output_title ?? '--',
                    'accomplishment' => optional($entry->ipcrItem)->indicator_text ?? '--',
                    'duration' => $entry->duration ?? ($entry->duration_minutes ?? '--'),
                    'evidence' => ($entry->evidences_count ?? 0) > 0,
                    'requestId' => $entry->request_id ?? ('ORS-' . $entry->id),
                    'quantity' => $entry->quantity ?? '--',
                    'status' => 'submitted',
                    'quality_rating' => optional($entry->monitoring)->quality_rating,
                    'timeliness_rating' => optional($entry->monitoring)->timeliness_rating,
                    'remarks' => optional($entry->monitoring)->remarks,
                ];
            })
            ->values();
    @endphp

    <style>
        #ors-calendar .fc-col-header-cell { background-color: rgba(15, 23, 42, 0.85); }
        #ors-calendar .fc-col-header-cell-cushion,
        #ors-calendar .fc-daygrid-day-number { color: #e2e8f0; }
        #ors-calendar .fc-daygrid-event {
            margin: 4px 6px;
            border-radius: 9999px;
            padding: 2px 8px;
            font-size: 11px;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #ors-calendar .fc-daygrid-event.ors-summary-event {
            background: rgba(59, 130, 246, 0.18);
            border: 1px solid rgba(59, 130, 246, 0.45);
            color: #dbeafe;
        }

        .status-chip{
            display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .55rem;border-radius:9999px;border-width:1px;
            font-size:.75rem;font-weight:600
        }
        .status-dot{width:.55rem;height:.55rem;border-radius:9999px}
    </style>

    <section class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-white">Daily ORS Monitoring</h1>
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
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="mb-3 flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">ORS Calendar</h2>
                </div>
            </div>
            <div id="ors-calendar"></div>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 text-xs text-slate-400">
            ORS Monitoring is daily coaching and documentation. Final IPCR evaluation occurs in Stage III.
        </div>

        <!-- Day List Modal -->
        <div id="ors-day-list-modal"
             class="ors-modal fixed inset-0 z-[61] hidden items-center justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6"
             role="dialog"
             aria-modal="true">
            <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 shadow-xl overflow-hidden">
                <div class="flex max-h-[84vh] flex-col">
                    <div class="border-b border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-white">Submitted Entries</h2>
                                <p id="dayListDateLabel" class="text-xs text-slate-400">--</p>
                            </div>
                            <button id="closeDayListTopBtn"
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white">
                                x
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-5">
                        <div id="dayListEntries" class="space-y-3"></div>
                    </div>

                    <div class="border-t border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-center justify-end">
                            <button id="closeDayListBottomBtn"
                                    type="button"
                                    class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty Date Message Modal -->
        <div id="ors-empty-date-modal"
             class="ors-modal fixed inset-0 z-[62] hidden items-center justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6"
             role="dialog"
             aria-modal="true">
            <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 shadow-xl overflow-hidden">
                <div class="border-b border-slate-800 bg-slate-900/80 px-5 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold text-white">No Submitted Entries</h2>
                            <p id="emptyDateLabel" class="text-xs text-slate-400">--</p>
                        </div>
                        <button id="closeEmptyDateTopBtn"
                                type="button"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white">
                            x
                        </button>
                    </div>
                </div>
                <div class="px-5 py-4 text-sm text-slate-300">
                    No submitted entries for this date.
                </div>
                <div class="border-t border-slate-800 bg-slate-900/80 px-5 py-3 text-right">
                    <button id="closeEmptyDateBottomBtn"
                            type="button"
                            class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
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
                            <form id="saveMonitoringForm" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="quality_rating" id="formQualityRating">
                                <input type="hidden" name="timeliness_rating" id="formTimelinessRating">
                                <input type="hidden" name="remarks" id="formRemarks">
                            </form>


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

        <!-- Rating Basis Sub-Modal (Performance Standards Q/E/T) -->
<div id="ratingBasisModal"
     class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/80 px-4 py-6 sm:px-6"
     role="dialog"
     aria-modal="true">

    <!-- Outer modal card -->
    <div class="w-full max-w-5xl overflow-hidden rounded-3xl border border-slate-700/60 bg-slate-950 shadow-2xl">

        <div class="flex max-h-[85vh] flex-col">

            <!-- Header (sticky-like look) -->
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-800/70 bg-slate-950 px-6 py-5">

                <div class="space-y-1">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Standards (Q/E/T)</p>
                    <h3 class="text-xl font-semibold text-white">Performance Standards</h3>

                    <div class="pt-1 space-y-1">
                        <p id="basisModalMfo" class="text-sm text-slate-300">
                            <span class="text-slate-400">MFO:</span> --
                        </p>
                        <p id="basisModalIndicator" class="text-sm text-slate-300">
                            <span class="text-slate-400">Indicator:</span> --
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] uppercase tracking-wide text-slate-400">Show</span>
                        <div class="relative">
                            <select id="basisFilter"
                            style="background:#0f172a;color:#e5e7eb;"
                                    class="h-10 min-w-[200px] rounded-xl border border-slate-700/70 bg-slate-950/40 px-4 pr-10 text-sm font-medium text-slate-100 shadow-inner shadow-black/40 focus:border-blue-500/60 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                <option value="qual">Quality (Q)</option>
                                <option value="eff">Efficiency (E)</option>
                                <option value="time">Timeliness (T)</option>
                            </select>
                        </div>
                    </div>

                    <button type="button"
                            id="closeRatingBasisBtn"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-700/70 bg-slate-950/30 text-slate-300 transition hover:bg-slate-800/70 hover:text-white">
                        <span class="sr-only">Close rating basis</span>
                        <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto px-6 py-6 text-sm text-slate-200">

                <div class="overflow-hidden rounded-3xl border border-slate-700/60 bg-slate-950 shadow-inner shadow-black/50">
                    <table class="w-full text-sm text-slate-200">
                        <thead class="bg-slate-900 text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="w-20 px-6 py-4 text-left">Rating</th>
                                <th class="px-6 py-4 text-left">Quality (Q)</th>
                                <th class="px-6 py-4 text-left">Efficiency (E)</th>
                                <th class="px-6 py-4 text-left">Timeliness (T)</th>
                            </tr>
                        </thead>

                        <tbody id="basisModalBody" class="divide-y divide-slate-800">
                            <!-- JS will inject <tr> rows here -->
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Footer -->
            <div class="border-t border-slate-800/70 bg-slate-950 px-6 py-4 text-[11px] text-slate-400">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p>Stage I standards are reference-only. Efficiency is advisory; supervisors rate Quality & Timeliness.</p>
                    <button type="button"
                            id="basisDoneBtn"
                            class="rounded-xl border border-slate-700/70 bg-slate-950/30 px-5 py-2.5 text-xs font-semibold text-slate-100 transition hover:bg-slate-800/70">
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
                const saveForm = document.getElementById('saveMonitoringForm');
                const formQualityRating = document.getElementById('formQualityRating');
                const formTimelinessRating = document.getElementById('formTimelinessRating');
                const formRemarks = document.getElementById('formRemarks');
                const dayListModal = document.getElementById('ors-day-list-modal');
                const dayListDateLabel = document.getElementById('dayListDateLabel');
                const dayListEntries = document.getElementById('dayListEntries');
                const closeDayListTopBtn = document.getElementById('closeDayListTopBtn');
                const closeDayListBottomBtn = document.getElementById('closeDayListBottomBtn');
                const emptyDateModal = document.getElementById('ors-empty-date-modal');
                const emptyDateLabel = document.getElementById('emptyDateLabel');
                const closeEmptyDateTopBtn = document.getElementById('closeEmptyDateTopBtn');
                const closeEmptyDateBottomBtn = document.getElementById('closeEmptyDateBottomBtn');

                // Basis sub-modal
                const basisModal = document.getElementById('ratingBasisModal');
                const basisBody = document.getElementById('basisModalBody');
                const basisMfo = document.getElementById('basisModalMfo');
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

                const STANDARDS_BY_INDICATOR = {
                    'Same-day verification of OTC transactions': {
                        mfo: 'Processing of Over-the-Counter Revenue Transactions',
                        indicator: 'Same-day verification of OTC transactions',
                        rows: [
                            { rating: 5, q: 'Verified without discrepancies', e: '100% OTC verified', t: 'Same working day' },
                            { rating: 4, q: 'Minor verifications pending', e: '100% OTC verified', t: 'Same working day' },
                            { rating: 3, q: 'Few pending verifications', e: '95–99% verified', t: 'End of working day' },
                            { rating: 2, q: 'Several unverified', e: '<95% verified', t: 'Beyond working day' },
                            { rating: 1, q: 'Verification not done', e: 'Majority unverified', t: 'Unacceptable' },
                        ],
                    },
                    'All e-bank transactions scanned and encoded daily': {
                        mfo: 'E-Bank Scanning and Encoding of Revenue Transactions',
                        indicator: 'All e-bank transactions scanned and encoded daily',
                        rows: [
                            { rating: 5, q: 'Scanned/encoded with zero errors', e: '100% encoded same batch', t: 'Same working day' },
                            { rating: 4, q: 'Minor errors, no rework', e: '100% encoded', t: 'Same working day' },
                            { rating: 3, q: 'Some errors, minimal rework', e: '95–99% encoded', t: 'End of working day' },
                            { rating: 2, q: 'Frequent errors, rework needed', e: '<95% encoded', t: 'Beyond working day' },
                            { rating: 1, q: 'Major errors, unacceptable', e: 'Majority not encoded', t: 'Unacceptable' },
                        ],
                    },
                    'OR validation completed daily': {
                        mfo: 'Processing of Over-the-Counter Revenue Transactions',
                        indicator: 'OR validation completed daily',
                        rows: [
                            { rating: 5, q: 'Validated with zero variance', e: '100% ORs validated', t: 'Same working day' },
                            { rating: 4, q: 'Minor variance corrected', e: '100% ORs validated', t: 'Same working day' },
                            { rating: 3, q: 'Some corrections required', e: '95–99% ORs validated', t: 'End of working day' },
                            { rating: 2, q: 'Frequent corrections required', e: '<95% ORs validated', t: 'Beyond working day' },
                            { rating: 1, q: 'Validation incomplete', e: 'Majority not validated', t: 'Unacceptable' },
                        ],
                    },
                    'Retrieval logs maintained for audit purposes': {
                        mfo: 'Maintenance of revenue records and filing system',
                        indicator: 'Retrieval logs maintained for audit purposes',
                        rows: [
                            { rating: 5, q: 'Logs complete and audit-ready', e: '100% requests logged', t: 'Within set turnaround' },
                            { rating: 4, q: 'Minor omissions corrected', e: '100% requests logged', t: 'Within set turnaround' },
                            { rating: 3, q: 'Some omissions', e: '95–99% requests logged', t: 'Slight delay' },
                            { rating: 2, q: 'Frequent omissions', e: '<95% requests logged', t: 'Delayed' },
                            { rating: 1, q: 'Logs unreliable', e: 'Majority not logged', t: 'Unacceptable' },
                        ],
                    },
                };
                const tasks = @json($calendarTasks);
                const monitorBaseUrl = @json(url('/supervisor/team-tasks'));
                const byDate = tasks.reduce((carry, task) => {
                    if (!task || !task.date) return carry;
                    if (!carry[task.date]) {
                        carry[task.date] = [];
                    }
                    carry[task.date].push(task);
                    return carry;
                }, {});

                Object.keys(byDate).forEach((dateKey) => {
                    byDate[dateKey].sort((left, right) => {
                        const employeeCompare = String(left?.employee || '').localeCompare(String(right?.employee || ''));
                        if (employeeCompare !== 0) return employeeCompare;
                        return String(left?.accomplishment || '').localeCompare(String(right?.accomplishment || ''));
                    });
                });

                const summaryEvents = Object.keys(byDate).map((date) => ({
                    id: `sum-${date}`,
                    start: date,
                    allDay: true,
                    title: `${byDate[date].length} submitted`,
                    classNames: ['ors-summary-event'],
                    extendedProps: {
                        date,
                        count: byDate[date].length,
                    },
                }));

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

                function refreshBodyLock() {
                    const monitoringOpen = modal && !modal.classList.contains('hidden');
                    const dayListOpen = dayListModal && !dayListModal.classList.contains('hidden');
                    const emptyDateOpen = emptyDateModal && !emptyDateModal.classList.contains('hidden');
                    const basisOpen = basisModal && !basisModal.classList.contains('hidden');
                    document.body.classList.toggle('overflow-hidden', Boolean(monitoringOpen || dayListOpen || emptyDateOpen || basisOpen));
                }

                function formatDateLabel(dateStr) {
                    if (!dateStr) return '--';
                    const parsed = new Date(`${dateStr}T00:00:00`);
                    if (Number.isNaN(parsed.getTime())) return dateStr;
                    return parsed.toLocaleDateString([], { month: 'long', day: 'numeric', year: 'numeric' });
                }

                // Calendar
                const calendarEl = document.getElementById('ors-calendar');
                const today = new Date();
                const currentYmd = today.toISOString().slice(0, 10);
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    initialDate: currentYmd,
                    height: 'auto',
                    headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                    dayMaxEvents: true,
                    dayMaxEventRows: 1,
                    moreLinkClick: 'popover',
                    eventDisplay: 'block',
                    editable: false,
                    selectable: true,
                    events: summaryEvents,
                    dateClick(info) {
                        if (Array.isArray(byDate[info.dateStr]) && byDate[info.dateStr].length > 0) {
                            openDayListModal(info.dateStr);
                            return;
                        }

                        openEmptyDateModal(info.dateStr);
                    },
                    eventClick(info) {
                        const dateStr = String(info.event.extendedProps?.date || '');
                        if (!dateStr) return;
                        openDayListModal(dateStr);
                    }
                });
                calendar.render();

                function applyBasisColumnFilter(selected) {
                    if (!basisBody) return;

                    const valid = ['qual', 'eff', 'time'];
                    const active = valid.includes(selected) ? selected : 'qual';
                    const highlightClasses = ['bg-slate-900/50', 'text-slate-100', 'border-emerald-500/30'];
                    const dimClasses = ['text-slate-400'];

                    basisBody.querySelectorAll('[data-col]').forEach((cell) => {
                        const col = cell.getAttribute('data-col');
                        cell.classList.remove(...highlightClasses, ...dimClasses);
                        if (col === active) {
                            cell.classList.add(...highlightClasses);
                        } else {
                            cell.classList.add(...dimClasses);
                        }
                    });
                }

                function renderBasisTable(basis) {
                    if (!basis || !Array.isArray(basis.rows) || basis.rows.length === 0) {
                        basisBody.innerHTML = `
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm text-slate-300">No locked standards found for this indicator.</td>
                            </tr>
                        `;
                        return;
                    }

                    const rowsHtml = [...basis.rows]
                        .sort((a, b) => Number(b.rating) - Number(a.rating))
                        .map((row) => `
                            <tr>
                                <td class="px-6 py-4 font-semibold text-white">${row.rating ?? '--'}</td>
                                <td class="px-6 py-4 align-top" data-col="qual">${row.q || '--'}</td>
                                <td class="px-6 py-4 align-top" data-col="eff">${row.e || '--'}</td>
                                <td class="px-6 py-4 align-top" data-col="time">${row.t || '--'}</td>
                            </tr>
                        `).join('');

                    basisBody.innerHTML = rowsHtml;
                }

                function openRatingBasisModal(indicatorText) {
                    const indicator = indicatorText || '';
                    const basis = STANDARDS_BY_INDICATOR[indicator];

                    if (basisMfo) {
                        basisMfo.textContent = `MFO: ${basis?.mfo || '--'}`;
                    }
                    if (basisIndicator) {
                        basisIndicator.textContent = `Indicator: ${indicator || '--'}`;
                    }

                    if (basisFilter) {
                        if (!['qual', 'eff', 'time'].includes(basisFilter.value)) {
                            basisFilter.value = 'qual';
                        }
                    }

                    renderBasisTable(basis);
                    applyBasisColumnFilter(basisFilter?.value || 'qual');

                    basisModal?.classList.remove('hidden');
                    basisModal?.classList.add('flex');
                    refreshBodyLock();
                }

                let currentModalData = null;

                function updateRatingBasis(data) {
                    const indicator = data.status === 'submitted'
                        ? (data.accomplishment || 'No submitted ORS entry')
                        : 'No submitted ORS entry';

                    ratingBasisIndicatorEl.textContent = indicator;

                    const canOpenBasis = data.status === 'submitted';
                    if (openBasisBtn) {
                        openBasisBtn.disabled = !canOpenBasis;
                        openBasisBtn.classList.toggle('opacity-60', !canOpenBasis);
                        openBasisBtn.classList.toggle('cursor-not-allowed', !canOpenBasis);
                    }
                }

                function closeDayListModal() {
                    if (!dayListModal) return;
                    dayListModal.classList.add('hidden');
                    dayListModal.classList.remove('flex');
                    refreshBodyLock();
                }

                function closeEmptyDateModal() {
                    if (!emptyDateModal) return;
                    emptyDateModal.classList.add('hidden');
                    emptyDateModal.classList.remove('flex');
                    refreshBodyLock();
                }

                function openEmptyDateModal(dateStr) {
                    if (!emptyDateModal) return;
                    if (emptyDateLabel) {
                        emptyDateLabel.textContent = formatDateLabel(dateStr);
                    }
                    emptyDateModal.classList.remove('hidden');
                    emptyDateModal.classList.add('flex');
                    refreshBodyLock();
                }

                function openDayListModal(dateStr) {
                    if (!dayListModal || !dayListEntries) return;
                    const entries = byDate[dateStr] || [];

                    if (dayListDateLabel) {
                        dayListDateLabel.textContent = formatDateLabel(dateStr);
                    }

                    dayListEntries.innerHTML = '';

                    if (entries.length === 0) {
                        const empty = document.createElement('p');
                        empty.className = 'rounded-lg border border-slate-800 bg-slate-950/50 px-4 py-3 text-sm text-slate-400';
                        empty.textContent = 'No submitted entries found for this date.';
                        dayListEntries.appendChild(empty);
                    } else {
                        entries.forEach((entry) => {
                            const row = document.createElement('div');
                            row.className = 'flex cursor-pointer items-center justify-between gap-3 rounded-xl border border-slate-800 bg-slate-950/40 px-4 py-3 hover:bg-slate-900/80';

                            const left = document.createElement('div');
                            left.className = 'min-w-0';
                            const title = document.createElement('p');
                            title.className = 'truncate text-sm font-semibold text-white';
                            title.textContent = entry.accomplishment || '--';
                            const sub = document.createElement('p');
                            sub.className = 'mt-1 text-xs text-slate-400';
                            sub.textContent = entry.employee || '--';
                            left.appendChild(title);
                            left.appendChild(sub);

                            const right = document.createElement('div');
                            right.className = 'flex shrink-0 items-center gap-2';
                            const evidence = document.createElement('span');
                            evidence.className = entry.evidence
                                ? 'rounded-full border border-emerald-500/60 bg-emerald-500/10 px-2 py-1 text-[11px] text-emerald-200'
                                : 'rounded-full border border-slate-700 bg-slate-800 px-2 py-1 text-[11px] text-slate-300';
                            evidence.textContent = entry.evidence ? 'Attached' : 'None';

                            const openBtn = document.createElement('button');
                            openBtn.type = 'button';
                            openBtn.className = 'rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500';
                            openBtn.textContent = 'Open Monitoring';

                            const openEntry = () => {
                                closeDayListModal();
                                openMonitoringModal(entry);
                            };

                            row.addEventListener('click', openEntry);
                            openBtn.addEventListener('click', (event) => {
                                event.stopPropagation();
                                openEntry();
                            });

                            right.appendChild(evidence);
                            right.appendChild(openBtn);

                            row.appendChild(left);
                            row.appendChild(right);
                            dayListEntries.appendChild(row);
                        });
                    }

                    dayListModal.classList.remove('hidden');
                    dayListModal.classList.add('flex');
                    refreshBodyLock();
                }

                function openMonitoringModal(data) {
                    if (!modal) return;
                    currentModalData = data;

                    employeeEl.textContent = data.employee || '--';
                    officeEl.textContent = data.office || '--';
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

                    const ratingQual = document.getElementById('ratingQual');
                    const ratingTime = document.getElementById('ratingTime');
                    const ratingRemarks = document.getElementById('ratingRemarks');
                    if (ratingQual) {
                        ratingQual.value = data.quality_rating ? String(data.quality_rating) : '';
                        ratingQual.disabled = !rateable;
                    }
                    if (ratingTime) {
                        ratingTime.value = data.timeliness_rating ? String(data.timeliness_rating) : '';
                        ratingTime.disabled = !rateable;
                    }
                    if (ratingRemarks) {
                        ratingRemarks.value = data.remarks || '';
                        ratingRemarks.disabled = !rateable;
                    }

                    if (saveBtn) {
                        setButtonLoading(saveBtn, false);
                        delete saveBtn.dataset.loadingActive;
                        saveBtn.disabled = !rateable;
                        saveBtn.classList.toggle('opacity-60', !rateable);
                        saveBtn.classList.toggle('cursor-not-allowed', !rateable);
                    }
                    if (saveForm) {
                        if (rateable && data.id) {
                            saveForm.action = `${monitorBaseUrl}/${encodeURIComponent(data.id)}/monitor`;
                        } else {
                            saveForm.removeAttribute('action');
                        }
                    }

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    refreshBodyLock();
                }

                window.closeOrsModal = (modalId) => {
                    const m = document.getElementById(modalId);
                    if (!m) return;
                    m.classList.add('hidden');
                    m.classList.remove('flex');
                    if (modalId === 'ors-monitoring-modal' && saveBtn) {
                        setButtonLoading(saveBtn, false);
                        delete saveBtn.dataset.loadingActive;
                    }
                    refreshBodyLock();
                };

                // Backdrop close (monitoring)
                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) closeOrsModal('ors-monitoring-modal');
                });
                dayListModal?.addEventListener('click', (event) => {
                    if (event.target === dayListModal) closeDayListModal();
                });
                emptyDateModal?.addEventListener('click', (event) => {
                    if (event.target === emptyDateModal) closeEmptyDateModal();
                });

                // ESC close (monitoring + basis)
                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') return;

                    if (basisModal && !basisModal.classList.contains('hidden')) {
                        closeBasis();
                        return;
                    }

                    if (emptyDateModal && !emptyDateModal.classList.contains('hidden')) {
                        closeEmptyDateModal();
                        return;
                    }

                    if (dayListModal && !dayListModal.classList.contains('hidden')) {
                        closeDayListModal();
                        return;
                    }

                    if (!modal.classList.contains('hidden')) {
                        closeOrsModal('ors-monitoring-modal');
                    }
                });

                function openBasis() {
                    if (!currentModalData) return;
                    openRatingBasisModal(currentModalData.accomplishment || '');
                }

                function closeBasis() {
                    basisModal?.classList.add('hidden');
                    basisModal?.classList.remove('flex');
                    refreshBodyLock();
                }

                openBasisBtn?.addEventListener('click', openBasis);
                closeBasisBtn?.addEventListener('click', closeBasis);
                basisDoneBtn?.addEventListener('click', closeBasis);
                closeDayListTopBtn?.addEventListener('click', closeDayListModal);
                closeDayListBottomBtn?.addEventListener('click', closeDayListModal);
                closeEmptyDateTopBtn?.addEventListener('click', closeEmptyDateModal);
                closeEmptyDateBottomBtn?.addEventListener('click', closeEmptyDateModal);

                basisModal?.addEventListener('click', (event) => {
                    if (event.target === basisModal) closeBasis();
                });

                basisFilter?.addEventListener('change', () => {
                    applyBasisColumnFilter(basisFilter.value);
                });

                // Save rating to backend
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

                    if (!saveForm || !saveForm.action) {
                        alert('Unable to save rating for this entry.');
                        return;
                    }

                    if (saveBtn.dataset.loadingActive === 'true') {
                        return;
                    }

                    formQualityRating.value = q;
                    formTimelinessRating.value = t;
                    formRemarks.value = document.getElementById('ratingRemarks').value || '';

                    setButtonLoading(saveBtn, true, 'Saving...');
                    saveBtn.dataset.loadingActive = 'true';
                    saveForm.submit();
                });
            });
        </script>
    @endpush
@endsection
