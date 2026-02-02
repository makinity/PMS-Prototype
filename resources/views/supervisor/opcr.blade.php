@extends('layouts.supervisor')

@section('main-content')
<section class="space-y-6 admin-page">

    <!-- HEADER -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-white">
                Office Performance Commitment and Review (OPCR)
            </h1>
            <p class="text-sm text-slate-400">
                Stage I – Performance Planning and Commitment
            </p>
            <p class="text-xs text-slate-500">
                Supervisor generates OPCR based on PMT-approved UWP and submits it for Department Head review.
            </p>
        </div>

        <!-- CREATE OPCR (DIRECT) -->
        <button type="button"
                data-direct="true"
                data-opens-modal="create-opcr-modal"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
            Create OPCR
        </button>
    </div>

    <!-- OPCR LIST -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-300">
                <thead class="text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-2 text-left">Office / Unit</th>
                        <th class="px-4 py-2 text-left">Period</th>
                        <th class="px-4 py-2 text-left">Source (Approved UWP)</th>
                        <th class="px-4 py-2 text-left">Outputs</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-slate-800">
                        <td class="px-4 py-3 text-white">Revenue Collection Unit</td>
                        <td class="px-4 py-3">January – June 2026</td>
                        <td class="px-4 py-3">PMT Approved UWP</td>
                        <td class="px-4 py-3">3 outputs</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300">
                                For Department Head Review
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button data-direct="true"
                                    data-opens-modal="view-opcr-modal"
                                    class="text-blue-400 hover:text-blue-300">
                                View
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE OPCR MODAL -->
    <div id="create-opcr-modal"
        class="fixed inset-0 z-[80] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-6">

            <!-- MODAL HEADER -->
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Generate OPCR</h2>
                    <p class="text-sm text-slate-400">
                        Generate Office Performance Commitment derived from PMT-approved UWP (Stage 1).
                    </p>
                </div>
                <button data-close-modal class="text-slate-400 hover:text-white">✕</button>
            </div>

            <!-- MODAL BODY -->
            <form class="mt-6 space-y-4">

                <!-- APPROVED UWP SELECT -->
                <div>
                    <label class="block text-sm text-slate-300 mb-1">
                        Approved Unit Work Plan (UWP)
                    </label>
                    <select id="uwpSelect"
                            class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white">
                        <option disabled selected>Select approved UWP</option>
                        <option value="uwp-rcu-2026">
                            Revenue Collection Unit – January–June 2026 (PMT Approved)
                        </option>
                    </select>
                </div>

                <!-- AUTO-DERIVED OPCR PREVIEW -->
                <div id="derivedPreview"
                        class="hidden rounded-lg border border-slate-800 bg-slate-900/50 p-4">

                    <p class="text-xs text-slate-400 mb-1">
                        Derived Office Performance Commitments (from approved UWP)
                    </p>
                    <p class="text-[11px] text-slate-500 mt-2">
                        Targets shown are aggregated from approved UWP success indicators.
                    </p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-slate-300">
                            <thead class="text-slate-500 uppercase">
                                <tr>
                                    <th class="py-2 text-left">Output</th>
                                    <th class="py-2 text-left">Success Indicators</th>
                                    <th class="py-2 text-left">Timeline / Target</th>
                                    <th class="py-2 pr-4 text-left">Weight</th>
                                    <th class="py-2 pl-4 text-left">Function</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t border-slate-800">
                                    <td class="py-2">
                                        E-Bank Scanning and Encoding of Revenue Transactions
                                    </td>
                                    <td class="py-2">
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
                                    <td class="py-2">Daily; all e-bank transactions processed within the same working day</td>
                                    <td class="py-2 pr-4">50%</td>
                                    <td class="py-2 pl-4">
                                        <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">
                                            Core
                                        </span>
                                    </td>
                                </tr>

                                <tr class="border-t border-slate-800">
                                    <td class="py-2">
                                        Processing of Over-the-Counter Revenue Transactions
                                    </td>
                                    <td class="py-2">
                                        <button type="button"
                                                class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                                data-uwp-view-indicators
                                                data-title="Processing of Over-the-Counter Revenue Transactions"
                                                data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'>
                                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span>(3)</span>
                                        </button>
                                    </td>
                                    <td class="py-2">Daily; 95% processed within the same working day</td>
                                    <td class="py-2 pr-4">30%</td>
                                    <td class="py-2 pl-4">
                                        <span class="rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">
                                            Core
                                        </span>
                                    </td>
                                </tr>

                                <tr class="border-t border-slate-800">
                                    <td class="py-2">
                                        Maintenance of Revenue Records Filing System
                                    </td>
                                    <td class="py-2">
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
                                    <td class="py-2">Quarterly validation and update</td>
                                    <td class="py-2 pr-4">20%</td>
                                    <td class="py-2 pl-4">
                                        <span class="rounded-md bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-300 border border-blue-400/30">
                                            Support
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button"
                            data-close-modal
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                        Cancel
                    </button>

                    <button type="button"
                            data-submit-loading
                            data-loading-text="Generating OPCR..."
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                        <span data-button-label>Generate OPCR</span>
                        <span data-button-spinner
                                class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- VIEW OPCR MODAL -->
    <div id="view-opcr-modal"
        class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">

        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl space-y-5">

            <div class="space-y-3">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-semibold text-white">
                            Office Performance Commitment and Review
                        </h3>
                        <p class="text-sm text-slate-400">
                            Derived from PMT-approved Unit Work Plan (Stage 1)
                        </p>
                    </div>
                    <button data-close-modal class="text-slate-400 hover:text-white">×</button>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-300">
                    <div class="hidden bg-slate-800 px-3 py-1 rounded-full text-slate-400 lg:flex">
                        Office / Unit:
                        <span class="ml-1 font-semibold text-white">Revenue Collection Unit</span>
                    </div>
                    <div class="hidden bg-slate-800 px-3 py-1 rounded-full text-slate-400 lg:flex">
                        Period:
                        <span class="ml-1 font-semibold text-white">January – June 2026</span>
                    </div>
                    <div class="hidden bg-slate-800 px-3 py-1 rounded-full text-slate-400 lg:flex">
                        Source:
                        <span class="ml-1 font-semibold text-white">PMT Approved UWP</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-800 bg-slate-950/40 px-4 py-3 text-xs text-slate-300">
                <div class="flex flex-wrap gap-4 text-sm">
                    <span class="font-semibold text-white">Revenue Collection Unit</span>
                    <span>Period: <span class="font-semibold text-white">January – June 2026</span></span>
                    <span>Source: <span class="font-semibold text-white">PMT Approved UWP</span></span>
                </div>
                <span class="rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-200 border border-amber-500/30">
                    For Department Head Review
                </span>
            </div>

            <div class="max-h-[58vh] overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-inner">
                <div class="max-h-[58vh] overflow-y-auto">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="sticky top-0 z-10 bg-slate-900/90 border-b border-slate-800 text-xs uppercase text-slate-500">
                            <tr class="grid grid-cols-[2.3fr_1fr_1.25fr_0.7fr_0.7fr]">
                                <th class="px-4 py-3 text-left">Output</th>
                                <th class="px-4 py-3 text-left">Success Indicators</th>
                                <th class="px-4 py-3 text-left">Target Summary</th>
                                <th class="px-4 py-3 text-left">Weight</th>
                                <th class="px-4 py-3 text-left">Function</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                        <tr class="grid grid-cols-[2.3fr_1fr_1.25fr_0.7fr_0.7fr] hover:bg-slate-900/40 transition-colors">
                            <td class="px-4 py-4 font-medium text-slate-100">E-Bank Scanning and Encoding of Revenue Transactions</td>
                            <td class="px-4 py-4">
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
                            <td class="px-4 py-4">
                                Daily; all e-bank transactions processed within the same working day
                            </td>
                            <td class="px-4 py-4 text-sm">50%</td>
                            <td class="px-4 py-4">
                                <span class="rounded-md bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">
                                    Core
                                </span>
                            </td>
                        </tr>
                        <tr class="grid grid-cols-[2.3fr_1fr_1.25fr_0.7fr_0.7fr] hover:bg-slate-900/40 transition-colors">
                            <td class="px-4 py-4 font-medium text-slate-100">Processing of Over-the-Counter Revenue Transactions</td>
                            <td class="px-4 py-4">
                                <button type="button"
                                        class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200"
                                        data-uwp-view-indicators
                                        data-title="Processing of Over-the-Counter Revenue Transactions"
                                        data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'>
                                    <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>(3)</span>
                                </button>
                            </td>
                            <td class="px-4 py-4">
                                Daily; 95% processed within the same working day
                            </td>
                            <td class="px-4 py-4 text-sm">30%</td>
                            <td class="px-4 py-4">
                                <span class="rounded-md bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/20">
                                    Core
                                </span>
                            </td>
                        </tr>
                        <tr class="grid grid-cols-[2.3fr_1fr_1.25fr_0.7fr_0.7fr] hover:bg-slate-900/40 transition-colors">
                            <td class="px-4 py-4 font-medium text-slate-100">Maintenance of Revenue Records Filing System</td>
                            <td class="px-4 py-4">
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
                            <td class="px-4 py-4">
                                Quarterly validation and update
                            </td>
                            <td class="px-4 py-4 text-sm">20%</td>
                            <td class="px-4 py-4">
                                <span class="rounded-md bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-300 border border-blue-400/30">
                                    Support
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-800 pt-4 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-left text-slate-500">
                    Export becomes available after Department Head approval.
                </p>
                <div class="flex flex-wrap items-center gap-3 justify-end">
                    <button data-close-modal
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                        Close
                    </button>
                    <a href="{{ route('stage1.opcr.export.excel') }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                        <span>Export Excel</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- SUCCESS INDICATORS MODAL (MATCH IMAGE: TABLE + STANDARDS + ASSIGNED EMPLOYEE) -->
    <div id="uwp-indicators-modal" class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-950 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-6 py-4">
                <div>
                    <h3 id="uwp-indicators-title" class="text-xl font-semibold text-white">--</h3>
                    <p class="text-sm text-slate-400 mt-1">
                        Read-only list of indicators for this output. One employee is assigned per success indicator.
                    </p>
                </div>
                <button type="button" onclick="closeUwpIndicatorsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="px-6 py-5">
                <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-950/40">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-slate-900/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                            <tr>
                                <th class="px-5 py-4 text-left">Success Indicator</th>
                                <th class="px-5 py-4 text-center">Standards</th>
                                <th class="px-5 py-4 text-center">Assigned Employee</th>
                            </tr>
                        </thead>
                        <tbody id="uwp-indicators-table-body" class="divide-y divide-slate-800"></tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end px-6 pb-6">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-5 py-2.5 text-sm text-slate-200 hover:bg-slate-900/60"
                        onclick="closeUwpIndicatorsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- STANDARDS MODAL (READ-ONLY, PER SUCCESS INDICATOR) -->
    <div id="uwp-standards-modal" class="fixed inset-0 z-[95] hidden items-center justify-center bg-black/70 px-4 py-8">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                    <h3 id="uwp-standards-title" class="text-lg font-semibold text-white">Standards (Q/E/T)</h3>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Indicator: <span id="uwp-standards-indicator">--</span>
                    </p>
                </div>
                <button type="button" onclick="closeUwpStandardsModal()" class="text-slate-400 hover:text-white">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>

            <div class="mt-4 space-y-4 text-sm text-slate-200 max-h-[70vh] overflow-y-auto">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400 mb-1">Indicator</p>
                        <p id="uwp-standards-indicator-display" class="text-sm font-semibold text-slate-100">--</p>
                    </div>
                </div>

                <div id="uwp-standards-table" class="w-full"></div>
            </div>

            <div class="mt-5 flex justify-end border-t border-slate-800 pt-3">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closeUwpStandardsModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- ASSIGNED EMPLOYEE MODAL (READ-ONLY, PER SUCCESS INDICATOR) -->
    <div id="uwp-assignees-modal" class="fixed inset-0 z-[96] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-950 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employee</p>
                    <h3 class="text-lg font-semibold text-white">Employee assigned to the selected indicator</h3>
                    <p class="text-xs text-slate-400 mt-1">
                        Indicator: <span data-assignee-indicator class="font-semibold text-slate-100">--</span>
                    </p>
                </div>
                <button type="button" onclick="closeUwpAssigneesModal()" class="text-slate-400 hover:text-white">
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
                    <tbody data-assignee-body class="divide-y divide-slate-800">
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"
                        onclick="closeUwpAssigneesModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // DIRECT MODAL OPEN (View / Create)
    document.querySelectorAll('[data-direct="true"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.opensModal;
            const modal = document.getElementById(target);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }
        });
    });

    // CLOSE MODALS
    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.fixed')?.classList.add('hidden');
            btn.closest('.fixed')?.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        });
    });

    // SUBMIT BUTTON LOADING
    document.querySelectorAll('[data-submit-loading]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.dataset.loading === 'true') return;

            btn.dataset.loading = 'true';

            const label = btn.querySelector('[data-button-label]');
            const spinner = btn.querySelector('[data-button-spinner]');
            const originalText = label.textContent;
            const loadingText = btn.dataset.loadingText || 'Loading...';

            label.textContent = loadingText;
            spinner.classList.remove('hidden');
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-wait');

            // DEMO DELAY
            setTimeout(() => {
                label.textContent = originalText;
                spinner.classList.add('hidden');
                btn.disabled = false;
                btn.classList.remove('opacity-70', 'cursor-wait');
                btn.dataset.loading = 'false';

                // close modal after submit (demo behavior)
                btn.closest('.fixed')?.classList.add('hidden');
                btn.closest('.fixed')?.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }, 1200);
        });
    });

    // Show derived preview after selecting approved UWP
    document.getElementById('uwpSelect')?.addEventListener('change', () => {
        document.getElementById('derivedPreview')?.classList.remove('hidden');
    });

    /**
     * ==========================================================
     *  Seeded Dummy Standards Map (SAME SOURCE AS UWP)
     * ==========================================================
     */
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
        return {
            5: { q: [], e: [], t: [] },
            4: { q: [], e: [], t: [] },
            3: { q: [], e: [], t: [] },
            2: { q: [], e: [], t: [] },
            1: { q: [], e: [], t: [] },
        };
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

    /**
     * ==========================================================
     *  Demo Assigned Employees (per indicator)
     *  - One employee assigned per success indicator (Ramon Reyes)
     * ==========================================================
     */
    const DEMO_UNIT = 'Revenue Collection Unit';
    const DEMO_ASSIGNEE_MAP = {
        'All e-bank transactions scanned and encoded daily': ['Ramon Reyes'],
        'Indexing complete with no missing pages': ['Ramon Reyes'],
        'Audit trail maintained within 24 hours': ['Ramon Reyes'],
        'Same-day verification of OTC transactions': ['Ramon Reyes'],
        '95% encoded within the business day': ['Ramon Reyes'],
        'OR validation completed daily': ['Ramon Reyes'],
        'Weekly filing updated and retrievable': ['Ramon Reyes'],
        'Digital backups synced monthly': ['Ramon Reyes'],
        'Retrieval logs maintained for audits': ['Ramon Reyes'],
    };

    /**
     * ==========================================================
     *  Success Indicators Modal (TABLE like image) + Standards + Assignee
     * ==========================================================
     */
    const indicatorsModal = document.getElementById('uwp-indicators-modal');
    const indicatorsTitle = document.getElementById('uwp-indicators-title');
    const indicatorsTableBody = document.getElementById('uwp-indicators-table-body');

    // Standards modal elements
    const standardsModal = document.getElementById('uwp-standards-modal');
    const standardsTitle = document.getElementById('uwp-standards-title');
    const standardsIndicatorLabel = document.getElementById('uwp-standards-indicator');
    const standardsIndicatorDisplay = document.getElementById('uwp-standards-indicator-display');
    const standardsTableWrap = document.getElementById('uwp-standards-table');

    // Assignees modal
    const assigneesModal = document.getElementById('uwp-assignees-modal');
    const assigneeIndicatorEl = assigneesModal?.querySelector('[data-assignee-indicator]');
    const assigneeBodyEl = assigneesModal?.querySelector('[data-assignee-body]');

    // Current context (per MFO row)
    let activeMfoTitle = '--';

    function openUwpIndicatorsModal(title, indicators) {
        activeMfoTitle = title || '--';
        if (indicatorsTitle) indicatorsTitle.textContent = activeMfoTitle;

        if (indicatorsTableBody) {
            indicatorsTableBody.innerHTML = '';

            (Array.isArray(indicators) ? indicators : []).forEach((text) => {
                const value = (text || '').trim();
                if (!value) return;

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-900/40';

                // indicator cell
                const tdIndicator = document.createElement('td');
                tdIndicator.className = 'px-5 py-5 text-sm text-slate-100';
                tdIndicator.textContent = value;

                // standards cell
                const tdStandards = document.createElement('td');
                tdStandards.className = 'px-5 py-5 text-center';

                const standardsBtn = document.createElement('button');
                standardsBtn.type = 'button';
                standardsBtn.className = 'inline-flex items-center justify-center gap-2 text-sm text-slate-200 hover:text-white';
                standardsBtn.innerHTML = `
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span class="font-medium">View Standards</span>
                `;
                standardsBtn.addEventListener('click', () => {
                    openUwpStandardsModal(activeMfoTitle, value);
                });

                tdStandards.appendChild(standardsBtn);

                // assigned employee cell
                const tdAssignee = document.createElement('td');
                tdAssignee.className = 'px-5 py-5 text-center';

                const assigned = DEMO_ASSIGNEE_MAP[value] || [];
                const count = Array.isArray(assigned) ? assigned.length : 0;

                const assigneeBtn = document.createElement('button');
                assigneeBtn.type = 'button';
                assigneeBtn.className = 'inline-flex items-center justify-center gap-2 text-sm text-slate-200 hover:text-white';
                assigneeBtn.innerHTML = `
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span class="font-medium">View (${count})</span>
                `;
                assigneeBtn.addEventListener('click', () => {
                    openUwpAssigneesModal(value, assigned);
                });

                tdAssignee.appendChild(assigneeBtn);

                tr.append(tdIndicator, tdStandards, tdAssignee);
                indicatorsTableBody.appendChild(tr);
            });
        }

        if (indicatorsModal) {
            indicatorsModal.classList.remove('hidden');
            indicatorsModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    window.closeUwpIndicatorsModal = function () {
        if (!indicatorsModal) return;
        indicatorsModal.classList.add('hidden');
        indicatorsModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    /**
     * ==========================================================
     *  Standards Modal (Read-only)
     * ==========================================================
     */
    function renderStandardsTableFor(indicatorText) {
        if (!standardsTableWrap) return;

        const data = seedStandardsForIndicator(indicatorText || '');
        standardsTableWrap.innerHTML = '';

        const table = document.createElement('table');
        table.className = 'w-full text-sm border border-slate-800 rounded-lg overflow-hidden';

        table.innerHTML = `
            <thead class="bg-slate-900/70 text-slate-200">
                <tr>
                    <th class="px-3 py-2 text-left w-[70px]">Rating</th>
                    <th class="px-3 py-2 text-left">Quality (Q)</th>
                    <th class="px-3 py-2 text-left">Efficiency (E)</th>
                    <th class="px-3 py-2 text-left">Timeliness (T)</th>
                </tr>
            </thead>
        `;

        const tbody = document.createElement('tbody');
        tbody.className = 'divide-y divide-slate-800 text-slate-100 bg-slate-950/40';

        const cellList = (arr) => {
            if (!arr || !arr.length) return `<span class="text-slate-500">—</span>`;
            return arr.map((t) => `
                <div class="flex items-start gap-2">
                    <span class="text-slate-400">•</span>
                    <span>${String(t)}</span>
                </div>
            `).join('');
        };

        [5,4,3,2,1].forEach((lvl) => {
            const row = data[lvl] || { q:[], e:[], t:[] };
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-900/40';
            tr.innerHTML = `
                <td class="px-3 py-2 font-semibold">${lvl}</td>
                <td class="px-3 py-2 align-top">${cellList(row.q)}</td>
                <td class="px-3 py-2 align-top">${cellList(row.e)}</td>
                <td class="px-3 py-2 align-top">${cellList(row.t)}</td>
            `;
            tbody.appendChild(tr);
        });

        table.appendChild(tbody);
        standardsTableWrap.appendChild(table);
    }

    function openUwpStandardsModal(mfoTitle, indicatorText) {
        activeMfoTitle = mfoTitle || '--';
        const indicator = (indicatorText || '').trim() || '--';

        if (standardsTitle) standardsTitle.textContent = `Standards (Q/E/T) — ${activeMfoTitle}`;
        if (standardsIndicatorLabel) standardsIndicatorLabel.textContent = indicator;
        if (standardsIndicatorDisplay) standardsIndicatorDisplay.textContent = indicator;

        renderStandardsTableFor(indicator);

        if (standardsModal) {
            standardsModal.classList.remove('hidden');
            standardsModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    window.closeUwpStandardsModal = function () {
        if (!standardsModal) return;
        standardsModal.classList.add('hidden');
        standardsModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    /**
     * ==========================================================
     *  Assignee Modal (Read-only, per indicator)
     * ==========================================================
     */
    function openUwpAssigneesModal(indicatorText, employees) {
        const indicator = (indicatorText || '').trim() || '--';
        const list = Array.isArray(employees) ? employees : [];

        assigneeIndicatorEl && (assigneeIndicatorEl.textContent = indicator);

        if (assigneeBodyEl) {
            assigneeBodyEl.innerHTML = '';

            if (!list.length) {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-900/40';
                tr.innerHTML = `<td class="px-4 py-3 text-slate-300" colspan="3">No assigned employees.</td>`;
                assigneeBodyEl.appendChild(tr);
            } else {
                list.forEach((name) => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-900/40';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${name}</td>
                        <td class="px-4 py-3">${DEMO_UNIT}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                                Assigned
                            </span>
                        </td>
                    `;
                    assigneeBodyEl.appendChild(tr);
                });
            }
        }

        if (assigneesModal) {
            assigneesModal.classList.remove('hidden');
            assigneesModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    window.closeUwpAssigneesModal = function () {
        if (!assigneesModal) return;
        assigneesModal.classList.add('hidden');
        assigneesModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    /**
     * ==========================================================
     *  Wire up buttons (Indicators)
     * ==========================================================
     */
    document.querySelectorAll('[data-uwp-view-indicators]').forEach((btn) => {
        btn.addEventListener('click', () => {
            let indicators = [];
            try { indicators = JSON.parse(btn.dataset.indicators || '[]'); } catch (e) { indicators = []; }
            openUwpIndicatorsModal(btn.dataset.title || '--', indicators);
        });
    });

    // click outside to close (standards)
    standardsModal?.addEventListener('click', (e) => {
        if (e.target === standardsModal) closeUwpStandardsModal();
    });

    // click outside to close (indicators)
    indicatorsModal?.addEventListener('click', (e) => {
        if (e.target === indicatorsModal) closeUwpIndicatorsModal();
    });

    // click outside to close (assignees)
    assigneesModal?.addEventListener('click', (e) => {
        if (e.target === assigneesModal) closeUwpAssigneesModal();
    });

    // Escape behavior: assignees -> standards -> indicators
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;

        if (assigneesModal && !assigneesModal.classList.contains('hidden')) {
            closeUwpAssigneesModal();
            return;
        }

        if (standardsModal && !standardsModal.classList.contains('hidden')) {
            closeUwpStandardsModal();
            return;
        }

        if (indicatorsModal && !indicatorsModal.classList.contains('hidden')) {
            closeUwpIndicatorsModal();
        }
    });
});
</script>
@endpush
@endsection
