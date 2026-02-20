@extends('layouts.supervisor')

@section('main-content')

    @php
        $periodName = $activePeriod?->name ?? 'No Active Period';
        $officeName = $user->supervisedOffice?->name ?? $user->office?->name ?? 'Office';
    @endphp

    <!-- 1. Welcome & Role Context -->
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">
                    Supervisor Overview
                </h2>
                <p class="text-gray-300">
                    {{ $officeName }} · Team Oversight & Validation
                </p>
                <p class="text-gray-400 text-sm mt-1">
                    Performance Period: <span class="text-gray-200 font-medium">{{ $periodName }}</span>
                </p>
            </div>

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
            <p class="mt-2 text-3xl font-bold text-white">{{ $teamMembersCount }}</p>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-blue-900/20 to-blue-800/10 border border-blue-700/30 shadow-lg">
            <h3 class="text-sm text-blue-300">Active Tasks</h3>
            <p class="mt-2 text-3xl font-bold text-white">{{ $activeTasksCount }}</p>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-amber-900/20 to-amber-800/10 border border-amber-700/30 shadow-lg">
            <h3 class="text-sm text-amber-300">Pending Validation</h3>
            <p class="mt-2 text-3xl font-bold text-white">{{ $pendingValidationCount }}</p>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-red-900/20 to-red-800/10 border border-red-700/30 shadow-lg">
            <h3 class="text-sm text-red-300">Overdue Tasks</h3>
            <p class="mt-2 text-3xl font-bold text-white">{{ $overdueCount }}</p>
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
            <div class="h-[220px]">
                <canvas id="taskStatusChart"></canvas>
            </div>
        </div>

        <!-- Weekly Output Trend -->
        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line text-emerald-400"></i>
                Weekly Team Output
            </h3>
            <div class="h-[220px]">
                <canvas id="weeklyOutputChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 4. Attention Required -->
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-amber-400"></i>
            Attention Required
        </h3>

        <div class="space-y-4">
            <div class="p-4 rounded-lg bg-amber-900/20 border border-amber-800/40">
                <p class="text-white font-medium">{{ $overdueCount }} tasks overdue</p>
                <p class="text-sm text-amber-300">
                    Immediate follow-up recommended
                </p>
            </div>

            <div class="p-4 rounded-lg bg-blue-900/20 border border-blue-800/40">
                <p class="text-white font-medium">{{ $pendingValidationCount }} tasks awaiting validation</p>
                <p class="text-sm text-blue-300">
                    Review submissions to avoid backlog
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const textColor = 'rgba(229,231,235,0.95)';
                const gridColor = 'rgba(255,255,255,0.08)';

                const taskStatusLabels = @json($taskStatusLabels ?? []);
                const taskStatusData = @json($taskStatusData ?? []);

                const weeklyLabels = @json($weeklyLabels ?? []);
                const weeklyCounts = @json($weeklyCounts ?? []);

                const taskCanvas = document.getElementById('taskStatusChart');
                if (taskCanvas) {
                    new Chart(taskCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: taskStatusLabels,
                            datasets: [{
                                data: taskStatusData,
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { labels: { color: textColor } }
                            }
                        }
                    });
                }

                const weeklyCanvas = document.getElementById('weeklyOutputChart');
                if (weeklyCanvas) {
                    new Chart(weeklyCanvas, {
                        type: 'line',
                        data: {
                            labels: weeklyLabels,
                            datasets: [{
                                label: 'Submitted / Rated Entries',
                                data: weeklyCounts,
                                tension: 0.35,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { labels: { color: textColor } }
                            },
                            scales: {
                                x: { ticks: { color: textColor }, grid: { color: gridColor } },
                                y: { ticks: { color: textColor }, grid: { color: gridColor }, beginAtZero: true }
                            }
                        }
                    });
                }
            })();
        </script>
    @endpush

@endsection
