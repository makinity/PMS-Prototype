<x-layouts.dept-head>
    <section class="space-y-6">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold text-white">Individual Development Plan (IDP)</h1>
            <p class="text-sm text-slate-300">Prototype view for Dept-Head to locate employees and encode their IDPs.</p>
        </div>

        @if (class_exists(\Livewire\Livewire::class))
            <livewire:dept-head-idp-search />
        @else
            <div class="rounded-2xl border border-[#2a3a4f] bg-[#111a29] p-5 text-sm text-amber-200">
                Livewire is not available in this build. Enable Livewire to use the interactive employee search and IDP entry.
            </div>
        @endif
    </section>
</x-layouts.dept-head>
