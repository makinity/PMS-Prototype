@extends('layouts.admin')

@section('main-content')
    @php
        $settings = $settings ?? [];
        $pmsApi = $pmsApi ?? [];
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
                    <button type="button"
                            id="open-pms-api-modal"
                            class="w-full rounded-lg border border-emerald-600/50 px-4 py-2 text-sm font-medium text-emerald-200 hover:bg-emerald-500/10 sm:w-auto">
                        View PMS API Details
                    </button>

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

        <div>
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
        </div>
    </section>

    <div id="pms-api-details-modal"
         class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/70 px-4 py-6"
         role="dialog"
         aria-modal="true"
         aria-labelledby="pms-api-details-title">
        <div class="w-full max-w-3xl rounded-2xl border border-white/10 bg-slate-900 shadow-2xl shadow-black/40">
            <div class="flex items-start justify-between gap-4 border-b border-white/10 px-6 py-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">PMS Provider API</p>
                    <h2 id="pms-api-details-title" class="mt-2 text-xl font-semibold text-white">Performance Management System API Details</h2>
                    <p class="mt-1 text-sm text-slate-300">Share these credentials with authorized systems that need read-only access to PMS master data.</p>
                </div>

                <button type="button"
                        data-close-pms-api-modal
                        class="rounded-lg border border-slate-700 px-3 py-2 text-slate-400 hover:bg-slate-800 hover:text-white">
                    &times;
                </button>
            </div>

            <div class="px-6 py-5">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">API Status</p>
                        <p class="mt-2 text-lg font-semibold {{ ($pmsApi['enabled'] ?? false) ? 'text-emerald-300' : 'text-rose-300' }}">
                            {{ ($pmsApi['enabled'] ?? false) ? 'Enabled' : 'Disabled' }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Generated At</p>
                        <p class="mt-2 text-sm font-semibold text-white">
                            {{ !empty($pmsApi['generated_at']) ? \Carbon\Carbon::parse($pmsApi['generated_at'])->format('M d, Y h:i A') : 'Just generated' }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Last Regenerated By</p>
                        <p class="mt-2 text-sm font-semibold text-white">{{ $pmsApi['regenerated_by'] ?? 'System' }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-white/10 bg-slate-950/40">
                    <div class="border-b border-white/10 px-3 py-3">
                        <div class="flex flex-wrap gap-2">
                            <button type="button"
                                    data-pms-api-tab="overview"
                                    class="pms-api-tab-btn rounded-full border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-200">
                                Overview
                            </button>
                            <button type="button"
                                    data-pms-api-tab="access"
                                    class="pms-api-tab-btn rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-300 hover:bg-slate-800">
                                Access
                            </button>
                            <button type="button"
                                    data-pms-api-tab="endpoints"
                                    class="pms-api-tab-btn rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-300 hover:bg-slate-800">
                                Endpoints
                            </button>
                            <button type="button"
                                    data-pms-api-tab="manage"
                                    class="pms-api-tab-btn rounded-full border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-300 hover:bg-slate-800">
                                Manage
                            </button>
                        </div>
                    </div>

                    <div class="p-5">
                        <div data-pms-api-panel="overview" class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <label for="pms-api-base-url" class="text-xs uppercase tracking-[0.3em] text-slate-400">API Base URL</label>
                                    <button type="button"
                                            data-copy-target="pms-api-base-url"
                                            class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                        Copy
                                    </button>
                                </div>
                                <input id="pms-api-base-url"
                                       type="text"
                                       readonly
                                       value="{{ $pmsApi['base_url'] ?? '' }}"
                                       style="background-color:#020617;color:#f8fafc;"
                                       class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500">
                            </div>

                            <div>
                                <label class="text-xs uppercase tracking-[0.3em] text-slate-400">Authentication</label>
                                <div class="mt-2 rounded-lg border border-white/10 bg-slate-950/80 px-4 py-3 text-sm text-slate-100">
                                    Bearer Token
                                </div>
                            </div>

                            <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Available Data</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach (($pmsApi['available_data'] ?? []) as $dataset)
                                        <span class="inline-flex rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                                            {{ $dataset }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div data-pms-api-panel="access" class="hidden space-y-4">
                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <label for="pms-api-token" class="text-xs uppercase tracking-[0.3em] text-slate-400">Active Token</label>
                                    <button type="button"
                                            data-copy-target="pms-api-token"
                                            class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                        Copy
                                    </button>
                                </div>
                                <textarea id="pms-api-token"
                                          rows="4"
                                          readonly
                                          style="background-color:#020617;color:#f8fafc;"
                                          class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500">{{ $pmsApi['token'] ?? '' }}</textarea>
                            </div>

                            <div class="rounded-xl border border-amber-500/20 bg-amber-500/10 p-4">
                                <p class="text-xs uppercase tracking-[0.25em] text-amber-300">Credential Note</p>
                                <p class="mt-2 text-sm text-amber-100">Share this token only with trusted systems. Regenerating it will invalidate all previous PMS API credentials immediately.</p>
                            </div>
                        </div>

                        <div data-pms-api-panel="endpoints" class="hidden space-y-3">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Available Endpoints</p>
                            @foreach (($pmsApi['sample_endpoints'] ?? []) as $index => $endpoint)
                                <div class="flex items-center gap-2 rounded-lg border border-white/10 bg-slate-950/60 px-3 py-2">
                                    <input id="pms-endpoint-{{ $index }}"
                                           type="text"
                                           readonly
                                           value="{{ $endpoint }}"
                                           style="background-color:#020617;color:#f8fafc;"
                                           class="w-full rounded-md border border-slate-800 bg-slate-950 px-3 py-2 text-sm text-slate-100 outline-none">
                                    <button type="button"
                                            data-copy-target="pms-endpoint-{{ $index }}"
                                            class="rounded-md border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                        Copy
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <div data-pms-api-panel="manage" class="hidden space-y-4">
                            <div class="rounded-xl border border-amber-500/20 bg-amber-500/10 p-4">
                                <p class="text-xs uppercase tracking-[0.25em] text-amber-300">Regenerate Token</p>
                                <p class="mt-2 text-sm text-amber-100">Regenerating the PMS API token immediately invalidates any credentials previously shared with other systems.</p>

                                <form method="POST" action="{{ route('admin.hris.pms-api.regenerate') }}" class="mt-4">
                                    @csrf
                                    <button type="submit"
                                            class="w-full rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-amber-400">
                                        Regenerate Token
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end border-t border-white/10 px-6 py-4">
                <button type="button"
                        data-close-pms-api-modal
                        class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('pms-api-details-modal');
            const openButton = document.getElementById('open-pms-api-modal');
            const closeButtons = modal ? modal.querySelectorAll('[data-close-pms-api-modal]') : [];
            const tabButtons = modal ? modal.querySelectorAll('[data-pms-api-tab]') : [];
            const tabPanels = modal ? modal.querySelectorAll('[data-pms-api-panel]') : [];
            const shouldAutoOpen = @json((bool) session('open_pms_api_modal'));

            if (!modal || !openButton) {
                return;
            }

            function setActiveTab(tabName) {
                tabButtons.forEach((button) => {
                    const isActive = button.getAttribute('data-pms-api-tab') === tabName;
                    button.classList.toggle('bg-emerald-500/10', isActive);
                    button.classList.toggle('border-emerald-500/30', isActive);
                    button.classList.toggle('text-emerald-200', isActive);
                    button.classList.toggle('border-slate-700', !isActive);
                    button.classList.toggle('text-slate-300', !isActive);
                });

                tabPanels.forEach((panel) => {
                    const isActive = panel.getAttribute('data-pms-api-panel') === tabName;
                    panel.classList.toggle('hidden', !isActive);
                });
            }

            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setActiveTab('overview');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            openButton.addEventListener('click', openModal);

            closeButtons.forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            tabButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    setActiveTab(button.getAttribute('data-pms-api-tab'));
                });
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.querySelectorAll('[data-copy-target]').forEach((button) => {
                button.addEventListener('click', async function () {
                    const targetId = button.getAttribute('data-copy-target');
                    const target = document.getElementById(targetId);
                    if (!target) {
                        return;
                    }

                    const value = 'value' in target ? target.value : target.textContent;
                    try {
                        await navigator.clipboard.writeText(value || '');
                        const previous = button.textContent;
                        button.textContent = 'Copied';
                        setTimeout(() => {
                            button.textContent = previous;
                        }, 1200);
                    } catch (error) {
                        console.error('Unable to copy text.', error);
                    }
                });
            });

            if (shouldAutoOpen) {
                openModal();
            } else {
                setActiveTab('overview');
            }
        });
    </script>
@endpush
