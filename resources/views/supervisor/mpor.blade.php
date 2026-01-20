<x-layouts.supervisor>
    @php
        $isValidationPhase = false;
        $modeTitle = 'Stage II - Monthly Performance Monitoring (MPOR Review)';
        $modeHelper = 'This view summarizes submitted ORS accomplishments for monthly monitoring. No validation or rating is performed at this stage.';
        $statusBadge = 'Captured for monitoring';
        $detailsBadge = 'Monitored';
        $finalNote = 'This report supports monthly performance monitoring only. Validation, SMPOR generation, and performance rating occur in Stage III.';
        $showLockButton = false;

        $entries = [
            [
                'output' => 'E-Bank Scanning and Encoding of Revenue Transactions',
                'ors' => 'REQ-2026-019',
                'date' => 'Jan 19, 2026',
                'duration' => '1h 30m',
                'durationCopy' => 'Auto-tracked via ORS',
                'evidence' => 'e-bank_scan.pdf',
                'status' => $detailsBadge,
                'badgeClass' => $isValidationPhase
                    ? 'border-blue-600/50 bg-blue-500/10 text-blue-200'
                    : 'border-emerald-600/50 bg-emerald-500/10 text-emerald-200',
                'unit' => 'Revenue Collection Unit',
                'employee' => 'Ramon Reyes',
                'start' => '09:12 AM',
                'end' => '10:42 AM',
            ],
            [
                'output' => 'Processing of Over-the-Counter Revenue Transactions',
                'ors' => 'REQ-2026-001',
                'date' => 'Jan 3, 2026',
                'duration' => '2h 10m',
                'durationCopy' => 'Auto-tracked via ORS',
                'evidence' => 'otc_receipt.pdf',
                'status' => $detailsBadge,
                'badgeClass' => $isValidationPhase
                    ? 'border-blue-600/50 bg-blue-500/10 text-blue-200'
                    : 'border-emerald-600/50 bg-emerald-500/10 text-emerald-200',
                'unit' => 'Revenue Collection Unit',
                'employee' => 'Ramon Reyes',
                'start' => '08:20 AM',
                'end' => '10:30 AM',
            ],
        ];
    @endphp

    <section class="space-y-6">

        <!-- Header -->
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Monthly Performance Output Report
                </p>
                <h1 class="mt-1 text-2xl font-bold text-white">
                    MPOR - January 2026
                </h1>
                <p class="text-sm text-slate-400 mt-1">
                    {{ $modeHelper }}
                </p>
                <p class="text-[11px] text-slate-500 mt-2">
                    Durations and outputs are auto-generated from ORS. Supervisor actions do not modify employee logs.
                </p>
            </div>
            <div class="flex flex-col items-end gap-2 text-right">
                <span class="rounded-full border border-blue-500/40 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                    {{ $modeTitle }}
                </span>
                <span class="rounded-full border border-slate-500/40 bg-slate-900/60 px-3 py-1 text-[11px] font-semibold text-slate-300">
                    {{ $statusBadge }}
                </span>
            </div>
        </div>

        <!-- MPOR Outputs -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div>
                    <h2 class="text-lg font-semibold text-white">MPOR entries</h2>
                    <p class="text-xs text-slate-400">
                        Only submitted ORS accomplishments are summarized here for monitoring. Status chips reflect monitoring state only.
                    </p>
                </div>
                <span class="text-xs text-slate-400">Captured from submitted ORS entries (read-only)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-slate-800">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Output</th>
                            <th class="px-4 py-3 text-left">ORS Ref</th>
                            <th class="px-4 py-3 text-left">Date Logged</th>
                            <th class="px-4 py-3 text-left">Monitoring Status</th>
                            <th class="px-4 py-3 text-left">Duration</th>
                            <th class="px-4 py-3 text-left">Evidence</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach ($entries as $entry)
                            <tr class="hover:bg-slate-900">
                                <td class="px-4 py-3">{{ $entry['output'] }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $entry['ors'] }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $entry['date'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full {{ $entry['badgeClass'] }} px-2 py-1 text-xs font-semibold border">
                                        {{ $entry['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-200">
                                    <div class="text-sm font-semibold">{{ $entry['duration'] }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $entry['durationCopy'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    <div class="text-sm font-semibold">{{ $entry['evidence'] }}</div>
                                    <div class="text-[11px] text-slate-500">Submitted in ORS (read-only)</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        data-view-entry
                                        data-output="{{ $entry['output'] }}"
                                        data-ors="{{ $entry['ors'] }}"
                                        data-date="{{ $entry['date'] }}"
                                        data-status="{{ $entry['status'] }}"
                                        data-unit="{{ $entry['unit'] }}"
                                        data-employee="{{ $entry['employee'] }}"
                                        data-duration="{{ $entry['duration'] }}"
                                        data-start="{{ $entry['start'] }}"
                                        data-end="{{ $entry['end'] }}"
                                        data-evidence="{{ $entry['evidence'] }}"
                                        class="text-blue-400 hover:text-blue-300 text-xs font-semibold"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Final Action -->
        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
            <p class="flex-1">
                {{ $finalNote }}
            </p>
            @if ($showLockButton)
                <button class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900 shadow-lg shadow-black/30">
                    Lock MPOR (Ready for SMPOR)
                </button>
            @else
                <span class="rounded-full border border-slate-700 px-3 py-1 text-xs font-semibold text-slate-400">
                    Monitoring-only mode
                </span>
            @endif
        </div>

    </section>

    <div id="view-mpor-modal"
         data-modal-container
         tabindex="-1"
         aria-hidden="true"
         class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-950/75 backdrop-blur-sm p-4"
    >
        <div class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900/95 shadow-2xl shadow-black/40">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 px-4 py-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-emerald-300">MPOR Entry</p>
                    <h3 class="text-lg font-semibold text-white">View MPOR Entry</h3>
                    <p class="mt-1 text-sm text-slate-400">
                        {{ $isValidationPhase ? 'Review details and choose an action below.' : 'Read-only detail of the submitted ORS entry.' }}
                    </p>
                </div>
                <button type="button"
                        data-modal-hide="view-mpor-modal"
                        class="rounded-full p-1 text-slate-400 transition hover:bg-slate-800 hover:text-slate-200">
                    <span class="sr-only">Close</span>
                    &times;
                </button>
            </div>
            <div class="space-y-3 px-4 py-4 text-sm text-slate-300">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Output</p>
                        <p id="modal-output" class="mt-1 text-sm font-semibold text-white">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">ORS Ref</p>
                        <p id="modal-ors" class="mt-1 text-sm font-semibold text-white">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Logged date</p>
                        <p id="modal-date" class="mt-1 text-sm font-semibold text-white">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Current status</p>
                        <p id="modal-status" class="mt-1 text-sm font-semibold text-amber-200">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Unit</p>
                        <p id="modal-unit" class="mt-1 text-sm font-semibold text-white">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Employee</p>
                        <p id="modal-employee" class="mt-1 text-sm font-semibold text-white">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Tracked duration</p>
                        <p id="modal-duration" class="mt-1 text-sm font-semibold text-white">--</p>
                        <p class="text-[11px] text-slate-500 mt-1">Captured automatically from ORS</p>
                        <div class="text-[11px] text-slate-500 mt-1 flex flex-col gap-0.5">
                            <span>Start: <span id="modal-start">--</span></span>
                            <span>End: <span id="modal-end">--</span></span>
                        </div>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Output evidence</p>
                        <p id="modal-evidence" class="mt-1 text-sm font-semibold text-white">--</p>
                        <p class="text-[11px] text-slate-500 mt-1">Submitted in ORS (read-only)</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-800 px-4 py-3">
                <button type="button"
                        data-modal-hide="view-mpor-modal"
                        class="rounded-lg border border-slate-700 bg-slate-800/80 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    Close
                </button>
                <a href="{{ route('supervisor.mpor.export.pdf') }}"
                    class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                        Export PDF
                </a>
                <button type="button"
                        class="rounded-lg border border-slate-700 bg-slate-800/80 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                    View Evidence
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const spinner = '<svg class="h-4 w-4 animate-spin text-emerald-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';

                const setLoadingState = (button, text) => {
                    if (!button || button.disabled) return;
                    button.dataset.originalContent = button.dataset.originalContent || button.innerHTML;
                    button.innerHTML = `<span class="flex items-center justify-center gap-2">${spinner}<span>${text}</span></span>`;
                    button.disabled = true;
                    button.classList.add('opacity-80', 'cursor-not-allowed');
                };

                const resetLoadingState = (button) => {
                    if (!button) return;
                    if (button.dataset.originalContent) {
                        button.innerHTML = button.dataset.originalContent;
                    }
                    button.disabled = false;
                    button.classList.remove('opacity-80', 'cursor-not-allowed');
                };

                const toggleModal = (modalId, shouldShow) => {
                    const modal = document.getElementById(modalId);
                    if (!modal) return;
                    if (shouldShow) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        modal.setAttribute('aria-hidden', 'false');
                    } else {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        modal.setAttribute('aria-hidden', 'true');
                    }
                };

                const modalId = 'view-mpor-modal';
                document.querySelectorAll('[data-view-entry]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const detailIds = ['output', 'ors', 'date', 'status', 'unit', 'employee', 'duration', 'start', 'end', 'evidence'];
                        detailIds.forEach((id) => {
                            const el = document.getElementById(`modal-${id}`);
                            if (el) {
                                el.textContent = button.dataset[id] || '--';
                            }
                        });
                        toggleModal(modalId, true);
                    });
                });

                document.querySelectorAll('[data-modal-hide="view-mpor-modal"]').forEach((button) => {
                    button.addEventListener('click', () => toggleModal(modalId, false));
                });

                document.getElementById(modalId)?.addEventListener('click', (event) => {
                    if (event.target.id === modalId) {
                        toggleModal(modalId, false);
                    }
                });

            });
        </script>
    @endpush
</x-layouts.supervisor>

