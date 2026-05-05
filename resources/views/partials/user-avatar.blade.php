@props(['user', 'size' => 'h-9 w-9'])

<div class="flex {{ $size }} items-center justify-center overflow-hidden rounded-full shadow-sm">
    @if ($user->profile_photo_url)
        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
    @else
        <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-indigo-600 via-blue-600 to-emerald-500 text-xs font-semibold text-white">
            {{ $user->initials }}
        </span>
    @endif
</div>
