@extends('layouts.pmt')

@section('main-content')
    @php
        $statusMeta = [
            'draft' => [
                'label' => 'Draft',
                'badge' => 'border-slate-600/60 bg-slate-700/40 text-slate-200',
            ],
            'dept_head_approved' => [
                'label' => 'Dept Head Approved',
                'badge' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200',
            ],
            'pmt_validated' => [
                'label' => 'PMT Validated',
                'badge' => 'border-cyan-500/40 bg-cyan-500/10 text-cyan-200',
            ],
        ];
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

        @if (session('error'))
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Stage II - PMT Final Validation</p>
            <h1 class="mt-1 text-2xl font-bold text-white">Quarterly Accomplishment Reports (QAR)</h1>
            <p class="mt-1 text-sm text-slate-400">Review Dept Head-approved QARs and validate as final approval.</p>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-white">Pending QAR Queue</h2>
                <span class="inline-flex items-center rounded-full border border-slate-700 bg-slate-900/70 px-3 py-1 text-xs text-slate-300">
                    Pending Validation: {{ $pendingQars->count() }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead>
                        <tr class="bg-slate-950/60 text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                            <th class="px-4 py-3">Office</th>
                            <th class="px-4 py-3">Quarter</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse ($qars as $qar)
                            @php
                                $meta = $statusMeta[$qar['status']] ?? $statusMeta['draft'];
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-white">{{ $qar['office'] }}</td>
                                <td class="px-4 py-3">{{ $qar['quarter'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $meta['badge'] }}">
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button"
                                        data-open-qar-view
                                        data-qar='@json($qar)'
                                        data-modal-target="pmtQarViewModal"
                                        data-modal-toggle="pmtQarViewModal"
                                        class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-800">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">
                                    No QAR records available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="pmtQarViewModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-6xl p-4">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between gap-4 border-b border-slate-800 p-5">
                    <div>
                        <h3 class="text-lg font-semibold text-white">View QAR (Annex I) - Read-only</h3>
                        <p class="mt-1 text-sm text-slate-400">Final PMT validation gate for Stage II.</p>
                    </div>
                    <button type="button" data-modal-hide="pmtQarViewModal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <span class="sr-only">Close modal</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="max-h-[70vh] overflow-y-auto p-5">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Office</p>
                            <p id="pmtQarModalOffice" class="mt-1 text-sm font-semibold text-white">-</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Quarter</p>
                            <p id="pmtQarModalQuarter" class="mt-1 text-sm font-semibold text-white">-</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Status</p>
                            <span id="pmtQarModalStatus"
                                class="mt-1 inline-flex rounded-full border px-2 py-1 text-xs font-semibold">-</span>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Prepared/Approved by</p>
                            <p id="pmtQarModalPreparedBy" class="mt-1 text-sm font-semibold text-white">-</p>
                        </div>
                    </div>

                    <div class="mt-4 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950/50">
                        <table class="min-w-full divide-y divide-slate-800 text-sm text-slate-200">
                            <thead>
                                <tr class="bg-slate-900/70 text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                                    <th class="px-4 py-3">PPA Code</th>
                                    <th class="px-4 py-3">MFO/PPA</th>
                                    <th class="px-4 py-3">Performance Indicator</th>
                                    <th class="px-4 py-3 text-center">Target Output</th>
                                    <th class="px-4 py-3 text-center">Actual Performance</th>
                                    <th class="px-4 py-3 text-center">Variance</th>
                                    <th class="px-4 py-3">Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="pmtQarRowsBody" class="divide-y divide-slate-800"></tbody>
                        </table>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Prepared/Approved by</p>
                            <p id="pmtQarPreparedName" class="mt-2 text-sm font-semibold text-white">-</p>
                            <p class="mt-2 text-xs text-slate-500">Date</p>
                            <p id="pmtQarPreparedDate" class="text-sm text-slate-300">-</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Validated by</p>
                            <p id="pmtQarValidatedName" class="mt-2 text-sm font-semibold text-white">Pending PMT validation</p>
                            <p class="mt-2 text-xs text-slate-500">Date</p>
                            <p id="pmtQarValidatedDate" class="text-sm text-slate-300">-</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-800 p-5">
                    <button type="button" data-modal-hide="pmtQarViewModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Close
                    </button>
                    <button type="button"
                        id="pmtQarValidateBtn"
                        data-modal-target="pmtQarValidateConfirmModal"
                        data-modal-toggle="pmtQarValidateConfirmModal"
                        class="hidden rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        Validate QAR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="pmtQarValidateConfirmModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-[60] hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-lg p-4">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between border-b border-slate-800 p-5">
                    <h3 class="text-lg font-semibold text-white">Validate QAR</h3>
                    <button type="button" data-modal-hide="pmtQarValidateConfirmModal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <span class="sr-only">Close modal</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="space-y-3 p-5 text-sm text-slate-300">
                    <p>
                        Validate this QAR as final approval?
                    </p>
                    <p>
                        Once validated, this QAR becomes locked and official.
                    </p>
                    <p id="pmtQarConfirmContext" class="rounded-lg border border-slate-800 bg-slate-950/70 px-3 py-2 text-xs text-slate-400">
                        -
                    </p>
                </div>

                <form id="pmtQarValidateForm"
                    method="POST"
                    action="{{ route('pmt.qar.validate', ['qar' => 1]) }}"
                    data-action-template="{{ route('pmt.qar.validate', ['qar' => '__QAR__']) }}"
                    class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                    @csrf
                    <button type="button" data-modal-hide="pmtQarValidateConfirmModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" id="pmtQarProceedValidateBtn"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        <span data-button-spinner
                            class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        <span data-button-label>Proceed Validate</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const statusMeta = {
                    draft: {
                        label: 'Draft',
                        badge: 'border-slate-600/60 bg-slate-700/40 text-slate-200',
                    },
                    dept_head_approved: {
                        label: 'Dept Head Approved',
                        badge: 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200',
                    },
                    pmt_validated: {
                        label: 'PMT Validated',
                        badge: 'border-cyan-500/40 bg-cyan-500/10 text-cyan-200',
                    },
                };

                let selectedQar = null;

                const parseJson = (value) => {
                    try {
                        return JSON.parse(value);
                    } catch (error) {
                        return null;
                    }
                };

                const escapeHtml = (value) => String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');

                const formatDate = (value) => {
                    if (!value) {
                        return '-';
                    }

                    const date = new Date(value);
                    if (Number.isNaN(date.getTime())) {
                        return String(value);
                    }

                    return date.toLocaleString('en-US', {
                        month: 'short',
                        day: '2-digit',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                    });
                };

                const renderRows = (rows) => {
                    const body = document.getElementById('pmtQarRowsBody');
                    if (!body) {
                        return;
                    }

                    body.innerHTML = '';

                    (Array.isArray(rows) ? rows : []).forEach((row) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-4 py-3">${escapeHtml(row.ppa_code ?? '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(row.mfo ?? '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(row.indicator ?? '-')}</td>
                            <td class="px-4 py-3 text-center">${escapeHtml(row.target_output ?? '-')}</td>
                            <td class="px-4 py-3 text-center font-semibold">${escapeHtml(row.actual_performance ?? '-')}</td>
                            <td class="px-4 py-3 text-center">${escapeHtml(row.variance ?? '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(row.remarks ?? '-')}</td>
                        `;
                        body.appendChild(tr);
                    });

                    if (!body.children.length) {
                        body.innerHTML = '<tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">No QAR rows found.</td></tr>';
                    }
                };

                const hydrateViewModal = (qar) => {
                    selectedQar = qar;

                    const office = qar?.office ?? '-';
                    const quarter = qar?.quarter ?? '-';
                    const preparedBy = qar?.prepared_by ?? 'Dept Head';
                    const preparedDate = formatDate(qar?.prepared_date);
                    const validatedBy = qar?.validated_by ?? 'Pending PMT validation';
                    const validatedDate = qar?.validated_at ? formatDate(qar.validated_at) : 'Pending PMT validation';
                    const statusKey = String(qar?.status ?? 'draft');
                    const status = statusMeta[statusKey] ?? statusMeta.draft;

                    document.getElementById('pmtQarModalOffice').textContent = office;
                    document.getElementById('pmtQarModalQuarter').textContent = quarter;
                    document.getElementById('pmtQarModalPreparedBy').textContent = preparedBy;

                    const statusEl = document.getElementById('pmtQarModalStatus');
                    statusEl.textContent = status.label;
                    statusEl.className = `mt-1 inline-flex rounded-full border px-2 py-1 text-xs font-semibold ${status.badge}`;

                    document.getElementById('pmtQarPreparedName').textContent = preparedBy;
                    document.getElementById('pmtQarPreparedDate').textContent = preparedDate;
                    document.getElementById('pmtQarValidatedName').textContent = validatedBy;
                    document.getElementById('pmtQarValidatedDate').textContent = validatedDate;

                    const validateButton = document.getElementById('pmtQarValidateBtn');
                    if (statusKey === 'dept_head_approved') {
                        validateButton.classList.remove('hidden');
                    } else {
                        validateButton.classList.add('hidden');
                    }

                    renderRows(qar?.rows ?? []);
                };

                document.querySelectorAll('[data-open-qar-view]').forEach((button) => {
                    button.addEventListener('click', function() {
                        const qar = parseJson(this.getAttribute('data-qar') || 'null');
                        if (!qar) {
                            return;
                        }

                        hydrateViewModal(qar);
                    });
                });

                const validateButton = document.getElementById('pmtQarValidateBtn');
                const validateForm = document.getElementById('pmtQarValidateForm');
                const confirmContext = document.getElementById('pmtQarConfirmContext');

                validateButton?.addEventListener('click', function() {
                    if (!selectedQar) {
                        return;
                    }

                    if (String(selectedQar.status) !== 'dept_head_approved') {
                        return;
                    }

                    if (confirmContext) {
                        confirmContext.textContent = `${selectedQar.office ?? '-'} - ${selectedQar.quarter ?? '-'}`;
                    }

                    if (validateForm) {
                        const actionTemplate = validateForm.getAttribute('data-action-template') || '';
                        if (actionTemplate) {
                            validateForm.setAttribute('action', actionTemplate.replace('__QAR__', String(selectedQar.id ?? 0)));
                        }
                    }
                });

                validateForm?.addEventListener('submit', function() {
                    const submitButton = document.getElementById('pmtQarProceedValidateBtn');
                    if (!submitButton) {
                        return;
                    }

                    const spinner = submitButton.querySelector('[data-button-spinner]');
                    const label = submitButton.querySelector('[data-button-label]');

                    submitButton.disabled = true;
                    submitButton.classList.add('cursor-not-allowed', 'opacity-80');

                    if (spinner) {
                        spinner.classList.remove('hidden');
                    }

                    if (label) {
                        label.textContent = 'Validating...';
                    }
                });
            });
        </script>
    @endpush
@endsection
