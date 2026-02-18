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
    @endphp
@section('main-content')
    <section class="space-y-6">
        <div>
            <a href="{{ route('supervisor.uwp-page') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-300">
                ← Back to Unit Work Plans
            </a>
        </div>

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
                        <p class="text-xs text-emerald-300/90">Draft mode: you can add/remove MFOs.</p>
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
                                    {{ old('office_id', 1) == $office->id ? 'selected' : '' }}>
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
    <div id="uwp-indicators-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="uwp-indicators-title" class="text-lg font-semibold text-white">--</h3>
                    <p class="text-xs text-slate-400 mt-1">One output may have multiple success indicators. Each indicator can be assigned to a specific employee.</p>
                </div>
                <button type="button" onclick="closeUwpIndicatorsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-3">
                @if ($canEdit)
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-xs text-slate-400">Manage success indicators (one per line, scalable list).</span>
                        <button type="button" id="uwp-add-indicator"
                                class="inline-flex items-center gap-1 rounded-lg border border-blue-500/50 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200 hover:bg-blue-500/20">
                            <span class="fa-solid fa-plus text-[10px]"></span>
                            <span>Add Indicator</span>
                        </button>
                    </div>
                @endif

                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
                    <div class="max-h-[340px] overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-900/70 text-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-[11px] uppercase tracking-[0.2em] text-slate-400">Success Indicator</th>
                                    <th class="px-4 py-3 text-center text-[11px] uppercase tracking-[0.2em] text-slate-400">Standards</th>
                                    <th class="px-4 py-3 text-center text-[11px] uppercase tracking-[0.2em] text-slate-400">Assign Employee</th>
                                    @if ($canEdit)
                                        <th class="px-4 py-3 text-center text-[11px] uppercase tracking-[0.2em] text-slate-400">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="uwp-indicators-list" class="divide-y divide-slate-800"></tbody>
                        </table>
                    </div>
                </div>

                @unless ($canEdit)
                    <p class="text-[11px] text-slate-500">Read-only view. Indicators were finalized at submission.</p>
                @endunless
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closeUwpIndicatorsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- STANDARDS SUB-MODAL --}}
    <div id="uwp-standards-modal" class="fixed inset-0 z-[86] hidden items-center justify-center bg-black/60 px-4 py-8">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards</p>
                    <h3 class="text-lg font-semibold text-white">Target Difficulty / Standards</h3>
                    <p id="uwp-standards-indicator" class="text-[11px] text-slate-400 mt-1"></p>
                </div>
                <button type="button" onclick="closeStandardsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-4 text-sm text-slate-200 max-h-[70vh] overflow-y-auto">
                <div id="uwp-standards-list" class="w-full"></div>

                @if ($canEdit)
                    <div class="space-y-3 rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-xs text-slate-400">Add a standard to a specific Rating × Dimension cell.</p>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-4">
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

                            <div class="sm:col-span-2">
                                <textarea id="uwp-standards-input"
                                          style="background:#0f172a;color:#e5e7eb;"
                                          rows="2"
                                          placeholder="Enter standard text"
                                          class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"></textarea>
                            </div>
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
    <div id="uwp-assigned-employees-modal" class="fixed inset-0 z-[85] hidden items-center justify-center bg-black/60 px-4 py-8">
        <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assign Employee</p>
                    <h3 class="text-lg font-semibold text-white">Employees under the selected Office/Unit</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Office / Unit: <span id="uwp-assigned-unit">--</span></p>
                    <p class="text-[11px] text-slate-400 mt-1">Success Indicator: <span id="uwp-assigned-indicator" class="font-semibold text-slate-200">--</span></p>
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
                        <tbody id="uwp-assigned-list" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>

                <p id="uwp-assigned-empty" class="text-[12px] text-slate-500 hidden">No employees available (demo).</p>
            </div>

            <div class="mt-4 flex items-center justify-end gap-3 border-t border-slate-800 pt-3">
                @if ($canEdit)
                    <button type="button"
                            id="uwp-save-assignments"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                        <span data-button-label>Save Assignment</span>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                @endif

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
                const indicatorsList = document.getElementById('uwp-indicators-list');
                const addIndicatorBtn = document.getElementById('uwp-add-indicator');

                const standardsModal = document.getElementById('uwp-standards-modal');
                const standardsList = document.getElementById('uwp-standards-list');
                const standardsIndicatorLabel = document.getElementById('uwp-standards-indicator');
                const standardsInput = document.getElementById('uwp-standards-input');
                const addStandardBtn = document.getElementById('uwp-add-standard');
                const ratingSelectEl = document.getElementById('uwp-standard-rating');
                const dimSelectEl = document.getElementById('uwp-standard-dimension');
                let standardsEditTarget = null; // { rating: '5', dim: 'q' }

                const assignedModal = document.getElementById('uwp-assigned-employees-modal');
                const assignedList = document.getElementById('uwp-assigned-list');
                const assignedEmpty = document.getElementById('uwp-assigned-empty');
                const assignedUnit = document.getElementById('uwp-assigned-unit');
                const assignedIndicator = document.getElementById('uwp-assigned-indicator');
                const saveAssignmentsBtn = document.getElementById('uwp-save-assignments');

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
                let activeAssignIndicatorIndex = null;
                let activeRowConfirmId = null;
                let activeFunctionConfirmId = null;

                const assignedData = {
                    'Revenue Collection Unit': [
                        { name: 'Ramon Reyes', unit: 'Revenue Collection Unit' },
                    ],
                    'Records Management Unit': [],
                    'Administrative Services Unit': [],
                    'Human Resource Management Unit': [],
                    'General Services Unit': [],
                    'Planning and Development Unit': [],
                };

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
                                        { text: 'All e-bank transactions scanned and encoded daily', standards: [], assignees: [] },
                                        { text: 'Indexing complete with no missing pages', standards: [], assignees: [] },
                                        { text: 'Audit trail maintained within 24 hours', standards: [], assignees: [] },
                                    ],
                                },
                                {
                                    title: 'Processing of Over-the-Counter Revenue Transactions',
                                    target: 'Daily; 95% processed within the same working day',
                                    indicators: [
                                        { text: 'Same-day verification of OTC transactions', standards: [], assignees: [] },
                                        { text: '95% encoded within the business day', standards: [], assignees: [] },
                                        { text: 'OR validation completed daily', standards: [], assignees: [] },
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
                                        { text: 'Weekly filing updated and retrievable', standards: [], assignees: [] },
                                        { text: 'Digital backups synced monthly', standards: [], assignees: [] },
                                        { text: 'Retrieval logs maintained for audits', standards: [], assignees: [] },
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

                function getSelectedUnitLabel() {
                    if (!unitSelect) return 'Revenue Collection Unit';
                    const option = unitSelect.options[unitSelect.selectedIndex];
                    return option ? option.text : 'Revenue Collection Unit';
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
                        if (!matrix[rating][dimKey]) return;
                        matrix[rating][dimKey] = item.text || '';
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
                        : seedStandardsForIndicator(indicator.text || '');
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

                function createIndicator(text) {
                    return {
                        text: text || 'New success indicator',
                        standards: [],
                        assignees: [],
                    };
                }

                function createMfo(title, target, indicators) {
                    return {
                        title: title || '',
                        target: target || '',
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

                function getAssignedEmployees(indicator) {
                    if (!indicator) return [];
                    return Array.isArray(indicator.assignees) ? [...indicator.assignees] : [];
                }

                function isEmployeeAssigned(indicator, employeeName) {
                    if (!indicator || !employeeName) return false;
                    return getAssignedEmployees(indicator).includes(employeeName);
                }

                function assignEmployee(indicator, employeeName) {
                    if (!indicator || !employeeName) return;
                    indicator.assignees = Array.isArray(indicator.assignees) ? indicator.assignees : [];
                    if (!indicator.assignees.includes(employeeName)) {
                        indicator.assignees.push(employeeName);
                    }
                }

                function unassignEmployee(indicator, employeeName) {
                    if (!indicator || !employeeName) return;
                    indicator.assignees = Array.isArray(indicator.assignees) ? indicator.assignees : [];
                    const index = indicator.assignees.indexOf(employeeName);
                    if (index !== -1) {
                        indicator.assignees.splice(index, 1);
                    }
                }

                function renderFunctions() {
                    if (!functionsWrapper) return;

                    const html = uwpState.functions.map((func, funcIndex) => {
                        const weightValue = Number(func.weight || 0);
                        const description = getFunctionDescription(func);
                        const inputDisabled = isDraft ? '' : 'disabled';
                        const mutedClass = isDraft ? '' : 'opacity-60 pointer-events-none';
                        const canDeleteFunction = isDraft;
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

                                    <td class="px-4 py-4">
                                        <textarea
                                            data-mfo-target
                                            data-function-index="${funcIndex}"
                                            data-mfo-index="${mfoIndex}"
                                            rows="2"
                                            class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none ${mutedClass}"
                                            style="background:#0f172a;color:#e5e7eb;"
                                            placeholder="e.g., Monthly; 1,200 files"
                                            ${inputDisabled}>${escapeHtml(mfo.target)}</textarea>
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
                                <td colspan="4" class="px-4 py-6 text-center text-xs text-slate-500">
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
                                                        <th class="px-4 py-3 text-center font-semibold uppercase text-[11px] tracking-wide">Target / Timeline</th>
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

                function renderIndicators(list) {
                    if (!indicatorsList) return;
                    indicatorsList.innerHTML = '';

                    list.forEach((indicator, idx) => {
                        const value = (indicator?.text || '').trim();
                        if (!value) return;

                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-900/40';

                        // Indicator text
                        const indicatorTd = document.createElement('td');
                        indicatorTd.className = 'px-4 py-3 align-top';
                        const indicatorWrap = document.createElement('div');
                        indicatorWrap.className = 'space-y-1';

                        const textSpan = document.createElement('div');
                        textSpan.className = 'text-slate-100';
                        textSpan.textContent = value;

                        const hint = document.createElement('div');
                        hint.className = 'text-[11px] text-slate-500';
                        hint.textContent = 'Assigned per indicator (task-level).';

                        indicatorWrap.append(textSpan);
                        indicatorWrap.append(hint);
                        indicatorTd.appendChild(indicatorWrap);

                        // Standards column
                        const standardsTd = document.createElement('td');
                        standardsTd.className = 'px-4 py-3 text-center align-top';

                        const standardBtn = document.createElement('button');
                        standardBtn.type = 'button';
                        standardBtn.className = 'inline-flex items-center justify-center rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[120px]';
                        standardBtn.innerHTML = `
                            <span class="inline-flex items-center gap-2">
                                <span class="fa-regular fa-eye text-[12px]"></span>
                                <span>Standards</span>
                            </span>
                        `;
                        standardBtn.addEventListener('click', () => openStandardsModal(idx));
                        standardsTd.appendChild(standardBtn);

                        // Assigned employee column
                        const assignedTd = document.createElement('td');
                        assignedTd.className = 'px-4 py-3 text-center align-top';

                        const assignedCount = getAssignedEmployees(indicator).length;
                        const assignBtn = document.createElement('button');
                        assignBtn.type = 'button';
                        assignBtn.className = 'inline-flex items-center gap-2 text-xs font-semibold text-blue-400 transition-colors hover:text-blue-300 focus:outline-none';
                        assignBtn.style.cursor = 'pointer';
                        assignBtn.innerHTML = `
                            <span class="fa-regular fa-user text-[12px]"></span>
                            <span>+ Assign</span>
                            <span class="text-[11px] text-slate-400">( ${assignedCount} )</span>
                        `;
                        assignBtn.addEventListener('click', () => openAssignedModalForIndicator(idx));
                        assignedTd.appendChild(assignBtn);

                        tr.appendChild(indicatorTd);
                        tr.appendChild(standardsTd);
                        tr.appendChild(assignedTd);

                        // Draft actions (Edit/Delete only — assignment is handled by Assign Employee button)
                        if (isDraft) {
                            const actionsTd = document.createElement('td');
                            actionsTd.className = 'px-4 py-3 text-center align-top';

                            const actionsWrap = document.createElement('div');
                            actionsWrap.className = 'inline-flex items-center gap-3 text-[11px] text-blue-200';

                            const editBtn = document.createElement('button');
                            editBtn.type = 'button';
                            editBtn.textContent = 'Edit';
                            editBtn.className = 'hover:text-blue-100 underline';
                            editBtn.addEventListener('click', () => startEditIndicator(idx, value));

                            const delBtn = document.createElement('button');
                            delBtn.type = 'button';
                            delBtn.textContent = 'Delete';
                            delBtn.className = 'hover:text-blue-100 underline';
                            delBtn.addEventListener('click', () => deleteIndicator(idx));

                            actionsWrap.appendChild(editBtn);
                            actionsWrap.appendChild(delBtn);
                            actionsTd.appendChild(actionsWrap);
                            tr.appendChild(actionsTd);
                        }

                        indicatorsList.appendChild(tr);
                    });
                }

                // ===== Assigned Modal (scoped to active indicator) =====
                function renderAssigned(unit) {
                    if (!assignedList || !assignedEmpty || !assignedUnit || !assignedIndicator) return;

                    const indicator = activeIndicators[activeAssignIndicatorIndex];
                    const indicatorText = indicator ? indicator.text : '';
                    assignedUnit.textContent = unit || '---';
                    assignedIndicator.textContent = indicatorText || '---';

                    assignedList.innerHTML = '';
                    const rows = assignedData[unit] || [];
                    if (!rows.length) {
                        assignedEmpty.classList.remove('hidden');
                        return;
                    }
                    assignedEmpty.classList.add('hidden');

                    rows.forEach((emp) => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-900/40';

                        const nameTd = document.createElement('td');
                        nameTd.className = 'px-4 py-2';
                        nameTd.textContent = emp.name;

                        const unitTd = document.createElement('td');
                        unitTd.className = 'px-4 py-2';
                        unitTd.textContent = emp.unit;

                        const statusTd = document.createElement('td');
                        statusTd.className = 'px-4 py-2';

                        const isAssigned = isEmployeeAssigned(indicator, emp.name);
                        const badge = document.createElement('span');
                        badge.className = `inline-flex items-center px-2 py-1 text-[11px] font-semibold rounded-full border ${
                            isAssigned ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200' : 'border-slate-600 bg-slate-800 text-slate-300'
                        }`;
                        badge.textContent = isAssigned ? 'Assigned' : 'Available';
                        statusTd.appendChild(badge);

                        const indicatorTd = document.createElement('td');
                        indicatorTd.className = 'px-4 py-2 text-slate-300';
                        indicatorTd.textContent = indicatorText || '---';

                        tr.appendChild(nameTd);
                        tr.appendChild(unitTd);
                        tr.appendChild(statusTd);
                        tr.appendChild(indicatorTd);

                        if (isDraft) {
                            const actionTd = document.createElement('td');
                            actionTd.className = 'px-4 py-2 text-center';

                            const toggle = document.createElement('button');
                            toggle.type = 'button';
                            toggle.className = 'text-blue-300 hover:text-blue-200 text-xs underline';
                            toggle.textContent = isAssigned ? 'Unassign' : 'Assign';

                            toggle.addEventListener('click', () => {
                                if (!indicator) return;
                                if (isEmployeeAssigned(indicator, emp.name)) {
                                    unassignEmployee(indicator, emp.name);
                                } else {
                                    assignEmployee(indicator, emp.name);
                                }

                                renderAssigned(unit);
                                renderIndicators(activeIndicators);
                            });

                            actionTd.appendChild(toggle);
                            tr.appendChild(actionTd);
                        }

                        assignedList.appendChild(tr);
                    });
                }

                function openAssignedModalForIndicator(indicatorIdx) {
                    if (!assignedModal) return;
                    activeAssignIndicatorIndex = indicatorIdx;

                    const unit = getSelectedUnitLabel();
                    renderAssigned(unit);

                    assignedModal.classList.remove('hidden');
                    assignedModal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function closeAssignedModal() {
                    if (assignedModal) {
                        assignedModal.classList.add('hidden');
                        assignedModal.classList.remove('flex');
                    }
                    document.body.classList.remove('overflow-hidden');
                }

                                                                                                                                                // ===== Standards Modal (existing, unchanged behavior) =====
                function openStandardsModal(idx) {
                    if (!standardsModal || !standardsList) return;
                    const indicator = activeIndicators[idx];
                    if (!indicator) return;

                    const data = ensureIndicatorMatrix(indicator);

                    standardsList.innerHTML = '';
                    if (standardsIndicatorLabel) standardsIndicatorLabel.textContent = indicator.text || '';

                    const table = document.createElement('table');
                    table.className = 'w-full text-sm border border-slate-800';
                    table.innerHTML = `
                        <thead class="bg-slate-900/70 text-slate-200">
                            <tr>
                                <th class="px-3 py-2 text-left">Rating</th>
                                <th class="px-3 py-2 text-left">Quality (Q)</th>
                                <th class="px-3 py-2 text-left">Efficiency (E)</th>
                                <th class="px-3 py-2 text-left">Timeliness (T)</th>
                            </tr>
                        </thead>
                    `;

                    const tbody = document.createElement('tbody');
                    tbody.className = 'divide-y divide-slate-800 text-slate-100';

                    const makeCell = (value, lvl, dim) => {
                        const td = document.createElement('td');
                        td.className = 'px-3 py-2 text-left align-top';

                        const txt = (value || '').trim();

                        const textEl = document.createElement('div');
                        textEl.className = 'text-slate-100';
                        textEl.textContent = txt ? txt : '---';
                        td.appendChild(textEl);

                        if (isDraft) {
                            const actionBtn = document.createElement('button');
                            actionBtn.type = 'button';
                            actionBtn.className = 'mt-1 text-[11px]';
                            actionBtn.style.color = '#60a5fa';
                            actionBtn.textContent = txt ? 'Edit' : 'Add +';

                            actionBtn.addEventListener('click', () => {
                                ratingSelectEl.value = String(lvl);
                                dimSelectEl.value = dim;
                                standardsEditTarget = { rating: String(lvl), dim };
                                standardsInput.value = txt ? txt : '';
                                standardsInput.focus();
                            });

                            td.appendChild(actionBtn);
                        }

                        if (isDraft && txt) {
                            const clearBtn = document.createElement('button');
                            clearBtn.type = 'button';
                            clearBtn.className = 'mt-1 ml-3 text-[11px]';
                            clearBtn.style.color = '#f87171';
                            clearBtn.textContent = 'Clear';

                            clearBtn.addEventListener('click', () => {
                                const matrix = ensureIndicatorMatrix(indicator);
                                if (!matrix[String(lvl)]) {
                                    matrix[String(lvl)] = { q:'', e:'', t:'' };
                                }

                                matrix[String(lvl)][dim] = '';
                                indicator._matrix = matrix;
                                indicator.standards = standardsMatrixToArray(matrix);

                                if (
                                    standardsEditTarget &&
                                    standardsEditTarget.rating === String(lvl) &&
                                    standardsEditTarget.dim === dim
                                ) {
                                    standardsEditTarget = null;
                                    if (standardsInput) standardsInput.value = '';
                                }

                                openStandardsModal(idx);
                            });

                            td.appendChild(clearBtn);
                        }

                        return td;
                    };

                    [5,4,3,2,1].forEach((lvl) => {
                        const row = data[lvl] || { q:'', e:'', t:'' };

                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-900/40';

                        const ratingTd = document.createElement('td');
                        ratingTd.className = 'px-3 py-2 text-left';
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

                    standardsModal.dataset.currentIndex = idx;
                    standardsModal.classList.remove('hidden');
                    standardsModal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function handleAddStandard() {
                    if (!standardsModal || !standardsInput) return;

                    const idx = Number(standardsModal.dataset.currentIndex || 0);
                    if (!ratingSelectEl || !dimSelectEl) return;

                    const indicator = activeIndicators[idx];
                    if (!indicator) return;

                    const raw = standardsInput.value.trim();
                    if (!raw) return;

                    const rating = standardsEditTarget?.rating || ratingSelectEl.value;
                    const dim = standardsEditTarget?.dim || dimSelectEl.value;

                    const matrix = ensureIndicatorMatrix(indicator);
                    if (!matrix[rating]) matrix[rating] = { q:'', e:'', t:'' };
                    matrix[rating][dim] = raw;
                    indicator._matrix = matrix;
                    indicator.standards = standardsMatrixToArray(matrix);

                    standardsInput.value = '';
                    standardsEditTarget = null;

                    openStandardsModal(idx);
                }

                function closeStandardsModal() {
                    if (standardsModal) {
                        standardsModal.classList.add('hidden');
                        standardsModal.classList.remove('flex');
                    }
                    document.body.classList.remove('overflow-hidden');
                }

                // ===== Indicator CRUD =====
                function startEditIndicator(idx, currentValue) {
                    if (!indicatorsList) return;
                    const rows = Array.from(indicatorsList.children);
                    const targetRow = rows[idx];
                    if (!targetRow) return;

                    const indicator = activeIndicators[idx];
                    if (!indicator) return;

                    const firstTd = targetRow.querySelector('td');
                    if (!firstTd) return;

                    const input = document.createElement('input');
                    input.type = 'text';
                    input.placeholder = 'Enter Success Indicator...';
                    input.className = 'w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm ' +
                                      'text-slate-100 placeholder:text-slate-500 focus:border-blue-500 ' +
                                      'focus:ring-2 focus:ring-blue-500/40 focus:outline-none';
                    input.style.background = '#0f172a';
                    input.style.color = '#e5e7eb';
                    input.value = currentValue || '';

                    const prevAssignees = Array.isArray(indicator.assignees) ? [...indicator.assignees] : [];

                    firstTd.innerHTML = '';
                    firstTd.appendChild(input);
                    input.focus();
                    input.select();

                    const commit = () => {
                        const next = input.value.trim() || 'New success indicator';
                        indicator.text = next;
                        indicator.assignees = prevAssignees;

                        if (!indicator._matrix) {
                            indicator._matrix = seedStandardsForIndicator(next);
                            indicator.standards = standardsMatrixToArray(indicator._matrix);
                        }

                        renderIndicators(activeIndicators);
                    };

                    input.addEventListener('blur', commit);
                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            commit();
                        }
                    });
                }

                function deleteIndicator(idx) {
                    activeIndicators.splice(idx, 1);
                    renderIndicators(activeIndicators);
                }

                function addIndicator() {
                    activeIndicators.push(createIndicator('New success indicator'));
                    renderIndicators(activeIndicators);
                    startEditIndicator(activeIndicators.length - 1, 'New success indicator');
                }

                function openUwpIndicatorsModal(functionIndex, mfoIndex) {
                    const func = uwpState.functions[functionIndex];
                    const mfo = func?.mfos?.[mfoIndex];
                    if (!mfo || !indicatorsModal) return;

                    activeFunctionIndex = functionIndex;
                    activeMfoIndex = mfoIndex;
                    if (!Array.isArray(mfo.indicators)) mfo.indicators = [];
                    activeIndicators = mfo.indicators;

                    if (indicatorsTitle) indicatorsTitle.textContent = mfo.title || '--';
                    renderIndicators(activeIndicators);

                    indicatorsModal.classList.remove('hidden');
                    indicatorsModal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                window.closeUwpIndicatorsModal = function () {
                    if (indicatorsModal) {
                        indicatorsModal.classList.add('hidden');
                        indicatorsModal.classList.remove('flex');
                    }
                    activeFunctionIndex = null;
                    activeMfoIndex = null;
                    activeIndicators = [];
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
                    func.mfos.push(createMfo('', '', []));
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
                        mfos: (func.mfos || []).map((mfo) => ({
                            title: mfo.title,
                            target: mfo.target,
                            indicators: (mfo.indicators || []).map((indicator) => ({
                                text: indicator.text,
                                standards: getIndicatorStandardsArray(indicator),
                                assignees: Array.isArray(indicator.assignees) ? [...indicator.assignees] : [],
                            })),
                        })),
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
                                    target_timeline: null,
                                    standards,
                                };
                            }).filter(Boolean);

                            payload.push({
                                function_code: functionCode,
                                title: titleText,
                                target_summary: (mfo.target || '').trim(),
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
                                uwpState.functions[idx].type = target.value;
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
                if (addStandardBtn && isDraft) addStandardBtn.addEventListener('click', handleAddStandard);

                const resetStandardBtn = document.getElementById('uwp-reset-standard');
                if (resetStandardBtn && isDraft) {
                    resetStandardBtn.addEventListener('click', () => {
                        const idx = Number(standardsModal?.dataset.currentIndex || 0);
                        const indicator = activeIndicators[idx];
                        if (!indicator) return;
                        indicator._matrix = seedStandardsForIndicator(indicator.text || '');
                        indicator.standards = standardsMatrixToArray(indicator._matrix);
                        standardsEditTarget = null;
                        if (standardsInput) standardsInput.value = '';
                        openStandardsModal(idx);
                    });
                }

                if (saveAssignmentsBtn && isDraft) {
                    saveAssignmentsBtn.addEventListener('click', () => {
                        setButtonLoading(saveAssignmentsBtn, true, 'Saving...');
                        setTimeout(() => {
                            setButtonLoading(saveAssignmentsBtn, false);
                            closeAssignedModal();
                        }, 800);
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

                // ===== Close on backdrop + Escape =====
                standardsModal?.addEventListener('click', (e) => {
                    if (e.target === standardsModal) closeStandardsModal();
                });

                assignedModal?.addEventListener('click', (e) => {
                    if (e.target === assignedModal) closeAssignedModal();
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        if (isDraft && (activeRowConfirmId !== null || activeFunctionConfirmId !== null)) {
                            activeRowConfirmId = null;
                            activeFunctionConfirmId = null;
                            renderFunctions();
                        } else if (standardsModal && !standardsModal.classList.contains('hidden')) {
                            closeStandardsModal();
                        } else if (assignedModal && !assignedModal.classList.contains('hidden')) {
                            closeAssignedModal();
                        } else if (indicatorsModal && !indicatorsModal.classList.contains('hidden')) {
                            closeUwpIndicatorsModal();
                        } else {
                            closeModal();
                        }
                    }
                });

                // expose closers
                window.closeStandardsModal = closeStandardsModal;
                window.closeAssignedModal = closeAssignedModal;
            });
        </script>
    @endpush
@endsection
