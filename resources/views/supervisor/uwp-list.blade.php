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

    <!-- ===================== -->
    <!-- UWP PREVIEW MODAL -->
    <!-- ===================== -->
    <div id="uwpPreviewModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 hidden overflow-y-auto overflow-x-hidden">
        <div class="relative mx-auto my-8 w-full max-w-3xl px-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100 shadow-xl">

                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-800 px-6 py-4">
                    <h3 class="text-lg font-semibold">
                        Unit Work Plan – Preview
                    </h3>
                    <button type="button"
                            data-modal-hide="uwpPreviewModal"
                            class="rounded-lg p-2 text-slate-400 hover:bg-slate-800 hover:text-slate-200">
                        <span class="sr-only">Close</span>
                        &times;
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="space-y-6 px-6 py-5">

                    <!-- Header Summary -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-slate-400">Unit</p>
                            <p class="mt-1 font-medium">Revenue Collection Unit</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-widest text-slate-400">Performance Period</p>
                            <p class="mt-1 font-medium">January–June 2026</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-widest text-slate-400">Status</p>
                            <span class="mt-1 inline-flex rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-400">
                                PMT Approved
                            </span>
                        </div>
                    </div>

                    <!-- Core Functions -->
                    <div>
                        <h4 class="mb-3 text-sm font-semibold uppercase tracking-widest text-slate-400">
                            Core Functions
                        </h4>

                        <div class="space-y-4">
                            <!-- CORE MFO 1 -->
                            <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-4">
                                <p class="font-semibold">
                                    E-Bank Scanning and Encoding of Revenue Transactions
                                </p>
                                <p class="mt-1 text-sm text-slate-400">
                                    Target / Timeline: Daily; same working day
                                </p>
                                <button type="button"
                                        data-modal-target="mfo1IndicatorsModal"
                                        data-modal-toggle="mfo1IndicatorsModal"
                                        class="mt-3 inline-flex text-sm font-medium text-slate-300 hover:text-white underline underline-offset-4">
                                    View Success Indicators
                                </button>
                            </div>

                            <!-- CORE MFO 2 -->
                            <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-4">
                                <p class="font-semibold">
                                    Processing of Over-the-Counter Revenue Transactions
                                </p>
                                <p class="mt-1 text-sm text-slate-400">
                                    Target / Timeline: Daily; 95% processed within same working day
                                </p>
                                <button type="button"
                                        data-modal-target="mfo2IndicatorsModal"
                                        data-modal-toggle="mfo2IndicatorsModal"
                                        class="mt-3 inline-flex text-sm font-medium text-slate-300 hover:text-white underline underline-offset-4">
                                    View Success Indicators
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Support Functions -->
                    <div>
                        <h4 class="mb-3 text-sm font-semibold uppercase tracking-widest text-slate-400">
                            Support Functions
                        </h4>

                        <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-4">
                            <p class="font-semibold">
                                Maintenance of Revenue Records Filing System
                            </p>
                            <p class="mt-1 text-sm text-slate-400">
                                Target / Timeline: Quarterly validation and update
                            </p>
                            <button type="button"
                                    data-modal-target="mfo3IndicatorsModal"
                                    data-modal-toggle="mfo3IndicatorsModal"
                                    class="mt-3 inline-flex text-sm font-medium text-slate-300 hover:text-white underline underline-offset-4">
                                View Success Indicators
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end border-t border-slate-800 px-6 py-4">
                    <button type="button"
                            data-modal-hide="uwpPreviewModal"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm font-medium text-slate-200 hover:bg-slate-800">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ===================== -->
    <!-- SUCCESS INDICATOR SUB-MODALS -->
    <!-- ===================== -->

    <!-- MFO 1 Indicators -->
    <div id="mfo1IndicatorsModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="relative mx-auto my-10 w-full max-w-md px-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 px-5 py-4">
                    <h3 class="text-sm font-semibold">Success Indicators</h3>
                    <button type="button" data-modal-hide="mfo1IndicatorsModal"
                            class="rounded-lg p-2 text-slate-400 hover:bg-slate-800">&times;</button>
                </div>
                <div class="px-5 py-4">
                    <ul class="list-disc space-y-2 pl-5 text-sm text-slate-300">
                        <li>All e-bank transactions scanned and encoded daily</li>
                        <li>Indexing complete with no missing pages</li>
                        <li>Audit trail maintained within 24 hours</li>
                    </ul>
                </div>
                <div class="flex justify-end border-t border-slate-800 px-5 py-3">
                    <button type="button" data-modal-hide="mfo1IndicatorsModal"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MFO 2 Indicators -->
    <div id="mfo2IndicatorsModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="relative mx-auto my-10 w-full max-w-md px-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 px-5 py-4">
                    <h3 class="text-sm font-semibold">Success Indicators</h3>
                    <button type="button" data-modal-hide="mfo2IndicatorsModal"
                            class="rounded-lg p-2 text-slate-400 hover:bg-slate-800">&times;</button>
                </div>
                <div class="px-5 py-4">
                    <ul class="list-disc space-y-2 pl-5 text-sm text-slate-300">
                        <li>Same-day verification of OTC transactions</li>
                        <li>95% encoded within the business day</li>
                        <li>OR validation completed daily</li>
                    </ul>
                </div>
                <div class="flex justify-end border-t border-slate-800 px-5 py-3">
                    <button type="button" data-modal-hide="mfo2IndicatorsModal"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MFO 3 Indicators -->
    <div id="mfo3IndicatorsModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="relative mx-auto my-10 w-full max-w-md px-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 px-5 py-4">
                    <h3 class="text-sm font-semibold">Success Indicators</h3>
                    <button type="button" data-modal-hide="mfo3IndicatorsModal"
                            class="rounded-lg p-2 text-slate-400 hover:bg-slate-800">&times;</button>
                </div>
                <div class="px-5 py-4">
                    <ul class="list-disc space-y-2 pl-5 text-sm text-slate-300">
                        <li>Weekly filing updated and retrievable</li>
                        <li>Digital backups synced monthly</li>
                        <li>Retrieval logs maintained for audits</li>
                    </ul>
                </div>
                <div class="flex justify-end border-t border-slate-800 px-5 py-3">
                    <button type="button" data-modal-hide="mfo3IndicatorsModal"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm">
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
