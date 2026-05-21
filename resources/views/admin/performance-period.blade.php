@extends('layouts.admin')

@section('main-content')
    <section class="space-y-4 px-3 md:px-6">
        <div class="rounded-2xl border border-white/10 bg-transparent p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-gray-100 sm:text-xl">Performance Periods</h1>
                    <p class="mt-1 text-sm text-gray-300">Manage performance cycles. Only one can be active.</p>
                </div>
                <button
                    id="openCreatePeriodBtn"
                    type="button"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-500">
                    Create Period
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-transparent shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-sm">
                    <thead class="bg-gray-900/70 text-xs uppercase tracking-wide text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Dates</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Data Summary</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-gray-200">
                        @forelse ($periods as $period)
                            @php
                                $startDisplay = $period->start_date ? \Carbon\Carbon::parse($period->start_date)->format('M d, Y') : '--';
                                $endDisplay = $period->end_date ? \Carbon\Carbon::parse($period->end_date)->format('M d, Y') : '--';
                                $startValue = $period->start_date ? \Carbon\Carbon::parse($period->start_date)->format('Y-m-d') : '';
                                $endValue = $period->end_date ? \Carbon\Carbon::parse($period->end_date)->format('Y-m-d') : '';
                                $hasData = ((int) ($period->uwp_count ?? 0) + (int) ($period->opcr_count ?? 0) + (int) ($period->ipcr_count ?? 0) + (int) ($period->ors_count ?? 0)) > 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-100">{{ $period->name }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-300">
                                    {{ $startDisplay }} - {{ $endDisplay }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($period->is_active)
                                        <span class="inline-flex items-center rounded-full border border-emerald-600/40 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-300">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-gray-600/50 bg-gray-700/50 px-2.5 py-1 text-xs font-semibold text-gray-300">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-300">
                                    <div class="grid grid-cols-2 gap-1 sm:grid-cols-4">
                                        <span>UWP: {{ (int) ($period->uwp_count ?? 0) }}</span>
                                        <span>OPCR: {{ (int) ($period->opcr_count ?? 0) }}</span>
                                        <span>IPCR: {{ (int) ($period->ipcr_count ?? 0) }}</span>
                                        <span>ORS: {{ (int) ($period->ors_count ?? 0) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @if (!$period->is_active)
                                            <form action="{{ route('admin.performance-periods.activate', ['period' => $period->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500">
                                                    Set Active
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.performance-periods.deactivate', ['period' => $period->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="rounded-lg border border-gray-500 px-3 py-1.5 text-xs font-semibold text-gray-100 hover:bg-gray-700/60">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @endif

                                        <button
                                            type="button"
                                            data-edit-period
                                            data-id="{{ $period->id }}"
                                            data-name="{{ $period->name }}"
                                            data-start="{{ $startValue }}"
                                            data-end="{{ $endValue }}"
                                            data-has-data="{{ $hasData ? 1 : 0 }}"
                                            class="rounded-lg border border-gray-500 px-3 py-1.5 text-xs font-semibold text-gray-100 hover:bg-gray-700/60">
                                            Edit
                                        </button>

                                        @if (!$hasData)
                                            <form action="{{ route('admin.performance-periods.destroy', ['period' => $period->id]) }}" method="POST" onsubmit="return confirm('Delete this period?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg border border-rose-500/60 bg-rose-500/10 px-3 py-1.5 text-xs font-semibold text-rose-300 hover:bg-rose-500/20">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" disabled title="Cannot delete. Period already in use." class="cursor-not-allowed rounded-lg border border-gray-600 px-3 py-1.5 text-xs font-semibold text-gray-500 opacity-60">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">
                                    No performance periods found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="createPeriodModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-3 py-6">
        <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-gray-900 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="text-base font-semibold text-gray-100">Create Performance Period</h2>
                <button type="button" data-close-create class="rounded-lg px-2 py-1 text-gray-400 hover:bg-gray-800 hover:text-white">x</button>
            </div>
            <form action="{{ route('admin.performance-periods.store') }}" method="POST" class="space-y-4 px-5 py-4">
                @csrf
                <div>
                    <label for="createPeriodName" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-400">Name</label>
                    <input id="createPeriodName" name="name" type="text" required value="{{ old('name') }}"
                        class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500" />
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="createPeriodStart" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-400">Start Date</label>
                        <input id="createPeriodStart" name="start_date" type="date" required value="{{ old('start_date') }}"
                            class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500" />
                    </div>
                    <div>
                        <label for="createPeriodEnd" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-400">End Date</label>
                        <input id="createPeriodEnd" name="end_date" type="date" required value="{{ old('end_date') }}"
                            class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500" />
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-300">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-white/20 bg-gray-800 text-blue-600" {{ old('is_active') ? 'checked' : '' }} />
                    Set as active
                </label>
                <div class="flex justify-end gap-2 border-t border-white/10 pt-3">
                    <button type="button" data-close-create class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800">Cancel</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500">Save Period</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editPeriodModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-3 py-6">
        <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-gray-900 shadow-xl">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="text-base font-semibold text-gray-100">Edit Performance Period</h2>
                <button type="button" data-close-edit class="rounded-lg px-2 py-1 text-gray-400 hover:bg-gray-800 hover:text-white">x</button>
            </div>
            <form id="editPeriodForm" action="#" method="POST" class="space-y-4 px-5 py-4">
                @csrf
                @method('PUT')

                <div id="editLockedNotice" class="hidden rounded-lg border border-amber-700/40 bg-amber-900/20 px-3 py-2 text-xs text-amber-200">
                    This period already has data. Dates are locked. You may change name only.
                </div>

                <div>
                    <label for="editPeriodName" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-400">Name</label>
                    <input id="editPeriodName" name="name" type="text" required
                        class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="editPeriodStart" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-400">Start Date</label>
                        <input id="editPeriodStart" name="start_date" type="date" required
                            class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500" />
                        <input id="editPeriodStartHidden" type="hidden" name="start_date" disabled />
                    </div>
                    <div>
                        <label for="editPeriodEnd" class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-400">End Date</label>
                        <input id="editPeriodEnd" name="end_date" type="date" required
                            class="w-full rounded-lg border border-white/10 bg-gray-800 px-3 py-2 text-sm text-gray-100 outline-none focus:border-blue-500" />
                        <input id="editPeriodEndHidden" type="hidden" name="end_date" disabled />
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-white/10 pt-3">
                    <button type="button" data-close-edit class="rounded-lg border border-white/10 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800">Cancel</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500">Update Period</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const createModal = document.getElementById('createPeriodModal');
                const editModal = document.getElementById('editPeriodModal');
                const openCreateBtn = document.getElementById('openCreatePeriodBtn');

                const editForm = document.getElementById('editPeriodForm');
                const editName = document.getElementById('editPeriodName');
                const editStart = document.getElementById('editPeriodStart');
                const editEnd = document.getElementById('editPeriodEnd');
                const editStartHidden = document.getElementById('editPeriodStartHidden');
                const editEndHidden = document.getElementById('editPeriodEndHidden');
                const editLockedNotice = document.getElementById('editLockedNotice');

                const updateUrlTemplate = @json(route('admin.performance-periods.update', ['period' => '__ID__']));

                function openModal(modal) {
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function closeModal(modal) {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    if ((createModal?.classList.contains('hidden') ?? true) && (editModal?.classList.contains('hidden') ?? true)) {
                        document.body.classList.remove('overflow-hidden');
                    }
                }

                function setDateLock(locked) {
                    if (!editStart || !editEnd || !editStartHidden || !editEndHidden) return;

                    editStart.disabled = locked;
                    editEnd.disabled = locked;

                    editStartHidden.disabled = !locked;
                    editEndHidden.disabled = !locked;

                    editStartHidden.value = editStart.value;
                    editEndHidden.value = editEnd.value;

                    if (locked) {
                        editLockedNotice?.classList.remove('hidden');
                    } else {
                        editLockedNotice?.classList.add('hidden');
                    }
                }

                function openEditModal(button) {
                    if (!button || !editForm || !editName || !editStart || !editEnd) return;

                    const periodId = button.dataset.id || '';
                    const periodName = button.dataset.name || '';
                    const startDate = button.dataset.start || '';
                    const endDate = button.dataset.end || '';
                    const hasData = Number(button.dataset.hasData || 0) === 1;

                    const encodedToken = encodeURIComponent('__ID__');
                    let actionUrl = updateUrlTemplate.replace('__ID__', encodeURIComponent(periodId));
                    actionUrl = actionUrl.replace(encodedToken, encodeURIComponent(periodId));

                    editForm.action = actionUrl;
                    editName.value = periodName;
                    editStart.value = startDate;
                    editEnd.value = endDate;

                    setDateLock(hasData);
                    openModal(editModal);
                }

                openCreateBtn?.addEventListener('click', () => openModal(createModal));

                document.querySelectorAll('[data-close-create]').forEach((btn) => {
                    btn.addEventListener('click', () => closeModal(createModal));
                });

                document.querySelectorAll('[data-close-edit]').forEach((btn) => {
                    btn.addEventListener('click', () => closeModal(editModal));
                });

                document.querySelectorAll('[data-edit-period]').forEach((btn) => {
                    btn.addEventListener('click', () => openEditModal(btn));
                });

                [createModal, editModal].forEach((modal) => {
                    modal?.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            closeModal(modal);
                        }
                    });
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') return;
                    closeModal(editModal);
                    closeModal(createModal);
                });
            })();
        </script>
    @endpush
@endsection
