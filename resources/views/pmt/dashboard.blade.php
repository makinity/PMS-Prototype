@extends('layouts.pmt')

@section('main-content')
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Performance Management Team</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Dashboard</h1>
                <p class="text-sm text-slate-400">Quick view of approvals, escalations, and performance signals.</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-300">
                <span class="rounded-full border border-emerald-600/50 bg-emerald-500/10 px-3 py-1 font-semibold text-emerald-200">Live</span>
                <span class="rounded-full border border-blue-600/50 bg-blue-500/10 px-3 py-1 font-semibold text-blue-200">Prototype</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">UWP approvals pending</p>
                <p class="mt-1 text-3xl font-semibold text-white">6</p>
                <p class="text-xs text-amber-300">3 due today</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">OPCR approvals</p>
                <p class="mt-1 text-3xl font-semibold text-white">4</p>
                <p class="text-xs text-emerald-300">2 auto-validated from UWP</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Performance reports queued</p>
                <p class="mt-1 text-3xl font-semibold text-white">12</p>
                <p class="text-xs text-slate-400">Awaiting export</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Alerts</p>
                <p class="mt-1 text-3xl font-semibold text-rose-300">2</p>
                <p class="text-xs text-rose-300">Missing linkage to ORS</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">UWP Approvals</h2>
                    <a href="" class="text-xs font-semibold text-emerald-300 hover:text-emerald-200">View all</a>
                </div>
                <div class="mt-3 space-y-3 text-sm text-slate-200">
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/70 px-3 py-2">
                        <div>
                            <p class="font-semibold text-white">Provincial HRMO</p>
                            <p class="text-xs text-slate-400">Jan–Jun 2025</p>
                        </div>
                        <span class="rounded-full border border-amber-600/50 bg-amber-500/10 px-2.5 py-1 text-[11px] font-semibold text-amber-200">For review</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/70 px-3 py-2">
                        <div>
                            <p class="font-semibold text-white">IT Services</p>
                            <p class="text-xs text-slate-400">Jul–Dec 2024</p>
                        </div>
                        <span class="rounded-full border border-emerald-600/50 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-200">Approved</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/70 px-3 py-2">
                        <div>
                            <p class="font-semibold text-white">Budget Office</p>
                            <p class="text-xs text-slate-400">Jan–Jun 2025</p>
                        </div>
                        <span class="rounded-full border border-blue-600/50 bg-blue-500/10 px-2.5 py-1 text-[11px] font-semibold text-blue-200">In routing</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Recent Actions</h2>
                    <a href="" class="text-xs font-semibold text-emerald-300 hover:text-emerald-200">Open reports</a>
                </div>
                <div class="mt-3 space-y-3 text-sm text-slate-200">
                    <div class="flex items-start gap-3 rounded-lg border border-slate-800 bg-slate-950/70 px-3 py-2">
                        <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/50">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-white">OPCR approved</p>
                            <p class="text-xs text-slate-400">PMT validated OPCR for Provincial HRMO</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-lg border border-slate-800 bg-slate-950/70 px-3 py-2">
                        <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-500/10 text-amber-200 border border-amber-500/50">
                            <i class="fa-solid fa-exclamation"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-white">UWP needs revision</p>
                            <p class="text-xs text-slate-400">Budget Office flagged missing linkage to ORS</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-lg border border-slate-800 bg-slate-950/70 px-3 py-2">
                        <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-500/10 text-blue-200 border border-blue-500/50">
                            <i class="fa-solid fa-chart-line"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-white">Report exported</p>
                            <p class="text-xs text-slate-400">Performance report queued for Q4 rollup</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
