@extends('layouts.dept-head')

@section('main-content')
    <section class="space-y-8">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">Department Head Dashboard</h1>
            <p class="text-sm text-slate-400">Real-time overview of team performance</p>
        </div>
        <div class="text-sm text-slate-400">Team: HRMO - Digital Operations</div>
    </div>

    <!-- KPI Cards (Team-Level Only) -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <p class="text-xs uppercase tracking-widest text-slate-400">Team Members</p>
            <p class="mt-2 text-3xl font-semibold text-white">4</p>
            <p class="text-xs text-slate-400 mt-1">Active: 4</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <p class="text-xs uppercase tracking-widest text-slate-400">Tasks Completed Today</p>
            <p class="mt-2 text-3xl font-semibold text-white">1</p>
            <p class="text-xs text-slate-400 mt-1">Target: 5</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <p class="text-xs uppercase tracking-widest text-slate-400">Avg Task Duration</p>
            <p class="mt-2 text-3xl font-semibold text-white">2.2h</p>
            <p class="text-xs text-slate-400 mt-1">Down 0.3h from last week</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <p class="text-xs uppercase tracking-widest text-slate-400">On-Time Completion</p>
            <p class="mt-2 text-3xl font-semibold text-emerald-400">92%</p>
            <p class="text-xs text-slate-400 mt-1">Up 1% from last week</p>
        </div>

    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- Task Throughput -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                Task Throughput (Last 7 Days)
            </h2>
            <canvas id="managerTaskChart" height="120"></canvas>
        </div>

        <!-- Task Duration -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                Average Task Duration by Type
            </h2>
            <canvas id="managerDurationChart" height="120"></canvas>
        </div>

    </div>

    <!-- Bottlenecks -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
        <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
            Bottleneck Tasks (Avg Delay Hours)
        </h2>
        <canvas id="managerBottleneckChart" height="120"></canvas>
    </div>

    <!-- Performance Ratings -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                Performance Rating Distribution
            </h2>
            <canvas id="managerRatingChart" height="120"></canvas>
        </div>

        <!-- System Recommendations -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                System Recommendations
            </h2>

            <ul class="space-y-3 text-sm text-slate-300">
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <span class="font-medium text-emerald-400">Completed:</span> Workload rebalanced after Client Form Review closure
                </li>
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <span class="font-medium text-amber-400">Action Needed:</span> Validate Report Generation (REQ-2025-012) pending review
                </li>
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <span class="font-medium text-amber-400">Action Needed:</span> Resolve missing output for Client Follow-up (REQ-2025-018)
                </li>
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <span class="font-medium text-blue-400">Suggestion:</span> Coach data entry on FS-03 to finish within SLA
                </li>
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <span class="font-medium text-blue-400">Suggestion:</span> Prepare Alpha Financials tax review before Dec 29
                </li>
            </ul>
        </div>

    </div>

    <!-- Team Activity -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
        <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
            Recent Team Activity
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-300">
                <thead class="text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-2 text-left">Employee</th>
                        <th class="px-4 py-2 text-left">Task</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Duration</th>
                        <th class="px-4 py-2 text-left">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                        <td class="px-4 py-3">Juan Dela Cruz</td>
                        <td class="px-4 py-3">E-Bank Scanning</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-400">In Progress</span></td>
                        <td class="px-4 py-3">1.7h (ongoing)</td>
                        <td class="px-4 py-3 text-slate-400">Today</td>
                    </tr>
                    <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                        <td class="px-4 py-3">Juan Dela Cruz</td>
                        <td class="px-4 py-3">Client Form Review</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-emerald-500/20 px-2 py-1 text-xs text-emerald-400">Completed</span></td>
                        <td class="px-4 py-3">2.3h</td>
                        <td class="px-4 py-3 text-slate-400">Today</td>
                    </tr>
                    <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                        <td class="px-4 py-3">Maria Garcia</td>
                        <td class="px-4 py-3">Report Generation</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-blue-500/20 px-2 py-1 text-xs text-blue-400">Pending Review</span></td>
                        <td class="px-4 py-3">3.2h</td>
                        <td class="px-4 py-3 text-slate-400">Yesterday</td>
                    </tr>
                    <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                        <td class="px-4 py-3">Pedro Reyes</td>
                        <td class="px-4 py-3">Financial Data Entry</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-400">In Progress</span></td>
                        <td class="px-4 py-3">1.5h (ongoing)</td>
                        <td class="px-4 py-3 text-slate-400">Today</td>
                    </tr>
                    <tr class="border-t border-slate-800 hover:bg-slate-800/50">
                        <td class="px-4 py-3">Juan Dela Cruz</td>
                        <td class="px-4 py-3">Client Follow-up</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-red-500/20 px-2 py-1 text-xs text-red-400">Delayed</span></td>
                        <td class="px-4 py-3">--</td>
                        <td class="px-4 py-3 text-slate-400">2 days ago</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</section>

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart is available
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }

    // Task Throughput Chart (Line Chart)
    new Chart(document.getElementById('managerTaskChart'), {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Tasks Assigned',
                data: [5, 4, 5, 4, 3, 2, 2],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }, {
                label: 'Tasks Completed',
                data: [4, 3, 4, 3, 2, 1, 1],
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
                    },
                    title: {
                        display: true,
                        text: 'Number of Tasks',
                        color: '#94a3b8'
                    }
                }
            }
        }
    });

    // Average Task Duration Chart (Bar Chart)
    new Chart(document.getElementById('managerDurationChart'), {
        type: 'bar',
        data: {
            labels: ['Scanning', 'Validation', 'Reporting', 'Data Entry', 'Follow-up', 'Review'],
            datasets: [{
                label: 'Average Hours',
                data: [1.7, 2.3, 3.2, 1.5, 1.0, 2.0],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(14, 165, 233, 0.8)'
                ],
                borderColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#8b5cf6',
                    '#ec4899',
                    '#0ea5e9'
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
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours',
                        color: '#94a3b8'
                    }
                }
            }
        }
    });

    // Bottleneck Tasks Chart (Horizontal Bar)
    new Chart(document.getElementById('managerBottleneckChart'), {
        type: 'bar',
        data: {
            labels: ['Approval Process', 'Data Validation', 'Reporting', 'Client Follow-up', 'Integration'],
            datasets: [{
                label: 'Average Delay (Hours)',
                data: [6.0, 5.0, 3.5, 4.0, 3.0],
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(236, 72, 153, 0.8)'
                ],
                borderColor: [
                    '#ef4444',
                    '#f59e0b',
                    '#3b82f6',
                    '#10b981',
                    '#ec4899'
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
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours of Delay',
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

    // Performance Rating Chart (Doughnut)
    new Chart(document.getElementById('managerRatingChart'), {
        type: 'doughnut',
        data: {
            labels: ['Excellent (5)', 'Good (4)', 'Average (3)', 'Below Avg (2)'],
            datasets: [{
                data: [25, 50, 20, 5],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    '#10b981',
                    '#22c55e',
                    '#f59e0b',
                    '#ef4444'
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
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw}%`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
