@extends('layouts.dept-head')

@section('main-content')
    @php
        $statusMeta = [
            'draft' => [
                'label' => 'Draft',
                'badge' => 'border-violet-500/40 bg-violet-500/10 text-violet-200',
            ],
            'dept_head_approved' => [
                'label' => 'Dept Head Approved',
                'badge' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200',
            ],
        ];

        $currentStatusMeta = $statusMeta[$status] ?? $statusMeta['draft'];
        $isApproved = $status === 'dept_head_approved';
        $canGenerate = ! $isApproved;
        $canApprove = ! $isApproved && !empty($generatedAt);
        $approvedDateLabel = $isApproved && !empty($approvedAt) ? \Illuminate\Support\Carbon::parse($approvedAt)->format('M d, Y g:i A') : '—';
        $generatedDateLabel = !empty($generatedAt) ? \Illuminate\Support\Carbon::parse($generatedAt)->format('M d, Y g:i A') : '—';
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

        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Annex I - Office Quarterly Accomplishment Report</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Office Quarterly Accomplishment Report (QAR)</h1>
                <p class="mt-1 text-sm text-slate-400">
                    Derived from locked Stage II MPOR submissions (prototype: using SUBMITTED MPORs; later switch to ENDORSED-only)
                </p>
            </div>

            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                <button type="button"
                    data-modal-target="qarGenerateConfirmModal"
                    data-modal-toggle="qarGenerateConfirmModal"
                    @disabled(!$canGenerate)
                    class="rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60">
                    Generate / Refresh
                </button>
                <button type="button"
                    data-modal-target="qarApproveConfirmModal"
                    data-modal-toggle="qarApproveConfirmModal"
                    @disabled(!$canApprove)
                    class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-500/20 disabled:cursor-not-allowed disabled:opacity-60">
                    Approve QAR
                </button>
                <button type="button" disabled
                    class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-400 opacity-70">
                    Export Excel
                </button>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Office</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $office }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Quarter</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $quarter }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Status</p>
                <span class="{{ $currentStatusMeta['badge'] }} mt-1 inline-flex rounded-full border px-2 py-1 text-xs font-semibold">
                    {{ $currentStatusMeta['label'] }}
                </span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">Consolidation Summary</h2>
                    <p class="text-xs text-slate-400">Using SUBMITTED MPORs (prototype)</p>
                </div>
                <p class="text-xs text-slate-500">Last generated: {{ $generatedDateLabel }}</p>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-4">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Included MPORs</p>
                    <p class="mt-1 text-base font-semibold text-white">{{ $includedMporCount }}</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Included Employees</p>
                    <p class="mt-1 text-base font-semibold text-white">{{ $includedEmployeeCount }}</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Included Months in Quarter</p>
                    <p class="mt-1 text-base font-semibold text-white">{{ $includedMonthsCount }}/{{ $includedMonthsTotal }}</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Data Source</p>
                    <p class="mt-1 text-sm font-semibold text-white">Submitted MPOR (Locked)</p>
                </div>
            </div>

            <div class="mt-4 rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Included MPOR Records</p>
                <ul class="mt-2 space-y-1 text-sm text-slate-300">
                    @foreach ($includedMpors as $mpor)
                        <li>• {{ $mpor['employee'] }} — {{ $mpor['month'] }} — {{ $mpor['status'] }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <h2 class="text-lg font-semibold text-white">Annex I Consolidated QAR</h2>
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead>
                        <tr class="bg-slate-950/60 text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                            <th class="px-4 py-3">PPA Code</th>
                            <th class="px-4 py-3">MFO/PPA</th>
                            <th class="px-4 py-3">Performance Indicator</th>
                            <th class="px-4 py-3 text-center">Target Output</th>
                            <th class="px-4 py-3 text-center">Actual Performance</th>
                            <th class="px-4 py-3 text-center">Variance</th>
                            <th class="px-4 py-3">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @foreach ($rows as $row)
                            <tr>
                                <td class="px-4 py-3">{{ $row['ppa_code'] }}</td>
                                <td class="px-4 py-3">{{ $row['mfo'] }}</td>
                                <td class="px-4 py-3">{{ $row['indicator'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['target_output'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $row['actual_performance'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['variance'] }}</td>
                                <td class="px-4 py-3">{{ $row['remarks'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Prepared/Approved by</p>
                <p class="mt-2 text-sm font-semibold text-white">{{ $deptHeadName }}</p>
                <p class="mt-2 text-xs text-slate-500">Date:</p>
                <p class="text-sm text-slate-300">{{ $approvedDateLabel }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Validated by</p>
                <p class="mt-2 text-sm font-semibold text-white">PMT</p>
                <p class="mt-2 text-sm text-amber-200">{{ $pmtStatusLabel }}</p>
            </div>
        </div>
    </section>

    <div id="qarGenerateConfirmModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-lg p-4">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between border-b border-slate-800 p-5">
                    <h3 class="text-lg font-semibold text-white">Generate / Refresh QAR</h3>
                    <button type="button" data-modal-hide="qarGenerateConfirmModal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <span class="sr-only">Close modal</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="space-y-3 p-5 text-sm text-slate-300">
                    <p>
                        Generate/refresh QAR for Q1 2026 from available locked MPOR submissions?
                    </p>
                    <p>
                        This will consolidate quantities by MFO + Indicator.
                    </p>
                </div>
                <form id="qarGenerateForm" method="POST" action="{{ route('dept-head.qar.generate') }}"
                    class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                    @csrf
                    <button type="button" data-modal-hide="qarGenerateConfirmModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" id="qarGenerateProceedBtn"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        <span data-button-spinner
                            class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        <span data-button-label>Proceed Generate</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="qarApproveConfirmModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-lg p-4">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between border-b border-slate-800 p-5">
                    <h3 class="text-lg font-semibold text-white">Approve QAR</h3>
                    <button type="button" data-modal-hide="qarApproveConfirmModal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <span class="sr-only">Close modal</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div class="space-y-3 p-5 text-sm text-slate-300">
                    <p>
                        Approve this QAR for PMT validation?
                    </p>
                    <p>
                        Once approved, QAR becomes read-only at Dept Head level.
                    </p>
                </div>
                <form id="qarApproveForm" method="POST" action="{{ route('dept-head.qar.approve') }}"
                    class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                    @csrf
                    <button type="button" data-modal-hide="qarApproveConfirmModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" id="qarApproveProceedBtn"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        <span data-button-spinner
                            class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        <span data-button-label>Proceed Approve</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const bindLoadingSubmit = (formId, buttonId, loadingLabel) => {
                    const form = document.getElementById(formId);
                    const button = document.getElementById(buttonId);
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

                bindLoadingSubmit('qarGenerateForm', 'qarGenerateProceedBtn', 'Generating...');
                bindLoadingSubmit('qarApproveForm', 'qarApproveProceedBtn', 'Approving...');
            });
        </script>
    @endpush
@endsection
