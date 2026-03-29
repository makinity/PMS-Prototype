@extends('layouts.admin')

@section('main-content')
    @php
        $officeIndexUrl = \Illuminate\Support\Facades\Route::has('admin.office') ? route('admin.office') : url('/administrator/offices');
        $storeAction = \Illuminate\Support\Facades\Route::has('admin.office.create')
            ? route('admin.office.create')
            : url('/administrator/offices/create');
        $updateActionTemplate = \Illuminate\Support\Facades\Route::has('admin.office.update')
            ? route('admin.office.update', ['id' => '__ID__'])
            : url('/administrator/offices/__ID__');
        $destroyActionTemplate = \Illuminate\Support\Facades\Route::has('admin.office.delete')
            ? route('admin.office.delete', ['id' => '__ID__'])
            : url('/administrator/offices/__ID__/delete');
    @endphp

    <section class="space-y-4 px-3 md:px-6">
        <div class="min-w-0 rounded-xl border border-white/10 bg-gray-800/90 p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-gray-100 sm:text-xl">Office Management</h1>
                    <p class="mt-1 text-sm text-gray-300">Manage offices, assign one department head, and review staffing summaries.</p>
                </div>

                <button
                    type="button"
                    id="openCreateOfficeModalBtn"
                    class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500 sm:w-auto">
                    Create Office
                </button>
            </div>
        </div>

        <div class="space-y-3 sm:hidden">
            @forelse ($offices as $office)
                @php
                    $employeesPayload = collect($office->employees ?? [])
                        ->map(fn ($employee) => [
                            'id' => (int) $employee->id,
                            'name' => (string) ($employee->name ?? ''),
                            'email' => (string) ($employee->email ?? ''),
                            'role' => strtolower((string) ($employee->role ?? 'employee')),
                            'position' => (string) ($employee->position ?? ''),
                            'is_active' => (int) ($employee->is_active ? 1 : 0),
                            'activated_at' => $employee->activated_at ? $employee->activated_at->format('Y-m-d H:i:s') : null,
                        ])
                        ->values();
                    $hasBlockingData = (int) ($office->employees_count ?? 0) > 0 || (int) ($office->unit_work_plans_count ?? 0) > 0;
                @endphp
                <article class="rounded-xl border border-white/10 bg-gray-800/90 p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <h2 class="break-words text-sm font-semibold text-gray-100">{{ $office->name }}</h2>
                        <span class="shrink-0 rounded-full border border-white/10 bg-gray-700/70 px-2.5 py-1 text-[11px] font-semibold text-gray-200">
                            {{ $office->code ?: '--' }}
                        </span>
                    </div>

                    <div class="mt-3 space-y-2 text-xs">
                        <div>
                            <p class="uppercase tracking-wide text-gray-500">Dept Head</p>
                            @if ($office->head)
                                <p class="mt-1 break-words text-sm font-medium text-gray-100">{{ $office->head->name }}</p>
                                <p class="break-words text-xs text-gray-400">{{ $office->head->email }}</p>
                            @else
                                <p class="mt-1 text-sm text-gray-400">--</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between rounded-lg border border-white/10 bg-gray-900/50 px-3 py-2">
                            <span class="text-gray-400">UWPs</span>
                            <span class="text-sm font-semibold text-gray-100">{{ (int) ($office->unit_work_plans_count ?? 0) }}</span>
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap justify-end gap-2">
                        <button
                            type="button"
                            data-view-office
                            data-id="{{ $office->id }}"
                            data-code="{{ $office->code ?? '' }}"
                            data-name="{{ $office->name }}"
                            data-head-id="{{ $office->head_id ?? '' }}"
                            data-head-name="{{ $office->head?->name ?? '' }}"
                            data-head-email="{{ $office->head?->email ?? '' }}"
                            data-employees-count="{{ (int) ($office->employees_count ?? 0) }}"
                            data-supervisors-count="{{ (int) ($office->supervisors_count ?? 0) }}"
                            data-uwp-count="{{ (int) ($office->unit_work_plans_count ?? 0) }}"
                            data-employees='@json($employeesPayload)'
                            class="min-w-[80px] rounded-lg border border-gray-500 px-3 py-2 text-sm font-semibold text-gray-100 hover:bg-gray-700/60">
                            View
                        </button>
                        <button
                            type="button"
                            data-edit-office
                            data-id="{{ $office->id }}"
                            data-code="{{ $office->code ?? '' }}"
                            data-name="{{ $office->name }}"
                            data-head-id="{{ $office->head_id ?? '' }}"
                            class="min-w-[80px] rounded-lg border border-white/10 bg-gray-700/70 px-3 py-2 text-sm font-semibold text-gray-100 hover:bg-gray-600/80">
                            Edit
                        </button>
                        <button
                            type="button"
                            data-delete-office
                            data-id="{{ $office->id }}"
                            data-name="{{ $office->name }}"
                            data-code="{{ $office->code ?? '' }}"
                            data-blocked="{{ $hasBlockingData ? 1 : 0 }}"
                            data-employees-count="{{ (int) ($office->employees_count ?? 0) }}"
                            data-uwp-count="{{ (int) ($office->unit_work_plans_count ?? 0) }}"
                            class="min-w-[80px] rounded-lg border border-rose-600/50 bg-rose-500/10 px-3 py-2 text-sm font-semibold text-rose-300 hover:bg-rose-500/20">
                            Delete
                        </button>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-white/10 bg-gray-800/90 px-4 py-10 text-center text-sm text-gray-400 shadow-sm">
                    No offices found.
                </div>
            @endforelse
        </div>

        <div class="hidden min-w-0 rounded-xl border border-white/10 bg-gray-800/90 shadow-sm sm:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-xs sm:text-sm">
                    <thead class="bg-gray-900/70 text-[11px] uppercase tracking-wide text-gray-400 sm:text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Code</th>
                            <th class="px-4 py-3 text-left">Office Name</th>
                            <th class="px-4 py-3 text-left">Dept Head</th>
                            <th class="px-4 py-3 text-center">UWPs</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-gray-200">
                        @forelse ($offices as $office)
                            @php
                                $employeesPayload = collect($office->employees ?? [])
                                    ->map(fn ($employee) => [
                                        'id' => (int) $employee->id,
                                        'name' => (string) ($employee->name ?? ''),
                                        'email' => (string) ($employee->email ?? ''),
                                        'role' => strtolower((string) ($employee->role ?? 'employee')),
                                        'position' => (string) ($employee->position ?? ''),
                                        'is_active' => (int) ($employee->is_active ? 1 : 0),
                                        'activated_at' => $employee->activated_at ? $employee->activated_at->format('Y-m-d H:i:s') : null,
                                    ])
                                    ->values();
                                $hasBlockingData = (int) ($office->employees_count ?? 0) > 0 || (int) ($office->unit_work_plans_count ?? 0) > 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-100">{{ $office->code ?: '--' }}</td>
                                <td class="px-4 py-3 break-words text-gray-100">{{ $office->name }}</td>
                                <td class="px-4 py-3">
                                    @if ($office->head)
                                        <p class="text-sm font-medium text-gray-100">{{ $office->head->name }}</p>
                                        <p class="break-words text-xs text-gray-400">{{ $office->head->email }}</p>
                                    @else
                                        <span class="text-gray-400">--</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center font-medium text-gray-200">{{ (int) ($office->unit_work_plans_count ?? 0) }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button
                                            type="button"
                                            data-view-office
                                            data-id="{{ $office->id }}"
                                            data-code="{{ $office->code ?? '' }}"
                                            data-name="{{ $office->name }}"
                                            data-head-id="{{ $office->head_id ?? '' }}"
                                            data-head-name="{{ $office->head?->name ?? '' }}"
                                            data-head-email="{{ $office->head?->email ?? '' }}"
                                            data-employees-count="{{ (int) ($office->employees_count ?? 0) }}"
                                            data-supervisors-count="{{ (int) ($office->supervisors_count ?? 0) }}"
                                            data-uwp-count="{{ (int) ($office->unit_work_plans_count ?? 0) }}"
                                            data-employees='@json($employeesPayload)'
                                            class="rounded-lg border border-gray-500 px-3 py-1.5 text-xs font-semibold text-gray-100 hover:bg-gray-700/60">
                                            View
                                        </button>
                                        <button
                                            type="button"
                                            data-edit-office
                                            data-id="{{ $office->id }}"
                                            data-code="{{ $office->code ?? '' }}"
                                            data-name="{{ $office->name }}"
                                            data-head-id="{{ $office->head_id ?? '' }}"
                                            class="rounded-lg border border-white/10 bg-gray-700/70 px-3 py-1.5 text-xs font-semibold text-gray-100 hover:bg-gray-600/80">
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            data-delete-office
                                            data-id="{{ $office->id }}"
                                            data-name="{{ $office->name }}"
                                            data-code="{{ $office->code ?? '' }}"
                                            data-blocked="{{ $hasBlockingData ? 1 : 0 }}"
                                            data-employees-count="{{ (int) ($office->employees_count ?? 0) }}"
                                            data-uwp-count="{{ (int) ($office->unit_work_plans_count ?? 0) }}"
                                            class="rounded-lg border border-rose-600/50 bg-rose-500/10 px-3 py-1.5 text-xs font-semibold text-rose-300 hover:bg-rose-500/20">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">
                                    No offices found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="createOfficeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-3 py-6">
        <div class="w-full max-w-2xl overflow-hidden rounded-2xl border border-white/10 bg-gray-900/95 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="text-base font-semibold text-gray-100">Create Office</h2>
                <button type="button" data-close-modal="createOfficeModal" class="rounded-lg px-2 py-1 text-gray-400 hover:bg-gray-800 hover:text-white">x</button>
            </div>

            <div class="max-h-[80vh] overflow-y-auto px-5 py-4">
                <form action="{{ $storeAction }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label for="createOfficeName" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Office Name</label>
                            <input id="createOfficeName" name="name" type="text" required
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label for="createOfficeCode" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Code</label>
                            <input id="createOfficeCode" name="code" type="text" required
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm uppercase text-gray-100 outline-none focus:border-blue-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="createOfficeHead" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Dept Head (Optional)</label>
                            <select id="createOfficeHead" name="head_id"
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500">
                                <option value="">-- Unassigned --</option>
                                @foreach ($deptHeads as $deptHead)
                                    <option value="{{ $deptHead->id }}">
                                        {{ $deptHead->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400">
                        Assigning a Dept Head will automatically link that user to this office.
                    </p>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-white/10 pt-4">
                        <button type="button" data-close-modal="createOfficeModal" class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500">
                            Save Office
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editOfficeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-3 py-6">
        <div class="w-full max-w-2xl overflow-hidden rounded-2xl border border-white/10 bg-gray-900/95 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="text-base font-semibold text-gray-100">Edit Office</h2>
                <button type="button" data-close-modal="editOfficeModal" class="rounded-lg px-2 py-1 text-gray-400 hover:bg-gray-800 hover:text-white">x</button>
            </div>

            <div class="max-h-[80vh] overflow-y-auto px-5 py-4">
                <form id="editOfficeForm" action="{{ $officeIndexUrl }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label for="editOfficeName" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Office Name</label>
                            <input id="editOfficeName" name="name" type="text" required
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label for="editOfficeCode" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Code</label>
                            <input id="editOfficeCode" name="code" type="text" required
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm uppercase text-gray-100 outline-none focus:border-blue-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="editOfficeHead" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Dept Head (Optional)</label>
                            <select id="editOfficeHead" name="head_id"
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500">
                                <option value="">-- Unassigned --</option>
                                @foreach ($deptHeads as $deptHead)
                                    <option value="{{ $deptHead->id }}">
                                        {{ $deptHead->name }}{{ $deptHead->email ? ' (' . $deptHead->email . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400">
                        Assigning a Dept Head will automatically link that user to this office.
                    </p>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-white/10 pt-4">
                        <button type="button" data-close-modal="editOfficeModal" class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500">
                            Update Office
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="viewOfficeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-3 py-6">
        <div class="w-full max-w-4xl overflow-hidden rounded-2xl border border-white/10 bg-gray-900/95 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="text-base font-semibold text-gray-100">Office Details</h2>
                <button type="button" data-close-modal="viewOfficeModal" class="rounded-lg px-2 py-1 text-gray-400 hover:bg-gray-800 hover:text-white">x</button>
            </div>

            <div class="max-h-[80vh] overflow-y-auto break-words px-5 py-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Code</p>
                        <p id="viewOfficeCode" class="mt-1 text-sm font-medium text-gray-100">--</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Office Name</p>
                        <p id="viewOfficeName" class="mt-1 text-sm font-medium text-gray-100">--</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Dept Head</p>
                        <p id="viewOfficeHead" class="mt-1 text-sm font-medium text-gray-100">--</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Employees</p>
                        <p id="viewOfficeEmployees" class="mt-1 text-sm font-medium text-gray-100">0</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Staff</p>
                        <p id="viewOfficeStaffCount" class="mt-1 text-sm font-medium text-gray-100">0</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">UWPs</p>
                        <p id="viewOfficeUwpCount" class="mt-1 text-sm font-medium text-gray-100">0</p>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-white/10 bg-gray-800/70 p-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Staffing</p>
                    <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-lg border border-white/10 bg-gray-900/70 p-3">
                            <p class="text-xs uppercase tracking-wide text-gray-400">Staff</p>
                            <div class="mt-2">
                                <p class="text-[11px] uppercase tracking-wide text-gray-500">Dept Head</p>
                                <div id="officeDeptHeadSlot" class="mt-2 max-h-72 space-y-2 overflow-y-auto pr-1 text-sm text-gray-200 break-words"></div>
                            </div>
                            <div class="mt-3 border-t border-white/10 pt-3">
                                <p class="text-[11px] uppercase tracking-wide text-gray-500">Supervisors</p>
                                <div id="officeSupervisorsList" class="mt-2 max-h-72 space-y-2 overflow-y-auto pr-1 text-sm text-gray-200 break-words"></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-white/10 bg-gray-900/70 p-3">
                            <p class="text-xs uppercase tracking-wide text-gray-400">Employees</p>
                            <div id="officeEmployeesList" class="mt-2 max-h-72 space-y-2 overflow-y-auto pr-1 text-sm text-gray-200 break-words"></div>
                            <p class="mt-3 text-xs text-gray-400">Employees are assigned in the Users module.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
                <button type="button" data-close-modal="viewOfficeModal" class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    <div id="deleteOfficeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-3 py-6">
        <div class="w-full max-w-lg overflow-hidden rounded-2xl border border-white/10 bg-gray-900/95 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="text-base font-semibold text-gray-100">Delete Office</h2>
                <button type="button" data-close-modal="deleteOfficeModal" class="rounded-lg px-2 py-1 text-gray-400 hover:bg-gray-800 hover:text-white">x</button>
            </div>

            <div class="max-h-[80vh] overflow-y-auto px-5 py-4">
                <p class="text-sm text-gray-200">
                    You are about to delete <span id="deleteOfficeLabel" class="font-semibold text-white">this office</span>.
                </p>
                <p id="deleteOfficeBlockedNotice" class="mt-3 hidden rounded-lg border border-rose-700/40 bg-rose-900/20 px-3 py-2 text-xs text-rose-200">
                    Cannot delete this office because it already has related Employees or UWP records.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-white/10 px-5 py-4">
                <button type="button" data-close-modal="deleteOfficeModal" class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800">
                    Cancel
                </button>
                <form id="deleteOfficeForm" action="{{ $officeIndexUrl }}" method="POST" onsubmit="return confirm('Delete this office?');">
                    @csrf
                    <button id="deleteOfficeSubmitBtn" type="submit" class="rounded-lg border border-rose-600/60 bg-rose-500/10 px-4 py-2 text-sm font-medium text-rose-300 hover:bg-rose-500/20">
                        Delete Office
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const createModal = document.getElementById('createOfficeModal');
                const editModal = document.getElementById('editOfficeModal');
                const viewModal = document.getElementById('viewOfficeModal');
                const deleteModal = document.getElementById('deleteOfficeModal');

                const editForm = document.getElementById('editOfficeForm');
                const editName = document.getElementById('editOfficeName');
                const editCode = document.getElementById('editOfficeCode');
                const editHead = document.getElementById('editOfficeHead');

                const viewCode = document.getElementById('viewOfficeCode');
                const viewName = document.getElementById('viewOfficeName');
                const viewHead = document.getElementById('viewOfficeHead');
                const viewEmployees = document.getElementById('viewOfficeEmployees');
                const viewStaffCount = document.getElementById('viewOfficeStaffCount');
                const viewUwpCount = document.getElementById('viewOfficeUwpCount');
                const viewDeptHeadSlot = document.getElementById('officeDeptHeadSlot');
                const viewSupervisorsList = document.getElementById('officeSupervisorsList');
                const viewEmployeesList = document.getElementById('officeEmployeesList');

                const deleteForm = document.getElementById('deleteOfficeForm');
                const deleteLabel = document.getElementById('deleteOfficeLabel');
                const deleteBlockedNotice = document.getElementById('deleteOfficeBlockedNotice');
                const deleteSubmitBtn = document.getElementById('deleteOfficeSubmitBtn');

                const updateTemplate = @json($updateActionTemplate);
                const destroyTemplate = @json($destroyActionTemplate);

                function buildUrl(template, id) {
                    const encodedToken = encodeURIComponent('__ID__');
                    let actionUrl = template.replace('__ID__', encodeURIComponent(id));
                    actionUrl = actionUrl.replace(encodedToken, encodeURIComponent(id));

                    return actionUrl;
                }

                function refreshBodyLock() {
                    const modals = [createModal, editModal, viewModal, deleteModal];
                    const anyVisible = modals.some((modal) => modal && !modal.classList.contains('hidden'));
                    document.body.classList.toggle('overflow-hidden', anyVisible);
                }

                function openModal(modal) {
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    refreshBodyLock();
                }

                function closeModal(modal) {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    refreshBodyLock();
                }

                function parseEmployees(rawEmployees) {
                    try {
                        const decoded = JSON.parse(rawEmployees || '[]');
                        if (!Array.isArray(decoded)) return [];

                        return decoded.map((item) => ({
                            id: Number(item?.id || 0),
                            name: String(item?.name ?? '').trim(),
                            email: String(item?.email ?? '').trim(),
                            role: String(item?.role ?? '').toLowerCase().trim(),
                            position: String(item?.position ?? '').trim(),
                        }));
                    } catch (error) {
                        return [];
                    }
                }

                function badgeClassesByRole(role) {
                    switch (role) {
                        case 'supervisor':
                            return 'border-blue-600/50 bg-blue-500/10 text-blue-300';
                        case 'dept-head':
                            return 'border-purple-600/50 bg-purple-500/10 text-purple-300';
                        case 'pmt':
                            return 'border-amber-600/50 bg-amber-500/10 text-amber-300';
                        default:
                            return 'border-gray-600/60 bg-gray-700/60 text-gray-200';
                    }
                }

                function roleLabel(role) {
                    if (role === 'dept-head') return 'Dept Head';
                    if (role === 'pmt') return 'PMT';
                    if (!role) return 'Employee';
                    return role.charAt(0).toUpperCase() + role.slice(1);
                }

                function appendUserListItem(container, employee, showRoleBadge = true) {
                    const row = document.createElement('div');
                    row.className = 'rounded-lg border border-white/10 bg-gray-800/80 px-3 py-2 break-words';

                    const top = document.createElement('div');
                    top.className = 'flex items-center justify-between gap-2';

                    const name = document.createElement('p');
                    name.className = 'text-sm font-medium text-gray-100';
                    name.textContent = employee.name || '--';

                    top.appendChild(name);
                    if (showRoleBadge) {
                        const badge = document.createElement('span');
                        badge.className = `inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide ${badgeClassesByRole(employee.role)}`;
                        badge.textContent = roleLabel(employee.role);
                        top.appendChild(badge);
                    }

                    row.appendChild(top);

                    if (employee.email) {
                        const email = document.createElement('p');
                        email.className = 'mt-1 text-xs text-gray-400';
                        email.textContent = employee.email;
                        row.appendChild(email);
                    }

                    if (employee.position) {
                        const position = document.createElement('p');
                        position.className = 'mt-1 text-xs text-gray-500';
                        position.textContent = employee.position;
                        row.appendChild(position);
                    }

                    container.appendChild(row);
                }

                function renderStaffingLists(allPeople, deptHead) {
                    if (viewEmployeesList) {
                        viewEmployeesList.replaceChildren();
                    }
                    if (viewSupervisorsList) {
                        viewSupervisorsList.replaceChildren();
                    }
                    if (viewDeptHeadSlot) {
                        viewDeptHeadSlot.replaceChildren();
                    }

                    const supervisors = allPeople.filter((person) => person.role === 'supervisor');
                    const employeesOnly = allPeople.filter((person) => person.role === 'employee');

                    if (viewDeptHeadSlot) {
                        if (!deptHead || !deptHead.name) {
                            const emptyDeptHead = document.createElement('p');
                            emptyDeptHead.className = 'text-sm text-gray-400';
                            emptyDeptHead.textContent = 'No dept head assigned.';
                            viewDeptHeadSlot.appendChild(emptyDeptHead);
                        } else {
                            appendUserListItem(viewDeptHeadSlot, deptHead, true);
                        }
                    }

                    if (viewSupervisorsList) {
                        if (!supervisors.length) {
                            const empty = document.createElement('p');
                            empty.className = 'text-sm text-gray-400';
                            empty.textContent = 'No supervisors assigned.';
                            viewSupervisorsList.appendChild(empty);
                        } else {
                            supervisors.forEach((employee) => appendUserListItem(viewSupervisorsList, employee));
                        }
                    }

                    if (viewEmployeesList) {
                        if (!employeesOnly.length) {
                            const empty = document.createElement('p');
                            empty.className = 'text-sm text-gray-400';
                            empty.textContent = 'No employees assigned.';
                            viewEmployeesList.appendChild(empty);
                        } else {
                            employeesOnly.forEach((employee) => appendUserListItem(viewEmployeesList, employee, true));
                        }
                    }

                    return {
                        supervisors,
                        employeesOnly,
                    };
                }

                function openEditModal(button) {
                    if (!button || !editForm || !editName || !editCode || !editHead) return;

                    const officeId = String(button.dataset.id || '').trim();
                    if (!officeId) return;

                    editForm.action = buildUrl(updateTemplate, officeId);
                    editName.value = String(button.dataset.name || '');
                    editCode.value = String(button.dataset.code || '');
                    editHead.value = String(button.dataset.headId || '');

                    openModal(editModal);
                }

                function openViewModal(button) {
                    if (!button) return;

                    const headName = String(button.dataset.headName || '').trim();
                    const headEmail = String(button.dataset.headEmail || '').trim();
                    const allPeople = parseEmployees(button.dataset.employees || '');
                    const deptHeadFromPeople = allPeople.find((person) => person.role === 'dept-head' && person.name);
                    const deptHead = headName
                        ? { name: headName, email: headEmail, role: 'dept-head', position: '' }
                        : (deptHeadFromPeople || null);
                    const deptHeadExists = !!(deptHead && deptHead.name);

                    if (viewCode) viewCode.textContent = String(button.dataset.code || '--') || '--';
                    if (viewName) viewName.textContent = String(button.dataset.name || '--') || '--';
                    if (viewHead) {
                        if (headName && headEmail) {
                            viewHead.textContent = `${headName} (${headEmail})`;
                        } else if (headName) {
                            viewHead.textContent = headName;
                        } else if (deptHeadFromPeople?.name && deptHeadFromPeople?.email) {
                            viewHead.textContent = `${deptHeadFromPeople.name} (${deptHeadFromPeople.email})`;
                        } else if (deptHeadFromPeople?.name) {
                            viewHead.textContent = deptHeadFromPeople.name;
                        } else {
                            viewHead.textContent = '--';
                        }
                    }
                    if (viewUwpCount) viewUwpCount.textContent = String(button.dataset.uwpCount || '0');
                    const rendered = renderStaffingLists(allPeople, deptHead);
                    const staffCount = (deptHeadExists ? 1 : 0) + rendered.supervisors.length;
                    if (viewEmployees) viewEmployees.textContent = String(rendered.employeesOnly.length);
                    if (viewStaffCount) viewStaffCount.textContent = String(staffCount);

                    openModal(viewModal);
                }

                function openDeleteModal(button) {
                    if (!button || !deleteForm || !deleteLabel || !deleteSubmitBtn || !deleteBlockedNotice) return;

                    const officeId = String(button.dataset.id || '').trim();
                    if (!officeId) return;

                    const officeName = String(button.dataset.name || 'this office').trim();
                    const officeCode = String(button.dataset.code || '').trim();
                    const isBlocked = Number(button.dataset.blocked || 0) === 1;

                    deleteForm.action = buildUrl(destroyTemplate, officeId);
                    deleteLabel.textContent = officeCode ? `${officeName} (${officeCode})` : officeName;
                    deleteBlockedNotice.classList.toggle('hidden', !isBlocked);

                    deleteSubmitBtn.disabled = isBlocked;
                    deleteSubmitBtn.className = isBlocked
                        ? 'cursor-not-allowed rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-500 opacity-70'
                        : 'rounded-lg border border-rose-600/60 bg-rose-500/10 px-4 py-2 text-sm font-medium text-rose-300 hover:bg-rose-500/20';

                    openModal(deleteModal);
                }

                document.getElementById('openCreateOfficeModalBtn')?.addEventListener('click', () => {
                    openModal(createModal);
                });

                document.querySelectorAll('[data-edit-office]').forEach((button) => {
                    button.addEventListener('click', () => openEditModal(button));
                });

                document.querySelectorAll('[data-view-office]').forEach((button) => {
                    button.addEventListener('click', () => openViewModal(button));
                });

                document.querySelectorAll('[data-delete-office]').forEach((button) => {
                    button.addEventListener('click', () => openDeleteModal(button));
                });

                document.querySelectorAll('[data-close-modal]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const modalId = button.getAttribute('data-close-modal');
                        const modal = modalId ? document.getElementById(modalId) : null;
                        closeModal(modal);
                    });
                });

                [createModal, editModal, viewModal, deleteModal].forEach((modal) => {
                    modal?.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            closeModal(modal);
                        }
                    });
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') return;
                    [deleteModal, viewModal, editModal, createModal].forEach((modal) => {
                        if (modal && !modal.classList.contains('hidden')) {
                            closeModal(modal);
                        }
                    });
                });
            })();
        </script>
    @endpush
@endsection
