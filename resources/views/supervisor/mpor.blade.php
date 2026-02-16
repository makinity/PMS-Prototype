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
                                $payload = [
                                    'id' => $mpor['id'],
                                    'employee' => $mpor['employee'],
                                    'supervisor' => auth()->user()?->name ?? 'Carlo D. Beray',
                                    'office' => $mpor['office'],
                                    'month' => $mpor['month'],
                                    'status' => $meta['label'],
                                    'status_key' => $status,
                                    'submitted_at' => $mpor['submitted_at'] ?? null,
                                    'approved_at' => $mpor['approved_at'] ?? null,
                                    'endorsed_at' => $mpor['endorsed_at'] ?? null,
                                    'attendance' => [
                                        'absence' => ['w1' => 0, 'w2' => 0, 'w3' => 0, 'w4' => 0, 'total' => 0, 'unit' => 'days'],
                                        'tardiness' => ['w1' => 185, 'w2' => 0, 'w3' => 1, 'w4' => 0, 'total' => 186, 'unit' => 'mins'],
                                    ],
                                    'preview' => $mpor['preview'] ?? ['outputs' => []],
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
        <div class="relative max-h-full w-full max-w-5xl p-4">
            <div class="relative flex max-h-[90vh] flex-col rounded-2xl border border-slate-800 bg-slate-900 shadow-lg">
                <div class="flex items-start justify-between border-b border-slate-800 p-5">
                    <div>
                        <h3 class="text-lg font-semibold text-white">View MPOR (Read-only)</h3>
                        <p class="mt-1 text-sm text-slate-400">Submitted employee MPOR details for supervisor review.</p>
                    </div>
                    <button type="button" data-modal-hide="mporViewModal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-800 hover:text-white">
                        <span class="sr-only">Close modal</span>
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="min-h-0 space-y-4 overflow-y-auto p-5">
                    <div class="grid gap-3 text-sm text-slate-300 md:grid-cols-4">
                        <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Employee</p>
                            <p id="mpor-view-employee" class="mt-1 font-semibold text-white">-</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Office / Unit</p>
                            <p id="mpor-view-office" class="mt-1 font-semibold text-white">-</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Month</p>
                            <p id="mpor-view-month" class="mt-1 font-semibold text-white">-</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Status</p>
                            <p id="mpor-view-status" class="mt-1 font-semibold text-white">-</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-800">
                        <table class="min-w-full divide-y divide-slate-800 text-sm">
                            <thead>
                                <tr class="bg-slate-950/60 text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                                    <th rowspan="2" class="px-4 py-3 text-left align-middle">Output / Task</th>
                                    <th colspan="5" class="px-4 py-3 text-center">Efficiency / Quantity</th>
                                    <th colspan="5" class="px-4 py-3 text-center">Quality / Effectiveness</th>
                                    <th colspan="5" class="px-4 py-3 text-center">Timeliness</th>
                                </tr>
                                <tr class="bg-slate-950/60 text-center text-[11px] uppercase tracking-[0.2em] text-slate-500">
                                    <th class="px-2 py-2">W1</th>
                                    <th class="px-2 py-2">W2</th>
                                    <th class="px-2 py-2">W3</th>
                                    <th class="px-2 py-2">W4</th>
                                    <th class="px-2 py-2 font-semibold">Total</th>
                                    <th class="px-2 py-2">W1</th>
                                    <th class="px-2 py-2">W2</th>
                                    <th class="px-2 py-2">W3</th>
                                    <th class="px-2 py-2">W4</th>
                                    <th class="px-2 py-2 font-semibold">Total</th>
                                    <th class="px-2 py-2">W1</th>
                                    <th class="px-2 py-2">W2</th>
                                    <th class="px-2 py-2">W3</th>
                                    <th class="px-2 py-2">W4</th>
                                    <th class="px-2 py-2 font-semibold">Total</th>
                                </tr>
                            </thead>
                            <tbody id="mpor-view-outputs" class="divide-y divide-slate-800 text-slate-200"></tbody>
                        </table>
                    </div>

                    <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-4">
                        <h4 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Attendance Impact (Read-only)</h4>
                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-800 text-sm">
                                <thead>
                                    <tr class="bg-slate-950/60 text-center text-[11px] uppercase tracking-[0.2em] text-slate-400">
                                        <th class="px-3 py-2 text-left">Label</th>
                                        <th class="px-3 py-2">Week 1</th>
                                        <th class="px-3 py-2">Week 2</th>
                                        <th class="px-3 py-2">Week 3</th>
                                        <th class="px-3 py-2">Week 4</th>
                                        <th class="px-3 py-2 font-semibold">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 text-slate-200">
                                    <tr>
                                        <td class="px-3 py-2 text-left">Man day(s) lost thru absence</td>
                                        <td id="mpor-att-absence-w1" class="px-3 py-2 text-center">0</td>
                                        <td id="mpor-att-absence-w2" class="px-3 py-2 text-center">0</td>
                                        <td id="mpor-att-absence-w3" class="px-3 py-2 text-center">0</td>
                                        <td id="mpor-att-absence-w4" class="px-3 py-2 text-center">0</td>
                                        <td id="mpor-att-absence-total" class="px-3 py-2 text-center font-semibold">0 days</td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 text-left">Man hrs./minutes lost thru tardiness / undertime</td>
                                        <td id="mpor-att-tardy-w1" class="px-3 py-2 text-center">185</td>
                                        <td id="mpor-att-tardy-w2" class="px-3 py-2 text-center">0</td>
                                        <td id="mpor-att-tardy-w3" class="px-3 py-2 text-center">1</td>
                                        <td id="mpor-att-tardy-w4" class="px-3 py-2 text-center">0</td>
                                        <td id="mpor-att-tardy-total" class="px-3 py-2 text-center font-semibold">186 mins</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">CONFIRMED:</p>
                            <p id="mpor-cert-supervisor-name" class="mt-3 text-sm font-semibold text-white">Carlo D. Beray</p>
                            <p class="mt-2 text-xs text-slate-500">Date:</p>
                            <p id="mpor-cert-supervisor-date" class="text-sm text-slate-300">Pending approval</p>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Above information are true and correct:</p>
                            <p id="mpor-cert-employee-name" class="mt-3 text-sm font-semibold text-white">Ramon Reyes</p>
                            <p class="mt-2 text-xs text-slate-500">Date:</p>
                            <p id="mpor-cert-employee-date" class="text-sm text-slate-300">-</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-800 p-5">
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
                const viewStatus = document.getElementById('mpor-view-status');
                const viewOutputs = document.getElementById('mpor-view-outputs');
                const viewApproveBtn = document.getElementById('mpor-view-approve-btn');
                const viewEndorseBtn = document.getElementById('mpor-view-endorse-btn');
                const certSupervisorName = document.getElementById('mpor-cert-supervisor-name');
                const certSupervisorDate = document.getElementById('mpor-cert-supervisor-date');
                const certEmployeeName = document.getElementById('mpor-cert-employee-name');
                const certEmployeeDate = document.getElementById('mpor-cert-employee-date');

                const absenceW1 = document.getElementById('mpor-att-absence-w1');
                const absenceW2 = document.getElementById('mpor-att-absence-w2');
                const absenceW3 = document.getElementById('mpor-att-absence-w3');
                const absenceW4 = document.getElementById('mpor-att-absence-w4');
                const absenceTotal = document.getElementById('mpor-att-absence-total');

                const tardyW1 = document.getElementById('mpor-att-tardy-w1');
                const tardyW2 = document.getElementById('mpor-att-tardy-w2');
                const tardyW3 = document.getElementById('mpor-att-tardy-w3');
                const tardyW4 = document.getElementById('mpor-att-tardy-w4');
                const tardyTotal = document.getElementById('mpor-att-tardy-total');

                document.querySelectorAll('[data-open-mpor-view]').forEach((button) => {
                    button.addEventListener('click', function() {
                        let payload = null;
                        try {
                            payload = JSON.parse(this.dataset.mpor || '{}');
                        } catch (error) {
                            payload = {};
                        }

                        viewEmployee.textContent = payload.employee || '-';
                        viewOffice.textContent = payload.office || '-';
                        viewMonth.textContent = payload.month || '-';
                        viewStatus.textContent = payload.status || '-';
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

                        const attendance = payload.attendance || {};
                        const absence = attendance.absence || {};
                        const tardiness = attendance.tardiness || {};

                        absenceW1.textContent = absence.w1 ?? 0;
                        absenceW2.textContent = absence.w2 ?? 0;
                        absenceW3.textContent = absence.w3 ?? 0;
                        absenceW4.textContent = absence.w4 ?? 0;
                        absenceTotal.textContent = `${absence.total ?? 0} ${absence.unit || 'days'}`;

                        tardyW1.textContent = tardiness.w1 ?? 185;
                        tardyW2.textContent = tardiness.w2 ?? 0;
                        tardyW3.textContent = tardiness.w3 ?? 1;
                        tardyW4.textContent = tardiness.w4 ?? 0;
                        tardyTotal.textContent = `${tardiness.total ?? 186} ${tardiness.unit || 'mins'}`;

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

                        const rows = payload.preview?.outputs || [];
                        viewOutputs.innerHTML = '';

                        if (!rows.length) {
                            viewOutputs.innerHTML =
                                '<tr><td colspan="16" class="px-4 py-6 text-center text-slate-400">No output rows available.</td></tr>';
                            return;
                        }

                        rows.forEach((row) => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="px-4 py-3 text-left">${row.title || '-'}</td>
                                <td class="px-2 py-3 text-center">${row.qty?.w1 ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.qty?.w2 ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.qty?.w3 ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.qty?.w4 ?? 0}</td>
                                <td class="px-2 py-3 text-center font-semibold">${row.qty?.total ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.quality?.w1 ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.quality?.w2 ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.quality?.w3 ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.quality?.w4 ?? 0}</td>
                                <td class="px-2 py-3 text-center font-semibold">${row.quality?.total ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.timeliness?.w1 ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.timeliness?.w2 ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.timeliness?.w3 ?? 0}</td>
                                <td class="px-2 py-3 text-center">${row.timeliness?.w4 ?? 0}</td>
                                <td class="px-2 py-3 text-center font-semibold">${row.timeliness?.total ?? 0}</td>
                            `;
                            viewOutputs.appendChild(tr);
                        });
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
