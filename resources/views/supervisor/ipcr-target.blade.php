@extends('layouts.supervisor')

@section('main-content')
<section class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">IPCR Target Review</h1>
            <p class="text-sm text-slate-400 mt-1">
                Stage I – Performance Planning and Commitment
            </p>
            <p class="text-xs text-slate-500">
                Review and endorse employee IPCR targets derived from approved UWP and OPCR.
                No ratings or accomplishments are performed at this stage.
            </p>
        </div>

        <span class="rounded-full border border-blue-600/40 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-200">
            Supervisor Endorsement
        </span>
    </div>

    {{-- IPCR LIST --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 overflow-hidden">
        <div class="border-b border-slate-800 p-4">
            <h2 class="text-lg font-semibold text-white">Submitted IPCR Targets</h2>
            <p class="text-xs text-slate-400">
                IPCR targets committed by employees and awaiting supervisor endorsement.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-950/80 text-slate-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Employee</th>
                        <th class="px-4 py-3 text-left font-semibold">Position</th>
                        <th class="px-4 py-3 text-left font-semibold">Rating Period</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold w-32">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-100">
                    <tr class="hover:bg-slate-900/60">
                        <td class="px-4 py-3 font-semibold">Ramon Reyes</td>
                        <td class="px-4 py-3 text-slate-300">Records Management Officer</td>
                        <td class="px-4 py-3 text-slate-300">January – June 2026</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full border border-blue-500/40 bg-blue-500/10 px-2.5 py-1 text-xs font-semibold text-blue-200">
                                For Endorsement
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button type="button"
                                    data-review-ipcr
                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                Review
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- REVIEW IPCR MODAL --}}
<div id="ipcr-review-modal"
     class="fixed inset-0 z-[70] hidden flex items-start justify-center bg-black/70 px-4 py-8">

    <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 shadow-xl">

        {{-- MODAL HEADER --}}
        <div class="flex items-start justify-between border-b border-slate-800 px-6 py-4">
            <div>
                <p class="text-xs uppercase tracking-widest text-blue-300">Stage I</p>
                <h2 class="text-lg font-semibold text-white">IPCR Target Review</h2>
                <p class="text-xs text-slate-400">
                    Supervisor endorsement of employee performance targets only.
                </p>
            </div>
            <button data-close-ipcr class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="px-6 py-5 space-y-6 max-h-[70vh] overflow-y-auto">

            {{-- EMPLOYEE INFO --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-xs uppercase text-slate-400">Employee</p>
                    <p class="font-semibold text-white">Ramon Reyes</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-xs uppercase text-slate-400">Position</p>
                    <p class="font-semibold text-white">Records Management Officer</p>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                    <p class="text-xs uppercase text-slate-400">Rating Period</p>
                    <p class="font-semibold text-white">January – June 2026</p>
                </div>
            </div>

            {{-- CORE FUNCTIONS --}}
            <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                <div class="border-b border-slate-800 px-4 py-3">
                    <h3 class="text-sm font-semibold text-white">
                        Core Functions <span class="text-slate-400">(80%)</span>
                    </h3>
                    <p class="text-xs text-slate-400">Derived from approved UWP and OPCR.</p>
                </div>

                <div class="p-4 space-y-3 text-sm text-slate-300">
                    <div>
                        <p class="font-semibold text-white">
                            E-Bank Scanning and Encoding of Revenue Transactions (50%)
                        </p>
                        <p class="text-xs">
                            Target: 95% same-day processing<br>
                            Timeline: January – June 2026
                        </p>
                    </div>

                    <div>
                        <p class="font-semibold text-white">
                            Processing of Over-the-Counter Revenue Transactions (30%)
                        </p>
                        <p class="text-xs">
                            Target: 95% same-day processing<br>
                            Timeline: January – June 2026
                        </p>
                    </div>
                </div>
            </div>

            {{-- SUPPORT FUNCTIONS --}}
            <div class="rounded-xl border border-slate-800 bg-slate-900/70">
                <div class="border-b border-slate-800 px-4 py-3">
                    <h3 class="text-sm font-semibold text-white">
                        Support Functions <span class="text-slate-400">(20%)</span>
                    </h3>
                </div>

                <div class="p-4 text-sm text-slate-300">
                    <p class="font-semibold text-white">
                        Maintenance of Revenue Records Filing System (20%)
                    </p>
                    <p class="text-xs">
                        Target: Quarterly validation<br>
                        Timeline: January – June 2026
                    </p>
                </div>
            </div>

            {{-- SUPERVISOR REMARKS --}}
            <div>
                <label class="text-sm font-semibold text-white">
                    Supervisor Remarks (required only if returning)
                </label>
                <textarea
                    style="background:#0f172a;color:#e5e7eb;"
                    rows="3"
                    class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950/60 px-4 py-3 text-sm text-slate-100"
                    placeholder="Enter remarks if returning IPCR"></textarea>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3 border-t border-slate-800 px-6 py-4">
            <button data-employee-loading="true" data-loading-text="Returning..."
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800">
                <span data-button-label>Return to Employee</span>
                <span data-button-spinner
                      class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            </button>

            <button data-employee-loading="true" data-loading-text="Endorsing..."
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                <span data-button-label>Endorse IPCR</span>
                <span data-button-spinner
                      class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('ipcr-review-modal');

    document.querySelector('[data-review-ipcr]').onclick = () => {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    document.querySelectorAll('[data-close-ipcr]').forEach(btn => {
        btn.onclick = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };
    });

    document.querySelectorAll('[data-employee-loading="true"]').forEach(button => {
        button.addEventListener('click', () => {
            if (button.dataset.loadingActive === 'true') return;
            button.dataset.loadingActive = 'true';

            const label = button.querySelector('[data-button-label]');
            const spinner = button.querySelector('[data-button-spinner]');
            const original = label.textContent;

            label.textContent = button.dataset.loadingText;
            spinner.classList.remove('hidden');
            button.disabled = true;

            setTimeout(() => {
                label.textContent = original;
                spinner.classList.add('hidden');
                button.disabled = false;
                button.dataset.loadingActive = 'false';

                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 1200);
        });
    });
});
</script>
@endpush
@endsection
