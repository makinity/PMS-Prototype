<x-layouts.pmt>
    <section class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Stage III – PMT IPCR Review</p>
                <h1 class="mt-1 text-2xl font-bold text-white">PMT Review & Recommendation</h1>
                <p class="text-sm text-slate-400 mt-1">Data derived from locked SMPOR, Supervisor IPCR Rating, and Department Head Endorsement. PMT may adjust for calibration.</p>
            </div>
            <span class="rounded-full border border-emerald-600/50 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                Endorsed
            </span>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">IPCRs For PMT Review</h2>
                <span class="text-[11px] text-slate-400">Showing Endorsed IPCRs only</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-slate-800">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Employee Name</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Position</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Office / Unit</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Rating Period</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Overall Rating</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">IPCR Status</th>
                            <th class="px-4 py-3 text-center font-semibold border-b border-slate-800">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3">Ramon Reyes</td>
                            <td class="px-4 py-3 text-slate-300">Records Management Officer</td>
                            <td class="px-4 py-3 text-slate-300">Revenue Collection Unit</td>
                            <td class="px-4 py-3 text-slate-300">January – June 2026</td>
                            <td class="px-4 py-3 text-emerald-300 font-semibold">5.00</td>
                            <td class="px-4 py-3">
                                <span data-status-badge="row-1" class="rounded-full bg-emerald-900 px-3 py-1 text-xs font-semibold text-emerald-200 border border-emerald-800">Endorsed</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    data-ipcr-view
                                    data-row-id="row-1"
                                    class="inline-flex items-center gap-1 text-blue-400 hover:text-blue-300 font-semibold text-xs">
                                    <i class="fa-regular fa-eye text-sm"></i>
                                    <span>View IPCR</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="ipcr-view-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4">
        <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-6 py-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR Review</p>
                    <h3 class="text-lg font-semibold text-white">PMT Review Detail</h3>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400 mt-1">
                        <span id="modal-employee" class="font-semibold text-slate-100">Ramon Reyes</span>
                        <span class="text-slate-600">|</span>
                        <span id="modal-position">Records Management Officer</span>
                        <span class="text-slate-600">|</span>
                        <span id="modal-period">January – June 2026</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span data-modal-status class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-emerald-900 text-emerald-200 border border-emerald-800">
                        Endorsed
                    </span>
                    <button type="button" data-modal-hide="ipcr-view-modal" class="text-slate-400 hover:text-white">&times;</button>
                </div>
            </div>

            <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-800 border border-slate-800 rounded-lg p-5 text-sm text-slate-200">
                    <div>
                        <p class="text-xs text-slate-500">Employee</p>
                        <p class="font-semibold">Ramon Reyes</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Office / Unit</p>
                        <p class="font-semibold">Revenue Collection Unit</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Immediate Supervisor</p>
                        <p class="font-semibold">Carlo D. Beray</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Rating Period</p>
                        <p class="font-semibold">January – June 2026</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Overall Rating</p>
                        <p id="modal-overall" class="font-semibold text-emerald-300">5.00</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Department Head Remarks</p>
                        <p id="modal-dh-remarks" class="font-semibold text-slate-200">Endorsed; no adjustments from DH.</p>
                    </div>
                </div>

                <div class="bg-slate-800 border border-slate-800 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-slate-200">
                        <div class="font-semibold text-white">Supervisor-encoded (read-only)</div>
                        <p class="text-xs text-slate-400">Q - Quality | E - Efficiency | T - Timeliness (1-5)</p>
                    </div>
                    <span class="text-[11px] text-slate-400">PMT may adjust with justification (calibration only).</span>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                    <div class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">Core Functions (80%)</h4>
                        </div>
                        <span class="text-[11px] text-slate-400">System-generated</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-800/60 text-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">MFO</th>
                                    <th class="px-4 py-3 text-left font-semibold">Actual Accomplishments / Evidence</th>
                                    <th class="px-4 py-3 text-center font-semibold">Q</th>
                                    <th class="px-4 py-3 text-center font-semibold">E</th>
                                    <th class="px-4 py-3 text-center font-semibold">T</th>
                                    <th class="px-4 py-3 text-center font-semibold">Average</th>
                                    <th class="px-4 py-3 text-center font-semibold">PMT Q</th>
                                    <th class="px-4 py-3 text-center font-semibold">PMT E</th>
                                    <th class="px-4 py-3 text-center font-semibold">PMT T</th>
                                    <th class="px-4 py-3 text-center font-semibold">PMT Avg</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 text-slate-200">
                                <tr class="hover:bg-slate-800/40">
                                    <td class="px-4 py-3">
                                        E-Bank Scanning and Encoding of Revenue Transactions
                                        <span class="block text-[11px] text-slate-500 mt-1">Target: Daily; same working day</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">
                                        Completed daily scanning and encoding of e-bank transactions based on submitted ORS entries.
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">5</td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">5</td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">5</td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">5.00</td>
                                    <td class="px-4 py-3 text-center">
                                        <input style="background:#0f172a;color:#e5e7eb;" data-pmt-input type="number" min="1" max="5" step="0.25" value="5" class="w-16 rounded border border-slate-700 bg-slate-800 text-center text-white text-xs">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input style="background:#0f172a;color:#e5e7eb;" data-pmt-input type="number" min="1" max="5" step="0.25" value="5" class="w-16 rounded border border-slate-700 bg-slate-800 text-center text-white text-xs">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input style="background:#0f172a;color:#e5e7eb;" data-pmt-input type="number" min="1" max="5" step="0.25" value="5" class="w-16 rounded border border-slate-700 bg-slate-800 text-center text-white text-xs">
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-emerald-300" data-pmt-avg>5.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                    <div class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">Support Functions (20%)</h4>
                        </div>
                        <span class="text-[11px] text-slate-400">System-generated</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-800/60 text-slate-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">MFO</th>
                                    <th class="px-4 py-3 text-left font-semibold">Actual Accomplishments / Evidence</th>
                                    <th class="px-4 py-3 text-center font-semibold">Q</th>
                                    <th class="px-4 py-3 text-center font-semibold">E</th>
                                    <th class="px-4 py-3 text-center font-semibold">T</th>
                                    <th class="px-4 py-3 text-center font-semibold">Average</th>
                                    <th class="px-4 py-3 text-center font-semibold">PMT Q</th>
                                    <th class="px-4 py-3 text-center font-semibold">PMT E</th>
                                    <th class="px-4 py-3 text-center font-semibold">PMT T</th>
                                    <th class="px-4 py-3 text-center font-semibold">PMT Avg</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 text-slate-200">
                                <tr class="hover:bg-slate-800/40">
                                    <td class="px-4 py-3">
                                        Maintenance of Revenue Records Filing System
                                        <span class="block text-[11px] text-slate-500 mt-1">Target: Quarterly validation and update</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">
                                        0
                                        <span class="block text-[11px] text-slate-500 mt-1">No output logged for the period</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">5</td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">5</td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">5</td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">5.00</td>
                                    <td class="px-4 py-3 text-center">
                                        <input style="background:#0f172a;color:#e5e7eb;" data-pmt-input type="number" min="1" max="5" step="0.25" value="5" class="w-16 rounded border border-slate-700 bg-slate-800 text-center text-white text-xs">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input style="background:#0f172a;color:#e5e7eb;" data-pmt-input type="number" min="1" max="5" step="0.25" value="5" class="w-16 rounded border border-slate-700 bg-slate-800 text-center text-white text-xs">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input style="background:#0f172a;color:#e5e7eb;" data-pmt-input type="number" min="1" max="5" step="0.25" value="5" class="w-16 rounded border border-slate-700 bg-slate-800 text-center text-white text-xs">
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-emerald-300" data-pmt-avg>5.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-white">PMT Cross-Office Comparison</h4>
                            <span class="text-[11px] text-slate-400">Same position, same period</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs text-slate-200">
                                <thead class="bg-slate-800/60">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold">Office / Unit</th>
                                        <th class="px-3 py-2 text-left font-semibold">Overall Rating</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800">
                                    <tr><td class="px-3 py-2">Revenue Collection Unit</td><td class="px-3 py-2">5.00</td></tr>
                                    <tr><td class="px-3 py-2">Treasury Unit</td><td class="px-3 py-2">4.25</td></tr>
                                    <tr><td class="px-3 py-2">Budget Unit</td><td class="px-3 py-2">4.50</td></tr>
                                    <tr><td class="px-3 py-2">Accounting Unit</td><td class="px-3 py-2">4.75</td></tr>
                                    <tr class="bg-slate-800/40 font-semibold"><td class="px-3 py-2">Average</td><td class="px-3 py-2">4.63</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-white">Audit Log</h4>
                            <span class="text-[11px] text-slate-400">All changes are logged for calibration and audit.</span>
                        </div>
                        <ul class="space-y-2 text-xs text-slate-300">
                            <li>Supervisor ratings submitted – Q/E/T all 5.00 (Jan 15, 2026 10:15)</li>
                            <li>Department Head endorsed without changes (Jan 20, 2026 14:30)</li>
                            <li>PMT review opened – no adjustments yet (Jan 22, 2026 09:10)</li>
                        </ul>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span class="text-slate-200 font-semibold">PMT Overall Rating</span>
                        <input style="background:#0f172a;color:#e5e7eb;" id="pmt-overall" data-pmt-input type="number" min="1" max="5" step="0.25" value="5.00" class="w-24 rounded border border-slate-700 bg-slate-800 text-white text-center text-sm">
                        <span class="text-xs text-slate-400">Adjust only for calibration.</span>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-white">PMT Justification</label>
                        <textarea style="background:#0f172a;color:#e5e7eb;" id="pmt-justification" class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40 h-24" placeholder="Required if any PMT adjustment is entered."></textarea>
                        <p class="text-xs text-slate-400">Adjustments optional; justification required when any Q/E/T, average, or overall rating is changed.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-slate-800 px-6 py-4">
                <div class="text-xs text-slate-400">No re-entry of SMPOR data. Adjustments are for calibration only.</div>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button"
                            data-pmt-action
                            data-action-title="Recommend for Approval"
                            data-action-message="Recommending this IPCR will forward it for calibration and final approval."
                            data-action-confirm="Recommend"
                            data-action-loading="Recommending..."
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/40 hover:bg-emerald-500">
                        <span data-button-label>Recommend for Approval</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                    <button type="button"
                            data-pmt-action
                            data-action-title="Return for Review"
                            data-action-message="Return this IPCR for further review. PMT adjustments will not be saved."
                            data-action-confirm="Return"
                            data-action-loading="Returning..."
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-500 px-4 py-2 text-sm font-semibold text-amber-200 hover:bg-amber-500/10">
                        <span data-button-label>Return for Review</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-amber-200/40 border-t-amber-200"></span>
                    </button>
                    <a href="{{ route('stage3.ipcr.export.pdf') }}"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                            Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="pmt-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 id="pmt-action-title" class="text-lg font-semibold text-white">Action</h2>
                    <p id="pmt-action-body" class="mt-1 text-sm text-slate-400">Prototype action preview.</p>
                </div>
                <button type="button" data-pmt-modal-close class="text-slate-400 hover:text-white">x</button>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-pmt-modal-close class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
                <button type="button" id="pmt-action-confirm" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <span data-button-label>Proceed</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const viewModal = document.getElementById('ipcr-view-modal');
        const actionModal = document.getElementById('pmt-action-modal');
        const actionTitle = document.getElementById('pmt-action-title');
        const actionBody = document.getElementById('pmt-action-body');
        const actionConfirm = document.getElementById('pmt-action-confirm');
        const statusBadges = document.querySelectorAll('[data-status-badge]');
        const modalStatus = document.querySelector('[data-modal-status]');
        const rowStatusField = document.querySelector('[data-modal-status-field]');
        const overallInput = document.getElementById('pmt-overall');
        const justification = document.getElementById('pmt-justification');
        let activeAction = null;

        const setButtonLoading = (button, isLoading, loadingText) => {
            if (!button) return;
            const label = button.querySelector('[data-button-label]');
            const spinner = button.querySelector('[data-button-spinner]');
            if (label && !button.dataset.originalLabel) {
                button.dataset.originalLabel = label.textContent.trim();
            }
            if (isLoading) {
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-wait');
                spinner?.classList.remove('hidden');
                if (label && loadingText) label.textContent = loadingText;
            } else {
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-wait');
                spinner?.classList.add('hidden');
                if (label && button.dataset.originalLabel) label.textContent = button.dataset.originalLabel;
            }
        };

        const toggleModal = (modalEl, show) => {
            if (!modalEl) return;
            modalEl.classList.toggle('hidden', !show);
            modalEl.classList.toggle('flex', show);
            modalEl.setAttribute('aria-hidden', show ? 'false' : 'true');
        };

        document.querySelectorAll('[data-ipcr-view]').forEach((btn) => {
            btn.addEventListener('click', () => {
                viewModal.dataset.rowId = btn.dataset.rowId || '';
                toggleModal(viewModal, true);
            });
        });

        document.querySelectorAll('[data-pmt-action]').forEach((btn) => {
            btn.addEventListener('click', () => {
                activeAction = btn;
                actionTitle.textContent = btn.dataset.actionTitle || 'Action';
                actionBody.textContent = btn.dataset.actionMessage || 'Prototype action preview.';
                actionConfirm.dataset.actionLoading = btn.dataset.actionLoading || 'Working...';
                actionConfirm.querySelector('[data-button-label]').textContent = btn.dataset.actionConfirm || 'Proceed';
                toggleModal(actionModal, true);
            });
        });

        actionConfirm.addEventListener('click', () => {
            setButtonLoading(actionConfirm, true, actionConfirm.dataset.actionLoading);
            if (activeAction) setButtonLoading(activeAction, true, activeAction.dataset.actionLoading || actionConfirm.dataset.actionLoading);

            setTimeout(() => {
                const targetId = viewModal?.dataset.rowId || '';
                if (activeAction?.dataset.actionConfirm === 'Recommend') {
                    statusBadges.forEach((badge) => {
                        if (badge.dataset.statusBadge === targetId) {
                            badge.textContent = 'Recommended';
                            badge.classList.remove('bg-emerald-900','text-emerald-200','border-emerald-800');
                            badge.classList.add('bg-blue-900','text-blue-200','border-blue-800');
                        }
                    });
                    if (modalStatus) {
                        modalStatus.textContent = 'Recommended';
                        modalStatus.classList.remove('bg-emerald-900','text-emerald-200','border-emerald-800');
                        modalStatus.classList.add('bg-blue-900','text-blue-200','border-blue-800');
                    }
                    viewModal.querySelectorAll('input[data-pmt-input], textarea').forEach((el) => {
                        el.disabled = true;
                        el.classList.add('opacity-70','cursor-not-allowed');
                    });
                    document.querySelectorAll('[data-pmt-action]').forEach((btn) => {
                        btn.disabled = true;
                        btn.classList.add('opacity-70','cursor-not-allowed');
                    });
                }
                if (activeAction?.dataset.actionConfirm === 'Return') {
                    statusBadges.forEach((badge) => {
                        if (badge.dataset.statusBadge === targetId) {
                            badge.textContent = 'Returned for Review';
                            badge.classList.remove('bg-emerald-900','text-emerald-200','border-emerald-800');
                            badge.classList.add('bg-amber-900','text-amber-200','border-amber-800');
                        }
                    });
                    if (modalStatus) {
                        modalStatus.textContent = 'Returned for Review';
                        modalStatus.classList.remove('bg-emerald-900','text-emerald-200','border-emerald-800');
                        modalStatus.classList.add('bg-amber-900','text-amber-200','border-amber-800');
                    }
                }
                setButtonLoading(actionConfirm, false);
                if (activeAction) setButtonLoading(activeAction, false);
                toggleModal(actionModal, false);
                toggleModal(viewModal, false);
            }, 1200);
        });

        actionModal.addEventListener('click', (event) => {
            if (event.target === actionModal) toggleModal(actionModal, false);
        });
        actionModal.querySelectorAll('[data-pmt-modal-close]').forEach((btn) => btn.addEventListener('click', () => toggleModal(actionModal, false)));

        document.querySelectorAll('[data-modal-hide="ipcr-view-modal"]').forEach((btn) => {
            btn.addEventListener('click', () => toggleModal(viewModal, false));
        });
        viewModal?.addEventListener('click', (event) => {
            if (event.target === viewModal) toggleModal(viewModal, false);
        });

        const recomputeAverages = () => {
            document.querySelectorAll('[data-pmt-avg]').forEach((cell) => {
                const row = cell.closest('tr');
                const inputs = row ? row.querySelectorAll('input[data-pmt-input]') : [];
                if (!inputs.length) return;
                const vals = Array.from(inputs).map((i) => Number(i.value) || 0);
                const avg = vals.reduce((a, b) => a + b, 0) / vals.length;
                cell.textContent = avg.toFixed(2);
            });
        };
        document.querySelectorAll('input[data-pmt-input]').forEach((input) => {
            input.addEventListener('input', recomputeAverages);
        });
        recomputeAverages();

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                toggleModal(viewModal, false);
                toggleModal(actionModal, false);
            }
        });
    });
    </script>
    @endpush
</x-layouts.pmt>
