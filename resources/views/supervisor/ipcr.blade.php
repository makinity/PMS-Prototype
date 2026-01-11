<x-layouts.supervisor>
    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">IPCR Reviews</h1>
                <p class="text-sm text-gray-400 mt-1">
                    Review and rate employee IPCRs derived from locked SMPOR outputs. Ratings live only inside the review modal.
                </p>
            </div>
            <span class="px-3 py-1 text-xs font-medium rounded bg-blue-900 text-blue-300 border border-blue-800">
                Supervisor Review
            </span>
        </div>

        {{-- IPCR LIST --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="p-4 border-b border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Submitted IPCRs</h2>
                    <p class="text-xs text-gray-400">Select an IPCR to view details and encode ratings in the modal.</p>
                </div>
                <span class="text-[11px] text-gray-500">Statuses: For Review / Returned / Approved</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900 text-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Employee Name</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Position</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Rating Period</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">IPCR Status</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700 w-32">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-gray-100">
                        <tr class="hover:bg-gray-800/60">
                            <td class="px-4 py-3 font-semibold">Juan Dela Cruz</td>
                            <td class="px-4 py-3 text-gray-300">Administrative Assistant I</td>
                            <td class="px-4 py-3 text-gray-300">Jan - Jun 2025</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-blue-500/10 text-blue-200 border border-blue-500/30">
                                    For Review
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-view-ipcr
                                        data-employee="Juan Dela Cruz"
                                        data-position="Administrative Assistant I"
                                        data-period="Jan - Jun 2025"
                                        data-status="For Review"
                                        class="inline-flex items-center gap-2 rounded-lg border border-gray-700 px-3 py-2 text-xs font-semibold text-gray-200 hover:bg-gray-700/70">
                                    <span class="fa-solid fa-eye text-gray-300"></span>
                                    View IPCR
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-800/60">
                            <td class="px-4 py-3 font-semibold">Maria Santos</td>
                            <td class="px-4 py-3 text-gray-300">HR Officer II</td>
                            <td class="px-4 py-3 text-gray-300">Jul - Dec 2024</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-emerald-500/10 text-emerald-200 border border-emerald-500/30">
                                    Approved
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-view-ipcr
                                        data-employee="Maria Santos"
                                        data-position="HR Officer II"
                                        data-period="Jul - Dec 2024"
                                        data-status="Approved"
                                        class="inline-flex items-center gap-2 rounded-lg border border-gray-700 px-3 py-2 text-xs font-semibold text-gray-200 hover:bg-gray-700/70">
                                    <span class="fa-solid fa-eye text-gray-300"></span>
                                    View IPCR
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- VIEW & RATE IPCR MODAL --}}
    <div id="view-ipcr-modal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/70 backdrop-blur-sm px-4 py-8">
        <div class="w-full max-w-5xl rounded-2xl border border-gray-800 bg-gray-900 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-gray-800 px-6 py-4">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR Review</p>
                    <h3 class="text-lg font-semibold text-white">View &amp; Rate IPCR</h3>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-400">
                        <span id="ipcr-employee" class="font-semibold text-gray-200">--</span>
                        <span class="text-gray-500">|</span>
                        <span id="ipcr-position">--</span>
                        <span class="text-gray-500">|</span>
                        <span id="ipcr-period">--</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span id="ipcr-status-badge" class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full border border-blue-500/40 bg-blue-500/10 text-blue-200">
                        For Review
                    </span>
                    <button type="button" data-close-ipcr class="text-gray-400 hover:text-white">
                        <span class="sr-only">Close</span>
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                    <div class="rounded-lg border border-gray-800 bg-gray-950/60 p-3">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Employee</p>
                        <p id="ipcr-employee-detail" class="font-semibold text-gray-100">--</p>
                    </div>
                    <div class="rounded-lg border border-gray-800 bg-gray-950/60 p-3">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Position</p>
                        <p id="ipcr-position-detail" class="font-semibold text-gray-100">--</p>
                    </div>
                    <div class="rounded-lg border border-gray-800 bg-gray-950/60 p-3">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Rating Period</p>
                        <p id="ipcr-period-detail" class="font-semibold text-gray-100">--</p>
                    </div>
                </div>

                {{-- Core Functions --}}
                <div class="rounded-xl border border-gray-800 bg-gray-900/70">
                    <div class="flex items-center justify-between border-b border-gray-800 px-4 py-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">Core Functions (80%)</h4>
                            <p class="text-xs text-gray-400">Expected outputs and evidence are read-only; rate Q/E/T only.</p>
                        </div>
                        <span class="text-[11px] text-gray-500">System-generated from SMPOR</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-800">
                            <thead class="bg-gray-900 text-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold border-b border-gray-800">Expected Output</th>
                                    <th class="px-4 py-3 text-left font-semibold border-b border-gray-800">Actual Accomplishments / Evidence</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">Q</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">E</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">T</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">Average</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800 text-gray-100">
                                <tr data-rating-row="core-1" class="hover:bg-gray-800/50">
                                    <td class="px-4 py-3">HRIS records updated</td>
                                    <td class="px-4 py-3 text-gray-300">100% of records updated; audit trail complete.</td>
                                    <td class="px-4 py-3 text-center">
                                        <select class="uwp-select text-white font-semibold text-center" style="min-width:72px; background:#0f172a;color:#e5e7eb;" data-rating-select="core-1">
                                            <option value="5" selected>5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <select class="uwp-select text-white font-semibold text-center" style="min-width:72px; background:#0f172a;color:#e5e7eb;" data-rating-select="core-1">
                                            <option value="5" selected>5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <select class="uwp-select text-white font-semibold text-center" style="min-width:72px; background:#0f172a;color:#e5e7eb;" data-rating-select="core-1">
                                            <option value="5" selected>5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">
                                        <span data-average-target="core-1">5.00</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Support Functions --}}
                <div class="rounded-xl border border-gray-800 bg-gray-900/70">
                    <div class="flex items-center justify-between border-b border-gray-800 px-4 py-3">
                        <div>
                            <h4 class="text-sm font-semibold text-white">Support Functions (20%)</h4>
                            <p class="text-xs text-gray-400">Rate Q/E/T; commitments and evidence remain read-only.</p>
                        </div>
                        <span class="text-[11px] text-gray-500">System-generated from SMPOR</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-800">
                            <thead class="bg-gray-900 text-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold border-b border-gray-800">Expected Output</th>
                                    <th class="px-4 py-3 text-left font-semibold border-b border-gray-800">Actual Accomplishments / Evidence</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">Q</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">E</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">T</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">Average</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800 text-gray-100">
                                <tr data-rating-row="support-1" class="hover:bg-gray-800/50">
                                    <td class="px-4 py-3">Reports prepared</td>
                                    <td class="px-4 py-3 text-gray-300">Monthly reports submitted with supporting documents.</td>
                                    <td class="px-4 py-3 text-center">
                                        <select class="uwp-select text-white font-semibold text-center" style="min-width:72px; background:#0f172a;color:#e5e7eb;" data-rating-select="support-1">
                                            <option value="5" selected>5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <select class="uwp-select text-white font-semibold text-center" style="min-width:72px; background:#0f172a;color:#e5e7eb;" data-rating-select="support-1">
                                            <option value="5" selected>5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <select class="uwp-select text-white font-semibold text-center" style="min-width:72px; background:#0f172a;color:#e5e7eb;" data-rating-select="support-1">
                                            <option value="5" selected>5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">
                                        <span data-average-target="support-1">5.00</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-white" for="ipcr-remarks">Supervisor Remarks</label>
                    <textarea id="ipcr-remarks" style="background:#0f172a;color:#e5e7eb;" class="w-full rounded-lg border border-gray-800 bg-gray-950 px-3 py-2 text-sm text-gray-100 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" rows="3" placeholder="Add remarks or justification..."></textarea>
                    <p class="text-[11px] text-gray-500">Submitting to Department Head locks ratings and remarks.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-gray-800 px-6 py-4">
                <div class="text-xs text-gray-500">This action will be recorded in the audit log.</div>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button"
                            data-employee-loading="true"
                            data-loading-text="Saving draft..."
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-200 hover:bg-gray-800/80">
                        <span data-button-label>Save Draft</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                    <button type="button"
                            data-employee-action
                            data-action-title="Submit IPCR to Department Head"
                            data-action-message="Submit this IPCR with current ratings and remarks. Further edits will be locked."
                            data-action-confirm="Submit"
                            data-action-loading="Submitting..."
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-900/40 hover:bg-blue-500">
                        <span data-button-label>Submit to Department Head</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                    <button type="button"
                            data-employee-loading="true"
                            data-loading-text="Returning..."
                            class="inline-flex items-center gap-2 rounded-lg border border-rose-500/40 bg-rose-600/10 px-4 py-2 text-sm font-semibold text-rose-200 hover:bg-rose-600/20">
                        <span data-button-label>Return to Employee</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-rose-200/40 border-t-rose-200"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div id="employee-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-gray-700 bg-gray-900 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 id="employee-action-title" class="text-lg font-semibold text-white">Action</h2>
                    <p id="employee-action-body" class="mt-1 text-sm text-gray-400">Prototype action preview.</p>
                </div>
                <button type="button" data-employee-modal-close class="text-gray-400 hover:text-white">x</button>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-employee-modal-close class="rounded-lg border border-gray-600 px-4 py-2 text-xs text-gray-300 hover:bg-gray-800">Close</button>
                <button type="button" id="employee-action-confirm" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <span data-button-label>Proceed</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const overlay = document.getElementById('view-ipcr-modal');
                const actionModal = document.getElementById('employee-action-modal');
                const actionTitle = document.getElementById('employee-action-title');
                const actionBody = document.getElementById('employee-action-body');
                const actionConfirm = document.getElementById('employee-action-confirm');
                let activeTrigger = null;

                if (!overlay || !actionModal || !actionTitle || !actionBody || !actionConfirm) {
                    return;
                }

                const employeeEls = {
                    nameTop: document.getElementById('ipcr-employee'),
                    positionTop: document.getElementById('ipcr-position'),
                    periodTop: document.getElementById('ipcr-period'),
                    badge: document.getElementById('ipcr-status-badge'),
                    nameDetail: document.getElementById('ipcr-employee-detail'),
                    positionDetail: document.getElementById('ipcr-position-detail'),
                    periodDetail: document.getElementById('ipcr-period-detail'),
                };

                function setButtonLoading(button, isLoading, loadingText) {
                    if (!button) return;
                    const label = button.querySelector('[data-button-label]');
                    const spinner = button.querySelector('[data-button-spinner]');
                    if (label && !button.dataset.originalLabel) {
                        button.dataset.originalLabel = label.textContent.trim();
                    }
                    if (isLoading) {
                        button.disabled = true;
                        button.classList.add('opacity-70', 'cursor-wait');
                        spinner && spinner.classList.remove('hidden');
                        if (label && loadingText) label.textContent = loadingText;
                    } else {
                        button.disabled = false;
                        button.classList.remove('opacity-70', 'cursor-wait');
                        spinner && spinner.classList.add('hidden');
                        if (label && button.dataset.originalLabel) label.textContent = button.dataset.originalLabel;
                    }
                }

                function updateRowAverage(rowId) {
                    const selects = overlay.querySelectorAll(`[data-rating-select="${rowId}"]`);
                    const target = overlay.querySelector(`[data-average-target="${rowId}"]`);
                    if (!selects.length || !target) return;
                    const values = Array.from(selects).map(sel => Number(sel.value) || 0);
                    const avg = values.reduce((a, b) => a + b, 0) / values.length;
                    target.textContent = avg.toFixed(2);
                }

                function bindAverages() {
                    overlay.querySelectorAll('[data-rating-row]').forEach(row => {
                        const rowId = row.dataset.ratingRow;
                        row.querySelectorAll('[data-rating-select]').forEach(sel => {
                            sel.addEventListener('change', () => updateRowAverage(rowId));
                        });
                        updateRowAverage(rowId);
                    });
                }

                function openIpcrModal(trigger) {
                    const name = trigger.dataset.employee || '--';
                    const position = trigger.dataset.position || '--';
                    const period = trigger.dataset.period || '--';
                    const status = trigger.dataset.status || 'For Review';

                    employeeEls.nameTop.textContent = name;
                    employeeEls.positionTop.textContent = position;
                    employeeEls.periodTop.textContent = period;
                    employeeEls.nameDetail.textContent = name;
                    employeeEls.positionDetail.textContent = position;
                    employeeEls.periodDetail.textContent = period;
                    employeeEls.badge.textContent = status;

                    employeeEls.badge.className = 'inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full';
                    if (status === 'Approved') {
                        employeeEls.badge.classList.add('border', 'border-emerald-500/50', 'bg-emerald-500/10', 'text-emerald-200');
                    } else if (status === 'Returned') {
                        employeeEls.badge.classList.add('border', 'border-amber-500/50', 'bg-amber-500/10', 'text-amber-200');
                    } else {
                        employeeEls.badge.classList.add('border', 'border-blue-500/50', 'bg-blue-500/10', 'text-blue-200');
                    }

                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    bindAverages();
                }

                function closeIpcrModal() {
                    overlay.classList.add('hidden');
                    overlay.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                document.querySelectorAll('[data-view-ipcr]').forEach(btn => {
                    btn.addEventListener('click', () => openIpcrModal(btn));
                });
                overlay.addEventListener('click', (event) => {
                    if (event.target === overlay) closeIpcrModal();
                });
                overlay.querySelectorAll('[data-close-ipcr]').forEach(btn => {
                    btn.addEventListener('click', closeIpcrModal);
                });

                function closeActionModal() {
                    actionModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    setButtonLoading(actionConfirm, false);
                    activeTrigger = null;
                }

                function openActionModal(trigger) {
                    activeTrigger = trigger;
                    actionTitle.textContent = trigger.dataset.actionTitle || 'Action';
                    actionBody.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
                    actionConfirm.dataset.actionLoading = trigger.dataset.actionLoading || 'Working...';
                    actionConfirm.querySelector('[data-button-label]').textContent = trigger.dataset.actionConfirm || 'Proceed';
                    actionModal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }

                window.openEmployeeActionModal = openActionModal;

                document.querySelectorAll('[data-employee-action]').forEach((button) => {
                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        openActionModal(button);
                    });
                });

                actionConfirm.addEventListener('click', function () {
                    setButtonLoading(actionConfirm, true, actionConfirm.dataset.actionLoading);
                    if (activeTrigger) {
                        setButtonLoading(activeTrigger, true, activeTrigger.dataset.actionLoading || actionConfirm.dataset.actionLoading);
                    }

                    setTimeout(() => {
                        setButtonLoading(actionConfirm, false);
                        if (activeTrigger) {
                            setButtonLoading(activeTrigger, false);
                        }
                        closeActionModal();
                    }, 1200);
                });

                actionModal.addEventListener('click', function (event) {
                    if (event.target === actionModal) {
                        closeActionModal();
                    }
                });

                actionModal.querySelectorAll('[data-employee-modal-close]').forEach((button) => {
                    button.addEventListener('click', closeActionModal);
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeActionModal();
                        closeIpcrModal();
                    }
                });

                document.querySelectorAll('[data-employee-loading="true"]').forEach((button) => {
                    button.addEventListener('click', function () {
                        if (button.dataset.loadingActive === 'true') {
                            return;
                        }
                        button.dataset.loadingActive = 'true';
                        setButtonLoading(button, true, button.dataset.loadingText || 'Loading...');

                        const duration = Number.parseInt(button.dataset.loadingDuration || '1200', 10);
                        if (!Number.isNaN(duration)) {
                            setTimeout(() => {
                                setButtonLoading(button, false);
                                button.dataset.loadingActive = 'false';
                            }, duration);
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layouts.supervisor>
