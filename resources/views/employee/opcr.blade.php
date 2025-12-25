<x-layouts.employee>
    <section class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-xl font-semibold">
                    Office Performance Commitment and Review (OPCR)
                </h1>
                <p class="text-sm text-gray-500">
                    System-generated summary based on approved IPCRs
                </p>
            </div>

            <span class="px-3 py-1 text-xs rounded bg-emerald-100 text-emerald-800">
                GENERATED
            </span>
        </div>

        {{-- BASIC INFORMATION --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 border rounded-lg p-4">
            <div>
                <label class="text-sm font-medium">Office / Division</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="Provincial Human Resource Management Office" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Period Covered</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="January – June 2025" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Total Employees</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="18" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Overall Rating</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="4.32 (Very Satisfactory)" disabled>
            </div>
        </div>

        {{-- PERFORMANCE SUMMARY --}}
        <div class="border rounded-lg p-4 space-y-3">
            <h2 class="font-semibold text-lg">Performance Summary</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 w-1/4">Major Final Output</th>
                            <th class="border px-3 py-2">Success Indicators</th>
                            <th class="border px-3 py-2">Target</th>
                            <th class="border px-3 py-2">Actual Accomplishment</th>
                            <th class="border px-3 py-2 w-24">Avg. Rating</th>
                            <th class="border px-3 py-2 w-32">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Generated row --}}
                        <tr>
                            <td class="border px-3 py-2">
                                HR Records Management
                            </td>
                            <td class="border px-3 py-2">
                                100% records updated, accurate, timely
                            </td>
                            <td class="border px-3 py-2">
                                100%
                            </td>
                            <td class="border px-3 py-2">
                                98% completed within period
                            </td>
                            <td class="border px-3 py-2 text-center">
                                4.4
                            </td>
                            <td class="border px-3 py-2">
                                Very Satisfactory
                            </td>
                        </tr>

                        <tr>
                            <td class="border px-3 py-2">
                                Administrative Support Services
                            </td>
                            <td class="border px-3 py-2">
                                Reports submitted, compliant
                            </td>
                            <td class="border px-3 py-2">
                                100%
                            </td>
                            <td class="border px-3 py-2">
                                100% submitted on time
                            </td>
                            <td class="border px-3 py-2 text-center">
                                4.6
                            </td>
                            <td class="border px-3 py-2">
                                Outstanding
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RATING DISTRIBUTION (OPTIONAL BUT NICE) --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="border rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500">Outstanding</p>
                <p class="text-2xl font-semibold">6</p>
            </div>
            <div class="border rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500">Very Satisfactory</p>
                <p class="text-2xl font-semibold">8</p>
            </div>
            <div class="border rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500">Satisfactory</p>
                <p class="text-2xl font-semibold">3</p>
            </div>
            <div class="border rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500">Unsatisfactory</p>
                <p class="text-2xl font-semibold">1</p>
            </div>
            <div class="border rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500">Poor</p>
                <p class="text-2xl font-semibold">0</p>
            </div>
        </div>

        {{-- SIGNATORIES --}}
        <div class="border rounded-lg p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-medium">Prepared By</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="System Generated" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Reviewed By</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="HRMO / PMT" disabled>
            </div>

            <div>
                <label class="text-sm font-medium">Approved By</label>
                <input type="text" class="w-full mt-1 border rounded px-3 py-2"
                       value="Office Head" disabled>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-2">
            <button class="px-4 py-2 border rounded">
                Export PDF
            </button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Submit to HR
            </button>
        </div>

    </section>
</x-layouts.employee>
