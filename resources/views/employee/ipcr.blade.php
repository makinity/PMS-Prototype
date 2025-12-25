<x-layouts.employee>
    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Individual Performance Commitment and Review (IPCR)
                </h1>
                <p class="text-sm text-gray-400 mt-1">
                    Commitment Phase (Generated from Unit Work Plan)
                </p>
            </div>

            <span class="px-3 py-1 text-xs font-medium rounded bg-blue-900 text-blue-300 border border-blue-800">
                COMMITMENT
            </span>
        </div>

        {{-- EMPLOYEE INFO --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div>
                <label class="block mb-2 text-sm font-medium text-white">Employee Name</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Juan Dela Cruz" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Position</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Administrative Assistant I" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Office / Unit</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Provincial Human Resource Management Office" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Rating Period</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="January – June 2025" disabled>
            </div>
        </div>

        {{-- CORE FUNCTIONS --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <h2 class="font-semibold text-lg text-white">
                Core Functions <span class="text-sm text-gray-400">(80%)</span>
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Expected Outputs</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Efficiency / Quantity</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Quality / Effectiveness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeliness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-28">Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Generated from UWP --}}
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                HRIS records updated
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                100% of records updated
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Accurate and error-free
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Within prescribed period
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="number" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
                                       placeholder="e.g. 20">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SUPPORT FUNCTIONS --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <h2 class="font-semibold text-lg text-white">
                Support Functions <span class="text-sm text-gray-400">(20%)</span>
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Expected Outputs</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Efficiency / Quantity</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Quality / Effectiveness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeliness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-28">Weight (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Reports prepared
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Reports submitted
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Complete and compliant
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                On or before deadline
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="number" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2"
                                       placeholder="e.g. 5">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- COMMITMENT CONFIRMATION --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block mb-2 text-sm font-medium text-white">Immediate Supervisor</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Supervisor Name" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Employee Confirmation</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Juan Dela Cruz" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Date Committed</label>
                <input type="date" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3">
            <button class="px-5 py-2.5 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                Save Draft
            </button>
            <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-800 transition-colors duration-200">
                Confirm Commitment
            </button>
        </div>

    </div>
</x-layouts.employee>