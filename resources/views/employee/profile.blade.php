<x-layouts.employee>
    <section class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-white">Profile & Security</h1>
            <p class="text-sm text-gray-400 mt-1">
                Manage your account information and security settings.
            </p>
        </div>

        {{-- Profile Card --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile Information
                </h2>
                <span class="px-3 py-1 text-xs rounded bg-blue-900 text-blue-300 border border-blue-800">
                    VERIFIED
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Profile Picture --}}
                <div class="lg:col-span-1 flex flex-col items-center">
                    <div class="relative mb-4">
                        <img class="w-32 h-32 rounded-full border-4 border-gray-700 object-cover" 
                             src="https://ui-avatars.com/api/?name=Juan+Dela+Cruz&background=1e40af&color=fff&size=128" 
                             alt="Profile picture">
                        <button class="absolute bottom-2 right-2 p-2 bg-blue-600 hover:bg-blue-700 rounded-full text-white transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-400">Max size: 5MB</p>
                    <p class="text-xs text-gray-500">JPG, PNG, or GIF</p>
                </div>

                {{-- Profile Form --}}
                <div class="lg:col-span-3 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Full Name</label>
                            <input type="text" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                                   value="Juan Dela Cruz" disabled>
                            <p class="mt-1 text-xs text-gray-400">Name updates require HR approval</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Email Address</label>
                            <div class="flex items-center">
                                <input type="email" 
                                       class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                       value="juan.delacruz@email.com">
                                <span class="ml-2 px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300 border border-emerald-800">
                                    Verified
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-gray-400">Used for notifications and login</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Position</label>
                            <input type="text" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                                   value="Administrative Assistant I" disabled>
                            <p class="mt-1 text-xs text-gray-400">Updated by HR</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Employee ID</label>
                            <input type="text" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                                   value="EMP-2023-04567" disabled>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Department</label>
                            <input type="text" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                                   value="Provincial Human Resource Management Office" disabled>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-white">Phone Number</label>
                            <input type="tel" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                   placeholder="+63 912 345 6789">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-700">
                        <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Profile Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Security Card --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 space-y-6">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Security Settings
            </h2>

            {{-- Password Change --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <h3 class="font-medium text-white mb-4">Change Password</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Current Password</label>
                        <div class="relative">
                            <input type="password" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                   placeholder="Enter current password">
                            <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">New Password</label>
                        <div class="relative">
                            <input type="password" 
                                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                   placeholder="Enter new password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <div class="flex space-x-1">
                                    <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                                    <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                                    <div class="w-1 h-1 rounded-full bg-gray-500"></div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Minimum 8 characters with letters and numbers</p>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-white">Confirm New Password</label>
                        <input type="password" 
                               class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                               placeholder="Confirm new password">
                    </div>

                    <div class="pt-4">
                        <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Update Password
                        </button>
                    </div>
                </div>
            </div>

            {{-- Security Features --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Two-Factor Authentication --}}
                <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium text-white">Two-Factor Authentication</h3>
                        <span class="px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300">Enabled</span>
                    </div>
                    <p class="text-sm text-gray-400 mb-4">Add an extra layer of security to your account</p>
                    <div class="flex items-center text-sm text-gray-400 mb-4">
                        <svg class="w-4 h-4 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Last used: Today, 2:30 PM
                    </div>
                    <button class="w-full px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        Manage 2FA
                    </button>
                </div>

                {{-- Session Management --}}
                <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium text-white">Active Sessions</h3>
                        <span class="px-2 py-1 text-xs rounded bg-blue-900 text-blue-300">2 Active</span>
                    </div>
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></div>
                            <div>
                                <p class="text-sm text-white">Chrome • Windows</p>
                                <p class="text-xs text-gray-400">Current session • Manila, PH</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></div>
                            <div>
                                <p class="text-sm text-white">Safari • iOS</p>
                                <p class="text-xs text-gray-400">3 hours ago • Quezon City, PH</p>
                            </div>
                        </div>
                    </div>
                    <button class="w-full px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        View All Sessions
                    </button>
                </div>
            </div>

            {{-- Login History --}}
            <div class="bg-gray-750 border border-gray-600 rounded-lg p-5">
                <h3 class="font-medium text-white mb-4">Recent Login History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-300">Date & Time</th>
                                <th class="px-4 py-2 text-left text-gray-300">Device</th>
                                <th class="px-4 py-2 text-left text-gray-300">Location</th>
                                <th class="px-4 py-2 text-left text-gray-300">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <tr>
                                <td class="px-4 py-2 text-gray-300">Aug 13, 2025 • 14:30</td>
                                <td class="px-4 py-2 text-gray-300">Chrome • Windows</td>
                                <td class="px-4 py-2 text-gray-300">Manila, PH</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300">Success</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-gray-300">Aug 13, 2025 • 09:15</td>
                                <td class="px-4 py-2 text-gray-300">Safari • iOS</td>
                                <td class="px-4 py-2 text-gray-300">Quezon City, PH</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300">Success</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Account Actions --}}
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.342 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Account Actions
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button class="p-4 border border-gray-600 rounded-lg hover:bg-gray-750 transition-colors duration-200 text-left">
                    <h3 class="font-medium text-white mb-1">Export Account Data</h3>
                    <p class="text-sm text-gray-400">Download all your personal data in ZIP format</p>
                </button>
                <button class="p-4 border border-red-600/50 rounded-lg hover:bg-red-900/20 transition-colors duration-200 text-left">
                    <h3 class="font-medium text-white mb-1">Deactivate Account</h3>
                    <p class="text-sm text-gray-400">Temporarily disable your account</p>
                </button>
            </div>
        </div>

    </section>
</x-layouts.employee>