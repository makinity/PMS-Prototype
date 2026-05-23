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
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-500">Records</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $opcrs->count() }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
            <div class="border-b border-slate-800 px-5 py-4">
                <form method="GET" class="flex w-full max-w-xl items-end gap-2">
                    <div class="flex-1">
                        <label for="pmt-calibration-search" class="mb-2 block text-xs uppercase tracking-[0.14em] text-slate-400">Search</label>
                        <input
                            id="pmt-calibration-search"
                            name="search"
                            type="text"
                            value="{{ $searchTermSafe }}"
                            placeholder="Search office, status..."
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
                        <a href="{{ route('pmt.office-calibration.index') }}"
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
                            <tr class="bg-slate-900/40 hover:bg-slate-900/60 transition">
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
            });
        </script>
    @endpush
@endsection
