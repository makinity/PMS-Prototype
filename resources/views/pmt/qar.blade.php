@extends('layouts.pmt')

@section('main-content')
    @php
        $statusMeta = [
            'draft' => [
                'label' => 'Draft',
                'badge' => 'border-slate-600/60 bg-slate-700/40 text-slate-200',
            ],
            'dept_head_endorsed' => [
                'label' => 'Dept Head Endorsed',
                'badge' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200',
            ],
            'pmt_approved' => [
                'label' => 'PMT Approved',
                'badge' => 'border-cyan-500/40 bg-cyan-500/10 text-cyan-200',
            ],
        ];

        $headersSafe = $headers ?? collect();

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

        $periodRange = '-';
        if ($period?->start_date && $period?->end_date) {
            $periodRange = \Illuminate\Support\Carbon::parse($period->start_date)->format('M d, Y')
                . ' - '
                . \Illuminate\Support\Carbon::parse($period->end_date)->format('M d, Y');
        }
    @endphp

    <section class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="rounded-xl border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-200">
                {{ session('info') }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Stage II - PMT Approval</p>
            <h1 class="mt-1 text-2xl font-bold text-white">Office Quarterly Accomplishment Report (QAR) - PMT</h1>
            <p class="mt-1 text-sm text-slate-400">Approve endorsed QAR records to open the employee IPCR/SMPOR accomplishment gate.</p>

            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Quarter</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $quarterLabel ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Performance Period</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $period?->name ?? 'No active period' }}</p>
                    <p class="text-xs text-slate-400">{{ $periodRange }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Queue</p>
                    <p class="mt-1 text-sm font-semibold text-white">Endorsed: {{ $endorsedCount ?? 0 }} | Approved: {{ $approvedCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        @if (!$period)
            <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">
                No active performance period found.
            </div>
        @endif

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-white">QAR Approval Queue</h2>
                <span class="inline-flex items-center rounded-full border border-slate-700 bg-slate-900/70 px-3 py-1 text-xs text-slate-300">
                    Records: {{ $headersSafe->count() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead>
                        <tr class="bg-slate-950/60 text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                            <th class="px-4 py-3">Office</th>
                            <th class="px-4 py-3">Quarter</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Endorsed Date</th>
                            <th class="px-4 py-3">PMT Validated</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse ($headersSafe as $header)
                            @php
                                $statusKey = (string) ($header->status ?? 'draft');
                                $meta = $statusMeta[$statusKey] ?? $statusMeta['draft'];
                                $isEndorsed = $statusKey === 'dept_head_endorsed';
                                $isApproved = $statusKey === 'pmt_approved';
                                $endorsedDate = $header->approved_at ?? $header->generated_at;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-semibold text-white">{{ $header->office?->name ?? 'Office' }}</td>
                                <td class="px-4 py-3">{{ $header->quarter_key ?? ($quarterKey ?? '-') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $meta['badge'] }}">
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ $formatDate($endorsedDate) }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $formatDate($header->pmt_validated_at) }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if ($isEndorsed)
                                        <button type="button"
                                            data-modal-target="pmtQarApproveModal-{{ $header->id }}"
                                            data-modal-toggle="pmtQarApproveModal-{{ $header->id }}"
                                            class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-500">
                                            Approve QAR
                                        </button>
                                    @elseif ($isApproved)
                                        <button type="button" disabled
                                            class="cursor-not-allowed rounded-lg border border-cyan-500/40 bg-cyan-500/10 px-3 py-1.5 text-xs font-semibold text-cyan-200 opacity-70">
                                            Approved
                                        </button>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">
                                    No endorsed/approved QAR records found for this quarter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @foreach ($headersSafe as $header)
        @if ((string) ($header->status ?? '') === 'dept_head_endorsed')
            <div id="pmtQarApproveModal-{{ $header->id }}" tabindex="-1" aria-hidden="true"
                class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
                <div class="relative max-h-full w-full max-w-lg p-4">
                    <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                        <div class="flex items-start justify-between border-b border-slate-800 p-5">
                            <h3 class="text-lg font-semibold text-white">Approve this QAR?</h3>
                            <button type="button" data-modal-hide="pmtQarApproveModal-{{ $header->id }}"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                                <span class="sr-only">Close modal</span>
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>

                        <div class="space-y-3 p-5 text-sm text-slate-300">
                            <p>
                                Office: <span class="font-semibold text-white">{{ $header->office?->name ?? 'Office' }}</span>
                                <span class="mx-1 text-slate-500">-</span>
                                Quarter: <span class="font-semibold text-white">{{ $header->quarter_key ?? ($quarterKey ?? '-') }}</span>
                            </p>
                            <p>
                                Once approved, QAR becomes read-only and employees may proceed to IPCR/SMPOR accomplishments.
                            </p>
                        </div>

                        <form id="pmtQarApproveForm-{{ $header->id }}"
                            data-approve-form
                            method="POST"
                            action="{{ route('pmt.qar.approve', ['qarHeader' => $header->id]) }}"
                            class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                            @csrf
                            <button type="button" data-modal-hide="pmtQarApproveModal-{{ $header->id }}"
                                class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                                Cancel
                            </button>
                            <button type="submit"
                                id="pmtQarApproveBtn-{{ $header->id }}"
                                data-submit-button
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                                <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                <span data-button-label>Approve</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const bindLoadingSubmit = (form, button, loadingLabel) => {
                    if (!form || !button) {
                        return;
                    }

                    const spinner = button.querySelector('[data-button-spinner]');
                    const label = button.querySelector('[data-button-label]');

                    form.addEventListener('submit', function() {
                        button.disabled = true;
                        button.classList.add('cursor-not-allowed', 'opacity-80');

                        if (spinner) {
                            spinner.classList.remove('hidden');
                        }

                        if (label) {
                            label.textContent = loadingLabel;
                        }
                    });
                };

                document.querySelectorAll('[data-approve-form]').forEach((form) => {
                    const button = form.querySelector('[data-submit-button]');
                    bindLoadingSubmit(form, button, 'Approving...');
                });
            });
        </script>
    @endpush
@endsection