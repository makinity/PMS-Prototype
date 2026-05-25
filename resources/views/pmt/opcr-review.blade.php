@extends('layouts.pmt')

@section('main-content')
<section class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">PMT Final OPCR Approval</h1>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4">
        <div class="mb-4 flex flex-wrap items-end gap-3">
            <div class="min-w-[220px] flex-1">
                <label for="pmt-opcr-search" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Search</label>
                <input
                    id="pmt-opcr-search"
                    type="text"
                    placeholder="Search office, period, OPCR ID..."
                    autocomplete="off"
                    data-live-opcr-search
                    style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                    class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500">
            </div>
            <div class="w-full min-w-[180px] sm:w-auto">
                <label for="opcr-status" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Status</label>
                <select id="opcr-status"
                        data-live-opcr-status
                        style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                        class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 [color-scheme:dark]">
                    <option value="">All</option>
                    <option value="submitted" {{ ($selectedStatus ?? '') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="endorsed" {{ ($selectedStatus ?? '') === 'endorsed' ? 'selected' : '' }}>Endorsed</option>
                    <option value="approved" {{ ($selectedStatus ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="returned" {{ ($selectedStatus ?? '') === 'returned' ? 'selected' : '' }}>Returned</option>
                </select>
            </div>
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
                        <tr class="border-t border-gray-700" data-opcr-row data-search-text="{{ $searchHaystack }}" data-status="{{ $opcr->status }}">
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
    const statusSelect = document.querySelector('[data-live-opcr-status]');
    const rows = Array.from(document.querySelectorAll('[data-opcr-row]'));
    const noMatchRow = document.getElementById('pmt-opcr-no-match-row');
    const emptyRow = document.getElementById('pmt-opcr-empty-row');
    if (!searchInput || !rows.length) return;

    const applyFilter = () => {
        const term = String(searchInput.value || '').trim().toLowerCase();
        const status = String(statusSelect?.value || '').toLowerCase();
        let matches = 0;
        rows.forEach((row) => {
            const hay = String(row.getAttribute('data-search-text') || '').toLowerCase();
            const rowStatus = String(row.getAttribute('data-status') || '').toLowerCase();
            const matchesTerm = term === '' || hay.includes(term);
            const matchesStatus = status === '' || rowStatus === status;
            const visible = matchesTerm && matchesStatus;
            row.classList.toggle('hidden', !visible);
            if (visible) matches++;
        });
        if (noMatchRow) noMatchRow.classList.toggle('hidden', matches !== 0);
        if (emptyRow) emptyRow.classList.toggle('hidden', true);
    };

    searchInput.addEventListener('input', applyFilter);
    if (statusSelect) statusSelect.addEventListener('change', applyFilter);
    applyFilter();
});
</script>
@endpush
@endsection
