@extends('layouts.dept-head')

@section('main-content')
@php
    $opcrStatus = strtolower((string) ($currentOpcr?->status ?? ''));
    $canRefreshPreview = !empty($submittedSeedUwpId) && in_array($opcrStatus, ['', \App\Models\Opcr::STATUS_DRAFT, \App\Models\Opcr::STATUS_RETURNED, \App\Models\Opcr::STATUS_SUBMITTED], true);
    $canSubmitToPmt = $currentOpcr && in_array($opcrStatus, [\App\Models\Opcr::STATUS_DRAFT, \App\Models\Opcr::STATUS_RETURNED, \App\Models\Opcr::STATUS_SUBMITTED, \App\Models\Opcr::STATUS_APPROVED], true);
    
    // Stage-based logic: Only allow calibration submission if we are in the rating phase
    $hasRatings = false;
    if ($currentOpcr) {
        $hasRatings = \App\Models\Ipcr::where('opcr_id', $currentOpcr->id)->whereNotNull('final_score')->exists();
    }
    
    $sourceCount = $sourceUwps->count();
@endphp

<section class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Office Performance Commitment and Review (OPCR)</h1>
            <p class="text-sm text-slate-400">Stage I - Performance Planning and Commitment</p>
            <p class="text-xs text-slate-500">Review Source UWPs, inspect each submission, then submit the consolidated OPCR to PMT.</p>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
            <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Active Period</p>
            <p class="mt-1 text-sm font-semibold text-white">{{ $activePeriod?->name ?? '—' }}</p>
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-800 px-5 py-4">
            <div>
                <h2 class="text-lg font-semibold text-white">Source UWPs</h2>
                <p class="mt-1 text-sm text-slate-400">Submitted UWPs and already included source records for the current office-period OPCR.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="rounded-full border border-slate-700 bg-slate-950/70 px-3 py-1 text-slate-300">{{ $sourceCount }} source record{{ $sourceCount === 1 ? '' : 's' }}</span>
                @if ($currentOpcr)
                    <span class="rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-cyan-200">OPCR #{{ $currentOpcr->id }}</span>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-slate-200">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                    <tr>
                        <th class="px-5 py-4 text-left">Unit</th>
                        <th class="px-5 py-4 text-left">Supervisor</th>
                        <th class="px-5 py-4 text-center">Outputs</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse ($sourceUwps as $uwp)
                        @php
                            $payload = $sourceUwpPayloads[$uwp->id] ?? null;
                            $statusKey = strtolower(str_replace('-', '_', (string) ($uwp->status ?? '')));
                            $outputCount = $uwp->uwpFunctions->sum(fn ($function) => $function->mfos->count());
                            $statusMeta = match ($statusKey) {
                                'submitted' => ['label' => 'Submitted', 'class' => 'border-blue-500/30 bg-blue-500/10 text-blue-300'],
                                'consolidated' => ['label' => 'Included', 'class' => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300'],
                                'returned' => ['label' => 'Returned', 'class' => 'border-rose-500/30 bg-rose-500/10 text-rose-300'],
                                default => ['label' => ucwords(str_replace('_', ' ', $statusKey ?: 'unknown')), 'class' => 'border-slate-500/30 bg-slate-500/10 text-slate-300'],
                            };
                        @endphp
                        <tr class="hover:bg-slate-950/40">
                            <td class="px-5 py-4">
                                <div class="font-medium text-white">{{ $uwp->office?->name ?? '—' }}</div>
                                <div class="mt-1 text-xs text-slate-500">UWP #{{ $uwp->id }}</div>
                            </td>
                            <td class="px-5 py-4 text-slate-300">{{ $uwp->creator?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-center text-slate-300">{{ $outputCount }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button type="button"
                                        data-open-source-uwp
                                        data-uwp='@json($payload)'
                                        class="inline-flex items-center rounded-lg border border-blue-500/40 px-3 py-2 text-sm font-medium text-blue-300 transition hover:bg-blue-500/10 {{ $payload ? '' : 'opacity-60 pointer-events-none' }}"
                                        {{ $payload ? '' : 'disabled' }}>
                                    View UWP
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">No submitted or linked Source UWPs found for this office and period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-800 px-5 py-4">
            <div>
                <h2 class="text-lg font-semibold text-white">OPCR Preview</h2>
                <p class="mt-1 text-sm text-slate-400">Consolidated preview of all Source UWP contents that feed the current office-period OPCR.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                @if ($currentOpcr)
                    <a href="{{ route('stage1.opcr.export.excel', ['opcr' => $currentOpcr->id]) }}" class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 font-semibold text-emerald-300 hover:bg-emerald-500/20 transition">
                        <i class="fa-solid fa-file-excel"></i> Export Excel
                    </a>
                    @php
                        $opcrBadge = match ($opcrStatus) {
                            \App\Models\Opcr::STATUS_DRAFT => 'border-slate-500/30 bg-slate-500/10 text-slate-300',
                            \App\Models\Opcr::STATUS_SUBMITTED => 'border-amber-500/30 bg-amber-500/10 text-amber-300',
                            \App\Models\Opcr::STATUS_ENDORSED => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
                            \App\Models\Opcr::STATUS_APPROVED => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
                            \App\Models\Opcr::STATUS_RETURNED => 'border-rose-500/30 bg-rose-500/10 text-rose-300',
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
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Source UWP IDs</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $currentOpcrPayload['opcr']['source_uwp']['id'] ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Source Status</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ ucwords(str_replace('_', ' ', (string) ($currentOpcrPayload['opcr']['source_uwp']['status'] ?? '—'))) }}</p>
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
            
            <div class="border-t border-slate-800 p-6 flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-slate-400 max-w-lg">Verify the consolidated outputs above. You can endorse this OPCR to PMT for final approval, or return it to supervisors if adjustments are needed.</p>
                <div class="flex gap-3">
                    @if ($canSubmitToPmt && $opcrStatus !== \App\Models\Opcr::STATUS_ENDORSED)
                        <form action="{{ route('dept-head.opcr.return', $currentOpcr->id) }}" method="POST" onsubmit="return confirm('Return this OPCR to supervisors for adjustment?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-rose-500/30 bg-rose-500/10 px-6 py-2.5 text-sm font-bold text-rose-300 transition hover:bg-rose-500/20">
                                <i class="fa-solid fa-rotate-left"></i> Return to Supervisors
                            </button>
                        </form>
                        
                        <form action="{{ route('dept-head.opcr.endorse', $currentOpcr->id) }}" method="POST" onsubmit="return confirm('Endorse this consolidated OPCR to PMT?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-500 active:scale-95">
                                <i class="fa-solid fa-check-double"></i> Endorse to PMT
                            </button>
                        </form>
                    @elseif ($opcrStatus === \App\Models\Opcr::STATUS_ENDORSED)
                        <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 px-6 py-2.5 text-sm font-bold text-emerald-300">
                            <i class="fa-solid fa-clock mr-2"></i> Endorsed - Awaiting PMT Review
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="px-5 py-20 text-center">
                <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-slate-800 text-slate-500 mb-4">
                    <i class="fa-solid fa-building-circle-exclamation text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white">No Consolidated OPCR Found</h3>
                <p class="text-sm text-slate-400 mt-2 max-w-sm mx-auto">Please ensure at least one Unit Work Plan is submitted and included in the consolidated record.</p>
            </div>
        @endif
    </section>
</section>

<div id="uwp-preview-modal" data-modal-container class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
    <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
            <div class="min-w-0">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Unit Work Plan Preview</p>
                <h2 id="uwp-modal-title" class="mt-1 truncate text-lg font-semibold text-white">Review UWP</h2>
            </div>
            <button type="button" data-close-modal class="shrink-0 rounded-lg border border-slate-800 bg-slate-950/50 px-2.5 py-2 text-slate-400 hover:bg-slate-950 hover:text-white">&times;</button>
        </div>
        
        <div id="uwp-modal-content" class="mt-5 max-h-[70vh] overflow-auto">
            <!-- Dynamic UWP Content -->
        </div>
        
        <div class="mt-6 flex justify-end border-t border-slate-800 pt-4">
            <button type="button" data-close-modal class="rounded-lg border border-slate-700 bg-slate-900 px-6 py-2 text-sm font-semibold text-slate-300 hover:bg-slate-800">Close Preview</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uwpModal = document.getElementById('uwp-preview-modal');
    const uwpContent = document.getElementById('uwp-modal-content');
    
    document.querySelectorAll('[data-open-source-uwp]').forEach(btn => {
        btn.addEventListener('click', () => {
            const payload = JSON.parse(btn.getAttribute('data-uwp'));
            if (!payload) return;
            
            document.getElementById('uwp-modal-title').textContent = `Unit Work Plan - ${payload.uwp.office.name}`;
            
            let html = `
                <div class="grid gap-3 sm:grid-cols-2 mb-6">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Supervisor</p>
                        <p class="mt-1 text-sm font-semibold text-white">${payload.uwp.creator.name}</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Period</p>
                        <p class="mt-1 text-sm font-semibold text-white">${payload.uwp.period.name}</p>
                    </div>
                </div>
            `;
            
            payload.outputs.forEach(output => {
                html += `
                    <div class="mb-6 rounded-xl border border-slate-800 bg-slate-950/40 overflow-hidden">
                        <div class="bg-slate-900/50 px-4 py-3 border-b border-slate-800 flex justify-between items-center">
                            <h3 class="font-bold text-white text-sm">${output.title}</h3>
                            <span class="text-xs font-semibold text-slate-400">${output.weight_percent}% Weight</span>
                        </div>
                        <div class="p-4">
                            <table class="w-full text-xs text-slate-300">
                                <thead>
                                    <tr class="text-slate-500 uppercase tracking-wider">
                                        <th class="text-left pb-2">Success Indicator</th>
                                        <th class="text-left pb-2">Target</th>
                                        <th class="text-left pb-2">Assignees</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/50">
                                    ${output.success_indicators.map(si => `
                                        <tr>
                                            <td class="py-2 pr-4">${si.indicator_text}</td>
                                            <td class="py-2 pr-4 text-slate-200">${si.target_quantity || ''} ${si.target_timeline || ''}</td>
                                            <td class="py-2">${si.assignees.join(', ') || 'No assignees'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            });
            
            uwpContent.innerHTML = html;
            uwpModal.classList.remove('hidden');
            uwpModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        });
    });
    
    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('[data-modal-container]');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        });
    });
});
</script>
@endpush
@endsection
