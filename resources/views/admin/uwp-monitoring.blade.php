@extends('layouts.admin')

@section('main-content')
    <section class="space-y-6 admin-page">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">UWP Monitoring</h1>
                <p class="text-sm text-slate-400">Overview of unit work plans, approvals, and alignment to office goals.</p>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">System-generated, read-only</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" x-data="{ loading: false }" @click="if (loading) return; loading = true; setTimeout(() => { loading = false; }, 1200)" :class="loading ? 'opacity-70 cursor-not-allowed' : ''" class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 hover:bg-slate-800">
                    <i class="fa-solid fa-arrow-rotate-right"></i>
                    <span x-show="!loading">Refresh</span>
                    <svg x-show="loading" class="h-4 w-4 animate-spin text-slate-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>
                <button type="button" x-data="{ loading: false }" @click="if (loading) return; loading = true; setTimeout(() => { loading = false; }, 1200)" :class="loading ? 'opacity-70 cursor-not-allowed' : ''" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    <span x-show="!loading">Export snapshot</span>
                    <svg x-show="loading" class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Semester</label>
                    <select class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        {{-- DUMMY_DATA: replace --}}
                        <option>First Semester (January – June 2026)</option>
                        {{-- DUMMY_DATA: replace --}}
                        <option>Second Semester (July – December 2026)</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Filters display only; data remains system-generated.</p>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Department</label>
                    {{-- DUMMY_DATA: replace --}}
                    <select class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Departments</option>
                        <option>Finance</option>
                        <option>IT Services</option>
                        <option>HR Management</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</label>
                    {{-- DUMMY_DATA: replace --}}
                    <select class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All</option>
                        <option>Awaiting Approval</option>
                        <option>Approved</option>
                        <option>In Revision</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Period</label>
                    {{-- DUMMY_DATA: replace --}}
                    <input type="text" value="System-set semestral coverage" disabled class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    <p class="mt-1 text-xs text-slate-500">Fixed January–June / July–December</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Active UWPs</p>
                {{-- DUMMY_DATA: replace --}}
                <p class="mt-2 text-2xl font-semibold text-white">18</p>
                <p class="text-xs text-slate-500">Across all units</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Pending Approval</p>
                {{-- DUMMY_DATA: replace --}}
                <p class="mt-2 text-2xl font-semibold text-amber-300">5</p>
                <p class="text-xs text-slate-500">Submitted by units</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Approved UWPs</p>
                {{-- DUMMY_DATA: replace --}}
                <p class="mt-2 text-2xl font-semibold text-emerald-400">12</p>
                <p class="text-xs text-slate-500">Locked for monitoring</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Overdue Tasks</p>
                {{-- DUMMY_DATA: replace --}}
                <p class="mt-2 text-2xl font-semibold text-rose-300">3</p>
                <p class="text-xs text-slate-500">Flagged for follow-up</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Unit Work Plans</h2>
                <p class="text-xs text-slate-400">Read-only overview of unit submissions and approvals.</p>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Unit / Department</th>
                            <th class="px-4 py-2 text-left">Semester</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Outputs</th>
                            <th class="px-4 py-2 text-left">Approver</th>
                            <th class="px-4 py-2 text-left">Last Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DUMMY_DATA: replace --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Finance</td>
                            <td class="px-4 py-3">First Semester (Jan–Jun 2026)</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-300">Approved</span>
                            </td>
                            <td class="px-4 py-3">24</td>
                            <td class="px-4 py-3">Director A</td>
                            <td class="px-4 py-3 text-slate-400">Apr 12, 2026</td>
                        </tr>
                        {{-- DUMMY_DATA: replace --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">IT Services</td>
                            <td class="px-4 py-3">First Semester (Jan–Jun 2026)</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300">Awaiting Approval</span>
                            </td>
                            <td class="px-4 py-3">18</td>
                            <td class="px-4 py-3">Director B</td>
                            <td class="px-4 py-3 text-slate-400">Apr 10, 2026</td>
                        </tr>
                        {{-- DUMMY_DATA: replace --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">HR Management</td>
                            <td class="px-4 py-3">Second Semester (Jul–Dec 2026)</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-blue-500/20 px-2 py-1 text-xs text-blue-300">In Revision</span>
                            </td>
                            <td class="px-4 py-3">20</td>
                            <td class="px-4 py-3">Director C</td>
                            <td class="px-4 py-3 text-slate-400">Mar 30, 2026</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Notes</h2>
                <p class="mt-2 text-sm text-slate-300">System-generated overview. Updates reflect approved UWPs only; no manual edits allowed.</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Actions (read-only)</h2>
                <p class="mt-2 text-sm text-slate-300">Filtering and exports are enabled. Approval changes are managed in the respective unit workflows.</p>
            </div>
        </div>
    </section>
@endsection
