@extends('layouts.pmt')

@section('main-content')
    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Account</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Profile &amp; Security</h1>
                <p class="text-sm text-slate-400">Manage PMT account details and session controls.</p>
            </div>
            <span class="rounded-full border border-emerald-600/50 bg-emerald-500/10 px-3 py-1 text-[11px] font-semibold text-emerald-200">PMT</span>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-4">
                <h2 class="text-lg font-semibold text-white">Organization</h2>
                <div class="space-y-3 text-sm text-slate-200">
                    <div>
                        <p class="text-slate-400 text-xs">Team Name</p>
                        <p class="font-semibold text-white">Performance Management Team</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">Role</p>
                        <p class="font-semibold text-white">PMT Administrator</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">Email</p>
                        <p class="font-semibold text-white">pmt@agency.gov</p>
                    </div>
                </div>
                <button class="rounded-lg border border-slate-700 px-3 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800">Update details</button>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-4">
                <h2 class="text-lg font-semibold text-white">Security</h2>
                <div class="space-y-3 text-sm text-slate-200">
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 px-3 py-2">
                        <div>
                            <p class="font-semibold text-white">Multi-factor auth</p>
                            <p class="text-xs text-slate-400">Required for all PMT members</p>
                        </div>
                        <span class="rounded-full border border-emerald-600/50 bg-emerald-500/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-200">Enabled</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 px-3 py-2">
                        <div>
                            <p class="font-semibold text-white">Active sessions</p>
                            <p class="text-xs text-slate-400">2 devices signed in</p>
                        </div>
                        <button class="rounded-lg border border-rose-700/60 bg-rose-500/10 px-3 py-1 text-xs font-semibold text-rose-200 hover:bg-rose-500/20">Sign out all</button>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 px-3 py-2">
                        <div>
                            <p class="font-semibold text-white">API access</p>
                            <p class="text-xs text-slate-400">Limited to reporting endpoints</p>
                        </div>
                        <button class="rounded-lg border border-slate-700 px-3 py-1 text-xs font-semibold text-slate-200 hover:bg-slate-800">Manage</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
