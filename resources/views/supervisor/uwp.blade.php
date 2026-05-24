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

        // Map persisted MFO ids to the dedicated Success Indicators page.
        $mfoSuccessIndicatorUrls = [];
        if (!empty($selectedUwpId) && !empty($initialFunctions) && is_array($initialFunctions)) {
            foreach ($initialFunctions as $func) {
                foreach (($func['mfos'] ?? []) as $mfo) {
                    $mfoId = (int) ($mfo['id'] ?? 0);
                    if ($mfoId > 0) {
                        $mfoSuccessIndicatorUrls[$mfoId] = route('supervisor.uwp.success-indicators', [
                            'uwpId' => (int) $selectedUwpId,
                            'mfoId' => $mfoId,
                        ]);
                    }
                }
            }
        }
    @endphp
@section('main-content')
    <section class="space-y-6">
        <div>
            <a href="{{ route('supervisor.uwp-page') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-300">
                â† Back to Unit Work Plans
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

            <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-5 shadow-sm space-y-6">
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
                        <span class="text-[10px] text-slate-500">Draft/Returned: editable Â· Submitted: read-only</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <span class="text-xs uppercase tracking-wide text-slate-400">Office / Unit</span>

                    @if(auth()->user()->role === 'supervisor')
                        <!-- Supervisors: Show their assigned office as plain text -->
                        <div class="w-full rounded-lg border border-gray-700 bg-slate-900/40 px-3 py-2 text-sm text-slate-100">
                            {{ auth()->user()->office->name ?? 'No office assigned' }}
                        </div>
                        <!-- Hidden field to submit the office_id -->
                        <input type="hidden" name="office_id" value="{{ $selectedOfficeId }}">
                    @else
                        <!-- Admins/Dept heads: Still show dropdown -->
                        <select
                            id="uwp-office-unit"
                            name="office_id"
                            class="w-full rounded-lg border border-gray-700 bg-slate-950 px-3 py-2
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
                        class="w-full rounded-lg border border-gray-700 bg-slate-950 px-3 py-2
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

            <div class="sticky bottom-0 z-30 -mx-5 mt-6 border-t border-gray-700 bg-slate-950/95 px-5 py-4 backdrop-blur">
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

    {{-- Success Indicator workspace migrated to a dedicated page. --}}

    {{-- GENERIC ACTION MODAL (unchanged) --}}
    <div id="employee-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-gray-700 bg-slate-900 p-5 shadow-xl">
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

                // Success Indicator workspace UI moved to a dedicated page.

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

                const mfoSuccessIndicatorUrls = @json($mfoSuccessIndicatorUrls ?? []);
                const successIndicatorsUrlTemplate = @json(route('supervisor.uwp.success-indicators', ['uwpId' => '__UWPID__', 'mfoId' => '__MFOID__']));
                
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
                        3: { q: ['Few minor errors'], e: ['95â€“99% processed'], t: ['End of working day'] },
                        2: { q: ['Multiple errors'], e: ['<95% processed'], t: ['Beyond working day'] },
                        1: { q: ['Major errors/missing'], e: ['Majority unprocessed'], t: ['Not within acceptable time'] },
                    },
                    'Indexing complete with no missing pages': {
                        5: { q: ['Indexing fully verified, zero gaps'], e: ['100% pages indexed'], t: ['Same day'] },
                        4: { q: ['Indexing minor rechecks'], e: ['100% pages indexed'], t: ['Same day'] },
                        3: { q: ['Occasional missing indexes fixed'], e: ['95â€“99% indexed'], t: ['Within 24 hours'] },
                        2: { q: ['Frequent missing pages'], e: ['<95% indexed'], t: ['Beyond 24 hours'] },
                        1: { q: ['Indexing largely incomplete'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Audit trail maintained within 24 hours': {
                        5: { q: ['Complete trail, no errors'], e: ['100% entries captured'], t: ['Within 24 hours'] },
                        4: { q: ['Minor corrections only'], e: ['100% entries captured'], t: ['Within 24 hours'] },
                        3: { q: ['Some gaps corrected'], e: ['95â€“99% entries captured'], t: ['Within 48 hours'] },
                        2: { q: ['Multiple missing logs'], e: ['<95% captured'], t: ['Beyond 48 hours'] },
                        1: { q: ['Trail missing'], e: ['Majority uncaptured'], t: ['Unacceptable'] },
                    },
                    'Same-day verification of OTC transactions': {
                        5: { q: ['Verified without discrepancies'], e: ['100% OTC verified'], t: ['Same working day'] },
                        4: { q: ['Minor verifications pending'], e: ['100% OTC verified'], t: ['Same working day'] },
                        3: { q: ['Few pending verifications'], e: ['95â€“99% verified'], t: ['End of working day'] },
                        2: { q: ['Several unverified'], e: ['<95% verified'], t: ['Beyond working day'] },
                        1: { q: ['Verification not done'], e: ['Majority unverified'], t: ['Unacceptable'] },
                    },
                    '95% encoded within the business day': {
                        5: { q: ['Encodings error-free'], e: ['100% encoded'], t: ['Same business day'] },
                        4: { q: ['Minor corrections'], e: ['100% encoded'], t: ['Same business day'] },
                        3: { q: ['Few delays'], e: ['95â€“99% encoded'], t: ['By end of day'] },
                        2: { q: ['Multiple delays'], e: ['<95% encoded'], t: ['Next day'] },
                        1: { q: ['Encoding largely incomplete'], e: ['Major backlog'], t: ['Unacceptable'] },
                    },
                    'OR validation completed daily': {
                        5: { q: ['All ORs validated error-free'], e: ['100% validated'], t: ['Daily'] },
                        4: { q: ['Minor issues corrected same day'], e: ['100% validated'], t: ['Daily'] },
                        3: { q: ['Some validations late'], e: ['95â€“99% validated'], t: ['Within 48 hours'] },
                        2: { q: ['Frequent late validations'], e: ['<95% validated'], t: ['Beyond 48 hours'] },
                        1: { q: ['Validations mostly missing'], e: ['Majority unvalidated'], t: ['Unacceptable'] },
                    },
                    'Weekly filing updated and retrievable': {
                        5: { q: ['Zero retrieval issues'], e: ['100% weekly updates'], t: ['Within week'] },
                        4: { q: ['Minor retrieval fixes'], e: ['100% weekly updates'], t: ['Within week'] },
                        3: { q: ['Some items late'], e: ['95â€“99% updates'], t: ['Within next week'] },
                        2: { q: ['Many late updates'], e: ['<95% updates'], t: ['Beyond next week'] },
                        1: { q: ['Updates not done'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Digital backups synced monthly': {
                        5: { q: ['Backups verified'], e: ['100% synced'], t: ['Within month'] },
                        4: { q: ['Minor sync corrections'], e: ['100% synced'], t: ['Within month'] },
                        3: { q: ['Some delays'], e: ['95â€“99% synced'], t: ['Within following week'] },
                        2: { q: ['Frequent delays'], e: ['<95% synced'], t: ['Beyond following week'] },
                        1: { q: ['Backups largely missing'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Retrieval logs maintained for audits': {
                        5: { q: ['Logs complete and audit-ready'], e: ['100% requests logged'], t: ['Same day'] },
                        4: { q: ['Minor log gaps corrected'], e: ['100% requests logged'], t: ['Same day'] },
                        3: { q: ['Some gaps'], e: ['95â€“99% logged'], t: ['Within 48 hours'] },
                        2: { q: ['Many gaps'], e: ['<95% logged'], t: ['Beyond 48 hours'] },
                        1: { q: ['Logs largely missing'], e: ['Majority unlogged'], t: ['Unacceptable'] },
                    },
                    'All daily collections posted to the ledger within the day': {
                        5: { q: ['Zero posting errors; entries accurate'], e: ['100% posted'], t: ['Same working day'] },
                        4: { q: ['Minor corrections only'], e: ['100% posted'], t: ['Same working day'] },
                        3: { q: ['Few correctable errors'], e: ['95â€“99% posted'], t: ['By end of day'] },
                        2: { q: ['Multiple errors requiring rework'], e: ['<95% posted'], t: ['Next day'] },
                        1: { q: ['Major inaccuracies'], e: ['Major backlog'], t: ['Unacceptable delay'] },
                    },
                    'Daily totals reconciled against validated ORs': {
                        5: { q: ['Reconciled with zero variance'], e: ['All ORs included'], t: ['Same day'] },
                        4: { q: ['Minor variance resolved'], e: ['All ORs included'], t: ['Same day'] },
                        3: { q: ['Some variances corrected'], e: ['95â€“99% ORs included'], t: ['Within 24 hours'] },
                        2: { q: ['Frequent variances'], e: ['<95% ORs included'], t: ['Beyond 24 hours'] },
                        1: { q: ['Not reconciled'], e: ['Majority missing'], t: ['Unacceptable'] },
                    },
                    'Posting errors corrected within 24 hours': {
                        5: { q: ['All corrections documented'], e: ['100% corrected'], t: ['Within 24 hours'] },
                        4: { q: ['Minor corrections documented'], e: ['100% corrected'], t: ['Within 24 hours'] },
                        3: { q: ['Some corrections delayed'], e: ['95â€“99% corrected'], t: ['Within 48 hours'] },
                        2: { q: ['Many corrections delayed'], e: ['<95% corrected'], t: ['Beyond 48 hours'] },
                        1: { q: ['Corrections not done'], e: ['Majority pending'], t: ['Unacceptable'] },
                    },
                    'Monthly revenue report prepared with complete schedules': {
                        5: { q: ['Complete schedules, no gaps'], e: ['All sections included'], t: ['Within 3 working days'] },
                        4: { q: ['Minor schedule tweaks'], e: ['All sections included'], t: ['Within 3 working days'] },
                        3: { q: ['Some missing items fixed'], e: ['95â€“99% complete'], t: ['Within 5 working days'] },
                        2: { q: ['Many missing schedules'], e: ['<95% complete'], t: ['Beyond 5 working days'] },
                        1: { q: ['Report incomplete'], e: ['Majority missing'], t: ['Unacceptable'] },
                    },
                    'Report figures match the ledger and subsidiary records': {
                        5: { q: ['Exact match, no variance'], e: ['All reconciled'], t: ['Before submission'] },
                        4: { q: ['Minor variance resolved'], e: ['All reconciled'], t: ['Before submission'] },
                        3: { q: ['Few variances corrected'], e: ['95â€“99% reconciled'], t: ['At submission'] },
                        2: { q: ['Frequent variances'], e: ['<95% reconciled'], t: ['After submission'] },
                        1: { q: ['Not reconciled'], e: ['Majority not reconciled'], t: ['Unacceptable'] },
                    },
                    'Report submitted on or before deadline': {
                        5: { q: ['Submission complete'], e: ['All attachments included'], t: ['On/before deadline'] },
                        4: { q: ['Minor attachment fixes'], e: ['All included'], t: ['On/before deadline'] },
                        3: { q: ['Late minor attachment'], e: ['95â€“99% included'], t: ['1 day late'] },
                        2: { q: ['Several missing attachments'], e: ['<95% included'], t: ['2â€“3 days late'] },
                        1: { q: ['Not submitted/very late'], e: ['Majority missing'], t: ['Unacceptable'] },
                    },
                    'Audit request documents compiled complete and accurate': {
                        5: { q: ['Complete packet, error-free'], e: ['All requested docs included'], t: ['Within 2 working days'] },
                        4: { q: ['Minor formatting fixes'], e: ['All included'], t: ['Within 2 working days'] },
                        3: { q: ['Some missing docs recovered'], e: ['95â€“99% included'], t: ['Within 3 working days'] },
                        2: { q: ['Many missing docs'], e: ['<95% included'], t: ['Beyond 3 working days'] },
                        1: { q: ['Packet incomplete'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Verification responses issued within 2 working days': {
                        5: { q: ['Clear, accurate response'], e: ['All queries answered'], t: ['Within 2 working days'] },
                        4: { q: ['Minor clarifications'], e: ['All answered'], t: ['Within 2 working days'] },
                        3: { q: ['Some clarifications needed'], e: ['95â€“99% answered'], t: ['Within 3 working days'] },
                        2: { q: ['Many clarifications needed'], e: ['<95% answered'], t: ['Beyond 3 working days'] },
                        1: { q: ['Responses inadequate'], e: ['Majority unanswered'], t: ['Unacceptable'] },
                    },
                    'Follow-up clarifications resolved within 3 working days': {
                        5: { q: ['Resolved fully with evidence'], e: ['All follow-ups closed'], t: ['Within 3 working days'] },
                        4: { q: ['Minor evidence follow-up'], e: ['All closed'], t: ['Within 3 working days'] },
                        3: { q: ['Some follow-ups delayed'], e: ['95â€“99% closed'], t: ['Within 5 working days'] },
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
                                            class="w-full rounded-lg border border-gray-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none ${mutedClass}"
                                            style="background:#0f172a;color:#e5e7eb;"
                                            placeholder="e.g., Records management and archiving"
                                            ${inputDisabled}>
                                    </td>

                                    <td class="px-4 py-4 text-center">
                                        ${(() => {
                                            const persistedId = Number(mfo?.id || mfo?.mfo_id || mfo?.uwp_mfo_id || 0);
                                            const url = persistedId ? (mfoSuccessIndicatorUrls[String(persistedId)] || mfoSuccessIndicatorUrls[persistedId]) : '';
                                            const label = `${indicatorCount} indicator${indicatorCount === 1 ? '' : 's'}`;
                                            if (url) {
                                                return `
                                                    <a
                                                        href="${url}"
                                                        class="inline-flex items-center rounded-full border border-slate-700 bg-slate-900 px-3 py-1 text-xs font-semibold text-slate-300 transition hover:bg-slate-700/40 hover:border-slate-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/60">
                                                        ${label}
                                                    </a>
                                                `;
                                            }
                                            return `
                                                <button type="button"
                                                    data-action="open-indicators"
                                                    data-function-index="${funcIndex}"
                                                    data-mfo-index="${mfoIndex}"
                                                    title="Save draft & manage success indicators"
                                                    class="inline-flex items-center gap-1 rounded-full border border-cyan-700/40 bg-slate-950 px-3 py-1 text-xs font-semibold text-cyan-400 transition hover:bg-cyan-500/10 hover:border-cyan-500/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 cursor-pointer">
                                                    ${label}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                                </button>
                                            `;
                                        })()}
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
                            <div data-function-card data-function-index="${funcIndex}" class="group scroll-mt-24 rounded-2xl border border-gray-700 bg-slate-900/40 p-6 shadow-sm space-y-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <input type="text"
                                                data-function-title
                                                data-function-index="${funcIndex}"
                                                value="${escapeHtml(func.title)}"
                                                class="rounded-lg border border-gray-700 bg-slate-950 px-3 py-2 text-lg font-semibold text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none ${mutedClass}"
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
                                            class="rounded-lg border border-gray-700 bg-slate-950 px-3 py-2 text-xs text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none ${mutedClass}"
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
                                            class="w-24 rounded-lg border border-gray-700 bg-slate-950 px-3 py-2 text-xs text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none ${mutedClass}"
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

                                <div class="relative rounded-xl overflow-hidden border border-gray-700 bg-slate-900/40">
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

                /**
                 * Auto-save the UWP draft via AJAX, then navigate to the
                 * success-indicators page for the given function/MFO row.
                 */
                async function saveDraftAndOpenIndicators(btn, funcIndex, mfoIndex) {
                    if (btn) {
                        btn.dataset.originalText = btn.textContent.trim();
                        btn.disabled = true;
                        btn.textContent = 'Saving...';
                        btn.classList.add('opacity-60', 'cursor-wait');
                    }

                    try {
                        const formData = new FormData(uwpForm);
                        if (selectedUwpId && !formData.has('uwp_id')) {
                            formData.append('uwp_id', String(selectedUwpId));
                        }

                        formData.set('functions_payload', JSON.stringify(buildFunctionsPayload()));
                        formData.set('mfos_payload', JSON.stringify(buildMfosPayloadFromState()));
                        formData.set('assignments_payload', JSON.stringify(buildAssignmentsPayloadMvp()));

                        const csrfToken = formData.get('_token') || document.querySelector('meta[name="csrf-token"]')?.content || '';

                        const saveDraftUrl = selectedUwpId
                            ? @json(route('supervisor.uwp.saveDraftData.byId', ['id' => '__ID__'])).replace('__ID__', selectedUwpId)
                            : @json(route('supervisor.uwp.saveDraftData'));

                        const response = await fetch(saveDraftUrl, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: formData,
                        });

                        const json = await response.json();

                        if (!response.ok || (json.hasOwnProperty('success') && !json.success)) {
                            let errorMsg = json.error || json.message || 'Failed to save draft.';
                            if (json.errors) {
                                errorMsg += '\n' + Object.values(json.errors).map(e => e.join('\n')).join('\n');
                            }
                            alert(errorMsg);
                            if (btn) {
                                btn.disabled = false;
                                btn.classList.remove('opacity-60', 'cursor-wait');
                                btn.textContent = btn.dataset.originalText || '0 indicators';
                            }
                            return;
                        }

                        const uwpId = json.uwp_id;
                        const mfoIds = Array.isArray(json.mfo_ids) ? json.mfo_ids : [];

                        let globalIdx = 0;
                        for (let f = 0; f < uwpState.functions.length; f++) {
                            if (f === funcIndex) {
                                globalIdx += mfoIndex;
                                break;
                            }
                            globalIdx += (uwpState.functions[f].mfos || []).length;
                        }

                        const mfoId = mfoIds[globalIdx];
                        if (!mfoId) {
                            window.location.href = window.location.pathname + '?uwp_id=' + uwpId;
                            return;
                        }

                        let targetUrl = successIndicatorsUrlTemplate;
                        targetUrl = targetUrl.replace('__UWPID__', uwpId).replace('__MFOID__', mfoId);
                        window.location.href = targetUrl;
                        
                    } catch (err) {
                        console.error('saveDraftAndOpenIndicators error:', err);
                        alert('An error occurred while saving. Please try again.');
                        if (btn) {
                            btn.disabled = false;
                            btn.classList.remove('opacity-60', 'cursor-wait');
                            btn.textContent = btn.dataset.originalText || '0 indicators';
                        }
                    }
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

                        const openIndicatorsBtn = event.target.closest('[data-action="open-indicators"]');
                        if (openIndicatorsBtn) {
                            const funcIdx = Number(openIndicatorsBtn.dataset.functionIndex);
                            const mfoIdx = Number(openIndicatorsBtn.dataset.mfoIndex);
                            saveDraftAndOpenIndicators(openIndicatorsBtn, funcIdx, mfoIdx);
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

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        if (isDraft && (activeRowConfirmId !== null || activeFunctionConfirmId !== null)) {
                            activeRowConfirmId = null;
                            activeFunctionConfirmId = null;
                            renderFunctions();
                        } else {
                            closeModal();
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
