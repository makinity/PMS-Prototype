<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Performance Management System | HR Ecosystem</title>
    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900">
    <!-- Sticky Navigation -->
    <nav id="mainNav" class="sticky-nav">
        <div class="max-w-screen-xl mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-bold text-white whitespace-nowrap">Performance Management</span>
                        <span class="bg-purple-900 text-purple-200 text-xs font-medium px-2 py-0.5 rounded mt-1">HR Ecosystem</span>
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <button id="mobileMenuBtn" class="mobile-menu-btn md:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#overview" class="text-gray-300 hover:text-white transition-colors">Overview</a>
                    <a href="#systems" class="text-gray-300 hover:text-white transition-colors">HR Systems</a>
                    <a href="#features" class="text-gray-300 hover:text-white transition-colors">Features</a>
                </div>
                
                <div class="hidden md:flex items-center space-x-4">
                    <button id="loginModalBtn" class="border border-gray-600 text-gray-300 hover:border-purple-500 hover:text-purple-300 px-4 py-2 rounded-lg font-medium transition-all">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                    <button id="accountActivationModalBtn" class="btn-gradient px-5 py-2.5 rounded-lg font-medium">
                        <i class="fas fa-user-plus mr-2"></i>Activate Account
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="mobile-menu">
                <a href="#overview" class="mobile-menu-link">Overview</a>
                <a href="#systems" class="mobile-menu-link">HR Systems</a>
                <a href="#features" class="mobile-menu-link">Features</a>
                <div class="pt-6 mt-6 border-t border-gray-700 space-y-3">
                    <button id="loginModalBtnMobile" class="w-full border border-gray-600 text-gray-300 hover:border-purple-500 hover:text-purple-300 px-4 py-3 rounded-lg font-medium transition-all">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                    <button id="accountActivationModalBtnMobile" class="w-full btn-gradient px-5 py-3 rounded-lg font-medium">
                        <i class="fas fa-user-plus mr-2"></i>Activate Account
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient pt-16 pb-12 md:pt-20 md:pb-16">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="text-center mb-8 md:mb-12 fade-in-up">
                <div class="pms-badge mb-4 md:mb-6">
                    <span class="bg-purple-600 text-white text-xs font-bold px-2 py-1 rounded mr-2">Core HR System</span>
                    <span class="text-sm">Part of the Integrated HR Ecosystem</span>
                </div>
                
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6">
                    Centralize Employee <span class="highlight-pms">Performance</span> Tracking
                </h1>
                
                <p class="text-lg md:text-xl text-gray-300 mb-6 md:mb-8 max-w-3xl mx-auto leading-relaxed">
                    The <span class="font-semibold text-purple-300">Performance Management System (PMS)</span> is the heart of your HR ecosystem, 
                    receiving data from Hiring Management, evaluating employee performance, and intelligently routing 
                    employees to development or recognition programs.
                </p>
            </div>
            
            <div class="text-center fade-in-up">
                <button id="accountActivationModalBtn2" class="btn-gradient px-6 py-3 md:px-8 md:py-4 rounded-xl font-bold text-base md:text-lg w-full md:w-auto">
                    <i class="fas fa-user-check mr-2 md:mr-3"></i> Activate Your PMS Account
                </button>
                <p class="mt-3 md:mt-4 text-gray-400 text-sm">Access granted to employees after hiring process completion</p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="overview" class="py-12 md:py-16 bg-gray-900/50">
        <div class="max-w-screen-xl mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-8 md:mb-12 fade-in-up">Why <span class="highlight-pms">PMS</span> is Essential</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                <div class="text-center fade-in-up">
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-br from-purple-600/20 to-blue-600/20 flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <i class="fas fa-link text-2xl md:text-3xl text-purple-400"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold mb-2 md:mb-3">Seamless HR Integration</h3>
                    <p class="text-gray-400 text-sm md:text-base">Direct integration with Hiring Management, L&D, and R&R systems creates a cohesive employee lifecycle management.</p>
                </div>
                
                <div class="text-center fade-in-up">
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-br from-purple-600/20 to-blue-600/20 flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <i class="fas fa-brain text-2xl md:text-3xl text-blue-400"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold mb-2 md:mb-3">Intelligent Routing</h3>
                    <p class="text-gray-400 text-sm md:text-base">Automatically directs employees to appropriate development or recognition programs based on performance analysis.</p>
                </div>
                
                <div class="text-center fade-in-up">
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-br from-purple-600/20 to-blue-600/20 flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <i class="fas fa-chart-network text-2xl md:text-3xl text-indigo-400"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold mb-2 md:mb-3">Data-Driven Decisions</h3>
                    <p class="text-gray-400 text-sm md:text-base">Provides actionable insights for managers and HR to make informed talent development decisions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- HR Systems Integration Section -->
    <section id="systems" class="py-12 md:py-16">
        <div class="max-w-screen-xl mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-3 md:mb-4 fade-in-up">Integrated <span class="highlight-pms">HR Ecosystem</span></h2>
            <p class="text-lg md:text-xl text-gray-400 text-center mb-8 md:mb-12 max-w-3xl mx-auto fade-in-up">
                Performance Management System works seamlessly with other HR systems to provide complete employee lifecycle management.
            </p>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- HMS Card -->
                <div class="system-card hms-card fade-in-up">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-lg bg-emerald-500/20 flex items-center justify-center mr-3 md:mr-4">
                            <i class="fas fa-user-tie text-emerald-400"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold">HMS</h4>
                            <div class="text-emerald-400 text-sm font-medium">Hiring Management System</div>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm mb-4">Manages recruitment, hiring, and onboarding processes.</p>
                    <div class="flex items-center text-emerald-400 text-sm">
                        <i class="fas fa-arrow-right mr-2"></i>
                        <span>Sends employee data to PMS</span>
                    </div>
                </div>
                
                <!-- PMS Card (Highlighted) -->
                <div class="system-card pms-card fade-in-up relative">
                    <div class="pms-card-badge absolute -top-2 -right-2 md:-top-3 md:-right-3">
                        <span class="pms-badge-pill bg-gradient-to-r from-purple-600 to-blue-600 text-white text-xs font-bold px-2 py-0.5 md:px-3 md:py-1 rounded-full">YOU ARE HERE</span>
                    </div>
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-lg bg-gradient-to-br from-purple-600/30 to-blue-600/30 flex items-center justify-center mr-3 md:mr-4">
                            <i class="fas fa-chart-line text-lg md:text-xl text-purple-300"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold highlight-pms">PMS</h4>
                            <div class="text-purple-300 text-sm font-medium">Performance Management System</div>
                        </div>
                    </div>
                    <p class="text-gray-300 text-sm mb-4">Evaluates employee performance, tracks KPIs, and analyzes development needs. Acts as the central hub in the HR ecosystem.</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                        <div class="text-sm">
                            <div class="text-emerald-400">Receives from:</div>
                            <div class="text-xs text-gray-500">Hiring Management System</div>
                        </div>
                        <div class="text-sm">
                            <div class="text-amber-400">Sends to LND:</div>
                            <div class="text-xs text-gray-500">Employees needing development</div>
                        </div>
                        <div class="text-sm">
                            <div class="text-red-400">Sends to RNR:</div>
                            <div class="text-xs text-gray-500">Top performers</div>
                        </div>
                    </div>
                </div>
                
                <!-- LND & RNR Combined -->
                <div class="space-y-4 md:space-y-6 fade-in-up">
                    <div class="system-card lnd-card">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-lg bg-amber-500/20 flex items-center justify-center mr-3 md:mr-4">
                                <i class="fas fa-graduation-cap text-amber-400"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold">LND</h4>
                                <div class="text-amber-400 text-sm font-medium">Learning & Development</div>
                            </div>
                        </div>
                        <p class="text-gray-400 text-sm mb-4">Provides training and skill development programs.</p>
                        <div class="flex items-center text-amber-400 text-sm">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Receives from PMS</span>
                        </div>
                    </div>
                    
                    <div class="system-card rnr-card">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-lg bg-red-500/20 flex items-center justify-center mr-3 md:mr-4">
                                <i class="fas fa-award text-red-400"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold">RNR</h4>
                                <div class="text-red-400 text-sm font-medium">Rewards & Recognition</div>
                            </div>
                        </div>
                        <p class="text-gray-400 text-sm mb-4">Manages employee rewards, bonuses, and recognition programs.</p>
                        <div class="flex items-center text-red-400 text-sm">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <span>Receives from PMS</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-12 md:py-16 bg-gray-900/50">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="text-center mb-8 md:mb-12 fade-in-up">
                <h2 class="text-2xl md:text-3xl font-bold mb-3 md:mb-4"><span class="highlight-pms">PMS</span> Core Features</h2>
                <p class="text-lg md:text-xl text-gray-400 max-w-3xl mx-auto">Comprehensive tools to evaluate, track, and enhance employee performance within the HR ecosystem.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                <div class="system-card fade-in-up">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-gradient-to-br from-purple-600/20 to-blue-600/20 flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-user-check text-xl md:text-2xl text-purple-400"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold mb-2 md:mb-3">Employee Performance Tracking</h3>
                    <p class="text-gray-400 text-sm md:text-base">Monitor performance metrics for employees received from Hiring Management System.</p>
                </div>
                
                <div class="system-card fade-in-up">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-gradient-to-br from-purple-600/20 to-blue-600/20 flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-route text-xl md:text-2xl text-blue-400"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold mb-2 md:mb-3">Intelligent Employee Routing</h3>
                    <p class="text-gray-400 text-sm md:text-base">Automatically route employees to LND for development or RNR for recognition based on performance.</p>
                </div>
                
                <div class="system-card fade-in-up">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-gradient-to-br from-purple-600/20 to-blue-600/20 flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-chart-bar text-xl md:text-2xl text-indigo-400"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold mb-2 md:mb-3">Performance Analytics</h3>
                    <p class="text-gray-400 text-sm md:text-base">Generate detailed reports and analytics to support data-driven HR decisions.</p>
                </div>
                
                <div class="system-card fade-in-up">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-gradient-to-br from-purple-600/20 to-blue-600/20 flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-bullseye text-xl md:text-2xl text-cyan-400"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold mb-2 md:mb-3">Goal Management</h3>
                    <p class="text-gray-400 text-sm md:text-base">Set and track individual and team goals aligned with organizational objectives.</p>
                </div>
                
                <div class="system-card fade-in-up">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-gradient-to-br from-purple-600/20 to-blue-600/20 flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-exchange-alt text-xl md:text-2xl text-violet-400"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold mb-2 md:mb-3">360° Feedback Integration</h3>
                    <p class="text-gray-400 text-sm md:text-base">Collect comprehensive feedback from peers, supervisors, and subordinates.</p>
                </div>
                
                <div class="system-card fade-in-up">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-gradient-to-br from-purple-600/20 to-blue-600/20 flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-sync-alt text-xl md:text-2xl text-purple-300"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold mb-2 md:mb-3">HR System Synchronization</h3>
                    <p class="text-gray-400 text-sm md:text-base">Real-time data sync with HMS, LND, and RNR systems for seamless employee lifecycle management.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 md:py-20 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-purple-900/10 to-blue-900/10"></div>
        <div class="max-w-screen-xl mx-auto px-4 relative z-10">
            <div class="text-center fade-in-up">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6">Ready to Optimize Employee Performance?</h2>
                <p class="text-lg md:text-xl text-gray-400 mb-6 md:mb-10 max-w-3xl mx-auto">
                    Access the Performance Management System to track, evaluate, and enhance employee performance within the integrated HR ecosystem.
                </p>
                
                <div class="flex flex-col md:flex-row justify-center gap-4 md:gap-6">
                    <button id="accountActivationModalBtn3" class="btn-gradient px-6 py-3 md:px-8 md:py-4 rounded-xl font-bold text-base md:text-lg">
                        <i class="fas fa-user-check mr-2 md:mr-3"></i> Activate PMS Account
                    </button>
                    <button id="loginModalBtn2" class="border border-purple-500 text-purple-300 hover:bg-purple-500/10 px-6 py-3 md:px-8 md:py-4 rounded-xl font-bold text-base md:text-lg">
                        <i class="fas fa-sign-in-alt mr-2 md:mr-3"></i> Login to PMS
                    </button>
                </div>
                
                <div class="mt-6 md:mt-10 text-gray-500 text-sm">
                    <i class="fas fa-info-circle mr-2"></i> Accounts are created by HR after hiring process completion
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 py-6 md:py-8">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0 text-center md:text-left">
                    <div class="flex items-center space-x-3 mb-4 justify-center md:justify-start">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <div>
                            <span class="text-lg md:text-xl font-bold text-white">Performance Management System</span>
                            <div class="text-gray-500 text-sm">Part of Integrated HR Ecosystem</div>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm max-w-md">Central hub for employee performance tracking, integrated with Hiring Management, Learning & Development, and Rewards & Recognition systems.</p>
                </div>
                
                <div class="text-gray-500 text-sm text-center md:text-right">
                    <p>© 2024 Performance Management System. HR Ecosystem v2.0</p>
                    <div class="mt-2 flex flex-wrap justify-center md:justify-end gap-2">
                        <span class="bg-gray-800 px-2 py-1 rounded text-xs">Integrated with HMS</span>
                        <span class="bg-gray-800 px-2 py-1 rounded text-xs">Connected to LND</span>
                        <span class="bg-gray-800 px-2 py-1 rounded text-xs">Linked to RNR</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modals -->
    <!-- Login Modal -->
    <div id="loginModal" tabindex="-1" aria-hidden="true" class="modal-overlay hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative modal-dark rounded-xl shadow-lg">
                <div class="flex items-center justify-between p-4 md:p-5 border-b modal-header-dark rounded-t-xl">
                    <div class="flex items-center">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center mr-3">
                            <i class="fas fa-sign-in-alt text-white text-sm md:text-base"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold text-white">Login to PMS</h3>
                    </div>
                    <button type="button" class="modal-close-btn rounded-lg text-sm w-8 h-8 flex justify-center items-center" data-modal-hide="loginModal">
                        <i class="fas fa-times"></i>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-6">
                    <form class="space-y-4 md:space-y-5">
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-300">Email Address</label>
                            <input type="email" name="email" id="email" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg block w-full p-3" placeholder="employee@company.com">
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-300">Password</label>
                            <input type="password" name="password" id="password" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg block w-full p-3">
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <input id="remember" type="checkbox" class="w-4 h-4 rounded bg-gray-700 border-gray-600">
                                <label for="remember" class="ms-2 text-sm font-medium text-gray-300">Remember me</label>
                            </div>
                            <a href="#" id="openPasswordReset" class="text-sm text-purple-400 hover:text-purple-300">Forgot Password?</a>
                        </div>
                        <button type="submit" class="btn-gradient w-full py-3 rounded-lg font-medium">Login to PMS Dashboard</button>
                        <div class="text-sm font-medium text-gray-400 text-center">
                            Need to activate your account? <a href="#" id="openAccountActivationFromLogin" class="text-purple-400 hover:text-purple-300">Activate here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Activation Modal -->
    <div id="accountActivationModal" tabindex="-1" aria-hidden="true" class="modal-overlay hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative modal-dark rounded-xl shadow-lg">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b modal-header-dark rounded-t-xl">
                    <div class="flex items-center">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center mr-3">
                            <i class="fas fa-user-check text-white text-sm md:text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-lg md:text-xl font-semibold text-white" id="modalTitle">Activate PMS Account</h3>
                            <!-- Step Indicator -->
                            <div class="flex items-center space-x-2 mt-1">
                                <div class="flex items-center">
                                    <div class="step-indicator active" data-step="1">1</div>
                                    <div class="h-0.5 w-4 bg-gray-600 mx-1"></div>
                                    <div class="step-indicator" data-step="2">2</div>
                                </div>
                                <span class="text-xs text-gray-400">Step <span id="currentStep">1</span> of 2</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="modal-close-btn text-gray-400 hover:text-white rounded-lg text-sm w-8 h-8 flex justify-center items-center" data-modal-hide="accountActivationModal">
                        <i class="fas fa-times text-lg"></i>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-4 md:p-6">
                    <form id="activationForm" class="space-y-4 md:space-y-5">
                        <!-- ===================== -->
                        <!-- PHASE 1: VERIFY IDENTITY -->
                        <!-- ===================== -->
                        <div id="activationPhaseVerify">
                            <div>
                                <label for="employee_id" class="block mb-2 text-sm font-medium text-gray-300">Employee ID</label>
                                <input type="text" name="employee_id" id="employee_id" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg block w-full p-3" placeholder="EMP-2024-00123">
                                <div class="mt-1 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i> Format: EMP-YYYY-XXXXX
                                </div>
                            </div>

                            <div>
                                <label for="act_email" class="block mb-2 text-sm font-medium text-gray-300">Email Address</label>
                                <input type="email" name="email" id="act_email" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg block w-full p-3" placeholder="employee@company.com">
                            </div>

                            <div class="bg-purple-900/30 border border-purple-800/50 p-3 md:p-4 rounded-lg">
                                <p class="text-sm text-purple-300">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Your account was created by HR after hiring. Please verify your identity to continue.
                                </p>
                            </div>

                            <div class="flex justify-between space-x-3">
                                <button type="button" class="border border-gray-600 text-gray-300 hover:border-gray-500 hover:text-white px-4 py-2 rounded-lg font-medium transition-all flex-1" data-modal-hide="accountActivationModal">
                                    Cancel
                                </button>
                                <button type="button" id="verifyAccountBtn" class="btn-gradient px-4 py-2 rounded-lg font-medium flex-1">
                                    Verify Account
                                </button>
                            </div>
                        </div>

                        <!-- ===================== -->
                        <!-- PHASE 2: SET PASSWORD -->
                        <!-- ===================== -->
                        <div id="activationPhasePassword" class="hidden activation-step">
                            <div class="activation-banner">
                                <div class="activation-banner-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div class="activation-banner-title">Account Verified Successfully!</div>
                                    <div class="activation-banner-text">Please create your password to activate your account.</div>
                                </div>
                            </div>

                            <div class="profile-upload">
                                <div class="profile-preview" id="profilePreview">
                                    <img id="profilePreviewImage" class="hidden" alt="Profile preview">
                                    <i id="profilePreviewIcon" class="fas fa-user"></i>
                                </div>
                                <div class="profile-upload-info">
                                    <div class="profile-upload-title">Profile photo</div>
                                    <div class="profile-upload-subtitle">Add a headshot to personalize your profile.</div>
                                    <div class="profile-upload-actions">
                                        <label for="profilePhoto" class="profile-upload-btn">Upload photo</label>
                                        <button type="button" id="removeProfilePhoto" class="profile-upload-remove hidden">Remove</button>
                                    </div>
                                    <div class="profile-upload-hint">JPG or PNG, max 5MB.</div>
                                </div>
                                <input type="file" id="profilePhoto" accept="image/*" class="hidden">
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="full_name" class="block mb-2 text-sm font-medium text-gray-300">Full name</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="full_name" id="full_name" class="activation-input bg-gray-800 border border-gray-700 text-white text-sm rounded-lg block w-full p-3 pl-10" placeholder="Enter Fullname">
                                    </div>
                                </div>

                                <div>
                                    <label for="act_password" class="block mb-2 text-sm font-medium text-gray-300">Create Password</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" name="password" id="act_password" class="activation-input bg-gray-800 border border-gray-700 text-white text-sm rounded-lg block w-full p-3 pl-10 pr-10">
                                        <button type="button" class="password-toggle hover:text-white" onclick="togglePassword('act_password', this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i> Minimum 8 characters with letters and numbers
                                    </div>
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-300">Confirm Password</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="activation-input bg-gray-800 border border-gray-700 text-white text-sm rounded-lg block w-full p-3 pl-10 pr-10">
                                        <button type="button" class="password-toggle hover:text-white" onclick="togglePassword('password_confirmation', this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs text-gray-400">Password Strength</span>
                                    <span id="passwordStrengthText" class="text-xs">None</span>
                                </div>
                                <div class="w-full bg-gray-700 rounded-full h-1.5">
                                    <div id="passwordStrengthBar" class="h-1.5 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="activation-actions">
                                <button type="button" id="backToVerifyBtn" class="border border-gray-600 text-gray-300 hover:border-gray-500 hover:text-white px-4 py-2 rounded-lg font-medium transition-all">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </button>
                                <button type="submit" id="activateAccountBtn" class="btn-gradient px-4 py-2 rounded-lg font-medium">
                                    Activate Account
                                </button>
                            </div>
                        </div>

                        <!-- Footer link -->
                        <div class="text-sm font-medium text-gray-400 text-center pt-4 border-t border-gray-800">
                            Already activated?
                            <a href="#" id="openLoginFromActivation" class="text-purple-400 hover:text-purple-300 ml-1">
                                Login here
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Reset Modal -->
    <div id="passwordResetModal" tabindex="-1" aria-hidden="true" class="modal-overlay hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative modal-dark rounded-xl shadow-lg">
                <div class="flex items-center justify-between p-4 md:p-5 border-b modal-header-dark rounded-t-xl">
                    <div class="flex items-center">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center mr-3">
                            <i class="fas fa-key text-white text-sm md:text-base"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold text-white">Reset PMS Password</h3>
                    </div>
                    <button type="button" class="modal-close-btn rounded-lg text-sm w-8 h-8 flex justify-center items-center" data-modal-hide="passwordResetModal">
                        <i class="fas fa-times"></i>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-6">
                    <form class="space-y-4 md:space-y-5">
                        <div>
                            <label for="reset_email" class="block mb-2 text-sm font-medium text-gray-300">Email Address</label>
                            <input type="email" name="email" id="reset_email" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg block w-full p-3" placeholder="employee@company.com">
                        </div>
                        <div class="bg-purple-900/30 border border-purple-800/50 p-3 md:p-4 rounded-lg">
                            <p class="text-sm text-purple-300">
                                <i class="fas fa-envelope mr-2"></i> A password reset link will be sent to your email. The link will expire in 24 hours.
                            </p>
                        </div>
                        <button type="submit" class="btn-gradient w-full py-3 rounded-lg font-medium">Send Reset Link</button>
                        <div class="text-sm font-medium text-gray-400 text-center">
                            Remember your password? <a href="#" id="openLoginFromReset" class="text-purple-400 hover:text-purple-300">Login here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Flowbite JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <!-- Custom Script -->
</body>
</html>
