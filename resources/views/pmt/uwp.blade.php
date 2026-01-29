@extends('layouts.pmt')

@section('main-content')
    {{-- Page Header --}}
    <div class="mb-6">
        <h1>Unit Work Plan Approval</h1>
        <p>Final review and approval of Unit Work Plans for standards compliance and alignment.</p>
    </div>

    {{-- Performance Period --}}
    <div class="mb-6 rounded-2xl border border-slate-800 bg-slate-950 p-5">
        <p class="text-xs uppercase tracking-wide text-slate-400">Performance Period</p>
        <p class="font-medium text-slate-100">January – June 2026</p>
    </div>

    {{-- UWP List --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-950 overflow-hidden">
        <div class="border-b border-slate-800 p-5">
            <h2 class="text-lg font-medium text-white">Unit Work Plans</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">Office / Unit</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">Supervisor</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">Dept Head</th>
                        <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Status</th>
                        <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <tr class="hover:bg-slate-900/60 transition">
                        <td class="px-4 py-3 text-sm font-semibold text-slate-100">
                            Revenue Collection Unit
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            Carlo D. Beray
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            Dept-head
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                For PMT Approval
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <button
                                type="button"
                                data-modal-target="pmt-view-uwp-modal"
                                data-modal-toggle="pmt-view-uwp-modal"
                                class="rounded-lg border border-blue-500 px-3 py-2 text-blue-400
                                hover:bg-blue-500/10 transition">
                                View UWP
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- PMT VIEW UWP MODAL --}}
    {{-- ========================= --}}
    <div
        id="pmt-view-uwp-modal"
        data-modal-container
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur"
    >
        <div class="w-full max-w-5xl px-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 shadow-2xl">

                {{-- Modal Header --}}
                <div class="flex items-start justify-between border-b border-slate-800 px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-indigo-300">
                            Unit Work Plan
                        </p>
                        <h3 class="text-lg font-semibold text-white">
                            Revenue Collection Unit
                        </h3>
                        <p class="mt-1 text-sm text-slate-400">
                            Jan – June 2026 • Supervisor: Carlo D. Beray
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('stage1.uwp.export.excel') }}"
                            class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                            Export Excel
                        </a>

                        <button
                            type="button"
                            data-modal-close
                            data-modal-hide="pmt-view-uwp-modal"
                            class="text-slate-400 hover:text-white"
                        >
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="max-h-[65vh] overflow-y-auto px-6 py-5 space-y-6">

                    {{-- Metadata --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-lg border border-slate-800 bg-slate-900 p-4">
                            <p class="text-xs uppercase text-slate-500">Office / Unit</p>
                            <p class="mt-1 text-sm font-semibold text-white">Revenue Collection Unit</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-900 p-4">
                            <p class="text-xs uppercase text-slate-500">Department Head</p>
                            <p class="mt-1 text-sm font-semibold text-white">Dept-head</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-900 p-4">
                            <p class="text-xs uppercase text-slate-500">Status</p>
                            <p class="mt-1 text-sm font-semibold text-emerald-300">For PMT Approval</p>
                        </div>
                    </div>

                    {{-- Planned Outputs --}}
                    <div class="rounded-xl border border-slate-800 bg-slate-900 overflow-hidden">
                        <div class="border-b border-slate-800 p-4">
                            <h4 class="text-sm font-semibold text-white">Planned Outputs</h4>
                        </div>

                        <table class="min-w-full">
                            <thead class="bg-slate-800/60">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs uppercase text-slate-400">PPA / MFO</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Success Indicators</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Standards (Q/E/T)</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Assigned Employees</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Target / Timeline</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase text-slate-400">Function</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-white">E-Bank Scanning and Encoding of Revenue Transactions</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex justify-center">
                                            <button
                                                type="button"
                                                data-uwp-view-indicators
                                                data-title="E-Bank Scanning and Encoding of Revenue Transactions"
                                                data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <span>(3)</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex justify-center">
                                            <button
                                                type="button"
                                                data-pmt-standards-trigger
                                                data-mfo-key="mfo1"
                                                data-mfo-title="E-Bank Scanning and Encoding of Revenue Transactions"
                                                data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                                <i class="fa-regular fa-eye text-sm"></i>
                                                <span>View</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex justify-center">
                                            <button
                                                type="button"
                                                data-pmt-assignees-trigger
                                                data-unit="Revenue Collection Unit"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                                <i class="fa-regular fa-eye text-sm"></i>
                                                <span>View</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center text-white">Daily; all e-bank transactions processed within the same working day</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium
                                            text-emerald-400 border border-emerald-500/20">
                                            Core
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="px-4 py-3 text-sm text-slate-100">Processing of over-the-counter revenue transactions</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex justify-center">
                                            <button
                                                type="button"
                                                data-uwp-view-indicators
                                                data-title="Processing of over-the-counter revenue transactions"
                                                data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <span>(3)</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex justify-center">
                                            <button
                                                type="button"
                                                data-pmt-standards-trigger
                                                data-mfo-key="mfo2"
                                                data-mfo-title="Processing of over-the-counter revenue transactions"
                                                data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                                <i class="fa-regular fa-eye text-sm"></i>
                                                <span>View</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex justify-center">
                                            <button
                                                type="button"
                                                data-pmt-assignees-trigger
                                                data-unit="Revenue Collection Unit"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                                <i class="fa-regular fa-eye text-sm"></i>
                                                <span>View</span>
                                            </button>
                                        </div>
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
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex justify-center">
                                            <button
                                                type="button"
                                                data-uwp-view-indicators
                                                data-title="Maintenance of Revenue Records Filing System"
                                                data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <span>(3)</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex justify-center">
                                            <button
                                                type="button"
                                                data-pmt-standards-trigger
                                                data-mfo-key="mfo3"
                                                data-mfo-title="Maintenance of Revenue Records Filing System"
                                                data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                                <i class="fa-regular fa-eye text-sm"></i>
                                                <span>View</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex justify-center">
                                            <button
                                                type="button"
                                                data-pmt-assignees-trigger
                                                data-unit="Revenue Collection Unit"
                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                                <i class="fa-regular fa-eye text-sm"></i>
                                                <span>View</span>
                                            </button>
                                        </div>
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

                <div class="flex flex-wrap items-center justify-between border-t border-slate-800 px-6 py-4">
                    <p class="text-xs text-slate-500">
                        PMT decision is final and will lock the Unit Work Plan.
                    </p>

                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            onclick="openPmtReturnModal()"
                            class="inline-flex items-center gap-2
                                rounded-lg border border-rose-500/30 bg-rose-600/10 px-4 py-2
                                text-sm font-semibold text-rose-300
                                hover:bg-rose-600/20 transition">
                            <span>Return to Dept Head</span>
                        </button>

                        <button
                            type="button"
                            data-admin-loading="true"
                            data-loading-text="Approving..."
                            class="inline-flex items-center gap-2
                                rounded-lg bg-emerald-600 px-4 py-2
                                text-sm font-semibold text-white
                                hover:bg-emerald-500 transition">
                            <span data-button-spinner
                                class="hidden h-4 w-4 animate-spin rounded-full
                                        border-2 border-white/40 border-t-white"></span>
                            <span data-button-label>Approve (Final)</span>
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
                    <p class="text-xs text-slate-400 mt-1">Read-only list of indicators for this output.</p>
                </div>
                <button type="button" onclick="closePmtIndicatorsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 max-h-64 overflow-y-auto rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                <ol id="uwp-indicators-list" class="list-decimal space-y-2 pl-5 text-sm text-slate-100"></ol>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closePmtIndicatorsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    <div id="pmt-standards-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-950 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                    <h3 class="text-lg font-semibold text-white">Standards (Q/E/T)</h3>
                    <p class="text-xs text-slate-400 mt-1">View target difficulty per success indicator.</p>
                </div>
                <button type="button" onclick="closePmtStandardsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-5 space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">MFO</p>
                    <p class="text-sm font-semibold text-white" data-standards-mfo>--</p>
                </div>

                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Select Indicator</label>
                    <div class="rounded-lg border border-slate-800 bg-slate-900/70 px-3 py-2">
                        <select
                            data-standards-indicator-select
                            class="w-full bg-transparent text-sm text-slate-100 focus:ring-0 focus:outline-none"
                        >
                        </select>
                    </div>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Indicator</p>
                    <p class="text-sm font-semibold text-white" data-standards-indicator>--</p>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
                    <table class="min-w-full text-sm text-slate-100">
                        <thead class="bg-slate-900/80 text-xs uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left">Rating</th>
                                <th class="px-4 py-3 text-left">Quality (Q)</th>
                                <th class="px-4 py-3 text-left">Efficiency (E)</th>
                                <th class="px-4 py-3 text-left">Timeliness (T)</th>
                            </tr>
                        </thead>
                        <tbody data-standards-body class="divide-y divide-slate-800">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closePmtStandardsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    <div id="pmt-assignees-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-950 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employees</p>
                    <h3 class="text-lg font-semibold text-white">Employees under the selected Office / Unit</h3>
                    <p class="text-xs text-slate-400 mt-1">
                        Office / Unit: <span data-assignees-unit class="font-semibold text-slate-100">--</span>
                    </p>
                </div>
                <button type="button" onclick="closePmtAssigneesModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
                <table class="min-w-full text-sm text-slate-100">
                    <thead class="bg-slate-900/80 text-xs uppercase tracking-[0.2em] text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee Name</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-3">Ramon Reyes</td>
                            <td class="px-4 py-3">Revenue Collection Unit</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                                    Assigned
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closePmtAssigneesModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    <div id="pmt-return-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-950 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-rose-300">Return to Dept Head</p>
                    <h3 class="text-lg font-semibold text-white">Provide Return Remarks</h3>
                    <p class="text-xs text-slate-400 mt-1">This will send the UWP back to the Department Head for revision.</p>
                </div>
                <button type="button" onclick="closePmtReturnModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-3">
                <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Remarks <span class="text-rose-300">*</span>
                </label>
                <textarea
                    id="pmt-return-remarks"
                    rows="4"
                    style="background:#0f172a;color:#e5e7eb;"
                    required
                    class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:ring-2 focus:ring-rose-500 focus:outline-none"
                    placeholder="State the reason for return and required revisions."></textarea>
            </div>

            <div class="mt-5 flex items-center justify-end gap-3">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closePmtReturnModal()">
                    Cancel
                </button>
                <button type="button"
                        data-admin-loading="true"
                        data-loading-text="Returning..."
                        class="inline-flex items-center gap-2 rounded-lg border border-rose-500/30 bg-rose-600/10 px-4 py-2 text-sm font-semibold text-rose-200 transition hover:bg-rose-600/20"
                        onclick="submitPmtReturn()">
                    <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-rose-200/40 border-t-rose-200"></span>
                    <span data-button-label>Confirm Return</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function initPmtIndicatorsModal() {
                const modal = document.getElementById('uwp-indicators-modal');
                const titleEl = document.getElementById('uwp-indicators-title');
                const listEl = document.getElementById('uwp-indicators-list');

                window.closePmtIndicatorsModal = function () {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                };

                function openIndicatorsModal(title, indicators) {
                    if (!modal || !titleEl || !listEl) return;
                    titleEl.textContent = title || '--';
                    listEl.innerHTML = '';
                    (indicators || []).forEach((text) => {
                        const value = (text || '').trim();
                        if (!value) return;
                        const li = document.createElement('li');
                        li.textContent = value;
                        listEl.appendChild(li);
                    });
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                document.querySelectorAll('[data-uwp-view-indicators]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        let indicators = [];
                        try {
                            indicators = JSON.parse(btn.dataset.indicators || '[]');
                        } catch (e) {
                            indicators = [];
                        }
                        openIndicatorsModal(btn.dataset.title || '--', indicators);
                    });
                });
            })();

            (function initPmtReturnModal() {
                const modal = document.getElementById('pmt-return-modal');
                const remarks = document.getElementById('pmt-return-remarks');

                window.openPmtReturnModal = function () {
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                    if (remarks) {
                        remarks.value = '';
                        setTimeout(() => remarks.focus(), 50);
                    }
                };

                window.closePmtReturnModal = function () {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                };

                window.submitPmtReturn = function () {
                    if (!modal || !remarks) return;
                    if (!remarks.value.trim()) {
                        remarks.focus();
                        return;
                    }
                    const confirmBtn = modal.querySelector('[onclick=\"submitPmtReturn()\"]');
                    if (confirmBtn && confirmBtn.dataset.adminLoading) {
                        const label = confirmBtn.querySelector('[data-button-label]');
                        const spinner = confirmBtn.querySelector('[data-button-spinner]');
                        confirmBtn.disabled = true;
                        confirmBtn.classList.add('opacity-70', 'cursor-wait');
                        if (spinner) spinner.classList.remove('hidden');
                        if (label && confirmBtn.dataset.loadingText) label.textContent = confirmBtn.dataset.loadingText;
                        setTimeout(() => {
                            if (spinner) spinner.classList.add('hidden');
                            if (label && confirmBtn.dataset.loadingText) label.textContent = 'Confirm Return';
                            confirmBtn.disabled = false;
                            confirmBtn.classList.remove('opacity-70', 'cursor-wait');
                            closePmtReturnModal();
                        }, 900);
                    } else {
                        closePmtReturnModal();
                    }
                };
            })();

            (function initPmtStandardsModal() {
                const modal = document.getElementById('pmt-standards-modal');
                const mfoEl = modal?.querySelector('[data-standards-mfo]');
                const indicatorEl = modal?.querySelector('[data-standards-indicator]');
                const selectEl = modal?.querySelector('[data-standards-indicator-select]');
                const bodyEl = modal?.querySelector('[data-standards-body]');
                const ratings = [5, 4, 3, 2, 1];
                const standardsSeedMap = {
                    'All e-bank transactions scanned and encoded daily': {
                        '5': { q: ['No errors; accurate encoding'], e: ['100% processed'], t: ['Same working day'] },
                        '4': { q: ['1–2 minor errors'], e: ['100% processed'], t: ['Same working day'] },
                        '3': { q: ['3–4 minor errors'], e: ['95–99% processed'], t: ['By end of working day'] },
                        '2': { q: ['Major errors'], e: ['<95% processed'], t: ['Beyond working day'] },
                        '1': { q: ['Unacceptable / not done'], e: ['Majority unprocessed'], t: ['Not within acceptable time'] },
                    },
                    'Indexing complete with no missing pages': {
                        '5': { q: ['Indexing fully verified, zero gaps'], e: ['100% pages indexed'], t: ['Same day'] },
                        '4': { q: ['Minor indexing rechecks'], e: ['100% pages indexed'], t: ['Same day'] },
                        '3': { q: ['Occasional missing indexes fixed'], e: ['95–99% indexed'], t: ['Within 24 hours'] },
                        '2': { q: ['Frequent missing pages'], e: ['<95% indexed'], t: ['Beyond 24 hours'] },
                        '1': { q: ['Indexing largely incomplete'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Audit trail maintained within 24 hours': {
                        '5': { q: ['Complete trail, no errors'], e: ['100% entries captured'], t: ['Within 24 hours'] },
                        '4': { q: ['Minor corrections only'], e: ['100% entries captured'], t: ['Within 24 hours'] },
                        '3': { q: ['Some gaps corrected'], e: ['95–99% entries captured'], t: ['Within 48 hours'] },
                        '2': { q: ['Multiple missing logs'], e: ['<95% captured'], t: ['Beyond 48 hours'] },
                        '1': { q: ['Trail missing'], e: ['Majority uncaptured'], t: ['Unacceptable'] },
                    },
                    'Same-day verification of OTC transactions': {
                        '5': { q: ['Verified without discrepancies'], e: ['100% OTC verified'], t: ['Same working day'] },
                        '4': { q: ['Minor verifications pending'], e: ['100% OTC verified'], t: ['Same working day'] },
                        '3': { q: ['Few pending verifications'], e: ['95–99% verified'], t: ['End of working day'] },
                        '2': { q: ['Several unverified'], e: ['<95% verified'], t: ['Beyond working day'] },
                        '1': { q: ['Verification not done'], e: ['Majority unverified'], t: ['Unacceptable'] },
                    },
                    '95% encoded within the business day': {
                        '5': { q: ['Encodings error-free'], e: ['100% encoded'], t: ['Same business day'] },
                        '4': { q: ['Minor corrections'], e: ['100% encoded'], t: ['Same business day'] },
                        '3': { q: ['Few delays'], e: ['95–99% encoded'], t: ['By end of day'] },
                        '2': { q: ['Multiple delays'], e: ['<95% encoded'], t: ['Next day'] },
                        '1': { q: ['Encoding largely incomplete'], e: ['Major backlog'], t: ['Unacceptable'] },
                    },
                    'OR validation completed daily': {
                        '5': { q: ['All ORs validated error-free'], e: ['100% validated'], t: ['Daily'] },
                        '4': { q: ['Minor issues corrected same day'], e: ['100% validated'], t: ['Daily'] },
                        '3': { q: ['Some validations late'], e: ['95–99% validated'], t: ['Within 48 hours'] },
                        '2': { q: ['Frequent late validations'], e: ['<95% validated'], t: ['Beyond 48 hours'] },
                        '1': { q: ['Validations mostly missing'], e: ['Majority unvalidated'], t: ['Unacceptable'] },
                    },
                    'Weekly filing updated and retrievable': {
                        '5': { q: ['Zero retrieval issues'], e: ['100% weekly updates'], t: ['Within week'] },
                        '4': { q: ['Minor retrieval fixes'], e: ['100% weekly updates'], t: ['Within week'] },
                        '3': { q: ['Some items late'], e: ['95–99% updates'], t: ['Within next week'] },
                        '2': { q: ['Many late updates'], e: ['<95% updates'], t: ['Beyond next week'] },
                        '1': { q: ['Updates not done'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Digital backups synced monthly': {
                        '5': { q: ['Backups verified'], e: ['100% synced'], t: ['Within month'] },
                        '4': { q: ['Minor sync corrections'], e: ['100% synced'], t: ['Within month'] },
                        '3': { q: ['Some delays'], e: ['95–99% synced'], t: ['Within following week'] },
                        '2': { q: ['Frequent delays'], e: ['<95% synced'], t: ['Beyond following week'] },
                        '1': { q: ['Backups largely missing'], e: ['Major gaps'], t: ['Unacceptable'] },
                    },
                    'Retrieval logs maintained for audits': {
                        '5': { q: ['Logs complete and audit-ready'], e: ['100% requests logged'], t: ['Same day'] },
                        '4': { q: ['Minor log gaps corrected'], e: ['100% requests logged'], t: ['Same day'] },
                        '3': { q: ['Some gaps'], e: ['95–99% logged'], t: ['Within 48 hours'] },
                        '2': { q: ['Many gaps'], e: ['<95% logged'], t: ['Beyond 48 hours'] },
                        '1': { q: ['Logs largely missing'], e: ['Majority unlogged'], t: ['Unacceptable'] },
                    },
                };

                function closeModal() {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                function showModal() {
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function renderStandards(indicator) {
                    if (!bodyEl) return;
                    indicatorEl && (indicatorEl.textContent = indicator || '--');
                    bodyEl.innerHTML = '';
                    ratings.forEach((level) => {
                        const data = standardsSeedMap[indicator]?.[String(level)] || {};
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-slate-900/40';
                        const ratingCell = document.createElement('td');
                        ratingCell.className = 'px-4 py-3 font-semibold';
                        ratingCell.textContent = level;

                        const makeList = (items) => {
                            const td = document.createElement('td');
                            td.className = 'px-4 py-3 align-top';
                            const listItems = Array.isArray(items) ? items : [];
                            if (!listItems.length) {
                                td.textContent = '—';
                                return td;
                            }
                            const ul = document.createElement('ul');
                            ul.className = 'list-disc space-y-1 pl-4 text-slate-200';
                            listItems.forEach((value) => {
                                const li = document.createElement('li');
                                li.textContent = value;
                                ul.appendChild(li);
                            });
                            td.appendChild(ul);
                            return td;
                        };

                        row.append(
                            ratingCell,
                            makeList(data.q),
                            makeList(data.e),
                            makeList(data.t)
                        );
                        bodyEl.appendChild(row);
                    });
                }

                window.closePmtStandardsModal = closeModal;

                function openStandardsModal(mfoTitle, indicators) {
                    if (!modal || !selectEl) return;
                    mfoEl && (mfoEl.textContent = mfoTitle || '--');
                    selectEl.innerHTML = '';
                    (indicators || []).forEach((indicator) => {
                        const option = document.createElement('option');
                        option.value = indicator;
                        option.textContent = indicator;
                        selectEl.appendChild(option);
                    });
                    const initial = indicators?.[0] || '';
                    selectEl.value = initial;
                    renderStandards(initial);
                    showModal();
                }

                selectEl?.addEventListener('change', () => {
                    renderStandards(selectEl.value);
                });

                document.querySelectorAll('[data-pmt-standards-trigger]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        let parsed = [];
                        try {
                            parsed = JSON.parse(btn.dataset.indicators || '[]');
                        } catch (error) {
                            parsed = [];
                        }
                        openStandardsModal(btn.dataset.mfoTitle || '--', parsed);
                    });
                });
            })();

            (function initPmtAssigneesModal() {
                const modal = document.getElementById('pmt-assignees-modal');
                const unitEl = modal?.querySelector('[data-assignees-unit]');

                function closeModal() {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                function showModal() {
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                window.closePmtAssigneesModal = closeModal;

                function openAssignees(unit) {
                    unitEl && (unitEl.textContent = unit || '--');
                    showModal();
                }

                document.querySelectorAll('[data-pmt-assignees-trigger]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        openAssignees(btn.dataset.unit || '--');
                    });
                });
            })();
        </script>
    @endpush
@endsection
