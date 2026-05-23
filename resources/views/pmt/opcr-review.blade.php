@extends('layouts.pmt')

@section('main-content')
<section class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">PMT Final OPCR Approval</h1>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
            <form method="GET" action="{{ route('pmt.opcr.review.index') }}" id="pmt-opcr-search-form" class="flex flex-wrap items-end justify-start gap-2">
                <input type="hidden" name="status" value="{{ $selectedStatus }}">
                <div class="min-w-[260px]">
                    <label for="pmt-opcr-search" class="mb-1 block text-xs uppercase tracking-wide text-slate-500">Search</label>
                    <div class="flex items-center gap-2">
                        <input
                            id="pmt-opcr-search"
                            type="text"
                            name="search"
                            value="{{ $searchTerm ?? '' }}"
                            placeholder="Search office, period, OPCR ID..."
                            autocomplete="off"
                            style="background:#0f172a;color:#e5e7eb;"
                            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                            data-live-opcr-search>
                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-700 bg-slate-950 text-slate-200 transition hover:bg-slate-800" aria-label="Search OPCR records">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </button>
                    </div>
                </div>
                @if (($searchTerm ?? '') !== '')
                    <a href="{{ route('pmt.opcr.review.index', $selectedStatus !== '' ? ['status' => $selectedStatus] : []) }}"
                       class="rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-800">
                        Clear
                    </a>
                @endif
            </form>

            <form method="GET" action="{{ route('pmt.opcr.review.index') }}" class="flex items-end justify-end gap-2">
                <input type="hidden" name="search" value="{{ $searchTerm ?? '' }}">
                <div>
                    <label for="opcr-status" class="mb-1 block text-xs uppercase tracking-wide text-slate-500">Status</label>
                    <select id="opcr-status"
                            name="status"
                            onchange="this.form.submit()"
                            style="background:#0f172a;color:#e5e7eb;"
                            class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 focus:outline-none">
                        <option value="" {{ $selectedStatus === '' ? 'selected' : '' }}>All Status</option>
                        <option value="submitted" {{ $selectedStatus === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="endorsed" {{ $selectedStatus === 'endorsed' ? 'selected' : '' }}>Endorsed</option>
                        <option value="approved" {{ $selectedStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="returned" {{ $selectedStatus === 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-300">
                <thead class="text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-2 text-left">Office / Unit</th>
                        <th class="px-4 py-2 text-left">Period</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($opcrs as $opcr)
                        @php
                            $isReviewable = in_array(strtolower((string) $opcr->status), ['endorsed', 'for_pmt_review'], true);
                            $statusMeta = match (strtolower((string) $opcr->status)) {
                                'endorsed', 'for_pmt_review' => ['label' => 'Endorsed', 'class' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300'],
                                'approved' => ['label' => 'Approved', 'class' => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300'],
                                'returned' => ['label' => 'Returned', 'class' => 'border-rose-500/30 bg-rose-500/10 text-rose-300'],
                                default => ['label' => 'Submitted', 'class' => 'border-amber-500/30 bg-amber-500/20 text-amber-300'],
                            };
                            $officeName = $opcr->office?->name ?? $opcr->unitWorkPlan?->office?->name ?? '-';
                            $periodName = $opcr->performancePeriod?->name ?? $opcr->unitWorkPlan?->performancePeriod?->name ?? '-';
                            $searchHaystack = strtolower(implode(' ', array_filter([
                                $officeName,
                                $periodName,
                                $statusMeta['label'],
                                'OPCR ' . $opcr->id,
                            ])));
                        @endphp
                        <tr class="border-t border-slate-800" data-opcr-row data-search-text="{{ $searchHaystack }}">
                            <td class="px-4 py-3 text-white">{{ $officeName }}</td>
                            <td class="px-4 py-3">{{ $periodName }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('pmt.opcr.review.show', ['opcr' => $opcr->id]) }}" class="text-blue-400 hover:text-blue-300">
                                    {{ $isReviewable ? 'Review' : 'View' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="pmt-opcr-empty-row">
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">No OPCR records found for PMT review.</td>
                        </tr>
                    @endforelse
                    @if($opcrs->isNotEmpty())
                        <tr id="pmt-opcr-no-match-row" class="hidden">
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">No matching OPCR records found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</section>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('[data-live-opcr-search]');
    const rows = Array.from(document.querySelectorAll('[data-opcr-row]'));
    const noMatchRow = document.getElementById('pmt-opcr-no-match-row');
    const emptyRow = document.getElementById('pmt-opcr-empty-row');
    if (!searchInput || !rows.length) return;

    const applySearch = () => {
        const term = String(searchInput.value || '').trim().toLowerCase();
        let matches = 0;
        rows.forEach((row) => {
            const hay = String(row.getAttribute('data-search-text') || '').toLowerCase();
            const visible = term === '' || hay.includes(term);
            row.classList.toggle('hidden', !visible);
            if (visible) matches++;
        });
        if (noMatchRow) noMatchRow.classList.toggle('hidden', matches !== 0);
        if (emptyRow) emptyRow.classList.toggle('hidden', true);
    };

    searchInput.addEventListener('input', applySearch);
    applySearch();
});
</script>
@endpush
@endsection
