@extends('layouts.dept-head')

@section('main-content')
    <section class="space-y-6">

        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Locked SMPOR</p>
                <h1 class="mt-1 text-2xl font-bold text-white">SMPOR Overview</h1>
                <p class="text-sm text-slate-400 mt-1">
                    System-generated, read-only snapshot of validated MPOR entries by unit. Feeds directly into IPCR/OPCR; no edits or validation here.
                </p>
                <p class="text-[11px] text-slate-500 mt-2">Derived from validated MPOR. Locked snapshot; no edits allowed.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-full border border-blue-600/50 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                    System-generated
                </span>
                <span class="rounded-full border border-emerald-600/50 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                    Locked SMPOR
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-slate-800">
                <div>
                    <h2 class="text-lg font-semibold text-white">Units</h2>
                    <p class="text-xs text-slate-400">Locked MPOR data only. Read-only; no validation or recomputation.</p>
                </div>
                <span class="text-xs text-slate-400">Lock date: Jan 7, 2026</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Office / Unit</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Supervisor</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Completion</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Missing outputs</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Total / Core / Support</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Status</th>
                            <th class="px-4 py-3 text-center font-semibold border-b border-slate-800">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3">Administrative Services Unit</td>
                            <td class="px-4 py-3 text-slate-300">Maria Santos</td>
                            <td class="px-4 py-3 text-emerald-300 font-semibold">100%</td>
                            <td class="px-4 py-3 text-slate-300">0</td>
                            <td class="px-4 py-3 text-slate-100">
                                <div class="text-sm font-semibold">100</div>
                                <div class="text-[11px] text-slate-400">80 core / 20 support</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300 border border-emerald-500/30">
                                    Locked
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    data-smpor-view
                                    data-unit="Administrative Services Unit"
                                    data-supervisor="Maria Santos"
                                    data-completion="100%"
                                    data-missing="0"
                                    data-status="Locked"
                                    data-monthly='[{"month":"October 2025","total":40,"core":30,"support":10},{"month":"November 2025","total":35,"core":28,"support":7},{"month":"December 2025","total":25,"core":22,"support":3}]'
                                    class="text-blue-400 hover:text-blue-300 text-xs font-semibold">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3">Records Management Unit</td>
                            <td class="px-4 py-3 text-slate-300">Carlo D. Beray</td>
                            <td class="px-4 py-3 text-emerald-300 font-semibold">100%</td>
                            <td class="px-4 py-3 text-slate-300">0</td>
                            <td class="px-4 py-3 text-slate-100">
                                <div class="text-sm font-semibold">100</div>
                                <div class="text-[11px] text-slate-400">80 core / 20 support</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300 border border-emerald-500/30">
                                    Locked
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    data-smpor-view
                                    data-unit="Records Management Unit"
                                    data-supervisor="Carlo D. Beray"
                                    data-completion="100%"
                                    data-missing="0"
                                    data-status="Locked"
                                    data-monthly='[{"month":"October 2025","total":40,"core":30,"support":10},{"month":"November 2025","total":35,"core":28,"support":7},{"month":"December 2025","total":25,"core":22,"support":3}]'
                                    class="text-blue-400 hover:text-blue-300 text-xs font-semibold">
                                    View
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <div id="smpor-view-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl shadow-black/40">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">SMPOR (Read-only)</p>
                    <h3 class="text-lg font-semibold text-white" id="modal-unit-name">--</h3>
                    <p class="mt-1 text-sm text-slate-400" id="modal-supervisor-name">Supervisor: --</p>
                </div>
                <button type="button" data-modal-hide="smpor-view-modal"
                        class="rounded-full p-1 text-slate-400 transition hover:bg-slate-800 hover:text-slate-200">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs text-slate-500">Completion</p>
                        <p class="mt-1 text-xl font-semibold text-emerald-300" id="modal-completion">--</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs text-slate-500">Missing outputs</p>
                        <p class="mt-1 text-xl font-semibold text-amber-300" id="modal-missing">--</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs text-slate-500">Status</p>
                        <p class="mt-1 text-sm font-semibold text-blue-200" id="modal-status">--</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs text-slate-500">Snapshot</p>
                        <p class="mt-1 text-sm font-semibold text-slate-200">Read-only</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/70 overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">Monthly rollup</h4>
                            <p class="text-xs text-slate-400">Locked MPOR data only.</p>
                        </div>
                        <p class="text-xs text-slate-400" id="modal-total-label">Totals</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-800/60 text-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Month</th>
                                    <th class="px-4 py-3 text-left font-semibold">Total outputs</th>
                                    <th class="px-4 py-3 text-left font-semibold">Core</th>
                                    <th class="px-4 py-3 text-left font-semibold">Support</th>
                                </tr>
                            </thead>
                            <tbody id="modal-monthly-rows" class="divide-y divide-slate-800 text-slate-200">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-800 px-6 py-4">
                <button type="button" data-modal-hide="smpor-view-modal"
                        class="rounded-lg border border-slate-700 bg-slate-800/80 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const viewModalId = 'smpor-view-modal';
                const modal = document.getElementById(viewModalId);
                const unitEl = document.getElementById('modal-unit-name');
                const supervisorEl = document.getElementById('modal-supervisor-name');
                const completionEl = document.getElementById('modal-completion');
                const missingEl = document.getElementById('modal-missing');
                const statusEl = document.getElementById('modal-status');
                const monthlyRows = document.getElementById('modal-monthly-rows');

                const toggleModal = (show) => {
                    if (!modal) return;
                    if (show) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        modal.setAttribute('aria-hidden', 'false');
                    } else {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        modal.setAttribute('aria-hidden', 'true');
                    }
                };

                const renderRows = (rows) => {
                    if (!monthlyRows) return;
                    monthlyRows.innerHTML = '';
                    rows.forEach((row) => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-800/40';
                        tr.innerHTML = `
                            <td class="px-4 py-3">${row.month || '--'}</td>
                            <td class="px-4 py-3 text-slate-100 font-semibold">${row.total ?? '--'}</td>
                            <td class="px-4 py-3 text-emerald-300 font-semibold">${row.core ?? '--'}</td>
                            <td class="px-4 py-3 text-blue-200 font-semibold">${row.support ?? '--'}</td>
                        `;
                        monthlyRows.appendChild(tr);
                    });
                };

                document.querySelectorAll('[data-smpor-view]').forEach((button) => {
                    button.addEventListener('click', () => {
                        if (unitEl) unitEl.textContent = button.dataset.unit || '--';
                        if (supervisorEl) supervisorEl.textContent = `Supervisor: ${button.dataset.supervisor || '--'}`;
                        if (completionEl) completionEl.textContent = button.dataset.completion || '--';
                        if (missingEl) missingEl.textContent = button.dataset.missing || '--';
                        if (statusEl) statusEl.textContent = button.dataset.status || '--';
                        const monthly = button.dataset.monthly ? JSON.parse(button.dataset.monthly) : [];
                        renderRows(monthly);
                        toggleModal(true);
                    });
                });

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        toggleModal(false);
                    }
                });

                document.querySelectorAll('[data-modal-hide="smpor-view-modal"]').forEach((button) => {
                    button.addEventListener('click', () => toggleModal(false));
                });
            });
        </script>
    @endpush
    @endsection