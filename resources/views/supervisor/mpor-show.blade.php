@extends('layouts.supervisor')

@section('main-content')
    @php
        $meta = $meta ?? [];
        $sectionLabels = $sectionLabels ?? ['core' => 'Core Functions (80%)', 'support' => 'Support Functions (20%)'];
        $sectionRows = $sectionRows ?? ['core' => [], 'support' => []];
        $grandTotals = $grandTotals ?? ['qty' => [1 => 0, 2 => 0, 3 => 0, 4 => 0], 'qtyTotal' => 0];
        $kpis = $kpis ?? ['includedRated' => 0, 'excluded' => 0];

        $status = strtolower((string) ($meta['status'] ?? ''));
        $statusBadgeClass = match ($status) {
            'submitted' => 'border-blue-500/30 bg-blue-500/10 text-blue-200',
            'approved' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200',
            'endorsed' => 'border-violet-500/30 bg-violet-500/10 text-violet-200',
            'returned' => 'border-rose-500/30 bg-rose-500/10 text-rose-200',
            default => 'border-slate-700 bg-slate-800 text-slate-200',
        };
    @endphp

    <section class="space-y-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Monthly Performance Output Report</p>
                <h1 class="mt-1 text-xl font-bold leading-tight text-white sm:text-2xl md:text-3xl">MONTHLY PERFORMANCE OUTPUT REPORT</h1>
                <p class="mt-1 text-sm text-slate-400 md:text-base">Read-only mirror of locked ORS entries with supervisor ratings.</p>
                <span class="mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $statusBadgeClass }}">
                    {{ strtoupper($status ?: '--') }}
                </span>
            </div>
            <a href="{{ route('supervisor.mpor') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                Back to MPOR List
            </a>
        </div>

        @if (!empty($meta['returnRemarks']))
            <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
                <p class="font-semibold">Returned remarks</p>
                <p class="mt-1 whitespace-pre-wrap text-rose-100/90">{{ $meta['returnRemarks'] }}</p>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-2 text-xs uppercase tracking-[0.2em] text-white sm:grid-cols-3">
            <div class="rounded-lg border border-gray-700 bg-slate-900/40 px-3 py-2">
                <p class="text-[0.6rem] text-slate-500">NAME</p>
                <p class="mt-0.5 truncate text-sm font-semibold normal-case tracking-normal">{{ $meta['employeeName'] ?? '--' }}</p>
            </div>
            <div class="rounded-lg border border-gray-700 bg-slate-900/40 px-3 py-2">
                <p class="text-[0.6rem] text-slate-500">OFFICE / DIVISION</p>
                <p class="mt-0.5 truncate text-sm font-semibold normal-case tracking-normal">{{ $meta['officeName'] ?? '--' }}</p>
            </div>
            <div class="col-span-2 rounded-lg border border-gray-700 bg-slate-900/40 px-3 py-2 sm:col-span-1">
                <p class="text-[0.6rem] text-slate-500">MONTH</p>
                <p class="mt-0.5 truncate text-sm font-semibold normal-case tracking-normal">{{ $meta['monthLabel'] ?? '--' }}</p>
            </div>
        </div>

        <div class="hidden lg:block">
            <div class="overflow-hidden rounded-2xl border border-gray-700 bg-slate-900/40">
                <div class="overflow-x-auto max-h-[38rem]">
                    <table class="min-w-full text-[0.75rem] text-slate-200">
                        <thead class="sticky top-0 z-20 bg-slate-900/95">
                            <tr class="text-left text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                                <th class="sticky left-0 z-30 whitespace-nowrap px-3 py-3 align-bottom bg-slate-900/95" rowspan="2">Output / Task</th>
                                <th class="border-l border-gray-700 px-3 py-3 text-center" colspan="5">Efficiency / Quantity</th>
                                <th class="border-l border-gray-700 px-3 py-3 text-center" colspan="5">Quality / Effectiveness</th>
                                <th class="border-l border-gray-700 px-3 py-3 text-center" colspan="5">Timeliness</th>
                            </tr>
                            <tr class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">
                                @for ($i = 0; $i < 3; $i++)
                                    <th class="{{ $i === 0 ? 'border-l border-gray-700' : '' }} px-2 py-2 text-right">W1</th>
                                    <th class="px-2 py-2 text-right">W2</th>
                                    <th class="px-2 py-2 text-right">W3</th>
                                    <th class="px-2 py-2 text-right">W4</th>
                                    <th class="px-2 py-2 text-right font-semibold">Total</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-[0.75rem]">
                            @foreach ($sectionLabels as $sectionKey => $sectionLabel)
                                <tr class="bg-slate-800/40 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                    <td class="sticky left-0 z-10 border-r border-gray-700 bg-slate-800/80 px-3 py-2 font-semibold text-slate-100" colspan="16">{{ $sectionLabel }}</td>
                                </tr>
                                @forelse ($sectionRows[$sectionKey] ?? [] as $row)
                                    <tr>
                                        <td class="sticky left-0 z-10 max-w-[20rem] border-r border-gray-700 bg-slate-900/95 px-3 py-3 font-semibold text-white">
                                            <span class="block truncate" title="{{ $row['label'] }}">{{ $row['label'] }}</span>
                                        </td>
                                        @foreach (['qty', 'qual', 'time'] as $groupIndex => $group)
                                            <td class="{{ $groupIndex === 0 ? 'border-l border-gray-700' : 'border-l border-gray-700' }} px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, "{$group}.1", 0), 0) }}</td>
                                            <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, "{$group}.2", 0), 0) }}</td>
                                            <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, "{$group}.3", 0), 0) }}</td>
                                            <td class="px-2 py-3 text-right tabular-nums">{{ number_format(data_get($row, "{$group}.4", 0), 0) }}</td>
                                            <td class="px-2 py-3 text-right font-semibold tabular-nums">{{ number_format(data_get($row, "{$group}Total", 0), 0) }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr><td colspan="16" class="px-3 py-6 text-center text-sm text-slate-500">No entries available.</td></tr>
                                @endforelse
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-3 lg:hidden">
            @foreach ($sectionLabels as $sectionKey => $sectionLabel)
                <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4">
                    <p class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-400">{{ $sectionLabel }}</p>
                    @forelse ($sectionRows[$sectionKey] ?? [] as $row)
                        <div class="mt-3 rounded-xl border border-gray-700 bg-slate-900/40 p-3">
                            <p class="text-sm font-semibold text-white">{{ $row['label'] }}</p>
                            <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-2">
                                    <p class="text-[0.55rem] uppercase tracking-[0.25em] text-slate-500">Quantity</p>
                                    <p class="mt-1 text-sm font-semibold text-white">{{ number_format(data_get($row, 'qtyTotal', 0), 0) }}</p>
                                </div>
                                <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-2">
                                    <p class="text-[0.55rem] uppercase tracking-[0.25em] text-slate-500">Quality</p>
                                    <p class="mt-1 text-sm font-semibold text-white">{{ number_format(data_get($row, 'qualTotal', 0), 0) }}</p>
                                </div>
                                <div class="rounded-lg border border-gray-700 bg-slate-900/40 p-2">
                                    <p class="text-[0.55rem] uppercase tracking-[0.25em] text-slate-500">Timeliness</p>
                                    <p class="mt-1 text-sm font-semibold text-white">{{ number_format(data_get($row, 'timeTotal', 0), 0) }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="mt-3 text-sm text-slate-500">No entries available.</p>
                    @endforelse
                </div>
            @endforeach
        </div>

        <p class="mt-3 text-xs text-slate-400">MPOR points = Quantity Ã— Supervisor Rating (Q/T). Only rated ORS entries with supervisor ratings are included.</p>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4 text-xs uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-[0.6rem] tracking-[0.3em] text-slate-500">
                    <span>Week 1</span><span>Week 2</span><span>Week 3</span><span>Week 4</span><span>Total</span>
                </div>
                <div class="mt-2 grid grid-cols-5 text-center text-sm font-semibold text-white">
                    <span>{{ number_format(data_get($grandTotals, 'qty.1', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qty.2', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qty.3', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qty.4', 0), 0) }}</span>
                    <span>{{ number_format(data_get($grandTotals, 'qtyTotal', 0), 0) }}</span>
                </div>
                <div class="my-5 border-t border-slate-700/70"></div>
                <div class="mt-3 space-y-2 text-[0.65rem] tracking-[0.2em] text-slate-500">
                    <div class="flex items-center justify-between gap-3">
                        <span class="min-w-0">Included ORS entries (rated)</span>
                        <span class="shrink-0 font-semibold text-white">{{ number_format((int) ($kpis['includedRated'] ?? 0), 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="min-w-0">Excluded entries (unrated/draft/missing)</span>
                        <span class="shrink-0 font-semibold text-white">{{ number_format((int) ($kpis['excluded'] ?? 0), 0) }}</span>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                <div class="flex items-center justify-between text-sm font-semibold text-white">
                    <span>Confirmed:</span>
                    <span class="text-slate-500">Stage II</span>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1 rounded-xl border border-gray-700 bg-slate-900/40 p-3 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Supervisor</p>
                        <p class="text-sm font-semibold text-white normal-case tracking-normal">{{ auth()->user()->name ?? '--' }}</p>
                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                    </div>
                    <div class="space-y-1 rounded-xl border border-gray-700 bg-slate-900/40 p-3 text-center">
                        <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Employee</p>
                        <p class="text-sm font-semibold text-white normal-case tracking-normal">{{ $meta['employeeName'] ?? '--' }}</p>
                        <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="mpor-action-section" class="rounded-2xl border border-gray-700 bg-slate-900/40 p-4">
            @if ($status === 'submitted')
                <div class="space-y-4">
                    <div>
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-400">Supervisor Action</p>
                        <p class="mt-1 text-sm text-slate-400">Approve this MPOR or return it with optional remarks.</p>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[0.65rem] font-semibold uppercase tracking-[0.3em] text-slate-400">Return Remarks (Optional)</label>
                        <textarea id="returnRemarksInput" rows="3"
                            style="background:#0b1220;color:#e2e8f0;"
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400"
                            placeholder="Reason for returning this MPOR..."></textarea>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-700 pt-3">
                        <button type="button" id="btnReturnMpor"
                            class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm font-semibold text-rose-200 hover:bg-rose-500/20">
                            Return to Employee
                        </button>
                        <button type="button" id="btnApproveMpor"
                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                            Approve MPOR
                        </button>
                    </div>
                </div>
            @elseif ($status === 'approved')
                <div class="flex items-center justify-end">
                    <button type="button" id="btnEndorseMpor"
                        class="rounded-lg border border-violet-500/40 bg-violet-500/10 px-4 py-2 text-sm font-semibold text-violet-200 hover:bg-violet-500/20">
                        Endorse to Department Head
                    </button>
                </div>
            @else
                <div class="text-sm text-slate-400">This MPOR is view-only.</div>
            @endif
        </div>

        <script>
        (function() {
            const csrfToken = '{{ csrf_token() }}';
            const approveUrl = '{{ route("supervisor.mpor.approve", $mpor) }}';
            const endorseUrl = '{{ route("supervisor.mpor.endorse", $mpor) }}';
            const returnUrl = '{{ route("supervisor.mpor.return", $mpor) }}';
            const actionSection = document.getElementById('mpor-action-section');

            function showSnackbar(msg, isError = false) {
                const el = document.createElement('div');
                el.className = `fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] rounded-lg px-5 py-3 text-sm font-semibold shadow-lg ${isError ? 'border border-rose-500/30 bg-rose-500/10 text-rose-200' : 'border border-emerald-500/30 bg-emerald-500/10 text-emerald-200'}`;
                el.innerHTML = `<i class="fa-solid ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'} mr-2"></i>${msg}`;
                document.body.appendChild(el);
                setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 3000);
            }

            function setLoading(btn, loading) {
                if (loading) {
                    btn.disabled = true;
                    btn.dataset.origText = btn.textContent;
                    btn.innerHTML = '<svg class="inline h-4 w-4 animate-spin mr-1" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="opacity-75"/></svg> Processing...';
                } else {
                    btn.disabled = false;
                    btn.textContent = btn.dataset.origText || 'Done';
                }
            }

            function updateStatus(newStatus) {
                const badge = document.querySelector('span.inline-flex.rounded-full.border.uppercase');
                if (badge) {
                    badge.textContent = newStatus.toUpperCase();
                    badge.className = 'mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] ' +
                        (newStatus === 'approved' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200' :
                         newStatus === 'endorsed' ? 'border-violet-500/30 bg-violet-500/10 text-violet-200' :
                         newStatus === 'returned' ? 'border-rose-500/30 bg-rose-500/10 text-rose-200' :
                         'border-slate-700 bg-slate-800 text-slate-200');
                }

                if (newStatus === 'approved') {
                    actionSection.innerHTML = `
                        <div class="flex items-center justify-end">
                            <button type="button" id="btnEndorseMpor"
                                class="rounded-lg border border-violet-500/40 bg-violet-500/10 px-4 py-2 text-sm font-semibold text-violet-200 hover:bg-violet-500/20">
                                Endorse to Department Head
                            </button>
                        </div>`;
                    bindEndorse();
                    showSnackbar('MPOR approved.');
                } else if (newStatus === 'endorsed') {
                    actionSection.innerHTML = '<div class="text-center text-sm text-emerald-300"><i class="fa-solid fa-check-circle mr-1"></i> MPOR endorsed to Department Head.</div>';
                    showSnackbar('MPOR endorsed to Department Head.');
                } else if (newStatus === 'returned') {
                    actionSection.innerHTML = '<div class="text-center text-sm text-rose-300"><i class="fa-solid fa-rotate-left mr-1"></i> MPOR returned to employee.</div>';
                    showSnackbar('MPOR returned to employee.');
                }
            }

            async function postAction(url, body, btn) {
                setLoading(btn, true);
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify(body || {})
                    });
                    if (res.ok) return true;
                    const data = await res.json().catch(() => ({}));
                    showSnackbar(data.message || 'Action failed.', true);
                    setLoading(btn, false);
                    return false;
                } catch (e) {
                    showSnackbar('Network error.', true);
                    setLoading(btn, false);
                    return false;
                }
            }

            function bindApprove() {
                const btn = document.getElementById('btnApproveMpor');
                if (!btn) return;
                btn.addEventListener('click', async () => {
                    const ok = await postAction(approveUrl, {}, btn);
                    if (ok) updateStatus('approved');
                });
            }

            function bindReturn() {
                const btn = document.getElementById('btnReturnMpor');
                if (!btn) return;
                btn.addEventListener('click', async () => {
                    const remarks = document.getElementById('returnRemarksInput')?.value || '';
                    const ok = await postAction(returnUrl, { return_remarks: remarks }, btn);
                    if (ok) updateStatus('returned');
                });
            }

            function bindEndorse() {
                const btn = document.getElementById('btnEndorseMpor');
                if (!btn) return;
                btn.addEventListener('click', async () => {
                    const ok = await postAction(endorseUrl, {}, btn);
                    if (ok) updateStatus('endorsed');
                });
            }

            bindApprove();
            bindReturn();
            bindEndorse();
        })();
        </script>
    </section>
@endsection
