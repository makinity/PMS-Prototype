<x-layouts.supervisor>
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Supervisor View</p>
                <h1 class="text-3xl font-bold text-white">Team Tasks</h1>
                <p class="text-gray-300">Monitor assignments, risk signals, and progress at a glance without changing owners.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-900/30 border border-emerald-700/50 text-emerald-300 text-xs font-semibold">Read-only oversight</span>
                <span class="px-3 py-1 rounded-full bg-amber-900/30 border border-amber-700/50 text-amber-200 text-xs font-semibold">Escalate via manager</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="xl:col-span-2 p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Active Work Queue</h2>
                <span class="text-xs text-slate-400">Static oversight list</span>
            </div>
            <div class="overflow-hidden rounded-lg border border-gray-800">
                <table class="min-w-full divide-y divide-gray-800 text-sm">
                    <thead class="bg-gray-900/70 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Task</th>
                            <th class="px-4 py-3 text-left font-semibold">Owner</th>
                            <th class="px-4 py-3 text-left font-semibold">Due</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Risk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-slate-200">
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Client Onboarding Checklist</td>
                            <td class="px-4 py-3">Maria Santos</td>
                            <td class="px-4 py-3">Today</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-emerald-900/40 border border-emerald-700/40 text-emerald-300 text-xs font-semibold">On Track</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-emerald-300">Low</td>
                        </tr>
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">API Error Review</td>
                            <td class="px-4 py-3">Jacob Lee</td>
                            <td class="px-4 py-3">Tomorrow</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-blue-900/40 border border-blue-700/40 text-blue-200 text-xs font-semibold">In Progress</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-amber-300">Watch</td>
                        </tr>
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Billing Audit</td>
                            <td class="px-4 py-3">Ava Cruz</td>
                            <td class="px-4 py-3">Fri</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-amber-900/40 border border-amber-700/40 text-amber-200 text-xs font-semibold">At Risk</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-amber-300">Needs update</td>
                        </tr>
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Vendor Access Cleanup</td>
                            <td class="px-4 py-3">Liam Park</td>
                            <td class="px-4 py-3">Mon</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-rose-900/40 border border-rose-700/40 text-rose-200 text-xs font-semibold">Delayed</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-rose-300">Escalation flagged</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 shadow-lg space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Oversight Signals</h3>
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">No edits</span>
            </div>
            <ul class="space-y-3 text-sm text-slate-300">
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_0_4px_rgba(16,185,129,0.15)]"></span>
                    <div>
                        <p class="text-white font-medium">Workload balanced</p>
                        <p class="text-slate-400">No owner above 3 concurrent critical tasks.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-amber-400 shadow-[0_0_0_4px_rgba(251,191,36,0.15)]"></span>
                    <div>
                        <p class="text-white font-medium">Two items aging</p>
                        <p class="text-slate-400">Follow-up recommended before end of week.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-rose-400 shadow-[0_0_0_4px_rgba(248,113,113,0.15)]"></span>
                    <div>
                        <p class="text-white font-medium">Escalation candidate</p>
                        <p class="text-slate-400">Vendor access cleanup blocked; notify manager if unchanged.</p>
                    </div>
                </li>
            </ul>

            <div class="rounded-lg border border-slate-800 bg-slate-900/70 p-4 text-xs text-slate-300">
                <p class="font-semibold text-slate-100 mb-1">Helper note</p>
                <p>Observation-only dashboard. Capture concerns for the manager; do not reassign or close tasks here.</p>
            </div>
        </div>
    </div>
</x-layouts.supervisor>
