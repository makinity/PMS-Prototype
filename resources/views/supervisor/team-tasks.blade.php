@extends('layouts.supervisor')

@section('main-content')
    @php
        $selectedStatus = strtolower((string) request('status', 'all'));
        $selectedEmployee = (string) request('employee_id', '');
        $workDate = (string) request('work_date', '');
        $dateFrom = (string) request('date_from', '');
        $dateTo = (string) request('date_to', '');

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

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                    {{ session('error') }}
                </div>
            @endif

            <form method="GET" action="{{ route('supervisor.team-tasks') }}"
                class="mb-4 grid grid-cols-1 gap-3 rounded-lg border border-slate-800 bg-slate-950/40 p-4 md:grid-cols-2 lg:grid-cols-3">
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

                <div>
                    <label for="work_date" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Work Date</label>
                    <input id="work_date"
                        style="background:#0f172a;color:#e5e7eb;"
                        type="date"
                        name="work_date"
                        value="{{ $workDate }}"
                        class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-slate-200">
                </div>

                <div>
                    <label for="date_from" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Date From</label>
                    <input id="date_from"
                        style="background:#0f172a;color:#e5e7eb;"
                        type="date"
                        name="date_from"
                        value="{{ $dateFrom }}"
                        class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-slate-200">
                </div>

                <div>
                    <label for="date_to" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Date To</label>
                    <input id="date_to"
                        style="background:#0f172a;color:#e5e7eb;"
                        type="date"
                        name="date_to"
                        value="{{ $dateTo }}"
                        class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-slate-200">
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
                            <th class="px-4 py-3 text-left font-semibold">Output Type</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Evidence</th>
                            <th class="px-4 py-3 text-left font-semibold">Quantity</th>
                            <th class="px-4 py-3 text-left font-semibold">Duration</th>
                            <th class="px-4 py-3 text-left font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800 text-slate-200">
                        @forelse ($entries as $entry)
                            @php
                                $taskLabel = ($entry->ipcrItem->output_title ?? '—') . ' — ' . ($entry->ipcrItem->indicator_text ?? '');
                                $outputType = $entry->ipcrItem->output_type ?? '—';
                                $status = strtolower((string) ($entry->status ?? 'draft'));
                                $statusBadge = $statusMeta[$status] ?? 'bg-slate-800 border border-slate-700 text-slate-200';
                                $duration = $entry->duration ?? $entry->duration_minutes ?? '—';
                            @endphp
                            <tr class="hover:bg-slate-900/60">
                                <td class="px-4 py-3 text-white">{{ $entry->employee->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-100">{{ $taskLabel }}</td>
                                <td class="px-4 py-3">{{ $entry->work_date ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $outputType }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if (($entry->evidences_count ?? 0) > 0)
                                        <span class="px-3 py-1 rounded-full bg-emerald-900/40 border border-emerald-700/40 text-emerald-300 text-xs font-semibold">
                                            Attached
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-slate-300 text-xs font-semibold">
                                            None
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $entry->quantity ?: '—' }}</td>
                                <td class="px-4 py-3">{{ $duration }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('supervisor.team-tasks.monitor', $entry) }}"
                                        class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500">
                                        Monitor
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-sm text-slate-400">
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
@endsection
