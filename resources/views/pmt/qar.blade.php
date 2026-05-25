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
            'returned' => [
                'label' => 'Returned',
                'badge' => 'border-rose-500/40 bg-rose-500/10 text-rose-200',
            ],
            'pmt_approved' => [
                'label' => 'PMT Approved',
                'badge' => 'border-cyan-500/40 bg-cyan-500/10 text-cyan-200',
            ],
        ];

        $headersSafe = $headers ?? collect();
        $allowedQuarterOptionsSafe = is_array($allowedQuarterOptions ?? null) ? $allowedQuarterOptions : [];
        $selectedQuarterNumberSafe = (int) ($selectedQuarterNumber ?? 0);
        $quarterInputValue = $selectedQuarterNumberSafe > 0 ? $selectedQuarterNumberSafe : (int) request('q', 0);
        $officeSearchSafe = isset($officeSearch) ? (string) $officeSearch : (string) request('office', '');
        $periodSafe = $period ?? null;

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
        if ($periodSafe?->start_date && $periodSafe?->end_date) {
            $periodRange = \Illuminate\Support\Carbon::parse($periodSafe->start_date)->format('M d, Y')
                . ' - '
                . \Illuminate\Support\Carbon::parse($periodSafe->end_date)->format('M d, Y');
        }
    @endphp

    <div id="pmtQarPageRoot">
    <section class="space-y-6">
        <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Stage II - PMT Approval</p>
            <h1 class="mt-1 text-2xl font-bold text-white">Office Quarterly Accomplishment Report (QAR) - PMT</h1>

            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Quarter</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $quarterLabel ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Performance Period</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $periodSafe?->name ?? 'No active period' }}</p>
                    <p class="text-xs text-slate-400">{{ $periodRange }}</p>
                </div>
                <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-3">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Queue</p>
                    <p class="mt-1 text-sm font-semibold text-white">Endorsed: {{ $endorsedCount ?? 0 }} | Approved: {{ $approvedCount ?? 0 }}</p>
                </div>
            </div>

            @if (!empty($allowedQuarterOptionsSafe))
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($allowedQuarterOptionsSafe as $option)
                        @php
                            $qValue = (int) ($option['value'] ?? 0);
                            $isQuarterSelected = $qValue === $selectedQuarterNumberSafe;
                            $quarterParams = ['q' => $qValue];
                            if ($officeSearchSafe !== '') {
                                $quarterParams['office'] = $officeSearchSafe;
                            }
                        @endphp
                        <a href="{{ route('pmt.qar', $quarterParams) }}"
                            class="{{ $isQuarterSelected ? 'border-sky-500/50 bg-sky-500/10 text-sky-200' : 'border-slate-700 bg-slate-900/60 text-slate-300 hover:bg-slate-800' }} rounded-lg border px-3 py-1.5 text-xs font-semibold transition">
                            {{ $option['label'] ?? ('Q' . $qValue) }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @if (!$periodSafe)
            <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">
                No active performance period found.
            </div>
        @endif

        <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4">
            <div class="mb-4 flex flex-wrap items-end gap-3">
                <div class="min-w-[220px] flex-1">
                    <label for="qar-live-search" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Search</label>
                    <input id="qar-live-search" type="text" placeholder="Search office, quarter, status..."
                        data-live-qar-search
                        style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                        class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500">
                </div>
                <div class="w-full min-w-[180px] sm:w-auto">
                    <label for="qar-status-filter" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Status</label>
                    <select id="qar-status-filter"
                            data-live-qar-status
                            style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                            class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 [color-scheme:dark]">
                        <option value="">All</option>
                        <option value="dept_head_endorsed">Endorsed</option>
                        <option value="pmt_approved">Approved</option>
                    </select>
                </div>
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
                            <tr data-qar-row
                                data-status="{{ $statusKey }}"
                                data-search-text="{{ strtolower(($header->office?->name ?? '') . ' ' . ($header->quarter_key ?? '') . ' ' . $meta['label']) }}">
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
                                    @if ($isEndorsed || $isApproved)
                                        <a href="{{ route('pmt.qar.show', ['qarHeader' => $header->id, 'q' => $quarterInputValue, 'office' => $officeSearchSafe]) }}"
                                            class="rounded-lg border border-slate-600 bg-slate-900/70 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-800">
                                            View
                                        </a>
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
                        <tr id="qar-no-match-row" class="hidden">
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">No matching records found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @foreach ($headersSafe as $header)
        @php
            $modalStatusKey = (string) ($header->status ?? '');
            $showViewModal = in_array($modalStatusKey, ['dept_head_endorsed', 'pmt_approved'], true);
        @endphp
        @if ($showViewModal)
            @php
                $headerStatusKey = (string) ($header->status ?? 'draft');
                $headerMeta = $statusMeta[$headerStatusKey] ?? $statusMeta['draft'];
                $endorsedDate = $header->approved_at ?? $header->generated_at;
                $isEndorsedModal = $headerStatusKey === 'dept_head_endorsed';
            @endphp
            <div id="pmtQarViewModal-{{ $header->id }}" tabindex="-1" aria-hidden="true"
                class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
                <div class="relative max-h-full w-full max-w-6xl p-4">
                    <div class="relative rounded-2xl border border-gray-700 bg-slate-900 shadow-lg">
                        <div class="flex items-start justify-between border-b border-gray-700 p-5">
                            <div>
                                <h3 class="text-lg font-semibold text-white">QAR Details</h3>
                                <p class="mt-1 text-xs text-slate-400">Review annex rows and included MPOR links before final action.</p>
                            </div>
                            <button type="button" data-modal-hide="pmtQarViewModal-{{ $header->id }}"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                                <span class="sr-only">Close modal</span>
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>

                        <div class="space-y-4 p-5 text-sm text-slate-300">
                            <div class="grid gap-3 md:grid-cols-4">
                                <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-3">
                                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Office</p>
                                    <p class="mt-1 text-sm font-semibold text-white">{{ $header->office?->name ?? 'Office' }}</p>
                                </div>
                                <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-3">
                                    <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Quarter</p>
                                    <p class="mt-1 text-sm font-semibold text-white">{{ $header->quarter_key ?? ($quarterKey ?? '-') }}</p>
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
                                                    <td colspan="7" class="px-3 py-6 text-center text-xs text-slate-400">
                                                        No Annex I rows saved for this QAR.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2 border-t border-gray-700 p-5">
                            <button type="button" data-modal-hide="pmtQarViewModal-{{ $header->id }}"
                                class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                                Close
                            </button>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('pmt.qar.previewPdf', ['qarHeader' => $header->id]) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 rounded-lg border border-slate-600 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                   <i class="fa-solid fa-file-pdf text-xs"></i>
                                   <span>Preview PDF</span>
                                </a>

                                @if ((string) ($header->status ?? '') === 'dept_head_endorsed')
                                    <form id="pmtQarReturnForm-{{ $header->id }}"
                                        data-return-form
                                        data-loading-label="Returning..."
                                        method="POST"
                                        action="{{ route('pmt.qar.return', ['qarHeader' => $header->id]) }}">
                                        @csrf
                                        <input type="hidden" name="q" value="{{ $quarterInputValue }}">
                                        <input type="hidden" name="office" value="{{ $officeSearchSafe }}">
                                        <button type="submit"
                                            data-submit-button
                                            class="inline-flex items-center gap-2 rounded-lg border border-amber-500/40 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20">
                                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-amber-200/40 border-t-amber-100"></span>
                                            <span data-button-label>Return</span>
                                        </button>
                                    </form>

                                    <form id="pmtQarApproveForm-{{ $header->id }}"
                                        data-approve-form
                                        data-loading-label="Approving..."
                                        method="POST"
                                        action="{{ route('pmt.qar.approve', ['qarHeader' => $header->id]) }}">
                                        @csrf
                                        <input type="hidden" name="q" value="{{ $quarterInputValue }}">
                                        <input type="hidden" name="office" value="{{ $officeSearchSafe }}">
                                        <button type="submit"
                                            data-submit-button
                                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                                            <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                            <span data-button-label>Approve</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Live search & status filter
                const qarSearchInput = document.querySelector('[data-live-qar-search]');
                const qarStatusSelect = document.querySelector('[data-live-qar-status]');
                const qarRows = Array.from(document.querySelectorAll('[data-qar-row]'));
                const qarNoMatch = document.getElementById('qar-no-match-row');

                function applyQarFilter() {
                    const term = String(qarSearchInput?.value || '').trim().toLowerCase();
                    const status = String(qarStatusSelect?.value || '').toLowerCase();
                    let visible = 0;
                    qarRows.forEach(row => {
                        const hay = String(row.dataset.searchText || '').toLowerCase();
                        const rowStatus = String(row.dataset.status || '').toLowerCase();
                        const show = (term === '' || hay.includes(term)) && (status === '' || rowStatus === status);
                        row.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });
                    if (qarNoMatch) qarNoMatch.classList.toggle('hidden', visible > 0 || qarRows.length === 0);
                }

                qarSearchInput?.addEventListener('input', applyQarFilter);
                qarStatusSelect?.addEventListener('change', applyQarFilter);
                applyQarFilter();

                const replaceRootFromHtml = (html) => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const returnedRoot = doc.getElementById('pmtQarPageRoot');
                    const currentRoot = document.getElementById('pmtQarPageRoot');

                    if (!returnedRoot || !currentRoot) {
                        return false;
                    }

                    currentRoot.innerHTML = returnedRoot.innerHTML;
                    return true;
                };

                const closeContainingModal = (formEl) => {
                    const modalEl = formEl?.closest?.('[id^="pmtQarViewModal-"]');
                    if (modalEl) {
                        try {
                            const inst = window.FlowbiteInstances?.getInstance?.('Modal', modalEl);
                            if (inst && typeof inst.hide === 'function') {
                                inst.hide();
                            } else {
                                modalEl.classList.add('hidden');
                                modalEl.setAttribute('aria-hidden', 'true');
                            }
                        } catch (e) {
                            modalEl.classList.add('hidden');
                            modalEl.setAttribute('aria-hidden', 'true');
                        }
                    }

                    document.body.classList.remove('overflow-hidden');
                    document.querySelectorAll('[modal-backdrop]').forEach((el) => el.remove());
                };

                const bindLoadingSubmit = (form, button, loadingLabel) => {
                    if (!form || !button) {
                        return;
                    }

                    if (form.dataset.loadingBound === 'true') {
                        return;
                    }
                    form.dataset.loadingBound = 'true';

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

                const fetchAndReplace = async (url, options = {}, { pushUrl = '', closeModalForm = null } = {}) => {
                    const response = await fetch(url, options);
                    if (!response.ok) {
                        throw new Error('Request failed.');
                    }

                    const html = await response.text();
                    const updated = replaceRootFromHtml(html);
                    if (!updated) {
                        throw new Error('Unable to update view.');
                    }

                    if (pushUrl) {
                        history.pushState({}, '', pushUrl);
                    }

                    initBindings(document);

                    if (closeModalForm) {
                        closeContainingModal(closeModalForm);
                    }
                };

                const bindAjaxForms = (root = document) => {
                    root.querySelectorAll('[data-search-form], [data-approve-form], [data-return-form]').forEach((form) => {
                        if (form.dataset.bound === 'true') {
                            return;
                        }
                        form.dataset.bound = 'true';

                        form.addEventListener('submit', async (event) => {
                            if (form.dataset.submitting === 'true') {
                                event.preventDefault();
                                return;
                            }

                            event.preventDefault();
                            form.dataset.submitting = 'true';

                            const isSearch = form.matches('[data-search-form]');
                            const action = form.getAttribute('action') || location.href;
                            let fallbackUrl = action;

                            try {
                                if (isSearch) {
                                    const params = new URLSearchParams(new FormData(form));
                                    const query = params.toString();
                                    const joiner = action.includes('?') ? '&' : '?';
                                    const url = query ? `${action}${joiner}${query}` : action;
                                    fallbackUrl = url;

                                    await fetchAndReplace(
                                        url,
                                        {
                                            method: 'GET',
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'text/html',
                                            },
                                        },
                                        { pushUrl: url }
                                    );
                                } else {
                                    const token = form.querySelector('input[name="_token"]')?.value || '';
                                    await fetchAndReplace(
                                        action,
                                        {
                                            method: 'POST',
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'text/html',
                                                ...(token ? { 'X-CSRF-TOKEN': token } : {}),
                                            },
                                            body: new FormData(form),
                                            redirect: 'follow',
                                        },
                                        { pushUrl: location.href, closeModalForm: form }
                                    );
                                }
                            } catch (error) {
                                window.location.href = fallbackUrl || location.href;
                            } finally {
                                form.dataset.submitting = 'false';
                            }
                        });
                    });
                };

                const initBindings = (root = document) => {
                    if (typeof window.initFlowbite === 'function') {
                        window.initFlowbite();
                    }

                    root.querySelectorAll('[data-approve-form], [data-return-form], [data-search-form]').forEach((form) => {
                        const button = form.querySelector('[data-submit-button]');
                        const loadingLabel = form.dataset.loadingLabel || 'Processing...';
                        bindLoadingSubmit(form, button, loadingLabel);
                    });

                    bindAjaxForms(root);

                    root.querySelectorAll('[data-clear-link]').forEach((link) => {
                        if (link.dataset.bound === 'true') {
                            return;
                        }
                        link.dataset.bound = 'true';

                        link.addEventListener('click', async (event) => {
                            event.preventDefault();
                            const href = link.getAttribute('href');
                            if (!href) {
                                return;
                            }

                            try {
                                await fetchAndReplace(
                                    href,
                                    {
                                        method: 'GET',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'text/html',
                                        },
                                    },
                                    { pushUrl: href }
                                );
                            } catch (error) {
                                window.location.href = href;
                            }
                        });
                    });
                };

                if (!window.__pmtQarPopStateBound) {
                    window.__pmtQarPopStateBound = true;
                    window.addEventListener('popstate', async () => {
                        try {
                            const response = await fetch(location.href, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'text/html',
                                },
                            });

                            if (!response.ok) {
                                throw new Error('Failed to load page.');
                            }

                            const html = await response.text();
                            const updated = replaceRootFromHtml(html);
                            if (!updated) {
                                throw new Error('Unable to update view.');
                            }

                            initBindings(document);
                        } catch (error) {
                            window.location.href = location.href;
                        }
                    });
                }

                initBindings(document);
            });
        </script>
    @endpush
@endsection
