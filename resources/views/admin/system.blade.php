<x-layouts.admin>
    <section class="space-y-6 admin-page">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-white">System Settings</h1>
                <p class="text-sm text-slate-400">Configure platform preferences, security, and retention rules.</p>
            </div>
            <button type="button"
                    data-admin-loading="true"
                    data-loading-text="Saving..."
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                <i class="fa-solid fa-gears"></i>
                <span data-button-label>Save Changes</span>
                <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            </button>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Environment</p>
                <p class="mt-2 text-2xl font-semibold text-white">Production</p>
                <p class="text-xs text-slate-500">Version 1.2.0</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Uptime</p>
                {{-- DUMMY_DATA: replace with dynamic value --}}
                <p class="mt-2 text-2xl font-semibold text-emerald-400">99.98%</p>
                <p class="text-xs text-slate-500">Last 30 days</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Data Retention</p>
                <p class="mt-2 text-2xl font-semibold text-white">365 days</p>
                <p class="text-xs text-slate-500">Audit logs</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                <p class="text-xs text-slate-400">Notifications</p>
                <p class="mt-2 text-2xl font-semibold text-amber-300">Email</p>
                <p class="text-xs text-slate-500">Primary channel</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">General Settings</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div>
                        <label class="text-xs uppercase text-slate-400">Timezone</label>
                        <select class="manager-filter-select mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                            <option>Asia/Manila</option>
                            <option>UTC</option>
                            <option>Asia/Singapore</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs uppercase text-slate-400">Fiscal Year Start</label>
                        <input type="month"
                               value="2025-01"
                               class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    </div>
                    <div>
                        <label class="text-xs uppercase text-slate-400">Data Retention (days)</label>
                        <input type="number"
                               value="365"
                               class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Notification Policy</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div>
                            <p class="font-medium text-white">Email Alerts</p>
                            <p class="text-xs text-slate-500">Send alerts for missing outputs.</p>
                        </div>
                        <input type="checkbox" checked>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div>
                            <p class="font-medium text-white">SMS Escalation</p>
                            <p class="text-xs text-slate-500">Escalate critical alerts.</p>
                        </div>
                        <input type="checkbox">
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div>
                            <p class="font-medium text-white">Weekly Digest</p>
                            <p class="text-xs text-slate-500">Performance summary to managers.</p>
                        </div>
                        <input type="checkbox" checked>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Security Settings</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div>
                            <p class="font-medium text-white">Require MFA for admins</p>
                            <p class="text-xs text-slate-500">Mandatory for elevated roles.</p>
                        </div>
                        <input type="checkbox" checked>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <div>
                            <p class="font-medium text-white">Session timeout</p>
                            <p class="text-xs text-slate-500">Auto sign-out after 30 minutes.</p>
                        </div>
                        <input type="checkbox" checked>
                    </div>
                    <div>
                        <label class="text-xs uppercase text-slate-400">Password Rotation (days)</label>
                        <input type="number"
                               value="90"
                               class="mt-1 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Auto-Logging Defaults</h2>
                <div class="mt-4 space-y-4 text-sm text-slate-300">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="font-medium text-white">Idle detection threshold</p>
                        <p class="text-xs text-slate-500">Pause tracking after 10 minutes.</p>
                        <input type="number" value="10" class="mt-2 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                        <p class="font-medium text-white">Auto-close tasks</p>
                        <p class="text-xs text-slate-500">Mark completed after output submit.</p>
                        <select class="manager-filter-select mt-2 w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-200">
                            <option>Enabled</option>
                            <option>Disabled</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layouts.admin>
