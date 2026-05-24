@extends('layouts.pmt')

@section('main-content')
    @php
        $statusMeta = [
            'draft' => ['label' => 'Draft', 'badge' => 'border-slate-600/60 bg-slate-700/40 text-slate-200'],
            'dept_head_endorsed' => ['label' => 'Dept Head Endorsed', 'badge' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'],
            'returned' => ['label' => 'Returned', 'badge' => 'border-rose-500/40 bg-rose-500/10 text-rose-200'],
            'pmt_approved' => ['label' => 'PMT Approved', 'badge' => 'border-cyan-500/40 bg-cyan-500/10 text-cyan-200'],
        ];

        $headerStatusKey = (string) ($header->status ?? 'draft');
        $headerMeta = $statusMeta[$headerStatusKey] ?? $statusMeta['draft'];
        $endorsedDate = $header->approved_at ?? $header->generated_at;
        $isEndorsed = $headerStatusKey === 'dept_head_endorsed';

        $formatDate = static function ($value): string {
            if (empty($value)) {
                return '-';
            }
            try {
                return \Illuminate\Support\Carbon::parse($value)->format('M d, Y g:i A');
            } catch (\Throwable) {
                return (string) $value;
            }
        };
    @endphp

    <section class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-white">QAR Details</h1>
                <p class="mt-1 text-sm text-slate-400">Review annex rows and included MPOR links before final action.</p>
            </div>
            <a href="{{ route('pmt.qar', array_filter(['q' => $quarterInputValue, 'office' => $officeSearchSafe])) }}"
               class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                Back to QAR Queue
            </a>
        </div>

        <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-5">
            <div class="space-y-4 text-sm text-slate-300">
                <div class="grid gap-3 md:grid-cols-4">
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-3">
                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Office</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $header->office?->name ?? 'Office' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-3">
                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Quarter</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $header->quarter_key ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-3">
                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Status</p>
                        <span class="mt-1 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $headerMeta['badge'] }}">
                            {{ $headerMeta['label'] }}
                        </span>
                    </div>
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-3">
                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Endorsed Date</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $formatDate($endorsedDate) }}</p>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                    <h4 class="text-sm font-semibold text-white">Included MPORs</h4>
                    <div class="mt-3 max-h-56 space-y-2 overflow-y-auto pr-1">
                        @forelse (($header->mporLinks ?? collect()) as $link)
                            <div class="rounded-lg border border-gray-700 bg-slate-900/80 px-3 py-2">
                                <div class="flex flex-wrap items-center gap-2 text-sm">
                                    <span class="font-semibold text-white">{{ $link->employee_name ?: '-' }}</span>
                                    <span class="text-slate-500">-</span>
                                    <span class="text-slate-300">{{ $link->month_label ?: '-' }}</span>
                                    <span class="text-slate-500">-</span>
                                    <span class="text-xs text-slate-400">{{ $link->status_label ?: '-' }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">No linked MPOR records.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                    <h4 class="text-sm font-semibold text-white">Annex I QAR Rows</h4>
                    <div class="mt-3 max-h-72 overflow-y-auto rounded-lg border border-gray-700">
                        <table class="min-w-full divide-y divide-slate-800 text-xs">
                            <thead class="bg-slate-900/90 text-[11px] uppercase tracking-[0.2em] text-slate-400">
                                <tr>
                                    <th class="px-3 py-2 text-left">PPA Code</th>
                                    <th class="px-3 py-2 text-left">MFO/PPA</th>
                                    <th class="px-3 py-2 text-left">Indicator</th>
                                    <th class="px-3 py-2 text-left">Target/Timeline</th>
                                    <th class="px-3 py-2 text-right">Actual Performance</th>
                                    <th class="px-3 py-2 text-right">Variance</th>
                                    <th class="px-3 py-2 text-left">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 text-slate-200">
                                @forelse (($header->rows ?? collect()) as $row)
                                    <tr>
                                        <td class="px-3 py-2">{{ $row->ppa_code ?: '-' }}</td>
                                        <td class="px-3 py-2">{{ $row->mfo_title ?: '-' }}</td>
                                        <td class="px-3 py-2">{{ $row->indicator_text ?: '-' }}</td>
                                        <td class="px-3 py-2">{{ $row->target_timeline ?: '-' }}</td>
                                        <td class="px-3 py-2 text-right">{{ (int) round((float) ($row->actual_performance ?? 0)) }}</td>
                                        <td class="px-3 py-2 text-right">{{ $row->variance !== null ? (int) round((float) $row->variance) : '-' }}</td>
                                        <td class="px-3 py-2">{{ $row->remarks ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-6 text-center text-xs text-slate-400">No Annex I rows saved for this QAR.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex items-center justify-between gap-2 border-t border-gray-700 pt-5">
                <a href="{{ route('pmt.qar.previewPdf', ['qarHeader' => $header->id]) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 rounded-lg border border-slate-600 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                   <i class="fa-solid fa-file-pdf text-xs"></i>
                   <span>Preview PDF</span>
                </a>

                <div class="flex items-center gap-2">
                    @if ($isEndorsed)
                        <form method="POST" action="{{ route('pmt.qar.return', ['qarHeader' => $header->id]) }}">
                            @csrf
                            <input type="hidden" name="q" value="{{ $quarterInputValue }}">
                            <input type="hidden" name="office" value="{{ $officeSearchSafe }}">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-amber-500/40 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20">
                                Return
                            </button>
                        </form>

                        <form method="POST" action="{{ route('pmt.qar.approve', ['qarHeader' => $header->id]) }}">
                            @csrf
                            <input type="hidden" name="q" value="{{ $quarterInputValue }}">
                            <input type="hidden" name="office" value="{{ $officeSearchSafe }}">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                                Approve
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
