@extends('layouts.pmt')

@section('main-content')
@php
    $statusKey = strtolower((string) ($payload['opcr']['status'] ?? ''));
    $statusMeta = match($statusKey) {
        'endorsed', 'for_pmt_review' => ['label' => 'Endorsed', 'class' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300'],
        'approved' => ['label' => 'Approved', 'class' => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300'],
        'returned' => ['label' => 'Returned', 'class' => 'border-rose-500/30 bg-rose-500/10 text-rose-300'],
        default => ['label' => ucwords(str_replace('_', ' ', $statusKey ?: 'submitted')), 'class' => 'border-amber-500/30 bg-amber-500/20 text-amber-300'],
    };
@endphp

<section class="space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">PMT OPCR Review</h1>
            <p class="text-sm text-slate-400">Final review workspace for consolidated office outputs.</p>
        </div>
        <a href="{{ route('pmt.opcr.review.index') }}"
           class="rounded-lg border border-slate-700 bg-slate-950/50 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-900">
            Back to OPCR Review
        </a>
    </div>

    <div class="grid gap-3 rounded-2xl border border-slate-800 bg-slate-900/70 p-4 sm:grid-cols-4">
        <div>
            <p class="text-[11px] uppercase tracking-wide text-slate-500">Office / Unit</p>
            <p class="mt-1 text-sm font-semibold text-white">{{ $payload['opcr']['office']['name'] ?? '-' }}</p>
        </div>
        <div>
            <p class="text-[11px] uppercase tracking-wide text-slate-500">Period</p>
            <p class="mt-1 text-sm font-semibold text-white">{{ $payload['opcr']['period']['name'] ?? '-' }}</p>
        </div>
        <div>
            <p class="text-[11px] uppercase tracking-wide text-slate-500">Referenced UWP</p>
            <p class="mt-1 text-sm font-semibold text-white">{{ $payload['opcr']['source_uwp']['id'] ?? '-' }}</p>
        </div>
        <div>
            <p class="text-[11px] uppercase tracking-wide text-slate-500">Status</p>
            <span class="mt-1 inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
        </div>
    </div>

    <div class="grid min-h-0 gap-0 overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 lg:grid-cols-[320px_minmax(0,1fr)]">
        <aside class="flex min-h-0 flex-col border-b border-slate-800 lg:border-b-0 lg:border-r">
            <div class="flex h-14 items-center justify-between border-b border-slate-800 px-4">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Consolidated Outputs</p>
                <span id="pmt-output-count" class="text-sm font-semibold text-blue-300">0</span>
            </div>
            <div class="flex h-10 items-end border-b border-slate-800 px-2">
                <button type="button" data-fn-filter="all" class="flex-1 border-b-2 border-blue-400 px-2.5 py-2.5 text-sm font-semibold text-white">All</button>
                <button type="button" data-fn-filter="core" class="flex-1 border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Core</button>
                <button type="button" data-fn-filter="support" class="flex-1 border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Support</button>
            </div>
            <div id="pmt-output-list" class="min-h-0 space-y-2 overflow-y-auto px-2 py-2"></div>
        </aside>

        <section class="flex min-h-0 flex-col">
            <div class="flex h-14 items-center border-b border-slate-800 px-6">
                <div class="flex flex-wrap items-center gap-3">
                    <h3 id="pmt-detail-title" class="text-lg font-semibold leading-tight text-white">No output selected</h3>
                    <span id="pmt-detail-function" class="hidden rounded-md border px-2 py-1 text-xs font-medium"></span>
                    <span id="pmt-detail-weight" class="hidden text-sm font-semibold text-slate-300"></span>
                </div>
            </div>

            <div class="px-5">
                <div class="flex h-10 items-end gap-1.5 border-b border-slate-800">
                    <button type="button" data-panel-tab="overview" class="border-b-2 border-blue-400 px-2.5 py-2.5 text-sm font-semibold text-white">Overview</button>
                    <button type="button" data-panel-tab="indicators" class="border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Success Indicators</button>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto px-6 py-4">
                <div data-panel="overview" class="space-y-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Linked Success Indicators</p>
                    <div id="pmt-overview-indicators" class="space-y-2.5"></div>
                </div>

                <div data-panel="indicators" class="hidden">
                    <div class="overflow-hidden rounded-xl border border-slate-800">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.22em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Success Indicator</th>
                                    <th class="px-4 py-3 text-left">Target Summary</th>
                                    <th class="px-4 py-3 text-center">Standards</th>
                                    <th class="px-4 py-3 text-center">Assigned</th>
                                </tr>
                            </thead>
                            <tbody id="pmt-indicators-body" class="divide-y divide-slate-800"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <form id="pmt-opcr-show-form" method="POST" action="{{ route('pmt.opcr.review.action') }}" class="grid gap-3 rounded-2xl border border-slate-800 bg-slate-900/70 px-5 py-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
        @csrf
        <input type="hidden" name="opcr_id" value="{{ $payload['opcr']['id'] ?? $opcr->id }}">
        <input type="hidden" name="action" id="pmt-show-action">
        <input type="hidden" name="redirect_to_list" value="1">

        <div>
            <label for="pmt-show-remarks" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Return Remarks</label>
            <textarea id="pmt-show-remarks" name="remarks" rows="3" style="background:#0f172a;color:#e5e7eb;" class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"></textarea>
            <p id="pmt-show-remarks-error" class="mt-2 hidden text-[11px] text-rose-300">Remarks are required when returning the OPCR.</p>
        </div>
        <div class="flex flex-wrap justify-end gap-3">
            <button type="button" id="pmt-show-return" data-loading-text="Returning..." class="inline-flex items-center gap-2 rounded-lg border border-amber-600 px-4 py-2 text-sm font-semibold text-amber-300 hover:bg-amber-600/10 {{ $isReviewable ? '' : 'opacity-60 pointer-events-none' }}" {{ $isReviewable ? '' : 'disabled' }}>
                <span data-button-label>Return to Dept. Head</span><span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            </button>
            <button type="button" id="pmt-show-approve" data-loading-text="Approving..." class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500 {{ $isReviewable ? '' : 'opacity-60 pointer-events-none' }}" {{ $isReviewable ? '' : 'disabled' }}>
                <span data-button-label>Approve OPCR</span><span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            </button>
        </div>
    </form>
</section>

<div id="pmt-standards-modal" class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/70 px-4 py-6">
    <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-6 py-5">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-slate-400">QET Standards</p>
                <h3 id="pmt-standards-modal-title" class="mt-2 text-lg font-semibold text-white">Indicator Standards</h3>
            </div>
            <button type="button" data-pmt-modal-close class="rounded-lg border border-slate-700 px-3 py-2 text-slate-400 hover:bg-slate-800 hover:text-white">&times;</button>
        </div>
        <div class="px-6 py-5">
            <div class="overflow-hidden rounded-xl border border-slate-800">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.22em] text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Rating</th>
                            <th class="px-4 py-3 text-left">Quality (Q)</th>
                            <th class="px-4 py-3 text-left">Efficiency (E)</th>
                            <th class="px-4 py-3 text-left">Timeliness (T)</th>
                        </tr>
                    </thead>
                    <tbody id="pmt-standards-modal-body" class="divide-y divide-slate-800"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="pmt-assignees-modal" class="fixed inset-0 z-[95] hidden items-center justify-center bg-black/70 px-4 py-6">
    <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-6 py-5">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Assigned Employees</p>
                <h3 id="pmt-assignees-modal-title" class="mt-2 text-lg font-semibold text-white">Indicator Assignees</h3>
            </div>
            <button type="button" data-pmt-modal-close class="rounded-lg border border-slate-700 px-3 py-2 text-slate-400 hover:bg-slate-800 hover:text-white">&times;</button>
        </div>
        <div class="px-6 py-5">
            <div class="overflow-hidden rounded-xl border border-slate-800">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.22em] text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee Name</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody id="pmt-assignees-modal-body" class="divide-y divide-slate-800"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const payload = @json($payload);
    const outputs = Array.isArray(payload?.outputs) ? payload.outputs : [];
    let fnFilter = 'all';
    let activeTab = 'overview';
    let selectedIndex = 0;

    const outputList = document.getElementById('pmt-output-list');
    const outputCount = document.getElementById('pmt-output-count');
    const title = document.getElementById('pmt-detail-title');
    const fnBadge = document.getElementById('pmt-detail-function');
    const wt = document.getElementById('pmt-detail-weight');
    const overview = document.getElementById('pmt-overview-indicators');
    const indicatorsBody = document.getElementById('pmt-indicators-body');
    const standardsModal = document.getElementById('pmt-standards-modal');
    const standardsModalTitle = document.getElementById('pmt-standards-modal-title');
    const standardsModalBody = document.getElementById('pmt-standards-modal-body');
    const assigneesModal = document.getElementById('pmt-assignees-modal');
    const assigneesModalTitle = document.getElementById('pmt-assignees-modal-title');
    const assigneesModalBody = document.getElementById('pmt-assignees-modal-body');

    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const openModal = (modal) => {
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    const showButtonLoading = (btn, loading) => {
        if (!btn) return;
        const label = btn.querySelector('[data-button-label]');
        const spinner = btn.querySelector('[data-button-spinner]');
        if (label) {
            const base = label.dataset.baseLabel || label.textContent.trim();
            label.dataset.baseLabel = base;
            label.textContent = loading ? (btn.getAttribute('data-loading-text') || 'Processing...') : base;
        }
        if (spinner) spinner.classList.toggle('hidden', !loading);
        btn.disabled = loading;
        btn.classList.toggle('opacity-70', loading);
    };

    const filteredOutputs = () => fnFilter === 'all' ? outputs : outputs.filter((o) => String(o.function_type || '').toLowerCase() === fnFilter);
    const selectedOutput = () => filteredOutputs()[selectedIndex] || null;
    const targetSummary = (i) => {
        const q = i?.target_quantity;
        const t = String(i?.target_timeline || '').trim();
        return q && t ? `${q} ${t}` : (q ? String(q) : (t || '-'));
    };

    const standardsCell = (items) => {
        const list = Array.isArray(items) ? items : [];
        if (!list.length) return '-';
        return `<ul class="list-disc space-y-1 pl-4">${list.map((item) => `<li>${escapeHtml(item)}</li>`).join('')}</ul>`;
    };

    const renderStandardsModal = (indicator) => {
        const titleText = indicator?.indicator_text || 'Indicator Standards';
        standardsModalTitle.textContent = titleText;
        const byRating = indicator?.standards_by_rating || {};
        const ratings = [5, 4, 3, 2, 1];
        standardsModalBody.innerHTML = ratings.map((rating) => {
            const row = byRating[String(rating)] || byRating[rating] || { Q: [], E: [], T: [] };
            const q = row.Q ?? row.q ?? [];
            const e = row.E ?? row.e ?? [];
            const t = row.T ?? row.t ?? [];
            return `<tr>
                <td class="px-4 py-3 align-top font-semibold text-white">${rating}</td>
                <td class="px-4 py-3 align-top">${standardsCell(q)}</td>
                <td class="px-4 py-3 align-top">${standardsCell(e)}</td>
                <td class="px-4 py-3 align-top">${standardsCell(t)}</td>
            </tr>`;
        }).join('');
        openModal(standardsModal);
    };

    const renderAssigneesModal = (indicator) => {
        const titleText = indicator?.indicator_text || 'Indicator Assignees';
        assigneesModalTitle.textContent = titleText;
        const assignees = Array.isArray(indicator?.assignees) ? indicator.assignees : [];
        const officeUnit = payload?.opcr?.office?.name || '-';
        if (!assignees.length) {
            assigneesModalBody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-center text-slate-400">No assigned employees.</td></tr>';
            openModal(assigneesModal);
            return;
        }
        assigneesModalBody.innerHTML = assignees.map((name) => `<tr>
            <td class="px-4 py-3">${escapeHtml(name)}</td>
            <td class="px-4 py-3">${escapeHtml(officeUnit)}</td>
            <td class="px-4 py-3"><span class="inline-flex items-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2 py-1 text-[11px] font-semibold text-emerald-200">Assigned</span></td>
        </tr>`).join('');
        openModal(assigneesModal);
    };

    const renderOutputs = () => {
        const list = filteredOutputs();
        if (selectedIndex >= list.length) selectedIndex = 0;
        outputCount.textContent = String(list.length);
        outputList.innerHTML = '';
        if (!list.length) {
            outputList.innerHTML = '<p class="px-3 py-6 text-sm text-slate-500">No outputs found.</p>';
            renderDetail();
            return;
        }
        list.forEach((o, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `w-full rounded-xl border px-3 py-3 text-left transition ${i === selectedIndex ? 'border-blue-500/40 bg-blue-500/10' : 'border-slate-800 bg-slate-900/50 hover:border-slate-700'}`;
            btn.innerHTML = `<div class="text-base font-semibold leading-snug text-white">${o.title || '-'}</div><div class="mt-2 text-xs text-slate-500">${(o.success_indicators || []).length} indicator(s)</div>`;
            btn.addEventListener('click', () => { selectedIndex = i; renderOutputs(); });
            outputList.appendChild(btn);
        });
        renderDetail();
    };

    const renderDetail = () => {
        const o = selectedOutput();
        if (!o) {
            title.textContent = 'No output selected';
            fnBadge.classList.add('hidden');
            wt.classList.add('hidden');
            overview.innerHTML = '';
            indicatorsBody.innerHTML = '';
            return;
        }
        title.textContent = o.title || '-';
        const type = String(o.function_type || '').toLowerCase();
        fnBadge.classList.remove('hidden');
        fnBadge.className = type === 'core'
            ? 'rounded-md border px-2 py-1 text-xs font-medium border-emerald-500/20 bg-emerald-500/10 text-emerald-400'
            : 'rounded-md border px-2 py-1 text-xs font-medium border-blue-400/30 bg-blue-500/10 text-blue-300';
        fnBadge.textContent = type ? type.charAt(0).toUpperCase() + type.slice(1) : 'Support';
        wt.classList.remove('hidden');
        wt.textContent = o.weight_percent ? `${o.weight_percent}%` : '-';

        const indicators = Array.isArray(o.success_indicators) ? o.success_indicators : [];
        overview.innerHTML = indicators.length
            ? indicators.map((i) => `<div class="rounded-xl border border-slate-800 bg-slate-900/40 px-4 py-3 text-sm text-slate-100">${i.indicator_text || '-'}</div>`).join('')
            : '<p class="text-sm text-slate-500">No linked indicators.</p>';

        indicatorsBody.innerHTML = indicators.length
            ? indicators.map((i, idx) => {
                const standardsCount = Object.values(i.standards_by_rating || {}).reduce((t, r) => t + (r.Q?.length || 0) + (r.E?.length || 0) + (r.T?.length || 0), 0);
                return `<tr>
                    <td class="px-4 py-3 align-top text-slate-100">${i.indicator_text || '-'}</td>
                    <td class="px-4 py-3 align-top text-slate-300">${targetSummary(i)}</td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" data-standards-index="${idx}" class="font-semibold text-blue-300 hover:text-blue-200">View</button>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" data-assignees-index="${idx}" class="inline-flex items-center gap-1.5 font-semibold text-blue-300 hover:text-blue-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span>${(i.assignees || []).length}</span>
                        </button>
                    </td>
                </tr>`;
            }).join('')
            : '<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No indicators found.</td></tr>';

        indicatorsBody.querySelectorAll('[data-standards-index]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const index = Number(btn.getAttribute('data-standards-index'));
                const indicator = indicators[index];
                if (!indicator) return;
                renderStandardsModal(indicator);
            });
        });
        indicatorsBody.querySelectorAll('[data-assignees-index]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const index = Number(btn.getAttribute('data-assignees-index'));
                const indicator = indicators[index];
                if (!indicator) return;
                renderAssigneesModal(indicator);
            });
        });
    };

    document.querySelectorAll('[data-fn-filter]').forEach((btn) => {
        btn.addEventListener('click', () => {
            fnFilter = btn.getAttribute('data-fn-filter') || 'all';
            selectedIndex = 0;
            document.querySelectorAll('[data-fn-filter]').forEach((b) => {
                const active = b === btn;
                b.classList.toggle('border-blue-400', active);
                b.classList.toggle('text-white', active);
                b.classList.toggle('border-transparent', !active);
                b.classList.toggle('text-slate-400', !active);
            });
            renderOutputs();
        });
    });

    document.querySelectorAll('[data-panel-tab]').forEach((btn) => {
        btn.addEventListener('click', () => {
            activeTab = btn.getAttribute('data-panel-tab') || 'overview';
            document.querySelectorAll('[data-panel-tab]').forEach((b) => {
                const active = b === btn;
                b.classList.toggle('border-blue-400', active);
                b.classList.toggle('text-white', active);
                b.classList.toggle('border-transparent', !active);
                b.classList.toggle('text-slate-400', !active);
            });
            document.querySelectorAll('[data-panel]').forEach((panel) => {
                panel.classList.toggle('hidden', panel.getAttribute('data-panel') !== activeTab);
            });
        });
    });

    const form = document.getElementById('pmt-opcr-show-form');
    const actionInput = document.getElementById('pmt-show-action');
    const remarks = document.getElementById('pmt-show-remarks');
    const remarksError = document.getElementById('pmt-show-remarks-error');
    const returnBtn = document.getElementById('pmt-show-return');
    const approveBtn = document.getElementById('pmt-show-approve');

    returnBtn?.addEventListener('click', () => {
        const value = String(remarks?.value || '').trim();
        if (!value) {
            remarksError?.classList.remove('hidden');
            remarks?.focus();
            return;
        }
        remarksError?.classList.add('hidden');
        actionInput.value = 'return';
        showButtonLoading(returnBtn, true);
        form.submit();
    });

    approveBtn?.addEventListener('click', () => {
        actionInput.value = 'approve';
        showButtonLoading(approveBtn, true);
        form.submit();
    });

    document.querySelectorAll('[data-pmt-modal-close]').forEach((btn) => {
        btn.addEventListener('click', () => {
            closeModal(btn.closest('.fixed'));
        });
    });
    [standardsModal, assigneesModal].forEach((modal) => {
        modal?.addEventListener('click', (event) => {
            if (event.target === modal) closeModal(modal);
        });
    });

    renderOutputs();
});
</script>
@endpush
@endsection
