@extends('layouts.supervisor')

@section('main-content')
    @php
        $mpors = $mpors ?? collect();
        $month = $month ?? now()->format('Y-m');
        $monthLabel = $monthLabel ?? now()->format('F Y');
        $search = $search ?? '';
    @endphp

    <section class="space-y-5">
        {{-- Header --}}
        <div>
            <h1 class="mt-1 text-xl font-bold text-white md:text-2xl">MPOR List (Supervisor)</h1>
            <p class="mt-1 text-sm text-slate-400">Showing submitted, approved, and endorsed MPORs for <span id="mpor-month-label">{{ $monthLabel }}</span>.</p>
        </div>

        {{-- Filters: single row --}}
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500"></i>
                <input type="text" id="mpor-search" value="{{ $search }}"
                    placeholder="Search employee..."
                    style="color-scheme: dark; background-color: #0f172a;"
                    class="w-full rounded-lg border border-slate-700 bg-slate-900 py-2.5 pl-10 pr-3 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none focus:ring-0">
            </div>
            <div class="shrink-0">
                <input type="month" id="mpor-month" value="{{ $month }}"
                    style="color-scheme: dark; background-color: #0f172a;"
                    class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none focus:ring-0 sm:w-44">
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-gray-700 bg-slate-900/40">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-200">
                    <thead class="text-[0.65rem] uppercase tracking-[0.3em] text-slate-500">
                        <tr class="border-b border-gray-700">
                            <th class="px-4 py-3">Employee</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="mpor-table-body" class="divide-y divide-slate-800/50">
                        @include('supervisor._mpor-table-body', ['mpors' => $mpors])
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script>
        (function() {
            const searchInput = document.getElementById('mpor-search');
            const monthInput = document.getElementById('mpor-month');
            const tableBody = document.getElementById('mpor-table-body');
            let debounceTimer = null;

            function fetchResults() {
                const params = new URLSearchParams();
                if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
                if (monthInput.value) params.set('month', monthInput.value);

                fetch(`{{ route('supervisor.mpor') }}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => { tableBody.innerHTML = data.html; })
                .catch(() => {});
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchResults, 300);
            });

            monthInput.addEventListener('change', fetchResults);
        })();
    </script>
@endsection
