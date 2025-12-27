<x-layouts.employee>
    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">Unit Work Plan (UWP)</h1>
                <p class="text-sm text-gray-400 mt-1">
                    Basis for IPCR and OPCR generation
                </p>
            </div>

            {{-- Status --}}
            <span class="px-3 py-1 text-xs font-medium rounded bg-yellow-900 text-yellow-300 border border-yellow-800">
                DRAFT
            </span>
        </div>

        {{-- Prototype Clarity --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Prototype Notes</h2>
                    <p class="text-sm text-gray-400">UWP entries become tracked task categories.</p>
                </div>
                <span class="px-2 py-1 text-xs rounded bg-blue-900 text-blue-300 border border-blue-800">
                    PROTOTYPE
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm">
                <div class="bg-gray-700 rounded-lg p-3">
                    <p class="text-gray-400 mb-1">Generated into</p>
                    <p class="font-medium text-white">IPCR and OPCR</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-3">
                    <p class="text-gray-400 mb-1">ORS linking</p>
                    <p class="font-medium text-white">Task-to-output mapping</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-3">
                    <p class="text-gray-400 mb-1">Data checks</p>
                    <p class="font-medium text-white">Missing output alerts</p>
                </div>
            </div>

            <p class="mt-3 text-xs text-gray-400">
                Each output will link to client requests and drive objective performance metrics.
            </p>
        </div>

        {{-- BASIC INFORMATION --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div>
                <label class="block mb-2 text-sm font-medium text-white">Office / Unit</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                       value="Provincial Human Resource Management Office">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Period Covered</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                       value="January - June 2025">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Prepared By</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                       value="HRMO / Office Head">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Date Prepared</label>
                <input type="date" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
        </div>

        {{-- CORE FUNCTIONS --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg text-white">
                    Core Functions <span class="text-sm text-gray-400">(80%)</span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Expected Outputs</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Efficiency / Quantity</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Quality / Effectiveness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeliness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-20">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400"
                                       placeholder="e.g. HRIS records updated">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400"
                                       placeholder="e.g. 100% of records">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400"
                                       placeholder="e.g. Accurate, complete, error-free">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400"
                                       placeholder="e.g. Within prescribed period">
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-center">
                                <button type="button"
                                        data-employee-action
                                        data-action-title="Remove output row"
                                        data-action-message="Remove this output row from the draft UWP."
                                        data-action-confirm="Remove"
                                        class="text-red-400 hover:text-red-300 text-sm font-medium transition-colors duration-200">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button"
                    data-employee-action
                    data-action-title="Add core output"
                    data-action-message="Add a new blank row to the Core Functions section."
                    data-action-confirm="Add row"
                    class="flex items-center text-sm text-blue-400 hover:text-blue-300 font-medium transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Core Output
            </button>
        </div>

        {{-- SUPPORT FUNCTIONS --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg text-white">
                    Support Functions <span class="text-sm text-gray-400">(20%)</span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Expected Outputs</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Efficiency / Quantity</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Quality / Effectiveness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeliness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-20">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400"
                                       placeholder="e.g. Reports prepared">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400">
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-center">
                                <button type="button"
                                        data-employee-action
                                        data-action-title="Remove output row"
                                        data-action-message="Remove this output row from the draft UWP."
                                        data-action-confirm="Remove"
                                        class="text-red-400 hover:text-red-300 text-sm font-medium transition-colors duration-200">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button"
                    data-employee-action
                    data-action-title="Add support output"
                    data-action-message="Add a new blank row to the Support Functions section."
                    data-action-confirm="Add row"
                    class="flex items-center text-sm text-blue-400 hover:text-blue-300 font-medium transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Support Output
            </button>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 p-4 bg-gray-800 border border-gray-700 rounded-lg">
            <div class="flex items-center text-sm text-gray-400">
                <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Once approved, this UWP will be locked and used to generate IPCRs.
            </div>

            <div class="flex gap-3">
                <button type="button"
                        data-employee-action
                        data-action-title="Save UWP draft"
                        data-action-message="Save this Unit Work Plan as a draft before submitting for approval."
                        data-action-confirm="Save draft"
                        data-action-loading="Saving..."
                        class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <span data-button-label>Save as Draft</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
                <button type="button"
                        data-employee-loading="true"
                        data-loading-text="Submitting..."
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-800 transition-colors duration-200">
                    <span data-button-label>Submit for Approval</span>
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
