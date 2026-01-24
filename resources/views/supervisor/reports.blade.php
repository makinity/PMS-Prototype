@extends('layouts.supervisor')

@section('main-content')
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Supervisor View</p>
                <h1 class="text-3xl font-bold text-white">Reports</h1>
                <p class="text-gray-300">Verify report availability and freshness; escalate gaps without regenerating files.</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-blue-900/40 border border-blue-700/40 text-blue-200 text-xs font-semibold">Read-only</span>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">No downloads triggered</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Reporting Inventory</h2>
                <span class="text-xs text-slate-400">Static overview</span>
            </div>
            <div class="overflow-hidden rounded-lg border border-gray-800">
                <table class="min-w-full divide-y divide-gray-800 text-sm">
                    <thead class="bg-gray-900/70 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Report</th>
                            <th class="px-4 py-3 text-left font-semibold">Cadence</th>
                            <th class="px-4 py-3 text-left font-semibold">Last Updated</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-slate-200">
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Weekly Productivity Summary</td>
                            <td class="px-4 py-3">Weekly</td>
                            <td class="px-4 py-3">Today 08:00</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-emerald-900/40 border border-emerald-700/40 text-emerald-300 text-xs font-semibold">Current</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-300">Validated by analytics</td>
                        </tr>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Overdue &amp; Risk Register</td>
                            <td class="px-4 py-3">Daily</td>
                            <td class="px-4 py-3">Yesterday</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-amber-900/40 border border-amber-700/40 text-amber-200 text-xs font-semibold">Stale</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-amber-300">Awaiting refresh</td>
                        </tr>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Compliance Evidence Tracker</td>
                            <td class="px-4 py-3">Weekly</td>
                            <td class="px-4 py-3">Mon</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-blue-900/40 border border-blue-700/40 text-blue-200 text-xs font-semibold">Processing</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-300">Review in progress</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 shadow-lg space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Reporting Guidance</h3>
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">Oversight</span>
            </div>
            <ul class="space-y-3 text-sm text-slate-200">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Freshness checks</p>
                    <p class="text-slate-400">Flag anything older than cadence; request refresh through analytics.</p>
                </li>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Access scope</p>
                    <p class="text-slate-400">Ensure reports exclude edit controls; view-only links preferred.</p>
                </li>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Escalation path</p>
                    <p class="text-slate-400">Coordinate with managers when risk registers go stale.</p>
                </li>
            </ul>

            <div class="rounded-lg border border-slate-800 bg-slate-900/70 p-4 text-xs text-slate-300">
                <p class="font-semibold text-slate-100 mb-1">Helper note</p>
                <p>Supervisors verify visibility and status only. Report generation or distribution happens outside this view.</p>
            </div>
        </div>
    </div>
@endsection
