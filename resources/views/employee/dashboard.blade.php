@extends('layouts.employee')

@section('main-content')        <!-- 1. Welcome & Status -->
        <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">Welcome, Ramon Reyes</h2>
                    <p class="text-gray-300">Records Management Officer - Revenue Collection Unit</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 rounded-full bg-gradient-to-r from-green-900/30 to-green-800/30 border border-green-700/50 text-green-300 text-sm font-medium flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        Active
                    </span>
                    <span class="px-4 py-2 rounded-full bg-gradient-to-r from-purple-900/30 to-blue-900/30 border border-purple-700/50 text-purple-300 text-sm font-medium">
                        ID: EMP-2024-0123
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. Today's Work Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Assigned Tasks</h3>
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-900/30 to-blue-800/20 flex items-center justify-center">
                        <i class="fas fa-tasks text-blue-400"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white mb-2">5</div>
                <p class="text-sm text-gray-400">Assigned for Dec 22–28</p>
            </div>

            <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">In Progress</h3>
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-900/30 to-amber-800/20 flex items-center justify-center">
                        <i class="fas fa-spinner text-amber-400"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white mb-2">2</div>
                <p class="text-sm text-gray-400">E-Bank Scanning, Data Entry</p>
            </div>

            <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Completed</h3>
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-900/30 to-green-800/20 flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white mb-2">1</div>
                <p class="text-sm text-gray-400">Client Form Review</p>
            </div>
        </div>

        <!-- 3. Task Status Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-purple-400"></i>
                    Task Status Overview
                </h3>

                <div class="space-y-4">
                    <!-- Task 1 -->
                    <div class="flex items-center justify-between p-4 rounded-lg bg-gray-800/50 border border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-900/20 to-amber-800/10 flex items-center justify-center">
                                <i class="fas fa-file-scan text-amber-400"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-white">E-Bank Scanning</h4>
                                <p class="text-sm text-gray-400">Due: Today 5:00 PM · Tracking active</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-amber-900/30 text-amber-300 text-sm font-medium border border-amber-800/50">
                            In Progress
                        </span>
                    </div>

                    <!-- Task 2 -->
                    <div class="flex items-center justify-between p-4 rounded-lg bg-gray-800/50 border border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-900/20 to-green-800/10 flex items-center justify-center">
                                <i class="fas fa-file-check text-green-400"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-white">Client Form Review</h4>
                                <p class="text-sm text-gray-400">Completed: 2 hours ago</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-green-900/30 text-green-300 text-sm font-medium border border-green-800/50">
                            Completed
                        </span>
                    </div>

                    <!-- Task 3 -->
                    <div class="flex items-center justify-between p-4 rounded-lg bg-gray-800/50 border border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-900/20 to-blue-800/10 flex items-center justify-center">
                                <i class="fas fa-file-alt text-blue-400"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-white">Report Generation</h4>
                                <p class="text-sm text-gray-400">Submitted: Awaiting validation</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-blue-900/30 text-blue-300 text-sm font-medium border border-blue-800/50">
                            Pending
                        </span>
                    </div>
                </div>
            </div>

            <!-- 4. Alerts & Reminders -->
            <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-bell text-red-400"></i>
                    Alerts & Reminders
                </h3>

                <div class="space-y-4">
                    <!-- Alert 1 -->
                    <div class="p-4 rounded-lg bg-gradient-to-r from-amber-900/20 to-amber-800/10 border border-amber-800/30">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-amber-400 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-white mb-1">Missing Output</h4>
                                <p class="text-sm text-gray-300">Missing output for <span class="text-amber-300 font-medium">Client Follow-up (REQ-2025-018)</span>. Submit and link in ORS.</p>
                                <p class="text-xs text-amber-400 mt-2">Priority: High - Overdue by 2 days</p>
                            </div>
                        </div>
                    </div>

                    <!-- Alert 2 -->
                    <div class="p-4 rounded-lg bg-gradient-to-r from-blue-900/20 to-blue-800/10 border border-blue-800/30">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-alt text-blue-400 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-white mb-1">SMPOR Deadline</h4>
                                <p class="text-sm text-gray-300">SMPOR cutoff for Q4 logs is in <span class="text-blue-300 font-medium">3 days</span>. Finalize outputs before lock.</p>
                                <p class="text-xs text-blue-400 mt-2">Due: Dec 29, 2025 - HRMO</p>
                            </div>
                        </div>
                    </div>

                    <!-- Alert 3 -->
                    <div class="p-4 rounded-lg bg-gradient-to-r from-green-900/20 to-green-800/10 border border-green-800/30">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chart-line text-green-400 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-white mb-1">Performance Review</h4>
                                <p class="text-sm text-gray-300">Monthly performance review scheduled for <span class="text-green-300 font-medium">next week</span>.</p>
                                <p class="text-xs text-green-400 mt-2">Schedule: Dec 30, 2025 - 2:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4b. Auto-Logging & Data Integrity -->
        <div class="mb-8 p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-clock text-emerald-400"></i>
                        Auto-Logging & Data Integrity
                    </h3>
                <span class="px-3 py-1 text-xs rounded bg-emerald-900 text-emerald-300 border border-emerald-800">
                    LIVE TRACKING
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="rounded-lg border border-gray-700 bg-gray-800/50 p-4">
                    <p class="text-gray-400">Active task tracking</p>
                    <p class="mt-1 text-lg font-semibold text-white">1 running (E-Bank Scanning)</p>
                    <p class="text-xs text-gray-500">Auto-started from My Tasks</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-gray-800/50 p-4">
                    <p class="text-gray-400">Linked outputs</p>
                    <p class="mt-1 text-lg font-semibold text-white">4 of 5</p>
                    <p class="text-xs text-gray-500">Missing links trigger alerts</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-gray-800/50 p-4">
                    <p class="text-gray-400">Incomplete fields</p>
                    <p class="mt-1 text-lg font-semibold text-amber-300">1 flagged</p>
                    <p class="text-xs text-gray-500">Waiting for submission</p>
                </div>
            </div>

            <p class="mt-4 text-xs text-gray-400">
                Start/end times and output links are captured automatically to keep performance stats objective.
            </p>
        </div>

        <!-- 5. Performance Snapshot -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="p-6 rounded-xl bg-gradient-to-br from-purple-900/20 to-purple-800/10 border border-purple-700/30 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Daily Rating</h3>
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-900/30 to-purple-800/20 flex items-center justify-center">
                        <i class="fas fa-sun text-purple-400"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white mb-2">4.5 <span class="text-xl text-gray-400">/ 5</span></div>
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-2 bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-purple-500 to-purple-600 rounded-full" style="width: 90%"></div>
                    </div>
                    <span class="text-sm text-green-400">+0.2</span>
                </div>
            </div>

            <div class="p-6 rounded-xl bg-gradient-to-br from-blue-900/20 to-blue-800/10 border border-blue-700/30 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Weekly Rating</h3>
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-900/30 to-blue-800/20 flex items-center justify-center">
                        <i class="fas fa-calendar-week text-blue-400"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white mb-2">4.3 <span class="text-xl text-gray-400">/ 5</span></div>
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-2 bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full" style="width: 86%"></div>
                    </div>
                    <span class="text-sm text-blue-400">Consistent</span>
                </div>
            </div>

            <div class="p-6 rounded-xl bg-gradient-to-br from-green-900/20 to-green-800/10 border border-green-700/30 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Within Baseline</h3>
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-900/30 to-green-800/20 flex items-center justify-center">
                        <i class="fas fa-chart-bar text-green-400"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-white mb-2">92%</div>
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-2 bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-green-500 to-green-600 rounded-full" style="width: 92%"></div>
                    </div>
                    <span class="text-sm text-green-400">+5%</span>
                </div>
            </div>
        </div>

        <!-- 6. Quick Actions -->
        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-bolt text-yellow-400"></i>
                Quick Actions
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#" class="p-4 rounded-lg bg-gradient-to-r from-blue-900/20 to-blue-800/10 border border-blue-700/30 hover:border-blue-500/50 transition-all duration-300 group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-900/30 to-blue-800/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-tasks text-blue-400"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-white">View My Tasks</h4>
                            <p class="text-xs text-gray-400">Check assigned work</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="p-4 rounded-lg bg-gradient-to-r from-green-900/20 to-green-800/10 border border-green-700/30 hover:border-green-500/50 transition-all duration-300 group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-900/30 to-green-800/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-upload text-green-400"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-white">Submit Output</h4>
                            <p class="text-xs text-gray-400">Upload completed work</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="p-4 rounded-lg bg-gradient-to-r from-purple-900/20 to-purple-800/10 border border-purple-700/30 hover:border-purple-500/50 transition-all duration-300 group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-900/30 to-purple-800/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-bar text-purple-400"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-white">View ORS</h4>
                            <p class="text-xs text-gray-400">Output Rating Sheet</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="p-4 rounded-lg bg-gradient-to-r from-amber-900/20 to-amber-800/10 border border-amber-700/30 hover:border-amber-500/50 transition-all duration-300 group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-900/30 to-amber-800/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-bullseye text-amber-400"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-white">Development Plan</h4>
                            <p class="text-xs text-gray-400">IDP progress</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- 7. Recent Activity (Bonus) -->
        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg mt-6">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-history text-gray-400"></i>
                Recent Activity
            </h3>

            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-800/30">
                    <div class="w-8 h-8 rounded-full bg-green-900/20 flex items-center justify-center">
                        <i class="fas fa-check text-green-400 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-white">Submitted output for <span class="text-green-300">Client Form Review</span></p>
                        <p class="text-xs text-gray-400">2 hours ago - Rating: 4.5/5.0</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-800/30">
                    <div class="w-8 h-8 rounded-full bg-blue-900/20 flex items-center justify-center">
                        <i class="fas fa-tasks text-blue-400 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-white">Tracking started on <span class="text-blue-300">E-Bank Scanning</span></p>
                        <p class="text-xs text-gray-400">4 hours ago - In Progress</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-800/30">
                    <div class="w-8 h-8 rounded-full bg-purple-900/20 flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-400 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-white">Weekly rating recalculated to <span class="text-purple-300">4.3</span></p>
                        <p class="text-xs text-gray-400">Yesterday - +0.2 improvement</p>
                    </div>
                </div>
            </div>
        </div>
@endsection
