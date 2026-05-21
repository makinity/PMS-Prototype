@extends('layouts.supervisor')

@section('main-content')
    @php
        $uwp = $uwp ?? [];
        $officeName = $uwp['office']['name'] ?? '--';
        $periodName = $uwp['performance_period']['name'] ?? '--';
        $supervisorName = $uwp['creator']['name'] ?? '--';
        $deptHeadName = $uwp['department_head']['name'] ?? '--';
        $status = strtolower((string) ($uwp['status'] ?? ''));
        $functions = $uwp['uwp_functions'] ?? [];

        $isEditable = (bool) ($isEditable ?? false);
        $canSubmit = (bool) ($canSubmit ?? false);

        $statusColors = [
            'draft' => 'border-yellow-500/30 bg-yellow-500/10 text-yellow-300',
            'submitted' => 'border-blue-500/30 bg-blue-500/10 text-blue-300',
            'consolidated' => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300',
            'endorsed' => 'border-indigo-500/30 bg-indigo-500/10 text-indigo-300',
            'pmt_approved' => 'border-purple-500/30 bg-purple-500/10 text-purple-300',
            'returned' => 'border-rose-500/30 bg-rose-500/10 text-rose-300',
        ];
        $statusClass = $statusColors[$status] ?? 'border-gray-500/30 bg-gray-500/10 text-gray-300';

        $flattenedOutputs = collect($functions)
            ->flatMap(fn ($fn) => collect($fn['mfos'] ?? [])->map(fn ($mfo) => [
                'function_type' => strtolower((string) ($fn['function_type'] ?? 'custom')),
                'function_name' => (string) ($fn['name'] ?? ''),
                'function_weight' => $fn['weight_percent'] ?? null,
                'mfo' => $mfo,
            ]))
            ->values();
    @endphp

    <section class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Stage I - Unit Work Plan (UWP)</p>
                <h1 class="mt-1 text-2xl font-semibold text-white">UWP Preview</h1>
                <p class="mt-1 text-sm text-slate-400">Read-only preview of planned outputs and success indicators.</p>
            </div>
            <a href="{{ route('supervisor.uwp-page') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-900">
                Back to UWP List
            </a>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-5">
            <div class="flex flex-wrap gap-6">
                <div class="min-w-[200px]">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Office / Unit</p>
                    <p class="mt-1 font-medium text-white">{{ $officeName }}</p>
                </div>
                <div class="min-w-[200px]">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Performance Period</p>
                    <p class="mt-1 font-medium text-white">{{ $periodName }}</p>
                </div>
                <div class="min-w-[200px]">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Supervisor</p>
                    <p class="mt-1 font-medium text-white">{{ $supervisorName }}</p>
                </div>
                <div class="min-w-[200px]">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Department Head</p>
                    <p class="mt-1 font-medium text-white">{{ $deptHeadName }}</p>
                </div>
                <div class="min-w-[160px]">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Status</p>
                    <span class="mt-2 inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                        {{ ucfirst(str_replace('_', ' ', $status ?: '--')) }}
                    </span>
                </div>
            </div>

            @if (!empty($uwp['return_remarks']))
                <div class="mt-5 rounded-xl border border-rose-500/20 bg-rose-500/5 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-rose-200">Returned Remarks</p>
                    <p class="mt-2 whitespace-pre-wrap text-sm text-slate-100">{{ $uwp['return_remarks'] }}</p>
                    <p class="mt-2 text-[11px] text-slate-400">
                        @if (!empty($uwp['returned_at']))
                            Returned at {{ $uwp['returned_at'] }}
                        @endif
                        @if (!empty($uwp['returned_by_user']['name']))
                            by {{ $uwp['returned_by_user']['name'] }}
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <div class="grid min-h-0 gap-4 lg:grid-cols-[320px_minmax(0,1fr)]">
            <aside class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Planned Outputs</p>
                    <span id="uwpOutputCountBadge" class="text-sm font-semibold text-blue-300">{{ $flattenedOutputs->count() }}</span>
                </div>
                <div class="mt-3 flex border-b border-slate-800 pb-2">
                    <button type="button" data-uwp-filter="all" class="flex-1 border-b-2 border-blue-400 pb-2 text-xs font-semibold text-white">All</button>
                    <button type="button" data-uwp-filter="core" class="flex-1 border-b-2 border-transparent pb-2 text-xs font-medium text-slate-400 hover:text-slate-200">Core</button>
                    <button type="button" data-uwp-filter="support" class="flex-1 border-b-2 border-transparent pb-2 text-xs font-medium text-slate-400 hover:text-slate-200">Support</button>
                </div>
                <div id="uwpOutputList" class="mt-3 min-h-0 space-y-2 overflow-y-auto pr-1"></div>
            </aside>

            <section class="rounded-2xl border border-slate-800 bg-slate-950/60">
                <div class="border-b border-slate-800 px-5 py-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 id="uwpDetailTitle" class="text-lg font-semibold text-white">Select an output</h2>
                        <span id="uwpDetailTypeBadge" class="hidden rounded-md border px-2 py-1 text-xs font-medium"></span>
                        <span id="uwpDetailWeight" class="hidden text-sm font-semibold text-slate-300"></span>
                    </div>
                </div>
                <div class="border-b border-slate-800 px-5">
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" data-uwp-tab="overview" class="border-b-2 border-blue-400 px-2.5 py-3 text-sm font-semibold text-white">Overview</button>
                        <button type="button" data-uwp-tab="indicators" class="border-b-2 border-transparent px-2.5 py-3 text-sm font-medium text-slate-400 hover:text-slate-200">Success Indicators</button>
                    </div>
                </div>
                <div class="min-h-0 overflow-y-auto px-6 py-5">
                    <div data-uwp-panel="overview" class="space-y-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Target</p>
                            <p id="uwpTargetSummary" class="mt-2 text-base leading-snug text-white">--</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Linked Success Indicators</p>
                            <div id="uwpIndicatorsSummary" class="mt-3 space-y-2"></div>
                        </div>
                    </div>

                    <div data-uwp-panel="indicators" class="hidden space-y-4">
                        <div class="overflow-hidden rounded-xl border border-slate-800">
                            <table class="min-w-full text-sm text-slate-200">
                                <thead class="bg-slate-950/70 text-xs uppercase tracking-wide text-slate-400">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Indicator</th>
                                        <th class="px-4 py-3 text-left">Target</th>
                                        <th class="px-4 py-3 text-left">Assignees</th>
                                    </tr>
                                </thead>
                                <tbody id="uwpIndicatorsTableBody" class="divide-y divide-slate-800"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3 rounded-2xl border border-slate-800 bg-slate-950/60 px-5 py-4">
            <a href="{{ route('uwp.excel.export', ['uwp' => (int) ($uwp['id'] ?? 0)]) }}"
               class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-900 {{ empty($uwp['id']) ? 'pointer-events-none opacity-60' : '' }}">
                Export Excel
            </a>

            <form method="POST" action="{{ route('supervisor.uwp.submit', ['id' => (int) ($uwp['id'] ?? 0)]) }}">
                @csrf
                <input type="hidden" name="redirect_to_list" value="1">
                <button type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-900/40 transition hover:bg-blue-500 {{ $canSubmit ? '' : 'opacity-50 cursor-not-allowed' }}"
                    @disabled(!$canSubmit)>
                    Submit for Approval
                </button>
            </form>
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                const outputs = @json($flattenedOutputs->all());

                let activeFilter = 'all';
                let activeTab = 'overview';
                let activeIndex = -1;

                const listEl = document.getElementById('uwpOutputList');
                const titleEl = document.getElementById('uwpDetailTitle');
                const typeBadgeEl = document.getElementById('uwpDetailTypeBadge');
                const weightEl = document.getElementById('uwpDetailWeight');
                const targetEl = document.getElementById('uwpTargetSummary');
                const indicatorsSummaryEl = document.getElementById('uwpIndicatorsSummary');
                const indicatorsTableBodyEl = document.getElementById('uwpIndicatorsTableBody');

                function escapeHtml(str) {
                    return String(str ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function formatFilterButton(activeBtn) {
                    document.querySelectorAll('[data-uwp-filter]').forEach((btn) => {
                        const isActive = btn === activeBtn;
                        btn.classList.toggle('border-blue-400', isActive);
                        btn.classList.toggle('text-white', isActive);
                        btn.classList.toggle('border-transparent', !isActive);
                        btn.classList.toggle('text-slate-400', !isActive);
                    });
                }

                function formatTabButton(activeBtn) {
                    document.querySelectorAll('[data-uwp-tab]').forEach((btn) => {
                        const isActive = btn === activeBtn;
                        btn.classList.toggle('border-blue-400', isActive);
                        btn.classList.toggle('text-white', isActive);
                        btn.classList.toggle('border-transparent', !isActive);
                        btn.classList.toggle('text-slate-400', !isActive);
                    });
                }

                function setTab(tab) {
                    activeTab = tab;
                    document.querySelectorAll('[data-uwp-panel]').forEach((panel) => {
                        panel.classList.toggle('hidden', panel.getAttribute('data-uwp-panel') !== tab);
                    });
                }

                function filteredOutputs() {
                    if (activeFilter === 'all') return outputs;
                    return outputs.filter((o) => String(o.function_type || '').toLowerCase() === activeFilter);
                }

                function renderList() {
                    if (!listEl) return;
                    const list = filteredOutputs();
                    listEl.innerHTML = '';

                    if (!list.length) {
                        listEl.innerHTML = '<p class="px-2 py-6 text-sm text-slate-500">No outputs found.</p>';
                        activeIndex = -1;
                        renderDetail(null);
                        return;
                    }

                    list.forEach((item, idx) => {
                        const mfo = item.mfo || {};
                        const type = String(item.function_type || 'custom').toLowerCase();
                        const badgeClass = type === 'core'
                            ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400'
                            : (type === 'support'
                                ? 'border-blue-400/30 bg-blue-500/10 text-blue-300'
                                : 'border-slate-700 bg-slate-900/60 text-slate-300');

                        const isActive = idx === activeIndex;
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = `block w-full rounded-xl border px-3 py-3 text-left transition ${isActive ? 'border-blue-400/60 bg-blue-500/10' : 'border-slate-800 bg-slate-950/30 hover:bg-slate-900/50'}`;
                        btn.innerHTML = `
                            <div class="line-clamp-2 text-sm font-semibold leading-snug text-white">${escapeHtml(mfo.title || 'Untitled Output')}</div>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-md border px-2 py-0.5 text-xs font-medium ${badgeClass}">${escapeHtml(type.charAt(0).toUpperCase() + type.slice(1))}</span>
                                <span class="text-xs text-slate-400">${escapeHtml(item.function_name || '')}</span>
                                <span class="text-xs text-slate-500">${(mfo.success_indicators || []).length} indicator${(mfo.success_indicators || []).length === 1 ? '' : 's'}</span>
                            </div>`;
                        btn.addEventListener('click', () => {
                            activeIndex = idx;
                            renderList();
                            renderDetail(item);
                            setTab(activeTab);
                        });
                        listEl.appendChild(btn);
                    });

                    if (activeIndex < 0) {
                        activeIndex = 0;
                        renderList();
                        renderDetail(list[0]);
                    }
                }

                function renderDetail(item) {
                    if (!titleEl || !typeBadgeEl || !weightEl || !targetEl || !indicatorsSummaryEl || !indicatorsTableBodyEl) return;

                    if (!item) {
                        titleEl.textContent = 'Select an output';
                        typeBadgeEl.classList.add('hidden');
                        weightEl.classList.add('hidden');
                        targetEl.textContent = '--';
                        indicatorsSummaryEl.innerHTML = '<p class="text-sm text-slate-500">No output selected.</p>';
                        indicatorsTableBodyEl.innerHTML = '<tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No output selected.</td></tr>';
                        return;
                    }

                    const mfo = item.mfo || {};
                    const type = String(item.function_type || 'custom').toLowerCase();

                    titleEl.textContent = mfo.title || 'Untitled Output';
                    typeBadgeEl.classList.remove('hidden');
                    typeBadgeEl.className = `rounded-md border px-2 py-1 text-xs font-medium ${
                        type === 'core'
                            ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400'
                            : (type === 'support'
                                ? 'border-blue-400/30 bg-blue-500/10 text-blue-300'
                                : 'border-slate-700 bg-slate-900/60 text-slate-300')
                    }`;
                    typeBadgeEl.textContent = type ? (type.charAt(0).toUpperCase() + type.slice(1)) : 'Custom';

                    if (item.function_weight !== null && item.function_weight !== undefined && String(item.function_weight) !== '') {
                        weightEl.classList.remove('hidden');
                        weightEl.textContent = `${item.function_weight}%`;
                    } else {
                        weightEl.classList.add('hidden');
                        weightEl.textContent = '';
                    }

                    const targetParts = [];
                    if (mfo.target_quantity) targetParts.push(String(mfo.target_quantity));
                    if (mfo.target_timeline) targetParts.push(String(mfo.target_timeline));
                    targetEl.textContent = targetParts.length ? targetParts.join(' ') : '--';

                    const indicators = Array.isArray(mfo.success_indicators) ? mfo.success_indicators : [];
                    indicatorsSummaryEl.innerHTML = indicators.length
                        ? indicators.map((ind) => `<div class="rounded-xl border border-slate-800 bg-slate-950/40 px-4 py-3 text-sm text-slate-100">${escapeHtml(ind.indicator_text || '--')}</div>`).join('')
                        : '<p class="text-sm text-slate-500">No success indicators.</p>';

                    indicatorsTableBodyEl.innerHTML = indicators.length
                        ? indicators.map((ind) => {
                            const assignees = (ind.assignments || []).map(a => a.employee?.name).filter(Boolean);
                            const assigneeLabel = assignees.length ? assignees.join(', ') : '--';
                            const targetSummary = [ind.target_quantity, ind.target_timeline].filter(Boolean).join(' ') || '--';
                            return `
                                <tr class="hover:bg-slate-900/30">
                                    <td class="px-4 py-3 text-slate-100">${escapeHtml(ind.indicator_text || '--')}</td>
                                    <td class="px-4 py-3 text-slate-300 text-xs">${escapeHtml(targetSummary)}</td>
                                    <td class="px-4 py-3 text-slate-300 text-xs">${escapeHtml(assigneeLabel)}</td>
                                </tr>`;
                        }).join('')
                        : '<tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No success indicators.</td></tr>';
                }

                document.querySelectorAll('[data-uwp-filter]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        activeFilter = btn.getAttribute('data-uwp-filter') || 'all';
                        activeIndex = -1;
                        formatFilterButton(btn);
                        renderList();
                    });
                });

                document.querySelectorAll('[data-uwp-tab]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        activeTab = btn.getAttribute('data-uwp-tab') || 'overview';
                        formatTabButton(btn);
                        setTab(activeTab);
                    });
                });

                // init
                const defaultFilterBtn = document.querySelector('[data-uwp-filter="all"]');
                if (defaultFilterBtn) formatFilterButton(defaultFilterBtn);
                const defaultTabBtn = document.querySelector('[data-uwp-tab="overview"]');
                if (defaultTabBtn) formatTabButton(defaultTabBtn);
                setTab('overview');
                renderList();
            })();
        </script>
    @endpush
@endsection
