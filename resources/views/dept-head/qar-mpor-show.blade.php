@extends('layouts.dept-head')

@section('main-content')
    @php
        $meta = $meta ?? [];
        $sectionLabels = $sectionLabels ?? ['core' => 'Core Functions (80%)', 'support' => 'Support Functions (20%)'];
        $sectionRows = $sectionRows ?? ['core' => [], 'support' => []];
        $grandTotals = $grandTotals ?? ['qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0], 'qtyTotal' => 0];
        $kpis = $kpis ?? ['includedRated' => 0, 'excluded' => 0];
        $status = strtolower((string) ($meta['status'] ?? ''));
        $statusBadgeClass = match ($status) {
            'submitted' => 'border-blue-500/30 bg-blue-500/10 text-blue-200',
            'approved' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200',
            'endorsed' => 'border-violet-500/30 bg-violet-500/10 text-violet-200',
            'returned' => 'border-rose-500/30 bg-rose-500/10 text-rose-200',
            default => 'border-slate-700 bg-slate-800 text-slate-200',
        };
    @endphp

    <section class="space-y-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Monthly Performance Output Report</p>
                <h1 class="mt-1 text-xl font-bold leading-tight text-white sm:text-2xl md:text-3xl">MONTHLY PERFORMANCE OUTPUT REPORT</h1>
                <p class="mt-1 text-sm text-slate-400 md:text-base">Read-only mirror of locked ORS entries with supervisor ratings.</p>
                <span class="mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $statusBadgeClass }}">
                    {{ strtoupper($status ?: '--') }}
                </span>
            </div>
            <a href="{{ route('dept-head.qar', ['q' => $backQuarter > 0 ? $backQuarter : null]) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                Back to QAR
            </a>
        </div>

        <div class="grid grid-cols-2 gap-2 text-xs uppercase tracking-[0.2em] text-white sm:grid-cols-3">
            <div class="rounded-lg border border-slate-800 bg-slate-900/50 px-3 py-2">
                <p class="text-[0.6rem] text-slate-500">NAME</p>
                <p class="mt-0.5 truncate text-sm font-semibold normal-case tracking-normal">{{ $meta['employeeName'] ?? '--' }}</p>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-900/50 px-3 py-2">
                <p class="text-[0.6rem] text-slate-500">OFFICE / DIVISION</p>
                <p class="mt-0.5 truncate text-sm font-semibold normal-case tracking-normal">{{ $meta['officeName'] ?? '--' }}</p>
            </div>
            <div class="col-span-2 rounded-lg border border-slate-800 bg-slate-900/50 px-3 py-2 sm:col-span-1">
                <p class="text-[0.6rem] text-slate-500">MONTH</p>
                <p class="mt-0.5 truncate text-sm font-semibold normal-case tracking-normal">{{ $meta['monthLabel'] ?? '--' }}</p>
            </div>
        </div>

        <div class="hidden lg:block">
            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                <div class="overflow-x-auto max-h-[38rem]">
                    <table class="min-w-full text-[0.75rem] text-slate-200">
                        <thead class="sticky top-0 z-20 bg-slate-900/95">
                            <tr class="text-left text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                                <th class="sticky left-0 z-30 whitespace-nowrap px-3 py-3 align-bottom bg-slate-900/95" rowspan="2">Output / Task</th>
                                <th class="border-l border-slate-800 px-3 py-3 text-center" colspan="5">Efficiency / Quantity</th>
                                <th class="border-l border-slate-800 px-3 py-3 text-center" colspan="5">Quality / Effectiveness</th>
                                <th class="border-l border-slate-800 px-3 py-3 text-center" colspan="5">Timeliness</th>
                            </tr>
                            <tr class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">
                                @for ($i = 0; $i < 3; $i++)
                                    <th class="{{ $i === 0 ? 'border-l border-slate-800' : '' }} px-2 py-2 text-right">W1</th>
                                    <th class="px-2 py-2 text-right">W2</th>
                                    <th class="px-2 py-2 text-right">W3</th>
                                    <th class="px-2 py-2 text-right">W4</th>
                                    <th class="px-2 py-2 text-right font-semibold">Total</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-[0.75rem]">
                            @foreach ($sectionLabels as $sectionKey => $sectionLabel)
                                <tr class="bg-slate-800/40 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                    <td class="sticky left-0 z-10 border-r border-slate-800 bg-slate-800/80 px-3 py-2 font-semibold text-slate-100" colspan="16">{{ $sectionLabel }}</td>
                                </tr>
                                @forelse ($sectionRows[$sectionKey] ?? [] as $row)
                                    <tr>
                                        <td class="sticky left-0 z-10 max-w-[20rem] border-r border-slate-800 bg-slate-900/95 px-3 py-3 font-semibold text-white">
                                            <span class="block truncate" title="{{ $row['label'] }}">{{ $row['label'] }}</span>
                                        </td>
                                        @foreach (['qty', 'qual', 'time'] as $group)
                                            <td class="border-l border-slate-800 px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, "{$group}.1", 0), 0) }}</td>
                                            <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, "{$group}.2", 0), 0) }}</td>
                                            <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, "{$group}.3", 0), 0) }}</td>
                                            <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, "{$group}.4", 0), 0) }}</td>
                                            <td class="px-2 py-3 text-right font-semibold tabular-nums">{{ number_format(data_get($row, "{$group}Total", 0), 0) }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr><td colspan="16" class="px-3 py-6 text-center text-sm text-slate-500">No entries available.</td></tr>
                                @endforelse
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-400">MPOR points = Quantity × Supervisor Rating (Q/T). Only rated ORS entries with supervisor ratings are included.</p>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-xs uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-[0.6rem] tracking-[0.3em] text-slate-500">
                    <span>Week 1</span><span>Week 2</span><span>Week 3</span><span>Week 4</span><span>Total</span>
                </div>
                <div class="mt-2 grid grid-cols-5 text-center text-sm font-semibold text-white">
                    <span>{{ number_format(data_get($grandTotals, 'qty.1', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qty.2', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qty.3', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qty.4', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qtyTotal', 0), 0) }}</span>
                </div>
                <div class="my-5 border-t border-slate-700/70"></div>
                <div class="mt-3 space-y-2 text-[0.65rem] tracking-[0.2em] text-slate-500">
                    <div class="flex items-center justify-between gap-3">
                        <span class="min-w-0">Included ORS entries (rated)</span>
                        <span class="shrink-0 font-semibold text-white">{{ number_format((int) ($kpis['includedRated'] ?? 0), 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="min-w-0">Excluded entries (unrated/draft/missing)</span>
                        <span class="shrink-0 font-semibold text-white">{{ number_format((int) ($kpis['excluded'] ?? 0), 0) }}</span>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-sm font-semibold text-white">
                    <span>Confirmed:</span>
                    <span class="text-slate-500">Stage II</span>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Supervisor</p>
                        <p class="text-sm font-semibold text-white normal-case tracking-normal">{{ $meta['officeName'] ?? '--' }}</p>
                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                    </div>
                    <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Employee</p>
                        <p class="text-sm font-semibold text-white normal-case tracking-normal">{{ $meta['employeeName'] ?? '--' }}</p>
                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
