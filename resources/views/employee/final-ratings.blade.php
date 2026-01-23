@extends('layouts.employee')

@section('main-content')
    <section class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">My Final Performance Ratings</h1>
                <p class="text-sm text-slate-400">Period: January – June 2026</p>
                <p class="text-[11px] text-slate-500 mt-1">
                    These ratings are final and form part of your official performance record.
                </p>
            </div>
            <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                Official
            </span>
        </div>

        <!-- FINAL IPCR SUMMARY -->
        <div class="rounded-2xl border border-emerald-600/40 bg-emerald-600/10 p-4 shadow-lg shadow-emerald-900/30">
            <h2 class="text-lg font-semibold text-white">Final IPCR Summary</h2>
            <div class="mt-3 grid grid-cols-1 gap-3 text-sm text-slate-200 sm:grid-cols-2">
                <div class="rounded-lg border border-emerald-600/40 bg-emerald-600/10 p-3">
                    <p class="text-[11px] uppercase text-emerald-200">Final IPCR Rating</p>
                    <p class="text-2xl font-bold text-white">5.00</p>
                </div>
                <div class="rounded-lg border border-emerald-600/40 bg-emerald-600/10 p-3">
                    <p class="text-[11px] uppercase text-emerald-200">Performance Level</p>
                    <p class="text-2xl font-bold text-white">Outstanding</p>
                </div>
            </div>
        </div>

        <!-- MFO BREAKDOWN -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">MFO Breakdown</h2>
                <span class="text-xs text-slate-400">Read-only</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-slate-200">
                    <thead class="bg-slate-950/60 text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left">MFO</th>
                            <th class="px-4 py-3 text-left">Final Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-slate-100">E-Bank Scanning and Encoding of Revenue Transactions</td>
                            <td class="px-4 py-3 text-slate-100">5.00</td>
                        </tr>
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-slate-100">Processing of Over-the-Counter Revenue Transactions</td>
                            <td class="px-4 py-3 text-slate-100">5.00</td>
                        </tr>
                        <tr class="hover:bg-slate-900/50">
                            <td class="px-4 py-3 text-slate-100">Maintenance of Revenue Records Filing System</td>
                            <td class="px-4 py-3 text-slate-100">N/A</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- REMARKS -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-2">
                <h3 class="text-sm font-semibold text-white">Supervisor Remarks</h3>
                <p class="text-sm text-slate-400">No remarks provided.</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 space-y-2">
                <h3 class="text-sm font-semibold text-white">PMT Remarks</h3>
                <p class="text-sm text-slate-400">No remarks provided.</p>
            </div>
        </div>

        <!-- OFFICIAL NOTICE -->
        <div class="rounded-2xl border border-emerald-600/30 bg-emerald-600/10 p-4 text-sm text-emerald-200">
            <p class="font-semibold">🟢 This performance rating is OFFICIAL</p>
            <p class="text-emerald-100 mt-1">It is used for rewards, promotion, and development planning.</p>
        </div>

    </section>
@endsection
