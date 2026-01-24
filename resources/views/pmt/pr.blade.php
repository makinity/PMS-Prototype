@extends('layouts.pmt')

@section('main-content')
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Reports</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Performance Reports</h1>
                <p class="text-sm text-slate-400">Generate rollups from approved OPCRs and UWP-linked ORS logs.</p>
            </div>
            <div class="flex gap-2 text-xs">
                <button class="rounded-lg border border-slate-700 px-3 py-1.5 text-slate-200 hover:bg-slate-800">Filter</button>
                <button class="rounded-lg border border-emerald-600/60 bg-emerald-500/10 px-3 py-1.5 font-semibold text-emerald-200 hover:bg-emerald-500/20">Export</button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Reports generated</p>
                <p class="mt-1 text-3xl font-semibold text-white">12</p>
                <p class="text-xs text-slate-400">Last 30 days</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Pending approvals</p>
                <p class="mt-1 text-3xl font-semibold text-amber-300">4</p>
                <p class="text-xs text-slate-400">Need PMT sign-off</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Alerts</p>
                <p class="mt-1 text-3xl font-semibold text-rose-300">2</p>
                <p class="text-xs text-slate-400">Missing ORS linkage</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-white">Report queue</p>
                    <p class="text-xs text-slate-400">Only approved OPCRs appear here.</p>
                </div>
                <div class="flex gap-2 text-xs">
                    <button class="rounded-lg border border-slate-700 px-3 py-1.5 text-slate-200 hover:bg-slate-800">Refresh</button>
                    <button class="rounded-lg border border-blue-600/60 bg-blue-500/10 px-3 py-1.5 font-semibold text-blue-200 hover:bg-blue-500/20">Generate</button>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm border border-slate-800 rounded-xl overflow-hidden">
                    <thead class="bg-slate-950/80 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Report</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Coverage</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Generated</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800">Status</th>
                            <th class="px-4 py-3 text-left font-semibold border-b border-slate-800 w-32">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-100">
                        <tr class="hover:bg-slate-900/70">
                            <td class="px-4 py-3">Q4 2025 Rollup</td>
                            <td class="px-4 py-3 text-slate-300">Oct–Dec 2025</td>
                            <td class="px-4 py-3 text-slate-300">Today</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full border border-emerald-600/50 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-200">Ready</span>
                            </td>
                            <td class="px-4 py-3 flex gap-2">
                                <button class="rounded-lg border border-slate-700 px-3 py-1 text-xs font-semibold text-slate-200 hover:bg-slate-800">View</button>
                                <button class="rounded-lg border border-emerald-600/60 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200 hover:bg-emerald-500/20">Export</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900/70">
                            <td class="px-4 py-3">OPCR Summary</td>
                            <td class="px-4 py-3 text-slate-300">Jan–Jun 2025</td>
                            <td class="px-4 py-3 text-slate-300">Jan 6, 2026</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full border border-blue-600/50 bg-blue-500/10 px-2.5 py-1 text-[11px] font-semibold text-blue-200">In progress</span>
                            </td>
                            <td class="px-4 py-3 flex gap-2">
                                <button class="rounded-lg border border-slate-700 px-3 py-1 text-xs font-semibold text-slate-200 hover:bg-slate-800">View</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900/70">
                            <td class="px-4 py-3">Performance Brief</td>
                            <td class="px-4 py-3 text-slate-300">YTD 2025</td>
                            <td class="px-4 py-3 text-slate-300">Dec 30, 2025</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full border border-rose-700/60 bg-rose-500/10 px-2.5 py-1 text-[11px] font-semibold text-rose-200">Blocked</span>
                            </td>
                            <td class="px-4 py-3 flex gap-2">
                                <button class="rounded-lg border border-slate-700 px-3 py-1 text-xs font-semibold text-slate-200 hover:bg-slate-800">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 text-xs text-slate-500">Reports inherit data from approved OPCRs and ORS logs. Manual overrides remain disabled to keep audit trails clean.</p>
        </div>
    </section>
@endsection
