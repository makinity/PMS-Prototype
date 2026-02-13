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

                        <select
                            id="uwp-office-unit"
                            name="office_id"
                            onchange="this.form.submit()"
                            class="w-full rounded-lg border border-slate-800 bg-slate-950 px-3 py-2
                                text-sm text-slate-100 focus:border-blue-500
                                focus:ring-2 focus:ring-blue-500/40 focus:outline-none"
                                style="background:#0f172a;color:#e5e7eb;"
                        >
                            <option value="">All Offices</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}"
                                    {{ request('office_id') == $office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        </select>
                </div>
            </div>

            <!-- Table -->
            @if($lists->isEmpty())
                <div class="text-center py-8 text-slate-400">
                    No Unit Work Plans found for the selected office.
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
                                        {{ $list->office->name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $list->performancePeriod->name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        @php
                                            $statusColors = [
                                                'draft' => 'border-yellow-500/30 bg-yellow-500/10 text-yellow-300',
                                                'submitted' => 'border-blue-500/30 bg-blue-500/10 text-blue-300',
                                                'endorsed' => 'border-indigo-500/30 bg-indigo-500/10 text-indigo-300',
                                                'pmt_approved' => 'border-purple-500/30 bg-purple-500/10 text-purple-300',
                                            ];
                                            $statusClass = $statusColors[strtolower($list->status)] ?? 'border-gray-500/30 bg-gray-500/10 text-gray-300';
                                        @endphp

                                        <span
                                            class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $list->status)) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <button type="button"
                                                aria-label="View Unit Work Plan"
                                                title="View Unit Work Plan"
                                                onclick="showUwpPreview({{ $list->id }})"
                                                class="inline-flex items-center justify-center rounded-lg
                                                    p-2 text-slate-400 hover:text-white
                                                    hover:bg-slate-800 transition">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </button>
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
                    <a href="{{ route('stage1.uwp.export.excel') }}"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800">
                        Export Excel
                    </a>
                    <button onclick="closeModal('uwpPreviewModal')"
                            class="rounded-lg border border-slate-700 bg-slate-900 px-5 py-2 text-sm hover:bg-slate-800">
                        Close
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
        // Get the route pattern and replace the {id} placeholder
        window.uwpPreviewBaseUrl = "{{ route('supervisor.uwp.preview', ['id' => '__ID__']) }}";
    </script>
    @push('scripts')
    <script>
        // Standard ratings
        const standardRatings = [5, 4, 3, 2, 1];

        // Create list element for standards
        function createListElement(items) {
            const container = document.createElement('div');
            if (!items || items.length === 0) {
                container.textContent = '—';
                return container;
            }

            if (!Array.isArray(items)) {
                items = [items];
            }

            const ul = document.createElement('ul');
            ul.className = 'list-disc space-y-1 pl-4 text-slate-200';
            items.forEach((item) => {
                if (item) {
                    const li = document.createElement('li');
                    li.textContent = item;
                    ul.appendChild(li);
                }
            });
            container.appendChild(ul);
            return container;
        }

        // Render standards for an indicator from the database
        function renderIndicatorStandards(qetStandards) {
            const standardsBody = document.getElementById('indicatorStandardsBody');
            if (!standardsBody) return;

            standardsBody.innerHTML = '';

            console.log('QET Standards received:', qetStandards); // Debug log

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

            // Group standards by rating and dimension
            const standardsByRating = {};

            qetStandards.forEach(standard => {
                const rating = standard.rating;
                const dimension = standard.dimension;
                const text = standard.standard_text;

                if (!standardsByRating[rating]) {
                    standardsByRating[rating] = {
                        q: [],
                        e: [],
                        t: []
                    };
                }

                // Add to appropriate dimension
                if (dimension === 'q') {
                    standardsByRating[rating].q.push(text);
                } else if (dimension === 'e') {
                    standardsByRating[rating].e.push(text);
                } else if (dimension === 't') {
                    standardsByRating[rating].t.push(text);
                }
            });

            // Render each rating level (5,4,3,2,1)
            const allRatings = [5, 4, 3, 2, 1];

            allRatings.forEach(rating => {
                const rowData = standardsByRating[rating] || { q: [], e: [], t: [] };

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-900/40';

                // Rating cell
                const ratingTd = document.createElement('td');
                ratingTd.className = 'px-4 py-3 font-semibold';
                ratingTd.textContent = rating;

                // Quality cell
                const qualityTd = document.createElement('td');
                qualityTd.className = 'px-4 py-3 align-top';
                qualityTd.appendChild(createListElement(rowData.q));

                // Efficiency cell
                const efficiencyTd = document.createElement('td');
                efficiencyTd.className = 'px-4 py-3 align-top';
                efficiencyTd.appendChild(createListElement(rowData.e));

                // Timeliness cell
                const timelinessTd = document.createElement('td');
                timelinessTd.className = 'px-4 py-3 align-top';
                timelinessTd.appendChild(createListElement(rowData.t));

                tr.appendChild(ratingTd);
                tr.appendChild(qualityTd);
                tr.appendChild(efficiencyTd);
                tr.appendChild(timelinessTd);

                standardsBody.appendChild(tr);
            });
        }

        // Main function to show UWP preview
        function showUwpPreview(uwpId) {
            // Show loading state
            document.getElementById('uwpPreviewModal').classList.remove('hidden');
            document.getElementById('modalPPAsContainer').innerHTML = '<div class="p-6 text-center text-slate-400">Loading UWP data...</div>';

            // Set default values while loading
            document.getElementById('modalOfficeUnit').textContent = 'Loading...';
            document.getElementById('modalUwpSubtitle').textContent = 'Loading...';
            document.getElementById('modalSupervisor').textContent = 'Loading...';
            document.getElementById('modalDeptHead').textContent = 'Loading...';
            document.getElementById('modalStatus').textContent = 'LOADING';

            // Replace the placeholder with the actual ID
            const url = window.uwpPreviewBaseUrl.replace('__ID__', uwpId);
            console.log('Fetching from:', url);

            // Fetch UWP data from server
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
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

        // Populate UWP modal with data
        function populateUwpModal(uwpData) {
            // Populate header and summary
            document.getElementById('modalOfficeUnit').textContent = uwpData.office?.name || 'N/A';
            document.getElementById('modalUwpSubtitle').textContent =
                `${uwpData.office?.name || 'Unit'} • ${uwpData.performance_period?.name || 'Performance Period'}`;
            document.getElementById('modalSupervisor').textContent = uwpData.creator?.name || 'Not Assigned';
            document.getElementById('modalDeptHead').textContent = uwpData.department_head?.name || 'Not Assigned';

            // Update status badge - FIXED VERSION
            const statusBadge = document.getElementById('modalStatus');
            const status = uwpData.status || 'draft';
            statusBadge.textContent = status.replace('_', ' ').toUpperCase();

            // Reset classes
            statusBadge.className = 'mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold';

            // Add status-specific classes
            const statusColors = {
                'draft': ['bg-yellow-500/15', 'text-yellow-400', 'border-yellow-500/30'],
                'submitted': ['bg-blue-500/15', 'text-blue-400', 'border-blue-500/30'],
                'endorsed': ['bg-indigo-500/15', 'text-indigo-400', 'border-indigo-500/30'],
                'pmt_approved': ['bg-purple-500/15', 'text-purple-400', 'border-purple-500/30']
            };

            const classes = statusColors[status] || ['bg-gray-500/15', 'text-gray-400', 'border-gray-500/30'];
            classes.forEach(cls => statusBadge.classList.add(cls));

            // Populate MFOs table
            const ppasContainer = document.getElementById('modalPPAsContainer');

            if (!uwpData.uwp_functions || uwpData.uwp_functions.length === 0) {
                ppasContainer.innerHTML = '<div class="p-6 text-center text-slate-400">No functions/MFOs found for this UWP.</div>';
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

            uwpData.uwp_functions.forEach(uwpFunction => {
                if (uwpFunction.mfos && uwpFunction.mfos.length > 0) {
                    uwpFunction.mfos.forEach(mfo => {
                        const indicatorCount = mfo.success_indicators?.length || 0;
                        const functionType = uwpFunction.function_type || 'Core';
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
                }
            });

            html += `
                    </tbody>
                </table>
            `;

            ppasContainer.innerHTML = html;
        }

                // Show indicators modal
        // Show indicators modal
        function showIndicatorsModal(mfoData) {
            console.log('MFO Data:', mfoData);

            document.getElementById('modalPpaTitle').textContent = mfoData.title || 'Untitled MFO';

            const indicatorsBody = document.getElementById('modalIndicatorsBody');

            if (!mfoData.success_indicators || mfoData.success_indicators.length === 0) {
                indicatorsBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-slate-400">
                            No success indicators found for this MFO.
                        </td>
                    </tr>
                `;
            } else {
                let html = '';

                mfoData.success_indicators.forEach(indicator => {
                    const assignments = indicator.assignments || [];
                    const assignmentCount = assignments.length;

                    // Get QET standards count
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
                    `;

                    // Handle different assignment scenarios - ALL CASES show eye icon
                    if (assignmentCount === 0) {
                        // No assignments - show eye icon with 0
                        html += `
                            <button
                                onclick='showAssignmentsModal(${JSON.stringify({
                                    indicatorText: indicator.indicator_text,
                                    mfoTitle: mfoData.title,
                                    assignments: assignments
                                }).replace(/'/g, "\\'")})'
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-transparent bg-slate-900/60 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:border-slate-700 hover:text-white hover:bg-slate-800/80 min-w-[90px]">
                                <i class="fa-regular fa-eye text-sm"></i>
                                <span>(0)</span>
                            </button>
                        `;
                    } else {
                        // One or more assignments - show eye icon with count
                        html += `
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
                        `;
                    }

                    html += `
                            </td>
                        </tr>
                    `;
                });

                indicatorsBody.innerHTML = html;
            }

            closeModal('uwpPreviewModal');
            document.getElementById('successIndicatorsModal').classList.remove('hidden');
        }

        // Show standards modal
        function showStandardsModal(data) {
            document.getElementById('indicatorStandardsModalMfo').textContent = data.mfoTitle || '--';
            document.getElementById('indicatorStandardsModalIndicator').textContent = data.indicatorText || '--';

            // Render standards from database
            renderIndicatorStandards(data.qetStandards);

            document.getElementById('indicatorStandardsModal').classList.remove('hidden');
        }

        // Close modal function
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modals = ['uwpPreviewModal', 'successIndicatorsModal', 'indicatorStandardsModal', 'assignmentsModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (event.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            });
        });

                // Show assignments modal for multiple employees
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

            // Close the indicators modal and open assignments modal
            closeModal('successIndicatorsModal');
            document.getElementById('assignmentsModal').classList.remove('hidden');
        }
    </script>
    @endpush
@endsection
