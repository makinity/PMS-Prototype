@extends('layouts.pmt')

@section('main-content')
    <section class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Stage III – OPCR Approval</p>
                <h1 class="text-2xl font-semibold text-white">Revenue Collection Unit</h1>
                <p class="text-sm text-slate-400">Period: January – June 2026</p>
                <p class="text-[11px] text-slate-500 mt-1">OPCR accomplishments and ratings are system-generated and locked.</p>
            </div>
            <div class="flex items-center gap-2">
                <span id="opcr-status"
                      class="rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-200">
                    For PMT Approval
                </span>
            </div>
        </div>

        <!-- CONSOLIDATED RATING SUMMARY -->
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
                    <p class="text-[11px] uppercase text-slate-500">Avg PMT Calibrated Rating</p>
                    <p class="text-xl font-semibold text-white">5.00</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <p class="text-[11px] uppercase text-slate-500">Overall OPCR Rating</p>
                    <p class="text-xl font-semibold text-white">5.00 – Outstanding</p>
                </div>
            </div>
        </div>

        <!-- OPCR ACCOMPLISHMENT SNAPSHOT -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">OPCR Accomplishment Snapshot</h2>
                <span class="text-xs text-slate-400">Read-only; no edits allowed</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">MFO / Output</th>
                            <th class="px-4 py-3 text-left">6-Month Summary of Accomplishment</th>
                            <th class="px-4 py-3 text-left">Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-slate-100">E-Bank Scanning and Encoding of Revenue Transactions</td>
                            <td class="px-4 py-3 text-slate-300">Completed daily scanning and encoding based on ORS entries.</td>
                            <td class="px-4 py-3 text-slate-100">5.00</td>
                        </tr>
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-slate-100">Processing of Over-the-Counter Revenue Transactions</td>
                            <td class="px-4 py-3 text-slate-300">Same-day verification completed based on ORS entries.</td>
                            <td class="px-4 py-3 text-slate-100">5.00</td>
                        </tr>
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-slate-100">Maintenance of Revenue Records Filing System</td>
                            <td class="px-4 py-3 text-slate-300">No output logged for the period.</td>
                            <td class="px-4 py-3 text-slate-100">N/A</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- GOVERNANCE CONFIRMATION -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-2 text-sm text-slate-200">
            <h3 class="text-sm font-semibold text-white">Approval Checklist (Read-only)</h3>
            <ul class="space-y-1 text-slate-300 list-disc list-inside">
                <li>All IPCRs included have been calibrated.</li>
                <li>OPCR accomplishments were generated by the system.</li>
                <li>Approval will permanently lock OPCR ratings.</li>
            </ul>
        </div>

        <!-- ACTIONS -->
        <div class="flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('pmt.opcr') }}"
               class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 transition">
                Back
            </a>
            <a href="#"
               class="rounded-lg border border-blue-500 px-4 py-2 text-sm font-semibold text-blue-200 hover:bg-blue-500/10 transition">
                Export OPCR
            </a>
            <button type="button"
                    id="approve-opcr"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-900/40 transition hover:bg-emerald-500">
                <span id="approve-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                <span id="approve-label">Approve OPCR</span>
            </button>
        </div>

    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const statusBadge = document.getElementById('opcr-status');
                const approveBtn = document.getElementById('approve-opcr');
                const approveSpinner = document.getElementById('approve-spinner');
                const approveLabel = document.getElementById('approve-label');

                approveBtn?.addEventListener('click', () => {
                    if (!approveBtn) return;
                    approveBtn.disabled = true;
                    approveBtn.classList.add('opacity-70', 'cursor-not-allowed');
                    approveSpinner?.classList.remove('hidden');
                    if (approveLabel) {
                        approveLabel.textContent = 'Approving...';
                    }

                    setTimeout(() => {
                        if (statusBadge) {
                            statusBadge.textContent = 'Approved';
                            statusBadge.className = 'rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200';
                        }
                        approveSpinner?.classList.add('hidden');
                        if (approveLabel) {
                            approveLabel.textContent = 'Approved';
                        }
                    }, 900);
                });
            });
        </script>
    @endpush
@endsection
