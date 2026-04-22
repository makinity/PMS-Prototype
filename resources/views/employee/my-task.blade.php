@extends('layouts.employee')

@section('main-content')
    <section class="space-y-6">

        <div>
            <h1 class="mt-1 text-2xl font-bold text-white">My Tasks</h1>
        </div>

        <div class="flex flex-wrap gap-3">
            <select id="myTasksStatusFilter"
                class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-blue-500">
                <option value="all" class="bg-gray-700">Status: All</option>
                <option value="draft" class="bg-gray-700">Draft</option>
                <option value="recording" class="bg-gray-700">Recording</option>
                <option value="submitted" class="bg-gray-700">Submitted</option>
                <option value="rated" class="bg-gray-700">Rated</option>
                <option value="missing" class="bg-gray-700">Missing / Overdue</option>
                <option value="returned" class="bg-gray-700">Returned</option>
            </select>

            <input id="myTasksDateFilter" type="date"
                class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-2.5 text-sm text-white focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-700 bg-gray-800">
            <div class="border-b border-gray-700 px-4 py-3 text-xs text-gray-400">
                Status reflects ORS state only; no submissions, uploads, or task creation here.
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-white">Task</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Evidence</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Quantity (ORS)</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody id="myTasksTbody" class="divide-y divide-gray-700"></tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center rounded-lg border border-gray-700 bg-gray-800 p-4 text-sm text-gray-400">
            <svg class="mr-3 inline h-4 w-4 flex-shrink-0 text-blue-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 0 0 1 0 2Z" />
            </svg>
            <span>My Tasks mirrors ORS activity and declared quantity. Tasks are created and submitted in ORS.</span>
        </div>

        <div id="task-view-modal" tabindex="-1" aria-hidden="true"
            class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4 py-6">
            <div class="relative w-full max-w-4xl">
                <div
                    class="relative flex max-h-[85vh] flex-col overflow-hidden rounded-2xl border border-gray-700 bg-gray-900 shadow-lg">
                    <div class="flex items-start justify-between border-b border-gray-700 px-6 py-4">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Task Details</h3>
                            <p class="text-xs text-gray-400">My Tasks mirrors ORS activity (read-only).</p>
                        </div>
                        <button type="button" id="closeTaskViewTop"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-800 hover:text-white">
                            <span class="sr-only">Close modal</span>
                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 12 12M13 1 1 13" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-5 text-sm text-gray-300">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Employee</p>
                                <p class="text-sm font-medium text-white">{{ $employeeName ?? 'Ramon Reyes' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Task / Indicator</p>
                                <p id="mvTask" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">MFO / UWP Output</p>
                                <p id="mvMfo" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Date</p>
                                <p id="mvDate" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                                <p id="mvStatus" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Output State</p>
                                <p id="mvOutputState" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Quantity (ORS)</p>
                                <p id="mvQuantity" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Evidence</p>
                                <div class="flex items-center gap-2">
                                    <p id="mvEvidence" class="text-sm font-medium text-white">--</p>
                                    <button id="mvViewEvidenceBtn"
                                        type="button"
                                        class="hidden inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 text-gray-200 transition hover:bg-slate-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                   c4.477 0 8.268 2.943 9.542 7
                                                   -1.274 4.057-5.065 7-9.542 7
                                                   -4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <p id="mvEvidenceFile" class="mt-1 text-xs text-gray-400">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Submitted At</p>
                                <p id="mvSubmittedAt" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Duration</p>
                                <p id="mvDuration" class="text-sm font-medium text-white">--</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Notes</p>
                                <p id="mvNotes" class="mt-1 text-sm text-gray-300">--</p>
                            </div>
                        </div>

                        <div id="mvSupervisorMonitoringSection"
                            class="mt-6 hidden rounded-2xl border border-gray-700 bg-gray-900/60 p-5 shadow-inner shadow-black/40">
                            <div class="flex items-center justify-between">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor Monitoring</p>
                                <span class="text-[0.65rem] font-semibold uppercase tracking-wider text-gray-500">Stage II</span>
                            </div>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor Name</p>
                                    <p id="mvSupName" class="text-sm font-medium text-white">--</p>
                                </div>
                                <div class="grid gap-4 md:grid-cols-3">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Quality</p>
                                        <span id="mvSupQuality"
                                            class="mt-1 inline-flex items-center justify-center rounded-full border border-gray-700 bg-gray-800 px-3 py-1 text-xs font-semibold text-gray-200">--</span>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Timeliness</p>
                                        <span id="mvSupTimeliness"
                                            class="mt-1 inline-flex items-center justify-center rounded-full border border-gray-700 bg-gray-800 px-3 py-1 text-xs font-semibold text-gray-200">--</span>
                                    </div>
                                    <div class="md:col-span-3">
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Remarks</p>
                                        <div id="mvSupRemarks"
                                            class="mt-1 min-h-[48px] rounded-xl border border-gray-700 bg-gray-800 px-3 py-2 text-sm leading-relaxed text-gray-300">
                                            --
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-700 px-6 py-3">
                        <button type="button" id="closeTaskViewBottom"
                            class="rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-200 transition hover:bg-gray-800">
                            Close
                        </button>
                        <a href="{{ route('employee.ors.export.pdf') }}"
                            id="exportOrsBtn"
                            title="Export available only after supervisor validation"
                            class="rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-200 opacity-50 cursor-not-allowed pointer-events-none">
                                Preview
                        </a>
                        <a href="{{ route('employee.ors') }}"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-500">
                            View in ORS
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="evidence-preview-modal"
            class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4 py-6">
            <div class="flex max-h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl border border-gray-700 bg-gray-900 shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-700 px-5 py-3">
                    <div class="flex items-center gap-3">
                        <div id="evFileIcon"
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-800 text-gray-200">
                        </div>
                        <div>
                            <p id="evFileName" class="text-sm font-semibold text-white">Evidence</p>
                            <p id="evFileMeta" class="text-xs text-gray-400">--</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button id="evZoomOut"
                            class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-1.5 text-xs font-semibold text-gray-200 hover:bg-gray-700">
                            -
                        </button>
                        <button id="evZoomReset"
                            class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-1.5 text-xs font-semibold text-gray-200 hover:bg-gray-700">
                            100%
                        </button>
                        <button id="evZoomIn"
                            class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-1.5 text-xs font-semibold text-gray-200 hover:bg-gray-700">
                            +
                        </button>

                        <a id="evDownloadBtn"
                            class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500"
                            href="#"
                            target="_blank"
                            rel="noopener">
                            Download
                        </a>

                        <button id="closeEvidencePreview"
                            class="ml-2 inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white">
                            x
                        </button>
                    </div>
                </div>

                <div class="grid min-h-0 flex-1 md:grid-cols-[18rem_minmax(0,1fr)]">
                    <div class="overflow-y-auto border-b border-gray-700 bg-gray-900/70 p-4 md:border-b-0 md:border-r">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Evidence Files</p>
                        <div id="evEvidenceList" class="mt-3 space-y-2">
                            <p class="text-sm text-gray-400">No evidence found.</p>
                        </div>
                    </div>

                    <div class="min-h-0 bg-black">
                        <div id="evidencePreviewWrapper"
                            class="relative flex h-[70vh] items-center justify-center overflow-auto bg-black">
                            <img id="evidenceImage"
                                class="hidden max-h-full max-w-full origin-center object-contain transition-transform duration-200 ease-out"
                                alt="Evidence preview" />

                            <iframe id="evidenceIframe"
                                class="hidden h-full w-full border-0 bg-black"
                                allowfullscreen></iframe>

                            <div id="evidenceNoPreview"
                                class="hidden text-center text-slate-400">
                                <p class="text-sm font-medium">Preview not available for this file type.</p>
                                <p class="mt-2 text-xs">Use the Download button to open the document.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const rawTasks = @json($orsEntries ?? []);
                const evidencesEndpointTemplate = @json(route('stage2.my_tasks.evidences', ['orsEntry' => '__ENTRY__']));
                const exportOrsBaseUrl = @json(route('employee.ors.export.pdf'));

                const tasks = Array.isArray(rawTasks)
                    ? rawTasks.map((entry) => {
                        const status = String(entry?.status || 'draft').toLowerCase();
                        const submittedAt = entry?.submitted_at ? new Date(entry.submitted_at) : null;
                        const startedAt = entry?.started_at ? new Date(entry.started_at) : null;
                        const stoppedAt = entry?.stopped_at ? new Date(entry.stopped_at) : null;
                        const evidences = Array.isArray(entry?.evidences) ? entry.evidences : [];
                        const firstEvidence = evidences.length ? evidences[0] : null;
                        const firstEvidenceUploadedAt = firstEvidence?.uploaded_at ? new Date(firstEvidence.uploaded_at) : null;

                        return {
                            id: String(entry?.id ?? ''),
                            title: String(entry?.ipcr_item?.indicator_text || '--'),
                            date: String(entry?.work_date || ''),
                            uwpOutputLabel: String(entry?.ipcr_item?.output_title || '--'),
                            quantity: entry?.quantity || '--',
                            state: status,
                            durationMs: Number(entry?.total_seconds || 0) * 1000,
                            startTime: startedAt && !Number.isNaN(startedAt.getTime()) ? startedAt : null,
                            stoppedAt: stoppedAt && !Number.isNaN(stoppedAt.getTime()) ? stoppedAt : null,
                            output_state: (status === 'submitted' || status === 'rated') ? 'submitted' : 'none',
                            hasEvidence: evidences.length > 0,
                            evidenceCount: evidences.length,
                            evidenceFileName: firstEvidence?.file_name || null,
                            evidenceFilePath: firstEvidence?.file_path || null,
                            evidenceMimeType: firstEvidence?.mime_type || null,
                            evidenceUploadedAt: firstEvidenceUploadedAt && !Number.isNaN(firstEvidenceUploadedAt.getTime())
                                ? firstEvidenceUploadedAt
                                : null,
                            submittedAt: submittedAt && !Number.isNaN(submittedAt.getTime()) ? submittedAt : null,
                            notes: entry?.notes || '--',
                            monitoring: entry?.monitoring ? {
                                supervisor_name: entry.monitoring?.supervisor?.name || '--',
                                quality_rating: entry.monitoring?.quality_rating ?? null,
                                timeliness_rating: entry.monitoring?.timeliness_rating ?? null,
                                remarks: entry.monitoring?.remarks || null,
                                rated_at: entry.monitoring?.rated_at || null,
                            } : null,
                        };
                    })
                    : [];

                const tbody = document.getElementById('myTasksTbody');
                const statusFilter = document.getElementById('myTasksStatusFilter');
                const dateFilter = document.getElementById('myTasksDateFilter');

                const modal = document.getElementById('task-view-modal');
                const closeTopBtn = document.getElementById('closeTaskViewTop');
                const closeBottomBtn = document.getElementById('closeTaskViewBottom');
                const exportOrsBtn = document.getElementById('exportOrsBtn');
                const viewEvidenceBtn = document.getElementById('mvViewEvidenceBtn');

                const evidenceModal = document.getElementById('evidence-preview-modal');
                const evFileIcon = document.getElementById('evFileIcon');
                const evFileName = document.getElementById('evFileName');
                const evFileMeta = document.getElementById('evFileMeta');
                const evEvidenceList = document.getElementById('evEvidenceList');
                const evidenceImage = document.getElementById('evidenceImage');
                const evidenceIframe = document.getElementById('evidenceIframe');
                const evidenceNoPreview = document.getElementById('evidenceNoPreview');
                const evDownloadBtn = document.getElementById('evDownloadBtn');
                const evZoomIn = document.getElementById('evZoomIn');
                const evZoomOut = document.getElementById('evZoomOut');
                const evZoomReset = document.getElementById('evZoomReset');
                const closeEvidencePreview = document.getElementById('closeEvidencePreview');

                let currentZoom = 1;
                let currentEvidenceUrl = null;
                let currentEvidenceDownloadUrl = null;
                let selectedEvidenceId = null;

                const fields = {
                    task: document.getElementById('mvTask'),
                    mfo: document.getElementById('mvMfo'),
                    date: document.getElementById('mvDate'),
                    status: document.getElementById('mvStatus'),
                    outputState: document.getElementById('mvOutputState'),
                    quantity: document.getElementById('mvQuantity'),
                    evidence: document.getElementById('mvEvidence'),
                    evidenceFile: document.getElementById('mvEvidenceFile'),
                    submittedAt: document.getElementById('mvSubmittedAt'),
                    duration: document.getElementById('mvDuration'),
                    notes: document.getElementById('mvNotes'),
                    supervisorSection: document.getElementById('mvSupervisorMonitoringSection'),
                    supName: document.getElementById('mvSupName'),
                    supQuality: document.getElementById('mvSupQuality'),
                    supTimeliness: document.getElementById('mvSupTimeliness'),
                    supRemarks: document.getElementById('mvSupRemarks'),
                };

                function formatDateHuman(dateStr) {
                    if (!dateStr) return '--';
                    const date = new Date(`${dateStr}T00:00:00`);
                    if (Number.isNaN(date.getTime())) return '--';
                    return date.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
                }

                function formatDateTime(value) {
                    if (!value) return '--';
                    const date = value instanceof Date ? value : new Date(value);
                    if (Number.isNaN(date.getTime())) return '--';
                    return date.toLocaleString([], {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                    });
                }

                function formatFileSize(bytes) {
                    const value = Number(bytes || 0);
                    if (!Number.isFinite(value) || value <= 0) return '--';
                    if (value < 1024) return `${Math.round(value)} B`;
                    if (value < 1024 * 1024) return `${(value / 1024).toFixed(1)} KB`;
                    return `${(value / (1024 * 1024)).toFixed(1)} MB`;
                }

                function updateBodyScrollLock() {
                    const taskModalOpen = modal && !modal.classList.contains('hidden');
                    const previewModalOpen = evidenceModal && !evidenceModal.classList.contains('hidden');
                    document.body.classList.toggle('overflow-hidden', Boolean(taskModalOpen || previewModalOpen));
                }

                function formatDuration(ms) {
                    const totalSeconds = Math.max(0, Math.floor((ms || 0) / 1000));
                    const hours = Math.floor(totalSeconds / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    return `${hours}h ${minutes}m`;
                }

                function evidencesEndpoint(entryId) {
                    return evidencesEndpointTemplate.replace('__ENTRY__', encodeURIComponent(String(entryId || '')));
                }

                function fileTypeInfo(mimeType, fileName) {
                    const mime = String(mimeType || '').toLowerCase();
                    const name = String(fileName || '').toLowerCase();
                    const ext = name.includes('.') ? name.split('.').pop() : '';

                    const isPdf = mime === 'application/pdf' || ext === 'pdf';
                    const isImage = mime.startsWith('image/') || ['jpg', 'jpeg', 'png'].includes(ext);
                    const isDoc = ['doc', 'docx'].includes(ext)
                        || mime === 'application/msword'
                        || mime === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    const isXls = ext === 'xlsx'
                        || mime === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

                    if (isPdf) return { type: 'pdf', label: 'PDF', previewable: true };
                    if (isImage) return { type: 'image', label: 'Image', previewable: true };
                    if (isDoc) return { type: 'doc', label: 'Document', previewable: false };
                    if (isXls) return { type: 'xls', label: 'Spreadsheet', previewable: false };

                    return { type: 'doc', label: 'Document', previewable: false };
                }

                function setEvIcon(type) {
                    if (!evFileIcon) return;
                    if (type === 'pdf') {
                        evFileIcon.textContent = 'PDF';
                        return;
                    }
                    if (type === 'image') {
                        evFileIcon.textContent = 'IMG';
                        return;
                    }
                    if (type === 'xls') {
                        evFileIcon.textContent = 'XLS';
                        return;
                    }
                    evFileIcon.textContent = 'DOC';
                }

                function applyZoom() {
                    if (evZoomReset) evZoomReset.textContent = `${Math.round(currentZoom * 100)}%`;
                    if (!evidenceImage || evidenceImage.classList.contains('hidden')) return;
                    evidenceImage.style.transform = `scale(${currentZoom})`;
                }

                function setZoomControlsEnabled(previewable) {
                    const zoomButtons = [evZoomIn, evZoomOut, evZoomReset];
                    zoomButtons.forEach((btn) => {
                        if (!btn) return;
                        btn.classList.toggle('hidden', !previewable);
                        btn.disabled = !previewable;
                    });
                }

                function statusLabel(state) {
                    switch (state) {
                        case 'submitted':
                            return 'Submitted (Locked)';
                        case 'rated':
                            return 'Rated (Locked)';
                        case 'draft':
                            return 'Draft (Stopped)';
                        case 'recording':
                            return 'Recording';
                        case 'paused':
                            return 'Paused';
                        default:
                            return '--';
                    }
                }

                function outputStateLabel(task) {
                    if (task.state === 'missing') return 'Output Missing';
                    if (task.output_state === 'submitted') return 'Output submitted';
                    if (task.output_state === 'validated') return 'Output validated';
                    return 'Output pending';
                }

                function statusChipClasses(state) {
                    switch (state) {
                        case 'submitted':
                            return 'bg-blue-900 text-blue-300';
                        case 'rated':
                            return 'bg-cyan-900 text-cyan-300';
                        case 'recording':
                        case 'paused':
                            return 'bg-amber-900 text-amber-300';
                        case 'draft':
                            return 'bg-slate-700 text-slate-200';
                        default:
                            return 'bg-slate-700 text-slate-200';
                    }
                }

                function normalizeStatusFilterValue(value) {
                    if (!value || value === 'all') return null;
                    if (value === 'returned') return 'returned';
                    return value;
                }

                function matchesFilters(task) {
                    const statusValue = normalizeStatusFilterValue(statusFilter ? statusFilter.value : 'all');
                    if (statusValue && task.state !== statusValue) return false;

                    const dateValue = dateFilter ? dateFilter.value : '';
                    if (dateValue && task.date !== dateValue) return false;

                    return true;
                }

                function quantityLabel(value) {
                    if (!value || !String(value).trim() || value === '--') return '--';
                    return value;
                }

                function computeElapsed(task) {
                    const base = task.durationMs || 0;
                    if ((task.state === 'recording' || task.state === 'paused') && task.startTime) {
                        const start = task.startTime instanceof Date ? task.startTime : new Date(task.startTime);
                        if (!Number.isNaN(start.getTime())) {
                            return base + (Date.now() - start.getTime());
                        }
                    }
                    return base;
                }

                function renderRows() {
                    if (!tbody) return;

                    const filteredTasks = tasks.filter(matchesFilters);

                    if (!filteredTasks.length) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">
                                    No tasks found for the selected filters.
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    tbody.innerHTML = filteredTasks.map((task) => `
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">${task.title || '--'}</td>
                            <td class="px-4 py-3 text-gray-300">${formatDateHuman(task.date)}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${statusChipClasses(task.state)}">
                                    ${statusLabel(task.state)}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${task.hasEvidence ? 'bg-emerald-900 text-emerald-300' : 'bg-slate-700 text-slate-200'}">
                                    ${task.hasEvidence ? 'Attached' : 'None'}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">${quantityLabel(task.quantity)}</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                    data-task-id="${task.id}"
                                    class="rounded-lg border border-gray-600 px-3 py-1.5 text-xs font-semibold text-gray-200 hover:bg-gray-800">
                                    View
                                </button>
                            </td>
                        </tr>
                    `).join('');
                }

                function resetSupervisorMonitoring() {
                    if (fields.supervisorSection) {
                        fields.supervisorSection.classList.add('hidden');
                    }
                    if (fields.supName) fields.supName.textContent = '--';
                    if (fields.supQuality) fields.supQuality.textContent = '--';
                    if (fields.supTimeliness) fields.supTimeliness.textContent = '--';
                    if (fields.supRemarks) fields.supRemarks.textContent = '--';
                }

                function setExportButton(task) {
                    if (!exportOrsBtn) return;

                    const canExport = Boolean(
                        task &&
                        task.id &&
                        (
                            String(task.state || '').toLowerCase() === 'rated' ||
                            task?.monitoring?.rated_at
                        )
                    );

                    if (!canExport) {
                        exportOrsBtn.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none', 'border-gray-600', 'text-gray-200');
                        exportOrsBtn.classList.remove('border-emerald-600', 'bg-emerald-600', 'text-white', 'hover:bg-emerald-500');
                        exportOrsBtn.href = '#';
                        exportOrsBtn.title = 'Export available only after supervisor validation';
                        exportOrsBtn.removeAttribute('target');
                        exportOrsBtn.removeAttribute('rel');
                        return;
                    }

                    exportOrsBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none', 'border-gray-600', 'text-gray-200');
                    exportOrsBtn.classList.add('border-emerald-600', 'bg-emerald-600', 'text-white', 'hover:bg-emerald-500');
                    exportOrsBtn.href = `${exportOrsBaseUrl}?ors_entry_id=${encodeURIComponent(task.id)}`;
                    exportOrsBtn.title = 'Export ORS (PDF)';
                    exportOrsBtn.setAttribute('target', '_blank');
                    exportOrsBtn.setAttribute('rel', 'noopener');
                }

                function openTaskViewModal(taskId) {
                    const task = tasks.find((item) => item.id === taskId);
                    if (!task || !modal) return;

                    fields.task.textContent = task.title || '--';
                    fields.mfo.textContent = task.uwpOutputLabel || '--';
                    fields.date.textContent = formatDateHuman(task.date);
                    fields.status.textContent = statusLabel(task.state);
                    fields.outputState.textContent = outputStateLabel(task);
                    fields.quantity.textContent = quantityLabel(task.quantity);

                    if (task.hasEvidence) {
                        fields.evidence.textContent = 'Attached';
                        fields.evidenceFile.textContent = task.evidenceCount > 1
                            ? `${task.evidenceCount} files attached`
                            : (task.evidenceFileName || '--');
                        if (viewEvidenceBtn) {
                            viewEvidenceBtn.classList.remove('hidden');
                            viewEvidenceBtn.dataset.entryId = task.id;
                            viewEvidenceBtn.onclick = () => openEvidencePreview(task);
                        }
                    } else {
                        fields.evidence.textContent = 'None';
                        fields.evidenceFile.textContent = '--';
                        if (viewEvidenceBtn) {
                            viewEvidenceBtn.classList.add('hidden');
                            delete viewEvidenceBtn.dataset.entryId;
                            viewEvidenceBtn.onclick = null;
                        }
                    }

                    fields.submittedAt.textContent = formatDateTime(task.submittedAt);
                    fields.duration.textContent = task.durationMs || task.startTime ? formatDuration(computeElapsed(task)) : '--';
                    fields.notes.textContent = task.notes || '--';
                    resetSupervisorMonitoring();
                    const mon = task.monitoring;
                    if (mon && (mon.quality_rating || mon.timeliness_rating || mon.remarks || mon.rated_at)) {
                        if (fields.supervisorSection) fields.supervisorSection.classList.remove('hidden');
                        if (fields.supName) fields.supName.textContent = mon.supervisor_name || '--';
                        if (fields.supQuality) fields.supQuality.textContent = mon.quality_rating ? String(mon.quality_rating) : '--';
                        if (fields.supTimeliness) fields.supTimeliness.textContent = mon.timeliness_rating ? String(mon.timeliness_rating) : '--';
                        if (fields.supRemarks) fields.supRemarks.textContent = (mon.remarks && String(mon.remarks).trim()) ? mon.remarks : '--';
                    }
                    setExportButton(task);

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    updateBodyScrollLock();
                }

                function closeTaskViewModal() {
                    if (!modal) return;
                    if (evidenceModal && !evidenceModal.classList.contains('hidden')) {
                        closeEvidenceModal();
                    }
                    resetSupervisorMonitoring();
                    if (fields.mfo) fields.mfo.textContent = '--';
                    if (viewEvidenceBtn) {
                        viewEvidenceBtn.classList.add('hidden');
                        delete viewEvidenceBtn.dataset.entryId;
                        viewEvidenceBtn.onclick = null;
                    }
                    setExportButton(null);
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    updateBodyScrollLock();
                }

                function setEvidenceListMessage(message) {
                    if (!evEvidenceList) return;
                    evEvidenceList.innerHTML = `<p class="text-sm text-gray-400">${message}</p>`;
                }

                function selectEvidence(evidence, allEvidences = []) {
                    if (!evidence) return;

                    selectedEvidenceId = String(evidence.id || '');
                    currentEvidenceUrl = evidence.view_url || null;
                    currentEvidenceDownloadUrl = evidence.download_url || null;

                    const info = fileTypeInfo(evidence.mime_type, evidence.file_name);
                    const mime = String(evidence.mime_type || '').toLowerCase();
                    const isImagePreview = Boolean(currentEvidenceUrl) && (mime.startsWith('image/') || info.type === 'image');
                    const isPdfPreview = Boolean(currentEvidenceUrl) && (mime === 'application/pdf' || info.type === 'pdf');

                    setEvIcon(info.type);
                    setZoomControlsEnabled(isImagePreview);

                    if (evFileName) {
                        evFileName.textContent = evidence.file_name || 'Evidence';
                    }

                    if (evFileMeta) {
                        const uploadedText = evidence.uploaded_at ? ` | ${formatDateTime(evidence.uploaded_at)}` : '';
                        const sizeText = formatFileSize(evidence.file_size);
                        evFileMeta.textContent = `${info.label}${sizeText !== '--' ? ` | ${sizeText}` : ''}${uploadedText}`;
                    }

                    if (evDownloadBtn) {
                        evDownloadBtn.href = currentEvidenceDownloadUrl || '#';
                        evDownloadBtn.setAttribute('target', '_blank');
                        evDownloadBtn.setAttribute('rel', 'noopener');
                    }

                    if (evidenceImage) {
                        evidenceImage.classList.add('hidden');
                        evidenceImage.removeAttribute('src');
                        evidenceImage.style.transform = 'scale(1)';
                    }

                    if (evidenceIframe) {
                        evidenceIframe.classList.add('hidden');
                        evidenceIframe.src = 'about:blank';
                    }

                    if (evidenceNoPreview) {
                        evidenceNoPreview.classList.add('hidden');
                    }

                    if (evidenceImage && isImagePreview) {
                        evidenceImage.src = currentEvidenceUrl;
                        evidenceImage.classList.remove('hidden');
                    } else if (evidenceIframe && isPdfPreview) {
                        evidenceIframe.src = currentEvidenceUrl;
                        evidenceIframe.classList.remove('hidden');
                        evidenceIframe.removeAttribute('aria-hidden');
                    } else if (evidenceNoPreview) {
                        evidenceNoPreview.classList.remove('hidden');
                    }

                    evEvidenceList?.querySelectorAll('button[data-evidence-id]').forEach((btn) => {
                        const isActive = btn.dataset.evidenceId === selectedEvidenceId;
                        btn.classList.toggle('border-blue-500/70', isActive);
                        btn.classList.toggle('bg-blue-500/10', isActive);
                        btn.classList.toggle('border-gray-700', !isActive);
                        btn.classList.toggle('bg-gray-800/70', !isActive);
                    });

                    currentZoom = 1;
                    applyZoom();
                }

                function renderEvidenceList(evidences) {
                    if (!evEvidenceList) return;

                    evEvidenceList.innerHTML = '';

                    evidences.forEach((evidence) => {
                        const info = fileTypeInfo(evidence.mime_type, evidence.file_name);
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.dataset.evidenceId = String(evidence.id || '');
                        button.className =
                            'w-full rounded-lg border border-gray-700 bg-gray-800/70 px-3 py-2 text-left transition hover:border-blue-500/60 hover:bg-gray-800';

                        const header = document.createElement('div');
                        header.className = 'flex items-center gap-2';

                        const typeBadge = document.createElement('span');
                        typeBadge.className = 'inline-flex min-w-[2.2rem] justify-center rounded border border-gray-600 bg-gray-900 px-1.5 py-0.5 text-[10px] font-semibold text-gray-300';
                        typeBadge.textContent = info.type === 'pdf'
                            ? 'PDF'
                            : (info.type === 'image' ? 'IMG' : (info.type === 'xls' ? 'XLS' : 'DOC'));

                        const title = document.createElement('p');
                        title.className = 'truncate text-sm font-medium text-white';
                        title.textContent = evidence.file_name || 'Unnamed evidence';

                        const meta = document.createElement('p');
                        meta.className = 'mt-1 text-xs text-gray-400';
                        const sizeText = formatFileSize(evidence.file_size);
                        const uploadedText = evidence.uploaded_at ? formatDateTime(evidence.uploaded_at) : '--';
                        const parts = [info.label];
                        if (sizeText !== '--') parts.push(sizeText);
                        if (uploadedText !== '--') parts.push(uploadedText);
                        meta.textContent = parts.join(' | ');

                        header.appendChild(typeBadge);
                        header.appendChild(title);
                        button.appendChild(header);
                        button.appendChild(meta);
                        button.addEventListener('click', () => selectEvidence(evidence, evidences));

                        evEvidenceList.appendChild(button);
                    });
                }

                async function openEvidencePreview(task) {
                    if (!task?.id || !evidenceModal) return;

                    evidenceModal.classList.remove('hidden');
                    evidenceModal.classList.add('flex');
                    updateBodyScrollLock();

                    setEvidenceListMessage('Loading evidence...');
                    selectedEvidenceId = null;
                    currentEvidenceUrl = null;
                    currentEvidenceDownloadUrl = null;

                    if (evFileName) evFileName.textContent = 'Evidence';
                    if (evFileMeta) evFileMeta.textContent = '--';
                    if (evFileIcon) evFileIcon.textContent = '--';

                    if (evidenceImage) {
                        evidenceImage.removeAttribute('src');
                        evidenceImage.classList.add('hidden');
                        evidenceImage.style.transform = 'scale(1)';
                    }

                    if (evidenceIframe) {
                        evidenceIframe.src = 'about:blank';
                        evidenceIframe.classList.add('hidden');
                        evidenceIframe.setAttribute('aria-hidden', 'true');
                    }

                    if (evidenceNoPreview) {
                        evidenceNoPreview.classList.add('hidden');
                    }

                    if (evDownloadBtn) {
                        evDownloadBtn.href = '#';
                        evDownloadBtn.setAttribute('target', '_blank');
                        evDownloadBtn.setAttribute('rel', 'noopener');
                    }

                    currentZoom = 1;
                    applyZoom();
                    setZoomControlsEnabled(false);

                    try {
                        const response = await fetch(evidencesEndpoint(task.id), {
                            headers: {
                                Accept: 'application/json',
                            },
                        });

                        const payload = await response.json().catch(() => ([]));
                        if (!response.ok) {
                            throw new Error(payload?.message || 'Failed to load evidence.');
                        }

                        const evidences = Array.isArray(payload) ? payload : [];
                        if (!evidences.length) {
                            setEvidenceListMessage('No evidence found.');
                            if (evidenceNoPreview) {
                                evidenceNoPreview.classList.remove('hidden');
                            }
                            return;
                        }

                        renderEvidenceList(evidences);
                        selectEvidence(evidences[0], evidences);
                    } catch (error) {
                        setEvidenceListMessage('Unable to load evidence.');
                        if (evidenceNoPreview) {
                            evidenceNoPreview.classList.remove('hidden');
                        }
                        alert(error?.message || 'Failed to load evidence.');
                    }
                }

                function closeEvidenceModal() {
                    if (!evidenceModal) return;

                    currentEvidenceUrl = null;
                    currentEvidenceDownloadUrl = null;
                    selectedEvidenceId = null;
                    currentZoom = 1;
                    applyZoom();

                    if (evEvidenceList) {
                        evEvidenceList.innerHTML = '<p class="text-sm text-gray-400">No evidence found.</p>';
                    }
                    if (evidenceImage) {
                        evidenceImage.removeAttribute('src');
                        evidenceImage.classList.add('hidden');
                        evidenceImage.style.transform = 'scale(1)';
                    }

                    if (evidenceIframe) {
                        evidenceIframe.src = 'about:blank';
                        evidenceIframe.classList.add('hidden');
                        evidenceIframe.setAttribute('aria-hidden', 'true');
                    }
                    if (evidenceNoPreview) {
                        evidenceNoPreview.classList.add('hidden');
                    }
                    if (evDownloadBtn) {
                        evDownloadBtn.href = '#';
                        evDownloadBtn.setAttribute('target', '_blank');
                        evDownloadBtn.setAttribute('rel', 'noopener');
                    }
                    if (evFileName) evFileName.textContent = 'Evidence';
                    if (evFileMeta) evFileMeta.textContent = '--';
                    if (evFileIcon) evFileIcon.textContent = '--';
                    setZoomControlsEnabled(false);

                    evidenceModal.classList.add('hidden');
                    evidenceModal.classList.remove('flex');
                    updateBodyScrollLock();
                }

                tbody?.addEventListener('click', (event) => {
                    const detailButton = event.target.closest('button[data-task-id]');
                    if (!detailButton) return;
                    openTaskViewModal(detailButton.dataset.taskId);
                });

                statusFilter?.addEventListener('change', renderRows);
                dateFilter?.addEventListener('change', renderRows);

                closeTopBtn?.addEventListener('click', closeTaskViewModal);
                closeBottomBtn?.addEventListener('click', closeTaskViewModal);

                evZoomIn?.addEventListener('click', () => {
                    currentZoom = Math.min(2, currentZoom + 0.1);
                    applyZoom();
                });

                evZoomOut?.addEventListener('click', () => {
                    currentZoom = Math.max(0.5, currentZoom - 0.1);
                    applyZoom();
                });

                evZoomReset?.addEventListener('click', () => {
                    currentZoom = 1;
                    applyZoom();
                });

                closeEvidencePreview?.addEventListener('click', closeEvidenceModal);

                modal?.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeTaskViewModal();
                    }
                });

                evidenceModal?.addEventListener('click', (event) => {
                    if (event.target === evidenceModal) {
                        closeEvidenceModal();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') return;

                    if (evidenceModal && !evidenceModal.classList.contains('hidden')) {
                        closeEvidenceModal();
                        return;
                    }

                    if (modal && !modal.classList.contains('hidden')) {
                        closeTaskViewModal();
                    }
                });

                setExportButton(null);
                renderRows();
            })();
        </script>

    </section>
@endsection
