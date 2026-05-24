<div class="space-y-6">
    <!-- Employee search & dropdown list -->
    <div class="rounded-2xl border border-gray-700 bg-slate-950 p-5 shadow-lg shadow-black/30">
        <div class="space-y-1">
            <p class="text-sm font-semibold text-white">Employee lookup</p>
            <p class="text-xs text-slate-400">Type a name or ID, then pick an employee to review and encode their IDP.</p>
        </div>
        <div class="relative mt-3 w-full md:w-2/3 lg:w-1/2">
            <input
                id="employee-search"
                type="text"
                autocomplete="off"
                placeholder="e.g. Ramon Reyes or EMP-0078"
                wire:model.live.debounce.300ms="searchTerm"
                class="w-full rounded-full border border-slate-700 bg-slate-900 px-4 py-2.5 pr-12 text-sm text-slate-100 placeholder-slate-300 shadow-inner shadow-black/30 focus:border-blue-500 focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                style="background:#0f172a;color:#e5e7eb;"
                aria-expanded="{{ count($filteredEmployees) > 0 ? 'true' : 'false' }}"
            >
            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"></path>
                </svg>
            </span>

            @if(!empty($searchTerm) && count($filteredEmployees) > 0)
                <ul class="absolute left-0 right-0 z-30 mt-2 max-h-72 overflow-y-auto rounded-xl border border-slate-700 bg-slate-900 shadow-2xl shadow-black/50">
                    @foreach($filteredEmployees as $employee)
                        <li>
                            <button
                                type="button"
                                wire:click="selectEmployee('{{ $employee['id'] }}')"
                                class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left text-sm text-slate-100 transition hover:bg-slate-800"
                            >
                                <div>
                                    <p class="font-semibold">{{ $employee['name'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $employee['id'] }} - {{ $employee['department'] }}</p>
                                </div>
                                <span class="rounded-full bg-slate-800 px-2 py-1 text-[11px] font-semibold text-slate-200">{{ $employee['status'] }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @elseif(!empty($searchTerm))
                <div class="absolute left-0 right-0 z-30 mt-2 rounded-xl border border-slate-700 bg-slate-900 px-4 py-2 text-xs text-slate-300 shadow-2xl shadow-black/50">
                    No matches found.
                </div>
            @endif
        </div>
        <p class="mt-2 text-xs text-slate-400">{{ count($filteredEmployees) }} found</p>
    </div>

    <!-- Employee info -->
    <div class="rounded-2xl border border-gray-700 bg-slate-950 p-5 shadow-lg shadow-black/30">
        <div class="flex items-start justify-between gap-2">
            <div>
                <p class="text-sm font-semibold text-white">Employee information</p>
                <p class="text-xs text-slate-400">Read-only overview of credentials and IDP status.</p>
            </div>
            @if (!empty($selectedEmployee))
                <span class="rounded-full border border-slate-700 bg-slate-900 px-3 py-1 text-xs font-semibold text-slate-200">{{ $selectedEmployee['status'] ?? 'Status' }}</span>
            @endif
        </div>

        @if (empty($selectedEmployee))
            <div class="mt-4 rounded-xl border border-dashed border-gray-700 bg-slate-950/40 p-6 text-center text-sm text-slate-400">
                Select an employee to view their IDP details.
            </div>
        @else
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-lg border border-gray-700 bg-slate-900 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Full name</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $selectedEmployee['name'] ?? '--' }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Employee ID</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $selectedEmployee['id'] ?? '--' }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Department</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $selectedEmployee['department'] ?? '--' }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Position / Role</p>
                    <p class="mt-1 text-sm font-semibold text-white">{{ $selectedEmployee['role'] ?? '--' }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Latest rating</p>
                    <p class="mt-1 text-sm font-semibold text-emerald-200">{{ $selectedEmployee['rating'] ?? '--' }}</p>
                </div>
                <div class="rounded-lg border border-gray-700 bg-slate-900 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">IDP progress</p>
                    <p class="mt-1 text-sm font-semibold text-blue-200">{{ $selectedEmployee['progress'] ?? '--' }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- IDP plan entry -->
    <div
        x-data="{ submitting: false, showDraftModal: false, savingDraft: false }"
        class="rounded-2xl border border-gray-700 bg-slate-950 p-5 shadow-lg shadow-black/30 {{ empty($selectedEmployee) ? 'hidden' : '' }}"
    >
        <div class="flex items-start justify-between gap-2">
            <div>
                <p class="text-sm font-semibold text-white">Encode IDP plan</p>
                <p class="text-xs text-slate-400">Dept-Head can capture objectives and support for the selected employee.</p>
            </div>
            <span class="text-xs font-semibold text-emerald-200">{{ $selectedEmployee['name'] ?? '' }}</span>
        </div>

        <div class="mt-4 space-y-4">
            <div>
                <label for="idp-objective" class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Development objective</label>
                <textarea
                    id="idp-objective"
                    rows="3"
                    placeholder="Outline the development goal"
                    wire:model.lazy="objective"
                    class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-400 shadow-inner shadow-black/30 focus:border-blue-500 focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                    style="background:#0f172a;color:#e5e7eb;border-color:#334155;"
                ></textarea>
            </div>
            <div>
                <label for="idp-activity" class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Planned activity</label>
                <textarea
                    id="idp-activity"
                    rows="3"
                    placeholder="Training, mentoring, or projects to fulfill the objective"
                    wire:model.lazy="activity"
                    class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-400 shadow-inner shadow-black/30 focus:border-blue-500 focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                    style="background:#0f172a;color:#e5e7eb;border-color:#334155;"
                ></textarea>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="idp-target" class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Target completion</label>
                    <input
                        id="idp-target"
                        type="date"
                        wire:model.lazy="targetDate"
                        class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-400 shadow-inner shadow-black/30 focus:border-blue-500 focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                        style="background:#0f172a;color:#e5e7eb;border-color:#334155;"
                    >
                </div>
                <div>
                    <label for="idp-support" class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Support needed</label>
                    <input
                        id="idp-support"
                        type="text"
                        placeholder="Budget, approvals, tools, coaching"
                        wire:model.lazy="support"
                        class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-400 shadow-inner shadow-black/30 focus:border-blue-500 focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                        style="background:#0f172a;color:#e5e7eb;border-color:#334155;"
                    >
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="idp-notes" class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Reviewer notes</label>
                    <textarea
                        id="idp-notes"
                        rows="3"
                        placeholder="Optional remarks or follow-ups"
                        wire:model.lazy="notes"
                        class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 placeholder-slate-300 shadow-inner shadow-black/30 focus:border-blue-500 focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/40"
                        style="background:#0f172a;color:#e5e7eb;border-color:#334155;"
                    ></textarea>
                </div>
                <div class="flex flex-col justify-end gap-3">
                    <p class="text-xs text-slate-400">Prototype actions only. No backend submission.</p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            @click="showDraftModal = true"
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800"
                        >
                            Save draft
                        </button>
                        <button
                            type="button"
                            @click="if (submitting) return; submitting = true; setTimeout(() => { submitting = false }, 1400);"
                            :class="submitting ? 'opacity-70 cursor-not-allowed' : ''"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            <svg x-show="submitting" class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span>Submit IDP</span>
                        </button>
        </div>
    </div>

    <div
        x-show="showDraftModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        @click.self="showDraftModal = false"
    >
        <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900 p-5 shadow-2xl shadow-black/50">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Save draft</h3>
                    <p class="text-sm text-slate-300">Save this IDP as a draft for later edits.</p>
                </div>
                <button @click="showDraftModal = false" class="rounded-full p-1 text-slate-400 hover:bg-slate-800 hover:text-white">
                    <span class="sr-only">Close</span>
                    Ã—
                </button>
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <button @click="showDraftModal = false" class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">Cancel</button>
                <button
                    type="button"
                    @click="if (savingDraft) return; savingDraft = true; setTimeout(() => { savingDraft = false; showDraftModal = false; }, 1200);"
                    :class="savingDraft ? 'opacity-70 cursor-not-allowed' : ''"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    <svg x-show="savingDraft" class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span>Confirm save</span>
                </button>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>

</div>
