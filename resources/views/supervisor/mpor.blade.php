@extends('layouts.supervisor')

@section('main-content')
    <section class="space-y-6">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Stage II</p>
                    <h1 class="mt-1 text-2xl font-bold text-white">MPOR Monitoring</h1>
                    <p class="mt-1 text-sm text-slate-400">Read-only view of employee MPOR attachments for the selected month.</p>
                </div>

                <form method="GET" action="{{ route('supervisor.mpor') }}" class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                    <div>
                        <label for="employee_id" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Employee</label>
                        <select id="employee_id"
                            name="employee_id"
                            style="background:#0f172a;color:#e5e7eb;"
                            class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-slate-200">
                            <option value="">Select employee</option>
                            @foreach ($teamEmployees as $teamEmployee)
                                <option value="{{ $teamEmployee->id }}" @selected($selectedEmployeeId === (int) $teamEmployee->id)>
                                    {{ $teamEmployee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="month" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Month</label>
                        <input id="month"
                            type="month"
                            name="month"
                            value="{{ $month }}"
                            style="background:#0f172a;color:#e5e7eb;"
                            class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-sm text-slate-200">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                            Apply
                        </button>
                        <a href="{{ route('supervisor.mpor') }}"
                            class="rounded-lg border border-slate-700 px-4 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        @if ($selectedEmployeeId <= 0)
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 text-sm text-slate-300">
                Select an employee to view MPOR details for {{ $monthLabel }}.
            </div>
        @elseif (!$mpor)
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 text-sm text-slate-300">
                No MPOR found for the selected employee and month.
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Employee</p>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $mpor->employee?->name ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Office / Division</p>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $officeLabel }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Month</p>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $monthLabel }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Status</p>
                    <p class="mt-2 text-lg font-semibold text-white">{{ ucfirst((string) $mpor->status) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Attached Rated ORS</p>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $summary['entry_count'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Days Count</p>
                    <p class="mt-2 text-lg font-semibold text-white">{{ $summary['days_count'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Sum Quantity</p>
                    <p class="mt-2 text-lg font-semibold text-white">{{ number_format((float) $summary['sum_quantity'], 2) }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Avg Quality</p>
                    <p class="mt-2 text-lg font-semibold text-white">
                        {{ is_null($summary['avg_quality']) ? '--' : number_format((float) $summary['avg_quality'], 2) }}
                    </p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Avg Timeliness</p>
                    <p class="mt-2 text-lg font-semibold text-white">
                        {{ is_null($summary['avg_timeliness']) ? '--' : number_format((float) $summary['avg_timeliness'], 2) }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-white">Attached Rated ORS (Read-only)</h2>
                    <span class="text-xs text-slate-400">MPOR #{{ $mpor->id }}</span>
                </div>

                <div class="overflow-x-auto rounded-lg border border-slate-800">
                    <table class="min-w-full divide-y divide-slate-800 text-sm">
                        <thead class="bg-slate-950/60 text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th class="px-3 py-2">Work Date</th>
                                <th class="px-3 py-2">Task / Indicator</th>
                                <th class="px-3 py-2">Quantity</th>
                                <th class="px-3 py-2">Q</th>
                                <th class="px-3 py-2">T</th>
                                <th class="px-3 py-2">Evidence</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-slate-200">
                            @forelse ($attachedEntries as $entry)
                                <tr class="hover:bg-slate-800/40">
                                    <td class="px-3 py-2 align-top">{{ $entry['work_date_label'] }}</td>
                                    <td class="px-3 py-2 align-top text-slate-100">{{ $entry['task_text'] }}</td>
                                    <td class="px-3 py-2 align-top">{{ $entry['quantity_label'] }}</td>
                                    <td class="px-3 py-2 align-top">{{ $entry['quality_label'] }}</td>
                                    <td class="px-3 py-2 align-top">{{ $entry['timeliness_label'] }}</td>
                                    <td class="px-3 py-2 align-top">{{ $entry['evidence_count'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-slate-400">No attached rated ORS entries found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </section>
@endsection
