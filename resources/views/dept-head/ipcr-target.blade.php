@extends('layouts.dept-head')

@section('main-content')
<section class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">IPCR Target Approval</h1>
            <p class="text-sm text-slate-400 mt-1">
                Review and approve Individual Performance Commitment targets.
            </p>
            <p class="text-xs text-slate-500">
                Stage I – Planning and Commitment only. No ratings or accomplishments.
            </p>
        </div>
        <span class="rounded-full bg-amber-500/10 border border-amber-500/40 px-3 py-1 text-xs font-semibold text-amber-200">
            Stage I – For Approval
        </span>
    </div>

    {{-- IPCR LIST --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-white">Submitted IPCR Targets</h2>
                <p class="text-xs text-slate-400">Committed by employee and endorsed by supervisor</p>
            </div>
            <span class="text-[11px] text-slate-500">
                Status: For Approval / Returned / Approved
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-950 text-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left">Employee</th>
                        <th class="px-4 py-3 text-left">Position</th>
                        <th class="px-4 py-3 text-left">Rating Period</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left w-32">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-100">
                    <tr class="hover:bg-slate-900/60">
                        <td class="px-4 py-3 font-semibold">Ramon Reyes</td>
                        <td class="px-4 py-3 text-slate-300">Records Management Officer</td>
                        <td class="px-4 py-3 text-slate-300">January – June 2026</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-amber-500/10 border border-amber-500/40 px-2.5 py-1 text-xs font-semibold text-amber-200">
                                For Approval
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button
                                data-view-ipcr
                                class="inline-flex items-center gap-2 rounded-lg border border-slate-700 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                                View
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</section>

{{-- VIEW IPCR TARGET MODAL --}}
<div id="ipcr-view-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4 py-6">
    <div class="w-full max-w-4xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-xl">

        {{-- MODAL HEADER --}}
        <div class="flex justify-between items-start border-b border-slate-800 pb-4">
            <div>
                <h3 class="text-lg font-semibold text-white">IPCR Targets</h3>
                <p class="text-xs text-slate-400">Stage I – Performance Commitment</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('stage1.ipcr.export.pdf') }}"
                   target="_blank"
                   class="inline-flex items-center gap-1 rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-slate-800">
                    Export PDF
                </a>
                <button data-close-modal class="text-slate-400 hover:text-white">✕</button>
            </div>
        </div>

        {{-- EMPLOYEE INFO --}}
        <div class="mt-5 grid grid-cols-3 gap-3 text-sm">
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                <p class="text-xs text-slate-500">Employee</p>
                <p class="font-semibold text-white">Ramon Reyes</p>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                <p class="text-xs text-slate-500">Position</p>
                <p class="font-semibold text-white">Records Management Officer</p>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3">
                <p class="text-xs text-slate-500">Rating Period</p>
                <p class="font-semibold text-white">January – June 2026</p>
            </div>
        </div>

        {{-- CORE FUNCTIONS --}}
        <div class="mt-6 rounded-xl border border-slate-800 bg-slate-900/70">
            <div class="px-4 py-3 border-b border-slate-800">
                <h4 class="text-sm font-semibold text-white">Core Functions (80%)</h4>
                <p class="text-xs text-slate-400">Derived from approved UWP and OPCR</p>
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
        <div class="mt-4 rounded-xl border border-slate-800 bg-slate-900/70">
            <div class="px-4 py-3 border-b border-slate-800">
                <h4 class="text-sm font-semibold text-white">Support Functions (20%)</h4>
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

        {{-- REMARKS --}}
        <div class="mt-5">
            <label class="text-sm font-semibold text-white">
                Department Head Remarks (required only if returning)
            </label>
            <textarea
                style="background:#0f172a;color:#e5e7eb;"
                class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-white"
                rows="3"
                placeholder="Enter remarks if returning IPCR"></textarea>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3 pt-5 border-t border-slate-800 mt-6">
            <button data-close-modal
                    class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                Close
            </button>
            <button data-return
                    class="inline-flex items-center gap-2 rounded-lg border border-rose-600 px-4 py-2 text-sm font-semibold text-rose-300 hover:bg-rose-600/10">
                <span data-button-label>Return</span>
                <span data-button-spinner
                    class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-rose-300/40 border-t-rose-300"></span>
            </button>

            <button data-approve
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                <span data-button-label>Approve</span>
                <span data-button-spinner
                    class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
            </button>

        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('ipcr-view-modal');

    document.querySelector('[data-view-ipcr]').onclick = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.onclick = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        };
    });

    function buttonLoading(btn, loadingText) {
        const label = btn.querySelector('[data-button-label]');
        const spinner = btn.querySelector('[data-button-spinner]');

        btn.disabled = true;
        label.textContent = loadingText;
        spinner.classList.remove('hidden');

        setTimeout(() => {
            btn.disabled = false;
            spinner.classList.add('hidden');
            label.textContent = loadingText === 'Approving...' ? 'Approve' : 'Return';

            // close modal after demo delay
            const modal = document.getElementById('ipcr-view-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }, 1000);
    }

    document.querySelector('[data-approve]').addEventListener('click', e => {
        buttonLoading(e.currentTarget, 'Approving...');
    });

    document.querySelector('[data-return]').addEventListener('click', e => {
        buttonLoading(e.currentTarget, 'Returning...');
    });
});
</script>
@endpush
@endsection
