<x-layouts.employee>
    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Individual Performance Commitment and Review (IPCR)
                </h1>
                <p class="text-sm text-gray-400 mt-1">
                    Final, approved IPCR computed from locked SMPOR (Q4 2025) and supervisor-encoded ratings; employee action is acknowledgment only. Acknowledgment does not change ratings or outputs.
                </p>
                <p class="text-[11px] text-gray-500 mt-2">Read-only | No edits, validation, or scoring by employee.</p>
            </div>

            {{-- DUMMY_DATA: replace with dynamic value --}}
            <span class="px-3 py-1 text-xs font-medium rounded bg-emerald-900 text-emerald-300 border border-emerald-800">
                APPROVED - LOCKED SMPOR
            </span>
        </div>

        {{-- Prototype Clarity --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Prototype Notes</h2>
                    <p class="text-sm text-gray-400">IPCR is auto-filled from approved UWP, locked SMPOR (Q4 2025), and supervisor-encoded ratings; employee action is acknowledgment only. Employee acknowledgment occurs after Department Head approval. Acknowledgment does not change ratings or outputs.</p>
                </div>
                <span class="px-2 py-1 text-xs rounded bg-blue-900 text-blue-300 border border-blue-800">
                    PROTOTYPE
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm">
                <div class="bg-gray-700 rounded-lg p-3">
                    <p class="text-gray-400 mb-1">Data sources</p>
                    <p class="font-medium text-white">Approved UWP + Locked SMPOR (Q4 2025)</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-3">
                    <p class="text-gray-400 mb-1">Ratings</p>
                    <p class="font-medium text-white">Supervisor-encoded (locked)</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-3">
                    <p class="text-gray-400 mb-1">Alerts</p>
                    <p class="font-medium text-white">ORS/MPOR gaps resolved</p>
                </div>
            </div>

            <p class="mt-3 text-xs text-gray-400">
                IPCR status: Approved. Targets, ratings, and outputs are read-only; acknowledgment is required for audit trail.
            </p>
            <p class="mt-1 text-[11px] text-gray-500">Efficiency, Quality, and Timeliness scores are supervisor-evaluated from locked SMPOR output performance.</p>
        </div>

        {{-- EMPLOYEE INFO --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div>
                <label class="block mb-2 text-sm font-medium text-white">Employee Name</label>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <input type="text"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Ramon Reyes" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Position</label>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <input type="text"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Records Management Officer" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Office / Unit</label>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <input type="text"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Records Management Unit" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Rating Period</label>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <input type="text"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Q4 2025" disabled>
            </div>
        </div>

        {{-- CORE FUNCTIONS --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <h2 class="font-semibold text-lg text-white">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                Core Functions <span class="text-sm text-gray-400">(80%)</span>
            </h2>
            <p class="text-xs text-gray-400">Computed from locked SMPOR (Q4 2025) and the approved UWP. Efficiency/Quality/Timeliness scores are supervisor-encoded from SMPOR performance; employee cannot edit targets or ratings.</p>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Expected Outputs</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Efficiency / Quantity</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Quality / Effectiveness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeliness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-28">Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Scan and digitize e-bank transaction records
                                <span class="block text-[11px] text-gray-500 mt-1">Target: 100 records | Actual: 120 records (exceeded target) | System-generated from locked SMPOR</span>
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                5
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                5
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                5
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="number"
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2 opacity-80 cursor-not-allowed"
                                       value="80" disabled>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SUPPORT FUNCTIONS --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <h2 class="font-semibold text-lg text-white">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                Support Functions <span class="text-sm text-gray-400">(20%)</span>
            </h2>
            <p class="text-xs text-gray-400">Computed from locked SMPOR and approved UWP; supervisor-encoded ratings only.</p>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Expected Outputs</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Efficiency / Quantity</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Quality / Effectiveness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeliness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-28">Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Reference and archival support
                                <span class="block text-[11px] text-gray-500 mt-1">Target: Fulfill reference pulls within 1 business day | System-generated from locked SMPOR</span>
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                5
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                5
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                5
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="number"
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2 opacity-80 cursor-not-allowed"
                                       value="20" disabled>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- COMMITMENT CONFIRMATION --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 grid grid-cols-1 md:grid-cols-3 gap-5">
            <p class="md:col-span-3 text-sm text-gray-400">Acknowledgment that this system-generated IPCR (from approved UWP and locked SMPOR) has been reviewed for commitment.</p>
            <div>
                <label class="block mb-2 text-sm font-medium text-white">Immediate Supervisor</label>
                <input type="text"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Carlo D. Beray" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Employee Confirmation</label>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <input type="text"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Ramon Reyes" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Date Committed</label>
                <input type="date"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5 opacity-80 cursor-not-allowed"
                       value="2026-01-15" disabled>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex flex-col gap-2 items-end">
            <p class="text-[11px] text-gray-500">Acknowledgment updates status only; locked SMPOR data remains unchanged. Approved and locked before acknowledgment.</p>
            <div class="flex justify-end gap-3 w-full">
            <button type="button"
                    data-employee-action
                    data-action-title="Save acknowledgement draft"
                    data-action-message="Save this IPCR acknowledgement draft. Targets and ratings remain system-generated and locked."
                    data-action-confirm="Save acknowledgment draft"
                    data-action-loading="Saving..."
                    class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                <span data-button-label>Save Acknowledgment Draft</span>
                <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            </button>
            <button type="button"
                    data-employee-loading="true"
                    data-loading-text="Acknowledging..."
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-800 transition-colors duration-200">
                <span data-button-label>Acknowledge Commitment</span>
                <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            </button>
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
        let activeTrigger = null;

        if (!modal || !title || !body || !confirmBtn) {
            return;
        }

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

        window.openEmployeeActionModal = openModal;

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
                }
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
</x-layouts.employee>























