@extends('layouts.pmt')

@section('main-content')
    @php
        $submissionRows = is_array($rows ?? null) ? $rows : [];
        $periodLabelSafe = (string) ($periodLabel ?? '--');
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
                <h1 class="text-2xl font-bold text-white">IPCR Final Calibration</h1>
                <p class="mt-1 text-xs text-slate-500">Active Performance Period: {{ $periodLabelSafe }}</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-slate-900/40 px-4 py-3 text-right">
                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Records</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ count($submissionRows) }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-700 bg-slate-900/40">
            <div class="border-b border-gray-700 px-5 py-4">
                <form method="GET" class="flex w-full max-w-xl items-end gap-2">
                    <div class="flex-1">
                        <label for="pmt-calibration-search" class="mb-2 block text-xs uppercase tracking-[0.14em] text-slate-400">Search</label>
                        <input
                            id="pmt-calibration-search"
                            name="search"
                            type="text"
                            value="{{ $searchTermSafe }}"
                            data-live-search
                            placeholder="Search employee, office, period, status..."
                            style="background-color: #020617 !important; color: #f1f5f9 !important;"
                            class="w-full rounded-xl border border-slate-700 px-4 py-3 text-sm placeholder:text-slate-500 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                    </div>
                    <button
                        type="submit"
                        aria-label="Search records"
                        style="background-color: #020617 !important; color: #f1f5f9 !important;"
                        class="inline-flex h-[50px] w-[50px] items-center justify-center rounded-xl border border-slate-700 text-slate-100 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path stroke-linecap="round" d="M20 20l-3.5-3.5"></path>
                        </svg>
                    </button>
                    @if ($searchTermSafe !== '')
                        <a href="{{ route('pmt.employee-calibration.index') }}"
                            class="inline-flex h-[50px] items-center rounded-xl border border-slate-700 px-4 text-sm font-medium text-slate-300 transition hover:bg-slate-800">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/70 text-xs uppercase tracking-[0.14em] text-slate-400">
                        <tr>
                            <th class="px-5 py-3 text-left">Employee</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($submissionRows as $row)
                            @php
                                $statusKey = strtolower((string) ($row['status'] ?? 'pending_pmt_calibration'));
                                $statusBadgeClasses = $statusBadgeClassMap[$statusKey] ?? 'inline-flex items-center rounded-full border border-slate-600 bg-slate-800/70 px-2.5 py-1 text-xs font-semibold text-slate-200';
                                $rowSearchText = collect([
                                    $row['employee_name'] ?? '',
                                    $row['office_name'] ?? '',
                                    $row['status_label'] ?? 'Pending',
                                ])->filter()->implode(' ');
                            @endphp
                            <tr class="bg-slate-900/40"
                                data-review-row
                                data-search-text="{{ \Illuminate\Support\Str::lower($rowSearchText) }}">
                                <td class="px-5 py-3 font-semibold text-slate-100">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($row['employee_name'] ?? 'U') }}&background=1e293b&color=cbd5e1" class="h-8 w-8 rounded-full object-cover" alt="Avatar">
                                        <span>{{ $row['employee_name'] ?? '--' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="{{ $statusBadgeClasses }}">{{ $row['status_label'] ?? 'Pending' }}</span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <a href="{{ route('pmt.employee-calibration.show', $row['id']) }}"
                                       class="inline-flex items-center justify-center rounded bg-blue-500/10 p-2 text-blue-400 transition-colors hover:bg-blue-500/20"
                                       title="View & Calibrate">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-slate-900/40">
                                <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">No IPCRs pending calibration, calibrated, or released found for the active period.</td>
                            </tr>
                        @endforelse
                        <tr id="pmt-submissions-no-match-row" class="hidden bg-slate-900/40">
                            <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">No matching IPCRs found.</td>
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

                const liveSearchInput = document.querySelector('[data-live-search]');
                const submissionRows = Array.from(document.querySelectorAll('[data-review-row]'));
                const noMatchRow = document.getElementById('pmt-submissions-no-match-row');

                function applySubmissionLiveSearch() {
                    if (!liveSearchInput) return;
                    const term = String(liveSearchInput.value || '').trim().toLowerCase();
                    let visibleCount = 0;
                    submissionRows.forEach((row) => {
                        const haystack = String(row.dataset.searchText || '').toLowerCase();
                        const isVisible = term === '' || haystack.includes(term);
                        row.classList.toggle('hidden', !isVisible);
                        if (isVisible) visibleCount += 1;
                    });
                    if (noMatchRow) noMatchRow.classList.toggle('hidden', visibleCount > 0 || submissionRows.length === 0);
                }

                liveSearchInput?.addEventListener('input', applySubmissionLiveSearch);
                applySubmissionLiveSearch();
            });
        </script>
    @endpush
@endsection
