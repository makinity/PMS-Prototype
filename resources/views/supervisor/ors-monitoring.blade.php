@extends('layouts.supervisor')

@section('main-content')
    @php
        $submittedEntries = $submittedEntries ?? collect();
        if ($submittedEntries instanceof \Illuminate\Database\Eloquent\Collection) {
            $submittedEntries->loadMissing('evidences');
        }

        $calendarTasks = $submittedEntries
            ->filter(fn ($entry) => in_array(strtolower((string) ($entry->status ?? '')), ['submitted', 'rated'], true))
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
                $evidences = collect($entry->evidences ?? [])->map(function ($evidence) {
                    $relativePath = ltrim((string) ($evidence->file_path ?? ''), '/');
                    $fileUrl = $relativePath !== '' ? asset('storage/' . $relativePath) : null;

                    return [
                        'id' => $evidence->id,
                        'file_name' => $evidence->file_name ?? 'Evidence File',
                        'mime_type' => $evidence->mime_type,
                        'file_size' => $evidence->file_size,
                        'uploaded_at' => optional($evidence->uploaded_at)->toIso8601String(),
                        'preview_url' => $fileUrl,
                        'download_url' => $fileUrl,
                    ];
                })->values();
                $evidenceCount = (int) ($entry->evidences_count ?? $evidences->count());

                return [
                    'id' => $entry->id,
                    'ipcr_item_id' => $entry->ipcr_item_id,
                    'date' => $dateValue,
                    'dateLabel' => $dateLabel,
                    'employee' => optional($entry->employee)->name ?? '--',
                    'office' => $officeValue,
                    'output' => optional($entry->ipcrItem)->output_title ?? '--',
                    'uwpOutput' => optional($entry->ipcrItem)->output_title ?? '--',
                    'accomplishment' => optional($entry->ipcrItem)->indicator_text ?? '--',
                    'indicator_text' => optional($entry->ipcrItem)->indicator_text ?? '--',
                    'standards_payload' => optional($entry->ipcrItem)->standards_payload,
                    'duration' => $entry->duration ?? ($entry->duration_minutes ?? '--'),
                    'evidence' => $evidenceCount > 0,
                    'evidence_count' => $evidenceCount,
                    'quantity' => $entry->quantity ?? '--',
                    'notes' => $entry->notes,
                    'total_seconds' => is_null($entry->total_seconds) ? null : (int) $entry->total_seconds,
                    'started_at' => optional($entry->started_at)->toIso8601String(),
                    'stopped_at' => optional($entry->stopped_at)->toIso8601String(),
                    'evidences' => $evidences,
                    'status' => strtolower((string) ($entry->status ?? 'submitted')),
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
        #ors-calendar .fc-daygrid-event.ors-summary-event { color: #dbeafe; }
        #ors-calendar .fc-daygrid-event.ors-submitted-event {
            background: rgba(59, 130, 246, 0.18);
            border: 1px solid rgba(59, 130, 246, 0.45);
            color: #dbeafe;
        }
        #ors-calendar .fc-daygrid-event.ors-rated-event {
            background: rgba(6, 182, 212, 0.18);
            border: 1px solid rgba(6, 182, 212, 0.45);
            color: #cffafe;
        }

        .status-chip{
            display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .55rem;border-radius:9999px;border-width:1px;
            font-size:.75rem;font-weight:600
        }
        .status-dot{width:.55rem;height:.55rem;border-radius:9999px}
    </style>

    <section class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-white">Daily ORS Monitoring</h1>
        </div>

        <!-- Legend (submitted/rated + missing only, aligned with manual ORS) -->
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-slate-400">ORS Monitoring Legend</p>
                    <p class="text-[11px] text-slate-500">
                        Visibility: submitted/rated outputs only · Rating: submitted/rated only
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
                    <span class="status-chip border-blue-500/60 bg-blue-500/10 text-blue-100">
                        <span class="status-dot bg-blue-500"></span>
                        Submitted (Locked)
                    </span>

                    <span class="status-chip border-cyan-500/60 bg-cyan-500/10 text-cyan-100">
                        <span class="status-dot bg-cyan-500"></span>
                        Rated (Locked)
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
                                <h2 class="text-lg font-semibold text-white">Submitted ORS Entries</h2>
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

        <!-- Rated Day List Modal -->
        <div id="ors-rated-day-list-modal"
             class="ors-modal fixed inset-0 z-[61] hidden items-center justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6"
             role="dialog"
             aria-modal="true">
            <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 shadow-xl overflow-hidden">
                <div class="flex max-h-[84vh] flex-col">
                    <div class="border-b border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-white">Rated ORS Entries</h2>
                                <p id="ratedDayListDateLabel" class="text-xs text-slate-400">--</p>
                            </div>
                            <button id="closeRatedDayListTopBtn"
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white">
                                x
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-5">
                        <div id="ratedDayListEntries" class="space-y-3"></div>
                    </div>

                    <div class="border-t border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-center justify-end">
                            <button id="closeRatedDayListBottomBtn"
                                    type="button"
                                    class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evidence Sub-Modal -->
        <div id="ors-evidence-modal"
             class="ors-modal fixed inset-0 z-[64] hidden items-center justify-center overflow-y-auto bg-black/70 px-4 py-6 sm:px-6"
             role="dialog"
             aria-modal="true">
            <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 shadow-xl overflow-hidden">
                <div class="flex max-h-[88vh] flex-col">
                    <div class="border-b border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-white">Evidence Files</h2>
                                <p id="evidenceModalEntryTitle" class="text-sm text-slate-200">--</p>
                                <p id="evidenceModalEntryMeta" class="text-xs text-slate-400">--</p>
                            </div>
                            <button id="closeEvidenceTopBtn"
                                    type="button"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white">
                                x
                            </button>
                        </div>
                    </div>

                    <div class="grid flex-1 grid-cols-1 overflow-hidden lg:grid-cols-3">
                        <div class="border-b border-slate-800 p-4 lg:border-b-0 lg:border-r">
                            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Attached Files</p>
                            <div id="evidenceFileList" class="space-y-2 overflow-y-auto max-h-[30vh] lg:max-h-[60vh]"></div>
                        </div>

                        <div class="lg:col-span-2 flex min-h-[320px] flex-col">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-800 px-4 py-3">
                                <p id="evidencePreviewFileName" class="truncate text-sm font-semibold text-white">Select a file</p>
                                <a id="evidenceTopDownloadBtn"
                                   href="#"
                                   target="_blank"
                                   rel="noopener"
                                   class="pointer-events-none rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 opacity-50 hover:bg-slate-700">
                                    Download
                                </a>
                            </div>

                            <div id="evidencePreviewArea" class="flex-1 overflow-auto bg-black px-4 py-4"></div>
                        </div>
                    </div>

                    <div class="border-t border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-center justify-end">
                            <button id="closeEvidenceBottomBtn"
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
                    No submitted or rated entries for this date.
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
             class="ors-modal fixed inset-0 z-[63] hidden items-center justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6"
             role="dialog"
             aria-modal="true">
            <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 shadow-xl overflow-hidden">
                <div class="flex max-h-[86vh] flex-col">
                    <div class="border-b border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-white">ORS Monitoring Detail</h2>
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
                                        <p class="text-xs text-slate-400">Actual Accomplishment</p>
                                        <p id="monitoringAccomplishment" class="text-slate-100">--</p>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Time Spent</p>
                                    <p id="monitoringDuration" class="text-slate-100 mt-1">--</p>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Evidence Attached</p>
                                    <div class="mt-1 flex items-center justify-between gap-2">
                                        <p id="monitoringEvidence" class="text-emerald-300 font-semibold">--</p>
                                        <button id="monitoringEvidenceBtn"
                                                type="button"
                                                class="hidden rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60">
                                            View Evidence
                                        </button>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Quantity (employee-declared)</p>
                                    <p id="monitoringQuantity" class="mt-1 text-base font-semibold text-white">--</p>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Notes</p>
                                    <p id="monitoringNotes" class="mt-1 text-sm text-slate-200 whitespace-pre-wrap break-words">--</p>
                                </div>
                            </div>

                            <!-- RIGHT -->
                            <div class="space-y-5">
                                <!-- Rating Basis (compact + opens sub-modal) -->
                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3 text-xs text-slate-200">
                                    <p class="text-[11px] uppercase text-slate-400">Rating basis</p>

                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <p id="ratingBasisIndicator" class="text-sm font-semibold text-white">--</p>

                                        <button id="openRatingBasisBtn"
                                                type="button"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-700 bg-slate-900/50 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                            View basis for rating
                                        </button>
                                    </div>

                                </div>

                                <!-- Monitoring Rating -->
                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3 text-xs text-slate-200">
                                    <p class="text-[11px] uppercase text-slate-400">Monitoring Rating (ORS Format)</p>

                                    <div id="rating-locked-note" class="mt-2 text-[11px] text-slate-400">
                                        Rating is available only for Submitted ORS entries.
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
                                            </div>
                                            <div>
                                                <label class="text-xs text-slate-300">Timeliness</label>
                                                <select id="ratingTime"
                                                        class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm text-slate-200">
                                                    <option value="">--</option>
                                                    <option>5</option><option>4</option><option>3</option><option>2</option><option>1</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="text-xs text-slate-300">Remarks</label>
                                            <textarea id="ratingRemarks"
                                                      rows="4"
                                                      class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm text-slate-200"
                                                      placeholder="Coaching notes / observations..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Status</p>
                                    <div class="mt-2 inline-flex flex-col gap-1">
                                        <span id="monitoringStatus" class="status-chip border border-slate-700 bg-slate-800 text-slate-200"></span>
                                        <span id="monitoringStatusDetail" class="text-xs text-slate-300"></span>
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
                            <form id="saveMonitoringForm"
                                  method="POST"
                                  class="hidden"
                                  data-action-template="{{ route('supervisor.ors-monitoring.store', ['orsEntry' => '__ID__']) }}">
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

        <!-- Rated Monitoring Detail Modal -->
        <div id="ors-rated-monitoring-modal"
             class="ors-modal fixed inset-0 z-[63] hidden items-center justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6"
             role="dialog"
             aria-modal="true">
            <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 shadow-xl overflow-hidden">
                <div class="flex max-h-[86vh] flex-col">
                    <div class="border-b border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-white">Rated ORS Monitoring Detail</h2>
                                <p class="text-xs text-slate-400">
                                    Read-only supervisor monitoring details for Rated (Locked) entries.
                                </p>
                            </div>
                            <button type="button"
                                    onclick="closeOrsModal('ors-rated-monitoring-modal')"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white">
                                x
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-5">
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                            <div class="space-y-5">
                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div>
                                            <p class="text-xs text-slate-400">Ratee (Employee)</p>
                                            <p id="ratedMonitoringEmployee" class="font-semibold text-white">--</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400">Office / Unit</p>
                                            <p id="ratedMonitoringOffice" class="text-white">--</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400">Date Submitted</p>
                                            <p id="ratedMonitoringDate" class="text-white">--</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3 text-sm text-slate-200">
                                    <div>
                                        <p class="text-xs text-slate-400">Major Output (MFO)</p>
                                        <p id="ratedMonitoringMajorOutput" class="text-white">--</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400">Actual Accomplishment</p>
                                        <p id="ratedMonitoringAccomplishment" class="text-slate-100">--</p>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Time Spent</p>
                                    <p id="ratedMonitoringDuration" class="text-slate-100 mt-1">--</p>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Evidence Attached</p>
                                    <div class="mt-1 flex items-center justify-between gap-2">
                                        <p id="ratedMonitoringEvidence" class="text-emerald-300 font-semibold">--</p>
                                        <button id="ratedMonitoringEvidenceBtn"
                                                type="button"
                                                class="hidden rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60">
                                            View Evidence
                                        </button>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Quantity (employee-declared)</p>
                                    <p id="ratedMonitoringQuantity" class="mt-1 text-base font-semibold text-white">--</p>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Notes</p>
                                    <p id="ratedMonitoringNotes" class="mt-1 text-sm text-slate-200 whitespace-pre-wrap break-words">--</p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3 text-xs text-slate-200">
                                    <p class="text-[11px] uppercase text-slate-400">Rating basis</p>
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <p id="ratedRatingBasisIndicator" class="text-sm font-semibold text-white">--</p>
                                        <button id="openRatedRatingBasisBtn"
                                                type="button"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-700 bg-slate-900/50 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                            View basis for rating
                                        </button>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 space-y-3 text-xs text-slate-200">
                                    <p class="text-[11px] uppercase text-slate-400">Monitoring Rating (Read-only)</p>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <p class="text-xs text-slate-400">Quality</p>
                                            <span id="ratedMonitoringQuality" class="mt-1 inline-flex items-center rounded-full border border-slate-700 bg-slate-800 px-2.5 py-1 text-xs font-semibold text-slate-100">--</span>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400">Timeliness</p>
                                            <span id="ratedMonitoringTimeliness" class="mt-1 inline-flex items-center rounded-full border border-slate-700 bg-slate-800 px-2.5 py-1 text-xs font-semibold text-slate-100">--</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400">Remarks</p>
                                        <p id="ratedMonitoringRemarks" class="mt-1 rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm text-slate-200 whitespace-pre-wrap break-words">--</p>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                                    <p class="text-xs text-slate-400">Status</p>
                                    <div class="mt-2 inline-flex flex-col gap-1">
                                        <span id="ratedMonitoringStatus" class="status-chip border border-slate-700 bg-slate-800 text-slate-200"></span>
                                        <span id="ratedMonitoringStatusDetail" class="text-xs text-slate-300"></span>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4 text-xs text-slate-400">
                                    Tip: Rated entries remain locked. Use this view for review and evidence inspection.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-800 bg-slate-900/80 px-6 py-4">
                        <div class="flex items-center justify-end">
                            <button id="closeRatedMonitoringBottomBtn"
                                    type="button"
                                    class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                                Close
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
                function showSnackbar(type, message) {
                    if (!message) return;
                    if (window.PMSnackbar) {
                        window.PMSnackbar.show({
                            type: String(type || 'info').toLowerCase(),
                            message: String(message),
                        });
                    }
                }
                const modal = document.getElementById('ors-monitoring-modal');
                const ratedMonitoringModal = document.getElementById('ors-rated-monitoring-modal');
                const employeeEl = document.getElementById('monitoringEmployee');
                const officeEl = document.getElementById('monitoringOffice');
                const dateEl = document.getElementById('monitoringDate');
                const majorOutputEl = document.getElementById('monitoringMajorOutput');
                const accomplishmentEl = document.getElementById('monitoringAccomplishment');
                const durationEl = document.getElementById('monitoringDuration');
                const evidenceEl = document.getElementById('monitoringEvidence');
                const monitoringEvidenceBtn = document.getElementById('monitoringEvidenceBtn');
                const statusEl = document.getElementById('monitoringStatus');
                const statusDetailEl = document.getElementById('monitoringStatusDetail');
                const quantityEl = document.getElementById('monitoringQuantity');
                const notesEl = document.getElementById('monitoringNotes');
                const ratedEmployeeEl = document.getElementById('ratedMonitoringEmployee');
                const ratedOfficeEl = document.getElementById('ratedMonitoringOffice');
                const ratedDateEl = document.getElementById('ratedMonitoringDate');
                const ratedMajorOutputEl = document.getElementById('ratedMonitoringMajorOutput');
                const ratedAccomplishmentEl = document.getElementById('ratedMonitoringAccomplishment');
                const ratedDurationEl = document.getElementById('ratedMonitoringDuration');
                const ratedEvidenceEl = document.getElementById('ratedMonitoringEvidence');
                const ratedMonitoringEvidenceBtn = document.getElementById('ratedMonitoringEvidenceBtn');
                const ratedStatusEl = document.getElementById('ratedMonitoringStatus');
                const ratedStatusDetailEl = document.getElementById('ratedMonitoringStatusDetail');
                const ratedQuantityEl = document.getElementById('ratedMonitoringQuantity');
                const ratedNotesEl = document.getElementById('ratedMonitoringNotes');
                const ratedMonitoringQualityEl = document.getElementById('ratedMonitoringQuality');
                const ratedMonitoringTimelinessEl = document.getElementById('ratedMonitoringTimeliness');
                const ratedMonitoringRemarksEl = document.getElementById('ratedMonitoringRemarks');

                const ratingBasisIndicatorEl = document.getElementById('ratingBasisIndicator');
                const ratedRatingBasisIndicatorEl = document.getElementById('ratedRatingBasisIndicator');

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
                const ratedDayListModal = document.getElementById('ors-rated-day-list-modal');
                const ratedDayListDateLabel = document.getElementById('ratedDayListDateLabel');
                const ratedDayListEntries = document.getElementById('ratedDayListEntries');
                const closeRatedDayListTopBtn = document.getElementById('closeRatedDayListTopBtn');
                const closeRatedDayListBottomBtn = document.getElementById('closeRatedDayListBottomBtn');
                const emptyDateModal = document.getElementById('ors-empty-date-modal');
                const emptyDateLabel = document.getElementById('emptyDateLabel');
                const closeEmptyDateTopBtn = document.getElementById('closeEmptyDateTopBtn');
                const closeEmptyDateBottomBtn = document.getElementById('closeEmptyDateBottomBtn');
                const evidenceModal = document.getElementById('ors-evidence-modal');
                const evidenceModalEntryTitle = document.getElementById('evidenceModalEntryTitle');
                const evidenceModalEntryMeta = document.getElementById('evidenceModalEntryMeta');
                const evidenceFileList = document.getElementById('evidenceFileList');
                const evidencePreviewArea = document.getElementById('evidencePreviewArea');
                const evidencePreviewFileName = document.getElementById('evidencePreviewFileName');
                const evidenceTopDownloadBtn = document.getElementById('evidenceTopDownloadBtn');
                const closeEvidenceTopBtn = document.getElementById('closeEvidenceTopBtn');
                const closeEvidenceBottomBtn = document.getElementById('closeEvidenceBottomBtn');
                const closeRatedMonitoringBottomBtn = document.getElementById('closeRatedMonitoringBottomBtn');

                // Basis sub-modal
                const basisModal = document.getElementById('ratingBasisModal');
                const basisBody = document.getElementById('basisModalBody');
                const basisMfo = document.getElementById('basisModalMfo');
                const basisIndicator = document.getElementById('basisModalIndicator');
                const basisFilter = document.getElementById('basisFilter');
                const openBasisBtn = document.getElementById('openRatingBasisBtn');
                const openRatedBasisBtn = document.getElementById('openRatedRatingBasisBtn');
                const closeBasisBtn = document.getElementById('closeRatingBasisBtn');
                const basisDoneBtn = document.getElementById('basisDoneBtn');

                const STATUS_META = {
                    submitted: {
                        label: 'Submitted (Locked)',
                        detail: 'Submitted output is ready for monitoring rating.',
                        color: '#3b82f6',
                        badge: 'border-blue-500/60 bg-blue-500/10 text-blue-100'
                    },
                    rated: {
                        label: 'Rated (Locked)',
                        detail: 'Supervisor monitoring rating has been saved. You may update it.',
                        color: '#06b6d4',
                        badge: 'border-cyan-500/60 bg-cyan-500/10 text-cyan-100'
                    },
                    missing: {
                        label: 'Missing / Overdue',
                        detail: 'No submitted or rated ORS entry for this date.',
                        color: '#ef4444',
                        badge: 'border-red-500/60 bg-red-500/10 text-red-100',
                        muted: true
                    }
                };
                const tasks = @json($calendarTasks);
                const byDateSubmitted = tasks.reduce((carry, task) => {
                    if (!task || !task.date || String(task.status || '').toLowerCase() !== 'submitted') return carry;
                    if (!carry[task.date]) {
                        carry[task.date] = [];
                    }
                    carry[task.date].push(task);
                    return carry;
                }, {});
                const byDateRated = tasks.reduce((carry, task) => {
                    if (!task || !task.date || String(task.status || '').toLowerCase() !== 'rated') return carry;
                    if (!carry[task.date]) {
                        carry[task.date] = [];
                    }
                    carry[task.date].push(task);
                    return carry;
                }, {});

                function sortDateMap(map) {
                    Object.keys(map).forEach((dateKey) => {
                        map[dateKey].sort((left, right) => {
                            const employeeCompare = String(left?.employee || '').localeCompare(String(right?.employee || ''));
                            if (employeeCompare !== 0) return employeeCompare;
                            return String(left?.accomplishment || '').localeCompare(String(right?.accomplishment || ''));
                        });
                    });
                }

                sortDateMap(byDateSubmitted);
                sortDateMap(byDateRated);

                const taskById = tasks.reduce((carry, task) => {
                    if (!task || task.id === undefined || task.id === null) return carry;
                    carry[String(task.id)] = task;
                    return carry;
                }, {});

                function buildSummaryEvents() {
                    const summaryDates = Array.from(new Set([
                        ...Object.keys(byDateSubmitted),
                        ...Object.keys(byDateRated),
                    ])).sort();

                    return summaryDates.flatMap((date) => {
                        const submittedCount = (byDateSubmitted[date] || []).length;
                        const ratedCount = (byDateRated[date] || []).length;
                        const events = [];

                        if (submittedCount > 0) {
                            events.push({
                                id: `sum-${date}-submitted`,
                                start: date,
                                allDay: true,
                                title: `${submittedCount} submitted`,
                                classNames: ['ors-summary-event', 'ors-submitted-event'],
                                extendedProps: {
                                    date,
                                    status: 'submitted',
                                    count: submittedCount,
                                },
                            });
                        }

                        if (ratedCount > 0) {
                            events.push({
                                id: `sum-${date}-rated`,
                                start: date,
                                allDay: true,
                                title: `${ratedCount} rated`,
                                classNames: ['ors-summary-event', 'ors-rated-event'],
                                extendedProps: {
                                    date,
                                    status: 'rated',
                                    count: ratedCount,
                                },
                            });
                        }

                        return events;
                    });
                }

                const summaryEvents = buildSummaryEvents();

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

                function openOrsModalStack(modalId) {
                    const modalEl = document.getElementById(modalId);
                    if (!modalEl) return;
                    modalEl.classList.remove('hidden');
                    modalEl.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    refreshBodyLock();
                }

                function refreshBodyLock() {
                    const monitoringOpen = modal && !modal.classList.contains('hidden');
                    const ratedMonitoringOpen = ratedMonitoringModal && !ratedMonitoringModal.classList.contains('hidden');
                    const dayListOpen = dayListModal && !dayListModal.classList.contains('hidden');
                    const ratedDayListOpen = ratedDayListModal && !ratedDayListModal.classList.contains('hidden');
                    const evidenceOpen = evidenceModal && !evidenceModal.classList.contains('hidden');
                    const emptyDateOpen = emptyDateModal && !emptyDateModal.classList.contains('hidden');
                    const basisOpen = basisModal && !basisModal.classList.contains('hidden');
                    document.body.classList.toggle(
                        'overflow-hidden',
                        Boolean(monitoringOpen || ratedMonitoringOpen || dayListOpen || ratedDayListOpen || evidenceOpen || emptyDateOpen || basisOpen)
                    );
                }

                function formatDateLabel(dateStr) {
                    if (!dateStr) return '--';
                    const parsed = new Date(`${dateStr}T00:00:00`);
                    if (Number.isNaN(parsed.getTime())) return dateStr;
                    return parsed.toLocaleDateString([], { month: 'long', day: 'numeric', year: 'numeric' });
                }

                function formatDateTimeLabel(value) {
                    if (!value) return '--';
                    const parsed = new Date(value);
                    if (Number.isNaN(parsed.getTime())) return '--';
                    return parsed.toLocaleString([], {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                    });
                }

                function formatFileSize(bytes) {
                    const size = Number(bytes || 0);
                    if (!Number.isFinite(size) || size <= 0) return '--';
                    if (size >= 1024 * 1024) {
                        return `${(size / (1024 * 1024)).toFixed(2)} MB`;
                    }
                    return `${Math.max(1, Math.round(size / 1024))} KB`;
                }

                function formatDurationSeconds(totalSeconds) {
                    const seconds = Math.max(0, Math.floor(Number(totalSeconds) || 0));
                    const hours = Math.floor(seconds / 3600);
                    const minutes = Math.floor((seconds % 3600) / 60);
                    const remainingSeconds = seconds % 60;

                    if (hours > 0) {
                        return `${hours}:${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
                    }

                    return `${minutes}:${String(remainingSeconds).padStart(2, '0')}`;
                }

                function formatDurationFromEntry(entry) {
                    const hasTotalSeconds = entry?.total_seconds !== null
                        && entry?.total_seconds !== undefined
                        && entry?.total_seconds !== '';

                    if (hasTotalSeconds && Number.isFinite(Number(entry.total_seconds))) {
                        return formatDurationSeconds(Number(entry.total_seconds));
                    }

                    if (entry?.started_at && entry?.stopped_at) {
                        const startedAt = new Date(entry.started_at);
                        const stoppedAt = new Date(entry.stopped_at);
                        if (!Number.isNaN(startedAt.getTime()) && !Number.isNaN(stoppedAt.getTime())) {
                            const elapsedSeconds = Math.max(0, Math.floor((stoppedAt.getTime() - startedAt.getTime()) / 1000));
                            return formatDurationSeconds(elapsedSeconds);
                        }
                    }

                    return entry?.duration && entry.duration !== '--' ? String(entry.duration) : '--';
                }

                function detectEvidenceType(evidence) {
                    const mimeType = String(evidence?.mime_type || '').toLowerCase();
                    const fileName = String(evidence?.file_name || '').toLowerCase();

                    if (mimeType.startsWith('image/') || /\.(png|jpg|jpeg|gif|webp|bmp|svg)$/.test(fileName)) {
                        return 'image';
                    }
                    if (mimeType === 'application/pdf' || fileName.endsWith('.pdf')) {
                        return 'pdf';
                    }
                    return 'other';
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
                    dayMaxEventRows: 2,
                    moreLinkClick: 'popover',
                    eventDisplay: 'block',
                    editable: false,
                    selectable: true,
                    events: summaryEvents,
                    dateClick(info) {
                        const hasSubmitted = Array.isArray(byDateSubmitted[info.dateStr]) && byDateSubmitted[info.dateStr].length > 0;
                        const hasRated = Array.isArray(byDateRated[info.dateStr]) && byDateRated[info.dateStr].length > 0;

                        if (hasSubmitted) {
                            openSubmittedDayListModal(info.dateStr);
                            return;
                        }

                        if (hasRated) {
                            openRatedDayListModal(info.dateStr);
                            return;
                        }

                        openEmptyDateModal(info.dateStr);
                    },
                    eventClick(info) {
                        const dateStr = String(info.event.extendedProps?.date || '');
                        const status = String(info.event.extendedProps?.status || '').toLowerCase();
                        if (!dateStr) return;
                        if (status === 'rated') {
                            openRatedDayListModal(dateStr);
                            return;
                        }
                        openSubmittedDayListModal(dateStr);
                    }
                });
                calendar.render();

                function refreshCalendarEvents() {
                    calendar.removeAllEvents();
                    buildSummaryEvents().forEach((eventConfig) => {
                        calendar.addEvent(eventConfig);
                    });
                }

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

                function normalizeStandardsPayload(payload) {
                    let normalizedPayload = payload;

                    if (typeof normalizedPayload === 'string') {
                        try {
                            normalizedPayload = JSON.parse(normalizedPayload);
                        } catch (error) {
                            normalizedPayload = null;
                        }
                    }

                    if (!normalizedPayload || typeof normalizedPayload !== 'object') {
                        return null;
                    }

                    const normalized = {};
                    [5, 4, 3, 2, 1].forEach((rating) => {
                        const key = String(rating);
                        const bucket = normalizedPayload[key] || normalizedPayload[rating] || {};
                        const q = bucket.Q || bucket.q || [];
                        const e = bucket.E || bucket.e || [];
                        const t = bucket.T || bucket.t || [];

                        normalized[key] = {
                            Q: Array.isArray(q) ? q : (q ? [String(q)] : []),
                            E: Array.isArray(e) ? e : (e ? [String(e)] : []),
                            T: Array.isArray(t) ? t : (t ? [String(t)] : []),
                        };
                    });

                    return normalized;
                }

                function hasAnyStandards(basis) {
                    if (!basis || typeof basis !== 'object') return false;
                    return Object.values(basis).some((bucket) => {
                        const q = Array.isArray(bucket?.Q) ? bucket.Q.length : 0;
                        const e = Array.isArray(bucket?.E) ? bucket.E.length : 0;
                        const t = Array.isArray(bucket?.T) ? bucket.T.length : 0;
                        return (q + e + t) > 0;
                    });
                }

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function renderStandardsCell(items) {
                    if (!Array.isArray(items) || items.length === 0) {
                        return '--';
                    }
                    return items
                        .map((item) => `<div class="mb-1 last:mb-0">${escapeHtml(item)}</div>`)
                        .join('');
                }

                function renderBasisTable(basis) {
                    if (!hasAnyStandards(basis)) {
                        basisBody.innerHTML = `
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm text-slate-300">No locked standards found for this indicator.</td>
                            </tr>
                        `;
                        return;
                    }

                    const rowsHtml = [5, 4, 3, 2, 1]
                        .map((rating) => {
                            const key = String(rating);
                            const bucket = basis[key] || { Q: [], E: [], T: [] };
                            return `
                            <tr>
                                <td class="px-6 py-4 font-semibold text-white">${key}</td>
                                <td class="px-6 py-4 align-top" data-col="qual">${renderStandardsCell(bucket.Q)}</td>
                                <td class="px-6 py-4 align-top" data-col="eff">${renderStandardsCell(bucket.E)}</td>
                                <td class="px-6 py-4 align-top" data-col="time">${renderStandardsCell(bucket.T)}</td>
                            </tr>
                        `;
                        })
                        .join('');

                    basisBody.innerHTML = rowsHtml;
                }

                function openRatingBasisModal(selectedTask) {
                    const task = selectedTask || {};
                    const indicator = task.indicator_text || task.accomplishment || '--';
                    const basis = normalizeStandardsPayload(task?.standards_payload);

                    if (basisMfo) {
                        basisMfo.textContent = `MFO: ${task.uwpOutput || '--'}`;
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
                let currentSubmittedDayListDate = null;
                let currentRatedDayListDate = null;

                function updateRatingBasis(data) {
                    const isSubmitted = String(data?.status || '').toLowerCase() === 'submitted';
                    const indicator = isSubmitted
                        ? (data.indicator_text || data.accomplishment || 'No submitted ORS entry')
                        : 'No submitted ORS entry';

                    ratingBasisIndicatorEl.textContent = indicator;

                    if (openBasisBtn) {
                        openBasisBtn.disabled = !isSubmitted;
                        openBasisBtn.classList.toggle('opacity-60', !isSubmitted);
                        openBasisBtn.classList.toggle('cursor-not-allowed', !isSubmitted);
                    }
                }

                function updateRatedBasis(data) {
                    const isRated = String(data?.status || '').toLowerCase() === 'rated';
                    const indicator = isRated
                        ? (data.indicator_text || data.accomplishment || 'No rated ORS entry')
                        : 'No rated ORS entry';

                    if (ratedRatingBasisIndicatorEl) {
                        ratedRatingBasisIndicatorEl.textContent = indicator;
                    }

                    if (openRatedBasisBtn) {
                        openRatedBasisBtn.disabled = !isRated;
                        openRatedBasisBtn.classList.toggle('opacity-60', !isRated);
                        openRatedBasisBtn.classList.toggle('cursor-not-allowed', !isRated);
                    }
                }

                function closeDayListModal() {
                    if (!dayListModal) return;
                    dayListModal.classList.add('hidden');
                    dayListModal.classList.remove('flex');
                    currentSubmittedDayListDate = null;
                    refreshBodyLock();
                }

                function closeRatedDayListModal() {
                    if (!ratedDayListModal) return;
                    ratedDayListModal.classList.add('hidden');
                    ratedDayListModal.classList.remove('flex');
                    currentRatedDayListDate = null;
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

                function closeEvidenceModal() {
                    if (!evidenceModal) return;
                    evidenceModal.classList.add('hidden');
                    evidenceModal.classList.remove('flex');
                    if (evidenceFileList) evidenceFileList.innerHTML = '';
                    if (evidencePreviewArea) evidencePreviewArea.innerHTML = '';
                    if (evidencePreviewFileName) evidencePreviewFileName.textContent = 'Select a file';
                    if (evidenceTopDownloadBtn) {
                        evidenceTopDownloadBtn.href = '#';
                        evidenceTopDownloadBtn.classList.add('pointer-events-none', 'opacity-50');
                    }
                    refreshBodyLock();
                }

                function renderEvidencePreview(file) {
                    if (!evidencePreviewArea || !evidencePreviewFileName || !evidenceTopDownloadBtn) return;

                    evidencePreviewArea.innerHTML = '';
                    evidencePreviewFileName.textContent = file?.file_name || 'Evidence File';
                    const downloadUrl = file?.download_url || file?.preview_url || '#';
                    evidenceTopDownloadBtn.href = downloadUrl;
                    evidenceTopDownloadBtn.classList.toggle('pointer-events-none', !downloadUrl || downloadUrl === '#');
                    evidenceTopDownloadBtn.classList.toggle('opacity-50', !downloadUrl || downloadUrl === '#');

                    const evidenceType = detectEvidenceType(file);

                    if (evidenceType === 'image' && file?.preview_url) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'flex h-[70vh] items-center justify-center';
                        const img = document.createElement('img');
                        img.src = file.preview_url;
                        img.alt = file.file_name || 'Evidence Image';
                        img.className = 'max-h-[70vh] max-w-full rounded-lg object-contain';
                        wrapper.appendChild(img);
                        evidencePreviewArea.appendChild(wrapper);
                        return;
                    }

                    if (evidenceType === 'pdf' && file?.preview_url) {
                        const iframe = document.createElement('iframe');
                        iframe.src = file.preview_url;
                        iframe.className = 'h-[70vh] w-full rounded-lg border border-slate-800 bg-black';
                        iframe.setAttribute('title', file.file_name || 'Evidence PDF');
                        evidencePreviewArea.appendChild(iframe);
                        return;
                    }

                    const fallback = document.createElement('div');
                    fallback.className = 'flex h-[70vh] flex-col items-center justify-center gap-3 text-center text-slate-300';

                    const note = document.createElement('p');
                    note.className = 'text-sm font-semibold text-white';
                    note.textContent = 'Preview not available for this file type.';
                    fallback.appendChild(note);

                    const sub = document.createElement('p');
                    sub.className = 'text-xs text-slate-400';
                    sub.textContent = 'Use download to open this file.';
                    fallback.appendChild(sub);

                    const downloadBtn = document.createElement('a');
                    downloadBtn.href = downloadUrl;
                    downloadBtn.target = '_blank';
                    downloadBtn.rel = 'noopener';
                    downloadBtn.className = 'rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-700';
                    downloadBtn.textContent = 'Download file';
                    fallback.appendChild(downloadBtn);

                    evidencePreviewArea.appendChild(fallback);
                }

                function openEvidenceModal(entryId) {
                    const entry = taskById[String(entryId)];
                    if (!entry || !evidenceModal || !evidenceFileList) return;

                    const files = Array.isArray(entry.evidences) ? entry.evidences : [];

                    if (evidenceModalEntryTitle) {
                        evidenceModalEntryTitle.textContent = entry.accomplishment || '--';
                    }
                    if (evidenceModalEntryMeta) {
                        evidenceModalEntryMeta.textContent = `${entry.employee || '--'} | ${entry.dateLabel || entry.date || '--'}`;
                    }

                    evidenceFileList.innerHTML = '';

                    if (files.length === 0) {
                        const empty = document.createElement('div');
                        empty.className = 'rounded-lg border border-slate-800 bg-slate-950/40 px-3 py-3 text-xs text-slate-400';
                        empty.textContent = 'No evidence files found for this entry.';
                        evidenceFileList.appendChild(empty);
                        renderEvidencePreview(null);
                    } else {
                        files.forEach((file, index) => {
                            const item = document.createElement('div');
                            item.className = 'rounded-lg border border-slate-800 bg-slate-950/40 px-3 py-3';

                            const name = document.createElement('p');
                            name.className = 'truncate text-xs font-semibold text-slate-100';
                            name.textContent = file.file_name || 'Evidence File';

                            const meta = document.createElement('p');
                            meta.className = 'mt-1 text-[11px] text-slate-400';
                            meta.textContent = `${formatFileSize(file.file_size)} | ${formatDateTimeLabel(file.uploaded_at)}`;

                            const actions = document.createElement('div');
                            actions.className = 'mt-2 flex items-center gap-2';

                            const previewBtn = document.createElement('button');
                            previewBtn.type = 'button';
                            previewBtn.className = 'rounded-md border border-blue-500/60 bg-blue-500/10 px-2.5 py-1 text-[11px] font-semibold text-blue-100 hover:bg-blue-500/20';
                            previewBtn.textContent = 'Preview';
                            previewBtn.addEventListener('click', () => {
                                evidenceFileList.querySelectorAll('[data-active="1"]').forEach((activeItem) => {
                                    activeItem.dataset.active = '0';
                                    activeItem.classList.remove('ring-1', 'ring-blue-500/50');
                                });
                                item.dataset.active = '1';
                                item.classList.add('ring-1', 'ring-blue-500/50');
                                renderEvidencePreview(file);
                            });

                            const downloadLink = document.createElement('a');
                            downloadLink.href = file.download_url || file.preview_url || '#';
                            downloadLink.target = '_blank';
                            downloadLink.rel = 'noopener';
                            downloadLink.className = 'rounded-md border border-slate-700 bg-slate-800 px-2.5 py-1 text-[11px] font-semibold text-slate-200 hover:bg-slate-700';
                            downloadLink.textContent = 'Download';

                            actions.appendChild(previewBtn);
                            actions.appendChild(downloadLink);
                            item.appendChild(name);
                            item.appendChild(meta);
                            item.appendChild(actions);
                            evidenceFileList.appendChild(item);

                            if (index === 0) {
                                item.dataset.active = '1';
                                item.classList.add('ring-1', 'ring-blue-500/50');
                                renderEvidencePreview(file);
                            }
                        });
                    }

                    openOrsModalStack('ors-evidence-modal');
                }

                function createDayListEntryCard(entry, openHandler) {
                    const card = document.createElement('div');
                    card.className = 'rounded-xl border border-slate-800 bg-slate-950/40 p-4';

                    const left = document.createElement('div');
                    left.className = 'min-w-0';

                    const title = document.createElement('p');
                    title.className = 'truncate text-sm font-semibold text-white';
                    title.textContent = entry.accomplishment || '--';

                    const subtitle = document.createElement('p');
                    subtitle.className = 'mt-1 text-xs text-slate-400';
                    subtitle.textContent = `Employee: ${entry.employee || '--'}`;
                    left.appendChild(title);
                    left.appendChild(subtitle);

                    const evidenceCount = Number(entry.evidence_count || 0);
                    const evidence = document.createElement('span');
                    evidence.className = evidenceCount > 0
                        ? 'rounded-full border border-emerald-500/60 bg-emerald-500/10 px-2.5 py-1 text-[11px] text-emerald-200'
                        : 'rounded-full border border-slate-700 bg-slate-800 px-2.5 py-1 text-[11px] text-slate-300';
                    evidence.textContent = evidenceCount > 0 ? `Attached (${evidenceCount})` : 'None';

                    const actions = document.createElement('div');
                    actions.className = 'mt-3 flex flex-wrap items-center justify-end gap-2';

                    const viewEvidenceBtn = document.createElement('button');
                    viewEvidenceBtn.type = 'button';
                    const hasEvidenceFiles = Array.isArray(entry.evidences) && entry.evidences.length > 0;
                    viewEvidenceBtn.className = 'rounded-lg border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60';
                    viewEvidenceBtn.textContent = 'View Evidence';
                    viewEvidenceBtn.disabled = !hasEvidenceFiles;
                    viewEvidenceBtn.addEventListener('click', () => openEvidenceModal(entry.id));

                    const openBtn = document.createElement('button');
                    openBtn.type = 'button';
                    openBtn.className = 'rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500';
                    openBtn.textContent = 'Open Monitoring';
                    openBtn.addEventListener('click', () => openHandler(entry));

                    const topRow = document.createElement('div');
                    topRow.className = 'flex flex-wrap items-start justify-between gap-3';
                    topRow.appendChild(left);
                    topRow.appendChild(evidence);

                    actions.appendChild(viewEvidenceBtn);
                    actions.appendChild(openBtn);
                    card.appendChild(topRow);
                    card.appendChild(actions);

                    return card;
                }

                function renderDayList(entries, container, emptyMessage, openHandler) {
                    if (!container) return;
                    container.innerHTML = '';

                    if (!Array.isArray(entries) || entries.length === 0) {
                        const empty = document.createElement('p');
                        empty.className = 'rounded-lg border border-slate-800 bg-slate-950/50 px-4 py-3 text-sm text-slate-400';
                        empty.textContent = emptyMessage;
                        container.appendChild(empty);
                        return;
                    }

                    entries.forEach((entry) => {
                        container.appendChild(createDayListEntryCard(entry, openHandler));
                    });
                }

                function removeEntryFromMap(map, entry) {
                    const date = String(entry?.date || '').trim();
                    if (!date || !Array.isArray(map[date])) return;

                    map[date] = map[date].filter((item) => String(item?.id ?? '') !== String(entry?.id ?? ''));
                    if (map[date].length === 0) {
                        delete map[date];
                    }
                }

                function upsertEntryInMap(map, entry) {
                    const date = String(entry?.date || '').trim();
                    if (!date) return;

                    if (!Array.isArray(map[date])) {
                        map[date] = [];
                    }

                    const existingIndex = map[date].findIndex((item) => String(item?.id ?? '') === String(entry?.id ?? ''));
                    if (existingIndex >= 0) {
                        map[date][existingIndex] = entry;
                    } else {
                        map[date].push(entry);
                    }
                }

                function moveEntryToRated(entry) {
                    const date = entry.date;
                    if (!date) return;

                    byDateSubmitted[date] = (byDateSubmitted[date] || []).filter(x => String(x.id) !== String(entry.id));
                    if (byDateSubmitted[date].length === 0) {
                        delete byDateSubmitted[date];
                    }

                    if (!byDateRated[date]) byDateRated[date] = [];
                    if (!byDateRated[date].some(x => String(x.id) === String(entry.id))) {
                        byDateRated[date].push(entry);
                    } else {
                        byDateRated[date] = byDateRated[date].map((item) =>
                            String(item.id) === String(entry.id) ? entry : item
                        );
                    }

                    sortDateMap(byDateSubmitted);
                    sortDateMap(byDateRated);
                }

                function refreshOpenDayListsForDate(date) {
                    const normalizedDate = String(date || '').trim();
                    if (!normalizedDate) return;

                    if (dayListModal && !dayListModal.classList.contains('hidden') && currentSubmittedDayListDate === normalizedDate) {
                        renderDayList(
                            byDateSubmitted[normalizedDate] || [],
                            dayListEntries,
                            'No submitted entries found for this date.',
                            (entry) => openMonitoringModal(entry)
                        );
                    }

                    if (ratedDayListModal && !ratedDayListModal.classList.contains('hidden') && currentRatedDayListDate === normalizedDate) {
                        renderDayList(
                            byDateRated[normalizedDate] || [],
                            ratedDayListEntries,
                            'No rated entries found for this date.',
                            (entry) => openRatedMonitoringModal(entry)
                        );
                    }
                }

                function openSubmittedDayListModal(dateStr) {
                    if (!dayListModal || !dayListEntries) return;
                    const entries = byDateSubmitted[dateStr] || [];
                    currentSubmittedDayListDate = dateStr;

                    if (dayListDateLabel) {
                        dayListDateLabel.textContent = formatDateLabel(dateStr);
                    }

                    renderDayList(entries, dayListEntries, 'No submitted entries found for this date.', (entry) => {
                        openMonitoringModal(entry);
                    });

                    dayListModal.classList.remove('hidden');
                    dayListModal.classList.add('flex');
                    refreshBodyLock();
                }

                function openRatedDayListModal(dateStr) {
                    if (!ratedDayListModal || !ratedDayListEntries) return;
                    const entries = byDateRated[dateStr] || [];
                    currentRatedDayListDate = dateStr;

                    if (ratedDayListDateLabel) {
                        ratedDayListDateLabel.textContent = formatDateLabel(dateStr);
                    }

                    renderDayList(entries, ratedDayListEntries, 'No rated entries found for this date.', (entry) => {
                        openRatedMonitoringModal(entry);
                    });

                    ratedDayListModal.classList.remove('hidden');
                    ratedDayListModal.classList.add('flex');
                    refreshBodyLock();
                }

                function openMonitoringModal(data) {
                    if (!modal) return;
                    data.status = String(data?.status || '').toLowerCase() || 'submitted';
                    currentModalData = data;

                    employeeEl.textContent = data.employee || '--';
                    officeEl.textContent = data.office || '--';
                    dateEl.textContent = data.dateLabel || data.date || '--';

                    majorOutputEl.textContent = data.uwpOutput || '--';
                    accomplishmentEl.textContent = data.accomplishment || '--';

                    durationEl.textContent = formatDurationFromEntry(data);
                    evidenceEl.textContent = Number(data.evidence_count || 0) > 0
                        ? `Evidence attached (${Number(data.evidence_count || 0)})`
                        : 'No evidence (read-only)';
                    quantityEl.textContent = data.quantity || '--';
                    notesEl.textContent = data.notes || '--';

                    if (monitoringEvidenceBtn) {
                        const hasEvidenceFiles = Array.isArray(data.evidences) && data.evidences.length > 0;
                        monitoringEvidenceBtn.disabled = !hasEvidenceFiles;
                        monitoringEvidenceBtn.classList.toggle('hidden', !hasEvidenceFiles);
                        monitoringEvidenceBtn.onclick = hasEvidenceFiles
                            ? () => openEvidenceModal(data.id)
                            : null;
                    }

                    const meta = STATUS_META[data.status] || STATUS_META.missing;
                    statusEl.textContent = meta.label;
                    statusEl.className = `status-chip ${meta.badge}`;
                    statusDetailEl.textContent = meta.detail || '';

                    updateRatingBasis(data);

                    const status = String(data.status || '').toLowerCase();
                    const rateable = status === 'submitted';
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
                            const tpl = saveForm.dataset.actionTemplate;
                            saveForm.action = tpl.replace('__ID__', encodeURIComponent(data.id));
                        } else {
                            saveForm.removeAttribute('action');
                        }
                    }

                    openOrsModalStack('ors-monitoring-modal');
                }

                function openRatedMonitoringModal(data) {
                    if (!ratedMonitoringModal) return;
                    currentModalData = data;

                    if (ratedEmployeeEl) ratedEmployeeEl.textContent = data.employee || '--';
                    if (ratedOfficeEl) ratedOfficeEl.textContent = data.office || '--';
                    if (ratedDateEl) ratedDateEl.textContent = data.dateLabel || data.date || '--';
                    if (ratedMajorOutputEl) ratedMajorOutputEl.textContent = data.uwpOutput || '--';
                    if (ratedAccomplishmentEl) ratedAccomplishmentEl.textContent = data.accomplishment || '--';
                    if (ratedDurationEl) ratedDurationEl.textContent = formatDurationFromEntry(data);

                    if (ratedEvidenceEl) {
                        ratedEvidenceEl.textContent = Number(data.evidence_count || 0) > 0
                            ? `Evidence attached (${Number(data.evidence_count || 0)})`
                            : 'No evidence (read-only)';
                    }

                    if (ratedMonitoringEvidenceBtn) {
                        const hasEvidenceFiles = Array.isArray(data.evidences) && data.evidences.length > 0;
                        ratedMonitoringEvidenceBtn.disabled = !hasEvidenceFiles;
                        ratedMonitoringEvidenceBtn.classList.toggle('hidden', !hasEvidenceFiles);
                        ratedMonitoringEvidenceBtn.onclick = hasEvidenceFiles
                            ? () => openEvidenceModal(data.id)
                            : null;
                    }

                    if (ratedQuantityEl) ratedQuantityEl.textContent = data.quantity || '--';
                    if (ratedNotesEl) ratedNotesEl.textContent = data.notes || '--';

                    const ratedMeta = STATUS_META.rated;
                    if (ratedStatusEl) {
                        ratedStatusEl.textContent = ratedMeta.label;
                        ratedStatusEl.className = `status-chip ${ratedMeta.badge}`;
                    }
                    if (ratedStatusDetailEl) {
                        ratedStatusDetailEl.textContent = ratedMeta.detail || '';
                    }

                    if (ratedMonitoringQualityEl) {
                        ratedMonitoringQualityEl.textContent = data.quality_rating ? String(data.quality_rating) : '--';
                    }
                    if (ratedMonitoringTimelinessEl) {
                        ratedMonitoringTimelinessEl.textContent = data.timeliness_rating ? String(data.timeliness_rating) : '--';
                    }
                    if (ratedMonitoringRemarksEl) {
                        const remarks = String(data.remarks || '').trim();
                        ratedMonitoringRemarksEl.textContent = remarks !== '' ? remarks : '--';
                    }

                    updateRatedBasis(data);
                    openOrsModalStack('ors-rated-monitoring-modal');
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
                ratedMonitoringModal?.addEventListener('click', (event) => {
                    if (event.target === ratedMonitoringModal) closeOrsModal('ors-rated-monitoring-modal');
                });
                dayListModal?.addEventListener('click', (event) => {
                    if (event.target === dayListModal) closeDayListModal();
                });
                ratedDayListModal?.addEventListener('click', (event) => {
                    if (event.target === ratedDayListModal) closeRatedDayListModal();
                });
                evidenceModal?.addEventListener('click', (event) => {
                    if (event.target === evidenceModal) closeEvidenceModal();
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

                    if (evidenceModal && !evidenceModal.classList.contains('hidden')) {
                        closeEvidenceModal();
                        return;
                    }

                    if (ratedMonitoringModal && !ratedMonitoringModal.classList.contains('hidden')) {
                        closeOrsModal('ors-rated-monitoring-modal');
                        return;
                    }

                    if (modal && !modal.classList.contains('hidden')) {
                        closeOrsModal('ors-monitoring-modal');
                        return;
                    }

                    if (ratedDayListModal && !ratedDayListModal.classList.contains('hidden')) {
                        closeRatedDayListModal();
                        return;
                    }

                    if (dayListModal && !dayListModal.classList.contains('hidden')) {
                        closeDayListModal();
                        return;
                    }

                    if (emptyDateModal && !emptyDateModal.classList.contains('hidden')) {
                        closeEmptyDateModal();
                    }
                });

                function openBasis() {
                    if (!currentModalData) return;
                    openRatingBasisModal(currentModalData);
                }

                function closeBasis() {
                    basisModal?.classList.add('hidden');
                    basisModal?.classList.remove('flex');
                    refreshBodyLock();
                }

                openBasisBtn?.addEventListener('click', openBasis);
                openRatedBasisBtn?.addEventListener('click', openBasis);
                closeBasisBtn?.addEventListener('click', closeBasis);
                basisDoneBtn?.addEventListener('click', closeBasis);
                closeDayListTopBtn?.addEventListener('click', closeDayListModal);
                closeDayListBottomBtn?.addEventListener('click', closeDayListModal);
                closeRatedDayListTopBtn?.addEventListener('click', closeRatedDayListModal);
                closeRatedDayListBottomBtn?.addEventListener('click', closeRatedDayListModal);
                closeEvidenceTopBtn?.addEventListener('click', closeEvidenceModal);
                closeEvidenceBottomBtn?.addEventListener('click', closeEvidenceModal);
                closeRatedMonitoringBottomBtn?.addEventListener('click', () => closeOrsModal('ors-rated-monitoring-modal'));
                closeEmptyDateTopBtn?.addEventListener('click', closeEmptyDateModal);
                closeEmptyDateBottomBtn?.addEventListener('click', closeEmptyDateModal);

                basisModal?.addEventListener('click', (event) => {
                    if (event.target === basisModal) closeBasis();
                });

                basisFilter?.addEventListener('change', () => {
                    applyBasisColumnFilter(basisFilter.value);
                });

                // Save rating to backend
                saveBtn?.addEventListener('click', async () => {
                    const modalStatus = String(currentModalData?.status || '').toLowerCase();
                    if (!currentModalData || modalStatus !== 'submitted') {
                        showSnackbar('error', 'Rating is available only for Submitted ORS entries.');
                        return;
                    }

                    const q = document.getElementById('ratingQual').value;
                    const t = document.getElementById('ratingTime').value;

                    if (!q || !t) {
                        showSnackbar('error', 'Select Quality and Timeliness ratings.');
                        return;
                    }

                    if (!saveForm || !saveForm.action) {
                        showSnackbar('error', 'Unable to save rating for this entry.');
                        return;
                    }

                    if (saveBtn.dataset.loadingActive === 'true') {
                        return;
                    }

                    formQualityRating.value = q;
                    formTimelinessRating.value = t;
                    formRemarks.value = document.getElementById('ratingRemarks').value || '';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        || saveForm.querySelector('input[name="_token"]')?.value
                        || '';

                    const previousStatus = modalStatus;

                    try {
                        setButtonLoading(saveBtn, true, 'Saving...');
                        saveBtn.dataset.loadingActive = 'true';

                        const response = await fetch(saveForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: new FormData(saveForm),
                        });

                        const payload = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            if (response.status === 422 && payload?.errors) {
                                const firstError = Object.values(payload.errors)?.[0]?.[0];
                                showSnackbar('error', firstError || payload?.message || 'Unable to save rating.');
                                return;
                            }

                            showSnackbar('error', payload?.message || 'Unable to save rating.');
                            return;
                        }

                        if (!payload?.success) {
                            showSnackbar('error', payload?.message || 'Unable to save rating.');
                            return;
                        }

                        const entryId = String(currentModalData.id);
                        const nextStatus = String(payload?.status || payload?.orsEntry?.status || previousStatus).toLowerCase();
                        const updatedEntry = {
                            ...currentModalData,
                            status: nextStatus,
                            quality_rating: payload?.monitoring?.quality_rating ?? Number(q),
                            timeliness_rating: payload?.monitoring?.timeliness_rating ?? Number(t),
                            remarks: payload?.monitoring?.remarks ?? (formRemarks.value || ''),
                        };

                        taskById[entryId] = {
                            ...(taskById[entryId] || {}),
                            ...updatedEntry,
                        };
                        currentModalData = taskById[entryId];

                        if (nextStatus === 'rated') {
                            if (saveBtn) {
                                saveBtn.disabled = true;
                                saveBtn.classList.add('opacity-60', 'cursor-not-allowed');
                            }

                            if (ratingControls) {
                                ratingControls.classList.add('hidden');
                            }

                            if (ratingLockedNote) {
                                ratingLockedNote.classList.remove('hidden');
                            }

                            const ratingQual = document.getElementById('ratingQual');
                            const ratingTime = document.getElementById('ratingTime');
                            const ratingRemarks = document.getElementById('ratingRemarks');

                            if (ratingQual) ratingQual.disabled = true;
                            if (ratingTime) ratingTime.disabled = true;
                            if (ratingRemarks) ratingRemarks.disabled = true;
                        }

                        if (previousStatus === 'submitted' && nextStatus === 'rated') {
                            moveEntryToRated(currentModalData);
                        } else if (nextStatus === 'rated') {
                            removeEntryFromMap(byDateSubmitted, currentModalData);
                            upsertEntryInMap(byDateRated, currentModalData);
                            sortDateMap(byDateSubmitted);
                            sortDateMap(byDateRated);
                        } else {
                            removeEntryFromMap(byDateRated, currentModalData);
                            upsertEntryInMap(byDateSubmitted, currentModalData);
                            sortDateMap(byDateSubmitted);
                            sortDateMap(byDateRated);
                        }

                        refreshCalendarEvents();
                        refreshOpenDayListsForDate(currentModalData.date);
                        closeOrsModal('ors-monitoring-modal');
                        showSnackbar('success', 'Rating saved.');
                    } finally {
                        setButtonLoading(saveBtn, false);
                        delete saveBtn.dataset.loadingActive;
                    }
                });

                @if(isset($orsEntry) && $orsEntry)
                    setTimeout(() => {
                        const autoLoadEntryId = '{{ $orsEntry->id }}';
                        const autoLoadEntry = taskById[autoLoadEntryId];
                        if (autoLoadEntry) {
                            const autoStatus = String(autoLoadEntry.status || '').toLowerCase();
                            if (autoStatus === 'rated') {
                                openRatedMonitoringModal(autoLoadEntry);
                            } else {
                                openMonitoringModal(autoLoadEntry);
                            }
                        }
                    }, 100);
                @endif
            });
        </script>
    @endpush
@endsection
