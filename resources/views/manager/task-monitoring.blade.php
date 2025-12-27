<x-layouts.manager>

<section class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-semibold text-white">Task Monitoring</h1>
        <p class="text-sm text-slate-400">
            Read-only view of employee task logs (ORS). Tasks and ratings are system-generated.
        </p>
    </div>

    <!-- Filters & Controls -->
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-slate-800 bg-slate-900/70 p-4">
        <div class="flex flex-wrap gap-3">
            <!-- Employee Filter -->
            <div>
                <label class="text-xs uppercase text-slate-400">Employee</label>
                <select id="employeeFilter" 
                        class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    <option value="all">All Employees</option>
                    <option value="juan">Juan Dela Cruz</option>
                    <option value="maria">Maria Santos</option>
                    <option value="pedro">Pedro Reyes</option>
                </select>
            </div>

            <!-- Date Range Filter -->
            <div>
                <label class="text-xs uppercase text-slate-400">Date Range</label>
                <select id="dateRangeFilter" 
                        class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    <option value="week">This Week</option>
                    <option value="month" selected>This Month</option>
                    <option value="custom">Custom</option>
                </select>
            </div>

            <div>
                <label class="text-xs uppercase text-slate-400">Client Type</label>
                <select id="clientTypeFilter"
                        class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    <option value="all">All Clients</option>
                    <option value="government">Government</option>
                    <option value="private">Private</option>
                    <option value="internal">Internal</option>
                </select>
            </div>

            <div>
                <label class="text-xs uppercase text-slate-400">Task Category</label>
                <select id="taskCategoryFilter"
                        class="manager-filter-select mt-1 rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    <option value="all">All Categories</option>
                    <option value="scanning">Scanning</option>
                    <option value="validation">Validation</option>
                    <option value="reporting">Reporting</option>
                    <option value="followup">Client Follow-up</option>
                </select>
            </div>

            <!-- Overdue Tasks Toggle -->
            <div class="flex items-end">
                <button id="toggleOverdue" 
                        class="flex items-center gap-2 rounded-lg border border-rose-800 bg-rose-950/30 px-4 py-2 text-sm text-rose-300 hover:bg-rose-950/50">
                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                    Show Overdue Tasks
                </button>
            </div>
        </div>

        <!-- Calendar View Toggle -->
        <div class="flex gap-1 rounded-lg border border-slate-800 bg-slate-950 p-1">
            <button id="monthViewBtn" 
                    class="rounded-md px-3 py-1.5 text-sm font-medium text-white bg-slate-800">
                Month
            </button>
            <button id="weekViewBtn" 
                    class="rounded-md px-3 py-1.5 text-sm font-medium text-slate-400 hover:text-white">
                Week
            </button>
        </div>
    </div>

    <!-- Overdue Tasks Banner (Initially Hidden) -->
    <div id="overdueBanner" 
         class="hidden animate-pulse rounded-xl border border-rose-800 bg-rose-950/20 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="rounded-full bg-rose-500 p-2">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.342 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-rose-300">Overdue Tasks Alert</h3>
                    <p class="text-sm text-rose-400/80" id="overdueCount">0 tasks are overdue</p>
                </div>
            </div>
            <button onclick="document.getElementById('overdueBanner').classList.add('hidden')" 
                    class="text-rose-400 hover:text-rose-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Calendar -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
        <div id="manager-ors-calendar"></div>
    </div>

    <!-- Tasks List for Selected Date (Initially Hidden) -->
    <div id="dateTasksList" class="hidden rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white" id="selectedDateTitle">Tasks for December 26, 2025</h2>
            <button onclick="document.getElementById('dateTasksList').classList.add('hidden')" 
                    class="text-sm text-slate-400 hover:text-white">
                Close List
            </button>
        </div>
        <div class="space-y-3" id="tasksListContainer">
            <!-- Tasks will be dynamically inserted here -->
        </div>
    </div>

    <!-- Legend -->
    <div class="flex flex-wrap items-center justify-between gap-4 text-xs text-slate-400">
        <div class="flex flex-wrap gap-4">
            <span class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-emerald-500"></span> Completed
            </span>
            <span class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-amber-500"></span> In Progress
            </span>
            <span class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-rose-500"></span> Delayed / Missing
            </span>
        </div>
        <div class="text-slate-500">
            Click any date to view tasks
        </div>
    </div>
</section>

<!-- Read-only Task Detail Modal -->
<div id="taskDetailModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60">
    <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-5">
        <h2 class="mb-4 text-lg font-semibold text-white">Task Details</h2>
        <div class="space-y-3 text-sm text-slate-300">
            <div>
                <p class="text-xs uppercase text-slate-400">Employee</p>
                <p id="detailEmployee">--</p>
            </div>
            <div>
                <p class="text-xs uppercase text-slate-400">Task</p>
                <p id="detailTask">--</p>
            </div>
            <div>
                <p class="text-xs uppercase text-slate-400">Client</p>
                <p id="detailClient">--</p>
            </div>
            <div>
                <p class="text-xs uppercase text-slate-400">Status</p>
                <p id="detailStatus">--</p>
            </div>
            <div>
                <p class="text-xs uppercase text-slate-400">Duration</p>
                <p id="detailDuration">--</p>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="document.getElementById('taskDetailModal').classList.add('hidden')"
                    class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<!-- FullCalendar Library -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize calendar
    const calendarEl = document.getElementById('manager-ors-calendar');
    let currentView = 'dayGridMonth';
    let currentEmployeeFilter = 'all';
    let overdueTasksVisible = false;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: currentView,
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        selectable: true,
        editable: false,
        
        // Feature 3: Click date -> list view under calendar
        dateClick: function(info) {
            showTasksForDate(info.dateStr);
        },
        
        eventClick: function(info) {
            document.getElementById('detailEmployee').innerText = info.event.extendedProps.employee;
            document.getElementById('detailTask').innerText = info.event.title;
            document.getElementById('detailClient').innerText = info.event.extendedProps.client;
            document.getElementById('detailStatus').innerText = info.event.extendedProps.status;
            document.getElementById('detailDuration').innerText = info.event.extendedProps.duration;
            document.getElementById('taskDetailModal').classList.remove('hidden');
        },

        events: function(fetchInfo, successCallback) {
            successCallback(getFilteredEvents());
        }
    });

    calendar.render();

    // Feature 1: Employee filter -> dynamic reload
    document.getElementById('employeeFilter').addEventListener('change', function() {
        currentEmployeeFilter = this.value;
        calendar.refetchEvents();
        updateOverdueHighlight();
    });

    // Feature 2: Switch calendar view (month/week)
    document.getElementById('monthViewBtn').addEventListener('click', function() {
        calendar.changeView('dayGridMonth');
        updateViewButtons('month');
    });

    document.getElementById('weekViewBtn').addEventListener('click', function() {
        calendar.changeView('timeGridWeek');
        updateViewButtons('week');
    });

    // Feature 4: Overdue tasks toggle
    document.getElementById('toggleOverdue').addEventListener('click', function() {
        overdueTasksVisible = !overdueTasksVisible;
        this.innerHTML = overdueTasksVisible ? 
            '<span class="h-2 w-2 rounded-full bg-rose-500"></span> Hide Overdue Tasks' :
            '<span class="h-2 w-2 rounded-full bg-rose-500"></span> Show Overdue Tasks';
        
        calendar.refetchEvents();
        updateOverdueBanner();
    });

    // Function to get filtered events based on current filters
    function getFilteredEvents() {
        const allEvents = [
            {
                title: 'E-Bank Scanning',
                start: '2025-12-26',
                color: '#10b981',
                extendedProps: {
                    employee: 'Juan Dela Cruz',
                    employeeId: 'juan',
                    client: 'ABC Corp',
                    status: 'Completed',
                    duration: '1h 42m',
                    overdue: false
                }
            },
            {
                title: 'Client Form Review',
                start: '2025-12-26',
                color: '#f59e0b',
                extendedProps: {
                    employee: 'Maria Santos',
                    employeeId: 'maria',
                    client: 'XYZ Ltd',
                    status: 'In Progress',
                    duration: '45m',
                    overdue: false
                }
            },
            {
                title: 'Report Submission',
                start: '2025-12-24', // Past date for overdue example
                color: '#ef4444',
                extendedProps: {
                    employee: 'Pedro Reyes',
                    employeeId: 'pedro',
                    client: 'Global Inc',
                    status: 'Missing',
                    duration: '2h',
                    overdue: true
                }
            },
            {
                title: 'Data Analysis',
                start: '2025-12-25',
                color: '#ef4444',
                extendedProps: {
                    employee: 'Juan Dela Cruz',
                    employeeId: 'juan',
                    client: 'Tech Solutions',
                    status: 'Delayed',
                    duration: '3h',
                    overdue: true
                }
            }
        ];

        // Apply filters
        return allEvents.filter(event => {
            // Employee filter
            if (currentEmployeeFilter !== 'all' && event.extendedProps.employeeId !== currentEmployeeFilter) {
                return false;
            }
            
            // Overdue filter
            if (!overdueTasksVisible && event.extendedProps.overdue) {
                return false;
            }
            
            return true;
        });
    }

    // Feature 3: Show tasks list for selected date
    function showTasksForDate(dateStr) {
        const date = new Date(dateStr);
        const formattedDate = date.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        document.getElementById('selectedDateTitle').textContent = `Tasks for ${formattedDate}`;
        
        // Filter tasks for the selected date
        const tasksForDate = getFilteredEvents().filter(event => {
            const eventDate = new Date(event.start).toDateString();
            return eventDate === date.toDateString();
        });
        
        // Populate tasks list
        const container = document.getElementById('tasksListContainer');
        container.innerHTML = '';
        
        if (tasksForDate.length === 0) {
            container.innerHTML = `
                <div class="rounded-lg border border-slate-800 bg-slate-950/50 p-4 text-center text-slate-500">
                    No tasks scheduled for this date
                </div>
            `;
        } else {
            tasksForDate.forEach(task => {
                const taskElement = document.createElement('div');
                taskElement.className = 'rounded-lg border border-slate-800 bg-slate-950/50 p-4';
                taskElement.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-white">${task.title}</h3>
                            <p class="text-sm text-slate-400">${task.extendedProps.employee} - ${task.extendedProps.client}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm ${task.extendedProps.overdue ? 'text-rose-400' : 'text-slate-300'}">
                                ${task.extendedProps.status}
                            </span>
                            <span class="h-3 w-3 rounded-full" style="background-color: ${task.color}"></span>
                        </div>
                    </div>
                `;
                container.appendChild(taskElement);
            });
        }
        
        // Show the tasks list
        document.getElementById('dateTasksList').classList.remove('hidden');
    }

    function updateViewButtons(activeView) {
        const monthBtn = document.getElementById('monthViewBtn');
        const weekBtn = document.getElementById('weekViewBtn');
        
        if (activeView === 'month') {
            monthBtn.className = 'rounded-md px-3 py-1.5 text-sm font-medium text-white bg-slate-800';
            weekBtn.className = 'rounded-md px-3 py-1.5 text-sm font-medium text-slate-400 hover:text-white';
        } else {
            monthBtn.className = 'rounded-md px-3 py-1.5 text-sm font-medium text-slate-400 hover:text-white';
            weekBtn.className = 'rounded-md px-3 py-1.5 text-sm font-medium text-white bg-slate-800';
        }
    }

    function updateOverdueHighlight() {
        const overdueCount = getFilteredEvents().filter(event => event.extendedProps.overdue).length;
        const toggleBtn = document.getElementById('toggleOverdue');
        
        if (overdueCount > 0) {
            toggleBtn.classList.remove('border-rose-800', 'bg-rose-950/30', 'text-rose-300');
            toggleBtn.classList.add('border-rose-600', 'bg-rose-950/50', 'text-rose-200');
        } else {
            toggleBtn.classList.remove('border-rose-600', 'bg-rose-950/50', 'text-rose-200');
            toggleBtn.classList.add('border-rose-800', 'bg-rose-950/30', 'text-rose-300');
        }
    }

    function updateOverdueBanner() {
        const overdueEvents = getFilteredEvents().filter(event => event.extendedProps.overdue);
        const banner = document.getElementById('overdueBanner');
        const countElement = document.getElementById('overdueCount');
        
        if (overdueEvents.length > 0 && overdueTasksVisible) {
            countElement.textContent = `${overdueEvents.length} task${overdueEvents.length !== 1 ? 's' : ''} ${overdueTasksVisible ? 'are' : 'is'} overdue`;
            banner.classList.remove('hidden');
        } else {
            banner.classList.add('hidden');
        }
    }

    // Initial setup
    updateViewButtons('month');
    updateOverdueHighlight();
    updateOverdueBanner();
});
</script>
@endpush

</x-layouts.manager>
