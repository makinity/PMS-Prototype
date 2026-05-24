@extends('layouts.dept-head')

@section('main-content')
    @php
        $periodLabelValue = $periodLabel ?? 'â€”';
    @endphp

    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-white">IPCR Accomplishment Report â€” {{ $periodLabelValue }}</h1>
            </div>
            <a href="{{ $backUrl ?? route('supervisor.employee-submissions') }}"
                class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                Back
            </a>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Employee</p>
                    <p class="mt-1 font-semibold text-white">{{ $employeeName ?? 'â€”' }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Office/Unit</p>
                    <p class="mt-1 font-semibold text-white">{{ $officeName ?? 'â€”' }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Period</p>
                    <p class="mt-1 font-semibold text-white">{{ $periodLabelValue }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Source</p>
                    <p class="mt-1 font-semibold text-white">IPCR Commitments</p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse ($ipcrSections ?? [] as $sectionIndex => $section)
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="text-2xl font-bold uppercase tracking-[0.12em] text-white">{{ strtoupper((string) ($section['title'] ?? 'Section')) }}</h3>
                            @if (isset($section['weight_percent']) && $section['weight_percent'] !== null)
                                <span class="rounded-full border border-sky-500/50 bg-sky-500/10 px-3 py-1 text-sm font-semibold text-sky-200">{{ rtrim(rtrim(number_format((float) $section['weight_percent'], 2, '.', ''), '0'), '.') }}%</span>
                            @endif
                        </div>
                        <div class="overflow-x-auto rounded-xl border border-gray-700">
                            <table class="min-w-full text-left text-sm text-slate-200">
                                <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                                    <tr>
                                        <th class="px-4 py-3">Major Output</th>
                                        <th class="px-4 py-3">Success Indicators</th>
                                        <th class="px-4 py-3">Timeline</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800">
                                    @forelse (($section['rows'] ?? []) as $rowIndex => $row)
                                        <tr class="bg-slate-900/40">
                                            <td class="px-4 py-3 font-semibold text-white">{{ $row['major_output'] ?? 'â€”' }}</td>
                                            <td class="px-4 py-3">
                                                <button type="button"
                                                    data-ipcr-open-indicators
                                                    data-section-index="{{ $sectionIndex }}"
                                                    data-row-index="{{ $rowIndex }}"
                                                    class="inline-flex items-center gap-2 rounded-md border border-slate-700 bg-slate-950/40 px-3 py-1.5 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    {{ count($row['indicators'] ?? []) }}
                                                </button>
                                            </td>
                                            <td class="px-4 py-3 text-slate-300">{{ $row['timeline'] ?? $periodLabelValue }}</td>
                                        </tr>
                                    @empty
                                        <tr class="bg-slate-900/40">
                                            <td colspan="3" class="px-4 py-3 text-center text-slate-400">No outputs found for this section.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4 text-sm text-slate-400">
                        No IPCR commitments found for this period.
                    </div>
                @endforelse
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-700 pt-4">
                <a href="{{ route('ipcr.export.excel') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Export</a>
            </div>
        </div>
    </section>

    <div id="ipcr-indicators-modal" data-preview-modal class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-6xl rounded-2xl border border-gray-700 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-gray-700 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR Success Indicators</p>
                    <h3 id="ipcrIndicatorsMajorOutput" class="text-lg font-semibold text-white">Success Indicators</h3>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 space-y-4 max-h-[65vh] overflow-y-auto text-sm text-slate-200">
                <div class="overflow-hidden rounded-xl border border-gray-700 bg-slate-900/40">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-fixed text-left text-sm text-slate-200">
                            <colgroup>
                                <col class="w-[52%]"><col class="w-[88px]"><col class="w-[88px]"><col class="w-[88px]"><col class="w-[88px]"><col class="w-[96px]">
                            </colgroup>
                            <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                                <tr><th class="px-4 py-3 text-left">Indicator</th><th class="px-4 py-3 text-center tabular-nums">Q</th><th class="px-4 py-3 text-center tabular-nums">E</th><th class="px-4 py-3 text-center tabular-nums">T</th><th class="px-4 py-3 text-center tabular-nums">A</th><th class="px-4 py-3 text-center">Standards</th></tr>
                            </thead>
                            <tbody id="ipcrIndicatorsTbody" class="divide-y divide-slate-800">
                                <tr class="bg-slate-900/40"><td colspan="6" class="px-4 py-3 text-center text-slate-400">Select a major output to view indicators.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-end gap-3 border-t border-gray-700 pt-4">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">Close</button>
            </div>
        </div>
    </div>

    <div id="ipcr-standards-modal" data-preview-modal data-parent-modal-id="ipcr-indicators-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-6xl rounded-2xl border border-gray-700 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-gray-700 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                    <h3 class="text-lg font-semibold text-white">Performance Standards</h3>
                    <p id="ipcrStandardsIndicatorText" class="text-sm text-slate-400 mt-1">Select an indicator to view standards.</p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 max-h-[65vh] overflow-y-auto text-sm text-slate-200">
                <div class="overflow-x-auto rounded-xl border border-gray-700">
                    <table class="min-w-full text-left text-sm text-slate-200">
                        <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                            <tr><th class="px-4 py-3">Rating</th><th class="px-4 py-3">Quality (Q)</th><th class="px-4 py-3">Efficiency (E)</th><th class="px-4 py-3">Timeliness (T)</th></tr>
                        </thead>
                        <tbody id="ipcrStandardsTbody" class="divide-y divide-slate-800">
                            <tr class="bg-slate-900/40"><td colspan="4" class="px-4 py-3 text-center text-slate-400">No standards loaded.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-gray-700 pt-4 mt-4">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">Close</button>
            </div>
        </div>
    </div>

    <script id="ipcr-sections-json" type="application/json">{!! json_encode($ipcrSections ?? [], JSON_UNESCAPED_UNICODE) !!}</script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ipcrSectionsJsonEl = document.getElementById('ipcr-sections-json');
            const ipcrIndicatorsMajorOutput = document.getElementById('ipcrIndicatorsMajorOutput');
            const ipcrIndicatorsTbody = document.getElementById('ipcrIndicatorsTbody');
            const ipcrStandardsIndicatorText = document.getElementById('ipcrStandardsIndicatorText');
            const ipcrStandardsTbody = document.getElementById('ipcrStandardsTbody');
            const previewModals = Array.from(document.querySelectorAll('[data-preview-modal]'));
            const openPreviewStack = [];
            let selectedIndicators = [];
            let ipcrSectionsData = [];

            try {
                const parsedPayload = JSON.parse(ipcrSectionsJsonEl?.textContent || '[]');
                ipcrSectionsData = Array.isArray(parsedPayload) ? parsedPayload : [];
            } catch (_) {}

            function escapeHtml(value) {
                return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
            }
            function normalizeStandardsPayload(payload) {
                if (!payload) return {};
                if (typeof payload === 'string') {
                    try { const parsed = JSON.parse(payload); return parsed && typeof parsed === 'object' ? parsed : {}; } catch (_) { return {}; }
                }
                return typeof payload === 'object' ? payload : {};
            }
            function buildStandardsCell(values) {
                if (!Array.isArray(values) || values.length === 0) return '<span class="text-slate-400">â€”</span>';
                return `<ul class="list-disc space-y-1 pl-4 text-xs text-slate-200">${values.map((v) => `<li>${escapeHtml(v)}</li>`).join('')}</ul>`;
            }
            function formatIndicatorRating(value) {
                const numeric = Number(value);
                if (!Number.isFinite(numeric)) return '&mdash;';
                return numeric.toFixed(2);
            }
            function formatQuantity(value) {
                const numeric = Number(value);
                if (!Number.isFinite(numeric)) return '&mdash;';
                if (Number.isInteger(numeric)) return String(numeric);
                return numeric.toFixed(2).replace(/\.?0+$/, '');
            }
            function refreshPreviewModalZIndices() {
                const baseZIndex = 80;
                openPreviewStack.forEach((modalEl, index) => { modalEl.style.zIndex = String(baseZIndex + (index * 10)); });
            }
            function openPreviewModal(modalId) {
                const target = document.getElementById(modalId);
                if (!target) return;
                target.classList.remove('hidden');
                target.classList.add('flex');
                const existingIndex = openPreviewStack.indexOf(target);
                if (existingIndex !== -1) openPreviewStack.splice(existingIndex, 1);
                openPreviewStack.push(target);
                refreshPreviewModalZIndices();
                document.body.classList.add('overflow-hidden');
            }
            function closePreviewModal(modalEl) {
                if (!modalEl) return;
                modalEl.classList.add('hidden');
                modalEl.classList.remove('flex');
                modalEl.style.zIndex = '';
                const index = openPreviewStack.indexOf(modalEl);
                if (index !== -1) openPreviewStack.splice(index, 1);
                refreshPreviewModalZIndices();
                if (openPreviewStack.length === 0) document.body.classList.remove('overflow-hidden');
            }

            function renderIndicatorsModal(sectionIndex, rowIndex) {
                const section = ipcrSectionsData?.[sectionIndex];
                const row = section?.rows?.[rowIndex];
                if (!row) return;
                selectedIndicators = Array.isArray(row.indicators) ? row.indicators : [];
                if (ipcrIndicatorsMajorOutput) ipcrIndicatorsMajorOutput.textContent = `Success Indicators - ${row.major_output ?? 'Major Output'}`;
                if (ipcrIndicatorsTbody) {
                    if (selectedIndicators.length === 0) {
                        ipcrIndicatorsTbody.innerHTML = `<tr class="bg-slate-900/40"><td colspan="6" class="px-4 py-3 text-center text-slate-400">No success indicators available.</td></tr>`;
                    } else {
                        ipcrIndicatorsTbody.innerHTML = selectedIndicators.map((indicator, indicatorIndex) => `
                            <tr class="bg-slate-900/40 hover:bg-slate-900/60 transition">
                                <td class="px-4 py-4 text-slate-100 font-medium leading-snug break-words">${escapeHtml(indicator?.indicator_text ?? 'â€”')}</td>
                                <td class="px-4 py-4 text-right tabular-nums whitespace-nowrap text-slate-200">${formatQuantity(indicator?.q)}</td>
                                <td class="px-4 py-4 text-right tabular-nums whitespace-nowrap text-slate-200">${formatIndicatorRating(indicator?.e)}</td>
                                <td class="px-4 py-4 text-right tabular-nums whitespace-nowrap text-slate-200">${formatIndicatorRating(indicator?.t)}</td>
                                <td class="px-4 py-4 text-right tabular-nums whitespace-nowrap text-slate-200">${formatIndicatorRating(indicator?.a)}</td>
                                <td class="px-4 py-4 text-center">
                                    <a href="javascript:void(0)" data-ipcr-open-standards data-indicator-index="${indicatorIndex}" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-700 bg-slate-950/40 hover:bg-slate-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </a>
                                </td>
                            </tr>
                        `).join('');
                    }
                }
                openPreviewModal('ipcr-indicators-modal');
            }
            function renderStandardsModal(indicatorIndex) {
                const indicator = selectedIndicators?.[indicatorIndex];
                if (!indicator) return;
                if (ipcrStandardsIndicatorText) ipcrStandardsIndicatorText.textContent = indicator.indicator_text || 'â€”';
                const payload = normalizeStandardsPayload(indicator.standards_payload);
                const ratings = ['5', '4', '3', '2', '1'];
                if (ipcrStandardsTbody) {
                    ipcrStandardsTbody.innerHTML = ratings.map((rating) => {
                        const ratingPayload = payload?.[rating] ?? {};
                        const getCell = (key) => {
                            const val = ratingPayload?.[key] ?? ratingPayload?.[key.toLowerCase()] ?? null;
                            if (Array.isArray(val)) return buildStandardsCell(val);
                            if (typeof val === 'string' && val.trim()) return `<span class="text-xs text-slate-200">${escapeHtml(val)}</span>`;
                            return '<span class="text-slate-400">â€”</span>';
                        };
                        return `<tr class="bg-slate-900/40 align-top"><td class="px-4 py-3 font-semibold text-slate-100">${rating}</td><td class="px-4 py-3">${getCell('q')}</td><td class="px-4 py-3">${getCell('e')}</td><td class="px-4 py-3">${getCell('t')}</td></tr>`;
                    }).join('');
                }
                openPreviewModal('ipcr-standards-modal');
            }

            document.querySelectorAll('[data-ipcr-open-indicators]').forEach((button) => {
                button.addEventListener('click', () => {
                    const sectionIndex = Number.parseInt(button.dataset.sectionIndex ?? '', 10);
                    const rowIndex = Number.parseInt(button.dataset.rowIndex ?? '', 10);
                    if (!Number.isNaN(sectionIndex) && !Number.isNaN(rowIndex)) renderIndicatorsModal(sectionIndex, rowIndex);
                });
            });
            ipcrIndicatorsTbody?.addEventListener('click', (event) => {
                const targetButton = event.target.closest('[data-ipcr-open-standards]');
                if (!targetButton) return;
                const indicatorIndex = Number.parseInt(targetButton.dataset.indicatorIndex ?? '', 10);
                if (!Number.isNaN(indicatorIndex)) renderStandardsModal(indicatorIndex);
            });
            document.querySelectorAll('[data-close-modal]').forEach((btn) => btn.addEventListener('click', () => closePreviewModal(btn.closest('[data-preview-modal]'))));
            previewModals.forEach((previewModal) => previewModal.addEventListener('click', (e) => { if (e.target === previewModal) closePreviewModal(previewModal); }));
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && openPreviewStack.length > 0) closePreviewModal(openPreviewStack[openPreviewStack.length - 1]); });
        });
    </script>
@endsection
