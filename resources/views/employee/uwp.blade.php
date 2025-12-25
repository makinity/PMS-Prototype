<x-layouts.employee>
    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">Unit Work Plan (UWP)</h1>
                <p class="text-sm text-gray-400 mt-1">
                    Basis for IPCR and OPCR generation
                </p>
            </div>

            {{-- Status --}}
            <span class="px-3 py-1 text-xs font-medium rounded bg-yellow-900 text-yellow-300 border border-yellow-800">
                DRAFT
            </span>
        </div>

        {{-- BASIC INFORMATION --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div>
                <label class="block mb-2 text-sm font-medium text-white">Office / Unit</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                       value="Provincial Human Resource Management Office">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Period Covered</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                       value="January – June 2025">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Prepared By</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                       value="HRMO / Office Head">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Date Prepared</label>
                <input type="date" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
        </div>

        {{-- CORE FUNCTIONS --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg text-white">
                    Core Functions <span class="text-sm text-gray-400">(80%)</span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Expected Outputs</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Efficiency / Quantity</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Quality / Effectiveness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeliness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-20">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400"
                                       placeholder="e.g. HRIS records updated">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400"
                                       placeholder="e.g. 100% of records">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400"
                                       placeholder="e.g. Accurate, complete, error-free">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400"
                                       placeholder="e.g. Within prescribed period">
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-center">
                                <button class="text-red-400 hover:text-red-300 text-sm font-medium transition-colors duration-200">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button class="flex items-center text-sm text-blue-400 hover:text-blue-300 font-medium transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Core Output
            </button>
        </div>

        {{-- SUPPORT FUNCTIONS --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-lg text-white">
                    Support Functions <span class="text-sm text-gray-400">(20%)</span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Expected Outputs</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Efficiency / Quantity</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Quality / Effectiveness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeliness</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-20">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400"
                                       placeholder="e.g. Reports prepared">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400">
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <input type="text" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 placeholder-gray-400">
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-center">
                                <button class="text-red-400 hover:text-red-300 text-sm font-medium transition-colors duration-200">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button class="flex items-center text-sm text-blue-400 hover:text-blue-300 font-medium transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Support Output
            </button>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 p-4 bg-gray-800 border border-gray-700 rounded-lg">
            <div class="flex items-center text-sm text-gray-400">
                <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Once approved, this UWP will be locked and used to generate IPCRs.
            </div>

            <div class="flex gap-3">
                <button class="px-5 py-2.5 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    Save as Draft
                </button>
                <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-800 transition-colors duration-200">
                    Submit for Approval
                </button>
            </div>
        </div>

    </div>
</x-layouts.employee>