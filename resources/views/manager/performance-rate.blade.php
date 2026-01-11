<x-layouts.manager>
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Performance Ratings</h1>
                <p class="text-sm text-slate-400">Objective ratings generated from auto-captured output data.</p>
            </div>
            <div class="flex gap-2">
                <button type="button"
                        data-manager-loading="true"
                        data-loading-text="Exporting..."
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                    <span data-button-label>Export CSV</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
                <button type="button"
                        data-manager-loading="true"
                        data-loading-text="Generating..."
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <span data-button-label>Generate Report</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap gap-3">
                <div>
                    <label class="text-xs uppercase text-slate-400">Rating Period</label>
                    {{-- DUMMY_DATA: replace with dynamic value --}}
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>This Month</option>
                        <option selected>This Quarter</option>
                        <option>Last Quarter</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Team</label>
                    {{-- DUMMY_DATA: replace with dynamic value --}}
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Teams</option>
                        <option>Operations</option>
                        <option>Finance</option>
                        <option>IT Support</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Rating Band</label>
                    {{-- DUMMY_DATA: replace with dynamic value --}}
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Ratings</option>
                        <option>4.5 - 5.0</option>
                        <option>4.0 - 4.4</option>
                        <option>3.0 - 3.9</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Average Rating</p>
                <p class="mt-2 text-2xl font-semibold text-white">4.2</p>
                <p class="text-xs text-slate-500">Based on ORS logs</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Top Performers</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-400">1</p>
                <p class="text-xs text-slate-500">4.5 and above</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Needs Support</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">1</p>
                <p class="text-xs text-slate-500">Below 3.5</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Missing Outputs</p>
                <p class="mt-2 text-2xl font-semibold text-rose-300">1</p>
                <p class="text-xs text-slate-500">Auto-flagged</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Rating Distribution</h2>
                <canvas id="ratingDistributionChart" height="130"></canvas>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Objective Drivers</h2>
                <ul class="space-y-3 text-sm text-slate-300">
                    <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        Timeliness: 40% weight based on auto-logged start and end times.
                    </li>
                    <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        Quality: 35% weight based on validation outcomes.
                    </li>
                    <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        Volume: 25% weight based on completed outputs.
                    </li>
                </ul>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Employee Ratings</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Employee</th>
                            <th class="px-4 py-2 text-left">Team</th>
                            <th class="px-4 py-2 text-left">Outputs</th>
                            <th class="px-4 py-2 text-left">On-Time</th>
                            <th class="px-4 py-2 text-left">Rating</th>
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Juan Dela Cruz</td>
                            <td class="px-4 py-3">Operations</td>
                            <td class="px-4 py-3">2</td>
                            <td class="px-4 py-3">96%</td>
                            <td class="px-4 py-3 text-emerald-300">4.5</td>
                            <td class="px-4 py-3 text-emerald-300">Excellent</td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Maria Garcia</td>
                            <td class="px-4 py-3">Operations</td>
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3">90%</td>
                            <td class="px-4 py-3 text-sky-300">4.1</td>
                            <td class="px-4 py-3 text-sky-300">Good</td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Pedro Reyes</td>
                            <td class="px-4 py-3">Finance</td>
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3">82%</td>
                            <td class="px-4 py-3 text-amber-300">3.5</td>
                            <td class="px-4 py-3 text-amber-300">Needs Review</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') {
            return;
        }

        const chart = document.getElementById('ratingDistributionChart');
        if (!chart) {
            return;
        }

        new Chart(chart, {
            type: 'doughnut',
            data: {
                labels: ['Excellent', 'Good', 'Average', 'Below'],
                datasets: [{
                    data: [25, 50, 20, 5],
                    backgroundColor: ['#10b981', '#38bdf8', '#f59e0b', '#ef4444']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8' }
                    }
                }
            }
        });
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function setButtonLoading(button, isLoading, loadingText) {
            if (!button) {
                return;
            }
            const label = button.querySelector('[data-button-label]');
            const spinner = button.querySelector('[data-button-spinner]');
            if (label && !button.dataset.originalLabel) {
                button.dataset.originalLabel = label.textContent.trim();
            }

            if (isLoading) {
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-wait');
                if (spinner) {
                    spinner.classList.remove('hidden');
                }
                if (label && loadingText) {
                    label.textContent = loadingText;
                }
            } else {
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-wait');
                if (spinner) {
                    spinner.classList.add('hidden');
                }
                if (label && button.dataset.originalLabel) {
                    label.textContent = button.dataset.originalLabel;
                }
            }
        }

        document.querySelectorAll('[data-manager-loading="true"]').forEach((button) => {
            button.addEventListener('click', function () {
                if (button.dataset.loadingActive === 'true') {
                    return;
                }
                button.dataset.loadingActive = 'true';
                setButtonLoading(button, true, button.dataset.loadingText || 'Loading...');

                const duration = Number.parseInt(button.dataset.loadingDuration || '1200', 10);
                if (!Number.isNaN(duration)) {
                    setTimeout(() => {
                        setButtonLoading(button, false);
                        button.dataset.loadingActive = 'false';
                    }, duration);
                }
            });
        });
    });
    </script>
    @endpush
</x-layouts.manager>
