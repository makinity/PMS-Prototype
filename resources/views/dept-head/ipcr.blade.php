<x-layouts.dept-head>
    <div class="space-y-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">Individual Performance Commitment and Review (IPCR)</h1>
                <p class="text-sm text-gray-400 mt-1">Department Head Review &amp; Approval</p>
            </div>
            <span class="px-3 py-1 text-xs font-medium rounded bg-yellow-900 text-yellow-300 border border-yellow-800">
                FOR APPROVAL
            </span>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Prototype Notes</h2>
                    <p class="text-sm text-gray-400">Supervisor submits IPCR with final ratings encoded from locked SMPOR data. Department Head reviews and either approves or returns.</p>
                </div>
                <span class="px-2 py-1 text-xs rounded bg-blue-900 text-blue-300 border border-blue-800">
                    PROTOTYPE
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b border-gray-700">
                <div>
                    <h2 class="text-lg font-semibold text-white">Submitted IPCRs</h2>
                    <p class="text-xs text-gray-400">Read-only list for review and approval.</p>
                </div>
                <span class="text-xs text-gray-400">Period: Q4 2025</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900 text-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Employee</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Employee ID</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Office / Unit</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Rating Period</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Overall Rating</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Status</th>
                            <th class="px-4 py-3 text-center font-semibold border-b border-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 text-gray-200">
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3">Ramon Reyes</td>
                            <td class="px-4 py-3 text-gray-300">EMP-0215</td>
                            <td class="px-4 py-3 text-gray-300">Records Management Unit</td>
                            <td class="px-4 py-3 text-gray-300">Q4 2025</td>
                            <td class="px-4 py-3 text-white font-semibold">5.00</td>
                            <td class="px-4 py-3">
                                <span data-status-badge="row-1" class="rounded-full bg-yellow-900 px-3 py-1 text-xs font-semibold text-yellow-200 border border-yellow-800">FOR APPROVAL</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    data-ipcr-view
                                    data-row-id="row-1"
                                    class="inline-flex items-center gap-1 text-blue-400 hover:text-blue-300 font-semibold text-xs">
                                    <i class="fa-regular fa-eye text-sm"></i>
                                    <span>View</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="ipcr-view-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur px-4 py-6">
        <div class="w-full max-w-6xl rounded-2xl border border-gray-700 bg-gray-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">IPCR Details</h2>
                    <p class="text-sm text-gray-400">Read-only. Approve or return with remarks.</p>
                </div>
                <button type="button" data-modal-hide="ipcr-view-modal" class="text-gray-400 hover:text-white">x</button>
            </div>

            <div class="mt-4 space-y-6 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-800 border border-gray-700 rounded-lg p-5">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Employee Name</label>
                        <input type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="Ramon Reyes" disabled>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Employee ID</label>
                        <input type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="EMP-0215" disabled>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Position</label>
                        <input type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="Records Management Officer" disabled>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Office / Unit</label>
                        <input type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="Records Management Unit" disabled>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Rating Period</label>
                        <input type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="Q4 2025" disabled>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Immediate Supervisor</label>
                        <input type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="Carlo D. Beray" disabled>
                    </div>
                </div>

                <style>
                    .ipcr-select,
                    .ipcr-input {
                        background-color: #0f172a;
                        border: 1px solid #1f2937;
                        color: #e5e7eb;
                        height: 44px;
                        border-radius: 12px;
                        font-weight: 600;
                        padding: 10px 14px;
                        width: 100%;
                    }
                </style>

                <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-lg text-white">Core Functions <span class="text-sm text-gray-400">(80%)</span></h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-700 text-sm">
                            <thead class="bg-gray-900">
                                <tr>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Expected Output</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Actual Accomplishments / Evidence</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-20">Q</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-20">E</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-20">T</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-24">Average</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-48">Supervisor Remarks</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-48">Employee Comment</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <tr class="hover:bg-gray-750">
                                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                        Scan and digitize e-bank transaction records
                                        <span class="block text-[11px] text-gray-500 mt-1">Target: 100 records per quarter</span>
                                    </td>
                                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                        Scanned and uploaded 120 transaction records (3 hours total). System-generated from SMPOR; validated and locked before IPCR.
                                    </td>
                                    <td class="border border-gray-700 px-4 py-3 text-white font-semibold">5</td>
                                    <td class="border border-gray-700 px-4 py-3 text-white font-semibold">5</td>
                                    <td class="border border-gray-700 px-4 py-3 text-white font-semibold">5</td>
                                    <td class="border border-gray-700 px-4 py-3 text-white font-semibold">5.00</td>
                                    <td class="border border-gray-700 px-4 py-3 text-gray-200">Supervisor rating encoded; aligns with locked SMPOR output.</td>
                                    <td class="border border-gray-700 px-4 py-3 text-gray-200">Acknowledged.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-lg text-white">Support Functions <span class="text-sm text-gray-400">(20%)</span></h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-700 text-sm">
                            <thead class="bg-gray-900">
                                <tr>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Expected Output</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Actual Accomplishments / Evidence</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-20">Q</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-20">E</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-20">T</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-24">Average</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-48">Supervisor Remarks</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-48">Employee Comment</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <tr class="hover:bg-gray-750">
                                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                        Reference and archival support
                                        <span class="block text-[11px] text-gray-500 mt-1">Target: Fulfill reference pulls within 1 business day</span>
                                    </td>
                                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                        Serviced internal reference requests and archival pulls within agreed timelines. System-generated from SMPOR; no additional outputs recorded here.
                                    </td>
                                    <td class="border border-gray-700 px-4 py-3 text-white font-semibold">5</td>
                                    <td class="border border-gray-700 px-4 py-3 text-white font-semibold">5</td>
                                    <td class="border border-gray-700 px-4 py-3 text-white font-semibold">5</td>
                                    <td class="border border-gray-700 px-4 py-3 text-white font-semibold">5.00</td>
                                    <td class="border border-gray-700 px-4 py-3 text-gray-200">Supervisor rating encoded; support functions align with SMPOR.</td>
                                    <td class="border border-gray-700 px-4 py-3 text-gray-200">Acknowledged.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-3">
                    <label class="block text-sm font-medium text-white">Department Head Remarks</label>
                    <textarea id="dh-remarks" class="ipcr-input h-24" placeholder="Add review notes for approval or return..."></textarea>
                    <p class="text-xs text-gray-400">Remarks are required when returning.</p>
                </div>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-gray-700 pt-4">
                <div class="flex items-center text-sm text-gray-400">
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Actions are limited to approval or return; ratings are final.
                </div>
                <div class="flex gap-3">
                    <button type="button"
                            data-employee-action
                            data-action-title="Approve IPCR"
                            data-action-message="Approving this IPCR will lock ratings and complete the review cycle."
                            data-action-confirm="Approve"
                            data-action-loading="Approving..."
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg focus:ring-4 focus:ring-emerald-800 transition-colors duration-200">
                        <span data-button-label>Approve IPCR</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                    <button type="button"
                            data-employee-action
                            data-action-title="Return to Supervisor"
                            data-action-message="Returning this IPCR will require supervisor revisions."
                            data-action-confirm="Return"
                            data-action-loading="Returning..."
                            class="inline-flex items-center gap-2 px-5 py-2.5 border border-red-500 text-red-300 rounded-lg hover:bg-red-500/10 transition-colors duration-200">
                        <span data-button-label>Return to Supervisor</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-red-200/40 border-t-red-200"></span>
                    </button>
                    <button type="button" data-modal-hide="ipcr-view-modal" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

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
        const modal = document.getElementById('employee-action-modal');
        const title = document.getElementById('employee-action-title');
        const body = document.getElementById('employee-action-body');
        const confirmBtn = document.getElementById('employee-action-confirm');
        const viewModal = document.getElementById('ipcr-view-modal');
        const viewCloseButtons = document.querySelectorAll('[data-modal-hide="ipcr-view-modal"]');
        const statusBadges = document.querySelectorAll('[data-status-badge]');
        let activeTrigger = null;

        if (!modal || !title || !body || !confirmBtn) {
            return;
        }

        const toggleViewModal = (show) => {
            if (!viewModal) return;
            if (show) {
                viewModal.classList.remove('hidden');
                viewModal.classList.add('flex');
                viewModal.setAttribute('aria-hidden', 'false');
            } else {
                viewModal.classList.add('hidden');
                viewModal.classList.remove('flex');
                viewModal.setAttribute('aria-hidden', 'true');
            }
        };

        function setButtonLoading(button, isLoading, loadingText) {
            if (!button) {
                return;
            }
            const label = button.querySelector('[data-button-label]');
            const spinner = button.querySelector('[data-button-spinner]');
            if (label && !button.dataset.originalLabel) {
                button.dataset.originalLabel = label.textContent.trim();
            }

            if (isLoading) {
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-wait');
                if (spinner) {
                    spinner.classList.remove('hidden');
                }
                if (label && loadingText) {
                    label.textContent = loadingText;
                }
            } else {
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-wait');
                if (spinner) {
                    spinner.classList.add('hidden');
                }
                if (label && button.dataset.originalLabel) {
                    label.textContent = button.dataset.originalLabel;
                }
            }
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            activeTrigger = null;
            setButtonLoading(confirmBtn, false);
        }

        function openModal(trigger) {
            activeTrigger = trigger;
            title.textContent = trigger.dataset.actionTitle || 'Action';
            body.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
            confirmBtn.dataset.actionLoading = trigger.dataset.actionLoading || 'Working...';
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function updateRowStatus(targetId, status, variant) {
            statusBadges.forEach((badge) => {
                if (badge.dataset.statusBadge === targetId) {
                    badge.textContent = status;
                    badge.classList.remove('bg-yellow-900','text-yellow-200','border-yellow-800','bg-red-900','text-red-200','border-red-800','bg-emerald-900','text-emerald-200','border-emerald-800');
                    if (variant === 'approved') {
                        badge.classList.add('bg-emerald-900','text-emerald-200','border-emerald-800');
                    } else if (variant === 'returned') {
                        badge.classList.add('bg-red-900','text-red-200','border-red-800');
                    } else {
                        badge.classList.add('bg-yellow-900','text-yellow-200','border-yellow-800');
                    }
                }
            });
        }

        window.openEmployeeActionModal = openModal;

        document.querySelectorAll('[data-ipcr-view]').forEach((button) => {
            button.addEventListener('click', () => {
                viewModal.dataset.rowId = button.dataset.rowId || '';
                toggleViewModal(true);
            });
        });

        document.querySelectorAll('[data-employee-action]').forEach((button) => {
            if (button.dataset.actionRequiresValidation === 'true') {
                return;
            }
            button.addEventListener('click', function (event) {
                event.preventDefault();
                openModal(button);
            });
        });

        confirmBtn.addEventListener('click', function () {
            setButtonLoading(confirmBtn, true, confirmBtn.dataset.actionLoading);
            if (activeTrigger) {
                setButtonLoading(activeTrigger, true, activeTrigger.dataset.actionLoading || confirmBtn.dataset.actionLoading);
            }

            setTimeout(() => {
                setButtonLoading(confirmBtn, false);
                if (activeTrigger) {
                    setButtonLoading(activeTrigger, false);
                    const targetId = viewModal?.dataset.rowId || '';
                    if (activeTrigger.dataset.actionConfirm === 'Approve') {
                        updateRowStatus(targetId, 'APPROVED', 'approved');
                        if (viewModal) {
                            viewModal.querySelectorAll('[data-employee-action]').forEach(btn => {
                                btn.disabled = true;
                                btn.classList.add('opacity-60','cursor-not-allowed');
                            });
                        }
                    } else if (activeTrigger.dataset.actionConfirm === 'Return') {
                        updateRowStatus(targetId, 'RETURNED', 'returned');
                    }
                }
                toggleViewModal(false);
                closeModal();
            }, 1200);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        modal.querySelectorAll('[data-employee-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
                toggleViewModal(false);
            }
        });

        viewCloseButtons.forEach((button) => {
            button.addEventListener('click', () => toggleViewModal(false));
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
</x-layouts.dept-head>
