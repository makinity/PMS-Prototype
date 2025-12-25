<x-layouts.employee>
    <section class="space-y-6">

        <div>
            <h1 class="text-xl font-semibold">Profile & Security</h1>
            <p class="text-sm text-gray-500">
                Manage your account information and security settings.
            </p>
        </div>

        {{-- Profile --}}
        <div class="border rounded-lg p-4 space-y-4">
            <h2 class="font-semibold">Profile Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-medium">Full Name</label>
                    <input type="text" class="w-full mt-1 border rounded px-3 py-2" disabled
                           value="Juan Dela Cruz">
                </div>

                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" class="w-full mt-1 border rounded px-3 py-2"
                           value="juan.delacruz@email.com">
                </div>

                <div>
                    <label class="text-sm font-medium">Position</label>
                    <input type="text" class="w-full mt-1 border rounded px-3 py-2" disabled
                           value="Administrative Assistant I">
                </div>
            </div>
        </div>

        {{-- Security --}}
        <div class="border rounded-lg p-4 space-y-4">
            <h2 class="font-semibold">Security</h2>

            <div>
                <label class="text-sm font-medium">Change Password</label>
                <input type="password"
                       class="w-full mt-1 border rounded px-3 py-2"
                       placeholder="New password">
            </div>

            <div class="flex justify-end">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Update Security
                </button>
            </div>
        </div>

    </section>
</x-layouts.employee>
