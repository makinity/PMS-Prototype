<x-layouts.pmt>
    <section class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Summary MPOR</p>
                <h1 class="mt-1 text-2xl font-bold text-white">SMPOR Advisory</h1>
                <p class="text-sm text-slate-400 mt-1">
                    Read-only SMPOR overview across units. PMT guidance only.
                </p>
            </div>
            <span class="rounded-full border border-blue-600/50 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                Read-only
            </span>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div>
                    <label class="text-xs text-slate-400">Period</label>
                    <select class="manager-filter-select mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>Q4 2025</option>
                        <option>Q3 2025</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-slate-400">Unit</label>
                    <select class="manager-filter-select mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Units</option>
                        <option>Administrative Services</option>
                        <option>Records Management</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-slate-400">Function</label>
                    <select class="manager-filter-select mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All</option>
                        <option>Core</option>
                        <option>Support</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-slate-800">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Unit</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Supervisor</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Completion</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Core</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Support</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Status</th>
                            <th class="px-4 py-3 text-center font-semibold border-b border-slate-800">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3">Administrative Services Unit</td>
                            <td class="px-4 py-3 text-slate-300">Maria Santos</td>
                            <td class="px-4 py-3 text-emerald-300 font-semibold">96%</td>
                            <td class="px-4 py-3 text-slate-300">34</td>
                            <td class="px-4 py-3 text-slate-300">18</td>
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
                                    data-completion="96%"
                                    data-core="34"
                                    data-support="18"
                                    data-status="Locked"
                                    data-monthly='[{"month":"October 2025","total":14,"core":9,"support":5},{"month":"November 2025","total":13,"core":8,"support":5},{"month":"December 2025","total":15,"core":10,"support":5}]'
                                    class="text-blue-400 hover:text-blue-300 text-xs font-semibold">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3">Records Management Unit</td>
                            <td class="px-4 py-3 text-slate-300">Carlo D. Beray</td>
                            <td class="px-4 py-3 text-emerald-300 font-semibold">90%</td>
                            <td class="px-4 py-3 text-slate-300">31</td>
                            <td class="px-4 py-3 text-slate-300">17</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200 border border-blue-500/30">
                                    In consolidation
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    data-smpor-view
                                    data-unit="Records Management Unit"
                                    data-supervisor="Carlo D. Beray"
                                    data-completion="90%"
                                    data-core="31"
                                    data-support="17"
                                    data-status="In consolidation"
                                    data-monthly='[{"month":"October 2025","total":12,"core":7,"support":5},{"month":"November 2025","total":11,"core":6,"support":5},{"month":"December 2025","total":11,"core":7,"support":4}]'
                                    class="text-blue-400 hover:text-blue-300 text-xs font-semibold">
                                    Analyze
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <div id="pmt-smpor-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl shadow-black/40">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">SMPOR (Advisory)</p>
                    <h3 class="text-lg font-semibold text-white" id="modal-unit-name">--</h3>
                    <p class="mt-1 text-sm text-slate-400" id="modal-supervisor-name">Supervisor: --</p>
                </div>
                <button type="button" data-modal-hide="pmt-smpor-modal"
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
                        <p class="text-xs text-slate-500">Core outputs</p>
                        <p class="mt-1 text-xl font-semibold text-slate-100" id="modal-core">--</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs text-slate-500">Support outputs</p>
                        <p class="mt-1 text-xl font-semibold text-slate-100" id="modal-support">--</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs text-slate-500">Status</p>
                        <p class="mt-1 text-sm font-semibold text-blue-200" id="modal-status">--</p>
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

                <div id="modal-comment-block" class="space-y-2 hidden">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Advisory comment (optional)</label>
                    <textarea
                        id="modal-comment-text"
                        rows="3"
                        placeholder="Add non-blocking PMT guidance here."
                        class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                        style="background:#0f172a;color:#e5e7eb;"
                    ></textarea>
                    <p class="text-xs text-slate-500">Comments do not affect SMPOR state.</p>
                    <button
                        type="button"
                        id="modal-send-comment"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-900/40 transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-70">
                        Send Comment
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-800 px-6 py-4">
                <button type="button" data-modal-hide="pmt-smpor-modal"
                        class="rounded-lg border border-slate-700 bg-slate-800/80 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modalId = 'pmt-smpor-modal';
                const modal = document.getElementById(modalId);
                const unitEl = document.getElementById('modal-unit-name');
                const supervisorEl = document.getElementById('modal-supervisor-name');
                const completionEl = document.getElementById('modal-completion');
                const coreEl = document.getElementById('modal-core');
                const supportEl = document.getElementById('modal-support');
                const statusEl = document.getElementById('modal-status');
                const monthlyRows = document.getElementById('modal-monthly-rows');
                const commentBlock = document.getElementById('modal-comment-block');
                const sendCommentBtn = document.getElementById('modal-send-comment');

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
                        if (coreEl) coreEl.textContent = button.dataset.core || '--';
                        if (supportEl) supportEl.textContent = button.dataset.support || '--';
                        if (statusEl) statusEl.textContent = button.dataset.status || '--';
                        const monthly = button.dataset.monthly ? JSON.parse(button.dataset.monthly) : [];
                        renderRows(monthly);
                        if (commentBlock) {
                            const isAnalyze = (button.dataset.status || '').toLowerCase() === 'in consolidation';
                            commentBlock.classList.toggle('hidden', !isAnalyze);
                        }
                        if (sendCommentBtn) {
                            sendCommentBtn.disabled = false;
                            sendCommentBtn.innerHTML = 'Send Comment';
                            sendCommentBtn.classList.remove('cursor-not-allowed', 'opacity-70');
                        }
                        toggleModal(true);
                    });
                });

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        toggleModal(false);
                    }
                });

                document.querySelectorAll('[data-modal-hide="pmt-smpor-modal"]').forEach((button) => {
                    button.addEventListener('click', () => toggleModal(false));
                });

                sendCommentBtn?.addEventListener('click', () => {
                    if (sendCommentBtn.disabled) return;
                    const spinner = '<svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';
                    sendCommentBtn.disabled = true;
                    sendCommentBtn.innerHTML = `${spinner}<span>Sending...</span>`;
                    setTimeout(() => {
                        sendCommentBtn.disabled = false;
                        sendCommentBtn.innerHTML = 'Send Comment';
                    }, 1400);
                });
            });
        </script>
    @endpush
</x-layouts.pmt>
