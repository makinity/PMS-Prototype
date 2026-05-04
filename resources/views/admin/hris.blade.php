@extends('layouts.admin')

@section('main-content')
    @php
        $settings = $settings ?? [];
        $sampleEndpoints = $sampleEndpoints ?? [];
        $status = strtolower((string) ($settings['last_test_status'] ?? 'unknown'));
        $statusLabel = match ($status) {
            'connected' => 'Connected',
            'failed' => 'Failed',
            default => 'Not Tested',
        };
        $statusClass = match ($status) {
            'connected' => 'text-emerald-300',
            'failed' => 'text-rose-300',
            default => 'text-slate-300',
        };
    @endphp

    <section class="space-y-4 px-3 md:px-6">
        <div class="rounded-xl border border-white/10 bg-gray-800/90 p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-gray-100 sm:text-xl">HMS Integration</h1>
                    <p class="mt-1 text-sm text-gray-300">Configure the external HMS API connection used to sync employee master data into PMS.</p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <form method="POST" action="{{ route('admin.hris.sync') }}">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500 sm:w-auto">
                            Sync Employees
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.hris.test') }}">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 hover:bg-slate-800 sm:w-auto">
                            Test Connection
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-white/10 bg-gray-800/90 p-4 shadow-sm">
                <p class="text-xs text-gray-400">Connection Status</p>
                <p class="mt-2 text-2xl font-semibold {{ $statusClass }}">{{ $statusLabel }}</p>
                <p class="text-xs text-gray-500">Fake HMS API</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-gray-800/90 p-4 shadow-sm">
                <p class="text-xs text-gray-400">Last Checked</p>
                <p class="mt-2 text-lg font-semibold text-white">
                    {{ !empty($settings['last_tested_at']) ? \Carbon\Carbon::parse($settings['last_tested_at'])->format('M d, Y h:i A') : 'Never' }}
                </p>
                <p class="text-xs text-gray-500">Connectivity verification</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-gray-800/90 p-4 shadow-sm">
                <p class="text-xs text-gray-400">Remote Employees</p>
                <p class="mt-2 text-2xl font-semibold text-white">{{ $settings['last_test_count'] !== null && $settings['last_test_count'] !== '' ? $settings['last_test_count'] : '--' }}</p>
                <p class="text-xs text-gray-500">From last successful test</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-gray-800/90 p-4 shadow-sm">
                <p class="text-xs text-gray-400">Sync Mode</p>
                <p class="mt-2 text-2xl font-semibold text-white">Manual</p>
                <p class="text-xs text-gray-500">Run sync manually from this screen</p>
            </div>
        </div>

        @php($syncSummary = session('hms_sync_summary'))
        @if (is_array($syncSummary))
            <div class="rounded-xl border border-white/10 bg-gray-800/90 p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Last Sync Summary</h2>
                        <p class="mt-1 text-sm text-slate-300">Most recent HMS employee import result from this session.</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-5">
                    <div class="rounded-lg border border-white/10 bg-slate-950/60 p-3">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Fetched</p>
                        <p class="mt-2 text-xl font-semibold text-white">{{ (int) ($syncSummary['fetched'] ?? 0) }}</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-slate-950/60 p-3">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Created</p>
                        <p class="mt-2 text-xl font-semibold text-emerald-300">{{ (int) ($syncSummary['created'] ?? 0) }}</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-slate-950/60 p-3">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Updated</p>
                        <p class="mt-2 text-xl font-semibold text-blue-300">{{ (int) ($syncSummary['updated'] ?? 0) }}</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-slate-950/60 p-3">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Skipped</p>
                        <p class="mt-2 text-xl font-semibold text-amber-300">{{ (int) ($syncSummary['skipped'] ?? 0) }}</p>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-slate-950/60 p-3">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Failed</p>
                        <p class="mt-2 text-xl font-semibold text-rose-300">{{ (int) ($syncSummary['failed'] ?? 0) }}</p>
                    </div>
                </div>

                @if (!empty($syncSummary['failures']) && is_array($syncSummary['failures']))
                    <div class="mt-4 rounded-lg border border-amber-700/30 bg-amber-900/10 p-4">
                        <p class="text-xs uppercase tracking-[0.25em] text-amber-300">Skipped / Failed Records</p>
                        <div class="mt-3 space-y-2 text-sm text-amber-100">
                            @foreach ($syncSummary['failures'] as $failure)
                                <div class="rounded-md border border-white/10 bg-slate-950/40 px-3 py-2">
                                    <span class="font-semibold">{{ $failure['employee_no'] ?? 'Unknown employee' }}</span>
                                    <span class="text-slate-300">- {{ $failure['message'] ?? 'Unknown issue' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
            <form method="POST" action="{{ route('admin.hris.update') }}" class="rounded-xl border border-white/10 bg-gray-800/90 p-5 shadow-sm">
                @csrf

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Connection Settings</h2>
                        <p class="mt-1 text-sm text-slate-300">Store the Fake HMS base API URL and bearer token that PMS will use.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-slate-900/60 px-3 py-1 text-xs text-slate-200">
                        <input type="checkbox" name="enabled" value="1" class="rounded border-slate-600 bg-slate-800 text-blue-500" @checked(($settings['enabled'] ?? true) === true)>
                        Enabled
                    </label>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="base_url" class="text-xs uppercase tracking-[0.3em] text-slate-400">Base API URL</label>
                        <input id="base_url"
                               name="base_url"
                               type="url"
                               value="{{ old('base_url', $settings['base_url'] ?? '') }}"
                               placeholder="http://fakehms.test/api/v1"
                               style="background-color:#020617;color:#f8fafc;"
                               class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500">
                        @error('base_url')
                            <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bearer_token" class="text-xs uppercase tracking-[0.3em] text-slate-400">Bearer Token</label>
                        <textarea id="bearer_token"
                                  name="bearer_token"
                                  rows="3"
                                  placeholder="hms_xxx..."
                                  style="background-color:#020617;color:#f8fafc;"
                                  class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500">{{ old('bearer_token', $settings['bearer_token'] ?? '') }}</textarea>
                        @error('bearer_token')
                            <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500">
                        Save Settings
                    </button>
                </div>
            </form>

            <div class="space-y-4 rounded-xl border border-white/10 bg-gray-800/90 p-5 shadow-sm">
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Connection Notes</h2>
                    <p class="mt-1 text-sm text-slate-300">This screen only manages connectivity. Employee sync, office mapping, and supervisor linking come next.</p>
                </div>

                <div class="rounded-lg border border-white/10 bg-slate-950/60 p-4">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Last Test Message</p>
                    <p class="mt-2 text-sm text-slate-200">{{ $settings['last_test_message'] ?? 'No connection test has been run yet.' }}</p>
                </div>

                <div class="rounded-lg border border-white/10 bg-slate-950/60 p-4">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Next Build Step</p>
                    <p class="mt-2 text-sm text-slate-200">After import, add office mapping rules, supervisor linking, and incremental sync by updated timestamp.</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-gray-800/90 p-5 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Sample Endpoints</h2>
            <div class="mt-4 space-y-3">
                @forelse ($sampleEndpoints as $endpoint)
                    <div class="rounded-lg border border-white/10 bg-slate-950/60 px-3 py-2 text-sm text-slate-200">{{ $endpoint }}</div>
                @empty
                    <p class="text-sm text-slate-400">Save a base API URL to generate endpoint samples.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-gray-800/90 p-5 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Expected Payload Fields</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm text-slate-300">
                    <thead class="text-xs uppercase tracking-[0.25em] text-slate-500">
                        <tr class="border-b border-white/10">
                            <th class="px-3 py-2 text-left">HMS Field</th>
                            <th class="px-3 py-2 text-left">PMS Target</th>
                            <th class="px-3 py-2 text-left">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-white/10">
                            <td class="px-3 py-3">id</td>
                            <td class="px-3 py-3">hms_employee_id</td>
                            <td class="px-3 py-3">Stable external identifier for upsert.</td>
                        </tr>
                        <tr class="border-b border-white/10">
                            <td class="px-3 py-3">employee_no</td>
                            <td class="px-3 py-3">employee_id / employee_no</td>
                            <td class="px-3 py-3">Human-readable employee code.</td>
                        </tr>
                        <tr class="border-b border-white/10">
                            <td class="px-3 py-3">full_name</td>
                            <td class="px-3 py-3">name</td>
                            <td class="px-3 py-3">Primary PMS display name.</td>
                        </tr>
                        <tr class="border-b border-white/10">
                            <td class="px-3 py-3">office_code</td>
                            <td class="px-3 py-3">office_id</td>
                            <td class="px-3 py-3">Resolve through local office mapping.</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-3">supervisor_employee_id</td>
                            <td class="px-3 py-3">supervisor link</td>
                            <td class="px-3 py-3">Resolve after all employees are synced.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
