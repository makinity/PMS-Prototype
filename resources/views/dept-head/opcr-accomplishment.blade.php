@extends('layouts.dept-head')

@section('main-content')
@php
    $canSubmitFinalRating = $currentOpcr && $opcrStatus === \App\Models\Opcr::STATUS_APPROVED && $hasRatings;
    $computedSummary = $currentOpcrPayload['computed_summary'] ?? null;
@endphp

<section class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Office Performance Commitment and Review (OPCR)</h1>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
            <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Active Period</p>
            <p class="mt-1 text-sm font-semibold text-white">{{ $activePeriod?->name ?? '—' }}</p>
        </div>
    </div>

    <div class="flex items-center justify-between gap-4 rounded-2xl border border-blue-500/20 bg-blue-500/5 px-6 py-4">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/20 text-blue-400">
                <i class="fa-solid fa-chart-line text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-white">Performance Consolidation</h3>
            </div>
        </div>
        @if ($canSubmitFinalRating)
            <form action="{{ route('dept-head.opcr.submit-calibration', $currentOpcr->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to submit the final office calibration to PMT?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-500 active:scale-95">
                    <i class="fa-solid fa-paper-plane"></i>
                    Submit Final OPCR to PMT
                </button>
            </form>
        @else
            <div class="rounded-lg border border-slate-700 bg-slate-800/50 px-4 py-2 text-xs text-slate-400">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Submit button will appear once all staff IPCRs are calibrated and OPCR is approved.
            </div>
        @endif
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70 shadow-xl">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-800 px-5 py-4">
            <div>
                <h2 class="text-lg font-semibold text-white">Office Accomplishments Preview</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                @if ($currentOpcr)
                    <a href="{{ route('dept-head.opcr.export-stage3', ['opcr' => $currentOpcr->id]) }}" class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 font-semibold text-emerald-300 hover:bg-emerald-500/20 transition">
                        <i class="fa-solid fa-file-excel"></i> Export Excel
                    </a>
                    <span class="rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-cyan-200">OPCR #{{ $currentOpcr->id }}</span>
                    @php
                        $statusBadge = match ($opcrStatus) {
                            \App\Models\Opcr::STATUS_DRAFT => 'border-slate-500/30 bg-slate-500/10 text-slate-300',
                            \App\Models\Opcr::STATUS_SUBMITTED => 'border-amber-500/30 bg-amber-500/10 text-amber-300',
                            \App\Models\Opcr::STATUS_ENDORSED => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
                            \App\Models\Opcr::STATUS_APPROVED => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
                            \App\Models\Opcr::STATUS_PENDING_PMT_CALIBRATION => 'border-sky-500/30 bg-sky-500/10 text-sky-300',
                            \App\Models\Opcr::STATUS_APPROVED_BY_PMT => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
                            \App\Models\Opcr::STATUS_ADJUSTED_BY_PMT => 'border-violet-500/30 bg-violet-500/10 text-violet-300',
                            \App\Models\Opcr::STATUS_RELEASED_BY_PMT => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300',
                            \App\Models\Opcr::STATUS_RETURNED_BY_PMT => 'border-rose-500/30 bg-rose-500/10 text-rose-300',
                            \App\Models\Opcr::STATUS_RETURNED => 'border-rose-500/30 bg-rose-500/10 text-rose-300',
                            default => 'border-slate-500/30 bg-slate-500/10 text-slate-300',
                        };
                        $statusLabel = match ($opcrStatus) {
                            \App\Models\Opcr::STATUS_PENDING_PMT_CALIBRATION => 'Pending PMT Calibration',
                            \App\Models\Opcr::STATUS_APPROVED_BY_PMT => 'Calibrated (Approved)',
                            \App\Models\Opcr::STATUS_ADJUSTED_BY_PMT => 'Calibrated (Adjusted)',
                            \App\Models\Opcr::STATUS_RELEASED_BY_PMT => 'Officially Released',
                            \App\Models\Opcr::STATUS_RETURNED_BY_PMT => 'Returned by PMT',
                            default => ucwords(str_replace('_', ' ', $opcrStatus)),
                        };
                    @endphp
                    <span class="rounded-full border px-3 py-1 {{ $statusBadge }}">{{ $statusLabel }}</span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-slate-200">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-left">Output</th>
                        <th class="px-5 py-4 text-left">Target</th>
                        <th class="px-5 py-4 text-left">Actual Accomplishment</th>
                        <th class="px-5 py-4 text-center">Q / E / T</th>
                        <th class="px-5 py-4 text-center">Rating</th>
                        <th class="px-5 py-4 text-left">Weight</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse (($currentOpcrPayload['outputs'] ?? []) as $output)
                        <tr class="hover:bg-slate-950/40 transition">
                            <td class="px-5 py-4 align-top">
                                <div class="font-medium text-white">{{ $output['title'] ?? '—' }}</div>
                                <div class="mt-1 text-xs text-slate-500">Source: {{ $output['source_supervisor'] ?? '—' }}</div>
                            </td>
                            <td class="px-5 py-4 align-top text-slate-300">
                                @php
                                    $targetQuantity = $output['target_quantity'] ?? null;
                                    $targetSummary = trim((string) ($output['target_summary'] ?? ''));
                                @endphp
                                @if ($targetSummary !== '')
                                    {{ trim(($targetQuantity !== null && $targetSummary !== 'Multiple indicator targets' ? $targetQuantity . ' ' : '') . $targetSummary) }}
                                @elseif ($targetQuantity !== null)
                                    {{ $targetQuantity }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-4 align-top">
                                <div class="font-semibold text-white">{{ number_format($output['actual_quantity'] ?? 0, 0) }}</div>
                                <div class="mt-1 text-[11px] text-slate-500">Total Units Done</div>
                            </td>
                            <td class="px-5 py-4 align-top text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="rounded bg-slate-800 px-1.5 py-0.5 text-[10px] text-slate-400" title="Quality">Q: <span class="text-slate-200">{{ number_format($output['actual_q'] ?? 0, 2) }}</span></span>
                                    <span class="rounded bg-slate-800 px-1.5 py-0.5 text-[10px] text-slate-400" title="Efficiency">E: <span class="text-slate-200">{{ number_format($output['actual_e'] ?? 0, 2) }}</span></span>
                                    <span class="rounded bg-slate-800 px-1.5 py-0.5 text-[10px] text-slate-400" title="Timeliness">T: <span class="text-slate-200">{{ number_format($output['actual_t'] ?? 0, 2) }}</span></span>
                                </div>
                            </td>
                            <td class="px-5 py-4 align-top text-center">
                                <div class="text-lg font-bold text-blue-400">{{ number_format($output['actual_avg'] ?? 0, 2) }}</div>
                                @php
                                    $ratingVal = (float)($output['actual_avg'] ?? 0);
                                    $ratingLabel = '--';
                                    $ratingClass = 'text-slate-500';
                                    if ($ratingVal >= 4.5) { $ratingLabel = 'Outstanding'; $ratingClass = 'text-emerald-400'; }
                                    elseif ($ratingVal >= 3.5) { $ratingLabel = 'Very Satisfactory'; $ratingClass = 'text-sky-400'; }
                                    elseif ($ratingVal >= 2.5) { $ratingLabel = 'Satisfactory'; $ratingClass = 'text-amber-400'; }
                                    elseif ($ratingVal >= 1.5) { $ratingLabel = 'Unsatisfactory'; $ratingClass = 'text-orange-400'; }
                                    elseif ($ratingVal > 0) { $ratingLabel = 'Poor'; $ratingClass = 'text-rose-400'; }
                                @endphp
                                <div class="text-[10px] uppercase font-bold {{ $ratingClass }}">{{ $ratingLabel }}</div>
                            </td>
                            <td class="px-5 py-4 align-top text-slate-300">{{ ($output['weight_percent'] ?? '') !== '' ? $output['weight_percent'] . '%' : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">No consolidated accomplishments found. Ensure staff IPCRs are approved.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($currentOpcr && $computedSummary && $computedSummary['is_ready'])
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400">Office Overall Rating</h3>
            <div class="mt-4 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-slate-300">Core Weighted Score</span>
                    <span class="font-semibold text-white">{{ number_format((float) $computedSummary['core_weighted'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-slate-300">Support Weighted Score</span>
                    <span class="font-semibold text-white">{{ number_format((float) $computedSummary['support_weighted'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-slate-300">Computed Overall Rating</span>
                    <span class="font-semibold text-blue-300">{{ number_format((float) $computedSummary['overall_score'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-slate-300">Computed Adjectival Rating</span>
                    <span class="font-semibold text-white">{{ $computedSummary['adjectival_rating'] }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-slate-300">Status</span>
                    @if ($computedSummary['is_provisional'])
                        <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-300">Provisional</span>
                    @else
                        <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Complete</span>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-300">Total Staff Calibrated</span>
                    @php
                        $officeIpcrs = \App\Models\Ipcr::where('opcr_id', $currentOpcr->id)
                            ->whereIn('status', [\App\Models\Ipcr::STATUS_APPROVED_BY_PMT, \App\Models\Ipcr::STATUS_ADJUSTED_BY_PMT, \App\Models\Ipcr::STATUS_RELEASED_BY_PMT])
                            ->get();
                        $calibratedCount = $officeIpcrs->filter(fn($i) => ($i->pmt_adjusted_score ?? $i->final_score) !== null)->count();
                        $totalStaff = \App\Models\Ipcr::where('opcr_id', $currentOpcr->id)->count();
                    @endphp
                    <span class="font-semibold text-white">{{ $calibratedCount }} / {{ $totalStaff }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-400">Calibration Status</h3>
            <div class="mt-4 flex items-center gap-4">
                @if ($currentOpcr->pmt_adjusted_score)
                    <div class="h-4 w-4 rounded-full bg-violet-500 animate-pulse"></div>
                    <div>
                        <p class="text-sm font-bold text-white text-violet-300">CALIBRATED BY PMT</p>
                        <p class="text-xs text-slate-400">Final adjusted score: {{ $currentOpcr->pmt_adjusted_score }}</p>
                    </div>
                @else
                    <div class="h-4 w-4 rounded-full bg-amber-500"></div>
                    <div>
                        <p class="text-sm font-bold text-white">AWAITING FINAL REVIEW</p>
                        <p class="text-xs text-slate-400">Computed office rating is ready for PMT review once submitted.</p>
                    </div>
                @endif
            </div>
            @if ($computedSummary['is_provisional'])
                <p class="mt-4 text-xs text-amber-300">This office rating is provisional because either the core or support bucket has no rated OPCR rows yet.</p>
            @endif
        </div>
    </div>
    @endif
</section>
@endsection
