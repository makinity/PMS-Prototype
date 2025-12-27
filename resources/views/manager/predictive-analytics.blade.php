<x-layouts.manager>
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Predictive Analytics</h1>
                <p class="text-sm text-slate-400">Forecast delays and output risks using time-stamped task data.</p>
            </div>
            <div class="text-xs text-slate-400">Forecast window: next 14 days</div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap gap-3">
                <div>
                    <label class="text-xs uppercase text-slate-400">Time Period</label>
                    <select id="timePeriodSelect" class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>Next 7 Days</option>
                        <option selected>Next 14 Days</option>
                        <option>Next 30 Days</option>
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
                    <label class="text-xs uppercase text-slate-400">Target Output</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>Weekly</option>
                        <option selected>Monthly</option>
                        <option>Quarterly</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Forecasted Delay Risk</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">Medium</p>
                <p class="text-xs text-slate-500">6 tasks at risk</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Projected On-Time</p>
                <p class="mt-2 text-2xl font-semibold text-white">90%</p>
                <p class="text-xs text-slate-500">Target: 94%</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Output Gap</p>
                <p class="mt-2 text-2xl font-semibold text-rose-300">-22</p>
                <p class="text-xs text-slate-500">Against monthly target</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Confidence Score</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-400">84%</p>
                <p class="text-xs text-slate-500">Based on last 90 days</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Forecasted Outputs vs Target</h2>
                <canvas id="predictiveForecastChart" height="130"></canvas>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Risk Drivers</h2>
                <ul class="space-y-3 text-sm text-slate-300">
                    <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        E-Bank Scanning duration trending 15% over baseline.
                    </li>
                    <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        Two analysts nearing capacity for reporting tasks.
                    </li>
                    <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        Client approvals lagging on private accounts.
                    </li>
                </ul>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">At-Risk Tasks</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Task</th>
                            <th class="px-4 py-2 text-left">Client</th>
                            <th class="px-4 py-2 text-left">Team</th>
                            <th class="px-4 py-2 text-left">Predicted Delay</th>
                            <th class="px-4 py-2 text-left">Risk</th>
                            <th class="px-4 py-2 text-left">Recommendation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">E-Bank Scanning</td>
                            <td class="px-4 py-3">ABC Corp</td>
                            <td class="px-4 py-3">Operations</td>
                            <td class="px-4 py-3 text-rose-300">+1.6h</td>
                            <td class="px-4 py-3 text-rose-300">High</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="Assign backup scanner"
                                        data-action-message="Open staffing panel to add a backup scanner for this task."
                                        data-action-confirm="Assign staff"
                                        class="text-blue-400 hover:text-blue-300">
                                    Assign backup scanner
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Client Form Review</td>
                            <td class="px-4 py-3">XYZ Ltd</td>
                            <td class="px-4 py-3">Operations</td>
                            <td class="px-4 py-3 text-amber-300">+0.9h</td>
                            <td class="px-4 py-3 text-amber-300">Medium</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="Shift reviewer slot"
                                        data-action-message="Move a reviewer slot to reduce delay risk for this client."
                                        data-action-confirm="Open planner"
                                        class="text-blue-400 hover:text-blue-300">
                                    Shift reviewer slot
                                </button>
                            </td>
                        </tr>
                        <tr class="border-t border-slate-800">
                            <td class="px-4 py-3">Report Generation</td>
                            <td class="px-4 py-3">Internal</td>
                            <td class="px-4 py-3">Finance</td>
                            <td class="px-4 py-3 text-amber-300">+0.7h</td>
                            <td class="px-4 py-3 text-amber-300">Medium</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        data-manager-action
                                        data-action-title="Pre-approve data sources"
                                        data-action-message="Open the approval checklist for data sources needed for reporting."
                                        data-action-confirm="Open checklist"
                                        class="text-blue-400 hover:text-blue-300">
                                    Pre-approve data sources
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Optimization Recommendations</h2>
            <ul class="space-y-3 text-sm text-slate-300">
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 flex items-center justify-between">
                    <div>
                        <span class="font-medium text-white">Add scanning slots</span>
                        <p class="mt-1 text-slate-400">Add two scanning slots on Mondays to reduce forecasted backlog.</p>
                    </div>
                    <button type="button"
                            data-manager-action
                            data-action-title="Add Monday Scanning Slots"
                            data-action-message="This will add two additional scanning slots every Monday for the next month. Estimated impact: 15% backlog reduction."
                            data-action-confirm="Schedule slots"
                            class="inline-flex w-32 items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 whitespace-nowrap">
                        Implement
                    </button>
                </li>
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 flex items-center justify-between">
                    <div>
                        <span class="font-medium text-white">Early Approval Reminders</span>
                        <p class="mt-1 text-slate-400">Trigger early reminder for private client approvals 48 hours before due date.</p>
                    </div>
                    <button type="button"
                            data-manager-action
                            data-action-title="Enable Early Reminders"
                            data-action-message="This will activate automated reminders sent 48 hours before approval deadlines for all private clients."
                            data-action-confirm="Activate reminders"
                            class="inline-flex w-32 items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 whitespace-nowrap">
                        Enable
                    </button>
                </li>
                <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 flex items-center justify-between">
                    <div>
                        <span class="font-medium text-white">Analyst Rotation</span>
                        <p class="mt-1 text-slate-400">Swap one analyst to reporting queue on high-risk weeks.</p>
                    </div>
                    <button type="button"
                            data-manager-action
                            data-action-title="Schedule Analyst Rotation"
                            data-action-message="Configure automatic analyst rotation to reporting queue during weeks with high-risk forecasts."
                            data-action-confirm="Set rotation"
                            class="inline-flex w-32 items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 whitespace-nowrap">
                        Configure
                    </button>
                </li>
            </ul>
        </div>
    </section>

    <!-- Enhanced Modal with Action Feedback -->
    <div id="manager-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 id="manager-action-title" class="text-lg font-semibold text-white">Action</h2>
                    <p id="manager-action-body" class="mt-1 text-sm text-slate-400">Prototype action preview.</p>
                </div>
                <button type="button" data-manager-modal-close class="text-slate-400 hover:text-white">x</button>
            </div>
            <div class="mt-4 hidden" id="action-feedback">
                <div class="rounded-lg bg-emerald-900/30 border border-emerald-800/50 p-3">
                    <p class="text-sm text-emerald-300" id="feedback-message"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-manager-modal-close class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Cancel</button>
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

        const ctx = document.getElementById('predictiveForecastChart');
        if (!ctx) {
            return;
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Forecasted Output',
                    data: [310, 305, 298, 290],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.15)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true
                }, {
                    label: 'Target Output',
                    data: [320, 320, 320, 320],
                    borderColor: '#94a3b8',
                    borderDash: [6, 6],
                    borderWidth: 1.5,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                plugins: { 
                    legend: { labels: { color: '#94a3b8' } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y;
                                if (context.datasetIndex === 0) {
                                    const target = 320;
                                    const gap = target - context.parsed.y;
                                    if (gap > 0) {
                                        label += ` (${gap} below target)`;
                                    } else if (gap < 0) {
                                        label += ` (${Math.abs(gap)} above target)`;
                                    } else {
                                        label += ' (on target)';
                                    }
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        ticks: { color: '#94a3b8' }, 
                        grid: { color: 'rgba(148, 163, 184, 0.08)' } 
                    },
                    y: { 
                        ticks: { color: '#94a3b8' }, 
                        grid: { color: 'rgba(148, 163, 184, 0.08)' },
                        title: {
                            display: true,
                            text: 'Output Units',
                            color: '#94a3b8'
                        }
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
        const feedback = document.getElementById('action-feedback');
        const feedbackMessage = document.getElementById('feedback-message');

        if (!modal || !title || !body || !confirmBtn) {
            return;
        }

        let currentAction = null;

        function closeModal() {
            modal.classList.add('hidden');
            feedback.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            currentAction = null;
        }

        function showFeedback(message) {
            feedbackMessage.textContent = message;
            feedback.classList.remove('hidden');
        }

        function openModal(trigger) {
            currentAction = trigger.dataset.actionTitle;
            title.textContent = trigger.dataset.actionTitle || 'Action';
            body.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
            confirmBtn.textContent = trigger.dataset.actionConfirm || 'Proceed';
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            feedback.classList.add('hidden');
        }

        // Handle optimization recommendations
        document.querySelectorAll('[data-manager-action]').forEach((button) => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                openModal(button);
            });
        });

        // Handle confirmation of actions
        confirmBtn.addEventListener('click', function() {
            if (currentAction) {
                // Simulate action success
                const successMessages = {
                    'Add Monday Scanning Slots': '✅ Monday scanning slots scheduled. Backlog reduction estimated at 15%.',
                    'Enable Early Reminders': '✅ Early approval reminders activated for private clients.',
                    'Schedule Analyst Rotation': '✅ Analyst rotation configured for high-risk weeks.',
                    'Assign backup scanner': '✅ Backup scanner assigned to E-Bank Scanning task.',
                    'Shift reviewer slot': '✅ Reviewer slot shifted to optimize client coverage.',
                    'Pre-approve data sources': '✅ Data sources pre-approved for Report Generation.'
                };
                
                const message = successMessages[currentAction] || '✅ Action completed successfully.';
                showFeedback(message);
                
                // Update UI based on action
                updateDashboardMetrics(currentAction);
                
                // Auto-close after 2 seconds
                setTimeout(() => {
                    closeModal();
                }, 2000);
            }
        });

        function updateDashboardMetrics(action) {
            const riskElement = document.querySelector('.text-2xl.font-semibold.text-amber-300');
            const gapElement = document.querySelector('.text-2xl.font-semibold.text-rose-300');
            const onTimeElement = document.querySelector('.text-2xl.font-semibold.text-white');
            
            if (action.includes('scanning') || action.includes('Analyst')) {
                // Improve metrics after optimization
                if (riskElement && riskElement.textContent.includes('Medium')) {
                    riskElement.textContent = 'Low';
                    riskElement.classList.remove('text-amber-300');
                    riskElement.classList.add('text-emerald-400');
                }
                
                if (gapElement && gapElement.textContent.includes('-22')) {
                    gapElement.textContent = '-12';
                    gapElement.classList.remove('text-rose-300');
                    gapElement.classList.add('text-amber-300');
                }
                
                if (onTimeElement && onTimeElement.textContent.includes('90%')) {
                    onTimeElement.textContent = '92%';
                }
            }
        }

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        modal.querySelectorAll('[data-manager-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Add real-time forecast updates
        const timePeriodSelect = document.getElementById('timePeriodSelect');
        if (timePeriodSelect) {
            timePeriodSelect.addEventListener('change', function() {
                // Simulate forecast update based on time period
                const periods = {
                    'Next 7 Days': { delayRisk: 'Low', outputGap: '-8', onTime: '95%' },
                    'Next 14 Days': { delayRisk: 'Medium', outputGap: '-22', onTime: '90%' },
                    'Next 30 Days': { delayRisk: 'High', outputGap: '-35', onTime: '85%' }
                };
                
                const selected = this.value;
                const forecast = periods[selected] || periods['Next 14 Days'];
                
                const riskElement = document.querySelector('.text-2xl.font-semibold.text-amber-300');
                const gapElement = document.querySelector('.text-2xl.font-semibold.text-rose-300');
                const onTimeElement = document.querySelector('.text-2xl.font-semibold.text-white');
                
                if (riskElement) {
                    riskElement.textContent = forecast.delayRisk;
                    riskElement.className = 'mt-2 text-2xl font-semibold ' + 
                        (forecast.delayRisk === 'High' ? 'text-rose-300' : 
                         forecast.delayRisk === 'Medium' ? 'text-amber-300' : 'text-emerald-400');
                }
                
                if (gapElement) gapElement.textContent = forecast.outputGap;
                if (onTimeElement) onTimeElement.textContent = forecast.onTime;
            });
        }
    });
    </script>
    @endpush
</x-layouts.manager>
