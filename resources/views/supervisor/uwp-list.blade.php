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
                                <tr class="hover:bg-slate-900/50 transition" data-uwp-row="{{ (int) $list->id }}">
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
                                            $previewPayload = [
                                                'id' => (int) $list->id,
                                                'status' => (string) $list->status,
                                                'return_remarks' => (string) ($list->return_remarks ?? ''),
                                                'returned_at' => optional($list->returned_at)->toDateTimeString(),
                                            ];
                                        @endphp

                                        <a href="{{ route('supervisor.uwp', ['uwp_id' => $list->id]) }}"
                                           aria-label="Open Unit Work Plan"
                                           title="{{ $isEditable ? 'Open for editing' : 'Open read-only' }}"
                                           class="inline-flex items-center justify-center rounded-lg p-2 text-slate-400 transition hover:bg-slate-800 hover:text-white">
                                            <i class="fa-regular fa-pen-to-square text-sm"></i>
                                        </a>

                                        <button type="button"
                                                aria-label="View Unit Work Plan"
                                                title="View Unit Work Plan"
                                                data-uwp-preview-btn
                                                data-uwp='@json($previewPayload)'
                                                onclick="showUwpPreview({{ $list->id }}, this)"
                                                class="inline-flex items-center justify-center rounded-lg
                                                    p-2 text-slate-400 hover:text-white
                                                    hover:bg-slate-800 transition">
                                            <i class="fa-regular fa-eye text-sm"></i>
                                        </button>

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
    
    @include('partials.signature-pad-modal')

    {{-- ====================================
        DYNAMIC MODALS - ONLY THESE SHOULD EXIST
    ===================================== --}}

    <!-- UWP Preview Modal -->
    <div id="uwpPreviewModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="w-full max-w-5xl px-6">
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

                <div id="uwp-return-remarks-wrap" class="hidden mx-8 mt-6 rounded-xl border border-rose-500/20 bg-rose-500/5 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-rose-200">Returned Remarks</p>
                    <p id="uwp-return-remarks-text" class="mt-2 whitespace-pre-wrap text-sm text-slate-100">—</p>
                    <p id="uwp-return-remarks-meta" class="mt-2 text-[11px] text-slate-400"></p>
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
                    <a id="modalExportExcelLink" href="#" aria-disabled="true"
                        class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800 opacity-50 cursor-not-allowed pointer-events-none">
                        Export Excel
                    </a>
                    <button type="button"
                            data-submit-uwp-trigger
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-900/40 transition hover:bg-blue-500">
                        <span>Submit for Approval</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="uwpWorkspacePreviewModal" class="fixed inset-0 z-[60] hidden items-start justify-center overflow-y-auto bg-black/70 px-4 py-4 backdrop-blur-sm sm:py-8">
        <div class="w-full max-w-[1200px]">
            <div class="flex h-[780px] max-h-[calc(100vh-2rem)] flex-col overflow-hidden rounded-[24px] border border-slate-800 bg-slate-950 text-slate-100 shadow-2xl sm:max-h-[calc(100vh-4rem)]">
                <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-6 py-5">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-lg font-semibold text-white">UWP Preview</h2>
                            <span class="rounded-full border border-indigo-500/20 bg-indigo-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-200">Stage I - Planning</span>
                        </div>
                        <div class="hidden">
                            <p id="workspaceModalUwpSubtitle" class="mt-2 text-sm text-slate-400">Select a UWP to view details</p>
                            <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-400">
                                <span id="workspaceModalOfficeUnitInline">-</span>
                                <span class="text-slate-600">•</span>
                                <span id="workspaceModalPeriodInline">-</span>
                                <span class="text-slate-600">•</span>
                                <span id="workspaceModalSupervisorInline">-</span>
                                <span id="workspaceModalStatus" class="ml-1 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold">-</span>
                                <span id="workspaceModalOutputCountInline" class="inline-flex rounded-full bg-slate-800 px-2.5 py-1 text-xs font-semibold text-slate-200">0 outputs</span>
                            </div>
                        </div>
                    </div>
                    <button type="button"
                            onclick="closeModal('uwpWorkspacePreviewModal')"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-700 bg-slate-950/60 text-slate-400 transition hover:bg-slate-900 hover:text-white">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div id="workspace-uwp-return-remarks-wrap" class="hidden border-b border-rose-500/20 bg-rose-500/5 px-6 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-rose-200">Returned Remarks</p>
                    <p id="workspace-uwp-return-remarks-text" class="mt-2 whitespace-pre-wrap text-sm text-slate-100">-</p>
                    <p id="workspace-uwp-return-remarks-meta" class="mt-2 text-[11px] text-slate-400"></p>
                </div>

                <div class="grid min-h-0 flex-1 lg:grid-cols-[300px_minmax(0,1fr)]">
                    <aside class="flex min-h-0 flex-col border-b border-slate-800 lg:border-b-0 lg:border-r">
                        <div class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Planned Outputs</p>
                            <span id="workspaceModalOutputCountBadge" class="text-sm font-semibold text-blue-300">0</span>
                        </div>
                        <div class="flex border-b border-slate-800 px-2 pt-2">
                            <button type="button" data-preview-function-tab="all" class="flex-1 border-b-2 border-blue-400 pb-2 text-xs font-semibold text-white transition">All</button>
                            <button type="button" data-preview-function-tab="core" class="flex-1 border-b-2 border-transparent pb-2 text-xs font-medium text-slate-400 transition hover:text-slate-300">Core</button>
                            <button type="button" data-preview-function-tab="support" class="flex-1 border-b-2 border-transparent pb-2 text-xs font-medium text-slate-400 transition hover:text-slate-300">Support</button>
                        </div>
                        <div id="workspaceModalOutputList" class="min-h-0 space-y-2 overflow-y-auto px-2 py-2"></div>
                    </aside>

                    <section class="flex min-h-0 flex-col">
                        <div class="border-b border-slate-800 px-6 py-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 id="workspaceModalDetailTitle" class="text-lg font-semibold leading-tight text-white">No output selected</h3>
                                <span id="workspaceModalDetailFunction" class="hidden rounded-md border px-2 py-1 text-xs font-medium"></span>
                                <span id="workspaceModalDetailWeight" class="hidden text-sm font-semibold text-slate-300"></span>
                            </div>
                        </div>

                        <div class="border-b border-slate-800 px-5">
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" data-supervisor-preview-tab="overview" class="border-b-2 border-blue-400 px-2.5 py-2.5 text-sm font-semibold text-white">Overview</button>
                                <button type="button" data-supervisor-preview-tab="indicators" class="border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Success Indicators</button>
                                <button type="button" data-supervisor-preview-tab="standards" class="border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Standards (Q/E/T)</button>
                                <button type="button" data-supervisor-preview-tab="assignees" class="border-b-2 border-transparent px-2.5 py-2.5 text-sm font-medium text-slate-400">Assigned Employees</button>
                            </div>
                        </div>

                        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-4">
                            <div data-supervisor-preview-panel="overview" class="space-y-5">
                                <div class="hidden">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Target Summary</p>
                                    <p id="workspaceModalTargetSummary" class="mt-2 text-lg leading-snug text-white">-</p>
                                </div>
                                <div class="hidden grid gap-5 sm:grid-cols-2">
                                    <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Function Type</p><div id="workspaceModalFunctionCopy" class="mt-2"></div></div>
                                    <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Weight</p><p id="workspaceModalWeightCopy" class="mt-2 text-lg font-semibold text-white">-</p></div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Linked Success Indicators</p>
                                    <div id="workspaceModalOverviewIndicators" class="mt-3 space-y-2.5"></div>
                                </div>
                            </div>

                            <div data-supervisor-preview-panel="indicators" class="hidden">
                                <div class="overflow-hidden rounded-xl border border-slate-800">
                                    <table class="min-w-full text-sm text-slate-200">
                                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.22em] text-slate-400">
                                            <tr>
                                                <th class="px-4 py-3 text-left">Success Indicator</th>
                                                <th class="px-4 py-3 text-left">Target Summary</th>
                                                <th class="px-4 py-3 text-center">Standards</th>
                                                <th class="px-4 py-3 text-center">Assigned</th>
                                            </tr>
                                        </thead>
                                        <tbody id="workspaceModalIndicatorsBody" class="divide-y divide-slate-800"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div data-supervisor-preview-panel="standards" class="hidden space-y-4">
                                <div class="hidden"><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Selected Indicator</p><p id="workspaceModalStandardsIndicator" class="mt-1.5 text-base font-semibold text-white">-</p></div>
                                <div class="overflow-hidden rounded-xl border border-slate-800">
                                    <table class="min-w-full text-sm text-slate-100">
                                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.22em] text-slate-400">
                                            <tr><th class="px-4 py-3 text-left">Rating</th><th class="px-4 py-3 text-left">Quality (Q)</th><th class="px-4 py-3 text-left">Efficiency (E)</th><th class="px-4 py-3 text-left">Timeliness (T)</th></tr>
                                        </thead>
                                        <tbody id="workspaceIndicatorStandardsBody" class="divide-y divide-slate-800"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div data-supervisor-preview-panel="assignees" class="hidden space-y-4">
                                <div class="hidden"><p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Selected Indicator</p><p id="workspaceModalAssigneesIndicator" class="mt-1.5 text-base font-semibold text-white">-</p></div>
                                <div class="overflow-hidden rounded-xl border border-slate-800">
                                    <table class="w-full text-sm text-slate-100">
                                        <thead class="bg-slate-900/70 text-xs uppercase tracking-[0.2em] text-slate-400">
                                            <tr>
                                                <th class="px-4 py-3 text-left">Employee Name</th>
                                                <th class="px-4 py-3 text-left">Office / Unit</th>
                                                <th class="px-4 py-3 text-left">Assigned Date</th>
                                                <th class="px-4 py-3 text-left">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="workspaceAssignmentsBody" class="divide-y divide-slate-800"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="grid shrink-0 gap-3 border-t border-slate-800 px-6 py-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                    <div class="flex items-center gap-3">
                        <a id="workspaceModalExportExcelLink" href="#" aria-disabled="true"
                            class="rounded-lg border border-slate-700 px-3 py-2 text-xs text-slate-300 hover:bg-slate-800 opacity-50 cursor-not-allowed pointer-events-none">
                            Export Excel
                        </a>
                        <p class="text-xs text-slate-500">Read-only preview of the submitted UWP content.</p>
                    </div>
                    <div class="flex flex-wrap justify-end gap-2.5">
                        <button type="button"
                                onclick="closeModal('uwpWorkspacePreviewModal')"
                                class="rounded-xl border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                            Close
                        </button>
                        <button type="button"
                                id="workspaceSubmitUwpTrigger"
                                class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-900/40 transition hover:bg-blue-500">
                            <span>Submit for Approval</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Indicators Modal -->
    <div id="successIndicatorsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="w-full max-w-5xl px-6">
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
<div id="assignmentsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
    <div class="w-full max-w-2xl px-6">
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
    <div id="indicatorStandardsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="w-full max-w-3xl px-6">
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


    <!-- Delete UWP Confirmation Modal -->

    <div id="deleteUwpModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70 backdrop-blur-sm overflow-y-auto">
        <div class="w-full max-w-xl px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 text-slate-100">
                <div class="border-b border-slate-800 px-6 py-5">
                    <p class="text-xs uppercase tracking-[0.2em] text-rose-300">Delete Unit Work Plan</p>
                    <h3 class="text-lg font-semibold">Delete this UWP?</h3>
                    <p class="mt-1 text-sm text-slate-400">This action is permanent and cannot be undone.</p>
                </div>

                <div class="space-y-3 px-6 py-5 text-sm text-slate-200">
                    <div class="rounded-xl border border-slate-800 bg-slate-900/60 p-4">
                        <p><span class="text-slate-400">Office / Unit:</span> <span id="deleteUwpOffice">--</span></p>
                        <p class="mt-1"><span class="text-slate-400">Performance Period:</span> <span id="deleteUwpPeriod">--</span></p>
                        <p class="mt-1"><span class="text-slate-400">Status:</span> <span id="deleteUwpStatus">--</span></p>
                    </div>
                    <p class="text-xs text-rose-300/90">Only Draft/Returned and unlocked UWP records can be deleted.</p>
                </div>

                <form id="delete-uwp-form" method="POST" action="" class="flex items-center justify-end gap-3 border-t border-slate-800 px-6 py-4">
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
        window.uwpPreviewBaseUrl = "{{ route('supervisor.uwp.show', ['id' => '__ID__']) }}";
        window.uwpSubmitBaseUrl = "{{ route('supervisor.uwp.submit', ['id' => '__ID__']) }}";
        window.uwpExportBaseUrl = "{{ route('uwp.excel.export', ['uwp' => '__ID__']) }}";
        window.uwpDeleteBaseUrl = "{{ route('supervisor.uwp.destroy', ['id' => '__ID__']) }}";
    </script>
    @push('scripts')
        <script>
            // Global triggers
            document.querySelectorAll('[data-submit-uwp-trigger], #workspaceSubmitUwpTrigger').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const targetId = currentUwpId || (currentPreviewUwp ? currentPreviewUwp.id : null);
                    if (!targetId) {
                        showNotification('No UWP selected for submission.', 'error');
                        return;
                    }
                    
                    // Set the current ID for the signature handler
                    window.uwpToSubmitId = targetId;
                    
                    // Show signature modal
                    const sigModal = document.getElementById('signature-pad-modal');
                    if (sigModal) {
                        sigModal.classList.remove('hidden');
                        sigModal.classList.add('flex');
                    }
                });
            });

            // Signature Modal Buttons
            const confirmSigBtn = document.getElementById('signature-pad-confirm');
            if (confirmSigBtn) {
                confirmSigBtn.addEventListener('click', async () => {
                    const signatureData = window.getSignatureData_signature_pad_modal ? window.getSignatureData_signature_pad_modal() : null;
                    
                    if (!signatureData) {
                        showNotification('Please provide your signature before submitting.', 'error');
                        return;
                    }

                    const uwpId = window.uwpToSubmitId;
                    if (!uwpId) return;

                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                    if (!csrfToken) {
                        showNotification('Security token missing. Please refresh the page.', 'error');
                        return;
                    }

                    // Start loading state
                    confirmSigBtn.disabled = true;
                    confirmSigBtn.textContent = 'Submitting...';

                    try {
                        const response = await fetch(window.uwpSubmitBaseUrl.replace('__ID__', uwpId), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                signature: signatureData
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            showNotification(result.message || 'UWP submitted successfully.', 'success');
                            // Close modals and refresh
                            document.getElementById('signature-pad-modal').classList.add('hidden');
                            document.getElementById('uwpPreviewModal').classList.add('hidden');
                            document.getElementById('uwpWorkspacePreviewModal').classList.add('hidden');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showNotification(result.error || 'Failed to submit UWP.', 'error');
                        }
                    } catch (error) {
                        console.error('Submission error:', error);
                        showNotification('An unexpected error occurred during submission.', 'error');
                    } finally {
                        confirmSigBtn.disabled = false;
                        confirmSigBtn.textContent = 'Confirm';
                    }
                });
            }

            // Close handlers for signature modal
            document.querySelectorAll('[data-signature-close]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const sigModal = document.getElementById('signature-pad-modal');
                    if (sigModal) sigModal.classList.add('hidden');
                    if (window.clearSignaturePad_signature_pad_modal) {
                        window.clearSignaturePad_signature_pad_modal();
                    }
                });
            });

            // Notification helper using PMSnackbar
            function showNotification(message, type = 'success') {
                if (window.PMSnackbar) {
                    window.PMSnackbar.show({
                        type: type,
                        message: message
                    });
                } else {
                    alert(message);
                }
            }

            let currentUwpId = null;
            let selectedUwp = null;
            let currentPreviewUwp = null;
            let selectedPreviewOutputIndex = 0;
            let selectedPreviewIndicatorIndex = 0;
            let activePreviewTab = 'overview';
            let activePreviewFunctionTab = 'all';

            function normalizeTargetQuantity(value) {
                if (value === null || value === undefined || value === '') {
                    return '';
                }

                const numeric = Number(value);
                if (!Number.isFinite(numeric)) {
                    return String(value).trim();
                }

                return Number.isInteger(numeric)
                    ? String(numeric)
                    : numeric.toFixed(2).replace(/\.00$/, '').replace(/(\.\d*[1-9])0+$/, '$1');
            }

            function formatTargetTimelineDisplay(targetQuantity, targetTimeline) {
                const summary = String(targetTimeline || '').trim();
                if (summary.toLowerCase() === 'multiple indicator targets') {
                    return summary;
                }

                const quantity = targetQuantity === null || targetQuantity === undefined || targetQuantity === ''
                    ? ''
                    : normalizeTargetQuantity(targetQuantity);
                const timeline = targetTimeline === null || targetTimeline === undefined || targetTimeline === ''
                    ? ''
                    : String(targetTimeline).trim();

                if (quantity !== '' && timeline !== '') {
                    return `${quantity} ${timeline}`.trim();
                }

                if (quantity !== '') {
                    return quantity;
                }

                if (timeline !== '') {
                    return timeline;
                }

                return 'Not specified';
            }

            function getIndicatorTargetSummary(indicator) {
                return formatTargetTimelineDisplay(
                    indicator?.targetQuantity ?? indicator?.target_quantity,
                    indicator?.targetTimeline ?? indicator?.target_timeline
                );
            }

            function getMfoTargetSummary(mfo) {
                const indicators = Array.isArray(mfo?.success_indicators) ? mfo.success_indicators : [];
                const summaries = Array.from(new Set(
                    indicators
                        .map((indicator) => getIndicatorTargetSummary(indicator))
                        .filter((value) => String(value || '').trim() !== '' && value !== 'Not specified')
                ));

                if (summaries.length === 1) {
                    return summaries[0];
                }

                if (summaries.length > 1) {
                    return 'Multiple indicator targets';
                }

                return formatTargetTimelineDisplay(mfo?.target_quantity, mfo?.target_timeline);
            }

            function labelStatus(status) {
                return String(status || '-')
                    .replace(/_/g, ' ')
                    .replace(/\b\w/g, (char) => char.toUpperCase());
            }

            function buildFunctionBadge(type) {
                const normalized = String(type || '').toLowerCase();
                const classes = normalized === 'core'
                    ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400'
                    : normalized === 'support'
                        ? 'border-blue-400/30 bg-blue-500/10 text-blue-300'
                        : 'border-slate-500/20 bg-slate-500/10 text-slate-200';

                return `<span class="inline-flex rounded-md border px-2 py-1 text-xs font-medium ${classes}">${labelStatus(normalized || '-')}</span>`;
            }

            function previewStatusClass(status) {
                const normalized = String(status || '').toLowerCase();
                return {
                    draft: 'border-yellow-500/30 bg-yellow-500/10 text-yellow-300',
                    submitted: 'border-blue-500/30 bg-blue-500/10 text-blue-300',
                    consolidated: 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300',
                    endorsed: 'border-indigo-500/30 bg-indigo-500/10 text-indigo-300',
                    pmt_approved: 'border-purple-500/30 bg-purple-500/10 text-purple-300',
                    returned: 'border-rose-500/30 bg-rose-500/10 text-rose-300',
                }[normalized] || 'border-slate-500/30 bg-slate-500/10 text-slate-300';
            }

            function getPreviewOutputs() {
                const functions = Array.isArray(currentPreviewUwp?.uwp_functions) ? currentPreviewUwp.uwp_functions : [];
                return functions.flatMap((uwpFunction) => {
                    const mfos = Array.isArray(uwpFunction?.mfos) ? uwpFunction.mfos : [];
                    return mfos.map((mfo) => ({
                        title: mfo?.title || '-',
                        function_type: uwpFunction?.function_type || 'Core',
                        weight_percent: mfo?.weight_percent ?? uwpFunction?.weight_percent ?? '',
                        target_summary: getMfoTargetSummary(mfo),
                        success_indicators: Array.isArray(mfo?.success_indicators) ? mfo.success_indicators : [],
                    }));
                });
            }

            function getSelectedPreviewOutput() {
                const outputs = getPreviewOutputs();
                if (!outputs.length) return null;
                selectedPreviewOutputIndex = Math.min(Math.max(selectedPreviewOutputIndex, 0), outputs.length - 1);
                return outputs[selectedPreviewOutputIndex] || null;
            }

            function getSelectedPreviewIndicator() {
                const output = getSelectedPreviewOutput();
                const indicators = Array.isArray(output?.success_indicators) ? output.success_indicators : [];
                if (!indicators.length) return null;
                selectedPreviewIndicatorIndex = Math.min(Math.max(selectedPreviewIndicatorIndex, 0), indicators.length - 1);
                return indicators[selectedPreviewIndicatorIndex] || null;
            }

            const previewSeedStandardsMap = {
                'All e-bank transactions scanned and encoded daily': {
                    5: { q: ['No errors; accurate encoding'], e: ['100% processed'], t: ['Same working day'] },
                    4: { q: ['Minor errors'], e: ['100% processed'], t: ['Same working day'] },
                    3: { q: ['Few minor errors'], e: ['95â€“99% processed'], t: ['End of working day'] },
                    2: { q: ['Multiple errors'], e: ['<95% processed'], t: ['Beyond working day'] },
                    1: { q: ['Major errors/missing'], e: ['Majority unprocessed'], t: ['Not within acceptable time'] },
                },
                'Indexing complete with no missing pages': {
                    5: { q: ['Indexing fully verified, zero gaps'], e: ['100% pages indexed'], t: ['Same day'] },
                    4: { q: ['Indexing minor rechecks'], e: ['100% pages indexed'], t: ['Same day'] },
                    3: { q: ['Occasional missing indexes fixed'], e: ['95â€“99% indexed'], t: ['Within 24 hours'] },
                    2: { q: ['Frequent missing pages'], e: ['<95% indexed'], t: ['Beyond 24 hours'] },
                    1: { q: ['Indexing largely incomplete'], e: ['Major gaps'], t: ['Unacceptable'] },
                },
                'Audit trail maintained within 24 hours': {
                    5: { q: ['Complete trail, no errors'], e: ['100% entries captured'], t: ['Within 24 hours'] },
                    4: { q: ['Minor corrections only'], e: ['100% entries captured'], t: ['Within 24 hours'] },
                    3: { q: ['Some gaps corrected'], e: ['95â€“99% entries captured'], t: ['Within 48 hours'] },
                    2: { q: ['Multiple missing logs'], e: ['<95% captured'], t: ['Beyond 48 hours'] },
                    1: { q: ['Trail missing'], e: ['Majority uncaptured'], t: ['Unacceptable'] },
                },
                'Same-day verification of OTC transactions': {
                    5: { q: ['Verified without discrepancies'], e: ['100% OTC verified'], t: ['Same working day'] },
                    4: { q: ['Minor verifications pending'], e: ['100% OTC verified'], t: ['Same working day'] },
                    3: { q: ['Few pending verifications'], e: ['95â€“99% verified'], t: ['End of working day'] },
                    2: { q: ['Several unverified'], e: ['<95% verified'], t: ['Beyond working day'] },
                    1: { q: ['Verification not done'], e: ['Majority unverified'], t: ['Unacceptable'] },
                },
                '95% encoded within the business day': {
                    5: { q: ['Encodings error-free'], e: ['100% encoded'], t: ['Same business day'] },
                    4: { q: ['Minor corrections'], e: ['100% encoded'], t: ['Same business day'] },
                    3: { q: ['Few delays'], e: ['95â€“99% encoded'], t: ['By end of day'] },
                    2: { q: ['Multiple delays'], e: ['<95% encoded'], t: ['Next day'] },
                    1: { q: ['Encoding largely incomplete'], e: ['Major backlog'], t: ['Unacceptable'] },
                },
                'OR validation completed daily': {
                    5: { q: ['All ORs validated error-free'], e: ['100% validated'], t: ['Daily'] },
                    4: { q: ['Minor issues corrected same day'], e: ['100% validated'], t: ['Daily'] },
                    3: { q: ['Some validations late'], e: ['95â€“99% validated'], t: ['Within 48 hours'] },
                    2: { q: ['Frequent late validations'], e: ['<95% validated'], t: ['Beyond 48 hours'] },
                    1: { q: ['Validations mostly missing'], e: ['Majority unvalidated'], t: ['Unacceptable'] },
                },
                'Weekly filing updated and retrievable': {
                    5: { q: ['Zero retrieval issues'], e: ['100% weekly updates'], t: ['Within week'] },
                    4: { q: ['Minor retrieval fixes'], e: ['100% weekly updates'], t: ['Within week'] },
                    3: { q: ['Some items late'], e: ['95â€“99% updates'], t: ['Within next week'] },
                    2: { q: ['Many late updates'], e: ['<95% updates'], t: ['Beyond next week'] },
                    1: { q: ['Updates not done'], e: ['Major gaps'], t: ['Unacceptable'] },
                },
                'Digital backups synced monthly': {
                    5: { q: ['Backups verified'], e: ['100% synced'], t: ['Within month'] },
                    4: { q: ['Minor sync corrections'], e: ['100% synced'], t: ['Within month'] },
                    3: { q: ['Some delays'], e: ['95â€“99% synced'], t: ['Within following week'] },
                    2: { q: ['Frequent delays'], e: ['<95% synced'], t: ['Beyond following week'] },
                    1: { q: ['Backups largely missing'], e: ['Major gaps'], t: ['Unacceptable'] },
                },
                'Retrieval logs maintained for audits': {
                    5: { q: ['Logs complete and audit-ready'], e: ['100% requests logged'], t: ['Same day'] },
                    4: { q: ['Minor log gaps corrected'], e: ['100% requests logged'], t: ['Same day'] },
                    3: { q: ['Some gaps'], e: ['95â€“99% logged'], t: ['Within 48 hours'] },
                    2: { q: ['Many gaps'], e: ['<95% logged'], t: ['Beyond 48 hours'] },
                    1: { q: ['Logs largely missing'], e: ['Majority unlogged'], t: ['Unacceptable'] },
                },
                'All daily collections posted to the ledger within the day': {
                    5: { q: ['Zero posting errors; entries accurate'], e: ['100% posted'], t: ['Same working day'] },
                    4: { q: ['Minor corrections only'], e: ['100% posted'], t: ['Same working day'] },
                    3: { q: ['Few correctable errors'], e: ['95â€“99% posted'], t: ['By end of day'] },
                    2: { q: ['Multiple errors requiring rework'], e: ['<95% posted'], t: ['Next day'] },
                    1: { q: ['Major inaccuracies'], e: ['Major backlog'], t: ['Unacceptable delay'] },
                },
                'Daily totals reconciled against validated ORs': {
                    5: { q: ['Reconciled with zero variance'], e: ['All ORs included'], t: ['Same day'] },
                    4: { q: ['Minor variance resolved'], e: ['All ORs included'], t: ['Same day'] },
                    3: { q: ['Some variances corrected'], e: ['95â€“99% ORs included'], t: ['Within 24 hours'] },
                    2: { q: ['Frequent variances'], e: ['<95% ORs included'], t: ['Beyond 24 hours'] },
                    1: { q: ['Not reconciled'], e: ['Majority missing'], t: ['Unacceptable'] },
                },
                'Posting errors corrected within 24 hours': {
                    5: { q: ['All corrections documented'], e: ['100% corrected'], t: ['Within 24 hours'] },
                    4: { q: ['Minor corrections documented'], e: ['100% corrected'], t: ['Within 24 hours'] },
                    3: { q: ['Some corrections delayed'], e: ['95â€“99% corrected'], t: ['Within 48 hours'] },
                    2: { q: ['Many corrections delayed'], e: ['<95% corrected'], t: ['Beyond 48 hours'] },
                    1: { q: ['Corrections not done'], e: ['Majority pending'], t: ['Unacceptable'] },
                },
                'Monthly revenue report prepared with complete schedules': {
                    5: { q: ['Complete schedules, no gaps'], e: ['All sections included'], t: ['Within 3 working days'] },
                    4: { q: ['Minor schedule tweaks'], e: ['All sections included'], t: ['Within 3 working days'] },
                    3: { q: ['Some missing items fixed'], e: ['95â€“99% complete'], t: ['Within 5 working days'] },
                    2: { q: ['Many missing schedules'], e: ['<95% complete'], t: ['Beyond 5 working days'] },
                    1: { q: ['Report incomplete'], e: ['Majority missing'], t: ['Unacceptable'] },
                },
                'Report figures match the ledger and subsidiary records': {
                    5: { q: ['Exact match, no variance'], e: ['All reconciled'], t: ['Before submission'] },
                    4: { q: ['Minor variance resolved'], e: ['All reconciled'], t: ['Before submission'] },
                    3: { q: ['Few variances corrected'], e: ['95â€“99% reconciled'], t: ['At submission'] },
                    2: { q: ['Frequent variances'], e: ['<95% reconciled'], t: ['After submission'] },
                    1: { q: ['Not reconciled'], e: ['Majority not reconciled'], t: ['Unacceptable'] },
                },
                'Report submitted on or before deadline': {
                    5: { q: ['Submission complete'], e: ['All attachments included'], t: ['On/before deadline'] },
                    4: { q: ['Minor attachment fixes'], e: ['All included'], t: ['On/before deadline'] },
                    3: { q: ['Late minor attachment'], e: ['95â€“99% included'], t: ['1 day late'] },
                    2: { q: ['Several missing attachments'], e: ['<95% included'], t: ['2â€“3 days late'] },
                    1: { q: ['Not submitted/very late'], e: ['Majority missing'], t: ['Unacceptable'] },
                },
                'Audit request documents compiled complete and accurate': {
                    5: { q: ['Complete packet, error-free'], e: ['All requested docs included'], t: ['Within 2 working days'] },
                    4: { q: ['Minor formatting fixes'], e: ['All included'], t: ['Within 2 working days'] },
                    3: { q: ['Some missing docs recovered'], e: ['95â€“99% included'], t: ['Within 3 working days'] },
                    2: { q: ['Many missing docs'], e: ['<95% included'], t: ['Beyond 3 working days'] },
                    1: { q: ['Packet incomplete'], e: ['Major gaps'], t: ['Unacceptable'] },
                },
                'Verification responses issued within 2 working days': {
                    5: { q: ['Clear, accurate response'], e: ['All queries answered'], t: ['Within 2 working days'] },
                    4: { q: ['Minor clarifications'], e: ['All answered'], t: ['Within 2 working days'] },
                    3: { q: ['Some clarifications needed'], e: ['95â€“99% answered'], t: ['Within 3 working days'] },
                    2: { q: ['Many clarifications needed'], e: ['<95% answered'], t: ['Beyond 3 working days'] },
                    1: { q: ['Responses inadequate'], e: ['Majority unanswered'], t: ['Unacceptable'] },
                },
                'Follow-up clarifications resolved within 3 working days': {
                    5: { q: ['Resolved fully with evidence'], e: ['All follow-ups closed'], t: ['Within 3 working days'] },
                    4: { q: ['Minor evidence follow-up'], e: ['All closed'], t: ['Within 3 working days'] },
                    3: { q: ['Some follow-ups delayed'], e: ['95â€“99% closed'], t: ['Within 5 working days'] },
                    2: { q: ['Many follow-ups delayed'], e: ['<95% closed'], t: ['Beyond 5 working days'] },
                    1: { q: ['Follow-ups not closed'], e: ['Majority open'], t: ['Unacceptable'] },
                },
            };
            function isDraftLikePreviewStatus() {
                const normalized = String(currentPreviewUwp?.status || selectedUwp?.status || '').toLowerCase();
                return normalized === 'draft' || normalized === 'returned';
            }

            function isLegacyPreviewSeedPayload() {
                const createdAt = currentPreviewUwp?.created_at;
                const updatedAt = currentPreviewUwp?.updated_at;
                if (!createdAt || !updatedAt) return true;

                const created = new Date(createdAt);
                const updated = new Date(updatedAt);
                if (Number.isNaN(created.getTime()) || Number.isNaN(updated.getTime())) return true;

                return Math.abs(updated.getTime() - created.getTime()) < 1000;
            }

            function buildPreviewStandardsByRating(standards) {
                const standardsByRating = {};
                standards.forEach((standard) => {
                    const rating = String(standard?.rating ?? '');
                    const dimension = String(standard?.dimension ?? '').toLowerCase();
                    const text = String(standard?.standard_text ?? '').trim();
                    if (!text) return;
                    if (!standardsByRating[rating]) standardsByRating[rating] = { q: [], e: [], t: [] };
                    if (dimension === 'quality' || dimension === 'q') standardsByRating[rating].q.push(text);
                    if (dimension === 'efficiency' || dimension === 'e') standardsByRating[rating].e.push(text);
                    if (dimension === 'timeliness' || dimension === 't') standardsByRating[rating].t.push(text);
                });
                return standardsByRating;
            }

            function normalizePreviewStandardText(value) {
                return String(value || '')
                    .replace(/â€“|â€”|–|—/g, '-')
                    .replace(/\s+/g, ' ')
                    .trim()
                    .toLowerCase();
            }

            function previewStandardsMatchSeed(indicatorText, standardsByRating) {
                const seed = previewSeedStandardsMap[String(indicatorText || '').trim()];
                if (!seed) return false;

                for (const rating of [5, 4, 3, 2, 1]) {
                    const actualRow = standardsByRating[String(rating)] || { q: [], e: [], t: [] };
                    const seedRow = seed[rating] || { q: [], e: [], t: [] };
                    for (const dimension of ['q', 'e', 't']) {
                        const actual = JSON.stringify(
                            (Array.isArray(actualRow[dimension]) ? actualRow[dimension] : []).map(normalizePreviewStandardText)
                        );
                        const expected = JSON.stringify(
                            (Array.isArray(seedRow[dimension]) ? seedRow[dimension] : []).map(normalizePreviewStandardText)
                        );
                        if (actual !== expected) {
                            return false;
                        }
                    }
                }

                return true;
            }

            function setPreviewTab(tabName) {
                activePreviewTab = tabName || 'overview';
                document.querySelectorAll('[data-supervisor-preview-tab]').forEach((button) => {
                    const active = button.getAttribute('data-supervisor-preview-tab') === activePreviewTab;
                    button.classList.toggle('border-blue-400', active);
                    button.classList.toggle('text-white', active);
                    button.classList.toggle('font-semibold', active);
                    button.classList.toggle('border-transparent', !active);
                    button.classList.toggle('text-slate-400', !active);
                    button.classList.toggle('font-medium', !active);
                });

                document.querySelectorAll('[data-supervisor-preview-panel]').forEach((panel) => {
                    panel.classList.toggle('hidden', panel.getAttribute('data-supervisor-preview-panel') !== activePreviewTab);
                });
            }

            function setPreviewFunctionTab(tabName) {
                activePreviewFunctionTab = tabName || 'all';
                document.querySelectorAll('[data-preview-function-tab]').forEach((button) => {
                    const active = button.getAttribute('data-preview-function-tab') === activePreviewFunctionTab;
                    button.classList.toggle('border-blue-400', active);
                    button.classList.toggle('text-white', active);
                    button.classList.toggle('border-transparent', !active);
                    button.classList.toggle('text-slate-400', !active);
                });
                renderPreviewOutputList();
            }

            function renderPreviewStandards() {
                const indicator = getSelectedPreviewIndicator();
                const tbody = document.getElementById('workspaceIndicatorStandardsBody');
                const label = document.getElementById('workspaceModalStandardsIndicator');
                if (!tbody || !label) return;

                label.textContent = indicator?.indicator_text || 'No success indicator selected';
                tbody.innerHTML = '';

                const standards = Array.isArray(indicator?.qet_standards) ? indicator.qet_standards : [];
                if (!standards.length) {
                    tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-slate-400">No Q/E/T standards defined for this indicator.</td></tr>';
                    return;
                }

                const standardsByRating = buildPreviewStandardsByRating(standards);
                [5, 4, 3, 2, 1].forEach((rating) => {
                    const row = standardsByRating[String(rating)] || { q: [], e: [], t: [] };
                    const makeCell = (items) => {
                        if (!items.length) return '<span class="text-slate-400">-</span>';
                        return `<ul class="list-disc space-y-1 pl-4 text-slate-200">${items.map((item) => `<li>${item}</li>`).join('')}</ul>`;
                    };
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-900/40';
                    tr.innerHTML = `
                        <td class="px-4 py-3 font-semibold text-white">${rating}</td>
                        <td class="px-4 py-3 align-top">${makeCell(row.q)}</td>
                        <td class="px-4 py-3 align-top">${makeCell(row.e)}</td>
                        <td class="px-4 py-3 align-top">${makeCell(row.t)}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            function renderPreviewAssignees() {
                const indicator = getSelectedPreviewIndicator();
                const tbody = document.getElementById('workspaceAssignmentsBody');
                const label = document.getElementById('workspaceModalAssigneesIndicator');
                if (!tbody || !label) return;

                label.textContent = indicator?.indicator_text || 'No success indicator selected';
                tbody.innerHTML = '';

                const assignments = Array.isArray(indicator?.assignments) ? indicator.assignments : [];
                if (!assignments.length) {
                    tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-slate-400">No employees assigned to this indicator.</td></tr>';
                    return;
                }

                assignments.forEach((assignment) => {
                    const employee = assignment?.employee || {};
                    const assignedDate = assignment?.assigned_at
                        ? new Date(assignment.assigned_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
                        : 'N/A';
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-900/40';
                    tr.innerHTML = `
                        <td class="px-4 py-3 text-slate-100">${employee?.name || 'Unknown Employee'}</td>
                        <td class="px-4 py-3 text-slate-300">${employee?.office?.name || 'N/A'}</td>
                        <td class="px-4 py-3 text-slate-300">${assignedDate}</td>
                        <td class="px-4 py-3"><span class="inline-flex rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Assigned</span></td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            function renderPreviewIndicators() {
                const tbody = document.getElementById('workspaceModalIndicatorsBody');
                const overview = document.getElementById('workspaceModalOverviewIndicators');
                const output = getSelectedPreviewOutput();
                if (!tbody || !overview) return;

                tbody.innerHTML = '';
                overview.innerHTML = '';

                const indicators = Array.isArray(output?.success_indicators) ? output.success_indicators : [];
                if (!indicators.length) {
                    tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-slate-400">No success indicators found for this output.</td></tr>';
                    overview.innerHTML = '<p class="text-sm text-slate-400">No linked success indicators.</p>';
                    return;
                }

                indicators.forEach((indicator, index) => {
                    const isSelected = index === selectedPreviewIndicatorIndex;
                    const standardsCount = Array.isArray(indicator?.qet_standards) ? indicator.qet_standards.length : 0;
                    const assignmentCount = Array.isArray(indicator?.assignments) ? indicator.assignments.length : 0;

                    const tr = document.createElement('tr');
                    tr.className = isSelected ? 'bg-slate-900/40' : 'hover:bg-slate-900/20';
                    tr.innerHTML = `
                        <td class="px-4 py-3 align-top text-slate-100">${indicator?.indicator_text || '-'}</td>
                        <td class="px-4 py-3 align-top text-slate-300">${getIndicatorTargetSummary(indicator)}</td>
                        <td class="px-4 py-3 text-center"><button type="button" data-preview-indicator-index="${index}" data-target-tab="standards" class="text-blue-300 hover:text-blue-200">View</button></td>
                        <td class="px-4 py-3 text-center"><button type="button" data-preview-indicator-index="${index}" data-target-tab="assignees" class="text-blue-300 hover:text-blue-200">(${assignmentCount})</button></td>
                    `;
                    tbody.appendChild(tr);

                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = `flex w-full items-start justify-between rounded-xl border px-4 py-3 text-left transition ${isSelected ? 'border-blue-500/30 bg-blue-500/10' : 'border-slate-800 bg-slate-950/50 hover:bg-slate-900/60'}`;
                    item.innerHTML = `
                        <span class="pr-4 text-sm text-slate-100">${indicator?.indicator_text || '-'}</span>
                    `;
                    item.addEventListener('click', () => {
                        selectedPreviewIndicatorIndex = index;
                        setPreviewTab('indicators');
                        renderPreviewDetail();
                    });
                    overview.appendChild(item);
                });

                tbody.querySelectorAll('[data-preview-indicator-index]').forEach((button) => {
                    button.addEventListener('click', () => {
                        selectedPreviewIndicatorIndex = Number(button.getAttribute('data-preview-indicator-index') || 0);
                        setPreviewTab(button.getAttribute('data-target-tab') || 'indicators');
                        renderPreviewDetail();
                    });
                });
            }

            function renderPreviewOutputList() {
                const container = document.getElementById('workspaceModalOutputList');
                const countInline = document.getElementById('workspaceModalOutputCountInline');
                const countBadge = document.getElementById('workspaceModalOutputCountBadge');
                if (!container || !countInline || !countBadge) return;

                const outputs = getPreviewOutputs();
                
                let filteredOutputs = outputs;
                if (activePreviewFunctionTab !== 'all') {
                    filteredOutputs = outputs.filter(o => {
                        const ft = String(o.function_type || '').toLowerCase();
                        return ft.includes(activePreviewFunctionTab);
                    });
                }

                countInline.textContent = `${filteredOutputs.length} output${filteredOutputs.length === 1 ? '' : 's'}`;
                countBadge.textContent = String(filteredOutputs.length);
                container.innerHTML = '';

                if (filteredOutputs.length === 0) {
                    container.innerHTML = '<p class="p-4 text-center text-sm text-slate-500">No outputs found.</p>';
                    return;
                }

                filteredOutputs.forEach((output) => {
                    const index = outputs.indexOf(output);
                    const active = index === selectedPreviewOutputIndex;
                    const indicatorCount = Array.isArray(output?.success_indicators) ? output.success_indicators.length : 0;
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = `block w-full rounded-xl border px-3 py-3 text-left transition ${active ? 'border-blue-400/60 bg-blue-500/10 shadow-[inset_0_0_0_1px_rgba(96,165,250,0.18)]' : 'border-slate-800 bg-slate-950/30 hover:bg-slate-900/50'}`;
                    button.innerHTML = `
                        <div class="line-clamp-2 text-base font-semibold leading-snug text-white">${output.title || '-'}</div>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="text-xs text-slate-500">${indicatorCount} indicator${indicatorCount === 1 ? '' : 's'}</span>
                        </div>
                    `;
                    button.addEventListener('click', () => {
                        selectedPreviewOutputIndex = index;
                        selectedPreviewIndicatorIndex = 0;
                        renderPreviewModal();
                    });
                    container.appendChild(button);
                });
            }

            function renderPreviewDetail() {
                const output = getSelectedPreviewOutput();
                const title = document.getElementById('workspaceModalDetailTitle');
                const functionBadge = document.getElementById('workspaceModalDetailFunction');
                const functionCopy = document.getElementById('workspaceModalFunctionCopy');
                const weight = document.getElementById('workspaceModalDetailWeight');
                const weightCopy = document.getElementById('workspaceModalWeightCopy');
                const target = document.getElementById('workspaceModalTargetSummary');
                if (!title || !functionBadge || !functionCopy || !weight || !weightCopy || !target) return;

                title.textContent = output?.title || 'No output selected';
                const type = String(output?.function_type || '').toLowerCase();
                if (type) {
                    functionBadge.classList.remove('hidden');
                    functionBadge.className = `rounded-md border px-2 py-1 text-xs font-medium ${type === 'core' ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : 'border-blue-400/30 bg-blue-500/10 text-blue-300'}`;
                    functionBadge.textContent = labelStatus(type);
                    functionCopy.innerHTML = buildFunctionBadge(type);
                } else {
                    functionBadge.classList.add('hidden');
                    functionCopy.textContent = '-';
                }

                const weightText = output && output.weight_percent !== '' ? `${output.weight_percent}%` : '-';
                weight.textContent = weightText;
                weightCopy.textContent = weightText;
                target.textContent = output?.target_summary || '-';

                renderPreviewIndicators();
                renderPreviewStandards();
                renderPreviewAssignees();
            }

            function renderPreviewModal() {
                renderPreviewOutputList();
                renderPreviewDetail();
                setPreviewTab(activePreviewTab);
            }

            function showUwpPreview(uwpId, trigger = null) {
                currentUwpId = uwpId;
                selectedUwp = null;
                currentPreviewUwp = null;
                if (trigger) {
                    try {
                        selectedUwp = JSON.parse(trigger.getAttribute('data-uwp') || 'null');
                    } catch (error) {
                        selectedUwp = null;
                    }
                }
                updateExportLink(uwpId);

                const workspaceModal = document.getElementById('uwpWorkspacePreviewModal');
                if (workspaceModal) {
                    workspaceModal.classList.remove('hidden');
                    workspaceModal.classList.add('flex');
                }

                resetSubmitButton();

                document.getElementById('workspaceModalUwpSubtitle').textContent = 'Loading...';
                document.getElementById('workspaceModalOfficeUnitInline').textContent = 'Loading...';
                document.getElementById('workspaceModalPeriodInline').textContent = 'Loading...';
                document.getElementById('workspaceModalSupervisorInline').textContent = 'Loading...';
                document.getElementById('workspaceModalStatus').textContent = 'LOADING';
                document.getElementById('workspaceModalOutputCountInline').textContent = '0 outputs';
                document.getElementById('workspaceModalOutputCountBadge').textContent = '0';
                document.getElementById('workspaceModalOutputList').innerHTML = '<div class="rounded-xl border border-slate-800 bg-slate-950/30 px-4 py-6 text-center text-slate-400">Loading UWP data...</div>';
                document.getElementById('workspaceModalDetailTitle').textContent = 'Loading output...';
                document.getElementById('workspaceModalDetailWeight').textContent = '';
                document.getElementById('workspaceModalTargetSummary').textContent = '-';
                document.getElementById('workspaceModalFunctionCopy').textContent = '-';
                document.getElementById('workspaceModalWeightCopy').textContent = '-';
                document.getElementById('workspaceModalOverviewIndicators').innerHTML = '';
                document.getElementById('workspaceModalIndicatorsBody').innerHTML = '';
                document.getElementById('workspaceIndicatorStandardsBody').innerHTML = '';
                document.getElementById('workspaceAssignmentsBody').innerHTML = '';
                const remarksWrap = document.getElementById('workspace-uwp-return-remarks-wrap');
                const remarksText = document.getElementById('workspace-uwp-return-remarks-text');
                const remarksMeta = document.getElementById('workspace-uwp-return-remarks-meta');
                selectedPreviewOutputIndex = 0;
                selectedPreviewIndicatorIndex = 0;
                activePreviewTab = 'overview';
                setPreviewTab('overview');
                if (remarksWrap) remarksWrap.classList.add('hidden');
                if (remarksText) remarksText.textContent = '—';
                if (remarksMeta) remarksMeta.textContent = '';

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
                        document.getElementById('workspaceModalOutputList').innerHTML =
                            `<div class="rounded-xl border border-red-500/20 bg-red-500/5 px-4 py-6 text-center text-red-400">
                                Error loading UWP data. Please try again.<br>
                                (${error.message})
                            </div>`;
                    });
            }

            function populateUwpModal(uwpData) {
                currentPreviewUwp = uwpData || null;
                updateExportLink(uwpData?.id || currentUwpId);

                document.getElementById('workspaceModalOfficeUnitInline').textContent = uwpData.office?.name || 'N/A';
                document.getElementById('workspaceModalPeriodInline').textContent = uwpData.performance_period?.name || 'Performance Period';
                document.getElementById('workspaceModalUwpSubtitle').textContent =
                    `${uwpData.office?.name || 'Unit'} • ${uwpData.performance_period?.name || 'Performance Period'}`;

                document.getElementById('workspaceModalSupervisorInline').textContent = uwpData.creator?.name || 'Not Assigned';

                const statusBadge = document.getElementById('workspaceModalStatus');
                const status = uwpData.status || selectedUwp?.status || 'draft';
                const normalizedStatus = String(status).toLowerCase();
                const isLocked = !!uwpData.locked_at;
                statusBadge.textContent = labelStatus(status);
                statusBadge.className = `ml-1 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold ${previewStatusClass(normalizedStatus)}`;

                const submitButton = document.getElementById('workspaceSubmitUwpTrigger');
                if (submitButton) {
                    if ((normalizedStatus === 'draft' || normalizedStatus === 'returned') && !isLocked) {
                        submitButton.classList.remove('hidden');
                        submitButton.disabled = false;
                        const label = submitButton.querySelector('span');
                        if (label) label.textContent = 'Submit for Approval';
                    } else {
                        submitButton.classList.add('hidden');
                    }
                }

                const wrap = document.getElementById('workspace-uwp-return-remarks-wrap');
                const txt = document.getElementById('workspace-uwp-return-remarks-text');
                const meta = document.getElementById('workspace-uwp-return-remarks-meta');
                const remarks = String(uwpData.return_remarks ?? selectedUwp?.return_remarks ?? '').trim();
                const returnedAt = String(uwpData.returned_at ?? selectedUwp?.returned_at ?? '').trim();

                if (wrap && txt && meta) {
                    if (normalizedStatus === 'returned' && remarks) {
                        wrap.classList.remove('hidden');
                        txt.textContent = remarks;
                        meta.textContent = returnedAt ? ('Returned at: ' + returnedAt) : '';
                    } else {
                        wrap.classList.add('hidden');
                        txt.textContent = '—';
                        meta.textContent = '';
                    }
                }

                selectedPreviewOutputIndex = 0;
                selectedPreviewIndicatorIndex = 0;
                activePreviewTab = 'overview';
                renderPreviewModal();
            }

            function resetSubmitButton() {
                const submitButton = document.getElementById('workspaceSubmitUwpTrigger');
                if (submitButton) {
                    submitButton.disabled = false;
                    const label = submitButton.querySelector('span');
                    if (label) label.textContent = 'Submit for Approval';
                }
            }

            function updateListRowAfterSubmit(uwpId) {
                const row = document.querySelector(`[data-uwp-row="${uwpId}"]`);
                if (!row) return;

                const statusBadge = row.querySelector('[data-status-badge]');
                if (statusBadge) {
                    statusBadge.textContent = 'Submitted';
                    statusBadge.className = 'inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold border-blue-500/30 bg-blue-500/10 text-blue-300';
                }

                const deleteBtn = row.querySelector('[data-delete-btn]');
                if (deleteBtn) {
                    deleteBtn.disabled = true;
                    deleteBtn.title = 'Only Draft/Returned & unlocked can be deleted';
                    deleteBtn.className = 'inline-flex cursor-not-allowed items-center justify-center rounded-lg p-2 text-slate-500 opacity-40';
                }

                const previewBtn = row.querySelector('[data-uwp-preview-btn]');
                if (previewBtn) {
                    try {
                        const payload = JSON.parse(previewBtn.getAttribute('data-uwp') || '{}');
                        payload.status = 'submitted';
                        payload.return_remarks = '';
                        payload.returned_at = null;
                        previewBtn.setAttribute('data-uwp', JSON.stringify(payload));
                    } catch (error) {
                        // Keep existing payload if malformed
                    }
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
                if (!items || items.length === 0) return '—';

                let html = '<ul class="list-disc space-y-1 pl-4 text-slate-200">';
                items.forEach(item => {
                    if (item) html += `<li>${item}</li>`;
                });
                html += '</ul>';
                return html;
            }

            function updateExportLink(uwpId) {
                const exportLink = document.getElementById('workspaceModalExportExcelLink') || document.getElementById('modalExportExcelLink');
                if (!exportLink) return;

                const parsedId = Number(uwpId);
                const hasValidId = Number.isFinite(parsedId) && parsedId > 0;

                if (!hasValidId) {
                    exportLink.setAttribute('href', '#');
                    exportLink.setAttribute('aria-disabled', 'true');
                    exportLink.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                    return;
                }

                const baseUrl = String(window.uwpExportBaseUrl || '');
                let exportUrl = baseUrl.replace('__ID__', String(parsedId));
                if (exportUrl === baseUrl) {
                    // Fallback in case placeholder was URL-encoded by route() helper.
                    exportUrl = baseUrl.replace('%5F%5FID%5F%5F', String(parsedId));
                }

                exportLink.setAttribute('href', exportUrl);
                exportLink.removeAttribute('aria-disabled');
                exportLink.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
            }

            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
                if (modalId === 'uwpPreviewModal' || modalId === 'uwpWorkspacePreviewModal') {
                    currentUwpId = null;
                    currentPreviewUwp = null;
                    resetSubmitButton();
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

                document.querySelectorAll('[data-supervisor-preview-tab]').forEach((button) => {
                    button.addEventListener('click', () => {
                        setPreviewTab(button.getAttribute('data-supervisor-preview-tab') || 'overview');
                        renderPreviewDetail();
                    });
                });

                document.querySelectorAll('[data-preview-function-tab]').forEach((button) => {
                    button.addEventListener('click', () => {
                        selectedPreviewOutputIndex = 0;
                        selectedPreviewIndicatorIndex = 0;
                        setPreviewFunctionTab(button.getAttribute('data-preview-function-tab') || 'all');
                        renderPreviewModal();
                    });
                });

                window.addEventListener('click', function(event) {
                    const modals = ['uwpWorkspacePreviewModal', 'uwpPreviewModal', 'successIndicatorsModal', 'indicatorStandardsModal', 'assignmentsModal', 'deleteUwpModal'];
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
                        const modals = ['uwpWorkspacePreviewModal', 'uwpPreviewModal', 'successIndicatorsModal', 'indicatorStandardsModal', 'assignmentsModal', 'deleteUwpModal', 'uwpCreationModal', 'creationStandardEditModal'];
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
                if (sel) document.getElementById('creationModalPeriod').textContent = sel.options[sel.selectedIndex]?.text ?? '—';
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
                    btn.className = `block w-full rounded-xl border px-3 py-3 text-left transition ${active ? 'border-blue-400/60 bg-blue-500/10' : 'border-slate-800 bg-slate-950/30 hover:bg-slate-900/50'}`;
                    btn.innerHTML = `
                        <div class="line-clamp-2 text-sm font-semibold leading-snug text-white">${mfo.title || '<span class="text-slate-500 italic">Untitled MFO</span>'}</div>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-md border px-2 py-0.5 text-xs font-medium ${badgeClass}">${type.charAt(0).toUpperCase() + type.slice(1)}</span>
                            <span class="text-xs text-slate-400">${mfo.weight_percent ?? '—'}%</span>
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
                    ? inds.map(ind => `<button type="button" class="flex w-full items-start justify-between rounded-xl border border-slate-800 bg-slate-950/50 px-4 py-3 text-left hover:bg-slate-900/60 transition"><span class="text-sm text-slate-100">${ind.text || '—'}</span><span class="ml-3 rounded-md bg-slate-900 px-2 py-0.5 text-xs text-slate-400">${ind.targetQuantity ? ind.targetQuantity + ' ' : ''}${ind.targetTimeline || ''}</span></button>`).join('')
                    : '<p class="text-sm text-slate-500">No success indicators yet. Add one from the "Success Indicators" tab.</p>';

                // Indicators table
                const tbody = document.getElementById('creationIndicatorsBody');
                tbody.innerHTML = inds.length
                    ? inds.map((ind, i) => `
                        <tr class="hover:bg-slate-900/30">
                            <td class="px-4 py-3 text-slate-100">${ind.text || '—'}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs">${ind.targetTimeline || '—'}</td>
                            <td class="px-4 py-3 text-center text-slate-300">${ind.targetQuantity ?? '—'}</td>
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
                label.textContent = ind.text || '—';
                if (!ind.standards) ind.standards = {5:{q:'',e:'',t:''},4:{q:'',e:'',t:''},3:{q:'',e:'',t:''},2:{q:'',e:'',t:''},1:{q:'',e:'',t:''}};
                tbody.innerHTML = [5,4,3,2,1].map(r => {
                    const s = ind.standards[r] || {q:'',e:'',t:''};
                    return `<tr class="hover:bg-slate-900/30">
                        <td class="px-4 py-3 font-semibold text-white">${r}</td>
                        <td class="px-4 py-3 text-slate-300 text-xs">${s.q || '<span class="text-slate-600">—</span>'}</td>
                        <td class="px-4 py-3 text-slate-300 text-xs">${s.e || '<span class="text-slate-600">—</span>'}</td>
                        <td class="px-4 py-3 text-slate-300 text-xs">${s.t || '<span class="text-slate-600">—</span>'}</td>
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
                document.getElementById('creationStdEditLabel').textContent = `Rating ${rating} — ${dimLabel}`;
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
                label.textContent = ind.text || '—';
                const assigned = new Set(ind.assignees.map(a => a.id));
                tbody.innerHTML = creationOfficeEmployees.map(emp => {
                    const isAssigned = assigned.has(emp.id);
                    return `<tr class="hover:bg-slate-900/30">
                        <td class="px-4 py-3 text-slate-100">${emp.name}</td>
                        <td class="px-4 py-3 text-slate-400 text-xs">${emp.office || '—'}</td>
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
                if (!confirm('Submit this UWP for approval? You cannot edit it after submission.')) return;
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
