<x-layouts.admin>
    <section class="space-y-8">

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-white">System Dashboard</h1>
                <p class="text-sm text-slate-400">Overall system status and performance overview</p>
            </div>
            <div class="text-sm text-slate-400">Last Updated: Today, 10:30 AM</div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <p class="text-xs uppercase tracking-widest text-slate-400">Total Users</p>
                <p class="mt-2 text-3xl font-semibold text-white">1,247</p>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <p class="text-xs uppercase tracking-widest text-slate-400">Active Employees</p>
                <p class="mt-2 text-3xl font-semibold text-white">856</p>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <p class="text-xs uppercase tracking-widest text-slate-400">Tasks Logged Today</p>
                <p class="mt-2 text-3xl font-semibold text-white">312</p>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <p class="text-xs uppercase tracking-widest text-slate-400">System Health</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-400">OK</p>
            </div>

        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            <!-- Task Volume Chart -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                    Task Volume Over Time
                </h2>
                <canvas id="taskVolumeChart" height="120"></canvas>
            </div>

            <!-- Average Task Duration -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                    Average Task Duration
                </h2>
                <canvas id="taskDurationChart" height="120"></canvas>
            </div>

        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            <!-- Performance Distribution -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                    Performance Rating Distribution
                </h2>
                <canvas id="performanceChart" height="120"></canvas>
            </div>

            <!-- Bottleneck Tasks -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                    Top Bottleneck Tasks
                </h2>
                <canvas id="bottleneckChart" height="120"></canvas>
            </div>

        </div>

        <!-- Recent Activity -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                Recent System Activity
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Timestamp</th>
                            <th class="px-4 py-2 text-left">User</th>
                            <th class="px-4 py-2 text-left">Action</th>
                            <th class="px-4 py-2 text-left">Module</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-2">Today, 10:15 AM</td>
                            <td class="px-4 py-2">John Smith</td>
                            <td class="px-4 py-2">Completed Task #245</td>
                            <td class="px-4 py-2">Task Management</td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-2">Today, 09:45 AM</td>
                            <td class="px-4 py-2">Sarah Johnson</td>
                            <td class="px-4 py-2">Uploaded Report</td>
                            <td class="px-4 py-2">Document Management</td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-2">Today, 09:30 AM</td>
                            <td class="px-4 py-2">System Admin</td>
                            <td class="px-4 py-2">User Permission Updated</td>
                            <td class="px-4 py-2">User Management</td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-2">Today, 09:15 AM</td>
                            <td class="px-4 py-2">Mike Chen</td>
                            <td class="px-4 py-2">Started New Project</td>
                            <td class="px-4 py-2">Project Management</td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-2">Today, 08:45 AM</td>
                            <td class="px-4 py-2">Lisa Wong</td>
                            <td class="px-4 py-2">Created 5 New Tasks</td>
                            <td class="px-4 py-2">Task Management</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
    
    @push('scripts')
    <script>
        // Task Volume Chart (Line Chart)
        new Chart(document.getElementById('taskVolumeChart'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Tasks Completed',
                    data: [120, 190, 300, 250, 200, 150, 100],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Tasks Created',
                    data: [150, 220, 280, 230, 280, 180, 130],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#94a3b8'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });

        // Average Task Duration Chart (Bar Chart)
        new Chart(document.getElementById('taskDurationChart'), {
            type: 'bar',
            data: {
                labels: ['Data Entry', 'Code Review', 'Testing', 'Design', 'Documentation', 'Meeting', 'Research'],
                datasets: [{
                    label: 'Average Hours',
                    data: [2.5, 3.2, 4.1, 5.5, 2.8, 1.5, 3.8],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
                    ],
                    borderColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#8b5cf6',
                        '#ef4444',
                        '#0ea5e9',
                        '#ec4899'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#94a3b8'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        ticks: {
                            color: '#94a3b8'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // Performance Distribution Chart (Doughnut)
        new Chart(document.getElementById('performanceChart'), {
            type: 'doughnut',
            data: {
                labels: ['Excellent (5)', 'Good (4)', 'Average (3)', 'Below Avg (2)', 'Poor (1)'],
                datasets: [{
                    data: [35, 40, 15, 7, 3],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(220, 38, 38, 0.8)'
                    ],
                    borderColor: [
                        '#10b981',
                        '#22c55e',
                        '#f59e0b',
                        '#ef4444',
                        '#dc2626'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#94a3b8',
                            padding: 20
                        }
                    }
                }
            }
        });

        // Bottleneck Tasks Chart (Horizontal Bar)
        new Chart(document.getElementById('bottleneckChart'), {
            type: 'bar',
            data: {
                labels: ['Approval Process', 'Data Validation', 'System Integration', 'Quality Check', 'Client Review'],
                datasets: [{
                    label: 'Average Delay (Hours)',
                    data: [12.5, 8.3, 6.7, 5.2, 4.8],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ],
                    borderColor: [
                        '#ef4444',
                        '#f59e0b',
                        '#3b82f6',
                        '#10b981',
                        '#8b5cf6'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#94a3b8'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        ticks: {
                            color: '#94a3b8'
                        },
                        beginAtZero: true
                    },
                    y: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)'
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-layouts.admin>