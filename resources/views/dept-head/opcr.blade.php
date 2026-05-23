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
                                <td class="px-5 py-4 align-top text-center text-slate-300">
                                    <a href="{{ route('dept-head.opcr.success-indicators', ['opcr' => $currentOpcr->id, 'mfoId' => $output['mfo_id']]) }}" class="group inline-flex items-center justify-center gap-1.5 text-cyan-400 hover:text-cyan-300 transition" title="View Success Indicators">
                                        <i class="fa-regular fa-eye transition group-hover:scale-110"></i>
                                        <span class="text-xs font-semibold">{{ count($output['success_indicators'] ?? []) }}</span>
                                    </a>
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
                <div class="flex gap-3">
                    @if ($canSubmitToPmt && $opcrStatus !== \App\Models\Opcr::STATUS_ENDORSED)
                        {{-- Hidden forms submitted by the confirmation modal --}}
                        <form id="form-opcr-return" action="{{ route('dept-head.opcr.return', $currentOpcr->id) }}" method="POST">
                            @csrf
                        </form>
                        <form id="form-opcr-endorse" action="{{ route('dept-head.opcr.endorse', $currentOpcr->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="signature" id="opcr-endorse-signature">
                        </form>

                        <button type="button"
                                data-confirm-action="return"
                                class="inline-flex items-center gap-2 rounded-xl border border-rose-500/30 bg-rose-500/10 px-6 py-2.5 text-sm font-bold text-rose-300 transition hover:bg-rose-500/20">
                            <i class="fa-solid fa-rotate-left"></i> Return to Supervisors
                        </button>

                        <button type="button"
                                data-confirm-action="endorse"
                                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-500 active:scale-95">
                            <i class="fa-solid fa-check-double"></i> Endorse to PMT
                        </button>
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

{{-- Confirmation Modal --}}
<div id="opcr-confirm-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 px-4 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-950 shadow-2xl">
        <div class="px-6 pt-6 pb-2">
            <p id="opcr-confirm-eyebrow" class="text-xs font-semibold uppercase tracking-[0.22em]"></p>
            <h3 id="opcr-confirm-title" class="mt-2 text-xl font-semibold text-white"></h3>
            <p id="opcr-confirm-message" class="mt-2 text-sm text-slate-400"></p>
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-5">
            <button type="button" id="opcr-confirm-cancel"
                    class="rounded-xl border border-slate-700 px-5 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800">
                Cancel
            </button>
            <button type="button" id="opcr-confirm-proceed"
                    class="rounded-xl px-5 py-2 text-sm font-bold text-white transition">
                Confirm
            </button>
        </div>
    </div>
</div>

@include('partials.signature-pad-modal', [
    'modalId' => 'opcr-signature-modal',
    'title' => 'Endorse Consolidated OPCR',
    'message' => 'Your e-signature will be applied to the consolidated OPCR Excel document for the PMT review.',
    'confirmText' => 'Sign & Endorse'
])

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Confirmation Modal ────────────────────────────────────────────────
    const confirmModal   = document.getElementById('opcr-confirm-modal');
    const confirmTitle   = document.getElementById('opcr-confirm-title');
    const confirmEyebrow = document.getElementById('opcr-confirm-eyebrow');
    const confirmMsg     = document.getElementById('opcr-confirm-message');
    const confirmProceed = document.getElementById('opcr-confirm-proceed');
    const confirmCancel  = document.getElementById('opcr-confirm-cancel');

    const ACTIONS = {
        return: {
            eyebrow:  'Return OPCR',
            eyebrowClass: 'text-rose-400',
            title:    'Return to Supervisors?',
            message:  'This OPCR will be sent back to the supervisors for adjustment. You can re-endorse it once the changes are made.',
            btnClass: 'bg-rose-600 hover:bg-rose-500',
            formId:   'form-opcr-return',
        },
        endorse: {
            eyebrow:  'Endorse OPCR',
            eyebrowClass: 'text-emerald-400',
            title:    'Endorse to PMT?',
            message:  'This consolidated OPCR will be submitted to the PMT for final review and approval. This action cannot be undone.',
            btnClass: 'bg-emerald-600 hover:bg-emerald-500',
            formId:   'form-opcr-endorse',
        },
    };

    let pendingFormId = null;

    document.querySelectorAll('[data-confirm-action]').forEach(btn => {
        btn.addEventListener('click', () => {
            const action = btn.getAttribute('data-confirm-action');
            const meta   = ACTIONS[action];
            if (!meta) return;

            pendingFormId = meta.formId;

            confirmEyebrow.textContent  = meta.eyebrow;
            confirmEyebrow.className    = `text-xs font-semibold uppercase tracking-[0.22em] ${meta.eyebrowClass}`;
            confirmTitle.textContent    = meta.title;
            confirmMsg.textContent      = meta.message;
            confirmProceed.className    = `rounded-xl px-5 py-2 text-sm font-bold text-white transition shadow-lg ${meta.btnClass}`;

            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('flex');
        });
    });

    confirmCancel.addEventListener('click', () => {
        confirmModal.classList.add('hidden');
        confirmModal.classList.remove('flex');
        pendingFormId = null;
    });

    confirmModal.addEventListener('click', e => {
        if (e.target === confirmModal) {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');
            pendingFormId = null;
        }
    });

    confirmProceed.addEventListener('click', () => {
        if (pendingFormId) {
            document.getElementById(pendingFormId)?.submit();
        }
    });

    // ── Signature Pad logic for OPCR ──────────────────────────────────────
    const opcrSigConfirm = document.getElementById('signature-pad-confirm');
    if (opcrSigConfirm) {
        opcrSigConfirm.addEventListener('click', async function() {
            const signature = window.getSignatureData_opcr_signature_modal();
            if (!signature) {
                showSnackbar('Please provide your signature before endorsing.', true);
                return;
            }

            this.disabled = true;
            this.innerHTML = '<svg class="inline h-4 w-4 animate-spin mr-1" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="opacity-75"/></svg> Endorsing...';

            const form = document.getElementById('form-opcr-endorse');
            const formData = new FormData(form);
            const sigInput = document.getElementById('opcr-endorse-signature');
            if (sigInput) sigInput.value = signature;
            formData.set('signature', signature);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': formData.get('_token') },
                    body: formData,
                });
                if (!res.ok) throw new Error('Failed');

                // Close modal
                const modal = document.getElementById('opcr-signature-modal');
                if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }

                // Update UI
                const actionArea = document.querySelector('[data-confirm-action="endorse"]')?.closest('.flex');
                if (actionArea) {
                    actionArea.innerHTML = '<div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 px-6 py-2.5 text-sm font-bold text-emerald-300"><i class="fa-solid fa-clock mr-2"></i> Endorsed - Awaiting PMT Review</div>';
                }

                showSnackbar('OPCR endorsed to PMT.');
            } catch (e) {
                this.disabled = false;
                this.textContent = 'Confirm & Endorse';
                showSnackbar('Failed to endorse.', true);
            }
        });
    }

    function showSnackbar(msg, isError = false) {
        const el = document.createElement('div');
        el.className = `fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] rounded-lg px-5 py-3 text-sm font-semibold shadow-lg ${isError ? 'border border-rose-500/30 bg-rose-500/10 text-rose-200' : 'border border-emerald-500/30 bg-emerald-500/10 text-emerald-200'}`;
        el.innerHTML = `<i class="fa-solid ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'} mr-2"></i>${msg}`;
        document.body.appendChild(el);
        setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 3000);
    }

    document.querySelectorAll('[data-signature-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = document.getElementById('opcr-signature-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    });

    // ── UWP Preview Modal ─────────────────────────────────────────────────
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
