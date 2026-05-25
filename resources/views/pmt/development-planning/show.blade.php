@extends('layouts.pmt')

@section('main-content')
    <section class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('pmt.development-planning.index') }}" class="mb-2 inline-flex items-center text-sm font-semibold text-blue-400 hover:text-blue-300">
                    &larr; Back to Development Planning
                </a>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Stage IV</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Development Planning Draft</h1>
                <p class="text-sm text-slate-400">Placeholder PMT record for a low-performing employee awaiting the final client IDP format.</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-slate-900/40 px-4 py-3 text-right">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Current Status</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $statusLabel }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-5 xl:col-span-2">
                <h2 class="text-lg font-semibold text-white">Released Performance Context</h2>
                <p class="mt-1 text-sm text-slate-400">This snapshot is taken from the official released Stage III IPCR result.</p>

                <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Employee</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $developmentPlan->employee?->name ?? '--' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Office</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $developmentPlan->office?->name ?? '--' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Position</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $developmentPlan->employee?->position ?? '--' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Performance Period</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $developmentPlan->performancePeriod?->name ?? '--' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Official Score Snapshot</p>
                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ number_format((float) $developmentPlan->source_score, 2) }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                        <p class="text-[11px] uppercase tracking-[0.22em] text-slate-500">Official Rating Snapshot</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $developmentPlan->source_rating }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-xl border border-dashed border-slate-700 bg-slate-950/60 p-5">
                    <h3 class="text-sm font-semibold text-white">IDP Format Pending</h3>
                    <p class="mt-2 text-sm text-slate-400">Detailed IDP fields are intentionally not implemented yet. This draft exists so PMT can queue the employee for development planning while waiting for the final client-provided IDP format.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-5">
                <h2 class="text-lg font-semibold text-white">PMT Draft Control</h2>
                <form method="POST" action="{{ route('pmt.development-planning.status', $developmentPlan) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs text-slate-400">Draft Status</label>
                        <select name="status" class="manager-filter-select mt-2 w-full rounded-lg border px-3 py-2 text-sm">
                            <option value="{{ \App\Models\DevelopmentPlan::STATUS_DRAFT }}" @selected($developmentPlan->status === \App\Models\DevelopmentPlan::STATUS_DRAFT)>Draft</option>
                            <option value="{{ \App\Models\DevelopmentPlan::STATUS_PENDING_DETAILS }}" @selected($developmentPlan->status === \App\Models\DevelopmentPlan::STATUS_PENDING_DETAILS)>Pending Details</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400">PMT Remarks</label>
                        <textarea name="pmt_remarks" rows="5" class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100" style="background-color:#020617;color:#f1f5f9;border-color:#334155;">{{ old('pmt_remarks', $developmentPlan->pmt_remarks) }}</textarea>
                    </div>
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 px-4 py-3 text-sm text-slate-400">
                        This placeholder draft is not yet submitted to Learning &amp; Development. External submission will be added after the final IDP format and integration flow are confirmed.
                    </div>
                    <div class="text-right">
                        <button type="submit" class="rounded-lg border border-blue-600 bg-blue-600/20 px-4 py-2 text-sm font-semibold text-blue-300 hover:bg-blue-600/30">
                            Update Draft
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
