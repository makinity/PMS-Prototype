@extends('layouts.pmt')

@section('main-content')
@php
    $currentOpcr = $opcr ?? null;
    $currentOpcrPayload = $payload ?? [];
    $opcrStatus = strtolower((string) ($currentOpcr?->status ?? ''));
    $canCalibrate = in_array($opcrStatus, [\App\Models\Opcr::STATUS_PENDING_PMT_CALIBRATION, \App\Models\Opcr::STATUS_APPROVED_BY_PMT, \App\Models\Opcr::STATUS_ADJUSTED_BY_PMT], true);
@endphp

<section class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('pmt.office-calibration.index') }}" class="mb-2 inline-flex items-center text-sm font-semibold text-blue-400 hover:text-blue-300">
                &larr; Back to Calibration List
            </a>
            <h1 class="text-2xl font-bold text-white">Office OPCR Calibration</h1>
            <p class="text-sm text-slate-400">Review and calibrate the final OPCR rating.</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
            <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Active Period</p>
            <p class="mt-1 text-sm font-semibold text-white">{{ $currentOpcr->performancePeriod?->name ?? '—' }}</p>
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-800 px-5 py-4">
            <div>
                <h2 class="text-lg font-semibold text-white">OPCR Final Snapshot</h2>
                <p class="mt-1 text-sm text-slate-400">Consolidated snapshot of all Source UWP contents that feed the current office-period OPCR.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                @if ($currentOpcr)
                    @php
                        $opcrBadge = match ($opcrStatus) {
                            \App\Models\Opcr::STATUS_PENDING_PMT_CALIBRATION => 'border-sky-500/30 bg-sky-500/10 text-sky-300',
                            \App\Models\Opcr::STATUS_APPROVED_BY_PMT => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
                            \App\Models\Opcr::STATUS_ADJUSTED_BY_PMT => 'border-violet-500/30 bg-violet-500/10 text-violet-300',
                            \App\Models\Opcr::STATUS_RETURNED_BY_PMT => 'border-rose-500/30 bg-rose-500/10 text-rose-300',
                            default => 'border-slate-500/30 bg-slate-500/10 text-slate-300',
                        };
                    @endphp
                    <span class="rounded-full border px-3 py-1 {{ $opcrBadge }}">{{ ucwords(str_replace('_', ' ', $opcrStatus)) }}</span>
                @else
                    <span class="rounded-full border border-slate-700 bg-slate-950/70 px-3 py-1 text-slate-300">No OPCR yet</span>
                @endif
                <span class="rounded-full border border-slate-700 bg-slate-950/70 px-3 py-1 text-slate-300">{{ count($currentOpcrPayload['outputs'] ?? []) }} output{{ count($currentOpcrPayload['outputs'] ?? []) === 1 ? '' : 's' }}</span>
            </div>
        </div>

        @if ($currentOpcr)
            <div class="grid gap-3 border-b border-slate-800 px-5 py-4 sm:grid-cols-4">
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Office / Unit</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $currentOpcrPayload['opcr']['office']['name'] ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Period</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $currentOpcrPayload['opcr']['period']['name'] ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Computed Score</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $currentOpcr->final_score !== null ? number_format($currentOpcr->final_score, 2) : '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Adjusted Score</p>
                    <p class="mt-1 text-sm font-semibold text-blue-300">{{ $currentOpcr->pmt_adjusted_score !== null ? number_format($currentOpcr->pmt_adjusted_score, 2) : '—' }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-left">Source</th>
                            <th class="px-5 py-4 text-left">Output</th>
                            <th class="px-5 py-4 text-center">Indicators</th>
                            <th class="px-5 py-4 text-left">Target Summary</th>
                            <th class="px-5 py-4 text-left">Weight</th>
                            <th class="px-5 py-4 text-left">Function</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse (($currentOpcrPayload['outputs'] ?? []) as $output)
                            <tr class="hover:bg-slate-950/40">
                                <td class="px-5 py-4 align-top">
                                    <div class="font-medium text-white">{{ $output['source_supervisor'] ?? '—' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">UWP #{{ $output['source_uwp_id'] ?? '—' }}</div>
                                </td>
                                <td class="px-5 py-4 align-top text-white">{{ $output['title'] ?? '—' }}</td>
                                <td class="px-5 py-4 align-top text-center text-slate-300">{{ count($output['success_indicators'] ?? []) }}</td>
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
                                <td class="px-5 py-4 align-top text-slate-300">{{ ($output['weight_percent'] ?? '') !== '' ? $output['weight_percent'] . '%' : '—' }}</td>
                                <td class="px-5 py-4 align-top">
                                    @php $functionType = strtolower((string) ($output['function_type'] ?? '')); @endphp
                                    @if ($functionType === 'core')
                                        <span class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-300">Core</span>
                                    @elseif ($functionType === 'support')
                                        <span class="rounded-md border border-blue-400/30 bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-300">Support</span>
                                    @else
                                        <span class="rounded-md border border-slate-500/20 bg-slate-500/10 px-2 py-1 text-xs font-medium text-slate-300">{{ $functionType !== '' ? ucfirst($functionType) : 'Custom' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">This OPCR does not have consolidated outputs yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-5 py-10 text-center">
                <p class="text-sm font-medium text-white">No consolidated OPCR preview yet.</p>
            </div>
        @endif

        @if ($canCalibrate)
            <div class="border-t border-slate-800 bg-slate-950/80 px-5 py-4">
                <div class="grid gap-4 lg:grid-cols-2">
                    <!-- Adjust Rating Form -->
                    <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                        <h4 class="text-sm font-semibold text-white">Adjust Rating</h4>
                        <form method="POST" action="{{ route('pmt.office-calibration.adjust', $currentOpcr->id) }}" class="mt-3 space-y-3">
                            @csrf
                            <div>
                                <label class="text-xs text-slate-400">Adjusted Score</label>
                                <input type="number" step="0.01" min="1" max="5" name="adjusted_score" value="{{ $currentOpcr->final_score }}" 
                                    style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                    class="w-full rounded border px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400">Adjusted Rating</label>
                                <select name="adjusted_rating" 
                                    style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                    class="w-full rounded border px-3 py-2 text-sm" required>
                                    <option value="Outstanding">Outstanding</option>
                                    <option value="Very Satisfactory">Very Satisfactory</option>
                                    <option value="Satisfactory">Satisfactory</option>
                                    <option value="Unsatisfactory">Unsatisfactory</option>
                                    <option value="Poor">Poor</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400">Adjustment Reason</label>
                                <textarea name="adjustment_reason" rows="2" 
                                    style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                    class="w-full rounded border px-3 py-2 text-sm" required></textarea>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="rounded border border-blue-600 bg-blue-600/20 px-4 py-2 text-sm font-semibold text-blue-300 hover:bg-blue-600/30">Submit Adjustment</button>
                            </div>
                        </form>
                    </div>

                    <!-- Approve or Return Form -->
                    <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                        <h4 class="text-sm font-semibold text-white">Approve / Return</h4>
                        <form method="POST" action="{{ route('pmt.office-calibration.approve', $currentOpcr->id) }}" class="mt-3 space-y-3">
                            @csrf
                            <div>
                                <label class="text-xs text-slate-400">PMT Remarks</label>
                                <textarea name="remarks" id="pmtRemarksInput" rows="3" 
                                    style="background-color: #020617 !important; color: #f1f5f9 !important; border-color: #334155 !important;"
                                    class="w-full rounded border px-3 py-2 text-sm"></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-3 mt-4">
                                <button type="button" id="pmtReturnBtn" class="rounded border border-rose-600 bg-rose-600/20 px-4 py-2 text-sm font-semibold text-rose-300 hover:bg-rose-600/30">Return to Office</button>
                                <button type="submit" class="rounded border border-emerald-600 bg-emerald-600/20 px-4 py-2 text-sm font-semibold text-emerald-300 hover:bg-emerald-600/30">Approve Final Rating</button>
                            </div>
                        </form>

                        <form method="POST" id="pmtSubmissionReturnForm" action="{{ route('pmt.office-calibration.return', $currentOpcr->id) }}" class="hidden">
                            @csrf
                            <input type="hidden" name="remarks" id="pmtReturnRemarksInput">
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </section>

</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
        if (window.PMSnackbar) {
            window.PMSnackbar.show({ type: 'success', message: @json(session('success')) });
        }
    @endif
    @if(session('error'))
        if (window.PMSnackbar) {
            window.PMSnackbar.show({ type: 'error', message: @json(session('error')) });
        }
    @endif

    document.getElementById('pmtReturnBtn')?.addEventListener('click', () => {
        const remarks = document.getElementById('pmtRemarksInput')?.value || '';
        if (!remarks.trim()) {
            alert('Please provide PMT Remarks before returning.');
            return;
        }
        document.getElementById('pmtReturnRemarksInput').value = remarks;
        document.getElementById('pmtSubmissionReturnForm').submit();
    });
});
</script>
@endpush
@endsection
