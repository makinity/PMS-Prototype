@extends('layouts.dept-head')

@section('main-content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-100">Unit Work Plan Review</h1>
    <p class="mt-1 text-sm text-slate-400">
        Select submitted Unit Work Plans. Consolidate them into OPCR or return with remarks.
    </p>
</div>

<div class="mb-6 flex flex-col gap-4 rounded-xl border border-gray-700 bg-slate-900/80 p-5 md:flex-row md:items-center md:justify-between">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-400">Performance Period</p>
        <p class="font-medium text-slate-100">{{ $activePeriod->name ?? '—' }}</p>
    </div>

    @php
        $statusFilter = strtolower((string) ($selectedStatus ?? request('status', '')));
    @endphp
    <form method="GET" action="{{ route('dept-head.uwp.index') }}">
        <label for="dh-uwp-status" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Status</label>
        <select
            id="dh-uwp-status"
            name="status"
            onchange="this.form.submit()"
            style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
            class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 [color-scheme:dark]">
            <option value="" {{ $statusFilter === '' ? 'selected' : '' }}>All</option>
            <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>Submitted</option>
            <option value="consolidated" {{ $statusFilter === 'consolidated' ? 'selected' : '' }}>Consolidated</option>
            <option value="returned" {{ $statusFilter === 'returned' ? 'selected' : '' }}>Returned</option>
            <option value="endorsed" {{ $statusFilter === 'endorsed' ? 'selected' : '' }}>Endorsed</option>
        </select>
    </form>
</div>

<div class="overflow-hidden rounded-xl border border-gray-700 bg-slate-900/80">
    <div class="border-b border-gray-700 p-5">
        <h2 class="text-lg font-medium text-slate-100">Offices / Units</h2>
        <p class="mt-1 text-sm text-slate-400">Open a unit to review its UWP details on a dedicated page.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-800/60">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-400">Office / Unit</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-400">Supervisor</th>
                    <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">UWP Type</th>
                    <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium uppercase text-slate-400">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse ($uwps as $uwp)
                    @php
                        $statusKey = strtolower(str_replace('-', '_', (string) ($uwp->status ?? '')));
                        $badge = match($statusKey) {
                            'returned' => ['bg'=>'bg-rose-500/10', 'text'=>'text-rose-300', 'border'=>'border-rose-500/20', 'label'=>'Returned'],
                            'submitted' => ['bg'=>'bg-blue-500/10', 'text'=>'text-blue-300', 'border'=>'border-blue-500/20', 'label'=>'Submitted'],
                            'consolidated' => ['bg'=>'bg-cyan-500/10', 'text'=>'text-cyan-300', 'border'=>'border-cyan-500/20', 'label'=>'Consolidated'],
                            'endorsed' => ['bg'=>'bg-violet-500/10', 'text'=>'text-violet-300', 'border'=>'border-violet-500/20', 'label'=>'Endorsed'],
                            'approved', 'pmt_approved' => ['bg'=>'bg-emerald-500/10', 'text'=>'text-emerald-300', 'border'=>'border-emerald-500/20', 'label'=>'Approved'],
                            default => ['bg'=>'bg-amber-500/10', 'text'=>'text-amber-300', 'border'=>'border-amber-500/20', 'label'=>ucwords(str_replace('_', ' ', $statusKey ?: 'unknown'))],
                        };
                    @endphp
                    <tr class="transition hover:bg-slate-800/40">
                        <td class="px-4 py-3 text-sm font-medium text-slate-100">{{ $uwp->office?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ $uwp->creator?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-sm text-slate-300">Unit-Level Plan</td>
                        <td class="px-4 py-3 text-center text-sm">
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-sm">
                            <a href="{{ route('dept-head.uwp.show', ['id' => $uwp->id, 'status' => $statusFilter]) }}"
                               class="inline-flex items-center justify-center rounded-lg border border-blue-500 px-3 py-2 text-sm font-medium text-blue-400 transition hover:bg-blue-500/10">
                                Review UWP
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">No Unit Work Plans found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
