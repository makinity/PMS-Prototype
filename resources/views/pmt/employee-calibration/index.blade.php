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
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[220px] flex-1">
                        <label for="pmt-calibration-search" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Search</label>
                        <input
                            id="pmt-calibration-search"
                            type="text"
                            data-live-search
                            placeholder="Search employee, office, status..."
                            style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                            class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500">
                    </div>
                    <div class="w-full min-w-[180px] sm:w-auto">
                        <label for="pmt-calibration-status" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Status</label>
                        <select id="pmt-calibration-status"
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
                                data-status="{{ $statusKey }}"
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
                const statusSelect = document.querySelector('[data-live-status]');
                const submissionRows = Array.from(document.querySelectorAll('[data-review-row]'));
                const noMatchRow = document.getElementById('pmt-submissions-no-match-row');

                function applySubmissionLiveSearch() {
                    if (!liveSearchInput) return;
                    const term = String(liveSearchInput.value || '').trim().toLowerCase();
                    const status = String(statusSelect?.value || '').toLowerCase();
                    let visibleCount = 0;
                    submissionRows.forEach((row) => {
                        const haystack = String(row.dataset.searchText || '').toLowerCase();
                        const rowStatus = String(row.dataset.status || '').toLowerCase();
                        const matchesTerm = term === '' || haystack.includes(term);
                        const matchesStatus = status === '' || rowStatus === status;
                        const isVisible = matchesTerm && matchesStatus;
                        row.classList.toggle('hidden', !isVisible);
                        if (isVisible) visibleCount += 1;
                    });
                    if (noMatchRow) noMatchRow.classList.toggle('hidden', visibleCount > 0 || submissionRows.length === 0);
                }

                liveSearchInput?.addEventListener('input', applySubmissionLiveSearch);
                statusSelect?.addEventListener('change', applySubmissionLiveSearch);
                applySubmissionLiveSearch();
            });
        </script>
    @endpush
@endsection
