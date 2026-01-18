<x-layouts.supervisor>
    <style>
        #ors-calendar .fc-col-header-cell {
            background-color: rgba(15, 23, 42, 0.85);
        }

        #ors-calendar .fc-col-header-cell-cushion,
        #ors-calendar .fc-daygrid-day-number {
            color: #e2e8f0;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            border-width: 1px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-dot {
            width: 0.55rem;
            height: 0.55rem;
            border-radius: 9999px;
        }
    </style>

    <section class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-white">Daily ORS Monitoring</h1>
            <p class="text-sm text-slate-400">
                Supervisors monitor daily outputs using the Output Rating Sheet (ORS). This calendar reflects accomplishments recorded by the team and is read-only for monitoring and coaching purposes.
            </p>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-slate-400">ORS Daily Monitoring Legend</p>
                    <p class="text-[11px] text-slate-500">
                        Accomplishment logged (green) · Ready for monthly consolidation (blue)
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400">
                    <div class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>Accomplishment logged</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        <span>Ready for consolidation</span>
                    </div>
                </div>
                <a href="{{ route('supervisor.ors.export.pdf') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500 transition">
                    Export ORS (PDF)
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <div class="mb-3 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-white">ORS Calendar</h2>
                        <p class="text-sm text-slate-400">
                            Click any date to view what the employee logged. Entries remain locked and read-only for supervisors.
                        </p>
                        <p class="text-xs text-slate-400 mt-1">
                            Only submitted ORS entries appear here. Draft and in-progress work is not yet visible for monitoring.
                        </p>
                    </div>
                </div>
            <div id="ors-calendar"></div>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 text-xs text-slate-400">
            This calendar reflects daily employee accomplishments recorded in ORS for monitoring and coaching purposes. Monthly review occurs after Stage II.
        </div>

        <div id="ors-monitoring-modal"
             class="ors-modal fixed inset-0 z-[60] hidden items-center justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6"
             role="dialog"
             aria-modal="true">
            <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">ORS Monitoring Detail</h2>
                        <p class="text-xs text-slate-400">Read-only insight for supervisors.</p>
                    </div>
                    <button type="button" onclick="closeOrsModal('ors-monitoring-modal')" class="text-slate-400 hover:text-white">x</button>
                </div>
                <div class="mt-5 space-y-3 text-sm text-slate-200">
                    <div>
                        <p class="text-xs text-slate-400">Employee</p>
                        <p id="monitoringEmployee">--</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Output</p>
                        <p id="monitoringOutput">--</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Actual accomplishment</p>
                        <p id="monitoringAccomplishment" class="text-slate-100">--</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Time spent</p>
                        <p id="monitoringDuration">--</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Evidence attached</p>
                        <p id="monitoringEvidence" class="text-emerald-300 font-semibold">--</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Status</p>
                        <span id="monitoringStatus" class="status-chip border border-emerald-500/30 bg-emerald-500/10 text-emerald-200"></span>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button"
                            onclick="closeOrsModal('ors-monitoring-modal')"
                            class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('ors-monitoring-modal');
                const employeeEl = document.getElementById('monitoringEmployee');
                const outputEl = document.getElementById('monitoringOutput');
                const accomplishmentEl = document.getElementById('monitoringAccomplishment');
                const durationEl = document.getElementById('monitoringDuration');
                const evidenceEl = document.getElementById('monitoringEvidence');
                const statusEl = document.getElementById('monitoringStatus');

                const STATUS_META = {
                    submitted: {
                        label: 'Accomplishment logged',
                        color: '#10b981',
                        badge: 'border-emerald-500/60 bg-emerald-500/10 text-emerald-100'
                    },
                    locked: {
                        label: 'Ready for monthly consolidation',
                        color: '#3b82f6',
                        badge: 'border-blue-500/60 bg-blue-500/10 text-blue-100'
                    },
                    missing: {
                        label: 'No output recorded',
                        color: '#64748b',
                        badge: 'border-slate-600/60 bg-slate-800 text-slate-300'
                    }
                };

                const tasks = [
                    {
                        id: 'task-3',
                        date: '2026-01-03',
                        title: 'E-Bank Scanning',
                        employee: 'Ramon Reyes',
                        output: 'Bank Statement Form (BSF-01)',
                        accomplishment: 'Morning batch of e-bank transactions scanned and submitted for consolidation.',
                        duration: '2h 00m',
                        evidence: true,
                        status: 'submitted'
                    },
                    {
                        id: 'task-4',
                        date: '2026-01-02',
                        title: 'OTC Revenue Transaction Processing',
                        employee: 'Ramon Reyes',
                        output: 'Official Receipt (OR)',
                        accomplishment: 'Supervisor validated; ready for consolidation.',
                        duration: '3h 00m',
                        evidence: true,
                        status: 'locked'
                    }
                ];

                const calendarEl = document.getElementById('ors-calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                    },
                    dayMaxEventRows: 3,
                    editable: false,
                    selectable: true,
                    events: tasks.map((task) => {
                        const meta = STATUS_META[task.status] || STATUS_META.missing;
                        return {
                            id: task.id,
                            title: task.output,
                            start: task.date,
                            color: meta.color,
                            extendedProps: {
                                ...task,
                                label: meta.label,
                                badge: meta.badge,
                                muted: meta.muted || false
                            }
                        };
                    }),
                    eventContent(arg) {
                        const meta = arg.event.extendedProps;
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('text-[11px]', 'leading-tight', 'px-1', 'py-[2px]');
                        if (meta.muted) {
                            wrapper.classList.add('opacity-60');
                            wrapper.setAttribute('title', 'Monthly review occurs after Stage II.');
                        }
                        wrapper.innerHTML = `
                            <div class="flex flex-col gap-1">
                                <span class="text-slate-100 font-semibold">${arg.event.title} â€” ${meta.label}</span>
                                <span class="rounded-full border px-2 py-[1px] text-[10px]" style="color:${meta.color}; border-color:${meta.color};">${meta.label}</span>
                            </div>
                        `;
                        return { domNodes: [wrapper] };
                    },
                    dateClick(info) {
                        const event = tasks.find((task) => task.date === info.dateStr);
                        if (event) {
                            openMonitoringModal(event);
                        } else {
                            openMonitoringModal({
                                employee: 'Ramon Reyes',
                                output: 'No output recorded',
                                accomplishment: 'No ORS entry for this date.',
                                duration: '--',
                                evidence: false,
                                status: 'missing'
                            });
                        }
                    },
                    eventClick(info) {
                        openMonitoringModal(info.event.extendedProps);
                    }
                });
                calendar.render();

                function openMonitoringModal(data) {
                    if (!modal) return;
                    employeeEl.textContent = data.employee || '--';
                    outputEl.textContent = data.output || '--';
                    accomplishmentEl.textContent = data.accomplishment || '--';
                    durationEl.textContent = data.duration || '--';
                    evidenceEl.textContent = data.evidence ? 'Evidence attached' : 'No evidence';

                    const meta = STATUS_META[data.status] || STATUS_META.missing;
                    statusEl.textContent = meta.label;
                    statusEl.className = `status-chip ${meta.badge}`;

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                window.closeOrsModal = (modalId) => {
                    const modal = document.getElementById(modalId);
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                };

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeOrsModal('ors-monitoring-modal');
                    }
                });
            });
        </script>
    @endpush
</x-layouts.supervisor>
