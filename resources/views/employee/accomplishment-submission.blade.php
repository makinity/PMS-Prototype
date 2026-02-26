@extends('layouts.employee')

@section('main-content')
    @php
        $isSubmitted = ($submissionStatus ?? 'draft') === 'submitted';
        $statusBadgeClasses = $isSubmitted
            ? 'inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200 border border-emerald-500/40'
            : 'inline-flex items-center rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-200 border border-amber-500/40';
        $periodLabelValue = $periodLabel ?? '—';
        $periodHeaderLabel = $periodLabelValue === '—' ? 'No active period' : $periodLabelValue;

        $smporMonthLabels = !empty($smporMonths ?? []) && is_array($smporMonths)
            ? array_values($smporMonths)
            : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
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

        @if ($errors->any())
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Page Header -->
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-white">SMPOR &amp; IPCR Accomplishment Submission</h1>
                <p class="text-sm text-slate-400">Formal end-of-period submission of accomplishments</p>
                <p class="text-xs text-slate-500 mt-1">Performance Period: {{ $periodHeaderLabel }}</p>
            </div>
            <div class="flex flex-col items-end gap-1">
                <span id="status-badge" class="{{ $statusBadgeClasses }}">
                    {{ $isSubmitted ? 'Submitted to Supervisor & Dept Head' : 'Draft' }}
                </span>
                @if ($isSubmitted)
                    <p class="text-xs text-slate-400">Submitted: {{ $submittedAtLabel ?? '--' }}</p>
                @endif
            </div>
        </div>

        <!-- SMPOR Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">SMPOR &ndash; Monitoring Summary</h2>
                    <p class="text-sm text-slate-400">System-generated summary based on submitted MPORs. Read-only.</p>
                </div>
                <div class="flex gap-2">
                    <a href="#"
                       data-open-modal="smpor-preview-modal"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800 transition">
                        View SMPOR
                    </a>

                    <a href="{{ route('smpor.export.excel') }}"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                            Export
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-slate-200">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Period</p>
                    <p class="mt-1 font-semibold">{{ $periodLabelValue }}</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Status</p>
                    <p class="mt-1 font-semibold">System-generated, monitoring-only</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Data Source</p>
                    <p class="mt-1 font-semibold">Submitted MPORs</p>
                </div>
            </div>
        </div>

        <!-- IPCR Card -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">IPCR Accomplishment Report</h2>
                    <p class="text-sm text-slate-400">System-generated accomplishments derived from SMPOR totals. Read-only.</p>
                </div>
                <div class="flex gap-2">
                    <a href="#"
                       data-open-modal="ipcr-preview-modal"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800 transition">
                        View IPCR Accomplishment
                    </a>

                    <a href="{{ route('stage2.ipcr.export.excel') }}"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                            Export
                    </a>

                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-slate-200">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Rating Period</p>
                    <p class="mt-1 font-semibold">{{ $periodLabelValue }}</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Status</p>
                    <p class="mt-1 font-semibold">System-generated, read-only</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Data Source</p>
                    <p class="mt-1 font-semibold">SMPOR totals (derived from submitted MPORs)</p>
                </div>
            </div>
        </div>

        <form id="accomplishment-submission-form"
              method="POST"
              action="{{ route('stage2.employee.accomplishment.submit') }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf

            <!-- Supporting Documents -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Supporting Documents (Optional)</h2>
                        <p class="text-sm text-slate-400">Uploads are optional and disabled after submission.</p>
                    </div>
                </div>
                <input id="supporting-files"
                       name="supporting_files[]"
                       type="file"
                       multiple
                       @disabled($isSubmitted)
                       class="block w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-200 file:mr-3 file:rounded-md file:border-0 file:bg-slate-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-200 disabled:cursor-not-allowed disabled:opacity-60">

                @if (!empty($attachmentNames ?? []))
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-xs uppercase text-slate-500">Submitted Attachments (Prototype)</p>
                        <ul class="mt-2 space-y-1 text-sm text-slate-300">
                            @foreach ($attachmentNames as $attachmentName)
                                <li>{{ $attachmentName }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Remarks -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Employee Remarks (Optional)</h2>
                        <p class="text-sm text-slate-400">Remarks become read-only after submission.</p>
                    </div>
                </div>
                <textarea id="employee-remarks"
                          name="remarks"
                          style="background:#0f172a;color:#e5e7eb;"
                          rows="3"
                          @disabled($isSubmitted)
                          class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500 disabled:cursor-not-allowed disabled:opacity-70"
                          placeholder="Add clarifications or context (optional)">{{ old('remarks', $remarksValue ?? '') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
                <button type="button"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Back
                </button>
                <button type="button"
                        id="submit-accomplishments"
                        data-action="confirm-submission"
                        @disabled($isSubmitted)
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-emerald-600 transition disabled:cursor-not-allowed disabled:opacity-70">
                    <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-emerald-900/30 border-t-emerald-900"></span>
                    <span data-button-label>{{ $isSubmitted ? 'Already Submitted' : 'Submit Accomplishments' }}</span>
                </button>
            </div>
        </form>

    </section>

    <!-- SMPOR Preview Modal -->
    <div id="smpor-preview-modal"
         data-preview-modal
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">SMPOR (Monitoring Summary)</p>
                    <h3 class="text-lg font-semibold text-white">SMPOR Preview &mdash; {{ $periodLabelValue }}</h3>
                    <p class="text-sm text-slate-400 mt-1">System-generated, monitoring-only. Derived from submitted MPORs.</p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 space-y-5 max-h-[65vh] overflow-y-auto text-sm text-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Employee</p>
                        <p class="mt-1 font-semibold">{{ $employeeName ?? 'Ramon Reyes' }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Office/Unit</p>
                        <p class="mt-1 font-semibold">{{ $officeName ?? 'Revenue Collection Unit' }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Period</p>
                        <p class="mt-1 font-semibold">{{ $periodLabelValue }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Source</p>
                        <p class="mt-1 font-semibold">Submitted MPORs</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-base font-semibold text-white">Monitoring Totals</h4>
                        <span class="text-xs text-slate-400">Quality Points = Quantity &times; Quality Rating &middot; Timeliness Points = Quantity &times; Timeliness Rating</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                                data-smpor-tab="quantity"
                                class="rounded-lg border border-sky-500/40 bg-sky-500/20 px-3 py-1.5 text-xs font-semibold text-sky-200 transition">
                            Efficiency/Quantity
                        </button>
                        <button type="button"
                                data-smpor-tab="quality"
                                class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-300 transition hover:bg-slate-800">
                            Quality/Effectiveness
                        </button>
                        <button type="button"
                                data-smpor-tab="timeliness"
                                class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-300 transition hover:bg-slate-800">
                            Timeliness
                        </button>
                    </div>
                    <p class="text-xs text-slate-400">Monthly totals are derived from rated ORS monitoring within submitted MPORs.</p>

                    @php
                        $smporSectionList = is_array($smporSections ?? null) ? $smporSections : [];
                        $formatSmporValue = static function ($value): string {
                            $numeric = (float) ($value ?? 0);
                            return fmod($numeric, 1.0) === 0.0
                                ? (string) (int) $numeric
                                : rtrim(rtrim(number_format($numeric, 2, '.', ''), '0'), '.');
                        };
                    @endphp

                    <div data-smpor-tab-panel="quantity" class="overflow-x-auto rounded-xl border border-slate-800">
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
                                        $sectionTitle = trim((string) ($section['title'] ?? 'Section'));
                                        $sectionTitle = $sectionTitle !== '' ? $sectionTitle : 'Section';
                                        $sectionQtyTotal = (float) ($section['totals']['quantity_total'] ?? 0);
                                    @endphp
                                    <tr class="bg-slate-950/60">
                                        <td colspan="{{ count($smporMonthLabels) + 2 }}" class="px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] text-slate-100">
                                            {{ $sectionTitle }}
                                        </td>
                                    </tr>
                                    @forelse ($sectionRows as $row)
                                        <tr class="bg-slate-900/40">
                                            <td class="px-4 py-3 font-semibold">{{ $row['expected_output'] ?? '—' }}</td>
                                            @foreach ($smporMonthLabels as $monthLabel)
                                                <td class="px-4 py-3 text-right">{{ $formatSmporValue($row['quantity'][$monthLabel] ?? 0) }}</td>
                                            @endforeach
                                            <td class="px-4 py-3 text-right">{{ $formatSmporValue($row['quantity_total'] ?? 0) }}</td>
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

                    <div data-smpor-tab-panel="quality" class="hidden overflow-x-auto rounded-xl border border-slate-800">
                        <table class="min-w-full text-left text-sm text-slate-200">
                            <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-4 py-3">Expected Outputs</th>
                                    @foreach ($smporMonthLabels as $monthLabel)
                                        <th class="px-4 py-3 text-right">{{ $monthLabel }}</th>
                                    @endforeach
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3 text-right">Average</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($smporSectionList as $section)
                                    @php
                                        $sectionRows = is_array($section['rows'] ?? null) ? $section['rows'] : [];
                                        $sectionTitle = trim((string) ($section['title'] ?? 'Section'));
                                        $sectionTitle = $sectionTitle !== '' ? $sectionTitle : 'Section';
                                        $sectionQualityTotal = (float) ($section['totals']['quality_total'] ?? 0);
                                        $sectionQualityAvg = (float) ($section['totals']['quality_avg'] ?? 0);
                                    @endphp
                                    <tr class="bg-slate-950/60">
                                        <td colspan="{{ count($smporMonthLabels) + 3 }}" class="px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] text-slate-100">
                                            {{ $sectionTitle }}
                                        </td>
                                    </tr>
                                    @forelse ($sectionRows as $row)
                                        @php
                                            $rowQty = (float) ($row['quantity_total'] ?? 0);
                                            $rowQuality = (float) ($row['quality_total'] ?? 0);
                                            $rowQualityAvg = $rowQty > 0 ? $rowQuality / $rowQty : 0;
                                        @endphp
                                        <tr class="bg-slate-900/40">
                                            <td class="px-4 py-3 font-semibold">{{ $row['expected_output'] ?? '—' }}</td>
                                            @foreach ($smporMonthLabels as $monthLabel)
                                                <td class="px-4 py-3 text-right">{{ $formatSmporValue($row['quality'][$monthLabel] ?? 0) }}</td>
                                            @endforeach
                                            <td class="px-4 py-3 text-right">{{ $formatSmporValue($rowQuality) }}</td>
                                            <td class="px-4 py-3 text-right">{{ number_format($rowQualityAvg, 2, '.', '') }}</td>
                                        </tr>
                                    @empty
                                        <tr class="bg-slate-900/40">
                                            <td colspan="{{ count($smporMonthLabels) + 3 }}" class="px-4 py-3 text-center text-slate-400">No outputs found for this section.</td>
                                        </tr>
                                    @endforelse
                                @empty
                                    <tr class="bg-slate-900/40">
                                        <td colspan="{{ count($smporMonthLabels) + 3 }}" class="px-4 py-3 text-center text-slate-400">No submitted MPOR data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div data-smpor-tab-panel="timeliness" class="hidden overflow-x-auto rounded-xl border border-slate-800">
                        <table class="min-w-full text-left text-sm text-slate-200">
                            <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-4 py-3">Expected Outputs</th>
                                    @foreach ($smporMonthLabels as $monthLabel)
                                        <th class="px-4 py-3 text-right">{{ $monthLabel }}</th>
                                    @endforeach
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3 text-right">Average</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($smporSectionList as $section)
                                    @php
                                        $sectionRows = is_array($section['rows'] ?? null) ? $section['rows'] : [];
                                        $sectionTitle = trim((string) ($section['title'] ?? 'Section'));
                                        $sectionTitle = $sectionTitle !== '' ? $sectionTitle : 'Section';
                                        $sectionTimelinessTotal = (float) ($section['totals']['timeliness_total'] ?? 0);
                                        $sectionTimelinessAvg = (float) ($section['totals']['timeliness_avg'] ?? 0);
                                    @endphp
                                    <tr class="bg-slate-950/60">
                                        <td colspan="{{ count($smporMonthLabels) + 3 }}" class="px-4 py-3 text-xs font-bold uppercase tracking-[0.14em] text-slate-100">
                                            {{ $sectionTitle }}
                                        </td>
                                    </tr>
                                    @forelse ($sectionRows as $row)
                                        @php
                                            $rowQty = (float) ($row['quantity_total'] ?? 0);
                                            $rowTimeliness = (float) ($row['timeliness_total'] ?? 0);
                                            $rowTimelinessAvg = $rowQty > 0 ? $rowTimeliness / $rowQty : 0;
                                        @endphp
                                        <tr class="bg-slate-900/40">
                                            <td class="px-4 py-3 font-semibold">{{ $row['expected_output'] ?? '—' }}</td>
                                            @foreach ($smporMonthLabels as $monthLabel)
                                                <td class="px-4 py-3 text-right">{{ $formatSmporValue($row['timeliness'][$monthLabel] ?? 0) }}</td>
                                            @endforeach
                                            <td class="px-4 py-3 text-right">{{ $formatSmporValue($rowTimeliness) }}</td>
                                            <td class="px-4 py-3 text-right">{{ number_format($rowTimelinessAvg, 2, '.', '') }}</td>
                                        </tr>
                                    @empty
                                        <tr class="bg-slate-900/40">
                                            <td colspan="{{ count($smporMonthLabels) + 3 }}" class="px-4 py-3 text-center text-slate-400">No outputs found for this section.</td>
                                        </tr>
                                    @endforelse
                                @empty
                                    <tr class="bg-slate-900/40">
                                        <td colspan="{{ count($smporMonthLabels) + 3 }}" class="px-4 py-3 text-center text-slate-400">No submitted MPOR data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <button type="button"
                        data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- IPCR Preview Modal -->
    <div id="ipcr-preview-modal"
         data-preview-modal
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR Accomplishment Report</p>
                    <h3 class="text-lg font-semibold text-white">IPCR Accomplishment Preview &mdash; {{ $periodLabelValue }}</h3>
                    <p class="text-sm text-slate-400 mt-1">System-generated commitments for digital accomplishment submission.</p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 space-y-5 max-h-[65vh] overflow-y-auto text-sm text-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Employee</p>
                        <p class="mt-1 font-semibold">{{ $employeeName ?? 'Ramon Reyes' }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Office/Unit</p>
                        <p class="mt-1 font-semibold">{{ $officeName ?? 'Revenue Collection Unit' }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Period</p>
                        <p class="mt-1 font-semibold">{{ $periodLabelValue }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Source</p>
                        <p class="mt-1 font-semibold">IPCR Commitments</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse (($ipcrSections ?? []) as $sectionIndex => $section)
                        @php
                            $sectionTitle = trim((string) ($section['title'] ?? '')) ?: 'Untitled Section';
                            $sectionWeight = $section['weight_percent'] ?? null;
                            $sectionRows = $section['rows'] ?? [];
                        @endphp
                        <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4 space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h4 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-100">{{ $sectionTitle }}</h4>
                                @if (!is_null($sectionWeight))
                                    <span class="rounded-full border border-sky-500/30 bg-sky-500/10 px-3 py-1 text-[11px] font-semibold text-sky-200">
                                        {{ rtrim(rtrim(number_format((float) $sectionWeight, 2, '.', ''), '0'), '.') }}%
                                    </span>
                                @endif
                            </div>
                            <div class="overflow-x-auto rounded-lg border border-slate-800">
                                <table class="min-w-full text-left text-sm text-slate-200">
                                    <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                                        <tr>
                                            <th class="px-4 py-3">Major Output</th>
                                            <th class="px-4 py-3">Success Indicators</th>
                                            <th class="px-4 py-3">Target Summary</th>
                                            <th class="px-4 py-3">Timeline</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800">
                                        @forelse ($sectionRows as $rowIndex => $row)
                                            <tr class="bg-slate-900/40">
                                                <td class="px-4 py-3 font-semibold text-slate-100">{{ $row['major_output'] ?? '—' }}</td>
                                                <td class="px-4 py-3">
                                                    <a href="javascript:void(0)"
                                                       data-ipcr-open-indicators
                                                       data-section-index="{{ $sectionIndex }}"
                                                       data-row-index="{{ $rowIndex }}"
                                                       aria-label="View success indicators ({{ (int) ($row['indicators_count'] ?? 0) }})"
                                                       class="inline-flex items-center gap-1 text-sky-300 transition hover:text-sky-200 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                             viewBox="0 0 24 24"
                                                             fill="none"
                                                             stroke="currentColor"
                                                             stroke-width="1.5"
                                                             class="h-4 w-4"
                                                             aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <span class="text-xs font-semibold leading-none">
                                                            {{ (int) ($row['indicators_count'] ?? 0) }}
                                                        </span>
                                                    </a>
                                                </td>
                                                <td class="px-4 py-3 text-slate-300">{{ $row['target_summary'] ?? '—' }}</td>
                                                <td class="px-4 py-3 text-slate-300">{{ $row['timeline'] ?? $periodLabelValue }}</td>
                                            </tr>
                                        @empty
                                            <tr class="bg-slate-900/40">
                                                <td colspan="4" class="px-4 py-3 text-center text-slate-400">No outputs found for this section.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-4 text-sm text-slate-400">
                            No IPCR commitments found for this period.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">


                <button type="button"
                        data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Success Indicators Modal -->
    <div id="ipcr-indicators-modal"
         data-preview-modal
         data-parent-modal-id="ipcr-preview-modal"
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR Success Indicators</p>
                    <h3 id="ipcrIndicatorsMajorOutput" class="text-lg font-semibold text-white">Success Indicators</h3>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 space-y-5 max-h-[65vh] overflow-y-auto text-sm text-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Employee</p>
                        <p class="mt-1 font-semibold">{{ $employeeName ?? 'Ramon Reyes' }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Office/Unit</p>
                        <p class="mt-1 font-semibold">{{ $officeName ?? 'Revenue Collection Unit' }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Period</p>
                        <p class="mt-1 font-semibold">{{ $periodLabelValue }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="min-w-full text-left text-sm text-slate-200">
                        <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Indicator</th>
                                <th class="px-4 py-3">Standards (Q/E/T)</th>
                            </tr>
                        </thead>
                        <tbody id="ipcrIndicatorsTbody" class="divide-y divide-slate-800">
                            <tr class="bg-slate-900/40">
                                <td colspan="2" class="px-4 py-3 text-center text-slate-400">Select a major output to view indicators.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <button type="button"
                        data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Standards Modal -->
    <div id="ipcr-standards-modal"
         data-preview-modal
         data-parent-modal-id="ipcr-indicators-modal"
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-6xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                    <h3 class="text-lg font-semibold text-white">Performance Standards</h3>
                    <p id="ipcrStandardsIndicatorText" class="text-sm text-slate-400 mt-1">Select an indicator to view standards.</p>
                </div>
                <button type="button" data-close-modal class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 max-h-[65vh] overflow-y-auto text-sm text-slate-200">
                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="min-w-full text-left text-sm text-slate-200">
                        <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Rating</th>
                                <th class="px-4 py-3">Quality (Q)</th>
                                <th class="px-4 py-3">Efficiency (E)</th>
                                <th class="px-4 py-3">Timeliness (T)</th>
                            </tr>
                        </thead>
                        <tbody id="ipcrStandardsTbody" class="divide-y divide-slate-800">
                            <tr class="bg-slate-900/40">
                                <td colspan="4" class="px-4 py-3 text-center text-slate-400">No standards loaded.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <button type="button"
                        data-close-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script id="ipcr-sections-json" type="application/json">{!! json_encode($ipcrSections ?? [], JSON_UNESCAPED_UNICODE) !!}</script>

    <!-- Generic Modal -->
    <div id="action-modal"
         class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p id="modal-eyebrow" class="text-xs uppercase tracking-[0.2em] text-blue-300">Action</p>
                    <h3 id="modal-title" class="text-lg font-semibold text-white">--</h3>
                </div>
                <button type="button" id="modal-close" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <div class="mt-4 space-y-3 max-h-[60vh] overflow-y-auto text-sm text-slate-200">
                <p id="modal-body" class="text-slate-200"></p>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <button type="button"
                        id="modal-cancel"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
                <button type="submit"
                        form="accomplishment-submission-form"
                        id="modal-confirm"
                        @disabled($isSubmitted)
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500 transition disabled:cursor-not-allowed disabled:opacity-70">
                    <span data-modal-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                    <span data-modal-label>{{ $isSubmitted ? 'Already Submitted' : 'Submit Accomplishments' }}</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('action-modal');
                const previewModals = Array.from(document.querySelectorAll('[data-preview-modal]'));
                const modalTitle = document.getElementById('modal-title');
                const modalBody = document.getElementById('modal-body');
                const modalConfirm = document.getElementById('modal-confirm');
                const modalCancel = document.getElementById('modal-cancel');
                const modalClose = document.getElementById('modal-close');
                const modalSpinner = modalConfirm?.querySelector('[data-modal-spinner]');
                const modalLabel = modalConfirm?.querySelector('[data-modal-label]');
                const submissionForm = document.getElementById('accomplishment-submission-form');
                const submitBtn = document.getElementById('submit-accomplishments');
                const smporTabButtons = Array.from(document.querySelectorAll('[data-smpor-tab]'));
                const smporTabPanels = Array.from(document.querySelectorAll('[data-smpor-tab-panel]'));
                const ipcrSectionsJsonEl = document.getElementById('ipcr-sections-json');
                const ipcrIndicatorsMajorOutput = document.getElementById('ipcrIndicatorsMajorOutput');
                const ipcrIndicatorsTbody = document.getElementById('ipcrIndicatorsTbody');
                const ipcrStandardsIndicatorText = document.getElementById('ipcrStandardsIndicatorText');
                const ipcrStandardsTbody = document.getElementById('ipcrStandardsTbody');
                const ipcrIndicatorButtons = Array.from(document.querySelectorAll('[data-ipcr-open-indicators]'));
                const openPreviewStack = [];
                let activeAction = null;
                let selectedIndicators = [];

                const modalContent = {
                    'confirm-submission': {
                        title: 'Submit Accomplishments',
                        body: 'Confirm formal submission of SMPOR and IPCR accomplishments for Stage III evaluation. After submission, uploads and remarks become read-only.',
                        showConfirm: true,
                    },
                };
                let ipcrSectionsData = [];
                if (ipcrSectionsJsonEl) {
                    try {
                        const parsedPayload = JSON.parse(ipcrSectionsJsonEl.textContent || '[]');
                        ipcrSectionsData = Array.isArray(parsedPayload) ? parsedPayload : [];
                    } catch (error) {
                        ipcrSectionsData = [];
                    }
                }

                previewModals.forEach((previewModal) => {
                    if (previewModal.classList.contains('flex') && !previewModal.classList.contains('hidden')) {
                        openPreviewStack.push(previewModal);
                    }
                });

                function isAnyModalOpen() {
                    const actionOpen = modal && modal.classList.contains('flex');
                    return actionOpen || openPreviewStack.length > 0;
                }

                function syncBodyScroll() {
                    document.body.classList.toggle('overflow-hidden', isAnyModalOpen());
                }

                function setModalState(show) {
                    if (!modal) return;
                    modal.classList.toggle('hidden', !show);
                    modal.classList.toggle('flex', show);
                    syncBodyScroll();
                }

                function openModal(action) {
                    const content = modalContent[action] || modalContent['confirm-submission'];
                    activeAction = action;
                    if (modalTitle) modalTitle.textContent = content.title;
                    if (modalBody) modalBody.textContent = content.body;
                    if (modalConfirm) modalConfirm.classList.toggle('hidden', !content.showConfirm);
                    setModalState(true);
                }

                function closeModal() {
                    setModalState(false);
                    activeAction = null;
                }

                function refreshPreviewModalZIndices() {
                    const baseZIndex = 80;
                    openPreviewStack.forEach((modalEl, index) => {
                        modalEl.style.zIndex = String(baseZIndex + (index * 10));
                    });
                }

                function getParentModalId(modalEl) {
                    return modalEl?.dataset?.parentModalId || '';
                }

                function isDescendantOfModal(modalEl, ancestorModalId) {
                    if (!modalEl || !ancestorModalId) {
                        return false;
                    }

                    let currentParentId = getParentModalId(modalEl);
                    while (currentParentId) {
                        if (currentParentId === ancestorModalId) {
                            return true;
                        }

                        const parentModal = document.getElementById(currentParentId);
                        if (!parentModal) {
                            break;
                        }

                        currentParentId = getParentModalId(parentModal);
                    }

                    return false;
                }

                function hidePreviewModal(modalEl) {
                    if (!modalEl) return;
                    modalEl.classList.add('hidden');
                    modalEl.classList.remove('flex');
                    modalEl.style.zIndex = '';
                }

                function openPreviewModal(modalId) {
                    if (!modalId) return;
                    const target = document.getElementById(modalId);
                    if (!target) return;

                    hidePreviewModal(target);
                    const existingIndex = openPreviewStack.indexOf(target);
                    if (existingIndex !== -1) {
                        openPreviewStack.splice(existingIndex, 1);
                    }

                    target.classList.remove('hidden');
                    target.classList.add('flex');
                    openPreviewStack.push(target);
                    refreshPreviewModalZIndices();
                    syncBodyScroll();
                }

                function closePreviewModal(modalEl, cascadeChildren = true) {
                    if (!modalEl) return;

                    if (cascadeChildren && modalEl.id) {
                        const descendants = openPreviewStack
                            .filter((item) => isDescendantOfModal(item, modalEl.id))
                            .reverse();
                        descendants.forEach((descendantModal) => closePreviewModal(descendantModal, false));
                    }

                    hidePreviewModal(modalEl);
                    const index = openPreviewStack.indexOf(modalEl);
                    if (index !== -1) {
                        openPreviewStack.splice(index, 1);
                    }
                    refreshPreviewModalZIndices();
                    syncBodyScroll();
                }

                function closeAllPreviewModals() {
                    openPreviewStack
                        .slice()
                        .reverse()
                        .forEach((modalEl) => closePreviewModal(modalEl, false));
                }

                refreshPreviewModalZIndices();
                syncBodyScroll();

                function setSmporTab(activeTab) {
                    smporTabButtons.forEach((button) => {
                        const isActive = button.dataset.smporTab === activeTab;
                        button.classList.toggle('border-sky-500/40', isActive);
                        button.classList.toggle('bg-sky-500/20', isActive);
                        button.classList.toggle('text-sky-200', isActive);
                        button.classList.toggle('border-slate-700', !isActive);
                        button.classList.toggle('text-slate-300', !isActive);
                        button.classList.toggle('hover:bg-slate-800', !isActive);
                        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });

                    smporTabPanels.forEach((panel) => {
                        panel.classList.toggle('hidden', panel.dataset.smporTabPanel !== activeTab);
                    });
                }

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function normalizeStandardsPayload(payload) {
                    if (!payload) return {};
                    if (typeof payload === 'string') {
                        try {
                            const parsed = JSON.parse(payload);
                            return parsed && typeof parsed === 'object' ? parsed : {};
                        } catch (error) {
                            return {};
                        }
                    }
                    return typeof payload === 'object' ? payload : {};
                }

                function buildStandardsCell(values) {
                    if (!Array.isArray(values) || values.length === 0) {
                        return '<span class="text-slate-400">—</span>';
                    }

                    return `
                        <ul class="list-disc space-y-1 pl-4 text-xs text-slate-200">
                            ${values.map((value) => `<li>${escapeHtml(value)}</li>`).join('')}
                        </ul>
                    `;
                }

                function renderIndicatorsModal(sectionIndex, rowIndex) {
                    const section = ipcrSectionsData?.[sectionIndex];
                    const row = section?.rows?.[rowIndex];
                    if (!row) return;

                    selectedIndicators = Array.isArray(row.indicators) ? row.indicators : [];

                    if (ipcrIndicatorsMajorOutput) {
                        ipcrIndicatorsMajorOutput.textContent = `Success Indicators - ${row.major_output ?? 'Major Output'}`;
                    }

                    if (ipcrIndicatorsTbody) {
                        if (selectedIndicators.length === 0) {
                            ipcrIndicatorsTbody.innerHTML = `
                                <tr class="bg-slate-900/40">
                                    <td colspan="2" class="px-4 py-3 text-center text-slate-400">No success indicators available.</td>
                                </tr>
                            `;
                        } else {
                            ipcrIndicatorsTbody.innerHTML = selectedIndicators.map((indicator, indicatorIndex) => `
                                <tr class="bg-slate-900/40">
                                    <td class="px-4 py-3 text-slate-100">${escapeHtml(indicator?.indicator_text ?? '—')}</td>
                                    <td class="px-4 py-3">
                                        <a href="javascript:void(0)"
                                           data-ipcr-open-standards
                                           data-indicator-index="${indicatorIndex}"
                                           aria-label="View standards"
                                           class="inline-flex items-center text-sky-300 transition hover:text-sky-200 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 24 24"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 stroke-width="1.5"
                                                 class="h-4 w-4"
                                                 aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
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

                    if (ipcrStandardsIndicatorText) {
                        ipcrStandardsIndicatorText.textContent = indicator.indicator_text || '—';
                    }

                    const payload = normalizeStandardsPayload(indicator.standards_payload);
                    const ratings = ['5', '4', '3', '2', '1'];

                    if (ipcrStandardsTbody) {
                        ipcrStandardsTbody.innerHTML = ratings.map((rating) => {
                            const ratingPayload = payload?.[rating] ?? {};
                            const qualityValues = Array.isArray(ratingPayload?.Q) ? ratingPayload.Q : [];
                            const efficiencyValues = Array.isArray(ratingPayload?.E) ? ratingPayload.E : [];
                            const timelinessValues = Array.isArray(ratingPayload?.T) ? ratingPayload.T : [];

                            return `
                                <tr class="bg-slate-900/40 align-top">
                                    <td class="px-4 py-3 font-semibold text-slate-100">${rating}</td>
                                    <td class="px-4 py-3">${buildStandardsCell(qualityValues)}</td>
                                    <td class="px-4 py-3">${buildStandardsCell(efficiencyValues)}</td>
                                    <td class="px-4 py-3">${buildStandardsCell(timelinessValues)}</td>
                                </tr>
                            `;
                        }).join('');
                    }

                    openPreviewModal('ipcr-standards-modal');
                }

                function handleConfirm(event) {
                    if (activeAction !== 'confirm-submission' || !submissionForm || !modalConfirm) {
                        closeModal();
                        return;
                    }

                    event.preventDefault();
                    if (modalSpinner) modalSpinner.classList.remove('hidden');
                    if (modalLabel) modalLabel.textContent = 'Submitting...';
                    modalConfirm.disabled = true;
                    modalConfirm.classList.add('opacity-70', 'cursor-not-allowed');

                    submissionForm.requestSubmit();
                }

                document.querySelectorAll('[data-action]').forEach((btn) => {
                    btn.addEventListener('click', () => openModal(btn.dataset.action));
                });

                document.querySelectorAll('[data-open-modal]').forEach((trigger) => {
                    trigger.addEventListener('click', (e) => {
                        e.preventDefault();
                        openPreviewModal(trigger.getAttribute('data-open-modal'));
                    });
                });

                smporTabButtons.forEach((button) => {
                    button.addEventListener('click', () => setSmporTab(button.dataset.smporTab));
                });
                setSmporTab('quantity');

                ipcrIndicatorButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const sectionIndex = Number.parseInt(button.dataset.sectionIndex ?? '', 10);
                        const rowIndex = Number.parseInt(button.dataset.rowIndex ?? '', 10);
                        if (Number.isNaN(sectionIndex) || Number.isNaN(rowIndex)) return;
                        renderIndicatorsModal(sectionIndex, rowIndex);
                    });
                });

                ipcrIndicatorsTbody?.addEventListener('click', (event) => {
                    const targetButton = event.target.closest('[data-ipcr-open-standards]');
                    if (!targetButton) return;

                    const indicatorIndex = Number.parseInt(targetButton.dataset.indicatorIndex ?? '', 10);
                    if (Number.isNaN(indicatorIndex)) return;
                    renderStandardsModal(indicatorIndex);
                });

                document.querySelectorAll('[data-close-modal]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const modalEl = btn.closest('[data-preview-modal]');
                        closePreviewModal(modalEl);
                    });
                });

                previewModals.forEach((previewModal) => {
                    previewModal.addEventListener('click', (e) => {
                        if (e.target === previewModal) closePreviewModal(previewModal);
                    });
                });

                submissionForm?.addEventListener('submit', () => {
                    if (modalSpinner) modalSpinner.classList.remove('hidden');
                    if (modalLabel) modalLabel.textContent = 'Submitting...';
                    if (modalConfirm) {
                        modalConfirm.disabled = true;
                        modalConfirm.classList.add('opacity-70', 'cursor-not-allowed');
                    }
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                    }
                });

                modalConfirm?.addEventListener('click', handleConfirm);
                modalClose?.addEventListener('click', closeModal);
                modalCancel?.addEventListener('click', closeModal);
                modal?.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal();
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        if (openPreviewStack.length > 0) {
                            const topmostPreviewModal = openPreviewStack[openPreviewStack.length - 1];
                            closePreviewModal(topmostPreviewModal);
                            return;
                        }

                        closeModal();
                    }
                });
            });
        </script>
    @endpush

@endsection
