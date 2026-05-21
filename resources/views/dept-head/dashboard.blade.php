@extends('layouts.dept-head')

@section('main-content')
    @php
        $periodLabel = $period?->name ?? 'No Active Performance Period';
        $statusCounts = $statusCounts ?? [];
        $trend = $trend ?? ['labels' => [], 'series' => ['submitted' => [], 'rated' => []]];
        $recentActivity = $recentActivity ?? [];
        $kpis = $kpis ?? [];
    @endphp

    <section class="space-y-6">
        <div class="rounded-2xl border border-white/10 bg-transparent p-5">
            <h1 class="text-2xl font-semibold text-white">Department Head Dashboard</h1>
            <p class="mt-1 text-sm text-slate-400">Operational view of office workflow queues and team throughput.</p>
            <p class="mt-2 text-xs text-slate-500">{{ $periodLabel }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Team Members</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ (int) ($kpis['teamMembers'] ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Pending Reviews</p>
                <p class="mt-2 text-3xl font-semibold text-amber-300">{{ (int) ($kpis['pendingReviews'] ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">At Risk / Returned</p>
                <p class="mt-2 text-3xl font-semibold text-rose-300">{{ (int) ($kpis['atRisk'] ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Completed / Endorsed</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-300">{{ (int) ($kpis['completedEndorsed'] ?? 0) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <h2 class="text-sm font-semibold text-gray-100">Team Throughput (Last 14 Days)</h2>
                <div class="relative mt-3 h-64 w-full overflow-hidden sm:h-72">
                    <canvas id="deptHeadThroughputChart" class="block h-full w-full"></canvas>
                </div>
            </div>
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <h2 class="text-sm font-semibold text-gray-100">Workflow Status Mix</h2>
                <div class="relative mt-3 h-64 w-full overflow-hidden sm:h-72">
                    <canvas id="deptHeadStatusChart" class="block h-full w-full"></canvas>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-transparent p-4">
            <h2 class="text-sm font-semibold text-gray-100">Recent Office Activity</h2>
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-xs sm:text-sm">
                    <thead class="bg-slate-900/60 text-[11px] uppercase tracking-wide text-slate-400 sm:text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee</th>
                            <th class="px-4 py-3 text-left">Task</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Work Date</th>
                            <th class="px-4 py-3 text-left">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-slate-200">
                        @forelse ($recentActivity as $row)
                            <tr>
                                <td class="px-4 py-3">{{ $row['employee'] ?? 'Unknown' }}</td>
                                <td class="px-4 py-3">{{ $row['task'] ?? 'Unlinked Output' }}</td>
                                <td class="px-4 py-3">{{ strtoupper((string) ($row['status'] ?? '--')) }}</td>
                                <td class="px-4 py-3">{{ $row['work_date'] ?? '--' }}</td>
                                <td class="px-4 py-3">{{ $row['updated_at'] ?? '--' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">No recent activity found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function () {
                const trendLabels = @json($trend['labels'] ?? []);
                const submittedSeries = @json($trend['series']['submitted'] ?? []);
                const ratedSeries = @json($trend['series']['rated'] ?? []);

                const statusCounts = @json($statusCounts ?? []);
                const statusLabels = ['Draft', 'Submitted', 'Endorsed', 'Returned', 'Completed'];
                const statusData = [
                    Number(statusCounts.draft ?? 0),
                    Number(statusCounts.submitted ?? 0),
                    Number(statusCounts.endorsed ?? 0),
                    Number(statusCounts.returned ?? 0),
                    Number(statusCounts.completed ?? 0)
                ];

                const textColor = '#d1d5db';
                const gridColor = 'rgba(148, 163, 184, 0.18)';

                const throughputEl = document.getElementById('deptHeadThroughputChart');
                if (throughputEl) {
                    new Chart(throughputEl, {
                        type: 'line',
                        data: {
                            labels: trendLabels,
                            datasets: [{
                                label: 'Submitted / Rated',
                                data: submittedSeries,
                                borderWidth: 2,
                                tension: 0.25
                            }, {
                                label: 'Rated',
                                data: ratedSeries,
                                borderWidth: 2,
                                tension: 0.25
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { labels: { color: textColor } } },
                            scales: {
                                x: { ticks: { color: textColor }, grid: { color: gridColor } },
                                y: { beginAtZero: true, ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } }
                            }
                        }
                    });
                }

                const statusEl = document.getElementById('deptHeadStatusChart');
                if (statusEl) {
                    new Chart(statusEl, {
                        type: 'doughnut',
                        data: {
                            labels: statusLabels,
                            datasets: [{ data: statusData }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom', labels: { color: textColor } } }
                        }
                    });
                }
            })();
        </script>
    @endpush
@endsection
