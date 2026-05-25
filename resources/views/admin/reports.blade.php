@extends('layouts.admin')

@section('main-content')
    @php
        $reportOptions = $reports ?? [];
        $selectedReport = $filters['report'] ?? '';
        $selectedPeriod = $filters['performance_period_id'] ?? '';
        $selectedOffice = $filters['office_id'] ?? '';
        $selectedDefinition = collect($reportOptions)->firstWhere('slug', $selectedReport);
        $previewEnabled = $selectedPeriod !== '' && $selectedOffice !== '';
    @endphp

    <section class="space-y-5 px-3 md:px-6">
        <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-5 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-white sm:text-2xl">Reports</h1>
                    <p class="mt-1 text-sm text-slate-300">Generate office-period PMS report PDFs for the core report set from a single admin workspace.</p>
                </div>
                <div class="rounded-xl border border-blue-400/20 bg-blue-500/10 px-4 py-3 text-sm text-blue-100">
                    Preview is the primary action. Download becomes available from the same selected scope.
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4">
                <p class="text-xs uppercase tracking-[0.28em] text-slate-400">Available Reports</p>
                <p class="mt-2 text-3xl font-semibold text-white">{{ count($reportOptions) }}</p>
            </div>
            <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4">
                <p class="text-xs uppercase tracking-[0.28em] text-slate-400">Selected Report</p>
                <p class="mt-2 text-lg font-semibold text-white">{{ $selectedDefinition['label'] ?? 'None selected' }}</p>
            </div>
            <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4">
                <p class="text-xs uppercase tracking-[0.28em] text-slate-400">Performance Period</p>
                <p class="mt-2 text-lg font-semibold text-white">
                    {{ optional(collect($periods)->firstWhere('id', (int) $selectedPeriod))->name ?? 'Not selected' }}
                </p>
            </div>
            <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4">
                <p class="text-xs uppercase tracking-[0.28em] text-slate-400">Office</p>
                <p class="mt-2 text-lg font-semibold text-white">
                    {{ optional(collect($offices)->firstWhere('id', (int) $selectedOffice))->name ?? 'Not selected' }}
                </p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.reports') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div>
                    <label for="report" class="mb-1 block text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Report Type</label>
                    <select id="report" name="report" style="background-color:#020617;color:#e2e8f0;color-scheme:dark;" class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none focus:border-blue-400">
                        <option value="">Select report</option>
                        @foreach ($reportOptions as $option)
                            <option value="{{ $option['slug'] }}" @selected($selectedReport === $option['slug'])>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="performance_period_id" class="mb-1 block text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Performance Period</label>
                    <select id="performance_period_id" name="performance_period_id" style="background-color:#020617;color:#e2e8f0;color-scheme:dark;" class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none focus:border-blue-400">
                        <option value="">Select period</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected((string) $selectedPeriod === (string) $period->id)>
                                {{ $period->name }} ({{ optional($period->start_date)->format('M d, Y') }} - {{ optional($period->end_date)->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="office_id" class="mb-1 block text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Office</label>
                    <select id="office_id" name="office_id" style="background-color:#020617;color:#e2e8f0;color-scheme:dark;" class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none focus:border-blue-400">
                        <option value="">Select office</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}" @selected((string) $selectedOffice === (string) $office->id)>{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-500">
                        Apply Scope
                    </button>
                    <a href="{{ route('admin.reports') }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-slate-950 px-4 py-3 text-sm font-semibold text-slate-200 hover:bg-slate-800">
                        Reset
                    </a>
                </div>
            </form>
            @error('performance_period_id')
                <p class="mt-3 text-sm text-rose-300">{{ $message }}</p>
            @enderror
            @error('office_id')
                <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            @foreach ($reportOptions as $option)
                @php
                    $isSelected = $selectedReport === $option['slug'];
                    $query = [
                        'report' => $option['slug'],
                        'performance_period_id' => $selectedPeriod,
                        'office_id' => $selectedOffice,
                    ];
                @endphp
                <article class="rounded-2xl border {{ $isSelected ? 'border-blue-400/40 bg-blue-500/10' : 'border-gray-700 bg-slate-900/40' }} p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">{{ strtoupper($option['slug']) }}</p>
                            <h2 class="mt-2 text-lg font-semibold text-white">{{ $option['label'] }}</h2>
                            <p class="mt-2 text-sm text-slate-300">
                                Generate an office-period PDF for {{ strtolower($option['label']) }} using the selected scope.
                            </p>
                        </div>
                        @if ($isSelected)
                            <span class="rounded-full bg-blue-500/20 px-3 py-1 text-xs font-semibold text-blue-200">Selected</span>
                        @endif
                    </div>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('admin.reports', ['report' => $option['slug'], 'performance_period_id' => $selectedPeriod, 'office_id' => $selectedOffice]) }}"
                            class="inline-flex items-center rounded-xl border border-white/10 bg-slate-950 px-4 py-2.5 text-sm font-semibold text-slate-200 hover:bg-slate-800">
                            Select
                        </a>
                        <a href="{{ route('admin.reports.preview', array_merge(['report' => $option['slug']], $query)) }}"
                            class="inline-flex items-center rounded-xl {{ $previewEnabled ? 'bg-blue-600 text-white hover:bg-blue-500' : 'cursor-not-allowed bg-slate-800 text-slate-500 pointer-events-none' }} px-4 py-2.5 text-sm font-semibold">
                            Preview PDF
                        </a>
                        <a href="{{ route('admin.reports.download', array_merge(['report' => $option['slug']], $query)) }}"
                            class="inline-flex items-center rounded-xl {{ $previewEnabled ? 'border border-emerald-400/30 bg-emerald-500/10 text-emerald-200 hover:bg-emerald-500/20' : 'pointer-events-none border border-white/10 bg-slate-900 text-slate-500' }} px-4 py-2.5 text-sm font-semibold">
                            Download PDF
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
