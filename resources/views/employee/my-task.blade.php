@extends('layouts.employee')

@section('main-content')
    <section class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-white">My Tasks</h1>
            <p class="text-sm text-gray-400 mt-1">
                Read-only mirror of ORS entries.
                <span class="block">Tasks are created and submitted in ORS.</span>
                <span class="block">This page mirrors ORS status and declared quantity only. Quantity is encoded in ORS after task logging.</span>
            </p>
        </div>

        {{-- Task Filters --}}
        <div class="flex flex-wrap gap-3">
            <select class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2.5">
                <option class="bg-gray-700">Status: All</option>
                <option class="bg-gray-700">Draft</option>
                <option class="bg-gray-700">Recording</option>
                <option class="bg-gray-700">Submitted</option>
                <option class="bg-gray-700">Missing / Overdue</option>
                <option class="bg-gray-700">Returned</option>
            </select>

            <input type="date"
                   class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2.5">
        </div>

        {{-- Task Table --}}
        <div class="bg-gray-800 border border-gray-700 rounded-lg overflow-hidden">
            <div class="px-4 py-3 text-xs text-gray-400 border-b border-gray-700">
                Status reflects ORS state only; no submissions, uploads, or task creation here.
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-white">Task</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Output State</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Quantity (ORS)</th>
                            <th class="px-4 py-3 text-left font-medium text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">

                        {{-- DEMO LOCKED DATASET (Stage II - Ramon Reyes) --}}

                        {{-- Jan 2, 2026 — Submitted (Locked) --}}
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">
                                Same-day verification of OTC transactions
                            </td>
                            <td class="px-4 py-3 text-gray-300">
                                Jan 2, 2026
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-900 text-violet-300">
                                    Submitted (Locked)
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-emerald-300">Output submitted</span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">12 transactions</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        onclick="openTaskViewModal({task:'Same-day verification of OTC transactions', date:'Jan 2, 2026', status:'Submitted (Locked)', outputState:'Output submitted', outputType:'Official Receipt (OR)', quantity:'12 transactions', evidence:'Attached', notes:'Visible to supervisor (rateable in ORS Monitoring)'})"
                                        class="rounded-lg border border-gray-600 px-3 py-1.5 text-xs font-semibold text-gray-200 hover:bg-gray-800">
                                    View
                                </button>
                            </td>
                        </tr>

                        {{-- Jan 4, 2026 — Submitted (Locked) --}}
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">
                                All e-bank transactions scanned and encoded daily
                            </td>
                            <td class="px-4 py-3 text-gray-300">
                                Jan 4, 2026
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-900 text-violet-300">
                                    Submitted (Locked)
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-emerald-300">Output submitted</span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">1 daily batch</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        onclick="openTaskViewModal({task:'All e-bank transactions scanned and encoded daily', date:'Jan 4, 2026', status:'Submitted (Locked)', outputState:'Output submitted', outputType:'Bank Statement Form (BSF-01)', quantity:'1 daily batch', evidence:'Attached', notes:'Evidence attached (BSF-01)'})"
                                        class="rounded-lg border border-gray-600 px-3 py-1.5 text-xs font-semibold text-gray-200 hover:bg-gray-800">
                                    View
                                </button>
                            </td>
                        </tr>

                        {{-- Jan 5, 2026 — Recording --}}
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">
                                OR validation completed daily
                            </td>
                            <td class="px-4 py-3 text-gray-300">
                                Jan 5, 2026
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-900 text-amber-300">
                                    Recording
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-amber-300">Output pending</span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">—</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        onclick="openTaskViewModal({task:'OR validation completed daily', date:'Jan 5, 2026', status:'Recording', outputState:'Output pending', outputType:'Official Receipt (OR)', quantity:'—', evidence:'N/A', notes:'Active timer'})"
                                        class="rounded-lg border border-gray-600 px-3 py-1.5 text-xs font-semibold text-gray-200 hover:bg-gray-800">
                                    View
                                </button>
                            </td>
                        </tr>

                        {{-- Jan 6, 2026 — Missing / Overdue --}}
                        <tr class="hover:bg-gray-750">
                            <td class="px-4 py-3 text-gray-300">
                                Retrieval logs maintained for audit purposes
                            </td>
                            <td class="px-4 py-3 text-gray-300">
                                Jan 6, 2026
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900 text-red-300">
                                    Missing / Overdue
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-red-300">Output Missing</span>
                            </td>
                            <td class="px-4 py-3 text-gray-300">—</td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        onclick="openTaskViewModal({task:'Retrieval logs maintained for audit purposes', date:'Jan 6, 2026', status:'Missing / Overdue', outputState:'Output Missing', outputType:'Records Inventory Checklist', quantity:'—', evidence:'None', notes:'No ORS entry submitted for the day'})"
                                        class="rounded-lg border border-gray-600 px-3 py-1.5 text-xs font-semibold text-gray-200 hover:bg-gray-800">
                                    View
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center p-4 text-sm text-gray-400 bg-gray-800 border border-gray-700 rounded-lg">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3 text-blue-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 0 0 1 0 2Z"/>
            </svg>
            <span>My Tasks mirrors ORS activity and declared quantity. Tasks are created and submitted in ORS, and quantity is encoded in ORS after task logging.</span>
        </div>

        <div id="task-view-modal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 px-4 py-6">
            <div class="relative w-full max-w-4xl">
                <div class="relative flex max-h-[85vh] flex-col overflow-hidden rounded-2xl border border-gray-700 bg-gray-900 shadow-lg">
                    <div class="flex items-start justify-between border-b border-gray-700 px-6 py-4">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Task Details</h3>
                            <p class="text-xs text-gray-400">My Tasks mirrors ORS activity (read-only).</p>
                        </div>
                        <button type="button" onclick="closeTaskViewModal()" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-800 hover:text-white">
                            <span class="sr-only">Close modal</span>
                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-5 text-sm text-gray-300">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Employee</p>
                                <p class="text-sm font-medium text-white">Ramon Reyes</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Task / Indicator</p>
                                <p id="mvTask" class="text-sm font-medium text-white">—</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Date</p>
                                <p id="mvDate" class="text-sm font-medium text-white">—</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                                <p id="mvStatus" class="text-sm font-medium text-white">—</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Output Type</p>
                                <p id="mvOutputType" class="text-sm font-medium text-white">—</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Output State</p>
                                <p id="mvOutputState" class="text-sm font-medium text-white">—</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Quantity (ORS)</p>
                                <p id="mvQuantity" class="text-sm font-medium text-white">—</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Evidence</p>
                                <p id="mvEvidence" class="text-sm font-medium text-white">N/A</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Notes</p>
                            <p id="mvNotes" class="mt-1 text-sm text-gray-300">—</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-700 px-6 py-3">
                        <button type="button" onclick="closeTaskViewModal()" class="rounded-lg border border-gray-600 px-4 py-2 text-sm font-medium text-gray-200 transition hover:bg-gray-800">
                            Close
                        </button>
                        <a href="{{ route('employee.ors') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-500">
                            View in ORS
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const modal = document.getElementById('task-view-modal');
                const fields = {
                    task: document.getElementById('mvTask'),
                    date: document.getElementById('mvDate'),
                    status: document.getElementById('mvStatus'),
                    outputType: document.getElementById('mvOutputType'),
                    outputState: document.getElementById('mvOutputState'),
                    quantity: document.getElementById('mvQuantity'),
                    evidence: document.getElementById('mvEvidence'),
                    notes: document.getElementById('mvNotes'),
                };

                function setField(key, value, fallback = '—') {
                    if (!fields[key]) return;
                    fields[key].textContent = value || fallback;
                }

                window.openTaskViewModal = function (data = {}) {
                    if (!modal) return;
                    setField('task', data.task);
                    setField('date', data.date);
                    setField('status', data.status);
                    setField('outputType', data.outputType);
                    setField('outputState', data.outputState);
                    setField('quantity', data.quantity, '—');
                    setField('evidence', data.evidence, 'N/A');
                    setField('notes', data.notes, '—');

                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                };

                window.closeTaskViewModal = function () {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                };

                modal?.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeTaskViewModal();
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                        closeTaskViewModal();
                    }
                });
            })();
        </script>

    </section>
@endsection
