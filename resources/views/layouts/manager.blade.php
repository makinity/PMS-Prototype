<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Page</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (class_exists(\Livewire\Livewire::class))
        @livewireStyles
    @endif
    <style>
        @keyframes rise-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .menu-stagger > li {
            animation: rise-in 0.4s ease-out both;
        }

        .menu-stagger > li:nth-child(2) {
            animation-delay: 0.05s;
        }

        .menu-stagger > li:nth-child(3) {
            animation-delay: 0.1s;
        }

        .menu-stagger > li:nth-child(4) {
            animation-delay: 0.15s;
        }

        .menu-stagger > li:nth-child(5) {
            animation-delay: 0.2s;
        }

        .sidebar-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-radius: 0.75rem;
            padding: 0.5rem 0.75rem;
            color: #cbd5e1;
            transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: rgba(30, 41, 59, 0.7);
            color: #f8fafc;
        }

        .sidebar-link[aria-current="page"] {
            background-color: rgba(15, 23, 42, 0.9);
            color: #f8fafc;
            box-shadow: inset 0 0 0 1px rgba(52, 211, 153, 0.35);
        }

        .sidebar-link[aria-current="page"]::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 26px;
            border-radius: 9999px;
            background: linear-gradient(180deg, #34d399, #22d3ee);
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.55);
        }

        .sidebar-link .sidebar-icon {
            color: #94a3b8;
            transition: color 0.2s ease;
        }

        .sidebar-link:hover .sidebar-icon {
            color: #e2e8f0;
        }

        .sidebar-link[aria-current="page"] .sidebar-icon {
            color: #34d399;
        }

        select {
            color-scheme: dark;
        }

        select option {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .manager-filter-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #020617;
            color: #e2e8f0;
            border-color: #1e293b;
            padding-right: 2.5rem;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 20 20' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M6 8l4 4 4-4'/></svg>");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 0.75rem;
        }

        .manager-filter-select:focus {
            outline: none;
        }

        .manager-filter-select option {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        @media (prefers-reduced-motion: reduce) {
            .menu-stagger > li {
                animation: none;
            }
        }
    </style>
</head>

<body class="min-h-screen antialiased font-sans">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">Skip to content</a>
    <div class="min-h-screen">
        @php
            $isManagerDashboard = request()->routeIs('manager.dashboard');
        @endphp
        <!-- Top Navigation -->
        <nav class="fixed top-0 z-50 w-full border-b border-slate-800/80 bg-slate-950/90 text-slate-100 shadow-lg shadow-slate-950/30 backdrop-blur">
            <div class="flex items-center justify-between gap-4 px-4 py-3 lg:px-6">
                <div class="flex items-center gap-3">
                    <button type="button"
                        data-drawer-target="manager-sidebar"
                        data-drawer-toggle="manager-sidebar"
                        aria-controls="manager-sidebar"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-900/70 text-slate-200 shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400/30 sm:hidden">
                        <span class="sr-only">Open sidebar</span>
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <a href="#" class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 via-blue-600 to-emerald-500 text-white shadow-sm">
                            <i class="fa-solid fa-user-tie"></i>
                        </span>
                        <span class="hidden sm:block">
                            <span class="block text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">PMS</span>
                            <span class="block text-lg font-semibold leading-tight text-white">Manager Console</span>
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
                        <button type="button" id="manager-user-menu-button" data-dropdown-toggle="manager-user-menu" class="flex items-center gap-3 rounded-full border border-slate-800 bg-slate-900/70 px-2 py-1.5 text-left text-slate-100 shadow-sm transition hover:bg-slate-800">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 via-blue-600 to-emerald-500 text-xs font-semibold text-white shadow-sm">MG</span>
                            <span class="hidden sm:block">
                                <span class="block text-sm font-semibold text-white">Department Manager</span>
                                <span class="block text-xs text-slate-400">Manager</span>
                            </span>
                            <i class="fa-solid fa-chevron-down hidden text-xs text-slate-500 sm:block"></i>
                        </button>
                        <div id="manager-user-menu" class="z-50 hidden w-56 divide-y divide-slate-800 rounded-2xl bg-slate-900 shadow-lg ring-1 ring-slate-800">
                            <div class="px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Signed in as</p>
                                <p class="mt-1 text-sm font-semibold text-white">Department Manager</p>
                                <p class="text-xs text-slate-400">Manager</p>
                            </div>
                            <ul class="py-2 text-sm text-slate-200" aria-labelledby="manager-user-menu-button">
                                <li><a href="#" class="block px-4 py-2 transition hover:bg-slate-800">Profile</a></li>
                                <li><a href="#" class="block px-4 py-2 transition hover:bg-slate-800">Settings</a></li>
                                <li><a href="#" class="block px-4 py-2 transition hover:bg-slate-800">Support</a></li>
                            </ul>
                            <div class="py-2">
                                <a href="/" type="button" id="logoutBtn"class="flex w-full items-center gap-2 px-4 py-2 text-sm font-semibold text-rose-300 transition hover:bg-rose-500/10">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    <span>Sign out</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside id="manager-sidebar"
            class="fixed left-0 top-0 z-40 h-screen w-72 -translate-x-full border-r border-slate-800/80 bg-slate-950/95 pt-16 text-slate-100 shadow-sm transition-transform sm:translate-x-0"
            aria-label="Sidebar">

            <div class="flex h-full flex-col gap-6 overflow-y-auto px-4 pb-6">

                <nav class="space-y-6 text-sm mt-5 font-medium text-slate-200">
                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Overview</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="{{ route('manager.dashboard') }}" class="sidebar-link" @if($isManagerDashboard) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-solid fa-house"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Team Management</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="{{ route('manager.my-team') }}" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-users"></i>
                                    <span>My Team</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('manager.task-monitoring') }}" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-list-check"></i>
                                    <span>Task Monitoring</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Performance</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="{{ route('manager.productivity') }}" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-chart-line"></i>
                                    <span>Productivity Analytics</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('manager.bottleneck') }}" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-stopwatch"></i>
                                    <span>Bottleneck Analysis</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('manager.predictive-analytics') }}" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-chart-area"></i>
                                    <span>Predictive Analytics</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('manager.performance-rate') }}" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-star"></i>
                                    <span>Performance Ratings</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Reports</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="{{ route('manager.ipcr-reports') }}" class="sidebar-link">
                                    <i class="sidebar-icon fa-solid fa-file-lines"></i>
                                    <span>IPCR Reports</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Account</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="{{ route('manager.profile') }}" class="sidebar-link">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    @if (class_exists(\Livewire\Livewire::class))
        @livewireScripts
    @endif
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
