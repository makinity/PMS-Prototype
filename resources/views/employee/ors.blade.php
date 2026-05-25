@extends('layouts.employee')

@section('main-content')
    @php
        $orsGate = $orsGate ?? ['blocked' => true, 'reason' => 'ORS unavailable.'];
        $orsOptions = $orsOptions ?? [];
        $mporMonthLocks = $mporMonthLocks ?? [];
        $currentMporLock = $currentMporLock ?? null;
    @endphp

    <style>
        #ors-calendar {
            width: 100%;
            overflow-x: hidden;
        }
        #ors-calendar .fc {
            max-width: 100%;
        }
        #ors-calendar .fc-scrollgrid {
            width: 100% !important;
        }
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
            margin: 4px 6px;
            border-radius: 9999px;
            padding: 2px 8px;
            font-size: 11px;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #ors-calendar .fc-daygrid-more-link {
            color: #93c5fd;
            font-size: 12px;
        }

        #ors-calendar .fc-daygrid-day-frame {
            min-height: 110px;
        }

        #ors-calendar .fc-daygrid-event.ors-summary-event {
            background: rgba(59, 130, 246, 0.18);
            border: 1px solid rgba(59, 130, 246, 0.45);
            color: #dbeafe;
        }

        #ors-calendar .fc-daygrid-event.ors-summary-event.is-rated {
            background: rgba(6, 182, 212, 0.18);
            border: 1px solid rgba(6, 182, 212, 0.45);
            color: #cffafe;
        }

        #ors-calendar .fc-daygrid-event.ors-summary-event.is-submitted {
            background: rgba(59, 130, 246, 0.18);
            border: 1px solid rgba(59, 130, 246, 0.45);
            color: #dbeafe;
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

        @if($currentMporLock)
            <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                <p class="font-semibold">Current month ORS locked</p>
                <p class="mt-1">{{ $currentMporLock['reason'] ?? 'ORS is locked because the MPOR is already submitted.' }}</p>
            </div>
        @endif

        <!-- Stats Overview (DEMO LOCKED: 4 tasks total) -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                <p class="text-xs text-slate-400">This Week</p>
                <p class="mt-1 text-2xl font-semibold text-white">{{ $orsStats['thisWeek'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Tasks logged (ORS)</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                <p class="text-xs text-slate-400">Drafts</p>
                <p class="mt-1 text-2xl font-semibold text-amber-300">{{ $orsStats['drafts'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Need submission</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                <p class="text-xs text-slate-400">Submitted</p>
                <p class="mt-1 text-2xl font-semibold text-blue-300">{{ $orsStats['submitted'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Eligible for MPOR summary</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                <p class="text-xs text-slate-400">Validated</p>
                <p class="mt-1 text-2xl font-semibold text-emerald-300">{{ $orsStats['validated'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">In SMPOR</p>
            </div>
        </div>

        <!-- Active Task Timer -->
        <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Task Tracking (single source)</h2>
                </div>
                <span class="rounded-full border border-emerald-600/60 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                    ONE ACTIVE TIMER
                </span>
            </div>

            <div id="active-task-empty" class="mt-4 rounded-lg border border-gray-700 bg-slate-900/40 p-4 text-sm text-slate-300">
                No task is recording. Open a Draft from the calendar task details to start timing.
                Starting a second task is blocked until the current one is stopped or submitted.
            </div>

            <div id="active-task-card" class="mt-4 hidden rounded-lg border border-gray-700 bg-slate-900/40 p-4 text-sm">
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
                        <span data-button-label>Pause</span>
                        <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-slate-500/40 border-t-slate-200"></span>
                    </button>
                    <button id="activeStopBtn"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-500/50 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-100 hover:border-amber-500 hover:bg-amber-500/20">
                        <span data-button-label>Stop (Draft)</span>
                        <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-amber-200/30 border-t-amber-100"></span>
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
        <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4">
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

        <div class="w-full max-w-md max-h-[calc(100vh-3rem)] overflow-y-auto rounded-2xl border border-gray-700 bg-slate-900 p-5 shadow-xl">

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
                        class="mt-1 w-full rounded-lg border border-gray-700 bg-slate-950 px-3 py-2 text-sm text-slate-400">
                </div>

                <!-- UWP OUTPUT -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">
                        UWP Output / Major Final Output
                    </label>
                    <select id="orsUwpOutput"
                        name="uwp_output_key"
                        class="mt-1 w-full rounded-lg border border-gray-700 bg-slate-950 px-3 py-2 text-sm text-slate-200"
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
                        class="mt-1 w-full rounded-lg border border-gray-700 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                        @disabled($orsModalBlocked)
                        required>
                        <option value="">Select task / activity</option>
                    </select>

                </div>

                <!-- SUPERVISOR -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">
                        Supervisor
                    </label>
                    <select id="orsSupervisorId"
                        name="supervisor_id"
                        class="mt-1 w-full rounded-lg border border-gray-700 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                        @disabled($orsModalBlocked)
                        required>
                        <option value="">Select supervisor</option>
                        @foreach(($supervisors ?? []) as $supervisor)
                            <option value="{{ is_array($supervisor) ? ($supervisor['id'] ?? '') : ($supervisor->id ?? '') }}">
                                {{ is_array($supervisor) ? ($supervisor['name'] ?? '--') : ($supervisor->name ?? '--') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- NOTES -->
                <div>
                    <label class="text-[11px] uppercase text-slate-400">Notes (optional)</label>
                    <textarea id="orsNotes"
                            name="notes"
                            rows="2"
                            class="mt-1 w-full rounded-lg border border-gray-700 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                            placeholder="Exceptions, clarifications, or additional context"></textarea>
                </div>

                <!-- SYSTEM RULE -->
                <div class="rounded-lg border border-gray-700 bg-slate-950 px-3 py-2 text-[11px] text-slate-400">
                    • Tasks must align with approved UWP outputs<br>
                    • Duration is tracked automatically<br>
                    • Draft until submitted inside ORS
                </div>

                <!-- ACTIONS (RESTORED â€“ LOADING SAFE) -->
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="closeOrsModal('orsTaskModal')"
                            class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs text-slate-300 hover:bg-slate-800">
                        Cancel
                    </button>

                    <button
                        id="orsLogTaskSubmitBtn"
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-1.5 text-xs font-semibold text-slate-900 hover:bg-emerald-600">
                        <span data-button-label>Log Task</span>
                        <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-emerald-900/30 border-t-emerald-900"></span>
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
            rounded-2xl border border-gray-700 bg-slate-900 shadow-2xl shadow-black/40">

            {{-- Header --}}
            <div class="shrink-0 border-b border-gray-700 bg-gradient-to-r from-slate-900 via-slate-900 to-slate-800/80 px-6 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <h2 class="text-lg font-bold leading-snug text-white" id="taskDetailTitle">Task Details</h2>
                        <p class="mt-1 text-xs text-slate-400" id="taskDetailDate">Date: --</p>
                    </div>
                    <button onclick="closeOrsModal('taskDetailsModal')"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-700 text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="mt-3">
                    <span id="taskDetailStatusBadge" class="status-chip border-slate-700 bg-slate-800 text-slate-200">--</span>
                </div>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto px-6 py-5 pb-24">

                {{-- Info Grid --}}
                <div class="space-y-3">
                    <div class="rounded-xl border border-slate-700/60 bg-slate-950/40 px-4 py-3">
                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Supervisor</p>
                        <p class="mt-1 text-sm font-medium text-slate-100" id="taskDetailClient">--</p>
                    </div>
                    <div class="rounded-xl border border-slate-700/60 bg-slate-950/40 px-4 py-3">
                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">MFO / UWP Output</p>
                        <p class="mt-1 text-sm font-medium text-slate-100" id="taskDetailMfo">--</p>
                    </div>
                </div>

                {{-- Quantity --}}
                <div class="mt-4">
                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Quantity</p>
                    <input id="taskDetailQuantity"
                           type="text"
                           style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                           class="mt-1.5 w-full rounded-xl border px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500"
                           placeholder="e.g., 12 transactions">
                </div>

                {{-- Status & Duration --}}
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-700/60 bg-slate-950/40 px-4 py-3">
                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Status</p>
                        <p class="mt-1 text-sm font-semibold text-slate-100" id="taskDetailStatusText">--</p>
                    </div>
                    <div class="rounded-xl border border-slate-700/60 bg-slate-950/40 px-4 py-3">
                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Duration</p>
                        <p class="mt-1 text-sm font-semibold tabular-nums text-slate-100" id="taskDetailDuration">--</p>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mt-4 rounded-xl border border-slate-700/60 bg-slate-950/40 px-4 py-3">
                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Notes</p>
                    <p class="mt-1 text-sm text-slate-200" id="taskDetailNotes">--</p>
                </div>

                {{-- Submission & Output Section --}}
                <div class="mt-5 rounded-xl border border-slate-700/60 bg-slate-950/30 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Submission & Output</p>

                    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Output State</p>
                            <span id="taskDetailOutputState"
                                class="mt-1.5 inline-flex rounded-full border border-slate-700 bg-slate-800 px-2.5 py-1 text-xs font-medium text-slate-200">
                                No output yet
                            </span>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Evidence</p>
                            <span id="taskDetailEvidenceState"
                                class="mt-1.5 inline-flex rounded-full border border-slate-700 bg-slate-800 px-2.5 py-1 text-xs font-medium text-slate-200">
                                None
                            </span>
                            <p id="taskDetailEvidenceFile" class="mt-1.5 max-w-full truncate text-[11px] text-slate-400">--</p>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Submitted At</p>
                            <p class="mt-1.5 text-sm text-slate-200" id="taskDetailSubmittedAt">--</p>
                        </div>
                    </div>

                    {{-- Upload --}}
                    <div class="mt-4">
                        <label class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Output Upload</label>
                        <input id="taskDetailUpload"
                            type="file"
                            multiple
                            class="mt-1.5 block w-full rounded-xl border border-slate-700 bg-slate-950/60 px-3 py-2.5 text-xs text-slate-200 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-500/20 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-blue-200 hover:file:bg-blue-500/30">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-4 flex flex-wrap justify-end gap-2" id="taskDetailActions">
                        <button id="taskDetailStartBtn"
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg border border-blue-500/50 bg-blue-500/10 px-3.5 py-2 text-xs font-semibold text-blue-100 transition hover:bg-blue-500/20">
                            <i class="fa-solid fa-play text-[10px]"></i>
                            <span data-button-label>Start Task</span>
                            <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-blue-200/30 border-t-blue-100"></span>
                        </button>
                        <button id="taskDetailPauseBtn"
                                type="button"
                                class="hidden inline-flex items-center gap-2 rounded-lg border border-slate-600 bg-slate-800/60 px-3.5 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-700">
                            <i class="fa-solid fa-pause text-[10px]"></i>
                            <span data-button-label>Pause</span>
                            <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-slate-500/40 border-t-slate-200"></span>
                        </button>
                        <button id="taskDetailResumeBtn"
                                type="button"
                                class="hidden inline-flex items-center gap-2 rounded-lg border border-amber-500/50 bg-amber-500/10 px-3.5 py-2 text-xs font-semibold text-amber-100 transition hover:bg-amber-500/20">
                            <i class="fa-solid fa-play text-[10px]"></i>
                            <span data-button-label>Resume</span>
                            <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-amber-200/30 border-t-amber-100"></span>
                        </button>
                        <button id="taskDetailStopBtn"
                                type="button"
                                class="hidden inline-flex items-center gap-2 rounded-lg border border-amber-500/50 bg-amber-500/10 px-3.5 py-2 text-xs font-semibold text-amber-100 transition hover:bg-amber-500/20">
                            <i class="fa-solid fa-stop text-[10px]"></i>
                            <span data-button-label>Stop (Draft)</span>
                            <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-amber-200/30 border-t-amber-100"></span>
                        </button>
                        <button id="taskDetailSubmitBtn"
                                type="button"
                                class="hidden inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-3.5 py-2 text-xs font-semibold text-slate-900 transition hover:bg-emerald-400">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                            <span data-button-label>Submit for Review</span>
                            <span data-button-spinner class="hidden h-3 w-3 animate-spin rounded-full border-2 border-emerald-900/30 border-t-emerald-900"></span>
                        </button>
                    </div>

                    <p id="taskDetailLockMessage" class="mt-2 hidden text-[11px] text-emerald-300">
                        Submitted (Locked) — visible in MPOR monthly summary. SMPOR is system-generated after validation.
                    </p>
                    <p id="taskDetailDraftMessage" class="mt-2 text-[11px] text-slate-400"></p>
                </div>
            </div>

            <div class="shrink-0 flex justify-end border-t border-gray-700 bg-slate-900/95 px-6 py-4 backdrop-blur">
                <button onclick="closeOrsModal('taskDetailsModal')"
                        class="rounded-xl border border-slate-700 px-5 py-2 text-xs font-medium text-slate-300 transition hover:bg-slate-800">
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
        <div class="flex w-full max-w-[95vw] sm:max-w-xl md:max-w-2xl max-h-[calc(100vh-3rem)] flex-col overflow-hidden rounded-2xl border border-gray-700 bg-slate-900 shadow-xl">
            <div class="shrink-0 border-b border-gray-700 bg-slate-900/95 p-5 backdrop-blur">
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

            <div class="shrink-0 flex items-center justify-between gap-2 border-t border-gray-700 bg-slate-900/95 p-4 backdrop-blur">
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
                rated: { label: 'Rated (Locked)', color: '#06b6d4', badge: 'border-cyan-500/60 bg-cyan-500/10 text-cyan-100', editable: false },
                locked: { label: 'Submitted (Locked)', color: '#10b981', badge: 'border-emerald-500/60 bg-emerald-500/10 text-emerald-100', editable: false },
                missing: { label: 'Missing / Overdue', color: '#ef4444', badge: 'border-red-500/60 bg-red-500/10 text-red-100', editable: false },
            };

            const orsGate = @json($orsGate);
            const mporMonthLocks = @json($mporMonthLocks ?? []);
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

            async function postOrsAction(url, body = null) {
                const csrfToken = String(orsConfig.csrf || '')
                    || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || document.querySelector('#ors-log-form input[name="_token"]')?.value
                    || '';

                const headers = {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                };

                const options = { method: 'POST', headers };

                if (body && typeof body === 'object') {
                    headers['Content-Type'] = 'application/json';
                    options.body = JSON.stringify(body);
                }

                const res = await fetch(url, options);

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

            const tasksFromDb = Array.isArray(dbTasksRaw)
                ? dbTasksRaw.map((entry) => {
                    const rawState = String(entry?.state || 'draft').toLowerCase();
                    const state = rawState === 'validated' ? 'locked' : rawState;
                    const submittedAt = entry?.submittedAt ? new Date(entry.submittedAt) : null;
                    const totalSeconds = Number(entry?.totalSeconds ?? entry?.durationSeconds ?? 0);
                    const startedAt = entry?.startedAt ? new Date(entry.startedAt) : null;
                    const stoppedAt = entry?.stoppedAt ? new Date(entry.stoppedAt) : null;
                    const supervisorIdValue = entry?.supervisorId ?? entry?.supervisor_id ?? null;
                    const supervisorNameValue = String(entry?.supervisorName ?? entry?.supervisor_name ?? '').trim();

                    return {
                        id: String(entry?.id ?? `task-db-${Date.now()}`),
                        title: String(entry?.title || '--'),
                        date: String(entry?.date || ''),
                        client: 'Revenue Collection Unit',
                        uwpOutputId: '',
                        uwpOutputLabel: String(entry?.uwpOutputLabel || '--'),
                        notes: String(entry?.notes || 'No notes'),
                        quantity: String(entry?.quantity || ''),
                        supervisorId: supervisorIdValue === null || supervisorIdValue === undefined || String(supervisorIdValue).trim() === ''
                            ? null
                            : String(supervisorIdValue),
                        supervisorName: supervisorNameValue !== '' ? supervisorNameValue : null,
                        rating: '--',
                        state: state,
                        output_state: (state === 'submitted' || state === 'rated' || state === 'locked') ? 'submitted' : 'none',
                        submittedAt: submittedAt,
                        evidenceRequired: true,
                        evidenceAttached: Boolean(entry?.evidenceAttached),
                        evidenceCount: Number(entry?.evidenceCount || 0),
                        evidenceFileName: null,
                        evidenceUploadedAt: null,
                        totalSeconds: Number.isFinite(totalSeconds) ? totalSeconds : 0,
                        startedAt: startedAt && !Number.isNaN(startedAt.getTime()) ? startedAt : null,
                        stoppedAt: stoppedAt && !Number.isNaN(stoppedAt.getTime()) ? stoppedAt : null,
                        monthLocked: Boolean(entry?.monthLocked ?? entry?.month_locked ?? false),
                        mporLockReason: String(entry?.mporLockReason ?? entry?.mpor_lock_reason ?? ''),
                    };
                })
                : [];

            let tasks = Array.isArray(tasksFromDb) ? tasksFromDb : [];

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
            const supervisorSelect = document.getElementById('orsSupervisorId');
            const notesField = document.getElementById('orsNotes');

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

            function monthKeyFromDate(dateValue) {
                const raw = String(dateValue || '').trim();
                const match = raw.match(/^(\d{4}-\d{2})/);
                return match ? match[1] : '';
            }

            function getMporMonthLock(dateValue) {
                const monthKey = monthKeyFromDate(dateValue);
                if (!monthKey || typeof mporMonthLocks !== 'object' || mporMonthLocks === null) {
                    return null;
                }

                return mporMonthLocks[monthKey] || null;
            }

            function setOrsModalDisabledState(disabled, reason = '') {
                const logTaskSubmitButton = document.getElementById('orsLogTaskSubmitBtn');
                [uwpSelect, taskSelect, supervisorSelect, notesField, logTaskSubmitButton].forEach((element) => {
                    if (!element) return;
                    element.disabled = Boolean(disabled);
                    element.classList.toggle('opacity-70', Boolean(disabled));
                });

                if (gateNote) {
                    if (disabled && reason) {
                        gateNote.textContent = reason;
                        gateNote.classList.remove('hidden');
                    } else {
                        gateNote.classList.add('hidden');
                    }
                }

                if (disabled) {
                    resetTaskOptions('Task selection unavailable');
                }
            }

            function applyOrsModalStateForDate(dateValue) {
                if (Boolean(orsGate?.blocked)) {
                    const reason = `ORS locked: ${String(orsGate?.reason || 'No committed IPCR targets available.')}`;
                    setOrsModalDisabledState(true, reason);
                    return { allowed: false, reason };
                }

                if (!Array.isArray(orsOptions) || orsOptions.length === 0) {
                    const reason = 'ORS locked: No committed IPCR targets available.';
                    setOrsModalDisabledState(true, reason);
                    return { allowed: false, reason };
                }

                const monthLock = getMporMonthLock(dateValue);
                if (monthLock?.reason) {
                    setOrsModalDisabledState(true, monthLock.reason);
                    return { allowed: false, reason: monthLock.reason };
                }

                setOrsModalDisabledState(false);
                return { allowed: true, reason: null };
            }

            function guardOrsDate(dateValue, announce = true) {
                const result = applyOrsModalStateForDate(dateValue);
                if (!result.allowed && announce && result.reason) {
                    showFlash('error', result.reason);
                }
                return result.allowed;
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
                const initialDate = document.getElementById('orsSelectedDate')?.value || new Date().toISOString().slice(0, 10);
                const initialResult = applyOrsModalStateForDate(initialDate);
                if (!initialResult.allowed && uwpSelect.options.length > 0) {
                    uwpSelect.options[0].textContent = initialResult.reason || 'ORS locked';
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

            // DEMO: exactly one active timer -> Jan 5 recording
            let activeTaskId = tasks.find(t => t.state === 'recording' || t.state === 'paused')?.id || null;
            let detailTaskId = null;
            let __returnToModalId = null;
            let daySummaryDate = null;
            let daySummaryStatusFilter = null;
            let byDate = {};
            let byDateStatus = {};

            const SUMMARY_STATE_LABELS = {
                draft: 'Draft',
                recording: 'Recording',
                paused: 'Paused',
                submitted: 'Submitted',
                rated: 'Rated',
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

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function clearFlash() {
                window.PMSnackbar?.clear();
            }

            function showFlash(type, message) {
                if (!message) return;
                window.PMSnackbar?.show({
                    type: String(type || 'info').toLowerCase(),
                    message,
                });
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

                    if (!guardOrsDate(dateStr)) {
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
                        const count = Number(props.count || 0);
                        const labelText = `${summaryStateLabel(stateKey)} (${count})`;

                        const wrapper = document.createElement('div');
                        wrapper.textContent = labelText;
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

            const syncCalendarSize = () => {
                requestAnimationFrame(() => {
                    calendar.updateSize();
                });
            };
            const syncCalendarDuringTransition = (durationMs = 260) => {
                const startedAt = performance.now();
                const tick = (now) => {
                    calendar.updateSize();
                    if ((now - startedAt) < durationMs) {
                        requestAnimationFrame(tick);
                    }
                };
                requestAnimationFrame(tick);
            };
            syncCalendarSize();
            setTimeout(syncCalendarSize, 180);
            setTimeout(syncCalendarSize, 320);

            window.addEventListener('resize', syncCalendarSize);
            document.getElementById('sidebar-toggle-btn')?.addEventListener('click', () => {
                syncCalendarDuringTransition(280);
                setTimeout(syncCalendarSize, 210);
                setTimeout(syncCalendarSize, 320);
            });
            document.getElementById('employee-sidebar')?.addEventListener('transitionstart', () => syncCalendarDuringTransition(260));
            document.getElementById('employee-sidebar')?.addEventListener('transitionend', syncCalendarSize);

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

            function isModalOpen(id) {
                const el = document.getElementById(id);
                return Boolean(el) && !el.classList.contains('hidden');
            }

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
                if (modalId === 'orsDaySummaryModal') {
                    daySummaryStatusFilter = null;
                    daySummaryDate = null;
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

            const setButtonLoading = window.setButtonLoading || function() {};

            const orsLogForm = document.getElementById('ors-log-form');
            const orsLogTaskSubmitBtn = document.getElementById('orsLogTaskSubmitBtn');

            function getTaskById(id) {
                return tasks.find((t) => t.id === id);
            }

            function isDbBackedTask(task) {
                return /^\d+$/.test(String(task?.id ?? '').trim());
            }

            function normalizeTaskState(state) {
                const normalized = String(state || 'draft').toLowerCase();
                return normalized === 'validated' ? 'locked' : normalized;
            }

            function normalizeServerEntry(entry, fallback = {}) {
                if (!entry || typeof entry !== 'object') return null;

                const id = entry.id ?? fallback.id ?? null;
                if (id === null || id === undefined || String(id).trim() === '') return null;

                const state = normalizeTaskState(entry.status ?? entry.state ?? fallback.state ?? 'draft');
                const evidenceCount = Number(
                    entry.evidenceCount
                    ?? entry.evidence_count
                    ?? fallback.evidenceCount
                    ?? 0
                );
                const evidenceAttached = Boolean(
                    entry.evidenceAttached
                    ?? entry.evidence_attached
                    ?? fallback.evidenceAttached
                    ?? (evidenceCount > 0)
                );
                const submittedAtValue = entry.submittedAt ?? entry.submitted_at ?? fallback.submittedAt ?? null;
                const startedAtValue = entry.startedAt ?? entry.started_at ?? fallback.startedAt ?? null;
                const stoppedAtValue = entry.stoppedAt ?? entry.stopped_at ?? fallback.stoppedAt ?? null;
                const supervisorIdValue = entry.supervisor_id ?? entry.supervisorId ?? fallback.supervisorId ?? null;
                const supervisorNameValue = String(entry.supervisor_name ?? entry.supervisorName ?? fallback.supervisorName ?? '').trim();
                const totalSecondsValue = Number(
                    entry.totalSeconds
                    ?? entry.total_seconds
                    ?? entry.durationSeconds
                    ?? entry.duration_seconds
                    ?? fallback.totalSeconds
                    ?? 0
                );

                return {
                    id: String(id),
                    title: String(entry.title ?? entry.task_name ?? fallback.title ?? '--'),
                    date: String(entry.date ?? entry.work_date ?? fallback.date ?? ''),
                    client: String(entry.client ?? fallback.client ?? 'Revenue Collection Unit'),
                    uwpOutputId: String(entry.uwp_output_key ?? fallback.uwpOutputId ?? ''),
                    uwpOutputLabel: String(entry.uwp_output_label ?? entry.uwpOutputLabel ?? fallback.uwpOutputLabel ?? '--'),
                    notes: String(entry.notes ?? fallback.notes ?? 'No notes'),
                    quantity: String(entry.quantity ?? fallback.quantity ?? ''),
                    supervisorId: supervisorIdValue === null || supervisorIdValue === undefined || String(supervisorIdValue).trim() === ''
                        ? null
                        : String(supervisorIdValue),
                    supervisorName: supervisorNameValue !== '' ? supervisorNameValue : null,
                    rating: String(fallback.rating ?? '--'),
                    state: state,
                    output_state: ['submitted', 'rated', 'locked'].includes(state) ? 'submitted' : 'none',
                    submittedAt: submittedAtValue ? new Date(submittedAtValue) : null,
                    evidenceRequired: true,
                    evidenceAttached: evidenceAttached,
                    evidenceCount: Number.isFinite(evidenceCount) ? evidenceCount : 0,
                    evidenceFileName: entry?.evidence?.file_name ?? fallback.evidenceFileName ?? null,
                    evidenceUploadedAt: entry?.evidence?.uploaded_at
                        ? new Date(entry.evidence.uploaded_at)
                        : (fallback.evidenceUploadedAt ? new Date(fallback.evidenceUploadedAt) : null),
                    totalSeconds: Number.isFinite(totalSecondsValue) ? totalSecondsValue : 0,
                    startedAt: startedAtValue ? new Date(startedAtValue) : null,
                    stoppedAt: stoppedAtValue ? new Date(stoppedAtValue) : null,
                    monthLocked: Boolean(entry.monthLocked ?? entry.month_locked ?? fallback.monthLocked ?? false),
                    mporLockReason: String(entry.mporLockReason ?? entry.mpor_lock_reason ?? fallback.mporLockReason ?? ''),
                };
            }

            function applyServerEntryToTask(task, entry) {
                if (!task) return;
                const normalized = normalizeServerEntry(entry, task);
                if (!normalized) return;
                Object.assign(task, normalized);

                if (task.state === 'recording' || task.state === 'paused') {
                    activeTaskId = task.id;
                } else if (activeTaskId === task.id) {
                    activeTaskId = null;
                }
            }

            function clearFieldErrors() {
                const fieldIds = [
                    'orsSelectedDate',
                    'orsUwpOutput',
                    'orsTaskType',
                    'orsSupervisorId',
                    'orsNotes',
                ];

                fieldIds.forEach((id) => {
                    const field = document.getElementById(id);
                    field?.classList.remove('border-rose-500/50');
                });

                document.getElementById('orsLogFormErrors')?.remove();
            }

            function showFieldErrors(errorsObj) {
                clearFieldErrors();
                if (!orsLogForm || !errorsObj || typeof errorsObj !== 'object') {
                    return;
                }

                const fieldMap = {
                    work_date: 'orsSelectedDate',
                    uwp_output_key: 'orsUwpOutput',
                    ipcr_item_id: 'orsTaskType',
                    supervisor_id: 'orsSupervisorId',
                    notes: 'orsNotes',
                };

                const allMessages = [];
                Object.entries(errorsObj).forEach(([key, messages]) => {
                    const keyBase = String(key).split('.')[0];
                    const fieldId = fieldMap[key] || fieldMap[keyBase];
                    if (fieldId) {
                        const fieldEl = document.getElementById(fieldId);
                        fieldEl?.classList.add('border-rose-500/50');
                    }

                    const list = Array.isArray(messages) ? messages : [messages];
                    list.forEach((msg) => {
                        if (msg) allMessages.push(String(msg));
                    });
                });

                if (allMessages.length === 0) return;

                const box = document.createElement('div');
                box.id = 'orsLogFormErrors';
                box.className = 'rounded-lg border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-xs text-rose-200';
                box.innerHTML = `<ul class="list-disc space-y-1 pl-4">${allMessages.map((msg) => `<li>${escapeHtml(msg)}</li>`).join('')}</ul>`;
                orsLogForm.prepend(box);
            }

            function isLockedState(state) {
                return state === 'submitted' || state === 'rated' || state === 'locked';
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

                const stateOrder = ['recording', 'paused', 'draft', 'submitted', 'rated', 'locked'];
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
                                classNames: ['ors-summary-event', `is-${stateKey}`],
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

            function openDaySummaryModal(dateStr, statusFilter = null, skipOpen = false) {
                const dateKey = String(dateStr || '').trim();
                if (!dateKey) return;

                daySummaryDate = dateKey;
                daySummaryStatusFilter = statusFilter ? String(statusFilter).toLowerCase() : null;
                const filterKey = daySummaryStatusFilter;
                if (daySummaryDateLabelEl) {
                    daySummaryDateLabelEl.textContent = filterKey
                        ? `${formatDateLabel(dateKey)} - ${summaryStateLabel(filterKey)}`
                        : formatDateLabel(dateKey);
                }

                if (!daySummaryListEl) {
                    if (!skipOpen) {
                        openOrsModal('orsDaySummaryModal');
                    }
                    return;
                }

                daySummaryListEl.innerHTML = '';

                const statusMap = byDateStatus[dateKey] || {};
                const stateOrder = ['recording', 'paused', 'draft', 'submitted', 'rated', 'locked'];
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
                    empty.className = 'rounded-lg border border-gray-700 bg-slate-900/40 px-3 py-2 text-sm text-slate-300';
                    empty.textContent = 'No ORS entries for this date.';
                    daySummaryListEl.appendChild(empty);
                } else {
                    statusKeys.forEach((stateKey) => {
                        const meta = STATE_META[stateKey] || STATE_META.draft;
                        const entries = statusMap[stateKey];

                        const section = document.createElement('div');
                        section.className = 'rounded-xl border border-slate-700/80 bg-slate-950/50 p-3';

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
                        list.className = 'space-y-2.5';

                        entries.forEach((entry) => {
                            const itemBtn = document.createElement('button');
                            itemBtn.type = 'button';
                            itemBtn.className = 'group relative w-full overflow-hidden rounded-xl border border-slate-700/90 bg-gradient-to-r from-slate-900/80 to-slate-900/45 px-4 py-3 text-left transition hover:border-slate-500/90 hover:from-slate-800/85 hover:to-slate-800/55';
                            itemBtn.innerHTML = `
                                <span class="pointer-events-none absolute inset-y-0 left-0 w-1 rounded-l-xl" style="background:${meta.color};"></span>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 pl-2">
                                        <p class="truncate text-base font-semibold leading-snug text-white">${entry.title || '--'}</p>
                                        <div class="mt-1.5 flex flex-wrap items-center gap-3 text-[11px] text-slate-400">
                                            <span class="inline-flex items-center gap-1">
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-500/80"></span>
                                                Duration:
                                                <span class="ors-day-duration font-semibold text-slate-300" data-task-id="${entry.id}">
                                                    ${formatDuration(computeElapsed(entry))}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-semibold" style="color:${meta.color}; border-color:${meta.color};">${summaryStateLabel(stateKey)}</span>
                                        <p class="mt-1.5 text-[11px] font-medium ${entry.evidenceAttached ? 'text-emerald-300' : 'text-slate-400'}">${entry.evidenceAttached ? 'Evidence attached' : 'No evidence'}</p>
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

                if (!skipOpen) {
                    openOrsModal('orsDaySummaryModal');
                }
            }

            function refreshDaySummaryIfOpen(changedTask = null) {
                if (!isModalOpen('orsDaySummaryModal') || !daySummaryDate) {
                    return;
                }

                if (changedTask) {
                    const changedTaskDate = String(changedTask?.date || '').trim();
                    if (changedTaskDate !== String(daySummaryDate || '').trim()) {
                        return;
                    }
                }

                const currentFilter = daySummaryStatusFilter ? String(daySummaryStatusFilter).toLowerCase() : null;
                const dateKey = String(daySummaryDate || '').trim();
                const statusMap = byDateStatus[dateKey] || {};
                const hasAny = Object.values(statusMap).some((arr) => Array.isArray(arr) && arr.length > 0);
                const hasFiltered = currentFilter
                    ? (Array.isArray(statusMap[currentFilter]) && statusMap[currentFilter].length > 0)
                    : hasAny;

                if (currentFilter && !hasFiltered && hasAny) {
                    daySummaryStatusFilter = null;
                    openDaySummaryModal(dateKey, null, true);
                    return;
                }

                openDaySummaryModal(dateKey, currentFilter, true);
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
                document.getElementById('taskDetailClient').textContent = task.supervisorName || '--';
                document.getElementById('taskDetailMfo').textContent = task.uwpOutputLabel || '--';
                document.getElementById('taskDetailStatusText').textContent = (STATE_META[task.state] || STATE_META.draft).label;
                document.getElementById('taskDetailDuration').textContent = formatDuration(computeElapsed(task));
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
                const monthLocked = Boolean(task.monthLocked);
                const monthLockReason = String(task.mporLockReason || '');
                if (quantityInput) {
                    quantityInput.value = task.quantity || '';
                    const quantityDisabled = isLockedState(task.state) || monthLocked;
                    quantityInput.disabled = quantityDisabled;
                    quantityInput.classList.toggle('opacity-70', quantityDisabled);
                }

                const isMissing = task.state === 'missing';
                const uploadInput = document.getElementById('taskDetailUpload');
                if (uploadInput) {
                    const uploadDisabled = isLockedState(task.state) || monthLocked;
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
                        syncQuantityFromInput(task);
                        openTaskDetails(task.id);
                    };
                }

                setStatusBadge(document.getElementById('taskDetailStatusBadge'), task.state);

                [
                    'taskDetailStartBtn',
                    'taskDetailPauseBtn',
                    'taskDetailResumeBtn',
                    'taskDetailStopBtn',
                    'taskDetailSubmitBtn',
                ].forEach((buttonId) => {
                    const button = document.getElementById(buttonId);
                    if (button) {
                        button.dataset.taskId = String(task.id);
                    }
                });

                const lockBadge = document.getElementById('taskDetailLockBadge');
                const lockMsg = document.getElementById('taskDetailLockMessage');
                const draftMsg = document.getElementById('taskDetailDraftMessage');

                const locked = !isMissing && (isLockedState(task.state) || monthLocked);

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
                        lockBadge.textContent = monthLocked ? 'MPOR Locked' : 'Submitted (Locked)';
                        lockBadge.classList.toggle('hidden', !locked);
                        lockBadge.style.display = locked ? '' : 'none';
                        lockBadge.removeAttribute('aria-hidden');
                    }
                    if (lockMsg) {
                        if (monthLocked && monthLockReason) {
                            lockMsg.textContent = monthLockReason;
                        } else {
                            lockMsg.textContent = 'Submitted (Locked) — visible in MPOR monthly summary. SMPOR is system-generated after validation.';
                        }
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
                        if (data?.message) {
                            showFlash('success', data.message);
                        }
                    } catch (error) {
                        const rawMessage = String(error?.message || 'Unable to start task.');
                        const message = rawMessage.toLowerCase().includes('currently recording')
                            ? 'Only one task can be recording at a time.'
                            : rawMessage;
                        showFlash('error', message);
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
                refreshDaySummaryIfOpen(task);
                updateActivePanel();
                openTaskDetails(taskId);
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
                        if (data?.message) {
                            showFlash('success', data.message);
                        }
                    } catch (error) {
                        const message = String(error?.message || 'Unable to pause task.');
                        showFlash('error', message);
                        alert(message);
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
                refreshDaySummaryIfOpen(task);
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
                        if (data?.message) {
                            showFlash('success', data.message);
                        }
                    } catch (error) {
                        const rawMessage = String(error?.message || 'Unable to resume task.');
                        const message = rawMessage.toLowerCase().includes('currently recording')
                            ? 'Only one task can be recording at a time.'
                            : rawMessage;
                        showFlash('error', message);
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
                refreshDaySummaryIfOpen(task);
                updateActivePanel();
                if (detailTaskId === taskId) openTaskDetails(taskId);
                return true;
            }

            async function stopTask(taskId) {
                const task = getTaskById(taskId);
                if (!task) return false;

                if (isDbBackedTask(task)) {
                    syncQuantityFromInput(task);
                    try {
                        const data = await postOrsAction(ORS_ROUTES.stop(task.id), { quantity: task.quantity || '' });
                        if (!data?.ok || !data?.entry) {
                            throw new Error('Unable to stop task.');
                        }
                        applyServerEntryToTask(task, data.entry);
                        if (data?.message) {
                            showFlash('success', data.message);
                        }
                    } catch (error) {
                        const message = String(error?.message || 'Unable to stop task.');
                        showFlash('error', message);
                        alert(message);
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
                refreshDaySummaryIfOpen(task);
                updateActivePanel();
                return true;
            }

            async function submitTask(taskId) {
                const task = getTaskById(taskId);
                if (!task) return false;
                if (isLockedState(task.state)) return false;

                const qtyValue = syncQuantityFromInput(task);
                if (!qtyValue) {
                    showFlash('warning', 'Quantity is required before submitting an ORS entry.');
                    return false;
                }

                const uploadInput = document.getElementById('taskDetailUpload');
                const selectedFiles = Array.from(uploadInput?.files || []);
                const hasSelectedFiles = selectedFiles.length > 0;
                const hasExistingEvidence = Boolean(task.evidenceAttached) || Number(task.evidenceCount || 0) > 0;
                if (!hasSelectedFiles && !hasExistingEvidence) {
                    showFlash('warning', 'Evidence attachment is required before submitting an ORS entry.');
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
                        refreshDaySummaryIfOpen(task);
                        updateActivePanel();
                        closeOrsModal('taskDetailsModal');
                        showFlash('success', 'Task submitted.');
                        return true;
                    } catch (error) {
                        const message = String(error?.message || 'Submit failed. Please try again.');
                        showFlash('error', message);
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
                refreshDaySummaryIfOpen(task);
                updateActivePanel();
                closeOrsModal('taskDetailsModal');
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

                const pauseLabel = pauseBtn?.querySelector('[data-button-label]');
                if (pauseLabel) {
                    pauseLabel.textContent = task.state === 'paused' ? 'Resume' : 'Pause';
                }

                if (task.monthLocked) {
                    [pauseBtn, stopBtn, submitBtn].forEach((button) => {
                        if (!button) return;
                        button.disabled = true;
                        button.classList.add('opacity-70', 'cursor-not-allowed');
                    });

                    pauseBtn.onclick = () => showFlash('error', task.mporLockReason || 'ORS is locked because the MPOR is already submitted.');
                    stopBtn.onclick = () => showFlash('error', task.mporLockReason || 'ORS is locked because the MPOR is already submitted.');
                    submitBtn.onclick = () => showFlash('error', task.mporLockReason || 'ORS is locked because the MPOR is already submitted.');
                    return;
                }

                [pauseBtn, stopBtn, submitBtn].forEach((button) => {
                    if (!button) return;
                    button.disabled = false;
                    button.classList.remove('opacity-70', 'cursor-not-allowed');
                });

                pauseBtn.onclick = () => runWithLoading(
                    pauseBtn,
                    task.state === 'paused' ? 'Resuming...' : 'Pausing...',
                    () => (task.state === 'paused' ? resumeTask(task.id) : pauseTask(task.id))
                );
                stopBtn.onclick = () => runWithLoading(stopBtn, 'Stopping...', async () => {
                    const ok = await stopTask(task.id);
                    if (ok) {
                        openTaskDetails(task.id);
                    }
                });
                submitBtn.onclick = () => runWithLoading(submitBtn, 'Submitting...', () => submitTask(task.id));
            }

            function updateDetailElapsed() {
                if (!detailTaskId) return;
                const task = getTaskById(detailTaskId);
                if (!task) return;
                const durationEl = document.getElementById('taskDetailDuration');
                if (durationEl) durationEl.textContent = formatDuration(computeElapsed(task));
            }

            function updateDaySummaryDurations() {
                if (!isModalOpen('orsDaySummaryModal')) return;

                const nodes = document.querySelectorAll('#daySummaryList .ors-day-duration[data-task-id]');
                if (!nodes.length) return;

                nodes.forEach((node) => {
                    const taskId = String(node.getAttribute('data-task-id') || '').trim();
                    if (!taskId) return;

                    const task = getTaskById(taskId);
                    if (!task) return;

                    if (String(task.state).toLowerCase() !== 'recording') return;

                    node.textContent = formatDuration(computeElapsed(task));
                });
            }

            function wireLogButton() {
                const btn = document.getElementById('openLogTaskBtn');
                if (!btn) return;

                btn.addEventListener('click', () => {
                    const today = new Date().toISOString().split('T')[0];
                    if (!guardOrsDate(today)) {
                        return;
                    }

                    const dateInput = document.getElementById('orsSelectedDate');
                    if (dateInput) dateInput.value = today;

                    openOrsModal('orsTaskModal');
                });
            }

            function wireDaySummaryLogButton() {
                if (!daySummaryLogBtn) return;
                daySummaryLogBtn.addEventListener('click', () => {
                    if (!daySummaryDate) return;
                    if (!guardOrsDate(daySummaryDate)) {
                        return;
                    }
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
                updateDaySummaryDurations();
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

            if (orsLogForm && orsLogTaskSubmitBtn) {
                orsLogForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    if (orsLogForm.dataset.loadingActive === 'true') return;

                    clearFlash();
                    clearFieldErrors();

                    await runWithLoading(orsLogTaskSubmitBtn, 'Logging...', async () => {
                        orsLogForm.dataset.loadingActive = 'true';
                        try {
                            const csrfToken = String(orsConfig.csrf || '')
                                || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                || document.querySelector('#ors-log-form input[name="_token"]')?.value
                                || '';

                            const selectedDate = String(document.getElementById('orsSelectedDate')?.value || '').trim();
                            if (!guardOrsDate(selectedDate)) {
                                return;
                            }

                            const formData = new FormData(orsLogForm);
                            const response = await fetch(orsLogForm.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                                body: formData,
                            });

                            const payload = await response.json().catch(() => null);
                            if (!response.ok) {
                                if (response.status === 422 && payload?.errors) {
                                    showFieldErrors(payload.errors);
                                    showFlash('error', payload?.message || 'Please correct the highlighted fields.');
                                    return;
                                }

                                showFlash('error', payload?.message || 'Unable to log ORS task. Please try again.');
                                return;
                            }

                            const entry = payload?.entry ?? payload;
                            const supervisorSelect = document.getElementById('orsSupervisorId');
                            const selectedSupervisorId = String(supervisorSelect?.value || '').trim();
                            const selectedSupervisorName = selectedSupervisorId !== ''
                                ? String(supervisorSelect?.selectedOptions?.[0]?.textContent || '').trim()
                                : '';
                            const fallback = {
                                date: String(document.getElementById('orsSelectedDate')?.value || ''),
                                uwpOutputId: String(document.getElementById('orsUwpOutput')?.value || ''),
                                uwpOutputLabel: document.getElementById('orsUwpOutput')?.selectedOptions?.[0]?.textContent?.trim() || '--',
                                notes: String(document.getElementById('orsNotes')?.value || ''),
                                supervisorId: selectedSupervisorId !== '' ? selectedSupervisorId : null,
                                supervisorName: selectedSupervisorName !== '' ? selectedSupervisorName : null,
                                quantity: '',
                                state: 'draft',
                            };

                            const task = normalizeServerEntry(entry, fallback);
                            if (!task) {
                                showFlash('error', payload?.message || 'Unable to parse ORS task response.');
                                return;
                            }

                            const existingTaskIndex = tasks.findIndex((item) => String(item.id) === String(task.id));
                            if (existingTaskIndex >= 0) {
                                tasks[existingTaskIndex] = { ...tasks[existingTaskIndex], ...task };
                            } else {
                                tasks.push(task);
                            }

                            const selectedDateValue = String(document.getElementById('orsSelectedDate')?.value || task.date || '');
                            orsLogForm.reset();
                            const dateInput = document.getElementById('orsSelectedDate');
                            if (dateInput && selectedDateValue) {
                                dateInput.value = selectedDateValue;
                            }
                            resetTaskOptions();
                            clearFieldErrors();

                            const autoStartOk = await startTask(task.id);
                            if (!autoStartOk) {
                                refreshCalendar();
                                refreshDaySummaryIfOpen(task);
                                updateActivePanel();
                            }

                            closeOrsModal('orsTaskModal');
                            showFlash('success', autoStartOk ? 'Logged task. Timer started.' : 'Logged task.');
                        } catch (error) {
                            const message = String(error?.message || 'Unexpected error while logging ORS task.');
                            showFlash('error', message);
                            alert(message);
                        } finally {
                            delete orsLogForm.dataset.loadingActive;
                        }
                    });
                });
            }

            document.getElementById('taskDetailStartBtn')?.addEventListener('click', (e) => {
                const taskId = String(e.currentTarget?.dataset?.taskId || '').trim();
                if (!taskId) return;
                runWithLoading(e.currentTarget, 'Starting...', () => startTask(taskId));
            });
            document.getElementById('taskDetailPauseBtn')?.addEventListener('click', (e) => {
                const taskId = String(e.currentTarget?.dataset?.taskId || '').trim();
                if (!taskId) return;
                runWithLoading(e.currentTarget, 'Pausing...', () => pauseTask(taskId));
            });
            document.getElementById('taskDetailResumeBtn')?.addEventListener('click', (e) => {
                const taskId = String(e.currentTarget?.dataset?.taskId || '').trim();
                if (!taskId) return;
                runWithLoading(e.currentTarget, 'Resuming...', () => resumeTask(taskId));
            });
            document.getElementById('taskDetailStopBtn')?.addEventListener('click', (e) => {
                const taskId = String(e.currentTarget?.dataset?.taskId || '').trim();
                if (!taskId) return;
                runWithLoading(e.currentTarget, 'Stopping...', async () => {
                    const ok = await stopTask(taskId);
                    if (ok) {
                        openTaskDetails(taskId);
                    }
                });
            });
            document.getElementById('taskDetailSubmitBtn')?.addEventListener('click', (e) => {
                const taskId = String(e.currentTarget?.dataset?.taskId || '').trim();
                if (!taskId) return;
                runWithLoading(e.currentTarget, 'Submitting...', () => submitTask(taskId));
            });

            // Auto-open task details modal if ?ors_entry_id= is in URL
            const urlParams = new URLSearchParams(window.location.search);
            const autoOpenEntryId = urlParams.get('ors_entry_id');
            if (autoOpenEntryId) {
                openTaskDetails(autoOpenEntryId);
            }
        });
    </script>
    @endpush

@endsection
