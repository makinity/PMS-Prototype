@extends('layouts.dept-head')

@section('main-content')
    <section class="space-y-6">

        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Quarterly Accomplishment Report (QAR)
                </p>
                <h1 class="text-2xl font-bold text-white">QAR Monitoring</h1>
                <p class="text-sm text-slate-400">
                    Quarterly consolidation of MPOR for monitoring only. Validation and rating occur in Stage III.
                </p>
                <p class="text-[11px] text-slate-500 mt-1">
                    Read-only | Derived from submitted MPOR
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button"
                        disabled
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-500 bg-slate-800/60 cursor-not-allowed">
                    Export QAR (Stage III)
                </button>
            </div>
        </div>

        @php
            $qarEntries = [
                [
                    'period' => 'January – June 2026',
                    'office' => 'Revenue Collection Unit',
                    'source' => 'MPOR – January to June 2026',
                    'status' => 'Monitoring',
                ],
            ];
        @endphp

        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">QAR List</h2>
                    <p class="text-xs text-slate-400">Stage II – Performance Monitoring (read-only)</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-300">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">Period</th>
                            <th class="px-4 py-3 text-left">Office / Unit</th>
                            <th class="px-4 py-3 text-left">Source</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach ($qarEntries as $qar)
                            <tr class="hover:bg-slate-900">
                                <td class="px-4 py-3">{{ $qar['period'] }}</td>
                                <td class="px-4 py-3 text-slate-200">{{ $qar['office'] }}</td>
                                <td class="px-4 py-3 text-slate-300">{{ $qar['source'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full border border-blue-500/40 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
                                        Monitoring
                                    </span>
                                    <span class="rounded-full border border-slate-500/40 bg-slate-900/60 px-2 py-1 text-[11px] font-semibold text-slate-300 ml-1">
                                        Read-only
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        data-qar-view
                                        data-period="{{ $qar['period'] }}"
                                        data-office="{{ $qar['office'] }}"
                                        data-source="{{ $qar['source'] }}"
                                        class="text-blue-400 hover:text-blue-300 text-xs font-semibold">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <div id="qar-view-modal"
         class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-3xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Quarterly Accomplishment Report (QAR)</p>
                    <h3 class="text-lg font-semibold text-white">Stage II – Performance Monitoring (Read-only)</h3>
                </div>
                <button type="button" id="qar-modal-close" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 text-sm text-slate-200">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Office / Unit</p>
                        <p id="qar-office" class="mt-1 font-semibold">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Period Covered</p>
                        <p id="qar-period" class="mt-1 font-semibold">--</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Source</p>
                        <p id="qar-source" class="mt-1 font-semibold">--</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-4 text-sm text-slate-200">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Total ORS Entries</p>
                        <p class="mt-1 font-semibold">2</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Core Outputs Logged</p>
                        <p class="mt-1 font-semibold">2</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Support Outputs Logged</p>
                        <p class="mt-1 font-semibold">0</p>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="text-[11px] uppercase text-slate-500">Months with Activity</p>
                        <p class="mt-1 font-semibold">January 2026</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-white">Core Functions (80%)</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200 border border-slate-800">
                            <thead class="bg-slate-900/70 text-slate-200">
                                <tr>
                                    <th class="px-3 py-2 text-left">Major Output</th>
                                    <th class="px-3 py-2 text-center">January</th>
                                    <th class="px-3 py-2 text-center">February</th>
                                    <th class="px-3 py-2 text-center">March</th>
                                    <th class="px-3 py-2 text-center">April</th>
                                    <th class="px-3 py-2 text-center">May</th>
                                    <th class="px-3 py-2 text-center">June</th>
                                    <th class="px-3 py-2 text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr>
                                    <td class="px-3 py-2">E-Bank Scanning and Encoding of Revenue Transactions</td>
                                    <td class="px-3 py-2 text-center">1</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">1</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2">Processing of Over-the-Counter Revenue Transactions</td>
                                    <td class="px-3 py-2 text-center">1</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">1</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-white">Support Functions (20%)</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-200 border border-slate-800">
                            <thead class="bg-slate-900/70 text-slate-200">
                                <tr>
                                    <th class="px-3 py-2 text-left">Major Output</th>
                                    <th class="px-3 py-2 text-center">January</th>
                                    <th class="px-3 py-2 text-center">February</th>
                                    <th class="px-3 py-2 text-center">March</th>
                                    <th class="px-3 py-2 text-center">April</th>
                                    <th class="px-3 py-2 text-center">May</th>
                                    <th class="px-3 py-2 text-center">June</th>
                                    <th class="px-3 py-2 text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <tr>
                                    <td class="px-3 py-2">Maintenance of Revenue Records Filing System</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                    <td class="px-3 py-2 text-center">0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-xs text-slate-400">
                    This report is a monitoring-only consolidation derived from MPOR. Validation, SMPOR generation, and performance rating occur in Stage III.
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-800 pt-4 mt-4">
                <a href="{{ route('stage2.qar.export.pdf') }}"
                    class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                        Export QAR (Monitoring Copy)Export PDF
                </a>

                <button type="button"
                        id="qar-modal-close-bottom"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('qar-view-modal');
                const closeButtons = [document.getElementById('qar-modal-close'), document.getElementById('qar-modal-close-bottom')];
                const periodEl = document.getElementById('qar-period');
                const officeEl = document.getElementById('qar-office');
                const sourceEl = document.getElementById('qar-source');

                function toggleModal(show) {
                    if (!modal) return;
                    if (show) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        document.body.classList.add('overflow-hidden');
                    } else {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.body.classList.remove('overflow-hidden');
                    }
                }

                document.querySelectorAll('[data-qar-view]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        if (periodEl) periodEl.textContent = btn.dataset.period || '--';
                        if (officeEl) officeEl.textContent = btn.dataset.office || '--';
                        if (sourceEl) sourceEl.textContent = btn.dataset.source || '--';
                        toggleModal(true);
                    });
                });

                closeButtons.forEach((btn) => {
                    if (!btn) return;
                    btn.addEventListener('click', () => toggleModal(false));
                });

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        toggleModal(false);
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        toggleModal(false);
                    }
                });
            });
        </script>
    @endpush
@endsection