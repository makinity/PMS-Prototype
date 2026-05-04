@extends('layouts.pmt')

@section('main-content')
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Stage IV</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Top Performers</h1>
                <p class="text-sm text-slate-400">Identification-only view for released top and low performers from Stage III results.</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Active Period</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $activePeriod?->name ?? '--' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Top Employees</p>
                <p class="mt-1 text-3xl font-semibold text-emerald-300">{{ $summaryCounts['top_employees'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Outstanding or Very Satisfactory</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Top Offices</p>
                <p class="mt-1 text-3xl font-semibold text-emerald-300">{{ $summaryCounts['top_offices'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Outstanding or Very Satisfactory</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Low Employees</p>
                <p class="mt-1 text-3xl font-semibold text-rose-300">{{ $summaryCounts['low_employees'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Unsatisfactory or Poor</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Low Offices</p>
                <p class="mt-1 text-3xl font-semibold text-rose-300">{{ $summaryCounts['low_offices'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Unsatisfactory or Poor</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-blue-500/30 bg-blue-500/10 text-blue-300">
                    <i class="fa-solid fa-circle-info"></i>
                </span>
                <div>
                    <h2 class="text-lg font-semibold text-white">Stage IV v1</h2>
                    <p class="mt-1 text-sm text-slate-400">This module only identifies released top and low performers. Endorsement, API handoff, and integration workflows are intentionally not included yet.</p>
                    @if ($infoMessage)
                        <p class="mt-3 text-sm font-medium text-amber-300">{{ $infoMessage }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <section class="space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-300">Stage IV Group</p>
                    <h2 class="mt-1 text-xl font-bold text-white">Top Performers</h2>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                    <div class="border-b border-slate-800 px-5 py-4">
                        <h3 class="text-lg font-semibold text-white">Employees</h3>
                        <p class="mt-1 text-sm text-slate-400">Released employee IPCR results classified as Outstanding or Very Satisfactory.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                                <tr>
                                    <th class="px-5 py-4 text-left">Employee</th>
                                    <th class="px-5 py-4 text-left">Office</th>
                                    <th class="px-5 py-4 text-left">Period</th>
                                    <th class="px-5 py-4 text-center">Official Score</th>
                                    <th class="px-5 py-4 text-left">Official Rating</th>
                                    <th class="px-5 py-4 text-left">Released</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($topEmployees as $row)
                                    <tr class="hover:bg-slate-950/40">
                                        <td class="px-5 py-4 font-medium text-white">{{ $row['employee_name'] }}</td>
                                        <td class="px-5 py-4 text-slate-300">{{ $row['office_name'] }}</td>
                                        <td class="px-5 py-4 text-slate-300">{{ $row['period_name'] }}</td>
                                        <td class="px-5 py-4 text-center font-semibold text-emerald-300">{{ number_format((float) $row['official_score'], 2) }}</td>
                                        <td class="px-5 py-4 text-slate-200">{{ $row['official_rating'] }}</td>
                                        <td class="px-5 py-4 text-slate-400">{{ optional($row['released_at'])->format('M d, Y h:i A') ?? '--' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">No top employee performers identified for the active period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                    <div class="border-b border-slate-800 px-5 py-4">
                        <h3 class="text-lg font-semibold text-white">Offices</h3>
                        <p class="mt-1 text-sm text-slate-400">Released office OPCR results classified as Outstanding or Very Satisfactory.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                                <tr>
                                    <th class="px-5 py-4 text-left">Office</th>
                                    <th class="px-5 py-4 text-left">Department Head</th>
                                    <th class="px-5 py-4 text-left">Period</th>
                                    <th class="px-5 py-4 text-center">Official Score</th>
                                    <th class="px-5 py-4 text-left">Official Rating</th>
                                    <th class="px-5 py-4 text-left">Released</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($topOffices as $row)
                                    <tr class="hover:bg-slate-950/40">
                                        <td class="px-5 py-4 font-medium text-white">{{ $row['office_name'] }}</td>
                                        <td class="px-5 py-4 text-slate-300">{{ $row['department_head_name'] }}</td>
                                        <td class="px-5 py-4 text-slate-300">{{ $row['period_name'] }}</td>
                                        <td class="px-5 py-4 text-center font-semibold text-emerald-300">{{ number_format((float) $row['official_score'], 2) }}</td>
                                        <td class="px-5 py-4 text-slate-200">{{ $row['official_rating'] }}</td>
                                        <td class="px-5 py-4 text-slate-400">{{ optional($row['released_at'])->format('M d, Y h:i A') ?? '--' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">No top office performers identified for the active period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-rose-300">Stage IV Group</p>
                    <h2 class="mt-1 text-xl font-bold text-white">Low Performers</h2>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                    <div class="border-b border-slate-800 px-5 py-4">
                        <h3 class="text-lg font-semibold text-white">Employees</h3>
                        <p class="mt-1 text-sm text-slate-400">Released employee IPCR results classified as Unsatisfactory or Poor.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                                <tr>
                                    <th class="px-5 py-4 text-left">Employee</th>
                                    <th class="px-5 py-4 text-left">Office</th>
                                    <th class="px-5 py-4 text-left">Period</th>
                                    <th class="px-5 py-4 text-center">Official Score</th>
                                    <th class="px-5 py-4 text-left">Official Rating</th>
                                    <th class="px-5 py-4 text-left">Released</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($lowEmployees as $row)
                                    <tr class="hover:bg-slate-950/40">
                                        <td class="px-5 py-4 font-medium text-white">{{ $row['employee_name'] }}</td>
                                        <td class="px-5 py-4 text-slate-300">{{ $row['office_name'] }}</td>
                                        <td class="px-5 py-4 text-slate-300">{{ $row['period_name'] }}</td>
                                        <td class="px-5 py-4 text-center font-semibold text-rose-300">{{ number_format((float) $row['official_score'], 2) }}</td>
                                        <td class="px-5 py-4 text-slate-200">{{ $row['official_rating'] }}</td>
                                        <td class="px-5 py-4 text-slate-400">{{ optional($row['released_at'])->format('M d, Y h:i A') ?? '--' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">No low employee performers identified for the active period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                    <div class="border-b border-slate-800 px-5 py-4">
                        <h3 class="text-lg font-semibold text-white">Offices</h3>
                        <p class="mt-1 text-sm text-slate-400">Released office OPCR results classified as Unsatisfactory or Poor.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200">
                            <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                                <tr>
                                    <th class="px-5 py-4 text-left">Office</th>
                                    <th class="px-5 py-4 text-left">Department Head</th>
                                    <th class="px-5 py-4 text-left">Period</th>
                                    <th class="px-5 py-4 text-center">Official Score</th>
                                    <th class="px-5 py-4 text-left">Official Rating</th>
                                    <th class="px-5 py-4 text-left">Released</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($lowOffices as $row)
                                    <tr class="hover:bg-slate-950/40">
                                        <td class="px-5 py-4 font-medium text-white">{{ $row['office_name'] }}</td>
                                        <td class="px-5 py-4 text-slate-300">{{ $row['department_head_name'] }}</td>
                                        <td class="px-5 py-4 text-slate-300">{{ $row['period_name'] }}</td>
                                        <td class="px-5 py-4 text-center font-semibold text-rose-300">{{ number_format((float) $row['official_score'], 2) }}</td>
                                        <td class="px-5 py-4 text-slate-200">{{ $row['official_rating'] }}</td>
                                        <td class="px-5 py-4 text-slate-400">{{ optional($row['released_at'])->format('M d, Y h:i A') ?? '--' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">No low office performers identified for the active period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection
