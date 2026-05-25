@extends('layouts.dept-head')

@section('main-content')
    @php
        $periodLabelValue = $periodLabel ?? '—';
        $smporMonthLabels = !empty($smporMonths ?? []) && is_array($smporMonths)
            ? array_values($smporMonths)
            : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];

        $smporSectionList = is_array($smporSections ?? null) ? $smporSections : [];
        $formatSmporValue = static function ($value): string {
            $numeric = (float) ($value ?? 0);
            return fmod($numeric, 1.0) === 0.0
                ? (string) (int) $numeric
                : rtrim(rtrim(number_format($numeric, 2, '.', ''), '0'), '.');
        };
    @endphp

    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-white">SMPOR Preview — {{ $periodLabelValue }}</h1>
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
                    <p class="mt-1 font-semibold text-white">{{ $employeeName ?? '—' }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Office/Unit</p>
                    <p class="mt-1 font-semibold text-white">{{ $officeName ?? '—' }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Period</p>
                    <p class="mt-1 font-semibold text-white">{{ $periodLabelValue }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Source</p>
                    <p class="mt-1 font-semibold text-white">{{ $smporSourceLabel ?? 'Submitted MPORs' }}</p>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h4 class="text-base font-semibold text-white">Monitoring Totals</h4>
                    <span class="text-xs text-slate-400">Quality Points = Quantity Ã— Quality Rating Â· Timeliness Points = Quantity Ã— Timeliness Rating</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" data-smpor-tab="quantity" class="rounded-lg border border-sky-500/40 bg-sky-500/20 px-3 py-1.5 text-xs font-semibold text-sky-200 transition">Efficiency/Quantity</button>
                    <button type="button" data-smpor-tab="quality" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-300 transition hover:bg-slate-800">Quality/Effectiveness</button>
                    <button type="button" data-smpor-tab="timeliness" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-300 transition hover:bg-slate-800">Timeliness</button>
                </div>

                @foreach (['quantity', 'quality', 'timeliness'] as $panel)
                    <div data-smpor-tab-panel="{{ $panel }}" class="{{ $panel === 'quantity' ? '' : 'hidden' }} overflow-x-auto rounded-xl border border-gray-700">
                        <table class="min-w-full text-left text-sm text-slate-200">
                            <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-4 py-3">Expected Outputs</th>
                                    @foreach ($smporMonthLabels as $monthLabel)
                                        <th class="px-4 py-3 text-right">{{ $monthLabel }}</th>
                                    @endforeach
                                    <th class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($smporSectionList as $section)
                                    @php
                                        $sectionRows = is_array($section['rows'] ?? null) ? $section['rows'] : [];
                                        $sectionTitle = trim((string) ($section['title'] ?? 'Section')) ?: 'Section';
                                        $key = $panel === 'quantity' ? 'quantity' : ($panel === 'quality' ? 'quality' : 'timeliness');
                                    @endphp
                                    <tr class="bg-slate-950/60">
                                        <td colspan="{{ count($smporMonthLabels) + 2 }}" class="px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] text-slate-100">{{ $sectionTitle }}</td>
                                    </tr>
                                    @forelse ($sectionRows as $row)
                                        <tr class="bg-slate-900/40">
                                            <td class="px-4 py-3 font-semibold">{{ $row['expected_output'] ?? '—' }}</td>
                                            @foreach ($smporMonthLabels as $monthLabel)
                                                <td class="px-4 py-3 text-right">{{ $formatSmporValue($row[$key][$monthLabel] ?? 0) }}</td>
                                            @endforeach
                                            <td class="px-4 py-3 text-right">{{ $formatSmporValue($row[$key . '_total'] ?? 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr class="bg-slate-900/40">
                                            <td colspan="{{ count($smporMonthLabels) + 2 }}" class="px-4 py-3 text-center text-slate-400">No outputs found for this section.</td>
                                        </tr>
                                    @endforelse
                                @empty
                                    <tr class="bg-slate-900/40">
                                        <td colspan="{{ count($smporMonthLabels) + 2 }}" class="px-4 py-3 text-center text-slate-400">No submitted MPOR data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-700 pt-4">
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const smporTabButtons = Array.from(document.querySelectorAll('[data-smpor-tab]'));
            const smporTabPanels = Array.from(document.querySelectorAll('[data-smpor-tab-panel]'));

            function setSmporTab(activeTab) {
                smporTabButtons.forEach((button) => {
                    const isActive = button.dataset.smporTab === activeTab;
                    button.classList.toggle('border-sky-500/40', isActive);
                    button.classList.toggle('bg-sky-500/20', isActive);
                    button.classList.toggle('text-sky-200', isActive);
                    button.classList.toggle('border-slate-700', !isActive);
                    button.classList.toggle('text-slate-300', !isActive);
                    button.classList.toggle('hover:bg-slate-800', !isActive);
                });

                smporTabPanels.forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.smporTabPanel !== activeTab);
                });
            }

            smporTabButtons.forEach((button) => {
                button.addEventListener('click', () => setSmporTab(button.dataset.smporTab));
            });
            setSmporTab('quantity');
        });
    </script>
@endsection
