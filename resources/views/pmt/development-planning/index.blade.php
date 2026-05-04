@extends('layouts.pmt')

@section('main-content')
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Stage IV</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Development Planning</h1>
                <p class="text-sm text-slate-400">PMT queue for released low-performing employees who need Learning & Development follow-up.</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Active Period</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $activePeriod?->name ?? '--' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Low-Performing Employees</p>
                <p class="mt-1 text-3xl font-semibold text-rose-300">{{ $summaryCounts['low_performers'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Unsatisfactory or Poor</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Drafts Created</p>
                <p class="mt-1 text-3xl font-semibold text-blue-300">{{ $summaryCounts['drafts_created'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Placeholder development plans</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Pending Details</p>
                <p class="mt-1 text-3xl font-semibold text-amber-300">{{ $summaryCounts['pending_details'] ?? 0 }}</p>
                <p class="text-xs text-slate-400">Awaiting final client IDP format</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-blue-500/30 bg-blue-500/10 text-blue-300">
                    <i class="fa-solid fa-circle-info"></i>
                </span>
                <div>
                    <h2 class="text-lg font-semibold text-white">Stage IV v1.5</h2>
                    <p class="mt-1 text-sm text-slate-400">This module creates and manages placeholder PMT development-planning drafts. Detailed IDP fields and L&D submission are intentionally deferred until the final client format is available.</p>
                    @if ($infoMessage)
                        <p class="mt-3 text-sm font-medium text-amber-300">{{ $infoMessage }}</p>
                    @endif
                </div>
            </div>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
            <div class="border-b border-slate-800 px-5 py-4">
                <h2 class="text-lg font-semibold text-white">Low-Performer Queue</h2>
                <p class="mt-1 text-sm text-slate-400">Released employee IPCR results classified as Unsatisfactory or Poor, with current development-planning draft status.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-left">Employee</th>
                            <th class="px-5 py-4 text-left">Office</th>
                            <th class="px-5 py-4 text-left">Position</th>
                            <th class="px-5 py-4 text-left">Period</th>
                            <th class="px-5 py-4 text-center">Official Score</th>
                            <th class="px-5 py-4 text-left">Official Rating</th>
                            <th class="px-5 py-4 text-left">Released</th>
                            <th class="px-5 py-4 text-left">Plan Status</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($candidates as $candidate)
                            <tr class="hover:bg-slate-950/40">
                                <td class="px-5 py-4 font-medium text-white">{{ $candidate['employee_name'] }}</td>
                                <td class="px-5 py-4 text-slate-300">{{ $candidate['office_name'] }}</td>
                                <td class="px-5 py-4 text-slate-300">{{ $candidate['position'] }}</td>
                                <td class="px-5 py-4 text-slate-300">{{ $candidate['period_name'] }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-rose-300">{{ number_format((float) $candidate['official_score'], 2) }}</td>
                                <td class="px-5 py-4 text-slate-200">{{ $candidate['official_rating'] }}</td>
                                <td class="px-5 py-4 text-slate-400">{{ optional($candidate['released_at'])->format('M d, Y h:i A') ?? '--' }}</td>
                                <td class="px-5 py-4">
                                    @php
                                        $status = (string) ($candidate['development_plan_status'] ?? '');
                                        $statusClass = match ($status) {
                                            \App\Models\DevelopmentPlan::STATUS_DRAFT => 'border-blue-500/30 bg-blue-500/10 text-blue-300',
                                            \App\Models\DevelopmentPlan::STATUS_PENDING_DETAILS => 'border-amber-500/30 bg-amber-500/10 text-amber-300',
                                            default => 'border-slate-700 bg-slate-950/70 text-slate-300',
                                        };
                                    @endphp
                                    <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $candidate['development_plan_status_label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if ($candidate['development_plan_id'])
                                        <a href="{{ route('pmt.development-planning.show', $candidate['development_plan_id']) }}" class="inline-flex items-center rounded-lg border border-blue-600 bg-blue-600/15 px-3 py-2 text-xs font-semibold text-blue-300 transition hover:bg-blue-600/25">
                                            Open Draft
                                        </a>
                                    @else
                                        <form method="POST" action="{{ route('pmt.development-planning.store') }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="ipcr_id" value="{{ $candidate['ipcr_id'] }}">
                                            <button type="submit" class="inline-flex items-center rounded-lg border border-emerald-600 bg-emerald-600/15 px-3 py-2 text-xs font-semibold text-emerald-300 transition hover:bg-emerald-600/25">
                                                Create Draft
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-10 text-center text-sm text-slate-400">No low-performing employees identified for the active period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </section>
@endsection
