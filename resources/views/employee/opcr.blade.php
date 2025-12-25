<x-layouts.employee>
    <section class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Office Performance Commitment and Review (OPCR)
                </h1>
                <p class="text-sm text-gray-400 mt-1">
                    System-generated summary based on approved IPCRs
                </p>
            </div>

            <span class="px-3 py-1 text-xs font-medium rounded bg-emerald-900 text-emerald-300 border border-emerald-800">
                GENERATED
            </span>
        </div>

        {{-- BASIC INFORMATION --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div>
                <label class="block mb-2 text-sm font-medium text-white">Office / Division</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Provincial Human Resource Management Office" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Period Covered</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="January – June 2025" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Total Employees</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="18" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Overall Rating</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="4.32 (Very Satisfactory)" disabled>
            </div>
        </div>

        {{-- PERFORMANCE SUMMARY --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <h2 class="font-semibold text-lg text-white">Performance Summary</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-1/4">Major Final Output</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Success Indicators</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Target</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Actual Accomplishment</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-24">Avg. Rating</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white w-32">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        {{-- Generated row --}}
                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                HR Records Management
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                100% records updated, accurate, timely
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                100%
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                98% completed within period
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">
                                4.4
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-900 text-emerald-300">
                                    Very Satisfactory
                                </span>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-750">
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Administrative Support Services
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                Reports submitted, compliant
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                100%
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-gray-300">
                                100% submitted on time
                            </td>
                            <td class="border border-gray-700 px-4 py-3 text-center text-white font-semibold">
                                4.6
                            </td>
                            <td class="border border-gray-700 px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900 text-blue-300">
                                    Outstanding
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RATING DISTRIBUTION --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 text-center hover:bg-gray-750 transition-colors duration-200">
                <p class="text-sm text-gray-400">Outstanding</p>
                <p class="text-3xl font-bold text-white mt-2">6</p>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 text-center hover:bg-gray-750 transition-colors duration-200">
                <p class="text-sm text-gray-400">Very Satisfactory</p>
                <p class="text-3xl font-bold text-white mt-2">8</p>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 text-center hover:bg-gray-750 transition-colors duration-200">
                <p class="text-sm text-gray-400">Satisfactory</p>
                <p class="text-3xl font-bold text-white mt-2">3</p>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 text-center hover:bg-gray-750 transition-colors duration-200">
                <p class="text-sm text-gray-400">Unsatisfactory</p>
                <p class="text-3xl font-bold text-white mt-2">1</p>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 text-center hover:bg-gray-750 transition-colors duration-200">
                <p class="text-sm text-gray-400">Poor</p>
                <p class="text-3xl font-bold text-white mt-2">0</p>
            </div>
        </div>

        {{-- SIGNATORIES --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block mb-2 text-sm font-medium text-white">Prepared By</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="System Generated" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Reviewed By</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="HRMO / PMT" disabled>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">Approved By</label>
                <input type="text" 
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                       value="Office Head" disabled>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3">
            <button class="px-5 py-2.5 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                Export PDF
            </button>
            <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-800 transition-colors duration-200">
                Submit to HR
            </button>
        </div>

    </section>
</x-layouts.employee>