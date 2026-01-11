<x-layouts.admin>
    <section class="space-y-6 admin-page">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Semestral Performance Reports</h1>
                <p class="text-sm text-slate-400">System-generated office performance reports for January–June and July–December periods.</p>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-300">System-Generated · Read-only</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        x-data="{ loading: false }"
                        @click="if (loading) return; loading = true; setTimeout(() => { loading = false; }, 1400)"
                        :class="loading ? 'opacity-70 cursor-not-allowed' : ''"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-200 transition hover:bg-slate-800">
                    <i class="fa-solid fa-arrow-rotate-right"></i>
                    <span x-show="!loading">Refresh</span>
                    <svg x-show="loading" class="h-4 w-4 animate-spin text-slate-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>
                <button type="button"
                        x-data="{ loading: false }"
                        @click="if (loading) return; loading = true; setTimeout(() => { loading = false; }, 1400)"
                        :class="loading ? 'opacity-70 cursor-not-allowed' : ''"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-500">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    <span x-show="!loading">Export Report</span>
                    <svg x-show="loading" class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Semester</label>
                    <select class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        {{-- DUMMY_DATA: replace --}}
                        <option>First Semester (January – June 2026)</option>
                        <option>Second Semester (July – December 2026)</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Filters display only; values remain system-generated.</p>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</label>
                    <input type="text" value="Generated (Read-only)" disabled class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Source</label>
                    <input type="text" value="Aggregated from approved IPCRs" disabled class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Core Functions Semestral Score</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-white">90</p>
                <p class="text-xs text-slate-500">System-generated from approved IPCRs</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Support Functions Semestral Score</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-white">85</p>
                <p class="text-xs text-slate-500">System-generated from approved IPCRs</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Overall Semestral Score (Core + Support)</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-emerald-400">175</p>
                <p class="text-xs text-slate-500">Aggregated, read-only</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Summary by Department</h2>
                <p class="text-xs text-slate-400">System-calculated; no manual edits.</p>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Department</th>
                            <th class="px-4 py-2 text-left">Avg Rating</th>
                            <th class="px-4 py-2 text-left">Accomplishment</th>
                            <th class="px-4 py-2 text-left">Approver</th>
                            <th class="px-4 py-2 text-left">Last Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- DUMMY_DATA: replace --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">Finance</td>
                            <td class="px-4 py-3 text-emerald-300">4.35</td>
                            <td class="px-4 py-3">95%</td>
                            <td class="px-4 py-3">Director A</td>
                            <td class="px-4 py-3 text-slate-400">Apr 12, 2026</td>
                        </tr>
                        {{-- DUMMY_DATA: replace --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">IT Services</td>
                            <td class="px-4 py-3 text-emerald-300">4.28</td>
                            <td class="px-4 py-3">92%</td>
                            <td class="px-4 py-3">Director B</td>
                            <td class="px-4 py-3 text-slate-400">Apr 10, 2026</td>
                        </tr>
                        {{-- DUMMY_DATA: replace --}}
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3 text-white">HR Management</td>
                            <td class="px-4 py-3 text-emerald-300">4.51</td>
                            <td class="px-4 py-3">94%</td>
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
                <p class="mt-2 text-sm text-slate-300">Reports are system-generated from approved IPCRs; values are read-only.</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Actions (Prototype)</h2>
                <p class="mt-2 text-sm text-slate-300">Filtering and exports are allowed; edits occur in source IPCR/department workflows.</p>
            </div>
        </div>
    </section>
</x-layouts.admin>
