@extends('layouts.supervisor')

@section('main-content')
    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">IPCR Accomplishment Rating (Stage III)</h1>
                <p class="text-sm text-gray-400 mt-1">
                    Supervisor encodes Q/E/T ratings against locked SMPOR & IPCR Accomplishment (January - June 2026). Outputs and accomplishments are read-only.
                </p>
            </div>
            <span class="px-3 py-1 text-xs font-medium rounded bg-blue-900 text-blue-300 border border-blue-800">
                Supervisor Rating
            </span>
        </div>

        {{-- IPCR LIST --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="p-4 border-b border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Submitted IPCRs</h2>
                    <p class="text-xs text-gray-400">Select an IPCR to view locked SMPOR accomplishments and encode supervisor ratings in the modal.</p>
                </div>
                <span class="text-[11px] text-gray-500">Statuses: For Supervisor Rating / Returned by Department Head / Final (Released)</span>
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
                            <td class="px-4 py-3 font-semibold">Ramon Reyes</td>
                            <td class="px-4 py-3 text-gray-300">Records Management Officer</td>
                            <td class="px-4 py-3 text-gray-300">January - June 2026</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-blue-500/10 text-blue-200 border border-blue-500/30">
                                    For Supervisor Rating
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-view-ipcr
                                        data-employee="Ramon Reyes"
                                        data-position="Records Management Officer"
                                        data-period="January - June 2026"
                                        data-status="For Supervisor Rating"
                                        class="inline-flex items-center gap-2 rounded-lg border border-gray-700 px-3 py-2 text-xs font-semibold text-gray-200 hover:bg-gray-700/70">
                                    <span class="fa-solid fa-eye text-gray-300"></span>
                                    View IPCR
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-800/60">
                            <td class="px-4 py-3 font-semibold">Ramon Reyes</td>
                            <td class="px-4 py-3 text-gray-300">Records Management Officer</td>
                            <td class="px-4 py-3 text-gray-300">January - June 2026</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-emerald-500/10 text-emerald-200 border border-emerald-500/30">
                                    Final (Released)
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-view-ipcr
                                        data-employee="Ramon Reyes"
                                        data-position="Records Management Officer"
                                        data-period="January - June 2026"
                                        data-status="Final (Released)"
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
                        For Supervisor Rating
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
                            <p class="text-xs text-gray-400">MFO outputs and evidence are read-only; rate Q/E/T only. Derived from locked SMPOR &amp; IPCR Accomplishment.</p>
                        </div>
                        <span class="text-[11px] text-gray-500">System-generated</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-800">
                            <thead class="bg-gray-900 text-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold border-b border-gray-800">MFO</th>
                                    <th class="px-4 py-3 text-left font-semibold border-b border-gray-800">Actual Accomplishments / Evidence</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">Q</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">E</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">T</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">Average</th>
                                </tr>
                                <tr class="text-[11px] text-gray-400">
                                    <th class="px-4 py-1 text-left border-b border-gray-800" colspan="6">Q - Quality | E - Efficiency | T - Timeliness (1 - 5)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800 text-gray-100">
                                <tr data-rating-row="core-1" class="hover:bg-gray-800/50">
                                    <td class="px-4 py-3">
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
                                    <td class="px-4 py-3 text-gray-300">
                                        Completed daily scanning and encoding of e-bank transactions based on submitted ORS entries.
                                        <span class="block text-[11px] text-gray-500 mt-1">System-generated IPCR Accomplishment (read-only)</span>
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
                                <tr data-rating-row="core-2" class="hover:bg-gray-800/50">
                                    <td class="px-4 py-3">
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
                                    <td class="px-4 py-3 text-gray-300">
                                        Same-day verification of over-the-counter revenue transactions completed based on submitted ORS entry.
                                        <span class="block text-[11px] text-gray-500 mt-1">System-generated IPCR Accomplishment (read-only)</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <select class="uwp-select text-white font-semibold text-center" style="min-width:72px; background:#0f172a;color:#e5e7eb;" data-rating-select="core-2">
                                            <option value="5" selected>5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <select class="uwp-select text-white font-semibold text-center" style="min-width:72px; background:#0f172a;color:#e5e7eb;" data-rating-select="core-2">
                                            <option value="5" selected>5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <select class="uwp-select text-white font-semibold text-center" style="min-width:72px; background:#0f172a;color:#e5e7eb;" data-rating-select="core-2">
                                            <option value="5" selected>5</option>
                                            <option value="4">4</option>
                                            <option value="3">3</option>
                                            <option value="2">2</option>
                                            <option value="1">1</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-white">
                                        <span data-average-target="core-2">5.00</span>
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
                            <p class="text-xs text-gray-400">Rate Q/E/T; commitments and evidence remain read-only. Derived from locked SMPOR.</p>
                        </div>
                        <span class="text-[11px] text-gray-500">System-generated</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-800">
                            <thead class="bg-gray-900 text-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold border-b border-gray-800">MFO</th>
                                    <th class="px-4 py-3 text-left font-semibold border-b border-gray-800">Actual Accomplishments / Evidence</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">Q</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">E</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">T</th>
                                    <th class="px-4 py-3 text-center font-semibold border-b border-gray-800 w-24">Average</th>
                                </tr>
                                <tr class="text-[11px] text-gray-400">
                                    <th class="px-4 py-1 text-left border-b border-gray-800" colspan="6">Q - Quality | E - Efficiency | T - Timeliness (1-5)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800 text-gray-100">
                                <tr data-rating-row="support-1" class="hover:bg-gray-800/50">
                                    <td class="px-4 py-3">
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
                                    <td class="px-4 py-3 text-gray-300">
                                        0
                                        <span class="block text-[11px] text-gray-500 mt-1">No output logged for the period</span>
                                        <span class="block text-[11px] text-gray-500 mt-1">System-generated from locked SMPOR</span>
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
                <div class="text-xs text-gray-500">Submitting sends ratings to Department Head; accomplishments remain locked from SMPOR.</div>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button"
                            data-employee-loading="true"
                            data-loading-text="Saving rating draft..."
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-200 hover:bg-gray-800/80">
                        <span data-button-label>Save Rating Draft</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                    <button type="button"
                            data-employee-action
                            data-action-title="Submit Ratings to Department Head"
                            data-action-message="Submit these Q/E/T ratings and supervisor remarks to the Department Head. Further edits will be locked."
                            data-action-confirm="Submit Ratings"
                            data-action-loading="Submitting ratings..."
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-900/40 hover:bg-blue-500">
                        <span data-button-label>Submit Ratings to Department Head</span>
                        <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Success Indicators Modal --}}
    <div id="indicators-modal" class="fixed inset-0 z-[75] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-xl rounded-2xl border border-gray-700 bg-gray-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-gray-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="indicators-output" class="text-lg font-semibold text-white">--</h3>
                    <p class="text-[11px] text-gray-400 mt-1">Indicators are derived from approved Unit Work Plan (Stage I). Read-only.</p>
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
                const indModal = document.getElementById('indicators-modal');
                const indOutput = document.getElementById('indicators-output');
                const indList = document.getElementById('indicators-list');
                const indCloseButtons = [document.getElementById('indicators-close'), document.getElementById('indicators-close-bottom')];
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
                    const status = trigger.dataset.status || 'For Supervisor Rating';
                    const editable = status === 'For Supervisor Rating';

                    employeeEls.nameTop.textContent = name;
                    employeeEls.positionTop.textContent = position;
                    employeeEls.periodTop.textContent = period;
                    employeeEls.nameDetail.textContent = name;
                    employeeEls.positionDetail.textContent = position;
                    employeeEls.periodDetail.textContent = period;
                    employeeEls.badge.textContent = status;

                    employeeEls.badge.className = 'inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full';
                    if (status === 'Final (Released)') {
                        employeeEls.badge.classList.add('border', 'border-emerald-500/50', 'bg-emerald-500/10', 'text-emerald-200');
                    } else if (status === 'Returned by Department Head') {
                        employeeEls.badge.classList.add('border', 'border-amber-500/50', 'bg-amber-500/10', 'text-amber-200');
                    } else {
                        employeeEls.badge.classList.add('border', 'border-blue-500/50', 'bg-blue-500/10', 'text-blue-200');
                    }

                    overlay.querySelectorAll('[data-rating-select], #ipcr-remarks').forEach((el) => {
                        el.disabled = !editable;
                        if (!editable) {
                            el.classList.add('opacity-70', 'cursor-not-allowed');
                        } else {
                            el.classList.remove('opacity-70', 'cursor-not-allowed');
                        }
                    });
                    overlay.querySelectorAll('[data-employee-action],[data-employee-loading]').forEach((btn) => {
                        const isSubmit = btn.textContent.includes('Submit Ratings');
                        const isDraft = btn.textContent.includes('Save Rating Draft');
                        if (isSubmit) {
                            btn.disabled = !editable;
                            btn.classList.toggle('opacity-70', !editable);
                            btn.classList.toggle('cursor-not-allowed', !editable);
                        }
                        if (isDraft) {
                            btn.disabled = !editable;
                            btn.classList.toggle('opacity-70', !editable);
                            btn.classList.toggle('cursor-not-allowed', !editable);
                        }
                    });

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

                function openIndicatorsModal(output, indicators) {
                    if (!indModal || !indOutput || !indList) return;
                    indOutput.textContent = output || '--';
                    indList.innerHTML = '';
                    (indicators || []).forEach((text) => {
                        if (!text) return;
                        const li = document.createElement('li');
                        li.textContent = text;
                        indList.appendChild(li);
                    });
                    indModal.classList.remove('hidden');
                    indModal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function closeIndicatorsModal() {
                    if (!indModal) return;
                    indModal.classList.add('hidden');
                    indModal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                document.querySelectorAll('[data-view-ipcr]').forEach(btn => {
                    btn.addEventListener('click', () => openIpcrModal(btn));
                });
                document.querySelectorAll('[data-view-indicators]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        let indicators = [];
                        try { indicators = JSON.parse(btn.dataset.indicators || '[]'); } catch (e) { indicators = []; }
                        openIndicatorsModal(btn.dataset.output || '--', indicators);
                    });
                });
                overlay.addEventListener('click', (event) => {
                    if (event.target === overlay) closeIpcrModal();
                });
                overlay.querySelectorAll('[data-close-ipcr]').forEach(btn => {
                    btn.addEventListener('click', closeIpcrModal);
                });
                indModal?.addEventListener('click', (event) => {
                    if (event.target === indModal) closeIndicatorsModal();
                });
                indCloseButtons.forEach((btn) => btn?.addEventListener('click', closeIndicatorsModal));

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
                        closeIndicatorsModal();
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
@endsection
