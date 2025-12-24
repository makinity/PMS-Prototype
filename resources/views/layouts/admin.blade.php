<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>System Administrator Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (class_exists(\Livewire\Livewire::class))
        @livewireStyles
    @endif
</head>
<body class="min-h-screen antialiased font-sans">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">Skip to content</a>
    <div class="min-h-screen">
        @php
            $isAdminDashboard = request()->routeIs('admin.dashboard');
        @endphp
        <!-- Top Navigation -->
        <nav class="fixed top-0 z-50 w-full border-b border-slate-800/80 bg-slate-950/90 text-slate-100 shadow-lg shadow-slate-950/30 backdrop-blur">
            <div class="flex items-center justify-between gap-4 px-4 py-3 lg:px-6">
                <div class="flex items-center gap-3">
                    <button type="button" data-drawer-target="admin-sidebar" data-drawer-toggle="admin-sidebar" aria-controls="admin-sidebar" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-900/70 text-slate-200 shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 sm:hidden">
                        <span class="sr-only">Open sidebar</span>
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <a href="#" class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-600 via-blue-600 to-emerald-500 text-white shadow-sm">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <span class="hidden sm:block">
                            <span class="block text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">PMS</span>
                            <span class="block text-lg font-semibold leading-tight text-white">Admin Console</span>
                        </span>
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    @if (class_exists(\Livewire\Livewire::class))
                        <livewire:notification-dropdown />
                    @else
                        <button type="button" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-900/70 text-slate-300 shadow-sm transition hover:bg-slate-800">
                            <span class="sr-only">View notifications</span>
                            <i class="fa-regular fa-bell"></i>
                            <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-rose-500"></span>
                        </button>
                    @endif
                    <div class="relative">
                        <button type="button" id="admin-user-menu-button" data-dropdown-toggle="admin-user-menu" class="flex items-center gap-3 rounded-full border border-slate-800 bg-slate-900/70 px-2 py-1.5 text-left text-slate-100 shadow-sm transition hover:bg-slate-800">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-900">SA</span>
                            <span class="hidden sm:block">
                                <span class="block text-sm font-semibold text-white">PMS Administrator</span>
                                <span class="block text-xs text-slate-400">System Admin</span>
                            </span>
                            <i class="fa-solid fa-chevron-down hidden text-xs text-slate-500 sm:block"></i>
                        </button>
                        <div id="admin-user-menu" class="z-50 hidden w-56 divide-y divide-slate-800 rounded-2xl bg-slate-900 shadow-lg ring-1 ring-slate-800">
                            <div class="px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Signed in as</p>
                                <p class="mt-1 text-sm font-semibold text-white">PMS Administrator</p>
                                <p class="text-xs text-slate-400">System Admin</p>
                            </div>
                            <ul class="py-2 text-sm text-slate-200" aria-labelledby="admin-user-menu-button">
                                <li><a href="#" class="block px-4 py-2 transition hover:bg-slate-800">Profile</a></li>
                                <li><a href="#" class="block px-4 py-2 transition hover:bg-slate-800">Settings</a></li>
                                <li><a href="#" class="block px-4 py-2 transition hover:bg-slate-800">Support</a></li>
                            </ul>
                            <div class="py-2">
                                <button type="button" class="flex w-full items-center gap-2 px-4 py-2 text-sm font-semibold text-rose-300 transition hover:bg-rose-500/10">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    <span>Sign out</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside id="admin-sidebar" class="fixed left-0 top-0 z-40 h-screen w-72 -translate-x-full border-r border-slate-800/80 bg-slate-950/95 pt-16 text-slate-100 shadow-sm transition-transform sm:translate-x-0" aria-label="Sidebar">
            <div class="flex h-full flex-col gap-6 overflow-y-auto px-4 pb-6">

                <nav class="space-y-6 text-sm mt-5 font-medium text-slate-200">
                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Overview</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="sidebar-link" @if($isAdminDashboard) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-solid fa-house"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">User Management</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="#" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-users"></i>
                                    <span>Users</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-user-shield"></i>
                                    <span>Roles &amp; Permissions</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">System Setup</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="#" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-list-check"></i>
                                    <span>Task Configuration</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-chart-line"></i>
                                    <span>Performance Metrics</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-gears"></i>
                                    <span>System Settings</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Data &amp; Integration</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="#" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-link"></i>
                                    <span>HRIS Integration</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-file-export"></i>
                                    <span>Data Export</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Audit &amp; Logs</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="#" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-clipboard-list"></i>
                                    <span>Audit Trail</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-file-lines"></i>
                                    <span>System Logs</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Account</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="#" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-user-gear"></i>
                                    <span>Profile &amp; Security</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="pt-2 sm:ml-72">
            <main id="main-content" class="px-4 pb-12 pt-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- Flowbite JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    @if (class_exists(\Livewire\Livewire::class))
        @livewireScripts
    @endif
    <!-- Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
