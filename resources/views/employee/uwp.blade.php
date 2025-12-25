<x-layouts.employee>
    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-xl font-semibold">Unit Work Plan (UWP)</h1>
                <p class="text-sm text-gray-500">
                    Basis for IPCR and OPCR generation
                </p>
            </div>

            {{-- Status --}}
            <span class="px-3 py-1 text-xs rounded bg-yellow-100 text-yellow-800">
                DRAFT
            </span>
        </div>

        {{-- BASIC INFORMATION --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 border rounded-lg p-4">
            <div>
                <label class="text-sm font-medium">Office / Unit</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="Provincial Human Resource Management Office">
            </div>

            <div>
                <label class="text-sm font-medium">Period Covered</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="January – June 2025">
            </div>

            <div>
                <label class="text-sm font-medium">Prepared By</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="HRMO / Office Head">
            </div>

            <div>
                <label class="text-sm font-medium">Date Prepared</label>
                <input type="date" class="w-full mt-1 border rounded px-3 py-2">
            </div>
        </div>

        {{-- CORE FUNCTIONS --}}
        <div class="border rounded-lg p-4 space-y-3">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg">
                    Core Functions <span class="text-sm text-gray-500">(80%)</span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 w-1/4">Expected Outputs</th>
                            <th class="border px-3 py-2">Efficiency / Quantity</th>
                            <th class="border px-3 py-2">Quality / Effectiveness</th>
                            <th class="border px-3 py-2">Timeliness</th>
                            <th class="border px-3 py-2 w-16">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border px-3 py-2">
                                <input type="text" class="w-full border rounded px-2 py-1"
                                       placeholder="e.g. HRIS records updated">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" class="w-full border rounded px-2 py-1"
                                       placeholder="e.g. 100% of records">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" class="w-full border rounded px-2 py-1"
                                       placeholder="e.g. Accurate, complete, error-free">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" class="w-full border rounded px-2 py-1"
                                       placeholder="e.g. Within prescribed period">
                            </td>
                            <td class="border px-3 py-2 text-center">
                                <button class="text-red-500 text-xs">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button class="text-sm text-blue-600 hover:underline">
                + Add Core Output
            </button>
        </div>

        {{-- SUPPORT FUNCTIONS --}}
        <div class="border rounded-lg p-4 space-y-3">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg">
                    Support Functions <span class="text-sm text-gray-500">(20%)</span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 w-1/4">Expected Outputs</th>
                            <th class="border px-3 py-2">Efficiency / Quantity</th>
                            <th class="border px-3 py-2">Quality / Effectiveness</th>
                            <th class="border px-3 py-2">Timeliness</th>
                            <th class="border px-3 py-2 w-16">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border px-3 py-2">
                                <input type="text" class="w-full border rounded px-2 py-1"
                                       placeholder="e.g. Reports prepared">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-3 py-2 text-center">
                                <button class="text-red-500 text-xs">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button class="text-sm text-blue-600 hover:underline">
                + Add Support Output
            </button>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex justify-between items-center">
            <p class="text-xs text-gray-500">
                Once approved, this UWP will be locked and used to generate IPCRs.
            </p>

            <div class="flex gap-2">
                <button class="px-4 py-2 border rounded">
                    Save as Draft
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Submit for Approval
                </button>
            </div>
        </div>

    </div>
</x-layouts.employee>
