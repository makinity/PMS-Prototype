@extends('layouts.supervisor')

@section('main-content')
    <section class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-white">Profile & Security</h1>
            <p class="text-sm text-gray-400 mt-1">
                Manage your account information and security settings.
            </p>
        </div>

        {{-- Profile Card --}}
        <div class="bg-transparent border border-gray-700 rounded-xl p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile Information
                </h2>
                @if(auth()->user()->email_verified_at)
                    <span class="px-3 py-1 text-xs rounded bg-emerald-900 text-emerald-300 border border-emerald-800">VERIFIED</span>
                @else
                    <span class="px-3 py-1 text-xs rounded bg-yellow-900 text-yellow-300 border border-yellow-800">UNVERIFIED</span>
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
                                data-manager-action
                                data-action-title="Update profile photo"
                                data-action-message="Upload a new profile image (JPG/PNG, max 5MB)."
                                data-action-confirm="Choose file"
                                class="absolute bottom-2 right-2 p-2 bg-blue-600 hover:bg-blue-700 rounded-full text-white transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-400">Max size: 5MB</p>
                    <p class="text-xs text-gray-500">JPG, PNG, or GIF</p>
                </div>

                {{-- Profile Form --}}
                <div class="lg:col-span-3 space-y-6">
                    <form method="POST" action="{{ route('user-profile-information.update') }}">
                        @csrf
                        @method('PUT')
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
                                       value="{{ auth()->user()->position ?? '—' }}" disabled>
                                <p class="mt-1 text-xs text-gray-400">Updated by HR</p>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-white">Employee ID</label>
                                <input type="text"
                                       class="!bg-gray-700 !border-gray-600 !text-white text-sm rounded-lg block w-full p-2.5 opacity-60 cursor-not-allowed"
                                       value="{{ auth()->user()->employee_id ?? '—' }}" disabled>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-white">Department</label>
                                <input type="text"
                                       class="!bg-gray-700 !border-gray-600 !text-white text-sm rounded-lg block w-full p-2.5 opacity-60 cursor-not-allowed"
                                       value="{{ auth()->user()->office?->name ?? '—' }}" disabled>
                            </div>
                        </div>

                        @if(session('status') === 'profile-information-updated')
                            <p class="mt-3 text-xs text-emerald-400 font-medium">Profile updated successfully.</p>
                        @endif

                        <div class="pt-4 border-t border-gray-700">
                            <button type="submit"
                                    class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Security Card --}}
        <div class="bg-transparent border border-gray-700 rounded-xl p-6 space-y-6">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
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
                                class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- Security Features --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Two-Factor Authentication --}}
                <div class="bg-transparent border border-gray-600 rounded-lg p-5">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium text-white">Two-Factor Authentication</h3>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <span class="px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300">Enabled</span>
                    </div>
                    <p class="text-sm text-gray-400 mb-4">Add an extra layer of security to your account</p>
                    {{-- DUMMY_DATA: replace with dynamic value --}}
                    <div class="flex items-center text-sm text-gray-400 mb-4">
                        <svg class="w-4 h-4 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Last used: Today, 2:30 PM
                    </div>
                    <button type="button"
                            data-manager-action
                            data-action-title="Manage 2FA"
                            data-action-message="Review devices and update two-factor authentication settings."
                            data-action-confirm="Open 2FA"
                            class="w-full px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        Manage 2FA
                    </button>
                </div>

                {{-- Session Management --}}
                <div class="bg-transparent border border-gray-600 rounded-lg p-5">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-medium text-white">Active Sessions</h3>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <span class="px-2 py-1 text-xs rounded bg-blue-900 text-blue-300">2 Active</span>
                    </div>
                    <div class="space-y-3 mb-4">
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></div>
                            <div>
                                <p class="text-sm text-white">Chrome • Windows</p>
                                <p class="text-xs text-gray-400">Current session • Manila, PH</p>
                            </div>
                        </div>
                        {{-- DUMMY_DATA: replace with dynamic value --}}
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></div>
                            <div>
                                <p class="text-sm text-white">Safari • iOS</p>
                                <p class="text-xs text-gray-400">3 hours ago • Quezon City, PH</p>
                            </div>
                        </div>
                    </div>
                    <button type="button"
                            data-manager-action
                            data-action-title="View all sessions"
                            data-action-message="Open the full session list and sign out other devices if needed."
                            data-action-confirm="View sessions"
                            class="w-full px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        View All Sessions
                    </button>
                </div>
            </div>

            {{-- Login History --}}
            <div class="bg-transparent border border-gray-600 rounded-lg p-5">
                <h3 class="font-medium text-white mb-4">Recent Login History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-300">Date & Time</th>
                                <th class="px-4 py-2 text-left text-gray-300">Device</th>
                                <th class="px-4 py-2 text-left text-gray-300">Location</th>
                                <th class="px-4 py-2 text-left text-gray-300">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            {{-- DUMMY_DATA: replace with dynamic value --}}
                            <tr>
                                <td class="px-4 py-2 text-gray-300">Aug 13, 2025 • 14:30</td>
                                <td class="px-4 py-2 text-gray-300">Chrome • Windows</td>
                                <td class="px-4 py-2 text-gray-300">Manila, PH</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300">Success</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-gray-300">Aug 13, 2025 • 09:15</td>
                                <td class="px-4 py-2 text-gray-300">Safari • iOS</td>
                                <td class="px-4 py-2 text-gray-300">Quezon City, PH</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded bg-emerald-900 text-emerald-300">Success</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Account Actions --}}
        <div class="bg-transparent border border-gray-700 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.342 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Account Actions
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button type="button"
                        data-manager-loading="true"
                        data-loading-text="Exporting..."
                        class="p-4 border border-gray-600 rounded-lg hover:bg-gray-750 transition-colors duration-200 text-left">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-medium text-white mb-1"><span data-button-label>Export Account Data</span></h3>
                            <p class="text-sm text-gray-400">Download all your personal data in ZIP format</p>
                        </div>
                        <span data-button-spinner class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    </div>
                </button>
                <button type="button"
                        data-manager-action
                        data-action-title="Deactivate account"
                        data-action-message="Temporarily disable this account. You can reactivate through HR or admin support."
                        data-action-confirm="Deactivate"
                        class="p-4 border border-red-600/50 rounded-lg hover:bg-red-900/20 transition-colors duration-200 text-left">
                    <h3 class="font-medium text-white mb-1">Deactivate Account</h3>
                    <p class="text-sm text-gray-400">Temporarily disable your account</p>
                </button>
            </div>
        </div>

    </section>

    <div id="manager-action-modal" role="dialog" aria-modal="true" class="fixed inset-0 z-[70] hidden flex items-center justify-center bg-black/60 px-4 py-6">
        <div class="w-full max-w-md rounded-2xl border border-gray-700 bg-gray-900 p-5 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 id="manager-action-title" class="text-lg font-semibold text-white">Action</h2>
                    <p id="manager-action-body" class="mt-1 text-sm text-gray-400">Prototype action preview.</p>
                </div>
                <button type="button" data-manager-modal-close class="text-gray-400 hover:text-white">x</button>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-manager-modal-close class="rounded-lg border border-gray-600 px-4 py-2 text-xs text-gray-300 hover:bg-gray-800">Close</button>
                <button type="button" id="manager-action-confirm" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-500">
                    <span data-button-label>Proceed</span>
                    <span data-button-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('manager-action-modal');
        const title = document.getElementById('manager-action-title');
        const body = document.getElementById('manager-action-body');
        const confirmBtn = document.getElementById('manager-action-confirm');

        if (!modal || !title || !body || !confirmBtn) {
            return;
        }

        function setButtonLoading(button, isLoading, loadingText) {
            if (!button) {
                return;
            }
            const label = button.querySelector('[data-button-label]');
            const spinner = button.querySelector('[data-button-spinner]');
            if (label && !button.dataset.originalLabel) {
                button.dataset.originalLabel = label.textContent.trim();
            }

            if (isLoading) {
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-wait');
                if (spinner) {
                    spinner.classList.remove('hidden');
                }
                if (label && loadingText) {
                    label.textContent = loadingText;
                }
            } else {
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-wait');
                if (spinner) {
                    spinner.classList.add('hidden');
                }
                if (label && button.dataset.originalLabel) {
                    label.textContent = button.dataset.originalLabel;
                }
            }
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            setButtonLoading(confirmBtn, false);
        }

        function openModal(trigger) {
            const label = confirmBtn.querySelector('[data-button-label]');
            title.textContent = trigger.dataset.actionTitle || 'Action';
            body.textContent = trigger.dataset.actionMessage || 'Prototype action preview.';
            if (label) {
                label.textContent = trigger.dataset.actionConfirm || 'Proceed';
                confirmBtn.dataset.originalLabel = label.textContent.trim();
            }
            confirmBtn.dataset.loadingText = trigger.dataset.actionLoading || 'Working...';
            setButtonLoading(confirmBtn, false);
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        document.querySelectorAll('[data-manager-action]').forEach((button) => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                openModal(button);
            });
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        modal.querySelectorAll('[data-manager-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        confirmBtn.addEventListener('click', function () {
            setButtonLoading(confirmBtn, true, confirmBtn.dataset.loadingText || 'Working...');
            setTimeout(() => {
                setButtonLoading(confirmBtn, false);
                closeModal();
            }, 1200);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        document.querySelectorAll('[data-manager-loading="true"]').forEach((button) => {
            button.addEventListener('click', function () {
                if (button.dataset.loadingActive === 'true') {
                    return;
                }
                button.dataset.loadingActive = 'true';
                setButtonLoading(button, true, button.dataset.loadingText || 'Loading...');

                const duration = Number.parseInt(button.dataset.loadingDuration || '1200', 10);
                if (!Number.isNaN(duration)) {
                    setTimeout(() => {
                        setButtonLoading(button, false);
                        button.dataset.loadingActive = 'false';
                    }, duration);
                }
            });
        });
    });
    </script>
    @endpush
@endsection
