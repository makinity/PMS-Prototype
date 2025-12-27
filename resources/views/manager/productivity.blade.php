<x-layouts.manager>
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">Productivity Analytics</h1>
                <p class="text-sm text-slate-400">Objective productivity trends based on auto-logged task data.</p>
            </div>
            <div class="text-xs text-slate-400">Last refresh: 3 minutes ago</div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap gap-3">
                <div>
                    <label class="text-xs uppercase text-slate-400">Time Period</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>This Week</option>
                        <option selected>This Month</option>
                        <option>This Quarter</option>
                        <option>Custom</option>
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
                    <label class="text-xs uppercase text-slate-400">Team</label>
                    <select class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                        <option>All Teams</option>
                        <option>Operations</option>
                        <option>Finance</option>
                        <option>IT Support</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Outputs Completed</p>
                <p class="mt-2 text-2xl font-semibold text-white">318</p>
                <p class="text-xs text-slate-500">Target: 340</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">On-Time Rate</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-400">91%</p>
                <p class="text-xs text-slate-500">Late: 28 outputs</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Avg Task Duration</p>
                <p class="mt-2 text-2xl font-semibold text-white">2.6h</p>
                <p class="text-xs text-slate-500">Down 0.2h</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Output Risk</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">Medium</p>
                <p class="text-xs text-slate-500">Forecasted delays: 7</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Output Trend</h2>
                <canvas id="productivityTrendChart" height="120"></canvas>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Productivity by Task Category</h2>
                <canvas id="productivityBreakdownChart" height="120"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">Client Segment Performance</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-slate-300">
                        <thead class="text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-2 text-left">Client Type</th>
                                <th class="px-4 py-2 text-left">Outputs</th>
                                <th class="px-4 py-2 text-left">On-Time</th>
                                <th class="px-4 py-2 text-left">Avg Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3">Government</td>
                                <td class="px-4 py-3">140</td>
                                <td class="px-4 py-3">93%</td>
                                <td class="px-4 py-3">2.5h</td>
                            </tr>
                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3">Private</td>
                                <td class="px-4 py-3">110</td>
                                <td class="px-4 py-3">89%</td>
                                <td class="px-4 py-3">2.8h</td>
                            </tr>
                            <tr class="border-t border-slate-800">
                                <td class="px-4 py-3">Internal</td>
                                <td class="px-4 py-3">68</td>
                                <td class="px-4 py-3">94%</td>
                                <td class="px-4 py-3">2.1h</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Optimization Recommendations</h2>
        <span class="text-xs text-slate-500">Click to implement</span>
    </div>
    <ul class="space-y-3">
        <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 hover:border-slate-700 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <span class="font-medium text-white">Priority Scanning Tasks</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-400">Prioritize scanning tasks for government clients to keep SLA above 95%.</p>
                    <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                        <span>Impact: High</span>
                        <span>Effort: Low</span>
                        <span>Time: 2 hours</span>
                    </div>
                </div>
                <button type="button"
                        data-optimization-action
                        data-action-title="Prioritize Government Scanning"
                        data-action-message="This will prioritize all government client scanning tasks in the queue for the next 7 days. Expected improvement: SLA increase to 95%."
                        data-action-confirm="Schedule Priority"
                        data-action-type="priority-scanning"
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
                        <span class="font-medium text-white">Task Reassignment</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-400">Reassign two tasks from validation queue to reduce average duration by 0.3h.</p>
                    <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                        <span>Impact: Medium</span>
                        <span>Effort: Medium</span>
                        <span>Time: 4 hours</span>
                    </div>
                </div>
                <button type="button"
                        data-optimization-action
                        data-action-title="Reassign Validation Tasks"
                        data-action-message="Move two validation tasks from the overloaded queue to available analysts. This will reduce average duration by 0.3h and improve on-time rate."
                        data-action-confirm="Reassign Tasks"
                        data-action-type="task-reassignment"
                        class="inline-flex w-32 items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 whitespace-nowrap">
                    Implement
                </button>
            </div>
        </li>
        
        <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 hover:border-slate-700 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="font-medium text-white">Mid-Week Review</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-400">Schedule mid-week review to prevent forecasted delays and adjust resource allocation.</p>
                    <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                        <span>Impact: High</span>
                        <span>Effort: Low</span>
                        <span>Time: 1 hour</span>
                    </div>
                </div>
                <button type="button"
                        data-optimization-action
                        data-action-title="Schedule Mid-Week Review"
                        data-action-message="Schedule a review meeting for Wednesday 2 PM to assess progress and adjust resources. Duration: 1 hour. Team leads will be notified automatically."
                        data-action-confirm="Schedule Meeting"
                        data-action-type="mid-week-review"
                        class="inline-flex w-32 items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 whitespace-nowrap">
                    Schedule
                </button>
            </div>
        </li>
        
        <li class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 hover:border-slate-700 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                        <span class="font-medium text-white">Private Client Optimization</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-400">Private clients have the lowest on-time rate (89%). Optimize workflow and add buffer time.</p>
                    <div class="mt-2 flex items-center gap-4 text-xs text-slate-500">
                        <span>Impact: High</span>
                        <span>Effort: High</span>
                        <span>Time: 8 hours</span>
                    </div>
                </div>
                <button type="button"
                        data-optimization-action
                        data-action-title="Optimize Private Client Workflow"
                        data-action-message="Create optimization plan for private clients: 1) Add 30-min buffer to all deadlines, 2) Assign senior analysts, 3) Enable early review triggers."
                        data-action-confirm="Create Plan"
                        data-action-type="private-optimization"
                        class="inline-flex w-32 items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 whitespace-nowrap">
                    Optimize
                </button>
            </div>
        </li>
    </ul>
</div>

<!-- Optimization Action Modal -->
<div id="optimization-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
    <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
        <div class="flex items-start justify-between">
            <div>
                <h2 id="optimization-title" class="text-lg font-semibold text-white">Optimization Action</h2>
                <p id="optimization-message" class="mt-1 text-sm text-slate-400">Action details will appear here.</p>
            </div>
            <button type="button" data-optimization-modal-close class="text-slate-400 hover:text-white">x</button>
        </div>
        
        <div id="optimization-impact" class="mt-4 hidden">
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                <h3 class="text-sm font-semibold text-white mb-2">Expected Impact</h3>
                <ul class="space-y-2 text-xs text-slate-300">
                    <li id="impact-metric-1" class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span>Metric improvement 1</span>
                    </li>
                    <li id="impact-metric-2" class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span>Metric improvement 2</span>
                    </li>
                    <li id="impact-metric-3" class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span>Metric improvement 3</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div id="optimization-feedback" class="mt-4 hidden">
            <div class="rounded-lg bg-emerald-900/30 border border-emerald-800/50 p-3">
                <p id="feedback-message" class="text-sm text-emerald-300"></p>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" data-optimization-modal-close class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">Cancel</button>
            <button type="button" id="optimization-confirm" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">Proceed</button>
        </div>
    </div>
</div>
        </div>
    </section>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') {
            return;
        }

        const trend = document.getElementById('productivityTrendChart');
        if (trend) {
            new Chart(trend, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Completed Outputs',
                        data: [42, 48, 45, 51, 49, 38, 45],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35
                    }, {
                        label: 'Target',
                        data: [46, 46, 46, 46, 46, 46, 46],
                        borderColor: '#94a3b8',
                        borderDash: [6, 6],
                        borderWidth: 1.5,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { labels: { color: '#94a3b8' } } },
                    scales: {
                        x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148, 163, 184, 0.08)' } },
                        y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148, 163, 184, 0.08)' } }
                    }
                }
            });
        }

        const breakdown = document.getElementById('productivityBreakdownChart');
        if (breakdown) {
            new Chart(breakdown, {
                type: 'bar',
                data: {
                    labels: ['Scanning', 'Validation', 'Reporting', 'Follow-up'],
                    datasets: [{
                        label: 'Outputs',
                        data: [120, 90, 70, 38],
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#a855f7']
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
        }

        const modal = document.getElementById('optimization-modal');
        const title = document.getElementById('optimization-title');
        const message = document.getElementById('optimization-message');
        const confirmBtn = document.getElementById('optimization-confirm');
        const impactSection = document.getElementById('optimization-impact');
        const feedback = document.getElementById('optimization-feedback');
        const feedbackMessage = document.getElementById('feedback-message');
        
        let currentAction = null;
        let currentActionType = null;
        
        // Action impact data
        const actionImpacts = {
            'priority-scanning': {
                metrics: [
                    'Government SLA increases from 93% to 95%',
                    'Scanning completion time reduces by 15%',
                    'Overall on-time rate improves by 1%'
                ]
            },
            'task-reassignment': {
                metrics: [
                    'Average validation duration reduces by 0.3h',
                    'On-time rate improves by 2%',
                    'Queue backlog decreases by 20%'
                ]
            },
            'mid-week-review': {
                metrics: [
                    'Forecasted delays reduced by 40%',
                    'Early issue detection improves by 60%',
                    'Resource allocation efficiency increases by 25%'
                ]
            },
            'private-optimization': {
                metrics: [
                    'Private client on-time rate improves from 89% to 92%',
                    'Client satisfaction increases by 15%',
                    'Re-work rate decreases by 30%'
                ]
            }
        };
        
        function closeModal() {
            modal.classList.add('hidden');
            impactSection.classList.add('hidden');
            feedback.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            currentAction = null;
            currentActionType = null;
        }
        
        function openModal(trigger) {
            currentAction = trigger.dataset.actionTitle;
            currentActionType = trigger.dataset.actionType;
            
            // Set modal content
            title.textContent = trigger.dataset.actionTitle || 'Optimization Action';
            message.textContent = trigger.dataset.actionMessage || 'No details available.';
            confirmBtn.textContent = trigger.dataset.actionConfirm || 'Proceed';
            
            // Show impact metrics if available
            if (currentActionType && actionImpacts[currentActionType]) {
                const impacts = actionImpacts[currentActionType];
                const impactItems = impactSection.querySelectorAll('[id^="impact-metric-"]');
                
                impactItems.forEach((item, index) => {
                    if (impacts.metrics[index]) {
                        item.querySelector('span').textContent = impacts.metrics[index];
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
                
                impactSection.classList.remove('hidden');
            } else {
                impactSection.classList.add('hidden');
            }
            
            // Show modal
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            feedback.classList.add('hidden');
        }
        
        function showFeedback(message) {
            feedbackMessage.textContent = message;
            feedback.classList.remove('hidden');
        }
        
        // Handle optimization action buttons
        document.querySelectorAll('[data-optimization-action]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(this);
            });
        });
        
        // Handle confirmation
        confirmBtn.addEventListener('click', function() {
            if (!currentActionType) return;
            
            // Action success messages
            const successMessages = {
                'priority-scanning': '✅ Government scanning tasks prioritized successfully. Tasks will be processed in priority order starting now.',
                'task-reassignment': '✅ Validation tasks reassigned to available analysts. Monitoring performance improvements.',
                'mid-week-review': '✅ Mid-week review scheduled for Wednesday 2 PM. Team leads have been notified.',
                'private-optimization': '✅ Private client optimization plan created. Implementation will begin tomorrow.'
            };
            
            const message = successMessages[currentActionType] || '✅ Action completed successfully.';
            showFeedback(message);
            
            // Update dashboard metrics based on action
            updateMetrics(currentActionType);
            
            // Auto-close after 2 seconds
            setTimeout(() => {
                closeModal();
                
                // Update the button to show completed state
                const button = document.querySelector(`[data-action-type="${currentActionType}"]`);
                if (button) {
                    button.innerHTML = '✅ Done';
                    button.classList.remove('bg-blue-600', 'hover:bg-blue-500');
                    button.classList.add('bg-emerald-600', 'hover:bg-emerald-500');
                    button.disabled = true;
                }
            }, 2000);
        });
        
        function updateMetrics(actionType) {
            // Update relevant metrics on the page based on action
            const outputsElement = document.querySelector('.text-2xl.font-semibold.text-white:first-of-type');
            const onTimeElement = document.querySelector('.text-2xl.font-semibold.text-emerald-400');
            const durationElement = document.querySelector('.text-2xl.font-semibold.text-white:contains("2.6")');
            const riskElement = document.querySelector('.text-2xl.font-semibold.text-amber-300');
            
            switch(actionType) {
                case 'priority-scanning':
                    if (onTimeElement && onTimeElement.textContent === '91%') {
                        onTimeElement.textContent = '92%';
                    }
                    break;
                    
                case 'task-reassignment':
                    if (durationElement && durationElement.textContent === '2.6h') {
                        durationElement.textContent = '2.4h';
                    }
                    if (onTimeElement && onTimeElement.textContent === '91%') {
                        onTimeElement.textContent = '92%';
                    }
                    break;
                    
                case 'mid-week-review':
                    if (riskElement && riskElement.textContent === 'Medium') {
                        riskElement.textContent = 'Low';
                        riskElement.classList.remove('text-amber-300');
                        riskElement.classList.add('text-emerald-400');
                    }
                    break;
                    
                case 'private-optimization':
                    if (onTimeElement && onTimeElement.textContent === '91%') {
                        onTimeElement.textContent = '92%';
                    }
                    break;
            }
        }
        
        // Modal close handlers
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
        
        modal.querySelectorAll('[data-optimization-modal-close]').forEach(button => {
            button.addEventListener('click', closeModal);
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    });
    </script>
    @endpush
</x-layouts.manager>
