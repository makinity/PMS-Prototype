<x-layouts.employee>
    <section class="space-y-6">

        <div>
            <h1 class="text-xl font-semibold">My Tasks</h1>
            <p class="text-sm text-gray-500">
                View and manage your assigned and logged tasks.
            </p>
        </div>

        {{-- Task Filters --}}
        <div class="flex flex-wrap gap-2">
            <select class="border rounded px-3 py-2 text-sm">
                <option>Status: All</option>
                <option>In Progress</option>
                <option>Completed</option>
                <option>Pending Review</option>
                <option>Overdue</option>
            </select>

            <input type="date" class="border rounded px-3 py-2 text-sm">
        </div>

        {{-- Task Table --}}
        <div class="border rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left">Task</th>
                        <th class="px-3 py-2">Client</th>
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="px-3 py-2">Report Generation</td>
                        <td class="px-3 py-2 text-center">ABC Corp</td>
                        <td class="px-3 py-2 text-center">Aug 12, 2025</td>
                        <td class="px-3 py-2 text-center">In Progress</td>
                        <td class="px-3 py-2 text-center">
                            <button class="text-blue-600 text-xs">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-xs text-gray-500">
            Tasks are linked to ORS entries and IPCR actual accomplishments.
        </div>

    </section>
</x-layouts.employee>
