<x-layouts.employee>
    <section class="space-y-6">

        {{-- Progress Indicator --}}
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-2xl font-bold text-white">Submit Output</h1>
            <div class="flex items-center space-x-2 text-sm text-gray-400">
                <span class="flex items-center">
                    <span class="flex h-2.5 w-2.5 rounded-full bg-blue-500 mr-2"></span>
                    <span class="font-medium text-white">1. Select Task</span>
                </span>
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="flex items-center">
                    <span class="flex h-2.5 w-2.5 rounded-full bg-gray-600 mr-2"></span>
                    <span>2. Upload File</span>
                </span>
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="flex items-center">
                    <span class="flex h-2.5 w-2.5 rounded-full bg-gray-600 mr-2"></span>
                    <span>3. Confirm</span>
                </span>
            </div>
        </div>

        <p class="text-sm text-gray-400 mb-6">
            Upload completed outputs for validation and review. All submissions are logged for IPCR tracking.
        </p>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 space-y-6">

            {{-- Task Selection Card --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Select Related Task
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Task Cards --}}
                    <div class="border border-gray-600 rounded-lg p-4 hover:border-blue-500 hover:bg-gray-700 transition-all duration-200 cursor-pointer group">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-white group-hover:text-blue-300">Report Generation</h4>
                                <p class="text-sm text-gray-400">ABC Corp</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded bg-blue-900 text-blue-300">Due: Aug 15</span>
                        </div>
                        <p class="text-sm text-gray-400 mb-3">Complete monthly financial report for Q3 2025</p>
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Started: Aug 10, 2025</span>
                        </div>
                    </div>

                    <div class="border border-gray-600 rounded-lg p-4 hover:border-blue-500 hover:bg-gray-700 transition-all duration-200 cursor-pointer group">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-white group-hover:text-blue-300">Client Form Review</h4>
                                <p class="text-sm text-gray-400">XYZ Ltd</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300">Completed</span>
                        </div>
                        <p class="text-sm text-gray-400 mb-3">Review and validate client registration forms</p>
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Completed: Aug 8, 2025</span>
                        </div>
                    </div>
                </div>

                {{-- Manual Selection Fallback --}}
                <div class="mt-6">
                    <label class="block mb-2 text-sm font-medium text-white">Or select manually:</label>
                    <select class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option class="bg-gray-700">Select task from list</option>
                        <option class="bg-gray-700">Report Generation – ABC Corp</option>
                        <option class="bg-gray-700">Client Form Review – XYZ Ltd</option>
                        <option class="bg-gray-700">Data Analysis – DEF Inc</option>
                        <option class="bg-gray-700">Presentation Preparation – GHI Co</option>
                    </select>
                </div>
            </div>

            {{-- File Upload Card --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Output File
                </h3>

                {{-- Upload Area --}}
                <div class="mb-6">
                    <div class="flex items-center justify-center w-full">
                        <label for="file-upload" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-600 border-dashed rounded-xl cursor-pointer bg-gray-700 hover:bg-gray-750 hover:border-blue-500 transition-all duration-200 group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="mb-1 text-sm text-gray-400 group-hover:text-white">
                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, PPT, JPG, PNG (MAX. 20MB)</p>
                            </div>
                            <input id="file-upload" type="file" class="hidden" />
                        </label>
                    </div>
                </div>

                {{-- Recent Uploads --}}
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-white mb-3">Recent Uploads</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-gray-700 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-white">report_q3_2025.pdf</p>
                                    <p class="text-xs text-gray-400">Uploaded: Aug 12, 2025 • 2.4 MB</p>
                                </div>
                            </div>
                            <button class="text-red-400 hover:text-red-300 text-sm">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                {{-- File Info --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Maximum File Size</p>
                        <p class="font-medium text-white">20 MB</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Supported Formats</p>
                        <p class="font-medium text-white">8 types</p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-gray-400 mb-1">Encryption</p>
                        <p class="font-medium text-white">256-bit SSL</p>
                    </div>
                </div>
            </div>

            {{-- Remarks & Details --}}
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
                        <textarea rows="3"
                                class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 placeholder-gray-400"
                                placeholder="Provide additional context, special instructions, or notes about this output..."></textarea>
                        <p class="mt-1 text-xs text-gray-400">Maximum 500 characters</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Completion Date</label>
                            <input type="date" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Confidentiality Level</label>
                            <select class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option class="bg-gray-700">Standard</option>
                                <option class="bg-gray-700">Confidential</option>
                                <option class="bg-gray-700">Internal Use Only</option>
                                <option class="bg-gray-700">Public</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-700">
                <div class="flex items-center text-sm text-gray-400">
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Submitted outputs are subject to supervisor review and contribute to IPCR ratings.
                </div>

                <div class="flex gap-3">
                    <button class="px-5 py-2.5 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        Save as Draft
                    </button>
                    <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-800 transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Submit for Review
                    </button>
                </div>
            </div>

        </div>

    </section>
</x-layouts.employee>