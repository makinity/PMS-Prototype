<x-layouts.employee>
    <section class="space-y-6">

        {{-- Progress Indicator --}}
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-2xl font-bold text-white">Submit Output</h1>
            <div class="flex items-center space-x-2 text-sm text-gray-400">
                <span class="flex items-center">
                    <span class="flex h-2.5 w-2.5 rounded-full bg-blue-500 mr-2"></span>
                    <span class="font-medium text-white">1. Select Task</span>
                </span>
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="flex items-center">
                    <span class="flex h-2.5 w-2.5 rounded-full bg-gray-600 mr-2"></span>
                    <span>2. Upload File</span>
                </span>
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="flex items-center">
                    <span class="flex h-2.5 w-2.5 rounded-full bg-gray-600 mr-2"></span>
                    <span>3. Confirm</span>
                </span>
            </div>
        </div>

        <p class="text-sm text-gray-400 mb-6">
            Upload completed outputs for validation and review. All submissions are logged for IPCR tracking.
        </p>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 space-y-6">

            {{-- Auto-Logging Status --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Auto-Logging Status</h3>
                        <p class="text-sm text-gray-400">Prototype preview of system-captured task data.</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300 border border-emerald-800">
                        TRACKING ON
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm">
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Tracking Source</p>
                        <p class="font-medium text-white">My Tasks</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Active Session</p>
                        <p class="font-medium text-white">E-Bank Scanning</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Time Window</p>
                        <p class="font-medium text-white">09:12 - 10:42</p>
                    </div>
                </div>

                <div class="mt-4 rounded-lg border border-amber-800 bg-amber-900/20 p-3 text-xs text-amber-300">
                    Incomplete submissions trigger alerts. Missing: output file, completion date, request ID.
                </div>
            </div>

            {{-- Task Selection Card --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Select Related Task
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Task Cards --}}
                    <div class="border border-gray-600 rounded-lg p-4 hover:border-blue-500 hover:bg-gray-700 transition-all duration-200 cursor-pointer group" data-task-card data-task-name="Report Generation" data-task-client="ABC Corp">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-white group-hover:text-blue-300">Report Generation</h4>
                                <p class="text-sm text-gray-400">ABC Corp</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded bg-blue-900 text-blue-300">Due: Aug 15</span>
                        </div>
                        <p class="text-sm text-gray-400 mb-3">Complete monthly financial report for Q3 2025</p>
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Started: Aug 10, 2025</span>
                        </div>
                    </div>

                    <div class="border border-gray-600 rounded-lg p-4 hover:border-blue-500 hover:bg-gray-700 transition-all duration-200 cursor-pointer group" data-task-card data-task-name="Client Form Review" data-task-client="XYZ Ltd">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-white group-hover:text-blue-300">Client Form Review</h4>
                                <p class="text-sm text-gray-400">XYZ Ltd</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300">Completed</span>
                        </div>
                        <p class="text-sm text-gray-400 mb-3">Review and validate client registration forms</p>
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Completed: Aug 8, 2025</span>
                        </div>
                    </div>
                </div>

                {{-- Manual Selection Fallback --}}
                <div class="mt-6">
                    <label class="block mb-2 text-sm font-medium text-white">Or select manually:</label>
                    <select id="submitTaskSelect" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option class="bg-gray-700">Select task from list</option>
                        <option class="bg-gray-700">Report Generation - ABC Corp</option>
                        <option class="bg-gray-700">Client Form Review - XYZ Ltd</option>
                        <option class="bg-gray-700">Data Analysis - DEF Inc</option>
                        <option class="bg-gray-700">Presentation Preparation - GHI Co</option>
                    </select>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">
                            Client Request ID
                            <span class="text-xs text-rose-300">Required</span>
                        </label>
                        <input id="submitRequestId" type="text"
                               class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                               placeholder="REQ-2025-01234">
                        <p class="mt-1 text-xs text-gray-400">Links this output to the exact client request.</p>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">
                            Output Type
                            <span class="text-xs text-rose-300">Required</span>
                        </label>
                        <select id="submitOutputType" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option class="bg-gray-700">Select form or output type</option>
                            <option class="bg-gray-700">Bank Statement Form (BSF-01)</option>
                            <option class="bg-gray-700">Expense Report (ER-02)</option>
                            <option class="bg-gray-700">Financial Statement (FS-03)</option>
                            <option class="bg-gray-700">Client Summary Report (CSR-04)</option>
                            <option class="bg-gray-700">Tax Compliance Form (TCF-05)</option>
                            <option class="bg-gray-700">Audit Report (AR-06)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-400">Matches the form required in the client request.</p>
                    </div>
                </div>
            </div>

            {{-- File Upload Card --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Output File
                </h3>

                {{-- Upload Area --}}
                <div class="mb-6">
                    <div class="flex items-center justify-center w-full">
                        <label id="submitFileLabel" for="file-upload" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-600 border-dashed rounded-xl cursor-pointer bg-gray-700 hover:bg-gray-750 hover:border-blue-500 transition-all duration-200 group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="mb-1 text-sm text-gray-400 group-hover:text-white">
                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, PPT, JPG, PNG (MAX. 20MB)</p>
                            </div>
                            <input id="file-upload" type="file" class="hidden" />
                        </label>
                    </div>
                </div>

                {{-- Recent Uploads --}}
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-white mb-3">Recent Uploads</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-gray-700 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-white">report_q3_2025.pdf</p>
                                    <p class="text-xs text-gray-400">Uploaded: Aug 12, 2025 - 2.4 MB</p>
                                </div>
                            </div>
                            <button type="button"
                                    data-employee-action
                                    data-action-title="Remove uploaded file"
                                    data-action-message="Remove this file from the draft submission list."
                                    data-action-confirm="Remove file"
                                    class="text-red-400 hover:text-red-300 text-sm">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                {{-- File Info --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Maximum File Size</p>
                        <p class="font-medium text-white">20 MB</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Supported Formats</p>
                        <p class="font-medium text-white">8 types</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Encryption</p>
                        <p class="font-medium text-white">256-bit SSL</p>
                    </div>
                </div>
            </div>

            {{-- Remarks & Details --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Additional Details
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Remarks / Notes</label>
                        <textarea rows="3"
                                class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 placeholder-gray-400"
                                placeholder="Provide additional context, special instructions, or notes about this output..."></textarea>
                        <p class="mt-1 text-xs text-gray-400">Maximum 500 characters</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Completion Date</label>
                            <input id="submitCompletionDate" type="date" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Confidentiality Level</label>
                            <select class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option class="bg-gray-700">Standard</option>
                                <option class="bg-gray-700">Confidential</option>
                                <option class="bg-gray-700">Internal Use Only</option>
                                <option class="bg-gray-700">Public</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Auto-Logged Start</label>
                            <input type="text" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                                   value="09:12 AM" disabled>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Auto-Logged End</label>
                            <input type="text" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                                   value="10:42 AM" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div id="submit-output-alert" class="hidden rounded-lg border border-rose-800 bg-rose-900/20 p-4 text-sm text-rose-200">
                <p class="font-semibold text-rose-100">Missing required fields</p>
                <p class="mt-1 text-xs text-rose-300">
                    Complete: <span id="submit-output-missing">task, request ID, output type, output file, completion date</span>
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-700">
                <div class="flex items-center text-sm text-gray-400">
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Submitted outputs are auto-linked to tracked tasks and contribute to IPCR ratings.
                </div>

                <div class="flex gap-3">
                    <button type="button"
                            data-employee-action
                            data-action-title="Save output draft"
                            data-action-message="Save this submission as a draft before sending for review."
                            data-action-confirm="Save draft"
                            data-action-loading="Saving..."
                            class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <span data-button-label>Save as Draft</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                    <button id="submitOutputBtn"
                            type="button"
                            data-employee-action
                            data-action-requires-validation="true"
                            data-action-title="Submit output for review"
                            data-action-message="Submit the output and auto-logged data for supervisor review."
                            data-action-confirm="Submit output"
                            data-action-loading="Submitting..."
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-800 transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span data-button-label>Submit for Review</span>
                        <span data-button-spinner class="ml-2 hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                </div>
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
            if (!modal) {
                return;
            }
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            activeTrigger = null;
            setButtonLoading(confirmBtn, false);
        }

        function openModal(trigger) {
            if (!modal || !title || !body || !confirmBtn) {
                return;
            }
            activeTrigger = trigger;
            title.textContent = trigger.dataset.actionTitle || 'Action';
            body.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
            confirmBtn.dataset.actionLoading = trigger.dataset.actionLoading || 'Working...';
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        window.openEmployeeActionModal = openModal;

        if (modal && title && body && confirmBtn) {
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
        }

        const submitBtn = document.getElementById('submitOutputBtn');
        const taskSelect = document.getElementById('submitTaskSelect');
        const requestInput = document.getElementById('submitRequestId');
        const outputSelect = document.getElementById('submitOutputType');
        const fileInput = document.getElementById('file-upload');
        const fileLabel = document.getElementById('submitFileLabel');
        const completionDate = document.getElementById('submitCompletionDate');
        const alertBox = document.getElementById('submit-output-alert');
        const missingText = document.getElementById('submit-output-missing');
        const taskCards = document.querySelectorAll('[data-task-card]');
        let selectedTask = '';

        function clearFieldState(element) {
            if (!element) {
                return;
            }
            element.classList.remove('border-rose-500');
        }

        function markFieldInvalid(element) {
            if (!element) {
                return;
            }
            element.classList.add('border-rose-500');
        }

        function clearLabelState(element) {
            if (!element) {
                return;
            }
            element.classList.remove('ring-2', 'ring-rose-500');
        }

        function markLabelInvalid(element) {
            if (!element) {
                return;
            }
            element.classList.add('ring-2', 'ring-rose-500');
        }

        taskCards.forEach((card) => {
            card.addEventListener('click', function () {
                selectedTask = this.dataset.taskName || '';
                taskCards.forEach((item) => item.classList.remove('ring-2', 'ring-blue-500'));
                this.classList.add('ring-2', 'ring-blue-500');
            });
        });

        if (!submitBtn) {
            return;
        }

        submitBtn.addEventListener('click', function () {
            clearFieldState(taskSelect);
            clearFieldState(requestInput);
            clearFieldState(outputSelect);
            clearFieldState(completionDate);
            clearLabelState(fileLabel);

            const missing = [];
            const taskSelected = (taskSelect && taskSelect.selectedIndex > 0) || selectedTask;

            if (!taskSelected) {
                missing.push('task');
                markFieldInvalid(taskSelect);
            }

            if (!requestInput || !requestInput.value.trim()) {
                missing.push('request ID');
                markFieldInvalid(requestInput);
            }

            if (!outputSelect || !outputSelect.value) {
                missing.push('output type');
                markFieldInvalid(outputSelect);
            }

            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                missing.push('output file');
                markLabelInvalid(fileLabel);
            }

            if (!completionDate || !completionDate.value) {
                missing.push('completion date');
                markFieldInvalid(completionDate);
            }

            if (missing.length > 0) {
                if (missingText) {
                    missingText.textContent = missing.join(', ');
                }
                if (alertBox) {
                    alertBox.classList.remove('hidden');
                }
                return;
            }

            if (alertBox) {
                alertBox.classList.add('hidden');
            }

            if (typeof window.openEmployeeActionModal === 'function') {
                window.openEmployeeActionModal(submitBtn);
            }
        });
    });
    </script>
    @endpush
</x-layouts.employee>
