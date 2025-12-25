<x-layouts.employee>
    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-xl font-semibold">
                    Individual Performance Commitment and Review (IPCR)
                </h1>
                <p class="text-sm text-gray-500">
                    Commitment Phase (Generated from Unit Work Plan)
                </p>
            </div>

            <span class="px-3 py-1 text-xs rounded bg-blue-100 text-blue-800">
                COMMITMENT
            </span>
        </div>

        {{-- EMPLOYEE INFO --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 border rounded-lg p-4">
            <div>
                <label class="text-sm font-medium">Employee Name</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="Juan Dela Cruz" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Position</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="Administrative Assistant I" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Office / Unit</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="Provincial Human Resource Management Office" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Rating Period</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="January – June 2025" disabled>
            </div>
        </div>

        {{-- CORE FUNCTIONS --}}
        <div class="border rounded-lg p-4 space-y-3">
            <h2 class="font-semibold text-lg">
                Core Functions <span class="text-sm text-gray-500">(80%)</span>
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 w-1/4">Expected Outputs</th>
                            <th class="border px-3 py-2">Efficiency / Quantity</th>
                            <th class="border px-3 py-2">Quality / Effectiveness</th>
                            <th class="border px-3 py-2">Timeliness</th>
                            <th class="border px-3 py-2 w-24">Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Generated from UWP --}}
                        <tr>
                            <td class="border px-3 py-2">
                                HRIS records updated
                            </td>
                            <td class="border px-3 py-2">
                                100% of records updated
                            </td>
                            <td class="border px-3 py-2">
                                Accurate and error-free
                            </td>
                            <td class="border px-3 py-2">
                                Within prescribed period
                            </td>
                            <td class="border px-3 py-2">
                                <input type="number" class="w-full border rounded px-2 py-1"
                                       placeholder="e.g. 20">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SUPPORT FUNCTIONS --}}
        <div class="border rounded-lg p-4 space-y-3">
            <h2 class="font-semibold text-lg">
                Support Functions <span class="text-sm text-gray-500">(20%)</span>
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 w-1/4">Expected Outputs</th>
                            <th class="border px-3 py-2">Efficiency / Quantity</th>
                            <th class="border px-3 py-2">Quality / Effectiveness</th>
                            <th class="border px-3 py-2">Timeliness</th>
                            <th class="border px-3 py-2 w-24">Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border px-3 py-2">
                                Reports prepared
                            </td>
                            <td class="border px-3 py-2">
                                Reports submitted
                            </td>
                            <td class="border px-3 py-2">
                                Complete and compliant
                            </td>
                            <td class="border px-3 py-2">
                                On or before deadline
                            </td>
                            <td class="border px-3 py-2">
                                <input type="number" class="w-full border rounded px-2 py-1"
                                       placeholder="e.g. 5">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- COMMITMENT CONFIRMATION --}}
        <div class="border rounded-lg p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-medium">Immediate Supervisor</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="Supervisor Name" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Employee Confirmation</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="Juan Dela Cruz" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Date Committed</label>
                <input type="date" class="w-full mt-1 border rounded px-3 py-2">
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-2">
            <button class="px-4 py-2 border rounded">
                Save Draft
            </button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Confirm Commitment
            </button>
        </div>

    </div>
</x-layouts.employee>
