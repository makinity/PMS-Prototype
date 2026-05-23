@extends('layouts.dept-head')

@section('main-content')
    <section class="space-y-5">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-400">Department Head Review</p>
                <h1 class="mt-1 text-xl font-bold text-white md:text-2xl">Employee Submission Details</h1>
                <p class="mt-1 text-sm text-slate-400">Read-only snapshot of employee accomplishment submission.</p>
            </div>
            <a href="{{ route('dept-head.acc-review') }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800">
                <i class="fa-solid fa-arrow-left mr-2 text-xs"></i> Back to List
            </a>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Status</p>
                @php
                    $badgeClass = match ($status) {
                        'supervisor_endorsed' => 'border-blue-500/30 bg-blue-500/10 text-blue-200',
                        'dept_head_endorsed' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200',
                        default => 'border-slate-700 bg-slate-800 text-slate-200',
                    };
                @endphp
                <span id="status-badge" class="mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                    {{ $statusLabel }}
                </span>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Submitted At</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $submittedAtLabel }}</p>
            </div>
            <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                <p class="text-[11px] uppercase tracking-[0.2em] text-emerald-400">Performance Score</p>
                <p class="mt-1 text-lg font-bold text-emerald-300">{{ number_format($computedScore, 2) }}</p>
            </div>
            <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                <p class="text-[11px] uppercase tracking-[0.2em] text-emerald-400">Performance Rating</p>
                <p class="mt-1 text-sm font-bold text-emerald-300">{{ $computedRating }}</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Employee</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $employeeName }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Office</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $officeName }}</p>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Period</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $periodLabel }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Employee Remarks</p>
            <p class="mt-2 text-sm text-slate-300">{{ $remarks ?: '--' }}</p>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Attachments</p>
            @if (!empty($attachments))
                <div class="mt-2 space-y-2">
                    @foreach ($attachments as $file)
                        <a href="{{ $file['url'] ?? '#' }}" target="_blank"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-200 hover:bg-slate-700">
                            <i class="fa-solid fa-paperclip text-xs text-slate-400"></i>
                            {{ $file['name'] ?? 'File' }}
                        </a>
                    @endforeach
                </div>
            @else
                <p class="mt-2 text-sm text-slate-400">No attachments uploaded.</p>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h4 class="text-sm font-semibold text-white">SMPOR - Monitoring Summary</h4>
                        <p class="mt-1 text-xs text-slate-400">Official (Submitted Snapshot) – QAR-linked MPORs</p>
                    </div>
                    <a href="{{ route('dept-head.acc-review.smpor-preview', $submission->id) }}"
                        class="inline-flex text-slate-300 transition hover:text-white">
                        <i class="fa-regular fa-eye"></i>
                    </a>
                </div>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h4 class="text-sm font-semibold text-white">IPCR Accomplishment Report</h4>
                        <p class="mt-1 text-xs text-slate-400">Success indicators and standards snapshot.</p>
                    </div>
                    <a href="{{ route('dept-head.acc-review.ipcr-preview', $submission->id) }}"
                        class="inline-flex text-slate-300 transition hover:text-white">
                        <i class="fa-regular fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>

        <div id="action-section" class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
            @if ($status === 'supervisor_endorsed')
                <div class="flex items-center justify-end">
                    <button type="button" id="btnEndorsePmt"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        <span id="endorse-spinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                        <span id="endorse-label">Endorse to PMT</span>
                    </button>
                </div>
            @elseif ($status === 'dept_head_endorsed')
                <div class="text-center text-sm text-emerald-300">
                    <i class="fa-solid fa-check-circle mr-1"></i> Endorsed to PMT.
                </div>
            @else
                <div class="text-sm text-slate-400">This submission is view-only.</div>
            @endif
        </div>
    </section>

    <script>
    (function() {
        const btn = document.getElementById('btnEndorsePmt');
        if (!btn) return;

        btn.addEventListener('click', async () => {
            const spinner = document.getElementById('endorse-spinner');
            const label = document.getElementById('endorse-label');
            btn.disabled = true;
            if (spinner) spinner.classList.remove('hidden');
            if (label) label.textContent = 'Endorsing...';

            try {
                const res = await fetch('{{ route("dept-head.acc-review.endorse", $submission->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    throw new Error(data.message || 'Failed to endorse.');
                }

                const badge = document.getElementById('status-badge');
                if (badge) {
                    badge.textContent = 'Dept Head Endorsed';
                    badge.className = 'mt-2 inline-flex rounded-full border px-3 py-1 text-xs font-semibold border-emerald-500/30 bg-emerald-500/10 text-emerald-200';
                }

                document.getElementById('action-section').innerHTML =
                    '<div class="text-center text-sm text-emerald-300"><i class="fa-solid fa-check-circle mr-1"></i> Endorsed to PMT.</div>';

                showSnackbar('Endorsed to PMT successfully.');
            } catch (err) {
                btn.disabled = false;
                if (spinner) spinner.classList.add('hidden');
                if (label) label.textContent = 'Endorse to PMT';
                showSnackbar(err.message || 'Something went wrong.', true);
            }
        });

        function showSnackbar(message, isError = false) {
            const el = document.createElement('div');
            el.className = `fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] rounded-lg px-5 py-3 text-sm font-semibold shadow-lg ${isError ? 'border border-rose-500/30 bg-rose-500/10 text-rose-200' : 'border border-emerald-500/30 bg-emerald-500/10 text-emerald-200'}`;
            el.innerHTML = `<i class="fa-solid ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'} mr-2"></i>${message}`;
            document.body.appendChild(el);
            setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 3000);
        }
    })();
    </script>
@endsection
