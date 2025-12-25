<x-layouts.employee>
    <section class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-white">My Tasks</h1>
            <p class="text-sm text-gray-400 mt-1">
                View and manage your assigned and logged tasks.
            </p>
        </div>

        {{-- Task Filters --}}
        <div class="flex flex-wrap gap-3">
            <select class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2.5">
                <option class="bg-gray-700">Status: All</option>
                <option class="bg-gray-700">In Progress</option>
                <option class="bg-gray-700">Completed</option>
                <option class="bg-gray-700">Pending Review</option>
                <option class="bg-gray-700">Overdue</option>
            </select>

            <input type="date" 
                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2.5">
        </div>

        {{-- Task Table --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-white">Task</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Client</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">Report Generation</td>
                            <td class="px-4 py-3 text-gray-300">ABC Corp</td>
                            <td class="px-4 py-3 text-gray-300">Aug 12, 2025</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900 text-blue-300">
                                    In Progress
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button" data-modal-target="task-view-modal" data-modal-toggle="task-view-modal" class="text-blue-400 hover:text-blue-300 font-medium text-sm transition-colors duration-200">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">Client Feedback Review</td>
                            <td class="px-4 py-3 text-gray-300">Nova Health</td>
                            <td class="px-4 py-3 text-gray-300">Aug 14, 2025</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-900 text-emerald-300">
                                    Completed
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button" data-modal-target="task-view-modal" data-modal-toggle="task-view-modal" class="text-blue-400 hover:text-blue-300 font-medium text-sm transition-colors duration-200">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">Weekly Sprint Planning</td>
                            <td class="px-4 py-3 text-gray-300">Internal</td>
                            <td class="px-4 py-3 text-gray-300">Aug 15, 2025</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-900 text-amber-300">
                                    Pending Review
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button" data-modal-target="task-view-modal" data-modal-toggle="task-view-modal" class="text-blue-400 hover:text-blue-300 font-medium text-sm transition-colors duration-200">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">Bug Triage</td>
                            <td class="px-4 py-3 text-gray-300">Brighton Labs</td>
                            <td class="px-4 py-3 text-gray-300">Aug 16, 2025</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-900 text-rose-300">
                                    Overdue
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button" data-modal-target="task-view-modal" data-modal-toggle="task-view-modal" class="text-blue-400 hover:text-blue-300 font-medium text-sm transition-colors duration-200">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">Quarterly Metrics Update</td>
                            <td class="px-4 py-3 text-gray-300">River City Bank</td>
                            <td class="px-4 py-3 text-gray-300">Aug 19, 2025</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900 text-blue-300">
                                    In Progress
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button" data-modal-target="task-view-modal" data-modal-toggle="task-view-modal" class="text-blue-400 hover:text-blue-300 font-medium text-sm transition-colors duration-200">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">Documentation Cleanup</td>
                            <td class="px-4 py-3 text-gray-300">Internal</td>
                            <td class="px-4 py-3 text-gray-300">Aug 20, 2025</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-700 text-slate-200">
                                    Not Started
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button" data-modal-target="task-view-modal" data-modal-toggle="task-view-modal" class="text-blue-400 hover:text-blue-300 font-medium text-sm transition-colors duration-200">
                                    View
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center p-4 text-sm text-gray-400 bg-gray-800 border border-gray-700 rounded-lg">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3 text-blue-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span>Tasks are linked to ORS entries and IPCR actual accomplishments.</span>
        </div>

        <div id="task-view-modal" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
            <div class="relative w-full max-w-2xl p-4">
                <div class="relative rounded-lg border border-gray-700 bg-gray-900 shadow-lg">
                    <div class="flex items-start justify-between border-b border-gray-700 p-4">
                        <div>
                            <h3 id="task-view-modal-title" class="text-lg font-semibold text-white">Task Details</h3>
                            <p class="text-sm text-gray-400">Report Generation</p>
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
                                <p class="text-sm font-medium text-white">Aug 12, 2025</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                                <span class="inline-flex items-center rounded-full bg-blue-900 px-2.5 py-0.5 text-xs font-medium text-blue-300">
                                    In Progress
                                </span>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Linked Entry</p>
                                <p class="text-sm font-medium text-white">ORS-2025-014</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Details</p>
                            <p class="mt-1 text-sm text-gray-300">
                                Compile weekly analytics, validate against source data, and export the report for review.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 border-t border-gray-700 p-4">
                        <button type="button" data-modal-hide="task-view-modal" class="rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-200 transition hover:bg-gray-800">
                            Close
                        </button>
                        <button type="button" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-500">
                            Open linked ORS
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </section>
</x-layouts.employee>
