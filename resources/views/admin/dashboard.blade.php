@extends('layouts.admin')

@section('main-content')
    @php
        $data = $data ?? \App\Http\Controllers\Admin\DashboardController::buildDashboardData();

        $period = $data['period'] ?? null;
        $kpis = $data['kpis'] ?? [];
        $roleCounts = $kpis['roles'] ?? [];

        $totalUsers = (int) ($kpis['totalUsers'] ?? 0);
        $activeUsers = (int) ($kpis['activeUsers'] ?? 0);
        $totalOffices = (int) ($kpis['totalOffices'] ?? 0);

        $uwpTotal = (int) ($data['uwpTotal'] ?? 0);
        $opcrTotal = (int) ($data['opcrTotal'] ?? 0);
        $ipcrTotal = (int) ($data['ipcrTotal'] ?? 0);
        $orsTotal = (int) ($data['orsTotal'] ?? 0);

        $uwpStatusCounts = $data['uwpStatusCounts'] ?? [];
        $ipcrStatusCounts = $data['ipcrStatusCounts'] ?? [];

        $orsTrendLabels = $data['orsTrendLabels'] ?? [];
        $orsTrendCounts = $data['orsTrendCounts'] ?? [];

        $periodLabel = $period?->name ?? null;
        $periodRange = null;
        if ($period && $period->start_date && $period->end_date) {
            $periodRange = \Carbon\Carbon::parse($period->start_date)->format('M d, Y')
                . ' - '
                . \Carbon\Carbon::parse($period->end_date)->format('M d, Y');
        }

        $kpiCards = [
            ['label' => 'UWP Total', 'value' => number_format($uwpTotal)],
            ['label' => 'OPCR Total', 'value' => number_format($opcrTotal)],
            ['label' => 'IPCR Total', 'value' => number_format($ipcrTotal)],
            ['label' => 'ORS Entries Total', 'value' => number_format($orsTotal)],
        ];
    @endphp

    <section class="space-y-4 px-3 md:px-6">
        <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-lg font-semibold text-gray-100 sm:text-xl">Administrator Dashboard</h1>
                    @if ($period)
                        <p class="mt-1 break-words text-sm text-gray-300">{{ $periodLabel }}</p>
                        <p class="break-words text-xs text-gray-400 sm:truncate">{{ $periodRange ?? 'Date range unavailable' }}</p>
                    @else
                        <p class="mt-1 text-sm text-gray-400">No active performance period available.</p>
                    @endif
                </div>
            </div>
        </div>

        @if (!$period)
            <div class="rounded-xl border border-amber-700/40 bg-amber-900/20 px-4 py-3 text-sm text-amber-200">
                No performance period found.
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($kpiCards as $card)
                <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-gray-400">{{ $card['label'] }}</p>
                    <p class="mt-2 text-2xl font-semibold text-white">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        <details id="roleDistributionPanel" class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
            <summary class="cursor-pointer list-none text-sm font-semibold text-gray-100 [&::-webkit-details-marker]:hidden">
                <div class="flex items-center justify-between gap-2">
                    <span>User Role Distribution</span>
                    <span class="text-xs text-gray-400 sm:hidden">Tap to expand</span>
                </div>
            </summary>
            <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                <div class="min-w-0 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Total Users</p>
                    <p class="text-lg font-semibold text-white">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="min-w-0 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Active Users</p>
                    <p class="text-lg font-semibold text-white">{{ number_format($activeUsers) }}</p>
                </div>
                <div class="min-w-0 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Offices</p>
                    <p class="text-lg font-semibold text-white">{{ number_format($totalOffices) }}</p>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="min-w-0 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">PMT</p>
                    <p class="text-lg font-semibold text-white">{{ number_format((int) ($roleCounts['pmt'] ?? 0)) }}</p>
                </div>
                <div class="min-w-0 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Dept Head</p>
                    <p class="text-lg font-semibold text-white">{{ number_format((int) ($roleCounts['dept-head'] ?? 0)) }}</p>
                </div>
                <div class="min-w-0 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Supervisor</p>
                    <p class="text-lg font-semibold text-white">{{ number_format((int) ($roleCounts['supervisor'] ?? 0)) }}</p>
                </div>
                <div class="min-w-0 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Employee</p>
                    <p class="text-lg font-semibold text-white">{{ number_format((int) ($roleCounts['employee'] ?? 0)) }}</p>
                </div>
            </div>
        </details>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="min-w-0 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-100">UWP Status</h2>
                <div class="relative mt-3 h-64 w-full overflow-hidden sm:h-72">
                    <canvas id="uwpStatusChart" class="block h-full w-full"></canvas>
                </div>
            </div>

            <div class="min-w-0 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-100">IPCR Status</h2>
                <div class="relative mt-3 h-64 w-full overflow-hidden sm:h-72">
                    <canvas id="ipcrStatusChart" class="block h-full w-full"></canvas>
                </div>
            </div>

            <div class="min-w-0 rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm lg:col-span-2">
                <h2 class="text-sm font-semibold text-gray-100">ORS Entries Trend (Last 14 Days)</h2>
                <div class="relative mt-3 h-64 w-full overflow-hidden sm:h-72">
                    <canvas id="orsTrendChart" class="block h-full w-full"></canvas>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function () {
                const uwpStatusCounts = @json($uwpStatusCounts);
                const ipcrStatusCounts = @json($ipcrStatusCounts);
                const orsTrendLabels = @json($orsTrendLabels);
                const orsTrendCounts = @json($orsTrendCounts);

                const uwpStatusOrder = ['draft', 'submitted', 'endorsed', 'pmt_approved', 'returned'];
                const ipcrStatusOrder = ['for_commitment', 'committed'];

                const normalizeLabel = (value) => value
                    .replace(/_/g, ' ')
                    .replace(/\b\w/g, (c) => c.toUpperCase());

                const pickSeries = (source, order) => order.map((key) => Number(source?.[key] ?? 0));

                const baseOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    layout: { padding: 8 },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#d1d5db',
                                boxWidth: 12,
                                padding: 12
                            }
                        },
                        tooltip: { enabled: true }
                    }
                };

                const barLineOptions = {
                    ...baseOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#9ca3af', precision: 0 }
                        },
                        x: { ticks: { color: '#9ca3af', maxRotation: 0, minRotation: 0 } }
                    }
                };

                const chartInstances = {};
                function renderChart(canvasId, config) {
                    const el = document.getElementById(canvasId);
                    if (!el) return;
                    if (chartInstances[canvasId]) {
                        chartInstances[canvasId].destroy();
                    }
                    chartInstances[canvasId] = new Chart(el, config);
                }

                const rolePanel = document.getElementById('roleDistributionPanel');
                if (rolePanel && window.matchMedia('(min-width: 640px)').matches) {
                    rolePanel.setAttribute('open', 'open');
                }

                renderChart('uwpStatusChart', {
                    type: 'bar',
                    data: {
                        labels: uwpStatusOrder.map(normalizeLabel),
                        datasets: [{
                            label: 'UWP Count',
                            data: pickSeries(uwpStatusCounts, uwpStatusOrder),
                            borderWidth: 1
                        }]
                    },
                    options: barLineOptions
                });

                renderChart('ipcrStatusChart', {
                    type: 'doughnut',
                    data: {
                        labels: ipcrStatusOrder.map(normalizeLabel),
                        datasets: [{
                            label: 'IPCR Count',
                            data: pickSeries(ipcrStatusCounts, ipcrStatusOrder),
                            cutout: '65%'
                        }]
                    },
                    options: {
                        ...baseOptions,
                        cutout: '65%'
                    }
                });

                renderChart('orsTrendChart', {
                    type: 'line',
                    data: {
                        labels: orsTrendLabels,
                        datasets: [{
                            label: 'ORS Entries',
                            data: orsTrendCounts,
                            fill: false,
                            tension: 0.25,
                            borderWidth: 2,
                            pointRadius: 3
                        }]
                    },
                    options: barLineOptions
                });
            })();
        </script>
    @endpush
@endsection
