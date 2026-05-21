@extends('layouts.supervisor')

@section('main-content')
    @php
        $mpors = $mpors ?? collect();
        $month = $month ?? now()->format('Y-m');
        $monthLabel = $monthLabel ?? now()->format('F Y');
    @endphp

    <section class="space-y-4">
        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Stage II</p>
                <h1 class="mt-1 text-xl font-bold text-white md:text-2xl">MPOR List (Supervisor)</h1>
                <p class="mt-1 text-sm text-slate-400">Showing submitted, approved, and endorsed MPORs for {{ $monthLabel }}.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('supervisor.mpor') }}"
            class="grid grid-cols-2 gap-2 rounded-2xl border border-slate-800 bg-slate-900/70 p-3 md:grid-cols-12 md:items-end">
            <div class="col-span-2 md:col-span-5">
                <label class="mb-2 block text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-500">Employee</label>
                <input type="text" name="employee_id" style="background:#0f172a;color:#e5e7eb;"
                    class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-emerald-500 focus:ring-0"
                    placeholder="Search...">
            </div>
            <div class="col-span-1 md:col-span-5">
                <label class="mb-2 block text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-500">Month</label>
                <input type="month" name="month" value="{{ $month }}" style="background:#0f172a;color:#e5e7eb;"
                    class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-emerald-500 focus:ring-0">
            </div>
            <div class="col-span-1 flex items-end gap-2 md:col-span-2">
                <button type="submit" class="w-full rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-500/20">
                    Apply
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-200">
                    <thead class="text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                        <tr class="border-b border-slate-800">
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($mpors as $mpor)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-white">{{ $mpor->employee?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $statusKey = strtolower((string) ($mpor->status ?? ''));
                                        $badgeClass = match ($statusKey) {
                                            'submitted' => 'border-blue-500/30 bg-blue-500/10 text-blue-200',
                                            'approved' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200',
                                            'endorsed' => 'border-violet-400/30 bg-violet-400/10 text-violet-200',
                                            default => 'border-slate-700 bg-slate-800 text-slate-200',
                                        };
                                    @endphp
                                    <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">{{ strtoupper($statusKey ?: '—') }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('supervisor.mpor.show', ['mpor' => $mpor->id]) }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-700 bg-slate-800 text-slate-200 transition hover:bg-slate-700 hover:text-white"
                                        title="Preview MPOR"
                                        aria-label="Preview MPOR">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center text-slate-500">No MPOR records found for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
