<x-layouts.supervisor>
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Supervisor View</p>
                <h1 class="text-3xl font-bold text-white">Bottleneck Reports</h1>
                <p class="text-gray-300">Surface process slowdowns and document where support is needed without changing workflow.</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-rose-900/30 border border-rose-700/50 text-rose-200 text-xs font-semibold">Escalation focus</span>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">No process edits</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- DUMMY_DATA: replace with dynamic value --}}
        <div class="p-5 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Intake</h3>
                <span class="px-3 py-1 rounded-full bg-amber-900/30 border border-amber-700/40 text-amber-200 text-xs font-semibold">Queue: 6</span>
            </div>
            <p class="text-sm text-slate-300 mt-2">Waiting on requirement clarifications for 2 items.</p>
            <p class="text-xs text-amber-300 mt-1">Supervisor note: keep managers aware of scope churn.</p>
        </div>
        {{-- DUMMY_DATA: replace with dynamic value --}}
        <div class="p-5 rounded-xl bg-gradient-to-br from-blue-900/20 to-blue-800/10 border border-blue-700/30 shadow-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Validation</h3>
                <span class="px-3 py-1 rounded-full bg-blue-900/40 border border-blue-700/40 text-blue-200 text-xs font-semibold">Queue: 5</span>
            </div>
            <p class="text-sm text-slate-300 mt-2">Evidence missing on two audits; awaiting attachments.</p>
            <p class="text-xs text-slate-300 mt-1">Supervisor note: ask for temporary reviewer support.</p>
        </div>
        {{-- DUMMY_DATA: replace with dynamic value --}}
        <div class="p-5 rounded-xl bg-gradient-to-br from-rose-900/20 to-rose-800/10 border border-rose-700/30 shadow-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">External approvals</h3>
                <span class="px-3 py-1 rounded-full bg-rose-900/40 border border-rose-700/40 text-rose-200 text-xs font-semibold">Queue: 3</span>
            </div>
            <p class="text-sm text-slate-300 mt-2">Waiting on vendor security clearance updates.</p>
            <p class="text-xs text-rose-200 mt-1">Supervisor note: escalation window within 48h.</p>
        </div>
    </div>

    <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-white">Blocker Log</h2>
            <span class="text-xs text-slate-400">Static oversight</span>
        </div>
        <div class="overflow-hidden rounded-lg border border-gray-800">
            <table class="min-w-full divide-y divide-gray-800 text-sm">
                <thead class="bg-gray-900/70 text-slate-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Item</th>
                        <th class="px-4 py-3 text-left font-semibold">Stage</th>
                        <th class="px-4 py-3 text-left font-semibold">Impact</th>
                        <th class="px-4 py-3 text-left font-semibold">Owner to notify</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-slate-200">
                    {{-- DUMMY_DATA: replace with dynamic value --}}
                    <tr class="hover:bg-slate-900/60">
                        <td class="px-4 py-3 font-medium text-white">Vendor Access Cleanup</td>
                        <td class="px-4 py-3">External approvals</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full bg-rose-900/40 border border-rose-700/40 text-rose-200 text-xs font-semibold">Critical</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-300">Security lead</td>
                        <td class="px-4 py-3 text-xs text-rose-300">Blocked 5 days</td>
                    </tr>
                    {{-- DUMMY_DATA: replace with dynamic value --}}
                    <tr class="hover:bg-slate-900/60">
                        <td class="px-4 py-3 font-medium text-white">Billing Audit Evidence</td>
                        <td class="px-4 py-3">Validation</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full bg-amber-900/40 border border-amber-700/40 text-amber-200 text-xs font-semibold">High</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-300">Finance manager</td>
                        <td class="px-4 py-3 text-xs text-amber-300">Waiting on files</td>
                    </tr>
                    {{-- DUMMY_DATA: replace with dynamic value --}}
                    <tr class="hover:bg-slate-900/60">
                        <td class="px-4 py-3 font-medium text-white">Client Onboarding Scope</td>
                        <td class="px-4 py-3">Intake</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full bg-blue-900/40 border border-blue-700/40 text-blue-200 text-xs font-semibold">Moderate</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-300">Product owner</td>
                        <td class="px-4 py-3 text-xs text-slate-300">Clarification pending</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 rounded-lg border border-slate-800 bg-slate-900/70 p-4 text-xs text-slate-300">
            <p class="font-semibold text-slate-100 mb-1">Helper note</p>
            <p>Use this log to brief decision-makers. Supervisors do not reroute work; capture and communicate blockers promptly.</p>
        </div>
    </div>
</x-layouts.supervisor>
