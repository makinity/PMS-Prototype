<x-layouts.dept-head>
    <div class="space-y-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">IPCR REVIEW</h1>
                <p class="text-sm text-gray-400 mt-1">Derived from locked SMPOR & Supervisor IPCR Rating. Read-only; endorse or return; no re-rating allowed.</p>
            </div>
            <span class="px-3 py-1 text-xs font-medium rounded bg-yellow-900 text-yellow-300 border border-yellow-800">
                Rated by Supervisor / For Endorsement
            </span>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Prototype Notes</h2>
                    <p class="text-sm text-gray-400">Supervisor submits IPCR with ratings encoded from locked SMPOR data. Department Head endorses or returns; no edits to ratings or outputs.</p>
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
                    <p class="text-xs text-gray-400">Read-only list for endorsement review; supervisor ratings final. System-generated from locked SMPOR.</p>
                </div>
                <span class="text-xs text-gray-400">Stage III - Department Head Endorsement</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-900 text-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Employee Name</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Position</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Office / Unit</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Rating Period</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Immediate Supervisor</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">Overall Rating</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-gray-700">IPCR Status</th>
                            <th class="px-4 py-3 text-center font-semibold border-b border-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 text-gray-200">
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3">Ramon Reyes</td>
                            <td class="px-4 py-3 text-gray-300">Records Management Officer</td>
                            <td class="px-4 py-3 text-gray-300">Revenue Collection Unit</td>
                            <td class="px-4 py-3 text-gray-300">Jan-Jun 2025</td>
                            <td class="px-4 py-3 text-gray-300">Carlo D. Beray</td>
                            <td class="px-4 py-3 text-white font-semibold">5.00</td>
                            <td class="px-4 py-3">
                                <span data-status-badge="row-1" class="rounded-full bg-yellow-900 px-3 py-1 text-xs font-semibold text-yellow-200 border border-yellow-800">For Endorsement</span>
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
    </div>
    <div id="ipcr-view-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur px-4 py-6">
        <div class="w-full max-w-6xl rounded-2xl border border-gray-700 bg-gray-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR Review</p>
                    <h2 class="text-lg font-semibold text-white">IPCR Endorsement Review</h2>
                    <p class="text-sm text-gray-400">Derived from locked SMPOR & Supervisor IPCR Rating.</p>
                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-400">
                        <span class="font-semibold text-gray-200">Ramon Reyes</span>
                        <span class="text-gray-600">|</span>
                        <span>Records Management Officer</span>
                        <span class="text-gray-600">|</span>
                        <span>Q4 2025</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span data-modal-status class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-yellow-900 text-yellow-200 border border-yellow-800">
                        Rated by Supervisor / For Endorsement
                    </span>
                    <button type="button" data-modal-hide="ipcr-view-modal" class="text-gray-400 hover:text-white">x</button>
                </div>
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
                        <input type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="Revenue Collection Unit" disabled>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Rating Period</label>
                        <input type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="Jan - Jun 2026" disabled>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Immediate Supervisor</label>
                        <input type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="Carlo D. Beray" disabled>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Overall Rating</label>
                        <input type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="5.00" disabled>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">IPCR Status</label>
                        <input data-modal-status-field type="text" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5" value="Rated by Supervisor / For Endorsement" disabled>
                    </div>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="text-sm text-gray-300">
                        <div class="font-semibold text-white">Supervisor-encoded (read-only)</div>
                        <p class="text-xs text-gray-400">No re-rating allowed. Supervisor ratings are final; DH endorses only.</p>
                    </div>
                    <span class="text-[11px] text-gray-400">Q - Quality | E - Efficiency | T - Timeliness (1-5)</span>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-lg text-white">Core Functions <span class="text-sm text-gray-400">(80%)</span></h3>
                        <span class="text-[11px] text-gray-400">System-generated</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-700 text-sm">
                            <thead class="bg-gray-900">
                                <tr>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">MFO</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Actual Accomplishments / Evidence</th>
                                    <th class="border border-gray-700 px-4 py-3 text-center font-medium text-white w-20">Q</th>
                                    <th class="border border-gray-700 px-4 py-3 text-center font-medium text-white w-20">E</th>
                                    <th class="border border-gray-700 px-4 py-3 text-center font-medium text-white w-20">T</th>
                                    <th class="border border-gray-700 px-4 py-3 text-center font-medium text-white w-24">Average</th>
                                </tr>
                                <tr class="text-[11px] text-gray-400">
                                    <th class="border border-gray-700 px-4 py-1 text-left" colspan="6">Q - Quality | E - Efficiency | T - Timeliness (1-5)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <tr class="hover:bg-gray-750">
                                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                        E-Bank Scanning and Encoding of Revenue Transactions
                                        <span class="block text-[11px] text-gray-500 mt-1">Target: Daily; same working day</span>
                                        <button type="button"
                                                data-view-indicators
                                                data-output="E-Bank Scanning and Encoding of Revenue Transactions"
                                                data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'
                                                class="mt-2 text-xs font-semibold text-blue-300 hover:text-blue-200">
                                            View Success Indicators
                                        </button>
                                    </td>
                                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                        Completed daily scanning and encoding of e-bank transactions based on submitted ORS entries.
                                        <span class="block text-[11px] text-gray-500 mt-1">System-generated IPCR Accomplishment (read-only)</span>
                                    </td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5 <span class="block text-[11px] text-gray-500">Supervisor-encoded (read-only)</span></td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5 <span class="block text-[11px] text-gray-500">Supervisor-encoded (read-only)</span></td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5 <span class="block text-[11px] text-gray-500">Supervisor-encoded (read-only)</span></td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5.00 <span class="block text-[11px] text-gray-500">System-computed</span></td>
                                </tr>
                                <tr class="hover:bg-gray-750">
                                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                        Processing of Over-the-Counter Revenue Transactions
                                        <span class="block text-[11px] text-gray-500 mt-1">Target: Daily; 95% processed within same working day</span>
                                        <button type="button"
                                                data-view-indicators
                                                data-output="Processing of Over-the-Counter Revenue Transactions"
                                                data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'
                                                class="mt-2 text-xs font-semibold text-blue-300 hover:text-blue-200">
                                            View Success Indicators
                                        </button>
                                    </td>
                                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                        Same-day verification of over-the-counter revenue transactions completed based on submitted ORS entry.
                                        <span class="block text-[11px] text-gray-500 mt-1">System-generated IPCR Accomplishment (read-only)</span>
                                    </td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5 <span class="block text-[11px] text-gray-500">Supervisor-encoded (read-only)</span></td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5 <span class="block text-[11px] text-gray-500">Supervisor-encoded (read-only)</span></td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5 <span class="block text-[11px] text-gray-500">Supervisor-encoded (read-only)</span></td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5.00 <span class="block text-[11px] text-gray-500">System-computed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-lg text-white">Support Functions <span class="text-sm text-gray-400">(20%)</span></h3>
                        <span class="text-[11px] text-gray-400">System-generated</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-700 text-sm">
                            <thead class="bg-gray-900">
                                <tr>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">MFO</th>
                                    <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Actual Accomplishments / Evidence</th>
                                    <th class="border border-gray-700 px-4 py-3 text-center font-medium text-white w-20">Q</th>
                                    <th class="border border-gray-700 px-4 py-3 text-center font-medium text-white w-20">E</th>
                                    <th class="border border-gray-700 px-4 py-3 text-center font-medium text-white w-20">T</th>
                                    <th class="border border-gray-700 px-4 py-3 text-center font-medium text-white w-24">Average</th>
                                </tr>
                                <tr class="text-[11px] text-gray-400">
                                    <th class="border border-gray-700 px-4 py-1 text-left" colspan="6">Q - Quality | E - Efficiency | T - Timeliness (1-5)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <tr class="hover:bg-gray-750">
                                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                        Maintenance of Revenue Records Filing System
                                        <span class="block text-[11px] text-gray-500 mt-1">Target: Quarterly validation and update</span>
                                        <button type="button"
                                                data-view-indicators
                                                data-output="Maintenance of Revenue Records Filing System"
                                                data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'
                                                class="mt-2 text-xs font-semibold text-blue-300 hover:text-blue-200">
                                            View Success Indicators
                                        </button>
                                    </td>
                                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                        0
                                        <span class="block text-[11px] text-gray-500 mt-1">No output logged for the period</span>
                                        <span class="block text-[11px] text-gray-500 mt-1">System-generated from locked SMPOR</span>
                                    </td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5 <span class="block text-[11px] text-gray-500">Supervisor-encoded (read-only)</span></td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5 <span class="block text-[11px] text-gray-500">Supervisor-encoded (read-only)</span></td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5 <span class="block text-[11px] text-gray-500">Supervisor-encoded (read-only)</span></td>
                                    <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">5.00 <span class="block text-[11px] text-gray-500">System-computed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-3">
                    <label class="block text-sm font-medium text-white">Department Head Remarks</label>
                    <textarea id="dh-remarks" class="w-full rounded-lg border border-gray-700 bg-gray-800 text-sm text-white p-3 h-24 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none" placeholder="Optional for endorsement; required when returning to supervisor."></textarea>
                    <p class="text-xs text-gray-400">Remarks optional for endorsement. Required when returning to supervisor.</p>
                </div>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-gray-700 pt-4">
                <div class="flex items-center text-sm text-gray-400">
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    No re-rating allowed. Supervisor ratings are final and SMPOR data is locked. <span data-forwarded-note class="ml-2 hidden text-emerald-300">Forwarded to PMT Review.</span>
                </div>
                <div class="flex gap-3">
                    <button type="button"
                            data-employee-action
                            data-action-title="Endorse IPCR"
                            data-action-message="Endorsing this IPCR will forward it to the PMT for review. Ratings will be locked."
                            data-action-confirm="Endorse"
                            data-action-loading="Endorsing..."
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg focus:ring-4 focus:ring-emerald-800 transition-colors duration-200">
                        <span data-button-label>Endorse IPCR</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                    <button type="button"
                            data-employee-action
                            data-action-title="Return to Supervisor"
                            data-action-message="Returning this IPCR will send it back to the supervisor. Remarks expected. Ratings remain supervisor-encoded."
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
    <div id="indicators-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-xl rounded-2xl border border-gray-700 bg-gray-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-gray-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="indicators-output" class="text-lg font-semibold text-white">--</h3>
                    <p class="text-[11px] text-gray-400 mt-1">Reference only – used by Supervisor during IPCR Rating.</p>
                </div>
                <button type="button" id="indicators-close" class="text-gray-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 max-h-64 overflow-y-auto">
                <ul id="indicators-list" class="list-disc list-inside space-y-2 text-sm text-gray-100"></ul>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="button" id="indicators-close-bottom" class="rounded-lg border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-200 hover:bg-gray-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const viewModal = document.getElementById('ipcr-view-modal');
        const viewCloseButtons = document.querySelectorAll('[data-modal-hide="ipcr-view-modal"]');
        const indicatorsModal = document.getElementById('indicators-modal');
        const indicatorsOutput = document.getElementById('indicators-output');
        const indicatorsList = document.getElementById('indicators-list');
        const indicatorsCloseButtons = [document.getElementById('indicators-close'), document.getElementById('indicators-close-bottom')];

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

        const toggleIndicatorsModal = (show) => {
            if (!indicatorsModal) return;
            if (show) {
                indicatorsModal.classList.remove('hidden');
                indicatorsModal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            } else {
                indicatorsModal.classList.add('hidden');
                indicatorsModal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }
        };

        document.querySelectorAll('[data-ipcr-view]').forEach((button) => {
            button.addEventListener('click', () => {
                toggleViewModal(true);
            });
        });

        document.querySelectorAll('[data-view-indicators]').forEach((button) => {
            button.addEventListener('click', () => {
                let indicators = [];
                try { indicators = JSON.parse(button.dataset.indicators || '[]'); } catch (e) { indicators = []; }
                if (indicatorsOutput) indicatorsOutput.textContent = button.dataset.output || '--';
                if (indicatorsList) {
                    indicatorsList.innerHTML = '';
                    indicators.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = item;
                        indicatorsList.appendChild(li);
                    });
                }
                toggleIndicatorsModal(true);
            });
        });

        viewCloseButtons.forEach((button) => {
            button.addEventListener('click', () => toggleViewModal(false));
        });

        indicatorsModal?.addEventListener('click', (event) => {
            if (event.target === indicatorsModal) toggleIndicatorsModal(false);
        });
        indicatorsCloseButtons.forEach(btn => btn?.addEventListener('click', () => toggleIndicatorsModal(false)));

        viewModal?.addEventListener('click', (event) => {
            if (event.target === viewModal) {
                toggleViewModal(false);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                toggleViewModal(false);
                toggleIndicatorsModal(false);
            }
        });
    });
    </script>
    @endpush
</x-layouts.dept-head>
