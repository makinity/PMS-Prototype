<x-layouts.employee>
    <section class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-white">My Tasks</h1>
            <p class="text-sm text-gray-400 mt-1">
                Read-only mirror of ORS entries.
                <span class="block">Tasks are created and submitted in ORS.</span>
                <span class="block">This page mirrors ORS status only.</span>
            </p>
        </div>

        {{-- Task Filters --}}
        <div class="flex flex-wrap gap-3">
            <select class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2.5">
                <option class="bg-gray-700">Status: All</option>
                <option class="bg-gray-700">Draft</option>
                <option class="bg-gray-700">Recording</option>
                <option class="bg-gray-700">Submitted</option>
                <option class="bg-gray-700">Missing / Overdue</option>
                <option class="bg-gray-700">Returned</option>
            </select>

            <input type="date"
                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2.5">
        </div>

        {{-- Task Table --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg overflow-hidden">
            <div class="px-4 py-3 text-xs text-gray-400 border-b border-gray-700">
                Status reflects ORS state only; no submissions, uploads, or task creation here.
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-white">Task</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Output State</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">E-Bank Scanning</td>
                            <td class="px-4 py-3 text-gray-300">Jan 4, 2026</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-900 text-violet-300">
                                    Submitted (Locked)
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-emerald-300">Output submitted</span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">Visible to supervisor</td>
                        </tr>

                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">E-Bank Scanning</td>
                            <td class="px-4 py-3 text-gray-300">Jan 5, 2026</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-900 text-amber-300">
                                    Recording
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-amber-300">Output pending</span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">Active timer</td>
                        </tr>

                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">OTC Revenue Processing</td>
                            <td class="px-4 py-3 text-gray-300">Jan 3, 2026</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-900 text-violet-300">
                                    Submitted (Locked)
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-emerald-300">Output submitted</span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">Visible to supervisor</td>
                        </tr>

                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">Maintenance of Revenue Records Filing System</td>
                            <td class="px-4 py-3 text-gray-300">Jan 6, 2026</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900 text-red-300">
                                    Missing / Overdue
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-red-300">Output Missing</span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">Unfinished Output</td>
                        </tr>

                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">OTC Revenue Transaction Processing</td>
                            <td class="px-4 py-3 text-gray-300">Jan 2, 2026</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-900 text-violet-300">
                                    Submitted (Locked)
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-emerald-300">	Output submitted</span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">Visible to supervisor</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center p-4 text-sm text-gray-400 bg-gray-800 border border-gray-700 rounded-lg">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3 text-blue-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span>My Tasks mirrors ORS activity. Tasks are created and submitted in ORS. This page mirrors ORS status only.</span>
        </div>

        <div id="task-view-modal" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
            <div class="relative w-full max-w-2xl p-4">
                <div class="relative rounded-lg border border-gray-700 bg-gray-900 shadow-lg">
                    <div class="flex items-start justify-between border-b border-gray-700 p-4">
                        <div>
                            <h3 id="task-view-modal-title" class="text-lg font-semibold text-white">Task Details</h3>
                            <p class="text-sm text-gray-400">E-Bank Scanning</p>
                        </div>
                        <button type="button" data-modal-hide="task-view-modal" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-800 hover:text-white">
                            <span class="sr-only">Close modal</span>
                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4 p-4 text-sm text-gray-300">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Client</p>
                                <p class="text-sm font-medium text-white">ABC Corp</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Due Date</p>
                                <p class="text-sm font-medium text-white">Dec 27, 2025</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                                <span class="inline-flex items-center rounded-full bg-amber-900 px-2.5 py-0.5 text-xs font-medium text-amber-200">
                                    Recording
                                </span>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Request ID</p>
                                <p class="text-sm font-medium text-white">REQ-2025-014</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Tracking</p>
                                <p class="text-sm font-medium text-white">Active since 09:12</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Output Link</p>
                                <p class="text-sm font-medium text-white">Pending submission</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Time Window</p>
                                <p class="text-sm font-medium text-white">09:12 - --</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Details</p>
                            <p class="mt-1 text-sm text-gray-300">
                                Scan and validate bank statements for ABC Corp, attach BSF-01 output, and log duration automatically. Start and end times are recorded automatically during the task session.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 border-t border-gray-700 p-4">
                        <button type="button" data-modal-hide="task-view-modal" class="rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-200 transition hover:bg-gray-800">
                            Close
                        </button>
                        <span class="text-xs text-gray-400 mr-auto">Submission is performed inside ORS.</span>
                        <a href="{{ route('employee.ors') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-500">
                            View in ORS
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </section>
</x-layouts.employee>
