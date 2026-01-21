<x-layouts.pmt>
    <section class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Stage III – Final Calibration of Ratings
                </p>
                <h1 class="text-2xl font-semibold text-white">Organization-Wide Final Calibration</h1>
                <p class="text-sm text-slate-400">
                    Organization-wide validation of final IPCR and OPCR ratings prior to closure.
                </p>
            </div>
            <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-200">
                Pending Final Calibration
            </span>
        </div>

        <!-- RATING DISTRIBUTION PLACEHOLDER -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Final Rating Distribution</h2>
                <span class="text-xs text-slate-400">UI placeholder – chart to be implemented</span>
            </div>
            <div class="h-48 rounded-xl border border-slate-800 bg-slate-950/60 flex items-center justify-center text-slate-500 text-sm">
                Chart placeholder (Rating bands 1.00 – 5.00 vs Count)
            </div>
        </div>

        <!-- SUMMARY METRICS -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="grid grid-cols-1 gap-3 text-sm text-slate-200 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Total Employees Rated</p>
                    <p class="text-xl font-semibold text-white">1</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Mean Rating</p>
                    <p class="text-xl font-semibold text-white">5.00</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">% Outstanding (5.00)</p>
                    <p class="text-xl font-semibold text-white">100%</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Spread Indicator</p>
                    <p class="text-xl font-semibold text-white">Within Norms</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Offices Flagged</p>
                    <p class="text-xl font-semibold text-white">0</p>
                </div>
            </div>
        </div>

        <!-- OFFICE-LEVEL FINAL CALIBRATION LIST -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Office-Level Final Calibration</h2>
                <span class="text-xs text-slate-400">Read-only; drill-down only</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Avg Final Rating</th>
                            <th class="px-4 py-3 text-left">% at 5.00</th>
                            <th class="px-4 py-3 text-left">Spread Status</th>
                            <th class="px-4 py-3 text-left">Calibration Status</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 text-white">Revenue Collection Unit</td>
                            <td class="px-4 py-3">5.00</td>
                            <td class="px-4 py-3">100%</td>
                            <td class="px-4 py-3 text-slate-300">Within Norms</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/10 text-emerald-200 border border-emerald-500/30 px-3 py-1 text-xs font-semibold">
                                    Ready
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('pmt.final-calibration-office') }}"
                                   class="inline-flex items-center gap-2 rounded-lg border border-blue-500 text-blue-400 px-3 py-2 text-xs font-semibold hover:bg-blue-500/10 transition">
                                    Review Office
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FINALIZATION GATE -->
        <div class="flex flex-col items-end gap-2">
            <button type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/40 transition hover:bg-emerald-500">
                Finalize Ratings
            </button>
            <p class="text-[11px] text-slate-500">
                Finalizing ratings will permanently lock all results. No further edits will be allowed.
            </p>
        </div>

    </section>
</x-layouts.pmt>
