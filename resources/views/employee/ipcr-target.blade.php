@extends('layouts.employee')

@section('main-content')
    @php
        $ipcr = $ipcr ?? null;
        $ipcrPayload = $ipcrPayload ?? ['status' => null, 'core' => [], 'support' => []];
    @endphp
    <div class="space-y-6">

        <!-- PAGE HEADER -->
        <div class="flex justify-between items-start gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Individual Performance Commitment and Review (IPCR)
                </h1>
                <p class="text-sm text-gray-400 mt-1">
                    Stage I – Performance Planning and Commitment
                </p>
                <p class="text-[11px] text-gray-500 mt-2">Read-only | No edits or validation by employee.</p>
            </div>

            <span id="ipcr-status-badge" class="px-3 py-1 text-xs font-medium rounded bg-blue-900 text-blue-300 border border-blue-800">
                FOR COMMITMENT
            </span>
        </div>

        <!-- STATUS / CONTEXT -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">Status & Context</h2>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('stage1.ipcr.export.excel') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">
                        Export IPCR (Excel)
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 text-sm">
                <div class="bg-gray-700 rounded-lg p-3">
                    <p class="text-gray-400 mb-1">Status</p>
                    <p id="ipcr-status-text" class="font-medium text-white">For Commitment</p>
                </div>
                <div class="bg-gray-700 rounded-lg p-3">
                    <p class="text-gray-400 mb-1">Basis</p>
                    <p class="font-medium text-white">Approved UWP (PMT-approved) and OPCR (Department Head–approved)</p>
                </div>
            </div>
        </div>

        @if (!$ipcr)
            <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 p-5">
                <h2 class="text-base font-semibold text-amber-200">No generated IPCR found for this period.</h2>
                <p class="mt-2 text-sm text-amber-100/90">
                    Please wait for PMT final approval of OPCR so the system can generate your IPCR.
                </p>
            </div>
        @endif

        <!-- EMPLOYEE INFORMATION -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5">
            <h2 class="font-semibold text-lg text-white mb-4">Employee Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Employee Name</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="{{ ($employeeName ?? '') ?: 'Ramon Reyes' }}" disabled>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Position</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="{{ ($employeePosition ?? '') ?: 'Records Management Officer' }}" disabled>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Office / Unit</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="{{ ($officeName ?? '') ?: 'Revenue Collection Unit' }}" disabled>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Rating Period</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="{{ ($periodName ?? '') ?: 'January - June 2026' }}" disabled>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Immediate Supervisor</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="{{ ($supervisorName ?? '') ?: 'Carlo D. Beray' }}" disabled>
                </div>
            </div>
        </div>

        <!-- CORE FUNCTIONS -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="font-semibold text-lg text-white">
                        {{ $functionTypeLabels['core'] ?? 'Core Functions' }}
                        <span class="text-sm text-gray-400">
                            ({{ $functionHeaderMeta['core_percent'] ?? 80 }}%)
                        </span>
                    </h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Major Output</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Success Indicators</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Target Summary</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeline</th>
                        </tr>
                    </thead>
                    <tbody id="ipcr-core-tbody"></tbody>
                </table>
            </div>
        </div>

        <!-- SUPPORT FUNCTIONS -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5 space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="font-semibold text-lg text-white">
                        {{ $functionTypeLabels['support'] ?? 'Support Functions' }}
                        <span class="text-sm text-gray-400">
                            ({{ $functionHeaderMeta['support_percent'] ?? 20 }}%)
                        </span>
                    </h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-700 text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Major Output</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Success Indicators</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Target Summary</th>
                            <th class="border border-gray-700 px-4 py-3 text-left font-medium text-white">Timeline</th>
                        </tr>
                    </thead>
                    <tbody id="ipcr-support-tbody"></tbody>
                </table>
            </div>
        </div>

        <!-- COMMITMENT SECTION -->
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-5">
            <h2 class="font-semibold text-lg text-white mb-4">Employee Commitment</h2>
            <div class="bg-gray-700/50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-300 italic">
                    I acknowledge and commit to the above performance targets derived from the
                    approved Unit Work Plan (UWP) and OPCR. I understand that these targets will
                    serve as the basis for performance monitoring and evaluation.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Employee Name</label>
                    <input type="text"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5"
                           value="{{ ($employeeName ?? '') ?: 'Ramon Reyes' }}" disabled>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-white">Date<small>(Date will be recorded upon commitment)</small></label>
                    <input type="date"
                           class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg block w-full p-2.5 opacity-80 cursor-not-allowed"
                           value="" disabled>
                </div>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="flex flex-col gap-2 items-end">
            <div class="flex justify-end gap-3 w-full">

                <button type="button"
                        id="commit-targets-btn"
                        data-employee-loading="true"
                        data-loading-text="Committing..."
                        @disabled(!$ipcr)
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-medium rounded-lg focus:ring-4 focus:ring-blue-800 transition-colors duration-200 {{ $ipcr ? 'bg-blue-600 hover:bg-blue-700' : 'bg-blue-600 opacity-60 cursor-not-allowed' }}">
                    <span data-button-label>Commit Targets</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>

    </div>

    <!-- SUCCESS INDICATORS MODAL -->
    <div id="ipcr-indicators-modal" data-modal-container role="dialog" aria-modal="true"
         class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Individual Performance Commitment and Review</p>
                    <h2 id="ipcr-indicators-title" class="mt-1 text-lg font-semibold text-white truncate">
                        Success Indicators
                    </h2>
                </div>
                <button type="button" data-close-modal
                        class="shrink-0 rounded-lg border border-slate-800 bg-slate-950/50 px-2.5 py-2 text-slate-400 hover:text-white hover:bg-slate-950">
                    ✕
                </button>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Office / Unit</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ ($officeName ?? '') ?: 'Revenue Collection Unit' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Period</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ ($periodName ?? '') ?: 'January - June 2026' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Employee</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ ($employeeName ?? '') ?: 'Ramon Reyes' }}</p>
                </div>
            </div>

            <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/40 overflow-hidden">
                <div class="max-h-[46vh] overflow-auto">
                    <table class="w-full text-sm text-slate-200">
                        <thead class="sticky top-0 z-10 bg-slate-950 text-slate-300 text-xs uppercase">
                            <tr class="border-b border-slate-800">
                                <th class="px-4 py-3 text-left w-[70%]">Indicator</th>
                                <th class="px-4 py-3 text-left w-[30%]">Standards (Q/E/T)</th>
                            </tr>
                        </thead>
                        <tbody id="ipcr-indicators-body" class="divide-y divide-slate-800">
                            <!-- injected -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-slate-800 pt-4">
                <button type="button" data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- STANDARDS MODAL -->
    <div id="ipcr-standards-modal" data-modal-container role="dialog" aria-modal="true"
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Standards</p>
                    <h2 id="ipcr-standards-title" class="mt-1 text-lg font-semibold text-white truncate">Standards (Q/E/T)</h2>
                    <p class="text-sm text-slate-400">Read-only | Encoded by Supervisor during UWP Draft; locked after submission</p>
                </div>
                <button type="button" data-close-modal
                        class="shrink-0 rounded-lg border border-slate-800 bg-slate-950/50 px-2.5 py-2 text-slate-400 hover:text-white hover:bg-slate-950">
                    ✕
                </button>
            </div>

            <div class="mt-5 rounded-xl border border-slate-800 bg-slate-950/40 overflow-hidden">
                <div class="max-h-[56vh] overflow-auto">
                    <table class="w-full text-sm text-slate-200">
                        <thead class="sticky top-0 z-10 bg-slate-950 text-slate-300 text-xs uppercase">
                            <tr class="border-b border-slate-800">
                                <th class="px-4 py-3 text-left w-[8%]">Rating</th>
                                <th class="px-4 py-3 text-left w-[42%]">Quality (Q)</th>
                                <th class="px-4 py-3 text-left w-[25%]">Efficiency (E)</th>
                                <th class="px-4 py-3 text-left w-[25%]">Timeliness (T)</th>
                            </tr>
                        </thead>
                        <tbody id="ipcr-standards-body" class="divide-y divide-slate-800">
                            <!-- injected -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-slate-800 pt-4">
                <button type="button" data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ipcr = @json($ipcr);
        const payload = @json($ipcrPayload);

        // --------- MODAL HELPERS (STACKED) ----------
        function getOpenModals() {
            return Array.from(document.querySelectorAll('[data-modal-container]'))
                .filter(m => !m.classList.contains('hidden'))
                .sort((a, b) => (Number(a.style.zIndex || 0) - Number(b.style.zIndex || 0)));
        }

        function openModal(modal) {
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal(modal) {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            const anyOpen = document.querySelector('[data-modal-container]:not(.hidden)');
            if (!anyOpen) {
                document.body.classList.remove('overflow-hidden');
            }
        }

        function closeTopMostModal() {
            const open = getOpenModals();
            if (!open.length) return;
            closeModal(open[open.length - 1]);
        }

        // Close buttons
        document.querySelectorAll('[data-close-modal]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('[data-modal-container]');
                closeModal(modal);
            });
        });

        // Backdrop click close
        document.querySelectorAll('[data-modal-container]').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal);
            });
        });

        // ESC close top-most
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const open = getOpenModals();
                if (open.length) {
                    closeTopMostModal();
                }
            }
        });

        // --------- INDICATORS MODAL BUILD ----------
        const indicatorsModal = document.getElementById('ipcr-indicators-modal');
        const indicatorsTitle = document.getElementById('ipcr-indicators-title');
        const indicatorsBody = document.getElementById('ipcr-indicators-body');

        const standardsModal = document.getElementById('ipcr-standards-modal');
        const standardsTitle = document.getElementById('ipcr-standards-title');
        const standardsBody = document.getElementById('ipcr-standards-body');

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderList(items) {
            const arr = Array.isArray(items) ? items.filter(Boolean) : [];
            if (!arr.length) return '&mdash;';
            return '<ul class="list-disc space-y-1 pl-5">' + arr.map(v => '<li>' + escapeHtml(v) + '</li>').join('') + '</ul>';
        }

        function findOutputByKey(key) {
            const all = [...(payload.core || []), ...(payload.support || [])];
            return all.find(x => String(x.key) === String(key));
        }

        function buildIndicatorsRows(output) {
            const indicators = Array.isArray(output?.indicators) ? output.indicators : [];
            if (!indicators.length) {
                return `<tr><td class="px-4 py-3 text-slate-300" colspan="2">No indicators available.</td></tr>`;
            }

            return indicators.map((indicator, idx) => {
                const text = indicator?.indicator_text || '--';
                return `
                    <tr class="hover:bg-slate-900/40">
                        <td class="px-4 py-3 align-top">
                            <p class="text-white">${escapeHtml(text)}</p>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <button type="button"
                                    data-open-standards-index="${idx}"
                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-950/40 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 14l3-3 3 3 5-6" />
                                </svg>
                                View Standards
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function openIndicatorsModal(outputKey) {
            const output = findOutputByKey(outputKey);
            if (!output) return;

            indicatorsTitle.textContent = `Success Indicators - ${output.output_title || ''}`;
            indicatorsBody.innerHTML = buildIndicatorsRows(output);

            indicatorsBody.querySelectorAll('[data-open-standards-index]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const idx = Number(btn.getAttribute('data-open-standards-index') || '0');
                    const indicator = (output.indicators || [])[idx];
                    openStandardsModal(output.output_title || 'Standards', indicator);
                });
            });

            openModal(indicatorsModal);
        }

        function buildStandardsRows(indicator) {
            const standards = indicator?.standards_by_rating || {};
            const ratings = [5, 4, 3, 2, 1];

            return ratings.map((r) => {
                const row = standards[String(r)] || {};
                const q = row.Q || [];
                const e = row.E || [];
                const t = row.T || [];

                return `
                    <tr class="hover:bg-slate-900/40">
                        <td class="px-4 py-3 text-white font-semibold">${r}</td>
                        <td class="px-4 py-3 text-slate-200">${renderList(q)}</td>
                        <td class="px-4 py-3 text-slate-200">${renderList(e)}</td>
                        <td class="px-4 py-3 text-slate-200">${renderList(t)}</td>
                    </tr>
                `;
            }).join('');
        }

        function openStandardsModal(outputTitle, indicator) {
            const indicatorText = indicator?.indicator_text || '';
            standardsTitle.textContent = `Standards (Q/E/T) - ${indicatorText}`;
            standardsBody.innerHTML = buildStandardsRows(indicator);
            openModal(standardsModal);
        }

        function renderTableRows(rows, tbodyId) {
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;
            tbody.innerHTML = '';

            if (!Array.isArray(rows) || !rows.length) {
                tbody.innerHTML = `<tr><td colspan="4" class="border border-gray-700 px-4 py-6 text-center text-gray-400">No IPCR targets found.</td></tr>`;
                return;
            }

            rows.forEach((row) => {
                const indicatorCount = Array.isArray(row.indicators) ? row.indicators.length : 0;

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-750';
                tr.innerHTML = `
                    <td class="border border-gray-700 px-4 py-3 text-gray-300">${escapeHtml(row.output_title || '--')}</td>
                    <td class="border border-gray-700 px-4 py-3 text-gray-300">
                        <button type="button"
                                data-open-indicators
                                data-mfo="${escapeHtml(row.key)}"
                                class="inline-flex items-center gap-2 text-blue-300 hover:text-blue-200">
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span>View (${indicatorCount})</span>
                        </button>
                    </td>
                    <td class="border border-gray-700 px-4 py-3 text-gray-300">${escapeHtml(row.target_summary || '--')}</td>
                    <td class="border border-gray-700 px-4 py-3 text-gray-300">${escapeHtml(row.timeline || '--')}</td>
                `;
                tbody.appendChild(tr);
            });

            tbody.querySelectorAll('[data-open-indicators]').forEach(btn => {
                btn.addEventListener('click', () => openIndicatorsModal(btn.dataset.mfo));
            });
        }

        renderTableRows(payload.core || [], 'ipcr-core-tbody');
        renderTableRows(payload.support || [], 'ipcr-support-tbody');

        function setButtonLoading(button, isLoading, loadingText) {
            if (!button) return;
            const label = button.querySelector('[data-button-label]');
            const spinner = button.querySelector('[data-button-spinner]');
            if (label && !button.dataset.originalLabel) {
                button.dataset.originalLabel = label.textContent.trim();
            }

            if (isLoading) {
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-wait');
                if (spinner) spinner.classList.remove('hidden');
                if (label && loadingText) label.textContent = loadingText;
            } else {
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-wait');
                if (spinner) spinner.classList.add('hidden');
                if (label && button.dataset.originalLabel) label.textContent = button.dataset.originalLabel;
            }
        }

        // --------- COMMIT BUTTON DEMO (status updates + disable after commit) ----------
        const statusBadge = document.getElementById('ipcr-status-badge');
        const statusText = document.getElementById('ipcr-status-text');

        function applyStatusUi(statusValue) {
            const statusKey = String(statusValue || '').toLowerCase();

            if (statusKey === 'committed') {
                if (statusBadge) {
                    statusBadge.textContent = 'COMMITTED';
                    statusBadge.className = 'px-3 py-1 text-xs font-medium rounded bg-emerald-900 text-emerald-300 border border-emerald-800';
                }
                if (statusText) {
                    statusText.textContent = 'Committed';
                }
                return;
            }

            if (statusBadge) {
                statusBadge.textContent = 'FOR COMMITMENT';
                statusBadge.className = 'px-3 py-1 text-xs font-medium rounded bg-blue-900 text-blue-300 border border-blue-800';
            }
            if (statusText) {
                statusText.textContent = 'For Commitment';
            }
        }

        applyStatusUi(ipcr?.status || payload.status);

        document.querySelectorAll('[data-employee-loading="true"]').forEach((button) => {
            button.addEventListener('click', function () {
                if (button.dataset.loadingActive === 'true') return;

                if (button.id === 'commit-targets-btn' && button.dataset.committed === 'true') return;

                button.dataset.loadingActive = 'true';
                setButtonLoading(button, true, button.dataset.loadingText || 'Loading...');

                const duration = Number.parseInt(button.dataset.loadingDuration || '1200', 10);
                const delay = Number.isNaN(duration) ? 1200 : duration;

                setTimeout(() => {
                    setButtonLoading(button, false);
                    button.dataset.loadingActive = 'false';

                    if (button.id === 'commit-targets-btn') {
                        button.dataset.committed = 'true';
                        button.disabled = true;
                        button.classList.add('opacity-70', 'cursor-not-allowed');
                        applyStatusUi('committed');
                    }
                }, delay);
            });
        });
    });
    </script>
    @endpush
@endsection
