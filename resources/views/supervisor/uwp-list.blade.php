@extends('layouts.supervisor')

@section('main-content')
    <section class="space-y-6">

        <!-- Header -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Stage I – Unit Work Plan (UWP)
                </p>
                <h1 class="text-2xl font-semibold text-white">Performance Period Planning and Commitment</h1>
            </div>
            <a href="{{ route('supervisor.uwp') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                + Create UWP
            </a>
        </div>

        <!-- UWP List -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-4">

            <!-- Filter / Context Bar -->
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="space-y-1">
                    <span class="block text-xs uppercase tracking-widest text-slate-400">
                        Office / Unit
                    </span>

                    <select
                        class="w-[280px] rounded-lg border border-slate-800 bg-slate-950 px-3 py-2
                            text-sm text-slate-100 focus:border-blue-500
                            focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                        style="background:#0f172a;color:#e5e7eb;"
                    >
                        <option selected>Revenue Collection Unit</option>
                        <option>Records Management Unit</option>
                        <option>Administrative Services Unit</option>
                        <option>Human Resource Management Unit</option>
                        <option>General Services Unit</option>
                        <option>Planning and Development Unit</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-800">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/60">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Unit</th>
                            <th class="px-4 py-3 text-left font-semibold">Performance Period</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-800">
                        <tr class="hover:bg-slate-900/50 transition">
                            <td class="px-4 py-3">
                                Revenue Collection Unit
                            </td>
                            <td class="px-4 py-3">
                                January–June 2026
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center rounded-full
                                        border border-emerald-500/30
                                        bg-emerald-500/10
                                        px-3 py-1 text-xs font-semibold text-emerald-300">
                                    PMT Approved
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                        aria-label="View Unit Work Plan"
                                        title="View Unit Work Plan"
                                        data-modal-target="uwpPreviewModal"
                                        data-modal-toggle="uwpPreviewModal"
                                        class="inline-flex items-center justify-center rounded-lg
                                            p-2 text-slate-400 hover:text-white
                                            hover:bg-slate-800 transition">
                                    <i class="fa-regular fa-eye text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <div id="uwpPreviewModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm">

        <div class="mx-auto my-10 w-full max-w-5xl px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">

            <!-- HEADER -->
            <div class="border-b border-slate-800 px-8 py-6">
                <h2 class="text-xl font-semibold">Unit Work Plan</h2>
                <p class="mt-1 text-sm text-slate-400">
                Revenue Collection Unit • Jan – June 2026
                </p>
            </div>

            <!-- SUMMARY -->
            <div class="flex items-stretch gap-10 border-b border-slate-800 px-8 py-6">

                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Office / Unit</p>
                    <p class="mt-1 font-medium">Revenue Collection Unit</p>
                </div>

                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Supervisor</p>
                    <p class="mt-1 font-medium">Carlo D. Beray</p>
                </div>

                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Department Head</p>
                    <p class="mt-1 font-medium">Dept-head</p>
                </div>

                <div class="w-1/4">
                    <p class="text-xs uppercase tracking-widest text-slate-500">Status</p>
                    <span class="mt-2 inline-flex rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-400">
                        PMT Approved
                    </span>
                </div>

            </div>


            <!-- PLANNED OUTPUTS -->
            <div class="px-8 py-6">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                    Planned Outputs
                </h3>

                <div class="overflow-hidden rounded-xl border border-slate-800">
                    <table class="w-full border-collapse text-left text-sm text-slate-200">

                        <!-- TABLE HEADER -->
                        <thead class="bg-slate-900/60 text-xs uppercase tracking-widest text-slate-400">
                        <tr>
                            <th class="px-5 py-4">PPA / MFO</th>
                            <th class="px-5 py-4 text-center">Success Indicators</th>
                            <th class="px-5 py-4 text-center">Standards (Q/E/T)</th>
                            <th class="px-5 py-4 text-center">Assigned Employees</th>
                            <th class="px-5 py-4">Target / Timeline</th>
                            <th class="px-5 py-4 text-center">Function</th>
                        </tr>
                    </thead>

                        <!-- TABLE BODY -->
                        <tbody class="divide-y divide-slate-800 bg-slate-950">

                            <!-- ROW 1 -->
                            <tr>
                                <td class="px-5 py-5 font-medium">
                                    E-Bank Scanning and Encoding of Revenue Transactions
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <div class="flex justify-center">
                                        <button
                                            data-modal-target="mfo1IndicatorsModal"
                                            data-modal-toggle="mfo1IndicatorsModal"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                            <span>(3)</span>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <div class="flex justify-center">
                                        <button
                                            data-modal-target="indicatorStandardsModal"
                                            data-modal-toggle="indicatorStandardsModal"
                                            data-indicator-standards
                                            data-mfo-title="E-Bank Scanning and Encoding of Revenue Transactions"
                                            data-indicator-text="All e-bank transactions scanned and encoded daily"
                                            data-indicators='["All e-bank transactions scanned and encoded daily","Indexing complete with no missing pages","Audit trail maintained within 24 hours"]'
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                            <span>View</span>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <div class="flex justify-center">
                                        <button
                                            data-modal-target="assignedEmployeesModal"
                                            data-modal-toggle="assignedEmployeesModal"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                            <span>View</span>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-5 text-slate-300">
                                    Daily; all e-bank transactions processed within the same working day
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <span class="rounded-full border border-emerald-500/40 px-3 py-1 text-xs font-semibold text-emerald-400">
                                        Core
                                    </span>
                                </td>
                            </tr>

                            <!-- ROW 2 -->
                            <tr>
                                <td class="px-5 py-5 font-medium">
                                    Processing of over-the-counter revenue transactions
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <div class="flex justify-center">
                                        <button
                                            data-modal-target="mfo2IndicatorsModal"
                                            data-modal-toggle="mfo2IndicatorsModal"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                            <span>(3)</span>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <div class="flex justify-center">
                                        <button
                                            data-modal-target="indicatorStandardsModal"
                                            data-modal-toggle="indicatorStandardsModal"
                                            data-indicator-standards
                                            data-mfo-title="Processing of over-the-counter revenue transactions"
                                            data-indicator-text="Same-day verification of OTC transactions"
                                            data-indicators='["Same-day verification of OTC transactions","95% encoded within the business day","OR validation completed daily"]'
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                            <span>View</span>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <div class="flex justify-center">
                                        <button
                                            data-modal-target="assignedEmployeesModal"
                                            data-modal-toggle="assignedEmployeesModal"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                            <span>View</span>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-5 text-slate-300">
                                    Daily; 95% processed within the same working day
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <span class="rounded-full border border-sky-500/40 px-3 py-1 text-xs font-semibold text-sky-400">
                                        Core
                                    </span>
                                </td>
                            </tr>

                            <!-- ROW 3 -->
                            <tr>
                                <td class="px-5 py-5 font-medium">
                                    Maintenance of Revenue Records Filing System
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <div class="flex justify-center">
                                        <button
                                            data-modal-target="mfo3IndicatorsModal"
                                            data-modal-toggle="mfo3IndicatorsModal"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                            <span>(3)</span>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <div class="flex justify-center">
                                        <button
                                            data-modal-target="indicatorStandardsModal"
                                            data-modal-toggle="indicatorStandardsModal"
                                            data-indicator-standards
                                            data-mfo-title="Maintenance of Revenue Records Filing System"
                                            data-indicator-text="Weekly filing updated and retrievable"
                                            data-indicators='["Weekly filing updated and retrievable","Digital backups synced monthly","Retrieval logs maintained for audits"]'
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                            <span>View</span>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <div class="flex justify-center">
                                        <button
                                            data-modal-target="assignedEmployeesModal"
                                            data-modal-toggle="assignedEmployeesModal"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                            <span>View</span>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-5 text-slate-300">
                                    Quarterly validation and update
                                </td>

                                <td class="px-5 py-5 text-center">
                                    <span class="rounded-full border border-indigo-500/40 px-3 py-1 text-xs font-semibold text-indigo-400">
                                        Support
                                    </span>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="flex gap-4 justify-end border-t border-slate-800 px-8 py-5">
                <a href="{{ route('stage1.uwp.export.excel') }}"
                    class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                    Export Excel
                </a>
                <button data-modal-hide="uwpPreviewModal"
                        class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                Close
                </button>
            </div>

            </div>
        </div>
    </div>

    <div id="mfo1IndicatorsModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm">

        <div class="mx-auto my-16 w-full max-w-lg px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">

            <!-- HEADER -->
            <div class="border-b border-slate-800 px-6 py-5">
                <h3 class="text-lg font-semibold">
                E-Bank Scanning and Encoding of Revenue Transactions
                </h3>
                <p class="mt-1 text-sm text-slate-400">
                Read-only list of indicators for this output.
                </p>
            </div>

            <!-- BODY -->
            <div class="px-6 py-6">
                    <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-5">
                <ol class="list-decimal space-y-3 pl-5 text-sm text-slate-300">
                    <li class="flex items-start gap-3">
                        <span class="text-slate-100">All e-bank transactions scanned and encoded daily</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-slate-100">Indexing complete with no missing pages</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-slate-100">Audit trail maintained within 24 hours</span>
                    </li>
                </ol>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="flex justify-end border-t border-slate-800 px-6 py-4">
                <button data-modal-hide="mfo1IndicatorsModal"
                        class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                Close
                </button>
            </div>

            </div>
        </div>
    </div>

    <div id="mfo2IndicatorsModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm">

        <div class="mx-auto my-16 w-full max-w-lg px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">

            <!-- HEADER -->
            <div class="border-b border-slate-800 px-6 py-5">
                <h3 class="text-lg font-semibold">
                Processing of over-the-counter revenue transactions
                </h3>
                <p class="mt-1 text-sm text-slate-400">
                Read-only list of indicators for this output.
                </p>
            </div>

            <!-- BODY -->
            <div class="px-6 py-6">
                <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-5">
                <ol class="list-decimal space-y-3 pl-5 text-sm text-slate-300">
                    <li class="flex items-start gap-3">
                        <span class="text-slate-100">Same-day verification of OTC transactions</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-slate-100">95% encoded within the business day</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-slate-100">OR validation completed daily</span>
                    </li>
                </ol>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="flex justify-end border-t border-slate-800 px-6 py-4">
                <button data-modal-hide="mfo2IndicatorsModal"
                        class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                Close
                </button>
            </div>

            </div>
        </div>
    </div>

    <div id="mfo3IndicatorsModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm">

        <div class="mx-auto my-16 w-full max-w-lg px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">

            <!-- HEADER -->
            <div class="border-b border-slate-800 px-6 py-5">
                <h3 class="text-lg font-semibold">
                Maintenance of Revenue Records Filing System
                </h3>
                <p class="mt-1 text-sm text-slate-400">
                Read-only list of indicators for this output.
                </p>
            </div>

            <!-- BODY -->
            <div class="px-6 py-6">
                <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-5">
                <ol class="list-decimal space-y-3 pl-5 text-sm text-slate-300">
                    <li class="flex items-start gap-3">
                        <span class="text-slate-100">Weekly filing updated and retrievable</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-slate-100">Digital backups synced monthly</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-slate-100">Retrieval logs maintained for audits</span>
                    </li>
                </ol>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="flex justify-end border-t border-slate-800 px-6 py-4">
                <button data-modal-hide="mfo3IndicatorsModal"
                        class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                Close
                </button>
            </div>

            </div>
        </div>
    </div>

    <div id="assignedEmployeesModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm">
        <div class="mx-auto my-16 w-full max-w-2xl px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">
                <div class="border-b border-slate-800 px-6 py-5">
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Assigned Employees</p>
                    <h3 class="text-lg font-semibold">Employees under the selected Office/Unit</h3>
                    <p class="mt-1 text-sm text-slate-400">
                        Office / Unit: <span class="font-semibold text-slate-100">Revenue Collection Unit</span>
                    </p>
                </div>
                <div class="px-6 py-6">
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
                <div class="flex justify-end border-t border-slate-800 px-6 py-4">
                    <button data-modal-hide="assignedEmployeesModal"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="indicatorStandardsModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm">
        <div class="mx-auto my-16 w-full max-w-3xl px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">
                <div class="border-b border-slate-800 px-6 py-5">
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                    <h3 class="text-lg font-semibold">Standards (Q/E/T)</h3>
                    <p class="mt-1 text-sm text-slate-400">
                        MFO: <span id="indicatorStandardsModalMfo" class="font-semibold text-slate-100">--</span>
                    </p>
                    <p class="text-sm text-slate-400">
                        Indicator: <span id="indicatorStandardsModalIndicator" class="font-semibold text-slate-100">--</span>
                    </p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <label class="flex flex-col text-sm text-slate-300">
                        <span class="text-xs uppercase tracking-[0.2em] text-slate-500">Select Indicator</span>
                        <select id="indicatorStandardsSelect"
                                style="background:#0f172a;color:#e5e7eb;"
                                class="mt-2 rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                                aria-label="Select indicator">
                        </select>
                    </label>
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
                            <tbody id="indicatorStandardsBody" class="divide-y divide-slate-800">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-end border-t border-slate-800 px-6 py-4">
                    <button data-modal-hide="indicatorStandardsModal"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const standardRatings = [5, 4, 3, 2, 1];
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
            const indicatorsByMfo = {
                'E-Bank Scanning and Encoding of Revenue Transactions': [
                    'All e-bank transactions scanned and encoded daily',
                    'Indexing complete with no missing pages',
                    'Audit trail maintained within 24 hours',
                ],
                'Processing of over-the-counter revenue transactions': [
                    'Same-day verification of OTC transactions',
                    '95% encoded within the business day',
                    'OR validation completed daily',
                ],
                'Maintenance of Revenue Records Filing System': [
                    'Weekly filing updated and retrievable',
                    'Digital backups synced monthly',
                    'Retrieval logs maintained for audits',
                ],
            };
            let indicatorStandardsBody;
            let standardsSelect;
            let standardsModalMfo;
            let standardsModalIndicator;
            const currentStandards = { mfo: '', indicator: '', indicators: [] };

            function createEmptyStandards() {
                const base = {};
                standardRatings.forEach((level) => {
                    base[level] = { q: [], e: [], t: [] };
                });
                return base;
            }

            function seedStandardsForIndicator(text) {
                const seed = standardsSeedMap[text];
                if (!seed) {
                    return createEmptyStandards();
                }
                const base = createEmptyStandards();
                standardRatings.forEach((level) => {
                    if (seed[level]) {
                        base[level] = {
                            q: Array.isArray(seed[level].q) ? [...seed[level].q] : [seed[level].q],
                            e: Array.isArray(seed[level].e) ? [...seed[level].e] : [seed[level].e],
                            t: Array.isArray(seed[level].t) ? [...seed[level].t] : [seed[level].t],
                        };
                    }
                });
                return base;
            }

            function renderIndicatorStandards(indicator) {
                if (!indicatorStandardsBody) {
                    return;
                }
                const data = seedStandardsForIndicator(indicator);
                indicatorStandardsBody.innerHTML = '';
                standardRatings.forEach((level) => {
                    const row = data[level] || { q: [], e: [], t: [] };
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-900/40';
                    const ratingTd = document.createElement('td');
                    ratingTd.className = 'px-4 py-3 font-semibold';
                    ratingTd.textContent = level;
                    const makeListCell = (items) => {
                        const td = document.createElement('td');
                        td.className = 'px-4 py-3 align-top';
                        if (!items || items.length === 0) {
                            td.textContent = '—';
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
                    tr.append(
                        ratingTd,
                        makeListCell(row.q),
                        makeListCell(row.e),
                        makeListCell(row.t)
                    );
                    indicatorStandardsBody.appendChild(tr);
                });
            }

            function populateStandardsSelect(indicators, preferred) {
                if (!standardsSelect) {
                    return;
                }
                standardsSelect.innerHTML = '';
                indicators.forEach((text) => {
                    const option = document.createElement('option');
                    option.value = text;
                    option.textContent = text;
                    standardsSelect.appendChild(option);
                });
                if (preferred && indicators.includes(preferred)) {
                    standardsSelect.value = preferred;
                } else if (indicators.length) {
                    standardsSelect.value = indicators[0];
                } else {
                    standardsSelect.value = '';
                }
                currentStandards.indicator = standardsSelect.value;
                if (standardsModalIndicator) {
                    standardsModalIndicator.textContent = currentStandards.indicator || '--';
                }
            }

            function handleStandardsButton(button) {
                const mfoTitle = button.dataset.mfoTitle || '';
                let indicators = [];
                try {
                    indicators = JSON.parse(button.dataset.indicators || '[]');
                } catch (error) {
                    indicators = [];
                }
                if (!indicators.length && mfoTitle) {
                    indicators = indicatorsByMfo[mfoTitle] || [];
                }
                currentStandards.mfo = mfoTitle;
                currentStandards.indicators = indicators;
                if (standardsModalMfo) {
                    standardsModalMfo.textContent = mfoTitle || '--';
                }
                populateStandardsSelect(indicators, indicators[0]);
                renderIndicatorStandards(currentStandards.indicator);
            }

            document.addEventListener('DOMContentLoaded', () => {
                const statusText = document.getElementById('uwp-status-text');
                document.querySelectorAll('[data-modal-target="uwpPreviewModal"]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const status = btn.dataset.status || 'Draft';
                        if (statusText) statusText.textContent = status;
                    });
                });
                indicatorStandardsBody = document.getElementById('indicatorStandardsBody');
                standardsSelect = document.getElementById('indicatorStandardsSelect');
                standardsModalMfo = document.getElementById('indicatorStandardsModalMfo');
                standardsModalIndicator = document.getElementById('indicatorStandardsModalIndicator');

                document.querySelectorAll('[data-indicator-standards]').forEach((button) => {
                    button.addEventListener('click', () => {
                        handleStandardsButton(button);
                    });
                });

                standardsSelect?.addEventListener('change', () => {
                    currentStandards.indicator = standardsSelect.value;
                    if (standardsModalIndicator) {
                        standardsModalIndicator.textContent = currentStandards.indicator || '--';
                    }
                    renderIndicatorStandards(currentStandards.indicator);
                });
            });
        </script>
    @endpush
@endsection
