@extends('layouts.dept-head')

@section('main-content')
    <section class="space-y-6">

        <!-- PAGE HEADER -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Office Performance Commitment and Review (OPCR)
                </h1>
                <p class="text-sm text-slate-400">
                    Stage I – Performance Planning and Commitment
                </p>
                <p class="text-xs text-slate-500">
                    Review OPCRs submitted by Admin based on PMT-approved Unit Work Plans.
                </p>
            </div>
        </div>

        <!-- OPCR LIST -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Office / Unit</th>
                            <th class="px-4 py-2 text-left">Period</th>
                            <th class="px-4 py-2 text-left">Referenced UWP</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- FOR REVIEW -->
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Revenue Collection Unit</td>
                            <td class="px-4 py-3">January – June 2026</td>
                            <td class="px-4 py-3">Approved UWP</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300">
                                    For Review
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-open-review-opcr
                                        class="text-blue-400 hover:text-blue-300">
                                    Review
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- REVIEW OPCR MODAL -->
        <div id="review-opcr-modal"
             data-modal-container
             class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-black/60 px-4 py-6">
            <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">

                <!-- Modal Header -->
                <div class="flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
                    <div class="min-w-0">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Office Performance Commitment and Review</p>
                        <h2 class="mt-1 text-lg font-semibold text-white truncate">
                            Review OPCR – Revenue Collection Unit
                        </h2>
                        <p class="text-sm text-slate-400">
                            Derived from PMT-approved Unit Work Plan (Stage 1)
                        </p>
                        <div class="mt-2">
                            <span class="inline-flex items-center rounded-full bg-amber-500/15 px-2.5 py-1 text-[11px] font-semibold text-amber-200 border border-amber-500/30">
                                For Department Head Review
                            </span>
                        </div>
                    </div>
                    <button data-close-modal
                            class="shrink-0 rounded-lg border border-slate-800 bg-slate-950/50 px-2.5 py-2 text-slate-400 hover:text-white hover:bg-slate-950">
                        ✕
                    </button>
                </div>

                <!-- OPCR Summary strip -->
                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Office / Unit</p>
                        <p class="mt-1 text-sm font-semibold text-white">Revenue Collection Unit</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Period</p>
                        <p class="mt-1 text-sm font-semibold text-white">January - June 2026</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Referenced UWP</p>
                        <p class="mt-1 text-sm font-semibold text-white">Approved</p>
                    </div>
                </div>

                <!-- OPCR Targets Table (FULL: indicators + standards + assigned + function) -->
                <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/40 overflow-hidden">
                    <div class="max-h-[46vh] overflow-auto">
                        <table class="w-full text-sm text-slate-200">
                            <thead class="sticky top-0 z-10 bg-slate-950 text-slate-300 text-xs uppercase">
                                <tr class="border-b border-slate-800">
                                    <th class="px-4 py-3 text-left w-[30%]">Output</th>
                                    <th class="px-4 py-3 text-center w-[12%]">Success Indicators</th>
                                    <th class="px-4 py-3 text-left w-[26%]">Target Summary</th>
                                    <th class="px-4 py-3 text-left w-[4%]">Weight</th>
                                    <th class="px-4 py-3 text-left w-[4%]">Function</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">

                                <!-- ROW 1 -->
                                <tr class="hover:bg-slate-900/40">
                                    <td class="px-4 py-3 align-top text-white">
                                        E-Bank Scanning and Encoding of Revenue Transactions
                                    </td>

                                    <td class="px-4 py-3 align-top text-center">
                                        <button type="button"
                                                class="inline-flex items-center justify-center gap-2 text-blue-300 hover:text-blue-200"
                                                data-dh-view-indicators
                                                data-title="E-Bank Scanning and Encoding of Revenue Transactions"
                                                data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'>
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span class="text-xs">(3)</span>
                                        </button>
                                    </td>

                                    <td class="px-4 py-3 align-top text-slate-200">
                                        Daily; all e-bank transactions processed within the same working day
                                    </td>

                                    <td class="px-4 py-3 align-top text-slate-200">
                                        50%
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">
                                            Core
                                        </span>
                                    </td>
                                </tr>

                                <!-- ROW 2 -->
                                <tr class="hover:bg-slate-900/40">
                                    <td class="px-4 py-3 align-top text-white">
                                        Processing of Over-the-Counter Revenue Transactions
                                    </td>

                                    <td class="px-4 py-3 align-top text-center">
                                        <button type="button"
                                                class="inline-flex items-center justify-center gap-2 text-blue-300 hover:text-blue-200"
                                                data-dh-view-indicators
                                                data-title="Processing of Over-the-Counter Revenue Transactions"
                                                data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'>
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span class="text-xs">(3)</span>
                                        </button>
                                    </td>

                                    <td class="px-4 py-3 align-top text-slate-200">
                                        Daily; 95% processed within the same working day
                                    </td>

                                    <td class="px-4 py-3 align-top text-slate-200">
                                        30%
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">
                                            Core
                                        </span>
                                    </td>
                                </tr>

                                <!-- ROW 3 -->
                                <tr class="hover:bg-slate-900/40">
                                    <td class="px-4 py-3 align-top text-white">
                                        Maintenance of Revenue Records Filing System
                                    </td>

                                    <td class="px-4 py-3 align-top text-center">
                                        <button type="button"
                                                class="inline-flex items-center justify-center gap-2 text-blue-300 hover:text-blue-200"
                                                data-dh-view-indicators
                                                data-title="Maintenance of Revenue Records Filing System"
                                                data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'>
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span class="text-xs">(3)</span>
                                        </button>
                                    </td>

                                    <td class="px-4 py-3 align-top text-slate-200">
                                        Quarterly validation and update
                                    </td>

                                    <td class="px-4 py-3 align-top text-slate-200">
                                        20%
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <span class="rounded-md bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-300 border border-blue-400/30">
                                            Support
                                        </span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- REMARKS -->
                <div class="mt-5">
                    <label class="block mb-1 text-sm text-slate-300">
                        Remarks (required if returning)
                    </label>
                    <textarea rows="3"
                              style="min-width:72px; background:#0f172a;color:#e5e7eb;"
                              class="w-full rounded-xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"></textarea>
                    <p class="mt-2 text-[11px] text-slate-500">
                        Remarks are required only when returning the OPCR to Admin (demo behavior).
                    </p>
                </div>

                <!-- ACTIONS -->
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between border-t border-slate-800 pt-4">
                    <p class="text-[11px] text-slate-500">
                        Ensure targets and weights match the PMT-approved Unit Work Plan before approving.
                    </p>

                    <div class="flex flex-wrap justify-end gap-3">
                        <button type="button"
                                data-close-modal
                                class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                            Close
                        </button>

                        <button type="button"
                                data-opcr-return
                                class="inline-flex items-center gap-2 rounded-lg border border-amber-600 px-4 py-2 text-sm font-semibold text-amber-300 hover:bg-amber-600/10">
                            <span data-button-label>Return to Admin</span>
                            <span data-button-spinner
                                  class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>

                        <button type="button"
                                data-opcr-approve
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                            <span data-button-label>Approve OPCR</span>
                            <span data-button-spinner
                                  class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW OPCR MODAL -->
        <div id="view-opcr-modal"
             data-modal-container
             class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-black/60 px-4 py-6">
            <h1>Reserved for approved opcr view modal</h1>
        </div>

        <!-- SUCCESS INDICATORS MODAL (Dept Head: Read-only) -->
        <!-- Updated to match provided image: table with Standards + Assigned Employee -->
        <div id="dh-indicators-modal" class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/70 px-4 py-6">
            <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">

                <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-4">
                    <div class="min-w-0">
                        <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                        <h3 id="dh-indicators-title" class="mt-1 text-xl font-semibold text-white truncate">--</h3>
                        <p class="text-xs text-slate-400 mt-1">Read-only list of indicators for this output. One employee is assigned per success indicator.</p>
                    </div>
                    <button type="button" data-dh-close-indicators class="text-slate-400 hover:text-white">
                        <span class="sr-only">Close</span>
                        &times;
                    </button>
                </div>

                <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/50 overflow-hidden">
                    <div class="max-h-[55vh] overflow-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-900/70 text-slate-200 text-xs uppercase">
                                <tr class="border-b border-slate-800">
                                    <th class="px-4 py-3 text-left w-[56%]">Success Indicator</th>
                                    <th class="px-4 py-3 text-left w-[20%]">Standards</th>
                                    <th class="px-4 py-3 text-left w-[24%]">Assigned Employee</th>
                                </tr>
                            </thead>
                            <tbody id="dh-indicators-table-body" class="divide-y divide-slate-800"></tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button"
                            data-dh-close-indicators
                            class="rounded-lg border border-slate-700 px-5 py-2 text-sm text-slate-200 hover:bg-slate-800">
                        Close
                    </button>
                </div>

            </div>
        </div>

        <!-- INDICATOR ASSIGNEE MODAL (Dept Head: Read-only; per indicator) -->
        <div id="dh-indicator-assignee-modal" class="fixed inset-0 z-[91] hidden items-center justify-center bg-black/70 px-4 py-8">
            <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
                <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                    <div class="min-w-0">
                        <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employee</p>
                        <h3 class="text-lg font-semibold text-white truncate">Employee assigned to this success indicator</h3>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Indicator: <span id="dh-indicator-assignee-indicator" class="text-slate-200">--</span>
                        </p>
                    </div>
                    <button type="button" data-dh-close-indicator-assignee class="text-slate-400 hover:text-white">
                        <span class="sr-only">Close</span>
                        &times;
                    </button>
                </div>

                <div class="mt-4 rounded-lg border border-slate-800 bg-slate-950/60 overflow-hidden">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-slate-900/70 text-slate-200">
                            <tr>
                                <th class="px-4 py-2 text-left">Employee Name</th>
                                <th class="px-4 py-2 text-left">Office / Unit</th>
                                <th class="px-4 py-2 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody id="dh-indicator-assignee-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-end gap-3 border-t border-slate-800 pt-3">
                    <button type="button"
                            data-dh-close-indicator-assignee
                            class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- STANDARDS MODAL (Dept Head: Read-only) -->
        <div id="dh-standards-modal" class="fixed inset-0 z-[92] hidden items-center justify-center bg-black/70 px-4 py-8">
            <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
                <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                        <h3 id="dh-standards-title" class="text-lg font-semibold text-white">Standards (Q/E/T)</h3>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Indicator: <span id="dh-standards-indicator">--</span>
                        </p>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-white" data-dh-close-standards>
                        <span class="sr-only">Close</span>
                        &times;
                    </button>
                </div>

                <div class="mt-4 space-y-4 text-sm text-slate-200 max-h-[70vh] overflow-y-auto">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400 mb-1">Indicator</p>
                            <p id="dh-standards-indicator-display" class="text-sm font-semibold text-slate-100">--</p>
                        </div>
                    </div>

                    <div id="dh-standards-list" class="w-full"></div>
                </div>

                <div class="mt-5 flex justify-end border-t border-slate-800 pt-3">
                    <button type="button"
                            data-dh-close-standards
                            class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- ASSIGNED EMPLOYEES MODAL (Dept Head: Read-only) -->
        <div id="dh-assigned-modal" class="fixed inset-0 z-[91] hidden items-center justify-center bg-black/70 px-4 py-8">
            <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
                <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                    <div class="min-w-0">
                        <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employees</p>
                        <h3 class="text-lg font-semibold text-white">Employees under the selected Office/Unit</h3>
                        <p class="text-[11px] text-slate-400 mt-1">Office / Unit: <span id="dh-assigned-unit">Revenue Collection Unit</span></p>
                    </div>
                    <button type="button" data-dh-close-assigned class="text-slate-400 hover:text-white">
                        <span class="sr-only">Close</span>
                        &times;
                    </button>
                </div>

                <div class="mt-4 space-y-3 text-sm text-slate-200 max-h-[60vh] overflow-y-auto">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-500 text-xs">🔍</span>
                        <input type="text"
                               id="dh-assigned-search"
                               style="background:#0f172a;color:#e5e7eb;"
                               placeholder="Search employee name…"
                               class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 pl-8 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                    </div>

                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 overflow-hidden">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-900/70 text-slate-200">
                                <tr>
                                    <th class="px-4 py-2 text-left">Employee Name</th>
                                    <th class="px-4 py-2 text-left">Office / Unit</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody id="dh-assigned-list" class="divide-y divide-slate-800"></tbody>
                        </table>
                    </div>

                    <p id="dh-assigned-empty" class="text-[12px] text-slate-500 hidden">No employees available (demo).</p>
                </div>

                <div class="mt-4 flex items-center justify-end gap-3 border-t border-slate-800 pt-3">
                    <button type="button"
                            data-dh-close-assigned
                            class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>

    </section>

   @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    function getModalEl(id) {
        return document.getElementById(id);
    }

    function openModal(id) {
        const modal = getModalEl(id);
        if (!modal) return;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(modal) {
        if (!modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        const anyOpen = document.querySelector('[data-modal-container]:not(.hidden), #dh-indicators-modal:not(.hidden), #dh-standards-modal:not(.hidden), #dh-assigned-modal:not(.hidden), #dh-indicator-assignee-modal:not(.hidden)');
        if (!anyOpen) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function closeAllModals() {
        document.querySelectorAll('[data-modal-container]').forEach(m => {
            m.classList.add('hidden');
            m.classList.remove('flex');
        });
        ['dh-indicators-modal','dh-standards-modal','dh-assigned-modal','dh-indicator-assignee-modal'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
        });
        document.body.classList.remove('overflow-hidden');
    }

    function setButtonLoading(button, isLoading, loadingText) {
        if (!button) return;
        const label = button.querySelector('[data-button-label]');
        const spinner = button.querySelector('[data-button-spinner]');

        if (label && !button.dataset.originalLabel) {
            button.dataset.originalLabel = label.textContent.trim();
        }

        if (isLoading) {
            button.disabled = true;
            button.classList.add('opacity-70', 'cursor-not-allowed');
            spinner?.classList.remove('hidden');
            if (label && loadingText) label.textContent = loadingText;
        } else {
            button.disabled = false;
            button.classList.remove('opacity-70', 'cursor-not-allowed');
            spinner?.classList.add('hidden');
            if (label && button.dataset.originalLabel) label.textContent = button.dataset.originalLabel;
        }
    }

    // -------------------------
    // Dept Head: Indicators Modal (UPDATED)
    // -------------------------
    const indicatorsModal = document.getElementById('dh-indicators-modal');
    const indicatorsTitle = document.getElementById('dh-indicators-title');
    const indicatorsTbody = document.getElementById('dh-indicators-table-body');
    let currentMfoTitle = '--';

    // demo: one employee per indicator
    const indicatorAssigneeMap = {
        'All e-bank transactions scanned and encoded daily': { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true },
        'Indexing complete with no missing pages': { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true },
        'Audit trail maintained within 24 hours': { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true },

        'Same-day verification of OTC transactions': { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true },
        '95% encoded within the business day': { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true },
        'OR validation completed daily': { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true },

        'Weekly filing updated and retrievable': { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true },
        'Digital backups synced monthly': { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true },
        'Retrieval logs maintained for audits': { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true },
    };

    function openIndicatorsModal(title, indicators) {
        if (!indicatorsModal || !indicatorsTitle || !indicatorsTbody) return;

        currentMfoTitle = title || '--';
        indicatorsTitle.textContent = currentMfoTitle;
        indicatorsTbody.innerHTML = '';

        (indicators || []).forEach((text) => {
            const indicatorText = (text || '').trim();
            if (!indicatorText) return;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';

            // Success Indicator
            const tdIndicator = document.createElement('td');
            tdIndicator.className = 'px-4 py-3 text-slate-100';
            tdIndicator.textContent = indicatorText;

            // Standards button
            const tdStandards = document.createElement('td');
            tdStandards.className = 'px-4 py-3';

            const standardsBtn = document.createElement('button');
            standardsBtn.type = 'button';
            standardsBtn.className = 'inline-flex items-center gap-2 text-blue-300 hover:text-blue-200';
            standardsBtn.innerHTML = `
                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span class="text-sm font-medium">View Standards</span>
            `;
            standardsBtn.addEventListener('click', () => openStandardsModal(indicatorText, currentMfoTitle));
            tdStandards.appendChild(standardsBtn);

            // Assigned Employee button (View (1))
            const tdAssignee = document.createElement('td');
            tdAssignee.className = 'px-4 py-3';

            const assigneeBtn = document.createElement('button');
            assigneeBtn.type = 'button';
            assigneeBtn.className = 'inline-flex items-center gap-2 text-blue-300 hover:text-blue-200';
            assigneeBtn.innerHTML = `
                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span class="text-sm font-medium">View (1)</span>
            `;
            assigneeBtn.addEventListener('click', () => openIndicatorAssigneeModal(indicatorText));
            tdAssignee.appendChild(assigneeBtn);

            tr.append(tdIndicator, tdStandards, tdAssignee);
            indicatorsTbody.appendChild(tr);
        });

        indicatorsModal.classList.remove('hidden');
        indicatorsModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeIndicatorsModal() {
        if (!indicatorsModal) return;
        indicatorsModal.classList.add('hidden');
        indicatorsModal.classList.remove('flex');

        const anyOpen = document.querySelector('[data-modal-container]:not(.hidden), #dh-standards-modal:not(.hidden), #dh-assigned-modal:not(.hidden), #dh-indicator-assignee-modal:not(.hidden)');
        if (!anyOpen) document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-dh-view-indicators]').forEach((btn) => {
        btn.addEventListener('click', () => {
            let indicators = [];
            try { indicators = JSON.parse(btn.dataset.indicators || '[]'); } catch (e) { indicators = []; }
            openIndicatorsModal(btn.dataset.title || '--', indicators);
        });
    });

    document.querySelectorAll('[data-dh-close-indicators]').forEach((btn) => {
        btn.addEventListener('click', closeIndicatorsModal);
    });

    // -------------------------
    // Indicator Assignee Modal (NEW)
    // -------------------------
    const indicatorAssigneeModal = document.getElementById('dh-indicator-assignee-modal');
    const indicatorAssigneeIndicator = document.getElementById('dh-indicator-assignee-indicator');
    const indicatorAssigneeBody = document.getElementById('dh-indicator-assignee-body');

    function openIndicatorAssigneeModal(indicatorText) {
        if (!indicatorAssigneeModal || !indicatorAssigneeIndicator || !indicatorAssigneeBody) return;

        const key = (indicatorText || '').trim();
        indicatorAssigneeIndicator.textContent = key || '--';
        indicatorAssigneeBody.innerHTML = '';

        const emp = indicatorAssigneeMap[key] || { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true };

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-900/40';

        const nameTd = document.createElement('td');
        nameTd.className = 'px-4 py-2';
        nameTd.textContent = emp.name;

        const unitTd = document.createElement('td');
        unitTd.className = 'px-4 py-2';
        unitTd.textContent = emp.unit;

        const statusTd = document.createElement('td');
        statusTd.className = 'px-4 py-2';

        const badge = document.createElement('span');
        badge.className = `inline-flex items-center px-2 py-1 text-[11px] font-semibold rounded-full border ${
            emp.assigned ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200' : 'border-slate-600 bg-slate-800 text-slate-300'
        }`;
        badge.textContent = emp.assigned ? 'Assigned' : 'Not Assigned';
        statusTd.appendChild(badge);

        tr.append(nameTd, unitTd, statusTd);
        indicatorAssigneeBody.appendChild(tr);

        indicatorAssigneeModal.classList.remove('hidden');
        indicatorAssigneeModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeIndicatorAssigneeModal() {
        if (!indicatorAssigneeModal) return;
        indicatorAssigneeModal.classList.add('hidden');
        indicatorAssigneeModal.classList.remove('flex');

        const anyOpen = document.querySelector('[data-modal-container]:not(.hidden), #dh-indicators-modal:not(.hidden), #dh-standards-modal:not(.hidden), #dh-assigned-modal:not(.hidden)');
        if (!anyOpen) document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-dh-close-indicator-assignee]').forEach((btn) => {
        btn.addEventListener('click', closeIndicatorAssigneeModal);
    });

    // -------------------------
    // Dept Head: Standards Modal (seeded)
    // -------------------------
    const standardsModal = document.getElementById('dh-standards-modal');
    const standardsList = document.getElementById('dh-standards-list');
    const standardsTitle = document.getElementById('dh-standards-title');
    const standardsIndicatorLabel = document.getElementById('dh-standards-indicator');
    const standardsIndicatorDisplay = document.getElementById('dh-standards-indicator-display');

    const standardsSeedMap = {
        'All e-bank transactions scanned and encoded daily': {
            5: { q: ['No errors; accurate encoding'], e: ['100% processed'], t: ['Same working day'] },
            4: { q: ['Minor errors'], e: ['100% processed'], t: ['Same working day'] },
            3: { q: ['Few minor errors'], e: ['95–99% processed'], t: ['End of working day'] },
            2: { q: ['Multiple errors'], e: ['<95% processed'], t: ['Beyond working day'] },
            1: { q: ['Major errors/missing'], e: ['Majority unprocessed'], t: ['Not within acceptable time'] },
        },
        'Indexing complete with no missing pages': {
            5: { q: ['Indexing fully verified, zero gaps'], e: ['100% pages indexed'], t: ['Same day'] },
            4: { q: ['Indexing minor rechecks'], e: ['100% pages indexed'], t: ['Same day'] },
            3: { q: ['Occasional missing indexes fixed'], e: ['95–99% indexed'], t: ['Within 24 hours'] },
            2: { q: ['Frequent missing pages'], e: ['<95% indexed'], t: ['Beyond 24 hours'] },
            1: { q: ['Indexing largely incomplete'], e: ['Major gaps'], t: ['Unacceptable'] },
        },
        'Audit trail maintained within 24 hours': {
            5: { q: ['Complete trail, no errors'], e: ['100% entries captured'], t: ['Within 24 hours'] },
            4: { q: ['Minor corrections only'], e: ['100% entries captured'], t: ['Within 24 hours'] },
            3: { q: ['Some gaps corrected'], e: ['95–99% entries captured'], t: ['Within 48 hours'] },
            2: { q: ['Multiple missing logs'], e: ['<95% captured'], t: ['Beyond 48 hours'] },
            1: { q: ['Trail missing'], e: ['Majority uncaptured'], t: ['Unacceptable'] },
        },
        'Same-day verification of OTC transactions': {
            5: { q: ['Verified without discrepancies'], e: ['100% OTC verified'], t: ['Same working day'] },
            4: { q: ['Minor verifications pending'], e: ['100% OTC verified'], t: ['Same working day'] },
            3: { q: ['Few pending verifications'], e: ['95–99% verified'], t: ['End of working day'] },
            2: { q: ['Several unverified'], e: ['<95% verified'], t: ['Beyond working day'] },
            1: { q: ['Verification not done'], e: ['Majority unverified'], t: ['Unacceptable'] },
        },
        '95% encoded within the business day': {
            5: { q: ['Encodings error-free'], e: ['100% encoded'], t: ['Same business day'] },
            4: { q: ['Minor corrections'], e: ['100% encoded'], t: ['Same business day'] },
            3: { q: ['Few delays'], e: ['95–99% encoded'], t: ['By end of day'] },
            2: { q: ['Multiple delays'], e: ['<95% encoded'], t: ['Next day'] },
            1: { q: ['Encoding largely incomplete'], e: ['Major backlog'], t: ['Unacceptable'] },
        },
        'OR validation completed daily': {
            5: { q: ['All ORs validated error-free'], e: ['100% validated'], t: ['Daily'] },
            4: { q: ['Minor issues corrected same day'], e: ['100% validated'], t: ['Daily'] },
            3: { q: ['Some validations late'], e: ['95–99% validated'], t: ['Within 48 hours'] },
            2: { q: ['Frequent late validations'], e: ['<95% validated'], t: ['Beyond 48 hours'] },
            1: { q: ['Validations mostly missing'], e: ['Majority unvalidated'], t: ['Unacceptable'] },
        },
        'Weekly filing updated and retrievable': {
            5: { q: ['Zero retrieval issues'], e: ['100% weekly updates'], t: ['Within week'] },
            4: { q: ['Minor retrieval fixes'], e: ['100% weekly updates'], t: ['Within week'] },
            3: { q: ['Some items late'], e: ['95–99% updates'], t: ['Within next week'] },
            2: { q: ['Many late updates'], e: ['<95% updates'], t: ['Beyond next week'] },
            1: { q: ['Updates not done'], e: ['Major gaps'], t: ['Unacceptable'] },
        },
        'Digital backups synced monthly': {
            5: { q: ['Backups verified'], e: ['100% synced'], t: ['Within month'] },
            4: { q: ['Minor sync corrections'], e: ['100% synced'], t: ['Within month'] },
            3: { q: ['Some delays'], e: ['95–99% synced'], t: ['Within following week'] },
            2: { q: ['Frequent delays'], e: ['<95% synced'], t: ['Beyond following week'] },
            1: { q: ['Backups largely missing'], e: ['Major gaps'], t: ['Unacceptable'] },
        },
        'Retrieval logs maintained for audits': {
            5: { q: ['Logs complete and audit-ready'], e: ['100% requests logged'], t: ['Same day'] },
            4: { q: ['Minor log gaps corrected'], e: ['100% requests logged'], t: ['Same day'] },
            3: { q: ['Some gaps'], e: ['95–99% logged'], t: ['Within 48 hours'] },
            2: { q: ['Many gaps'], e: ['<95% logged'], t: ['Beyond 48 hours'] },
            1: { q: ['Logs largely missing'], e: ['Majority unlogged'], t: ['Unacceptable'] },
        },
    };

    function createEmptyStandards() {
        return { 5:{q:[],e:[],t:[]},4:{q:[],e:[],t:[]},3:{q:[],e:[],t:[]},2:{q:[],e:[],t:[]},1:{q:[],e:[],t:[]} };
    }

    function seedStandardsForIndicator(text) {
        const seed = standardsSeedMap[text];
        if (!seed) return createEmptyStandards();
        const base = createEmptyStandards();
        [5,4,3,2,1].forEach((lvl) => {
            if (seed[lvl]) {
                base[lvl] = {
                    q: Array.isArray(seed[lvl].q) ? [...seed[lvl].q] : [seed[lvl].q],
                    e: Array.isArray(seed[lvl].e) ? [...seed[lvl].e] : [seed[lvl].e],
                    t: Array.isArray(seed[lvl].t) ? [...seed[lvl].t] : [seed[lvl].t],
                };
            }
        });
        return base;
    }

    function renderStandardsTable(data) {
        if (!standardsList) return;
        standardsList.innerHTML = '';

        const table = document.createElement('table');
        table.className = 'w-full text-sm border border-slate-800 overflow-hidden rounded-lg';
        table.innerHTML = `
            <thead class="bg-slate-900/70 text-slate-200">
                <tr>
                    <th class="px-3 py-2 text-left">Rating</th>
                    <th class="px-3 py-2 text-left">Quality (Q)</th>
                    <th class="px-3 py-2 text-left">Efficiency (E)</th>
                    <th class="px-3 py-2 text-left">Timeliness (T)</th>
                </tr>
            </thead>
        `;

        const tbody = document.createElement('tbody');
        tbody.className = 'divide-y divide-slate-800 text-slate-100';

        const makeCell = (arr) => {
            const td = document.createElement('td');
            td.className = 'px-3 py-2 align-top';
            if (!arr || arr.length === 0) {
                td.textContent = '—';
                td.classList.add('text-slate-500');
                return td;
            }
            const wrap = document.createElement('div');
            wrap.className = 'space-y-1';
            arr.forEach((txt) => {
                const line = document.createElement('div');
                line.className = 'flex items-start gap-2';
                const bullet = document.createElement('span');
                bullet.textContent = '•';
                bullet.className = 'text-slate-400';
                const text = document.createElement('span');
                text.className = 'flex-1';
                text.textContent = txt;
                line.append(bullet, text);
                wrap.appendChild(line);
            });
            td.appendChild(wrap);
            return td;
        };

        [5,4,3,2,1].forEach((lvl) => {
            const row = data[lvl] || { q:[], e:[], t:[] };
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';

            const ratingTd = document.createElement('td');
            ratingTd.className = 'px-3 py-2 text-left text-white font-semibold';
            ratingTd.textContent = lvl;

            tr.appendChild(ratingTd);
            tr.appendChild(makeCell(row.q));
            tr.appendChild(makeCell(row.e));
            tr.appendChild(makeCell(row.t));
            tbody.appendChild(tr);
        });

        table.appendChild(tbody);
        standardsList.appendChild(table);
    }

    function openStandardsModal(indicatorText, mfoTitle) {
        if (!standardsModal) return;
        const rawIndicator = (indicatorText || '').trim();
        const indicatorLabel = rawIndicator || '--';
        const activeMfo = (mfoTitle || currentMfoTitle || '--').trim() || '--';

        if (standardsIndicatorLabel) standardsIndicatorLabel.textContent = indicatorLabel;
        if (standardsIndicatorDisplay) standardsIndicatorDisplay.textContent = indicatorLabel;
        if (standardsTitle) standardsTitle.textContent = `Standards (Q/E/T) — ${activeMfo}`;

        const data = seedStandardsForIndicator(rawIndicator);
        renderStandardsTable(data);

        standardsModal.classList.remove('hidden');
        standardsModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeStandardsModal() {
        if (!standardsModal) return;
        standardsModal.classList.add('hidden');
        standardsModal.classList.remove('flex');

        const anyOpen = document.querySelector('[data-modal-container]:not(.hidden), #dh-indicators-modal:not(.hidden), #dh-assigned-modal:not(.hidden), #dh-indicator-assignee-modal:not(.hidden)');
        if (!anyOpen) document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-dh-close-standards]').forEach((btn) => {
        btn.addEventListener('click', closeStandardsModal);
    });

    // -------------------------
    // Dept Head: Assigned Employees Modal (seeded)
    // -------------------------
    const assignedModal = document.getElementById('dh-assigned-modal');
    const assignedList = document.getElementById('dh-assigned-list');
    const assignedEmpty = document.getElementById('dh-assigned-empty');
    const assignedSearch = document.getElementById('dh-assigned-search');

    const assignedSeed = [
        { name: 'Ramon Reyes', unit: 'Revenue Collection Unit', assigned: true },
    ];

    function renderAssigned(filterText) {
        if (!assignedList || !assignedEmpty) return;
        const q = (filterText || '').trim().toLowerCase();

        assignedList.innerHTML = '';
        const rows = assignedSeed.filter((r) => !q || r.name.toLowerCase().includes(q));

        if (!rows.length) {
            assignedEmpty.classList.remove('hidden');
            return;
        }
        assignedEmpty.classList.add('hidden');

        rows.forEach((emp) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';

            const nameTd = document.createElement('td');
            nameTd.className = 'px-4 py-2';
            nameTd.textContent = emp.name;

            const unitTd = document.createElement('td');
            unitTd.className = 'px-4 py-2';
            unitTd.textContent = emp.unit;

            const statusTd = document.createElement('td');
            statusTd.className = 'px-4 py-2';
            const badge = document.createElement('span');
            badge.className = `inline-flex items-center px-2 py-1 text-[11px] font-semibold rounded-full border ${
                emp.assigned ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200' : 'border-slate-600 bg-slate-800 text-slate-300'
            }`;
            badge.textContent = emp.assigned ? 'Assigned' : 'Not Assigned';
            statusTd.appendChild(badge);

            tr.append(nameTd, unitTd, statusTd);
            assignedList.appendChild(tr);
        });
    }

    function openAssignedModal() {
        if (!assignedModal) return;
        renderAssigned('');
        if (assignedSearch) assignedSearch.value = '';
        assignedModal.classList.remove('hidden');
        assignedModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeAssignedModal() {
        if (!assignedModal) return;
        assignedModal.classList.add('hidden');
        assignedModal.classList.remove('flex');

        const anyOpen = document.querySelector('[data-modal-container]:not(.hidden), #dh-indicators-modal:not(.hidden), #dh-standards-modal:not(.hidden), #dh-indicator-assignee-modal:not(.hidden)');
        if (!anyOpen) document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-dh-open-assignees]').forEach((btn) => {
        btn.addEventListener('click', openAssignedModal);
    });

    document.querySelectorAll('[data-dh-close-assigned]').forEach((btn) => {
        btn.addEventListener('click', closeAssignedModal);
    });

    if (assignedSearch) {
        assignedSearch.addEventListener('input', () => renderAssigned(assignedSearch.value));
    }

    // -------------------------
    // Main modals open/close
    // -------------------------
    document.querySelectorAll('[data-open-review-opcr]').forEach(btn => {
        btn.addEventListener('click', () => openModal('review-opcr-modal'));
    });

    document.querySelectorAll('[data-open-view-opcr]').forEach(btn => {
        btn.addEventListener('click', () => openModal('view-opcr-modal'));
    });

    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('[data-modal-container]');
            closeModal(modal);
        });
    });

    document.querySelectorAll('[data-modal-container]').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
        });
    });

    // click outside close for sub-modals
    [indicatorsModal, standardsModal, assignedModal, indicatorAssigneeModal].forEach((m) => {
        if (!m) return;
        m.addEventListener('click', (e) => {
            if (e.target !== m) return;
            if (m === indicatorsModal) closeIndicatorsModal();
            else if (m === standardsModal) closeStandardsModal();
            else if (m === assignedModal) closeAssignedModal();
            else if (m === indicatorAssigneeModal) closeIndicatorAssigneeModal();
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;

        if (standardsModal && !standardsModal.classList.contains('hidden')) {
            closeStandardsModal();
            return;
        }
        if (indicatorAssigneeModal && !indicatorAssigneeModal.classList.contains('hidden')) {
            closeIndicatorAssigneeModal();
            return;
        }
        if (assignedModal && !assignedModal.classList.contains('hidden')) {
            closeAssignedModal();
            return;
        }
        if (indicatorsModal && !indicatorsModal.classList.contains('hidden')) {
            closeIndicatorsModal();
            return;
        }
        closeAllModals();
    });

    // Approve/Return loading demo
    document.querySelectorAll('[data-opcr-approve],[data-opcr-return]').forEach(btn => {
        btn.addEventListener('click', () => {
            const isReturn = btn.hasAttribute('data-opcr-return');
            setButtonLoading(btn, true, isReturn ? 'Returning...' : 'Approving...');

            setTimeout(() => {
                setButtonLoading(btn, false);
                closeAllModals();
            }, 1000);
        });
    });

});
</script>
@endpush
@endsection
