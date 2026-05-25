@extends('layouts.pmt')

@section('main-content')
    @php
        $periodLabelSafe = (string) ($activePeriod?->name ?? '--');
        $searchTermSafe = trim((string) ($search ?? ''));

        $statusBadgeClassMap = [
            'pending_pmt_calibration' => 'inline-flex items-center rounded-full border border-sky-500/40 bg-sky-500/10 px-2.5 py-1 text-xs font-semibold text-sky-200',
            'approved_by_pmt' => 'inline-flex items-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-200',
            'adjusted_by_pmt' => 'inline-flex items-center rounded-full border border-violet-500/40 bg-violet-500/10 px-2.5 py-1 text-xs font-semibold text-violet-200',
            'released_by_pmt' => 'inline-flex items-center rounded-full border border-cyan-500/40 bg-cyan-500/10 px-2.5 py-1 text-xs font-semibold text-cyan-200',
            'returned_by_pmt' => 'inline-flex items-center rounded-full border border-rose-500/40 bg-rose-500/10 px-2.5 py-1 text-xs font-semibold text-rose-200',
        ];
    @endphp

    <section class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-white">OPCR Final Calibration</h1>
                <p class="mt-1 text-xs text-slate-500">Active Performance Period: {{ $periodLabelSafe }}</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-slate-900/40 px-4 py-3 text-right">
                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Records</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $opcrs->count() }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-700 bg-slate-900/40">
            <div class="border-b border-gray-700 px-5 py-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[220px] flex-1">
                        <label for="pmt-calibration-search" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Search</label>
                        <input
                            id="pmt-calibration-search"
                            type="text"
                            data-live-search
                            placeholder="Search office, head, status..."
                            style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                            class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500">
                    </div>
                    <div class="w-full min-w-[180px] sm:w-auto">
                        <label for="pmt-office-cal-status" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Status</label>
                        <select id="pmt-office-cal-status"
                                data-live-status
                                style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                                class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 [color-scheme:dark]">
                            <option value="">All</option>
                            <option value="pending_pmt_calibration">Pending Calibration</option>
                            <option value="approved_by_pmt">Approved</option>
                            <option value="adjusted_by_pmt">Adjusted</option>
                            <option value="released_by_pmt">Released</option>
                            <option value="returned_by_pmt">Returned</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/70 text-xs uppercase tracking-[0.14em] text-slate-400">
                        <tr>
                            <th class="px-5 py-3 text-left">Office</th>
                            <th class="px-5 py-3 text-left">Head</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($opcrs as $opcr)
                            @php
                                $statusKey = strtolower((string) ($opcr->status ?? 'pending_pmt_calibration'));
                                $statusBadgeClasses = $statusBadgeClassMap[$statusKey] ?? 'inline-flex items-center rounded-full border border-slate-600 bg-slate-800/70 px-2.5 py-1 text-xs font-semibold text-slate-200';
                                $statusLabel = match ($statusKey) {
                                    'pending_pmt_calibration' => 'Pending Calibration',
                                    'approved_by_pmt' => 'Calibrated (Approved)',
                                    'adjusted_by_pmt' => 'Calibrated (Adjusted)',
                                    'released_by_pmt' => 'Officially Released',
                                    'returned_by_pmt' => 'Returned by PMT',
                                    default => ucwords(str_replace('_', ' ', $statusKey)),
                                };
                            @endphp
                            <tr class="bg-slate-900/40 hover:bg-slate-900/60 transition"
                                data-review-row
                                data-status="{{ $statusKey }}"
                                data-search-text="{{ strtolower(($opcr->office->name ?? '') . ' ' . ($opcr->office->head->name ?? '') . ' ' . $statusLabel) }}">
                                <td class="px-5 py-3 font-semibold text-slate-100">{{ $opcr->office->name ?? '--' }}</td>
                                <td class="px-5 py-3">{{ $opcr->office->head->name ?? '--' }}</td>
                                <td class="px-5 py-3">
                                    <span class="{{ $statusBadgeClasses }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <a href="{{ route('pmt.office-calibration.show', $opcr->id) }}"
                                       class="inline-flex items-center justify-center rounded-lg border border-slate-700 p-2 text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40"
                                       title="Review">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-slate-900/40">
                                <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-400">{{ $infoMessage ?? 'No OPCRs pending calibration or calibrated found for the active period.' }}</td>
                            </tr>
                        @endforelse
                        <tr id="pmt-office-cal-no-match-row" class="hidden bg-slate-900/40">
                            <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-400">No matching records found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                @if (!empty($infoMessage ?? null))
                    if (window.PMSnackbar && !window.PMSnackbar.hasActive()) {
                        window.PMSnackbar.show({
                            type: 'info',
                            message: @json((string) $infoMessage),
                        });
                    }
                @endif
                @if(session('success'))
                    if (window.PMSnackbar) {
                        window.PMSnackbar.show({
                            type: 'success',
                            message: @json(session('success')),
                        });
                    }
                @endif
                @if(session('error'))
                    if (window.PMSnackbar) {
                        window.PMSnackbar.show({
                            type: 'error',
                            message: @json(session('error')),
                        });
                    }
                @endif

                const searchInput = document.querySelector('[data-live-search]');
                const statusSelect = document.querySelector('[data-live-status]');
                const rows = Array.from(document.querySelectorAll('[data-review-row]'));
                const noMatchRow = document.getElementById('pmt-office-cal-no-match-row');

                function applyFilter() {
                    const term = String(searchInput?.value || '').trim().toLowerCase();
                    const status = String(statusSelect?.value || '').toLowerCase();
                    let visible = 0;
                    rows.forEach(row => {
                        const hay = String(row.dataset.searchText || '').toLowerCase();
                        const rowStatus = String(row.dataset.status || '').toLowerCase();
                        const show = (term === '' || hay.includes(term)) && (status === '' || rowStatus === status);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });
                    if (noMatchRow) noMatchRow.classList.toggle('hidden', visible > 0 || rows.length === 0);
                }

                searchInput?.addEventListener('input', applyFilter);
                statusSelect?.addEventListener('change', applyFilter);
                applyFilter();
            });
        </script>
    @endpush
@endsection
