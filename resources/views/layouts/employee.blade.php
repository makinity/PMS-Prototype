<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Employee Dashboard | Performance Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (class_exists(\Livewire\Livewire::class))
        @livewireStyles
    @endif
    <style>
        html.sidebar-collapsed #employee-sidebar {
            width: 4.5rem;
            overflow: hidden;
        }

        html.sidebar-collapsed #main-wrapper {
            margin-left: 4.5rem !important;
        }

        html.sidebar-collapsed #employee-sidebar .sidebar-link span,
        html.sidebar-collapsed #employee-sidebar nav > div > p,
        html.sidebar-collapsed #nav-logo-link {
            display: none !important;
        }
    </style>
    <script>
    (function() {
        try {
            if (window.localStorage.getItem('pms:sidebar:employee:collapsed') === '1') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch (error) {}
    })();
    </script>
</head>
<body class="min-h-screen antialiased font-sans">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">Skip to content</a>
    <div class="min-h-screen">
        @php
            $isDashboard = request()->routeIs('employee.dashboard');
            $isMyTasks = request()->routeIs('employee.my-task');
            $isAccomplishmentSubmission = request()->routeIs('employee.accomplishment-submission');
            $isOutputRating = request()->routeIs('employee.ors');
            $isMpor = request()->routeIs('employee.mpor');
            $isIPCRTARGET = request()->routeIs('employee.ipcr-target');
            $isProfile = request()->routeIs('employee.profile');
        @endphp

        <!-- Sidebar -->
        <aside id="employee-sidebar" class="fixed left-0 top-0 z-40 h-screen w-72 -translate-x-full border-r border-gray-700 bg-slate-950 pt-16 text-slate-100 transition-[transform] duration-200 ease-out will-change-transform motion-reduce:transition-none sm:translate-x-0" aria-label="Sidebar">
            <div class="flex h-full flex-col gap-6 overflow-y-auto px-4 pb-6">


                <nav class="space-y-6 text-sm mt-5 font-medium text-slate-200">
                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Overview</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="{{ route('employee.dashboard') }}" class="sidebar-link" @if($isDashboard) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-solid fa-house"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">My Work</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            {{-- <li>
                                <a href="{{ route('employee.uwp') }}" class="sidebar-link" @if($isUnitWorkPlan) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-regular fa-clipboard"></i>
                                    <span>Unit Work Plan</span>
                                </a>
                            </li> --}}
                            <li>
                                <a href="{{ route('employee.my-task') }}" class="sidebar-link" @if($isMyTasks) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-solid fa-list-check"></i>
                                    <span>My Tasks</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('employee.accomplishment-submission') }}" class="sidebar-link" @if($isAccomplishmentSubmission) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-solid fa-file-arrow-up"></i>
                                    <span>Accomplishments</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('employee.submit-output') }}" class="sidebar-link" @if($isSubmitOutput) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-solid fa-upload"></i>
                                    <span>Submit Output</span>
                                </a>
                            </li> --}}
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">My Performance</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="{{ route('employee.ors') }}" class="sidebar-link" @if($isOutputRating) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-regular fa-clock-rotate-left"></i>
                                    <span>Output Rating Sheet</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('employee.mpor') }}" class="sidebar-link" @if($isMpor) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-solid fa-calendar"></i>
                                    <span>MPOR</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('employee.ipcr-target') }}" class="sidebar-link" @if($isIPCRTARGET) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-solid fa-bullseye"></i>
                                    <span>IPCR Target</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Account</p>
                        <ul class="mt-2 space-y-1 menu-stagger">
                            <li>
                                <a href="{{ route('employee.profile') }}" class="sidebar-link" @if($isProfile) aria-current="page" @endif>
                                    <i class="sidebar-icon fa-solid fa-user-gear"></i>
                                    <span>Profile &amp; Security</span>
                                </a>
                            </li>
                        </ul>
                </nav>
            </div>
        </aside>

        <!-- Top Navigation -->
        <nav class="fixed top-0 z-50 w-full bg-slate-950 text-slate-100" id="top-nav">
            <div id="sidebar-nav-divider" class="pointer-events-none absolute inset-y-0 w-px bg-gray-700" style="left: calc(18rem - 1px); display: none; transform: translateX(-18rem); transition: transform 200ms ease-out;"></div>
            <div class="flex items-center justify-between gap-4 px-4 py-3 lg:px-6">
                <div class="flex items-center gap-3">
                    <button type="button" id="sidebar-toggle-btn" aria-controls="employee-sidebar" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-700 bg-slate-900/40 text-slate-200 shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
                        <span class="sr-only">Toggle sidebar</span>
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <a href="#" class="flex items-center gap-3" id="nav-logo-link">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-600 via-blue-600 to-emerald-500 text-white shadow-sm" id="nav-logo-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </span>
                        <span class="hidden sm:block" id="nav-logo-text">
                            <span class="block text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">PMS</span>
                            <span class="block text-lg font-semibold leading-tight text-white">Employee Hub</span>
                        </span>
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    @if (class_exists(\Livewire\Livewire::class))
                        <livewire:notification-dropdown />
                    @else
                        <button type="button" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-700 bg-slate-900/40 text-slate-300 shadow-sm transition hover:bg-slate-800">
                            <span class="sr-only">View notifications</span>
                            <i class="fa-regular fa-bell"></i>
                            <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-rose-500"></span>
                        </button>
                    @endif
                    <div class="relative">
                        <button type="button" id="employee-user-menu-button" data-dropdown-toggle="employee-user-menu" class="flex items-center gap-3 rounded-full border border-gray-700 bg-slate-900/40 px-2 py-1.5 text-left text-slate-100 shadow-sm transition hover:bg-slate-800">
                            @include('partials.user-avatar', ['user' => Auth::user()])
                            <span class="hidden sm:block">
                                <span class="block text-sm font-semibold text-white">{{ Auth::user()->name }}</span>
                                <span class="block text-xs text-slate-400">{{ Auth::user()->office->name }}</span>
                            </span>
                            <i class="fa-solid fa-chevron-down hidden text-xs text-slate-500 sm:block"></i>
                        </button>
                        <div id="employee-user-menu" class="z-50 hidden w-56 divide-y divide-slate-800 rounded-2xl bg-slate-900 shadow-lg ring-1 ring-slate-800">
                            <div class="px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Signed in as</p>
                                <p class="mt-1 text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-400">{{ Auth::user()->position }}</p>
                            </div>
                            <ul class="py-2 text-sm text-slate-200" aria-labelledby="employee-user-menu-button">
                                <li><a href="{{ route('employee.profile') }}" class="block px-4 py-2 transition hover:bg-slate-800">My profile</a></li>
                                <li><a href="#" class="block px-4 py-2 transition hover:bg-slate-800">Settings</a></li>
                                <li><a href="#" class="block px-4 py-2 transition hover:bg-slate-800">Support</a></li>
                            </ul>
                            <div class="py-2">
                                <a href="{{ route('logout') }}" data-logout-url="{{ route('logout') }}" id="logoutBtn" class="flex w-full items-center gap-2 px-4 py-2 text-sm font-semibold text-rose-300 transition hover:bg-rose-500/10">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    <span>Sign out</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>



        <!-- Main Content -->
        <div id="main-wrapper" class="pt-2 sm:ml-72">
            <main id="main-content" class="px-3 pb-12 pt-6 lg:px-5">
                @php
                    $breadcrumbMap = [
                        'employee.dashboard' => 'Dashboard',
                        'employee.my-task' => 'My Tasks',
                        'employee.accomplishment-submission' => 'Accomplishments',
                        'employee.ors' => 'Output Rating Sheet',
                        'employee.mpor' => 'MPOR',
                        'employee.ipcr-target' => 'IPCR Target',
                        'employee.profile' => 'Profile & Security',
                    ];
                    $currentBreadcrumb = collect($breadcrumbMap)->first(fn($label, $route) => request()->routeIs($route . '*')) ?? 'Page';
                @endphp
                <nav class="mb-4 flex items-center gap-2 text-sm text-slate-400">
                    <i class="fa-solid fa-house text-xs text-slate-500"></i>
                    <span class="text-slate-600">/</span>
                    <span class="text-slate-200">{{ $currentBreadcrumb }}</span>
                </nav>
                <div class="mx-auto w-full max-w-none">
                    @yield('main-content')
                </div>
            </main>
        </div>
    </div>

    <!-- Flowbite JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    @if (class_exists(\Livewire\Livewire::class))
        @livewireScripts
    @endif
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    @include('partials.auth-snackbar')
    @stack('scripts')
    <script>
    (function() {
        const sidebar = document.getElementById('employee-sidebar');
        const navDivider = document.getElementById('sidebar-nav-divider');
        const mainWrapper = document.getElementById('main-wrapper');
        const toggleBtn = document.getElementById('sidebar-toggle-btn');
        const logoLink = document.getElementById('nav-logo-link');
        const logoText = document.getElementById('nav-logo-text');
        const logoIcon = document.getElementById('nav-logo-icon');
        let collapsed = false;
        const SIDEBAR_STATE_KEY = 'pms:sidebar:employee:collapsed';
        const MOBILE_SIDEBAR_WIDTH = '18rem';
        const DIVIDER_OFFSET_PX = -1;

        function persistCollapsedState() {
            document.documentElement.classList.toggle('sidebar-collapsed', collapsed);
            try {
                window.localStorage.setItem(SIDEBAR_STATE_KEY, collapsed ? '1' : '0');
            } catch (error) {}
        }

        function readCollapsedState() {
            try {
                return window.localStorage.getItem(SIDEBAR_STATE_KEY) === '1';
            } catch (error) {
                return false;
            }
        }

        function setDividerLeft(value) {
            if (!navDivider) return;
            navDivider.style.left = `calc(${value} + ${DIVIDER_OFFSET_PX}px)`;
        }

        function setMobileDividerOpenState(isOpen, animate = true) {
            if (!navDivider) return;
            navDivider.style.display = 'block';
            setDividerLeft(MOBILE_SIDEBAR_WIDTH);
            if (!animate) {
                navDivider.style.transition = 'none';
            } else {
                navDivider.style.transition = 'transform 200ms ease-out';
            }
            navDivider.style.transform = isOpen ? 'translateX(0)' : `translateX(-${MOBILE_SIDEBAR_WIDTH})`;
            if (!animate) {
                navDivider.offsetHeight;
                navDivider.style.transition = 'transform 200ms ease-out';
            }
        }

        function isDesktop() { return window.innerWidth >= 640; }

        function updateNavDivider() {
            if (!navDivider) return;
            const sidebarHidden = !isDesktop() && sidebar?.classList.contains('-translate-x-full');
            if (sidebarHidden) {
                navDivider.style.display = 'none';
                navDivider.style.transform = `translateX(-${MOBILE_SIDEBAR_WIDTH})`;
                return;
            }
            navDivider.style.display = 'block';
            if (isDesktop()) {
                setDividerLeft(!collapsed ? '18rem' : '4.5rem');
                navDivider.style.transform = 'translateX(0)';
            } else {
                setDividerLeft(MOBILE_SIDEBAR_WIDTH);
                navDivider.style.transform = 'translateX(0)';
            }
        }

        function updateMobileLogoVisibility() {
            if (!logoLink || !logoText || !logoIcon || !sidebar) return;
            if (isDesktop()) {
                if (collapsed) {
                    logoLink.style.display = 'none';
                } else {
                    logoLink.style.display = 'flex';
                    logoIcon.style.display = '';
                    logoText.style.display = '';
                }
                return;
            }

            const sidebarHidden = sidebar.classList.contains('-translate-x-full');
            if (sidebarHidden) {
                logoLink.style.display = 'none';
                return;
            }

            logoLink.style.display = 'flex';
            logoIcon.style.display = '';
            logoText.style.display = 'block';
        }

        function resetCollapse() {
            sidebar.style.width = '';
            sidebar.style.overflow = '';
            mainWrapper.style.marginLeft = '';
            sidebar.querySelectorAll('.sidebar-link span, nav > div > p').forEach(el => el.style.display = '');
            if (logoText) logoText.style.display = '';
            if (logoIcon) logoIcon.style.display = '';
            collapsed = false;
            persistCollapsedState();
            updateNavDivider();
            updateMobileLogoVisibility();
        }

        function applyDesktopCollapseState(nextCollapsed) {
            collapsed = Boolean(nextCollapsed);
            if (collapsed) {
                sidebar.style.width = '4.5rem';
                sidebar.style.overflow = 'hidden';
                mainWrapper.style.marginLeft = '4.5rem';
                sidebar.querySelectorAll('.sidebar-link span, nav > div > p').forEach(el => el.style.display = 'none');
                if (logoText) logoText.style.display = 'none';
                if (logoIcon) logoIcon.style.display = 'none';
            } else {
                sidebar.style.width = '';
                sidebar.style.overflow = '';
                mainWrapper.style.marginLeft = '';
                sidebar.querySelectorAll('.sidebar-link span, nav > div > p').forEach(el => el.style.display = '');
                if (logoText) logoText.style.display = '';
                if (logoIcon) logoIcon.style.display = '';
            }
            persistCollapsedState();
            updateNavDivider();
            updateMobileLogoVisibility();
        }

        toggleBtn?.addEventListener('click', () => {
            if (!isDesktop()) {
                // Mobile: mirror desktop toggle flow (toggle, then update UI state)
                // Ensure mobile drawer always uses full sidebar width/content,
                // even if desktop was previously in collapsed mode.
                sidebar.style.width = '';
                sidebar.style.overflow = '';
                mainWrapper.style.marginLeft = '';
                sidebar.querySelectorAll('.sidebar-link span, nav > div > p').forEach(el => el.style.display = '');
                collapsed = false;
                persistCollapsedState();
                const isOpening = sidebar.classList.contains('-translate-x-full');
                if (isOpening) {
                    setMobileDividerOpenState(false, false);
                    sidebar.classList.remove('-translate-x-full');
                    requestAnimationFrame(() => setMobileDividerOpenState(true, true));
                } else {
                    setMobileDividerOpenState(true, false);
                    sidebar.classList.add('-translate-x-full');
                    requestAnimationFrame(() => setMobileDividerOpenState(false, true));
                }
                updateMobileLogoVisibility();
                return;
            }
            // Desktop: collapse/expand
            applyDesktopCollapseState(!collapsed);
        });

        // On resize: if going to mobile, reset collapse
        window.addEventListener('resize', () => {
            if (!isDesktop() && collapsed) {
                resetCollapse();
                sidebar.classList.add('-translate-x-full');
            } else if (isDesktop()) {
                applyDesktopCollapseState(readCollapsedState());
            }
            updateNavDivider();
            updateMobileLogoVisibility();
        });

        sidebar?.addEventListener('transitionend', () => {
            updateNavDivider();
        });
        if (sidebar) {
            const observer = new MutationObserver(updateNavDivider);
            observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        }

        if (isDesktop()) {
            applyDesktopCollapseState(readCollapsedState());
        }
        updateNavDivider();
        updateMobileLogoVisibility();
    })();
    </script>
</body>
</html>
