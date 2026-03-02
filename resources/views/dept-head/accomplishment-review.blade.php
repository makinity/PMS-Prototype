
@extends('layouts.dept-head')

@section('main-content')
    @php
        $submissionRows = is_array($rows ?? null) ? $rows : [];
        $periodLabelSafe = (string) ($periodLabel ?? '--');

        $statusBadgeClassMap = [
            'submitted_to_supervisor' => 'inline-flex items-center rounded-full border border-sky-500/40 bg-sky-500/10 px-2.5 py-1 text-xs font-semibold text-sky-200',
            'supervisor_endorsed' => 'inline-flex items-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-200',
            'dept_head_endorsed' => 'inline-flex items-center rounded-full border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 text-xs font-semibold text-amber-200',
            'pmt_approved' => 'inline-flex items-center rounded-full border border-violet-500/40 bg-violet-500/10 px-2.5 py-1 text-xs font-semibold text-violet-200',
            'returned_to_employee' => 'inline-flex items-center rounded-full border border-rose-500/40 bg-rose-500/10 px-2.5 py-1 text-xs font-semibold text-rose-200',
            'draft' => 'inline-flex items-center rounded-full border border-slate-600 bg-slate-800/70 px-2.5 py-1 text-xs font-semibold text-slate-200',
        ];
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

        @if (!empty($infoMessage ?? null))
            <div class="rounded-xl border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-200">
                {{ $infoMessage }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-white">Accomplishment Review</h1>
                <p class="text-sm text-slate-400">Supervisor- and Dept Head-endorsed submissions for your office/unit.</p>
                <p class="mt-1 text-xs text-slate-500">Active Performance Period: {{ $periodLabelSafe }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Records</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ count($submissionRows) }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/70 text-xs uppercase tracking-[0.14em] text-slate-400">
                        <tr>
                            <th class="px-5 py-3 text-left">Employee</th>
                            <th class="px-5 py-3 text-left">Office</th>
                            <th class="px-5 py-3 text-left">Period</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-left">Submitted At</th>
                            <th class="px-5 py-3 text-left">Supervisor Action</th>
                            <th class="px-5 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($submissionRows as $row)
                            @php
                                $statusKey = strtolower((string) ($row['status'] ?? 'draft'));
                                $statusBadgeClasses = $statusBadgeClassMap[$statusKey] ?? $statusBadgeClassMap['draft'];
                            @endphp
                            <tr class="bg-slate-900/40">
                                <td class="px-5 py-3 font-semibold text-slate-100">{{ $row['employee_name'] ?? '--' }}</td>
                                <td class="px-5 py-3">{{ $row['office_name'] ?? '--' }}</td>
                                <td class="px-5 py-3">{{ $row['period_label'] ?? $periodLabelSafe }}</td>
                                <td class="px-5 py-3">
                                    <span class="{{ $statusBadgeClasses }}">{{ $row['status_label'] ?? 'Draft' }}</span>
                                </td>
                                <td class="px-5 py-3">{{ $row['submitted_at_label'] ?? '--' }}</td>
                                <td class="px-5 py-3">{{ $row['supervisor_action_at_label'] ?? '--' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <button type="button"
                                            data-open-submission
                                            data-submission-id="{{ $row['id'] }}"
                                            aria-label="View submission"
                                            class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-2.5 py-2 text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-slate-900/40">
                                <td colspan="7" class="px-5 py-8 text-center text-sm text-slate-400">No supervisor- or dept-head-endorsed submissions found for your office/unit.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script id="dept-head-submissions-json" type="application/json">{!! json_encode($submissionPayloads ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

    <div id="submission-view-modal" data-preview-modal class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-sky-300">Dept Head Review</p>
                    <h3 class="text-lg font-semibold text-white">Accomplishment Review Details</h3>
                    <p class="mt-1 text-sm text-slate-400">Read-only snapshot of employee accomplishment submission.</p>
                </div>
                <button type="button" data-close-modal class="text-2xl leading-none text-slate-400 transition hover:text-white">&times;</button>
            </div>

            <div class="mt-4 max-h-[68vh] space-y-4 overflow-y-auto pr-1 text-sm text-slate-200">
                {{-- KPI Cards (JS-filled) --}}
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                    {{-- Row 1 (equal 50/50 on lg) --}}
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Status</p>
                        <div class="mt-2">
                            <span id="viewSubmissionStatus"
                                class="inline-flex items-center rounded-full border border-slate-600 bg-slate-800/70 px-3 py-1 text-xs font-semibold text-slate-200">--</span>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Submitted At</p>
                        <p id="viewSubmissionSubmittedAt" class="mt-2 text-base font-semibold text-white">--</p>
                    </div>

                    {{-- Row 2 (3 equal cards under the 2-card row) --}}
                    <div class="grid grid-cols-1 gap-3 lg:col-span-2 lg:grid-cols-3">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Employee</p>
                            <p id="viewSubmissionEmployee" class="mt-2 text-base font-semibold text-white">--</p>
                        </div>

                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Office</p>
                            <p id="viewSubmissionOffice" class="mt-2 text-base font-semibold text-white">--</p>
                        </div>

                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Period</p>
                            <p id="viewSubmissionPeriod" class="mt-2 text-base font-semibold text-white">--</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Employee Remarks</p>
                    <p id="viewSubmissionRemarks" class="mt-2 whitespace-pre-line text-sm text-slate-200">--</p>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Supervisor Action</p>
                    <p id="viewSubmissionSupervisorActionAt" class="mt-2 text-sm font-semibold text-slate-100">--</p>
                    <p id="viewSubmissionSupervisorRemarks" class="mt-2 whitespace-pre-line text-sm text-slate-300">--</p>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Attachments</p>
                    <ul id="viewSubmissionAttachments" class="mt-2 space-y-2"></ul>
                    <p id="viewSubmissionAttachmentsEmpty" class="mt-2 text-sm text-slate-400">No attachments uploaded.</p>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-semibold text-white">SMPOR - Monitoring Summary</h4>
                                <p id="viewSmporSource" class="mt-1 text-xs text-slate-400">Submitted MPORs snapshot.</p>
                            </div>
                            <button type="button" data-open-smpor-preview aria-label="Open SMPOR preview" class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-2.5 py-2 text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-semibold text-white">IPCR Accomplishment Report</h4>
                                <p class="mt-1 text-xs text-slate-400">Success indicators and standards snapshot.</p>
                            </div>
                            <button type="button" data-open-ipcr-preview aria-label="Open IPCR preview" class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-2.5 py-2 text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-4 border-t border-slate-800 pt-4">
                <form method="POST"
                    id="submissionEndorseForm"
                    data-action-template="{{ route('dept-head.acc-review.endorse', ['id' => '__SUBMISSION_ID__']) }}"
                    action="{{ route('dept-head.acc-review.endorse', ['id' => 0]) }}"
                    class="inline">
                    @csrf
                    <button id="submissionEndorseBtn" type="submit"
                        class="rounded-lg border border-emerald-600/40 bg-emerald-600/10 px-3 py-2 text-xs font-semibold text-emerald-300 transition hover:bg-emerald-600/20">
                        Endorse
                    </button>
                </form>
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    <div id="dept-head-smpor-preview-modal" data-preview-modal data-parent-modal-id="submission-view-modal" class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">SMPOR (Monitoring Summary)</p>
                    <h3 class="text-lg font-semibold text-white">SMPOR Preview</h3>
                    <p class="mt-1 text-sm text-slate-400">Read-only monitoring totals from the submitted snapshot.</p>
                </div>
                <button type="button" data-close-modal class="text-2xl leading-none text-slate-400 transition hover:text-white">&times;</button>
            </div>

            <div class="mt-4 max-h-[66vh] space-y-5 overflow-y-auto pr-1 text-sm text-slate-200">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3"><p class="text-[11px] uppercase text-slate-500">Employee</p><p id="smporEmployeeName" class="mt-1 font-semibold">--</p></div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3"><p class="text-[11px] uppercase text-slate-500">Office/Unit</p><p id="smporOfficeName" class="mt-1 font-semibold">--</p></div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3"><p class="text-[11px] uppercase text-slate-500">Period</p><p id="smporPeriodLabel" class="mt-1 font-semibold">--</p></div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3"><p class="text-[11px] uppercase text-slate-500">Source</p><p id="smporSourceLabel" class="mt-1 font-semibold">--</p></div>
                </div>

                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" data-smpor-tab="quantity" class="rounded-lg border border-sky-500/40 bg-sky-500/20 px-3 py-1.5 text-xs font-semibold text-sky-200 transition">Efficiency/Quantity</button>
                        <button type="button" data-smpor-tab="quality" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-300 transition hover:bg-slate-800">Quality/Effectiveness</button>
                        <button type="button" data-smpor-tab="timeliness" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-300 transition hover:bg-slate-800">Timeliness</button>
                    </div>
                    <p class="text-xs text-slate-400">Monthly totals are derived from rated ORS monitoring within the selected submission snapshot.</p>
                </div>

                <div id="smporQuantityPanel" data-smpor-tab-panel="quantity" class="overflow-x-auto rounded-xl border border-slate-800"></div>
                <div id="smporQualityPanel" data-smpor-tab-panel="quality" class="hidden overflow-x-auto rounded-xl border border-slate-800"></div>
                <div id="smporTimelinessPanel" data-smpor-tab-panel="timeliness" class="hidden overflow-x-auto rounded-xl border border-slate-800"></div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-800 pt-4"><button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Close</button></div>
        </div>
    </div>

    <div id="dept-head-ipcr-preview-modal" data-preview-modal data-parent-modal-id="submission-view-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-7xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR (Accomplishment Report)</p>
                    <h3 class="text-lg font-semibold text-white">IPCR Preview</h3>
                    <p class="mt-1 text-sm text-slate-400">Read-only commitments and indicators for the submitted snapshot.</p>
                </div>
                <button type="button" data-close-modal class="text-2xl leading-none text-slate-400 transition hover:text-white">&times;</button>
            </div>

            <div class="mt-4 max-h-[66vh] space-y-4 overflow-y-auto pr-1 text-sm text-slate-200">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3"><p class="text-[11px] uppercase text-slate-500">Employee</p><p id="ipcrEmployeeName" class="mt-1 font-semibold">--</p></div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3"><p class="text-[11px] uppercase text-slate-500">Office/Unit</p><p id="ipcrOfficeName" class="mt-1 font-semibold">--</p></div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3"><p class="text-[11px] uppercase text-slate-500">Period</p><p id="ipcrPeriodLabel" class="mt-1 font-semibold">--</p></div>
                </div>
                <div id="ipcrSectionsContainer" class="space-y-4"></div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-800 pt-4"><button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Close</button></div>
        </div>
    </div>

    <div id="dept-head-ipcr-indicators-modal" data-preview-modal data-parent-modal-id="dept-head-ipcr-preview-modal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="ipcrIndicatorsTitle" class="text-lg font-semibold text-white">Success Indicators</h3>
                </div>
                <button type="button" data-close-modal class="text-2xl leading-none text-slate-400 transition hover:text-white">&times;</button>
            </div>

            <div class="mt-4 max-h-[60vh] overflow-y-auto pr-1">
                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="min-w-full text-left text-sm text-slate-200">
                        <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Indicator</th>
                                <th class="px-4 py-3 text-center">Q</th>
                                <th class="px-4 py-3 text-center">E</th>
                                <th class="px-4 py-3 text-center">T</th>
                                <th class="px-4 py-3 text-center">A</th>
                                <th class="px-4 py-3 text-center">Standards</th>
                            </tr>
                        </thead>
                        <tbody id="ipcrIndicatorsBody" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-800 pt-4"><button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Close</button></div>
        </div>
    </div>

    <div id="dept-head-ipcr-standards-modal" data-preview-modal data-parent-modal-id="dept-head-ipcr-indicators-modal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards Matrix</p>
                    <h3 class="text-lg font-semibold text-white">Standards (Q/E/T)</h3>
                    <p id="ipcrStandardsIndicatorText" class="mt-1 text-sm text-slate-400">--</p>
                </div>
                <button type="button" data-close-modal class="text-2xl leading-none text-slate-400 transition hover:text-white">&times;</button>
            </div>

            <div class="mt-4 max-h-[60vh] overflow-y-auto pr-1">
                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="min-w-full text-left text-sm text-slate-200">
                        <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Rating</th>
                                <th class="px-4 py-3">Quality (Q)</th>
                                <th class="px-4 py-3">Efficiency (E)</th>
                                <th class="px-4 py-3">Timeliness (T)</th>
                            </tr>
                        </thead>
                        <tbody id="ipcrStandardsBody" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3 border-t border-slate-800 pt-4"><button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Close</button></div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const payloadScript = document.getElementById('dept-head-submissions-json');
                const previewModals = Array.from(document.querySelectorAll('[data-preview-modal]'));
                const openPreviewStack = [];

                const submissionEmployeeEl = document.getElementById('viewSubmissionEmployee');
                const submissionOfficeEl = document.getElementById('viewSubmissionOffice');
                const submissionPeriodEl = document.getElementById('viewSubmissionPeriod');
                const submissionStatusEl = document.getElementById('viewSubmissionStatus');
                const submissionSubmittedAtEl = document.getElementById('viewSubmissionSubmittedAt');
                const submissionRemarksEl = document.getElementById('viewSubmissionRemarks');
                const submissionSupervisorActionAtEl = document.getElementById('viewSubmissionSupervisorActionAt');
                const submissionSupervisorRemarksEl = document.getElementById('viewSubmissionSupervisorRemarks');
                const submissionAttachmentsEl = document.getElementById('viewSubmissionAttachments');
                const submissionAttachmentsEmptyEl = document.getElementById('viewSubmissionAttachmentsEmpty');
                const smporSourceSummaryEl = document.getElementById('viewSmporSource');
                const submissionEndorseFormEl = document.getElementById('submissionEndorseForm');
                const submissionEndorseBtnEl = document.getElementById('submissionEndorseBtn');

                const smporEmployeeEl = document.getElementById('smporEmployeeName');
                const smporOfficeEl = document.getElementById('smporOfficeName');
                const smporPeriodEl = document.getElementById('smporPeriodLabel');
                const smporSourceEl = document.getElementById('smporSourceLabel');
                const smporQuantityPanelEl = document.getElementById('smporQuantityPanel');
                const smporQualityPanelEl = document.getElementById('smporQualityPanel');
                const smporTimelinessPanelEl = document.getElementById('smporTimelinessPanel');
                const smporTabButtons = Array.from(document.querySelectorAll('[data-smpor-tab]'));
                const smporTabPanels = Array.from(document.querySelectorAll('[data-smpor-tab-panel]'));

                const ipcrEmployeeEl = document.getElementById('ipcrEmployeeName');
                const ipcrOfficeEl = document.getElementById('ipcrOfficeName');
                const ipcrPeriodEl = document.getElementById('ipcrPeriodLabel');
                const ipcrSectionsContainerEl = document.getElementById('ipcrSectionsContainer');
                const ipcrIndicatorsTitleEl = document.getElementById('ipcrIndicatorsTitle');
                const ipcrIndicatorsBodyEl = document.getElementById('ipcrIndicatorsBody');
                const ipcrStandardsIndicatorTextEl = document.getElementById('ipcrStandardsIndicatorText');
                const ipcrStandardsBodyEl = document.getElementById('ipcrStandardsBody');

                const openSmporPreviewBtn = document.querySelector('[data-open-smpor-preview]');
                const openIpcrPreviewBtn = document.querySelector('[data-open-ipcr-preview]');

                let payloadMap = {};
                let currentPayload = null;
                let currentIpcrSections = [];
                let selectedIndicators = [];

                try {
                    const parsed = JSON.parse(payloadScript?.textContent || '{}');
                    payloadMap = parsed && typeof parsed === 'object' ? parsed : {};
                } catch (error) {
                    payloadMap = {};
                }

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function isAnyModalOpen() {
                    return openPreviewStack.length > 0;
                }

                function syncBodyScroll() {
                    document.body.classList.toggle('overflow-hidden', isAnyModalOpen());
                }

                function refreshPreviewModalZIndices() {
                    const baseZ = 80;
                    openPreviewStack.forEach((modalEl, index) => {
                        modalEl.style.zIndex = String(baseZ + (index * 10));
                    });
                }

                function getParentModalId(modalEl) {
                    return modalEl?.dataset?.parentModalId || '';
                }

                function isDescendantOfModal(modalEl, ancestorModalId) {
                    if (!modalEl || !ancestorModalId) {
                        return false;
                    }

                    let parentId = getParentModalId(modalEl);
                    while (parentId) {
                        if (parentId === ancestorModalId) {
                            return true;
                        }

                        const parentModal = document.getElementById(parentId);
                        if (!parentModal) {
                            break;
                        }
                        parentId = getParentModalId(parentModal);
                    }

                    return false;
                }

                function hidePreviewModal(modalEl) {
                    if (!modalEl) return;
                    modalEl.classList.add('hidden');
                    modalEl.classList.remove('flex');
                    modalEl.style.zIndex = '';
                }

                function openPreviewModal(modalId) {
                    if (!modalId) return;
                    const modalEl = document.getElementById(modalId);
                    if (!modalEl) return;

                    const existingIndex = openPreviewStack.indexOf(modalEl);
                    if (existingIndex !== -1) {
                        openPreviewStack.splice(existingIndex, 1);
                    }

                    modalEl.classList.remove('hidden');
                    modalEl.classList.add('flex');
                    openPreviewStack.push(modalEl);
                    refreshPreviewModalZIndices();
                    syncBodyScroll();
                }

                function closePreviewModal(modalEl, cascadeChildren = true) {
                    if (!modalEl) return;

                    if (cascadeChildren && modalEl.id) {
                        const descendants = openPreviewStack.filter((item) => isDescendantOfModal(item, modalEl.id)).reverse();
                        descendants.forEach((descendantEl) => closePreviewModal(descendantEl, false));
                    }

                    hidePreviewModal(modalEl);
                    const index = openPreviewStack.indexOf(modalEl);
                    if (index !== -1) {
                        openPreviewStack.splice(index, 1);
                    }

                    refreshPreviewModalZIndices();
                    syncBodyScroll();
                }

                function statusBadgeClasses(status) {
                    switch ((status || '').toLowerCase()) {
                        case 'submitted_to_supervisor':
                            return 'inline-flex items-center rounded-full border border-sky-500/40 bg-sky-500/10 px-2.5 py-1 text-xs font-semibold text-sky-200';
                        case 'supervisor_endorsed':
                            return 'inline-flex items-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-200';
                        case 'dept_head_endorsed':
                            return 'inline-flex items-center rounded-full border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 text-xs font-semibold text-amber-200';
                        case 'pmt_approved':
                            return 'inline-flex items-center rounded-full border border-violet-500/40 bg-violet-500/10 px-2.5 py-1 text-xs font-semibold text-violet-200';
                        case 'returned_to_employee':
                            return 'inline-flex items-center rounded-full border border-rose-500/40 bg-rose-500/10 px-2.5 py-1 text-xs font-semibold text-rose-200';
                        default:
                            return 'inline-flex items-center rounded-full border border-slate-600 bg-slate-800/70 px-2.5 py-1 text-xs font-semibold text-slate-200';
                    }
                }

                function statusLabel(status, fallback) {
                    if (fallback && String(fallback).trim() !== '') {
                        return String(fallback);
                    }

                    switch ((status || '').toLowerCase()) {
                        case 'submitted_to_supervisor':
                            return 'Submitted to Supervisor';
                        case 'supervisor_endorsed':
                            return 'Supervisor Endorsed';
                        case 'dept_head_endorsed':
                            return 'Dept Head Endorsed';
                        case 'pmt_approved':
                            return 'PMT Approved';
                        case 'returned_to_employee':
                            return 'Returned to Employee';
                        default:
                            return 'Draft';
                    }
                }

                function formatNumber(value, fixed = null) {
                    const numeric = Number(value ?? 0);
                    if (!Number.isFinite(numeric)) {
                        return fixed === 2 ? '0.00' : '0';
                    }

                    if (fixed === 2) {
                        return numeric.toFixed(2);
                    }

                    if (Math.floor(numeric) === numeric) {
                        return String(numeric);
                    }

                    return numeric.toFixed(2).replace(/\.00$/, '').replace(/(\.\d*[1-9])0+$/, '$1');
                }

                function normalizeStandardsPayload(payload) {
                    if (!payload) return {};
                    if (typeof payload === 'string') {
                        try {
                            const parsed = JSON.parse(payload);
                            return parsed && typeof parsed === 'object' ? parsed : {};
                        } catch (error) {
                            return {};
                        }
                    }

                    return typeof payload === 'object' ? payload : {};
                }

                function normalizeStandardsDimension(value) {
                    if (Array.isArray(value)) {
                        return value.map((item) => String(item ?? '').trim()).filter((item) => item !== '');
                    }

                    const text = String(value ?? '').trim();
                    return text === '' ? [] : [text];
                }

                function buildStandardsCell(values) {
                    if (!Array.isArray(values) || values.length === 0) {
                        return '<span class="text-slate-400">--</span>';
                    }

                    return `<ul class="list-disc space-y-1 pl-4 text-xs text-slate-200">${values.map((item) => `<li>${escapeHtml(item)}</li>`).join('')}</ul>`;
                }

                function renderAttachments(attachments) {
                    if (!submissionAttachmentsEl || !submissionAttachmentsEmptyEl) {
                        return;
                    }

                    const list = Array.isArray(attachments) ? attachments : [];
                    submissionAttachmentsEl.innerHTML = '';

                    if (list.length === 0) {
                        submissionAttachmentsEmptyEl.classList.remove('hidden');
                        return;
                    }

                    submissionAttachmentsEmptyEl.classList.add('hidden');
                    list.forEach((attachment) => {
                        const item = document.createElement('li');
                        item.className = 'rounded-lg border border-slate-800 bg-slate-900/40 px-3 py-2';

                        const name = escapeHtml(attachment?.name || 'Attachment');
                        const url = attachment?.url ? String(attachment.url) : null;

                        item.innerHTML = url
                            ? `<a href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm text-sky-300 transition hover:text-sky-200"><i class="fa-solid fa-paperclip text-xs"></i><span>${name}</span></a>`
                            : `<span class="inline-flex items-center gap-2 text-sm text-slate-300"><i class="fa-solid fa-paperclip text-xs"></i><span>${name}</span></span>`;

                        submissionAttachmentsEl.appendChild(item);
                    });
                }

                function renderSubmissionModal(payload) {
                    currentPayload = payload || null;
                    if (!currentPayload) return;

                    if (submissionEmployeeEl) submissionEmployeeEl.textContent = payload.employee_name || '--';
                    if (submissionOfficeEl) submissionOfficeEl.textContent = payload.office_name || '--';
                    if (submissionPeriodEl) submissionPeriodEl.textContent = payload.period_label || '--';
                    if (submissionSubmittedAtEl) submissionSubmittedAtEl.textContent = payload.submitted_at_label || '--';
                    if (submissionRemarksEl) submissionRemarksEl.textContent = (payload.remarks || '').trim() !== '' ? payload.remarks : '--';
                    if (submissionSupervisorActionAtEl) {
                        submissionSupervisorActionAtEl.textContent = payload.supervisor_action_at_label || '--';
                    }
                    if (submissionSupervisorRemarksEl) {
                        submissionSupervisorRemarksEl.textContent = (payload.supervisor_remarks || '').trim() !== '' ? payload.supervisor_remarks : '--';
                    }

                    if (submissionStatusEl) {
                        submissionStatusEl.className = statusBadgeClasses(payload.status || 'draft');
                        submissionStatusEl.textContent = statusLabel(payload.status || 'draft', payload.status_label || '');
                    }

                    if (smporSourceSummaryEl) {
                        const mode = payload.smporModeLabel || 'Preview (Submitted Snapshot)';
                        const source = payload.smporSourceLabel || 'Submitted MPORs (snapshot)';
                        smporSourceSummaryEl.textContent = `${mode} - ${source}`;
                    }

                    if (submissionEndorseFormEl) {
                        const submissionId = String(payload.id ?? '').trim();
                        const actionTemplate = String(submissionEndorseFormEl.dataset.actionTemplate || '');
                        if (submissionId !== '' && actionTemplate.includes('__SUBMISSION_ID__')) {
                            submissionEndorseFormEl.action = actionTemplate.replace('__SUBMISSION_ID__', encodeURIComponent(submissionId));
                        }
                    }

                    if (submissionEndorseBtnEl) {
                        const canEndorse = String(payload.status || '').toLowerCase() === 'supervisor_endorsed';
                        submissionEndorseBtnEl.disabled = !canEndorse;
                        submissionEndorseBtnEl.classList.toggle('opacity-50', !canEndorse);
                        submissionEndorseBtnEl.classList.toggle('cursor-not-allowed', !canEndorse);
                    }

                    renderAttachments(payload.attachments || []);
                    openPreviewModal('submission-view-modal');
                }

                function setSmporTab(activeTab) {
                    smporTabButtons.forEach((button) => {
                        const isActive = button.dataset.smporTab === activeTab;
                        button.classList.toggle('border-sky-500/40', isActive);
                        button.classList.toggle('bg-sky-500/20', isActive);
                        button.classList.toggle('text-sky-200', isActive);
                        button.classList.toggle('border-slate-700', !isActive);
                        button.classList.toggle('text-slate-300', !isActive);
                        button.classList.toggle('hover:bg-slate-800', !isActive);
                        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });

                    smporTabPanels.forEach((panel) => {
                        panel.classList.toggle('hidden', panel.dataset.smporTabPanel !== activeTab);
                    });
                }

                function buildSmporTable(mode, months, sections) {
                    const monthLabels = Array.isArray(months) && months.length > 0 ? months : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                    const dataSections = Array.isArray(sections) ? sections : [];
                    const isQuantity = mode === 'quantity';
                    const hasAverage = mode !== 'quantity';
                    const valueKey = mode === 'quality' ? 'quality' : (mode === 'timeliness' ? 'timeliness' : 'quantity');
                    const totalKey = mode === 'quality' ? 'quality_total' : (mode === 'timeliness' ? 'timeliness_total' : 'quantity_total');
                    const avgKey = mode === 'quality' ? 'quality_avg' : 'timeliness_avg';
                    const colspan = monthLabels.length + (hasAverage ? 3 : 2);

                    let tableHtml = `<table class="min-w-full text-left text-sm text-slate-200"><thead class="bg-slate-950/70 text-xs uppercase text-slate-400"><tr><th class="px-4 py-3">Expected Outputs</th>${monthLabels.map((label) => `<th class="px-4 py-3 text-right">${escapeHtml(label)}</th>`).join('')}<th class="px-4 py-3 text-right">Total</th>${hasAverage ? '<th class="px-4 py-3 text-right">Average</th>' : ''}</tr></thead><tbody class="divide-y divide-slate-800">`;

                    if (dataSections.length === 0) {
                        tableHtml += `<tr class="bg-slate-900/40"><td colspan="${colspan}" class="px-4 py-3 text-center text-slate-400">No SMPOR snapshot data available.</td></tr>`;
                    } else {
                        dataSections.forEach((section) => {
                            const sectionTitle = String(section?.title || 'Section').trim() || 'Section';
                            const sectionRows = Array.isArray(section?.rows) ? section.rows : [];
                            tableHtml += `<tr class="bg-slate-950/60"><td colspan="${colspan}" class="px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] text-slate-100">${escapeHtml(sectionTitle)}</td></tr>`;

                            if (sectionRows.length === 0) {
                                tableHtml += `<tr class="bg-slate-900/40"><td colspan="${colspan}" class="px-4 py-3 text-center text-slate-400">No outputs found for this section.</td></tr>`;
                                return;
                            }

                            sectionRows.forEach((row) => {
                                const monthlyValues = row?.[valueKey] && typeof row[valueKey] === 'object' ? row[valueKey] : {};
                                const totalValue = row?.[totalKey] ?? 0;
                                const avgValue = hasAverage ? row?.[avgKey] ?? 0 : null;

                                tableHtml += '<tr class="bg-slate-900/40">';
                                tableHtml += `<td class="px-4 py-3 font-semibold">${escapeHtml(row?.expected_output || '--')}</td>`;
                                monthLabels.forEach((monthLabel) => {
                                    const cellValue = monthlyValues?.[monthLabel] ?? 0;
                                    tableHtml += `<td class="px-4 py-3 text-right">${isQuantity ? formatNumber(cellValue) : formatNumber(cellValue, 2)}</td>`;
                                });
                                tableHtml += `<td class="px-4 py-3 text-right">${isQuantity ? formatNumber(totalValue) : formatNumber(totalValue, 2)}</td>`;
                                if (hasAverage) {
                                    tableHtml += `<td class="px-4 py-3 text-right">${formatNumber(avgValue, 2)}</td>`;
                                }
                                tableHtml += '</tr>';
                            });
                        });
                    }

                    tableHtml += '</tbody></table>';
                    return tableHtml;
                }

                function renderSmporPreview(payload) {
                    if (!payload) return;

                    const months = Array.isArray(payload.smporMonths) && payload.smporMonths.length > 0
                        ? payload.smporMonths
                        : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                    const sections = Array.isArray(payload.smporSections) ? payload.smporSections : [];

                    if (smporEmployeeEl) smporEmployeeEl.textContent = payload.employee_name || '--';
                    if (smporOfficeEl) smporOfficeEl.textContent = payload.office_name || '--';
                    if (smporPeriodEl) smporPeriodEl.textContent = payload.period_label || '--';
                    if (smporSourceEl) smporSourceEl.textContent = payload.smporSourceLabel || 'Submitted MPORs (snapshot)';

                    if (smporQuantityPanelEl) smporQuantityPanelEl.innerHTML = buildSmporTable('quantity', months, sections);
                    if (smporQualityPanelEl) smporQualityPanelEl.innerHTML = buildSmporTable('quality', months, sections);
                    if (smporTimelinessPanelEl) smporTimelinessPanelEl.innerHTML = buildSmporTable('timeliness', months, sections);

                    setSmporTab('quantity');
                    openPreviewModal('dept-head-smpor-preview-modal');
                }

                function renderIpcrPreview(payload) {
                    if (!payload) return;

                    currentIpcrSections = Array.isArray(payload.ipcrSections) ? payload.ipcrSections : [];

                    if (ipcrEmployeeEl) ipcrEmployeeEl.textContent = payload.employee_name || '--';
                    if (ipcrOfficeEl) ipcrOfficeEl.textContent = payload.office_name || '--';
                    if (ipcrPeriodEl) ipcrPeriodEl.textContent = payload.period_label || '--';

                    if (!ipcrSectionsContainerEl) {
                        openPreviewModal('dept-head-ipcr-preview-modal');
                        return;
                    }

                    if (currentIpcrSections.length === 0) {
                        ipcrSectionsContainerEl.innerHTML = '<div class="rounded-xl border border-slate-800 bg-slate-950/60 px-4 py-8 text-center text-slate-400">No IPCR commitments found for this submission.</div>';
                    } else {
                        ipcrSectionsContainerEl.innerHTML = currentIpcrSections.map((section, sectionIndex) => {
                            const rows = Array.isArray(section?.rows) ? section.rows : [];
                            const weight = Number(section?.weight_percent ?? 0);
                            const weightLabel = Number.isFinite(weight) && weight > 0 ? ` (${formatNumber(weight)}%)` : '';

                            const rowsHtml = rows.length === 0
                                ? '<tr class="bg-slate-900/40"><td colspan="4" class="px-4 py-3 text-center text-slate-400">No major outputs found.</td></tr>'
                                : rows.map((row, rowIndex) => {
                                    const indicatorsCount = Number(row?.indicators_count ?? 0);

                                    return `<tr class="bg-slate-900/40 align-top"><td class="px-4 py-3 font-semibold text-slate-100">${escapeHtml(row?.major_output || '--')}</td><td class="px-4 py-3"><a href="javascript:void(0)" data-ipcr-open-indicators data-section-index="${sectionIndex}" data-row-index="${rowIndex}" aria-label="View success indicators (${indicatorsCount})" class="inline-flex items-center gap-1 text-sky-300 transition hover:text-sky-200 focus:outline-none focus:ring-2 focus:ring-sky-500/40"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg><span class="text-xs">(${indicatorsCount})</span></a></td><td class="px-4 py-3 text-slate-200">${escapeHtml(row?.target_summary || '--')}</td><td class="px-4 py-3 text-slate-300">${escapeHtml(row?.timeline || '--')}</td></tr>`;
                                }).join('');

                            return `<div class="rounded-xl border border-slate-800 bg-slate-950/60"><div class="border-b border-slate-800 px-4 py-3"><h4 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-100">${escapeHtml(String(section?.title || 'Section') + weightLabel)}</h4></div><div class="overflow-x-auto"><table class="min-w-full text-left text-sm text-slate-200"><thead class="bg-slate-950/70 text-xs uppercase text-slate-400"><tr><th class="px-4 py-3">Major Output</th><th class="px-4 py-3">Success Indicators</th><th class="px-4 py-3">Target Summary</th><th class="px-4 py-3">Timeline</th></tr></thead><tbody class="divide-y divide-slate-800">${rowsHtml}</tbody></table></div></div>`;
                        }).join('');
                    }

                    openPreviewModal('dept-head-ipcr-preview-modal');
                }

                function renderIndicatorsModal(sectionIndex, rowIndex) {
                    const section = currentIpcrSections?.[sectionIndex];
                    const row = section?.rows?.[rowIndex];
                    if (!row) return;

                    selectedIndicators = Array.isArray(row.indicators) ? row.indicators : [];
                    if (ipcrIndicatorsTitleEl) ipcrIndicatorsTitleEl.textContent = `Success Indicators - ${row.major_output || 'Major Output'}`;

                    if (ipcrIndicatorsBodyEl) {
                        ipcrIndicatorsBodyEl.innerHTML = selectedIndicators.length === 0
                            ? '<tr class="bg-slate-900/40"><td colspan="6" class="px-4 py-3 text-center text-slate-400">No success indicators available.</td></tr>'
                            : selectedIndicators.map((indicator, indicatorIndex) => {
                                const qVal = indicator?.q === null || indicator?.q === undefined
                                    ? '&mdash;'
                                    : String(Math.round(Number(indicator.q)));
                                const eVal = indicator?.e === null || indicator?.e === undefined ? '&mdash;' : formatNumber(indicator.e, 2);
                                const tVal = indicator?.t === null || indicator?.t === undefined ? '&mdash;' : formatNumber(indicator.t, 2);
                                const aVal = indicator?.a === null || indicator?.a === undefined ? '&mdash;' : formatNumber(indicator.a, 2);

                                return `<tr class="bg-slate-900/40 align-top">
                                    <td class="px-4 py-3 text-slate-100">${escapeHtml(indicator?.indicator_text || '--')}</td>
                                    <td class="px-4 py-3 text-center tabular-nums">${qVal}</td>
                                    <td class="px-4 py-3 text-center tabular-nums">${eVal}</td>
                                    <td class="px-4 py-3 text-center tabular-nums">${tVal}</td>
                                    <td class="px-4 py-3 text-center tabular-nums">${aVal}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="javascript:void(0)" data-ipcr-open-standards data-indicator-index="${indicatorIndex}" aria-label="View standards" class="inline-flex items-center text-sky-300 transition hover:text-sky-200 focus:outline-none focus:ring-2 focus:ring-sky-500/40"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg></a>
                                    </td>
                                </tr>`;
                            }).join('');
                    }

                    openPreviewModal('dept-head-ipcr-indicators-modal');
                }

                function renderStandardsModal(indicatorIndex) {
                    const indicator = selectedIndicators?.[indicatorIndex];
                    if (!indicator) return;

                    if (ipcrStandardsIndicatorTextEl) ipcrStandardsIndicatorTextEl.textContent = indicator.indicator_text || '--';

                    const payload = normalizeStandardsPayload(indicator.standards_payload);
                    const ratings = ['5', '4', '3', '2', '1'];

                    if (ipcrStandardsBodyEl) {
                        ipcrStandardsBodyEl.innerHTML = ratings.map((rating) => {
                            const ratingPayload = payload?.[rating] ?? payload?.[Number(rating)] ?? {};
                            const qValues = normalizeStandardsDimension(ratingPayload?.Q);
                            const eValues = normalizeStandardsDimension(ratingPayload?.E);
                            const tValues = normalizeStandardsDimension(ratingPayload?.T);

                            return `<tr class="bg-slate-900/40 align-top"><td class="px-4 py-3 font-semibold text-slate-100">${rating}</td><td class="px-4 py-3">${buildStandardsCell(qValues)}</td><td class="px-4 py-3">${buildStandardsCell(eValues)}</td><td class="px-4 py-3">${buildStandardsCell(tValues)}</td></tr>`;
                        }).join('');
                    }

                    openPreviewModal('dept-head-ipcr-standards-modal');
                }

                document.querySelectorAll('[data-open-submission]').forEach((trigger) => {
                    trigger.addEventListener('click', () => {
                        const submissionId = String(trigger.dataset.submissionId || '').trim();
                        if (submissionId === '') return;

                        const payload = payloadMap?.[submissionId] || null;
                        if (!payload) return;
                        renderSubmissionModal(payload);
                    });
                });

                openSmporPreviewBtn?.addEventListener('click', () => {
                    if (!currentPayload) return;
                    renderSmporPreview(currentPayload);
                });

                openIpcrPreviewBtn?.addEventListener('click', () => {
                    if (!currentPayload) return;
                    renderIpcrPreview(currentPayload);
                });

                smporTabButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const tab = button.dataset.smporTab;
                        if (!tab) return;
                        setSmporTab(tab);
                    });
                });
                setSmporTab('quantity');

                ipcrSectionsContainerEl?.addEventListener('click', (event) => {
                    const trigger = event.target.closest('[data-ipcr-open-indicators]');
                    if (!trigger) return;

                    const sectionIndex = Number.parseInt(trigger.dataset.sectionIndex || '', 10);
                    const rowIndex = Number.parseInt(trigger.dataset.rowIndex || '', 10);
                    if (Number.isNaN(sectionIndex) || Number.isNaN(rowIndex)) return;

                    renderIndicatorsModal(sectionIndex, rowIndex);
                });

                ipcrIndicatorsBodyEl?.addEventListener('click', (event) => {
                    const trigger = event.target.closest('[data-ipcr-open-standards]');
                    if (!trigger) return;

                    const indicatorIndex = Number.parseInt(trigger.dataset.indicatorIndex || '', 10);
                    if (Number.isNaN(indicatorIndex)) return;

                    renderStandardsModal(indicatorIndex);
                });

                document.querySelectorAll('[data-close-modal]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const modalEl = button.closest('[data-preview-modal]');
                        closePreviewModal(modalEl);
                    });
                });

                previewModals.forEach((previewModal) => {
                    previewModal.addEventListener('click', (event) => {
                        if (event.target === previewModal) {
                            closePreviewModal(previewModal);
                        }
                    });
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') return;

                    if (openPreviewStack.length > 0) {
                        closePreviewModal(openPreviewStack[openPreviewStack.length - 1]);
                    }
                });

                refreshPreviewModalZIndices();
                syncBodyScroll();
            });
        </script>
    @endpush
@endsection
