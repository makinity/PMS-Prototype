@extends('layouts.admin')

@section('main-content')
    @php
        if (!isset($users) || !isset($offices) || !isset($filters)) {
            $payload = \App\Http\Controllers\Admin\UsersController::buildIndexPayload(request());
            $users = $payload['users'];
            $offices = $payload['offices'];
            $filters = $payload['filters'];
        }

        $indexUrl = \Illuminate\Support\Facades\Route::has('admin.users') ? route('admin.users') : url()->current();
        $canUpdateRoute = \Illuminate\Support\Facades\Route::has('admin.users.update');
        $canToggleRoute = \Illuminate\Support\Facades\Route::has('admin.users.toggle');
        $canResetRoute = \Illuminate\Support\Facades\Route::has('admin.users.reset-password');
        $canSendCodeRoute = \Illuminate\Support\Facades\Route::has('admin.users.send-code');
        $updateRouteTemplate = $canUpdateRoute ? route('admin.users.update', ['user' => '__ID__']) : '';
        $toggleRouteTemplate = $canToggleRoute ? route('admin.users.toggle', ['user' => '__ID__']) : '';
        $resetRouteTemplate = $canResetRoute ? route('admin.users.reset-password', ['user' => '__ID__']) : '';
        $sendCodeRouteTemplate = $canSendCodeRoute ? route('admin.users.send-code', ['user' => '__ID__']) : '';
    @endphp

    <section class="space-y-4 px-3 md:px-6">
        <div class="min-w-0 rounded-xl border border-white/10 bg-transparent p-4 shadow-sm">
            <h1 class="text-lg font-semibold text-gray-100 sm:text-xl">Users Management</h1>
            <p class="mt-1 text-sm text-gray-300">Manage roles, offices, and activation status.</p>
        </div>

        @if (session('temporary_password'))
            <div class="rounded-xl border border-amber-700/40 bg-amber-900/20 px-4 py-3 text-sm text-amber-200">
                Temporary password for <span class="font-semibold">{{ session('temporary_password_user') }}</span>:
                <span class="font-mono font-semibold">{{ session('temporary_password') }}</span>
            </div>
        @endif

        <div class="min-w-0 rounded-xl border border-white/10 bg-transparent p-4 shadow-sm">
            <form method="GET" action="{{ $indexUrl }}" class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                <div class="min-w-0 xl:col-span-2">
                    <label for="filterSearch" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Search</label>
                    <input
                        id="filterSearch"
                        type="text"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Name, email, employee ID"
                        style="background:#0f172a;color:#e5e7eb;"
                        class="w-full rounded-lg border border-white/10 bg-gray-900/60 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500" />
                </div>

                <div>
                    <label for="filterRole" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Role</label>
                    <select
                        id="filterRole"
                        name="role"
                        style="background:#0f172a;color:#e5e7eb;"
                        class="w-full rounded-lg border border-white/10 bg-gray-900/60 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500">
                        <option value="">All Roles</option>
                        <option value="employee" @selected(($filters['role'] ?? '') === 'employee')>Employee</option>
                        <option value="supervisor" @selected(($filters['role'] ?? '') === 'supervisor')>Supervisor</option>
                        <option value="dept-head" @selected(($filters['role'] ?? '') === 'dept-head')>Dept Head</option>
                        <option value="pmt" @selected(($filters['role'] ?? '') === 'pmt')>PMT</option>
                    </select>
                </div>

                <div>
                    <label for="filterOffice" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Office</label>
                    <select
                        id="filterOffice"
                        name="office_id"
                        style="background:#0f172a;color:#e5e7eb;"
                        class="w-full rounded-lg border border-white/10 bg-gray-900/60 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500">
                        <option value="">All Offices</option>
                        @foreach ($offices as $office)
                            <option value="{{ $office->id }}" @selected((string) ($filters['office_id'] ?? '') === (string) $office->id)>
                                {{ $office->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filterStatus" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Status</label>
                    <select
                        id="filterStatus"
                        name="status"
                        style="background:#0f172a;color:#e5e7eb;"
                        class="w-full rounded-lg border border-white/10 bg-gray-900/60 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500">
                        <option value="all" @selected(($filters['status'] ?? 'all') === 'all')>All Statuses</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                        <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending Activation</option>
                        <option value="disabled" @selected(($filters['status'] ?? '') === 'disabled')>Disabled</option>
                    </select>
                </div>

                <div class="md:col-span-2 xl:col-span-5">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <div class="min-w-0 rounded-xl border border-white/10 bg-transparent shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-xs sm:text-sm">
                    <thead class="bg-gray-900/70 text-[11px] uppercase tracking-wide text-gray-400 sm:text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Employee ID</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Position</th>
                            <th class="px-4 py-3 text-left">Activation Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-gray-200">
                        @forelse ($users as $user)
                            @php
                                if (is_null($user->activated_at)) {
                                    $statusLabel = 'Pending Activation';
                                    $statusClasses = 'border border-amber-600/50 bg-amber-500/10 text-amber-300';
                                } elseif (!$user->is_active) {
                                    $statusLabel = 'Disabled';
                                    $statusClasses = 'border border-rose-600/50 bg-rose-500/10 text-rose-300';
                                } else {
                                    $statusLabel = 'Active';
                                    $statusClasses = 'border border-emerald-600/50 bg-emerald-500/10 text-emerald-300';
                                }
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-gray-300">{{ $user->employee_id ?: '--' }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-100">{{ $user->name }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-300">{{ $user->position ?: '--' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusClasses }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end">
                                        <button
                                            type="button"
                                            data-open-user-modal
                                            data-id="{{ $user->id }}"
                                            data-employee-id="{{ $user->employee_id ?? '' }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ strtolower((string) $user->role) }}"
                                            data-office="{{ $user->office?->name ?? '' }}"
                                            data-office-id="{{ $user->office_id ?? '' }}"
                                            data-position="{{ $user->position ?? '' }}"
                                            data-is-active="{{ $user->is_active ? 1 : 0 }}"
                                            data-activated-at="{{ $user->activated_at ? $user->activated_at->format('M d, Y h:i A') : '' }}"
                                            class="rounded-lg border border-gray-500 px-3 py-1.5 text-xs font-semibold text-gray-100 hover:bg-gray-700/60">
                                            View
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-white/10 px-4 py-3">
                {{ $users->links() }}
            </div>
        </div>
    </section>

    <div id="userDetailsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-3 py-6">
        <div class="w-full max-w-2xl overflow-hidden rounded-2xl border border-white/10 bg-gray-900/95 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="text-base font-semibold text-gray-100">User Details</h2>
                <button type="button" id="closeUserModalTop" class="rounded-lg px-2 py-1 text-gray-400 hover:bg-gray-800 hover:text-white">x</button>
            </div>

            <div class="max-h-[80vh] overflow-y-auto break-words px-5 py-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Employee ID</p>
                        <p id="vmEmployeeId" class="mt-1 text-sm font-medium text-gray-100">--</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Name</p>
                        <p id="vmName" class="mt-1 text-sm font-medium text-gray-100">--</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Email</p>
                        <p id="vmEmail" class="mt-1 text-sm font-medium text-gray-100">--</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Role</p>
                        <span id="vmRole" class="mt-1 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold">--</span>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Office</p>
                        <p id="vmOffice" class="mt-1 text-sm font-medium text-gray-100">N/A</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Position</p>
                        <p id="vmPosition" class="mt-1 text-sm font-medium text-gray-100">--</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-400">Activation Status</p>
                        <span id="vmActivationStatus" class="mt-1 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold">--</span>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs uppercase tracking-wide text-gray-400">Activated At</p>
                        <p id="vmActivatedAt" class="mt-1 text-sm font-medium text-gray-100">--</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-white/10 px-5 py-4">


                <form id="modalResetForm" action="#" method="POST" onsubmit="return confirm('Reset password for this user?');">
                    @csrf
                    <button type="submit" id="modalResetBtn" class="rounded-lg border border-amber-500/60 bg-amber-500/10 px-4 py-2 text-sm font-medium text-amber-300 hover:bg-amber-500/20">
                        Reset Password
                    </button>
                </form>

                <form id="modalSendCodeForm" action="#" method="POST">
                    @csrf
                    <button type="submit" id="modalSendCodeBtn" class="rounded-lg border border-sky-500/60 bg-sky-500/10 px-4 py-2 text-sm font-medium text-sky-300 hover:bg-sky-500/20">
                        Send Employee Code
                    </button>
                </form>

                <button type="button" id="openEditUserFromDetails" class="rounded-lg border border-gray-500 px-4 py-2 text-sm font-medium text-gray-100 hover:bg-gray-700/60">
                    Edit
                </button>

                <button type="button" id="closeUserModalBottom" class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800">
                    Close
                </button>
            </div>
        </div>
    </div>

    <div id="userEditModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-3 py-6">
        <div class="w-full max-w-2xl overflow-hidden rounded-2xl border border-white/10 bg-gray-900/95 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="text-base font-semibold text-gray-100">Edit User</h2>
                <button type="button" id="closeUserEditTop" class="rounded-lg px-2 py-1 text-gray-400 hover:bg-gray-800 hover:text-white">x</button>
            </div>

            <div class="max-h-[80vh] overflow-y-auto break-words px-5 py-4">
                <form id="userEditForm" action="#" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label for="editName" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Name</label>
                            <input id="editName" name="name" type="text" required
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500" />
                        </div>
                        <div>
                            <label for="editEmail" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Email</label>
                            <input id="editEmail" name="email" type="email" required
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500" />
                        </div>
                        <div>
                            <label for="editRole" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Role</label>
                            <select id="editRole" name="role" required
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500">
                                <option value="employee">Employee</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="dept-head">Dept Head</option>
                                <option value="pmt">PMT</option>
                            </select>
                        </div>
                        <div>
                            <label for="editOffice" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Office</label>
                            <select id="editOffice" name="office_id"
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500">
                                <option value="">Select office...</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="editPosition" class="mb-1 block text-xs uppercase tracking-wide text-gray-400">Position</label>
                            <input id="editPosition" name="position" type="text"
                                class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500" />
                        </div>
                    </div>

                    <p id="editOfficeHint" class="text-xs text-gray-400">
                        Required for Employee/Supervisor/Dept Head. PMT has no office.
                    </p>
                    <p id="deptHeadWarning" class="hidden rounded-lg border border-amber-700/40 bg-amber-900/20 px-3 py-2 text-xs text-amber-200">
                        Only one Dept Head per Office is allowed. If another exists, saving will fail.
                    </p>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-white/10 pt-4">
                        <button type="button" id="closeUserEditBottom" class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800">
                            Close
                        </button>
                        <button type="submit" id="saveUserEditBtn" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const detailsModal = document.getElementById('userDetailsModal');
                const editModal = document.getElementById('userEditModal');

                const resetForm = document.getElementById('modalResetForm');
                const resetBtn = document.getElementById('modalResetBtn');
                const sendCodeForm = document.getElementById('modalSendCodeForm');
                const sendCodeBtn = document.getElementById('modalSendCodeBtn');
                const openEditFromDetailsBtn = document.getElementById('openEditUserFromDetails');

                const editForm = document.getElementById('userEditForm');
                const editNameInput = document.getElementById('editName');
                const editEmailInput = document.getElementById('editEmail');
                const editRoleSelect = document.getElementById('editRole');
                const editOfficeSelect = document.getElementById('editOffice');
                const editPositionInput = document.getElementById('editPosition');
                const editOfficeHint = document.getElementById('editOfficeHint');
                const deptHeadWarning = document.getElementById('deptHeadWarning');
                const saveUserEditBtn = document.getElementById('saveUserEditBtn');

                const fields = {
                    employeeId: document.getElementById('vmEmployeeId'),
                    name: document.getElementById('vmName'),
                    email: document.getElementById('vmEmail'),
                    role: document.getElementById('vmRole'),
                    office: document.getElementById('vmOffice'),
                    position: document.getElementById('vmPosition'),
                    activationStatus: document.getElementById('vmActivationStatus'),
                    activatedAt: document.getElementById('vmActivatedAt'),
                };

                let currentUserData = null;

                const updateUrlTemplate = @json($updateRouteTemplate);
                const resetUrlTemplate = @json($resetRouteTemplate);
                const sendCodeRouteTemplate = @json($sendCodeRouteTemplate);

                function buildUrl(template, id) {
                    const encodedToken = encodeURIComponent('__ID__');
                    let actionUrl = template.replace('__ID__', encodeURIComponent(id));
                    actionUrl = actionUrl.replace(encodedToken, encodeURIComponent(id));

                    return actionUrl;
                }

                function showSnackbar(type, message) {
                    if (window.PMSnackbar?.show) {
                        window.PMSnackbar.show({ type, message });
                        return;
                    }

                    console[type === 'error' ? 'error' : 'log'](message);
                }

                function refreshBodyLock() {
                    const anyOpen = !(detailsModal?.classList.contains('hidden') ?? true)
                        || !(editModal?.classList.contains('hidden') ?? true);
                    document.body.classList.toggle('overflow-hidden', anyOpen);
                }

                function setBadgeClasses(element, type, key) {
                    if (!element) return;

                    let classes = 'border border-gray-600/60 bg-gray-700/60 text-gray-200';

                    if (type === 'role') {
                        classes = {
                            employee: 'border border-gray-600/60 bg-gray-700/60 text-gray-200',
                            supervisor: 'border border-blue-600/50 bg-blue-500/10 text-blue-300',
                            'dept-head': 'border border-purple-600/50 bg-purple-500/10 text-purple-300',
                            pmt: 'border border-amber-600/50 bg-amber-500/10 text-amber-300',
                        }[key] || classes;
                    }

                    if (type === 'account') {
                        classes = key === 'active'
                            ? 'border border-emerald-600/50 bg-emerald-500/10 text-emerald-300'
                            : 'border border-rose-600/50 bg-rose-500/10 text-rose-300';
                    }

                    if (type === 'activation') {
                        classes = {
                            activated: 'border border-emerald-600/50 bg-emerald-500/10 text-emerald-300',
                            disabled: 'border border-rose-600/50 bg-rose-500/10 text-rose-300',
                            pending: 'border border-amber-600/50 bg-amber-500/10 text-amber-300',
                        }[key] || classes;
                    }

                    element.className = `mt-1 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold ${classes}`;
                }

                function normalizeUserData(source) {
                    const dataset = source?.dataset ? source.dataset : source || {};

                    return {
                        id: String(dataset.id || ''),
                        employee_id: String(dataset.employeeId || dataset.employee_id || ''),
                        name: String(dataset.name || ''),
                        email: String(dataset.email || ''),
                        role: String(dataset.role || '').toLowerCase(),
                        office: String(dataset.office || ''),
                        office_id: String(dataset.officeId || dataset.office_id || ''),
                        position: String(dataset.position || ''),
                        is_active: Number(dataset.isActive || dataset.is_active || 0) === 1,
                        activated_at: String(dataset.activatedAt || dataset.activated_at || ''),
                    };
                }

                function applyOfficeRuleByRole(roleValue) {
                    const role = String(roleValue || '').toLowerCase();
                    const isPmt = role === 'pmt';

                    if (!editOfficeSelect) return;

                    if (isPmt) {
                        editOfficeSelect.value = '';
                        editOfficeSelect.disabled = true;
                        editOfficeSelect.required = false;
                        if (editOfficeHint) editOfficeHint.textContent = 'N/A for PMT.';
                    } else {
                        editOfficeSelect.disabled = false;
                        editOfficeSelect.required = true;
                        if (editOfficeHint) editOfficeHint.textContent = 'Required for Employee/Supervisor/Dept Head. PMT has no office.';
                    }

                    if (deptHeadWarning) {
                        deptHeadWarning.classList.toggle('hidden', role !== 'dept-head');
                    }
                }

                function openUserDetailsModal(button) {
                    if (!button || !detailsModal) return;

                    currentUserData = normalizeUserData(button);

                    const userId = currentUserData.id;
                    const employeeId = currentUserData.employee_id || '--';
                    const name = currentUserData.name || '--';
                    const email = currentUserData.email || '--';
                    const role = currentUserData.role;
                    const office = currentUserData.office.trim();
                    const position = currentUserData.position.trim();
                    const isActive = currentUserData.is_active;
                    const activatedAt = currentUserData.activated_at.trim();

                    fields.employeeId.textContent = employeeId;
                    fields.name.textContent = name;
                    fields.email.textContent = email;
                    fields.office.textContent = office || 'N/A';
                    fields.position.textContent = position || '--';
                    fields.activatedAt.textContent = activatedAt || '--';

                    fields.role.textContent = role ? role.toUpperCase() : '--';
                    setBadgeClasses(fields.role, 'role', role);

                    const activationState = activatedAt
                        ? (isActive ? 'activated' : 'disabled')
                        : 'pending';
                    fields.activationStatus.textContent = activationState === 'activated'
                        ? 'Activated'
                        : activationState === 'disabled'
                            ? 'Disabled'
                            : 'Pending Activation';
                    setBadgeClasses(fields.activationStatus, 'activation', activationState);



                    if (resetUrlTemplate && userId && resetForm && resetBtn) {
                        resetForm.action = buildUrl(resetUrlTemplate, userId);
                        resetBtn.disabled = false;
                        resetBtn.className = 'rounded-lg border border-amber-500/60 bg-amber-500/10 px-4 py-2 text-sm font-medium text-amber-300 hover:bg-amber-500/20';
                        resetBtn.title = '';
                    } else if (resetBtn) {
                        resetBtn.disabled = true;
                        resetBtn.className = 'cursor-not-allowed rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-500 opacity-70';
                        resetBtn.title = 'Route admin.users.reset-password not available.';
                    }

                    const canSendCode = !!activatedAt === false && !!employeeId && !!email;
                    if (sendCodeRouteTemplate && userId && sendCodeForm && sendCodeBtn) {
                        sendCodeForm.action = buildUrl(sendCodeRouteTemplate, userId);
                        sendCodeBtn.disabled = !canSendCode;
                        sendCodeBtn.className = canSendCode
                            ? 'rounded-lg border border-sky-500/60 bg-sky-500/10 px-4 py-2 text-sm font-medium text-sky-300 hover:bg-sky-500/20'
                            : 'cursor-not-allowed rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-500 opacity-70';
                        sendCodeBtn.title = canSendCode
                            ? ''
                            : 'Only pending accounts with a valid employee code and email can receive the activation email.';
                    } else if (sendCodeBtn) {
                        sendCodeBtn.disabled = true;
                        sendCodeBtn.className = 'cursor-not-allowed rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-500 opacity-70';
                        sendCodeBtn.title = 'Route admin.users.send-code not available.';
                    }

                    detailsModal.classList.remove('hidden');
                    detailsModal.classList.add('flex');
                    refreshBodyLock();
                }

                function closeUserDetailsModal() {
                    if (!detailsModal) return;
                    detailsModal.classList.add('hidden');
                    detailsModal.classList.remove('flex');
                    refreshBodyLock();
                }

                function openUserEditModal(userDataFromViewModalOrButton) {
                    if (!editModal || !editForm || !editNameInput || !editEmailInput || !editRoleSelect || !editOfficeSelect || !editPositionInput) return;

                    const userData = normalizeUserData(userDataFromViewModalOrButton);
                    if (!userData.id) return;

                    currentUserData = userData;

                    if (updateUrlTemplate) {
                        editForm.action = buildUrl(updateUrlTemplate, userData.id);
                        if (saveUserEditBtn) {
                            saveUserEditBtn.disabled = false;
                            saveUserEditBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                            saveUserEditBtn.title = '';
                        }
                    } else if (saveUserEditBtn) {
                        saveUserEditBtn.disabled = true;
                        saveUserEditBtn.classList.add('opacity-60', 'cursor-not-allowed');
                        saveUserEditBtn.title = 'Route admin.users.update not available.';
                    }

                    editNameInput.value = userData.name || '';
                    editEmailInput.value = userData.email || '';
                    editRoleSelect.value = userData.role || 'employee';
                    editOfficeSelect.value = userData.office_id || '';
                    editPositionInput.value = userData.position || '';

                    applyOfficeRuleByRole(editRoleSelect.value);
                    closeUserDetailsModal();

                    editModal.classList.remove('hidden');
                    editModal.classList.add('flex');
                    refreshBodyLock();
                }

                function closeUserEditModal() {
                    if (!editModal) return;
                    editModal.classList.add('hidden');
                    editModal.classList.remove('flex');
                    refreshBodyLock();
                }

                document.querySelectorAll('[data-open-user-modal]').forEach((button) => {
                    button.addEventListener('click', () => openUserDetailsModal(button));
                });

                openEditFromDetailsBtn?.addEventListener('click', () => {
                    if (!currentUserData) return;
                    openUserEditModal(currentUserData);
                });

                editRoleSelect?.addEventListener('change', () => {
                    applyOfficeRuleByRole(editRoleSelect.value);
                });

                document.getElementById('closeUserModalTop')?.addEventListener('click', closeUserDetailsModal);
                document.getElementById('closeUserModalBottom')?.addEventListener('click', closeUserDetailsModal);
                document.getElementById('closeUserEditTop')?.addEventListener('click', closeUserEditModal);
                document.getElementById('closeUserEditBottom')?.addEventListener('click', closeUserEditModal);

                detailsModal?.addEventListener('click', (event) => {
                    if (event.target === detailsModal) {
                        closeUserDetailsModal();
                    }
                });

                editModal?.addEventListener('click', (event) => {
                    if (event.target === editModal) {
                        closeUserEditModal();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        if (!(editModal?.classList.contains('hidden') ?? true)) {
                            closeUserEditModal();
                            return;
                        }
                        closeUserDetailsModal();
                    }
                });

                sendCodeForm?.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    if (!currentUserData || !currentUserData.id) return;
                    
                    const originalHTML = sendCodeBtn.innerHTML;
                    sendCodeBtn.disabled = true;
                    sendCodeBtn.innerHTML = '<svg class="inline mr-2 h-4 w-4 animate-spin text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Sending...';

                    try {
                        const token = document.querySelector('input[name="_token"]')?.value;
                        const response = await fetch(sendCodeForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await response.json();
                        
                        if (response.ok) {
                            showSnackbar('success', data.message || 'Code sent successfully!');
                            closeUserDetailsModal();
                        } else {
                            showSnackbar('error', data.message || 'Error sending code.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showSnackbar('error', 'An unexpected error occurred while sending the code.');
                    } finally {
                        sendCodeBtn.disabled = false;
                        sendCodeBtn.innerHTML = originalHTML;
                    }
                });
            })();
        </script>
    @endpush
@endsection
