<x-layouts.employee>
    <section class="space-y-6">

        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-white">Individual Development Plan (IDP)</h1>
            <p class="text-sm text-gray-400 mt-1">
                Plan your learning, growth, and career development.
            </p>
        </div>

        <!-- IDP Form Card -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-6 space-y-6">
            
            <!-- Development Objective -->
            <div>
                <label for="objective" class="block mb-2 text-sm font-medium text-white">
                    Development Objective
                </label>
                <input type="text" 
                       id="objective"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400"
                       placeholder="e.g. Improve data analysis skills">
            </div>

            <!-- Planned Activity -->
            <div>
                <label for="activity" class="block mb-2 text-sm font-medium text-white">
                    Planned Activity
                </label>
                <input type="text" 
                       id="activity"
                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400"
                       placeholder="e.g. Attend training / workshop">
            </div>

            <!-- Two-column Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Target Date -->
                <div>
                    <label for="target-date" class="block mb-2 text-sm font-medium text-white">
                        Target Date
                    </label>
                    <div class="relative">
                        <input type="date" 
                               id="target-date"
                               class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400">
                    </div>
                </div>

                <!-- Support Needed -->
                <div>
                    <label for="support" class="block mb-2 text-sm font-medium text-white">
                        Support Needed
                    </label>
                    <input type="text" 
                           id="support"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400"
                           placeholder="e.g. Manager approval, Budget allocation">
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end pt-4">
                <button type="button" 
                        class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none transition-colors duration-200">
                    Save IDP
                </button>
            </div>

        </div>

        <!-- Note -->
        <div class="flex items-center p-4 text-sm text-gray-300 bg-gray-800 border border-gray-700 rounded-lg" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3 text-blue-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium text-white">Note:</span> IDP is reviewed separately and does not affect IPCR ratings.
            </div>
        </div>

    </section>
</x-layouts.employee>