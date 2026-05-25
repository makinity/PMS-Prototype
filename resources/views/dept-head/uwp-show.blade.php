@extends('layouts.dept-head')

@section('main-content')
@php
    $statusKey = strtolower(str_replace('-', '_', (string) ($uwpPayload['status'] ?? '')));
    $statusBadge = match($statusKey) {
        'returned' => ['bg'=>'bg-rose-500/10', 'text'=>'text-rose-300', 'border'=>'border-rose-500/20', 'label'=>'Returned'],
        'submitted' => ['bg'=>'bg-blue-500/10', 'text'=>'text-blue-300', 'border'=>'border-blue-500/20', 'label'=>'Submitted'],
        'consolidated' => ['bg'=>'bg-cyan-500/10', 'text'=>'text-cyan-300', 'border'=>'border-cyan-500/20', 'label'=>'Consolidated'],
        'endorsed' => ['bg'=>'bg-violet-500/10', 'text'=>'text-violet-300', 'border'=>'border-violet-500/20', 'label'=>'Endorsed'],
        'approved', 'pmt_approved' => ['bg'=>'bg-emerald-500/10', 'text'=>'text-emerald-300', 'border'=>'border-emerald-500/20', 'label'=>'Approved'],
        default => ['bg'=>'bg-amber-500/10', 'text'=>'text-amber-300', 'border'=>'border-amber-500/20', 'label'=>ucwords(str_replace('_', ' ', $statusKey ?: 'unknown'))],
    };
    $isSubmitted = $statusKey === 'submitted';
@endphp

<div class="mb-4 flex items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-semibold text-slate-100">UWP Review</h1>
        <p class="mt-1 text-sm text-slate-400">Dedicated review workspace for this Unit Work Plan.</p>
    </div>
    <a href="{{ route('dept-head.uwp.index', ['status' => $statusFilter]) }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">Back to UWP List</a>
</div>

<div class="mb-4 grid gap-3 rounded-xl border border-gray-700 bg-slate-900/80 p-5 md:grid-cols-5">
    <div>
        <p class="text-xs uppercase tracking-widest text-slate-500">Office / Unit</p>
        <p class="mt-1 font-medium text-slate-100">{{ $uwpPayload['office']['name'] ?? '—' }}</p>
    </div>
    <div>
        <p class="text-xs uppercase tracking-widest text-slate-500">Supervisor</p>
        <p class="mt-1 font-medium text-slate-100">{{ $uwpPayload['supervisor']['name'] ?? '—' }}</p>
    </div>
    <div>
        <p class="text-xs uppercase tracking-widest text-slate-500">Department Head</p>
        <p class="mt-1 font-medium text-slate-100">{{ $uwpPayload['department_head']['name'] ?? '—' }}</p>
    </div>
    <div>
        <p class="text-xs uppercase tracking-widest text-slate-500">Performance Period</p>
        <p class="mt-1 font-medium text-slate-100">{{ $uwpPayload['period']['name'] ?? '—' }}</p>
    </div>
    <div>
        <p class="text-xs uppercase tracking-widest text-slate-500">Status</p>
        <span class="mt-1 inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} {{ $statusBadge['border'] }}">{{ $statusBadge['label'] }}</span>
    </div>
</div>

@if (!empty($uwpPayload['return_remarks']))
    <div class="mb-4 rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-rose-200">
        <p class="text-sm font-semibold">Returned Remarks</p>
        <p class="mt-1 text-xs text-rose-200/80">
            {{ $uwpPayload['returned_by_role'] === 'dept-head' ? 'Returned by Department Head' : 'Returned for revision' }}
            @if (!empty($uwpPayload['returned_at'])) • {{ \Illuminate\Support\Carbon::parse($uwpPayload['returned_at'])->format('M d, Y h:i A') }} @endif
        </p>
        <div class="mt-2 whitespace-pre-line text-sm text-rose-100">{{ $uwpPayload['return_remarks'] }}</div>
    </div>
@endif

<div class="grid min-h-0 overflow-hidden rounded-2xl border border-slate-700/90 bg-gradient-to-br from-[#0f1629] via-[#0c1424] to-[#0b1120] shadow-[0_20px_60px_-30px_rgba(14,165,233,0.35)] lg:grid-cols-[300px_minmax(0,1fr)]">
    <aside class="flex min-h-0 flex-col border-b border-slate-700/80 bg-slate-950/25 lg:border-b-0 lg:border-r">
        <div class="flex h-14 items-center justify-between border-b border-slate-700/80 px-4">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Planned Outputs</p>
            <span id="dh-output-count-badge" class="text-sm font-semibold text-blue-300">0</span>
        </div>
        <div class="flex h-10 items-end border-b border-slate-700/80 px-2">
            <button type="button" data-dh-function-tab="all" class="flex-1 border-b-2 border-blue-400 px-2.5 py-2.5 text-sm font-semibold text-white">All</button>
            <button type="button" data-dh-function-tab="core" class="flex-1 border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Core</button>
            <button type="button" data-dh-function-tab="support" class="flex-1 border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Support</button>
        </div>
        <div id="dh-output-list" class="min-h-0 space-y-2 overflow-y-auto px-2 py-2"></div>
    </aside>

    <section class="flex min-h-0 flex-col bg-slate-950/10">
        <div class="flex h-14 items-center border-b border-slate-700/80 px-5">
            <div class="flex flex-wrap items-center gap-3">
                <h3 id="dh-detail-title" class="text-lg font-semibold leading-tight text-white">No output selected</h3>
                <span id="dh-detail-function" class="hidden rounded-md border px-2 py-1 text-xs font-medium"></span>
                <span id="dh-detail-weight" class="hidden text-sm font-semibold text-slate-300"></span>
            </div>
        </div>

        <div class="border-b border-slate-700/80 px-4">
            <div class="flex h-10 items-end gap-1.5">
                <button type="button" data-dh-tab="overview" class="border-b-2 border-blue-400 px-2.5 py-2.5 text-sm font-semibold text-white">Overview</button>
                <button type="button" data-dh-tab="indicators" class="border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Success Indicators</button>
            </div>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
            <div data-dh-panel="overview" class="space-y-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Target Summary</p>
                    <p id="dh-target-summary" class="mt-2 text-lg leading-snug text-white">-</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Linked Success Indicators</p>
                    <div id="dh-overview-indicators" class="mt-2 space-y-2"></div>
                </div>
            </div>

            <div data-dh-panel="indicators" class="hidden">
                <div class="overflow-x-auto rounded-xl border border-slate-700/80 bg-slate-950/35 shadow-inner shadow-black/20">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-[#0f1b33]/90 text-xs uppercase tracking-widest text-slate-300">
                            <tr>
                                <th class="px-4 py-3 text-left">Success Indicator</th>
                                <th class="px-4 py-3 text-left">Target</th>
                                <th class="px-4 py-3 text-left">Assigned</th>
                                <th class="px-4 py-3 text-left">Standards</th>
                            </tr>
                        </thead>
                        <tbody id="dh-indicators-table" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="mt-4 rounded-xl border border-slate-700/90 bg-gradient-to-b from-[#0f1a32]/90 to-[#0e172d]/90 p-4 shadow-[0_16px_40px_-30px_rgba(56,189,248,0.4)]">
    <label for="dh-remarks" class="text-xs font-semibold uppercase tracking-wide text-slate-400">Review Remarks (required if returning)</label>
    <textarea id="dh-remarks" rows="3" class="mt-2 w-full rounded-lg border border-slate-700 bg-[#0b1324] px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500" style="background:#0b1324;color:#e2e8f0;" placeholder="Add clear instructions or justification..."></textarea>
    <p class="mt-2 text-[11px] text-slate-500">Consolidating will create or refresh the office OPCR using all submitted UWPs for this period.</p>

    <div class="mt-4 flex items-center justify-between">
        <p class="text-xs text-slate-500">Department Head can return submitted UWPs and consolidate submitted UWPs to OPCR.</p>
        <div class="flex items-center gap-3">
            <form id="dh-return-form" method="POST" action="{{ route('dept-head.uwp.return') }}">
                @csrf
                <input type="hidden" name="unit_work_plan_id" value="{{ (int) $uwp->id }}">
                <input type="hidden" name="remarks" id="dh-return-remarks">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <button type="button" id="dh-return-btn" class="inline-flex items-center gap-2 rounded-lg border border-rose-500/30 bg-rose-600/10 px-4 py-2 text-sm font-semibold text-rose-200 transition hover:bg-rose-600/20">Return to Supervisor</button>
            </form>

            @if ($isSubmitted)
                <form id="dh-endorse-form" method="POST" action="{{ route('dept-head.uwp.review') }}">
                    @csrf
                    <input type="hidden" name="unit_work_plan_id" value="{{ (int) $uwp->id }}">
                    <input type="hidden" name="action" value="endorse">
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                    <button type="submit" id="dh-endorse-btn" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">Consolidate to OPCR</button>
                </form>
            @else
                <button type="button" class="inline-flex items-center rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-slate-300" disabled>Consolidate to OPCR</button>
            @endif
        </div>
    </div>
</div>

<script>
(() => {
    const payload = @json($uwpPayload);
    const outputs = (payload.functions || []).flatMap((fn) => (fn.mfos || []).map((mfo) => ({
        ...mfo,
        function_type: String(fn.function_type || ''),
        function_name: String(fn.name || ''),
        function_weight: String(fn.weight_percent || ''),
    })));

    const state = {
        tab: 'overview',
        filter: 'all',
        selected: 0,
    };

    const outputListEl = document.getElementById('dh-output-list');
    const outputCountEl = document.getElementById('dh-output-count-badge');
    const titleEl = document.getElementById('dh-detail-title');
    const functionEl = document.getElementById('dh-detail-function');
    const weightEl = document.getElementById('dh-detail-weight');
    const summaryEl = document.getElementById('dh-target-summary');
    const overviewIndicatorsEl = document.getElementById('dh-overview-indicators');
    const indicatorsTableEl = document.getElementById('dh-indicators-table');

    function filteredOutputs() {
        if (state.filter === 'all') return outputs;
        return outputs.filter((item) => String(item.function_type || '').toLowerCase() === state.filter);
    }

    function normalizeSelectedIndex(list) {
        if (!list.length) {
            state.selected = -1;
            return;
        }
        if (state.selected < 0 || state.selected >= list.length) {
            state.selected = 0;
        }
    }

    function renderOutputList() {
        const list = filteredOutputs();
        normalizeSelectedIndex(list);
        outputCountEl.textContent = String(list.length);
        outputListEl.innerHTML = '';

        if (!list.length) {
            outputListEl.innerHTML = '<div class="rounded-xl border border-gray-700 bg-slate-900/40 px-3 py-4 text-sm text-slate-400">No planned outputs.</div>';
            renderDetail();
            return;
        }

        list.forEach((item, idx) => {
            const isActive = idx === state.selected;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `w-full rounded-xl border px-3 py-3 text-left transition ${isActive ? 'border-blue-500/40 bg-blue-500/10' : 'border-gray-700 bg-slate-900/50 hover:border-slate-700'}`;
            btn.innerHTML = `
                <p class="text-base font-semibold text-slate-100">${item.title || 'Untitled Output'}</p>
                <p class="mt-1 text-sm text-slate-400">${(item.success_indicators || []).length} indicators</p>
            `;
            btn.addEventListener('click', () => {
                state.selected = idx;
                renderOutputList();
            });
            outputListEl.appendChild(btn);
        });

        renderDetail();
    }

    function renderDetail() {
        const list = filteredOutputs();
        const output = list[state.selected];
        if (!output) {
            titleEl.textContent = 'No output selected';
            functionEl.classList.add('hidden');
            weightEl.classList.add('hidden');
            summaryEl.textContent = '-';
            overviewIndicatorsEl.innerHTML = '';
            indicatorsTableEl.innerHTML = '';
            return;
        }

        const type = String(output.function_type || '').toLowerCase();
        const badgeClass = type === 'core'
            ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
            : 'border-amber-500/40 bg-amber-500/10 text-amber-200';

        titleEl.textContent = output.title || 'Untitled Output';
        functionEl.textContent = type ? type.charAt(0).toUpperCase() + type.slice(1) : 'Support';
        functionEl.className = `rounded-md border px-2 py-1 text-xs font-medium ${badgeClass}`;
        functionEl.classList.remove('hidden');

        weightEl.textContent = output.weight_percent ? `${output.weight_percent}% weight` : '';
        if (output.weight_percent) weightEl.classList.remove('hidden');
        else weightEl.classList.add('hidden');

        summaryEl.textContent = `Target Quantity: ${output.target_quantity || '—'} • Timeline: ${output.target_timeline || '—'}`;

        const indicators = output.success_indicators || [];
        overviewIndicatorsEl.innerHTML = indicators.length
            ? indicators.map((si) => `<div class="rounded-xl border border-gray-700 bg-slate-900/40 px-3 py-2.5 text-sm text-slate-200">${si.indicator_text || 'Untitled indicator'}</div>`).join('')
            : '<p class="text-sm text-slate-400">No linked success indicators.</p>';

        indicatorsTableEl.innerHTML = indicators.length
            ? indicators.map((si) => {
                const standardsCount = Object.values(si.standards_by_rating || {}).reduce((total, group) => {
                    return total + (group.Q || []).length + (group.E || []).length + (group.T || []).length;
                }, 0);
                return `
                    <tr>
                        <td class="px-4 py-3 align-top text-slate-100">${si.indicator_text || 'Untitled indicator'}</td>
                        <td class="px-4 py-3 align-top text-slate-300">${si.target_quantity || '—'} / ${si.target_timeline || '—'}</td>
                        <td class="px-4 py-3 align-top text-slate-300">${(si.assignees || []).length ? si.assignees.join(', ') : '—'}</td>
                        <td class="px-4 py-3 align-top text-slate-300">${standardsCount}</td>
                    </tr>
                `;
            }).join('')
            : '<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">No success indicators.</td></tr>';
    }

    function setTab(tab) {
        state.tab = tab;
        document.querySelectorAll('[data-dh-tab]').forEach((btn) => {
            const active = btn.getAttribute('data-dh-tab') === tab;
            btn.classList.toggle('border-blue-400', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('border-transparent', !active);
            btn.classList.toggle('text-slate-400', !active);
            btn.classList.toggle('font-semibold', active);
            btn.classList.toggle('font-medium', !active);
        });

        document.querySelectorAll('[data-dh-panel]').forEach((panel) => {
            panel.classList.toggle('hidden', panel.getAttribute('data-dh-panel') !== tab);
        });
    }

    function setFilter(filter) {
        state.filter = filter;
        state.selected = 0;
        document.querySelectorAll('[data-dh-function-tab]').forEach((btn) => {
            const active = btn.getAttribute('data-dh-function-tab') === filter;
            btn.classList.toggle('border-blue-400', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('border-transparent', !active);
            btn.classList.toggle('text-slate-400', !active);
            btn.classList.toggle('font-semibold', active);
            btn.classList.toggle('font-medium', !active);
        });
        renderOutputList();
    }

    document.querySelectorAll('[data-dh-tab]').forEach((btn) => {
        btn.addEventListener('click', () => setTab(btn.getAttribute('data-dh-tab') || 'overview'));
    });
    document.querySelectorAll('[data-dh-function-tab]').forEach((btn) => {
        btn.addEventListener('click', () => setFilter(btn.getAttribute('data-dh-function-tab') || 'all'));
    });

    const returnBtn = document.getElementById('dh-return-btn');
    const returnForm = document.getElementById('dh-return-form');
    const returnRemarks = document.getElementById('dh-return-remarks');
    const remarksInput = document.getElementById('dh-remarks');
    if (returnBtn && returnForm && returnRemarks && remarksInput) {
        returnBtn.addEventListener('click', () => {
            const value = String(remarksInput.value || '').trim();
            if (!value) {
                window.PMSnackbar?.show({ type: 'error', message: 'Remarks are required when returning a Unit Work Plan.' });
                remarksInput.focus();
                return;
            }
            returnRemarks.value = value;
            returnForm.submit();
        });
    }

    setFilter('all');
    setTab('overview');
})();
</script>
@endsection
