@extends('layouts.supervisor')

@section('main-content')
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Supervisor View</p>
                <h1 class="text-3xl font-bold text-white">Recommendations</h1>
                <p class="text-gray-300">Document improvement requests and highlight support needs for management review.</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-emerald-900/30 border border-emerald-700/50 text-emerald-300 text-xs font-semibold">Advisory</span>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">No direct changes</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Submitted Recommendations</h2>
                <span class="text-xs text-slate-400">Static list</span>
            </div>
            <div class="space-y-3 text-sm text-slate-200">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <div class="p-4 rounded-lg bg-slate-900/60 border border-slate-800">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-white font-semibold">Add backup reviewer for validation window</p>
                        <span class="px-3 py-1 rounded-full bg-amber-900/40 border border-amber-700/40 text-amber-200 text-xs font-semibold">Pending</span>
                    </div>
                    <p class="text-slate-300">Reduce midweek backlog by assigning secondary reviewer on Tuesdays and Thursdays.</p>
                    <p class="text-xs text-slate-400 mt-1">Submitted to: Operations Manager • Priority: High</p>
                </div>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <div class="p-4 rounded-lg bg-slate-900/60 border border-slate-800">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-white font-semibold">Standardize evidence checklist</p>
                        <span class="px-3 py-1 rounded-full bg-emerald-900/40 border border-emerald-700/40 text-emerald-300 text-xs font-semibold">Accepted</span>
                    </div>
                    <p class="text-slate-300">Provide template for screenshots and logs to speed up validation.</p>
                    <p class="text-xs text-slate-400 mt-1">Submitted to: Quality Lead • Priority: Medium</p>
                </div>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <div class="p-4 rounded-lg bg-slate-900/60 border border-slate-800">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-white font-semibold">Rotate on-call for escalations</p>
                        <span class="px-3 py-1 rounded-full bg-blue-900/40 border border-blue-700/40 text-blue-200 text-xs font-semibold">Under review</span>
                    </div>
                    <p class="text-slate-300">Weekly rotation to avoid bottlenecks when owners are unavailable.</p>
                    <p class="text-xs text-slate-400 mt-1">Submitted to: Department Head • Priority: Medium</p>
                </div>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 shadow-lg space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Impact Focus</h3>
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">Guidance</span>
            </div>
            <ul class="space-y-3 text-sm text-slate-200">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Reduce validation latency</p>
                    <p class="text-slate-400">Evidence checklist can trim 0.5 days per item.</p>
                </li>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Stabilize support coverage</p>
                    <p class="text-slate-400">On-call rotation mitigates single-point delays.</p>
                </li>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Protect compliance deadlines</p>
                    <p class="text-slate-400">Escalate overdue evidence to management within 24h.</p>
                </li>
            </ul>

            <div class="rounded-lg border border-slate-800 bg-slate-900/70 p-4 text-xs text-slate-300">
                <p class="font-semibold text-slate-100 mb-1">Helper note</p>
                <p>Recommendations are advisory only; supervisors capture and route suggestions to decision-makers.</p>
            </div>
        </div>
    </div>
@endsection
