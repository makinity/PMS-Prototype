@extends('layouts.dept-head')

@section('main-content')
    @php
        $statusKey = strtolower((string) ($status ?? 'draft'));
        $statusColors = [
            'draft' => 'border-yellow-500/30 bg-yellow-500/10 text-yellow-300',
            'returned' => 'border-rose-500/30 bg-rose-500/10 text-rose-300',
            'submitted' => 'border-blue-500/30 bg-blue-500/10 text-blue-300',
            'consolidated' => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300',
            'endorsed' => 'border-indigo-500/30 bg-indigo-500/10 text-indigo-300',
            'pmt_approved' => 'border-purple-500/30 bg-purple-500/10 text-purple-300',
        ];
        $statusClass = $statusColors[$statusKey] ?? 'border-slate-600 bg-slate-800/40 text-slate-200';
    @endphp

    <section class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-semibold text-white">Success Indicator Workspace</h1>
                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                        {{ ucfirst(str_replace('_', ' ', (string) ($status ?? 'draft'))) }}
                    </span>
                    @if(!$canEdit || !empty($locked_at))
                        <span class="inline-flex items-center rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-200">
                            Read-only / Locked
                        </span>
                    @endif
                </div>

                <p class="mt-2 text-sm text-slate-400">
                    Output: <span class="font-semibold text-slate-200">{{ $mfo->title }}</span>
                </p>
            </div>
            
            <a href="{{ route('dept-head.opcr') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-950/50 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-900">
                &larr; Back to OPCR Preview
            </a>
        </div>

        @if (session('error'))
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid min-h-0 gap-4 lg:grid-cols-[320px_minmax(0,1fr)]">
            <aside class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Indicators</p>
                    <span id="si-indicator-count-badge" class="text-sm font-semibold text-cyan-300">0</span>
                </div>

                <div class="mt-3 flex items-center justify-between gap-3">
                    <p class="text-xs text-slate-500">Select an indicator to edit details.</p>
                    @if($canEdit)
                        <button type="button"
                                id="si-add-indicator"
                                class="inline-flex items-center gap-1 rounded-lg border border-cyan-500/40 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-200 hover:bg-cyan-500/15">
                            <span class="fa-solid fa-plus text-[10px]"></span>
                            Add
                        </button>
                    @endif
                </div>

                <div id="si-indicator-nav" class="mt-4 space-y-2 overflow-y-auto pr-1 max-h-[calc(100vh-260px)]"></div>
            </aside>

            <section class="rounded-2xl border border-slate-800 bg-slate-950/60">
                <div class="border-b border-slate-800 px-6 py-5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h2 id="si-selected-indicator-title" class="text-lg font-semibold text-white">Select an indicator</h2>
                            <p id="si-selected-indicator-subtitle" class="mt-1 text-sm text-slate-400">Use the left panel to choose an indicator.</p>
                        </div>
                    </div>
                </div>

                <div class="border-b border-slate-800 px-6">
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" data-si-tab="overview" class="border-b-2 border-cyan-400 px-2.5 py-3 text-sm font-semibold text-white">Overview</button>
                        <button type="button" data-si-tab="targets" class="border-b-2 border-transparent px-2.5 py-3 text-sm font-medium text-slate-400 hover:text-slate-200">Targets</button>
                        <button type="button" data-si-tab="standards" class="border-b-2 border-transparent px-2.5 py-3 text-sm font-medium text-slate-400 hover:text-slate-200">Standards</button>
                        <button type="button" data-si-tab="assignees" class="border-b-2 border-transparent px-2.5 py-3 text-sm font-medium text-slate-400 hover:text-slate-200">Assignees</button>
                    </div>
                </div>

                <div class="min-h-0 overflow-y-auto px-6 py-5">
                    <div data-si-panel="overview" class="space-y-4">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Indicator Text</p>
                            <textarea id="si-indicator-text"
                                      rows="3"
                                      class="mt-2 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 focus:outline-none {{ $canEdit ? '' : 'opacity-60 pointer-events-none' }}"
                                      style="background:#0f172a;color:#e5e7eb;"
                                      {{ $canEdit ? '' : 'disabled' }}></textarea>
                            @if(!$canEdit)
                                <p class="mt-2 text-xs text-slate-500">Read-only: this UWP is locked.</p>
                            @endif
                        </div>

                        <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Quick Summary</p>
                            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                                    <p class="text-xs text-slate-500">Standards</p>
                                    <p id="si-summary-standards" class="mt-1 text-lg font-semibold text-white">0</p>
                                </div>
                                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                                    <p class="text-xs text-slate-500">Assignees</p>
                                    <p id="si-summary-assignees" class="mt-1 text-lg font-semibold text-white">0</p>
                                </div>
                                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                                    <p class="text-xs text-slate-500">Target</p>
                                    <p id="si-summary-target" class="mt-1 text-sm font-semibold text-slate-200">--</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div data-si-panel="targets" class="hidden space-y-4">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Targets</p>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <label class="space-y-1">
                                    <span class="text-xs text-slate-400">Quantity</span>
                                    <input id="si-target-quantity"
                                           type="text"
                                           class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 focus:outline-none {{ $canEdit ? '' : 'opacity-60 pointer-events-none' }}"
                                           style="background:#0f172a;color:#e5e7eb;"
                                           {{ $canEdit ? '' : 'disabled' }}>
                                </label>
                                <label class="space-y-1">
                                    <span class="text-xs text-slate-400">Timeline</span>
                                    <input id="si-target-timeline"
                                           type="text"
                                           class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 focus:outline-none {{ $canEdit ? '' : 'opacity-60 pointer-events-none' }}"
                                           style="background:#0f172a;color:#e5e7eb;"
                                           {{ $canEdit ? '' : 'disabled' }}>
                                </label>
                            </div>
                            @if(!$canEdit)
                                <p class="mt-3 text-xs text-slate-500">Read-only: this UWP is locked.</p>
                            @endif
                        </div>
                    </div>

                    <div data-si-panel="standards" class="hidden space-y-4">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">QET Standards Matrix</p>
                                <p class="text-xs text-slate-500">Quality · Efficiency · Timeliness</p>
                            </div>
                            @if(!$canEdit)
                                <p class="mb-3 text-xs text-slate-500">Read-only: this UWP is locked.</p>
                            @endif
                            <div class="overflow-hidden rounded-xl border border-slate-800">
                                <table class="min-w-full text-sm" id="si-standards-matrix">
                                    <thead class="bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400">
                                        <tr>
                                            <th class="w-16 px-4 py-3 text-center font-semibold border-r border-slate-800">Rating</th>
                                            <th class="px-4 py-3 text-left font-semibold text-cyan-400/80 border-r border-slate-800">Q — Quality</th>
                                            <th class="px-4 py-3 text-left font-semibold text-violet-400/80 border-r border-slate-800">E — Efficiency</th>
                                            <th class="px-4 py-3 text-left font-semibold text-amber-400/80">T — Timeliness</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800">
                                        @foreach([5,4,3,2,1] as $r)
                                            @php
                                                $ratingColors = [5=>'text-emerald-300',4=>'text-cyan-300',3=>'text-blue-300',2=>'text-amber-300',1=>'text-rose-300'];
                                                $ratingBg = [5=>'bg-emerald-500/5',4=>'bg-cyan-500/5',3=>'bg-blue-500/5',2=>'bg-amber-500/5',1=>'bg-rose-500/5'];
                                            @endphp
                                            <tr class="{{ $ratingBg[$r] ?? '' }} align-top">
                                                <td class="w-16 px-4 py-3 text-center font-bold border-r border-slate-800 {{ $ratingColors[$r] ?? 'text-slate-300' }}">{{ $r }}</td>
                                                @foreach(['q','e','t'] as $dim)
                                                    <td class="px-3 py-2.5 align-top {{ $dim !== 't' ? 'border-r border-slate-800' : '' }}" data-si-matrix-cell data-rating="{{ $r }}" data-dimension="{{ $dim }}">
                                                        <!-- Rendered via JS -->
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <div data-si-panel="assignees" class="hidden space-y-4">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/40 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Assignees</p>
                            <div class="mt-4 overflow-hidden rounded-xl border border-slate-800">
                                <table class="min-w-full text-sm text-slate-200">
                                    <thead class="bg-slate-950/70 text-xs uppercase tracking-wide text-slate-400">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Employee</th>
                                            <th class="px-4 py-3 text-left">Office / Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody id="si-assignees-list" class="divide-y divide-slate-800"></tbody>
                                </table>
                            </div>
                            @if(!$canEdit)
                                <p class="mt-3 text-xs text-slate-500">Read-only: this UWP is locked.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="flex items-center justify-end gap-3 rounded-2xl border border-slate-800 bg-slate-950/60 px-5 py-4">
            <a href="{{ route('dept-head.opcr.index') }}"
               class="rounded-lg border border-slate-700 bg-slate-950/50 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-900">
                Back to OPCR Review
            </a>
        </div>
    </section>

    @push('scripts')
        @php
            $assignedDataArray = collect($officeEmployees ?? [])
                ->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'office_id' => $u->office_id,
                    'unit' => '—',
                ])
                ->values()
                ->all();
        @endphp
        <script>
            (function () {
                const canEdit = {{ $canEdit ? 'true' : 'false' }};
                const initialIndicators = @json($initialIndicators ?? []);
                const assignedData = @json($assignedDataArray);

                const siState = {
                    indicators: Array.isArray(initialIndicators) ? initialIndicators.map((i) => ({
                        id: i.id ?? null,
                        text: String(i.text ?? i.indicator_text ?? ''),
                        targetQuantity: i.targetQuantity ?? i.target_quantity ?? '',
                        targetTimeline: String(i.targetTimeline ?? i.target_timeline ?? ''),
                        standards: Array.isArray(i.standards) ? i.standards.map((s) => ({
                            rating: Number(s.rating ?? 3),
                            dimension: String(s.dimension ?? 'q'),
                            text: String(s.text ?? ''),
                        })) : [],
                        assignees: Array.isArray(i.assignees)
                            ? i.assignees
                                .map((x) => {
                                    if (typeof x === 'number' || typeof x === 'string') return Number(x);
                                    if (x && typeof x === 'object') return Number(x.id ?? x.employee_id ?? 0);
                                    return 0;
                                })
                                .filter((id) => Number.isFinite(id) && id > 0)
                            : [],
                        assigneeDetails: Array.isArray(i.assignees)
                            ? i.assignees
                                .filter((x) => x && typeof x === 'object')
                                .map((x) => ({
                                    id: Number(x.id ?? x.employee_id ?? 0),
                                    name: String(x.employee?.name ?? x.name ?? ''),
                                    unit: String(x.employee?.office?.name ?? x.unit ?? '—'),
                                }))
                                .filter((x) => x.id > 0 || x.name)
                            : [],
                    })) : [],
                    activeIndex: 0,
                    activeTab: 'overview',
                };

                const navEl = document.getElementById('si-indicator-nav');
                const countBadgeEl = document.getElementById('si-indicator-count-badge');
                const addBtn = document.getElementById('si-add-indicator');

                const titleEl = document.getElementById('si-selected-indicator-title');
                const subtitleEl = document.getElementById('si-selected-indicator-subtitle');
                const textEl = document.getElementById('si-indicator-text');
                const qtyEl = document.getElementById('si-target-quantity');
                const timelineEl = document.getElementById('si-target-timeline');
                const sumStandardsEl = document.getElementById('si-summary-standards');
                const sumAssigneesEl = document.getElementById('si-summary-assignees');
                const sumTargetEl = document.getElementById('si-summary-target');

                const matrixEl = document.getElementById('si-standards-matrix');

                const assigneesListEl = document.getElementById('si-assignees-list');

                const payloadInput = document.getElementById('si-indicators-payload');

                function escapeHtml(str) {
                    return String(str ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function ensureActiveIndex() {
                    if (!siState.indicators.length) {
                        siState.activeIndex = 0;
                        return;
                    }
                    siState.activeIndex = Math.max(0, Math.min(siState.activeIndex, siState.indicators.length - 1));
                }

                function getActiveIndicator() {
                    ensureActiveIndex();
                    return siState.indicators[siState.activeIndex] || null;
                }

                function renderNav() {
                    if (!navEl || !countBadgeEl) return;
                    countBadgeEl.textContent = String(siState.indicators.length || 0);
                    navEl.innerHTML = '';

                    if (!siState.indicators.length) {
                        navEl.innerHTML = '<p class="px-2 py-6 text-sm text-slate-500">No indicators yet.</p>';
                        renderDetail(null);
                        return;
                    }

                    siState.indicators.forEach((ind, idx) => {
                        const active = idx === siState.activeIndex;
                        const itemWrapper = document.createElement('div');
                        itemWrapper.className = `group relative block w-full rounded-xl border p-3 text-left transition ${active ? 'border-cyan-400/60 bg-cyan-500/10' : 'border-slate-800 bg-slate-950/30 hover:bg-slate-900/40'}`;

                        let isEditing = false;

                        function renderContent() {
                            if (isEditing) {
                                itemWrapper.innerHTML = `
                                    <div class="flex flex-col gap-2">
                                        <textarea class="w-full resize-none rounded-lg border border-slate-700 bg-slate-950 px-2 py-1.5 text-sm text-slate-100 placeholder:text-slate-600 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/30 focus:outline-none" rows="2">${escapeHtml(ind.text)}</textarea>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" class="btn-cancel rounded px-2 py-1 text-xs font-medium text-slate-400 hover:text-slate-200">Cancel</button>
                                            <button type="button" class="btn-save rounded bg-cyan-600/20 px-2 py-1 text-xs font-semibold text-cyan-400 hover:bg-cyan-600/40 hover:text-cyan-300">Save</button>
                                        </div>
                                    </div>
                                `;

                                const ta = itemWrapper.querySelector('textarea');
                                ta.focus();

                                itemWrapper.querySelector('.btn-cancel').addEventListener('click', (e) => {
                                    e.stopPropagation();
                                    isEditing = false;
                                    renderContent();
                                });
                                itemWrapper.querySelector('.btn-save').addEventListener('click', (e) => {
                                    e.stopPropagation();
                                    ind.text = ta.value;
                                    isEditing = false;
                                    if (active) renderDetail(ind);
                                    renderNav();
                                });
                                ta.addEventListener('click', (e) => e.stopPropagation());
                            } else {
                                itemWrapper.innerHTML = `
                                    <div class="cursor-pointer">
                                        <div class="line-clamp-2 pr-14 text-sm font-semibold leading-snug text-white">${escapeHtml(ind.text || 'Untitled indicator')}</div>
                                        <div class="mt-2 text-xs text-slate-400">${(ind.assignees || []).length} assignee(s) &middot; ${(ind.standards || []).length} standard(s)</div>
                                    </div>
                                    <div class="absolute right-2 top-2 flex items-center opacity-0 transition-opacity group-hover:opacity-100 ${active ? 'opacity-100' : ''}">
                                        ${canEdit ? `
                                            <button type="button" class="btn-edit p-1 text-slate-400 hover:text-cyan-400" title="Edit">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                            <button type="button" class="btn-delete p-1 text-slate-400 hover:text-rose-400" title="Delete">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        ` : ''}
                                    </div>
                                `;

                                itemWrapper.querySelector('.cursor-pointer').addEventListener('click', () => {
                                    siState.activeIndex = idx;
                                    renderNav();
                                });

                                if (canEdit) {
                                    itemWrapper.querySelector('.btn-edit').addEventListener('click', (e) => {
                                        e.stopPropagation();
                                        siState.activeIndex = idx;
                                        isEditing = true;
                                        renderContent();
                                    });
                                    itemWrapper.querySelector('.btn-delete').addEventListener('click', (e) => {
                                        e.stopPropagation();
                                        if (confirm('Are you sure you want to delete this success indicator?')) {
                                            siState.indicators.splice(idx, 1);
                                            if (siState.activeIndex >= siState.indicators.length) {
                                                siState.activeIndex = Math.max(0, siState.indicators.length - 1);
                                            }
                                            renderNav();
                                        }
                                    });
                                }
                            }
                        }

                        renderContent();
                        navEl.appendChild(itemWrapper);
                    });

                    renderDetail(getActiveIndicator());
                }

                function renderDetail(indicator) {
                    if (!titleEl || !subtitleEl) return;
                    if (!indicator) {
                        titleEl.textContent = 'Select an indicator';
                        subtitleEl.textContent = siState.indicators.length ? 'Choose an indicator from the list.' : 'Add an indicator to begin.';
                        if (textEl) textEl.value = '';
                        if (qtyEl) qtyEl.value = '';
                        if (timelineEl) timelineEl.value = '';
                        if (sumStandardsEl) sumStandardsEl.textContent = '0';
                        if (sumAssigneesEl) sumAssigneesEl.textContent = '0';
                        if (sumTargetEl) sumTargetEl.textContent = '--';
                        if (standardsListEl) standardsListEl.innerHTML = '';
                        if (assigneesListEl) assigneesListEl.innerHTML = '';
                        return;
                    }

                    titleEl.textContent = 'Indicator Details';
                    subtitleEl.textContent = 'Manage indicator text, targets, standards, and assignees.';

                    if (textEl) textEl.value = indicator.text || '';
                    if (qtyEl) qtyEl.value = indicator.targetQuantity ?? '';
                    if (timelineEl) timelineEl.value = indicator.targetTimeline ?? '';

                    if (sumStandardsEl) sumStandardsEl.textContent = String((indicator.standards || []).length);
                    if (sumAssigneesEl) sumAssigneesEl.textContent = String((indicator.assignees || []).length);
                    if (sumTargetEl) {
                        const parts = [];
                        if (indicator.targetQuantity !== null && indicator.targetQuantity !== undefined && String(indicator.targetQuantity).trim() !== '') parts.push(String(indicator.targetQuantity));
                        if (String(indicator.targetTimeline || '').trim()) parts.push(String(indicator.targetTimeline).trim());
                        sumTargetEl.textContent = parts.length ? parts.join(' ') : '--';
                    }

                    renderStandards(indicator);
                    renderAssignees(indicator);
                }

                function renderStandards(indicator) {
                    if (!matrixEl) return;
                    
                    const standardsList = Array.isArray(indicator?.standards) ? indicator.standards : [];
                    
                    matrixEl.querySelectorAll('[data-si-matrix-cell]').forEach((td) => {
                        const rating = Number(td.dataset.rating);
                        const dim = String(td.dataset.dimension);
                        
                        const stdIndex = standardsList.findIndex(s => s.rating === rating && s.dimension === dim);
                        const existingText = stdIndex >= 0 ? standardsList[stdIndex].text : '';
                        
                        let isEditing = false;
                        
                        function renderCell() {
                            if (isEditing) {
                                td.innerHTML = `
                                    <div class="flex flex-col gap-2">
                                        <textarea class="w-full resize-none rounded-lg border border-slate-700 bg-slate-950 px-2 py-1.5 text-xs text-slate-100 placeholder:text-slate-600 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/30 focus:outline-none" style="background:#0a0f1a;color:#e2e8f0;" rows="3" placeholder="Enter standard...">${escapeHtml(existingText)}</textarea>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" class="btn-cancel rounded px-2 py-1 text-[10px] font-medium text-slate-400 hover:text-slate-200">Cancel</button>
                                            <button type="button" class="btn-save rounded bg-cyan-600/20 px-2 py-1 text-[10px] font-semibold text-cyan-400 hover:bg-cyan-600/40 hover:text-cyan-300">Save</button>
                                        </div>
                                    </div>
                                `;
                                
                                const ta = td.querySelector('textarea');
                                ta.focus();
                                
                                td.querySelector('.btn-cancel').addEventListener('click', (e) => {
                                    e.stopPropagation();
                                    isEditing = false;
                                    renderCell();
                                });
                                
                                td.querySelector('.btn-save').addEventListener('click', (e) => {
                                    e.stopPropagation();
                                    const val = ta.value.trim();
                                    if (stdIndex >= 0) {
                                        if (val) standardsList[stdIndex].text = val;
                                        else standardsList.splice(stdIndex, 1);
                                    } else if (val) {
                                        standardsList.push({ rating, dimension: dim, text: val });
                                    }
                                    isEditing = false;
                                    renderDetail(indicator); // re-render to update the summary badge
                                });
                            } else {
                                if (existingText) {
                                    td.innerHTML = `
                                        <div class="group relative flex min-h-[3rem] w-full items-start justify-between gap-2 rounded-lg border border-transparent px-2 py-1.5 transition hover:border-slate-700 hover:bg-slate-800/40">
                                            <div class="whitespace-pre-wrap text-xs text-slate-300">${escapeHtml(existingText)}</div>
                                            <div class="flex shrink-0 items-center opacity-0 transition-opacity group-hover:opacity-100">
                                                ${canEdit ? `
                                                    <button type="button" class="btn-edit p-1 text-slate-400 hover:text-cyan-400" title="Edit">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                    </button>
                                                    <button type="button" class="btn-delete p-1 text-slate-400 hover:text-rose-400" title="Delete">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    `;
                                    
                                    if (canEdit) {
                                        td.querySelector('.btn-edit').addEventListener('click', (e) => {
                                            e.stopPropagation();
                                            isEditing = true;
                                            renderCell();
                                        });
                                        td.querySelector('.btn-delete').addEventListener('click', (e) => {
                                            e.stopPropagation();
                                            if (confirm('Delete this standard?')) {
                                                standardsList.splice(stdIndex, 1);
                                                renderDetail(indicator);
                                            }
                                        });
                                    }
                                } else {
                                    td.innerHTML = `
                                        <div class="flex min-h-[3rem] w-full cursor-pointer items-center justify-center rounded-lg border border-dashed border-slate-800/60 bg-slate-950/20 px-2 py-1.5 transition hover:border-slate-600 hover:bg-slate-900/40">
                                            <span class="text-[10px] font-medium uppercase tracking-wider text-slate-600">Add Standard</span>
                                        </div>
                                    `;
                                    if (canEdit) {
                                        td.firstElementChild.addEventListener('click', () => {
                                            isEditing = true;
                                            renderCell();
                                        });
                                    }
                                }
                            }
                        }
                        
                        renderCell();
                    });
                }

                function syncStandardsFromMatrix(indicator) {
                    // No longer used. State is kept in sync by inline editors directly updating the standards array.
                }

                function renderAssignees(indicator) {
                    if (!assigneesListEl) return;
                    assigneesListEl.innerHTML = '';

                    const fallbackAssignees = Array.isArray(indicator?.assigneeDetails) ? indicator.assigneeDetails : [];
                    if (!assignedData.length && fallbackAssignees.length) {
                        fallbackAssignees.forEach((emp) => {
                            const tr = document.createElement('tr');
                            tr.className = 'hover:bg-slate-900/30';
                            tr.innerHTML = `
                                <td class="px-4 py-3 text-slate-100">${escapeHtml(emp.name || '--')}</td>
                                <td class="px-4 py-3 text-slate-300">${escapeHtml(emp.unit || '—')}</td>
                            `;
                            assigneesListEl.appendChild(tr);
                        });
                        return;
                    }

                    if (!assignedData.length) {
                        assigneesListEl.innerHTML = '<tr><td colspan="2" class="px-4 py-6 text-center text-xs text-slate-500">No employees found.</td></tr>';
                        return;
                    }

                    assignedData.forEach((emp) => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-900/30';
                        tr.innerHTML = `
                            <td class="px-4 py-3 text-slate-100">${escapeHtml(emp.name || '--')}</td>
                            <td class="px-4 py-3 text-slate-300">${escapeHtml(emp.unit || '—')}</td>
                        `;
                        assigneesListEl.appendChild(tr);
                    });

                }

                function setTab(tab) {
                    siState.activeTab = tab;
                    document.querySelectorAll('[data-si-tab]').forEach((btn) => {
                        const active = btn.getAttribute('data-si-tab') === tab;
                        btn.classList.toggle('border-cyan-400', active);
                        btn.classList.toggle('text-white', active);
                        btn.classList.toggle('font-semibold', active);
                        btn.classList.toggle('border-transparent', !active);
                        btn.classList.toggle('text-slate-400', !active);
                        btn.classList.toggle('font-medium', !active);
                    });
                    document.querySelectorAll('[data-si-panel]').forEach((panel) => {
                        panel.classList.toggle('hidden', panel.getAttribute('data-si-panel') !== tab);
                    });
                }

                function addIndicator() {
                    if (!canEdit) return;
                    siState.indicators.push({
                        id: null,
                        text: 'New success indicator',
                        targetQuantity: '',
                        targetTimeline: '',
                        standards: [],
                        assignees: [],
                    });
                    siState.activeIndex = siState.indicators.length - 1;
                    renderNav();
                }

                function bindInputs() {
                    if (textEl) {
                        textEl.addEventListener('input', () => {
                            if (!canEdit) return;
                            const active = getActiveIndicator();
                            if (!active) return;
                            active.text = String(textEl.value || '');
                            renderNav();
                        });
                    }
                    if (qtyEl) {
                        qtyEl.addEventListener('input', () => {
                            if (!canEdit) return;
                            const active = getActiveIndicator();
                            if (!active) return;
                            active.targetQuantity = qtyEl.value;
                            renderDetail(active);
                        });
                    }
                    if (timelineEl) {
                        timelineEl.addEventListener('input', () => {
                            if (!canEdit) return;
                            const active = getActiveIndicator();
                            if (!active) return;
                            active.targetTimeline = timelineEl.value;
                            renderDetail(active);
                        });
                    }

                    // Matrix cell input listeners (removed)
                    // The matrix is now driven by inline components that update state directly.
                }

                function updatePayload() {
                    if (!payloadInput) return;
                    const payload = (siState.indicators || []).map((i) => ({
                        text: String(i.text || '').trim(),
                        targetQuantity: i.targetQuantity === '' ? null : i.targetQuantity,
                        targetTimeline: String(i.targetTimeline || '').trim(),
                        standards: Array.isArray(i.standards) ? i.standards.map((s) => ({
                            rating: Number(s.rating || 3),
                            dimension: String(s.dimension || 'q'),
                            text: String(s.text || '').trim(),
                        })) : [],
                        assignees: Array.isArray(i.assignees) ? i.assignees.map((x) => Number(x)) : [],
                    }));
                    payloadInput.value = JSON.stringify(payload);
                }

                // Initial tab binding
                document.querySelectorAll('[data-si-tab]').forEach((btn) => {
                    btn.addEventListener('click', () => setTab(btn.getAttribute('data-si-tab') || 'overview'));
                });

                if (addBtn) addBtn.addEventListener('click', addIndicator);

                // Form submit serialization
                const form = payloadInput ? payloadInput.closest('form') : null;
                if (form) {
                    form.addEventListener('submit', () => {
                        updatePayload();
                    });
                }

                // First paint
                if (!siState.indicators.length && canEdit) {
                    // Start with one indicator to reduce empty-state friction.
                    siState.indicators.push({ id: null, text: 'New success indicator', targetQuantity: '', targetTimeline: '', standards: [], assignees: [] });
                }
                renderNav();
                setTab('overview');
                bindInputs();
                updatePayload();
            })();
        </script>
    @endpush
@endsection
