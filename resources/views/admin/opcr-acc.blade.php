@extends('layouts.admin')

@section('main-content')
    <section class="space-y-6 admin-page">

        <!-- HEADER -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Stage III – OPCR Accomplishment
                </p>
                <h1 class="text-2xl font-semibold text-white">OPCR Accomplishment List</h1>
                <p class="text-sm text-slate-400">
                    System-generated OPCRs based on final calibrated IPCRs.
                </p>
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
                    <p class="text-xl font-semibold text-white">0.00</p>
                </div>
            </div>
        </div>

        <!-- OPCR ACCOMPLISHMENT LIST -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-300">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Office / Unit</th>
                            <th class="px-4 py-2 text-left">Period</th>
                            <th class="px-4 py-2 text-left">IPCRs Included</th>
                            <th class="px-4 py-2 text-left">Avg Supervisor Rating</th>
                            <th class="px-4 py-2 text-left">Avg PMT Calibrated Rating</th>
                            <th class="px-4 py-2 text-left">OPCR Status</th>
                            <th class="px-4 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-800 hover:bg-slate-900/60">
                            <td class="px-4 py-3 text-white">Revenue Collection Unit</td>
                            <td class="px-4 py-3">Jan–Jun 2026</td>
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3">5.00</td>
                            <td class="px-4 py-3">5.00</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-300 border border-amber-500/30">
                                    For PMT Approval
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.opcr-acc-view') }}"
                                   class="inline-flex items-center gap-2 rounded-lg border border-blue-500 text-blue-400 px-3 py-2 text-xs font-semibold hover:bg-blue-500/10 transition">
                                    View OPCR
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-[11px] text-slate-500">
            OPCR Accomplishments are automatically generated from calibrated IPCRs and cannot be modified at this stage.
        </p>

    </section>
@endsection
