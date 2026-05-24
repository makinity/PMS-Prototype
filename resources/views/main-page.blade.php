<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Performance Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root {
            color-scheme: dark;
            --page-bg: #07101f;
            --panel-border: rgba(122, 149, 197, 0.22);
            --field-bg: rgba(20, 31, 54, 0.92);
            --field-border: rgba(111, 132, 175, 0.26);
            --field-border-focus: rgba(118, 105, 255, 0.78);
            --text-main: #f7f8fc;
            --text-soft: #adc0e1;
            --text-muted: #798cb0;
            --brand-start: #8e5cff;
            --brand-end: #3d8dff;
            --shadow: 0 30px 70px rgba(0, 0, 0, 0.35);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: 'Outfit', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(145, 84, 255, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(61, 141, 255, 0.16), transparent 30%),
                var(--page-bg);
            color: var(--text-main);
        }

        body {
            min-height: 100vh;
            padding-top: 0 !important;
            overflow-x: hidden;
            overflow-y: auto;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input {
            font: inherit;
        }

        .auth-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(390px, 0.8fr);
            gap: 0;
            background:
                radial-gradient(circle at 12% 14%, rgba(135, 90, 255, 0.16), transparent 18%),
                radial-gradient(circle at 90% 20%, rgba(44, 100, 255, 0.12), transparent 22%),
                #07101f;
        }

        .auth-hero {
            position: relative;
            overflow: hidden;
            padding: 2.25rem 2.35rem 0.7rem;
            display: flex;
            align-items: stretch;
            min-height: 100vh;
        }

        .auth-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(8, 16, 37, 0.2), rgba(5, 10, 24, 0.72)),
                linear-gradient(105deg, rgba(94, 48, 190, 0.68) 0%, rgba(11, 21, 45, 0.38) 42%, rgba(6, 16, 34, 0.82) 100%),
                url('{{ asset('images/pms-campus.jpg') }}') center/cover no-repeat;
            box-shadow: inset 0 0 0 1px rgba(151, 170, 212, 0.08);
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(10, 6, 39, 0.82) 0%, rgba(15, 20, 46, 0.48) 48%, rgba(8, 14, 28, 0.82) 100%);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 100%;
        }

        .hero-copy {
            max-width: 32rem;
            padding-top: 2.6rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.5rem 0.82rem;
            border-radius: 999px;
            border: 1px solid rgba(152, 108, 255, 0.92);
            background: rgba(96, 54, 181, 0.18);
            box-shadow: inset 0 0 0 1px rgba(120, 172, 255, 0.22);
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-title {
            margin: 1.15rem 0 0.72rem;
            font-size: clamp(2.35rem, 4vw, 3.55rem);
            line-height: 0.94;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .hero-title span {
            display: block;
            background: linear-gradient(135deg, #ab7aff 0%, #5d8eff 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-rule {
            width: 3.75rem;
            height: 0.2rem;
            border-radius: 999px;
            margin: 0 0 0.95rem;
            background: linear-gradient(90deg, rgba(142, 92, 255, 0.95), rgba(61, 141, 255, 0.95));
            box-shadow: 0 0 22px rgba(120, 109, 255, 0.55);
        }

        .hero-text {
            margin: 0;
            color: rgba(236, 241, 255, 0.92);
            font-size: 0.95rem;
            line-height: 1.58;
            max-width: 26rem;
        }

        .hero-features {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.65rem;
            padding: 0.75rem 0.9rem 0.8rem;
            border-top: 1px solid rgba(104, 123, 168, 0.22);
            background: linear-gradient(180deg, rgba(15, 22, 43, 0.08), rgba(9, 15, 30, 0.6));
            backdrop-filter: blur(18px);
        }

        .feature-item {
            display: grid;
            grid-template-columns: 2.2rem 1fr;
            gap: 0.6rem;
            align-items: start;
        }

        .feature-icon {
            width: 2.2rem;
            height: 2.2rem;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #d9d9ff;
            background: linear-gradient(145deg, rgba(140, 88, 255, 0.42), rgba(49, 97, 255, 0.24));
            border: 1px solid rgba(129, 146, 255, 0.28);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.05);
            font-size: 0.7rem;
        }

        .feature-title {
            margin: 0 0 0.2rem;
            font-size: 0.68rem;
            font-weight: 600;
        }

        .feature-text {
            margin: 0;
            color: rgba(210, 220, 244, 0.88);
            font-size: 0.58rem;
            line-height: 1.38;
        }

        .auth-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 1.85rem 1rem 1.7rem;
            min-height: 100vh;
        }

        .auth-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(131, 77, 255, 0.08), transparent 28%),
                radial-gradient(circle at 75% 25%, rgba(61, 141, 255, 0.08), transparent 28%);
            pointer-events: none;
        }

        .auth-card {
            position: relative;
            width: min(100%, 30.8rem);
            border-radius: 1.9rem;
            background: linear-gradient(180deg, rgba(11, 20, 39, 0.9), rgba(7, 14, 28, 0.96));
            border: 1px solid var(--panel-border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .auth-card-header {
            padding: 1.55rem 1.7rem 1.05rem;
            border-bottom: 1px solid rgba(129, 148, 184, 0.12);
            background: linear-gradient(180deg, rgba(15, 24, 44, 0.94), rgba(10, 18, 34, 0.72));
        }

        .auth-title {
            margin: 0;
            font-size: 1.55rem;
            line-height: 1.1;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .auth-subtitle {
            margin: 0.55rem 0 0;
            color: var(--text-soft);
            font-size: 0.8rem;
            line-height: 1.5;
        }

        .auth-card-body {
            padding: 1.2rem 1.7rem 1.15rem;
        }

        .auth-view {
            display: none;
        }

        .auth-view.is-active {
            display: block;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 0.92rem;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .field-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .field-shell {
            position: relative;
        }

        .field-shell i.leading-icon {
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            color: #95a7ca;
            font-size: 0.88rem;
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            min-height: 2.6rem;
            padding: 0.72rem 0.9rem 0.72rem 2.55rem;
            border-radius: 0.95rem;
            border: 1px solid var(--field-border);
            background: var(--field-bg);
            color: var(--text-main);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            font-size: 0.78rem;
        }

        .field-input::placeholder {
            color: rgba(146, 162, 196, 0.75);
        }

        .field-input:focus {
            border-color: var(--field-border-focus);
            box-shadow: 0 0 0 4px rgba(118, 105, 255, 0.14);
            background: rgba(20, 32, 58, 0.98);
        }

        .password-toggle {
            position: absolute;
            right: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2rem;
            height: 2rem;
            border: 0;
            border-radius: 999px;
            color: #9fb0d0;
            background: transparent;
            cursor: pointer;
            font-size: 0.84rem;
        }

        .password-toggle:hover {
            color: #edf2ff;
        }

        .field-hint {
            font-size: 0.72rem;
            color: var(--text-muted);
        }

        .field-error {
            display: none;
            font-size: 0.88rem;
            color: #fca5a5;
        }

        .field-error.is-visible {
            display: block;
        }

        .field-input.is-error {
            border-color: rgba(248, 113, 113, 0.85);
            box-shadow: 0 0 0 4px rgba(248, 113, 113, 0.12);
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            color: var(--text-main);
            margin-top: 0.1rem;
            font-size: 0.82rem;
        }

        .remember-wrap {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            font-size: 0.78rem;
            font-weight: 500;
        }

        .remember-wrap input {
            width: 0.95rem;
            height: 0.95rem;
            border-radius: 0.3rem;
            accent-color: #223454;
            background-color: #16233c;
            box-shadow: inset 0 0 0 1px rgba(126, 145, 184, 0.42);
        }

        .text-link {
            color: #a47dff;
            font-weight: 500;
            transition: color 0.2s ease;
            font-size: 0.78rem;
        }

        .text-link:hover {
            color: #c7b3ff;
        }

        .submit-button,
        .secondary-button {
            min-height: 2.6rem;
            border-radius: 0.95rem;
            border: 0;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease, border-color 0.2s ease;
            font-size: 0.82rem;
        }

        .submit-button {
            width: 100%;
            color: #fff;
            background: linear-gradient(90deg, var(--brand-start), var(--brand-end));
            box-shadow: 0 16px 34px rgba(72, 112, 255, 0.24);
        }

        .submit-button:hover,
        .secondary-button:hover {
            transform: translateY(-1px);
        }

        .submit-button:disabled,
        .secondary-button:disabled {
            cursor: wait;
            opacity: 0.75;
        }

        .secondary-button {
            background: transparent;
            color: var(--text-main);
            border: 1px solid rgba(126, 145, 184, 0.34);
        }

        .footer-link {
            margin-top: 0.7rem;
            padding-top: 0.95rem;
            border-top: 1px solid rgba(129, 148, 184, 0.12);
            color: var(--text-soft);
            font-size: 0.76rem;
            text-align: center;
        }

        .section-intro {
            margin: 0 0 1rem;
            color: var(--text-soft);
            font-size: 1rem;
            line-height: 1.6;
        }

        .step-summary {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-top: 0.9rem;
            color: var(--text-soft);
        }

        .step-track {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .step-indicator {
            width: 1.6rem;
            height: 1.6rem;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font-size: 0.72rem;
            font-weight: 700;
            color: #98a7c8;
            background: rgba(41, 52, 77, 0.9);
            border: 1px solid rgba(117, 132, 168, 0.34);
        }

        .step-indicator.active {
            color: #fff;
            background: linear-gradient(135deg, var(--brand-start), var(--brand-end));
            border-color: transparent;
            box-shadow: 0 0 20px rgba(123, 103, 255, 0.3);
        }

        .step-connector {
            width: 1.8rem;
            height: 1px;
            background: rgba(114, 129, 162, 0.45);
        }

        .activation-banner {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            padding: 1rem 1rem 1rem 0.95rem;
            border-radius: 1rem;
            border: 1px solid rgba(61, 220, 151, 0.32);
            background: linear-gradient(135deg, rgba(61, 220, 151, 0.14), rgba(12, 19, 35, 0.58));
        }

        .activation-banner-icon {
            flex-shrink: 0;
            width: 2.1rem;
            height: 2.1rem;
            border-radius: 999px;
            display: grid;
            place-items: center;
            color: #d6ffeb;
            background: rgba(61, 220, 151, 0.22);
        }

        .activation-banner-title {
            font-weight: 600;
            color: #e3fff1;
        }

        .activation-banner-text {
            margin-top: 0.2rem;
            color: #a4e7c4;
            font-size: 0.92rem;
        }

        .profile-upload {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            border-radius: 1rem;
            border: 1px dashed rgba(126, 145, 184, 0.34);
            background: rgba(17, 25, 43, 0.72);
        }

        .profile-preview {
            width: 4.25rem;
            height: 4.25rem;
            border-radius: 999px;
            overflow: hidden;
            display: grid;
            place-items: center;
            border: 2px solid rgba(137, 115, 255, 0.44);
            background: rgba(23, 33, 55, 0.98);
            color: #aebeee;
        }

        .profile-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hidden {
            display: none !important;
        }

        .profile-upload-title {
            font-weight: 600;
        }

        .profile-upload-subtitle,
        .profile-upload-hint {
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .profile-upload-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.75rem;
        }

        .profile-upload-btn,
        .profile-upload-remove {
            min-height: 2.45rem;
            padding: 0.65rem 0.95rem;
            border-radius: 0.8rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
        }

        .profile-upload-btn {
            color: #fff;
            background: linear-gradient(135deg, var(--brand-start), var(--brand-end));
        }

        .profile-upload-remove {
            color: var(--text-main);
            border: 1px solid rgba(126, 145, 184, 0.34);
            background: transparent;
        }

        .password-strength {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .password-strength-head {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            font-size: 0.88rem;
            color: var(--text-soft);
        }

        .password-strength-track {
            width: 100%;
            height: 0.45rem;
            border-radius: 999px;
            background: rgba(59, 71, 98, 0.9);
            overflow: hidden;
        }

        .password-strength-bar {
            width: 0%;
            height: 100%;
            border-radius: inherit;
            transition: width 0.22s ease, background-color 0.22s ease;
        }

        .action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.9rem;
        }

        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            margin-right: 0.6rem;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.28);
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
            vertical-align: -0.15rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 1024px) {
            .auth-page {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background: var(--page-bg);
                padding: 1.5rem 1rem;
                gap: 1.5rem;
            }

            .auth-hero {
                width: 100%;
                max-width: 30rem;
                min-height: unset;
                padding: 1rem 0 0.75rem;
                background: transparent;
            }

            .auth-hero::before,
            .hero-overlay {
                display: none !important;
            }

            .hero-content {
                display: block;
            }

            .hero-copy {
                padding-top: 0;
                text-align: center;
                max-width: none;
            }

            .hero-badge {
                display: none !important;
            }

            .hero-title {
                margin: 0;
                font-size: 1.65rem;
                line-height: 1.1;
                letter-spacing: -0.03em;
                text-align: center;
            }

            .hero-title span {
                display: inline;
            }

            .hero-rule,
            .hero-text {
                display: none !important;
            }

            .hero-features {
                display: none !important;
            }

            .auth-panel {
                width: 100%;
                max-width: 30rem;
                min-height: unset;
                padding: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .auth-panel::before {
                display: none;
            }

            .auth-card {
                width: 100%;
                margin: 0 auto;
                border-radius: 1.35rem;
                border-color: rgba(122, 149, 197, 0.18);
                background: linear-gradient(180deg, rgba(10, 18, 35, 0.96), rgba(7, 14, 28, 0.98));
            }

            .auth-card-header {
                padding: 1.25rem 1.15rem 0.9rem;
            }

            .auth-card-body {
                padding: 1rem 1.15rem 1.15rem;
            }

            .auth-title {
                font-size: 1.05rem;
                line-height: 1.2;
            }

            .auth-subtitle {
                margin-top: 0.35rem;
                font-size: 0.72rem;
                line-height: 1.45;
                max-width: 16rem;
            }

            .auth-form {
                gap: 0.95rem;
            }

            .field-group {
                gap: 0.46rem;
            }

            .field-label {
                font-size: 0.78rem;
            }

            .field-input {
                min-height: 3.15rem;
                font-size: 0.82rem;
                padding-left: 2.45rem;
                border-radius: 1rem;
            }

            .field-input::placeholder {
                color: rgba(161, 178, 214, 0.9);
            }

            .field-shell i.leading-icon {
                font-size: 0.8rem;
                left: 0.8rem;
            }

            .form-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                flex-wrap: wrap;
                margin-top: 0.15rem;
            }

            .remember-wrap,
            .text-link[data-open-view="reset"] {
                min-height: 2rem;
                display: inline-flex;
                align-items: center;
            }

            .remember-wrap {
                font-size: 0.78rem;
            }

            .text-link[data-open-view="reset"] {
                margin-left: auto;
                text-align: right;
            }

            .submit-button {
                font-size: 0.88rem;
                min-height: 3.2rem;
                padding: 0.78rem;
                border-radius: 1rem;
            }

            .footer-link {
                margin-top: 0.2rem;
                padding-top: 0.9rem;
                font-size: 0.74rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <main class="auth-page">
        <section class="auth-hero" aria-label="PMS introduction">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="hero-copy">
                    <div class="hero-badge">
                        <i class="fa-solid fa-chart-simple"></i>
                        <span>Performance Management System</span>
                    </div>
                    <h1 class="hero-title">
                        Welcome to
                        <span>PMS</span>
                    </h1>
                    <div class="hero-rule"></div>
                    <p class="hero-text">
                        Your performance. Our mission.
                        Together, we drive excellence.
                    </p>
                </div>

                <div class="hero-features">
                    <article class="feature-item">
                        <div class="feature-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <h2 class="feature-title">Track Performance</h2>
                            <p class="feature-text">Real-time insights and metrics that matter.</p>
                        </div>
                    </article>
                    <article class="feature-item">
                        <div class="feature-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h2 class="feature-title">Empower Growth</h2>
                            <p class="feature-text">Personalized development and recognition.</p>
                        </div>
                    </article>
                    <article class="feature-item">
                        <div class="feature-icon">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h2 class="feature-title">Drive Results</h2>
                            <p class="feature-text">Data-driven decisions for a stronger organization.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="auth-panel" aria-label="PMS authentication">
            <div class="auth-card">
                <header class="auth-card-header">
                    <h2 class="auth-title" id="authTitle">Login to PMS</h2>
                    <p class="auth-subtitle" id="authSubtitle">Access your performance dashboard securely.</p>
                    <div class="step-summary hidden" id="activationStepSummary">
                        <div class="step-track">
                            <div class="step-indicator active" data-step="1">1</div>
                            <div class="step-connector"></div>
                            <div class="step-indicator" data-step="2">2</div>
                        </div>
                        <span>Step <span id="currentStep">1</span> of 2</span>
                    </div>
                </header>

                <div class="auth-card-body">
                    <section class="auth-view is-active" id="loginView" data-auth-view="login">
                        <form id="loginForm" class="auth-form" novalidate>
                            <div class="field-group">
                                <label class="field-label" for="login_name">Name</label>
                                <div class="field-shell">
                                    <i class="fa-regular fa-user leading-icon"></i>
                                    <input type="text" id="login_name" name="name" class="field-input" placeholder="Enter your login name" autocomplete="username">
                                </div>
                                <div class="field-error" data-error-for="login_name"></div>
                            </div>

                            <div class="field-group">
                                <label class="field-label" for="password">Password</label>
                                <div class="field-shell">
                                    <i class="fa-solid fa-lock leading-icon"></i>
                                    <input type="password" id="password" name="password" class="field-input" placeholder="Enter your password" autocomplete="current-password">
                                    <button type="button" class="password-toggle" data-toggle-password="password" aria-label="Toggle password visibility">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <div class="field-error" data-error-for="password"></div>
                            </div>

                            <div class="form-row">
                                <label class="remember-wrap" for="remember">
                                    <input id="remember" name="remember" type="checkbox">
                                    <span>Remember me</span>
                                </label>
                                <a href="#" class="text-link" data-open-view="reset">Forgot Password?</a>
                            </div>

                            <button type="submit" id="loginToDashboardBtn" class="submit-button">
                                <span id="loginText">Login to PMS Dashboard</span>
                            </button>

                            <div class="footer-link">
                                Need to activate your account?
                                <a href="#" class="text-link" data-open-view="activation">Activate here</a>
                            </div>
                        </form>
                    </section>

                    <section class="auth-view" id="activationView" data-auth-view="activation">
                        <form id="activationForm" class="auth-form" novalidate>
                            <div id="activationPhaseVerify" class="auth-form">
                                <p class="section-intro">Verify your employee account to continue with first-time activation.</p>

                                <div class="field-group">
                                    <label class="field-label" for="employee_id">Employee ID</label>
                                    <div class="field-shell">
                                        <i class="fa-regular fa-id-badge leading-icon"></i>
                                        <input type="text" id="employee_id" name="employee_id" class="field-input" placeholder="EMP-RCU-0003">
                                    </div>
                                    <div class="field-hint">Format: EMP-ABC-0000</div>
                                    <div class="field-error" data-error-for="employee_id"></div>
                                </div>

                                <div class="field-group">
                                    <label class="field-label" for="act_email">Email Address</label>
                                    <div class="field-shell">
                                        <i class="fa-regular fa-envelope leading-icon"></i>
                                        <input type="email" id="act_email" name="email" class="field-input" placeholder="employee@company.com">
                                    </div>
                                    <div class="field-error" data-error-for="act_email"></div>
                                </div>

                                <div class="activation-banner">
                                    <div class="activation-banner-icon">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </div>
                                    <div>
                                        <div class="activation-banner-title">Account verification required</div>
                                        <div class="activation-banner-text">Your account is prepared by HR. Verify your employee ID and email to continue.</div>
                                    </div>
                                </div>

                                <div class="action-grid">
                                    <button type="button" class="secondary-button" data-open-view="login">Back to Login</button>
                                    <button type="button" id="verifyAccountBtn" class="submit-button">Verify Account</button>
                                </div>
                            </div>

                            <div id="activationPhasePassword" class="auth-form hidden">
                                <div class="activation-banner">
                                    <div class="activation-banner-icon">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <div>
                                        <div class="activation-banner-title">Account verified successfully</div>
                                        <div class="activation-banner-text">Create your password to finish activating your PMS account.</div>
                                    </div>
                                </div>

                                <div class="profile-upload">
                                    <div class="profile-preview" id="profilePreview">
                                        <img id="profilePreviewImage" class="hidden" alt="Profile preview">
                                        <i id="profilePreviewIcon" class="fa-regular fa-user"></i>
                                    </div>
                                    <div>
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

                                <div class="field-group">
                                    <label class="field-label" for="act_password">Create Password</label>
                                    <div class="field-shell">
                                        <i class="fa-solid fa-lock leading-icon"></i>
                                        <input type="password" id="act_password" name="password" class="field-input" placeholder="Create your password" autocomplete="new-password">
                                        <button type="button" class="password-toggle" data-toggle-password="act_password" aria-label="Toggle password visibility">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="field-hint">Minimum 8 characters with letters and numbers.</div>
                                    <div class="field-error" data-error-for="act_password"></div>
                                </div>

                                <div class="field-group">
                                    <label class="field-label" for="password_confirmation">Confirm Password</label>
                                    <div class="field-shell">
                                        <i class="fa-solid fa-lock leading-icon"></i>
                                        <input type="password" id="password_confirmation" name="password_confirmation" class="field-input" placeholder="Confirm your password" autocomplete="new-password">
                                        <button type="button" class="password-toggle" data-toggle-password="password_confirmation" aria-label="Toggle password visibility">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="field-error" data-error-for="password_confirmation"></div>
                                </div>

                                <div class="password-strength">
                                    <div class="password-strength-head">
                                        <span>Password Strength</span>
                                        <span id="passwordStrengthText">None</span>
                                    </div>
                                    <div class="password-strength-track">
                                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                                    </div>
                                </div>

                                <div class="action-grid">
                                    <button type="button" id="backToVerifyBtn" class="secondary-button">Back</button>
                                    <button type="submit" id="activateAccountBtn" class="submit-button">Activate Account</button>
                                </div>
                            </div>

                            <div class="footer-link">
                                Already activated?
                                <a href="#" class="text-link" data-open-view="login">Login here</a>
                            </div>
                        </form>
                    </section>

                    <section class="auth-view" id="resetView" data-auth-view="reset">
                        <form id="resetForm" class="auth-form" novalidate>
                            <p class="section-intro">Enter your PMS email address and we’ll send you a password reset link.</p>

                            <div class="field-group">
                                <label class="field-label" for="reset_email">Email Address</label>
                                <div class="field-shell">
                                    <i class="fa-regular fa-envelope leading-icon"></i>
                                    <input type="email" id="reset_email" name="email" class="field-input" placeholder="employee@company.com" autocomplete="email">
                                </div>
                                <div class="field-error" data-error-for="reset_email"></div>
                            </div>

                            <div class="activation-banner">
                                <div class="activation-banner-icon">
                                    <i class="fa-regular fa-paper-plane"></i>
                                </div>
                                <div>
                                    <div class="activation-banner-title">Reset link delivery</div>
                                    <div class="activation-banner-text">A secure password reset link will be sent to your email address.</div>
                                </div>
                            </div>

                            <button type="submit" id="sendResetLinkBtn" class="submit-button">Send Reset Link</button>

                            <div class="footer-link">
                                Remembered your password?
                                <a href="#" class="text-link" data-open-view="login">Login here</a>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </section>
    </main>

    @include('partials.auth-snackbar')

    <script>
        (() => {
            const titleMap = {
                login: {
                    title: 'Login to PMS',
                    subtitle: 'Access your performance dashboard securely.',
                    stepSummary: false,
                },
                activation: {
                    title: 'Activate PMS Account',
                    subtitle: 'Verify your account and finish your first-time setup.',
                    stepSummary: true,
                },
                reset: {
                    title: 'Reset PMS Password',
                    subtitle: 'Request a password reset link for your PMS account.',
                    stepSummary: false,
                },
            };

            const authTitle = document.getElementById('authTitle');
            const authSubtitle = document.getElementById('authSubtitle');
            const activationStepSummary = document.getElementById('activationStepSummary');
            const authPage = document.querySelector('.auth-page');
            const authViews = Array.from(document.querySelectorAll('[data-auth-view]'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

            const loginView = document.getElementById('loginView');
            const activationView = document.getElementById('activationView');
            const resetView = document.getElementById('resetView');
            const loginForm = document.getElementById('loginForm');
            const loginNameInput = document.getElementById('login_name');
            const loginPasswordInput = document.getElementById('password');
            const loginSubmitBtn = document.getElementById('loginToDashboardBtn');
            const loginText = document.getElementById('loginText');

            const activationForm = document.getElementById('activationForm');
            const verifyPhase = document.getElementById('activationPhaseVerify');
            const passwordPhase = document.getElementById('activationPhasePassword');
            const verifyBtn = document.getElementById('verifyAccountBtn');
            const backToVerifyBtn = document.getElementById('backToVerifyBtn');
            const activateBtn = document.getElementById('activateAccountBtn');
            const currentStepSpan = document.getElementById('currentStep');
            const employeeIdInput = document.getElementById('employee_id');
            const activationEmailInput = document.getElementById('act_email');
            const activationPasswordInput = document.getElementById('act_password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const passwordStrengthBar = document.getElementById('passwordStrengthBar');
            const passwordStrengthText = document.getElementById('passwordStrengthText');
            const stepIndicators = Array.from(document.querySelectorAll('.step-indicator'));
            const profilePhotoInput = document.getElementById('profilePhoto');
            const profilePreviewImage = document.getElementById('profilePreviewImage');
            const profilePreviewIcon = document.getElementById('profilePreviewIcon');
            const removeProfilePhotoBtn = document.getElementById('removeProfilePhoto');

            const resetForm = document.getElementById('resetForm');
            const resetEmailInput = document.getElementById('reset_email');
            const sendResetLinkBtn = document.getElementById('sendResetLinkBtn');

            let currentActivationStep = 1;
            let verifiedData = null;

            const notify = (type, message) => {
                if (window.PMSnackbar) {
                    window.PMSnackbar.clear();
                    window.PMSnackbar.show({
                        type,
                        message,
                        durationMs: type === 'error' ? 4500 : 3200,
                    });
                    return;
                }

                window.alert(message);
            };

            const parseJsonSafe = async response => {
                try {
                    return await response.json();
                } catch {
                    return null;
                }
            };

            const setButtonLoading = (button, text) => {
                const original = button.innerHTML;
                button.innerHTML = `<span class="loading-spinner"></span>${text}`;
                button.disabled = true;
                return original;
            };

            const restoreButton = (button, original) => {
                button.innerHTML = original;
                button.disabled = false;
            };

            const fieldErrorElement = input => document.querySelector(`[data-error-for="${input.id}"]`);

            const clearFieldError = input => {
                input.classList.remove('is-error');
                const errorElement = fieldErrorElement(input);
                if (errorElement) {
                    errorElement.textContent = '';
                    errorElement.classList.remove('is-visible');
                }
            };

            const showFieldError = (input, message) => {
                input.classList.add('is-error');
                const errorElement = fieldErrorElement(input);
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.add('is-visible');
                }
                input.focus();
            };

            const clearErrorsInView = view => {
                view.querySelectorAll('.field-input').forEach(clearFieldError);
            };

            const switchView = viewName => {
                authViews.forEach(view => {
                    view.classList.toggle('is-active', view.dataset.authView === viewName);
                });

                if (authPage) {
                    authPage.dataset.mobileGap = ['login', 'reset'].includes(viewName) ? 'extended' : 'compact';
                }

                const meta = titleMap[viewName];
                authTitle.textContent = meta.title;
                authSubtitle.textContent = meta.subtitle;
                activationStepSummary.classList.toggle('hidden', !meta.stepSummary);
                clearErrorsInView(document.querySelector(`[data-auth-view="${viewName}"]`));

                if (viewName === 'login') {
                    loginNameInput.focus();
                } else if (viewName === 'activation') {
                    if (currentActivationStep === 1) {
                        employeeIdInput.focus();
                    } else {
                        activationPasswordInput.focus();
                    }
                } else if (viewName === 'reset') {
                    resetEmailInput.focus();
                }
            };

            document.querySelectorAll('[data-open-view]').forEach(link => {
                link.addEventListener('click', event => {
                    event.preventDefault();
                    const targetView = link.dataset.openView;
                    if (targetView === 'activation' && currentActivationStep !== 1) {
                        goToActivationStep(1);
                    }
                    switchView(targetView);
                });
            });

            document.querySelectorAll('[data-toggle-password]').forEach(button => {
                button.addEventListener('click', () => {
                    const input = document.getElementById(button.dataset.togglePassword);
                    const icon = button.querySelector('i');
                    if (!input || !icon) {
                        return;
                    }

                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    icon.classList.toggle('fa-eye', !isHidden);
                    icon.classList.toggle('fa-eye-slash', isHidden);
                });
            });

            const calculatePasswordStrength = password => {
                let strength = 0;

                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/\d/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                return strength;
            };

            const updatePasswordStrength = password => {
                const strength = calculatePasswordStrength(password);
                const states = [
                    { label: 'None', width: '0%', color: 'transparent' },
                    { label: 'Weak', width: '25%', color: '#ef4444' },
                    { label: 'Fair', width: '50%', color: '#f59e0b' },
                    { label: 'Good', width: '75%', color: '#10b981' },
                    { label: 'Strong', width: '100%', color: '#22c55e' },
                ];
                const state = states[strength];
                passwordStrengthText.textContent = state.label;
                passwordStrengthBar.style.width = state.width;
                passwordStrengthBar.style.backgroundColor = state.color;
            };

            const resetProfilePhoto = () => {
                profilePhotoInput.value = '';
                profilePreviewImage.src = '';
                profilePreviewImage.classList.add('hidden');
                profilePreviewIcon.classList.remove('hidden');
                removeProfilePhotoBtn.classList.add('hidden');
            };

            const goToActivationStep = step => {
                currentActivationStep = step;
                verifyPhase.classList.toggle('hidden', step !== 1);
                passwordPhase.classList.toggle('hidden', step !== 2);
                currentStepSpan.textContent = String(step);
                stepIndicators.forEach(indicator => {
                    indicator.classList.toggle('active', Number(indicator.dataset.step) <= step);
                });
            };

            activationPasswordInput.addEventListener('input', event => {
                clearFieldError(activationPasswordInput);
                updatePasswordStrength(event.target.value);
            });

            [loginNameInput, loginPasswordInput, employeeIdInput, activationEmailInput, activationPasswordInput, confirmPasswordInput, resetEmailInput].forEach(input => {
                input.addEventListener('input', () => clearFieldError(input));
            });

            profilePhotoInput.addEventListener('change', () => {
                const file = profilePhotoInput.files?.[0];
                if (!file) {
                    resetProfilePhoto();
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    resetProfilePhoto();
                    notify('error', 'Please select a valid image file.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = event => {
                    profilePreviewImage.src = event.target?.result ?? '';
                    profilePreviewImage.classList.remove('hidden');
                    profilePreviewIcon.classList.add('hidden');
                    removeProfilePhotoBtn.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });

            removeProfilePhotoBtn.addEventListener('click', resetProfilePhoto);
            backToVerifyBtn.addEventListener('click', () => goToActivationStep(1));

            loginForm.addEventListener('submit', async event => {
                event.preventDefault();
                clearErrorsInView(loginView);

                const identifier = loginNameInput.value.trim();
                const password = loginPasswordInput.value.trim();

                if (!identifier) {
                    showFieldError(loginNameInput, 'Name (login name) is required.');
                    return;
                }

                if (!password) {
                    showFieldError(loginPasswordInput, 'Password is required.');
                    return;
                }

                const original = setButtonLoading(loginSubmitBtn, 'Signing in...');
                loginText.classList.add('hidden');

                try {
                    const response = await fetch('/login', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new URLSearchParams({
                            name: identifier,
                            password,
                            remember: document.getElementById('remember').checked ? 'on' : '',
                        }),
                    });

                    const payload = await parseJsonSafe(response);

                    if (response.ok) {
                        window.location.href = '/dashboard';
                        return;
                    }

                    if (response.status === 422) {
                        const errors = payload?.errors ?? {};
                        if (errors.name?.length) {
                            showFieldError(loginNameInput, errors.name[0]);
                        }
                        if (errors.password?.length) {
                            showFieldError(loginPasswordInput, errors.password[0]);
                        }
                        if (payload?.message === 'Activate account first.') {
                            showFieldError(loginNameInput, payload.message);
                        }
                        return;
                    }

                    if (response.status === 401) {
                        showFieldError(loginNameInput, 'Invalid credentials.');
                        return;
                    }

                    notify('error', payload?.message ?? 'Login failed. Please try again.');
                } catch {
                    notify('error', 'Login failed. Please try again.');
                } finally {
                    restoreButton(loginSubmitBtn, original);
                    loginText.classList.remove('hidden');
                }
            });

            verifyBtn.addEventListener('click', async () => {
                clearErrorsInView(activationView);

                const employeeId = employeeIdInput.value.trim().toUpperCase();
                const email = activationEmailInput.value.trim();
                const employeeIdPattern = /^EMP-[A-Z]{3}-\d{4}$/i;
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!employeeId) {
                    showFieldError(employeeIdInput, 'Employee ID is required.');
                    return;
                }

                if (!email) {
                    showFieldError(activationEmailInput, 'Email address is required.');
                    return;
                }

                if (!employeeIdPattern.test(employeeId)) {
                    showFieldError(employeeIdInput, 'Invalid format. Use EMP-ABC-0000.');
                    return;
                }

                if (!emailPattern.test(email)) {
                    showFieldError(activationEmailInput, 'Please enter a valid email address.');
                    return;
                }

                const original = setButtonLoading(verifyBtn, 'Verifying...');

                try {
                    const response = await fetch('/activate/verify', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            employee_id: employeeId,
                            email,
                        }),
                    });

                    const payload = await parseJsonSafe(response);

                    if (response.ok) {
                        verifiedData = {
                            employeeId,
                            email,
                            token: payload?.token ?? '',
                        };
                        goToActivationStep(2);
                        notify('success', 'Account verified. You can now create your password.');
                        return;
                    }

                    if (response.status === 409) {
                        notify('info', payload?.message ?? 'Account already activated. Please login.');
                        switchView('login');
                        return;
                    }

                    const message = payload?.message ?? 'Account verification failed.';
                    if (/email/i.test(message)) {
                        showFieldError(activationEmailInput, message);
                    } else {
                        showFieldError(employeeIdInput, message);
                    }
                } catch {
                    notify('error', 'Unable to verify your account. Please try again.');
                } finally {
                    restoreButton(verifyBtn, original);
                }
            });

            activationForm.addEventListener('submit', async event => {
                event.preventDefault();

                if (currentActivationStep !== 2) {
                    return;
                }

                clearErrorsInView(activationView);

                const password = activationPasswordInput.value.trim();
                const confirmation = confirmPasswordInput.value.trim();

                if (!password) {
                    showFieldError(activationPasswordInput, 'Password is required.');
                    return;
                }

                if (!confirmation) {
                    showFieldError(confirmPasswordInput, 'Please confirm your password.');
                    return;
                }

                if (password !== confirmation) {
                    showFieldError(confirmPasswordInput, 'Passwords do not match.');
                    return;
                }

                if (calculatePasswordStrength(password) < 2) {
                    showFieldError(activationPasswordInput, 'Use a stronger password with at least 8 characters and letters and numbers.');
                    return;
                }

                if (!verifiedData?.token) {
                    goToActivationStep(1);
                    notify('error', 'Please verify your account first.');
                    return;
                }

                const original = setButtonLoading(activateBtn, 'Activating...');

                try {
                    const formData = new FormData();
                    formData.append('token', verifiedData.token);
                    formData.append('password', password);
                    formData.append('password_confirmation', confirmation);
                    if (profilePhotoInput.files?.length) {
                        formData.append('profile_photo', profilePhotoInput.files[0]);
                    }

                    const response = await fetch('/activate/complete', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });

                    const payload = await parseJsonSafe(response);

                    if (response.ok) {
                        verifiedData = null;
                        activationForm.reset();
                        resetProfilePhoto();
                        updatePasswordStrength('');
                        goToActivationStep(1);
                        switchView('login');
                        notify('success', payload?.message ?? 'Account activated successfully.');
                        return;
                    }

                    if (payload?.errors?.password?.length) {
                        showFieldError(activationPasswordInput, payload.errors.password[0]);
                    } else if (payload?.errors?.token?.length) {
                        goToActivationStep(1);
                        notify('error', payload.errors.token[0]);
                    } else {
                        notify('error', payload?.message ?? 'Unable to activate your account.');
                    }
                } catch {
                    notify('error', 'Unable to activate your account. Please try again.');
                } finally {
                    restoreButton(activateBtn, original);
                }
            });

            resetForm.addEventListener('submit', async event => {
                event.preventDefault();
                clearErrorsInView(resetView);

                const email = resetEmailInput.value.trim();
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!email) {
                    showFieldError(resetEmailInput, 'Email address is required.');
                    return;
                }

                if (!emailPattern.test(email)) {
                    showFieldError(resetEmailInput, 'Please enter a valid email address.');
                    return;
                }

                const original = setButtonLoading(sendResetLinkBtn, 'Sending...');

                try {
                    const response = await fetch('/forgot-password', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new URLSearchParams({ email }),
                    });

                    const payload = await parseJsonSafe(response);

                    if (response.ok) {
                        resetForm.reset();
                        notify('success', payload?.status ?? payload?.message ?? 'Password reset link sent successfully.');
                        return;
                    }

                    if (response.status === 422) {
                        const message = payload?.errors?.email?.[0] ?? payload?.message ?? 'Unable to send a reset link.';
                        showFieldError(resetEmailInput, message);
                        return;
                    }

                    notify('error', payload?.message ?? 'Unable to send a reset link.');
                } catch {
                    notify('error', 'Unable to send a reset link. Please try again.');
                } finally {
                    restoreButton(sendResetLinkBtn, original);
                }
            });

            switchView('login');
            goToActivationStep(1);
            updatePasswordStrength('');
        })();
    </script>
</body>
</html>
