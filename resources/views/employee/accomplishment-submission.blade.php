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

                    <a href="{{ route('stage2.smpor.export.excel') }}"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                            Export PDF
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
                                @forelse ($smporRows as $smporRow)
                                    <tr class="bg-slate-900/40">
                                        <td class="px-4 py-3 font-semibold">{{ $smporRow['mfo'] }}</td>
                                        @foreach ($smporMonthLabels as $monthLabel)
                                            <td class="px-4 py-3 text-right">{{ $smporRow['monthly_quantity'][$monthLabel] ?? 0 }}</td>
                                        @endforeach
                                        <td class="px-4 py-3 text-right">{{ $smporRow['total_quantity'] ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr class="bg-slate-900/40">
                                        <td colspan="{{ count($smporMonthLabels) + 2 }}" class="px-4 py-3 text-center text-slate-400">No submitted MPOR data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-slate-950/70">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-100">Grand Total</th>
                                    @foreach ($smporMonthLabels as $monthLabel)
                                        <th class="px-4 py-3 text-right font-semibold text-slate-100">{{ $smporTotals['monthly_quantity'][$monthLabel] ?? 0 }}</th>
                                    @endforeach
                                    <th class="px-4 py-3 text-right font-semibold text-slate-100">{{ $smporTotals['quantity'] ?? 0 }}</th>
                                </tr>
                            </tfoot>
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
                                @forelse ($smporRows as $smporRow)
                                    @php
                                        $rowQty = (float) ($smporRow['total_quantity'] ?? 0);
                                        $rowQuality = (float) ($smporRow['total_quality_points'] ?? 0);
                                        $rowQualityAvg = $rowQty > 0 ? $rowQuality / $rowQty : 0;
                                    @endphp
                                    <tr class="bg-slate-900/40">
                                        <td class="px-4 py-3 font-semibold">{{ $smporRow['mfo'] }}</td>
                                        @foreach ($smporMonthLabels as $monthLabel)
                                            <td class="px-4 py-3 text-right">{{ $smporRow['monthly_quality_points'][$monthLabel] ?? 0 }}</td>
                                        @endforeach
                                        <td class="px-4 py-3 text-right">{{ $rowQuality }}</td>
                                        <td class="px-4 py-3 text-right">{{ number_format($rowQualityAvg, 2, '.', '') }}</td>
                                    </tr>
                                @empty
                                    <tr class="bg-slate-900/40">
                                        <td colspan="{{ count($smporMonthLabels) + 3 }}" class="px-4 py-3 text-center text-slate-400">No submitted MPOR data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-slate-950/70">
                                @php
                                    $totalQty = (float) ($smporTotals['quantity'] ?? 0);
                                    $totalQuality = (float) ($smporTotals['quality_points'] ?? 0);
                                    $totalQualityAvg = $totalQty > 0 ? $totalQuality / $totalQty : 0;
                                @endphp
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-100">Grand Total</th>
                                    @foreach ($smporMonthLabels as $monthLabel)
                                        <th class="px-4 py-3 text-right font-semibold text-slate-100">{{ $smporTotals['monthly_quality_points'][$monthLabel] ?? 0 }}</th>
                                    @endforeach
                                    <th class="px-4 py-3 text-right font-semibold text-slate-100">{{ $totalQuality }}</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-100">{{ number_format($totalQualityAvg, 2, '.', '') }}</th>
                                </tr>
                            </tfoot>
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
                                @forelse ($smporRows as $smporRow)
                                    @php
                                        $rowQty = (float) ($smporRow['total_quantity'] ?? 0);
                                        $rowTimeliness = (float) ($smporRow['total_timeliness_points'] ?? 0);
                                        $rowTimelinessAvg = $rowQty > 0 ? $rowTimeliness / $rowQty : 0;
                                    @endphp
                                    <tr class="bg-slate-900/40">
                                        <td class="px-4 py-3 font-semibold">{{ $smporRow['mfo'] }}</td>
                                        @foreach ($smporMonthLabels as $monthLabel)
                                            <td class="px-4 py-3 text-right">{{ $smporRow['monthly_timeliness_points'][$monthLabel] ?? 0 }}</td>
                                        @endforeach
                                        <td class="px-4 py-3 text-right">{{ $rowTimeliness }}</td>
                                        <td class="px-4 py-3 text-right">{{ number_format($rowTimelinessAvg, 2, '.', '') }}</td>
                                    </tr>
                                @empty
                                    <tr class="bg-slate-900/40">
                                        <td colspan="{{ count($smporMonthLabels) + 3 }}" class="px-4 py-3 text-center text-slate-400">No submitted MPOR data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-slate-950/70">
                                @php
                                    $totalQty = (float) ($smporTotals['quantity'] ?? 0);
                                    $totalTimeliness = (float) ($smporTotals['timeliness_points'] ?? 0);
                                    $totalTimelinessAvg = $totalQty > 0 ? $totalTimeliness / $totalQty : 0;
                                @endphp
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-100">Grand Total</th>
                                    @foreach ($smporMonthLabels as $monthLabel)
                                        <th class="px-4 py-3 text-right font-semibold text-slate-100">{{ $smporTotals['monthly_timeliness_points'][$monthLabel] ?? 0 }}</th>
                                    @endforeach
                                    <th class="px-4 py-3 text-right font-semibold text-slate-100">{{ $totalTimeliness }}</th>
                                    <th class="px-4 py-3 text-right font-semibold text-slate-100">{{ number_format($totalTimelinessAvg, 2, '.', '') }}</th>
                                </tr>
                            </tfoot>
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
        <div class="w-full max-w-5xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">IPCR Accomplishment Report</p>
                    <h3 class="text-lg font-semibold text-white">IPCR Accomplishment Preview &mdash; {{ $periodLabelValue }}</h3>
                    <p class="text-sm text-slate-400 mt-1">System-generated accomplishments derived from SMPOR totals (Stage II).</p>
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
                        <p class="mt-1 font-semibold">SMPOR totals</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <h4 class="text-base font-semibold text-white">Accomplishment Summary</h4>
                    <div class="overflow-x-auto rounded-xl border border-slate-800">
                        <table class="min-w-full text-left text-sm text-slate-200">
                            <thead class="bg-slate-950/70 text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-4 py-3">MFO</th>
                                    <th class="px-4 py-3">Accomplishment Summary</th>
                                    <th class="px-4 py-3">Evidence</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse ($ipcrRows as $ipcrRow)
                                    <tr class="bg-slate-900/40">
                                        <td class="px-4 py-3 font-semibold">{{ $ipcrRow['mfo'] }}</td>
                                        <td class="px-4 py-3 text-slate-300">{{ $ipcrRow['accomplishment_summary'] }}</td>
                                        <td class="px-4 py-3 text-slate-300">{{ $ipcrRow['evidence_label'] }}</td>
                                    </tr>
                                @empty
                                    <tr class="bg-slate-900/40">
                                        <td colspan="3" class="px-4 py-3 text-center text-slate-400">No SMPOR totals available for IPCR preview.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 text-xs text-slate-400">
                        Final IPCR rating is completed in Stage III. This preview is monitoring-derived and read-only.
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
                let activeAction = null;

                const modalContent = {
                    'confirm-submission': {
                        title: 'Submit Accomplishments',
                        body: 'Confirm formal submission of SMPOR and IPCR accomplishments for Stage III evaluation. After submission, uploads and remarks become read-only.',
                        showConfirm: true,
                    },
                };

                function isAnyModalOpen() {
                    const actionOpen = modal && modal.classList.contains('flex');
                    const previewOpen = previewModals.some((item) => item.classList.contains('flex'));
                    return actionOpen || previewOpen;
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

                function openPreviewModal(modalId) {
                    if (!modalId) return;
                    const target = document.getElementById(modalId);
                    if (!target) return;
                    previewModals.forEach((item) => {
                        if (item !== target) {
                            item.classList.add('hidden');
                            item.classList.remove('flex');
                        }
                    });
                    target.classList.remove('hidden');
                    target.classList.add('flex');
                    syncBodyScroll();
                }

                function closePreviewModal(modalEl) {
                    if (!modalEl) return;
                    modalEl.classList.add('hidden');
                    modalEl.classList.remove('flex');
                    syncBodyScroll();
                }

                function closeAllPreviewModals() {
                    previewModals.forEach((item) => closePreviewModal(item));
                }

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
                        closeAllPreviewModals();
                        closeModal();
                    }
                });
            });
        </script>
    @endpush

@endsection
