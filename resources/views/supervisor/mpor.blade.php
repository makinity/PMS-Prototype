@extends('layouts.supervisor')

@section('main-content')
    @php
        $statusMeta = [
            'submitted' => [
                'label' => 'Submitted',
                'badge' => 'border-blue-500/40 bg-blue-500/10 text-blue-200',
            ],
            'approved' => [
                'label' => 'Approved',
                'badge' => 'border-amber-500/40 bg-amber-500/10 text-amber-200',
            ],
            'endorsed' => [
                'label' => 'Endorsed',
                'badge' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200',
            ],
        ];
    @endphp

    <section class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        @if (session('info'))
            <div class="rounded-xl border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-200">
                {{ session('info') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Stage II - Supervisor Review</p>
                <h1 class="mt-1 text-2xl font-bold text-white">MPOR for Review</h1>
                <p class="mt-1 text-sm text-slate-400">Submitted MPORs require approval before endorsement (Stage II)</p>
            </div>

            <form method="GET" action="{{ route('supervisor.mpor.index') }}" class="flex items-center gap-2">
                <label for="mpor-status-filter" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    Filter
                </label>
                <select id="mpor-status-filter"
                    name="status"
                    onchange="this.form.submit()"
                    style="background:#0f172a;color:#e5e7eb;"
                    class="rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold text-slate-200 transition focus:border-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500/40">
                    <option value="submitted" @selected($selectedStatus === 'submitted')>
                        Submitted ({{ $counts['submitted'] ?? 0 }})
                    </option>
                    <option value="approved" @selected($selectedStatus === 'approved')>
                        Approved ({{ $counts['approved'] ?? 0 }})
                    </option>
                    <option value="endorsed" @selected($selectedStatus === 'endorsed')>
                        Endorsed ({{ $counts['endorsed'] ?? 0 }})
                    </option>
                </select>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead>
                        <tr class="bg-slate-950/60 text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3">Office / Unit</th>
                            <th class="px-4 py-3">Month</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse ($mpors as $mpor)
                            @php
                                $status = strtolower((string) ($mpor['status'] ?? 'submitted'));
                                $meta = $statusMeta[$status] ?? $statusMeta['submitted'];
                                $preview = is_array($mpor['preview'] ?? null) ? $mpor['preview'] : ['outputs' => []];
                                $outputs = is_array($preview['outputs'] ?? null) ? $preview['outputs'] : [];
                                $groups = is_array($preview['groups'] ?? null) ? $preview['groups'] : [];

                                if (empty($groups)) {
                                    $coreRows = [];
                                    $supportRows = [];

                                    foreach ($outputs as $output) {
                                        $title = (string) ($output['title'] ?? $output['task_title'] ?? '-');
                                        $row = [
                                            'title' => $title,
                                            'qty' => is_array($output['qty'] ?? null) ? $output['qty'] : [],
                                            'quality' => is_array($output['quality'] ?? null) ? $output['quality'] : [],
                                            'timeliness' => is_array($output['timeliness'] ?? null) ? $output['timeliness'] : [],
                                        ];

                                        if (str_contains(strtolower($title), 'maintenance')) {
                                            $supportRows[] = $row;
                                        } else {
                                            $coreRows[] = $row;
                                        }
                                    }

                                    if (empty($supportRows)) {
                                        $supportRows[] = [
                                            'title' => 'Maintenance of Revenue Records Filing System',
                                            'qty' => ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0],
                                            'quality' => ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0],
                                            'timeliness' => ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0],
                                        ];
                                    }

                                    $groups = [
                                        [
                                            'label' => 'CORE FUNCTIONS',
                                            'weight_label' => '80%',
                                            'rows' => $coreRows,
                                        ],
                                        [
                                            'label' => 'SUPPORT FUNCTIONS',
                                            'weight_label' => '20%',
                                            'rows' => $supportRows,
                                        ],
                                    ];
                                }

                                $week1Total = array_sum(array_map(static fn($row) => (int) ($row['qty']['w1'] ?? 0), $outputs));
                                $week2Total = array_sum(array_map(static fn($row) => (int) ($row['qty']['w2'] ?? 0), $outputs));
                                $week3Total = array_sum(array_map(static fn($row) => (int) ($row['qty']['w3'] ?? 0), $outputs));
                                $week4Total = array_sum(array_map(static fn($row) => (int) ($row['qty']['w4'] ?? 0), $outputs));
                                $grandTotal = array_sum(array_map(static fn($row) => (int) ($row['qty']['total'] ?? 0), $outputs));

                                $summary = is_array($mpor['summary'] ?? null) ? $mpor['summary'] : [
                                    'week1_total' => $week1Total,
                                    'week2_total' => $week2Total,
                                    'week3_total' => $week3Total,
                                    'week4_total' => $week4Total,
                                    'grand_total' => $grandTotal,
                                    'included_entries' => 2,
                                    'excluded_entries' => 3,
                                ];

                                $statusBadgeLabel = match ($status) {
                                    'approved' => 'Approved',
                                    'endorsed' => 'Endorsed',
                                    default => 'Submitted (Locked)',
                                };

                                $payload = [
                                    'id' => $mpor['id'],
                                    'employee' => $mpor['employee'],
                                    'supervisor' => auth()->user()?->name ?? 'Carlo D. Beray',
                                    'office' => $mpor['office'],
                                    'office_division' => $mpor['office'],
                                    'month' => $mpor['month'],
                                    'status' => $meta['label'],
                                    'status_badge_label' => $statusBadgeLabel,
                                    'status_key' => $status,
                                    'submitted_at' => $mpor['submitted_at'] ?? null,
                                    'approved_at' => $mpor['approved_at'] ?? null,
                                    'endorsed_at' => $mpor['endorsed_at'] ?? null,
                                    'preview' => [
                                        'outputs' => $outputs,
                                        'groups' => $groups,
                                    ],
                                    'summary' => $summary,
                                ];
                            @endphp
                            <tr class="text-slate-200 hover:bg-slate-900/60">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-white">{{ $mpor['employee'] }}</p>
                                    @if (!empty($mpor['submitted_at']))
                                        <p class="text-xs text-slate-500">Submitted: {{ $mpor['submitted_at'] }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $mpor['office'] }}</td>
                                <td class="px-4 py-3">{{ $mpor['month'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $meta['badge'] }} inline-flex rounded-full border px-2 py-1 text-xs font-semibold">
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button"
                                            data-open-mpor-view
                                            data-mpor='@json($payload)'
                                            data-modal-target="mporViewModal"
                                            data-modal-toggle="mporViewModal"
                                            class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-slate-800">
                                            View
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">
                                    No MPOR records under the selected status.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="mporViewModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-6xl p-4">
            <div class="relative flex max-h-[90vh] flex-col rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between gap-3 border-b border-slate-800 p-5">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Monthly Performance Output Report</h3>
                        <p class="mt-1 text-xs text-slate-400">Read-only mirror of locked ORS entries with supervisor ratings.</p>
                        <p class="mt-2 text-xs text-slate-500">
                            Submitted at:
                            <span id="mpor-view-submitted-at" class="text-slate-300">-</span>
                        </p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span id="mpor-view-status-pill"
                            class="inline-flex rounded-full border border-slate-700 bg-slate-950/40 px-2 py-1 text-xs font-semibold text-slate-200">
                            Submitted (Locked)
                        </span>
                        <button type="button" data-modal-hide="mporViewModal"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                            <span class="sr-only">Close modal</span>
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="min-h-0 space-y-5 overflow-y-auto p-5">
                    <div class="grid gap-3 text-sm text-slate-300 md:grid-cols-3">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Name</p>
                            <p id="mpor-view-employee" class="mt-1 font-semibold text-white">-</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Office / Division</p>
                            <p id="mpor-view-office" class="mt-1 font-semibold text-white">-</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Month</p>
                            <p id="mpor-view-month" class="mt-1 font-semibold text-white">-</p>
                        </div>
                    </div>

                    <div>
                        <div>
                            <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-950/60">
                                <table class="min-w-full text-[0.75rem] text-slate-200">
                                    <thead>
                                        <tr class="text-left text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                                            <th class="whitespace-nowrap px-3 py-2 align-bottom" rowspan="2">Output / Task</th>
                                            <th class="border-l border-slate-800 px-3 py-2 text-center" colspan="5">Efficiency / Quantity</th>
                                            <th class="border-l border-slate-800 px-3 py-2 text-center" colspan="5">Quality / Effectiveness</th>
                                            <th class="border-l border-slate-800 px-3 py-2 text-center" colspan="5">Timeliness</th>
                                        </tr>
                                        <tr class="text-[0.6rem] uppercase tracking-[0.3em] text-slate-500">
                                            <th class="border-l border-slate-800 px-2 py-1 text-right">W1</th>
                                            <th class="px-2 py-1 text-right">W2</th>
                                            <th class="px-2 py-1 text-right">W3</th>
                                            <th class="px-2 py-1 text-right">W4</th>
                                            <th class="px-2 py-1 text-right font-semibold">Total</th>
                                            <th class="border-l border-slate-800 px-2 py-1 text-right">W1</th>
                                            <th class="px-2 py-1 text-right">W2</th>
                                            <th class="px-2 py-1 text-right">W3</th>
                                            <th class="px-2 py-1 text-right">W4</th>
                                            <th class="px-2 py-1 text-right font-semibold">Total</th>
                                            <th class="border-l border-slate-800 px-2 py-1 text-right">W1</th>
                                            <th class="px-2 py-1 text-right">W2</th>
                                            <th class="px-2 py-1 text-right">W3</th>
                                            <th class="px-2 py-1 text-right">W4</th>
                                            <th class="px-2 py-1 text-right font-semibold">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="mpor-view-outputs" class="divide-y divide-slate-800"></tbody>
                                </table>
                            </div>

                            <p class="mt-3 text-xs text-slate-400">
                                Stage II demo: MPOR points = Quantity &times; Supervisor Rating (Q/T). Batch quantities are treated as single units.
                            </p>

                            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                                <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4 text-xs uppercase tracking-[0.3em] text-slate-400">
                                    <div class="flex items-center justify-between text-[0.6rem] tracking-[0.3em] text-slate-500">
                                        <span>Week 1</span><span>Week 2</span><span>Week 3</span><span>Week 4</span><span>Total</span>
                                    </div>
                                    <div class="mt-2 grid grid-cols-5 text-center text-sm font-semibold text-white">
                                        <span id="mpor-summary-w1">0</span>
                                        <span id="mpor-summary-w2">0</span>
                                        <span id="mpor-summary-w3">0</span>
                                        <span id="mpor-summary-w4">0</span>
                                        <span id="mpor-summary-total">0</span>
                                    </div>
                                    <div class="my-5 border-t border-slate-700/70"></div>
                                    <div class="space-y-2 text-[0.65rem] tracking-[0.2em] text-slate-500">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="min-w-0">Included ORS Entries (Rated)</span>
                                            <span id="mpor-summary-included" class="shrink-0 font-semibold text-white">0</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="min-w-0">Excluded Entries (Unrated/Draft/Missing)</span>
                                            <span id="mpor-summary-excluded" class="shrink-0 font-semibold text-white">0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                    <div class="flex items-center justify-between text-sm font-semibold text-white">
                                        <span>Confirmed:</span>
                                        <span class="text-slate-500">Stage II</span>
                                    </div>
                                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                        <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                                            <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Supervisor</p>
                                            <p id="mpor-cert-supervisor-name" class="text-sm font-semibold text-white normal-case tracking-normal">-</p>
                                            <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                                        </div>
                                        <div class="space-y-1 rounded-xl border border-slate-800 bg-slate-900/40 p-3 text-center">
                                            <p class="text-[0.55rem] uppercase tracking-[0.3em] text-slate-500">Employee</p>
                                            <p id="mpor-cert-employee-name" class="text-sm font-semibold text-white normal-case tracking-normal">-</p>
                                            <p class="text-[0.6rem] text-slate-500 normal-case tracking-normal">Signature over printed name</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 grid gap-2 text-[0.6rem] normal-case tracking-normal text-slate-400 sm:grid-cols-2">
                                        <p>Supervisor Date: <span id="mpor-cert-supervisor-date" class="text-slate-200">-</span></p>
                                        <p>Employee Date: <span id="mpor-cert-employee-date" class="text-slate-200">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end border-t border-slate-800 p-5">
                    <button type="button" id="mpor-view-approve-btn"
                        data-open-mpor-approve
                        data-modal-hide="mporViewModal"
                        data-modal-target="mporApproveConfirmModal"
                        data-modal-toggle="mporApproveConfirmModal"
                        class="mr-2 hidden rounded-lg border border-amber-500/40 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-500/20">
                        Approve
                    </button>
                    <button type="button" id="mpor-view-endorse-btn"
                        data-open-mpor-endorse
                        data-modal-hide="mporViewModal"
                        data-modal-target="mporEndorseConfirmModal"
                        data-modal-toggle="mporEndorseConfirmModal"
                        class="mr-2 hidden rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-500/20">
                        Endorse
                    </button>
                    <button type="button" data-modal-hide="mporViewModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="mporApproveConfirmModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-lg p-4">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between border-b border-slate-800 p-5">
                    <h3 class="text-lg font-semibold text-white">Approve MPOR</h3>
                    <button type="button" data-modal-hide="mporApproveConfirmModal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <span class="sr-only">Close modal</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="space-y-3 p-5 text-sm text-slate-300">
                    <p>
                        You are about to approve this MPOR.
                    </p>
                    <p>
                        Approval must be completed before endorsement to the Department Head.
                    </p>
                    <p class="font-medium text-white">Proceed?</p>

                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 text-xs text-slate-400">
                        <p>Employee: <span id="approve-mpor-employee" class="text-slate-200">-</span></p>
                        <p class="mt-1">Month: <span id="approve-mpor-month" class="text-slate-200">-</span></p>
                    </div>
                </div>

                <form id="mporApproveForm" method="POST" action=""
                    data-action-template="{{ url('/supervisor/mpor/__ID__/approve') }}"
                    class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                    @csrf
                    <button type="button" data-modal-hide="mporApproveConfirmModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" id="mporProceedApproveBtn"
                        class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-amber-400">
                        <span data-button-spinner
                            class="hidden h-4 w-4 animate-spin rounded-full border-2 border-slate-900/30 border-t-slate-900"></span>
                        <span data-button-label>Proceed Approval</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="mporEndorseConfirmModal" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-lg p-4">
            <div class="relative rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between border-b border-slate-800 p-5">
                    <h3 class="text-lg font-semibold text-white">Endorse MPOR</h3>
                    <button type="button" data-modal-hide="mporEndorseConfirmModal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <span class="sr-only">Close modal</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="space-y-3 p-5 text-sm text-slate-300">
                    <p>
                        You are about to endorse this MPOR to the Department Head.
                    </p>
                    <p>
                        Once endorsed, the MPOR will be locked and will be included in QAR consolidation.
                    </p>
                    <p class="font-medium text-white">Proceed?</p>

                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 text-xs text-slate-400">
                        <p>Employee: <span id="endorse-mpor-employee" class="text-slate-200">-</span></p>
                        <p class="mt-1">Month: <span id="endorse-mpor-month" class="text-slate-200">-</span></p>
                    </div>
                </div>

                <form id="mporEndorseForm" method="POST" action=""
                    data-action-template="{{ url('/supervisor/mpor/__ID__/endorse') }}"
                    class="flex items-center justify-end gap-2 border-t border-slate-800 p-5">
                    @csrf
                    <button type="button" data-modal-hide="mporEndorseConfirmModal"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 transition hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" id="mporProceedEndorseBtn"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        <span data-button-spinner
                            class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        <span data-button-label>Proceed Endorsement</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const viewEmployee = document.getElementById('mpor-view-employee');
                const viewOffice = document.getElementById('mpor-view-office');
                const viewMonth = document.getElementById('mpor-view-month');
                const viewStatusPill = document.getElementById('mpor-view-status-pill');
                const viewSubmittedAt = document.getElementById('mpor-view-submitted-at');
                const viewOutputs = document.getElementById('mpor-view-outputs');
                const viewApproveBtn = document.getElementById('mpor-view-approve-btn');
                const viewEndorseBtn = document.getElementById('mpor-view-endorse-btn');
                const summaryW1 = document.getElementById('mpor-summary-w1');
                const summaryW2 = document.getElementById('mpor-summary-w2');
                const summaryW3 = document.getElementById('mpor-summary-w3');
                const summaryW4 = document.getElementById('mpor-summary-w4');
                const summaryTotal = document.getElementById('mpor-summary-total');
                const summaryIncluded = document.getElementById('mpor-summary-included');
                const summaryExcluded = document.getElementById('mpor-summary-excluded');
                const certSupervisorName = document.getElementById('mpor-cert-supervisor-name');
                const certSupervisorDate = document.getElementById('mpor-cert-supervisor-date');
                const certEmployeeName = document.getElementById('mpor-cert-employee-name');
                const certEmployeeDate = document.getElementById('mpor-cert-employee-date');

                const escapeHtml = (value) => String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');

                const metricCells = (metric, withLeftBorder) => {
                    const safeMetric = metric || {};
                    const keys = ['w1', 'w2', 'w3', 'w4', 'total'];
                    return keys.map((key, index) => {
                        const classes = ['px-2', 'py-2', 'text-right', 'tabular-nums'];
                        if (index === 0 && withLeftBorder) {
                            classes.push('border-l', 'border-slate-800');
                        }
                        if (key === 'total') {
                            classes.push('font-semibold', 'text-white');
                        } else {
                            classes.push('text-slate-200');
                        }
                        return `<td class="${classes.join(' ')}">${escapeHtml(safeMetric[key] ?? 0)}</td>`;
                    }).join('');
                };

                const renderGroupedOutputs = (preview) => {
                    if (!viewOutputs) {
                        return;
                    }

                    const groups = Array.isArray(preview?.groups) ? preview.groups : [];
                    const fallbackRows = Array.isArray(preview?.outputs) ? preview.outputs : [];
                    viewOutputs.innerHTML = '';

                    if (groups.length) {
                        const html = [];

                        groups.forEach((group) => {
                            const rows = Array.isArray(group?.rows) ? group.rows : [];
                            const label = `${group?.label || 'GROUP'}${group?.weight_label ? ` (${group.weight_label})` : ''}`;

                            html.push(`
                                <tr class="bg-slate-900/60 text-[0.65rem] uppercase tracking-[0.3em] text-slate-400">
                                    <td class="px-3 py-2 font-semibold text-slate-200" colspan="16">${escapeHtml(label)}</td>
                                </tr>
                            `);

                            rows.forEach((row) => {
                                const title = row?.title || row?.task_title || '-';
                                const qty = row?.qty || row?.eff || {};
                                const quality = row?.quality || row?.qual || {};
                                const timeliness = row?.timeliness || row?.time || {};

                                html.push(`
                                    <tr class="text-slate-200">
                                        <td class="px-3 py-2 font-medium text-white">${escapeHtml(title)}</td>
                                        ${metricCells(qty, true)}
                                        ${metricCells(quality, true)}
                                        ${metricCells(timeliness, true)}
                                    </tr>
                                `);
                            });
                        });

                        viewOutputs.innerHTML = html.join('');
                        return;
                    }

                    if (!fallbackRows.length) {
                        viewOutputs.innerHTML =
                            '<tr><td colspan="16" class="px-4 py-6 text-center text-slate-400">No output rows available.</td></tr>';
                        return;
                    }

                    fallbackRows.forEach((row) => {
                        const tr = document.createElement('tr');
                        tr.className = 'text-slate-200';
                        tr.innerHTML = `
                            <td class="px-3 py-2 font-medium text-white">${escapeHtml(row?.title || row?.task_title || '-')}</td>
                            ${metricCells(row?.qty || row?.eff || {}, true)}
                            ${metricCells(row?.quality || row?.qual || {}, true)}
                            ${metricCells(row?.timeliness || row?.time || {}, true)}
                        `;
                        viewOutputs.appendChild(tr);
                    });
                };

                document.querySelectorAll('[data-open-mpor-view]').forEach((button) => {
                    button.addEventListener('click', function() {
                        let payload = null;
                        try {
                            payload = JSON.parse(this.dataset.mpor || '{}');
                        } catch (error) {
                            payload = {};
                        }

                        viewEmployee.textContent = payload.employee || '-';
                        viewOffice.textContent = payload.office_division || payload.office || '-';
                        viewMonth.textContent = payload.month || '-';
                        viewStatusPill.textContent = payload.status_badge_label || payload.status || '-';
                        viewSubmittedAt.textContent = payload.submitted_at || '-';
                        const statusKey = (payload.status_key || payload.status || '').toString().toLowerCase();

                        if (viewApproveBtn) {
                            viewApproveBtn.dataset.mporId = payload.id ?? '';
                            viewApproveBtn.dataset.mporEmployee = payload.employee ?? '-';
                            viewApproveBtn.dataset.mporMonth = payload.month ?? '-';
                        }

                        if (viewEndorseBtn) {
                            viewEndorseBtn.dataset.mporId = payload.id ?? '';
                            viewEndorseBtn.dataset.mporEmployee = payload.employee ?? '-';
                            viewEndorseBtn.dataset.mporMonth = payload.month ?? '-';
                        }

                        if (statusKey === 'submitted') {
                            viewApproveBtn?.classList.remove('hidden');
                            viewEndorseBtn?.classList.add('hidden');
                        } else if (statusKey === 'approved') {
                            viewApproveBtn?.classList.add('hidden');
                            viewEndorseBtn?.classList.remove('hidden');
                        } else {
                            viewApproveBtn?.classList.add('hidden');
                            viewEndorseBtn?.classList.add('hidden');
                        }

                        certSupervisorName.textContent = payload.supervisor || 'Carlo D. Beray';
                        certEmployeeName.textContent = payload.employee || 'Ramon Reyes';

                        if (statusKey === 'endorsed') {
                            certEmployeeDate.textContent = payload.submitted_at || '-';
                            certSupervisorDate.textContent = payload.endorsed_at || payload.approved_at || '-';
                        } else if (statusKey === 'approved') {
                            certEmployeeDate.textContent = payload.submitted_at || '-';
                            certSupervisorDate.textContent = payload.approved_at || '-';
                        } else if (statusKey === 'submitted') {
                            certEmployeeDate.textContent = payload.submitted_at || '-';
                            certSupervisorDate.textContent = 'Pending approval';
                        } else {
                            certEmployeeDate.textContent = '-';
                            certSupervisorDate.textContent = '-';
                        }

                        summaryW1.textContent = String(payload.summary?.week1_total ?? 0);
                        summaryW2.textContent = String(payload.summary?.week2_total ?? 0);
                        summaryW3.textContent = String(payload.summary?.week3_total ?? 0);
                        summaryW4.textContent = String(payload.summary?.week4_total ?? 0);
                        summaryTotal.textContent = String(payload.summary?.grand_total ?? 0);
                        summaryIncluded.textContent = String(payload.summary?.included_entries ?? 0);
                        summaryExcluded.textContent = String(payload.summary?.excluded_entries ?? 0);

                        renderGroupedOutputs(payload.preview || {});
                    });
                });

                const bindActionModal = ({
                    triggerSelector,
                    formId,
                    employeeId,
                    monthId,
                    buttonId,
                    loadingText,
                    defaultText,
                    modalHideSelector,
                }) => {
                    const form = document.getElementById(formId);
                    if (!form) {
                        return;
                    }

                    const employee = document.getElementById(employeeId);
                    const month = document.getElementById(monthId);
                    const button = document.getElementById(buttonId);
                    const spinner = button?.querySelector('[data-button-spinner]');
                    const label = button?.querySelector('[data-button-label]');
                    const actionTemplate = form.dataset.actionTemplate || '';

                    const resetButton = () => {
                        if (!button || !spinner || !label) {
                            return;
                        }

                        button.disabled = false;
                        button.classList.remove('cursor-not-allowed', 'opacity-80');
                        spinner.classList.add('hidden');
                        label.textContent = defaultText;
                    };

                    document.querySelectorAll(triggerSelector).forEach((trigger) => {
                        trigger.addEventListener('click', function() {
                            const mporId = this.dataset.mporId || '';
                            form.action = actionTemplate.replace('__ID__', mporId);
                            if (employee) {
                                employee.textContent = this.dataset.mporEmployee || '-';
                            }
                            if (month) {
                                month.textContent = this.dataset.mporMonth || '-';
                            }
                            resetButton();
                        });
                    });

                    document.querySelectorAll(modalHideSelector).forEach((hideBtn) => {
                        hideBtn.addEventListener('click', resetButton);
                    });

                    if (button && spinner && label) {
                        form.addEventListener('submit', function() {
                            button.disabled = true;
                            button.classList.add('cursor-not-allowed', 'opacity-80');
                            spinner.classList.remove('hidden');
                            label.textContent = loadingText;
                        });
                    }
                };

                bindActionModal({
                    triggerSelector: '[data-open-mpor-approve]',
                    formId: 'mporApproveForm',
                    employeeId: 'approve-mpor-employee',
                    monthId: 'approve-mpor-month',
                    buttonId: 'mporProceedApproveBtn',
                    loadingText: 'Approving...',
                    defaultText: 'Proceed Approval',
                    modalHideSelector: '[data-modal-hide="mporApproveConfirmModal"]',
                });

                bindActionModal({
                    triggerSelector: '[data-open-mpor-endorse]',
                    formId: 'mporEndorseForm',
                    employeeId: 'endorse-mpor-employee',
                    monthId: 'endorse-mpor-month',
                    buttonId: 'mporProceedEndorseBtn',
                    loadingText: 'Endorsing...',
                    defaultText: 'Proceed Endorsement',
                    modalHideSelector: '[data-modal-hide="mporEndorseConfirmModal"]',
                });
            });
        </script>
    @endpush
@endsection
