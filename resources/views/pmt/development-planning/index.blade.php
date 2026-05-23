@extends('layouts.pmt')

@section('main-content')
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="mt-1 text-2xl font-bold text-white">Development Planning</h1>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-right">
                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Active Period</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $activePeriod?->name ?? '--' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Low Employees</p>
                <p class="mt-1 text-3xl font-semibold text-rose-300">{{ $summaryCounts['low_employees'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Low Offices</p>
                <p class="mt-1 text-3xl font-semibold text-rose-300">{{ $summaryCounts['low_offices'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Drafts Created</p>
                <p class="mt-1 text-3xl font-semibold text-blue-300">{{ $summaryCounts['drafts_created'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Pending Details</p>
                <p class="mt-1 text-3xl font-semibold text-amber-300">{{ $summaryCounts['pending_details'] ?? 0 }}</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
            <div class="border-b border-slate-800 px-5 py-4">
                <h2 class="text-lg font-semibold text-white">Employee Queue</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-left">Employee</th>
                            <th class="px-5 py-4 text-left">Office</th>
                            <th class="px-5 py-4 text-center">Official Score</th>
                            <th class="px-5 py-4 text-left">Plan Status</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($candidates as $candidate)
                            <tr class="hover:bg-slate-950/40">
                                <td class="px-5 py-4 font-medium text-white">{{ $candidate['employee_name'] }}</td>
                                <td class="px-5 py-4 text-slate-300">{{ $candidate['office_name'] }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-2.5 py-0.5 text-xs font-semibold text-rose-300 border border-rose-500/20">
                                        {{ number_format((float) $candidate['official_score'], 2) }}
                                    </span>
                                </td>
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
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                            data-open-details
                                            data-details='@json($candidate)'
                                            class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-2.5 py-2 text-xs font-semibold text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                                            Details
                                        </button>
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
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">No low-performing employees identified for the active period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
            <div class="border-b border-slate-800 px-5 py-4">
                <h2 class="text-lg font-semibold text-white">Office Queue</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/60 text-xs uppercase tracking-[0.22em] text-slate-500">
                        <tr>
                            <th class="px-5 py-4 text-left">Office</th>
                            <th class="px-5 py-4 text-left">Department Head</th>
                            <th class="px-5 py-4 text-center">Official Score</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($lowOffices as $row)
                            <tr class="hover:bg-slate-950/40">
                                <td class="px-5 py-4 font-medium text-white">{{ $row['office_name'] }}</td>
                                <td class="px-5 py-4 text-slate-300">{{ $row['department_head_name'] }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-2.5 py-0.5 text-xs font-semibold text-rose-300 border border-rose-500/20">
                                        {{ number_format((float) $row['official_score'], 2) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button type="button"
                                        data-open-details
                                        data-details='@json($row)'
                                        class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-400">No low-performing offices identified for the active period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </section>

    <!-- Details Modal -->
    <div id="performer-details-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-4">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-rose-300">Stage III Result Details</p>
                    <h3 id="modal-title" class="text-xl font-bold text-white">Performer Details</h3>
                </div>
                <button type="button" data-close-modal class="text-2xl leading-none text-slate-400 transition hover:text-white">&times;</button>
            </div>

            <div class="mt-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Official Score</p>
                        <p id="modal-score" class="mt-1 text-2xl font-bold text-rose-300">--</p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Official Rating</p>
                        <p id="modal-rating" class="mt-1 text-lg font-semibold text-white">--</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div id="modal-office-row">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Office</p>
                        <p id="modal-office" class="mt-0.5 text-sm font-medium text-slate-200">--</p>
                    </div>
                    <div id="modal-extra-row">
                        <p id="modal-extra-label" class="text-[10px] uppercase tracking-[0.2em] text-slate-500">--</p>
                        <p id="modal-extra-value" class="mt-0.5 text-sm font-medium text-slate-200">--</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Performance Period</p>
                        <p id="modal-period" class="mt-0.5 text-sm font-medium text-slate-200">--</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-500">Released On</p>
                        <p id="modal-released" class="mt-0.5 text-sm font-medium text-slate-200">--</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="button" data-close-modal class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('performer-details-modal');
            const closeBtns = modal.querySelectorAll('[data-close-modal]');

            const titleEl = document.getElementById('modal-title');
            const scoreEl = document.getElementById('modal-score');
            const ratingEl = document.getElementById('modal-rating');
            const officeEl = document.getElementById('modal-office');
            const extraLabelEl = document.getElementById('modal-extra-label');
            const extraValueEl = document.getElementById('modal-extra-value');
            const periodEl = document.getElementById('modal-period');
            const releasedEl = document.getElementById('modal-released');

            function openModal(data) {
                const isEmployee = !!data.employee_name;

                titleEl.textContent = isEmployee ? data.employee_name : data.office_name;
                scoreEl.textContent = Number(data.official_score).toFixed(2);
                ratingEl.textContent = data.official_rating;
                officeEl.textContent = data.office_name;

                if (isEmployee) {
                    extraLabelEl.textContent = 'Position';
                    extraValueEl.textContent = data.position || '--';
                    document.getElementById('modal-office-row').classList.remove('hidden');
                } else {
                    extraLabelEl.textContent = 'Department Head';
                    extraValueEl.textContent = data.department_head_name;
                    document.getElementById('modal-office-row').classList.add('hidden');
                }

                periodEl.textContent = data.period_name;
                releasedEl.textContent = data.released_at ? new Date(data.released_at).toLocaleString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                }) : '--';

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }

            document.querySelectorAll('[data-open-details]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const data = JSON.parse(btn.dataset.details);
                    openModal(data);
                });
            });

            closeBtns.forEach(btn => btn.addEventListener('click', closeModal));
            modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        });
    </script>
    @endpush
@endsection
