@extends('layouts.supervisor')

@section('main-content')
    <section class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-white">Profile &amp; Security</h1>
            <p class="mt-1 text-sm text-gray-400">
                Manage your account information and security settings.
            </p>
        </div>

        {{-- Profile Card --}}
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 space-y-6">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile Information
                </h2>

                @if(auth()->user()->email_verified_at)
                    <span class="px-3 py-1 text-xs rounded bg-emerald-900 text-emerald-300 border border-emerald-800">VERIFIED</span>
                @else

                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Profile Picture --}}
                <div class="lg:col-span-1 flex flex-col items-center">
                    <div class="relative mb-4">
                        @if(auth()->user()->profile_photo_path)
                            <img class="w-32 h-32 rounded-full border-4 border-gray-700 object-cover"
                                 src="{{ auth()->user()->profile_photo_url }}"
                                 alt="Profile picture">
                        @else
                            <img class="w-32 h-32 rounded-full border-4 border-gray-700 object-cover"
                                 src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=1e40af&color=fff&size=128"
                                 alt="Profile picture">
                        @endif

                        <button type="button"
                                onclick="document.getElementById('profile_photo').click()"
                                class="absolute bottom-2 right-2 p-2 bg-blue-600 hover:bg-blue-700 rounded-full text-white transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-400">Max size: 5MB</p>
                    <p class="text-xs text-gray-500">JPG, PNG, or GIF</p>
                </div>

                {{-- Profile Form --}}
                <div class="lg:col-span-3 space-y-6">
                    <form method="POST" action="{{ route('user-profile-information.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input id="profile_photo" name="profile_photo" type="file" accept="image/*" class="hidden" onchange="this.form.requestSubmit()">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-white">Full Name</label>
                                <input type="text"
                                       class="!bg-gray-700 !border-gray-600 !text-white text-sm rounded-lg block w-full p-2.5 opacity-60 cursor-not-allowed"
                                       value="{{ auth()->user()->name }}" disabled>
                                <p class="mt-1 text-xs text-gray-400">Name updates require HR approval</p>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-white">Email Address</label>
                                <div class="flex items-center gap-2">
                                    <input id="email" name="email" type="email"
                                           class="!bg-gray-700 !border-gray-600 !text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 [color-scheme:dark] @error('email', 'updateProfileInformation') border-red-500 @enderror"
                                           value="{{ old('email', auth()->user()->email) }}">
                                    @if(auth()->user()->email_verified_at)
                                        <span class="shrink-0 px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300 border border-emerald-800">Verified</span>
                                    @endif
                                </div>
                                @error('email', 'updateProfileInformation')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-400">Used for notifications and login</p>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-white">Position</label>
                                <input type="text"
                                       class="!bg-gray-700 !border-gray-600 !text-white text-sm rounded-lg block w-full p-2.5 opacity-60 cursor-not-allowed"
                                       value="{{ ucfirst(auth()->user()->role) }}" disabled>
                                <p class="mt-1 text-xs text-gray-400">Updated by HR</p>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-white">Employee ID</label>
                                <input type="text"
                                       class="!bg-gray-700 !border-gray-600 !text-white text-sm rounded-lg block w-full p-2.5 opacity-60 cursor-not-allowed"
                                       value="{{ auth()->user()->employee_id ?? '—' }}" disabled>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-white">Department</label>
                                <input type="text"
                                       class="!bg-gray-700 !border-gray-600 !text-white text-sm rounded-lg block w-full p-2.5 opacity-60 cursor-not-allowed"
                                       value="{{ auth()->user()->office?->name ?? '—' }}" disabled>
                            </div>
                        </div>

                        @if(session('status') === 'profile-information-updated')
                            <p class="text-xs text-emerald-400 font-medium mt-4">Profile updated successfully.</p>
                        @endif
                        @error('profile_photo', 'updateProfileInformation')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        <div class="pt-6 border-t border-gray-700 flex justify-start">
                            <button type="submit"
                                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Security Card --}}
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-6 space-y-6">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Security Settings
            </h2>

            {{-- Password Change --}}
            <div class="bg-transparent border border-gray-600 rounded-lg p-5">
                <h3 class="font-medium text-white mb-4">Change Password</h3>
                <form method="POST" action="{{ route('user-password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block mb-2 text-sm font-medium text-white">Current Password</label>
                        <input id="current_password" name="current_password" type="password"
                               class="!bg-gray-700 !border-gray-600 !text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 [color-scheme:dark] @error('current_password', 'updatePassword') border-red-500 @enderror"
                               placeholder="Enter current password" autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-white">New Password</label>
                        <input id="password" name="password" type="password"
                               class="!bg-gray-700 !border-gray-600 !text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 [color-scheme:dark] @error('password', 'updatePassword') border-red-500 @enderror"
                               placeholder="Enter new password" autocomplete="new-password">
                        @error('password', 'updatePassword')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">Minimum 8 characters with letters and numbers</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block mb-2 text-sm font-medium text-white">Confirm New Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                               class="!bg-gray-700 !border-gray-600 !text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 [color-scheme:dark]"
                               placeholder="Confirm new password" autocomplete="new-password">
                    </div>

                    @if(session('status') === 'password-updated')
                        <p class="text-xs text-emerald-400 font-medium">Password updated successfully.</p>
                    @endif

                    <div class="pt-2">
                        <button type="submit"
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </section>
@endsection
