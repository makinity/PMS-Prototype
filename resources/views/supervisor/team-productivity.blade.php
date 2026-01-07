<x-layouts.supervisor>
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Supervisor View</p>
                <h1 class="text-3xl font-bold text-white">Team Productivity</h1>
                <p class="text-gray-300">Observe throughput trends and validation load without altering work assignments.</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-emerald-900/30 border border-emerald-700/50 text-emerald-300 text-xs font-semibold">Monitoring only</span>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">Insights, not actions</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="p-5 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <p class="text-xs uppercase tracking-wide text-slate-400">On-time rate</p>
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <p class="text-3xl font-bold text-white mt-2">86%</p>
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <p class="text-xs text-emerald-300 mt-1">+3% vs last week</p>
        </div>
        <div class="p-5 rounded-xl bg-gradient-to-br from-blue-900/20 to-blue-800/10 border border-blue-700/30 shadow-lg">
            <p class="text-xs uppercase tracking-wide text-blue-200">Avg cycle time</p>
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <p class="text-3xl font-bold text-white mt-2">3.8 days</p>
            <p class="text-xs text-slate-300 mt-1">Story start to validation</p>
        </div>
        <div class="p-5 rounded-xl bg-gradient-to-br from-amber-900/20 to-amber-800/10 border border-amber-700/30 shadow-lg">
            <p class="text-xs uppercase tracking-wide text-amber-200">Validation backlog</p>
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <p class="text-3xl font-bold text-white mt-2">5</p>
            <p class="text-xs text-amber-300 mt-1">Flag items needing evidence</p>
        </div>
        <div class="p-5 rounded-xl bg-gradient-to-br from-emerald-900/20 to-emerald-800/10 border border-emerald-700/30 shadow-lg">
            <p class="text-xs uppercase tracking-wide text-emerald-200">Outputs this week</p>
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <p class="text-3xl font-bold text-white mt-2">27</p>
            <p class="text-xs text-slate-300 mt-1">Ahead of forecast</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="xl:col-span-2 p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Daily Output Snapshot</h2>
                <span class="text-xs text-slate-400">Static data</span>
            </div>
            <div class="overflow-hidden rounded-lg border border-gray-800">
                <table class="min-w-full divide-y divide-gray-800 text-sm">
                    <thead class="bg-gray-900/70 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Day</th>
                            <th class="px-4 py-3 text-left font-semibold">Completed</th>
                            <th class="px-4 py-3 text-left font-semibold">On-time</th>
                            <th class="px-4 py-3 text-left font-semibold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-slate-200">
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Mon</td>
                            <td class="px-4 py-3">6</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-emerald-900/40 border border-emerald-700/40 text-emerald-300 text-xs font-semibold">92%</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-300">Light validation queue</td>
                        </tr>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Tue</td>
                            <td class="px-4 py-3">5</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-blue-900/40 border border-blue-700/40 text-blue-200 text-xs font-semibold">84%</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-300">Two items awaiting evidence</td>
                        </tr>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Wed</td>
                            <td class="px-4 py-3">7</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-emerald-900/40 border border-emerald-700/40 text-emerald-300 text-xs font-semibold">88%</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-300">Spike in validation backlog</td>
                        </tr>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Thu</td>
                            <td class="px-4 py-3">9</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-amber-900/40 border border-amber-700/40 text-amber-200 text-xs font-semibold">81%</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-300">Two overdue carry-overs</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 shadow-lg space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Observations</h3>
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">Supervisor notes</span>
            </div>
            <ul class="space-y-3 text-sm text-slate-200">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Throughput stable</p>
                    <p class="text-slate-400">Team holding steady above forecast; maintain cadence.</p>
                </li>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Validation latency</p>
                    <p class="text-slate-400">Backlog rising midweek; recommend manager adds reviewer capacity.</p>
                </li>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Quality signal</p>
                    <p class="text-slate-400">On-time rate holding but evidence completeness needs reminders.</p>
                </li>
            </ul>
            <div class="rounded-lg border border-slate-800 bg-slate-900/70 p-4 text-xs text-slate-300">
                <p class="font-semibold text-slate-100 mb-1">Helper note</p>
                <p>Share insights with managers; do not adjust forecasts or redistribute work from this page.</p>
            </div>
        </div>
    </div>
</x-layouts.supervisor>
