@extends('layouts.supervisor')

@section('main-content')
    <section class="space-y-6">

        <!-- Header -->
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    Stage I – Unit Work Plan (UWP)
                </p>
                <h1 class="text-2xl font-semibold text-white">Performance Period Planning and Commitment</h1>
            </div>
            <a href="{{ route('supervisor.uwp') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                + Create UWP
            </a>
        </div>

        <!-- UWP List -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 space-y-4">

            <!-- Filter / Context Bar -->
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="space-y-1">
                    <span class="block text-xs uppercase tracking-widest text-slate-400">
                        Office / Unit
                    </span>
                    <p class="text-sm font-medium text-slate-200">
                        Office / Unit: {{ $office?->name ?? '—' }}
                    </p>
                </div>
            </div>

            <!-- Table -->
            @if($lists->isEmpty())
                <div class="text-center py-8 text-slate-400">
                    {{ $office ? 'No Unit Work Plans found for your assigned office.' : 'No assigned office found for your account.' }}
                </div>
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-800">
                    <table class="min-w-full text-sm text-slate-200">
                        <thead class="bg-slate-950/60">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Unit</th>
                                <th class="px-4 py-3 text-left font-semibold">Performance Period</th>
                                <th class="px-4 py-3 text-left font-semibold">Status</th>
                                <th class="px-4 py-3 text-center font-semibold">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-800">
                            @foreach ($lists as $list)
                                <tr class="hover:bg-slate-900/50 transition">
                                    <td class="px-4 py-3">
                                        {{ $list->office?->name ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $list->performancePeriod?->name ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        @php
                                            $statusColors = [
                                                'draft' => 'border-yellow-500/30 bg-yellow-500/10 text-yellow-300',
                                                'submitted' => 'border-blue-500/30 bg-blue-500/10 text-blue-300',
                                                'endorsed' => 'border-indigo-500/30 bg-indigo-500/10 text-indigo-300',
                                                'pmt_approved' => 'border-purple-500/30 bg-purple-500/10 text-purple-300',
                                                'returned' => 'border-rose-500/30 bg-rose-500/10 text-rose-300',
                                            ];
                                            $statusClass = $statusColors[strtolower($list->status)] ?? 'border-gray-500/30 bg-gray-500/10 text-gray-300';
                                        @endphp

                                        <span
                                            class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $list->status)) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $isDraftAndUnlocked = strtolower((string) $list->status) === 'draft' && is_null($list->locked_at);
                                        @endphp

                                        <button type="button"
                                                aria-label="View Unit Work Plan"
                                                title="View Unit Work Plan"
                                                onclick="showUwpPreview({{ $list->id }})"
                                                class="inline-flex items-center justify-center rounded-lg
                                                    p-2 text-slate-400 hover:text-white
                                                    hover:bg-slate-800 transition">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </button>

                                        @if ($isDraftAndUnlocked)
                                            <form method="POST"
                                                  action="{{ route('supervisor.uwp.submit', ['id' => $list->id]) }}"
                                                  class="inline-flex"
                                                  onsubmit="return submitRowUwp(this);">
                                                @csrf
                                                <button type="submit"
                                                        data-admin-loading="true"
                                                        data-loading-text="Submitting..."
                                                        class="ml-2 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-500">
                                                    <span data-button-label>Submit for Approval</span>
                                                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                                </button>
                                            </form>
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

    {{-- ====================================
        DYNAMIC MODALS - ONLY THESE SHOULD EXIST
    ===================================== --}}

    <!-- UWP Preview Modal -->
    <div id="uwpPreviewModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="mx-auto my-10 w-full max-w-5xl px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">

                <!-- HEADER -->
                <div class="border-b border-slate-800 px-8 py-6">
                    <h2 class="text-xl font-semibold">Unit Work Plan</h2>
                    <p id="modalUwpSubtitle" class="mt-1 text-sm text-slate-400">
                        Select a UWP to view details
                    </p>
                </div>

                <!-- SUMMARY -->
                <div class="flex items-stretch gap-10 border-b border-slate-800 px-8 py-6">
                    <div class="w-1/4">
                        <p class="text-xs uppercase tracking-widest text-slate-500">Office / Unit</p>
                        <p id="modalOfficeUnit" class="mt-1 font-medium">-</p>
                    </div>

                    <div class="w-1/4">
                        <p class="text-xs uppercase tracking-widest text-slate-500">Supervisor</p>
                        <p id="modalSupervisor" class="mt-1 font-medium">-</p>
                    </div>

                    <div class="w-1/4">
                        <p class="text-xs uppercase tracking-widest text-slate-500">Department Head</p>
                        <p id="modalDeptHead" class="mt-1 font-medium">-</p>
                    </div>

                    <div class="w-1/4">
                        <p class="text-xs uppercase tracking-widest text-slate-500">Status</p>
                        <span id="modalStatus" class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                            -
                        </span>
                    </div>
                </div>

                <!-- PLANNED OUTPUTS -->
                <div class="px-8 py-6">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-widest text-slate-400">
                        Planned Outputs
                    </h3>

                    <div id="modalPPAsContainer" class="overflow-hidden rounded-xl border border-slate-800">
                        <!-- PPAs will be dynamically inserted here -->
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="flex gap-4 justify-end border-t border-slate-800 px-8 py-5">
                    <a id="modalExportExcelLink" href="#"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                        Export Excel
                    </a>
                    <button type="submit"
                            data-employee-loading="true"
                            data-loading-text="Submitting..."
                            data-submit-uwp-btn
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-900/40 transition hover:bg-blue-500">
                        <span data-button-label>Submit for Approval</span>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Indicators Modal -->
    <div id="successIndicatorsModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="mx-auto my-16 w-full max-w-5xl px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">

                <!-- HEADER -->
                <div class="border-b border-slate-800 px-6 py-5">
                    <h3 id="modalPpaTitle" class="text-lg font-semibold">
                        Success Indicators
                    </h3>
                    <p class="mt-1 text-sm text-slate-400">
                        Read-only list of indicators for this output.
                    </p>
                </div>

                <!-- BODY -->
                <div class="px-6 py-6">
                    <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
                        <table class="w-full text-sm text-slate-100">
                            <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Success Indicator</th>
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
                <div class="flex justify-end border-t border-slate-800 px-6 py-4">
                    <button onclick="closeModal('successIndicatorsModal')"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Modal (for multiple employees) -->
<div id="assignmentsModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm overflow-y-auto">
    <div class="mx-auto my-16 w-full max-w-2xl px-6">
        <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">

            <!-- HEADER -->
            <div class="border-b border-slate-800 px-6 py-5">
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
                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
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
            <div class="flex justify-end border-t border-slate-800 px-6 py-4">
                <button onclick="closeModal('assignmentsModal')"
                        class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Standards Modal -->
    <div id="indicatorStandardsModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="mx-auto my-16 w-full max-w-3xl px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">
                <div class="border-b border-slate-800 px-6 py-5">
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
                    <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/40">
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
                <div class="flex justify-end border-t border-slate-800 px-6 py-4">
                    <button onclick="closeModal('indicatorStandardsModal')"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.uwpPreviewBaseUrl = "{{ route('supervisor.uwp.show', ['id' => '__ID__']) }}";
        window.uwpSubmitBaseUrl = "{{ route('supervisor.uwp.submit', ['id' => '__ID__']) }}";
        window.uwpExportBaseUrl = "{{ route('uwp.export', ['uwp' => '__ID__']) }}";
    </script>
    @push('scripts')
        <script>
            let currentUwpId = null;
            function showUwpPreview(uwpId) {
                currentUwpId = uwpId;
                updateExportLink(uwpId);

                document.getElementById('uwpPreviewModal').classList.remove('hidden');
                document.getElementById('modalPPAsContainer').innerHTML = '<div class="p-6 text-center text-slate-400">Loading UWP data...</div>';

                resetSubmitButton();

                document.getElementById('modalOfficeUnit').textContent = 'Loading...';
                document.getElementById('modalUwpSubtitle').textContent = 'Loading...';
                document.getElementById('modalSupervisor').textContent = 'Loading...';
                document.getElementById('modalDeptHead').textContent = 'Loading...';
                document.getElementById('modalStatus').textContent = 'LOADING';

                const url = window.uwpPreviewBaseUrl.replace('__ID__', uwpId);
                console.log('Fetching UWP from:', url);

                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                    .then(async response => {
                        const payload = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            throw new Error(payload.message || `HTTP error! status: ${response.status}`);
                        }
                        return payload;
                    })
                    .then(uwpData => {
                        console.log('UWP Data loaded:', uwpData);
                        populateUwpModal(uwpData);
                    })
                    .catch(error => {
                        console.error('Error loading UWP:', error);
                        document.getElementById('modalPPAsContainer').innerHTML =
                            `<div class="p-6 text-center text-red-400">
                                Error loading UWP data. Please try again.<br>
                                (${error.message})
                            </div>`;
                    });
            }

            function populateUwpModal(uwpData) {
                if (uwpData.id) {
                    updateExportLink(uwpData.id);
                }

                document.getElementById('modalOfficeUnit').textContent = uwpData.office?.name || 'N/A';
                document.getElementById('modalUwpSubtitle').textContent =
                    `${uwpData.office?.name || 'Unit'} • ${uwpData.performance_period?.name || 'Performance Period'}`;

                document.getElementById('modalSupervisor').textContent = uwpData.creator?.name || 'Not Assigned';
                document.getElementById('modalDeptHead').textContent = uwpData.department_head?.name || 'Not Assigned';

                const statusBadge = document.getElementById('modalStatus');
                const status = uwpData.status || 'draft';
                const isLocked = !!uwpData.locked_at;
                statusBadge.textContent = status.replace('_', ' ').toUpperCase();

                statusBadge.className = 'mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold';

                const statusColors = {
                    'draft': ['bg-yellow-500/15', 'text-yellow-400', 'border-yellow-500/30'],
                    'submitted': ['bg-blue-500/15', 'text-blue-400', 'border-blue-500/30'],
                    'endorsed': ['bg-indigo-500/15', 'text-indigo-400', 'border-indigo-500/30'],
                    'pmt_approved': ['bg-purple-500/15', 'text-purple-400', 'border-purple-500/30'],
                    'returned': ['bg-rose-500/15', 'text-rose-400', 'border-rose-500/30']
                };

                const classes = statusColors[status] || ['bg-gray-500/15', 'text-gray-400', 'border-gray-500/30'];
                classes.forEach(cls => statusBadge.classList.add(cls));

                const submitButton = document.querySelector('[data-submit-uwp-btn]');
                if (submitButton) {
                    if (status === 'draft' && !isLocked) {
                        submitButton.classList.remove('hidden');
                        submitButton.disabled = false;
                        submitButton.querySelector('[data-button-label]').textContent = 'Submit for Approval';
                    } else {
                        submitButton.classList.add('hidden');
                    }
                }

                const ppasContainer = document.getElementById('modalPPAsContainer');

                let mfos = [];
                if (uwpData.uwp_functions && uwpData.uwp_functions.length > 0) {
                    uwpData.uwp_functions.forEach(uwpFunction => {
                        if (uwpFunction.mfos && uwpFunction.mfos.length > 0) {
                            uwpFunction.mfos.forEach(mfo => {
                                mfos.push({
                                    ...mfo,
                                    function_type: uwpFunction.function_type || 'Core',
                                    function_name: uwpFunction.name
                                });
                            });
                        }
                    });
                }

                if (mfos.length === 0) {
                    ppasContainer.innerHTML = '<div class="p-6 text-center text-slate-400">No MFOs/PPAs found for this UWP.</div>';
                    return;
                }

                let html = `
                    <table class="w-full border-collapse text-left text-sm text-slate-200">
                        <thead class="bg-slate-900/60 text-xs uppercase tracking-widest text-slate-400">
                            <tr>
                                <th class="px-5 py-4">PPA / MFO</th>
                                <th class="px-5 py-4 text-center">Success Indicators</th>
                                <th class="px-5 py-4">Target / Timeline</th>
                                <th class="px-5 py-4 text-center">Function</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 bg-slate-950">
                `;

                mfos.forEach(mfo => {
                    const indicatorCount = mfo.success_indicators?.length || 0;
                    const functionType = mfo.function_type || 'Core';
                    const functionColor = functionType.toLowerCase() === 'core' ? 'emerald' : 'indigo';

                    html += `
                        <tr>
                            <td class="px-5 py-5 font-medium">
                                ${mfo.title || 'Untitled MFO'}
                            </td>
                            <td class="px-5 py-5 text-center">
                                <div class="flex justify-center">
                                    <button
                                        onclick='showIndicatorsModal(${JSON.stringify(mfo).replace(/'/g, "\\'")})'
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                        <i class="fa-regular fa-eye text-sm"></i>
                                        <span>(${indicatorCount})</span>
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-5 text-slate-300">
                                ${mfo.target_timeline || 'Not specified'}
                            </td>
                            <td class="px-5 py-5 text-center">
                                <span class="rounded-full border border-${functionColor}-500/40 px-3 py-1 text-xs font-semibold text-${functionColor}-400">
                                    ${functionType}
                                </span>
                            </td>
                        </tr>
                    `;
                });

                html += `
                        </tbody>
                    </table>
                `;

                ppasContainer.innerHTML = html;
            }

            // ====================================
            // SUBMIT FOR APPROVAL - FIXED VERSION
            // ====================================
            function submitUwpForApproval() {
                if (!currentUwpId) {
                    showNotification('No UWP selected to submit.', 'error');
                    return;
                }

                const submitButton = document.querySelector('[data-submit-uwp-btn]');
                const buttonLabel = submitButton.querySelector('[data-button-label]');
                const buttonSpinner = submitButton.querySelector('[data-button-spinner]');

                submitButton.disabled = true;
                buttonLabel.textContent = 'Submitting...';
                buttonSpinner.classList.remove('hidden');

                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');

                const submitUrl = `{{ route('supervisor.uwp.submit', ['id' => '__ID__']) }}`.replace('__ID__', currentUwpId);

                console.log('Submitting to:', submitUrl);
                console.log('UWP ID:', currentUwpId);

                fetch(submitUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);

                    return response.json().then(data => {
                        if (!response.ok) {
                            throw new Error(data.error || data.message || `Server error: ${response.status}`);
                        }
                        return data;
                    });
                })
                .then(data => {
                    console.log('Success response:', data);

                    if (data.success) {
                        showNotification(data.message || 'UWP submitted successfully!', 'success');


                        const statusBadge = document.getElementById('modalStatus');
                        if (statusBadge) {
                            statusBadge.textContent = 'SUBMITTED';
                            statusBadge.className = 'mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold bg-blue-500/15 text-blue-400 border-blue-500/30';
                        }

                        submitButton.classList.add('hidden');

                        setTimeout(() => {
                            closeModal('uwpPreviewModal');
                            location.reload();
                        }, 1500);
                    } else {
                        throw new Error(data.error || 'Failed to submit UWP');
                    }
                })
                .catch(error => {
                    console.error('Error submitting UWP:', error);
                    showNotification(error.message || 'Error submitting UWP. Please try again.', 'error');

                    resetSubmitButton();
                });
            }

            function resetSubmitButton() {
                const submitButton = document.querySelector('[data-submit-uwp-btn]');
                if (submitButton) {
                    submitButton.disabled = false;
                    const buttonLabel = submitButton.querySelector('[data-button-label]');
                    const buttonSpinner = submitButton.querySelector('[data-button-spinner]');
                    if (buttonLabel) buttonLabel.textContent = 'Submit for Approval';
                    if (buttonSpinner) buttonSpinner.classList.add('hidden');
                }
            }

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
                            <td colspan="3" class="px-4 py-8 text-center text-slate-400">
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
                if (!items || items.length === 0) return '—';

                let html = '<ul class="list-disc space-y-1 pl-4 text-slate-200">';
                items.forEach(item => {
                    if (item) html += `<li>${item}</li>`;
                });
                html += '</ul>';
                return html;
            }

            function updateExportLink(uwpId) {
                const exportLink = document.getElementById('modalExportExcelLink');
                if (!exportLink) return;

                if (!uwpId) {
                    exportLink.setAttribute('href', '#');
                    return;
                }

                exportLink.setAttribute('href', window.uwpExportBaseUrl.replace('__ID__', uwpId));
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
                if (modalId === 'uwpPreviewModal') {
                    currentUwpId = null;
                    resetSubmitButton();
                }
            }

            function showNotification(message, type = 'success') {
                const existingContainer = document.getElementById('notification-container');
                if (existingContainer) {
                    existingContainer.remove();
                }

                const container = document.createElement('div');
                container.id = 'notification-container';
                container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2';
                document.body.appendChild(container);

                const notification = document.createElement('div');
                notification.className = `px-4 py-3 rounded-lg text-sm font-semibold shadow-lg flex items-center justify-between min-w-[300px] ${
                    type === 'success'
                        ? 'bg-emerald-500/20 border border-emerald-500/30 text-emerald-200'
                        : 'bg-red-500/20 border border-red-500/30 text-red-200'
                }`;

                notification.innerHTML = `
                    <span>${message}</span>
                    <button onclick="this.parentElement.remove()" class="ml-3 text-current opacity-70 hover:opacity-100">×</button>
                `;

                container.appendChild(notification);

                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                        if (container.children.length === 0) {
                            container.remove();
                        }
                    }
                }, 5000);
            }

            // ====================================
            // INITIALIZATION
            // ====================================
            document.addEventListener('DOMContentLoaded', function() {
                const submitButton = document.querySelector('[data-submit-uwp-btn]');
                if (submitButton) {
                    submitButton.replaceWith(submitButton.cloneNode(true));
                    const newSubmitButton = document.querySelector('[data-submit-uwp-btn]');
                    newSubmitButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        submitUwpForApproval();
                    });
                }

                window.addEventListener('click', function(event) {
                    const modals = ['uwpPreviewModal', 'successIndicatorsModal', 'indicatorStandardsModal', 'assignmentsModal'];
                    modals.forEach(modalId => {
                        const modal = document.getElementById(modalId);
                        if (event.target === modal) {
                            closeModal(modalId);
                        }
                    });
                });

                window.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        const modals = ['uwpPreviewModal', 'successIndicatorsModal', 'indicatorStandardsModal', 'assignmentsModal'];
                        modals.forEach(modalId => {
                            const modal = document.getElementById(modalId);
                            if (!modal.classList.contains('hidden')) {
                                closeModal(modalId);
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection
