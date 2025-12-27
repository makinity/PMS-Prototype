<x-layouts.manager>
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Performance Ratings</h1>
                <p class="text-sm text-slate-400">Objective ratings generated from auto-captured output data.</p>
            </div>
            <div class="flex gap-2">
                <button type="button"
                        data-manager-action
                        data-action-title="Export ratings"
                        data-action-message="Download a CSV file with objective performance ratings for the selected filters."
                        data-action-confirm="Download CSV"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                    Export CSV
                </button>
                <button type="button"
                        data-manager-action
                        data-action-title="Generate report"
                        data-action-message="Compile an IPCR-ready report using auto-logged ORS output data."
                        data-action-confirm="Generate report"
                        class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    Generate Report
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap gap-3">
                <div>
                    <label class="text-xs uppercase text-slate-400">Rating Period</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>This Month</option>
                        <option selected>This Quarter</option>
                        <option>Last Quarter</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Team</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Teams</option>
                        <option>Operations</option>
                        <option>Finance</option>
                        <option>IT Support</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Rating Band</label>
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
                <p class="mt-2 text-2xl font-semibold text-emerald-400">4</p>
                <p class="text-xs text-slate-500">4.5 and above</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Needs Support</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">3</p>
                <p class="text-xs text-slate-500">Below 3.5</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Missing Outputs</p>
                <p class="mt-2 text-2xl font-semibold text-rose-300">2</p>
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
                            <td class="px-4 py-3">42</td>
                            <td class="px-4 py-3">96%</td>
                            <td class="px-4 py-3 text-emerald-300">4.7</td>
                            <td class="px-4 py-3 text-emerald-300">Excellent</td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Maria Santos</td>
                            <td class="px-4 py-3">Operations</td>
                            <td class="px-4 py-3">38</td>
                            <td class="px-4 py-3">90%</td>
                            <td class="px-4 py-3 text-sky-300">4.1</td>
                            <td class="px-4 py-3 text-sky-300">Good</td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Pedro Reyes</td>
                            <td class="px-4 py-3">Finance</td>
                            <td class="px-4 py-3">26</td>
                            <td class="px-4 py-3">82%</td>
                            <td class="px-4 py-3 text-amber-300">3.5</td>
                            <td class="px-4 py-3 text-amber-300">Needs Review</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="manager-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 id="manager-action-title" class="text-lg font-semibold text-white">Action</h2>
                    <p id="manager-action-body" class="mt-1 text-sm text-slate-400">Prototype action preview.</p>
                </div>
                <button type="button" data-manager-modal-close class="text-slate-400 hover:text-white">x</button>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-manager-modal-close class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Close</button>
                <button type="button" id="manager-action-confirm" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">Proceed</button>
            </div>
        </div>
    </div>

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
                    data: [25, 50, 18, 7],
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
        const modal = document.getElementById('manager-action-modal');
        const title = document.getElementById('manager-action-title');
        const body = document.getElementById('manager-action-body');
        const confirmBtn = document.getElementById('manager-action-confirm');

        if (!modal || !title || !body || !confirmBtn) {
            return;
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openModal(trigger) {
            title.textContent = trigger.dataset.actionTitle || 'Action';
            body.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
            confirmBtn.textContent = trigger.dataset.actionConfirm || 'Proceed';
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        document.querySelectorAll('[data-manager-action]').forEach((button) => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                openModal(button);
            });
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        modal.querySelectorAll('[data-manager-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        confirmBtn.addEventListener('click', closeModal);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    });
    </script>
    @endpush
</x-layouts.manager>

