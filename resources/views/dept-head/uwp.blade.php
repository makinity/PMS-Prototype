@extends('layouts.dept-head')
    {{-- Page Header --}}
    @section('main-content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-100">Unit Work Plan Review</h1>
        <p class="text-sm text-slate-400 mt-1">
            Select an office/unit to review its submitted Unit Work Plan. Approve or return with remarks.
        </p>
    </div>

    {{-- Filters / Meta (Optional but useful) --}}
    <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-5 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400">Performance Period</p>
            <p class="font-medium text-slate-100">January – June 2026</p>
        </div>

        <div class="flex items-center gap-3">
            <input
                type="text"
                placeholder="Search office/unit..."
                class="w-full md:w-72
                    bg-slate-900 text-slate-100 placeholder-slate-300
                    border border-slate-700
                    rounded-lg px-3 py-2 text-sm
                    focus:bg-slate-900 focus:border-blue-500
                    focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                style="background:#0f172a;color:#e5e7eb;"
            />
            <select
                style="background:#0f172a;color:#e5e7eb;"
                class="bg-slate-950 border border-slate-800 rounded-lg px-3 py-2
                text-sm text-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option>All Status</option>
                <option>Submitted</option>
                <option>Draft</option>
                <option>Endorsed</option>
                <option>Returned</option>
            </select>
        </div>
    </div>

    {{-- Office/Unit List --}}
    <div class="bg-slate-900/80 border border-slate-800 rounded-xl overflow-hidden">
        <div class="p-5 border-b border-slate-800">
            <h2 class="text-lg font-medium text-slate-100">Offices / Units</h2>
            <p class="text-sm text-slate-400 mt-1">
                Click a unit to open its UWP planned outputs.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-800/60">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Office / Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Supervisor</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">UWP Type</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-800">
                    {{-- SAMPLE ROW: Submitted --}}
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-4 py-3 text-sm text-slate-100 font-medium">
                            Revenue Collection Unit
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            Carlo D. Beray
                        </td>
                        <td class="px-4 py-3 text-sm text-center text-slate-300">
                            Unit-Level Plan
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full
                                bg-blue-500/10 text-blue-300 border border-blue-500/20">
                                Submitted
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            {{-- Clicking opens modal --}}
                            <a href="#uwp-unit-1"
                               data-modal-target="uwp-review-modal"
                               data-modal-toggle="uwp-review-modal"
                               class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg
                               border border-blue-500 text-blue-400 hover:bg-blue-500/10 transition">
                                Review UWP
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- MODAL 1: Review UWP --}}
    {{-- ========================= --}}
    <div id="uwp-unit-1" class="fixed inset-0 z-50 hidden target:block">
        {{-- Backdrop --}}
        <a href="#" class="absolute inset-0 bg-black/60"></a>

        {{-- Modal Card --}}
        <div class="relative mx-auto mt-10 w-[92%] md:w-[900px] bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            {{-- Modal Header --}}
            <div class="p-5 border-b border-slate-800 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-100">Unit Work Plan (UWP)</h3>
                    <p class="text-sm text-slate-400 mt-1">
                        Revenue Collection Unit • January – June 2026
                    </p>

                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full
                            bg-blue-500/10 text-blue-300 border border-blue-500/20">
                            Submitted for Approval
                        </span>
                        <span class="text-xs text-slate-500">
                            Supervisor: <span class="text-slate-300">Carlo D. Beray</span> •
                            Dept Head: <span class="text-slate-300">Engr. Roberto Reyes</span>
                        </span>
                    </div>
                </div>

                <a href="#"
                   class="px-3 py-2 text-sm rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800/60 transition">
                    Close
                </a>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 space-y-6 max-h-[70vh] overflow-y-auto">
                {{-- Planned Outputs --}}
                <div class="bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
                    <div class="p-4 border-b border-slate-800">
                        <h4 class="text-sm font-medium text-slate-100">Planned Outputs</h4>
                        <p class="text-xs text-slate-400 mt-1">Read-only. Review targets and classification.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Major Final Output</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Expected Output</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Target</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Timeframe</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Function</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-slate-100">Records Management</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">Process and file incoming documents</td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-100">1,200 documents</td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-300">Jan – Dec</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="px-2 py-1 rounded-md text-xs font-medium
                                            bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Core
                                        </span>
                                    </td>
                                </tr>
                                {{-- Add more planned outputs rows --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Review Remarks --}}
                <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-4">
                    <h4 class="text-sm font-medium text-slate-100 mb-2">Review Remarks</h4>
                    <textarea
                        rows="4"
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3
                        text-sm text-slate-200 placeholder-slate-500
                        focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Add remarks or justification (required if returning the plan)..."></textarea>
                    <p class="text-xs text-slate-500 mt-2">
                        Tip: If returning, include specific revision instructions (e.g., adjust targets, clarify indicators).
                    </p>
                </div>
            </div>

            {{-- Modal Footer Actions --}}
            <div class="p-5 border-t border-slate-800 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="text-xs text-slate-500">
                    This action will be recorded in the audit log.
                </div>

                <div class="flex items-center gap-3 justify-end">
                    <button
                        class="px-4 py-2 text-sm font-medium rounded-lg
                        bg-emerald-600 hover:bg-emerald-500 text-white transition">
                        Endorse & Forward to PMT
                    </button>

                    <button
                        class="px-4 py-2 text-sm font-medium rounded-lg
                        bg-rose-600/10 text-rose-400 border border-rose-500/30
                        hover:bg-rose-600/20 transition">
                        Return for Revision
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- MODAL 2: View Endorsed UWP --}}
    {{-- ========================= --}}
    <div id="uwp-unit-2" class="fixed inset-0 z-50 hidden target:block">
        <a href="#" class="absolute inset-0 bg-black/60"></a>

        <div class="relative mx-auto mt-10 w-[92%] md:w-[900px] bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="p-5 border-b border-slate-800 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-100">Unit Work Plan (UWP)</h3>
                    <p class="text-sm text-slate-400 mt-1">Records Management Unit • January – June 2026</p>

                    <div class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full
                            bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                            Endorsed
                        </span>
                    </div>
                </div>

                <a href="#"
                   class="px-3 py-2 text-sm rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800/60 transition">
                    Close
                </a>
            </div>

            <div class="p-5 max-h-[70vh] overflow-y-auto">
                <div class="bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
                    <div class="p-4 border-b border-slate-800">
                        <h4 class="text-sm font-medium text-slate-100">Planned Outputs</h4>
                        <p class="text-xs text-slate-400 mt-1">Read-only. Endorsed UWPs cannot be modified.</p>
                    </div>
                    <div class="p-4 text-sm text-slate-300">
                        (Put the same planned outputs table here.)
                    </div>
                </div>
            </div>

            <div class="p-5 border-t border-slate-800 flex justify-end">
                <a href="#"
                   class="px-4 py-2 text-sm font-medium rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800/60 transition">
                    Close
                </a>
            </div>
        </div>
    </div>

    {{-- Review UWP Modal (Flowbite-style) --}}
    <div id="uwp-review-modal" data-modal-container tabindex="-1" aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur" data-modal-hide="uwp-review-modal"></div>
        <div class="relative z-10 w-full max-w-5xl px-4">
            <div class="w-full overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-6 py-4">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Unit Work Plan</p>
                        <h3 class="text-lg font-semibold text-white">Review UWP</h3>
                        <div class="grid grid-cols-1 gap-3 text-sm text-slate-300 sm:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">Office / Unit</p>
                                <p class="font-medium text-slate-100">Revenue Collection Unit</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">Supervisor</p>
                                <p class="font-medium text-slate-100">Carlo D. Beray                                </p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">Performance Period</p>
                                <p class="font-medium text-slate-100">January - June 2026</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500">UWP Type</p>
                                <p class="font-medium text-slate-100">Unit-Level Plan</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" data-modal-hide="uwp-review-modal"
                            class="text-slate-400 transition hover:text-white">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">
                    <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                        <div class="border-b border-slate-800 px-4 py-3">
                            <h4 class="text-sm font-medium text-slate-100">Planned Outputs</h4>
                            <p class="text-xs text-slate-400 mt-1">Read-only reference for review.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-slate-800/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-400">PPA / MFO</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Success Indicators</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Assigned Employees</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Timeline / Target</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Function</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="px-4 py-3 text-sm text-slate-100">E-Bank Scanning and Encoding of Revenue Transactions</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">
                                        <button type="button"
                                                class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                                data-uwp-view-indicators
                                                data-title="E-Bank Scanning and Encoding of Revenue Transactions"
                                                data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'>
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>(3)</span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button"
                                                class="inline-flex items-center gap-1 text-blue-300 hover:text-blue-200"
                                                data-uwp-view-assignees
                                                data-unit="Revenue Collection Unit">
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5c-4.5 0-8.25 3.75-8.25 8.25S7.5 21 12 21s8.25-3.75 8.25-8.25S16.5 4.5 12 4.5zm0 0v0" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5z" />
                                            </svg>
                                            <span class="text-xs">View</span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-100">Daily; all e-bank transactions processed within the same working day</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Core
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="px-4 py-3 text-sm text-slate-100">Processing of over-the-counter revenue transactions</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">
                                        <button type="button"
                                                class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                                data-uwp-view-indicators
                                                data-title="Processing of over-the-counter revenue transactions"
                                                data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'>
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>(3)</span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button"
                                                class="inline-flex items-center gap-1 text-blue-300 hover:text-blue-200"
                                                data-uwp-view-assignees
                                                data-unit="Revenue Collection Unit">
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5c-4.5 0-8.25 3.75-8.25 8.25S7.5 21 12 21s8.25-3.75 8.25-8.25S16.5 4.5 12 4.5zm0 0v0" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5z" />
                                            </svg>
                                            <span class="text-xs">View</span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-100">Daily; 95% processed within the same working day</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-blue-500/10 text-blue-300 border border-blue-400/30">
                                            Core
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="px-4 py-3 text-sm text-slate-100">Maintenance of Revenue Records Filing System</td>
                                    <td class="px-4 py-3 text-sm text-slate-300">
                                        <button type="button"
                                                class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                                data-uwp-view-indicators
                                                data-title="Maintenance of Revenue Records Filing System"
                                                data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'>
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>(3)</span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button"
                                                class="inline-flex items-center gap-1 text-blue-300 hover:text-blue-200"
                                                data-uwp-view-assignees
                                                data-unit="Revenue Collection Unit">
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5c-4.5 0-8.25 3.75-8.25 8.25S7.5 21 12 21s8.25-3.75 8.25-8.25S16.5 4.5 12 4.5zm0 0v0" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5z" />
                                            </svg>
                                            <span class="text-xs">View</span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-slate-100">Quarterly validation and update</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-blue-500/10 text-blue-300 border border-blue-400/30">
                                            Support
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 border-t border-slate-800 px-6 py-4">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Review Remarks <span class="text-slate-500">(required if returning)</span>
                        </label>
                        <textarea
                            rows="3"
                            required
                            class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            style="background:#0f172a;color:#e5e7eb;"
                            placeholder="Add clear instructions or justification for your decision..."></textarea>
                    </div>
                    <div class="flex flex-wrap items-center justify-end gap-3">
                        <button type="button" data-modal-hide="uwp-review-modal"
                                class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-slate-800/80">
                            Cancel
                        </button>
                        <button type="button"
                                data-admin-loading="true"
                                data-loading-text="Processing..."
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/40 transition hover:bg-emerald-500">
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                            <span data-button-label>Endorse & Forward to PMT</span>
                        </button>
                        <button type="button"
                                data-admin-loading="true"
                                data-loading-text="Returning..."
                                class="inline-flex items-center gap-2 rounded-lg border border-rose-500/30 bg-rose-600/10 px-4 py-2 text-sm font-semibold text-rose-200 transition hover:bg-rose-600/20">
                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-rose-200/40 border-t-rose-200"></span>
                            <span data-button-label>Return for Revision</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="uwp-indicators-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="uwp-indicators-title" class="text-lg font-semibold text-white">--</h3>
                    <p class="text-xs text-slate-400 mt-1">Read-only list of success indicators for this output.</p>
                </div>
                <button type="button" data-modal-hide="uwp-indicators-modal" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4">
                <div class="overflow-hidden rounded-lg border border-slate-800 bg-slate-950/70">
                    <div class="max-h-64 overflow-y-auto">
                        <table class="w-full text-sm text-slate-100">
                            <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Success Indicator</th>
                                    <th class="px-4 py-3 text-center">Standards</th>
                                </tr>
                            </thead>
                            <tbody id="uwp-indicators-table-body" class="divide-y divide-slate-800">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button"
                        data-modal-hide="uwp-indicators-modal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    <div id="uwp-standards-viewer-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Target Difficulty / Standards</p>
                    <h3 class="text-lg font-semibold text-white">Standards (Q/E/T)</h3>
                    <p class="text-sm text-slate-400 mt-1">
                        Indicator: <span id="uwp-standards-indicator-label" class="font-semibold text-slate-100">--</span>
                    </p>
                </div>
                <button type="button" data-modal-hide="uwp-standards-viewer-modal" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-4">
                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
                    <table class="w-full text-sm text-slate-100">
                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left">Rating</th>
                                <th class="px-4 py-3 text-left">Quality (Q)</th>
                                <th class="px-4 py-3 text-left">Efficiency (E)</th>
                                <th class="px-4 py-3 text-left">Timeliness (T)</th>
                            </tr>
                        </thead>
                        <tbody id="uwp-standards-table-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex justify-end border-t border-slate-800 pt-3">
                <button data-modal-hide="uwp-standards-viewer-modal"
                        class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    <div id="uwp-assignees-viewer-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employees</p>
                    <h3 class="text-lg font-semibold text-white">Employees under the selected Office/Unit</h3>
                    <p class="text-sm text-slate-400 mt-1">
                        Office / Unit: <span id="uwp-assignees-unit-label" class="font-semibold text-slate-100">--</span>
                    </p>
                </div>
                <button type="button" data-modal-hide="uwp-assignees-viewer-modal" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-4">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <input type="text"
                               placeholder="Search employee…"
                               class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                               style="background:#0f172a;color:#e5e7eb;">
                    </div>
                    <div>
                        <select
                            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                            style="background:#0f172a;color:#e5e7eb;">
                            <option>All Status</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
                    <table class="w-full text-sm text-slate-100">
                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left">Employee Name</th>
                                <th class="px-4 py-3 text-left">Office / Unit</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <tr>
                                <td class="px-4 py-3">Ramon Reyes</td>
                                <td class="px-4 py-3">Revenue Collection Unit</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                                        Assigned
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex justify-end border-t border-slate-800 pt-3">
                <button data-modal-hide="uwp-assignees-viewer-modal"
                        class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const standardRatings = [5, 4, 3, 2, 1];
            const uwpIndicatorsByMfo = {
                "E-Bank Scanning and Encoding of Revenue Transactions": [
                    "All e-bank transactions scanned and encoded daily",
                    "Indexing complete with no missing pages",
                    "Audit trail maintained within 24 hours"
                ],
                "Processing of over-the-counter revenue transactions": [
                    "Same-day verification of OTC transactions",
                    "95% encoded within the business day",
                    "OR validation completed daily"
                ],
                "Maintenance of Revenue Records Filing System": [
                    "Weekly filing updated and retrievable",
                    "Digital backups synced monthly",
                    "Retrieval logs maintained for audits"
                ]
            };
            const uwpStandardsByMfo = {
                "E-Bank Scanning and Encoding of Revenue Transactions": {
                    "All e-bank transactions scanned and encoded daily": {
                        "5": { Q: ["No errors; accurate encoding"], E: ["100% processed"], T: ["Same working day"] },
                        "4": { Q: ["1–2 minor errors"], E: ["100% processed"], T: ["Next working day"] },
                        "3": { Q: ["3–4 minor errors"], E: ["95–99% processed"], T: ["By end of working day"] },
                        "2": { Q: ["Major errors"], E: ["<95% processed"], T: ["Beyond working day"] },
                        "1": { Q: ["Unacceptable / not done"], E: ["Majority unprocessed"], T: ["Not within acceptable time"] }
                    },
                    "Indexing complete with no missing pages": {
                        "5": { Q: ["Indexing fully verified, zero gaps"], E: ["100% pages indexed"], T: ["Same day"] },
                        "4": { Q: ["Minor indexing rechecks"], E: ["100% pages indexed"], T: ["Same day"] },
                        "3": { Q: ["Occasional missing indexes fixed"], E: ["95–99% indexed"], T: ["Within 24 hours"] },
                        "2": { Q: ["Frequent missing pages"], E: ["<95% indexed"], T: ["Beyond 24 hours"] },
                        "1": { Q: ["Indexing largely incomplete"], E: ["Major gaps"], T: ["Unacceptable"] }
                    },
                    "Audit trail maintained within 24 hours": {
                        "5": { Q: ["Complete trail, no errors"], E: ["100% entries captured"], T: ["Within 24 hours"] },
                        "4": { Q: ["Minor corrections only"], E: ["100% entries captured"], T: ["Within 24 hours"] },
                        "3": { Q: ["Some gaps corrected"], E: ["95–99% entries captured"], T: ["Within 48 hours"] },
                        "2": { Q: ["Multiple missing logs"], E: ["<95% captured"], T: ["Beyond 48 hours"] },
                        "1": { Q: ["Trail missing"], E: ["Majority uncaptured"], T: ["Unacceptable"] }
                    }
                },
                "Processing of over-the-counter revenue transactions": {
                    "Same-day verification of OTC transactions": {
                        "5": { Q: ["Verified without discrepancies"], E: ["100% OTC verified"], T: ["Same working day"] },
                        "4": { Q: ["Minor verifications pending"], E: ["100% OTC verified"], T: ["Same working day"] },
                        "3": { Q: ["Few pending verifications"], E: ["95–99% verified"], T: ["End of working day"] },
                        "2": { Q: ["Several unverified"], E: ["<95% verified"], T: ["Beyond working day"] },
                        "1": { Q: ["Verification not done"], E: ["Majority unverified"], T: ["Unacceptable"] }
                    },
                    "95% encoded within the business day": {
                        "5": { Q: ["Encodings error-free"], E: ["100% encoded"], T: ["Same business day"] },
                        "4": { Q: ["Minor corrections"], E: ["100% encoded"], T: ["Same business day"] },
                        "3": { Q: ["Few delays"], E: ["95–99% encoded"], T: ["By end of day"] },
                        "2": { Q: ["Multiple delays"], E: ["<95% encoded"], T: ["Next day"] },
                        "1": { Q: ["Encoding largely incomplete"], E: ["Major backlog"], T: ["Unacceptable"] }
                    },
                    "OR validation completed daily": {
                        "5": { Q: ["All ORs validated error-free"], E: ["100% validated"], T: ["Daily"] },
                        "4": { Q: ["Minor issues corrected same day"], E: ["100% validated"], T: ["Daily"] },
                        "3": { Q: ["Some validations late"], E: ["95–99% validated"], T: ["Within 48 hours"] },
                        "2": { Q: ["Frequent late validations"], E: ["<95% validated"], T: ["Beyond 48 hours"] },
                        "1": { Q: ["Validations mostly missing"], E: ["Majority unvalidated"], T: ["Unacceptable"] }
                    }
                },
                "Maintenance of Revenue Records Filing System": {
                    "Weekly filing updated and retrievable": {
                        "5": { Q: ["Zero retrieval issues"], E: ["100% weekly updates"], T: ["Within week"] },
                        "4": { Q: ["Minor retrieval fixes"], E: ["100% weekly updates"], T: ["Within week"] },
                        "3": { Q: ["Some items late"], E: ["95–99% updates"], T: ["Within next week"] },
                        "2": { Q: ["Many late updates"], E: ["<95% updates"], T: ["Beyond next week"] },
                        "1": { Q: ["Updates not done"], E: ["Major gaps"], T: ["Unacceptable"] }
                    },
                    "Digital backups synced monthly": {
                        "5": { Q: ["Backups verified"], E: ["100% synced"], T: ["Within month"] },
                        "4": { Q: ["Minor sync corrections"], E: ["100% synced"], T: ["Within month"] },
                        "3": { Q: ["Some delays"], E: ["95–99% synced"], T: ["Within following week"] },
                        "2": { Q: ["Frequent delays"], E: ["<95% synced"], T: ["Beyond following week"] },
                        "1": { Q: ["Backups largely missing"], E: ["Major gaps"], T: ["Unacceptable"] }
                    },
                    "Retrieval logs maintained for audits": {
                        "5": { Q: ["Logs complete and audit-ready"], E: ["100% requests logged"], T: ["Same day"] },
                        "4": { Q: ["Minor log gaps corrected"], E: ["100% requests logged"], T: ["Same day"] },
                        "3": { Q: ["Some gaps"], E: ["95–99% logged"], T: ["Within 48 hours"] },
                        "2": { Q: ["Many gaps"], E: ["<95% logged"], T: ["Beyond 48 hours"] },
                        "1": { Q: ["Logs largely missing"], E: ["Majority unlogged"], T: ["Unacceptable"] }
                    }
                }
            };
            let indicatorStandardsBody;
            let standardsModal;
            let assigneesModal;
            const bodyOverflowClass = 'overflow-hidden';

            function createEmptyStandardsRow() {
                return { Q: [], E: [], T: [] };
            }

            function renderIndicatorStandards(mfoTitle, indicator) {
                if (!indicatorStandardsBody) {
                    return;
                }
                indicatorStandardsBody.innerHTML = '';
                const mfoData = uwpStandardsByMfo[mfoTitle] || {};
                const indicatorData = mfoData[indicator] || {};
                standardRatings.forEach((level) => {
                    const row = indicatorData[level] || createEmptyStandardsRow();
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-900/40';
                    const ratingTd = document.createElement('td');
                    ratingTd.className = 'px-4 py-3 font-semibold';
                    ratingTd.textContent = level;
                    const makeListCell = (items) => {
                        const td = document.createElement('td');
                        td.className = 'px-4 py-3 align-top';
                        if (!items || items.length === 0) {
                            td.textContent = '\u2014';
                            return td;
                        }
                        const ul = document.createElement('ul');
                        ul.className = 'list-disc space-y-1 pl-4 text-slate-200';
                        items.forEach((item) => {
                            const li = document.createElement('li');
                            li.textContent = item;
                            ul.appendChild(li);
                        });
                        td.appendChild(ul);
                        return td;
                    };
                    tr.append(ratingTd, makeListCell(row.Q), makeListCell(row.E), makeListCell(row.T));
                    indicatorStandardsBody.appendChild(tr);
                });
            }

            function showModal(modal) {
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add(bodyOverflowClass);
            }

            function closeModal(modal) {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove(bodyOverflowClass);
            }

            function openStandardsViewer(mfoTitle, indicator) {
                if (!mfoTitle || !indicator) return;
                if (!standardsModal) {
                    standardsModal = document.getElementById('uwp-standards-viewer-modal');
                }
                if (!standardsModal) return;
                const label = document.getElementById('uwp-standards-indicator-label');
                if (label) {
                    label.textContent = indicator;
                }
                renderIndicatorStandards(mfoTitle, indicator);
                showModal(standardsModal);
            }

            function openAssigneesViewer(unit) {
                const currentUnit = unit || 'Revenue Collection Unit';
                if (!assigneesModal) {
                    assigneesModal = document.getElementById('uwp-assignees-viewer-modal');
                }
                if (!assigneesModal) return;
                const label = document.getElementById('uwp-assignees-unit-label');
                if (label) {
                    label.textContent = currentUnit;
                }
                showModal(assigneesModal);
            }

            function initModalHandlers() {
                indicatorStandardsBody = document.getElementById('uwp-standards-table-body');
                standardsModal = document.getElementById('uwp-standards-viewer-modal');
                assigneesModal = document.getElementById('uwp-assignees-viewer-modal');

                document.querySelectorAll('[data-uwp-view-assignees]').forEach((button) => {
                    button.addEventListener('click', () => {
                        openAssigneesViewer(button.dataset.unit);
                    });
                });

                document.querySelectorAll('[data-modal-hide="uwp-standards-viewer-modal"]').forEach((btn) => {
                    btn.addEventListener('click', () => closeModal(standardsModal));
                });
                document.querySelectorAll('[data-modal-hide="uwp-assignees-viewer-modal"]').forEach((btn) => {
                    btn.addEventListener('click', () => closeModal(assigneesModal));
                });

                standardsModal?.addEventListener('click', (event) => {
                    if (event.target === standardsModal) {
                        closeModal(standardsModal);
                    }
                });
                assigneesModal?.addEventListener('click', (event) => {
                    if (event.target === assigneesModal) {
                        closeModal(assigneesModal);
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initModalHandlers);
            } else {
                initModalHandlers();
            }

            (function initIndicatorsModal() {
                const modal = document.getElementById('uwp-indicators-modal');
                const titleEl = document.getElementById('uwp-indicators-title');
                const tableBody = document.getElementById('uwp-indicators-table-body');

                if (!modal || !titleEl || !tableBody) {
                    return;
                }

                function openIndicatorsModal(title, mfoTitle, indicators) {
                    titleEl.textContent = title || '--';
                    tableBody.innerHTML = '';
                    (indicators || []).forEach((text) => {
                        const value = (text || '').trim();
                        if (!value) {
                            return;
                        }
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-900/40';
                        const indicatorTd = document.createElement('td');
                        indicatorTd.className = 'px-4 py-3 text-slate-100';
                        indicatorTd.textContent = value;
                        const actionTd = document.createElement('td');
                        actionTd.className = 'px-4 py-3 text-center';
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80';
                        button.dataset.mfoTitle = mfoTitle || '';
                        button.dataset.indicator = value;
                        button.innerHTML = '<i class="fa-regular fa-eye text-sm"></i><span>View Standards</span>';
                        button.addEventListener('click', () => {
                            openStandardsViewer(button.dataset.mfoTitle, button.dataset.indicator);
                        });
                        actionTd.appendChild(button);
                        tr.append(indicatorTd, actionTd);
                        tableBody.appendChild(tr);
                    });
                    showModal(modal);
                }

                document.querySelectorAll('[data-uwp-view-indicators]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        let indicators = [];
                        try {
                            indicators = JSON.parse(btn.dataset.indicators || '[]');
                        } catch (e) {
                            indicators = [];
                        }
                        openIndicatorsModal(btn.dataset.title || '--', btn.dataset.title || '', indicators);
                    });
                });

                document.querySelectorAll('[data-modal-hide="uwp-indicators-modal"]').forEach((button) => {
                    button.addEventListener('click', () => closeModal(modal));
                });

                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal(modal);
                    }
                });
            })();

            (function initViewUwpModal() {
                const run = () => {
                    const viewModal = document.getElementById('view-uwp-modal');
                    const viewTriggers = document.querySelectorAll('[data-modal-target="view-uwp-modal"]');

                    if (!viewModal || !viewTriggers.length) {
                        return;
                    }

                    const viewModalInstance = typeof Modal !== 'undefined' && viewModal ? new Modal(viewModal) : null;

                    const showViewModal = () => {
                        if (viewModalInstance) {
                            viewModalInstance.show();
                        } else {
                            viewModal.classList.remove('hidden');
                        }
                    };

                    const hideViewModal = () => {
                        if (viewModalInstance) {
                            viewModalInstance.hide();
                        } else {
                            viewModal.classList.add('hidden');
                        }
                    };

                    viewTriggers.forEach((trigger) => {
                        trigger.addEventListener('click', (event) => {
                            event.preventDefault();
                            showViewModal();
                        });
                    });

                    viewModal.querySelectorAll('[data-modal-hide="view-uwp-modal"]').forEach((btn) => {
                        btn.addEventListener('click', (event) => {
                            event.preventDefault();
                            hideViewModal();
                        });
                    });

                    viewModal.addEventListener('click', (event) => {
                        if (event.target === viewModal) {
                            hideViewModal();
                        }
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', run);
                } else {
                    run();
                }
            })();
        </script>
    @endpush
    @endsection
