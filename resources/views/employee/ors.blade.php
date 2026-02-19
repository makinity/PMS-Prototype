@extends('layouts.employee')

@section('main-content')
    @php
        $orsGate = $orsGate ?? ['blocked' => true, 'reason' => 'ORS unavailable.'];
        $orsOptions = $orsOptions ?? [];
    @endphp

    <style>
        #ors-calendar .fc-col-header-cell {
            background-color: rgba(15, 23, 42, 0.85);
        }

        #ors-calendar .fc-col-header-cell-cushion,
        #ors-calendar .fc-daygrid-day-number {
            color: #e2e8f0;
        }

        #ors-calendar .fc-daygrid-day-events {
            margin-top: 4px;
        }

        #ors-calendar .fc-daygrid-event {
            margin-top: 4px;
        }

        #ors-calendar .fc-daygrid-more-link {
            color: #93c5fd;
            font-size: 12px;
        }

        #ors-calendar .fc-daygrid-day-frame {
            min-height: 110px;
        }

        .ors-summary-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.18rem 0.55rem;
            border-radius: 9999px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
        </div>

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

        @if ($errors->any())
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <!-- Stats Overview (DEMO LOCKED: 4 tasks total) -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">This Week</p>
                <p class="mt-1 text-2xl font-semibold text-white">{{ $orsStats['thisWeek'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Tasks logged (ORS)</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Drafts</p>
                <p class="mt-1 text-2xl font-semibold text-amber-300">{{ $orsStats['drafts'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Need submission</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Submitted</p>
                <p class="mt-1 text-2xl font-semibold text-blue-300">{{ $orsStats['submitted'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Eligible for MPOR summary</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Validated</p>
                <p class="mt-1 text-2xl font-semibold text-emerald-300">{{ $orsStats['validated'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">In SMPOR</p>
            </div>
        </div>

        <!-- Active Task Timer -->
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Task Tracking (single source)</h2>
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
                </div>
                <button type="button"
                        id="openLogTaskBtn"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-3 py-1.5 text-xs font-semibold text-slate-900 hover:bg-blue-600">
                    Log Task
                </button>
            </div>
            <div id="ors-calendar"></div>
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

            @php
                $orsModalBlocked = (bool) ($orsGate['blocked'] ?? false) || empty($orsOptions);
            @endphp
            <form id="ors-log-form" class="space-y-3" method="POST" action="{{ route('stage2.ors.store') }}">
                @csrf

                <!-- DATE -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">Date</label>
                    <input id="orsSelectedDate"
                        name="work_date"
                        type="text"
                        readonly
                        class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-400">
                </div>

                <!-- UWP OUTPUT -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">
                        UWP Output / Major Final Output
                    </label>
                    <select id="orsUwpOutput"
                        name="uwp_output_key"
                        class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                        @disabled($orsModalBlocked)
                        required>
                        <option value="">Select approved UWP output</option>
                        @foreach($orsOptions as $opt)
                            <option value="{{ $opt['output_key'] ?? '' }}">
                                {{ $opt['output_title'] ?? 'Untitled Output' }}
                            </option>
                        @endforeach
                    </select>
                    <p id="orsModalGateNote" class="mt-1 text-[11px] text-amber-300 {{ $orsModalBlocked ? '' : 'hidden' }}">
                        ORS locked: {{ $orsGate['reason'] ?? 'No committed IPCR targets available.' }}
                    </p>

                </div>

                <!-- TASK / ACTIVITY -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">
                        Task / Activity
                    </label>
                    <select id="orsTaskType"
                        name="ipcr_item_id"
                        class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                        @disabled($orsModalBlocked)
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
                        name="client_request_id"
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
                            name="output_type"
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
                            name="notes"
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
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-1.5 text-xs font-semibold text-slate-900 hover:bg-emerald-600">
                        Log Task
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
        <div class="flex w-full max-w-[95vw] sm:max-w-xl md:max-w-2xl lg:max-w-3xl
            max-h-[calc(100vh-3rem)] flex-col overflow-hidden
            rounded-2xl border border-slate-800 bg-slate-900 shadow-xl">
            <div class="shrink-0 border-b border-slate-800 bg-slate-900/95 p-5 backdrop-blur">
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

            <div class="flex-1 overflow-y-auto p-5 pb-24">
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
                        <label class="text-xs text-slate-300">Output Upload (multiple files allowed)</label>
                        <input id="taskDetailUpload"
                            type="file"
                            multiple
                            class="block w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-xs text-slate-200 file:mr-3 file:rounded-md file:border-0 file:bg-slate-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-200">
                        <p class="text-[11px] text-slate-400">
                            You can upload multiple evidence files. After submit, the entry is locked and visible in My Tasks (read-only) and MPOR/SMPOR.
                        </p>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2" id="taskDetailActions">
                        <button id="taskDetailStartBtn"
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg border border-blue-500/60 bg-blue-500/10 px-3 py-1.5 text-xs font-semibold text-blue-100 hover:bg-blue-500/20">
                            Start Task
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

            <div class="shrink-0 flex justify-end gap-2 border-t border-slate-800 bg-slate-900/95 p-4 backdrop-blur">
                <button onclick="closeOrsModal('taskDetailsModal')"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Day Summary Modal -->
    <div id="orsDaySummaryModal"
        role="dialog"
        aria-modal="true"
        class="ors-modal fixed inset-0 z-[59] hidden flex items-center justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6">
        <div class="flex w-full max-w-[95vw] sm:max-w-xl md:max-w-2xl max-h-[calc(100vh-3rem)] flex-col overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-xl">
            <div class="shrink-0 border-b border-slate-800 bg-slate-900/95 p-5 backdrop-blur">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-white">ORS Entries</h2>
                        <p id="daySummaryDateLabel" class="text-xs text-slate-400">--</p>
                        <p class="text-[11px] text-slate-500">Select an entry to view details.</p>
                    </div>
                    <button type="button" onclick="closeOrsModal('orsDaySummaryModal')" class="text-slate-400 hover:text-white">
                        x
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-5">
                <div id="daySummaryList" class="space-y-4"></div>
            </div>

            <div class="shrink-0 flex items-center justify-between gap-2 border-t border-slate-800 bg-slate-900/95 p-4 backdrop-blur">
                <button type="button" onclick="closeOrsModal('orsDaySummaryModal')" class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                    Close
                </button>
                <button id="daySummaryLogBtn" type="button" class="rounded-lg bg-blue-500 px-4 py-2 text-xs font-semibold text-slate-900 hover:bg-blue-600">
                    Log Task for this date
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        window.__ORS = {
            csrf: "{{ csrf_token() }}",
            submitUrlTemplate: "{{ route('stage2.ors.submit', ['orsEntry' => '__ID__']) }}"
        };

        document.addEventListener('DOMContentLoaded', function () {
            const STATE_META = {
                recording: { label: 'Recording', color: '#f59e0b', badge: 'border-amber-500/60 bg-amber-500/10 text-amber-200', editable: false },
                paused: { label: 'Paused', color: '#f59e0b', badge: 'border-amber-500/60 bg-amber-500/10 text-amber-200', editable: false },
                draft: { label: 'Draft (Stopped)', color: '#fbbf24', badge: 'border-amber-300/60 bg-amber-300/10 text-amber-100', editable: true },
                submitted: { label: 'Submitted (Locked)', color: '#3b82f6', badge: 'border-blue-500/60 bg-blue-500/10 text-blue-100', editable: false },
                locked: { label: 'Submitted (Locked)', color: '#10b981', badge: 'border-emerald-500/60 bg-emerald-500/10 text-emerald-100', editable: false },
                missing: { label: 'Missing / Overdue', color: '#ef4444', badge: 'border-red-500/60 bg-red-500/10 text-red-100', editable: false },
            };

            const orsGate = @json($orsGate);
            const orsOptions = @json($orsOptions);
            const orsOptionsByKey = Array.isArray(orsOptions)
                ? orsOptions.reduce((carry, option) => {
                    const key = String(option?.output_key || '').trim();
                    if (key) carry[key] = option;
                    return carry;
                }, {})
                : {};
            const orsConfig = window.__ORS || {};
            const orsBaseUrl = @json(url('/employee/ors'));
            const ORS_ACTIONS = {
                submit: String(orsConfig.submitUrlTemplate || ''),
            };
            const ORS_ROUTES = {
                start: (id) => `${orsBaseUrl}/${encodeURIComponent(String(id))}/start`,
                pause: (id) => `${orsBaseUrl}/${encodeURIComponent(String(id))}/pause`,
                resume: (id) => `${orsBaseUrl}/${encodeURIComponent(String(id))}/resume`,
                stop: (id) => `${orsBaseUrl}/${encodeURIComponent(String(id))}/stop`,
            };

            function actionUrl(template, id) {
                const encodedId = encodeURIComponent(String(id));
                return String(template).replace(/:id|%3Aid|__ID__/gi, encodedId);
            }

            async function postOrsAction(url) {
                const csrfToken = String(orsConfig.csrf || '')
                    || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || document.querySelector('#ors-log-form input[name="_token"]')?.value
                    || '';

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                });

                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const firstValidation = data?.errors
                        ? Object.values(data.errors)[0]?.[0]
                        : null;
                    const msg = firstValidation || data?.message || 'Request failed.';
                    throw new Error(msg);
                }
                return data;
            }

            const dbTasksRaw = @json($orsEntries ?? []);

            /**
             * DEMO LOCKED DATASET (Stage II) — Employee Assigned: Ramon Reyes ONLY
             * Required statuses:
             * - Jan 2, 2026: Submitted (Locked) — Same-day verification of OTC transactions
             * - Jan 4, 2026: Submitted (Locked) — All e-bank transactions scanned and encoded daily
             * - Jan 5, 2026: Recording — OR validation completed daily
             * - Jan 6, 2026: Missing / Overdue — Retrieval logs maintained for audit purposes
             */
            const demoTasks = [
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

            function outputTypeLabel(code) {
                const key = String(code || '').trim().toLowerCase();
                if (key === 'bsf_01') return 'Bank Statement Form (BSF-01)';
                if (key === 'official_receipt') return 'Official Receipt (OR)';
                if (key === 'scanned_doc') return 'Scanned Supporting Document';
                if (key === 'records_checklist') return 'Records Inventory Checklist';
                return code || '--';
            }

            const tasksFromDb = Array.isArray(dbTasksRaw) && dbTasksRaw.length
                ? dbTasksRaw.map((entry) => {
                    const rawState = String(entry?.state || 'draft').toLowerCase();
                    const state = rawState === 'validated' ? 'locked' : rawState;
                    const submittedAt = entry?.submittedAt ? new Date(entry.submittedAt) : null;
                    const totalSeconds = Number(entry?.totalSeconds ?? entry?.durationSeconds ?? 0);
                    const startedAt = entry?.startedAt ? new Date(entry.startedAt) : null;
                    const stoppedAt = entry?.stoppedAt ? new Date(entry.stoppedAt) : null;

                    return {
                        id: String(entry?.id ?? `task-db-${Date.now()}`),
                        title: String(entry?.title || '--'),
                        date: String(entry?.date || ''),
                        client: 'Revenue Collection Unit',
                        requestId: entry?.requestId || null,
                        uwpOutputId: '',
                        uwpOutputLabel: String(entry?.uwpOutputLabel || '--'),
                        output: outputTypeLabel(entry?.output),
                        notes: String(entry?.notes || 'No notes'),
                        quantity: String(entry?.quantity || ''),
                        rating: '--',
                        state: state,
                        output_state: (state === 'submitted' || state === 'locked') ? 'submitted' : 'none',
                        submittedAt: submittedAt,
                        evidenceRequired: true,
                        evidenceAttached: Boolean(entry?.evidenceAttached),
                        evidenceCount: Number(entry?.evidenceCount || 0),
                        evidenceFileName: null,
                        evidenceUploadedAt: null,
                        totalSeconds: Number.isFinite(totalSeconds) ? totalSeconds : 0,
                        startedAt: startedAt && !Number.isNaN(startedAt.getTime()) ? startedAt : null,
                        stoppedAt: stoppedAt && !Number.isNaN(stoppedAt.getTime()) ? stoppedAt : null,
                    };
                })
                : [];

            let tasks = tasksFromDb.length ? tasksFromDb : demoTasks;

            const TASK_DEFAULTS = {
                output_state: 'none',
                submittedAt: null,
                evidenceRequired: true,
                evidenceAttached: false,
                evidenceCount: 0,
                evidenceFileName: null,
                evidenceUploadedAt: null,
            };

            tasks = tasks.map((task) => ({
                ...TASK_DEFAULTS,
                ...task,
                submittedAt: task.submittedAt ? new Date(task.submittedAt) : null,
                evidenceUploadedAt: task.evidenceUploadedAt ? new Date(task.evidenceUploadedAt) : null,
                startedAt: task.startedAt ? new Date(task.startedAt) : null,
                stoppedAt: task.stoppedAt ? new Date(task.stoppedAt) : null,
            }));

            const uwpSelect = document.getElementById('orsUwpOutput');
            const taskSelect = document.getElementById('orsTaskType');
            const ipcrItemHidden = document.getElementById('orsIpcrItemIdHidden');
            const gateNote = document.getElementById('orsModalGateNote');

            function resetTaskOptions(placeholder = 'Select task / activity') {
                if (!taskSelect) return;
                taskSelect.innerHTML = '';
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = placeholder;
                taskSelect.appendChild(opt);
                taskSelect.value = '';
                if (ipcrItemHidden) ipcrItemHidden.value = '';
            }

            function populateTaskOptions(outputKey) {
                if (!taskSelect) return;
                resetTaskOptions('Select task / activity');

                const selectedOutput = orsOptionsByKey[String(outputKey || '').trim()] || null;
                const indicators = Array.isArray(selectedOutput?.indicators) ? selectedOutput.indicators : [];

                indicators.forEach((indicator) => {
                    const indicatorId = String(indicator?.ipcr_item_id || '').trim();
                    const indicatorLabel = String(indicator?.indicator_text || '').trim();
                    if (!indicatorId || !indicatorLabel) return;

                    const opt = document.createElement('option');
                    opt.value = indicatorId;
                    opt.textContent = indicatorLabel;
                    taskSelect.appendChild(opt);
                });
            }

            if (uwpSelect && taskSelect) {
                resetTaskOptions();

                const shouldDisable = Boolean(orsGate?.blocked) || !Array.isArray(orsOptions) || orsOptions.length === 0;
                if (shouldDisable) {
                    const reason = String(orsGate?.reason || 'No committed IPCR targets available.');
                    uwpSelect.disabled = true;
                    taskSelect.disabled = true;
                    if (gateNote) {
                        gateNote.textContent = `ORS locked: ${reason}`;
                        gateNote.classList.remove('hidden');
                    }
                    if (uwpSelect.options.length > 0) {
                        uwpSelect.options[0].textContent = `ORS locked: ${reason}`;
                    }
                    resetTaskOptions('Task selection unavailable');
                } else {
                    if (gateNote) {
                        gateNote.classList.add('hidden');
                    }

                    uwpSelect.addEventListener('change', () => {
                        populateTaskOptions(uwpSelect.value);
                    });
                    taskSelect.addEventListener('change', () => {
                        if (ipcrItemHidden) {
                            ipcrItemHidden.value = taskSelect.value || '';
                        }
                    });
                }
            }

            // DEMO: exactly one active timer -> Jan 5 recording
            let activeTaskId = tasks.find(t => t.state === 'recording' || t.state === 'paused')?.id || null;
            let detailTaskId = null;
            let __returnToModalId = null;
            let daySummaryDate = null;
            let byDate = {};
            let byDateStatus = {};

            const SUMMARY_STATE_LABELS = {
                draft: 'Draft',
                recording: 'Recording',
                paused: 'Paused',
                submitted: 'Submitted',
                locked: 'Submitted',
            };

            const daySummaryDateLabelEl = document.getElementById('daySummaryDateLabel');
            const daySummaryListEl = document.getElementById('daySummaryList');
            const daySummaryLogBtn = document.getElementById('daySummaryLogBtn');

            function summaryStateLabel(state) {
                const key = String(state || '').toLowerCase();
                return SUMMARY_STATE_LABELS[key] || (STATE_META[key]?.label || key || 'Unknown');
            }

            function formatDateLabel(dateStr) {
                if (!dateStr) return '--';
                const parsed = new Date(`${dateStr}T00:00:00`);
                if (Number.isNaN(parsed.getTime())) return dateStr;
                return parsed.toLocaleDateString([], { month: 'long', day: 'numeric', year: 'numeric' });
            }

            function rebuildCalendarBuckets() {
                byDate = {};
                byDateStatus = {};

                tasks.forEach((task) => {
                    const dateKey = String(task?.date || '').trim();
                    if (!dateKey) return;

                    const stateKey = String(task?.state || 'draft').toLowerCase();
                    if (stateKey === 'missing') return;

                    if (!byDate[dateKey]) byDate[dateKey] = [];
                    byDate[dateKey].push(task);

                    if (!byDateStatus[dateKey]) byDateStatus[dateKey] = {};
                    if (!byDateStatus[dateKey][stateKey]) byDateStatus[dateKey][stateKey] = [];
                    byDateStatus[dateKey][stateKey].push(task);
                });
            }

            const calendarEl = document.getElementById('ors-calendar');
            const today = new Date();
            const currentYmd = today.toISOString().slice(0, 10);
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: currentYmd,
                height: 'auto',
                contentHeight: 600,
                dayMaxEventRows: 3,
                dayMaxEvents: true,
                moreLinkClick: 'popover',
                eventDisplay: 'block',
                fixedWeekCount: false,
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
                    const dateStr = info.dateStr;
                    if (Array.isArray(byDate[dateStr]) && byDate[dateStr].length > 0) {
                        openDaySummaryModal(dateStr, null);
                        return;
                    }

                    const dateInput = document.getElementById('orsSelectedDate');
                    if (dateInput) dateInput.value = dateStr;
                    openOrsModal('orsTaskModal');
                },
                eventClick(info) {
                    const props = info.event.extendedProps || {};
                    if (props.type === 'summary') {
                        openDaySummaryModal(props.date, props.status || null);
                        return;
                    }
                    openTaskDetails(info.event.id);
                },
                events: [],
                eventContent(arg) {
                    const props = arg.event.extendedProps || {};
                    if (props.type === 'summary') {
                        const stateKey = String(props.status || '').toLowerCase();
                        const meta = STATE_META[stateKey] || STATE_META.draft;
                        const count = Number(props.count || 0);
                        const labelText = `${summaryStateLabel(stateKey)} (${count})`;

                        const wrapper = document.createElement('div');
                        wrapper.classList.add('text-[11px]', 'leading-tight', 'px-1', 'py-[1px]');
                        wrapper.innerHTML = `<span class="ors-summary-pill" style="color:${meta.color}; border-color:${meta.color};">${labelText}</span>`;
                        return { domNodes: [wrapper] };
                    }

                    const meta = STATE_META[props.state] || STATE_META.draft;
                    const label = meta.label.length > 16 ? meta.label.substring(0, 13) + '...' : meta.label;
                    const title = arg.event.title.length > 28 ? arg.event.title.substring(0, 25) + '...' : arg.event.title;
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('text-[11px]', 'leading-tight', 'px-1', 'py-[1px]');
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

            window.openOrsModalStack = function (modalId) {
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
                    if (__returnToModalId === 'orsDaySummaryModal') {
                        __returnToModalId = null;
                    }
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
                    const taskDetailsModal = document.getElementById('taskDetailsModal');
                    if (taskDetailsModal && !taskDetailsModal.classList.contains('hidden')) {
                        closeOrsModal('taskDetailsModal');
                        return;
                    }
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

            function isDbBackedTask(task) {
                return /^\d+$/.test(String(task?.id ?? '').trim());
            }

            function applyServerEntryToTask(task, entry) {
                const status = String(entry?.status || task.state || 'draft').toLowerCase();
                task.state = status === 'validated' ? 'locked' : status;
                task.totalSeconds = Number(entry?.total_seconds || 0);
                task.startedAt = entry?.started_at ? new Date(entry.started_at) : null;
                task.stoppedAt = entry?.stopped_at ? new Date(entry.stopped_at) : null;

                if (task.state === 'recording' || task.state === 'paused') {
                    activeTaskId = task.id;
                } else if (activeTaskId === task.id) {
                    activeTaskId = null;
                }
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
                const seconds = totalSeconds % 60;

                if (hours > 0) {
                    return `${hours}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                }

                return `${minutes}:${String(seconds).padStart(2, '0')}`;
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
                if (typeof task.totalSeconds === 'number' || task.startedAt) {
                    const baseMs = Math.max(0, Number(task.totalSeconds || 0)) * 1000;
                    const startedAt = task.startedAt instanceof Date ? task.startedAt : (task.startedAt ? new Date(task.startedAt) : null);
                    if (task.state === 'recording' && startedAt && !Number.isNaN(startedAt.getTime())) {
                        return baseMs + (Date.now() - startedAt.getTime());
                    }
                    return baseMs;
                }

                const base = task.durationMs || 0;
                if (task.state === 'recording' && task.startTime) {
                    return base + (Date.now() - task.startTime.getTime());
                }
                return base;
            }

            function refreshCalendar() {
                rebuildCalendarBuckets();
                calendar.removeAllEvents();

                const stateOrder = ['recording', 'paused', 'draft', 'submitted', 'locked'];
                const stateIndex = (state) => {
                    const idx = stateOrder.indexOf(state);
                    return idx === -1 ? Number.MAX_SAFE_INTEGER : idx;
                };

                Object.keys(byDateStatus).sort().forEach((dateKey) => {
                    const statusMap = byDateStatus[dateKey] || {};
                    Object.keys(statusMap)
                        .sort((left, right) => stateIndex(left) - stateIndex(right) || left.localeCompare(right))
                        .forEach((stateKey) => {
                            const count = Array.isArray(statusMap[stateKey]) ? statusMap[stateKey].length : 0;
                            if (count <= 0) return;

                            calendar.addEvent({
                                id: `sum-${dateKey}-${stateKey}`,
                                title: `${summaryStateLabel(stateKey)} (${count})`,
                                start: dateKey,
                                allDay: true,
                                color: 'transparent',
                                extendedProps: {
                                    type: 'summary',
                                    date: dateKey,
                                    status: stateKey,
                                    count: count,
                                }
                            });
                        });
                });
            }

            function openDaySummaryModal(dateStr, statusFilter = null) {
                const dateKey = String(dateStr || '').trim();
                if (!dateKey) return;

                daySummaryDate = dateKey;
                const filterKey = statusFilter ? String(statusFilter).toLowerCase() : null;
                if (daySummaryDateLabelEl) {
                    daySummaryDateLabelEl.textContent = filterKey
                        ? `${formatDateLabel(dateKey)} - ${summaryStateLabel(filterKey)}`
                        : formatDateLabel(dateKey);
                }

                if (!daySummaryListEl) {
                    openOrsModal('orsDaySummaryModal');
                    return;
                }

                daySummaryListEl.innerHTML = '';

                const statusMap = byDateStatus[dateKey] || {};
                const stateOrder = ['recording', 'paused', 'draft', 'submitted', 'locked'];
                let statusKeys = filterKey ? [filterKey] : Object.keys(statusMap);
                statusKeys = statusKeys
                    .filter((stateKey) => Array.isArray(statusMap[stateKey]) && statusMap[stateKey].length > 0)
                    .sort((left, right) => {
                        const l = stateOrder.indexOf(left);
                        const r = stateOrder.indexOf(right);
                        const li = l === -1 ? Number.MAX_SAFE_INTEGER : l;
                        const ri = r === -1 ? Number.MAX_SAFE_INTEGER : r;
                        return li - ri || left.localeCompare(right);
                    });

                if (statusKeys.length === 0) {
                    const empty = document.createElement('p');
                    empty.className = 'rounded-lg border border-slate-800 bg-slate-950/60 px-3 py-2 text-sm text-slate-300';
                    empty.textContent = 'No ORS entries for this date.';
                    daySummaryListEl.appendChild(empty);
                } else {
                    statusKeys.forEach((stateKey) => {
                        const meta = STATE_META[stateKey] || STATE_META.draft;
                        const entries = statusMap[stateKey];

                        const section = document.createElement('div');
                        section.className = 'rounded-xl border border-slate-800 bg-slate-950/40 p-3';

                        if (!filterKey) {
                            const header = document.createElement('div');
                            header.className = 'mb-2 flex items-center justify-between';
                            header.innerHTML = `
                                <span class="text-sm font-semibold text-white">${summaryStateLabel(stateKey)}</span>
                                <span class="rounded-full border px-2 py-[2px] text-[11px]" style="color:${meta.color}; border-color:${meta.color};">${entries.length}</span>
                            `;
                            section.appendChild(header);
                        }

                        const list = document.createElement('div');
                        list.className = 'space-y-2';

                        entries.forEach((entry) => {
                            const itemBtn = document.createElement('button');
                            itemBtn.type = 'button';
                            itemBtn.className = 'w-full rounded-lg border border-slate-800 bg-slate-900/50 px-3 py-2 text-left hover:bg-slate-800';
                            itemBtn.innerHTML = `
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-white">${entry.title || '--'}</p>
                                        <p class="mt-1 text-[11px] text-slate-400">Duration: ${formatDuration(computeElapsed(entry))}</p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="rounded-full border px-2 py-[2px] text-[11px]" style="color:${meta.color}; border-color:${meta.color};">${summaryStateLabel(stateKey)}</span>
                                        <p class="mt-1 text-[11px] ${entry.evidenceAttached ? 'text-emerald-300' : 'text-slate-400'}">${entry.evidenceAttached ? 'Evidence' : 'No evidence'}</p>
                                    </div>
                                </div>
                            `;
                            itemBtn.addEventListener('click', () => {
                                __returnToModalId = 'orsDaySummaryModal';
                                openTaskDetails(entry.id);
                            });
                            list.appendChild(itemBtn);
                        });

                        section.appendChild(list);
                        daySummaryListEl.appendChild(section);
                    });
                }

                openOrsModal('orsDaySummaryModal');
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
                    if (task.evidenceAttached) {
                        const evidenceCount = Number(task.evidenceCount || 0);
                        const label = evidenceCount > 1
                            ? `${evidenceCount} files attached`
                            : (task.evidenceFileName || (evidenceCount === 1 ? '1 file attached' : '--'));
                        const uploadedAtText = task.evidenceUploadedAt
                            ? ` (${formatDateTime(task.evidenceUploadedAt)})`
                            : '';
                        evidenceFileEl.textContent = `${label}${uploadedAtText}`;
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
                        const files = Array.from(this.files || []);
                        if (files.length > 0) {
                            task.evidenceAttached = true;
                            task.evidenceCount = files.length;
                            task.evidenceFileName = files.length === 1 ? files[0].name : `${files.length} files selected`;
                            task.evidenceUploadedAt = new Date();
                        } else if (!task.evidenceAttached && Number(task.evidenceCount || 0) <= 0) {
                            task.evidenceAttached = false;
                            task.evidenceCount = 0;
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

                if (__returnToModalId === 'orsDaySummaryModal') {
                    openOrsModalStack('taskDetailsModal');
                } else {
                    openOrsModal('taskDetailsModal');
                }
            }

            async function startTask(taskId) {
                const task = getTaskById(taskId);
                if (!task) return false;
                if (isLockedState(task.state)) return false;

                if (isDbBackedTask(task)) {
                    try {
                        const data = await postOrsAction(ORS_ROUTES.start(task.id));
                        if (!data?.ok || !data?.entry) {
                            throw new Error('Unable to start task.');
                        }
                        applyServerEntryToTask(task, data.entry);
                    } catch (error) {
                        const rawMessage = String(error?.message || 'Unable to start task.');
                        const message = rawMessage.toLowerCase().includes('currently recording')
                            ? 'Only one task can be recording at a time.'
                            : rawMessage;
                        alert(message);
                        return false;
                    }
                } else {
                    if (activeTaskId && activeTaskId !== taskId) return false;
                    task.state = 'recording';
                    task.startTime = new Date();
                    activeTaskId = taskId;
                }

                refreshCalendar();
                updateActivePanel();
                if (detailTaskId === taskId) openTaskDetails(taskId);
                return true;
            }

            async function pauseTask(taskId) {
                const task = getTaskById(taskId);
                if (!task || task.state !== 'recording') return;

                if (isDbBackedTask(task)) {
                    try {
                        const data = await postOrsAction(ORS_ROUTES.pause(task.id));
                        if (!data?.ok || !data?.entry) {
                            throw new Error('Unable to pause task.');
                        }
                        applyServerEntryToTask(task, data.entry);
                    } catch (error) {
                        alert(error?.message || 'Unable to pause task.');
                        return false;
                    }
                } else {
                    if (task.startTime) {
                        task.durationMs = (task.durationMs || 0) + (Date.now() - task.startTime.getTime());
                    }
                    task.startTime = null;
                    task.state = 'paused';
                    activeTaskId = taskId;
                }

                refreshCalendar();
                updateActivePanel();
                if (detailTaskId === taskId) openTaskDetails(taskId);
                return true;
            }

            async function resumeTask(taskId) {
                const task = getTaskById(taskId);
                if (!task || task.state !== 'paused') return;

                if (isDbBackedTask(task)) {
                    try {
                        const data = await postOrsAction(ORS_ROUTES.resume(task.id));
                        if (!data?.ok || !data?.entry) {
                            throw new Error('Unable to resume task.');
                        }
                        applyServerEntryToTask(task, data.entry);
                    } catch (error) {
                        const rawMessage = String(error?.message || 'Unable to resume task.');
                        const message = rawMessage.toLowerCase().includes('currently recording')
                            ? 'Only one task can be recording at a time.'
                            : rawMessage;
                        alert(message);
                        return false;
                    }
                } else {
                    if (activeTaskId && activeTaskId !== taskId) return false;
                    task.startTime = new Date();
                    task.state = 'recording';
                    activeTaskId = taskId;
                }

                refreshCalendar();
                updateActivePanel();
                if (detailTaskId === taskId) openTaskDetails(taskId);
                return true;
            }

            async function stopTask(taskId) {
                const task = getTaskById(taskId);
                if (!task) return;

                if (isDbBackedTask(task)) {
                    try {
                        const data = await postOrsAction(ORS_ROUTES.stop(task.id));
                        if (!data?.ok || !data?.entry) {
                            throw new Error('Unable to stop task.');
                        }
                        applyServerEntryToTask(task, data.entry);
                    } catch (error) {
                        alert(error?.message || 'Unable to stop task.');
                        return false;
                    }
                } else {
                    syncQuantityFromInput(task);

                    if (task.state === 'recording' && task.startTime) {
                        task.durationMs = (task.durationMs || 0) + (Date.now() - task.startTime.getTime());
                    }
                    task.startTime = null;
                    task.state = 'draft';

                    if (activeTaskId === taskId) activeTaskId = null;
                }

                refreshCalendar();
                updateActivePanel();
                if (detailTaskId === taskId) openTaskDetails(taskId);
                return true;
            }

            async function submitTask(taskId) {
                const task = getTaskById(taskId);
                if (!task) return false;
                if (isLockedState(task.state)) return false;

                const qtyValue = syncQuantityFromInput(task);
                if (!qtyValue) {
                    alert('Quantity is required before submitting an ORS entry.');
                    return false;
                }

                const uploadInput = document.getElementById('taskDetailUpload');
                const selectedFiles = Array.from(uploadInput?.files || []);
                const hasSelectedFiles = selectedFiles.length > 0;
                const hasExistingEvidence = Boolean(task.evidenceAttached) || Number(task.evidenceCount || 0) > 0;
                if (!hasSelectedFiles && !hasExistingEvidence) {
                    alert('Evidence attachment is required before submitting an ORS entry.');
                    return false;
                }

                if (isDbBackedTask(task)) {
                    try {
                        const url = actionUrl(ORS_ACTIONS.submit, task.id);
                        if (!url) {
                            throw new Error('Submit route is not configured.');
                        }

                        const fd = new FormData();
                        fd.append('quantity', qtyValue);
                        selectedFiles.forEach((file) => {
                            fd.append('evidence[]', file);
                        });

                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': String(orsConfig.csrf || ''),
                                'Accept': 'application/json',
                            },
                            body: fd,
                        });

                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            const firstValidation = data?.errors
                                ? Object.values(data.errors)[0]?.[0]
                                : null;
                            throw new Error(firstValidation || data?.message || 'Submit failed. Please try again.');
                        }

                        if (!data?.ok) {
                            throw new Error('Unable to submit ORS entry.');
                        }

                        task.quantity = qtyValue;
                        task.state = 'submitted';
                        task.output_state = 'submitted';
                        task.submittedAt = data?.submitted_at ? new Date(data.submitted_at) : new Date();
                        task.startedAt = null;
                        task.stoppedAt = data?.locked_at ? new Date(data.locked_at) : new Date();
                        task.totalSeconds = Number(data?.total_seconds || 0);
                        task.evidenceCount = Number(data?.evidence_count || task.evidenceCount || 0);
                        task.evidenceAttached = task.evidenceCount > 0 || hasExistingEvidence || hasSelectedFiles;
                        if (task.evidenceCount > 1) {
                            task.evidenceFileName = `${task.evidenceCount} files attached`;
                        } else {
                            task.evidenceFileName = data?.evidence?.file_name
                                || (selectedFiles.length === 1 ? selectedFiles[0].name : task.evidenceFileName);
                        }
                        task.evidenceUploadedAt = data?.evidence?.uploaded_at
                            ? new Date(data.evidence.uploaded_at)
                            : (hasSelectedFiles ? new Date() : task.evidenceUploadedAt);
                        if (uploadInput) {
                            uploadInput.value = '';
                        }
                        if (activeTaskId === taskId) activeTaskId = null;

                        refreshCalendar();
                        updateActivePanel();
                        if (detailTaskId === taskId) openTaskDetails(taskId);
                        return true;
                    } catch (error) {
                        const message = String(error?.message || 'Submit failed. Please try again.');
                        alert(message);
                        return false;
                    }
                }

                task.quantity = qtyValue;
                task.state = 'submitted';
                task.output_state = 'submitted';
                task.submittedAt = new Date();
                task.startedAt = null;
                task.stoppedAt = new Date();
                if (hasSelectedFiles) {
                    task.evidenceCount = selectedFiles.length;
                    task.evidenceAttached = true;
                    task.evidenceFileName = selectedFiles.length > 1
                        ? `${selectedFiles.length} files attached`
                        : selectedFiles[0].name;
                    task.evidenceUploadedAt = new Date();
                } else {
                    task.evidenceAttached = hasExistingEvidence;
                    task.evidenceCount = Number(task.evidenceCount || (hasExistingEvidence ? 1 : 0));
                }
                if (uploadInput) {
                    uploadInput.value = '';
                }
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
                startEl.textContent = formatTime(task.startedAt || task.startTime);
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

            function wireLogButton() {
                const btn = document.getElementById('openLogTaskBtn');
                if (!btn) return;

                btn.addEventListener('click', () => {
                    const today = new Date().toISOString().split('T')[0];

                    const dateInput = document.getElementById('orsSelectedDate');
                    if (dateInput) dateInput.value = today;

                    openOrsModal('orsTaskModal');
                });
            }

            function wireDaySummaryLogButton() {
                if (!daySummaryLogBtn) return;
                daySummaryLogBtn.addEventListener('click', () => {
                    if (!daySummaryDate) return;
                    const dateInput = document.getElementById('orsSelectedDate');
                    if (dateInput) dateInput.value = daySummaryDate;
                    openOrsModal('orsTaskModal');
                });
            }

            wireLogButton();
            wireDaySummaryLogButton();
            refreshCalendar();
            updateActivePanel();

            setInterval(() => {
                if (activeTaskId) updateActivePanel();
                if (detailTaskId) updateDetailElapsed();
            }, 1000);

            async function runWithLoading(button, loadingText, actionFn) {
                if (!button || typeof actionFn !== 'function') return;
                if (button.dataset.loadingActive === 'true') return;

                button.dataset.loadingActive = 'true';
                setButtonLoading(button, true, loadingText);

                try {
                    await actionFn();
                } finally {
                    setButtonLoading(button, false);
                    delete button.dataset.loadingActive;
                }
            }

            document.getElementById('taskDetailStartBtn')?.addEventListener('click', () => {
                startTask(detailTaskId);
            });
            document.getElementById('taskDetailPauseBtn')?.addEventListener('click', () => {
                pauseTask(detailTaskId);
            });
            document.getElementById('taskDetailResumeBtn')?.addEventListener('click', () => {
                resumeTask(detailTaskId);
            });
            document.getElementById('taskDetailStopBtn')?.addEventListener('click', () => {
                stopTask(detailTaskId);
            });
            document.getElementById('taskDetailSubmitBtn')?.addEventListener('click', (e) => {
                runWithLoading(e.currentTarget, 'Submitting...', () => submitTask(detailTaskId));
            });
        });
    </script>
    @endpush

@endsection
