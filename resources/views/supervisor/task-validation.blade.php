@extends('layouts.supervisor')

@section('main-content')
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Supervisor View</p>
                <h1 class="text-3xl font-bold text-white">Task Validation</h1>
                <p class="text-gray-300">Review submitted work, flag risks, and document follow-up needs without approving changes.</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-amber-900/30 border border-amber-700/50 text-amber-200 text-xs font-semibold">Validation watch</span>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">No approvals issued</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="xl:col-span-2 p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Awaiting Validation</h2>
                <span class="text-xs text-slate-400">Observation only</span>
            </div>
            <div class="overflow-hidden rounded-lg border border-gray-800">
                <table class="min-w-full divide-y divide-gray-800 text-sm">
                    <thead class="bg-gray-900/70 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Task</th>
                            <th class="px-4 py-3 text-left font-semibold">Contributor</th>
                            <th class="px-4 py-3 text-left font-semibold">Submitted</th>
                            <th class="px-4 py-3 text-left font-semibold">Evidence</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-slate-200">
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">API Error Review</td>
                            <td class="px-4 py-3">Jacob Lee</td>
                            <td class="px-4 py-3">Today 09:20</td>
                            <td class="px-4 py-3 text-xs text-slate-300">Logs + screenshots</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-emerald-900/40 border border-emerald-700/40 text-emerald-300 text-xs font-semibold">Ready for sign-off</span>
                            </td>
                        </tr>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Client Onboarding Checklist</td>
                            <td class="px-4 py-3">Maria Santos</td>
                            <td class="px-4 py-3">Yesterday</td>
                            <td class="px-4 py-3 text-xs text-slate-300">Doc + approval note</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-blue-900/40 border border-blue-700/40 text-blue-200 text-xs font-semibold">Needs review</span>
                            </td>
                        </tr>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <tr class="hover:bg-slate-900/60">
                            <td class="px-4 py-3 font-medium text-white">Billing Audit</td>
                            <td class="px-4 py-3">Ava Cruz</td>
                            <td class="px-4 py-3">2 days ago</td>
                            <td class="px-4 py-3 text-xs text-slate-300">Summary only</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full bg-amber-900/40 border border-amber-700/40 text-amber-200 text-xs font-semibold">Missing evidence</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 shadow-lg space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Validation Notes</h3>
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">Documentation</span>
            </div>
            <ul class="space-y-3 text-sm text-slate-200">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Evidence completeness</p>
                    <p class="text-slate-400">Ensure attachments and screenshots are linked before forwarding to approver.</p>
                </li>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Quality flags</p>
                    <p class="text-slate-400">Billing audit missing source data; note blocker and request clarification via manager.</p>
                </li>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                    <p class="text-white font-medium mb-1">Follow-up cadence</p>
                    <p class="text-slate-400">Recheck pending items every morning; no approvals issued from this page.</p>
                </li>
            </ul>

            <div class="rounded-lg border border-slate-800 bg-slate-900/70 p-4 text-xs text-slate-300">
                <p class="font-semibold text-slate-100 mb-1">Helper note</p>
                <p>Use this view to record observations and escalate gaps. Approval decisions remain with the designated approver.</p>
            </div>
        </div>
    </div>
@endsection
