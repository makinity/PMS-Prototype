<x-layouts.employee>

<section class="space-y-6">

    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-semibold text-white">Task Calendar (ORS)</h1>
        <p class="text-sm text-slate-400">
            Log and view your tasks by date. Link each task to a client request and output. All durations and performance ratings are system-generated.
        </p>
    </div>

    <!-- Color Legend -->
    <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
        <div class="flex flex-wrap items-center gap-4 text-xs">
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                <span class="text-slate-300">Completed</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full bg-amber-500"></div>
                <span class="text-slate-300">In Progress</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                <span class="text-slate-300">Pending Review</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full bg-red-500"></div>
                <span class="text-slate-300">Missing/Overdue</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full bg-purple-500"></div>
                <span class="text-slate-300">Scheduled</span>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-xs text-slate-400">This Week</p>
            <p class="mt-1 text-2xl font-semibold text-white">12</p>
            <p class="text-xs text-slate-400">Tasks logged</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-xs text-slate-400">Pending</p>
            <p class="mt-1 text-2xl font-semibold text-amber-400">3</p>
            <p class="text-xs text-slate-400">Require action</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-xs text-slate-400">Avg Duration</p>
            <p class="mt-1 text-2xl font-semibold text-white">2.4h</p>
            <p class="text-xs text-slate-400">Per task</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-xs text-slate-400">Rating</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-400">4.2</p>
            <p class="text-xs text-slate-400">This month</p>
        </div>
    </div>

    <!-- Active Task Timer -->
    <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-white">Active Task Timer</h2>
                <p class="text-sm text-slate-400">Prototype view of automatic start and end tracking.</p>
            </div>
            <span class="rounded-full border border-emerald-600/60 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                AUTO TRACKING
            </span>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 text-sm md:grid-cols-4">
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                <p class="text-xs text-slate-400">Task</p>
                <p class="font-medium text-white">E-Bank Scanning</p>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                <p class="text-xs text-slate-400">Start</p>
                <p class="font-medium text-white">09:12</p>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                <p class="text-xs text-slate-400">Elapsed</p>
                <p class="font-medium text-white">1h 08m</p>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                <p class="text-xs text-slate-400">Status</p>
                <p class="font-medium text-emerald-300">Recording</p>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <button type="button" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                Pause Tracking
            </button>
            <a href="{{ route('employee.submit-output') }}" class="rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-slate-900 hover:bg-emerald-600">
                Stop and Submit
            </a>
        </div>

        <p class="mt-3 text-xs text-slate-500">
            Timing data is captured in real time and attached to the linked output.
        </p>
    </div>

    <!-- Calendar -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
        <div id="ors-calendar"></div>
    </div>

    <!-- System Notice -->
    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 text-xs text-slate-400">
        Note: Task start/end times and performance ratings are recorded automatically.
        Manual duration entry or rating is not allowed. Missing fields generate alerts.
    </div>

</section>

<!-- ORS Task Modal (Compact) -->
<div id="orsTaskModal"
     role="dialog"
     aria-modal="true"
     class="ors-modal fixed inset-0 z-[60] hidden flex items-start justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6">

    <div class="w-full max-w-md max-h-[calc(100vh-3rem)] overflow-y-auto rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">

        <!-- Header -->
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-white">Log Task</h2>
            <p class="text-xs text-slate-400">
                Task details only. Performance is system-generated.
            </p>
        </div>

        <form class="space-y-3">

            <!-- Date -->
            <div>
                <label class="text-[11px] uppercase text-slate-400">Date</label>
                <input id="orsSelectedDate"
                       type="text"
                       disabled
                       class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-400">
            </div>

            <!-- Task Type -->
            <div>
                <label class="text-[11px] uppercase text-slate-400">Task</label>
                <select id="orsTaskType" class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    <option value="">Select task</option>
                    <option value="bank_scanning">E-Bank Statement Scanning</option>
                    <option value="form_review">Client Form Review</option>
                    <option value="data_entry">Financial Data Entry</option>
                    <option value="report_generation">Report Generation</option>
                    <option value="client_communication">Client Communication</option>
                    <option value="quality_check">Quality Check</option>
                    <option value="document_upload">Document Upload</option>
                    <option value="follow_up">Client Follow-up</option>
                </select>
            </div>

            <!-- Client -->
            <div>
                <label class="text-[11px] uppercase text-slate-400">Client</label>
                <select id="orsClient" class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    <option value="">Select client</option>
                    <option value="abc">ABC Corporation</option>
                    <option value="xyz">XYZ Limited</option>
                    <option value="global">Global Enterprises</option>
                    <option value="tech">Tech Solutions Inc.</option>
                    <option value="prime">Prime Services Group</option>
                    <option value="alpha">Alpha Financials</option>
                </select>
            </div>

            <!-- Client Request ID -->
            <div>
                <label class="text-[11px] uppercase text-slate-400">Client Request ID</label>
                <input id="orsRequestId"
                       type="text"
                       placeholder="REQ-2025-01234"
                       class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
            </div>

            <!-- Output -->
            <div>
                <label class="text-[11px] uppercase text-slate-400">Form / Output Type</label>
                <select id="orsOutput" class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    <option value="">Select form/output</option>
                    <option value="bank_form">Bank Statement Form (BSF-01)</option>
                    <option value="expense_report">Expense Report (ER-02)</option>
                    <option value="financial_statement">Financial Statement (FS-03)</option>
                    <option value="client_summary">Client Summary Report (CSR-04)</option>
                    <option value="tax_form">Tax Compliance Form (TCF-05)</option>
                    <option value="audit_report">Audit Report (AR-06)</option>
                </select>
            </div>

            <!-- Notes -->
            <div>
                <label class="text-[11px] uppercase text-slate-400">Notes (optional)</label>
                <textarea id="orsNotes" rows="2"
                          class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200"
                          placeholder="Additional details, special instructions, etc."></textarea>
            </div>

            <!-- System Rule -->
            <div class="rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-[11px] text-slate-400">
                - Duration is tracked automatically.<br>
                - Performance rating calculated by system.<br>
                - Missing fields trigger alerts and follow-ups.
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-2">
                <button type="button"
                        onclick="closeOrsModal('orsTaskModal')"
                        class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs text-slate-300 hover:bg-slate-800">
                    Cancel
                </button>
                <button type="submit"
                        class="rounded-lg bg-emerald-500 px-4 py-1.5 text-xs font-semibold text-slate-900 hover:bg-emerald-600">
                    Log Task
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Task Details Modal -->
<div id="taskDetailsModal"
     role="dialog"
     aria-modal="true"
     class="ors-modal fixed inset-0 z-[60] hidden flex items-start justify-center overflow-y-auto bg-black/60 px-4 py-6 sm:px-6">
    <div class="w-full max-w-md max-h-[calc(100vh-3rem)] overflow-y-auto rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-xl">
        <div class="mb-4 flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-white" id="taskDetailTitle">Task Details</h2>
                <p class="text-xs text-slate-400" id="taskDetailDate">Date: --</p>
            </div>
            <button onclick="closeOrsModal('taskDetailsModal')"
                    class="text-slate-400 hover:text-white">
                x
            </button>
        </div>
        
        <div class="space-y-3 text-sm">
            <div>
                <p class="text-xs text-slate-400">Client</p>
                <p class="text-slate-200" id="taskDetailClient">--</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Request ID</p>
                <p class="text-slate-200" id="taskDetailRequest">--</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Status</p>
                <p class="text-slate-200" id="taskDetailStatus">--</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Output Type</p>
                <p class="text-slate-200" id="taskDetailOutput">--</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Duration</p>
                <p class="text-slate-200" id="taskDetailDuration">--</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Performance Rating</p>
                <p class="text-slate-200" id="taskDetailRating">--</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Notes</p>
                <p class="text-slate-200" id="taskDetailNotes">--</p>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button onclick="closeOrsModal('taskDetailsModal')"
                    class="rounded-lg border border-slate-700 px-4 py-2 text-xs text-slate-300 hover:bg-slate-800">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('ors-calendar');
    const orsModals = Array.from(document.querySelectorAll('.ors-modal'));

    function updateModalState() {
        const anyOpen = orsModals.some((modal) => !modal.classList.contains('hidden'));
        document.body.classList.toggle('overflow-hidden', anyOpen);
    }

    window.openOrsModal = function (modalId) {
        orsModals.forEach((modal) => modal.classList.add('hidden'));
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
        }
        updateModalState();
    };

    window.closeOrsModal = function (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
        updateModalState();
    };

    orsModals.forEach((modal) => {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                window.closeOrsModal(modal.id);
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            orsModals.forEach((modal) => modal.classList.add('hidden'));
            updateModalState();
        }
    });

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        contentHeight: 600,
        dayMaxEventRows: 3,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },

        selectable: true,
        editable: false,
        dayHeaderClassNames: 'text-slate-300',
        dayCellClassNames: 'hover:bg-slate-800/30',

        dateClick(info) {
            document.getElementById('orsSelectedDate').value = info.dateStr;
            window.openOrsModal('orsTaskModal');
        },

        eventClick(info) {
            const task = info.event;
            document.getElementById('taskDetailTitle').textContent = task.title;
            document.getElementById('taskDetailDate').textContent = `Date: ${task.start.toLocaleDateString()}`;
            document.getElementById('taskDetailClient').textContent = task.extendedProps.client || '--';
            document.getElementById('taskDetailRequest').textContent = task.extendedProps.requestId || '--';
            document.getElementById('taskDetailStatus').textContent = task.extendedProps.status || '--';
            document.getElementById('taskDetailOutput').textContent = task.extendedProps.output || '--';
            document.getElementById('taskDetailDuration').textContent = task.extendedProps.duration || '--';
            document.getElementById('taskDetailRating').textContent = task.extendedProps.rating || '--';
            document.getElementById('taskDetailNotes').textContent = task.extendedProps.notes || '--';
            window.openOrsModal('taskDetailsModal');
        },

        events: [
            // Completed tasks (Green)
            {
                title: 'E-Bank Scanning',
                start: new Date().toISOString().split('T')[0], // Today
                color: '#10b981',
                extendedProps: {
                    client: 'ABC Corporation',
                    requestId: 'REQ-2025-014',
                    output: 'Bank Statement Form (BSF-01)',
                    status: 'Completed',
                    duration: '1.8 hours',
                    rating: '4.5/5',
                    notes: 'All statements verified and uploaded'
                }
            },
            {
                title: 'Client Form Review',
                start: new Date().toISOString().split('T')[0],
                color: '#10b981',
                extendedProps: {
                    client: 'XYZ Limited',
                    requestId: 'REQ-2025-009',
                    output: 'Client Summary Report (CSR-04)',
                    status: 'Completed',
                    duration: '2.3 hours',
                    rating: '4.2/5',
                    notes: 'Forms approved for processing'
                }
            },
            // In Progress tasks (Amber)
            {
                title: 'Financial Data Entry',
                start: new Date(Date.now() + 86400000).toISOString().split('T')[0], // Tomorrow
                color: '#f59e0b',
                extendedProps: {
                    client: 'Global Enterprises',
                    requestId: 'REQ-2025-017',
                    output: 'Financial Statement (FS-03)',
                    status: 'In Progress',
                    duration: '1.5 hours (ongoing)',
                    rating: '--',
                    notes: 'Quarterly financial data entry'
                }
            },
            // Pending Review (Blue)
            {
                title: 'Report Generation',
                start: new Date(Date.now() - 86400000).toISOString().split('T')[0], // Yesterday
                color: '#3b82f6',
                extendedProps: {
                    client: 'Tech Solutions Inc.',
                    requestId: 'REQ-2025-012',
                    output: 'Financial Statement (FS-03)',
                    status: 'Pending Review',
                    duration: '3.2 hours',
                    rating: '4.0/5',
                    notes: 'Awaiting manager approval'
                }
            },
            // Missing/Overdue (Red)
            {
                title: 'Client Follow-up',
                start: new Date(Date.now() - 2 * 86400000).toISOString().split('T')[0], // 2 days ago
                color: '#ef4444',
                extendedProps: {
                    client: 'Prime Services Group',
                    requestId: 'REQ-2025-018',
                    output: 'Client Follow-up Notes',
                    status: 'Overdue',
                    duration: '--',
                    rating: '--',
                    notes: 'Missed deadline - needs attention'
                }
            },
            // Scheduled (Purple)
            {
                title: 'Tax Compliance Review',
                start: new Date(Date.now() + 3 * 86400000).toISOString().split('T')[0], // 3 days ahead
                color: '#8b5cf6',
                extendedProps: {
                    client: 'Alpha Financials',
                    requestId: 'REQ-2025-023',
                    output: 'Tax Compliance Form (TCF-05)',
                    status: 'Scheduled',
                    duration: '--',
                    rating: '--',
                    notes: 'Scheduled for end of week'
                }
            },
            // This week's tasks
            {
                title: 'Quality Check',
                start: new Date(Date.now() + 86400000).toISOString().split('T')[0],
                color: '#10b981'
            },
            {
                title: 'Document Upload',
                start: new Date(Date.now() + 2 * 86400000).toISOString().split('T')[0],
                color: '#3b82f6'
            },
            // Last week's tasks
            {
                title: 'Bank Reconciliation',
                start: new Date(Date.now() - 4 * 86400000).toISOString().split('T')[0],
                color: '#10b981'
            },
            {
                title: 'Audit Preparation',
                start: new Date(Date.now() - 5 * 86400000).toISOString().split('T')[0],
                color: '#10b981'
            },
            {
                title: 'Client Meeting',
                start: new Date(Date.now() - 3 * 86400000).toISOString().split('T')[0],
                color: '#10b981'
            }
        ],

        eventContent: function(arg) {
            const dotsOnly = arg.event.title.length > 15;
            const displayText = dotsOnly ? arg.event.title.substring(0, 12) + '...' : arg.event.title;
            
            const arrayOfDomNodes = [
                document.createElement('div'),
            ];
            arrayOfDomNodes[0].innerHTML = `
                <div class="fc-event-main-frame">
                    <div class="fc-event-title-container">
                        <div class="fc-event-title fc-sticky">
                            ${displayText}
                        </div>
                    </div>
                </div>
            `;
            arrayOfDomNodes[0].classList.add('text-xs', 'px-1', 'py-0.5');
            
            return { domNodes: arrayOfDomNodes };
        }
    });

    calendar.render();

    // Handle form submission
    const taskForm = document.querySelector('#orsTaskModal form');
    if (taskForm) {
        taskForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const date = document.getElementById('orsSelectedDate').value;
            const taskTypeSelect = document.getElementById('orsTaskType');
            const clientSelect = document.getElementById('orsClient');
            const outputSelect = document.getElementById('orsOutput');
            const requestInput = document.getElementById('orsRequestId');
            const notesInput = document.getElementById('orsNotes');

            const taskType = taskTypeSelect ? taskTypeSelect.value : '';
            const client = clientSelect ? clientSelect.value : '';
            const outputType = outputSelect ? outputSelect.value : '';
            const requestId = requestInput ? requestInput.value.trim() : '';
            
            if (taskType && client && date && requestId && outputType) {
                // Add new event to calendar
                calendar.addEvent({
                    title: taskTypeSelect.options[taskTypeSelect.selectedIndex].text,
                    start: date,
                    color: '#f59e0b', // Default to "In Progress" color
                    extendedProps: {
                        client: clientSelect.options[clientSelect.selectedIndex].text,
                        requestId: requestId,
                        output: outputSelect.options[outputSelect.selectedIndex].text,
                        status: 'In Progress',
                        duration: 'Tracking...',
                        rating: 'Pending',
                        notes: notesInput && notesInput.value ? notesInput.value : 'No notes'
                    }
                });
                
                // Close modal and reset form
                window.closeOrsModal('orsTaskModal');
                this.reset();
                
                // Show success message
                alert('Task logged successfully! Duration and rating will be calculated automatically.');
            } else {
                alert('Please complete required fields: task, client, request ID, and output type.');
            }
        });
    }
});
</script>
@endpush

</x-layouts.employee>
