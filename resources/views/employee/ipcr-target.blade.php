@extends('layouts.employee')

@section('main-content')
    <div class="space-y-6">

        <!-- PAGE HEADER -->
        <div class="flex justify-between items-start gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Individual Performance Commitment and Review (IPCR)
                </h1>
                <p class="text-sm text-gray-400 mt-1">
                    Stage I – Performance Planning and Commitment
                </p>
                <p class="text-sm text-gray-400">
                    Targets are system-generated from the approved Unit Work Plan (UWP) and OPCR.
                    Editing is not allowed. Employee action is acknowledgment only.
                </p>
                <p class="text-[11px] text-gray-500 mt-2">Read-only | No edits or validation by employee.</p>
            </div>

            <span id="ipcr-status-badge" class="px-3 py-1 text-xs font-medium rounded bg-blue-900 text-blue-300 border border-blue-800">
                FOR COMMITMENT
            </span>
        </div>

        <!-- STATUS / CONTEXT -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Status & Context</h2>
                    <p class="text-sm text-gray-400">IPCR is auto-filled from approved UWP and OPCR (PMT-approved) and OPCR (Department Head–approved).</p>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('stage1.ipcr.export.excel') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">
                        Export IPCR (Excel)
                    </a>
                    <button type="button"
                            data-open-assigned-employees
                            class="inline-flex items-center gap-2 rounded-lg border border-gray-600 bg-gray-900/40 px-3 py-2 text-xs font-semibold text-gray-200 hover:bg-gray-700">
                        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.949 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766v-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        View Assigned Employees
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 text-sm">
                <div class="bg-gray-700 rounded-lg p-3">
                    <p class="text-gray-400 mb-1">Status</p>
                    <p id="ipcr-status-text" class="font-medium text-white">For Commitment</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-3">
                    <p class="text-gray-400 mb-1">Basis</p>
                    <p class="font-medium text-white">Approved UWP (PMT-approved) and OPCR (Department Head–approved)</p>
                </div>
            </div>
        </div>

        <!-- EMPLOYEE INFORMATION -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5">
            <h2 class="font-semibold text-lg text-white mb-4">Employee Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Employee Name</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="Ramon Reyes" disabled>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Position</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="Records Management Officer" disabled>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Office / Unit</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="Revenue Collection Unit" disabled>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Rating Period</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="January – June 2026" disabled>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Immediate Supervisor</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="Carlo D. Beray" disabled>
                </div>
            </div>
        </div>

        <!-- CORE FUNCTIONS -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="font-semibold text-lg text-white">
                        Core Functions <span class="text-sm text-gray-400">(80%)</span>
                    </h2>
                    <p class="text-sm text-gray-400">Derived from approved UWP and OPCR targets.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Major Output</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Success Indicators</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Target Summary</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeline</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-28">Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                E-Bank Scanning and Encoding of Revenue Transactions
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                <button type="button"
                                        data-open-indicators
                                        data-mfo="mfo_1"
                                        class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200">
                                    <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>View (3)</span>
                                </button>
                                <span class="block text-[11px] text-gray-500 mt-1">Derived from approved UWP</span>
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Daily; all e-bank transactions processed within the same working day
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                January – June 2026
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="number"
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2 opacity-80 cursor-not-allowed"
                                       value="50" disabled>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Processing of Over-the-Counter Revenue Transactions
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                <button type="button"
                                        data-open-indicators
                                        data-mfo="mfo_2"
                                        class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200">
                                    <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>View (3)</span>
                                </button>
                                <span class="block text-[11px] text-gray-500 mt-1">Derived from approved UWP</span>
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Daily; 95% processed within the same working day
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                January – June 2026
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="number"
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2 opacity-80 cursor-not-allowed"
                                       value="30" disabled>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- SUPPORT FUNCTIONS -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="font-semibold text-lg text-white">
                        Support Functions <span class="text-sm text-gray-400">(20%)</span>
                    </h2>
                    <p class="text-sm text-gray-400">Derived from approved UWP and OPCR targets.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Major Output</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Success Indicators</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Target Summary</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeline</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-28">Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Maintenance of Revenue Records Filing System
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                <button type="button"
                                        data-open-indicators
                                        data-mfo="mfo_support"
                                        class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200">
                                    <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>View (3)</span>
                                </button>
                                <span class="block text-[11px] text-gray-500 mt-1">Derived from approved UWP</span>
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Quarterly validation and update
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                January – June 2026
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

        <!-- COMMITMENT SECTION -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5">
            <h2 class="font-semibold text-lg text-white mb-4">Employee Commitment</h2>
            <div class="bg-gray-700/50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-300 italic">
                    I acknowledge and commit to the above performance targets derived from the
                    approved Unit Work Plan (UWP) and OPCR. I understand that these targets will
                    serve as the basis for performance monitoring and evaluation.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Employee Name</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="Ramon Reyes" disabled>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Date<small>(Date will be recorded upon commitment)</small></label>
                    <input type="date"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5 opacity-80 cursor-not-allowed"
                           value="" disabled>
                </div>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="flex flex-col gap-2 items-end">
            <p class="text-[11px] text-gray-500">Commitment acknowledgment only; targets remain system-generated from approved UWP and OPCR.</p>
            <div class="flex justify-end gap-3 w-full">
                <button type="button"
                        data-employee-action
                        data-action-title="Save commitment draft"
                        data-action-message="Save this IPCR commitment draft. Targets remain system-generated from approved UWP and OPCR."
                        data-action-confirm="Save draft"
                        data-action-loading="Saving..."
                        class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <span data-button-label>Save Commitment Draft</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>

                <button type="button"
                        id="commit-targets-btn"
                        data-employee-loading="true"
                        data-loading-text="Committing..."
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-800 transition-colors duration-200">
                    <span data-button-label>Commit Targets</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>

    </div>

    <!-- SUCCESS INDICATORS MODAL -->
    <div id="ipcr-indicators-modal" data-modal-container role="dialog" aria-modal="true"
         class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Individual Performance Commitment and Review</p>
                    <h2 id="ipcr-indicators-title" class="mt-1 text-lg font-semibold text-white truncate">
                        Success Indicators
                    </h2>
                    <p class="text-sm text-slate-400">Read-only | Derived from PMT-approved UWP</p>
                    <div class="mt-2">
                        <span class="inline-flex items-center rounded-full bg-blue-500/15 px-2.5 py-1 text-[11px] font-semibold text-blue-200 border border-blue-500/30">
                            Modal-only indicators
                        </span>
                    </div>
                </div>
                <button type="button" data-close-modal
                        class="shrink-0 rounded-lg border border-slate-800 bg-slate-950/50 px-2.5 py-2 text-slate-400 hover:text-white hover:bg-slate-950">
                    ✕
                </button>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Office / Unit</p>
                    <p class="mt-1 text-sm font-semibold text-white">Revenue Collection Unit</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Period</p>
                    <p class="mt-1 text-sm font-semibold text-white">January – June 2026</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Employee</p>
                    <p class="mt-1 text-sm font-semibold text-white">Ramon Reyes</p>
                </div>
            </div>

            <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/40 overflow-hidden">
                <div class="max-h-[46vh] overflow-auto">
                    <table class="w-full text-sm text-slate-200">
                        <thead class="sticky top-0 z-10 bg-slate-950 text-slate-300 text-xs uppercase">
                            <tr class="border-b border-slate-800">
                                <th class="px-4 py-3 text-left w-[70%]">Indicator</th>
                                <th class="px-4 py-3 text-left w-[30%]">Standards (Q/E/T)</th>
                            </tr>
                        </thead>
                        <tbody id="ipcr-indicators-body" class="divide-y divide-slate-800">
                            <!-- injected -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-slate-800 pt-4">
                <button type="button" data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- STANDARDS MODAL -->
    <div id="ipcr-standards-modal" data-modal-container role="dialog" aria-modal="true"
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Standards</p>
                    <h2 id="ipcr-standards-title" class="mt-1 text-lg font-semibold text-white truncate">Standards (Q/E/T)</h2>
                    <p class="text-sm text-slate-400">Read-only | Encoded by Supervisor during UWP Draft; locked after submission</p>
                </div>
                <button type="button" data-close-modal
                        class="shrink-0 rounded-lg border border-slate-800 bg-slate-950/50 px-2.5 py-2 text-slate-400 hover:text-white hover:bg-slate-950">
                    ✕
                </button>
            </div>

            <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/40 overflow-hidden">
                <div class="max-h-[56vh] overflow-auto">
                    <table class="w-full text-sm text-slate-200">
                        <thead class="sticky top-0 z-10 bg-slate-950 text-slate-300 text-xs uppercase">
                            <tr class="border-b border-slate-800">
                                <th class="px-4 py-3 text-left w-[8%]">Rating</th>
                                <th class="px-4 py-3 text-left w-[42%]">Quality (Q)</th>
                                <th class="px-4 py-3 text-left w-[25%]">Efficiency (E)</th>
                                <th class="px-4 py-3 text-left w-[25%]">Timeliness (T)</th>
                            </tr>
                        </thead>
                        <tbody id="ipcr-standards-body" class="divide-y divide-slate-800">
                            <!-- injected -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-slate-800 pt-4">
                <button type="button" data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- ASSIGNED EMPLOYEES MODAL -->
    <div id="ipcr-assigned-modal" data-modal-container role="dialog" aria-modal="true"
         class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Assigned Employees</p>
                    <h2 class="mt-1 text-lg font-semibold text-white truncate">Revenue Collection Unit</h2>
                    <p class="text-sm text-slate-400">Read-only | Assignments locked after UWP submission</p>
                    <div class="mt-2">
                        <span class="inline-flex items-center rounded-full bg-emerald-500/15 px-2.5 py-1 text-[11px] font-semibold text-emerald-200 border border-emerald-500/30">
                            Client-approved demo
                        </span>
                    </div>
                </div>
                <button type="button" data-close-modal
                        class="shrink-0 rounded-lg border border-slate-800 bg-slate-950/50 px-2.5 py-2 text-slate-400 hover:text-white hover:bg-slate-950">
                    ✕
                </button>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Office / Unit</p>
                    <p class="mt-1 text-sm font-semibold text-white">Revenue Collection Unit</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Period</p>
                    <p class="mt-1 text-sm font-semibold text-white">January – June 2026</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Source</p>
                    <p class="mt-1 text-sm font-semibold text-white">UWP Assigned Employees</p>
                </div>
            </div>

            <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/40 overflow-hidden">
                <table class="w-full text-sm text-slate-200">
                    <thead class="bg-slate-950 text-slate-300 text-xs uppercase">
                        <tr class="border-b border-slate-800">
                            <th class="px-4 py-3 text-left">Employee Name</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr class="hover:bg-slate-900/40">
                            <td class="px-4 py-3 text-white font-semibold">Ramon Reyes</td>
                            <td class="px-4 py-3 text-slate-200">Revenue Collection Unit</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-emerald-500/15 px-2.5 py-1 text-[11px] font-semibold text-emerald-200 border border-emerald-500/30">
                                    Assigned
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end border-t border-slate-800 pt-4">
                <button type="button" data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- EXISTING GENERIC MODAL (kept for save draft preview consistency) -->
    <div id="employee-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/60 px-4 py-6">
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
        // --------- DEMO DATA (LOCKED) ----------
        const demoMfos = {
            mfo_1: {
                title: 'E-Bank Scanning and Encoding of Revenue Transactions',
                indicators: [
                    'All e-bank transactions scanned and encoded daily',
                    'Indexing complete with no missing pages',
                    'Audit trail maintained within 24 hours',
                ],
            },
            mfo_2: {
                title: 'Processing of Over-the-Counter Revenue Transactions',
                indicators: [
                    'Same-day verification of OTC transactions',
                    '95% encoded within the business day',
                    'OR validation completed daily',
                ],
            },
            mfo_support: {
                title: 'Maintenance of Revenue Records Filing System',
                indicators: [
                    'Weekly filing updated and retrievable',
                    'Digital backups synced monthly',
                    'Retrieval logs maintained for audits',
                ],
            },
        };

        const standardsSeedMap = {
            'All e-bank transactions scanned and encoded daily': {
                5: { q: 'No errors; accurate encoding', e: '100% processed', t: 'Same working day' },
                4: { q: 'Minor errors', e: '100% processed', t: 'Same working day' },
                3: { q: 'Few minor errors', e: '95–99% processed', t: 'End of working day' },
                2: { q: 'Multiple errors', e: '<95% processed', t: 'Beyond working day' },
                1: { q: 'Major errors/missing', e: 'Majority unprocessed', t: 'Not within acceptable time' },
            },
            'Indexing complete with no missing pages': {
                5: { q: 'Indexing fully verified, zero gaps', e: '100% pages indexed', t: 'Same day' },
                4: { q: 'Indexing minor rechecks', e: '100% pages indexed', t: 'Same day' },
                3: { q: 'Occasional missing indexes fixed', e: '95–99% indexed', t: 'Within 24 hours' },
                2: { q: 'Frequent missing pages', e: '<95% indexed', t: 'Beyond 24 hours' },
                1: { q: 'Indexing largely incomplete', e: 'Major gaps', t: 'Unacceptable' },
            },
            'Audit trail maintained within 24 hours': {
                5: { q: 'Complete trail, no errors', e: '100% entries captured', t: 'Within 24 hours' },
                4: { q: 'Minor corrections only', e: '100% entries captured', t: 'Within 24 hours' },
                3: { q: 'Some gaps corrected', e: '95–99% entries captured', t: 'Within 48 hours' },
                2: { q: 'Multiple missing logs', e: '<95% captured', t: 'Beyond 48 hours' },
                1: { q: 'Trail missing', e: 'Majority uncaptured', t: 'Unacceptable' },
            },
            'Same-day verification of OTC transactions': {
                5: { q: 'Verified without discrepancies', e: '100% OTC verified', t: 'Same working day' },
                4: { q: 'Minor verifications pending', e: '100% OTC verified', t: 'Same working day' },
                3: { q: 'Few pending verifications', e: '95–99% verified', t: 'End of working day' },
                2: { q: 'Several unverified', e: '<95% verified', t: 'Beyond working day' },
                1: { q: 'Verification not done', e: 'Majority unverified', t: 'Unacceptable' },
            },
            '95% encoded within the business day': {
                5: { q: 'Encodings error-free', e: '100% encoded', t: 'Same business day' },
                4: { q: 'Minor corrections', e: '100% encoded', t: 'Same business day' },
                3: { q: 'Few delays', e: '95–99% encoded', t: 'By end of day' },
                2: { q: 'Multiple delays', e: '<95% encoded', t: 'Next day' },
                1: { q: 'Encoding largely incomplete', e: 'Major backlog', t: 'Unacceptable' },
            },
            'OR validation completed daily': {
                5: { q: 'All ORs validated error-free', e: '100% validated', t: 'Daily' },
                4: { q: 'Minor issues corrected same day', e: '100% validated', t: 'Daily' },
                3: { q: 'Some validations late', e: '95–99% validated', t: 'Within 48 hours' },
                2: { q: 'Frequent late validations', e: '<95% validated', t: 'Beyond 48 hours' },
                1: { q: 'Validations mostly missing', e: 'Majority unvalidated', t: 'Unacceptable' },
            },
            'Weekly filing updated and retrievable': {
                5: { q: 'Zero retrieval issues', e: '100% weekly updates', t: 'Within week' },
                4: { q: 'Minor retrieval fixes', e: '100% weekly updates', t: 'Within week' },
                3: { q: 'Some items late', e: '95–99% updates', t: 'Within next week' },
                2: { q: 'Many late updates', e: '<95% updates', t: 'Beyond next week' },
                1: { q: 'Updates not done', e: 'Major gaps', t: 'Unacceptable' },
            },
            'Digital backups synced monthly': {
                5: { q: 'Backups verified', e: '100% synced', t: 'Within month' },
                4: { q: 'Minor sync corrections', e: '100% synced', t: 'Within month' },
                3: { q: 'Some delays', e: '95–99% synced', t: 'Within following week' },
                2: { q: 'Frequent delays', e: '<95% synced', t: 'Beyond following week' },
                1: { q: 'Backups largely missing', e: 'Major gaps', t: 'Unacceptable' },
            },
            'Retrieval logs maintained for audits': {
                5: { q: 'Logs complete and audit-ready', e: '100% requests logged', t: 'Same day' },
                4: { q: 'Minor log gaps corrected', e: '100% requests logged', t: 'Same day' },
                3: { q: 'Some gaps', e: '95–99% logged', t: 'Within 48 hours' },
                2: { q: 'Many gaps', e: '<95% logged', t: 'Beyond 48 hours' },
                1: { q: 'Logs largely missing', e: 'Majority unlogged', t: 'Unacceptable' },
            },
        };

        // --------- MODAL HELPERS (STACKED) ----------
        function getOpenModals() {
            return Array.from(document.querySelectorAll('[data-modal-container]'))
                .filter(m => !m.classList.contains('hidden'))
                .sort((a, b) => (Number(a.style.zIndex || 0) - Number(b.style.zIndex || 0)));
        }

        function openModal(modal) {
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal(modal) {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            const anyOpen = document.querySelector('[data-modal-container]:not(.hidden)');
            if (!anyOpen) {
                document.body.classList.remove('overflow-hidden');
            }
        }

        function closeTopMostModal() {
            const open = getOpenModals();
            if (!open.length) return;
            closeModal(open[open.length - 1]);
        }

        function closeAllModals() {
            document.querySelectorAll('[data-modal-container]').forEach(m => {
                m.classList.add('hidden');
                m.classList.remove('flex');
            });
            document.body.classList.remove('overflow-hidden');
        }

        // Close buttons
        document.querySelectorAll('[data-close-modal]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('[data-modal-container]');
                closeModal(modal);
            });
        });

        // Backdrop click close
        document.querySelectorAll('[data-modal-container]').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal);
            });
        });

        // ESC close top-most
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const open = getOpenModals();
                if (open.length) {
                    closeTopMostModal();
                } else {
                    // also close the generic modal if open
                    const legacy = document.getElementById('employee-action-modal');
                    if (legacy && !legacy.classList.contains('hidden')) {
                        legacy.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    }
                }
            }
        });

        // --------- INDICATORS MODAL BUILD ----------
        const indicatorsModal = document.getElementById('ipcr-indicators-modal');
        const indicatorsTitle = document.getElementById('ipcr-indicators-title');
        const indicatorsBody = document.getElementById('ipcr-indicators-body');

        const standardsModal = document.getElementById('ipcr-standards-modal');
        const standardsTitle = document.getElementById('ipcr-standards-title');
        const standardsBody = document.getElementById('ipcr-standards-body');

        const assignedModal = document.getElementById('ipcr-assigned-modal');

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildIndicatorsRows(mfoKey) {
            const mfo = demoMfos[mfoKey];
            if (!mfo || !Array.isArray(mfo.indicators)) return '';

            return mfo.indicators.map((indicator) => {
                const safe = escapeHtml(indicator);
                return `
                    <tr class="hover:bg-slate-900/40">
                        <td class="px-4 py-3 align-top">
                            <p class="text-white">${safe}</p>
                            <p class="mt-1 text-[11px] text-slate-500">Derived from approved UWP (locked)</p>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <button type="button"
                                    data-open-standards
                                    data-indicator="${safe}"
                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 14l3-3 3 3 5-6" />
                                </svg>
                                View Standards
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function openIndicatorsModal(mfoKey) {
            const mfo = demoMfos[mfoKey];
            if (!mfo) return;

            indicatorsTitle.textContent = `Success Indicators – ${mfo.title}`;
            indicatorsBody.innerHTML = buildIndicatorsRows(mfoKey) || `
                <tr><td class="px-4 py-3 text-slate-300" colspan="2">No indicators available.</td></tr>
            `;

            // attach standards listeners inside indicators table
            indicatorsBody.querySelectorAll('[data-open-standards]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const indicator = btn.dataset.indicator || '';
                    openStandardsModal(indicator);
                });
            });

            openModal(indicatorsModal);
        }

        // --------- STANDARDS MODAL BUILD ----------
        function buildStandardsRows(indicatorText) {
            const map = standardsSeedMap[indicatorText];
            if (!map) {
                return `<tr><td colspan="4" class="px-4 py-3 text-slate-300">No standards available for this indicator.</td></tr>`;
            }

            const ratings = [5,4,3,2,1];
            return ratings.map((r) => {
                const row = map[r] || {};
                return `
                    <tr class="hover:bg-slate-900/40">
                        <td class="px-4 py-3 text-white font-semibold">${r}</td>
                        <td class="px-4 py-3 text-slate-200">${escapeHtml(row.q || '-')}</td>
                        <td class="px-4 py-3 text-slate-200">${escapeHtml(row.e || '-')}</td>
                        <td class="px-4 py-3 text-slate-200">${escapeHtml(row.t || '-')}</td>
                    </tr>
                `;
            }).join('');
        }

        function openStandardsModal(indicatorText) {
            standardsTitle.textContent = `Standards (Q/E/T) – ${indicatorText}`;
            standardsBody.innerHTML = buildStandardsRows(indicatorText);
            openModal(standardsModal);
        }

        // open indicators buttons
        document.querySelectorAll('[data-open-indicators]').forEach(btn => {
            btn.addEventListener('click', () => openIndicatorsModal(btn.dataset.mfo));
        });

        // assigned employees modal open
        document.querySelectorAll('[data-open-assigned-employees]').forEach(btn => {
            btn.addEventListener('click', () => openModal(assignedModal));
        });

        // --------- LEGACY GENERIC MODAL (for Save Draft only) ----------
        const legacyModal = document.getElementById('employee-action-modal');
        const legacyTitle = document.getElementById('employee-action-title');
        const legacyBody = document.getElementById('employee-action-body');
        const legacyConfirmBtn = document.getElementById('employee-action-confirm');
        let legacyActiveTrigger = null;

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
                if (spinner) spinner.classList.remove('hidden');
                if (label && loadingText) label.textContent = loadingText;
            } else {
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-wait');
                if (spinner) spinner.classList.add('hidden');
                if (label && button.dataset.originalLabel) label.textContent = button.dataset.originalLabel;
            }
        }

        function openLegacyModal(trigger) {
            legacyActiveTrigger = trigger;
            legacyTitle.textContent = trigger.dataset.actionTitle || 'Action';
            legacyBody.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
            legacyConfirmBtn.dataset.actionLoading = trigger.dataset.actionLoading || 'Working...';
            legacyModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeLegacyModal() {
            legacyModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            legacyActiveTrigger = null;
            setButtonLoading(legacyConfirmBtn, false);
        }

        // Only keep legacy for Save Draft action (prevent indicators from using it)
        document.querySelectorAll('[data-employee-action]').forEach((button) => {
            const title = (button.dataset.actionTitle || '').toLowerCase();
            if (!title.includes('save')) return;

            button.addEventListener('click', function (event) {
                event.preventDefault();
                openLegacyModal(button);
            });
        });

        legacyConfirmBtn?.addEventListener('click', function () {
            setButtonLoading(legacyConfirmBtn, true, legacyConfirmBtn.dataset.actionLoading);
            if (legacyActiveTrigger) {
                setButtonLoading(legacyActiveTrigger, true, legacyActiveTrigger.dataset.actionLoading || legacyConfirmBtn.dataset.actionLoading);
            }

            setTimeout(() => {
                setButtonLoading(legacyConfirmBtn, false);
                if (legacyActiveTrigger) setButtonLoading(legacyActiveTrigger, false);
                closeLegacyModal();
            }, 1200);
        });

        legacyModal?.addEventListener('click', function (event) {
            if (event.target === legacyModal) closeLegacyModal();
        });

        legacyModal?.querySelectorAll('[data-employee-modal-close]').forEach((button) => {
            button.addEventListener('click', closeLegacyModal);
        });

        // --------- COMMIT BUTTON DEMO (status updates + disable after commit) ----------
        const commitBtn = document.getElementById('commit-targets-btn');
        const statusBadge = document.getElementById('ipcr-status-badge');
        const statusText = document.getElementById('ipcr-status-text');

        document.querySelectorAll('[data-employee-loading="true"]').forEach((button) => {
            button.addEventListener('click', function () {
                if (button.dataset.loadingActive === 'true') return;

                // prevent double commit after committed
                if (button.id === 'commit-targets-btn' && button.dataset.committed === 'true') return;

                button.dataset.loadingActive = 'true';
                setButtonLoading(button, true, button.dataset.loadingText || 'Loading...');

                const duration = Number.parseInt(button.dataset.loadingDuration || '1200', 10);
                const delay = Number.isNaN(duration) ? 1200 : duration;

                setTimeout(() => {
                    setButtonLoading(button, false);
                    button.dataset.loadingActive = 'false';

                    if (button.id === 'commit-targets-btn') {
                        button.dataset.committed = 'true';
                        button.disabled = true;
                        button.classList.add('opacity-70', 'cursor-not-allowed');

                        if (statusBadge) {
                            statusBadge.textContent = 'COMMITTED';
                            statusBadge.className = 'px-3 py-1 text-xs font-medium rounded bg-emerald-900 text-emerald-300 border border-emerald-800';
                        }
                        if (statusText) {
                            statusText.textContent = 'Committed';
                        }
                    }
                }, delay);
            });
        });
    });
    </script>
    @endpush
@endsection
