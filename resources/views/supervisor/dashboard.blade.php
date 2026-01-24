@extends('layouts.supervisor')

@section('main-content')

    <!-- 1. Welcome & Role Context -->
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">
                    Supervisor Overview
                </h2>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="text-gray-300">
                    IT Department · Team Oversight & Validation
                </p>
            </div>
            {{-- DUMMY_DATA: replace with dynamic value --}}
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <span class="px-4 py-2 rounded-full bg-gradient-to-r from-emerald-900/30 to-emerald-800/30 border border-emerald-700/50 text-emerald-300 text-sm font-medium flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Active Supervision
            </span>
        </div>
    </div>

    <!-- 2. Team Snapshot Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <h3 class="text-sm text-gray-400">Team Members</h3>
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <p class="mt-2 text-3xl font-bold text-white">8</p>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-blue-900/20 to-blue-800/10 border border-blue-700/30 shadow-lg">
            <h3 class="text-sm text-blue-300">Active Tasks</h3>
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <p class="mt-2 text-3xl font-bold text-white">21</p>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-amber-900/20 to-amber-800/10 border border-amber-700/30 shadow-lg">
            <h3 class="text-sm text-amber-300">Pending Validation</h3>
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <p class="mt-2 text-3xl font-bold text-white">5</p>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-red-900/20 to-red-800/10 border border-red-700/30 shadow-lg">
            <h3 class="text-sm text-red-300">Overdue Tasks</h3>
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <p class="mt-2 text-3xl font-bold text-white">3</p>
        </div>
    </div>

    <!-- 3. Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <!-- Task Status Distribution -->
        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-purple-400"></i>
                Task Status Distribution
            </h3>
            <canvas id="taskStatusChart" height="180"></canvas>
        </div>

        <!-- Weekly Output Trend -->
        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line text-emerald-400"></i>
                Weekly Team Output
            </h3>
            <canvas id="weeklyOutputChart" height="180"></canvas>
        </div>
    </div>

    <!-- 4. Attention Required -->
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-amber-400"></i>
            Attention Required
        </h3>

        <div class="space-y-4">
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <div class="p-4 rounded-lg bg-amber-900/20 border border-amber-800/40">
                <p class="text-white font-medium">3 tasks overdue</p>
                <p class="text-sm text-amber-300">
                    Immediate follow-up recommended
                </p>
            </div>

            {{-- DUMMY_DATA: replace with dynamic value --}}
            <div class="p-4 rounded-lg bg-blue-900/20 border border-blue-800/40">
                <p class="text-white font-medium">5 tasks awaiting validation</p>
                <p class="text-sm text-blue-300">
                    Review submissions to avoid backlog
                </p>
            </div>
        </div>
    </div>

    <!-- 5. Recent Team Activity -->
    <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <i class="fas fa-users text-gray-400"></i>
            Recent Team Activity
        </h3>

        <div class="space-y-3 text-sm">
            {{-- DUMMY_DATA: replace with dynamic value --}}
            <div class="p-3 rounded-lg bg-gray-800/40">
                <p class="text-white">
                    <span class="text-emerald-300">Maria S.</span> completed
                    <span class="text-gray-300">Client Form Review</span>
                </p>
                <p class="text-xs text-gray-400">1 hour ago</p>
            </div>

            {{-- DUMMY_DATA: replace with dynamic value --}}
            <div class="p-3 rounded-lg bg-gray-800/40">
                <p class="text-white">
                    <span class="text-amber-300">Juan D.</span> submitted output
                    for validation
                </p>
                <p class="text-xs text-gray-400">3 hours ago</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const textColor = '#e5e7eb';

        // Task Status Pie Chart
        new Chart(document.getElementById('taskStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending', 'Overdue'],
                datasets: [{
                    data: [12, 6, 3, 3],
                    backgroundColor: [
                        '#22c55e',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: { color: textColor }
                    }
                }
            }
        });

        // Weekly Output Line Chart
        new Chart(document.getElementById('weeklyOutputChart'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                datasets: [{
                    label: 'Outputs Submitted',
                    data: [8, 6, 9, 7, 10],
                    borderColor: '#34d399',
                    backgroundColor: 'rgba(52, 211, 153, 0.15)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: { color: textColor }
                    }
                },
                scales: {
                    x: { ticks: { color: textColor } },
                    y: { ticks: { color: textColor } }
                }
            }
        });
    </script>
    @endpush

@endsection
