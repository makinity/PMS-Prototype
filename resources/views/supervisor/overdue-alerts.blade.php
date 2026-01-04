<x-layouts.supervisor>
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Supervisor View</p>
                <h1 class="text-3xl font-bold text-white">Overdue &amp; Alerts</h1>
                <p class="text-gray-300">Track aging tasks and alerts requiring attention without acting on them directly.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-rose-900/30 border border-rose-700/50 text-rose-200 text-xs font-semibold">Overdue watch</span>
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">Notify manager only</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="xl:col-span-2 p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Overdue Items</h2>
                <span class="text-xs text-slate-400">Monitoring only</span>
            </div>
            <div class="overflow-hidden rounded-lg border border-gray-800">
                <table class="min-w-full divide-y divide-gray-800 text-sm">
                    <thead class="bg-gray-900/70 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Task</th>
                            <th class="px-4 py-3 text-left font-semibold">Owner</th>
                            <th class="px-4 py-3 text-left font-semibold">Age</th>
                            <th class="px-4 py-3 text-left font-semibold">Severity</th>
                            <th class="px-4 py-3 text-left font-semibold">Next Check</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-slate-200">
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Compliance Evidence Pack</td>
                            <td class="px-4 py-3">Rhea Kim</td>
                            <td class="px-4 py-3">3 days</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-amber-900/40 border border-amber-700/40 text-amber-200 text-xs font-semibold">High</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-300">Confirm blocker; log note</td>
                        </tr>
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Vendor Access Cleanup</td>
                            <td class="px-4 py-3">Liam Park</td>
                            <td class="px-4 py-3">5 days</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-rose-900/40 border border-rose-700/40 text-rose-200 text-xs font-semibold">Critical</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-rose-300">Escalation recommended</td>
                        </tr>
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Sprint Documentation</td>
                            <td class="px-4 py-3">Noah Cruz</td>
                            <td class="px-4 py-3">1 day</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-blue-900/40 border border-blue-700/40 text-blue-200 text-xs font-semibold">Moderate</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-300">Check status update</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Alert Feed</h3>
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">Audit trail</span>
            </div>
            <div class="space-y-3 text-sm text-slate-200">
                <div class="p-3 rounded-lg bg-rose-900/20 border border-rose-800/40">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded-full bg-rose-800 text-rose-100 text-[11px] font-semibold">Breach Risk</span>
                        <span class="text-xs text-slate-400">08:15</span>
                    </div>
                    <p class="text-white">Unrevoked vendor credential detected; awaiting admin response.</p>
                </div>
                <div class="p-3 rounded-lg bg-amber-900/20 border border-amber-800/40">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded-full bg-amber-800 text-amber-100 text-[11px] font-semibold">SLA Drift</span>
                        <span class="text-xs text-slate-400">09:40</span>
                    </div>
                    <p class="text-white">Compliance evidence overdue; reminder sent to owner.</p>
                </div>
                <div class="p-3 rounded-lg bg-blue-900/20 border border-blue-800/40">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-0.5 rounded-full bg-blue-800 text-blue-100 text-[11px] font-semibold">Info</span>
                        <span class="text-xs text-slate-400">10:05</span>
                    </div>
                    <p class="text-white">Documentation update pending review; no action needed.</p>
                </div>
            </div>

            <div class="mt-4 rounded-lg border border-slate-800 bg-slate-900/70 p-4 text-xs text-slate-300">
                <p class="font-semibold text-slate-100 mb-1">Helper note</p>
                <p>Capture notes and escalate patterns. Supervisor role does not clear alerts; route actions to the responsible manager.</p>
            </div>
        </div>
    </div>
</x-layouts.supervisor>
