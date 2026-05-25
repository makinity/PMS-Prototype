@extends('layouts.admin')

@section('main-content')
    @php
        if (!isset($logs) || !isset($filters) || !isset($summary)) {
            $payload = \App\Http\Controllers\Admin\AuditLogsController::buildIndexPayload(request());
            $logs = $payload['logs'];
            $filters = $payload['filters'];
            $summary = $payload['summary'];
            $actors = $payload['actors'];
            $roles = $payload['roles'];
            $modules = $payload['modules'];
            $actions = $payload['actions'];
        }

        $indexUrl = \Illuminate\Support\Facades\Route::has('admin.audit-logs')
            ? route('admin.audit-logs')
            : url()->current();

        $formatLabel = static fn (?string $value): string => $value
            ? \Illuminate\Support\Str::title(str_replace(['_', '.'], ' ', $value))
            : '--';
    @endphp

    <section class="space-y-4 px-3 md:px-6">
        <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
            <h1 class="text-lg font-semibold text-gray-100 sm:text-xl">Audit Logs</h1>
            <p class="mt-1 text-sm text-gray-300">Monitor state-changing activity across authentication, admin, and workflow modules.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-gray-400">Total Events</p>
                <p id="audit-summary-total" class="mt-2 text-2xl font-semibold text-white">{{ number_format((int) ($summary['total'] ?? 0)) }}</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-gray-400">Successful</p>
                <p id="audit-summary-success" class="mt-2 text-2xl font-semibold text-emerald-300">{{ number_format((int) ($summary['success'] ?? 0)) }}</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-gray-400">Failed</p>
                <p id="audit-summary-failed" class="mt-2 text-2xl font-semibold text-rose-300">{{ number_format((int) ($summary['failed'] ?? 0)) }}</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-gray-400">Unique Actors</p>
                <p id="audit-summary-actors" class="mt-2 text-2xl font-semibold text-white">{{ number_format((int) ($summary['unique_actors'] ?? 0)) }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 p-4 shadow-sm">
            <form id="audit-filter-form" method="GET" action="{{ $indexUrl }}" class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                <div class="xl:col-span-2">
                    <label for="auditSearch" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Search</label>
                    <input
                        id="auditSearch"
                        type="text"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Actor, summary, module, target, IP"
                        style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                        class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500" />
                </div>

                <div>
                    <label for="auditActor" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Actor</label>
                    <select
                        id="auditActor"
                        name="actor_user_id"
                        style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                        class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 [color-scheme:dark]">
                        <option value="">All Actors</option>
                        @foreach ($actors as $actor)
                            <option value="{{ $actor['id'] }}" @selected((string) ($filters['actor_user_id'] ?? '') === (string) $actor['id'])>
                                {{ $actor['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="auditModule" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Module</label>
                    <select
                        id="auditModule"
                        name="module"
                        style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                        class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 [color-scheme:dark]">
                        <option value="">All Modules</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>{{ $formatLabel($module) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="auditStatus" class="mb-1 block text-xs uppercase tracking-[0.14em] text-slate-400">Status</label>
                    <select
                        id="auditStatus"
                        name="status"
                        style="background-color:#020617;color:#e2e8f0;border-color:#334155;"
                        class="w-full rounded-xl border px-3 py-2 text-sm text-slate-200 [color-scheme:dark]">
                        <option value="">All Statuses</option>
                        <option value="success" @selected(($filters['status'] ?? '') === 'success')>Success</option>
                        <option value="failed" @selected(($filters['status'] ?? '') === 'failed')>Failed</option>
                    </select>
                </div>

                <div class="md:col-span-2 xl:col-span-4 flex flex-wrap items-center gap-2">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500">
                        Apply Filters
                    </button>
                    <a href="{{ $indexUrl }}" class="inline-flex items-center rounded-lg border border-white/10 bg-gray-900/60 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-700/60">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-gray-700 bg-gradient-to-br from-gray-900 to-gray-800 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-xs sm:text-sm">
                    <thead class="bg-gray-900/70 text-[11px] uppercase tracking-wide text-gray-400 sm:text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Summary</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody id="audit-log-table-body" class="divide-y divide-white/10 text-gray-200">
                        @forelse ($logs as $log)
                            @php
                                $detailPayload = [
                                    'summary' => $log->summary,
                                    'actor' => $log->actor_name ?: 'System / Guest',
                                    'role' => $log->actor_role,
                                    'action' => $log->action_key,
                                    'module' => $log->module_key,
                                    'route' => $log->route_name,
                                    'method' => $log->http_method,
                                    'target_type' => $log->target_type,
                                    'target_id' => $log->target_id,
                                    'ip_address' => $log->ip_address,
                                    'status' => $log->status,
                                    'timestamp' => optional($log->created_at)->format('M d, Y h:i A'),
                                    'metadata' => $log->metadata ?? [],
                                ];
                                $rowTimestamp = optional($log->created_at)->toDateString();
                                $rowActor = $log->actor_name ?: 'System / Guest';
                                $rowRole = (string) ($log->actor_role ?? '');
                                $rowModule = (string) ($log->module_key ?? '');
                                $rowSearch = strtolower(implode(' ', array_filter([
                                    $rowActor,
                                    $log->summary,
                                    $rowModule,
                                    $log->action_key,
                                    $log->target_id,
                                    $log->ip_address,
                                ])));
                            @endphp
                            <tr
                                class="hover:bg-white/5"
                                data-audit-row
                                data-search="{{ e($rowSearch) }}"
                                data-actor-id="{{ (string) ($log->actor_user_id ?? '') }}"
                                data-role="{{ e(strtolower($rowRole)) }}"
                                data-module="{{ e(strtolower($rowModule)) }}"
                                data-status="{{ e(strtolower((string) $log->status)) }}"
                                data-date="{{ e($rowTimestamp) }}"
                            >
                                <td class="px-4 py-3">
                                    <div class="font-medium text-white">{{ $log->summary ?: $formatLabel($log->action_key) }}</div>
                                    <div class="mt-1 text-xs text-gray-400">{{ optional($log->created_at)->format('M d, Y h:i A') }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->actor_name ?: 'System / Guest' }}{{ $log->actor_role ? ' Ã¢â‚¬Â¢ ' . $formatLabel($log->actor_role) : '' }}</div>
                                    <div class="text-xs text-gray-400">{{ $formatLabel($log->module_key) }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $log->status === 'success' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-rose-500/15 text-rose-300' }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        type="button"
                                        data-audit-detail='@json($detailPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
                                        class="text-blue-400 hover:text-blue-300">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="audit-empty-row">
                                <td colspan="3" class="px-4 py-10 text-center text-sm text-gray-400">
                                    No audit events found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($logs, 'links'))
                <div id="audit-pagination-wrap" class="border-t border-white/10 px-4 py-3">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </section>

    <div id="audit-detail-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/70 px-4 py-6">
        <div class="w-full max-w-3xl rounded-2xl border border-gray-700 bg-slate-900 shadow-xl">
            <div class="flex items-start justify-between gap-4 border-b border-gray-700 px-5 py-4">
                <div>
                    <h2 id="audit-detail-title" class="text-lg font-semibold text-white">Audit Event</h2>
                    <p id="audit-detail-subtitle" class="mt-1 text-sm text-slate-400">Details</p>
                </div>
                <button type="button" data-audit-close class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-4 px-5 py-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Actor</p>
                        <p id="audit-detail-actor" class="mt-2 text-sm text-white">--</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Timestamp</p>
                        <p id="audit-detail-time" class="mt-2 text-sm text-white">--</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Action</p>
                        <p id="audit-detail-action" class="mt-2 text-sm text-white">--</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Module</p>
                        <p id="audit-detail-module" class="mt-2 text-sm text-white">--</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Target</p>
                        <p id="audit-detail-target" class="mt-2 text-sm text-white">--</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-400">Route / Method</p>
                        <p id="audit-detail-route" class="mt-2 text-sm text-white">--</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4 md:col-span-2">
                        <p class="text-xs uppercase tracking-wide text-slate-400">IP / Status</p>
                        <p id="audit-detail-status" class="mt-2 text-sm text-white">--</p>
                    </div>
                </div>

                <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Metadata</p>
                    <pre id="audit-detail-metadata" class="mt-3 overflow-x-auto whitespace-pre-wrap break-all rounded-lg bg-slate-950 px-3 py-3 text-xs text-slate-300">--</pre>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('audit-detail-modal');
                const filterForm = document.getElementById('audit-filter-form');
                const tableBody = document.getElementById('audit-log-table-body');
                if (!modal || !filterForm || !tableBody) {
                    return;
                }

                const formatLabel = (value) => String(value || '--')
                    .replace(/[._]/g, ' ')
                    .replace(/\b\w/g, char => char.toUpperCase());

                const fields = {
                    title: document.getElementById('audit-detail-title'),
                    subtitle: document.getElementById('audit-detail-subtitle'),
                    actor: document.getElementById('audit-detail-actor'),
                    time: document.getElementById('audit-detail-time'),
                    action: document.getElementById('audit-detail-action'),
                    module: document.getElementById('audit-detail-module'),
                    target: document.getElementById('audit-detail-target'),
                    route: document.getElementById('audit-detail-route'),
                    status: document.getElementById('audit-detail-status'),
                    metadata: document.getElementById('audit-detail-metadata'),
                };
                const filterElements = {
                    search: document.getElementById('auditSearch'),
                    actor: document.getElementById('auditActor'),
                    module: document.getElementById('auditModule'),
                    status: document.getElementById('auditStatus'),
                };
                const summaryFields = {
                    total: document.getElementById('audit-summary-total'),
                    success: document.getElementById('audit-summary-success'),
                    failed: document.getElementById('audit-summary-failed'),
                    actors: document.getElementById('audit-summary-actors'),
                };
                const rows = Array.from(tableBody.querySelectorAll('[data-audit-row]'));
                const existingEmptyRow = document.getElementById('audit-empty-row');
                const paginationWrap = document.getElementById('audit-pagination-wrap');

                if (!existingEmptyRow) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.id = 'audit-live-empty-row';
                    emptyRow.classList.add('hidden');
                    emptyRow.innerHTML = `
                        <td colspan="3" class="px-4 py-10 text-center text-sm text-gray-400">
                            No audit events match the live filters on this page.
                        </td>
                    `;
                    tableBody.appendChild(emptyRow);
                }

                const liveEmptyRow = document.getElementById('audit-live-empty-row');

                const updateSummary = (visibleRows) => {
                    const successCount = visibleRows.filter(row => row.dataset.status === 'success').length;
                    const failedCount = visibleRows.filter(row => row.dataset.status === 'failed').length;
                    const actorIds = new Set(
                        visibleRows
                            .map(row => row.dataset.actorId || '')
                            .filter(value => value !== '')
                    );

                    summaryFields.total.textContent = visibleRows.length.toLocaleString();
                    summaryFields.success.textContent = successCount.toLocaleString();
                    summaryFields.failed.textContent = failedCount.toLocaleString();
                    summaryFields.actors.textContent = actorIds.size.toLocaleString();
                };

                const applyLiveFilters = () => {
                    const search = String(filterElements.search?.value || '').trim().toLowerCase();
                    const actor = String(filterElements.actor?.value || '').trim();
                    const module = String(filterElements.module?.value || '').trim().toLowerCase();
                    const status = String(filterElements.status?.value || '').trim().toLowerCase();

                    const visibleRows = rows.filter((row) => {
                        if (search && !(row.dataset.search || '').includes(search)) {
                            return false;
                        }

                        if (actor && (row.dataset.actorId || '') !== actor) {
                            return false;
                        }

                        if (module && (row.dataset.module || '') !== module) {
                            return false;
                        }

                        if (status && (row.dataset.status || '') !== status) {
                            return false;
                        }

                        return true;
                    });

                    rows.forEach((row) => {
                        row.classList.toggle('hidden', !visibleRows.includes(row));
                    });

                    if (liveEmptyRow) {
                        liveEmptyRow.classList.toggle('hidden', visibleRows.length !== 0 || rows.length === 0);
                    }

                    if (paginationWrap) {
                        paginationWrap.classList.toggle('opacity-50', visibleRows.length !== rows.length);
                    }

                    updateSummary(visibleRows);
                };

                const closeModal = () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                };

                const openModal = (payload) => {
                    fields.title.textContent = payload.summary || 'Audit Event';
                    fields.subtitle.textContent = payload.actor || 'System / Guest';
                    fields.actor.textContent = `${payload.actor || 'System / Guest'}${payload.role ? ' (' + formatLabel(payload.role) + ')' : ''}`;
                    fields.time.textContent = payload.timestamp || '--';
                    fields.action.textContent = formatLabel(payload.action);
                    fields.module.textContent = formatLabel(payload.module);
                    fields.target.textContent = payload.target_type || payload.target_id
                        ? `${formatLabel(payload.target_type)} ${payload.target_id ? '#' + payload.target_id : ''}`.trim()
                        : '--';
                    fields.route.textContent = `${payload.route || '--'} / ${payload.method || '--'}`;
                    fields.status.textContent = `${payload.status || '--'}${payload.ip_address ? ' / ' + payload.ip_address : ''}`;
                    fields.metadata.textContent = JSON.stringify(payload.metadata || {}, null, 2);

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                };

                document.querySelectorAll('[data-audit-detail]').forEach((button) => {
                    button.addEventListener('click', () => {
                        openModal(JSON.parse(button.dataset.auditDetail || '{}'));
                    });
                });

                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                modal.querySelectorAll('[data-audit-close]').forEach((button) => {
                    button.addEventListener('click', closeModal);
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeModal();
                    }
                });

                Object.values(filterElements).forEach((element) => {
                    if (!element) {
                        return;
                    }

                    const eventName = element.tagName === 'SELECT' || element.type === 'date' ? 'change' : 'input';
                    element.addEventListener(eventName, applyLiveFilters);

                    if (eventName !== 'input') {
                        element.addEventListener('input', applyLiveFilters);
                    }
                });

                filterForm.addEventListener('reset', () => {
                    window.requestAnimationFrame(applyLiveFilters);
                });

                applyLiveFilters();
            });
        </script>
    @endpush
@endsection
