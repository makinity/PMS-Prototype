@extends('layouts.supervisor')

@section('main-content')
    @php
        $mpors = $mpors ?? collect();
        $selectedEmployeeId = $selectedEmployeeId ?? 0;
        $month = $month ?? now()->format('Y-m');
        $monthLabel = $monthLabel ?? now()->format('F Y');
    @endphp

    <section class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Stage II</p>
                <h1 class="mt-1 text-2xl font-bold text-white md:text-3xl">MPOR List (Supervisor)</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Showing submitted/approved/endorsed MPORs for {{ $monthLabel }}.
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('supervisor.mpor') }}"
            class="grid gap-3 rounded-2xl border border-slate-800 bg-slate-900/70 p-4 md:grid-cols-3">
            <div>
                <label class="mb-2 block text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-500">
                    Employee
                </label>
                <input type="text" name="employee_id"
                    style="background:#0f172a;color:#e5e7eb;"
                    class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-emerald-500 focus:ring-0"
                    placeholder="Search...">
            </div>

            <div>
                <label class="mb-2 block text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-500">
                    Month
                </label>
                <input type="month" name="month" value="{{ $month }}"
                    style="background:#0f172a;color:#e5e7eb;"
                    class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-emerald-500 focus:ring-0">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                    Apply Filters
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-200">
                    <thead class="text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                        <tr class="border-b border-slate-800">
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3">Office</th>
                            <th class="px-4 py-3">Month</th>
                            <th class="px-4 py-3 text-center">Rated ORS</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($mpors as $mpor)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-white">
                                    {{ $mpor->employee?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    {{ $mpor->employee?->office?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    {{ $mpor->month ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center tabular-nums">
                                    {{ (int) ($mpor->rated_ors_entries_count ?? 0) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="rounded-full border border-slate-700 bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-200">
                                        {{ strtoupper($mpor->status ?? '—') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if (empty($mpor->id) || empty($mpor->month))
                                        <span class="text-xs font-semibold text-amber-300">Invalid MPOR row</span>
                                    @else
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                data-preview-mpor
                                                data-mpor-id="{{ $mpor->id }}"
                                                data-modal-target="mporPreviewModal"
                                                data-modal-toggle="mporPreviewModal"
                                                class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-700"
                                            >
                                                Preview
                                            </button>

                                            @if (($mpor->status ?? null) === 'submitted')
                                                <form method="POST" action="{{ route('supervisor.mpor.approve', $mpor) }}" data-action-form>
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-500"
                                                    >
                                                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                                        <span data-button-label>Approve</span>
                                                    </button>
                                                </form>
                                            @endif

                                            @if (($mpor->status ?? null) === 'approved')
                                                <form method="POST" action="{{ route('supervisor.mpor.endorse', $mpor) }}" data-action-form>
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-sky-500"
                                                    >
                                                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                                        <span data-button-label>Endorse</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                    No MPOR records found for this month.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div
            id="mporPreviewModal"
            tabindex="-1"
            aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0"
        >
            <div class="relative w-full max-w-7xl p-4">
                <div class="relative flex max-h-[85vh] w-full flex-col overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                    <div class="flex shrink-0 items-start justify-between border-b border-slate-800 p-5">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Monthly Performance Output Report</p>
                            <h3 class="mt-1 text-xl font-bold text-white md:text-2xl">MONTHLY PERFORMANCE OUTPUT REPORT</h3>
                            <p class="mt-1 text-sm text-slate-400">
                                Read-only mirror of locked ORS entries with supervisor ratings.
                            </p>
                        </div>

                        <button
                            type="button"
                            data-modal-hide="mporPreviewModal"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white"
                        >
                            <span class="sr-only">Close modal</span>
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <div class="max-h-[calc(85vh-140px)] flex-1 overflow-y-auto p-5 space-y-6">
                        <div
                            id="mporModalLoading"
                            class="rounded-2xl border border-slate-800 bg-slate-900/50 p-4 text-sm text-slate-300"
                        >
                            Loading MPOR preview...
                        </div>

                        <div
                            id="mporHeaderCards"
                            class="hidden mt-2 grid gap-3 text-xs uppercase tracking-[0.3em] text-white sm:grid-cols-3"
                        >
                            <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-3">
                                <p class="text-slate-400">NAME</p>
                                <p class="mt-1 font-semibold normal-case tracking-normal" id="mporEmployeeName">--</p>
                            </div>
                            <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-3">
                                <p class="text-slate-400">OFFICE / DIVISION</p>
                                <p class="mt-1 font-semibold normal-case tracking-normal" id="mporOfficeName">--</p>
                            </div>
                            <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-3">
                                <p class="text-slate-400">MONTH</p>
                                <p class="mt-1 font-semibold normal-case tracking-normal" id="mporMonthLabel">--</p>
                            </div>
                        </div>

                        <div id="mporTableWrap" class="hidden">
                            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-[0.75rem] text-slate-200">
                                        <thead>
                                            <tr class="text-left text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                                                <th class="whitespace-nowrap px-3 py-3 align-bottom" rowspan="2">Output / Task</th>
                                                <th class="border-l border-slate-800 px-3 py-3 text-center" colspan="5">Efficiency / Quantity</th>
                                                <th class="border-l border-slate-800 px-3 py-3 text-center" colspan="5">Quality / Effectiveness</th>
                                                <th class="border-l border-slate-800 px-3 py-3 text-center" colspan="5">Timeliness</th>
                                            </tr>
                                            <tr class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">
                                                @for ($i = 0; $i < 3; $i++)
                                                    <th class="{{ $i === 0 ? 'border-l border-slate-800' : '' }} px-2 py-2 text-right">W1</th>
                                                    <th class="px-2 py-2 text-right">W2</th>
                                                    <th class="px-2 py-2 text-right">W3</th>
                                                    <th class="px-2 py-2 text-right">W4</th>
                                                    <th class="px-2 py-2 text-right font-semibold">Total</th>
                                                @endfor
                                            </tr>
                                        </thead>

                                        <tbody id="mporModalTbody" class="divide-y divide-slate-800 text-[0.75rem]"></tbody>
                                    </table>
                                </div>
                            </div>

                            <p class="mt-3 text-xs text-slate-400">
                                MPOR points = Quantity x Supervisor Rating (Q/T). Only rated ORS entries with supervisor ratings are included.
                            </p>
                        </div>

                        <div id="mporBottomCards" class="hidden grid gap-4 lg:grid-cols-2">
                            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-xs uppercase tracking-[0.3em] text-slate-400">
                                <div class="flex items-center justify-between text-[0.6rem] tracking-[0.3em] text-slate-500">
                                    <span>Week 1</span><span>Week 2</span><span>Week 3</span><span>Week 4</span><span>Total</span>
                                </div>
                                <div class="mt-2 grid grid-cols-5 text-center text-sm font-semibold text-white">
                                    <span id="kpiW1">0</span>
                                    <span id="kpiW2">0</span>
                                    <span id="kpiW3">0</span>
                                    <span id="kpiW4">0</span>
                                    <span id="kpiTotal">0</span>
                                </div>

                                <div class="my-5 border-t border-slate-700/70"></div>

                                <div class="mt-3 space-y-2 text-[0.65rem] tracking-[0.2em] text-slate-500">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="min-w-0">Included ORS entries (rated)</span>
                                        <span class="shrink-0 font-semibold text-white" id="kpiIncluded">0</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="min-w-0">Excluded entries (unrated/draft/missing)</span>
                                        <span class="shrink-0 font-semibold text-white" id="kpiExcluded">0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                <div class="flex items-center justify-between text-sm font-semibold text-white">
                                    <span>Confirmed:</span>
                                    <span class="text-slate-500">Stage II</span>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Supervisor</p>
                                        <p class="text-sm font-semibold text-white normal-case tracking-normal" id="confirmSupervisor">--</p>
                                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                                    </div>
                                    <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Employee</p>
                                        <p class="text-sm font-semibold text-white normal-case tracking-normal" id="confirmEmployee">--</p>
                                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center justify-end gap-2 border-t border-slate-800 p-5">
                        <button
                            type="button"
                            data-modal-hide="mporPreviewModal"
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form[data-action-form]').forEach((form) => {
                form.addEventListener('submit', function () {
                    const btn = form.querySelector('button[type="submit"]');
                    if (!btn) {
                        return;
                    }

                    const label = btn.querySelector('[data-button-label]');
                    const spinner = btn.querySelector('[data-button-spinner]');

                    btn.disabled = true;
                    btn.classList.add('cursor-not-allowed', 'opacity-80');

                    if (label) {
                        label.textContent = 'Processing...';
                    }

                    if (spinner) {
                        spinner.classList.remove('hidden');
                    }
                });
            });

            const loadingBox = document.getElementById('mporModalLoading');
            const headerCards = document.getElementById('mporHeaderCards');
            const tableWrap = document.getElementById('mporTableWrap');
            const bottomCards = document.getElementById('mporBottomCards');

            const elEmployeeName = document.getElementById('mporEmployeeName');
            const elOfficeName = document.getElementById('mporOfficeName');
            const elMonthLabel = document.getElementById('mporMonthLabel');

            const tbody = document.getElementById('mporModalTbody');

            const kpiW1 = document.getElementById('kpiW1');
            const kpiW2 = document.getElementById('kpiW2');
            const kpiW3 = document.getElementById('kpiW3');
            const kpiW4 = document.getElementById('kpiW4');
            const kpiTotal = document.getElementById('kpiTotal');
            const kpiIncluded = document.getElementById('kpiIncluded');
            const kpiExcluded = document.getElementById('kpiExcluded');

            const confirmSupervisor = document.getElementById('confirmSupervisor');
            const confirmEmployee = document.getElementById('confirmEmployee');

            function escapeHtml(str) {
                return String(str ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function num(value) {
                const n = Number(value);
                return Number.isFinite(n) ? n : 0;
            }

            function fmt0(value) {
                return Math.round(num(value)).toLocaleString();
            }

            function metricGroup(row, key, withBorder) {
                const borderClass = withBorder ? ' border-l border-slate-800' : '';
                const w1 = fmt0(row?.[key]?.[1] ?? 0);
                const w2 = fmt0(row?.[key]?.[2] ?? 0);
                const w3 = fmt0(row?.[key]?.[3] ?? 0);
                const w4 = fmt0(row?.[key]?.[4] ?? 0);
                const total = fmt0(row?.[`${key}Total`] ?? 0);

                return `
                    <td class="${borderClass} px-2 py-3 text-right tabular-nums">${w1}</td>
                    <td class="px-2 py-3 text-right tabular-nums">${w2}</td>
                    <td class="px-2 py-3 text-right tabular-nums">${w3}</td>
                    <td class="px-2 py-3 text-right tabular-nums">${w4}</td>
                    <td class="px-2 py-3 text-right font-semibold tabular-nums">${total}</td>
                `;
            }

            function buildSectionRow(label) {
                return `
                    <tr class="bg-slate-800/40 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                        <td class="px-3 py-2 font-semibold text-slate-200" colspan="16">${escapeHtml(label)}</td>
                    </tr>
                `;
            }

            function buildRow(row) {
                return `
                    <tr>
                        <td class="px-3 py-3 font-semibold text-white">${escapeHtml(row?.label ?? '--')}</td>
                        ${metricGroup(row, 'qty', true)}
                        ${metricGroup(row, 'qual', true)}
                        ${metricGroup(row, 'time', true)}
                    </tr>
                `;
            }

            function renderTable(sectionLabels, sectionRows) {
                const coreLabel = sectionLabels?.core ?? 'Core Functions';
                const supportLabel = sectionLabels?.support ?? 'Support Functions';

                const coreRows = Array.isArray(sectionRows?.core) ? sectionRows.core : [];
                const supportRows = Array.isArray(sectionRows?.support) ? sectionRows.support : [];

                let html = '';

                html += buildSectionRow(coreLabel);
                if (coreRows.length > 0) {
                    html += coreRows.map((row) => buildRow(row)).join('');
                } else {
                    html += '<tr><td colspan="16" class="px-3 py-6 text-center text-sm text-slate-500">No core entries available.</td></tr>';
                }

                html += buildSectionRow(supportLabel);
                if (supportRows.length > 0) {
                    html += supportRows.map((row) => buildRow(row)).join('');
                } else {
                    html += '<tr><td colspan="16" class="px-3 py-6 text-center text-sm text-slate-500">No support entries available.</td></tr>';
                }

                tbody.innerHTML = html;
            }

            function applyTotals(grandTotals, kpis, meta) {
                kpiW1.textContent = fmt0(grandTotals?.qty?.[1] ?? 0);
                kpiW2.textContent = fmt0(grandTotals?.qty?.[2] ?? 0);
                kpiW3.textContent = fmt0(grandTotals?.qty?.[3] ?? 0);
                kpiW4.textContent = fmt0(grandTotals?.qty?.[4] ?? 0);
                kpiTotal.textContent = fmt0(grandTotals?.qtyTotal ?? 0);

                kpiIncluded.textContent = fmt0(kpis?.includedRated ?? 0);
                kpiExcluded.textContent = fmt0(kpis?.excluded ?? 0);

                confirmSupervisor.textContent = meta?.supervisorName ?? '--';
                confirmEmployee.textContent = meta?.employeeName ?? '--';
            }

            async function loadMporPreview(mporId) {
                if (!mporId) {
                    return;
                }

                loadingBox.textContent = 'Loading MPOR preview...';
                loadingBox.classList.remove('hidden');

                headerCards.classList.add('hidden');
                tableWrap.classList.add('hidden');
                bottomCards.classList.add('hidden');
                tbody.innerHTML = '';

                try {
                    const url = @json(route('supervisor.mpor.show', ['mpor' => '__ID__'])).replace('__ID__', String(mporId));
                    const response = await fetch(url, {
                        headers: {
                            Accept: 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load MPOR preview.');
                    }

                    const data = await response.json();

                    const meta = data?.meta ?? {};
                    const sectionLabels = data?.sectionLabels ?? {};
                    const sectionRows = data?.sectionRows ?? {};
                    const grandTotals = data?.grandTotals ?? {};
                    const kpis = data?.kpis ?? {};

                    elEmployeeName.textContent = meta?.employeeName ?? '--';
                    elOfficeName.textContent = meta?.officeName ?? '--';
                    elMonthLabel.textContent = meta?.monthLabel ?? meta?.month ?? '--';

                    renderTable(sectionLabels, sectionRows);
                    applyTotals(grandTotals, kpis, meta);

                    loadingBox.classList.add('hidden');
                    headerCards.classList.remove('hidden');
                    tableWrap.classList.remove('hidden');
                    bottomCards.classList.remove('hidden');
                } catch (error) {
                    loadingBox.textContent = error?.message ?? 'Unable to load MPOR preview.';
                    loadingBox.classList.remove('hidden');
                }
            }

            document.querySelectorAll('[data-preview-mpor]').forEach((button) => {
                button.addEventListener('click', function () {
                    const mporId = button.getAttribute('data-mpor-id');
                    loadMporPreview(mporId);
                });
            });
        });
    </script>
@endpush
