<x-layouts.pmt>
    <section class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">IPCR Monitoring</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Approved IPCRs</h1>
                <p class="text-sm text-slate-400 mt-1">Read-only oversight. PMT cannot edit or approve.</p>
            </div>
            <span class="rounded-full border border-blue-600/50 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                Read-only
            </span>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-slate-800">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Employee</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Employee ID</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Office / Unit</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Rating Period</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Overall Rating</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Status</th>
                            <th class="px-4 py-3 text-center font-semibold border-b border-slate-800">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3">Juan Dela Cruz</td>
                            <td class="px-4 py-3 text-slate-300">EMP-0078</td>
                            <td class="px-4 py-3 text-slate-300">Administrative Services Unit</td>
                            <td class="px-4 py-3 text-slate-300">Jan - Jun 2025</td>
                            <td class="px-4 py-3 text-emerald-300 font-semibold">4.50</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300 border border-emerald-500/30">
                                    APPROVED
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <button
                                    type="button"
                                    data-ipcr-view
                                    data-unit="Administrative Services Unit"
                                    data-employee="Juan Dela Cruz"
                                    data-employee-id="EMP-0078"
                                    data-position="Administrative Assistant I"
                                    data-period="January - June 2025"
                                    data-supervisor="Maria Santos"
                                    data-overall="4.50"
                                    data-core='[{"output":"HRIS records updated","evidence":"100% of records updated; audit trail complete.","q":5,"e":5,"t":4,"avg":"4.67","sup":"Strong data hygiene; continue monthly audits.","emp":"Noted and acknowledged."}]'
                                    data-support='[{"output":"Reports prepared","evidence":"Monthly reports submitted with supporting documents.","q":4,"e":4,"t":5,"avg":"4.33","sup":"Quality improving; maintain template use.","emp":"Will continue current process."}]'
                                    data-dh="Approved; no further action."
                                    class="inline-flex items-center gap-1 text-blue-400 hover:text-blue-300 font-semibold text-xs">
                                    <i class="fa-regular fa-eye text-sm"></i>
                                    <span>View</span>
                                </button>
                                <button
                                    type="button"
                                    data-ipcr-analyze
                                    class="inline-flex items-center gap-1 text-indigo-400 hover:text-indigo-300 font-semibold text-xs">
                                    <i class="fa-solid fa-chart-line text-sm"></i>
                                    <span>Analyze</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- VIEW MODAL --}}
    <div id="ipcr-view-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4">
        <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">View IPCR (Read-only)</p>
                    <h3 class="text-lg font-semibold text-white" id="modal-employee-name">--</h3>
                    <p class="mt-1 text-sm text-slate-400" id="modal-employee-id">--</p>
                </div>
                <button type="button" data-modal-hide="ipcr-view-modal"
                        class="rounded-full p-1 text-slate-400 transition hover:bg-slate-800 hover:text-slate-200">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-800 border border-slate-800 rounded-lg p-5">
                    <div>
                        <label class="text-xs text-slate-400">Employee</label>
                        <p class="text-sm font-semibold text-white" id="modal-employee">--</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400">Position</label>
                        <p class="text-sm font-semibold text-white" id="modal-position">--</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400">Office / Unit</label>
                        <p class="text-sm font-semibold text-white" id="modal-unit">--</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400">Rating Period</label>
                        <p class="text-sm font-semibold text-white" id="modal-period">--</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400">Supervisor</label>
                        <p class="text-sm font-semibold text-white" id="modal-supervisor">--</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400">Overall Rating</label>
                        <p class="text-sm font-semibold text-emerald-300" id="modal-overall">--</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                    <div class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">Core Functions <span class="text-xs text-slate-400">(80%)</span></h4>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-800/60 text-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Expected Output</th>
                                    <th class="px-4 py-3 text-left font-semibold">Actual Accomplishments / Evidence</th>
                                    <th class="px-4 py-3 text-left font-semibold">Q</th>
                                    <th class="px-4 py-3 text-left font-semibold">E</th>
                                    <th class="px-4 py-3 text-left font-semibold">T</th>
                                    <th class="px-4 py-3 text-left font-semibold">Average</th>
                                    <th class="px-4 py-3 text-left font-semibold">Supervisor Remarks</th>
                                    <th class="px-4 py-3 text-left font-semibold">Employee Comment</th>
                                </tr>
                            </thead>
                            <tbody id="modal-core-rows" class="divide-y divide-slate-800 text-slate-200"></tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                    <div class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">Support Functions <span class="text-xs text-slate-400">(20%)</span></h4>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-800/60 text-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Expected Output</th>
                                    <th class="px-4 py-3 text-left font-semibold">Actual Accomplishments / Evidence</th>
                                    <th class="px-4 py-3 text-left font-semibold">Q</th>
                                    <th class="px-4 py-3 text-left font-semibold">E</th>
                                    <th class="px-4 py-3 text-left font-semibold">T</th>
                                    <th class="px-4 py-3 text-left font-semibold">Average</th>
                                    <th class="px-4 py-3 text-left font-semibold">Supervisor Remarks</th>
                                    <th class="px-4 py-3 text-left font-semibold">Employee Comment</th>
                                </tr>
                            </thead>
                            <tbody id="modal-support-rows" class="divide-y divide-slate-800 text-slate-200"></tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs text-slate-400 mb-1">Department Head Remarks</p>
                    <p class="text-sm text-slate-200" id="modal-dh-remarks">--</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-800 px-6 py-4">
                <button type="button" data-modal-hide="ipcr-view-modal"
                        class="rounded-lg border border-slate-700 bg-slate-800/80 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- ANALYZE MODAL --}}
    <div id="ipcr-analyze-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-300">Analyze IPCR (Oversight)</p>
                    <h3 class="text-lg font-semibold text-white">Trend Snapshot</h3>
                    <p class="mt-1 text-sm text-slate-400">Advisory only. No impact on IPCR state.</p>
                </div>
                <button type="button" data-modal-hide="ipcr-analyze-modal"
                        class="rounded-full p-1 text-slate-400 transition hover:bg-slate-800 hover:text-slate-200">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs text-slate-500">Rating distribution</p>
                        <p class="mt-1 text-sm font-semibold text-emerald-300">Q: 5, E: 4.5, T: 4.8</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs text-slate-500">Core vs Support</p>
                        <p class="mt-1 text-sm font-semibold text-slate-200">Core 80% / Support 20%</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs text-slate-500">Period comparison</p>
                        <p class="mt-1 text-sm font-semibold text-slate-200">Stable vs previous cycle</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Advisory Comment (optional)</label>
                    <textarea
                        id="analyze-comment"
                        rows="3"
                        placeholder="Add non-blocking PMT guidance here."
                        class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                        style="background:#0f172a;color:#e5e7eb;"
                    ></textarea>
                    <p class="text-xs text-slate-500">Comments do not affect IPCR state or ratings.</p>
                    <button
                        type="button"
                        id="send-comment-btn"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-900/40 transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-70">
                        Send Comment
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-800 px-6 py-4">
                <button type="button" data-modal-hide="ipcr-analyze-modal"
                        class="rounded-lg border border-slate-700 bg-slate-800/80 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const viewModal = document.getElementById('ipcr-view-modal');
                const analyzeModal = document.getElementById('ipcr-analyze-modal');
                const coreRows = document.getElementById('modal-core-rows');
                const supportRows = document.getElementById('modal-support-rows');
                const sendCommentBtn = document.getElementById('send-comment-btn');

                const fields = {
                    employeeName: document.getElementById('modal-employee-name'),
                    employeeId: document.getElementById('modal-employee-id'),
                    employee: document.getElementById('modal-employee'),
                    position: document.getElementById('modal-position'),
                    unit: document.getElementById('modal-unit'),
                    period: document.getElementById('modal-period'),
                    supervisor: document.getElementById('modal-supervisor'),
                    overall: document.getElementById('modal-overall'),
                    dh: document.getElementById('modal-dh-remarks'),
                };

                const toggleModal = (modalEl, show) => {
                    if (!modalEl) return;
                    if (show) {
                        modalEl.classList.remove('hidden');
                        modalEl.classList.add('flex');
                        modalEl.setAttribute('aria-hidden', 'false');
                    } else {
                        modalEl.classList.add('hidden');
                        modalEl.classList.remove('flex');
                        modalEl.setAttribute('aria-hidden', 'true');
                    }
                };

                const renderRows = (rows, target) => {
                    if (!target) return;
                    target.innerHTML = '';
                    (rows || []).forEach((row) => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-800/40';
                        tr.innerHTML = `
                            <td class="px-4 py-3 text-slate-100">${row.output || '--'}</td>
                            <td class="px-4 py-3 text-slate-300">${row.evidence || '--'}</td>
                            <td class="px-4 py-3 text-slate-100 font-semibold">${row.q ?? '--'}</td>
                            <td class="px-4 py-3 text-slate-100 font-semibold">${row.e ?? '--'}</td>
                            <td class="px-4 py-3 text-slate-100 font-semibold">${row.t ?? '--'}</td>
                            <td class="px-4 py-3 text-slate-100 font-semibold">${row.avg || '--'}</td>
                            <td class="px-4 py-3 text-slate-200">${row.sup || '--'}</td>
                            <td class="px-4 py-3 text-slate-200">${row.emp || '--'}</td>
                        `;
                        target.appendChild(tr);
                    });
                };

                document.querySelectorAll('[data-ipcr-view]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const coreData = btn.dataset.core ? JSON.parse(btn.dataset.core) : [];
                        const supportData = btn.dataset.support ? JSON.parse(btn.dataset.support) : [];

                        if (fields.employeeName) fields.employeeName.textContent = btn.dataset.employee || '--';
                        if (fields.employeeId) fields.employeeId.textContent = btn.dataset.employeeId || '--';
                        if (fields.employee) fields.employee.textContent = btn.dataset.employee || '--';
                        if (fields.position) fields.position.textContent = btn.dataset.position || '--';
                        if (fields.unit) fields.unit.textContent = btn.dataset.unit || '--';
                        if (fields.period) fields.period.textContent = btn.dataset.period || '--';
                        if (fields.supervisor) fields.supervisor.textContent = btn.dataset.supervisor || '--';
                        if (fields.overall) fields.overall.textContent = btn.dataset.overall || '--';
                        if (fields.dh) fields.dh.textContent = btn.dataset.dh || '--';

                        renderRows(coreData, coreRows);
                        renderRows(supportData, supportRows);

                        toggleModal(viewModal, true);
                    });
                });

                document.querySelectorAll('[data-ipcr-analyze]').forEach((btn) => {
                    btn.addEventListener('click', () => toggleModal(analyzeModal, true));
                });

                [viewModal, analyzeModal].forEach((modalEl) => {
                    modalEl?.addEventListener('click', (event) => {
                        if (event.target === modalEl) {
                            toggleModal(modalEl, false);
                        }
                    });
                });

                document.querySelectorAll('[data-modal-hide="ipcr-view-modal"]').forEach((btn) => {
                    btn.addEventListener('click', () => toggleModal(viewModal, false));
                });
                document.querySelectorAll('[data-modal-hide="ipcr-analyze-modal"]').forEach((btn) => {
                    btn.addEventListener('click', () => toggleModal(analyzeModal, false));
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
