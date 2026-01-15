<x-layouts.employee>
    <section class="space-y-6">

        {{-- Header / Status --}}
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-white">Output Details (Read-only)</h1>
                <p class="text-sm text-gray-400 mt-1">
                    My Tasks viewer only. All edits, uploads, and submissions are performed inside ORS.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-blue-900 px-3 py-1 text-xs font-semibold text-blue-200 border border-blue-700">
                    Submitted (Locked)
                </span>
                <span class="inline-flex items-center rounded-full bg-blue-900/40 px-3 py-1 text-[11px] font-semibold text-blue-200 border border-blue-800">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.306 0 2.417-.835 2.83-2H21a1 1 0 011 1v8a2 2 0 01-2 2H4a2 2 0 01-2-2v-8a1 1 0 011-1h6.17A3.001 3.001 0 0112 11z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5a3 3 0 00-6 0v4h6V5z"/>
                    </svg>
                    Locked / In MPOR
                </span>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 space-y-6">

            {{-- Lock awareness --}}
            <div class="rounded-lg border border-blue-800 bg-blue-900/30 p-4 text-sm text-blue-100 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.306 0 2.417-.835 2.83-2H21a1 1 0 011 1v8a2 2 0 01-2 2H4a2 2 0 01-2-2v-8a1 1 0 011-1h6.17A3.001 3.001 0 0112 11z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5a3 3 0 00-6 0v4h6V5z"/>
                </svg>
                <div>
                    <p class="font-semibold text-blue-100">This output is locked and cannot be modified here.</p>
                    <p class="text-xs text-blue-200 mt-1">Submission and edits are handled inside ORS. My Tasks/Submit Output are read-only mirrors.</p>
                </div>
            </div>

            {{-- Selected Task Context --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Selected Task</h3>
                        <p class="text-sm text-gray-400">Context pulled from ORS. Task selection is locked.</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded bg-blue-900 text-blue-200 border border-blue-800">
                        Submitted
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm">
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Task Name</p>
                        <p class="font-medium text-white">E-Bank Scanning</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Client Name</p>
                        <p class="font-medium text-white">ABC Corp</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Task Date</p>
                        <p class="font-medium text-white">Aug 15, 2025</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Tracking Status</p>
                        <p class="font-medium text-white">Stopped at 10:42 (submitted)</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Auto-logged Window</p>
                        <p class="font-medium text-white">09:12 - 10:42</p>
                    </div>
                </div>
            </div>

            {{-- Output Details (read-only) --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Client Request ID</label>
                        <input type="text" value="REQ-2025-01234" disabled class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Output Type</label>
                        <input type="text" value="Bank Statement Form (BSF-01)" disabled class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Completion Date</label>
                        <input type="text" value="Aug 15, 2025" disabled class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Confidentiality Level</label>
                        <input type="text" value="Standard" disabled class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Auto-Logged Start</label>
                        <input type="text" value="09:12 AM" disabled class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Auto-Logged End</label>
                        <input type="text" value="10:42 AM" disabled class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5">
                    </div>
                </div>
            </div>

            {{-- Uploaded Files (read-only) --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Uploaded Output</h3>
                    <span class="text-xs text-gray-400">Uploads managed in ORS.</span>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between bg-gray-700 rounded-lg p-3 text-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-white">report_q3_2025.pdf</p>
                                <p class="text-xs text-gray-400">Uploaded: Aug 12, 2025 | 2.4 MB</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">Locked</span>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-400">Upload changes and removals are performed in ORS.</p>
            </div>

            {{-- Remarks & Details (read-only) --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Additional Details
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Remarks / Notes</label>
                        <textarea rows="3" disabled class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-3 placeholder-gray-400">Provide additional context, special instructions, or notes about this output...</textarea>
                        <p class="mt-1 text-xs text-gray-500">Submission handled in ORS.</p>
                    </div>
                </div>
            </div>

            {{-- Audit / Timeline --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-white">Audit / Timeline</h3>
                    <span class="text-xs text-gray-400">Read-only</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">ORS Logged</p>
                        <p class="font-medium text-white">Aug 15, 2025 | 09:12 AM</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Submitted</p>
                        <p class="font-medium text-white">Aug 15, 2025 | 10:45 AM</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Supervisor Validation</p>
                        <p class="font-medium text-white">Pending review</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-700">
                <div class="flex items-center text-sm text-gray-400">
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    All edits and submissions are performed inside ORS.
                </div>
                <div class="flex gap-3">
                    <button type="button" disabled class="cursor-not-allowed inline-flex items-center gap-2 px-5 py-2.5 border border-gray-700 text-gray-500 rounded-lg bg-gray-800">
                        Submission handled in ORS
                    </button>
                    <a href="{{ route('employee.ors') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-800 transition-colors duration-200 flex items-center">
                        Open in ORS
                    </a>
                </div>
            </div>

        </div>

    </section>
</x-layouts.employee>
