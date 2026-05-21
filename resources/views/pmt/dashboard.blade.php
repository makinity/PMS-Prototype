@extends('layouts.pmt')

@section('main-content')
    @php
        $periodLabel = $period?->name ?? 'No Active Performance Period';
        $kpis = $kpis ?? [];
        $queueCounts = $queueCounts ?? [];
        $trend = $trend ?? ['labels' => [], 'series' => ['approved' => [], 'returned' => []]];
        $approvalQueue = $approvalQueue ?? [];
        $recentActions = $recentActions ?? [];
    @endphp

    <section class="space-y-6">
        <div class="rounded-2xl border border-white/10 bg-transparent p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Performance Management Team</p>
            <h1 class="mt-1 text-2xl font-bold text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-400">Operational queue and approval workflow overview.</p>
            <p class="mt-2 text-xs text-slate-500">{{ $periodLabel }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <p class="text-xs text-slate-400">Pending Actions</p>
                <p class="mt-1 text-3xl font-semibold text-amber-300">{{ (int) ($kpis['pendingActions'] ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <p class="text-xs text-slate-400">Returned / Escalated</p>
                <p class="mt-1 text-3xl font-semibold text-rose-300">{{ (int) ($kpis['returnedEscalated'] ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <p class="text-xs text-slate-400">Finalized Approvals</p>
                <p class="mt-1 text-3xl font-semibold text-emerald-300">{{ (int) ($kpis['finalizedApprovals'] ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <p class="text-xs text-slate-400">Queue Items</p>
                <p class="mt-1 text-3xl font-semibold text-white">{{ (int) ($kpis['queueItems'] ?? 0) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <h2 class="text-sm font-semibold text-white">PMT Action Trend (Last 14 Days)</h2>
                <div class="relative mt-3 h-64 w-full overflow-hidden sm:h-72">
                    <canvas id="pmtTrendChart" class="block h-full w-full"></canvas>
                </div>
            </div>
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <h2 class="text-sm font-semibold text-white">Current Queue Composition</h2>
                <div class="relative mt-3 h-64 w-full overflow-hidden sm:h-72">
                    <canvas id="pmtQueueChart" class="block h-full w-full"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <h2 class="text-sm font-semibold text-white">Approval Queue</h2>
                <div class="mt-3 space-y-2 text-sm text-slate-200">
                    @forelse ($approvalQueue as $item)
                        <div class="rounded-lg border border-white/10 bg-transparent px-3 py-2">
                            <p class="font-semibold text-white">{{ $item['office'] ?? 'Unknown Office' }}</p>
                            <p class="text-xs text-slate-400">
                                Status: {{ strtoupper((string) ($item['status'] ?? '--')) }} |
                                Updated: {{ $item['updated_at'] ?? '--' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-slate-400">No approval items in queue.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-white/10 bg-transparent p-4">
                <h2 class="text-sm font-semibold text-white">Recent Actions</h2>
                <div class="mt-3 space-y-2 text-sm text-slate-200">
                    @forelse ($recentActions as $item)
                        <div class="rounded-lg border border-white/10 bg-transparent px-3 py-2">
                            <p class="font-semibold text-white">{{ $item['office'] ?? 'Unknown Office' }}</p>
                            <p class="text-xs text-slate-400">
                                Status: {{ strtoupper((string) ($item['status'] ?? '--')) }} |
                                Updated: {{ $item['updated_at'] ?? '--' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-slate-400">No recent PMT actions found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function () {
                const trendLabels = @json($trend['labels'] ?? []);
                const approvedSeries = @json($trend['series']['approved'] ?? []);
                const returnedSeries = @json($trend['series']['returned'] ?? []);

                const queueCounts = @json($queueCounts ?? []);
                const queueLabels = ['UWP', 'OPCR', 'Accomplishment', 'Returned'];
                const queueData = [
                    Number(queueCounts.uwp ?? 0),
                    Number(queueCounts.opcr ?? 0),
                    Number(queueCounts.accomplishment ?? 0),
                    Number(queueCounts.returned ?? 0)
                ];

                const textColor = '#d1d5db';
                const gridColor = 'rgba(148, 163, 184, 0.18)';

                const trendEl = document.getElementById('pmtTrendChart');
                if (trendEl) {
                    new Chart(trendEl, {
                        type: 'line',
                        data: {
                            labels: trendLabels,
                            datasets: [{
                                label: 'Approved',
                                data: approvedSeries,
                                borderWidth: 2,
                                tension: 0.25
                            }, {
                                label: 'Returned',
                                data: returnedSeries,
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

                const queueEl = document.getElementById('pmtQueueChart');
                if (queueEl) {
                    new Chart(queueEl, {
                        type: 'doughnut',
                        data: {
                            labels: queueLabels,
                            datasets: [{ data: queueData }]
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

