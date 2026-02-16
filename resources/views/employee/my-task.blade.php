@extends('layouts.employee')

@section('main-content')
    <section class="space-y-6">

        <div>
            <h1 class="mt-1 text-2xl font-bold text-white">My Tasks</h1>
            <p class="mt-1 text-sm text-gray-400">
                Read-only mirror of ORS entries.
                <span class="block">Tasks are created and submitted in ORS.</span>
                <span class="block">This page mirrors ORS status and declared quantity only.</span>
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <select id="myTasksStatusFilter"
                class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-blue-500">
                <option value="all" class="bg-gray-700">Status: All</option>
                <option value="draft" class="bg-gray-700">Draft</option>
                <option value="recording" class="bg-gray-700">Recording</option>
                <option value="submitted" class="bg-gray-700">Submitted</option>
                <option value="missing" class="bg-gray-700">Missing / Overdue</option>
                <option value="returned" class="bg-gray-700">Returned</option>
            </select>

            <input id="myTasksDateFilter" type="date"
                class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-700 bg-gray-800">
            <div class="border-b border-gray-700 px-4 py-3 text-xs text-gray-400">
                Status reflects ORS state only; no submissions, uploads, or task creation here.
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-white">Task</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Output State</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Quantity (ORS)</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody id="myTasksTbody" class="divide-y divide-gray-700"></tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center rounded-lg border border-gray-700 bg-gray-800 p-4 text-sm text-gray-400">
            <svg class="mr-3 inline h-4 w-4 flex-shrink-0 text-blue-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 0 0 1 0 2Z" />
            </svg>
            <span>My Tasks mirrors ORS activity and declared quantity. Tasks are created and submitted in ORS.</span>
        </div>

        <div id="task-view-modal" tabindex="-1" aria-hidden="true"
            class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
            <div class="relative w-full max-w-4xl">
                <div
                    class="relative flex max-h-[85vh] flex-col overflow-hidden rounded-2xl border border-gray-700 bg-gray-900 shadow-lg">
                    <div class="flex items-start justify-between border-b border-gray-700 px-6 py-4">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Task Details</h3>
                            <p class="text-xs text-gray-400">My Tasks mirrors ORS activity (read-only).</p>
                        </div>
                        <button type="button" id="closeTaskViewTop"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-800 hover:text-white">
                            <span class="sr-only">Close modal</span>
                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 12 12M13 1 1 13" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-5 text-sm text-gray-300">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Employee</p>
                                <p class="text-sm font-medium text-white">Ramon Reyes</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Task / Indicator</p>
                                <p id="mvTask" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">MFO / UWP Output</p>
                                <p id="mvMfo" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Date</p>
                                <p id="mvDate" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Client Request ID</p>
                                <p id="mvRequestId" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                                <p id="mvStatus" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Output State</p>
                                <p id="mvOutputState" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Output Type</p>
                                <p id="mvOutputType" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Quantity (ORS)</p>
                                <p id="mvQuantity" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Evidence</p>
                                <p id="mvEvidence" class="text-sm font-medium text-white">--</p>
                                <p id="mvEvidenceFile" class="mt-1 text-xs text-gray-400">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Submitted At</p>
                                <p id="mvSubmittedAt" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Duration</p>
                                <p id="mvDuration" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Notes</p>
                                <p id="mvNotes" class="mt-1 text-sm text-gray-300">--</p>
                            </div>
                        </div>

                        <div id="mvSupervisorMonitoringSection"
                            class="mt-6 hidden rounded-2xl border border-gray-700 bg-gray-900/60 p-5 shadow-inner shadow-black/40">
                            <div class="flex items-center justify-between">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor Monitoring</p>
                                <span class="text-[0.65rem] font-semibold uppercase tracking-wider text-gray-500">Stage II</span>
                            </div>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor Name</p>
                                    <p id="mvSupName" class="text-sm font-medium text-white">--</p>
                                </div>
                                <div class="grid gap-4 md:grid-cols-3">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Quality</p>
                                        <span id="mvSupQuality"
                                            class="mt-1 inline-flex items-center justify-center rounded-full border border-gray-700 bg-gray-800 px-3 py-1 text-xs font-semibold text-gray-200">--</span>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Timeliness</p>
                                        <span id="mvSupTimeliness"
                                            class="mt-1 inline-flex items-center justify-center rounded-full border border-gray-700 bg-gray-800 px-3 py-1 text-xs font-semibold text-gray-200">--</span>
                                    </div>
                                    <div class="md:col-span-3">
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Remarks</p>
                                        <div id="mvSupRemarks"
                                            class="mt-1 min-h-[48px] rounded-xl border border-gray-700 bg-gray-800 px-3 py-2 text-sm leading-relaxed text-gray-300">
                                            --
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-700 px-6 py-3">
                        <button type="button" id="closeTaskViewBottom"
                            class="rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-200 transition hover:bg-gray-800">
                            Close
                        </button>
                        <a href="{{ route('employee.ors.export.pdf') }}" id="exportOrsBtn"
                            title="Export available only after supervisor validation"
                            class="pointer-events-none cursor-not-allowed rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-200 opacity-50"
                            aria-disabled="true">
                            Export ORS
                        </a>
                        <a href="{{ route('employee.ors') }}"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-500">
                            View in ORS
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const tasks = [
                    {
                        id: 'task-jan-02',
                        title: 'Same-day verification of OTC transactions',
                        date: '2026-01-02',
                        requestId: 'REQ-2026-002',
                        uwpOutputId: 'otc_processing',
                        uwpOutputLabel: 'Processing of Over-the-Counter Revenue Transactions',
                        output: 'Official Receipt (OR)',
                        quantity: '12 transactions',
                        state: 'submitted',
                        durationMs: 2 * 60 * 60 * 1000,
                        startTime: null,
                        output_state: 'submitted',
                        evidenceRequired: true,
                        evidenceAttached: true,
                        evidenceFileName: 'REQ-2026-002_OR.pdf',
                        evidenceUploadedAt: new Date('2026-01-02T10:15:00'),
                        submittedAt: new Date('2026-01-02T10:15:00'),
                        supervisorName: 'Carlo D. Beray',
                        supervisorQuality: '5',
                        supervisorTimeliness: '5',
                        supervisorRemarks: 'All Goods',
                        notes: '--',
                    },
                    {
                        id: 'task-jan-04',
                        title: 'All e-bank transactions scanned and encoded daily',
                        date: '2026-01-04',
                        requestId: 'REQ-2026-004',
                        uwpOutputId: 'ebank_scanning',
                        uwpOutputLabel: 'E-Bank Scanning and Encoding of Revenue Transactions',
                        output: 'Bank Statement Form (BSF-01)',
                        quantity: '1 daily batch',
                        state: 'submitted',
                        durationMs: 90 * 60 * 1000,
                        startTime: null,
                        output_state: 'submitted',
                        evidenceRequired: true,
                        evidenceAttached: true,
                        evidenceFileName: 'REQ-2026-004_BSF-01.pdf',
                        evidenceUploadedAt: new Date('2026-01-04T15:20:00'),
                        submittedAt: new Date('2026-01-04T15:20:00'),
                        supervisorName: 'Carlo D. Beray',
                        supervisorQuality: '5',
                        supervisorTimeliness: '5',
                        supervisorRemarks: 'All Goods',
                        notes: '--',
                    },
                    {
                        id: 'task-jan-05',
                        title: 'OR validation completed daily',
                        date: '2026-01-05',
                        requestId: 'REQ-2026-005',
                        uwpOutputId: 'otc_processing',
                        uwpOutputLabel: 'Processing of Over-the-Counter Revenue Transactions',
                        output: 'Official Receipt (OR)',
                        quantity: '6 receipts validated',
                        state: 'recording',
                        durationMs: 18 * 60 * 1000,
                        startTime: null,
                        output_state: 'none',
                        evidenceRequired: true,
                        evidenceAttached: false,
                        evidenceFileName: null,
                        evidenceUploadedAt: null,
                        submittedAt: null,
                        supervisorName: null,
                        supervisorQuality: null,
                        supervisorTimeliness: null,
                        supervisorRemarks: null,
                        notes: 'Active timer',
                    },
                    {
                        id: 'task-jan-06',
                        title: 'Retrieval logs maintained for audit purposes',
                        date: '2026-01-06',
                        requestId: 'REQ-2026-006',
                        uwpOutputId: 'records_maintenance',
                        uwpOutputLabel: 'Maintenance of revenue records and filing system',
                        output: 'Records Inventory Checklist',
                        quantity: '--',
                        state: 'missing',
                        durationMs: 0,
                        startTime: null,
                        output_state: 'none',
                        evidenceRequired: true,
                        evidenceAttached: false,
                        evidenceFileName: null,
                        evidenceUploadedAt: null,
                        submittedAt: null,
                        supervisorName: null,
                        supervisorQuality: null,
                        supervisorTimeliness: null,
                        supervisorRemarks: null,
                        notes: 'No ORS entry submitted for the day',
                    },
                ];

                const tbody = document.getElementById('myTasksTbody');
                const statusFilter = document.getElementById('myTasksStatusFilter');
                const dateFilter = document.getElementById('myTasksDateFilter');

                const modal = document.getElementById('task-view-modal');
                const closeTopBtn = document.getElementById('closeTaskViewTop');
                const closeBottomBtn = document.getElementById('closeTaskViewBottom');

                const fields = {
                    task: document.getElementById('mvTask'),
                    mfo: document.getElementById('mvMfo'),
                    date: document.getElementById('mvDate'),
                    requestId: document.getElementById('mvRequestId'),
                    status: document.getElementById('mvStatus'),
                    outputType: document.getElementById('mvOutputType'),
                    outputState: document.getElementById('mvOutputState'),
                    quantity: document.getElementById('mvQuantity'),
                    evidence: document.getElementById('mvEvidence'),
                    evidenceFile: document.getElementById('mvEvidenceFile'),
                    submittedAt: document.getElementById('mvSubmittedAt'),
                    duration: document.getElementById('mvDuration'),
                    notes: document.getElementById('mvNotes'),
                    supervisorSection: document.getElementById('mvSupervisorMonitoringSection'),
                    supName: document.getElementById('mvSupName'),
                    supQuality: document.getElementById('mvSupQuality'),
                    supTimeliness: document.getElementById('mvSupTimeliness'),
                    supRemarks: document.getElementById('mvSupRemarks'),
                };

                function formatDateHuman(dateStr) {
                    if (!dateStr) return '--';
                    const date = new Date(`${dateStr}T00:00:00`);
                    if (Number.isNaN(date.getTime())) return '--';
                    return date.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
                }

                function formatDateTime(value) {
                    if (!value) return '--';
                    const date = value instanceof Date ? value : new Date(value);
                    if (Number.isNaN(date.getTime())) return '--';
                    return date.toLocaleString([], {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                    });
                }

                function formatDuration(ms) {
                    const totalSeconds = Math.max(0, Math.floor((ms || 0) / 1000));
                    const hours = Math.floor(totalSeconds / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    return `${hours}h ${minutes}m`;
                }

                function statusLabel(state) {
                    switch (state) {
                        case 'submitted':
                            return 'Submitted (Locked)';
                        case 'recording':
                            return 'Recording';
                        case 'paused':
                            return 'Paused';
                        case 'draft':
                            return 'Draft';
                        case 'missing':
                            return 'Missing / Overdue';
                        default:
                            return '--';
                    }
                }

                function outputStateLabel(task) {
                    if (task.state === 'missing') return 'Output Missing';
                    if (task.output_state === 'submitted') return 'Output submitted';
                    if (task.output_state === 'validated') return 'Output validated';
                    return 'Output pending';
                }

                function statusChipClasses(state) {
                    switch (state) {
                        case 'submitted':
                            return 'bg-violet-900 text-violet-300';
                        case 'recording':
                        case 'paused':
                            return 'bg-amber-900 text-amber-300';
                        case 'draft':
                            return 'bg-slate-700 text-slate-200';
                        case 'missing':
                            return 'bg-red-900 text-red-300';
                        default:
                            return 'bg-slate-700 text-slate-200';
                    }
                }

                function normalizeStatusFilterValue(value) {
                    if (!value || value === 'all') return null;
                    if (value === 'returned') return 'returned';
                    return value;
                }

                function matchesFilters(task) {
                    const statusValue = normalizeStatusFilterValue(statusFilter ? statusFilter.value : 'all');
                    if (statusValue && task.state !== statusValue) return false;

                    const dateValue = dateFilter ? dateFilter.value : '';
                    if (dateValue && task.date !== dateValue) return false;

                    return true;
                }

                function quantityLabel(value) {
                    if (!value || !String(value).trim() || value === '--') return '--';
                    return value;
                }

                function computeElapsed(task) {
                    const base = task.durationMs || 0;
                    if ((task.state === 'recording' || task.state === 'paused') && task.startTime) {
                        const start = task.startTime instanceof Date ? task.startTime : new Date(task.startTime);
                        if (!Number.isNaN(start.getTime())) {
                            return base + (Date.now() - start.getTime());
                        }
                    }
                    return base;
                }

                function renderRows() {
                    if (!tbody) return;

                    const filteredTasks = tasks.filter(matchesFilters);

                    if (!filteredTasks.length) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">
                                    No tasks found for the selected filters.
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    tbody.innerHTML = filteredTasks.map((task) => `
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">${task.title || '--'}</td>
                            <td class="px-4 py-3 text-gray-300">${formatDateHuman(task.date)}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${statusChipClasses(task.state)}">
                                    ${statusLabel(task.state)}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium ${task.state === 'missing' ? 'text-red-300' : (task.output_state === 'submitted' ? 'text-emerald-300' : 'text-amber-300')}">
                                    ${outputStateLabel(task)}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">${quantityLabel(task.quantity)}</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                    data-task-id="${task.id}"
                                    class="rounded-lg border border-gray-600 px-3 py-1.5 text-xs font-semibold text-gray-200 hover:bg-gray-800">
                                    View
                                </button>
                            </td>
                        </tr>
                    `).join('');
                }

                function resetSupervisorMonitoring() {
                    if (fields.supervisorSection) {
                        fields.supervisorSection.classList.add('hidden');
                    }
                    if (fields.supName) fields.supName.textContent = '--';
                    if (fields.supQuality) fields.supQuality.textContent = '--';
                    if (fields.supTimeliness) fields.supTimeliness.textContent = '--';
                    if (fields.supRemarks) fields.supRemarks.textContent = '--';
                }

                function openTaskViewModal(taskId) {
                    const task = tasks.find((item) => item.id === taskId);
                    if (!task || !modal) return;

                    fields.task.textContent = task.title || '--';
                    fields.mfo.textContent = task.uwpOutputLabel || '--';
                    fields.date.textContent = formatDateHuman(task.date);
                    fields.requestId.textContent = task.requestId && String(task.requestId).trim() ? task.requestId : '--';
                    fields.status.textContent = statusLabel(task.state);
                    fields.outputType.textContent = task.output || '--';
                    fields.outputState.textContent = outputStateLabel(task);
                    fields.quantity.textContent = quantityLabel(task.quantity);

                    if (task.evidenceAttached) {
                        fields.evidence.textContent = 'Attached';
                        fields.evidenceFile.textContent = `${task.evidenceFileName || '--'}${task.evidenceUploadedAt ? ` (${formatDateTime(task.evidenceUploadedAt)})` : ''}`;
                    } else {
                        fields.evidence.textContent = 'None';
                        fields.evidenceFile.textContent = '--';
                    }

                    fields.submittedAt.textContent = formatDateTime(task.submittedAt);
                    fields.duration.textContent = task.durationMs || task.startTime ? formatDuration(computeElapsed(task)) : '--';
                    fields.notes.textContent = task.notes || '--';

                    const isSubmitted = task.state === 'submitted';
                    if (fields.supervisorSection) {
                        fields.supervisorSection.classList.toggle('hidden', !isSubmitted);
                    }

                    if (isSubmitted) {
                        if (fields.supName) fields.supName.textContent = task.supervisorName || '--';
                        if (fields.supQuality) fields.supQuality.textContent = task.supervisorQuality ?? '--';
                        if (fields.supTimeliness) fields.supTimeliness.textContent = task.supervisorTimeliness ?? '--';
                        if (fields.supRemarks) fields.supRemarks.textContent = task.supervisorRemarks || '--';
                    } else {
                        resetSupervisorMonitoring();
                    }

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function closeTaskViewModal() {
                    if (!modal) return;
                    resetSupervisorMonitoring();
                    if (fields.mfo) fields.mfo.textContent = '--';
                    if (fields.requestId) fields.requestId.textContent = '--';
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                tbody?.addEventListener('click', (event) => {
                    const button = event.target.closest('button[data-task-id]');
                    if (!button) return;
                    openTaskViewModal(button.dataset.taskId);
                });

                statusFilter?.addEventListener('change', renderRows);
                dateFilter?.addEventListener('change', renderRows);

                closeTopBtn?.addEventListener('click', closeTaskViewModal);
                closeBottomBtn?.addEventListener('click', closeTaskViewModal);

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeTaskViewModal();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                        closeTaskViewModal();
                    }
                });

                renderRows();
            })();
        </script>

    </section>
@endsection
