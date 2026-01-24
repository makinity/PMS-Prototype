@extends('layouts.admin')

@section('main-content')
    <section class="space-y-6 admin-page">

        <!-- HEADER -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Stage III – OPCR Accomplishment Finalization
                </p>
                <h1 class="text-2xl font-semibold text-white">OPCR Accomplishment (View)</h1>
                <p class="text-sm text-slate-400">
                    Read-only OPCR accomplishments derived from locked SMPOR and Supervisor IPCR ratings.
                </p>
                <p class="text-[11px] text-slate-500 mt-1">
                    Office / Unit: Revenue Collection Unit • Position: Records Management Officer • Rating Period: January – June 2026
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="#"
                   class="inline-flex items-center gap-2 rounded-lg border border-blue-500 text-blue-200 px-3 py-2 text-xs font-semibold hover:bg-blue-500/10 transition">
                    Export OPCR
                </a>
                <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                    Finalization (Read-only)
                </span>
            </div>
        </div>

        <!-- IPCR SOURCE SUMMARY -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="grid grid-cols-1 gap-3 text-sm text-slate-200 sm:grid-cols-2 md:grid-cols-4">
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">IPCRs Included</p>
                    <p class="text-xl font-semibold text-white">1</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Avg Supervisor Rating</p>
                    <p class="text-xl font-semibold text-white">5.00</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Avg PMT Calibrated Rating (System)</p>
                    <p class="text-xl font-semibold text-white">5.00</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Calibration Delta</p>
                    <p class="text-xl font-semibold text-white">0.00 (No adjustment)</p>
                </div>
            </div>
        </div>

        <!-- OPCR ACCOMPLISHMENT TABLE -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">OPCR Accomplishments</h2>
                    <p class="text-xs text-slate-400">Ratings and accomplishments are system-generated and locked. View success indicators for reference.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">MFO / Output</th>
                            <th class="px-4 py-3 text-left">6-Month Summary of Accomplishment</th>
                            <th class="px-4 py-3 text-left">Rating</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-slate-100">E-Bank Scanning and Encoding of Revenue Transactions</td>
                            <td class="px-4 py-3 text-slate-300">Completed daily scanning and encoding of e-bank transactions based on submitted ORS entries.</td>
                            <td class="px-4 py-3 text-slate-100">5.00</td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    class="text-blue-400 hover:text-blue-300 text-xs font-semibold"
                                    data-mfo="ebank_scanning">
                                    View Success Indicators
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-slate-100">Processing of Over-the-Counter Revenue Transactions</td>
                            <td class="px-4 py-3 text-slate-300">Same-day verification of over-the-counter revenue transactions completed based on submitted ORS entry.</td>
                            <td class="px-4 py-3 text-slate-100">5.00</td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    class="text-blue-400 hover:text-blue-300 text-xs font-semibold"
                                    data-mfo="otc_processing">
                                    View Success Indicators
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-slate-100">Maintenance of Revenue Records Filing System</td>
                            <td class="px-4 py-3 text-slate-300">0 (No output logged for the period)</td>
                            <td class="px-4 py-3 text-slate-100">—</td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    type="button"
                                    class="text-blue-400 hover:text-blue-300 text-xs font-semibold"
                                    data-mfo="records_filing">
                                    View Success Indicators
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-[11px] text-slate-500">
            Export generates a copy of the OPCR for reference. Approval is performed by PMT.
        </p>

    </section>

    <!-- SUCCESS INDICATORS MODAL -->
    <div id="sic-modal"
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Success Indicators</p>
                    <h3 id="sic-title" class="text-lg font-semibold text-white">--</h3>
                </div>
                <button type="button" id="sic-close" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <div class="mt-4 space-y-3 max-h-[60vh] overflow-y-auto text-sm text-slate-200">
                <ul id="sic-list" class="space-y-2 list-disc list-inside text-slate-200"></ul>
                <p class="text-[11px] text-slate-400">
                    Success indicators are defined at OPCR setup and are not editable at this stage.
                </p>
            </div>

            <div class="mt-4 flex justify-end border-t border-slate-800 pt-4">
                <button type="button"
                        id="sic-close-bottom"
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('sic-modal');
                const closeButtons = [document.getElementById('sic-close'), document.getElementById('sic-close-bottom')];
                const titleEl = document.getElementById('sic-title');
                const listEl = document.getElementById('sic-list');

                const INDICATORS = {
                    ebank_scanning: {
                        title: 'Success Indicators – E-Bank Scanning and Encoding of Revenue Transactions',
                        indicators: [
                            'All e-bank transactions scanned and encoded based on submitted ORS entries',
                            'Zero missing or duplicate transaction records',
                            'Same-day completion of scanning and encoding activities',
                        ],
                    },
                    otc_processing: {
                        title: 'Success Indicators – Processing of Over-the-Counter Revenue Transactions',
                        indicators: [
                            'All over-the-counter transactions verified against submitted ORS entries',
                            'Same-day processing of revenue transactions',
                            'Zero discrepancies identified during verification',
                        ],
                    },
                    records_filing: {
                        title: 'Success Indicators – Maintenance of Revenue Records Filing System',
                        indicators: [
                            'Revenue records filing system maintained in accordance with records standards',
                            'No lost, misplaced, or damaged revenue documents',
                            'Filing system kept audit-ready throughout the period',
                        ],
                    },
                };

                function openModal(key) {
                    const data = INDICATORS[key];
                    if (!data || !modal || !titleEl || !listEl) return;
                    titleEl.textContent = data.title || 'Success Indicators';
                    listEl.innerHTML = '';
                    (data.indicators || []).forEach((text) => {
                        const li = document.createElement('li');
                        li.textContent = text || '--';
                        listEl.appendChild(li);
                    });
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function closeModal() {
                    modal?.classList.add('hidden');
                    modal?.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                document.querySelectorAll('[data-mfo]').forEach((btn) => {
                    btn.addEventListener('click', () => openModal(btn.dataset.mfo));
                });

                closeButtons.forEach((btn) => {
                    btn?.addEventListener('click', closeModal);
                });

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeModal();
                    }
                });
            });
        </script>
    @endpush
@endsection
