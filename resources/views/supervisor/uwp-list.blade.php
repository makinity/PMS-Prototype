<x-layouts.supervisor>
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
                                    <button
                                        data-modal-target="mfo1IndicatorsModal"
                                        data-modal-toggle="mfo1IndicatorsModal"
                                        class="inline-flex items-center gap-2 text-slate-300 hover:text-white">
                                        <i class="fa-regular fa-eye text-sm"></i><span>(3)</span>
                                    </button>
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
                                    <button
                                        data-modal-target="mfo2IndicatorsModal"
                                        data-modal-toggle="mfo2IndicatorsModal"
                                        class="inline-flex items-center gap-2 text-slate-300 hover:text-white">
                                        <i class="fa-regular fa-eye text-sm"></i><span>(3)</span>
                                    </button>
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
                                    <button
                                        data-modal-target="mfo3IndicatorsModal"
                                        data-modal-toggle="mfo3IndicatorsModal"
                                        class="inline-flex items-center gap-2 text-slate-300 hover:text-white">
                                        <i class="fa-regular fa-eye text-sm"></i><span>(3)</span>
                                    </button>
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
            <div class="flex justify-end border-t border-slate-800 px-8 py-5">
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
                    <li>All e-bank transactions scanned and encoded daily</li>
                    <li>Indexing complete with no missing pages</li>
                    <li>Audit trail maintained within 24 hours</li>
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
                    <li>Same-day verification of OTC transactions</li>
                    <li>95% encoded within the business day</li>
                    <li>OR validation completed daily</li>
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
                    <li>Weekly filing updated and retrievable</li>
                    <li>Digital backups synced monthly</li>
                    <li>Retrieval logs maintained for audits</li>
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const statusText = document.getElementById('uwp-status-text');
                document.querySelectorAll('[data-modal-target="uwpPreviewModal"]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const status = btn.dataset.status || 'Draft';
                        if (statusText) statusText.textContent = status;
                    });
                });
            });
        </script>
    @endpush
</x-layouts.supervisor>
