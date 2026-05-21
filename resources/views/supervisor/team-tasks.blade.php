@extends('layouts.supervisor')

@section('main-content')
    @php
        $selectedStatus = strtolower((string) request('status', 'all'));
        $selectedEmployee = (string) request('employee_id', '');

        $statusOptions = collect(['all', 'draft', 'recording', 'submitted', 'rated']);

        $statusMeta = [
            'draft' => 'bg-amber-900/40 border border-amber-700/40 text-amber-200',
            'recording' => 'bg-blue-900/40 border border-blue-700/40 text-blue-200',
            'submitted' => 'bg-emerald-900/40 border border-emerald-700/40 text-emerald-300',
            'rated' => 'bg-cyan-900/40 border border-cyan-700/40 text-cyan-300',
        ];
    @endphp

    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Supervisor View</p>
                <h1 class="text-3xl font-bold text-white">Team Tasks</h1>
                <p class="text-gray-300">Monitor assignments, risk signals, and progress at a glance without changing owners.</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-emerald-900/30 border border-emerald-700/50 text-emerald-300 text-xs font-semibold">Read-only oversight</span>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-amber-900/30 border border-amber-700/50 text-amber-200 text-xs font-semibold">Escalate via manager</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="xl:col-span-2 p-6 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Active Work Queue</h2>
                <span class="text-xs text-slate-400">Read-only supervision list</span>
            </div>

            <form method="GET" action="{{ route('supervisor.team-tasks') }}"
                class="mb-4 grid grid-cols-1 gap-3 rounded-lg border border-slate-800 bg-slate-950/40 p-4 md:grid-cols-3">
                <div>
                    <label for="status" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</label>
                    <select id="status" name="status"
                    style="background:#0f172a;color:#e5e7eb;"
                        class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-slate-200">
                        @foreach ($statusOptions as $statusOption)
                            <option value="{{ $statusOption }}" @selected($selectedStatus === $statusOption)>
                                {{ $statusOption === 'all' ? 'All' : ucfirst($statusOption) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="employee_id" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Employee</label>
                    <select id="employee_id" name="employee_id"
                    style="background:#0f172a;color:#e5e7eb;"
                        class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-slate-200">
                        <option value="">All employees</option>
                        @foreach ($teamEmployees as $teamEmployee)
                            <option value="{{ $teamEmployee->id }}" @selected($selectedEmployee === (string) $teamEmployee->id)>
                                {{ $teamEmployee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                        Apply Filters
                    </button>
                    <a href="{{ route('supervisor.team-tasks') }}"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                        Reset
                    </a>
                </div>
            </form>

            <div class="overflow-hidden rounded-lg border border-gray-800">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800 text-sm">
                    <thead class="bg-gray-900/70 text-slate-300">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Employee</th>
                            <th class="px-4 py-3 text-left font-semibold">Task / Indicator</th>
                            <th class="px-4 py-3 text-left font-semibold">Work Date</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Quantity</th>
                            <th class="px-4 py-3 text-left font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-slate-200">
                        @forelse ($entries as $entry)
                            @php
                                $taskLabel = ($entry->ipcrItem->output_title ?? '—') . ' — ' . ($entry->ipcrItem->indicator_text ?? '');
                                $status = strtolower((string) ($entry->status ?? 'draft'));
                                $statusBadge = $statusMeta[$status] ?? 'bg-slate-800 border border-slate-700 text-slate-200';
                            @endphp
                            <tr class="hover:bg-slate-900/60">
                                <td class="px-4 py-3 text-white">{{ $entry->employee->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-100">{{ $taskLabel }}</td>
                                <td class="px-4 py-3">{{ $entry->work_date ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $entry->quantity ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($status === 'rated' || (int) ($entry->supervisor_id ?? 0) !== (int) ($supervisor->id ?? 0))
                                        <button type="button"
                                            data-task-id="{{ $entry->id }}"
                                            class="view-task-btn rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                            View
                                        </button>
                                    @else
                                        <a href="{{ route('supervisor.team-tasks.monitor', $entry) }}"
                                            class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500">
                                            Monitor
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">
                                    No ORS entries found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            <div class="mt-4">
                {{ $entries->links() }}
            </div>
        </div>

        <div class="p-6 rounded-xl bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 shadow-lg space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Oversight Signals</h3>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-200 text-xs font-semibold">No edits</span>
            </div>
            <ul class="space-y-3 text-sm text-slate-300">
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_0_4px_rgba(16,185,129,0.15)]"></span>
                    <div>
                        <p class="text-white font-medium">Workload balanced</p>
                        <p class="text-slate-400">Records team owners within 3 concurrent critical tasks.</p>
                    </div>
                </li>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-amber-400 shadow-[0_0_0_4px_rgba(251,191,36,0.15)]"></span>
                    <div>
                        <p class="text-white font-medium">Two items aging</p>
                        <p class="text-slate-400">Follow-up recommended on indexing and audit before end of week.</p>
                    </div>
                </li>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-rose-400 shadow-[0_0_0_4px_rgba(248,113,113,0.15)]"></span>
                    <div>
                        <p class="text-white font-medium">Escalation candidate</p>
                        <p class="text-slate-400">Records access permission cleanup blocked; notify manager if unchanged.</p>
                    </div>
                </li>
            </ul>

            <div class="rounded-lg border border-slate-800 bg-slate-900/70 p-4 text-xs text-slate-300">
                <p class="font-semibold text-slate-100 mb-1">Helper note</p>
                <p>Observation-only dashboard. Capture concerns for the manager; do not reassign or close tasks here.</p>
            </div>
        </div>
    </div>

    <div id="task-view-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="relative w-full max-w-4xl">
            <div class="relative flex max-h-[85vh] flex-col overflow-hidden rounded-2xl border border-gray-700 bg-gray-900 shadow-lg">
                <div class="flex items-start justify-between border-b border-gray-700 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Task Details</h3>
                        <p class="text-xs text-gray-400">Team Tasks mirrors ORS activity (read-only).</p>
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
                            <p id="mvEmployee" class="text-sm font-medium text-white">--</p>
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
                            <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                            <p id="mvStatus" class="text-sm font-medium text-white">--</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Output State</p>
                            <p id="mvOutputState" class="text-sm font-medium text-white">--</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Quantity (ORS)</p>
                            <p id="mvQuantity" class="text-sm font-medium text-white">--</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Evidence</p>
                            <div class="flex items-center gap-2">
                                <p id="mvEvidence" class="text-sm font-medium text-white">--</p>
                            </div>
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
                        class="mt-6 rounded-2xl border border-gray-700 bg-gray-900/60 p-5 shadow-inner shadow-black/40">
                        <div class="flex items-center justify-between">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor Monitoring</p>
                            <span class="text-[0.65rem] font-semibold uppercase tracking-wider text-gray-500">Stage II</span>
                        </div>
                        <div class="mt-4 space-y-4">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor Name</p>
                                    <p id="mvSupName" class="text-sm font-medium text-white">--</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor Office</p>
                                    <p id="mvSupOffice" class="text-sm font-medium text-white">--</p>
                                </div>
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
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rawTasks = @json($entries->items() ?? []);
            
            const tasks = Array.isArray(rawTasks)
                ? rawTasks.map((entry) => {
                    const status = String(entry?.status || 'draft').toLowerCase();
                    const submittedAt = entry?.submitted_at ? new Date(entry.submitted_at) : null;
                    const startedAt = entry?.started_at ? new Date(entry.started_at) : null;
                    const stoppedAt = entry?.stopped_at ? new Date(entry.stopped_at) : null;
                    
                    return {
                        id: String(entry?.id ?? ''),
                        employeeName: entry?.employee?.name || '--',
                        title: String(entry?.ipcr_item?.indicator_text || '--'),
                        date: String(entry?.work_date || ''),
                        uwpOutputLabel: String(entry?.ipcr_item?.output_title || '--'),
                        quantity: entry?.quantity || '--',
                        state: status,
                        durationMs: Number(entry?.total_seconds || 0) * 1000,
                        startTime: startedAt && !Number.isNaN(startedAt.getTime()) ? startedAt : null,
                        stoppedAt: stoppedAt && !Number.isNaN(stoppedAt.getTime()) ? stoppedAt : null,
                        output_state: (status === 'submitted' || status === 'rated') ? 'submitted' : 'none',
                        hasEvidence: Number(entry?.evidences_count || 0) > 0,
                        evidenceCount: Number(entry?.evidences_count || 0),
                        submittedAt: submittedAt && !Number.isNaN(submittedAt.getTime()) ? submittedAt : null,
                        notes: entry?.notes || '--',
                        supervisor: {
                            name: entry?.supervisor?.name || '--',
                            office: entry?.supervisor?.office?.name || '--'
                        },
                        monitoring: entry?.monitoring ? {
                            supervisor_name: entry.monitoring?.supervisor?.name || entry?.supervisor?.name || '--',
                            quality_rating: entry.monitoring?.quality_rating ?? null,
                            timeliness_rating: entry.monitoring?.timeliness_rating ?? null,
                            remarks: entry.monitoring?.remarks || null,
                            rated_at: entry.monitoring?.rated_at || null,
                        } : null,
                    };
                })
                : [];

            const modal = document.getElementById('task-view-modal');
            const closeTopBtn = document.getElementById('closeTaskViewTop');
            const closeBottomBtn = document.getElementById('closeTaskViewBottom');

            const fields = {
                employee: document.getElementById('mvEmployee'),
                task: document.getElementById('mvTask'),
                mfo: document.getElementById('mvMfo'),
                date: document.getElementById('mvDate'),
                status: document.getElementById('mvStatus'),
                outputState: document.getElementById('mvOutputState'),
                quantity: document.getElementById('mvQuantity'),
                evidence: document.getElementById('mvEvidence'),
                evidenceFile: document.getElementById('mvEvidenceFile'),
                submittedAt: document.getElementById('mvSubmittedAt'),
                duration: document.getElementById('mvDuration'),
                notes: document.getElementById('mvNotes'),
                supName: document.getElementById('mvSupName'),
                supOffice: document.getElementById('mvSupOffice'),
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
                    case 'submitted': return 'Submitted (Locked)';
                    case 'rated': return 'Rated (Locked)';
                    case 'draft': return 'Draft (Stopped)';
                    case 'recording': return 'Recording';
                    case 'paused': return 'Paused';
                    default: return '--';
                }
            }

            function outputStateLabel(task) {
                if (task.state === 'missing') return 'Output Missing';
                if (task.output_state === 'submitted') return 'Output submitted';
                if (task.output_state === 'validated') return 'Output validated';
                return 'Output pending';
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

            function openTaskViewModal(taskId) {
                const task = tasks.find((item) => item.id === taskId);
                if (!task || !modal) return;

                fields.employee.textContent = task.employeeName;
                fields.task.textContent = task.title;
                fields.mfo.textContent = task.uwpOutputLabel;
                fields.date.textContent = formatDateHuman(task.date);
                fields.status.textContent = statusLabel(task.state);
                fields.outputState.textContent = outputStateLabel(task);
                fields.quantity.textContent = quantityLabel(task.quantity);

                if (task.hasEvidence) {
                    fields.evidence.textContent = 'Attached';
                    fields.evidenceFile.textContent = task.evidenceCount > 1
                        ? `${task.evidenceCount} files attached`
                        : (task.evidenceFileName || 'File attached');
                } else {
                    fields.evidence.textContent = 'None';
                    fields.evidenceFile.textContent = '--';
                }

                fields.submittedAt.textContent = formatDateTime(task.submittedAt);
                fields.duration.textContent = task.durationMs || task.startTime ? formatDuration(computeElapsed(task)) : '--';
                fields.notes.textContent = task.notes || '--';
                
                fields.supName.textContent = task.supervisor.name || '--';
                fields.supOffice.textContent = task.supervisor.office || '--';

                const mon = task.monitoring;
                if (mon && (mon.quality_rating || mon.timeliness_rating || mon.remarks || mon.rated_at)) {
                    fields.supQuality.textContent = mon.quality_rating ? String(mon.quality_rating) : '--';
                    fields.supTimeliness.textContent = mon.timeliness_rating ? String(mon.timeliness_rating) : '--';
                    fields.supRemarks.textContent = (mon.remarks && String(mon.remarks).trim()) ? mon.remarks : '--';
                } else {
                    fields.supQuality.textContent = '--';
                    fields.supTimeliness.textContent = '--';
                    fields.supRemarks.textContent = '--';
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeTaskViewModal() {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.querySelectorAll('.view-task-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const taskId = btn.dataset.taskId;
                    if (taskId) {
                        openTaskViewModal(taskId);
                    }
                });
            });

            if (closeTopBtn) closeTopBtn.addEventListener('click', closeTaskViewModal);
            if (closeBottomBtn) closeBottomBtn.addEventListener('click', closeTaskViewModal);
        });
    </script>
@endsection
