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
    <style>
        :root {
            --primary: #0f172a;
            --primary-dark: #020617;
            --secondary: #3b82f6;
            --pms-accent: #8b5cf6;
            --hms-color: #10b981;
            --lnd-color: #f59e0b;
            --rnr-color: #ef4444;
            --text-light: #f8fafc;
            --text-gray: #94a3b8;
            --card-bg: #1e293b;
            --card-border: #334155;
        }
        
        * {
            box-sizing: border-box;
        }
        
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }
        
        body {
            background-color: var(--primary);
            color: var(--text-light);
            font-family: system-ui, -apple-system, sans-serif;
            padding-top: 70px; /* Add padding to prevent content from hiding under fixed nav */
        }
        
        /* Sticky Navigation */
        .sticky-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
            background-color: rgba(15, 23, 42, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .sticky-nav.scrolled {
            background-color: rgba(2, 6, 23, 0.98);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            width: 44px;
            height: 44px;
            border-radius: 8px;
            font-size: 1.2rem;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        /* Mobile Menu */
        .mobile-menu {
            display: none;
            background: var(--primary-dark);
            border-bottom: 1px solid var(--card-border);
            padding: 20px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 99;
        }
        
        .mobile-menu.active {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .mobile-menu a {
            display: block;
            padding: 14px 0;
            color: var(--text-light);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-decoration: none;
            font-size: 1.1rem;
        }
        
        .mobile-menu a:last-child {
            border-bottom: none;
        }
        
        /* Hero Section */
        .hero-gradient {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #111827 50%, #1e1b4b 100%);
            position: relative;
            overflow: hidden;
            margin-top: -70px; /* Compensate for body padding */
            padding-top: 70px; /* Add padding to account for fixed nav */
        }
        
        .hero-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%);
        }
        
        .pms-badge {
            background-color: rgba(139, 92, 246, 0.15);
            border-left: 4px solid var(--pms-accent);
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        
        /* System Cards */
        .system-card {
            background: linear-gradient(145deg, var(--card-bg), #1a2332);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            padding: 24px;
        }
        
        .system-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .hms-card {
            border-top: 4px solid var(--hms-color);
        }
        
        .pms-card {
            border-top: 4px solid var(--pms-accent);
        }
        
        .lnd-card {
            border-top: 4px solid var(--lnd-color);
        }
        
        .rnr-card {
            border-top: 4px solid var(--rnr-color);
        }
        
        .highlight-pms {
            background: linear-gradient(90deg, var(--pms-accent), #a78bfa);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: bold;
        }
        
        /* Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, var(--pms-accent), var(--secondary));
            border: none;
            transition: all 0.3s ease;
            color: white;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);
        }
        
        /* Animations */
        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Modal Styles */
        .modal-dark {
            background-color: var(--primary-dark);
            border: 1px solid var(--card-border);
            border-radius: 12px;
        }
        
        .modal-header-dark {
            background-color: rgba(30, 41, 59, 0.8);
            border-bottom: 1px solid var(--card-border);
        }

        .modal-overlay {
            z-index: 2000 !important;
            inset: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(2, 6, 23, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .modal-close-btn {
            color: #e2e8f0;
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.4);
            transition: color 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
        }

        .modal-close-btn:hover {
            color: #ffffff;
            border-color: rgba(226, 232, 240, 0.6);
            background-color: rgba(15, 23, 42, 0.85);
        }

        body.modal-open {
            overflow: hidden;
        }

        body.modal-open > *:not(.modal-overlay) {
            filter: blur(4px);
            transition: filter 0.2s ease;
        }

        .activation-step {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .activation-step.hidden {
            display: none;
        }

        .activation-banner {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid rgba(34, 197, 94, 0.35);
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(15, 23, 42, 0.4));
        }

        .activation-banner-icon {
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            background: rgba(34, 197, 94, 0.2);
            color: #34d399;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activation-banner-title {
            color: #d1fae5;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .activation-banner-text {
            color: #86efac;
            font-size: 0.8rem;
            margin-top: 2px;
        }

        .profile-upload {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 14px;
            align-items: center;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px dashed rgba(148, 163, 184, 0.35);
            background: rgba(15, 23, 42, 0.55);
        }

        .profile-preview {
            width: 64px;
            height: 64px;
            border-radius: 9999px;
            border: 2px solid rgba(139, 92, 246, 0.5);
            background: rgba(30, 41, 59, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #cbd5f5;
            font-size: 1.1rem;
        }

        .profile-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .profile-upload-title {
            font-weight: 600;
            color: #e2e8f0;
        }

        .profile-upload-subtitle {
            color: #94a3b8;
            font-size: 0.8rem;
            margin-top: 2px;
        }

        .profile-upload-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .profile-upload-btn {
            background: linear-gradient(135deg, var(--pms-accent), var(--secondary));
            color: #ffffff;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
        }

        .profile-upload-remove {
            border: 1px solid rgba(148, 163, 184, 0.6);
            color: #cbd5f5;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .profile-upload-hint {
            color: #64748b;
            font-size: 0.75rem;
            margin-top: 6px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .activation-input:focus {
            outline: none;
            border-color: rgba(139, 92, 246, 0.7);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .activation-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding-top: 64px; /* Adjust for smaller nav on mobile */
            }
            
            .hero-gradient {
                margin-top: -64px;
                padding-top: 64px;
            }
            
            /* Navigation */
            .mobile-menu-btn {
                display: flex;
            }
            
            .sticky-nav .hidden.md\:flex {
                display: none !important;
            }
            
            .sticky-nav .hidden.md\:flex + div {
                display: none !important;
            }
            
            .sticky-nav {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000; /* Higher z-index to ensure nav stays on top */
            }
            
            .mobile-menu {
                position: fixed; /* Change to fixed for mobile */
                top: 64px; /* Height of the nav bar */
                left: 0;
                right: 0;
                height: auto;
                max-height: calc(100vh - 64px);
                overflow-y: auto;
                z-index: 999; /* Just below the nav bar */
            }
            
            /* Typography */
            h1 {
                font-size: 2rem !important;
                line-height: 1.2;
            }
            
            .text-4xl, .text-5xl, .text-6xl {
                font-size: 2rem !important;
            }
            
            .text-3xl {
                font-size: 1.75rem !important;
            }
            
            .text-xl {
                font-size: 1.125rem !important;
            }
            
            .text-2xl {
                font-size: 1.5rem !important;
            }
            
            /* Spacing */
            .px-4 {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }
            
            .py-16 {
                padding-top: 40px !important;
                padding-bottom: 40px !important;
            }
            
            .py-20 {
                padding-top: 50px !important;
                padding-bottom: 50px !important;
            }
            
            .pt-16, .pt-20 {
                padding-top: 40px !important;
            }
            
            .pb-12, .pb-16 {
                padding-bottom: 40px !important;
            }
            
            .mb-12 {
                margin-bottom: 32px !important;
            }
            
            .mb-8 {
                margin-bottom: 24px !important;
            }
            
            .mb-6 {
                margin-bottom: 20px !important;
            }
            
            /* Layout */
            .grid-cols-1 {
                grid-template-columns: 1fr !important;
            }
            
            .md\:grid-cols-3, .lg\:grid-cols-3 {
                grid-template-columns: 1fr !important;
            }
            
            .lg\:col-span-2 {
                grid-column: span 1 !important;
            }
            
            .md\:grid-cols-2 {
                grid-template-columns: 1fr !important;
            }
            
            /* Gap adjustments */
            .gap-8 {
                gap: 24px !important;
            }
            
            .gap-6 {
                gap: 20px !important;
            }
            
            /* Cards */
            .system-card {
                padding: 20px !important;
            }
            
            .p-6 {
                padding: 20px !important;
            }
            
            /* Buttons */
            .btn-gradient, .border-purple-500 {
                width: 100%;
                padding: 14px 20px !important;
            }
            
            .px-8 {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            
            /* CTA Section */
            .sm\:flex-row {
                flex-direction: column !important;
            }
            
            .sm\:space-x-6 {
                gap: 16px !important;
            }
            
            .space-y-4 > * + * {
                margin-top: 16px !important;
            }
            
            /* Footer */
            .md\:flex-row {
                flex-direction: column !important;
                text-align: center;
            }
            
            .md\:text-right {
                text-align: center !important;
                margin-top: 24px;
            }
            
            /* Modals */
            .modal-dark {
                margin: 16px;
                max-width: calc(100% - 32px);
            }

            .profile-upload {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .profile-preview {
                margin: 0 auto;
            }

            .profile-upload-actions {
                justify-content: center;
            }

            .activation-actions {
                grid-template-columns: 1fr;
            }
            
            /* System flow diagram */
            .system-flow-diagram {
                padding: 16px !important;
                margin: 0;
            }
            
            /* Improve touch targets */
            button, a {
                min-height: 44px;
            }
            
            input, select, textarea {
                font-size: 16px !important;
            }
            
            /* Remove hover effects on touch devices */
            @media (hover: none) {
                .system-card:hover {
                    transform: none;
                }
                
                .btn-gradient:hover {
                    transform: none;
                }
            }
            
            /* Body overlay when mobile menu is open */
            body.menu-open {
                overflow: hidden;
            }
            
            body.menu-open::after {
                content: '';
                position: fixed;
                top: 64px;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 998;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            /* Tablet styles */
            .lg\:grid-cols-3 {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            
            .system-card.lg\:col-span-2 {
                grid-column: span 2 !important;
            }
            
            h1 {
                font-size: 3rem !important;
            }
            
            .text-4xl, .text-5xl, .text-6xl {
                font-size: 3rem !important;
            }
        }
        
        /* Touch feedback for mobile */
        @media (hover: none) and (pointer: coarse) {
            .system-card:active {
                transform: scale(0.98);
                transition: transform 0.1s;
            }
            
            button:active {
                opacity: 0.8;
            }
        }


        /* Step Indicator Styles */
        .step-indicator {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .step-indicator.active {
            background: linear-gradient(135deg, #8b5cf6, #3b82f6);
            color: white;
            box-shadow: 0 0 10px rgba(139, 92, 246, 0.3);
        }

        .step-indicator:not(.active) {
            background-color: #374151;
            color: #9CA3AF;
            border: 1px solid #4B5563;
        }

        /* Progress Bar Colors */
        #passwordStrengthBar.weak {
            background-color: #EF4444;
            width: 25% !important;
        }

        #passwordStrengthBar.fair {
            background-color: #F59E0B;
            width: 50% !important;
        }

        #passwordStrengthBar.good {
            background-color: #10B981;
            width: 75% !important;
        }

        #passwordStrengthBar.strong {
            background-color: #10B981;
            width: 100% !important;
        }

        /* Smooth Transitions */
        #activationPhaseVerify,
        #activationPhasePassword {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        /* Loading State */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
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
                <div class="system-card pms-card lg:col-span-2 fade-in-up relative">
                    <div class="absolute -top-2 -right-2 md:-top-3 md:-right-3">
                        <span class="bg-gradient-to-r from-purple-600 to-blue-600 text-white text-xs font-bold px-2 py-0.5 md:px-3 md:py-1 rounded-full">YOU ARE HERE</span>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu functionality
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const loginModalBtnMobile = document.getElementById('loginModalBtnMobile');
            const accountActivationModalBtnMobile = document.getElementById('accountActivationModalBtnMobile');
            const body = document.body;
            
            function toggleMobileMenu() {
                const isActive = mobileMenu.classList.contains('active');
                
                if (isActive) {
                    // Close menu
                    mobileMenu.classList.remove('active');
                    body.classList.remove('menu-open');
                    mobileMenuBtn.querySelector('i').classList.remove('fa-times');
                    mobileMenuBtn.querySelector('i').classList.add('fa-bars');
                } else {
                    // Open menu
                    mobileMenu.classList.add('active');
                    body.classList.add('menu-open');
                    mobileMenuBtn.querySelector('i').classList.remove('fa-bars');
                    mobileMenuBtn.querySelector('i').classList.add('fa-times');
                }
            }
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleMobileMenu();
                });
                
                // Close mobile menu when clicking links
                mobileMenu.querySelectorAll('a, button').forEach(element => {
                    element.addEventListener('click', function() {
                        mobileMenu.classList.remove('active');
                        body.classList.remove('menu-open');
                        if (mobileMenuBtn) {
                            mobileMenuBtn.querySelector('i').classList.remove('fa-times');
                            mobileMenuBtn.querySelector('i').classList.add('fa-bars');
                        }
                    });
                });
                
                // Close mobile menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                        mobileMenu.classList.remove('active');
                        body.classList.remove('menu-open');
                        if (mobileMenuBtn) {
                            mobileMenuBtn.querySelector('i').classList.remove('fa-times');
                            mobileMenuBtn.querySelector('i').classList.add('fa-bars');
                        }
                    }
                });
                
                // Close mobile menu on scroll
                window.addEventListener('scroll', function() {
                    if (mobileMenu && mobileMenu.classList.contains('active')) {
                        mobileMenu.classList.remove('active');
                        body.classList.remove('menu-open');
                        if (mobileMenuBtn) {
                            mobileMenuBtn.querySelector('i').classList.remove('fa-times');
                            mobileMenuBtn.querySelector('i').classList.add('fa-bars');
                        }
                    }
                });
                
                // Close menu on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                        toggleMobileMenu();
                    }
                });
            }
            
            // Sticky nav scroll effect
            const nav = document.getElementById('mainNav');
            
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }
            });
            
            // Scroll animations
            const fadeElements = document.querySelectorAll('.fade-in-up');
            
            const fadeInOnScroll = function() {
                fadeElements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 100;
                    
                    if (elementTop < window.innerHeight - elementVisible) {
                        element.classList.add('visible');
                    }
                });
            };
            
            // Check on load and scroll
            fadeInOnScroll();
            window.addEventListener('scroll', fadeInOnScroll);
            
            // Get modal elements
            const loginModal = document.getElementById('loginModal');
            const accountActivationModal = document.getElementById('accountActivationModal');
            const passwordResetModal = document.getElementById('passwordResetModal');
            
            // Get button elements
            const loginModalBtns = document.querySelectorAll('#loginModalBtn, #loginModalBtn2');
            const accountActivationModalBtns = document.querySelectorAll('#accountActivationModalBtn, #accountActivationModalBtn2, #accountActivationModalBtn3');
            const openPasswordResetBtn = document.getElementById('openPasswordReset');
            const openAccountActivationFromLogin = document.getElementById('openAccountActivationFromLogin');
            const openLoginFromActivation = document.getElementById('openLoginFromActivation');
            const openLoginFromReset = document.getElementById('openLoginFromReset');
            
            // Modal instances (Flowbite)
            const loginModalInstance = new Modal(loginModal);
            const accountActivationModalInstance = new Modal(accountActivationModal);
            const passwordResetModalInstance = new Modal(passwordResetModal);

            const modalOverlays = document.querySelectorAll('.modal-overlay');
            const updateModalBackdrop = () => {
                const anyOpen = Array.from(modalOverlays).some(modal => !modal.classList.contains('hidden'));
                body.classList.toggle('modal-open', anyOpen);
            };

            if (modalOverlays.length) {
                const modalObserver = new MutationObserver(updateModalBackdrop);
                modalOverlays.forEach(modal => {
                    modalObserver.observe(modal, { attributes: true, attributeFilter: ['class'] });
                });
                updateModalBackdrop();
            }
            
            // Close mobile menu when opening modals
            function closeMobileMenuForModal() {
                if (mobileMenu && mobileMenu.classList.contains('active')) {
                    toggleMobileMenu();
                }
            }
            
            // Mobile modal buttons
            if (loginModalBtnMobile) {
                loginModalBtnMobile.addEventListener('click', function() {
                    closeMobileMenuForModal();
                    loginModalInstance.show();
                });
            }
            
            if (accountActivationModalBtnMobile) {
                accountActivationModalBtnMobile.addEventListener('click', function() {
                    closeMobileMenuForModal();
                    accountActivationModalInstance.show();
                });
            }
            
            // Open login modal
            loginModalBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    closeMobileMenuForModal();
                    loginModalInstance.show();
                });
            });
            
            // Open account activation modal
            accountActivationModalBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    closeMobileMenuForModal();
                    accountActivationModalInstance.show();
                });
            });
            
            // Open password reset modal
            if (openPasswordResetBtn) {
                openPasswordResetBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeMobileMenuForModal();
                    loginModalInstance.hide();
                    setTimeout(() => {
                        passwordResetModalInstance.show();
                    }, 300);
                });
            }
            
            // Switch from login to activation
            if (openAccountActivationFromLogin) {
                openAccountActivationFromLogin.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeMobileMenuForModal();
                    loginModalInstance.hide();
                    setTimeout(() => {
                        accountActivationModalInstance.show();
                    }, 300);
                });
            }
            
            // Switch from activation to login
            if (openLoginFromActivation) {
                openLoginFromActivation.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeMobileMenuForModal();
                    accountActivationModalInstance.hide();
                    setTimeout(() => {
                        loginModalInstance.show();
                    }, 300);
                });
            }
            
            // Switch from reset to login
            if (openLoginFromReset) {
                openLoginFromReset.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeMobileMenuForModal();
                    passwordResetModalInstance.hide();
                    setTimeout(() => {
                        loginModalInstance.show();
                    }, 300);
                });
            }
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href !== '#') {
                        e.preventDefault();
                        closeMobileMenuForModal();
                        const target = document.querySelector(href);
                        if (target) {
                            window.scrollTo({
                                top: target.offsetTop - 80,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
        // Get DOM elements
        const verifyPhase = document.getElementById('activationPhaseVerify');
        const passwordPhase = document.getElementById('activationPhasePassword');
        const verifyBtn = document.getElementById('verifyAccountBtn');
        const backBtn = document.getElementById('backToVerifyBtn');
        const activateBtn = document.getElementById('activateAccountBtn');
        const stepIndicators = document.querySelectorAll('.step-indicator');
        const currentStepSpan = document.getElementById('currentStep');
        const modalTitle = document.getElementById('modalTitle');
        const employeeIdInput = document.getElementById('employee_id');
        const emailInput = document.getElementById('act_email');
        const passwordInput = document.getElementById('act_password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const passwordStrengthBar = document.getElementById('passwordStrengthBar');
        const passwordStrengthText = document.getElementById('passwordStrengthText');
        const fullNameInput = document.getElementById('full_name');
        const profilePhotoInput = document.getElementById('profilePhoto');
        const profilePreviewImage = document.getElementById('profilePreviewImage');
        const profilePreviewIcon = document.getElementById('profilePreviewIcon');
        const removeProfilePhotoBtn = document.getElementById('removeProfilePhoto');

        // Track current step
        let currentStep = 1;
        let verifiedData = null;

        // Step 1: Verify Account
        verifyBtn.addEventListener('click', function() {
            // Get form values
            const employeeId = employeeIdInput.value.trim();
            const email = emailInput.value.trim();

            // Validation
            if (!employeeId) {
                showError(employeeIdInput, 'Employee ID is required');
                return;
            }

            if (!email) {
                showError(emailInput, 'Email is required');
                return;
            }

            // Validate Employee ID format
            const employeeIdPattern = /^EMP-\d{4}-\d{5}$/;
            if (!employeeIdPattern.test(employeeId)) {
                showError(employeeIdInput, 'Invalid format. Use EMP-YYYY-XXXXX');
                return;
            }

            // Validate email format
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                showError(emailInput, 'Please enter a valid email address');
                return;
            }

            // Show loading state
            const originalText = verifyBtn.innerHTML;
            verifyBtn.innerHTML = '<span class="loading-spinner"></span> Verifying...';
            verifyBtn.disabled = true;

            // Simulate API call to verify account
            setTimeout(() => {
                // For demo purposes, we'll simulate a successful verification
                // In real application, this would be an API call
                const isVerified = Math.random() > 0.2; // 80% success rate for demo

                if (isVerified) {
                    // Save verified data
                    verifiedData = {
                        employeeId: employeeId,
                        email: email
                    };

                    // Move to next step
                    goToStep(2);
                } else {
                    // Show error
                    alert('Account verification failed. Please check your Employee ID and email, or contact HR.');
                }

                // Reset button
                verifyBtn.innerHTML = originalText;
                verifyBtn.disabled = false;
            }, 1500);
        });

        // Step 2: Back to Verify
        backBtn.addEventListener('click', function() {
            goToStep(1);
        });

        // Password strength checker
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        // Profile photo preview
        function resetProfilePhoto() {
            if (profilePhotoInput) {
                profilePhotoInput.value = '';
            }
            if (profilePreviewImage) {
                profilePreviewImage.src = '';
                profilePreviewImage.classList.add('hidden');
            }
            if (profilePreviewIcon) {
                profilePreviewIcon.classList.remove('hidden');
            }
            if (removeProfilePhotoBtn) {
                removeProfilePhotoBtn.classList.add('hidden');
            }
        }

        if (profilePhotoInput) {
            profilePhotoInput.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) {
                    resetProfilePhoto();
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file.');
                    resetProfilePhoto();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    if (profilePreviewImage) {
                        profilePreviewImage.src = event.target.result;
                        profilePreviewImage.classList.remove('hidden');
                    }
                    if (profilePreviewIcon) {
                        profilePreviewIcon.classList.add('hidden');
                    }
                    if (removeProfilePhotoBtn) {
                        removeProfilePhotoBtn.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        if (removeProfilePhotoBtn) {
            removeProfilePhotoBtn.addEventListener('click', function() {
                resetProfilePhoto();
            });
        }

        // Password visibility toggle function
        window.togglePassword = function(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        };

        // Form submission
        document.getElementById('activationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate passwords
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (!password) {
                showError(passwordInput, 'Password is required');
                return;
            }

            if (!confirmPassword) {
                showError(confirmPasswordInput, 'Please confirm your password');
                return;
            }

            if (password !== confirmPassword) {
                showError(confirmPasswordInput, 'Passwords do not match');
                return;
            }

            // Password strength validation
            const strength = calculatePasswordStrength(password);
            if (strength < 2) { // Less than "fair"
                alert('Please use a stronger password (at least 8 characters with letters and numbers)');
                return;
            }

            // Show loading state
            const originalText = activateBtn.innerHTML;
            activateBtn.innerHTML = '<span class="loading-spinner"></span> Activating...';
            activateBtn.disabled = true;

            // Simulate account activation
            setTimeout(() => {
                // Success!
                alert(`Account activated successfully!\n\nEmployee ID: ${verifiedData.employeeId}\nEmail: ${verifiedData.email}\n\nYou can now login to the Performance Management System.`);

                // Reset form and close modal
                resetForm();
                
                // Close modal (using Flowbite)
                const modal = document.getElementById('accountActivationModal');
                const modalInstance = Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }

                // Reset button
                activateBtn.innerHTML = originalText;
                activateBtn.disabled = false;
            }, 2000);
        });

        // Helper functions
        function goToStep(step) {
            currentStep = step;
            
            // Update UI
            if (step === 1) {
                verifyPhase.classList.remove('hidden');
                passwordPhase.classList.add('hidden');
                modalTitle.textContent = 'Activate PMS Account';
            } else if (step === 2) {
                verifyPhase.classList.add('hidden');
                passwordPhase.classList.remove('hidden');
                modalTitle.textContent = 'Set Your Password';
                
                // Pre-fill email from verification
                if (verifiedData) {
                    document.getElementById('act_password').focus();
                }
            }

            // Update step indicators
            stepIndicators.forEach(indicator => {
                const stepNum = parseInt(indicator.getAttribute('data-step'));
                if (stepNum <= step) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            });

            // Update step text
            currentStepSpan.textContent = step;
        }

        function checkPasswordStrength(password) {
            const strength = calculatePasswordStrength(password);
            
            // Update progress bar and text
            passwordStrengthBar.className = 'h-1.5 rounded-full';
            passwordStrengthBar.style.width = '0%';
            
            switch(strength) {
                case 0:
                    passwordStrengthText.textContent = 'None';
                    break;
                case 1:
                    passwordStrengthText.textContent = 'Weak';
                    passwordStrengthBar.classList.add('weak');
                    break;
                case 2:
                    passwordStrengthText.textContent = 'Fair';
                    passwordStrengthBar.classList.add('fair');
                    break;
                case 3:
                    passwordStrengthText.textContent = 'Good';
                    passwordStrengthBar.classList.add('good');
                    break;
                case 4:
                    passwordStrengthText.textContent = 'Strong';
                    passwordStrengthBar.classList.add('strong');
                    break;
            }
        }

        function calculatePasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            return strength;
        }

        function showError(inputElement, message) {
            // Add error styling
            inputElement.classList.add('border-red-500');
            
            // Show error message
            let errorDiv = inputElement.nextElementSibling;
            if (!errorDiv || !errorDiv.classList.contains('error-message')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'error-message text-xs text-red-400 mt-1';
                inputElement.parentNode.insertBefore(errorDiv, inputElement.nextSibling);
            }
            errorDiv.textContent = message;
            
            // Focus the input
            inputElement.focus();
            
            // Remove error after 3 seconds
            setTimeout(() => {
                inputElement.classList.remove('border-red-500');
                if (errorDiv && errorDiv.parentNode) {
                    errorDiv.remove();
                }
            }, 3000);
        }

        function resetForm() {
            // Reset form inputs
            employeeIdInput.value = '';
            emailInput.value = '';
            if (fullNameInput) {
                fullNameInput.value = '';
            }
            passwordInput.value = '';
            confirmPasswordInput.value = '';
            resetProfilePhoto();
            
            // Reset steps
            goToStep(1);
            verifiedData = null;
            
            // Reset password strength meter
            passwordStrengthBar.style.width = '0%';
            passwordStrengthText.textContent = 'None';
            passwordStrengthBar.className = 'h-1.5 rounded-full';
        }

        // Initialize
        goToStep(1);
    });
    </script>
</body>
</html>
