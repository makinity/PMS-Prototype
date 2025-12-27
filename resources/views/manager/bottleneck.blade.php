<x-layouts.manager>
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Bottleneck Analysis</h1>
                <p class="text-sm text-slate-400">Identify delays using time-stamped task durations.</p>
            </div>
            <div class="text-xs text-slate-400">Auto-generated from ORS logs</div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap gap-3">
                <div>
                    <label class="text-xs uppercase text-slate-400">Time Period</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>This Week</option>
                        <option selected>This Month</option>
                        <option>This Quarter</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Task Category</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Categories</option>
                        <option>Scanning</option>
                        <option>Validation</option>
                        <option>Reporting</option>
                        <option>Follow-up</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase text-slate-400">Client Type</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Clients</option>
                        <option>Government</option>
                        <option>Private</option>
                        <option>Internal</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Top Bottleneck</p>
                <p class="mt-2 text-2xl font-semibold text-white">E-Bank Scan</p>
                <p class="text-xs text-slate-500">Avg delay: 1.4h</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Avg Delay</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">2.1h</p>
                <p class="text-xs text-slate-500">Across 5 tasks</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">At-Risk Tasks</p>
                <p class="mt-2 text-2xl font-semibold text-white">6</p>
                <p class="text-xs text-slate-500">Forecasted delays</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">On-Time Recovery</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-400">88%</p>
                <p class="text-xs text-slate-500">After intervention</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Delay Trends by Task</h2>
                <div class="relative">
                    <canvas id="bottleneckTrendChart" height="130" class="relative z-0"></canvas>
                    <div id="bottleneckTrendFallback" class="absolute inset-0 z-10 flex flex-col justify-center gap-3 text-xs text-slate-300">
                        <div class="flex items-center gap-3">
                            <span class="w-28 text-slate-400">E-Bank Scan</span>
                            <div class="h-2 flex-1 rounded-full bg-slate-800">
                                <div class="h-2 w-10/12 rounded-full bg-rose-500"></div>
                            </div>
                            <span class="w-12 text-right text-rose-300">1.4h</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-28 text-slate-400">Form Review</span>
                            <div class="h-2 flex-1 rounded-full bg-slate-800">
                                <div class="h-2 w-8/12 rounded-full bg-amber-400"></div>
                            </div>
                            <span class="w-12 text-right text-amber-300">1.1h</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-28 text-slate-400">Doc Upload</span>
                            <div class="h-2 flex-1 rounded-full bg-slate-800">
                                <div class="h-2 w-7/12 rounded-full bg-orange-400"></div>
                            </div>
                            <span class="w-12 text-right text-amber-300">0.9h</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-28 text-slate-400">Reporting</span>
                            <div class="h-2 flex-1 rounded-full bg-slate-800">
                                <div class="h-2 w-5/12 rounded-full bg-sky-400"></div>
                            </div>
                            <span class="w-12 text-right text-slate-300">0.6h</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Top Delay Drivers</h2>
                <div class="space-y-3 text-sm text-slate-300">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        E-Bank Scanning: 1.4h average over target
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        Client Form Review: 1.1h average over target
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        Document Upload: 0.9h average over target
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Bottleneck Task Details</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Task</th>
                            <th class="px-4 py-2 text-left">Client</th>
                            <th class="px-4 py-2 text-left">Avg Duration</th>
                            <th class="px-4 py-2 text-left">Target</th>
                            <th class="px-4 py-2 text-left">Delay</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">E-Bank Scanning</td>
                            <td class="px-4 py-3">ABC Corp</td>
                            <td class="px-4 py-3">3.4h</td>
                            <td class="px-4 py-3">2.0h</td>
                            <td class="px-4 py-3 text-rose-300">+1.4h</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="Investigate delay"
                                        data-action-message="Open task timeline, assignee history, and linked output details."
                                        data-action-confirm="Open review"
                                        class="text-blue-400 hover:text-blue-300">
                                    Investigate
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Client Form Review</td>
                            <td class="px-4 py-3">XYZ Ltd</td>
                            <td class="px-4 py-3">2.6h</td>
                            <td class="px-4 py-3">1.5h</td>
                            <td class="px-4 py-3 text-amber-300">+1.1h</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="Rebalance workload"
                                        data-action-message="Reassign tasks to reduce delay risk for this workflow."
                                        data-action-confirm="Open reassignment"
                                        class="text-blue-400 hover:text-blue-300">
                                    Rebalance
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Document Upload</td>
                            <td class="px-4 py-3">Internal</td>
                            <td class="px-4 py-3">1.9h</td>
                            <td class="px-4 py-3">1.0h</td>
                            <td class="px-4 py-3 text-amber-300">+0.9h</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="Coach assignee"
                                        data-action-message="Review task notes and schedule a coaching follow-up."
                                        data-action-confirm="Schedule"
                                        class="text-blue-400 hover:text-blue-300">
                                    Coach
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Optimization Recommendations</h2>
                <span class="text-xs text-slate-500">Click to implement solutions</span>
            </div>
            <ul class="space-y-3">
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 hover:border-slate-700 transition-colors">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                <span class="font-medium text-white">Backup Scanner Assignment</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-400">Assign a backup scanner during peak hours for E-Bank tasks to reduce 1.4h delays.</p>
                            <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                                <span>Impact: High</span>
                                <span>Effort: Low</span>
                                <span>Peak Hours: 10 AM - 2 PM</span>
                            </div>
                        </div>
                        <button type="button"
                                data-bottleneck-action
                                data-action-title="Assign Backup Scanner for E-Bank Tasks"
                                data-action-message="Assign an additional scanner during peak hours (10 AM - 2 PM) specifically for E-Bank scanning tasks. This will reduce the current 1.4h average delay by 60%."
                                data-action-confirm="Schedule Backup"
                                data-action-type="backup-scanner"
                                data-impact-task="E-Bank Scanning"
                                class="inline-flex w-32 items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 whitespace-nowrap">
                            Assign
                        </button>
                    </div>
                </li>
                
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 hover:border-slate-700 transition-colors">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                <span class="font-medium text-white">Pre-Checklist for Form Validation</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-400">Add a pre-checklist to reduce rework in client form validation, decreasing 1.1h delays.</p>
                            <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                                <span>Impact: Medium</span>
                                <span>Effort: Medium</span>
                                <span>Rework Reduction: 40%</span>
                            </div>
                        </div>
                        <button type="button"
                                data-bottleneck-action
                                data-action-title="Implement Pre-Checklist for Form Validation"
                                data-action-message="Add mandatory pre-validation checklist to all client form reviews. Includes completeness check, required fields, and document attachments verification."
                                data-action-confirm="Deploy Checklist"
                                data-action-type="pre-checklist"
                                data-impact-task="Client Form Review"
                                class="inline-flex w-32 items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 whitespace-nowrap">
                            Implement
                        </button>
                    </div>
                </li>
                
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 hover:border-slate-700 transition-colors">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <span class="font-medium text-white">Batch Document Uploads</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-400">Schedule batch uploads to compress document handoff time, reducing 0.9h delays.</p>
                            <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                                <span>Impact: High</span>
                                <span>Effort: Medium</span>
                                <span>Batch Size: 10 docs/hour</span>
                            </div>
                        </div>
                        <button type="button"
                                data-bottleneck-action
                                data-action-title="Schedule Batch Document Uploads"
                                data-action-message="Schedule automated batch uploads every hour for document processing. This compresses handoff time and reduces individual upload delays by 70%."
                                data-action-confirm="Schedule Batches"
                                data-action-type="batch-uploads"
                                data-impact-task="Document Upload"
                                class="inline-flex w-32 items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 whitespace-nowrap">
                            Schedule
                        </button>
                    </div>
                </li>
                
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 hover:border-slate-700 transition-colors">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="font-medium text-white">Automated Quality Checks</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-400">Implement automated quality checks for scanning tasks to catch errors early.</p>
                            <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                                <span>Impact: Medium</span>
                                <span>Effort: High</span>
                                <span>Error Reduction: 65%</span>
                            </div>
                        </div>
                        <button type="button"
                                data-bottleneck-action
                                data-action-title="Enable Automated Quality Checks"
                                data-action-message="Activate automated quality checks for all scanning tasks. Includes resolution validation, format verification, and metadata completeness checks."
                                data-action-confirm="Activate Checks"
                                data-action-type="quality-checks"
                                data-impact-task="E-Bank Scanning"
                                class="inline-flex w-32 items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 whitespace-nowrap">
                            Enable
                        </button>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Bottleneck Action Modal -->
        <div id="bottleneck-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
            <div class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h2 id="bottleneck-title" class="text-lg font-semibold text-white">Bottleneck Optimization</h2>
                        <p id="bottleneck-message" class="mt-1 text-sm text-slate-400">Action details will appear here.</p>
                    </div>
                    <button type="button" data-bottleneck-modal-close class="text-slate-400 hover:text-white ml-4">x</button>
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <h3 class="text-xs font-semibold text-slate-400 mb-1">Impacted Task</h3>
                        <p id="impacted-task" class="text-sm font-medium text-white">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <h3 class="text-xs font-semibold text-slate-400 mb-1">Current Delay</h3>
                        <p id="current-delay" class="text-sm font-medium text-rose-300">--</p>
                    </div>
                </div>
                
                <div id="bottleneck-impact" class="mt-4">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <h3 class="text-sm font-semibold text-white mb-2">Expected Results</h3>
                        <ul class="space-y-2 text-xs text-slate-300">
                            <li id="result-1" class="flex items-start gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></div>
                                <span>Delay reduction of 0.8h</span>
                            </li>
                            <li id="result-2" class="flex items-start gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></div>
                                <span>On-time rate improvement by 12%</span>
                            </li>
                            <li id="result-3" class="flex items-start gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 flex-shrink-0"></div>
                                <span>Task completion increase by 15%</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div id="bottleneck-feedback" class="mt-4 hidden">
                    <div class="rounded-lg bg-emerald-900/30 border border-emerald-800/50 p-3">
                        <p id="bottleneck-feedback-message" class="text-sm text-emerald-300"></p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" data-bottleneck-modal-close class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Cancel</button>
                    <button type="button" id="bottleneck-confirm" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">Proceed</button>
                </div>
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
        const trendCanvas = document.getElementById('bottleneckTrendChart');
        const trendFallback = document.getElementById('bottleneckTrendFallback');

        if (trendCanvas && typeof Chart !== 'undefined') {
            if (trendFallback) {
                trendFallback.classList.add('hidden');
            }

            new Chart(trendCanvas, {
                type: 'bar',
                data: {
                    labels: ['E-Bank Scan', 'Form Review', 'Document Upload', 'Reporting'],
                    datasets: [{
                        label: 'Avg Delay (hours)',
                        data: [1.4, 1.1, 0.9, 0.6],
                        backgroundColor: ['#ef4444', '#f59e0b', '#f97316', '#38bdf8']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { labels: { color: '#94a3b8' } } },
                    scales: {
                        x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148, 163, 184, 0.08)' } },
                        y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148, 163, 184, 0.08)' }, beginAtZero: true }
                    }
                }
            });
        } else if (trendFallback) {
            trendFallback.classList.remove('hidden');
        }

        const managerModal = document.getElementById('manager-action-modal');
        const managerTitle = document.getElementById('manager-action-title');
        const managerBody = document.getElementById('manager-action-body');
        const managerConfirmBtn = document.getElementById('manager-action-confirm');

        if (managerModal && managerTitle && managerBody && managerConfirmBtn) {
            function closeManagerModal() {
                managerModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            function openManagerModal(trigger) {
                managerTitle.textContent = trigger.dataset.actionTitle || 'Action';
                managerBody.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
                managerConfirmBtn.textContent = trigger.dataset.actionConfirm || 'Proceed';
                managerModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            document.querySelectorAll('[data-manager-action]').forEach((button) => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    openManagerModal(button);
                });
            });

            managerModal.addEventListener('click', function (event) {
                if (event.target === managerModal) {
                    closeManagerModal();
                }
            });

            managerModal.querySelectorAll('[data-manager-modal-close]').forEach((button) => {
                button.addEventListener('click', closeManagerModal);
            });

            managerConfirmBtn.addEventListener('click', closeManagerModal);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeManagerModal();
                }
            });
        }

        const bottleneckModal = document.getElementById('bottleneck-modal');
        const bottleneckTitle = document.getElementById('bottleneck-title');
        const bottleneckMessage = document.getElementById('bottleneck-message');
        const bottleneckConfirmBtn = document.getElementById('bottleneck-confirm');
        const feedback = document.getElementById('bottleneck-feedback');
        const feedbackMessage = document.getElementById('bottleneck-feedback-message');
        const impactedTask = document.getElementById('impacted-task');
        const currentDelay = document.getElementById('current-delay');

        if (!bottleneckModal || !bottleneckTitle || !bottleneckMessage || !bottleneckConfirmBtn || !feedback || !feedbackMessage || !impactedTask || !currentDelay) {
            return;
        }

        let currentActionType = null;
        let currentImpactTask = null;

        // Bottleneck action data
        const bottleneckActions = {
            'backup-scanner': {
                impactedTask: 'E-Bank Scanning',
                currentDelay: '+1.4h',
                results: [
                    'Average delay reduces from 1.4h to 0.6h',
                    'On-time rate improves from 82% to 94%',
                    'Peak hour throughput increases by 40%'
                ],
                successMessage: 'Done. Backup scanner assigned for E-Bank tasks during peak hours (10 AM - 2 PM). Monitoring performance improvements.'
            },
            'pre-checklist': {
                impactedTask: 'Client Form Review',
                currentDelay: '+1.1h',
                results: [
                    'Rework rate decreases by 40%',
                    'Average duration reduces from 2.6h to 2.0h',
                    'Client satisfaction increases by 25%'
                ],
                successMessage: 'Done. Pre-checklist implemented for client form validation. All new tasks will include mandatory validation steps.'
            },
            'batch-uploads': {
                impactedTask: 'Document Upload',
                currentDelay: '+0.9h',
                results: [
                    'Upload processing time reduces by 70%',
                    'Handoff delays decrease from 0.9h to 0.3h',
                    'Batch efficiency improves by 50%'
                ],
                successMessage: 'Done. Batch uploads scheduled hourly. Documents will be processed in groups of 10 to optimize handoff time.'
            },
            'quality-checks': {
                impactedTask: 'E-Bank Scanning',
                currentDelay: '+1.4h',
                results: [
                    'Error detection rate increases to 98%',
                    'Re-scan requests decrease by 65%',
                    'First-pass quality improves by 45%'
                ],
                successMessage: 'Done. Automated quality checks activated for all scanning tasks. Real-time validation now active.'
            }
        };

        function closeBottleneckModal() {
            bottleneckModal.classList.add('hidden');
            feedback.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            currentActionType = null;
            currentImpactTask = null;
        }

        function openBottleneckModal(trigger) {
            currentActionType = trigger.dataset.actionType;
            currentImpactTask = trigger.dataset.impactTask;

            // Get action data
            const actionData = bottleneckActions[currentActionType] || {};

            // Set modal content
            bottleneckTitle.textContent = trigger.dataset.actionTitle || 'Bottleneck Optimization';
            bottleneckMessage.textContent = trigger.dataset.actionMessage || 'No details available.';
            bottleneckConfirmBtn.textContent = trigger.dataset.actionConfirm || 'Proceed';

            // Set impacted task and current delay
            impactedTask.textContent = actionData.impactedTask || currentImpactTask || '--';
            currentDelay.textContent = actionData.currentDelay || '--';

            // Set expected results
            if (actionData.results) {
                const resultElements = [
                    document.getElementById('result-1'),
                    document.getElementById('result-2'),
                    document.getElementById('result-3')
                ];

                actionData.results.forEach((result, index) => {
                    if (resultElements[index]) {
                        resultElements[index].querySelector('span').textContent = result;
                        resultElements[index].classList.remove('hidden');
                    }
                });

                // Hide any unused result slots
                for (let i = actionData.results.length; i < 3; i++) {
                    if (resultElements[i]) {
                        resultElements[i].classList.add('hidden');
                    }
                }
            }

            // Show modal
            bottleneckModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            feedback.classList.add('hidden');
        }

        function showFeedback(message) {
            feedbackMessage.textContent = message;
            feedback.classList.remove('hidden');
        }

        // Handle bottleneck action buttons
        document.querySelectorAll('[data-bottleneck-action]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                openBottleneckModal(this);
            });
        });

        // Handle confirmation
        bottleneckConfirmBtn.addEventListener('click', function() {
            if (!currentActionType) return;

            const actionData = bottleneckActions[currentActionType];
            if (!actionData) return;

            // Show success message
            showFeedback(actionData.successMessage);

            // Update dashboard metrics
            updateBottleneckMetrics(currentActionType, currentImpactTask);

            // Update the bottleneck table
            updateBottleneckTable(currentActionType, currentImpactTask);

            // Auto-close after 2 seconds
            setTimeout(() => {
                closeBottleneckModal();

                // Update the button to show completed state
                const button = document.querySelector(`[data-action-type="${currentActionType}"]`);
                if (button) {
                    const originalText = button.textContent;
                    button.innerHTML = `
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Applied
                    `;
                    button.classList.remove('bg-blue-600', 'hover:bg-blue-500');
                    button.classList.add('bg-emerald-600', 'hover:bg-emerald-500');
                    button.disabled = true;
                    button.title = `Optimization applied for ${currentImpactTask}`;
                }
            }, 2000);
        });

        function updateBottleneckMetrics(actionType, taskName) {
            // Update metrics on the page
            const avgDelayElement = document.querySelector('.text-2xl.font-semibold.text-amber-300');
            const atRiskTasksElement = document.querySelector('.text-2xl.font-semibold.text-white:nth-of-type(2)');
            const recoveryRateElement = document.querySelector('.text-2xl.font-semibold.text-emerald-400');

            // Get chart instance
            const chart = typeof Chart !== 'undefined' ? Chart.getChart('bottleneckTrendChart') : null;

            switch(actionType) {
                case 'backup-scanner':
                case 'quality-checks':
                    if (avgDelayElement && avgDelayElement.textContent === '2.1h') {
                        avgDelayElement.textContent = '1.8h';
                    }
                    if (atRiskTasksElement && atRiskTasksElement.textContent === '6') {
                        atRiskTasksElement.textContent = '5';
                    }
                    if (recoveryRateElement && recoveryRateElement.textContent === '88%') {
                        recoveryRateElement.textContent = '91%';
                    }
                    // Update chart data
                    if (chart && taskName === 'E-Bank Scanning') {
                        chart.data.datasets[0].data[0] = 0.6; // Reduce E-Bank delay
                        chart.update();
                    }
                    break;

                case 'pre-checklist':
                    if (avgDelayElement && avgDelayElement.textContent === '2.1h') {
                        avgDelayElement.textContent = '1.9h';
                    }
                    if (recoveryRateElement && recoveryRateElement.textContent === '88%') {
                        recoveryRateElement.textContent = '90%';
                    }
                    // Update chart data
                    if (chart && taskName === 'Client Form Review') {
                        chart.data.datasets[0].data[1] = 0.5; // Reduce Form Review delay
                        chart.update();
                    }
                    break;

                case 'batch-uploads':
                    if (avgDelayElement && avgDelayElement.textContent === '2.1h') {
                        avgDelayElement.textContent = '1.7h';
                    }
                    if (atRiskTasksElement && atRiskTasksElement.textContent === '6') {
                        atRiskTasksElement.textContent = '4';
                    }
                    // Update chart data
                    if (chart && taskName === 'Document Upload') {
                        chart.data.datasets[0].data[2] = 0.3; // Reduce Document Upload delay
                        chart.update();
                    }
                    break;
            }

            // Update top bottleneck card if improved
            const topBottleneckElement = document.querySelector('.text-2xl.font-semibold.text-white:first-of-type');
            if (topBottleneckElement && taskName && topBottleneckElement.textContent === taskName) {
                const delays = {
                    'E-Bank Scanning': 0.6,
                    'Client Form Review': 0.5,
                    'Document Upload': 0.3
                };

                if (delays[taskName]) {
                    const delayElement = topBottleneckElement.nextElementSibling;
                    if (delayElement && delayElement.classList.contains('text-xs')) {
                        delayElement.innerHTML = `Avg delay: <span class="text-emerald-400">${delays[taskName]}h</span>`;
                    }
                }
            }
        }

        function updateBottleneckTable(actionType, taskName) {
            // Find the table row for this task
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                const taskCell = row.querySelector('td:first-child');
                if (taskCell && taskCell.textContent.includes(taskName)) {
                    // Update delay cell
                    const delayCell = row.querySelector('td:nth-child(5)');
                    if (delayCell && delayCell.classList.contains('text-rose-300', 'text-amber-300')) {
                        // Reduce the delay
                        const currentDelay = parseFloat(delayCell.textContent.replace('+', '').replace('h', ''));
                        const newDelay = Math.max(0, currentDelay - 0.8).toFixed(1);

                        delayCell.textContent = `+${newDelay}h`;
                        delayCell.classList.remove('text-rose-300', 'text-amber-300');

                        if (newDelay >= 1.0) {
                            delayCell.classList.add('text-amber-300');
                        } else if (newDelay >= 0.5) {
                            delayCell.classList.add('text-yellow-300');
                        } else {
                            delayCell.classList.add('text-emerald-300');
                        }
                    }

                    // Update action button
                    const actionButton = row.querySelector('button');
                    if (actionButton) {
                        actionButton.textContent = 'Optimized';
                        actionButton.classList.remove('text-blue-400', 'hover:text-blue-300');
                        actionButton.classList.add('text-emerald-400');
                        actionButton.disabled = true;
                    }
                }
            });
        }

        // Modal close handlers
        bottleneckModal.addEventListener('click', function(e) {
            if (e.target === bottleneckModal) closeBottleneckModal();
        });

        bottleneckModal.querySelectorAll('[data-bottleneck-modal-close]').forEach(button => {
            button.addEventListener('click', closeBottleneckModal);
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !bottleneckModal.classList.contains('hidden')) {
                closeBottleneckModal();
            }
        });
    });
    </script>
    @endpush
</x-layouts.manager>

