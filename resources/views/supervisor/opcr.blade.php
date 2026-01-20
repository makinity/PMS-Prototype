<x-layouts.supervisor>
    <section class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Office Performance Commitment and Review (OPCR)</h1>
                <p class="text-sm text-slate-400">Unit Performance Summary (View Only)</p>
            </div>
            <span class="rounded-full border border-emerald-600/60 bg-emerald-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-emerald-200">APPROVED</span>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="space-y-1">
                <p class="text-sm font-semibold text-white">Period Comparison</p>
                <p class="text-xs text-slate-400">Switch periods to view read-only OPCR trends.</p>
            </div>
            <select class="min-w-[220px] rounded-xl border border-slate-700 bg-slate-950/70 px-4 py-2 text-sm text-slate-100 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                <option>January - June 2025</option>
                <option>July - December 2024</option>
                <option>January - June 2024</option>
            </select>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Office / Unit</p>
                <p class="mt-2 text-lg font-semibold text-white">Revenue Collection Unit</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Rating Period</p>
                <p class="mt-2 text-lg font-semibold text-white">January - June 2025</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Total IPCRs Included</p>
                <p class="mt-2 text-lg font-semibold text-white">18</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Overall Office Rating</p>
                <p class="mt-2 text-lg font-semibold text-emerald-200">4.32 (Very Satisfactory)</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-white">Period Comparison Summary</h2>
                <p class="text-xs text-slate-500">Display-only analytics for supervisors.</p>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Overall OPCR Rating</p>
                    <div class="mt-2 flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-white">4.32</p>
                        <span class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-200">^ 0.03</span>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Core Avg (80%)</p>
                    <div class="mt-2 flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-white">4.35</p>
                        <span class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-200">^ 0.05</span>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Support Avg (20%)</p>
                    <div class="mt-2 flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-white">4.12</p>
                        <span class="rounded-full border border-amber-500/40 bg-amber-500/10 px-2 py-0.5 text-[11px] font-semibold text-amber-200">-> 0.00</span>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">IPCRs Counted</p>
                    <div class="mt-2 flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-white">18</p>
                        <span class="rounded-full border border-blue-500/40 bg-blue-500/10 px-2 py-0.5 text-[11px] font-semibold text-blue-200">v 1</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-white">OPCR Trends (Last 4 Periods)</h2>
                <p class="text-xs text-slate-500">Trends are informational and do not affect OPCR ratings.</p>
            </div>
            <div class="space-y-3">
                <div class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3">
                    <div class="h-2 w-10 rounded-full bg-emerald-500/30"></div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-white">January - June 2025</p>
                        <p class="text-xs text-emerald-300">Improving ▲</p>
                    </div>
                    <span class="text-sm font-semibold text-emerald-200">4.32</span>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3">
                    <div class="h-2 w-10 rounded-full bg-emerald-500/30"></div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-white">July - December 2024</p>
                        <p class="text-xs text-emerald-300">Improving ▲</p>
                    </div>
                    <span class="text-sm font-semibold text-emerald-200">4.29</span>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3">
                    <div class="h-2 w-10 rounded-full bg-amber-500/30"></div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-white">January - June 2024</p>
                        <p class="text-xs text-amber-300">Stable →</p>
                    </div>
                    <span class="text-sm font-semibold text-amber-200">4.26</span>
                </div>
                <div class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3">
                    <div class="h-2 w-10 rounded-full bg-amber-500/30"></div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-white">July - December 2023</p>
                        <p class="text-xs text-amber-300">Stable →</p>
                    </div>
                    <span class="text-sm font-semibold text-amber-200">4.25</span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-white">Performance Summary</h2>
                <p class="text-xs text-slate-500">Read-only; derived from approved IPCRs</p>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Core Functions Avg (80%)</p>
                    <p class="mt-2 text-2xl font-bold text-white">4.35</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Support Functions Avg (20%)</p>
                    <p class="mt-2 text-2xl font-bold text-white">4.12</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Overall Office Rating</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-200">4.28</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-white">IPCR Contribution</h2>
                <span class="text-xs text-slate-400">Approved IPCRs only - Read-only</span>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full overflow-hidden rounded-xl border border-slate-800 text-sm">
                    <thead class="bg-slate-950/80 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Employee Name</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Position</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Core Avg</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Support Avg</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Overall IPCR Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-100">
                        <tr class="hover:bg-slate-900/70">
                            <td class="px-4 py-3">Ramon Reyes</td>
                            <td class="px-4 py-3 text-slate-300">Administrative Officer IV</td>
                            <td class="px-4 py-3">4.40</td>
                            <td class="px-4 py-3">4.20</td>
                            <td class="px-4 py-3 font-semibold text-white">4.30</td>
                        </tr>
                        <tr class="hover:bg-slate-900/70">
                            <td class="px-4 py-3">Mara dela Cruz</td>
                            <td class="px-4 py-3 text-slate-300">HR Specialist</td>
                            <td class="px-4 py-3">4.28</td>
                            <td class="px-4 py-3">4.05</td>
                            <td class="px-4 py-3 font-semibold text-white">4.22</td>
                        </tr>
                        <tr class="hover:bg-slate-900/70">
                            <td class="px-4 py-3">Leo Santos</td>
                            <td class="px-4 py-3 text-slate-300">Records Officer</td>
                            <td class="px-4 py-3">4.10</td>
                            <td class="px-4 py-3">3.95</td>
                            <td class="px-4 py-3 font-semibold text-white">4.02</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-sm text-slate-300">This OPCR is derived from approved IPCRs and is read-only for supervisors.</p>
        </div>
    </section>
</x-layouts.supervisor>
