
@extends('layouts.pmt')

@section('main-content')
<section class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">PMT Final OPCR Approval</h1>
            <p class="text-sm text-slate-400">Stage I - Performance Planning and Commitment</p>
            <p class="text-xs text-slate-500">Review Department Head-endorsed OPCRs and issue final approval to proceed to Stage II.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Active Period</p>
                <p class="text-sm font-semibold text-white">{{ $activePeriod?->name ?? '-' }}</p>
            </div>

            <form method="GET" action="{{ route('pmt.opcr.review.index') }}" class="flex items-center gap-2">
                <label for="opcr-status" class="text-xs uppercase tracking-wide text-slate-500">Status</label>
                <select id="opcr-status"
                        name="status"
                        onchange="this.form.submit()"
                        style="background:#0f172a;color:#e5e7eb;"
                        class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                    <option value="" {{ $selectedStatus === '' ? 'selected' : '' }}>All Status</option>
                    <option value="submitted" {{ $selectedStatus === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="endorsed" {{ $selectedStatus === 'endorsed' ? 'selected' : '' }}>Endorsed</option>
                    <option value="approved" {{ $selectedStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="returned" {{ $selectedStatus === 'returned' ? 'selected' : '' }}>Returned</option>

                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-300">
                <thead class="text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-2 text-left">Office / Unit</th>
                        <th class="px-4 py-2 text-left">Period</th>
                        <th class="px-4 py-2 text-left">Referenced UWP</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($opcrs as $opcr)
                        @php
                            $payload = $opcrPayloads[$opcr->id] ?? null;
                            $isReviewable = in_array(strtolower((string) $opcr->status), ['endorsed', 'for_pmt_review'], true);
                            $statusMeta = match (strtolower((string) $opcr->status)) {
                                'endorsed', 'for_pmt_review' => ['label' => 'Endorsed', 'class' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300'],
                                'approved' => ['label' => 'Approved', 'class' => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300'],
                                'returned' => ['label' => 'Returned', 'class' => 'border-rose-500/30 bg-rose-500/10 text-rose-300'],
                                default => ['label' => 'Submitted', 'class' => 'border-amber-500/30 bg-amber-500/20 text-amber-300'],
                            };
                        @endphp
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">{{ $opcr->unitWorkPlan?->office?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $opcr->unitWorkPlan?->performancePeriod?->name ?? '-' }}</td>
                            <td class="px-4 py-3">UWP #{{ $opcr->unit_work_plan_id ?? '-' }} ({{ ucwords(str_replace('_', ' ', (string) $opcr->unitWorkPlan?->status)) }})</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-open-review-opcr
                                        data-opcr='@json($payload)'
                                        class="text-blue-400 hover:text-blue-300 {{ $payload ? '' : 'opacity-60 pointer-events-none' }}"
                                        {{ $payload ? '' : 'disabled' }}>
                                    {{ $isReviewable ? 'Review' : 'View' }}
                                </button>
                                @unless ($isReviewable)
                                    <span class="ml-2 inline-flex items-center rounded-full border border-slate-600/60 bg-slate-700/30 px-2 py-0.5 text-[10px] font-semibold text-slate-300">Read-only</span>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No OPCR records found for PMT review.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="review-opcr-modal" data-modal-container class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Office Performance Commitment and Review</p>
                    <h2 id="dh-opcr-modal-title" class="mt-1 truncate text-lg font-semibold text-white">PMT Review OPCR -</h2>
                    <p class="text-sm text-slate-400">Derived from PMT-approved Unit Work Plan (Stage I)</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full border border-cyan-500/30 bg-cyan-500/10 px-2.5 py-1 text-[11px] font-semibold text-cyan-300">Final Approval</span>
                        <span id="dh-opcr-modal-status" class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold">-</span>
                    </div>
                </div>
                <button type="button" data-close-modal class="shrink-0 rounded-lg border border-slate-800 bg-slate-950/50 px-2.5 py-2 text-slate-400 hover:bg-slate-950 hover:text-white">&times;</button>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Office / Unit</p>
                    <p id="dh-opcr-modal-office" class="mt-1 text-sm font-semibold text-white">-</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Period</p>
                    <p id="dh-opcr-modal-period" class="mt-1 text-sm font-semibold text-white">-</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Referenced UWP</p>
                    <p id="dh-opcr-modal-uwp" class="mt-1 text-sm font-semibold text-white">-</p>
                </div>
            </div>

            <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/40 overflow-hidden">
                <div class="max-h-[46vh] overflow-auto">
                    <table class="w-full text-sm text-slate-200">
                        <thead class="sticky top-0 z-10 bg-slate-950 text-xs uppercase text-slate-300">
                            <tr class="border-b border-slate-800">
                                <th class="w-[30%] px-4 py-3 text-left">Output</th>
                                <th class="w-[12%] px-4 py-3 text-center">Success Indicators</th>
                                <th class="w-[26%] px-4 py-3 text-left">Target Summary</th>
                                <th class="w-[8%] px-4 py-3 text-left">Weight</th>
                                <th class="w-[12%] px-4 py-3 text-left">Function</th>
                            </tr>
                        </thead>
                        <tbody id="dh-opcr-outputs-tbody" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>
            <form id="dh-opcr-review-form" method="POST" action="{{ route('pmt.opcr.review.action') }}" class="mt-5">
                @csrf
                <input type="hidden" name="opcr_id" id="dh-opcr-id">
                <input type="hidden" name="action" id="dh-opcr-action">

                <div>
                    <label for="dh-opcr-remarks" class="mb-1 block text-sm text-slate-300">Remarks (required when returning)</label>
                    <textarea id="dh-opcr-remarks"
                              name="remarks"
                              rows="3"
                              style="background:#0f172a;color:#e5e7eb;"
                              class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"></textarea>
                    <p id="dh-opcr-remarks-error" class="mt-2 hidden text-[11px] text-rose-300">Remarks are required when returning the OPCR.</p>
                    <p class="mt-2 text-[11px] text-slate-500">Verify targets, weights, and indicator standards align with the PMT-approved UWP.</p>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-800 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-[11px] text-slate-500">Approve to finalize OPCR and proceed to Stage II; return sends it back for correction.</p>
                    <div class="flex flex-wrap justify-end gap-3">
                        <button type="button" data-close-modal class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Close</button>

                        <button type="button"
                                data-review-action="return"
                                data-loading-text="Returning..."
                                class="inline-flex items-center gap-2 rounded-lg border border-amber-600 px-4 py-2 text-sm font-semibold text-amber-300 hover:bg-amber-600/10">
                            <span data-button-label>Return</span>
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>

                        <button type="button"
                                data-review-action="approve"
                                data-loading-text="Approving..."
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                            <span data-button-label>Approve OPCR</span>
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="dh-indicators-modal" data-modal-container class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-4">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="dh-indicators-title" class="mt-1 truncate text-xl font-semibold text-white">--</h3>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/50 overflow-hidden">
                <div class="max-h-[55vh] overflow-auto">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-slate-900/70 text-xs uppercase text-slate-300">
                            <tr class="border-b border-slate-800">
                                <th class="w-[56%] px-4 py-3 text-left">Success Indicator</th>
                                <th class="w-[24%] px-4 py-3 text-left">Target Summary</th>
                                <th class="w-[20%] px-4 py-3 text-left">Standards</th>
                                <th class="w-[24%] px-4 py-3 text-left">Assigned Employee</th>
                            </tr>
                        </thead>
                        <tbody id="dh-indicators-table-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-5 py-2 text-sm text-slate-200 hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    <div id="dh-standards-modal" data-modal-container class="fixed inset-0 z-[91] hidden items-center justify-center bg-black/70 px-4 py-8">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                    <h3 id="dh-standards-title" class="text-lg font-semibold text-white">Target Difficulty / Standards</h3>
                    <p class="mt-1 text-[11px] text-slate-400">Indicator: <span id="dh-standards-indicator" class="font-semibold text-slate-200">--</span></p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
                <table class="w-full text-sm text-slate-100">
                    <thead class="bg-slate-900/70 text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-3 py-2 text-left">Rating</th>
                            <th class="px-3 py-2 text-left">Quality (Q)</th>
                            <th class="px-3 py-2 text-left">Efficiency (E)</th>
                            <th class="px-3 py-2 text-left">Timeliness (T)</th>
                        </tr>
                    </thead>
                    <tbody id="dh-standards-table-body" class="divide-y divide-slate-800"></tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end border-t border-slate-800 pt-3">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    <div id="dh-indicator-assignee-modal" data-modal-container class="fixed inset-0 z-[92] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employees</p>
                    <h3 id="dh-assignee-title" class="text-lg font-semibold text-white">--</h3>
                    <p class="mt-1 text-[11px] text-slate-400">Indicator: <span id="dh-assignee-indicator" class="font-semibold text-slate-200">--</span></p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-800 bg-slate-950/70">
                <table class="w-full text-sm text-slate-100">
                    <thead class="bg-slate-900/70 text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee Name</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody id="dh-assignee-body" class="divide-y divide-slate-800"></tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ratingLevels = [5, 4, 3, 2, 1];
    let selectedOpcr = null;

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const formatTargetSummaryDisplay = (targetQuantity, targetSummary) => {
        const quantity = targetQuantity === null || targetQuantity === undefined || targetQuantity === ''
            ? ''
            : String(targetQuantity).trim();
        const summary = targetSummary === null || targetSummary === undefined || targetSummary === ''
            ? ''
            : String(targetSummary).trim();

        if (summary.toLowerCase() === 'multiple indicator targets') {
            return summary;
        }

        if (quantity !== '' && summary !== '') {
            return `${quantity} ${summary}`.trim();
        }

        if (quantity !== '') {
            return quantity;
        }

        if (summary !== '') {
            return summary;
        }

        return '-';
    };

    const getIndicatorTargetSummary = (indicator) => formatTargetSummaryDisplay(
        indicator?.target_quantity,
        indicator?.target_timeline
    );

    const parseJson = (raw) => {
        try { return JSON.parse(raw); } catch (e) { return null; }
    };

    const openModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        const anyOpen = Array.from(document.querySelectorAll('[data-modal-container]'))
            .some((node) => !node.classList.contains('hidden'));

        if (!anyOpen) {
            document.body.classList.remove('overflow-hidden');
        }
    };

    const opcrStatusMeta = (status) => {
        const key = String(status || '').toLowerCase();
        if (key === 'endorsed' || key === 'for_pmt_review') return { label: 'For PMT Review', cls: 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300' };
        if (key === 'approved') return { label: 'Final Approved', cls: 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300' };
        if (key === 'returned') return { label: 'Returned', cls: 'border-rose-500/30 bg-rose-500/10 text-rose-300' };
        return { label: 'Submitted', cls: 'border-amber-500/30 bg-amber-500/20 text-amber-300' };
    };

    const functionBadge = (functionType) => {
        const type = String(functionType || '').toLowerCase();
        if (type === 'core') return '<span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">Core</span>';
        if (type === 'support') return '<span class="rounded-md bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-300 border border-blue-400/30">Support</span>';
        return '<span class="rounded-md bg-slate-500/10 px-2 py-1 text-xs font-medium text-slate-300 border border-slate-500/20">' + escapeHtml(type || 'Custom') + '</span>';
    };

    const setButtonLoading = (button, loading, loadingText) => {
        if (!button) return;
        const label = button.querySelector('[data-button-label]');
        const spinner = button.querySelector('[data-button-spinner]');

        if (loading) {
            if (label) {
                if (!button.dataset.originalLabel) {
                    button.dataset.originalLabel = label.textContent || '';
                }
                label.textContent = loadingText || 'Processing...';
            }
            if (spinner) spinner.classList.remove('hidden');
            button.disabled = true;
            button.classList.add('opacity-70', 'cursor-wait');
            return;
        }

        if (label && button.dataset.originalLabel) {
            label.textContent = button.dataset.originalLabel;
        }
        if (spinner) spinner.classList.add('hidden');
        button.disabled = false;
        button.classList.remove('opacity-70', 'cursor-wait');
    };

    const renderOutputs = (outputs) => {
        const tbody = document.getElementById('dh-opcr-outputs-tbody');
        if (!tbody) return;
        tbody.innerHTML = '';

        (Array.isArray(outputs) ? outputs : []).forEach((output) => {
            const indicators = Array.isArray(output.success_indicators) ? output.success_indicators : [];
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-3 align-top text-white">${escapeHtml(output.title || '-')}</td>
                <td class="px-4 py-3 align-top text-center">
                    <button type="button" class="inline-flex items-center justify-center gap-2 text-blue-300 hover:text-blue-200" data-indicators-btn>
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span class="text-xs">(${indicators.length})</span>
                    </button>
                </td>
                <td class="px-4 py-3 align-top text-slate-200">${escapeHtml(formatTargetSummaryDisplay(output.target_quantity, output.target_summary))}</td>
                <td class="px-4 py-3 align-top text-slate-200">${output.weight_percent !== null && output.weight_percent !== undefined && output.weight_percent !== '' ? escapeHtml(String(output.weight_percent) + '%') : '-'}</td>
                <td class="px-4 py-3 align-top">${functionBadge(output.function_type)}</td>
            `;

            tr.querySelector('[data-indicators-btn]')?.addEventListener('click', () => {
                openIndicatorsModal(output.title || '--', indicators);
            });

            tbody.appendChild(tr);
        });

        if (!tbody.children.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">No OPCR outputs found.</td></tr>';
        }
    };

    const renderStandardsModal = (mfoTitle, indicatorText, standardsByRating) => {
        document.getElementById('dh-standards-title').textContent = mfoTitle || '--';
        document.getElementById('dh-standards-indicator').textContent = indicatorText || '--';

        const tbody = document.getElementById('dh-standards-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        const renderCell = (items) => {
            const values = Array.isArray(items) ? items : [];
            if (!values.length) return '-';
            return '<ul class="list-disc space-y-1 pl-4">' + values.map((item) => '<li>' + escapeHtml(item) + '</li>').join('') + '</ul>';
        };

        ratingLevels.forEach((rating) => {
            const row = standardsByRating?.[String(rating)] || standardsByRating?.[rating] || { Q: [], E: [], T: [] };
            const q = row.Q ?? row.q ?? [];
            const e = row.E ?? row.e ?? [];
            const t = row.T ?? row.t ?? [];

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-3 py-2 text-left font-semibold text-white">${rating}</td>
                <td class="px-3 py-2 align-top">${renderCell(q)}</td>
                <td class="px-3 py-2 align-top">${renderCell(e)}</td>
                <td class="px-3 py-2 align-top">${renderCell(t)}</td>
            `;
            tbody.appendChild(tr);
        });

        openModal('dh-standards-modal');
    };

    const renderAssigneeModal = (mfoTitle, indicatorText, assignees) => {
        document.getElementById('dh-assignee-title').textContent = mfoTitle || '--';
        document.getElementById('dh-assignee-indicator').textContent = indicatorText || '--';

        const tbody = document.getElementById('dh-assignee-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        const unitName = selectedOpcr?.opcr?.office?.name || '-';
        const names = Array.isArray(assignees) ? assignees : [];

        if (!names.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">No assigned employees.</td></tr>';
            openModal('dh-indicator-assignee-modal');
            return;
        }

        names.forEach((name) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-2">${escapeHtml(name)}</td>
                <td class="px-4 py-2">${escapeHtml(unitName)}</td>
                <td class="px-4 py-2"><span class="inline-flex items-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2 py-1 text-[11px] font-semibold text-emerald-200">Assigned</span></td>
            `;
            tbody.appendChild(tr);
        });

        openModal('dh-indicator-assignee-modal');
    };

    const openIndicatorsModal = (mfoTitle, indicators) => {
        document.getElementById('dh-indicators-title').textContent = mfoTitle || '--';

        const tbody = document.getElementById('dh-indicators-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        (Array.isArray(indicators) ? indicators : []).forEach((indicator) => {
            const indicatorText = indicator?.indicator_text || '-';
            const standards = indicator?.standards_by_rating || {};
            const assignees = Array.isArray(indicator?.assignees) ? indicator.assignees : [];

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-4 py-3 align-top text-slate-100">${escapeHtml(indicatorText)}</td>
                <td class="px-4 py-3 align-top text-slate-300">${escapeHtml(getIndicatorTargetSummary(indicator))}</td>
                <td class="px-4 py-3 align-top">
                    <button type="button" class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200" data-standards-btn>
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>View Standards</span>
                    </button>
                </td>
                <td class="px-4 py-3 align-top">
                    <button type="button" class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200" data-assignee-btn>
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <span>View (${assignees.length})</span>
                    </button>
                </td>
            `;

            tr.querySelector('[data-standards-btn]')?.addEventListener('click', () => {
                renderStandardsModal(mfoTitle, indicatorText, standards);
            });

            tr.querySelector('[data-assignee-btn]')?.addEventListener('click', () => {
                renderAssigneeModal(mfoTitle, indicatorText, assignees);
            });

            tbody.appendChild(tr);
        });

        if (!tbody.children.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No indicators found.</td></tr>';
        }

        openModal('dh-indicators-modal');
    };

    const hydrateReviewModal = (payload) => {
        selectedOpcr = payload;

        const office = payload?.opcr?.office?.name || '-';
        const period = payload?.opcr?.period?.name || '-';
        const opcrStatus = String(payload?.opcr?.status || '').toLowerCase();
        const sourceUwpId = payload?.opcr?.source_uwp?.id || '-';
        const sourceUwpStatus = payload?.opcr?.source_uwp?.status || '-';

        document.getElementById('dh-opcr-modal-title').textContent = `PMT Review OPCR - ${office}`;
        document.getElementById('dh-opcr-modal-office').textContent = office;
        document.getElementById('dh-opcr-modal-period').textContent = period;
        document.getElementById('dh-opcr-modal-uwp').textContent = `UWP #${sourceUwpId} (${String(sourceUwpStatus).replaceAll('_', ' ')})`;

        const statusEl = document.getElementById('dh-opcr-modal-status');
        const meta = opcrStatusMeta(opcrStatus);
        statusEl.textContent = meta.label;
        statusEl.className = `inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold ${meta.cls}`;

        const remarks = document.getElementById('dh-opcr-remarks');
        const remarksError = document.getElementById('dh-opcr-remarks-error');
        const opcrId = document.getElementById('dh-opcr-id');
        const actionInput = document.getElementById('dh-opcr-action');

        if (remarks) remarks.value = '';
        if (remarksError) remarksError.classList.add('hidden');
        if (opcrId) opcrId.value = payload?.opcr?.id || '';
        if (actionInput) actionInput.value = '';

        const canReview = opcrStatus === 'endorsed' || opcrStatus === 'for_pmt_review';

        document.querySelectorAll('[data-review-action]').forEach((btn) => {
            setButtonLoading(btn, false);
            btn.classList.remove('opacity-70', 'cursor-wait');
            btn.disabled = !canReview;

            if (!canReview) {
                btn.classList.add('opacity-60', 'pointer-events-none');
            } else {
                btn.classList.remove('opacity-60', 'pointer-events-none');
            }
        });

        renderOutputs(payload?.outputs || []);
    };

    document.querySelectorAll('[data-open-review-opcr]').forEach((button) => {
        button.addEventListener('click', () => {
            const payload = parseJson(button.getAttribute('data-opcr') || 'null');
            if (!payload) return;
            hydrateReviewModal(payload);
            openModal('review-opcr-modal');
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            closeModal(button.closest('[data-modal-container]'));
        });
    });

    document.querySelectorAll('[data-modal-container]').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const openModals = Array.from(document.querySelectorAll('[data-modal-container]')).filter((modal) => !modal.classList.contains('hidden'));
        if (!openModals.length) return;
        closeModal(openModals[openModals.length - 1]);
    });

    const reviewForm = document.getElementById('dh-opcr-review-form');
    const remarksEl = document.getElementById('dh-opcr-remarks');
    const remarksErrorEl = document.getElementById('dh-opcr-remarks-error');
    const actionInput = document.getElementById('dh-opcr-action');

    document.querySelectorAll('[data-review-action]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!reviewForm || !actionInput) return;

            const action = button.getAttribute('data-review-action');
            actionInput.value = action || '';

            const opcrId = document.getElementById('dh-opcr-id')?.value || '';
            if (!opcrId) return;

            if (remarksErrorEl) remarksErrorEl.classList.add('hidden');

            if (action === 'return') {
                const remarks = (remarksEl?.value || '').trim();
                if (!remarks) {
                    if (remarksErrorEl) remarksErrorEl.classList.remove('hidden');
                    remarksEl?.focus();
                    return;
                }
            }

            const loadingText = button.getAttribute('data-loading-text') || 'Processing...';
            setButtonLoading(button, true, loadingText);

            document.querySelectorAll('[data-review-action]').forEach((peer) => {
                if (peer !== button) {
                    peer.disabled = true;
                    peer.classList.add('opacity-70', 'cursor-wait');
                }
            });

            reviewForm.submit();
        });
    });
});
</script>
@endpush
@endsection
