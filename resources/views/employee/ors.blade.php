@extends('layouts.employee')

@section('main-content')

    <style>
        #ors-calendar .fc-col-header-cell {
            background-color: rgba(15, 23, 42, 0.85);
        }

        #ors-calendar .fc-col-header-cell-cushion,
        #ors-calendar .fc-daygrid-day-number {
            color: #e2e8f0;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            border-width: 1px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-dot {
            width: 0.55rem;
            height: 0.55rem;
            border-radius: 9999px;
        }
    </style>

    <section class="space-y-6">

        <!-- Page Header -->
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-white">Output Rating Sheet (ORS)</h1>
            <p class="text-sm text-slate-400">
                ORS is the single source of truth for task creation, timing, output submission, and the only trigger for MPOR.
                My Tasks, Submit Output, MPOR, and SMPOR are read-only mirrors.
            </p>
        </div>

        <!-- Color Legend -->
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="grid grid-cols-1 gap-3 text-xs sm:grid-cols-2 lg:grid-cols-3">
                <div class="flex items-center gap-2">
                    <span class="status-chip border-amber-500/60 bg-amber-500/10 text-amber-200">
                        <span class="status-dot bg-amber-500"></span>
                        Recording (auto-timer)
                    </span>
                    <span class="text-slate-400">Not editable</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="status-chip border-amber-300/60 bg-amber-300/10 text-amber-100">
                        <span class="status-dot bg-amber-300"></span>
                        Draft (stopped)
                    </span>
                    <span class="text-slate-400">Editable</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="status-chip border-blue-500/60 bg-blue-500/10 text-blue-100">
                        <span class="status-dot bg-blue-500"></span>
                        Submitted (locked)
                    </span>
                    <span class="text-slate-400">Not editable</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="status-chip border-emerald-500/60 bg-emerald-500/10 text-emerald-100">
                        <span class="status-dot bg-emerald-500"></span>
                        Submitted (Locked) – mirrored in MPOR
                    </span>
                    <span class="text-slate-400">Not editable</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="status-chip border-red-500/60 bg-red-500/10 text-red-100">
                        <span class="status-dot bg-red-500"></span>
                        Missing / Overdue
                    </span>
                    <span class="text-slate-400">Not editable</span>
                </div>
            </div>
            <p class="mt-3 text-[11px] text-slate-400">
                Stopped entries remain drafts until submitted. Submitting happens once, inside ORS, and locks the entry (sent to supervisor/MPOR).
                Downstream pages mirror state only; no secondary submission exists.
            </p>
        </div>

        <!-- Stats Overview (DEMO LOCKED: 4 tasks total) -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">This Week</p>
                <p class="mt-1 text-2xl font-semibold text-white">4</p>
                <p class="text-xs text-slate-400">Tasks logged (ORS)</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Drafts</p>
                <p class="mt-1 text-2xl font-semibold text-amber-300">0</p>
                <p class="text-xs text-slate-400">Need submission</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Submitted</p>
                <p class="mt-1 text-2xl font-semibold text-blue-300">2</p>
                <p class="text-xs text-slate-400">Eligible for MPOR summary</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Validated</p>
                <p class="mt-1 text-2xl font-semibold text-emerald-300">0</p>
                <p class="text-xs text-slate-400">In SMPOR</p>
            </div>
        </div>

        <!-- Active Task Timer -->
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Task Tracking (single source)</h2>
                    <p class="text-sm text-slate-400">
                        ORS is the only place to start, stop, and submit. Only one entry can be recording at a time; paused entries still block new starts.
                    </p>
                </div>
                <span class="rounded-full border border-emerald-600/60 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                    ONE ACTIVE TIMER
                </span>
            </div>

            <div id="active-task-empty" class="mt-4 rounded-lg border border-slate-800 bg-slate-950/60 p-4 text-sm text-slate-300">
                No task is recording. Open a Draft from the calendar task details to start timing.
                Starting a second task is blocked until the current one is stopped or submitted.
            </div>

            <div id="active-task-card" class="mt-4 hidden rounded-lg border border-slate-800 bg-slate-950/60 p-4 text-sm">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <p class="text-[11px] uppercase text-slate-500">Task</p>
                        <p id="activeTaskName" class="font-semibold text-white">--</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase text-slate-500">Start</p>
                        <p id="activeTaskStart" class="text-slate-200">--</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase text-slate-500">Elapsed</p>
                        <p id="activeTaskElapsed" class="text-slate-200">--</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase text-slate-500">Status</p>
                        <p id="activeTaskStatus" class="font-semibold text-amber-300">Recording</p>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button id="activePauseBtn"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                        Pause
                    </button>
                    <button id="activeStopBtn"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-500/50 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-100 hover:border-amber-500 hover:bg-amber-500/20">
                        Stop (Draft)
                    </button>
                    <button id="activeSubmitBtn"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-slate-900 hover:bg-emerald-600">
                        <span data-button-label>Submit for Review</span>
                        <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-emerald-900/30 border-t-emerald-900"></span>
                    </button>
                </div>

                <p class="mt-3 text-[11px] text-slate-500">
                    Stop ends timing and saves Draft (editable). Submit for Review locks the entry and creates MPOR visibility.
                    My Tasks and Submit Output remain display-only mirrors.
                </p>
            </div>
        </div>

        <!-- Calendar -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">ORS Calendar</h2>
                    <p class="text-sm text-slate-400">
                        Supports multiple tasks per day. Click an entry to start, pause, stop, or submit. Locked entries open read-only.
                    </p>
                </div>
                <button id="openLogTaskBtn"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-slate-900 hover:bg-blue-600">
                    Log Task
                </button>
            </div>
            <div id="ors-calendar"></div>
        </div>

        <!-- System Notice -->
        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 text-xs text-slate-400">
            ORS captures timing and submissions once. Manual duration entry is disabled. After submission, entries are locked and visible in MPOR -> SMPOR.
            My Tasks and Submit Output remain read-only displays of ORS state.
        </div>

    </section>

    <!-- ORS Task Modal (Log Task) -->
    <div id="orsTaskModal"
        role="dialog"
        aria-modal="true"
        class="ors-modal fixed inset-0 z-[60] hidden flex items-start justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6">

        <div class="w-full max-w-md max-h-[calc(100vh-3rem)] overflow-y-auto rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">

            <div class="mb-4">
                <h2 class="text-lg font-semibold text-white">Log ORS Task</h2>
                <p class="text-xs text-slate-400">
                    Log actual work performed aligned with approved Unit Work Plan (UWP) outputs.
                    Entries start as Draft (stopped).
                </p>
            </div>

            <form class="space-y-3">

                <!-- DATE -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">Date</label>
                    <input id="orsSelectedDate"
                        type="text"
                        disabled
                        class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-400">
                </div>

                <!-- UWP OUTPUT -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">
                        UWP Output / Major Final Output
                    </label>
                    <select id="orsUwpOutput"
                        class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                        required>
                        <option value="">Select approved UWP output</option>
                        <option value="ebank_scanning">
                            E-Bank Scanning and Encoding of Revenue Transactions
                        </option>
                        <option value="otc_processing">
                            Processing of Over-the-Counter Revenue Transactions
                        </option>
                        <option value="records_maintenance">
                            Maintenance of revenue records and filing system
                        </option>
                    </select>

                </div>

                <!-- TASK / ACTIVITY -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">
                        Task / Activity
                    </label>
                    <select id="orsTaskType"
                        class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                        required>
                        <option value="">Select task / activity</option>
                    </select>

                </div>

                <!-- CLIENT REQUEST ID -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">
                        Client Request ID (optional)
                    </label>
                    <input id="orsRequestId"
                        type="text"
                        placeholder="REQ-2026-00123"
                        class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                </div>

                <!-- FORM / OUTPUT -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">
                        Form / Output Type
                    </label>

                    <select id="orsOutput"
                            class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                            required>
                        <option value="">Select form/output</option>
                        <option value="bsf_01">Bank Statement Form (BSF-01)</option>
                        <option value="official_receipt">Official Receipt (OR)</option>
                        <option value="scanned_doc">Scanned Supporting Document</option>
                        <option value="records_checklist">Records Inventory Checklist</option>
                    </select>

                </div>

                <!-- NOTES -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">Notes (optional)</label>
                    <textarea id="orsNotes"
                            rows="2"
                            class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                            placeholder="Exceptions, clarifications, or additional context"></textarea>
                </div>

                <!-- SYSTEM RULE -->
                <div class="rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-[11px] text-slate-400">
                    • Tasks must align with approved UWP outputs<br>
                    • Duration is tracked automatically<br>
                    • Draft until submitted inside ORS
                </div>

                <!-- ACTIONS (RESTORED – LOADING SAFE) -->
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="closeOrsModal('orsTaskModal')"
                            class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs text-slate-300 hover:bg-slate-800">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        data-admin-loading="true"
                        data-loading-text="Logging..."
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-1.5
                            text-xs font-semibold text-slate-900 hover:bg-emerald-600">

                        <span data-button-spinner
                            class="hidden h-3 w-3 animate-spin rounded-full
                                    border-2 border-emerald-900/30 border-t-emerald-900"></span>

                        <span data-button-label>Log Task</span>
                    </button>

                </div>

            </form>
        </div>
    </div>


    <!-- Task Details Modal -->
    <div id="taskDetailsModal"
        role="dialog"
        aria-modal="true"
        class="ors-modal fixed inset-0 z-[60] hidden flex items-start justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6">
        <div class="w-full max-w-[95vw] sm:max-w-xl md:max-w-2xl lg:max-w-3xl max-h-[calc(100vh-3rem)] overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-xl">
            <div class="sticky top-0 z-10 border-b border-slate-800 bg-slate-900/95 p-5 backdrop-blur">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white" id="taskDetailTitle">Task Details</h2>
                        <p class="text-xs text-slate-400" id="taskDetailDate">Date: --</p>
                    </div>
                    <button onclick="closeOrsModal('taskDetailsModal')"
                            class="text-slate-400 hover:text-white">
                        x
                    </button>
                </div>

                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    <span id="taskDetailStatusBadge" class="status-chip border-slate-700 bg-slate-800 text-slate-200">--</span>
                    <span id="taskDetailLockBadge" class="hidden status-chip border-emerald-500/60 bg-emerald-500/10 text-emerald-100">
                        Submitted (Locked)
                    </span>
                </div>
            </div>

            <div class="max-h-[calc(100vh-12rem)] overflow-y-auto p-5">
                <div class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
                    <div>
                        <p class="text-xs text-slate-400">Supervisor</p>
                        <p class="text-slate-200" id="taskDetailClient">--</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Request ID</p>
                        <p class="text-slate-200" id="taskDetailRequest">--</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-400">MFO / UWP Output</p>
                        <p class="text-slate-200" id="taskDetailMfo">--</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-400">Quantity (employee-declared)</p>
                        <input id="taskDetailQuantity"
                               type="text"
                               class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                               placeholder="e.g., 12 transactions">
                        <p class="mt-1 text-[11px] text-slate-400">
                            Quantity is declared by the employee. Supervisor rates Quality & Timeliness during ORS Monitoring.
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Output Type</p>
                        <p class="text-slate-200" id="taskDetailOutput">--</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Status</p>
                        <p class="text-slate-200" id="taskDetailStatusText">--</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Duration</p>
                        <p class="text-slate-200" id="taskDetailDuration">--</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Performance Rating</p>
                        <p class="text-slate-200" id="taskDetailRating">--</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-400">Notes</p>
                        <p class="text-slate-200" id="taskDetailNotes">--</p>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-slate-800 bg-slate-950/60 p-3 text-sm">
                    <p class="text-[11px] uppercase text-slate-400">Submission & Output</p>

                    <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <p class="text-xs text-slate-400">Output State</p>
                            <span id="taskDetailOutputState"
                                class="mt-1 inline-flex rounded-full border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-200">
                                No output yet
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Evidence</p>
                            <span id="taskDetailEvidenceState"
                                class="mt-1 inline-flex rounded-full border border-slate-700 bg-slate-800 px-2 py-1 text-xs text-slate-200">
                                None
                            </span>
                            <p id="taskDetailEvidenceFile" class="mt-1 text-[11px] text-slate-400">--</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Submitted At</p>
                            <p class="text-slate-200" id="taskDetailSubmittedAt">--</p>
                        </div>
                    </div>

                    <div class="mt-3 space-y-2">
                        <label class="text-xs text-slate-300">Output Upload (part of ORS submission)</label>
                        <input id="taskDetailUpload"
                            type="file"
                            class="block w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-xs text-slate-200 file:mr-3 file:rounded-md file:border-0 file:bg-slate-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-200">
                        <p class="text-[11px] text-slate-400">
                            Submission occurs once inside ORS. After submit, the entry is locked and visible in My Tasks (read-only) and MPOR/SMPOR.
                        </p>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2" id="taskDetailActions">
                        <button id="taskDetailStartBtn"
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg border border-blue-500/60 bg-blue-500/10 px-3 py-1.5 text-xs font-semibold text-blue-100 hover:bg-blue-500/20">
                            <span data-button-label>Start Task</span>
                            <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-blue-200/40 border-t-blue-200"></span>
                        </button>
                        <button id="taskDetailPauseBtn"
                                type="button"
                                class="hidden rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                            Pause
                        </button>
                        <button id="taskDetailResumeBtn"
                                type="button"
                                class="hidden rounded-lg border border-amber-500/50 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-100 hover:bg-amber-500/20">
                            Resume
                        </button>
                        <button id="taskDetailStopBtn"
                                type="button"
                                class="hidden rounded-lg border border-amber-500/50 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-100 hover:bg-amber-500/20">
                            Stop (Draft)
                        </button>
                        <button id="taskDetailSubmitBtn"
                                type="button"
                                class="hidden inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-slate-900 hover:bg-emerald-600">
                            <span data-button-label>Submit for Review</span>
                            <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-emerald-900/30 border-t-emerald-900"></span>
                        </button>
                    </div>

                    <p id="taskDetailLockMessage" class="mt-2 hidden text-[11px] text-emerald-300">
                        Submitted (Locked) — visible in MPOR monthly summary. SMPOR is system-generated after validation.
                    </p>
                    <p id="taskDetailDraftMessage" class="mt-2 text-[11px] text-slate-400">
                        Stop ends timing and keeps Draft editable. Submit for Review locks the entry and prevents duplicate submissions.
                    </p>
                </div>
            </div>

            <div class="sticky bottom-0 z-10 flex justify-end gap-2 border-t border-slate-800 bg-slate-900/95 p-4 backdrop-blur">
                <button onclick="closeOrsModal('taskDetailsModal')"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const STATE_META = {
                recording: { label: 'Recording', color: '#f59e0b', badge: 'border-amber-500/60 bg-amber-500/10 text-amber-200', editable: false },
                paused: { label: 'Paused', color: '#f59e0b', badge: 'border-amber-500/60 bg-amber-500/10 text-amber-200', editable: false },
                draft: { label: 'Draft (Stopped)', color: '#fbbf24', badge: 'border-amber-300/60 bg-amber-300/10 text-amber-100', editable: true },
                submitted: { label: 'Submitted (Locked)', color: '#3b82f6', badge: 'border-blue-500/60 bg-blue-500/10 text-blue-100', editable: false },
                locked: { label: 'Submitted (Locked)', color: '#10b981', badge: 'border-emerald-500/60 bg-emerald-500/10 text-emerald-100', editable: false },
                missing: { label: 'Missing / Overdue', color: '#ef4444', badge: 'border-red-500/60 bg-red-500/10 text-red-100', editable: false },
            };

            const UWP_INDICATORS = {
                ebank_scanning: [
                    'All e-bank transactions scanned and encoded daily',
                    'Indexing complete with no missing pages',
                    'Audit trail maintained within 24 hours',
                ],
                otc_processing: [
                    'Same-day verification of OTC transactions',
                    '95% encoded within the business day',
                    'OR validation completed daily',
                ],
                records_maintenance: [
                    'Weekly filing updated and retrievable',
                    'Digital backups synced monthly',
                    'Retrieval logs maintained for audit purposes',
                ],
            };

            /**
             * DEMO LOCKED DATASET (Stage II) — Employee Assigned: Ramon Reyes ONLY
             * Required statuses:
             * - Jan 2, 2026: Submitted (Locked) — Same-day verification of OTC transactions
             * - Jan 4, 2026: Submitted (Locked) — All e-bank transactions scanned and encoded daily
             * - Jan 5, 2026: Recording — OR validation completed daily
             * - Jan 6, 2026: Missing / Overdue — Retrieval logs maintained for audit purposes
             */
            let tasks = [
                {
                    id: 'task-jan-02',
                    title: 'Same-day verification of OTC transactions',
                    date: '2026-01-02',
                    client: 'Revenue Collection Unit',
                    requestId: 'REQ-2026-002',
                    uwpOutputId: 'otc_processing',
                    uwpOutputLabel: 'Processing of Over-the-Counter Revenue Transactions',
                    output: 'Official Receipt (OR)',
                    notes: 'Demo: Same-day OTC verification completed and submitted.',
                    quantity: '12 transactions',
                    rating: '--',
                    state: 'submitted',
                    output_state: 'submitted',
                    submittedAt: new Date('2026-01-02T10:15:00'),
                    evidenceRequired: true,
                    evidenceAttached: true,
                    evidenceFileName: 'REQ-2026-002_OR.pdf',
                    evidenceUploadedAt: new Date('2026-01-02T10:15:00'),
                    startTime: null,
                    durationMs: 2 * 60 * 60 * 1000
                },
                {
                    id: 'task-jan-04',
                    title: 'All e-bank transactions scanned and encoded daily',
                    date: '2026-01-04',
                    client: 'Revenue Collection Unit',
                    requestId: 'REQ-2026-004',
                    uwpOutputId: 'ebank_scanning',
                    uwpOutputLabel: 'E-Bank Scanning and Encoding of Revenue Transactions',
                    output: 'Bank Statement Form (BSF-01)',
                    notes: 'Demo: E-bank scanning submitted with evidence (BSF-01).',
                    quantity: '1 daily batch',
                    rating: '--',
                    state: 'submitted',
                    output_state: 'submitted',
                    submittedAt: new Date('2026-01-04T15:20:00'),
                    evidenceRequired: true,
                    evidenceAttached: true,
                    evidenceFileName: 'REQ-2026-004_BSF-01.pdf',
                    evidenceUploadedAt: new Date('2026-01-04T15:20:00'),
                    startTime: null,
                    durationMs: 90 * 60 * 1000
                },
                {
                    id: 'task-jan-05',
                    title: 'OR validation completed daily',
                    date: '2026-01-05',
                    client: 'Revenue Collection Unit',
                    requestId: 'REQ-2026-005',
                    uwpOutputId: 'otc_processing',
                    uwpOutputLabel: 'Processing of Over-the-Counter Revenue Transactions',
                    output: 'Official Receipt (OR)',
                    notes: 'Demo: OR validation is currently in progress.',
                    quantity: '6 receipts validated',
                    rating: '--',
                    state: 'recording',
                    output_state: 'none',
                    submittedAt: null,
                    evidenceRequired: true,
                    evidenceAttached: false,
                    evidenceFileName: null,
                    evidenceUploadedAt: null,
                    // make it look active without depending on user clock too much
                    startTime: new Date(Date.now() - (18 * 60 * 1000)), // started 18 minutes ago
                    durationMs: 0
                },
                {
                    id: 'task-jan-06',
                    title: 'Retrieval logs maintained for audit purposes',
                    date: '2026-01-06',
                    client: 'Revenue Collection Unit',
                    requestId: 'REQ-2026-006',
                    uwpOutputId: 'records_maintenance',
                    uwpOutputLabel: 'Maintenance of revenue records and filing system',
                    output: 'Records Inventory Checklist',
                    notes: 'Demo: No ORS entry submitted — flagged as Missing / Overdue.',
                    quantity: '',
                    rating: '--',
                    state: 'missing',
                    output_state: 'none',
                    submittedAt: null,
                    evidenceRequired: true,
                    evidenceAttached: false,
                    evidenceFileName: null,
                    evidenceUploadedAt: null,
                    startTime: null,
                    durationMs: 0
                },
            ];

            const TASK_DEFAULTS = {
                output_state: 'none',
                submittedAt: null,
                evidenceRequired: true,
                evidenceAttached: false,
                evidenceFileName: null,
                evidenceUploadedAt: null,
            };

            tasks = tasks.map((task) => ({
                ...TASK_DEFAULTS,
                ...task,
                submittedAt: task.submittedAt ? new Date(task.submittedAt) : null,
                evidenceUploadedAt: task.evidenceUploadedAt ? new Date(task.evidenceUploadedAt) : null,
            }));

            const uwpSelect = document.getElementById('orsUwpOutput');
            const taskSelect = document.getElementById('orsTaskType');

            function resetTaskOptions() {
                if (!taskSelect) return;
                taskSelect.innerHTML = '<option value="">Select task / activity</option>';
            }

            function populateTaskOptions(outputKey) {
                resetTaskOptions();
                const indicators = UWP_INDICATORS[outputKey] || [];
                indicators.forEach((indicator) => {
                    const opt = document.createElement('option');
                    opt.value = indicator;
                    opt.textContent = indicator;
                    taskSelect.appendChild(opt);
                });
            }

            if (uwpSelect && taskSelect) {
                resetTaskOptions();
                uwpSelect.addEventListener('change', () => {
                    populateTaskOptions(uwpSelect.value);
                });
            }

            // DEMO: exactly one active timer -> Jan 5 recording
            let activeTaskId = tasks.find(t => t.state === 'recording' || t.state === 'paused')?.id || null;
            let detailTaskId = null;

            const calendarEl = document.getElementById('ors-calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: '2026-01-01',
                height: 'auto',
                contentHeight: 600,
                dayMaxEventRows: 3,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                selectable: true,
                editable: false,
                dayHeaderClassNames: 'text-slate-300',
                dayCellClassNames: 'hover:bg-slate-800/30',
                dateClick(info) {
                    document.getElementById('orsSelectedDate').value = info.dateStr;
                    openOrsModal('orsTaskModal');
                },
                eventClick(info) {
                    openTaskDetails(info.event.id);
                },
                events: [],
                eventContent(arg) {
                    const meta = STATE_META[arg.event.extendedProps.state] || STATE_META.draft;
                    const label = meta.label.length > 16 ? meta.label.substring(0, 13) + '...' : meta.label;
                    const title = arg.event.title.length > 28 ? arg.event.title.substring(0, 25) + '...' : arg.event.title;
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('text-[11px]', 'leading-tight', 'px-1', 'py-[2px]');
                    wrapper.innerHTML = `
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-slate-100">${title}</span>
                            <span class="rounded-full border px-2 py-[1px]" style="color:${meta.color}; border-color:${meta.color};">${label}</span>
                        </div>
                    `;
                    return { domNodes: [wrapper] };
                }
            });
            calendar.render();

            const orsModals = Array.from(document.querySelectorAll('.ors-modal'));

            function updateModalState() {
                const anyOpen = orsModals.some((modal) => !modal.classList.contains('hidden'));
                document.body.classList.toggle('overflow-hidden', anyOpen);
            }

            window.openOrsModal = function (modalId) {
                orsModals.forEach((modal) => modal.classList.add('hidden'));
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('hidden');
                }
                updateModalState();
            };

            window.closeOrsModal = function (modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                }
                if (modalId === 'taskDetailsModal') {
                    if (detailTaskId) {
                        const currentTask = getTaskById(detailTaskId);
                        syncQuantityFromInput(currentTask);
                    }
                    detailTaskId = null;
                }
                updateModalState();
            };

            orsModals.forEach((modal) => {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        window.closeOrsModal(modal.id);
                    }
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    orsModals.forEach((modal) => modal.classList.add('hidden'));
                    updateModalState();
                }
            });

            function setButtonLoading(button, isLoading, loadingText) {
                if (!button) return;
                const label = button.querySelector('[data-button-label]');
                const spinner = button.querySelector('[data-button-spinner]');
                if (label && !button.dataset.originalLabel) {
                    button.dataset.originalLabel = label.textContent.trim();
                }
                if (isLoading) {
                    button.disabled = true;
                    button.classList.add('opacity-70', 'cursor-wait');
                    if (spinner) spinner.classList.remove('hidden');
                    if (label && loadingText) label.textContent = loadingText;
                } else {
                    button.disabled = false;
                    button.classList.remove('opacity-70', 'cursor-wait');
                    if (spinner) spinner.classList.add('hidden');
                    if (label && button.dataset.originalLabel) label.textContent = button.dataset.originalLabel;
                }
            }

            function getTaskById(id) {
                return tasks.find((t) => t.id === id);
            }

            function isLockedState(state) {
                return state === 'submitted' || state === 'locked';
            }

            function syncQuantityFromInput(task) {
                if (!task) return '';
                const quantityInput = document.getElementById('taskDetailQuantity');
                if (!quantityInput || detailTaskId !== task.id) {
                    return task.quantity || '';
                }
                const trimmed = quantityInput.value.trim();
                task.quantity = trimmed;
                return trimmed;
            }

            function formatDuration(ms) {
                const totalSeconds = Math.max(0, Math.floor(ms / 1000));
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                if (hours === 0) {
                    return `${minutes}m`;
                }
                return `${hours}h ${minutes}m`;
            }

            function formatTime(dateObj) {
                if (!dateObj) return '--';
                return dateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            function formatDateTime(dateObj) {
                if (!dateObj) return '--';
                const value = dateObj instanceof Date ? dateObj : new Date(dateObj);
                if (Number.isNaN(value.getTime())) return '--';
                return value.toLocaleString([], {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                });
            }

            function computeElapsed(task) {
                const base = task.durationMs || 0;
                if (task.state === 'recording' && task.startTime) {
                    return base + (Date.now() - task.startTime.getTime());
                }
                return base;
            }

            function refreshCalendar() {
                calendar.removeAllEvents();
                tasks.forEach((task) => {
                    const meta = STATE_META[task.state] || STATE_META.draft;
                    calendar.addEvent({
                        id: task.id,
                        title: task.title,
                        start: task.date,
                        color: meta.color,
                        extendedProps: {
                            state: task.state,
                            client: task.client,
                            requestId: task.requestId,
                            output: task.output,
                            notes: task.notes,
                            duration: formatDuration(computeElapsed(task)),
                            rating: task.rating || '--'
                        }
                    });
                });
            }

            function setStatusBadge(element, state) {
                if (!element) return;
                const meta = STATE_META[state] || STATE_META.draft;
                element.textContent = meta.label;
                element.className = `status-chip ${meta.badge}`;
            }

            function toggleAction(button, shouldShow, disabled = false) {
                if (!button) return;
                button.classList.toggle('hidden', !shouldShow);
                button.disabled = disabled;
            }

            function openTaskDetails(taskId) {
                const task = getTaskById(taskId);
                if (!task) return;
                detailTaskId = taskId;

                if (!task.output_state) task.output_state = 'none';
                if (typeof task.evidenceRequired !== 'boolean') task.evidenceRequired = true;
                if (typeof task.evidenceAttached !== 'boolean') task.evidenceAttached = false;
                if (!('evidenceFileName' in task)) task.evidenceFileName = null;
                if (!('evidenceUploadedAt' in task)) task.evidenceUploadedAt = null;

                document.getElementById('taskDetailTitle').textContent = task.title || '--';
                document.getElementById('taskDetailDate').textContent = `Date: ${task.date || '--'}`;
                document.getElementById('taskDetailClient').textContent = task.client || '--';
                document.getElementById('taskDetailRequest').textContent = task.requestId || '--';
                document.getElementById('taskDetailMfo').textContent = task.uwpOutputLabel || '--';
                document.getElementById('taskDetailOutput').textContent = task.output || '--';
                document.getElementById('taskDetailStatusText').textContent = (STATE_META[task.state] || STATE_META.draft).label;
                document.getElementById('taskDetailDuration').textContent = formatDuration(computeElapsed(task));
                document.getElementById('taskDetailRating').textContent = task.rating || '--';
                document.getElementById('taskDetailNotes').textContent = task.notes || '--';

                const outputStateEl = document.getElementById('taskDetailOutputState');
                if (outputStateEl) {
                    let outputLabel = 'No output yet';
                    let outputClass = 'border-slate-700 bg-slate-800 text-slate-200';
                    if (task.output_state === 'submitted') {
                        outputLabel = 'Output submitted';
                        outputClass = 'border-blue-500/60 bg-blue-500/10 text-blue-200';
                    } else if (task.output_state === 'validated') {
                        outputLabel = 'Output validated';
                        outputClass = 'border-emerald-500/60 bg-emerald-500/10 text-emerald-200';
                    }
                    outputStateEl.textContent = outputLabel;
                    outputStateEl.className = `mt-1 inline-flex rounded-full border px-2 py-1 text-xs ${outputClass}`;
                }

                const evidenceStateEl = document.getElementById('taskDetailEvidenceState');
                if (evidenceStateEl) {
                    evidenceStateEl.textContent = task.evidenceAttached ? 'Attached' : 'None';
                    evidenceStateEl.className = `mt-1 inline-flex rounded-full border px-2 py-1 text-xs ${
                        task.evidenceAttached
                            ? 'border-emerald-500/60 bg-emerald-500/10 text-emerald-200'
                            : 'border-slate-700 bg-slate-800 text-slate-200'
                    }`;
                }

                const evidenceFileEl = document.getElementById('taskDetailEvidenceFile');
                if (evidenceFileEl) {
                    if (task.evidenceAttached && task.evidenceFileName) {
                        const uploadedAtText = task.evidenceUploadedAt
                            ? ` (${formatDateTime(task.evidenceUploadedAt)})`
                            : '';
                        evidenceFileEl.textContent = `${task.evidenceFileName}${uploadedAtText}`;
                    } else {
                        evidenceFileEl.textContent = '--';
                    }
                }

                document.getElementById('taskDetailSubmittedAt').textContent = formatDateTime(task.submittedAt);

                const quantityInput = document.getElementById('taskDetailQuantity');
                if (quantityInput) {
                    quantityInput.value = task.quantity || '';
                    const quantityDisabled = isLockedState(task.state);
                    quantityInput.disabled = quantityDisabled;
                    quantityInput.classList.toggle('opacity-70', quantityDisabled);
                }

                const isMissing = task.state === 'missing';
                const uploadInput = document.getElementById('taskDetailUpload');
                if (uploadInput) {
                    const uploadDisabled = isLockedState(task.state);
                    uploadInput.disabled = uploadDisabled;
                    uploadInput.classList.toggle('opacity-70', uploadDisabled);
                    uploadInput.onchange = function () {
                        if (uploadDisabled) return;
                        const file = this.files && this.files[0] ? this.files[0] : null;
                        if (file) {
                            task.evidenceAttached = true;
                            task.evidenceFileName = file.name;
                            task.evidenceUploadedAt = new Date();
                        } else {
                            task.evidenceAttached = false;
                            task.evidenceFileName = null;
                            task.evidenceUploadedAt = null;
                        }
                        openTaskDetails(task.id);
                    };
                }

                setStatusBadge(document.getElementById('taskDetailStatusBadge'), task.state);

                const lockBadge = document.getElementById('taskDetailLockBadge');
                const lockMsg = document.getElementById('taskDetailLockMessage');
                const draftMsg = document.getElementById('taskDetailDraftMessage');

                const locked = !isMissing && isLockedState(task.state);

                if (isMissing) {
                    if (lockBadge) {
                        lockBadge.classList.add('hidden');
                        lockBadge.style.display = 'none';
                        lockBadge.setAttribute('aria-hidden', 'true');
                        lockBadge.textContent = '';
                    }
                    if (lockMsg) {
                        lockMsg.classList.add('hidden');
                        lockMsg.style.display = 'none';
                        lockMsg.setAttribute('aria-hidden', 'true');
                    }
                } else {
                    if (lockBadge) {
                        lockBadge.textContent = 'Submitted (Locked)';
                        lockBadge.classList.toggle('hidden', !locked);
                        lockBadge.style.display = locked ? '' : 'none';
                        lockBadge.removeAttribute('aria-hidden');
                    }
                    if (lockMsg) {
                        lockMsg.classList.toggle('hidden', !locked);
                        lockMsg.style.display = locked ? '' : 'none';
                        lockMsg.removeAttribute('aria-hidden');
                    }
                }
                draftMsg?.classList.toggle('hidden', locked);

                toggleAction(document.getElementById('taskDetailStartBtn'), !locked && task.state === 'draft' && (!activeTaskId || activeTaskId === task.id));
                toggleAction(document.getElementById('taskDetailPauseBtn'), task.state === 'recording');
                toggleAction(document.getElementById('taskDetailResumeBtn'), task.state === 'paused');
                toggleAction(document.getElementById('taskDetailStopBtn'), task.state === 'recording' || task.state === 'paused');
                toggleAction(document.getElementById('taskDetailSubmitBtn'), !locked && (task.state === 'draft' || isMissing));

                openOrsModal('taskDetailsModal');
            }

            function startTask(taskId) {
                const task = getTaskById(taskId);
                if (!task) return false;
                if (activeTaskId && activeTaskId !== taskId) return false;
                if (isLockedState(task.state)) return false;

                task.state = 'recording';
                task.startTime = new Date();
                activeTaskId = taskId;

                refreshCalendar();
                updateActivePanel();
                if (detailTaskId === taskId) openTaskDetails(taskId);
                return true;
            }

            function pauseTask(taskId) {
                const task = getTaskById(taskId);
                if (!task || task.state !== 'recording') return;
                if (task.startTime) {
                    task.durationMs = (task.durationMs || 0) + (Date.now() - task.startTime.getTime());
                }
                task.startTime = null;
                task.state = 'paused';
                activeTaskId = taskId;

                refreshCalendar();
                updateActivePanel();
                if (detailTaskId === taskId) openTaskDetails(taskId);
            }

            function resumeTask(taskId) {
                const task = getTaskById(taskId);
                if (!task || task.state !== 'paused') return;
                if (activeTaskId && activeTaskId !== taskId) return;

                task.startTime = new Date();
                task.state = 'recording';
                activeTaskId = taskId;

                refreshCalendar();
                updateActivePanel();
                if (detailTaskId === taskId) openTaskDetails(taskId);
            }

            function stopTask(taskId) {
                const task = getTaskById(taskId);
                if (!task) return;
                syncQuantityFromInput(task);

                if (task.state === 'recording' && task.startTime) {
                    task.durationMs = (task.durationMs || 0) + (Date.now() - task.startTime.getTime());
                }
                task.startTime = null;
                task.state = 'draft';

                if (activeTaskId === taskId) activeTaskId = null;

                refreshCalendar();
                updateActivePanel();
                if (detailTaskId === taskId) openTaskDetails(taskId);
            }

            function submitTask(taskId) {
                const task = getTaskById(taskId);
                if (!task) return false;
                if (task.state === 'recording') return false;
                if (isLockedState(task.state)) return false;
                const qtyValue = syncQuantityFromInput(task);
                if (!qtyValue) {
                    alert('Quantity is required before submitting an ORS entry.');
                    return false;
                }
                if (task.evidenceRequired && !task.evidenceAttached) {
                    alert('Evidence attachment is required before submitting an ORS entry.');
                    return false;
                }

                task.state = 'submitted';
                task.output_state = 'submitted';
                task.submittedAt = new Date();
                task.startTime = null;

                if (activeTaskId === taskId) activeTaskId = null;

                refreshCalendar();
                updateActivePanel();
                if (detailTaskId === taskId) openTaskDetails(taskId);
                return true;
            }

            function updateActivePanel() {
                const activeCard = document.getElementById('active-task-card');
                const emptyState = document.getElementById('active-task-empty');
                const nameEl = document.getElementById('activeTaskName');
                const startEl = document.getElementById('activeTaskStart');
                const elapsedEl = document.getElementById('activeTaskElapsed');
                const statusEl = document.getElementById('activeTaskStatus');

                const task = activeTaskId ? getTaskById(activeTaskId) : null;
                if (!task || isLockedState(task.state) || task.state === 'draft') {
                    activeTaskId = null;
                    activeCard.classList.add('hidden');
                    emptyState.classList.remove('hidden');
                    return;
                }

                emptyState.classList.add('hidden');
                activeCard.classList.remove('hidden');

                nameEl.textContent = task.title || '--';
                startEl.textContent = formatTime(task.startTime);
                elapsedEl.textContent = formatDuration(computeElapsed(task));
                statusEl.textContent = task.state === 'paused' ? 'Paused' : 'Recording';
                statusEl.className = 'font-semibold ' + (task.state === 'paused' ? 'text-amber-200' : 'text-amber-300');

                const pauseBtn = document.getElementById('activePauseBtn');
                const stopBtn = document.getElementById('activeStopBtn');
                const submitBtn = document.getElementById('activeSubmitBtn');

                pauseBtn.textContent = task.state === 'paused' ? 'Resume' : 'Pause';
                pauseBtn.onclick = () => task.state === 'paused' ? resumeTask(task.id) : pauseTask(task.id);
                stopBtn.onclick = () => stopTask(task.id);
                submitBtn.onclick = () => runWithLoading(submitBtn, 'Submitting...', () => submitTask(task.id));
            }

            function updateDetailElapsed() {
                if (!detailTaskId) return;
                const task = getTaskById(detailTaskId);
                if (!task) return;
                const durationEl = document.getElementById('taskDetailDuration');
                if (durationEl) durationEl.textContent = formatDuration(computeElapsed(task));
            }

            function wireTaskForm() {
                const taskForm = document.querySelector('#orsTaskModal form');
                if (!taskForm) return;

                taskForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const selectedDate = document.getElementById('orsSelectedDate').value;
                    const uwpOutputSelect = document.getElementById('orsUwpOutput');
                    const taskTypeSelect = document.getElementById('orsTaskType');
                    const outputSelect = document.getElementById('orsOutput');
                    const requestInput = document.getElementById('orsRequestId');
                    const notesInput = document.getElementById('orsNotes');
                    const submitBtn = taskForm.querySelector('button[type="submit"]');

                    const uwpOutputKey = uwpOutputSelect ? uwpOutputSelect.value : '';
                    const uwpOutputLabel = (uwpOutputSelect && uwpOutputSelect.selectedIndex > -1)
                        ? uwpOutputSelect.options[uwpOutputSelect.selectedIndex].text
                        : '';
                    const taskType = taskTypeSelect ? taskTypeSelect.value : '';
                    const outputType = outputSelect ? outputSelect.value : '';
                    const requestId = requestInput ? requestInput.value.trim() : '';
                    const validIndicators = UWP_INDICATORS[uwpOutputKey] || [];

                    if (!uwpOutputKey || !taskType || !selectedDate || !outputType) {
                        alert('Select a valid UWP output and task / activity.');
                        return;
                    }
                    if (!validIndicators.includes(taskType)) {
                        alert('Task / activity must match the selected UWP output.');
                        return;
                    }

                    setButtonLoading(submitBtn, true, 'Logging...');

                    setTimeout(() => {
                        const currentActive = activeTaskId ? getTaskById(activeTaskId) : null;
                        if (currentActive && currentActive.state === 'recording') {
                            if (currentActive.startTime) {
                                currentActive.durationMs = (currentActive.durationMs || 0) + (Date.now() - currentActive.startTime.getTime());
                            }
                            currentActive.startTime = null;
                            currentActive.state = 'draft';
                            activeTaskId = null;
                        }

                        const newTask = {
                            id: `task-${Date.now()}`,
                            title: taskTypeSelect.options[taskTypeSelect.selectedIndex].text,
                            date: selectedDate,
                            client: 'Revenue Collection Unit',
                            requestId: requestId,
                            output: outputSelect.options[outputSelect.selectedIndex].text,
                            uwpOutputId: uwpOutputKey,
                            uwpOutputLabel: uwpOutputLabel,
                            notes: notesInput && notesInput.value ? notesInput.value : 'No notes',
                            quantity: '',
                            rating: '--',
                            state: 'recording',
                            output_state: 'none',
                            submittedAt: null,
                            evidenceRequired: true,
                            evidenceAttached: false,
                            evidenceFileName: null,
                            evidenceUploadedAt: null,
                            startTime: new Date(),
                            durationMs: 0
                        };

                        tasks.push(newTask);
                        activeTaskId = newTask.id;

                        refreshCalendar();
                        if (calendar && selectedDate) calendar.gotoDate(selectedDate);
                        updateActivePanel();

                        closeOrsModal('orsTaskModal');
                        taskForm.reset();
                        document.getElementById('orsSelectedDate').value = '';
                        setButtonLoading(submitBtn, false);

                        openTaskDetails(newTask.id);
                    }, 400);
                });
            }

            function wireLogButton() {
                const btn = document.getElementById('openLogTaskBtn');
                if (!btn) return;
                btn.addEventListener('click', () => {
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('orsSelectedDate').value = today;
                    openOrsModal('orsTaskModal');
                });
            }

            wireTaskForm();
            wireLogButton();
            refreshCalendar();
            updateActivePanel();

            setInterval(() => {
                if (activeTaskId) updateActivePanel();
                if (detailTaskId) updateDetailElapsed();
            }, 1000);

            function runWithLoading(button, loadingText, actionFn) {
                if (!button || typeof actionFn !== 'function') return;
                setButtonLoading(button, true, loadingText);
                setTimeout(() => {
                    actionFn();
                    setButtonLoading(button, false);
                }, 150);
            }

            document.getElementById('taskDetailStartBtn')?.addEventListener('click', (e) => {
                runWithLoading(e.currentTarget, 'Starting...', () => startTask(detailTaskId));
            });
            document.getElementById('taskDetailPauseBtn')?.addEventListener('click', () => pauseTask(detailTaskId));
            document.getElementById('taskDetailResumeBtn')?.addEventListener('click', () => resumeTask(detailTaskId));
            document.getElementById('taskDetailStopBtn')?.addEventListener('click', () => stopTask(detailTaskId));
            document.getElementById('taskDetailSubmitBtn')?.addEventListener('click', (e) => {
                runWithLoading(e.currentTarget, 'Submitting...', () => submitTask(detailTaskId));
            });
        });
    </script>
    @endpush

@endsection
