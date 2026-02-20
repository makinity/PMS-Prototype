@extends('layouts.employee')

@section('main-content')
    @php
        $fmtHms = function ($seconds) {
            $seconds = max(0, (int) $seconds);
            $h = intdiv($seconds, 3600);
            $m = intdiv($seconds % 3600, 60);
            $s = $seconds % 60;
            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        };

        $fmtHours = function ($seconds) {
            $seconds = max(0, (int) $seconds);
            return number_format($seconds / 3600, 2);
        };

        $periodName = $activePeriod?->name ?? 'No Active Period';
        $ipcrStatus = $currentIpcr?->status ?? '—';
    @endphp

    <!-- 1. Welcome & Status -->
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">Welcome, {{ Auth::user()->name }}</h2>
                <p class="text-gray-300">{{ Auth::user()->position }} - {{ Auth::user()->office?->name }}</p>
                <p class="text-gray-400 text-sm mt-1">
                    Performance Period: <span class="text-gray-200 font-medium">{{ $periodName }}</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <span class="px-4 py-2 rounded-full bg-gradient-to-r from-green-900/30 to-green-800/30 border border-green-700/50 text-green-300 text-sm font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    {{ Auth::user()->is_active ? 'Active' : 'Inactive' }}
                </span>

                <span class="px-4 py-2 rounded-full bg-gradient-to-r from-purple-900/30 to-blue-900/30 border border-purple-700/50 text-purple-300 text-sm font-medium">
                    {{ Auth::user()->employee_id }}
                </span>

                <span class="px-4 py-2 rounded-full bg-gradient-to-r from-gray-900/30 to-gray-800/30 border border-gray-600/50 text-gray-200 text-sm font-medium">
                    IPCR: {{ $ipcrStatus }}
                </span>
            </div>
        </div>
    </div>

    <!-- 2. Today's Work Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <p class="text-gray-400 text-sm">Total Time Today</p>
            <p class="text-3xl font-bold text-white mt-2">{{ $fmtHms($todayTotalSeconds) }}</p>
            <p class="text-gray-400 text-sm mt-2">({{ $fmtHours($todayTotalSeconds) }} hrs)</p>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <p class="text-gray-400 text-sm">Tasks Logged Today</p>
            <p class="text-3xl font-bold text-white mt-2">{{ $todayTasksCount }}</p>
            <p class="text-gray-400 text-sm mt-2">
                With Evidence: <span class="text-gray-200 font-medium">{{ $todayWithEvidenceCount }}</span>
            </p>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <p class="text-gray-400 text-sm">Submitted Today</p>
            <p class="text-3xl font-bold text-white mt-2">{{ $todaySubmittedCount }}</p>

            <div class="mt-3">
                <div class="flex items-center justify-between text-sm text-gray-400">
                    <span>Submission Rate</span>
                    <span class="text-gray-200 font-medium">
                        {{ $todayTasksCount > 0 ? (int) round(($todaySubmittedCount / $todayTasksCount) * 100) : 0 }}%
                    </span>
                </div>
                <div class="h-2 bg-gray-700 rounded-full mt-2 overflow-hidden">
                    <div class="h-2 bg-green-600 rounded-full"
                        style="width: {{ $todayTasksCount > 0 ? (int) round(($todaySubmittedCount / $todayTasksCount) * 100) : 0 }}%;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3b. Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-white">Today Status</h3>
                <span class="text-sm text-gray-400">D/S/R</span>
            </div>
            <div class="h-56">
                <canvas id="chartTodayStatus"></canvas>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-white">Period Status</h3>
                <span class="text-sm text-gray-400">Counts</span>
            </div>
            <div class="h-56">
                <canvas id="chartPeriodStatus"></canvas>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-white">Last 7 Days</h3>
                <span class="text-sm text-gray-400">Hours</span>
            </div>
            <div class="h-56">
                <canvas id="chartWeeklyHours"></canvas>
            </div>
        </div>
    </div>

    <!-- 5. Performance Snapshot -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <p class="text-gray-400 text-sm">IPCR Status</p>
            <p class="text-2xl font-bold text-white mt-2">{{ $ipcrStatus }}</p>
            <p class="text-gray-500 text-sm mt-1">
                {{ $currentIpcr ? 'Active period IPCR found' : 'No IPCR for active period' }}
            </p>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <p class="text-gray-400 text-sm">IPCR Items</p>
            <p class="text-2xl font-bold text-white mt-2">{{ $ipcrItemsTotal }}</p>
            <p class="text-gray-500 text-sm mt-1">Total assigned outputs</p>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <p class="text-gray-400 text-sm">Progress Snapshot</p>
            <p class="text-2xl font-bold text-white mt-2">{{ $ipcrProgressPct }}%</p>
            <p class="text-gray-500 text-sm mt-1">
                Worked items: {{ $distinctWorkedIpcrItems }} / {{ $ipcrItemsTotal }}
            </p>

            <div class="h-2 bg-gray-700 rounded-full mt-3 overflow-hidden">
                <div class="h-2 bg-blue-600 rounded-full" style="width: {{ $ipcrProgressPct }}%;"></div>
            </div>
        </div>
    </div>

    <!-- 3. Task Status Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Task Status Overview</h3>
                <span class="text-sm text-gray-400">Active Period</span>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-4">
                <div class="p-4 rounded-lg bg-gray-900/40 border border-gray-700">
                    <p class="text-gray-400 text-xs">Draft</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $periodDraftCount }}</p>
                </div>

                <div class="p-4 rounded-lg bg-gray-900/40 border border-gray-700">
                    <p class="text-gray-400 text-xs">Submitted</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $periodSubmittedCount }}</p>
                </div>

                <div class="p-4 rounded-lg bg-gray-900/40 border border-gray-700">
                    <p class="text-gray-400 text-xs">Rated</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $periodRatedCount }}</p>
                </div>
            </div>

            <div class="mt-4 text-sm text-gray-400">
                Total Logged Time: <span class="text-gray-200 font-medium">{{ $fmtHms($periodTotalSeconds) }}</span>
                <span class="text-gray-500">({{ $fmtHours($periodTotalSeconds) }} hrs)</span>
            </div>

            <div class="mt-2 text-sm text-gray-400">
                With Evidence: <span class="text-gray-200 font-medium">{{ $periodWithEvidenceCount }}</span>
            </div>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Coaching & Monitoring</h3>
                <span class="text-sm text-gray-400">Supervisor</span>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="p-4 rounded-lg bg-gray-900/40 border border-gray-700">
                    <p class="text-gray-400 text-xs">Needs Rating</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $needsRatingCount }}</p>
                    <p class="text-gray-500 text-xs mt-1">Submitted, not yet rated</p>
                </div>

                <div class="p-4 rounded-lg bg-gray-900/40 border border-gray-700">
                    <p class="text-gray-400 text-xs">Monitored</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $monitoredCount }}</p>
                    <p class="text-gray-500 text-xs mt-1">Has coaching ratings</p>
                </div>
            </div>

            @if($runningEntry)
                <div class="mt-4 p-4 rounded-lg border border-yellow-700/40 bg-yellow-900/10">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-yellow-200 font-medium text-sm">Running Timer Detected</p>
                            <p class="text-yellow-300/80 text-xs">
                                Started at {{ optional($runningEntry->started_at)->format('h:i A') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-yellow-900/30 border border-yellow-700/40 text-yellow-200 text-xs">
                            Ongoing
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <!-- 6. Quick Actions -->
    <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-white">Quick Actions</h3>
                <p class="text-gray-400 text-sm">Jump to common tasks for this performance period.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('employee.ors') }}"
                   class="px-4 py-2 rounded-lg bg-blue-600/90 hover:bg-blue-600 text-white text-sm font-medium border border-blue-500/30">
                    Open ORS Logger
                </a>

                <a href="{{ route('employee.my-task') }}"
                   class="px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium border border-gray-600">
                    View My Tasks
                </a>

                <a href="{{ route('employee.ipcr-target') }}"
                   class="px-4 py-2 rounded-lg bg-purple-700/90 hover:bg-purple-700 text-white text-sm font-medium border border-purple-500/30">
                    View IPCR
                </a>
            </div>
        </div>
    </div>

    <!-- 7. Recent Activity -->
    <div class="p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg mt-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Recent Activity</h3>
            <span class="text-sm text-gray-400">Today</span>
        </div>

        <div class="mt-4 space-y-3">
            @forelse($orsToday as $entry)
                <div class="p-4 rounded-lg bg-gray-900/40 border border-gray-700">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-white font-medium truncate">
                                {{ $entry->ipcrItem?->output_title ?? 'Unlinked Output' }}
                            </p>
                            <p class="text-gray-400 text-xs mt-1">
                                {{ \Illuminate\Support\Carbon::parse($entry->work_date)->format('M d, Y') }}
                                • Qty: <span class="text-gray-200">{{ $entry->quantity ?? 0 }}</span>
                                • Evidence: <span class="text-gray-200">{{ $entry->evidences_count }}</span>
                                • Rated:
                                @if($entry->monitoring)
                                    <span class="text-green-300">Yes</span>
                                @else
                                    <span class="text-gray-300">No</span>
                                @endif
                            </p>
                            @if($entry->notes)
                                <p class="text-gray-500 text-xs mt-2 line-clamp-2">{{ $entry->notes }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-3 py-1 rounded-full text-xs border
                                {{ $entry->status === 'draft' ? 'bg-gray-800 text-gray-200 border-gray-600' : '' }}
                                {{ $entry->status === 'submitted' ? 'bg-blue-900/30 text-blue-200 border-blue-700/40' : '' }}
                                {{ $entry->status === 'rated' ? 'bg-green-900/30 text-green-200 border-green-700/40' : '' }}
                            ">
                                {{ strtoupper($entry->status ?? '—') }}
                            </span>

                            <span class="px-3 py-1 rounded-full bg-gray-900/40 border border-gray-700 text-gray-200 text-xs">
                                {{ $fmtHms((int) ($entry->total_seconds ?? 0)) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 rounded-lg bg-gray-900/40 border border-gray-700 text-gray-400 text-sm">
                    No ORS entries logged today.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Chart.js CDN + Charts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const isDarkGrid = 'rgba(255,255,255,0.08)';
            const isDarkTicks = 'rgba(255,255,255,0.65)';

            const todayDraft = @json($todayDraftCount ?? 0);
            const todaySubmitted = @json($todaySubmittedCount ?? 0);
            const todayRated = @json($todayRatedCount ?? 0);

            const periodDraft = @json($periodDraftCount ?? 0);
            const periodSubmitted = @json($periodSubmittedCount ?? 0);
            const periodRated = @json($periodRatedCount ?? 0);

            const weeklyLabels = @json($weeklyLabels ?? []);
            const weeklyHours = @json($weeklyHours ?? []);

            // 1) Today Doughnut
            const el1 = document.getElementById('chartTodayStatus');
            if (el1) {
                new Chart(el1, {
                    type: 'doughnut',
                    data: {
                        labels: ['Draft', 'Submitted', 'Rated'],
                        datasets: [{
                            data: [todayDraft, todaySubmitted, todayRated],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: isDarkTicks }
                            }
                        }
                    }
                });
            }

            // 2) Period Bar
            const el2 = document.getElementById('chartPeriodStatus');
            if (el2) {
                new Chart(el2, {
                    type: 'bar',
                    data: {
                        labels: ['Draft', 'Submitted', 'Rated'],
                        datasets: [{
                            label: 'Count',
                            data: [periodDraft, periodSubmitted, periodRated],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { ticks: { color: isDarkTicks }, grid: { color: isDarkGrid } },
                            y: { ticks: { color: isDarkTicks }, grid: { color: isDarkGrid }, beginAtZero: true }
                        },
                        plugins: {
                            legend: { labels: { color: isDarkTicks } }
                        }
                    }
                });
            }

            // 3) Weekly Hours Line
            const el3 = document.getElementById('chartWeeklyHours');
            if (el3) {
                new Chart(el3, {
                    type: 'line',
                    data: {
                        labels: weeklyLabels,
                        datasets: [{
                            label: 'Hours Logged',
                            data: weeklyHours,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { ticks: { color: isDarkTicks }, grid: { color: isDarkGrid } },
                            y: { ticks: { color: isDarkTicks }, grid: { color: isDarkGrid }, beginAtZero: true }
                        },
                        plugins: {
                            legend: { labels: { color: isDarkTicks } }
                        }
                    }
                });
            }
        })();
    </script>
@endsection
