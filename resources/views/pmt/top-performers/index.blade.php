@extends('layouts.pmt')

@section('main-content')
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Stage IV</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Top Performers</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('pmt.top-performers.preview-pdf') }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 rounded-xl border border-rose-500/25 bg-rose-500/10 px-4 py-3 text-sm font-semibold text-rose-200 transition hover:bg-rose-500/20">
                    <i class="fa-solid fa-file-pdf text-xs"></i>
                    <span>Preview PDF</span>
                </a>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
                    <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Active Period</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $activePeriod?->name ?? '--' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Top Employees</p>
                <p class="mt-1 text-3xl font-semibold text-emerald-300">{{ $summaryCounts['top_employees'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Top Offices</p>
                <p class="mt-1 text-3xl font-semibold text-emerald-300">{{ $summaryCounts['top_offices'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <section class="space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-300">Stage IV Group</p>
                    <h2 class="mt-1 text-xl font-bold text-white">Top Performers</h2>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="inline-flex rounded-xl border border-slate-700 bg-slate-950/60 p-1">
                            <button type="button" data-tp-tab-btn="employees" class="rounded-lg bg-sky-600/20 px-4 py-2 text-xs font-semibold text-sky-200">Employees</button>
                            <button type="button" data-tp-tab-btn="offices" class="rounded-lg px-4 py-2 text-xs font-semibold text-slate-300">Offices</button>
                        </div>
                        <div class="min-w-[220px] flex-1">
                            <label for="tp-live-search" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Search</label>
                            <input id="tp-live-search" type="text" placeholder="Search name, office, adjective..."
                                style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500">
                        </div>
                        <div class="w-full min-w-[180px] sm:w-auto">
                            <label for="tp-adjective-filter" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Adjective</label>
                            <select id="tp-adjective-filter"
                                style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3 py-2 text-sm text-slate-200 [color-scheme:dark]">
                                <option value="">All</option>
                                <option value="Outstanding">Outstanding</option>
                                <option value="Very Satisfactory">Very Satisfactory</option>
                                <option value="Satisfactory">Satisfactory</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div data-tp-panel="employees" class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                    <div class="border-b border-slate-800 px-5 py-4">
                        <h3 class="text-lg font-semibold text-white">Employees</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                                <tr>
                                    <th class="px-4 py-4 text-center">Rank</th>
                                    <th class="px-4 py-4 text-left">Employee</th>
                                    <th class="px-4 py-4 text-center">Numerical</th>
                                    <th class="px-4 py-4 text-center">Adjective</th>
                                    <th class="px-4 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($topEmployees as $row)
                                    @php
                                        $rowSearch = strtolower(trim(implode(' ', [
                                            $row['employee_name'] ?? '',
                                            $row['office_name'] ?? '',
                                            $row['official_rating'] ?? '',
                                        ])));
                                    @endphp
                                    <tr class="hover:bg-slate-950/40"
                                        data-tp-row
                                        data-tp-kind="employees"
                                        data-tp-search="{{ $rowSearch }}"
                                        data-tp-adjective="{{ $row['official_rating'] ?? '' }}">
                                        <td class="px-4 py-4 text-center text-slate-300">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-4 font-medium text-white">{{ $row['employee_name'] }}</td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-300 border border-emerald-500/20">
                                                {{ number_format((float) $row['official_score'], 2) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center text-slate-200">{{ $row['official_rating'] }}</td>
                                        <td class="px-4 py-4 text-right">
                                            <button type="button"
                                                data-open-details
                                                data-details='@json($row)'
                                                class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">No top employee performers identified for the active period.</td>
                                    </tr>
                                @endforelse
                                <tr data-tp-no-match="employees" class="hidden">
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">No matching employee performers.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div data-tp-panel="offices" class="hidden overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                    <div class="border-b border-slate-800 px-5 py-4">
                        <h3 class="text-lg font-semibold text-white">Offices</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                                <tr>
                                    <th class="px-5 py-4 text-left">Office</th>
                                    <th class="px-5 py-4 text-left">Department Head</th>
                                    <th class="px-5 py-4 text-center">Numerical</th>
                                    <th class="px-5 py-4 text-center">Adjective</th>
                                    <th class="px-5 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($topOffices as $row)
                                    @php
                                        $rowSearch = strtolower(trim(implode(' ', [
                                            $row['office_name'] ?? '',
                                            $row['department_head_name'] ?? '',
                                            $row['official_rating'] ?? '',
                                        ])));
                                    @endphp
                                    <tr class="hover:bg-slate-950/40"
                                        data-tp-row
                                        data-tp-kind="offices"
                                        data-tp-search="{{ $rowSearch }}"
                                        data-tp-adjective="{{ $row['official_rating'] ?? '' }}">
                                        <td class="px-5 py-4 font-medium text-white">{{ $row['office_name'] }}</td>
                                        <td class="px-5 py-4 text-slate-300">{{ $row['department_head_name'] }}</td>
                                        <td class="px-5 py-4 text-center">
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-300 border border-emerald-500/20">
                                                {{ number_format((float) $row['official_score'], 2) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center text-slate-200">{{ $row['official_rating'] }}</td>
                                        <td class="px-5 py-4 text-right">
                                            <button type="button"
                                                data-open-details
                                                data-details='@json($row)'
                                                class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">No top office performers identified for the active period.</td>
                                    </tr>
                                @endforelse
                                <tr data-tp-no-match="offices" class="hidden">
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">No matching office performers.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <!-- Details Modal -->
    <div id="performer-details-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-4">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-emerald-300">Stage III Result Details</p>
                    <h3 id="modal-title" class="text-xl font-bold text-white">Performer Details</h3>
                </div>
                <button type="button" data-close-modal class="text-2xl leading-none text-slate-400 transition hover:text-white">&times;</button>
            </div>

            <div class="mt-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Official Score</p>
                        <p id="modal-score" class="mt-1 text-2xl font-bold text-emerald-300">--</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Official Rating</p>
                        <p id="modal-rating" class="mt-1 text-lg font-semibold text-white">--</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div id="modal-office-row">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Office</p>
                        <p id="modal-office" class="mt-0.5 text-sm font-medium text-slate-200">--</p>
                    </div>
                    <div id="modal-extra-row">
                        <p id="modal-extra-label" class="text-[10px] uppercase tracking-[0.2em] text-slate-500">--</p>
                        <p id="modal-extra-value" class="mt-0.5 text-sm font-medium text-slate-200">--</p>
                    </div>
                    <div id="modal-remarks-row">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Remarks</p>
                        <p id="modal-remarks" class="mt-0.5 text-sm font-medium text-slate-200">--</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Performance Period</p>
                        <p id="modal-period" class="mt-0.5 text-sm font-medium text-slate-200">--</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Released On</p>
                        <p id="modal-released" class="mt-0.5 text-sm font-medium text-slate-200">--</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('performer-details-modal');
            const closeBtns = modal.querySelectorAll('[data-close-modal]');

            const titleEl = document.getElementById('modal-title');
            const scoreEl = document.getElementById('modal-score');
            const ratingEl = document.getElementById('modal-rating');
            const officeEl = document.getElementById('modal-office');
            const extraLabelEl = document.getElementById('modal-extra-label');
            const extraValueEl = document.getElementById('modal-extra-value');
            const remarksEl = document.getElementById('modal-remarks');
            const periodEl = document.getElementById('modal-period');
            const releasedEl = document.getElementById('modal-released');

            function openModal(data) {
                const isEmployee = !!data.employee_name;

                titleEl.textContent = isEmployee ? data.employee_name : data.office_name;
                scoreEl.textContent = Number(data.official_score).toFixed(2);
                ratingEl.textContent = data.official_rating;
                officeEl.textContent = data.office_name;

                if (isEmployee) {
                    extraLabelEl.textContent = 'Designation';
                    extraValueEl.textContent = data.designation || '--';
                    document.getElementById('modal-office-row').classList.remove('hidden');
                } else {
                    extraLabelEl.textContent = 'Department Head';
                    extraValueEl.textContent = data.department_head_name;
                    document.getElementById('modal-office-row').classList.remove('hidden');
                }

                periodEl.textContent = data.period_name;
                remarksEl.textContent = data.remarks || '--';
                releasedEl.textContent = data.released_at ? new Date(data.released_at).toLocaleString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                }) : '--';

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }

            document.querySelectorAll('[data-open-details]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const data = JSON.parse(btn.dataset.details);
                    openModal(data);
                });
            });

            const tabButtons = Array.from(document.querySelectorAll('[data-tp-tab-btn]'));
            const panels = Array.from(document.querySelectorAll('[data-tp-panel]'));
            const liveSearchInput = document.getElementById('tp-live-search');
            const adjectiveSelect = document.getElementById('tp-adjective-filter');
            let activeTab = 'employees';

            function setActiveTab(nextTab) {
                activeTab = nextTab;
                tabButtons.forEach((btn) => {
                    const isActive = btn.dataset.tpTabBtn === nextTab;
                    btn.classList.toggle('bg-sky-600/20', isActive);
                    btn.classList.toggle('text-sky-200', isActive);
                    btn.classList.toggle('text-slate-300', !isActive);
                });
                panels.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.tpPanel !== nextTab));
                applyTopPerformerFilters();
            }

            function applyTopPerformerFilters() {
                const query = (liveSearchInput?.value || '').trim().toLowerCase();
                const adjective = adjectiveSelect?.value || '';
                const rows = Array.from(document.querySelectorAll('[data-tp-row]'));
                const tabRows = rows.filter((row) => row.dataset.tpKind === activeTab);
                let visibleCount = 0;

                rows.forEach((row) => {
                    const isTabMatch = row.dataset.tpKind === activeTab;
                    if (!isTabMatch) {
                        row.classList.add('hidden');
                        return;
                    }
                    const haystack = (row.dataset.tpSearch || '').toLowerCase();
                    const rowAdj = row.dataset.tpAdjective || '';
                    const matchesQuery = query === '' || haystack.includes(query);
                    const matchesAdjective = adjective === '' || rowAdj === adjective;
                    const shouldShow = matchesQuery && matchesAdjective;
                    row.classList.toggle('hidden', !shouldShow);
                    if (shouldShow) visibleCount += 1;
                });

                document.querySelectorAll('[data-tp-no-match]').forEach((row) => {
                    row.classList.toggle('hidden', row.dataset.tpNoMatch !== activeTab || visibleCount > 0 || tabRows.length === 0);
                });
            }

            tabButtons.forEach((btn) => btn.addEventListener('click', () => setActiveTab(btn.dataset.tpTabBtn)));
            liveSearchInput?.addEventListener('input', applyTopPerformerFilters);
            adjectiveSelect?.addEventListener('change', applyTopPerformerFilters);
            setActiveTab('employees');

            closeBtns.forEach(btn => btn.addEventListener('click', closeModal));
            modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        });
    </script>
    @endpush
@endsection
