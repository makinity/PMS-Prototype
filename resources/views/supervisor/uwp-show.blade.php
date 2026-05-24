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
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-700 pb-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-white">UWP Preview</h1>
                <p class="mt-0.5 text-sm text-slate-400">{{ $officeName }} Â· {{ $periodName }}</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1.5 rounded-full border {{ $statusClass }} px-3 py-1 text-xs font-medium">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    {{ ucfirst(str_replace('_', ' ', $status ?: '--')) }}
                </span>
                <a href="{{ route('supervisor.uwp-page') }}"
                   class="inline-flex items-center gap-1 text-sm text-slate-400 transition hover:text-white">
                    <i class="fas fa-arrow-left text-xs"></i> Back to list
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-5 shadow-lg">
                <p class="text-xs uppercase tracking-widest text-slate-500">Office / Unit</p>
                <p class="mt-1 font-medium text-white">{{ $officeName }}</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-5 shadow-lg">
                <p class="text-xs uppercase tracking-widest text-slate-500">Performance Period</p>
                <p class="mt-1 font-medium text-white">{{ $periodName }}</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-5 shadow-lg">
                <p class="text-xs uppercase tracking-widest text-slate-500">Supervisor</p>
                <p class="mt-1 font-medium text-white">{{ $supervisorName }}</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-5 shadow-lg">
                <p class="text-xs uppercase tracking-widest text-slate-500">Department Head</p>
                <p class="mt-1 font-medium text-white">{{ $deptHeadName }}</p>
            </div>
        </div>

        @if (!empty($uwp['return_remarks']))
            <div class="rounded-xl border border-rose-500/20 bg-rose-500/5 p-4">
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

        <div class="grid min-h-0 gap-4 lg:grid-cols-[320px_minmax(0,1fr)]">
            <aside class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-lg">
                <div class="flex items-center justify-between border-b border-gray-700 pb-3">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Planned Outputs</p>
                    <span id="uwpOutputCountBadge" class="text-sm font-semibold text-blue-300">{{ $flattenedOutputs->count() }}</span>
                </div>
                <div class="mt-3 flex border-b border-gray-700 pb-2">
                    <button type="button" data-uwp-filter="all" class="flex-1 border-b-2 border-blue-400 pb-2 text-xs font-semibold text-white">All</button>
                    <button type="button" data-uwp-filter="core" class="flex-1 border-b-2 border-transparent pb-2 text-xs font-medium text-slate-400 hover:text-slate-200">Core</button>
                    <button type="button" data-uwp-filter="support" class="flex-1 border-b-2 border-transparent pb-2 text-xs font-medium text-slate-400 hover:text-slate-200">Support</button>
                </div>
                <div id="uwpOutputList" class="mt-3 min-h-0 space-y-2 overflow-y-auto pr-1"></div>
            </aside>

            <section class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 shadow-lg">
                <div class="border-b border-gray-700 px-5 py-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 id="uwpDetailTitle" class="text-lg font-semibold text-white">Select an output</h2>
                        <span id="uwpDetailTypeBadge" class="hidden rounded-md border px-2 py-1 text-xs font-medium"></span>
                        <span id="uwpDetailWeight" class="hidden text-sm font-semibold text-slate-300"></span>
                    </div>
                </div>
                <div class="border-b border-gray-700 px-5">
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
                        <div class="overflow-hidden rounded-lg border border-gray-700 bg-gray-900/40">
                            <table class="min-w-full text-sm text-slate-200">
                                <thead class="bg-gray-900/50 text-xs uppercase tracking-wide text-slate-400">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Indicator</th>
                                        <th class="px-4 py-3 text-left">Target</th>
                                        <th class="px-4 py-3 text-left">Standards</th>
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

        <div class="flex flex-wrap items-center justify-end gap-3 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 px-5 py-4 shadow-lg">
            <a href="{{ route('uwp.excel.export', ['uwp' => (int) ($uwp['id'] ?? 0)]) }}"
               class="rounded-lg border border-gray-600 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-gray-800 {{ empty($uwp['id']) ? 'pointer-events-none opacity-60' : '' }}">
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

    {{-- Assignees Modal --}}
    <div id="assignees-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-slate-950/80 px-4 py-6 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-md rounded-2xl border border-slate-700/40 bg-slate-900 shadow-2xl shadow-black/40" style="animation:fadeInScale .2s ease-out">
            {{-- Header --}}
            <div class="flex items-center justify-between rounded-t-2xl px-5 py-4" style="background:linear-gradient(135deg,rgba(6,182,212,.08),rgba(99,102,241,.06))">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-500/10 text-cyan-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-white">Assigned Employees</h2>
                        <p class="text-xs text-slate-400">Task Assignees</p>
                    </div>
                </div>
                <button type="button" data-assignees-modal-close class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-800/60 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            {{-- List --}}
            <div id="assignees-modal-list" class="max-h-[60vh] overflow-y-auto px-3 py-3">
                <!-- Assignee items injected here -->
            </div>
            {{-- Footer --}}
            <div class="border-t border-gray-700/40 px-5 py-3">
                <button type="button" data-assignees-modal-close class="w-full rounded-xl bg-slate-800/80 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-slate-700">Close</button>
            </div>
        </div>
    </div>
    <style>
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    {{-- Standards Modal --}}
    <div id="standards-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[75] hidden flex items-center justify-center bg-slate-950/80 px-4 py-6 backdrop-blur-sm transition-opacity">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-700/40 bg-slate-900 shadow-2xl shadow-black/40" style="animation:fadeInScale .2s ease-out">
            <div class="flex items-center justify-between rounded-t-2xl px-5 py-4" style="background:linear-gradient(135deg,rgba(59,130,246,.08),rgba(99,102,241,.06))">
                <div>
                    <h2 class="text-base font-semibold text-white">Success Indicator Standards</h2>
                    <p class="text-xs text-slate-400">
                        Indicator: <span id="standards-modal-indicator" class="font-semibold text-slate-200">--</span>
                    </p>
                </div>
                <button type="button" data-standards-modal-close class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-800/60 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="max-h-[65vh] overflow-y-auto px-5 py-4">
                <div class="overflow-hidden rounded-xl border border-gray-700">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-slate-950/70 text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left">Rating</th>
                                <th class="px-4 py-3 text-left">Q</th>
                                <th class="px-4 py-3 text-left">E</th>
                                <th class="px-4 py-3 text-left">T</th>
                            </tr>
                        </thead>
                        <tbody id="standards-modal-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="border-t border-gray-700/40 px-5 py-3">
                <button type="button" data-standards-modal-close class="w-full rounded-xl bg-slate-800/80 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-slate-700">Close</button>
            </div>
        </div>
    </div>

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

                const assigneesModal = document.getElementById('assignees-modal');
                const assigneesList = document.getElementById('assignees-modal-list');
                const standardsModal = document.getElementById('standards-modal');
                const standardsModalBody = document.getElementById('standards-modal-body');
                const standardsModalIndicator = document.getElementById('standards-modal-indicator');

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

                function normalizeStandardsPayload(indicator) {
                    const empty = {
                        5: { q: '', e: '', t: '' },
                        4: { q: '', e: '', t: '' },
                        3: { q: '', e: '', t: '' },
                        2: { q: '', e: '', t: '' },
                        1: { q: '', e: '', t: '' },
                    };

                    if (indicator?.standards_by_rating && typeof indicator.standards_by_rating === 'object') {
                        [5, 4, 3, 2, 1].forEach((rating) => {
                            const row = indicator.standards_by_rating[rating] || indicator.standards_by_rating[String(rating)] || {};
                            empty[rating] = {
                                q: String(row.q || '').trim(),
                                e: String(row.e || '').trim(),
                                t: String(row.t || '').trim(),
                            };
                        });
                        return empty;
                    }

                    const standards = Array.isArray(indicator?.qet_standards)
                        ? indicator.qet_standards
                        : (Array.isArray(indicator?.standards) ? indicator.standards : []);
                    standards.forEach((item) => {
                        const rating = Number(item?.rating ?? item?.rating_level ?? 0);
                        const dimension = String(item?.dimension || '').toLowerCase();
                        const text = String(item?.standard_text ?? item?.text ?? item?.standard ?? '').trim();
                        if (![1, 2, 3, 4, 5].includes(rating)) return;
                        if (!['q', 'e', 't'].includes(dimension)) return;
                        empty[rating][dimension] = text;
                    });

                    return empty;
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
                        btn.className = `block w-full rounded-lg border px-3 py-3 text-left transition ${isActive ? 'border-blue-400/60 bg-blue-500/10' : 'border-gray-700 bg-gray-900/40 hover:bg-gray-800/60'}`;
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
                        indicatorsTableBodyEl.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No output selected.</td></tr>';
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
                        ? indicators.map((ind) => `<div class="rounded-lg border border-gray-700 bg-gray-900/40 px-4 py-3 text-sm text-slate-100">${escapeHtml(ind.indicator_text || '--')}</div>`).join('')
                        : '<p class="text-sm text-slate-500">No success indicators.</p>';

                    indicatorsTableBodyEl.innerHTML = indicators.length
                        ? indicators.map((ind) => {
                            const assignees = (ind.assignments || []).map(a => {
                                if (!a.employee) return null;
                                return {
                                    name: a.employee.name,
                                    photo: a.employee.profile_photo_url || null,
                                    office: (a.employee.office && a.employee.office.name) ? a.employee.office.name : null
                                };
                            }).filter(Boolean);
                            const targetSummary = [ind.target_quantity, ind.target_timeline].filter(Boolean).join(' ') || '--';
                            
                            let assigneeCell = `<span class="text-slate-500">--</span>`;
                            if (assignees.length > 0) {
                                const assigneesData = escapeHtml(JSON.stringify(assignees));
                                assigneeCell = `
                                    <button type="button" class="btn-view-assignees group inline-flex items-center gap-1 text-slate-400 transition hover:text-cyan-400" title="View Assignees" data-assignees="${assigneesData}">
                                        <svg class="h-4 w-4 transition group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span class="text-xs font-medium">${assignees.length}</span>
                                    </button>
                                `;
                            }

                            const standardsData = escapeHtml(JSON.stringify(normalizeStandardsPayload(ind)));
                            const standardsCell = `
                                <button type="button" class="btn-view-standards inline-flex items-center gap-1 text-blue-300 transition hover:text-blue-200" title="View Standards" data-indicator="${escapeHtml(ind.indicator_text || '--')}" data-standards="${standardsData}">
                                    <span class="text-xs font-medium">View</span>
                                </button>
                            `;

                            return `
                                <tr class="hover:bg-slate-900/30">
                                    <td class="px-4 py-3 text-slate-100">${escapeHtml(ind.indicator_text || '--')}</td>
                                    <td class="px-4 py-3 text-slate-300 text-xs">${escapeHtml(targetSummary)}</td>
                                    <td class="px-4 py-3 text-slate-300 text-xs">${standardsCell}</td>
                                    <td class="px-4 py-3 text-slate-300 text-xs">${assigneeCell}</td>
                                </tr>`;
                        }).join('')
                        : '<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No success indicators.</td></tr>';
                }

                function openStandardsModal(indicatorName, standardsByRating) {
                    if (!standardsModal || !standardsModalBody || !standardsModalIndicator) return;

                    standardsModalIndicator.textContent = indicatorName || '--';

                    standardsModalBody.innerHTML = [5, 4, 3, 2, 1].map((rating) => {
                        const row = standardsByRating?.[rating] || standardsByRating?.[String(rating)] || {};
                        const qRaw = String(row.q || '').trim();
                        const eRaw = String(row.e || '').trim();
                        const tRaw = String(row.t || '').trim();
                        const q = qRaw ? escapeHtml(qRaw) : '<span class="text-slate-500">--</span>';
                        const e = eRaw ? escapeHtml(eRaw) : '<span class="text-slate-500">--</span>';
                        const t = tRaw ? escapeHtml(tRaw) : '<span class="text-slate-500">--</span>';

                        return `
                            <tr class="align-top">
                                <td class="px-4 py-3 font-semibold text-slate-100">${rating}</td>
                                <td class="px-4 py-3 text-slate-300 text-xs leading-6">${q}</td>
                                <td class="px-4 py-3 text-slate-300 text-xs leading-6">${e}</td>
                                <td class="px-4 py-3 text-slate-300 text-xs leading-6">${t}</td>
                            </tr>
                        `;
                    }).join('');

                    standardsModal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }

                function openAssigneesModal(assigneesData) {
                    if (!assigneesModal || !assigneesList) return;
                    assigneesList.innerHTML = '';
                    if (!assigneesData || assigneesData.length === 0) {
                        assigneesList.innerHTML = '<div class="px-4 py-8 text-center text-sm text-slate-500">No assignees found.</div>';
                    } else {
                        assigneesData.forEach(emp => {
                            const name = typeof emp === 'string' ? emp : (emp.name || 'Unknown');
                            const photo = typeof emp === 'string' ? null : (emp.photo || null);
                            const office = typeof emp === 'string' ? '' : (emp.office || '');
                            let avatarHtml = '';
                            if (photo) {
                                avatarHtml = `<img src="${escapeHtml(photo)}" alt="${escapeHtml(name)}" class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 ring-slate-700/60">`;
                            } else {
                                const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                                avatarHtml = `<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-slate-700 to-slate-800 text-xs font-bold text-slate-300 ring-2 ring-slate-700/60">${escapeHtml(initials)}</div>`;
                            }

                            assigneesList.innerHTML += `
                                <div class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-slate-800/40">
                                    ${avatarHtml}
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-slate-100">${escapeHtml(name)}</p>
                                        ${office ? `<p class="truncate text-xs text-slate-500">${escapeHtml(office)}</p>` : ''}
                                    </div>
                                </div>
                            `;
                        });
                    }
                    assigneesModal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }

                function closeAssigneesModal() {
                    if (!assigneesModal) return;
                    assigneesModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }

                function closeStandardsModal() {
                    if (!standardsModal) return;
                    standardsModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
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

                if (indicatorsTableBodyEl) {
                    indicatorsTableBodyEl.addEventListener('click', (e) => {
                        const standardsBtn = e.target.closest('.btn-view-standards');
                        if (standardsBtn) {
                            try {
                                let dataStr = standardsBtn.getAttribute('data-standards') || '{}';
                                dataStr = dataStr.replace(/&quot;/g, '"');
                                const standards = JSON.parse(dataStr);
                                openStandardsModal(standardsBtn.getAttribute('data-indicator') || '--', standards);
                            } catch (err) {
                                console.error('Failed to parse standards data', err);
                            }
                            return;
                        }

                        const btn = e.target.closest('.btn-view-assignees');
                        if (!btn) return;
                        try {
                            // Using standard JSON parsing but handling escaped HTML quotes if necessary
                            // Using unescapeHtml logic conceptually if we had one, but we escaped quotes as &quot;
                            let dataStr = btn.getAttribute('data-assignees') || '[]';
                            // Convert &quot; back to " for JSON parsing
                            dataStr = dataStr.replace(/&quot;/g, '"');
                            const data = JSON.parse(dataStr);
                            openAssigneesModal(data);
                        } catch(err) {
                            console.error('Failed to parse assignees data', err);
                        }
                    });
                }

                document.querySelectorAll('[data-assignees-modal-close]').forEach(btn => {
                    btn.addEventListener('click', closeAssigneesModal);
                });

                document.querySelectorAll('[data-standards-modal-close]').forEach(btn => {
                    btn.addEventListener('click', closeStandardsModal);
                });
                
                if (assigneesModal) {
                    assigneesModal.addEventListener('click', (e) => {
                        if (e.target === assigneesModal) closeAssigneesModal();
                    });
                }

                if (standardsModal) {
                    standardsModal.addEventListener('click', (e) => {
                        if (e.target === standardsModal) closeStandardsModal();
                    });
                }

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
