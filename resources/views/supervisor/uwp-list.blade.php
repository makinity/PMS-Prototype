@extends('layouts.supervisor')

@section('main-content')
    <section class="space-y-6">

        <!-- Header -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Stage I â€“ Unit Work Plan (UWP)
                </p>
                <h1 class="text-2xl font-semibold text-white">Performance Period Planning and Commitment</h1>
            </div>
            <a href="{{ route('supervisor.uwp') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                + Create UWP
            </a>
        </div>

        <!-- UWP List -->
        <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-5 space-y-4">

            <!-- Filter / Context Bar -->
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="space-y-1">
                    <span class="block text-xs uppercase tracking-widest text-slate-400">
                        Office / Unit
                    </span>
                    <p class="text-sm font-medium text-slate-200">
                        Office / Unit: {{ $office?->name ?? 'â€”' }}
                    </p>
                </div>
            </div>

            <!-- Table -->
            @if($lists->isEmpty())
                <div class="text-center py-8 text-slate-400">
                    {{ $office ? 'No Unit Work Plans found for your assigned office.' : 'No assigned office found for your account.' }}
                </div>
            @else
                <div class="overflow-x-auto rounded-xl border border-gray-700">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-slate-950/60">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Unit</th>
                                <th class="px-4 py-3 text-left font-semibold">Status</th>
                                <th class="px-4 py-3 text-center font-semibold">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-800">
                            @foreach ($lists as $list)
                                <tr class="hover:bg-slate-900/50 transition" data-uwp-row="{{ (int) $list->id }}">
                                    <td class="px-4 py-3">
                                        {{ $list->office?->name ?? 'â€”' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        @php
                                            $statusColors = [
                                                'draft' => 'border-yellow-500/30 bg-yellow-500/10 text-yellow-300',
                                                'submitted' => 'border-blue-500/30 bg-blue-500/10 text-blue-300',
                                                'consolidated' => 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300',
                                                'endorsed' => 'border-indigo-500/30 bg-indigo-500/10 text-indigo-300',
                                                'pmt_approved' => 'border-purple-500/30 bg-purple-500/10 text-purple-300',
                                                'returned' => 'border-rose-500/30 bg-rose-500/10 text-rose-300',
                                            ];
                                            $statusClass = $statusColors[strtolower($list->status)] ?? 'border-gray-500/30 bg-gray-500/10 text-gray-300';
                                        @endphp

                                        <span
                                            data-status-badge
                                            class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $list->status)) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $isEditable = in_array(strtolower((string) $list->status), ['draft', 'returned'], true) && is_null($list->locked_at);
                                        @endphp

                                        <a href="{{ route('supervisor.uwp', ['uwp_id' => $list->id]) }}"
                                           aria-label="Open Unit Work Plan"
                                           title="{{ $isEditable ? 'Open for editing' : 'Open read-only' }}"
                                           class="inline-flex items-center justify-center rounded-lg p-2 text-slate-400 transition hover:bg-slate-800 hover:text-white">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </a>

                                        <a href="{{ route('supervisor.uwp.show.page', ['id' => $list->id]) }}"
                                           aria-label="View Unit Work Plan"
                                           title="View Unit Work Plan"
                                           class="inline-flex items-center justify-center rounded-lg
                                                  p-2 text-slate-400 hover:text-white
                                                  hover:bg-slate-800 transition">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </a>

                                        @if ($isEditable)
                                            <button type="button"
                                                    aria-label="Delete Unit Work Plan"
                                                    title="Delete Unit Work Plan"
                                                    data-delete-btn
                                                    onclick='openDeleteUwpModal(
                                                        {{ (int) $list->id }},
                                                        @json($list->office?->name ?? "--"),
                                                        @json($list->performancePeriod?->name ?? "--"),
                                                        @json(ucfirst(str_replace("_", " ", (string) $list->status))),
                                                        {{ is_null($list->locked_at) ? 'false' : 'true' }}
                                                    )'
                                                    class="inline-flex items-center justify-center rounded-lg p-2 text-slate-400 transition hover:bg-slate-800 hover:text-rose-300">
                                                <i class="fa-regular fa-trash-can text-sm"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                    aria-label="Delete Unit Work Plan"
                                                    title="Only Draft/Returned & unlocked can be deleted"
                                                    data-delete-btn
                                                    disabled
                                                    class="inline-flex cursor-not-allowed items-center justify-center rounded-lg p-2 text-slate-500 opacity-40">
                                                <i class="fa-regular fa-trash-can text-sm"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endif

        </div>
    </section>

    {{-- UWP preview modal removed: migrated to dedicated show page. --}}

    <!-- Success Indicators Modal -->
    <div id="successIndicatorsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="w-full max-w-5xl px-6">
            <div class="rounded-2xl border border-gray-700 bg-slate-950 text-slate-100">

                <!-- HEADER -->
                <div class="border-b border-gray-700 px-6 py-5">
                    <h3 id="modalPpaTitle" class="text-lg font-semibold">
                        Success Indicators
                    </h3>
                    <p class="mt-1 text-sm text-slate-400">
                        Read-only list of indicators for this output.
                    </p>
                </div>

                <!-- BODY -->
                <div class="px-6 py-6">
                    <div class="overflow-hidden rounded-xl border border-gray-700 bg-slate-900/40">
                        <table class="w-full text-sm text-slate-100">
                            <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Success Indicator</th>
                                    <th class="px-4 py-3 text-left">Target Summary</th>
                                    <th class="px-4 py-3 text-center">Standards</th>
                                    <th class="px-4 py-3 text-center">Assigned Employee</th>
                                </tr>
                            </thead>
                            <tbody id="modalIndicatorsBody" class="divide-y divide-slate-800">
                                <!-- Indicators will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="flex justify-end border-t border-gray-700 px-6 py-4">
                    <button onclick="closeModal('successIndicatorsModal')"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Modal (for multiple employees) -->
<div id="assignmentsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-2xl px-6">
        <div class="rounded-2xl border border-gray-700 bg-slate-950 text-slate-100">

            <!-- HEADER -->
            <div class="border-b border-gray-700 px-6 py-5">
                <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Employee Assignments</p>
                <h3 id="assignmentsModalIndicator" class="text-lg font-semibold">
                    Success Indicator
                </h3>
                <p class="mt-1 text-sm text-slate-400">
                    MFO: <span id="assignmentsModalMfo" class="font-semibold text-slate-100">--</span>
                </p>
            </div>

            <!-- BODY -->
            <div class="px-6 py-6">
                <div class="overflow-hidden rounded-xl border border-gray-700 bg-slate-900/40">
                    <table class="w-full text-sm text-slate-100">
                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left">Employee Name</th>
                                <th class="px-4 py-3 text-left">Office / Unit</th>
                                <th class="px-4 py-3 text-left">Assigned Date</th>
                                <th class="px-4 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody id="assignmentsModalBody" class="divide-y divide-slate-800">
                            <!-- Assignments will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="flex justify-end border-t border-gray-700 px-6 py-4">
                <button onclick="closeModal('assignmentsModal')"
                        class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Standards Modal -->
    <div id="indicatorStandardsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="w-full max-w-3xl px-6">
            <div class="rounded-2xl border border-gray-700 bg-slate-950 text-slate-100">
                <div class="border-b border-gray-700 px-6 py-5">
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-300">Standards (Q/E/T)</p>
                    <h3 class="text-lg font-semibold">Performance Standards</h3>
                    <p class="mt-1 text-sm text-slate-400">
                        MFO: <span id="indicatorStandardsModalMfo" class="font-semibold text-slate-100">--</span>
                    </p>
                    <p class="text-sm text-slate-400">
                        Indicator: <span id="indicatorStandardsModalIndicator" class="font-semibold text-slate-100">--</span>
                    </p>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="overflow-hidden rounded-xl border border-gray-700 bg-slate-900/40">
                        <table class="w-full text-sm text-slate-100">
                            <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Rating</th>
                                    <th class="px-4 py-3 text-left">Quality (Q)</th>
                                    <th class="px-4 py-3 text-left">Efficiency (E)</th>
                                    <th class="px-4 py-3 text-left">Timeliness (T)</th>
                                </tr>
                            </thead>
                            <tbody id="indicatorStandardsBody" class="divide-y divide-slate-800">
                                <!-- Standards will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-end border-t border-gray-700 px-6 py-4">
                    <button onclick="closeModal('indicatorStandardsModal')"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete UWP Confirmation Modal -->

    <div id="deleteUwpModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="w-full max-w-xl px-6">
            <div class="rounded-2xl border border-gray-700 bg-slate-950 text-slate-100">
                <div class="border-b border-gray-700 px-6 py-5">
                    <p class="text-xs uppercase tracking-[0.2em] text-rose-300">Delete Unit Work Plan</p>
                    <h3 class="text-lg font-semibold">Delete this UWP?</h3>
                    <p class="mt-1 text-sm text-slate-400">This action is permanent and cannot be undone.</p>
                </div>

                <div class="space-y-3 px-6 py-5 text-sm text-slate-200">
                    <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-4">
                        <p><span class="text-slate-400">Office / Unit:</span> <span id="deleteUwpOffice">--</span></p>
                        <p class="mt-1"><span class="text-slate-400">Performance Period:</span> <span id="deleteUwpPeriod">--</span></p>
                        <p class="mt-1"><span class="text-slate-400">Status:</span> <span id="deleteUwpStatus">--</span></p>
                    </div>
                    <p class="text-xs text-rose-300/90">Only Draft/Returned and unlocked UWP records can be deleted.</p>
                </div>

                <form id="delete-uwp-form" method="POST" action="" class="flex items-center justify-end gap-3 border-t border-gray-700 px-6 py-4">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            onclick="closeDeleteUwpModal()"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-slate-200 hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit"
                            id="deleteUwpConfirmBtn"
                            data-delete-loading="true"
                            data-loading-text="Deleting..."
                            class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500">
                        <span data-button-label>Delete</span>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.uwpDeleteBaseUrl = "{{ route('supervisor.uwp.destroy', ['id' => '__ID__']) }}";
    </script>
    @push('scripts')
        <script>
            function submitRowUwp(formElement) {
                if (!formElement) return true;

                const button = formElement.querySelector('[data-admin-loading="true"]');
                if (!button) return true;

                const label = button.querySelector('[data-button-label]');
                const spinner = button.querySelector('[data-button-spinner]');

                button.disabled = true;
                if (label) {
                    label.textContent = button.dataset.loadingText || 'Submitting...';
                }
                if (spinner) {
                    spinner.classList.remove('hidden');
                }

                return true;
            }

            function setDeleteButtonLoading(isLoading) {
                const button = document.getElementById('deleteUwpConfirmBtn');
                if (!button) return;

                const label = button.querySelector('[data-button-label]');
                const spinner = button.querySelector('[data-button-spinner]');
                const loadingText = button.dataset.loadingText || 'Deleting...';

                button.disabled = !!isLoading;
                if (label) {
                    label.textContent = isLoading ? loadingText : 'Delete';
                }
                if (spinner) {
                    spinner.classList.toggle('hidden', !isLoading);
                }
            }

            function openDeleteUwpModal(uwpId, officeName, periodName, status, isLocked) {
                const normalizedStatus = String(status || '').toLowerCase();
                const locked = isLocked === true || isLocked === 'true' || isLocked === 1 || isLocked === '1';
                const deletable = (normalizedStatus === 'draft' || normalizedStatus === 'returned') && !locked;

                if (!deletable) {
                    alert('Only Draft/Returned & unlocked UWP can be deleted.');
                    return;
                }

                document.getElementById('deleteUwpOffice').textContent = officeName || '--';
                document.getElementById('deleteUwpPeriod').textContent = periodName || '--';
                document.getElementById('deleteUwpStatus').textContent = status || '--';
                const deleteForm = document.getElementById('delete-uwp-form');
                if (deleteForm) {
                    deleteForm.action = window.uwpDeleteBaseUrl.replace('__ID__', String(uwpId));
                }
                setDeleteButtonLoading(false);

                const modal = document.getElementById('deleteUwpModal');
                if (modal) {
                    modal.classList.remove('hidden');
                }
            }

            function closeDeleteUwpModal() {
                setDeleteButtonLoading(false);
                const deleteForm = document.getElementById('delete-uwp-form');
                if (deleteForm) {
                    deleteForm.action = '';
                }
                const modal = document.getElementById('deleteUwpModal');
                if (modal) {
                    modal.classList.add('hidden');
                }
            }

            // ====================================
            // MODAL FUNCTIONS
            // ====================================

            function showIndicatorsModal(mfoData) {
                console.log('MFO Data:', mfoData);

                document.getElementById('modalPpaTitle').textContent = mfoData.title || 'Untitled MFO';

                const indicatorsBody = document.getElementById('modalIndicatorsBody');
                const indicators = mfoData.success_indicators || [];

                if (!indicators || indicators.length === 0) {
                    indicatorsBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                No success indicators found for this MFO.
                            </td>
                        </tr>
                    `;
                } else {
                    let html = '';

                    indicators.forEach(indicator => {
                        const assignments = indicator.assignments || [];
                        const assignmentCount = assignments.length;
                        const standardsCount = indicator.qet_standards?.length || 0;

                        html += `
                            <tr>
                                <td class="px-4 py-4 text-slate-100">
                                    ${indicator.indicator_text || 'Unnamed Indicator'}
                                </td>
                                <td class="px-4 py-4 text-slate-300">
                                    ${getIndicatorTargetSummary(indicator)}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button
                                        onclick='showStandardsModal(${JSON.stringify({
                                            mfoTitle: mfoData.title,
                                            indicatorText: indicator.indicator_text,
                                            qetStandards: indicator.qet_standards || []
                                        }).replace(/'/g, "\\'")})'
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[120px]">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                        <span>View Standards (${standardsCount})</span>
                                    </button>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button
                                        onclick='showAssignmentsModal(${JSON.stringify({
                                            indicatorText: indicator.indicator_text,
                                            mfoTitle: mfoData.title,
                                            assignments: assignments
                                        }).replace(/'/g, "\\'")})'
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                        <span>(${assignmentCount})</span>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    indicatorsBody.innerHTML = html;
                }

                document.getElementById('successIndicatorsModal').classList.remove('hidden');
            }

            function showStandardsModal(data) {
                document.getElementById('indicatorStandardsModalMfo').textContent = data.mfoTitle || '--';
                document.getElementById('indicatorStandardsModalIndicator').textContent = data.indicatorText || '--';
                renderIndicatorStandards(data.qetStandards);
                document.getElementById('indicatorStandardsModal').classList.remove('hidden');
            }

            function showAssignmentsModal(data) {
                document.getElementById('assignmentsModalIndicator').textContent = data.indicatorText || 'Success Indicator';
                document.getElementById('assignmentsModalMfo').textContent = data.mfoTitle || '--';

                const assignmentsBody = document.getElementById('assignmentsModalBody');

                if (!data.assignments || data.assignments.length === 0) {
                    assignmentsBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                No employees assigned to this indicator.
                            </td>
                        </tr>
                    `;
                } else {
                    let html = '';

                    data.assignments.forEach(assignment => {
                        const employee = assignment.employee || {};
                        const assignedDate = assignment.assigned_at
                            ? new Date(assignment.assigned_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            })
                            : 'N/A';

                        html += `
                            <tr class="hover:bg-slate-900/40">
                                <td class="px-4 py-3 font-medium">
                                    ${employee.name || 'Unknown Employee'}
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    ${employee.office?.name || 'N/A'}
                                </td>
                                <td class="px-4 py-3 text-slate-300">
                                    ${assignedDate}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                                        Assigned
                                    </span>
                                </td>
                            </tr>
                        `;
                    });

                    assignmentsBody.innerHTML = html;
                }

                document.getElementById('assignmentsModal').classList.remove('hidden');
            }

            function renderIndicatorStandards(qetStandards) {
                const standardsBody = document.getElementById('indicatorStandardsBody');
                if (!standardsBody) return;

                standardsBody.innerHTML = '';

                if (!qetStandards || qetStandards.length === 0) {
                    standardsBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                No Q/E/T standards defined for this indicator.
                            </td>
                        </tr>
                    `;
                    return;
                }

                const standardsByRating = {};

                qetStandards.forEach(standard => {
                    const rating = standard.rating;
                    const dimension = standard.dimension;
                    const text = standard.standard_text;

                    if (!standardsByRating[rating]) {
                        standardsByRating[rating] = { q: [], e: [], t: [] };
                    }

                    if (dimension === 'quality' || dimension === 'q') {
                        standardsByRating[rating].q.push(text);
                    } else if (dimension === 'efficiency' || dimension === 'e') {
                        standardsByRating[rating].e.push(text);
                    } else if (dimension === 'timeliness' || dimension === 't') {
                        standardsByRating[rating].t.push(text);
                    }
                });

                [5, 4, 3, 2, 1].forEach(rating => {
                    const rowData = standardsByRating[rating] || { q: [], e: [], t: [] };

                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-900/40';

                    tr.innerHTML = `
                        <td class="px-4 py-3 font-semibold">${rating}</td>
                        <td class="px-4 py-3 align-top">${createStandardsList(rowData.q)}</td>
                        <td class="px-4 py-3 align-top">${createStandardsList(rowData.e)}</td>
                        <td class="px-4 py-3 align-top">${createStandardsList(rowData.t)}</td>
                    `;

                    standardsBody.appendChild(tr);
                });
            }

            function createStandardsList(items) {
                if (!items || items.length === 0) return '--';

                let html = '<ul class="list-disc space-y-1 pl-4 text-slate-200">';
                items.forEach(item => {
                    if (item) html += `<li>${item}</li>`;
                });
                html += '</ul>';
                return html;
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            // ====================================
            // INITIALIZATION
            // ====================================
            document.addEventListener('DOMContentLoaded', function() {

                const deleteForm = document.getElementById('delete-uwp-form');
                if (deleteForm) {
                    deleteForm.addEventListener('submit', function() {
                        setDeleteButtonLoading(true);
                    });
                }

                window.addEventListener('click', function(event) {
                    const modals = ['successIndicatorsModal', 'indicatorStandardsModal', 'assignmentsModal', 'deleteUwpModal'];
                    modals.forEach(modalId => {
                        const modal = document.getElementById(modalId);
                        if (event.target === modal) {
                            if (modalId === 'deleteUwpModal') {
                                closeDeleteUwpModal();
                            } else {
                                closeModal(modalId);
                            }
                        }
                    });
                });

                window.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        const modals = ['successIndicatorsModal', 'indicatorStandardsModal', 'assignmentsModal', 'deleteUwpModal', 'uwpCreationModal', 'creationStandardEditModal'];
                        modals.forEach(modalId => {
                            const modal = document.getElementById(modalId);
                            if (modal && !modal.classList.contains('hidden')) {
                                if (modalId === 'deleteUwpModal') {
                                    closeDeleteUwpModal();
                                } else if (modalId === 'uwpCreationModal') {
                                    closeCreationModal();
                                } else {
                                    closeModal(modalId);
                                }
                            }
                        });
                    }
                });

                // Click-outside for creation modal
                document.getElementById('uwpCreationModal').addEventListener('click', function(e) {
                    if (e.target === this) closeCreationModal();
                });
            });

            // ============================================================
            // UWP CREATION MODAL STATE & LOGIC
            // ============================================================
            const creationOfficeEmployees = @json(
                collect($officeEmployees ?? [])
                    ->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'office' => $u->office?->name ?? ''])
                    ->values()->all()
            );

            const creationSaveDraftUrl  = @json(route('supervisor.uwp.saveDraftData'));
            const creationSubmitDataUrl = @json(route('supervisor.uwp.submitData'));

            let creationState = { mfos: [] };
            let creationActiveMfoIdx = null;
            let creationActiveTab = 'overview';
            let creationEditingIndicatorIdx = null;
            let creationStdEditRating = null;
            let creationStdEditDim = null;

            function openCreationModal() {
                // Reset state with one empty MFO to start
                creationState = {
                    mfos: [{
                        title: '', target: '', function_type: 'core',
                        weight_percent: 80, indicators: []
                    }]
                };
                creationActiveMfoIdx = 0;
                creationActiveTab = 'overview';
                // Sync period label
                const sel = document.getElementById('creationPeriodSelect');
                if (sel) document.getElementById('creationModalPeriod').textContent = sel.options[sel.selectedIndex]?.text ?? 'â€”';
                const modal = document.getElementById('uwpCreationModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                creationRefreshAll();
                creationSetTab('overview');
            }

            function closeCreationModal() {
                const modal = document.getElementById('uwpCreationModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function creationGetCurrentMfo() {
                if (creationActiveMfoIdx === null) return null;
                return creationState.mfos[creationActiveMfoIdx] ?? null;
            }

            function creationSyncCurrentMfo(field, value) {
                const mfo = creationGetCurrentMfo();
                if (!mfo) return;
                mfo[field] = value;
            }

            function creationSetTab(tab) {
                creationActiveTab = tab;
                document.querySelectorAll('[data-creation-tab]').forEach(btn => {
                    const active = btn.dataset.creationTab === tab;
                    btn.classList.toggle('border-blue-400', active);
                    btn.classList.toggle('text-white', active);
                    btn.classList.toggle('font-semibold', active);
                    btn.classList.toggle('border-transparent', !active);
                    btn.classList.toggle('text-slate-400', !active);
                    btn.classList.toggle('font-medium', !active);
                });
                document.querySelectorAll('[data-creation-panel]').forEach(panel => {
                    panel.classList.toggle('hidden', panel.dataset.creationPanel !== tab);
                });
                if (tab === 'standards') creationRenderStandards();
                if (tab === 'assignees') creationRenderAssignees();
            }

            function creationRefreshAll() {
                creationRefreshLeftPanel();
                creationRefreshRightPanel();
            }

            function creationRefreshLeftPanel() {
                const list = document.getElementById('creationOutputList');
                const countLeft = document.getElementById('creationOutputCountLeft');
                const countBadge = document.getElementById('creationModalOutputCountBadge');
                if (!list) return;
                const n = creationState.mfos.length;
                countLeft.textContent = String(n);
                countBadge.textContent = `${n} output${n === 1 ? '' : 's'}`;
                list.innerHTML = '';
                creationState.mfos.forEach((mfo, idx) => {
                    const active = idx === creationActiveMfoIdx;
                    const type = (mfo.function_type || 'core').toLowerCase();
                    const badgeClass = type === 'core'
                        ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400'
                        : 'border-blue-400/30 bg-blue-500/10 text-blue-300';
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `block w-full rounded-xl border px-3 py-3 text-left transition ${active ? 'border-blue-400/60 bg-blue-500/10' : 'border-gray-700 bg-slate-950/30 hover:bg-slate-900/50'}`;
                    btn.innerHTML = `
                        <div class="line-clamp-2 text-sm font-semibold leading-snug text-white">${mfo.title || '<span class="text-slate-500 italic">Untitled MFO</span>'}</div>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-md border px-2 py-0.5 text-xs font-medium ${badgeClass}">${type.charAt(0).toUpperCase() + type.slice(1)}</span>
                            <span class="text-xs text-slate-400">${mfo.weight_percent ?? 'â€”'}%</span>
                            <span class="text-xs text-slate-500">${(mfo.indicators || []).length} indicator${(mfo.indicators || []).length === 1 ? '' : 's'}</span>
                        </div>`;
                    btn.addEventListener('click', () => {
                        creationActiveMfoIdx = idx;
                        creationRefreshAll();
                        creationSetTab('overview');
                    });
                    list.appendChild(btn);
                });
            }

            function creationRefreshRightPanel() {
                const mfo = creationGetCurrentMfo();
                const titleEl  = document.getElementById('creationDetailTitle');
                const badge    = document.getElementById('creationDetailFunctionBadge');
                const weightEl = document.getElementById('creationDetailWeight');
                if (!mfo) {
                    titleEl.textContent = 'Select an output to edit';
                    badge.classList.add('hidden');
                    weightEl.textContent = '';
                    return;
                }
                titleEl.textContent = mfo.title || 'Untitled MFO';
                const type = (mfo.function_type || 'core').toLowerCase();
                badge.classList.remove('hidden');
                badge.className = `rounded-md border px-2 py-1 text-xs font-medium ${type === 'core' ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : 'border-blue-400/30 bg-blue-500/10 text-blue-300'}`;
                badge.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                weightEl.textContent = mfo.weight_percent ? `${mfo.weight_percent}%` : '';

                // Sync form fields
                document.getElementById('creationMfoTitle').value = mfo.title || '';
                document.getElementById('creationMfoTarget').value = mfo.target || '';
                document.getElementById('creationMfoFunctionType').value = mfo.function_type || 'core';
                document.getElementById('creationMfoWeight').value = mfo.weight_percent ?? '';

                // Overview indicators list
                const overviewEl = document.getElementById('creationOverviewIndicators');
                const inds = mfo.indicators || [];
                overviewEl.innerHTML = inds.length
                    ? inds.map(ind => `<button type="button" class="flex w-full items-start justify-between rounded-xl border border-gray-700 bg-slate-900/40 px-4 py-3 text-left hover:bg-slate-900/60 transition"><span class="text-sm text-slate-100">${ind.text || 'â€”'}</span><span class="ml-3 rounded-md bg-slate-900 px-2 py-0.5 text-xs text-slate-400">${ind.targetQuantity ? ind.targetQuantity + ' ' : ''}${ind.targetTimeline || ''}</span></button>`).join('')
                    : '<p class="text-sm text-slate-500">No success indicators yet. Add one from the "Success Indicators" tab.</p>';

                // Indicators table
                const tbody = document.getElementById('creationIndicatorsBody');
                tbody.innerHTML = inds.length
                    ? inds.map((ind, i) => `
                        <tr class="hover:bg-slate-900/30">
                            <td class="px-4 py-3 text-slate-100">${ind.text || 'â€”'}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs">${ind.targetTimeline || 'â€”'}</td>
                            <td class="px-4 py-3 text-center text-slate-300">${ind.targetQuantity ?? 'â€”'}</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="creationEditIndicator(${i})" class="mr-2 text-xs text-blue-400 hover:text-blue-300">Edit</button>
                                <button onclick="creationDeleteIndicator(${i})" class="text-xs text-rose-400 hover:text-rose-300">Del</button>
                            </td>
                        </tr>`).join('')
                    : '<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No indicators yet.</td></tr>';

                // Populate indicator selects for Standards & Assignees tabs
                ['creationStandardsIndicatorSelect', 'creationAssigneesIndicatorSelect'].forEach(id => {
                    const sel = document.getElementById(id);
                    sel.innerHTML = inds.length
                        ? inds.map((ind, i) => `<option value="${i}">${ind.text || 'Indicator ' + (i+1)}</option>`).join('')
                        : '<option value="">No indicators</option>';
                });
            }

            function creationAddMfo() {
                creationState.mfos.push({ title: '', target: '', function_type: 'core', weight_percent: '', indicators: [] });
                creationActiveMfoIdx = creationState.mfos.length - 1;
                creationRefreshAll();
                creationSetTab('overview');
                document.getElementById('creationMfoTitle').focus();
            }

            function creationAddIndicator() {
                const mfo = creationGetCurrentMfo();
                if (!mfo) return;
                const text = prompt('Success Indicator text:');
                if (!text) return;
                const qty = prompt('Target Quantity (number, or leave blank):');
                const timeline = prompt('Target Timeline (e.g. "processed within the semester"):');
                mfo.indicators.push({
                    text: text.trim(),
                    targetQuantity: qty ? parseInt(qty) : null,
                    targetTimeline: timeline?.trim() ?? '',
                    standards: { 5:{q:'',e:'',t:''}, 4:{q:'',e:'',t:''}, 3:{q:'',e:'',t:''}, 2:{q:'',e:'',t:''}, 1:{q:'',e:'',t:''} },
                    assignees: []
                });
                creationRefreshRightPanel();
            }

            function creationEditIndicator(idx) {
                const mfo = creationGetCurrentMfo();
                if (!mfo) return;
                const ind = mfo.indicators[idx];
                const text = prompt('Success Indicator text:', ind.text);
                if (text !== null) ind.text = text.trim();
                const qty = prompt('Target Quantity:', ind.targetQuantity ?? '');
                ind.targetQuantity = qty ? parseInt(qty) : null;
                const timeline = prompt('Target Timeline:', ind.targetTimeline);
                if (timeline !== null) ind.targetTimeline = timeline.trim();
                creationRefreshRightPanel();
            }

            function creationDeleteIndicator(idx) {
                const mfo = creationGetCurrentMfo();
                if (!mfo || !confirm('Delete this indicator?')) return;
                mfo.indicators.splice(idx, 1);
                creationRefreshRightPanel();
            }

            function creationRenderStandards() {
                const sel = document.getElementById('creationStandardsIndicatorSelect');
                const label = document.getElementById('creationStandardsIndicatorLabel');
                const tbody = document.getElementById('creationStandardsBody');
                const mfo = creationGetCurrentMfo();
                if (!mfo || !sel.value) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No indicator selected.</td></tr>';
                    return;
                }
                const idx = parseInt(sel.value);
                const ind = mfo.indicators[idx];
                if (!ind) return;
                label.textContent = ind.text || 'â€”';
                if (!ind.standards) ind.standards = {5:{q:'',e:'',t:''},4:{q:'',e:'',t:''},3:{q:'',e:'',t:''},2:{q:'',e:'',t:''},1:{q:'',e:'',t:''}};
                tbody.innerHTML = [5,4,3,2,1].map(r => {
                    const s = ind.standards[r] || {q:'',e:'',t:''};
                    return `<tr class="hover:bg-slate-900/30">
                        <td class="px-4 py-3 font-semibold text-white">${r}</td>
                        <td class="px-4 py-3 text-slate-300 text-xs">${s.q || '<span class="text-slate-600">â€”</span>'}</td>
                        <td class="px-4 py-3 text-slate-300 text-xs">${s.e || '<span class="text-slate-600">â€”</span>'}</td>
                        <td class="px-4 py-3 text-slate-300 text-xs">${s.t || '<span class="text-slate-600">â€”</span>'}</td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="creationOpenStdEdit(${idx},${r},'q')" class="mr-1 text-[10px] text-blue-400 hover:text-blue-300">Q</button>
                            <button onclick="creationOpenStdEdit(${idx},${r},'e')" class="mr-1 text-[10px] text-blue-400 hover:text-blue-300">E</button>
                            <button onclick="creationOpenStdEdit(${idx},${r},'t')" class="text-[10px] text-blue-400 hover:text-blue-300">T</button>
                        </td>
                    </tr>`;
                }).join('');
            }

            function creationOpenStdEdit(indicatorIdx, rating, dim) {
                const mfo = creationGetCurrentMfo();
                if (!mfo) return;
                const ind = mfo.indicators[indicatorIdx];
                if (!ind || !ind.standards) return;
                creationStdEditRating = { indicatorIdx, rating, dim };
                const dimLabel = dim === 'q' ? 'Quality' : dim === 'e' ? 'Efficiency' : 'Timeliness';
                document.getElementById('creationStdEditLabel').textContent = `Rating ${rating} â€” ${dimLabel}`;
                document.getElementById('creationStdEditText').value = ind.standards[rating]?.[dim] ?? '';
                const modal = document.getElementById('creationStandardEditModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.getElementById('creationStdEditText').focus();
            }

            function creationSaveStandard() {
                if (!creationStdEditRating) return;
                const { indicatorIdx, rating, dim } = creationStdEditRating;
                const mfo = creationGetCurrentMfo();
                if (!mfo) return;
                const ind = mfo.indicators[indicatorIdx];
                if (!ind) return;
                if (!ind.standards) ind.standards = {};
                if (!ind.standards[rating]) ind.standards[rating] = {q:'',e:'',t:''};
                ind.standards[rating][dim] = document.getElementById('creationStdEditText').value.trim();
                document.getElementById('creationStandardEditModal').classList.add('hidden');
                document.getElementById('creationStandardEditModal').classList.remove('flex');
                creationRenderStandards();
            }

            function creationRenderAssignees() {
                const sel = document.getElementById('creationAssigneesIndicatorSelect');
                const label = document.getElementById('creationAssigneesIndicatorLabel');
                const tbody = document.getElementById('creationAssigneesBody');
                const mfo = creationGetCurrentMfo();
                if (!mfo || !sel.value && sel.value !== '0') {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">No indicator selected.</td></tr>';
                    return;
                }
                const idx = parseInt(sel.value);
                const ind = mfo.indicators[idx];
                if (!ind) return;
                if (!ind.assignees) ind.assignees = [];
                label.textContent = ind.text || 'â€”';
                const assigned = new Set(ind.assignees.map(a => a.id));
                tbody.innerHTML = creationOfficeEmployees.map(emp => {
                    const isAssigned = assigned.has(emp.id);
                    return `<tr class="hover:bg-slate-900/30">
                        <td class="px-4 py-3 text-slate-100">${emp.name}</td>
                        <td class="px-4 py-3 text-slate-400 text-xs">${emp.office || 'â€”'}</td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="creationToggleAssignee(${idx},${emp.id},'${emp.name.replace(/'/g, "\\'")}')"
                                    class="rounded-full px-3 py-1 text-xs font-semibold transition ${isAssigned ? 'bg-rose-500/10 border border-rose-500/30 text-rose-300 hover:bg-rose-500/20' : 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 hover:bg-emerald-500/20'}">
                                ${isAssigned ? 'Remove' : 'Assign'}
                            </button>
                        </td>
                    </tr>`;
                }).join('') || '<tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">No employees found.</td></tr>';
            }

            function creationToggleAssignee(indicatorIdx, empId, empName) {
                const mfo = creationGetCurrentMfo();
                if (!mfo) return;
                const ind = mfo.indicators[indicatorIdx];
                if (!ind) return;
                if (!ind.assignees) ind.assignees = [];
                const existingIdx = ind.assignees.findIndex(a => a.id === empId);
                if (existingIdx >= 0) {
                    ind.assignees.splice(existingIdx, 1);
                } else {
                    ind.assignees.push({ id: empId, name: empName });
                }
                creationRenderAssignees();
            }

            function creationBuildPayload(isDraft = false) {
                const sel = document.getElementById('creationPeriodSelect');
                return {
                    _token: document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    office_id: {{ auth()->user()->office_id ?? 'null' }},
                    performance_period_id: sel?.value ?? '',
                    is_draft: isDraft ? 1 : 0,
                    mfos_payload: JSON.stringify(creationState.mfos.map(mfo => ({
                        title: mfo.title,
                        target: mfo.target,
                        function_type: mfo.function_type,
                        weight_percent: mfo.weight_percent,
                        indicators: (mfo.indicators || []).map(ind => ({
                            text: ind.text,
                            targetQuantity: ind.targetQuantity,
                            targetTimeline: ind.targetTimeline,
                            standards: ind.standards ?? {},
                            assignees: (ind.assignees || []).map(a => a.id),
                        }))
                    }))),
                    assignments_payload: JSON.stringify([]),
                    functions_payload: JSON.stringify([]),
                };
            }

            function creationSetLoading(btnId, loading) {
                const btn = document.getElementById(btnId);
                if (!btn) return;
                const label = btn.querySelector('[data-btn-label]');
                const spinner = btn.querySelector('[data-btn-spinner]');
                btn.disabled = loading;
                if (label) label.textContent = loading ? (btnId.includes('Draft') ? 'Saving...' : 'Submitting...') : (btnId.includes('Draft') ? 'Save Draft' : 'Submit for Approval');
                if (spinner) spinner.classList.toggle('hidden', !loading);
            }

            function creationPost(url, payload, successMsg) {
                const formData = new FormData();
                Object.entries(payload).forEach(([k, v]) => formData.append(k, v));
                return fetch(url, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': payload._token, 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                }).then(r => r.json().then(d => ({ ok: r.ok, data: d })));
            }

            function creationSaveDraft() {
                creationSetLoading('creationSaveDraftBtn', true);
                const payload = creationBuildPayload(true);
                creationPost(creationSaveDraftUrl, payload)
                    .then(({ ok, data }) => {
                        if (ok && data.success) {
                            // Add new row to table
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to save draft.');
                            creationSetLoading('creationSaveDraftBtn', false);
                        }
                    })
                    .catch(() => {
                        alert('Network error. Please try again.');
                        creationSetLoading('creationSaveDraftBtn', false);
                    });
            }

            function creationSubmit() {
                creationSetLoading('creationSubmitBtn', true);
                const payload = creationBuildPayload(false);
                creationPost(creationSaveDraftUrl, payload)
                    .then(({ ok, data }) => {
                        if (ok && data.success && data.uwp_id) {
                            // Auto-submit after save
                            const submitUrl = @json(route('supervisor.uwp.submitData.byId', ['id' => '__ID__'])).replace('__ID__', String(data.uwp_id));
                            return creationPost(submitUrl, { _token: payload._token });
                        }
                        throw new Error(data.message || 'Save failed');
                    })
                    .then(() => { location.reload(); })
                    .catch(err => {
                        alert(err.message || 'Submission failed.');
                        creationSetLoading('creationSubmitBtn', false);
                    });
            }

            // Wire up tab buttons after DOM ready
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('[data-creation-tab]').forEach(btn => {
                    btn.addEventListener('click', () => creationSetTab(btn.dataset.creationTab));
                });
            });
        </script>
    @endpush
@endsection
