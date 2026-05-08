@extends('layouts.supervisor')
    @php
        $status = $status ?? 'Draft';
        $statusKey = strtolower((string) $status);
        $isDraft = $statusKey === 'draft';
        $isReturned = $statusKey === 'returned';
        $isLocked = (bool) ($locked_at ?? $lockedAt ?? false);
        $canEdit = ($isDraft || $isReturned) && !$isLocked;
        $selectedUwpId = $uwp->id ?? null;
        $selectedOfficeId = old('office_id', $selectedOfficeId ?? auth()->user()->office_id);
        $activePeriod = $periods->firstWhere('is_active', true);
        $selectedPerformancePeriodId = old('performance_period_id', $selectedPerformancePeriodId ?? optional($activePeriod)->id);
        $assignedData = collect($officeEmployees ?? [])
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'office_id' => $u->office_id,
                'unit' => auth()->user()->office->name ?? '',
            ])
            ->values()
            ->all();
    @endphp
@section('main-content')
    <section class="space-y-6">
        <div>
            <a href="{{ route('supervisor.uwp-page') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-300">
                ← Back to Unit Work Plans
            </a>
        </div>

        @if($uwp && $uwp->status === 'returned' && $uwp->return_remarks)
            <div class="mb-4 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-amber-200">
                <p class="text-sm font-semibold">
                    Returned by {{ $uwp->returned_by_role === 'pmt' ? 'PMT' : 'Department Head' }}
                </p>
                <p class="mt-1 text-xs text-amber-200/80">
                    {{ optional($uwp->returned_at)->format('M d, Y h:i A') }}
                    @if($uwp->returnedByUser) &bull; {{ $uwp->returnedByUser->name }} @endif
                </p>
                <div class="mt-2 whitespace-pre-line text-sm text-amber-100">{{ $uwp->return_remarks }}</div>
            </div>
        @endif

        <form id="uwp-form" method="POST">
            @csrf
            <input type="hidden" name="uwp_id" id="uwp_id" value="{{ old('uwp_id', $selectedUwpId) }}">
            <input type="hidden" name="mfos_payload" id="mfos_payload">
            <input type="hidden" name="assignments_payload" id="assignments_payload">
            <input type="hidden" name="functions_payload" id="functions_payload">

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-white">Planning details</p>
                    <p class="text-xs text-slate-400">Define commitments for the period. Editing is allowed only while in Draft/Returned.</p>
                    @if ($canEdit)
                        @if ($isReturned)
                            <p class="text-xs text-amber-300/90">Returned: revise required before re-submission.</p>
                        @else
                            <p class="text-xs text-emerald-300/90">Draft mode: you can add/remove MFOs.</p>
                        @endif
                    @else
                        <span class="inline-flex items-center rounded-full border border-amber-500/30 bg-amber-500/10 px-2.5 py-1 text-[11px] font-semibold text-amber-200">Locked: read-only after submission.</span>
                    @endif
                </div>
                <div class="flex flex-wrap items-start justify-end gap-3 text-[11px] text-slate-400">
                    <div class="flex flex-col items-end gap-1">
                        <span class="inline-flex items-center gap-1 rounded-full border border-blue-500/30 bg-blue-500/10 px-2.5 py-1 font-semibold text-blue-200">
                            Status: {{ $status }}
                        </span>
                        <span class="text-[10px] text-slate-500">Draft/Returned: editable · Submitted: read-only</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <span class="text-xs uppercase tracking-wide text-slate-400">Office / Unit</span>

                    @if(auth()->user()->role === 'supervisor')
                        <!-- Supervisors: Show their assigned office as plain text -->
                        <div class="w-full rounded-lg border border-slate-800 bg-slate-950/50 px-3 py-2 text-sm text-slate-100">
                            {{ auth()->user()->office->name ?? 'No office assigned' }}
                        </div>
                        <!-- Hidden field to submit the office_id -->
                        <input type="hidden" name="office_id" value="{{ $selectedOfficeId }}">
                    @else
                        <!-- Admins/Dept heads: Still show dropdown -->
                        <select
                            id="uwp-office-unit"
                            name="office_id"
                            class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2
                                text-sm text-slate-100 focus:border-blue-500
                                focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                            style="background:#0f172a;color:#e5e7eb;"
                        >
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}"
                                    {{ (int) old('office_id', $selectedOfficeId) === (int) $office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <label class="space-y-1 text-sm text-slate-300">
                    <span class="text-xs uppercase tracking-wide text-slate-400">Performance Period</span>

                    <select
                        name="performance_period_id"
                        class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2
                            text-sm text-slate-100 focus:border-blue-500
                            focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                            style="background:#0f172a;color:#e5e7eb;"
                    >
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}"
                                {{ (int) $selectedPerformancePeriodId === (int) $period->id ? 'selected' : '' }}>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>

                </label>
            </div>
            <div id="uwp-functions-wrapper" class="space-y-6"></div>
            @if ($canEdit)
                <div class="mt-8 flex justify-center">
                    <button type="button"
                            id="uwp-add-function"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-600/70 bg-gradient-to-b from-cyan-500/15 to-slate-800/80 px-5 py-3 text-sm font-semibold text-slate-100 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-400/60 hover:from-cyan-400/20 hover:to-slate-700/80 hover:shadow focus:outline-none focus:ring-2 focus:ring-cyan-500/60 md:w-auto">
                        <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 4v12m6-6H4" />
                        </svg>
                        <span>Add Function</span>
                    </button>
                </div>
            @endif

            <div class="sticky bottom-0 z-30 -mx-5 mt-6 border-t border-slate-800 bg-slate-950/95 px-5 py-4 backdrop-blur">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="space-y-1">
                        <p class="text-xs text-slate-400">Once submitted, this plan becomes read-only until reviewed.</p>
                        <span class="text-[11px] text-slate-500">UWP remains editable only while in Draft/Returned.</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button"
                                data-employee-action
                                data-save-draft-btn
                                data-action-title="Save UWP Draft"
                                data-action-message="This will save the Unit Work Plan as a draft. You may continue editing until it is submitted for approval."
                                data-action-confirm="Save draft"
                                data-action-loading="Saving..."
                                class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800/80 {{ $canEdit ? '' : 'opacity-60 pointer-events-none' }}"
                                {{ $canEdit ? '' : 'disabled' }}>
                            <span data-button-label>Save to Draft</span>
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                        </button>
                    </div>
                </div>
            </div>
            </div>
        </form>
    </section>

    {{-- SUCCESS INDICATORS MODAL (now includes Assigned Employees per indicator) --}}
    <div id="uwp-indicators-modal" class="fixed inset-0 z-[80] hidden items-start justify-center overflow-y-auto bg-black/70 px-4 py-4 backdrop-blur-sm sm:py-8">
        <div class="w-full max-w-[1200px]">
            <div class="flex h-[780px] max-h-[calc(100vh-2rem)] flex-col overflow-hidden rounded-[24px] border border-slate-800 bg-slate-950 text-slate-100 shadow-2xl sm:max-h-[calc(100vh-4rem)]">
                <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-6 py-5">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-lg font-semibold text-white">Success Indicator Workspace</h2>
                            <span class="rounded-full border border-cyan-500/20 bg-cyan-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-200">Stage I - Editor</span>
                        </div>
                        <h3 id="uwp-indicators-title" class="mt-3 text-2xl font-semibold tracking-tight text-white">--</h3>
                        <div class="hidden mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-400">
                            <span id="uwp-workspace-function-type" class="inline-flex rounded-full border border-slate-700 px-2.5 py-1 text-xs font-semibold text-slate-200">--</span>
                            <span id="uwp-workspace-function-weight" class="inline-flex rounded-full bg-slate-800 px-2.5 py-1 text-xs font-semibold text-slate-200">--</span>
                            <span class="text-slate-600">•</span>
                            <span id="uwp-workspace-target-summary">--</span>
                        </div>
                        <p class="hidden mt-2 text-sm text-slate-400">
                            {{ $canEdit
                                ? 'Manage indicators, standards, and employee assignments in one workspace.'
                                : 'Read-only indicator workspace for this output.' }}
                        </p>
                    </div>
                    <button type="button"
                            onclick="closeUwpIndicatorsModal()"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-700 bg-slate-950/60 text-slate-400 transition hover:bg-slate-900 hover:text-white">
                        <span class="sr-only">Close</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="grid min-h-0 flex-1 lg:grid-cols-[300px_minmax(0,1fr)]">
                    <aside class="flex min-h-0 flex-col border-b border-slate-800 lg:border-b-0 lg:border-r">
                        <div class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Indicators</p>
                            <span id="uwp-workspace-indicator-count-badge" class="text-sm font-semibold text-cyan-300">0</span>
                        </div>
                        <div id="uwp-workspace-indicator-nav" class="min-h-0 space-y-2 overflow-y-auto px-2 py-2"></div>
                        @if ($canEdit)
                            <div class="border-t border-slate-800 px-3 py-3">
                                <button type="button"
                                        data-indicator-add-secondary
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-800/90 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                                    <span class="fa-solid fa-plus text-[11px]"></span>
                                    <span>Add Indicator</span>
                                </button>
                            </div>
                        @endif
                    </aside>

                    <section class="flex min-h-0 flex-col">
                        <div class="hidden border-b border-slate-800 px-6 py-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 id="uwp-workspace-selected-indicator-title" class="text-lg font-semibold leading-tight text-white">No success indicator selected</h3>
                                <span id="uwp-workspace-selected-indicator-target" class="text-sm font-semibold text-slate-300"></span>
                            </div>
                        </div>

                        <div class="border-b border-slate-800 px-5">
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" data-editor-workspace-tab="overview" class="border-b-2 border-cyan-400 px-2.5 py-2.5 text-sm font-semibold text-white">Overview</button>
                                <button type="button" data-editor-workspace-tab="targets" class="border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Targets</button>
                            </div>
                        </div>

                        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-4">
                            <div data-editor-workspace-panel="overview" class="space-y-5">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Output Target Summary</p>
                                    <p id="uwp-workspace-overview-summary" class="mt-2 text-lg leading-snug text-white">-</p>
                                </div>
                                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                    <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Function Type</p>
                                        <p id="uwp-workspace-overview-function" class="mt-2 text-lg font-semibold text-white">-</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Weight</p>
                                        <p id="uwp-workspace-overview-weight" class="mt-2 text-lg font-semibold text-white">-</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Indicators</p>
                                        <p id="uwp-workspace-overview-indicator-count" class="mt-2 text-lg font-semibold text-white">0</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Selected Q/E/T Cells</p>
                                        <p id="uwp-workspace-overview-standards-count" class="mt-2 text-lg font-semibold text-white">0</p>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Selected Assigned Employees</p>
                                    <p id="uwp-workspace-overview-assignee-count" class="mt-2 text-lg font-semibold text-white">0</p>
                                </div>
                                <div>
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Linked Success Indicators</p>
                                            <p class="mt-1 text-sm text-slate-400">Pick an indicator here to jump directly to its editor tabs.</p>
                                        </div>
                                        @if ($canEdit)
                                            <button type="button" id="uwp-add-indicator"
                                                    class="inline-flex items-center gap-2 rounded-full bg-slate-800/90 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                                                <span class="fa-solid fa-plus text-[11px]"></span>
                                                <span>Add Indicator</span>
                                            </button>
                                        @endif
                                    </div>
                                    <div id="uwp-workspace-overview-indicators" class="mt-3 space-y-2.5"></div>
                                </div>
                            </div>

                            <div data-editor-workspace-panel="targets" class="hidden space-y-5">
                                <div class="grid gap-4 xl:grid-cols-[180px_minmax(0,1fr)]">
                                    <label class="space-y-2 text-sm text-slate-300">
                                        <span class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Quantity</span>
                                        <input id="uwp-targets-quantity"
                                               type="number"
                                               min="0"
                                               placeholder="Enter quantity"
                                               style="background:#0f172a;color:#e5e7eb;"
                                               class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                                    </label>
                                    <label class="space-y-2 text-sm text-slate-300">
                                        <span class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Target / Timeline</span>
                                        <input id="uwp-targets-timeline"
                                               type="text"
                                               placeholder="Describe the target or timeline"
                                               style="background:#0f172a;color:#e5e7eb;"
                                               class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                                    </label>
                                </div>
                                @unless ($canEdit)
                                    <p class="text-[11px] text-slate-500">Targets are read-only in this stage.</p>
                                @endunless
                            </div>

                            <div data-editor-workspace-panel="standards" class="hidden space-y-4">
                                <div class="hidden">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Selected Indicator</p>
                                    <p id="uwp-standards-indicator" class="mt-1.5 text-base font-semibold text-white">-</p>
                                </div>
                                <div id="uwp-standards-list" class="overflow-hidden rounded-xl border border-slate-800"></div>

                                @if ($canEdit)
                                    <div class="space-y-3 rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                                        <p class="text-xs text-slate-400">Add a standard to a specific Rating x Dimension cell.</p>
                                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-[220px_220px_minmax(0,1fr)]">
                                            <select id="uwp-standard-rating"
                                                    style="background:#0f172a;color:#e5e7eb;"
                                                    class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                                                <option value="5" selected>Rating: 5</option>
                                                <option value="4">Rating: 4</option>
                                                <option value="3">Rating: 3</option>
                                                <option value="2">Rating: 2</option>
                                                <option value="1">Rating: 1</option>
                                            </select>

                                            <select id="uwp-standard-dimension"
                                                    style="background:#0f172a;color:#e5e7eb;"
                                                    class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                                                <option value="q" selected>Dimension: Q (Quality)</option>
                                                <option value="e">Dimension: E (Efficiency)</option>
                                                <option value="t">Dimension: T (Timeliness)</option>
                                            </select>

                                            <textarea id="uwp-standards-input"
                                                      style="background:#0f172a;color:#e5e7eb;"
                                                      rows="2"
                                                      placeholder="Enter standard text"
                                                      class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"></textarea>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <button type="button"
                                                    id="uwp-add-standard"
                                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                                                Save to Table
                                            </button>
                                            <button type="button"
                                                    id="uwp-reset-standard"
                                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                                Reset to Seeded Dummy
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-[11px] text-slate-500">Standards are read-only in this stage.</p>
                                @endif
                            </div>

                            <div data-editor-workspace-panel="assignees" class="hidden space-y-4">
                                <div class="hidden">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Selected Indicator</p>
                                    <p id="uwp-assigned-indicator" class="mt-1.5 text-base font-semibold text-white">-</p>
                                    <p class="mt-1 text-[11px] text-slate-400">Office / Unit: <span id="uwp-assigned-unit">--</span></p>
                                </div>

                                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-950/60">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-slate-900/70 text-slate-200">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-[11px] uppercase tracking-[0.2em] text-slate-400">Employee Name</th>
                                                <th class="px-4 py-3 text-left text-[11px] uppercase tracking-[0.2em] text-slate-400">Office / Unit</th>
                                                <th class="px-4 py-3 text-left text-[11px] uppercase tracking-[0.2em] text-slate-400">Status</th>
                                                <th class="px-4 py-3 text-left text-[11px] uppercase tracking-[0.2em] text-slate-400">Success Indicator</th>
                                                @if ($canEdit)
                                                    <th class="px-4 py-3 text-center text-[11px] uppercase tracking-[0.2em] text-slate-400">Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody id="uwp-assigned-list" class="divide-y divide-slate-800"></tbody>
                                    </table>
                                </div>

                                <p id="uwp-assigned-empty" class="text-[12px] text-slate-500 hidden">No employees available...</p>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="grid shrink-0 gap-3 border-t border-slate-800 px-6 py-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                    <div class="hidden text-xs text-slate-500">
                        {{ $canEdit ? 'One workspace for indicator editing, Q/E/T standards, and employee assignment.' : 'Read-only indicator workspace.' }}
                    </div>
                    <div class="flex flex-wrap justify-end gap-2.5">
                        <button type="button"
                                onclick="closeUwpIndicatorsModal()"
                                class="rounded-xl border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STANDARDS SUB-MODAL --}}
    <div id="uwp-standards-modal-legacy" class="fixed inset-0 z-[86] hidden items-center justify-center bg-black/60 px-4 py-8">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards</p>
                    <h3 class="text-lg font-semibold text-white">Target Difficulty / Standards</h3>
                    <p id="uwp-standards-indicator-legacy" class="text-[11px] text-slate-400 mt-1"></p>
                </div>
                <button type="button" onclick="closeStandardsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-4 text-sm text-slate-200 max-h-[70vh] overflow-y-auto">
                <div id="uwp-standards-list-legacy" class="w-full"></div>

                @if ($canEdit)
                    <div class="space-y-3 rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-xs text-slate-400">Add a standard to a specific Rating × Dimension cell.</p>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-4">
                            <select id="uwp-standard-rating-legacy"
                                    style="background:#0f172a;color:#e5e7eb;"
                                    class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                                <option value="5" selected>Rating: 5</option>
                                <option value="4">Rating: 4</option>
                                <option value="3">Rating: 3</option>
                                <option value="2">Rating: 2</option>
                                <option value="1">Rating: 1</option>
                            </select>

                            <select id="uwp-standard-dimension-legacy"
                                    style="background:#0f172a;color:#e5e7eb;"
                                    class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                                <option value="q" selected>Dimension: Q (Quality)</option>
                                <option value="e">Dimension: E (Efficiency)</option>
                                <option value="t">Dimension: T (Timeliness)</option>
                            </select>

                            <div class="sm:col-span-2">
                                <textarea id="uwp-standards-input-legacy"
                                          style="background:#0f172a;color:#e5e7eb;"
                                          rows="2"
                                          placeholder="Enter standard text"
                                          class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"></textarea>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button type="button"
                                    id="uwp-add-standard-legacy"
                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                                Save to Table
                            </button>
                            <button type="button"
                                    id="uwp-reset-standard-legacy"
                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                Reset to Seeded Dummy
                            </button>
                        </div>
                    </div>
                @else
                    <p class="text-[11px] text-slate-500">Standards are read-only in this stage.</p>
                @endif
            </div>

            <div class="mt-5 flex justify-end border-t border-slate-800 pt-3">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closeStandardsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- ASSIGN EMPLOYEE SUB-MODAL (scoped to a specific success indicator) --}}
    <div id="uwp-assigned-employees-modal-legacy" class="fixed inset-0 z-[85] hidden items-center justify-center bg-black/60 px-4 py-8">
        <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assign Employee</p>
                    <h3 class="text-lg font-semibold text-white">Employees under the selected Office/Unit</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Office / Unit: <span id="uwp-assigned-unit-legacy">--</span></p>
                    <p class="text-[11px] text-slate-400 mt-1">Success Indicator: <span id="uwp-assigned-indicator-legacy" class="font-semibold text-slate-200">--</span></p>
                </div>
                <button type="button" onclick="closeAssignedModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-3 text-sm text-slate-200 max-h-[60vh] overflow-y-auto">
                <div>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-500 text-xs">🔍</span>
                        <input type="text"
                               style="background:#0f172a;color:#e5e7eb;"
                               placeholder="Search employee name…"
                               class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 pl-8 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                    </div>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950/60 overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-900/70 text-slate-200">
                            <tr>
                                <th class="px-4 py-2 text-left">Employee Name</th>
                                <th class="px-4 py-2 text-left">Office / Unit</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Success Indicator</th>
                                @if ($canEdit)
                                    <th class="px-4 py-2 text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="uwp-assigned-list-legacy" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>

                <p id="uwp-assigned-empty-legacy" class="text-[12px] text-slate-500 hidden">No employees available...</p>
            </div>

            <div class="mt-4 flex items-center justify-end gap-3 border-t border-slate-800 pt-3">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closeAssignedModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- GENERIC ACTION MODAL (unchanged) --}}
    <div id="employee-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 id="employee-action-title" class="text-lg font-semibold text-white">Action</h2>
                    <p id="employee-action-body" class="mt-1 text-sm text-slate-400">Prototype action preview.</p>
                </div>
                <button type="button" data-employee-modal-close class="text-slate-400 hover:text-white">x</button>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-employee-modal-close class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
                <button type="button" id="employee-action-confirm" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <span data-button-label>Proceed</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // ===== Generic Action Modal (existing) =====
                const modal = document.getElementById('employee-action-modal');
                const title = document.getElementById('employee-action-title');
                const body = document.getElementById('employee-action-body');
                const confirmBtn = document.getElementById('employee-action-confirm');
                let activeTrigger = null;

                if (!modal || !title || !body || !confirmBtn) {
                    return;
                }

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

                function closeModal() {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    activeTrigger = null;
                    setButtonLoading(confirmBtn, false);
                }

                function openModal(trigger) {
                    activeTrigger = trigger;
                    title.textContent = trigger.dataset.actionTitle || 'Action';
                    body.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
                    confirmBtn.dataset.actionLoading = trigger.dataset.actionLoading || 'Working...';
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }

                window.openEmployeeActionModal = openModal;

                document.querySelectorAll('[data-employee-action]').forEach((button) => {
                    if (button.dataset.actionRequiresValidation === 'true') return;
                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        openModal(button);
                    });
                });

                confirmBtn.addEventListener('click', function () {
                    const isSaveDraft = activeTrigger && activeTrigger.hasAttribute('data-save-draft-btn');
                    if (isSaveDraft) {
                        setButtonLoading(confirmBtn, true, confirmBtn.dataset.actionLoading);
                        setButtonLoading(activeTrigger, true, activeTrigger.dataset.actionLoading || confirmBtn.dataset.actionLoading);
                        submitUwp(saveDraftUrl);
                        return;
                    }

                    setButtonLoading(confirmBtn, true, confirmBtn.dataset.actionLoading);
                    if (activeTrigger) {
                        setButtonLoading(activeTrigger, true, activeTrigger.dataset.actionLoading || confirmBtn.dataset.actionLoading);
                    }

                    setTimeout(() => {
                        setButtonLoading(confirmBtn, false);
                        if (activeTrigger) setButtonLoading(activeTrigger, false);
                        closeModal();
                    }, 1200);
                });

                modal.addEventListener('click', function (event) {
                    if (event.target === modal) closeModal();
                });

                modal.querySelectorAll('[data-employee-modal-close]').forEach((button) => {
                    button.addEventListener('click', closeModal);
                });

                // ===== UWP Modals =====
                const indicatorsModal = document.getElementById('uwp-indicators-modal');
                const indicatorsTitle = document.getElementById('uwp-indicators-title');
                const addIndicatorBtn = document.getElementById('uwp-add-indicator');
                const addIndicatorSecondaryBtn = document.querySelector('[data-indicator-add-secondary]');
                const workspaceIndicatorNav = document.getElementById('uwp-workspace-indicator-nav');
                const workspaceIndicatorCountBadge = document.getElementById('uwp-workspace-indicator-count-badge');
                const workspaceSelectedIndicatorTitle = document.getElementById('uwp-workspace-selected-indicator-title');
                const workspaceSelectedIndicatorTarget = document.getElementById('uwp-workspace-selected-indicator-target');
                const workspaceFunctionType = document.getElementById('uwp-workspace-function-type');
                const workspaceFunctionWeight = document.getElementById('uwp-workspace-function-weight');
                const workspaceTargetSummary = document.getElementById('uwp-workspace-target-summary');
                const workspaceOverviewSummary = document.getElementById('uwp-workspace-overview-summary');
                const workspaceOverviewFunction = document.getElementById('uwp-workspace-overview-function');
                const workspaceOverviewWeight = document.getElementById('uwp-workspace-overview-weight');
                const workspaceOverviewIndicatorCount = document.getElementById('uwp-workspace-overview-indicator-count');
                const workspaceOverviewStandardsCount = document.getElementById('uwp-workspace-overview-standards-count');
                const workspaceOverviewAssigneeCount = document.getElementById('uwp-workspace-overview-assignee-count');
                const workspaceOverviewIndicators = document.getElementById('uwp-workspace-overview-indicators');
                const targetsQuantityInput = document.getElementById('uwp-targets-quantity');
                const targetsTimelineInput = document.getElementById('uwp-targets-timeline');
                const workspaceTabButtons = Array.from(document.querySelectorAll('[data-editor-workspace-tab]'));
                const workspacePanels = Array.from(document.querySelectorAll('[data-editor-workspace-panel]'));
                const standardsList = document.getElementById('uwp-standards-list');
                const standardsIndicatorLabel = document.getElementById('uwp-standards-indicator');
                const standardsInput = document.getElementById('uwp-standards-input');
                const addStandardBtn = document.getElementById('uwp-add-standard');
                const ratingSelectEl = document.getElementById('uwp-standard-rating');
                const dimSelectEl = document.getElementById('uwp-standard-dimension');
                let standardsEditTarget = null; // { rating: '5', dim: 'q' }

                const assignedList = document.getElementById('uwp-assigned-list');
                const assignedEmpty = document.getElementById('uwp-assigned-empty');
                const assignedUnit = document.getElementById('uwp-assigned-unit');
                const assignedIndicator = document.getElementById('uwp-assigned-indicator');

                const unitSelect = document.getElementById('uwp-office-unit');
                const uwpForm = document.getElementById('uwp-form');
                const uwpIdInput = document.getElementById('uwp_id');
                const mfosPayloadInput = document.getElementById('mfos_payload');
                const assignmentsPayloadInput = document.getElementById('assignments_payload');
                const functionsPayloadInput = document.getElementById('functions_payload');
                const functionsWrapper = document.getElementById('uwp-functions-wrapper');
                const addFunctionBtn = document.getElementById('uwp-add-function');
                const submitUwpBtn = document.querySelector('[data-submit-uwp-btn]');

                const selectedUwpId = @json($selectedUwpId);
                const saveDraftUrl = selectedUwpId
                    ? @json(route('supervisor.uwp.saveDraftData.byId', ['id' => '__ID__'])).replace('__ID__', String(selectedUwpId))
                    : @json(route('supervisor.uwp.saveDraftData'));
                const submitUwpUrl = selectedUwpId
                    ? @json(route('supervisor.uwp.submitData.byId', ['id' => '__ID__'])).replace('__ID__', String(selectedUwpId))
                    : @json(route('supervisor.uwp.submitData'));

                let activeFunctionIndex = null;
                let activeMfoIndex = null;
                let activeIndicators = [];
                let activeIndicatorIndex = 0;
                let activeWorkspaceTab = 'overview';
                let activeEditingIndicatorIndex = null;
                let activeRowConfirmId = null;
                let activeFunctionConfirmId = null;

                const assignedData = @json($assignedData);

                const isDraft = {{ $canEdit ? 'true' : 'false' }};

                const seededFunctions = [
                        {
                            title: 'Core Functions',
                            type: 'core',
                            weight: 80,
                            isCustom: false,
                            mfos: [
                                {
                                    title: 'E-Bank Scanning and Encoding of Revenue Transactions',
                                    target: 'Daily; all e-bank transactions processed within the same working day',
                                    indicators: [
                                        { text: 'All e-bank transactions scanned and encoded daily', targetQuantity: 1200, targetTimeline: 'e-bank transactions processed within the semester', standards: [], assignees: [] },
                                        { text: 'Indexing complete with no missing pages', targetQuantity: 1200, targetTimeline: 'e-bank transactions processed within the semester', standards: [], assignees: [] },
                                        { text: 'Audit trail maintained within 24 hours', targetQuantity: 1200, targetTimeline: 'e-bank transactions processed within the semester', standards: [], assignees: [] },
                                    ],
                                },
                                {
                                    title: 'Processing of Over-the-Counter Revenue Transactions',
                                    target: 'Daily; 95% processed within the same working day',
                                    indicators: [
                                        { text: 'Same-day verification of OTC transactions', targetQuantity: 3000, targetTimeline: 'OCR processed within the semester', standards: [], assignees: [] },
                                        { text: '95% encoded within the business day', targetQuantity: 3000, targetTimeline: 'OCR processed within the semester', standards: [], assignees: [] },
                                        { text: 'OR validation completed daily', targetQuantity: 3000, targetTimeline: 'OCR processed within the semester', standards: [], assignees: [] },
                                    ],
                                },
                            ],
                        },
                        {
                            title: 'Support Functions',
                            type: 'support',
                            weight: 20,
                            isCustom: false,
                            mfos: [
                                {
                                    title: 'Maintenance of revenue records and filing system',
                                    target: 'Quarterly; records validated and properly filed',
                                    indicators: [
                                        { text: 'Weekly filing updated and retrievable', targetQuantity: 2400, targetTimeline: 'records validated and properly filed within the semester', standards: [], assignees: [] },
                                        { text: 'Digital backups synced monthly', targetQuantity: 2400, targetTimeline: 'records validated and properly filed within the semester', standards: [], assignees: [] },
                                        { text: 'Retrieval logs maintained for audits', targetQuantity: 2400, targetTimeline: 'records validated and properly filed within the semester', standards: [], assignees: [] },
                                    ],
                                },
                            ],
                        },
                    ];

                const serverFunctions = @json($initialFunctions ?? null);
                const uwpState = {
                    functions: Array.isArray(serverFunctions) && serverFunctions.length > 0
                        ? serverFunctions
                        : (selectedUwpId ? seededFunctions : []),
                };

                uwpState.functions = (uwpState.functions || []).map((func) => ({
                    ...func,
                    mfos: Array.isArray(func?.mfos)
                        ? func.mfos.map((mfo) => ({
                            ...mfo,
                            targetQuantity: normalizeTargetQuantity(mfo?.targetQuantity ?? mfo?.target_quantity),
                            indicators: Array.isArray(mfo?.indicators)
                                ? mfo.indicators.map((indicator) => ({
                                    ...indicator,
                                    targetQuantity: normalizeTargetQuantity(indicator?.targetQuantity ?? indicator?.target_quantity),
                                    targetTimeline: String(indicator?.targetTimeline ?? indicator?.target_timeline ?? '').trim(),
                                }))
                                : [],
                        }))
                        : [],
                }));

                const standardsSeedMap = {
                    'All e-bank transactions scanned and encoded daily': {
                        5: { q: ['No errors; accurate encoding'], e: ['100% processed'], t: ['Same working day'] },
                        4: { q: ['Minor errors'], e: ['100% processed'], t: ['Same working day'] },
                        3: { q: ['Few minor errors'], e: ['95–99% processed'], t: ['End of working day'] },
                        2: { q: ['Multiple errors'], e: ['<95% processed'], t: ['Beyond working day'] },
                        1: { q: ['Major errors/missing'], e: ['Majority unprocessed'], t: ['Not within acceptable time'] },
                    },
                    'Indexing complete with no missing pages': {
                        5: { q: ['Indexing fully verified, zero gaps'], e: ['100% pages indexed'], t: ['Same day'] },
                        4: { q: ['Indexing minor rechecks'], e: ['100% pages indexed'], t: ['Same day'] },
                        3: { q: ['Occasional missing indexes fixed'], e: ['95–99% indexed'], t: ['Within 24 hours'] },
                        2: { q: ['Frequent missing pages'], e: ['<95% indexed'], t: ['Beyond 24 hours'] },
                        1: { q: ['Indexing largely incomplete'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Audit trail maintained within 24 hours': {
                        5: { q: ['Complete trail, no errors'], e: ['100% entries captured'], t: ['Within 24 hours'] },
                        4: { q: ['Minor corrections only'], e: ['100% entries captured'], t: ['Within 24 hours'] },
                        3: { q: ['Some gaps corrected'], e: ['95–99% entries captured'], t: ['Within 48 hours'] },
                        2: { q: ['Multiple missing logs'], e: ['<95% captured'], t: ['Beyond 48 hours'] },
                        1: { q: ['Trail missing'], e: ['Majority uncaptured'], t: ['Unacceptable'] },
                    },
                    'Same-day verification of OTC transactions': {
                        5: { q: ['Verified without discrepancies'], e: ['100% OTC verified'], t: ['Same working day'] },
                        4: { q: ['Minor verifications pending'], e: ['100% OTC verified'], t: ['Same working day'] },
                        3: { q: ['Few pending verifications'], e: ['95–99% verified'], t: ['End of working day'] },
                        2: { q: ['Several unverified'], e: ['<95% verified'], t: ['Beyond working day'] },
                        1: { q: ['Verification not done'], e: ['Majority unverified'], t: ['Unacceptable'] },
                    },
                    '95% encoded within the business day': {
                        5: { q: ['Encodings error-free'], e: ['100% encoded'], t: ['Same business day'] },
                        4: { q: ['Minor corrections'], e: ['100% encoded'], t: ['Same business day'] },
                        3: { q: ['Few delays'], e: ['95–99% encoded'], t: ['By end of day'] },
                        2: { q: ['Multiple delays'], e: ['<95% encoded'], t: ['Next day'] },
                        1: { q: ['Encoding largely incomplete'], e: ['Major backlog'], t: ['Unacceptable'] },
                    },
                    'OR validation completed daily': {
                        5: { q: ['All ORs validated error-free'], e: ['100% validated'], t: ['Daily'] },
                        4: { q: ['Minor issues corrected same day'], e: ['100% validated'], t: ['Daily'] },
                        3: { q: ['Some validations late'], e: ['95–99% validated'], t: ['Within 48 hours'] },
                        2: { q: ['Frequent late validations'], e: ['<95% validated'], t: ['Beyond 48 hours'] },
                        1: { q: ['Validations mostly missing'], e: ['Majority unvalidated'], t: ['Unacceptable'] },
                    },
                    'Weekly filing updated and retrievable': {
                        5: { q: ['Zero retrieval issues'], e: ['100% weekly updates'], t: ['Within week'] },
                        4: { q: ['Minor retrieval fixes'], e: ['100% weekly updates'], t: ['Within week'] },
                        3: { q: ['Some items late'], e: ['95–99% updates'], t: ['Within next week'] },
                        2: { q: ['Many late updates'], e: ['<95% updates'], t: ['Beyond next week'] },
                        1: { q: ['Updates not done'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Digital backups synced monthly': {
                        5: { q: ['Backups verified'], e: ['100% synced'], t: ['Within month'] },
                        4: { q: ['Minor sync corrections'], e: ['100% synced'], t: ['Within month'] },
                        3: { q: ['Some delays'], e: ['95–99% synced'], t: ['Within following week'] },
                        2: { q: ['Frequent delays'], e: ['<95% synced'], t: ['Beyond following week'] },
                        1: { q: ['Backups largely missing'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Retrieval logs maintained for audits': {
                        5: { q: ['Logs complete and audit-ready'], e: ['100% requests logged'], t: ['Same day'] },
                        4: { q: ['Minor log gaps corrected'], e: ['100% requests logged'], t: ['Same day'] },
                        3: { q: ['Some gaps'], e: ['95–99% logged'], t: ['Within 48 hours'] },
                        2: { q: ['Many gaps'], e: ['<95% logged'], t: ['Beyond 48 hours'] },
                        1: { q: ['Logs largely missing'], e: ['Majority unlogged'], t: ['Unacceptable'] },
                    },
                    'All daily collections posted to the ledger within the day': {
                        5: { q: ['Zero posting errors; entries accurate'], e: ['100% posted'], t: ['Same working day'] },
                        4: { q: ['Minor corrections only'], e: ['100% posted'], t: ['Same working day'] },
                        3: { q: ['Few correctable errors'], e: ['95–99% posted'], t: ['By end of day'] },
                        2: { q: ['Multiple errors requiring rework'], e: ['<95% posted'], t: ['Next day'] },
                        1: { q: ['Major inaccuracies'], e: ['Major backlog'], t: ['Unacceptable delay'] },
                    },
                    'Daily totals reconciled against validated ORs': {
                        5: { q: ['Reconciled with zero variance'], e: ['All ORs included'], t: ['Same day'] },
                        4: { q: ['Minor variance resolved'], e: ['All ORs included'], t: ['Same day'] },
                        3: { q: ['Some variances corrected'], e: ['95–99% ORs included'], t: ['Within 24 hours'] },
                        2: { q: ['Frequent variances'], e: ['<95% ORs included'], t: ['Beyond 24 hours'] },
                        1: { q: ['Not reconciled'], e: ['Majority missing'], t: ['Unacceptable'] },
                    },
                    'Posting errors corrected within 24 hours': {
                        5: { q: ['All corrections documented'], e: ['100% corrected'], t: ['Within 24 hours'] },
                        4: { q: ['Minor corrections documented'], e: ['100% corrected'], t: ['Within 24 hours'] },
                        3: { q: ['Some corrections delayed'], e: ['95–99% corrected'], t: ['Within 48 hours'] },
                        2: { q: ['Many corrections delayed'], e: ['<95% corrected'], t: ['Beyond 48 hours'] },
                        1: { q: ['Corrections not done'], e: ['Majority pending'], t: ['Unacceptable'] },
                    },
                    'Monthly revenue report prepared with complete schedules': {
                        5: { q: ['Complete schedules, no gaps'], e: ['All sections included'], t: ['Within 3 working days'] },
                        4: { q: ['Minor schedule tweaks'], e: ['All sections included'], t: ['Within 3 working days'] },
                        3: { q: ['Some missing items fixed'], e: ['95–99% complete'], t: ['Within 5 working days'] },
                        2: { q: ['Many missing schedules'], e: ['<95% complete'], t: ['Beyond 5 working days'] },
                        1: { q: ['Report incomplete'], e: ['Majority missing'], t: ['Unacceptable'] },
                    },
                    'Report figures match the ledger and subsidiary records': {
                        5: { q: ['Exact match, no variance'], e: ['All reconciled'], t: ['Before submission'] },
                        4: { q: ['Minor variance resolved'], e: ['All reconciled'], t: ['Before submission'] },
                        3: { q: ['Few variances corrected'], e: ['95–99% reconciled'], t: ['At submission'] },
                        2: { q: ['Frequent variances'], e: ['<95% reconciled'], t: ['After submission'] },
                        1: { q: ['Not reconciled'], e: ['Majority not reconciled'], t: ['Unacceptable'] },
                    },
                    'Report submitted on or before deadline': {
                        5: { q: ['Submission complete'], e: ['All attachments included'], t: ['On/before deadline'] },
                        4: { q: ['Minor attachment fixes'], e: ['All included'], t: ['On/before deadline'] },
                        3: { q: ['Late minor attachment'], e: ['95–99% included'], t: ['1 day late'] },
                        2: { q: ['Several missing attachments'], e: ['<95% included'], t: ['2–3 days late'] },
                        1: { q: ['Not submitted/very late'], e: ['Majority missing'], t: ['Unacceptable'] },
                    },
                    'Audit request documents compiled complete and accurate': {
                        5: { q: ['Complete packet, error-free'], e: ['All requested docs included'], t: ['Within 2 working days'] },
                        4: { q: ['Minor formatting fixes'], e: ['All included'], t: ['Within 2 working days'] },
                        3: { q: ['Some missing docs recovered'], e: ['95–99% included'], t: ['Within 3 working days'] },
                        2: { q: ['Many missing docs'], e: ['<95% included'], t: ['Beyond 3 working days'] },
                        1: { q: ['Packet incomplete'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Verification responses issued within 2 working days': {
                        5: { q: ['Clear, accurate response'], e: ['All queries answered'], t: ['Within 2 working days'] },
                        4: { q: ['Minor clarifications'], e: ['All answered'], t: ['Within 2 working days'] },
                        3: { q: ['Some clarifications needed'], e: ['95–99% answered'], t: ['Within 3 working days'] },
                        2: { q: ['Many clarifications needed'], e: ['<95% answered'], t: ['Beyond 3 working days'] },
                        1: { q: ['Responses inadequate'], e: ['Majority unanswered'], t: ['Unacceptable'] },
                    },
                    'Follow-up clarifications resolved within 3 working days': {
                        5: { q: ['Resolved fully with evidence'], e: ['All follow-ups closed'], t: ['Within 3 working days'] },
                        4: { q: ['Minor evidence follow-up'], e: ['All closed'], t: ['Within 3 working days'] },
                        3: { q: ['Some follow-ups delayed'], e: ['95–99% closed'], t: ['Within 5 working days'] },
                        2: { q: ['Many follow-ups delayed'], e: ['<95% closed'], t: ['Beyond 5 working days'] },
                        1: { q: ['Follow-ups not closed'], e: ['Majority open'], t: ['Unacceptable'] },
                    },
                };

                function createEmptyStandards() {
                    return {
                        5: { q: '', e: '', t: '' },
                        4: { q: '', e: '', t: '' },
                        3: { q: '', e: '', t: '' },
                        2: { q: '', e: '', t: '' },
                        1: { q: '', e: '', t: '' },
                    };
                }

                function seedStandardsForIndicator(text) {
                    const seed = standardsSeedMap[text];
                    if (!seed) return createEmptyStandards();
                    const base = createEmptyStandards();
                    [5,4,3,2,1].forEach((lvl) => {
                        if (!seed[lvl]) return;

                        base[lvl] = {
                            q: Array.isArray(seed[lvl].q) ? (seed[lvl].q[0] || '') : (seed[lvl].q || ''),
                            e: Array.isArray(seed[lvl].e) ? (seed[lvl].e[0] || '') : (seed[lvl].e || ''),
                            t: Array.isArray(seed[lvl].t) ? (seed[lvl].t[0] || '') : (seed[lvl].t || ''),
                        };
                    });
                    return base;
                }

                function escapeHtml(value) {
                    const str = String(value ?? '');
                    return str
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/\"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function clampNumber(value, min, max) {
                    const num = Number(value);
                    if (Number.isNaN(num)) return min;
                    return Math.min(max, Math.max(min, num));
                }

                function normalizeTargetQuantity(value) {
                    if (value === null || value === undefined || value === '') return null;

                    const num = Number(value);
                    if (!Number.isFinite(num)) return null;

                    return Math.max(0, Math.trunc(num));
                }

                const supervisorOfficeName = @json(auth()->user()->office->name ?? '');

                function getSelectedUnitLabel() {
                    if (!unitSelect) return supervisorOfficeName || 'Office / Unit';
                    const option = unitSelect.options[unitSelect.selectedIndex];
                    return option ? option.text : (supervisorOfficeName || 'Office / Unit');
                }

                function getSelectedOfficeId() {
                    const hidden = document.querySelector('input[name="office_id"]');
                    if (hidden && hidden.value) return Number(hidden.value);

                    if (unitSelect && unitSelect.value) return Number(unitSelect.value);

                    return 0;
                }

                function getFunctionDescription(func) {
                    if (func.type === 'core') {
                        return 'Each row is a measurable, loggable core output. No scoring here; capture targets only.';
                    }
                    if (func.type === 'support') {
                        return 'Log support outputs that enable the unit. Keep them measurable and planned.';
                    }
                    return 'Define custom outputs for this function. Keep them measurable and planned.';
                }

                function standardsArrayToMatrix(standards) {
                    const matrix = createEmptyStandards();
                    (standards || []).forEach((item) => {
                        const rating = Number(item.rating);
                        if (!matrix[rating]) return;
                        const dimKey = item.dimension === 'quality' ? 'q' : item.dimension === 'efficiency' ? 'e' : item.dimension === 'timeliness' ? 't' : item.dimension;
                        if (!Object.prototype.hasOwnProperty.call(matrix[rating], dimKey)) return;
                        matrix[rating][dimKey] = item.text ?? item.standard_text ?? '';
                    });
                    return matrix;
                }

                function standardsMatrixToArray(matrix) {
                    const list = [];
                    [5,4,3,2,1].forEach((rating) => {
                        const row = matrix[rating] || {};
                        ['q','e','t'].forEach((dimKey) => {
                            const text = (row[dimKey] || '').trim();
                            if (!text) return;
                            const dimension = dimKey === 'q' ? 'quality' : dimKey === 'e' ? 'efficiency' : 'timeliness';
                            list.push({ rating, dimension, text });
                        });
                    });
                    return list;
                }

                function ensureIndicatorMatrix(indicator) {
                    if (indicator._matrix) return indicator._matrix;
                    const hasStandards = Array.isArray(indicator.standards) && indicator.standards.length > 0;
                    const matrix = hasStandards
                        ? standardsArrayToMatrix(indicator.standards)
                        : createEmptyStandards();
                    indicator._matrix = matrix;
                    indicator.standards = standardsMatrixToArray(matrix);
                    return matrix;
                }

                function getIndicatorStandardsArray(indicator) {
                    if (!indicator) return [];
                    if (!Array.isArray(indicator.standards) || indicator.standards.length === 0) {
                        ensureIndicatorMatrix(indicator);
                    }
                    return Array.isArray(indicator.standards) ? indicator.standards : [];
                }

                function getIndicatorTargetSummary(indicator) {
                    const quantity = normalizeTargetQuantity(indicator?.targetQuantity ?? indicator?.target_quantity);
                    const timeline = String(indicator?.targetTimeline ?? indicator?.target_timeline ?? '').trim();
                    const parts = [];

                    if (quantity !== null && quantity !== undefined && quantity !== '') {
                        parts.push(String(quantity));
                    }

                    if (timeline) {
                        parts.push(timeline);
                    }

                    return parts.join(' ').trim();
                }

                function deriveMfoTargetMeta(mfo) {
                    const indicators = Array.isArray(mfo?.indicators) ? mfo.indicators : [];
                    const summaries = indicators
                        .map((indicator) => getIndicatorTargetSummary(indicator))
                        .filter((value, index, array) => value && array.indexOf(value) === index);
                    const totalQuantity = indicators.reduce((sum, indicator) => {
                        const quantity = normalizeTargetQuantity(indicator?.targetQuantity ?? indicator?.target_quantity);
                        return quantity === null ? sum : sum + quantity;
                    }, 0);

                    if (summaries.length === 1) {
                        return {
                            summary: summaries[0],
                            targetQuantity: totalQuantity > 0 ? totalQuantity : normalizeTargetQuantity(mfo?.targetQuantity ?? mfo?.target_quantity),
                        };
                    }

                    if (summaries.length > 1) {
                        return {
                            summary: 'Multiple indicator targets',
                            targetQuantity: totalQuantity > 0 ? totalQuantity : null,
                        };
                    }

                    const fallbackQuantity = normalizeTargetQuantity(mfo?.targetQuantity ?? mfo?.target_quantity);
                    const fallbackTimeline = String(mfo?.target ?? mfo?.target_timeline ?? '').trim();
                    const fallbackParts = [];

                    if (fallbackQuantity !== null && fallbackQuantity !== undefined && fallbackQuantity !== '') {
                        fallbackParts.push(String(fallbackQuantity));
                    }

                    if (fallbackTimeline) {
                        fallbackParts.push(fallbackTimeline);
                    }

                    return {
                        summary: fallbackParts.join(' ').trim(),
                        targetQuantity: fallbackQuantity,
                    };
                }

                function createIndicator(text) {
                    return {
                        text: text || 'New success indicator',
                        targetQuantity: null,
                        targetTimeline: '',
                        standards: [],
                        assignees: [],
                    };
                }

                function finalizeIndicatorValues(indicator) {
                    if (!indicator) return;

                    indicator.text = String(indicator.text || '').trim() || 'New success indicator';
                    indicator.targetTimeline = String(indicator.targetTimeline || '').trim();
                    indicator.targetQuantity = normalizeTargetQuantity(indicator.targetQuantity);

                    if (!indicator._matrix) {
                        indicator._matrix = createEmptyStandards();
                        indicator.standards = standardsMatrixToArray(indicator._matrix);
                    }
                }

                function createMfo(title, target, targetQuantity, indicators) {
                    return {
                        title: title || '',
                        target: target || '',
                        targetQuantity: normalizeTargetQuantity(targetQuantity),
                        indicators: Array.isArray(indicators) ? indicators : [],
                    };
                }

                function createFunctionContainer() {
                    return {
                        title: '',
                        titlePlaceholder: 'Enter Function Title (e.g., Special Projects / Administrative Tasks)',
                        type: 'custom',
                        weight: 0,
                        isCustom: true,
                        mfos: [],
                    };
                }

                function normalizeFunctionType(type) {
                    const value = String(type || '').toLowerCase();
                    return ['core', 'support', 'custom'].includes(value) ? value : 'custom';
                }

                function isFunctionTypeTaken(type, exceptIndex = -1) {
                    const normalized = normalizeFunctionType(type);
                    if (!['core', 'support'].includes(normalized)) return false;

                    return uwpState.functions.some((func, idx) => {
                        if (idx === exceptIndex) return false;
                        return normalizeFunctionType(func?.type) === normalized;
                    });
                }

                function resolveFunctionTypeSelection(type, currentIndex) {
                    const normalized = normalizeFunctionType(type);
                    if (!['core', 'support'].includes(normalized)) {
                        return 'custom';
                    }

                    if (isFunctionTypeTaken(normalized, currentIndex)) {
                        return 'custom';
                    }

                    return normalized;
                }

                function getAssignedEmployees(indicator) {
                    if (!indicator) return [];
                    return Array.isArray(indicator.assignees) ? [...indicator.assignees] : [];
                }

                function getActiveFunction() {
                    return activeFunctionIndex === null ? null : uwpState.functions[activeFunctionIndex] || null;
                }

                function getActiveMfo() {
                    const func = getActiveFunction();
                    return func?.mfos?.[activeMfoIndex] || null;
                }

                function getSelectedIndicator() {
                    if (!Array.isArray(activeIndicators) || !activeIndicators.length) {
                        activeIndicatorIndex = 0;
                        return null;
                    }

                    activeIndicatorIndex = Math.min(Math.max(activeIndicatorIndex, 0), activeIndicators.length - 1);
                    return activeIndicators[activeIndicatorIndex] || null;
                }

                function setSelectedIndicatorIndex(index) {
                    if (!Array.isArray(activeIndicators) || !activeIndicators.length) {
                        activeIndicatorIndex = 0;
                        return;
                    }

                    activeIndicatorIndex = Math.min(Math.max(Number(index) || 0, 0), activeIndicators.length - 1);
                }

                function setEditorWorkspaceTab(tabName) {
                    activeWorkspaceTab = ['overview', 'targets', 'standards', 'assignees'].includes(tabName) ? tabName : 'overview';

                    workspaceTabButtons.forEach((button) => {
                        const active = button.getAttribute('data-editor-workspace-tab') === activeWorkspaceTab;
                        button.classList.toggle('border-cyan-400', active);
                        button.classList.toggle('text-white', active);
                        button.classList.toggle('font-semibold', active);
                        button.classList.toggle('border-transparent', !active);
                        button.classList.toggle('text-slate-400', !active);
                        button.classList.toggle('font-medium', !active);
                    });

                    workspacePanels.forEach((panel) => {
                        panel.classList.toggle('hidden', panel.getAttribute('data-editor-workspace-panel') !== activeWorkspaceTab);
                    });
                }

                function isEmployeeAssigned(indicator, employeeId) {
                    return getAssignedEmployees(indicator).includes(Number(employeeId));
                }

                function assignEmployee(indicator, employeeId) {
                    employeeId = Number(employeeId);
                    if (!employeeId) return;
                    indicator.assignees = Array.isArray(indicator.assignees) ? indicator.assignees : [];
                    if (!indicator.assignees.includes(employeeId)) indicator.assignees.push(employeeId);
                }

                function unassignEmployee(indicator, employeeId) {
                    employeeId = Number(employeeId);
                    if (!employeeId) return;
                    indicator.assignees = Array.isArray(indicator.assignees) ? indicator.assignees : [];
                    const idx = indicator.assignees.indexOf(employeeId);
                    if (idx !== -1) indicator.assignees.splice(idx, 1);
                }

                function renderFunctions() {
                    if (!functionsWrapper) return;

                    const html = uwpState.functions.map((func, funcIndex) => {
                        const functionType = normalizeFunctionType(func.type);
                        func.type = functionType;
                        const weightValue = Number(func.weight || 0);
                        const description = getFunctionDescription(func);
                        const inputDisabled = isDraft ? '' : 'disabled';
                        const mutedClass = isDraft ? '' : 'opacity-60 pointer-events-none';
                        const canDeleteFunction = isDraft && functionType === 'custom';
                        const coreTakenByOther = isFunctionTypeTaken('core', funcIndex);
                        const supportTakenByOther = isFunctionTypeTaken('support', funcIndex);
                        const isFunctionConfirmOpen = activeFunctionConfirmId === funcIndex;

                        const mfoRows = (func.mfos || []).map((mfo, mfoIndex) => {
                            const indicatorCount = Array.isArray(mfo.indicators) ? mfo.indicators.length : 0;
                            const rowId = `${funcIndex}-${mfoIndex}`;
                            const isConfirmOpen = activeRowConfirmId === rowId;
                            return `
                                <tr class="group hover:bg-slate-800/40 transition-colors" data-mfo-row-id="${rowId}">
                                    <td class="px-4 py-4">
                                        <input type="text"
                                            data-mfo-title
                                            data-function-index="${funcIndex}"
                                            data-mfo-index="${mfoIndex}"
                                            value="${escapeHtml(mfo.title)}"
                                            class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none ${mutedClass}"
                                            style="background:#0f172a;color:#e5e7eb;"
                                            placeholder="e.g., Records management and archiving"
                                            ${inputDisabled}>
                                    </td>

                                    <td class="px-4 py-4 text-center">
                                        <button
                                            type="button"
                                            data-action="view-indicators"
                                            data-function-index="${funcIndex}"
                                            data-mfo-index="${mfoIndex}"
                                            class="inline-flex items-center rounded-full border border-slate-700 bg-slate-900 px-3 py-1 text-xs font-semibold text-slate-300 transition hover:bg-slate-700/40 hover:border-slate-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/60 cursor-pointer">
                                            ${indicatorCount} indicator${indicatorCount === 1 ? '' : 's'}
                                        </button>
                                    </td>

                                    <td class="px-4 py-4 text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            ${isDraft ? `
                                                <button type="button"
                                                    data-action="trigger-remove-mfo"
                                                    data-delete-trigger="true"
                                                    data-row-id="${rowId}"
                                                    data-function-index="${funcIndex}"
                                                    data-mfo-index="${mfoIndex}"
                                                    aria-label="Remove MFO"
                                                    title="Remove MFO"
                                                    class="${isConfirmOpen ? 'hidden' : 'inline-flex'} h-8 w-8 items-center justify-center rounded-lg text-red-400 opacity-0 transition hover:bg-red-500/10 hover:text-red-300 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:opacity-100 group-hover:opacity-100">
                                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2m-7 0h8m-9 3 1 9a1 1 0 0 0 1 .9h6a1 1 0 0 0 1-.9l1-9M10 11v6M14 11v6"/>
                                                    </svg>
                                                </button>
                                                <div data-delete-confirm="${rowId}" class="${isConfirmOpen ? 'inline-flex' : 'hidden'} items-center gap-1">
                                                    <button
                                                        type="button"
                                                        data-action="cancel-remove-mfo"
                                                        data-row-id="${rowId}"
                                                        class="rounded-full border border-slate-600 px-2.5 py-1 text-[11px] font-semibold text-slate-300 transition hover:bg-slate-800">
                                                        Cancel
                                                    </button>
                                                    <button
                                                        type="button"
                                                        data-action="confirm-remove-mfo"
                                                        data-row-id="${rowId}"
                                                        data-function-index="${funcIndex}"
                                                        data-mfo-index="${mfoIndex}"
                                                        class="rounded-full border border-red-500/40 bg-red-500/10 px-2.5 py-1 text-[11px] font-semibold text-red-300 transition hover:bg-red-500/20 hover:text-red-200 focus:outline-none focus:ring-2 focus:ring-red-500/40">
                                                        Remove
                                                    </button>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }).join('');

                        const emptyRow = `
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-xs text-slate-500">
                                    No MFOs yet. Use "+ Add MFO" to add entries.
                                </td>
                            </tr>
                        `;

                        return `
                            <div data-function-card data-function-index="${funcIndex}" class="group scroll-mt-24 rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-sm space-y-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <input type="text"
                                                data-function-title
                                                data-function-index="${funcIndex}"
                                                value="${escapeHtml(func.title)}"
                                                class="rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-lg font-semibold text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none ${mutedClass}"
                                                style="background:#0f172a;color:#e5e7eb;"
                                                ${inputDisabled}>
                                            <span class="inline-flex items-center rounded-full border border-slate-700 bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-300" data-function-weight-label="${funcIndex}">(${weightValue}%)</span>
                                        </div>
                                        <p class="text-sm text-slate-400">${description}</p>
                                        ${isDraft
                                            ? '<p class="text-xs text-emerald-300/90">Draft mode: you can add/remove MFOs.</p>'
                                            : '<span class="inline-flex items-center rounded-full border border-amber-500/30 bg-amber-500/10 px-2.5 py-1 text-[11px] font-semibold text-amber-200">Locked: read-only after submission.</span>'
                                        }
                                    </div>
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <select
                                            data-function-type
                                            data-function-index="${funcIndex}"
                                            class="rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none ${mutedClass}"
                                            style="background:#0f172a;color:#e5e7eb;"
                                            ${inputDisabled}>
                                            <option value="core" ${functionType === 'core' ? 'selected' : ''} ${functionType !== 'core' && coreTakenByOther ? 'disabled' : ''}>Core</option>
                                            <option value="support" ${functionType === 'support' ? 'selected' : ''} ${functionType !== 'support' && supportTakenByOther ? 'disabled' : ''}>Support</option>
                                            <option value="custom" ${functionType === 'custom' ? 'selected' : ''}>Custom</option>
                                        </select>

                                        <input type="number" min="0" max="100"
                                            data-function-weight
                                            data-function-index="${funcIndex}"
                                            value="${weightValue}"
                                            class="w-24 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none ${mutedClass}"
                                            style="background:#0f172a;color:#e5e7eb;"
                                            ${inputDisabled}>

                                        ${isDraft ? `
                                            <button type="button"
                                                data-action="add-mfo"
                                                data-function-index="${funcIndex}"
                                                class="inline-flex items-center gap-1 rounded-lg border border-blue-500/50 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200 hover:bg-blue-500/20">
                                                <span class="fa-solid fa-plus text-[10px]"></span>
                                                <span>+ Add MFO</span>
                                            </button>
                                        ` : ''}

                                        ${canDeleteFunction ? `
                                            <button type="button"
                                                data-action="trigger-remove-function"
                                                data-function-index="${funcIndex}"
                                                aria-label="Remove Function"
                                                title="Remove Function"
                                                class="${isFunctionConfirmOpen ? 'hidden' : 'inline-flex'} h-8 w-8 items-center justify-center rounded-lg text-red-400 opacity-0 transition hover:bg-red-500/10 hover:text-red-300 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:opacity-100 group-hover:opacity-100">
                                                <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2m-7 0h8m-9 3 1 9a1 1 0 0 0 1 .9h6a1 1 0 0 0 1-.9l1-9M10 11v6M14 11v6"/>
                                                </svg>
                                            </button>
                                            <div data-function-delete-confirm="${funcIndex}" class="${isFunctionConfirmOpen ? 'inline-flex' : 'hidden'} items-center gap-1">
                                                <button
                                                    type="button"
                                                    data-action="cancel-remove-function"
                                                    class="rounded-full border border-slate-600 px-2.5 py-1 text-[11px] font-semibold text-slate-300 transition hover:bg-slate-800">
                                                    Cancel
                                                </button>
                                                <button
                                                    type="button"
                                                    data-action="confirm-remove-function"
                                                    data-function-index="${funcIndex}"
                                                    class="rounded-full border border-red-500/40 bg-red-500/10 px-2.5 py-1 text-[11px] font-semibold text-red-300 transition hover:bg-red-500/20 hover:text-red-200 focus:outline-none focus:ring-2 focus:ring-red-500/40">
                                                    Remove
                                                </button>
                                            </div>
                                        ` : ''}

                                    </div>
                                </div>

                                <div class="relative rounded-xl overflow-hidden border border-slate-800 bg-slate-950/60">
                                    <div class="${isDraft ? '' : 'opacity-60'}">
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full text-sm">
                                                <thead class="bg-slate-800/60 text-slate-300">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left font-semibold uppercase text-[11px] tracking-wide">PPA / MFO</th>
                                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Success Indicators</th>
                                                        <th class="px-4 py-3 text-right font-semibold uppercase text-[11px] tracking-wide">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-800 text-slate-100">
                                                    ${mfoRows || emptyRow}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    ${isDraft ? '' : '<div class="pointer-events-none absolute inset-0 rounded-xl border border-slate-700/60 bg-slate-950/50"></div>'}
                                </div>
                            </div>
                        `;
                    }).join('');

                    functionsWrapper.innerHTML = html;
                }

                function renderTargetsPanel() {
                    const indicator = getSelectedIndicator();
                    const hasSelection = Boolean(indicator);

                    if (targetsQuantityInput) {
                        targetsQuantityInput.disabled = !isDraft || !hasSelection;
                        targetsQuantityInput.value = hasSelection && indicator?.targetQuantity !== null && indicator?.targetQuantity !== undefined
                            ? String(indicator.targetQuantity)
                            : '';
                    }

                    if (targetsTimelineInput) {
                        targetsTimelineInput.disabled = !isDraft || !hasSelection;
                        targetsTimelineInput.value = hasSelection ? String(indicator?.targetTimeline || '') : '';
                    }
                }

                function renderAssigned(unit) {
                    if (!assignedList || !assignedEmpty || !assignedUnit || !assignedIndicator) return;

                    const indicator = getSelectedIndicator();
                    const indicatorText = indicator ? indicator.text : '';
                    assignedUnit.textContent = unit || '---';
                    assignedIndicator.textContent = indicatorText || '---';

                    assignedList.innerHTML = '';
                    if (!indicator) {
                        assignedEmpty.classList.remove('hidden');
                        assignedEmpty.textContent = 'No success indicator selected.';
                        return;
                    }

                    const officeId = getSelectedOfficeId();
                    const rows = (assignedData || []).filter((employee) => Number(employee.office_id) === Number(officeId));
                    if (!rows.length) {
                        assignedEmpty.classList.remove('hidden');
                        assignedEmpty.textContent = 'No employees available...';
                        return;
                    }
                    assignedEmpty.classList.add('hidden');

                    rows.forEach((emp) => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-900/40';

                        const nameTd = document.createElement('td');
                        nameTd.className = 'px-4 py-3';
                        nameTd.textContent = emp.name;

                        const unitTd = document.createElement('td');
                        unitTd.className = 'px-4 py-3';
                        unitTd.textContent = emp.unit;

                        const statusTd = document.createElement('td');
                        statusTd.className = 'px-4 py-3';

                        const isAssigned = isEmployeeAssigned(indicator, emp.id);

                        const badge = document.createElement('span');
                        badge.className = `inline-flex items-center px-2 py-1 text-[11px] font-semibold rounded-full border ${
                            isAssigned ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200' : 'border-slate-600 bg-slate-800 text-slate-300'
                        }`;
                        badge.textContent = isAssigned ? 'Assigned' : 'Available';
                        statusTd.appendChild(badge);

                        const indicatorTd = document.createElement('td');
                        indicatorTd.className = 'px-4 py-3 text-slate-300';
                        indicatorTd.textContent = indicatorText || '---';

                        tr.appendChild(nameTd);
                        tr.appendChild(unitTd);
                        tr.appendChild(statusTd);
                        tr.appendChild(indicatorTd);

                        if (isDraft) {
                            const actionTd = document.createElement('td');
                            actionTd.className = 'px-4 py-3 text-center';

                            const toggle = document.createElement('button');
                            toggle.type = 'button';
                            toggle.className = 'text-blue-300 hover:text-blue-200 text-xs underline';
                            toggle.textContent = isAssigned ? 'Unassign' : 'Assign';
                            toggle.addEventListener('click', () => {
                                if (isEmployeeAssigned(indicator, emp.id)) {
                                    unassignEmployee(indicator, emp.id);
                                } else {
                                    assignEmployee(indicator, emp.id);
                                }
                                renderEditorWorkspaceDetail();
                            });

                            actionTd.appendChild(toggle);
                            tr.appendChild(actionTd);
                        }

                        assignedList.appendChild(tr);
                    });
                }

                function renderStandardsPanel() {
                    if (!standardsList || !standardsIndicatorLabel) return;

                    const indicator = getSelectedIndicator();
                    standardsList.innerHTML = '';
                    standardsIndicatorLabel.textContent = indicator?.text || 'No success indicator selected';

                    if (!indicator) {
                        standardsList.innerHTML = '<div class="px-4 py-6 text-sm text-slate-400">No success indicator selected.</div>';
                        return;
                    }

                    const data = ensureIndicatorMatrix(indicator);
                    const table = document.createElement('table');
                    table.className = 'min-w-full text-sm text-slate-100';
                    table.innerHTML = `
                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.22em] text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left">Rating</th>
                                <th class="px-4 py-3 text-left">Quality (Q)</th>
                                <th class="px-4 py-3 text-left">Efficiency (E)</th>
                                <th class="px-4 py-3 text-left">Timeliness (T)</th>
                            </tr>
                        </thead>
                    `;

                    const tbody = document.createElement('tbody');
                    tbody.className = 'divide-y divide-slate-800 text-slate-100';

                    const makeCell = (value, lvl, dim) => {
                        const td = document.createElement('td');
                        td.className = 'px-4 py-3 align-top';

                        const txt = (value || '').trim();
                        const textEl = document.createElement('div');
                        textEl.className = 'text-slate-100';
                        textEl.textContent = txt || '---';
                        td.appendChild(textEl);

                        if (isDraft) {
                            const actionBtn = document.createElement('button');
                            actionBtn.type = 'button';
                            actionBtn.className = 'mt-1 text-[11px] text-blue-300 hover:text-blue-200';
                            actionBtn.textContent = txt ? 'Edit' : 'Add +';
                            actionBtn.addEventListener('click', () => {
                                ratingSelectEl.value = String(lvl);
                                dimSelectEl.value = dim;
                                standardsEditTarget = { rating: String(lvl), dim };
                                standardsInput.value = txt || '';
                                standardsInput.focus();
                            });
                            td.appendChild(actionBtn);
                        }

                        if (isDraft && txt) {
                            const clearBtn = document.createElement('button');
                            clearBtn.type = 'button';
                            clearBtn.className = 'mt-1 ml-3 text-[11px] text-rose-300 hover:text-rose-200';
                            clearBtn.textContent = 'Clear';
                            clearBtn.addEventListener('click', () => {
                                const matrix = ensureIndicatorMatrix(indicator);
                                if (!matrix[String(lvl)]) {
                                    matrix[String(lvl)] = { q: '', e: '', t: '' };
                                }

                                matrix[String(lvl)][dim] = '';
                                indicator._matrix = matrix;
                                indicator.standards = standardsMatrixToArray(matrix);

                                if (
                                    standardsEditTarget
                                    && standardsEditTarget.rating === String(lvl)
                                    && standardsEditTarget.dim === dim
                                ) {
                                    standardsEditTarget = null;
                                    if (standardsInput) standardsInput.value = '';
                                }

                                renderEditorWorkspaceDetail();
                            });
                            td.appendChild(clearBtn);
                        }

                        return td;
                    };

                    [5, 4, 3, 2, 1].forEach((lvl) => {
                        const row = data[lvl] || { q: '', e: '', t: '' };
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-900/40';

                        const ratingTd = document.createElement('td');
                        ratingTd.className = 'px-4 py-3 font-semibold text-white';
                        ratingTd.textContent = lvl;

                        tr.append(
                            ratingTd,
                            makeCell(row.q, lvl, 'q'),
                            makeCell(row.e, lvl, 'e'),
                            makeCell(row.t, lvl, 't')
                        );
                        tbody.appendChild(tr);
                    });

                    table.appendChild(tbody);
                    standardsList.appendChild(table);
                }

                function renderIndicatorWorkspaceNav() {
                    if (!workspaceIndicatorNav || !workspaceIndicatorCountBadge) return;

                    workspaceIndicatorNav.innerHTML = '';
                    workspaceIndicatorCountBadge.textContent = String(activeIndicators.length || 0);

                    if (!activeIndicators.length) {
                        workspaceIndicatorNav.innerHTML = '<p class="px-3 py-4 text-sm text-slate-500">No success indicators yet.</p>';
                        return;
                    }

                    activeIndicators.forEach((indicator, idx) => {
                        const isSelected = idx === activeIndicatorIndex;
                        const isEditingIndicator = isDraft && activeEditingIndicatorIndex === idx;
                        const card = document.createElement('div');
                        card.className = `rounded-xl border px-4 py-3 transition ${
                            isSelected ? 'border-cyan-500/30 bg-cyan-500/10' : 'border-slate-800 bg-slate-950/50 hover:bg-slate-900/60'
                        }`;

                        const topRow = document.createElement('div');
                        topRow.className = 'flex items-start justify-between gap-3';

                        const textWrap = document.createElement('div');
                        textWrap.className = 'min-w-0 flex-1';

                        if (isEditingIndicator) {
                            const input = document.createElement('input');
                            input.type = 'text';
                            input.value = indicator?.text || '';
                            input.placeholder = 'Enter success indicator';
                            input.dataset.indicatorNavInput = String(idx);
                            input.className = 'w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/40 focus:outline-none';
                            input.style.background = '#0f172a';
                            input.style.color = '#e5e7eb';
                            input.addEventListener('input', (event) => {
                                const value = String(event.target.value || '');
                                indicator.text = value;
                                if (workspaceSelectedIndicatorTitle && idx === activeIndicatorIndex) {
                                    workspaceSelectedIndicatorTitle.textContent = value.trim() || 'New success indicator';
                                }
                            });
                            input.addEventListener('keydown', (event) => {
                                if (event.key === 'Enter') {
                                    event.preventDefault();
                                    finishEditIndicator(idx);
                                }
                            });
                            textWrap.appendChild(input);
                        } else {
                            const selectBtn = document.createElement('button');
                            selectBtn.type = 'button';
                            selectBtn.className = 'w-full text-left';
                            selectBtn.innerHTML = `<span class="block text-sm font-medium leading-6 text-slate-100">${escapeHtml(indicator?.text || 'Untitled indicator')}</span>`;
                            selectBtn.addEventListener('click', () => {
                                setSelectedIndicatorIndex(idx);
                                renderEditorWorkspaceDetail();
                            });
                            textWrap.appendChild(selectBtn);
                        }

                        topRow.appendChild(textWrap);

                        if (isDraft) {
                            const actionWrap = document.createElement('div');
                            actionWrap.className = 'flex items-center gap-2';

                            const editBtn = document.createElement('button');
                            editBtn.type = 'button';
                            editBtn.className = 'rounded-full border border-slate-700 bg-slate-900 px-2.5 py-1 text-[11px] font-semibold text-slate-300 transition hover:text-white';
                            editBtn.textContent = isEditingIndicator ? 'Done' : 'Edit';
                            editBtn.addEventListener('click', () => {
                                if (isEditingIndicator) {
                                    finishEditIndicator(idx);
                                } else {
                                    startEditIndicator(idx);
                                }
                            });

                            const delBtn = document.createElement('button');
                            delBtn.type = 'button';
                            delBtn.className = 'rounded-full border border-rose-500/40 bg-rose-500/10 px-2.5 py-1 text-[11px] font-semibold text-rose-200 transition hover:bg-rose-500/20';
                            delBtn.textContent = 'Delete';
                            delBtn.addEventListener('click', () => deleteIndicator(idx));

                            actionWrap.appendChild(editBtn);
                            actionWrap.appendChild(delBtn);
                            topRow.appendChild(actionWrap);
                        }

                        card.appendChild(topRow);
                        card.addEventListener('click', (event) => {
                            if (event.target.closest('button, input, textarea, select')) return;
                            setSelectedIndicatorIndex(idx);
                            renderEditorWorkspaceDetail();
                        });
                        workspaceIndicatorNav.appendChild(card);
                    });
                }

                function renderWorkspaceOverview() {
                    const func = getActiveFunction();
                    const mfo = getActiveMfo();
                    const indicator = getSelectedIndicator();
                    const functionType = normalizeFunctionType(func?.type || 'custom');
                    const indicatorCount = activeIndicators.length;
                    const selectedStandardsCount = indicator ? getIndicatorStandardsArray(indicator).length : 0;
                    const selectedAssigneeCount = indicator ? getAssignedEmployees(indicator).length : 0;
                    const targetSummary = deriveMfoTargetMeta(mfo || {}).summary || String(mfo?.target || '').trim() || '--';

                    if (workspaceFunctionType) workspaceFunctionType.textContent = `${functionType.charAt(0).toUpperCase()}${functionType.slice(1)} Function`;
                    if (workspaceFunctionWeight) workspaceFunctionWeight.textContent = `${Number(func?.weight || 0)}% Weight`;
                    if (workspaceTargetSummary) workspaceTargetSummary.textContent = targetSummary;
                    if (workspaceSelectedIndicatorTitle) workspaceSelectedIndicatorTitle.textContent = indicator?.text || 'No success indicator selected';
                    if (workspaceSelectedIndicatorTarget) workspaceSelectedIndicatorTarget.textContent = indicator ? getIndicatorTargetSummary(indicator) || '--' : '';
                    if (workspaceOverviewSummary) workspaceOverviewSummary.textContent = targetSummary;
                    if (workspaceOverviewFunction) workspaceOverviewFunction.textContent = `${functionType.charAt(0).toUpperCase()}${functionType.slice(1)}`;
                    if (workspaceOverviewWeight) workspaceOverviewWeight.textContent = `${Number(func?.weight || 0)}%`;
                    if (workspaceOverviewIndicatorCount) workspaceOverviewIndicatorCount.textContent = String(indicatorCount);
                    if (workspaceOverviewStandardsCount) workspaceOverviewStandardsCount.textContent = String(selectedStandardsCount);
                    if (workspaceOverviewAssigneeCount) workspaceOverviewAssigneeCount.textContent = String(selectedAssigneeCount);

                    if (workspaceOverviewIndicators) {
                        workspaceOverviewIndicators.innerHTML = '';

                        if (!activeIndicators.length) {
                            workspaceOverviewIndicators.innerHTML = '<p class="text-sm text-slate-500">No linked success indicators.</p>';
                        } else {
                            activeIndicators.forEach((entry, idx) => {
                                const item = document.createElement('button');
                                item.type = 'button';
                                item.className = `flex w-full items-start justify-between rounded-xl border px-4 py-3 text-left transition ${
                                    idx === activeIndicatorIndex ? 'border-cyan-500/30 bg-cyan-500/10' : 'border-slate-800 bg-slate-950/50 hover:bg-slate-900/60'
                                }`;
                                item.innerHTML = `
                                    <span class="pr-4 text-sm text-slate-100">${escapeHtml(entry?.text || 'Untitled indicator')}</span>
                                    <span class="rounded-md bg-slate-900 px-3 py-1 text-xs text-slate-400">${getIndicatorTargetSummary(entry) || '--'}</span>
                                `;
                                item.addEventListener('click', () => {
                                    setSelectedIndicatorIndex(idx);
                                    setEditorWorkspaceTab('targets');
                                    renderEditorWorkspaceDetail();
                                });
                                workspaceOverviewIndicators.appendChild(item);
                            });
                        }
                    }
                }

                function renderEditorWorkspaceDetail() {
                    renderIndicatorWorkspaceNav();
                    renderWorkspaceOverview();
                    renderTargetsPanel();
                    renderStandardsPanel();
                    renderAssigned(getSelectedUnitLabel());
                }

                function openAssignedModalForIndicator(indicatorIdx) {
                    setSelectedIndicatorIndex(indicatorIdx);
                    setEditorWorkspaceTab('assignees');
                    renderEditorWorkspaceDetail();
                }

                function openStandardsModal(idx) {
                    setSelectedIndicatorIndex(idx);
                    setEditorWorkspaceTab('standards');
                    renderEditorWorkspaceDetail();
                }

                function handleAddStandard() {
                    if (!standardsInput || !ratingSelectEl || !dimSelectEl) return;

                    const indicator = getSelectedIndicator();
                    if (!indicator) return;

                    const raw = standardsInput.value.trim();
                    if (!raw) return;

                    const rating = standardsEditTarget?.rating || ratingSelectEl.value;
                    const dim = standardsEditTarget?.dim || dimSelectEl.value;

                    const matrix = ensureIndicatorMatrix(indicator);
                    if (!matrix[rating]) matrix[rating] = { q: '', e: '', t: '' };
                    matrix[rating][dim] = raw;
                    indicator._matrix = matrix;
                    indicator.standards = standardsMatrixToArray(matrix);

                    standardsInput.value = '';
                    standardsEditTarget = null;
                    renderEditorWorkspaceDetail();
                }

                // ===== Indicator CRUD =====
                function startEditIndicator(idx) {
                    const indicator = activeIndicators[idx];
                    if (!indicator) return;
                    setSelectedIndicatorIndex(idx);
                    if (activeEditingIndicatorIndex !== null && activeEditingIndicatorIndex !== idx) {
                        finalizeIndicatorValues(activeIndicators[activeEditingIndicatorIndex]);
                    }

                    activeEditingIndicatorIndex = idx;
                    setEditorWorkspaceTab('targets');
                    renderEditorWorkspaceDetail();

                    requestAnimationFrame(() => {
                        const input = workspaceIndicatorNav?.querySelector(`[data-indicator-nav-input="${idx}"]`);
                        if (!input) return;
                        input.focus();
                        input.select();
                    });
                }

                function finishEditIndicator(idx) {
                    const indicator = activeIndicators[idx];
                    if (!indicator) return;

                    finalizeIndicatorValues(indicator);
                    setSelectedIndicatorIndex(idx);
                    activeEditingIndicatorIndex = null;
                    renderEditorWorkspaceDetail();
                }

                function deleteIndicator(idx) {
                    if (activeEditingIndicatorIndex === idx) {
                        activeEditingIndicatorIndex = null;
                    } else if (activeEditingIndicatorIndex !== null && idx < activeEditingIndicatorIndex) {
                        activeEditingIndicatorIndex -= 1;
                    }
                    activeIndicators.splice(idx, 1);
                    if (!activeIndicators.length) {
                        activeIndicatorIndex = 0;
                    } else if (idx <= activeIndicatorIndex) {
                        activeIndicatorIndex = Math.max(0, Math.min(activeIndicatorIndex, activeIndicators.length - 1));
                    }
                    renderEditorWorkspaceDetail();
                }

                function addIndicator() {
                    activeIndicators.push(createIndicator('New success indicator'));
                    setSelectedIndicatorIndex(activeIndicators.length - 1);
                    setEditorWorkspaceTab('targets');
                    renderEditorWorkspaceDetail();
                    startEditIndicator(activeIndicators.length - 1);
                }

                function openUwpIndicatorsModal(functionIndex, mfoIndex) {
                    const func = uwpState.functions[functionIndex];
                    const mfo = func?.mfos?.[mfoIndex];
                    if (!mfo || !indicatorsModal) return;

                    activeFunctionIndex = functionIndex;
                    activeMfoIndex = mfoIndex;
                    activeEditingIndicatorIndex = null;
                    if (!Array.isArray(mfo.indicators)) mfo.indicators = [];
                    activeIndicators = mfo.indicators;
                    activeIndicatorIndex = activeIndicators.length ? 0 : 0;
                    activeWorkspaceTab = 'overview';

                    if (indicatorsTitle) indicatorsTitle.textContent = mfo.title || '--';
                    setEditorWorkspaceTab('overview');
                    renderEditorWorkspaceDetail();

                    indicatorsModal.classList.remove('hidden');
                    indicatorsModal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                window.closeUwpIndicatorsModal = function () {
                    if (activeEditingIndicatorIndex !== null) {
                        finalizeIndicatorValues(activeIndicators[activeEditingIndicatorIndex]);
                    }
                    if (indicatorsModal) {
                        indicatorsModal.classList.add('hidden');
                        indicatorsModal.classList.remove('flex');
                    }
                    activeFunctionIndex = null;
                    activeMfoIndex = null;
                    activeIndicators = [];
                    activeIndicatorIndex = 0;
                    activeWorkspaceTab = 'overview';
                    activeEditingIndicatorIndex = null;
                    standardsEditTarget = null;
                    if (standardsInput) standardsInput.value = '';
                    renderFunctions();
                    document.body.classList.remove('overflow-hidden');
                };

                // ===== Function Container Actions =====
                function addFunction() {
                    uwpState.functions.push(createFunctionContainer());
                    renderFunctions();

                    requestAnimationFrame(() => {
                        const cards = functionsWrapper?.querySelectorAll('[data-function-card]');
                        const lastCard = cards && cards.length ? cards[cards.length - 1] : null;
                        if (!lastCard) return;

                        lastCard.scrollIntoView({ behavior: 'smooth', block: 'start' });

                        const titleInput = lastCard.querySelector('[data-function-title]');
                        if (titleInput && typeof titleInput.focus === 'function') {
                            try {
                                titleInput.focus({ preventScroll: true });
                            } catch (error) {
                                titleInput.focus();
                            }
                        }
                    });
                }

                function addMfo(functionIndex) {
                    const func = uwpState.functions[functionIndex];
                    if (!func) return;
                    func.mfos = Array.isArray(func.mfos) ? func.mfos : [];
                    func.mfos.push(createMfo('', '', null, []));
                    renderFunctions();
                }

                function enterRowConfirm(rowId) {
                    if (!isDraft) return;
                    activeFunctionConfirmId = null;
                    activeRowConfirmId = rowId;
                    renderFunctions();
                }

                function exitRowConfirm() {
                    if (activeRowConfirmId === null) return;
                    activeRowConfirmId = null;
                    renderFunctions();
                }

                function enterFunctionConfirm(functionIndex) {
                    if (!isDraft) return;
                    activeRowConfirmId = null;
                    activeFunctionConfirmId = functionIndex;
                    renderFunctions();
                }

                function exitFunctionConfirm() {
                    if (activeFunctionConfirmId === null) return;
                    activeFunctionConfirmId = null;
                    renderFunctions();
                }

                function removeMfo(functionIndex, mfoIndex) {
                    const func = uwpState.functions[functionIndex];
                    if (!func || !Array.isArray(func.mfos)) return;
                    func.mfos.splice(mfoIndex, 1);
                    activeRowConfirmId = null;
                    renderFunctions();
                }

                function removeFunction(functionIndex) {
                    if (!isDraft) return;
                    uwpState.functions.splice(functionIndex, 1);
                    activeFunctionConfirmId = null;
                    activeRowConfirmId = null;
                    renderFunctions();
                }

                function buildFunctionsPayload() {
                    return uwpState.functions.map((func) => ({
                        title: func.title,
                        type: func.type,
                        weight: func.weight,
                        mfos: (func.mfos || []).map((mfo) => {
                            const targetMeta = deriveMfoTargetMeta(mfo);
                            return {
                                title: mfo.title,
                                target_quantity: targetMeta.targetQuantity,
                                target: targetMeta.summary === 'Multiple indicator targets'
                                    ? 'Per success indicator'
                                    : (String(mfo?.target ?? '').trim() || 'Per success indicator'),
                                indicators: (mfo.indicators || []).map((indicator) => ({
                                    text: indicator.text,
                                    target_quantity: normalizeTargetQuantity(indicator.targetQuantity),
                                    target_timeline: String(indicator.targetTimeline || '').trim(),
                                    standards: getIndicatorStandardsArray(indicator),
                                    assignees: Array.isArray(indicator.assignees) ? [...indicator.assignees] : [],
                                })),
                            };
                        }),
                    }));
                }

                function buildMfosPayloadFromState() {
                    const payload = [];
                    let sortOrder = 1;

                    uwpState.functions.forEach((func) => {
                        const functionCode = ['core', 'support', 'custom'].includes(func.type) ? func.type : 'custom';
                        const weight = Number(func.weight || 0);

                        (func.mfos || []).forEach((mfo) => {
                            const titleText = (mfo.title || '').trim();
                            if (!titleText) return;
                            const targetMeta = deriveMfoTargetMeta(mfo);

                            const successIndicators = (mfo.indicators || []).map((indicator) => {
                                const description = (indicator.text || '').trim();
                                if (!description) return null;

                                const standards = getIndicatorStandardsArray(indicator).map((item) => ({
                                    dimension: item.dimension,
                                    rating_level: item.rating,
                                    standard: item.text,
                                }));

                                return {
                                    description,
                                    target_quantity: normalizeTargetQuantity(indicator.targetQuantity),
                                    target_timeline: String(indicator.targetTimeline || '').trim(),
                                    standards,
                                };
                            }).filter(Boolean);

                            payload.push({
                                function_code: functionCode,
                                title: titleText,
                                target_quantity: targetMeta.targetQuantity,
                                target_summary: targetMeta.summary,
                                weight: weight,
                                sort_order: sortOrder,
                                success_indicators: successIndicators,
                            });

                            sortOrder += 1;
                        });
                    });

                    return payload;
                }

                function buildAssignmentsPayloadMvp() {
                    const unique = new Set();
                    uwpState.functions.forEach((func) => {
                        (func.mfos || []).forEach((mfo) => {
                            (mfo.indicators || []).forEach((indicator) => {
                                (indicator.assignees || []).forEach((entry) => {
                                    if (entry) unique.add(entry);
                                });
                            });
                        });
                    });
                    return Array.from(unique);
                }

                function submitUwp(actionUrl) {
                    if (!uwpForm || !actionUrl) return;
                    if (uwpIdInput && selectedUwpId && !uwpIdInput.value) {
                        uwpIdInput.value = String(selectedUwpId);
                    }
                    if (functionsPayloadInput) {
                        functionsPayloadInput.value = JSON.stringify(buildFunctionsPayload());
                    }
                    if (mfosPayloadInput) {
                        mfosPayloadInput.value = JSON.stringify(buildMfosPayloadFromState());
                    }
                    if (assignmentsPayloadInput) {
                        assignmentsPayloadInput.value = JSON.stringify(buildAssignmentsPayloadMvp());
                    }
                    uwpForm.action = actionUrl;
                    uwpForm.submit();
                }

                // ===== Wire events =====
                if (functionsWrapper) {
                    functionsWrapper.addEventListener('input', (event) => {
                        const target = event.target;
                        if (target.matches('[data-function-title]')) {
                            const idx = Number(target.dataset.functionIndex);
                            if (!Number.isNaN(idx) && uwpState.functions[idx]) {
                                uwpState.functions[idx].title = target.value;
                            }
                        }

                        if (target.matches('[data-function-weight]')) {
                            const idx = Number(target.dataset.functionIndex);
                            if (!Number.isNaN(idx) && uwpState.functions[idx]) {
                                const weight = clampNumber(target.value, 0, 100);
                                uwpState.functions[idx].weight = weight;
                                const label = functionsWrapper.querySelector(`[data-function-weight-label="${idx}"]`);
                                if (label) label.textContent = `(${weight}%)`;
                            }
                        }

                        if (target.matches('[data-mfo-title]')) {
                            const funcIdx = Number(target.dataset.functionIndex);
                            const mfoIdx = Number(target.dataset.mfoIndex);
                            const mfo = uwpState.functions[funcIdx]?.mfos?.[mfoIdx];
                            if (mfo) mfo.title = target.value;
                        }

                        if (target.matches('[data-mfo-target-quantity]')) {
                            const funcIdx = Number(target.dataset.functionIndex);
                            const mfoIdx = Number(target.dataset.mfoIndex);
                            const mfo = uwpState.functions[funcIdx]?.mfos?.[mfoIdx];
                            if (mfo) mfo.targetQuantity = normalizeTargetQuantity(target.value);
                        }

                        if (target.matches('[data-mfo-target]')) {
                            const funcIdx = Number(target.dataset.functionIndex);
                            const mfoIdx = Number(target.dataset.mfoIndex);
                            const mfo = uwpState.functions[funcIdx]?.mfos?.[mfoIdx];
                            if (mfo) mfo.target = target.value;
                        }
                    });

                    functionsWrapper.addEventListener('change', (event) => {
                        const target = event.target;
                        if (target.matches('[data-function-type]')) {
                            const idx = Number(target.dataset.functionIndex);
                            if (!Number.isNaN(idx) && uwpState.functions[idx]) {
                                const selectedType = normalizeFunctionType(target.value);
                                const resolvedType = resolveFunctionTypeSelection(selectedType, idx);
                                uwpState.functions[idx].type = resolvedType;
                                uwpState.functions[idx].isCustom = resolvedType === 'custom';
                                if (resolvedType !== selectedType) {
                                    target.value = resolvedType;
                                }
                                renderFunctions();
                            }
                        }
                    });

                    functionsWrapper.addEventListener('click', (event) => {
                        const viewBtn = event.target.closest('[data-action="view-indicators"]');
                        if (viewBtn) {
                            const funcIdx = Number(viewBtn.dataset.functionIndex);
                            const mfoIdx = Number(viewBtn.dataset.mfoIndex);
                            openUwpIndicatorsModal(funcIdx, mfoIdx);
                            return;
                        }

                        const addMfoBtn = event.target.closest('[data-action="add-mfo"]');
                        if (addMfoBtn) {
                            const funcIdx = Number(addMfoBtn.dataset.functionIndex);
                            addMfo(funcIdx);
                            return;
                        }

                        const triggerRemoveBtn = event.target.closest('[data-action="trigger-remove-mfo"]');
                        if (triggerRemoveBtn) {
                            if (!isDraft) return;
                            const rowId = triggerRemoveBtn.dataset.rowId;
                            enterRowConfirm(rowId);
                            return;
                        }

                        const cancelRemoveBtn = event.target.closest('[data-action="cancel-remove-mfo"]');
                        if (cancelRemoveBtn) {
                            if (!isDraft) return;
                            exitRowConfirm();
                            return;
                        }

                        const confirmRemoveBtn = event.target.closest('[data-action="confirm-remove-mfo"]');
                        if (confirmRemoveBtn) {
                            if (!isDraft) return;
                            const funcIdx = Number(confirmRemoveBtn.dataset.functionIndex);
                            const mfoIdx = Number(confirmRemoveBtn.dataset.mfoIndex);
                            removeMfo(funcIdx, mfoIdx);
                            return;
                        }

                        const triggerRemoveFunctionBtn = event.target.closest('[data-action="trigger-remove-function"]');
                        if (triggerRemoveFunctionBtn) {
                            if (!isDraft) return;
                            const funcIdx = Number(triggerRemoveFunctionBtn.dataset.functionIndex);
                            enterFunctionConfirm(funcIdx);
                            return;
                        }

                        const cancelRemoveFunctionBtn = event.target.closest('[data-action="cancel-remove-function"]');
                        if (cancelRemoveFunctionBtn) {
                            if (!isDraft) return;
                            exitFunctionConfirm();
                            return;
                        }

                        const confirmRemoveFunctionBtn = event.target.closest('[data-action="confirm-remove-function"]');
                        if (confirmRemoveFunctionBtn) {
                            if (!isDraft) return;
                            const funcIdx = Number(confirmRemoveFunctionBtn.dataset.functionIndex);
                            removeFunction(funcIdx);
                        }
                    });
                }

                if (addFunctionBtn && isDraft) addFunctionBtn.addEventListener('click', addFunction);
                if (addIndicatorBtn && isDraft) addIndicatorBtn.addEventListener('click', addIndicator);
                if (addIndicatorSecondaryBtn && isDraft) addIndicatorSecondaryBtn.addEventListener('click', addIndicator);
                if (addStandardBtn && isDraft) addStandardBtn.addEventListener('click', handleAddStandard);
                if (targetsQuantityInput) {
                    targetsQuantityInput.addEventListener('input', (event) => {
                        const indicator = getSelectedIndicator();
                        if (!indicator || !isDraft) return;
                        indicator.targetQuantity = normalizeTargetQuantity(event.target.value);
                        if (workspaceSelectedIndicatorTarget) {
                            workspaceSelectedIndicatorTarget.textContent = getIndicatorTargetSummary(indicator) || '--';
                        }
                    });
                    targetsQuantityInput.addEventListener('blur', () => {
                        if (!isDraft) return;
                        renderEditorWorkspaceDetail();
                    });
                }
                if (targetsTimelineInput) {
                    targetsTimelineInput.addEventListener('input', (event) => {
                        const indicator = getSelectedIndicator();
                        if (!indicator || !isDraft) return;
                        indicator.targetTimeline = String(event.target.value || '');
                        if (workspaceSelectedIndicatorTarget) {
                            workspaceSelectedIndicatorTarget.textContent = getIndicatorTargetSummary(indicator) || '--';
                        }
                    });
                    targetsTimelineInput.addEventListener('blur', () => {
                        if (!isDraft) return;
                        renderEditorWorkspaceDetail();
                    });
                }
                const resetStandardBtn = document.getElementById('uwp-reset-standard');
                if (resetStandardBtn && isDraft) {
                    resetStandardBtn.addEventListener('click', () => {
                        const indicator = getSelectedIndicator();
                        if (!indicator) return;
                        indicator._matrix = seedStandardsForIndicator(indicator.text || '');
                        indicator.standards = standardsMatrixToArray(indicator._matrix);
                        standardsEditTarget = null;
                        if (standardsInput) standardsInput.value = '';
                        renderEditorWorkspaceDetail();
                    });
                }

                if (submitUwpBtn && isDraft) {
                    submitUwpBtn.addEventListener('click', () => {
                        setButtonLoading(submitUwpBtn, true, submitUwpBtn.dataset.loadingText || 'Submitting...');
                        submitUwp(submitUwpUrl);
                    });
                }

                renderFunctions();

                document.addEventListener('click', (event) => {
                    if (!isDraft) return;

                    let shouldRender = false;

                    if (activeRowConfirmId !== null) {
                        const row = event.target.closest('[data-mfo-row-id]');
                        if (!(row && row.dataset.mfoRowId === activeRowConfirmId)) {
                            activeRowConfirmId = null;
                            shouldRender = true;
                        }
                    }

                    if (activeFunctionConfirmId !== null) {
                        const card = event.target.closest('[data-function-card]');
                        const cardIndex = card ? Number(card.dataset.functionIndex) : null;
                        if (cardIndex !== activeFunctionConfirmId) {
                            activeFunctionConfirmId = null;
                            shouldRender = true;
                        }
                    }

                    if (shouldRender) {
                        renderFunctions();
                    }
                });

                workspaceTabButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        setEditorWorkspaceTab(button.getAttribute('data-editor-workspace-tab') || 'overview');
                    });
                });

                indicatorsModal?.addEventListener('click', (e) => {
                    if (e.target === indicatorsModal) closeUwpIndicatorsModal();
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        if (isDraft && (activeRowConfirmId !== null || activeFunctionConfirmId !== null)) {
                            activeRowConfirmId = null;
                            activeFunctionConfirmId = null;
                            renderFunctions();
                        } else if (indicatorsModal && !indicatorsModal.classList.contains('hidden')) {
                            closeUwpIndicatorsModal();
                        } else {
                            closeModal();
                        }
                    }
                });

                window.closeStandardsModal = closeUwpIndicatorsModal;
                window.closeAssignedModal = closeUwpIndicatorsModal;
            });
        </script>
    @endpush
@endsection
