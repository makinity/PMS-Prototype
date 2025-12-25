<x-layouts.employee>
    <section class="space-y-6">

        <div>
            <h1 class="text-xl font-semibold">Individual Development Plan (IDP)</h1>
            <p class="text-sm text-gray-500">
                Plan your learning, growth, and career development.
            </p>
        </div>

        <div class="border rounded-lg p-4 space-y-4">

            <div>
                <label class="text-sm font-medium">Development Objective</label>
                <input type="text"
                       class="w-full mt-1 border rounded px-3 py-2"
                       placeholder="e.g. Improve data analysis skills">
            </div>

            <div>
                <label class="text-sm font-medium">Planned Activity</label>
                <input type="text"
                       class="w-full mt-1 border rounded px-3 py-2"
                       placeholder="e.g. Attend training / workshop">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Target Date</label>
                    <input type="date" class="w-full mt-1 border rounded px-3 py-2">
                </div>
                <div>
                    <label class="text-sm font-medium">Support Needed</label>
                    <input type="text" class="w-full mt-1 border rounded px-3 py-2">
                </div>
            </div>

            <div class="flex justify-end">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Save IDP
                </button>
            </div>

        </div>

        <p class="text-xs text-gray-500">
            IDP is reviewed separately and does not affect IPCR ratings.
        </p>

    </section>
</x-layouts.employee>
