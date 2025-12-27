<x-layouts.employee>
    <section class="space-y-6">

        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-white">Individual Development Plan (IDP)</h1>
            <p class="text-sm text-gray-400 mt-1">
                Plan your learning, growth, and career development.
            </p>
        </div>

        <!-- IDP Form Card -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 space-y-6">
            
            <!-- Development Objective -->
            <div>
                <label for="objective" class="block mb-2 text-sm font-medium text-white">
                    Development Objective
                </label>
                <input type="text" 
                       id="objective"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400"
                       placeholder="e.g. Improve data analysis skills">
            </div>

            <!-- Planned Activity -->
            <div>
                <label for="activity" class="block mb-2 text-sm font-medium text-white">
                    Planned Activity
                </label>
                <input type="text" 
                       id="activity"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400"
                       placeholder="e.g. Attend training / workshop">
            </div>

            <!-- Two-column Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Target Date -->
                <div>
                    <label for="target-date" class="block mb-2 text-sm font-medium text-white">
                        Target Date
                    </label>
                    <div class="relative">
                        <input type="date" 
                               id="target-date"
                               class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400">
                    </div>
                </div>

                <!-- Support Needed -->
                <div>
                    <label for="support" class="block mb-2 text-sm font-medium text-white">
                        Support Needed
                    </label>
                    <input type="text" 
                           id="support"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400"
                           placeholder="e.g. Manager approval, Budget allocation">
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end pt-4">
                <button type="button"
                        data-employee-loading="true"
                        data-loading-text="Saving..."
                        class="inline-flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none transition-colors duration-200">
                    <span data-button-label>Save IDP</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>

        </div>

        <!-- Note -->
        <div class="flex items-center p-4 text-sm text-gray-300 bg-gray-800 border border-gray-700 rounded-lg" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3 text-blue-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium text-white">Note:</span> IDP is reviewed separately and does not affect IPCR ratings.
            </div>
        </div>

    </section>

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
