@extends('layouts.admin')

@section('main-content')
    <section class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Account</p>
                <h1 class="mt-1 text-2xl font-bold text-white">Profile &amp; Security</h1>
                <p class="text-sm text-slate-400">Manage your administrator account and security settings.</p>
            </div>
            <span class="rounded-full border border-sky-600/50 bg-sky-500/10 px-3 py-1 text-[11px] font-semibold text-sky-200">ADMIN</span>
        </div>

        {{-- Profile Card --}}
        <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile Information
                </h2>
                @if(auth()->user()->email_verified_at)
                    <span class="px-3 py-1 text-xs rounded-full border border-emerald-600/50 bg-emerald-500/10 text-emerald-300">VERIFIED</span>
                @else

                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Avatar --}}
                <div class="lg:col-span-1 flex flex-col items-center">
                    <div class="relative mb-4">
                        @if(auth()->user()->profile_photo_path)
                            <img class="w-32 h-32 rounded-full border-4 border-slate-700 object-cover"
                                 src="{{ auth()->user()->profile_photo_url }}"
                                 alt="Profile picture">
                        @else
                            <img class="w-32 h-32 rounded-full border-4 border-slate-700 object-cover"
                                 src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0369a1&color=fff&size=128"
                                 alt="Profile picture">
                        @endif
                        <button type="button" onclick="document.getElementById('profile_photo').click()" class="absolute bottom-2 right-2 p-2 bg-sky-600 hover:bg-sky-700 rounded-full text-white transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-slate-400">Max size: 5MB</p>
                    <p class="text-xs text-slate-500">JPG, PNG, or GIF</p>
                </div>

                {{-- Profile Form --}}
                <div class="lg:col-span-3">
                    <form method="POST" action="{{ route('user-profile-information.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input id="profile_photo" name="profile_photo" type="file" accept="image/*" class="hidden" onchange="this.form.requestSubmit()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Full Name</label>
                                <input type="text"
                                       class="!bg-slate-900 !border-slate-700 !text-white text-sm rounded-lg block w-full p-2.5 opacity-60 cursor-not-allowed"
                                       value="{{ auth()->user()->name }}" disabled>
                                <p class="mt-1 text-xs text-slate-500">Name changes require system audit</p>
                            </div>

                            <div>
                                <label class="block mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Email Address</label>
                                <div class="flex items-center gap-2">
                                    <input id="email" name="email" type="email"
                                           class="!bg-slate-900 !border-slate-700 !text-white text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5 [color-scheme:dark] @error('email', 'updateProfileInformation') border-red-500 @enderror"
                                           value="{{ old('email', auth()->user()->email) }}">
                                    @if(auth()->user()->email_verified_at)
                                        <span class="shrink-0 px-2 py-1 text-xs rounded-full border border-emerald-600/50 bg-emerald-500/10 text-emerald-300">Verified</span>
                                    @endif
                                </div>
                                @error('email', 'updateProfileInformation')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Role</label>
                                <input type="text"
                                       class="!bg-slate-900 !border-slate-700 !text-white text-sm rounded-lg block w-full p-2.5 opacity-60 cursor-not-allowed"
                                       value="System Administrator" disabled>
                            </div>

                            <div>
                                <label class="block mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Employee ID</label>
                                <input type="text"
                                       class="!bg-slate-900 !border-slate-700 !text-white text-sm rounded-lg block w-full p-2.5 opacity-60 cursor-not-allowed"
                                       value="{{ auth()->user()->employee_id ?? '—' }}" disabled>
                            </div>
                        </div>

                        @if(session('status') === 'profile-information-updated')
                            <p class="mt-4 text-xs text-emerald-400 font-medium">Profile updated successfully.</p>
                        @endif
                        @error('profile_photo', 'updateProfileInformation')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        <div class="mt-5 pt-4 border-t border-gray-700">
                            <button type="submit"
                                    class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg transition-colors duration-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Security Card --}}
        <div class="rounded-2xl border border-gray-700 bg-slate-900/40 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Security Settings
            </h2>

            {{-- Password Change --}}
            <div class="rounded-xl border border-gray-700 bg-slate-900/40 p-5">
                <h3 class="font-semibold text-white mb-4">Change Password</h3>
                <form method="POST" action="{{ route('user-password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="current_password" class="block mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Current Password</label>
                            <input id="current_password" name="current_password" type="password"
                                   class="!bg-slate-900 !border-slate-700 !text-white text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5 [color-scheme:dark] @error('current_password', 'updatePassword') border-red-500 @enderror"
                                   placeholder="••••••••" autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">New Password</label>
                            <input id="password" name="password" type="password"
                                   class="!bg-slate-900 !border-slate-700 !text-white text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5 [color-scheme:dark] @error('password', 'updatePassword') border-red-500 @enderror"
                                   placeholder="••••••••" autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Confirm Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                   class="!bg-slate-900 !border-slate-700 !text-white text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5 [color-scheme:dark]"
                                   placeholder="••••••••" autocomplete="new-password">
                        </div>
                    </div>

                    @if(session('status') === 'password-updated')
                        <p class="text-xs text-emerald-400 font-medium">Password updated successfully.</p>
                    @endif

                    <button type="submit"
                            class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Update Password
                    </button>
                </form>
            </div>


        </div>

    </section>
@endsection
