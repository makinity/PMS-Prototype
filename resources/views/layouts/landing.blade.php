<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- DUMMY_DATA: replace with dynamic value --}}
    <title>Denji Kun | Portfolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: #f5f7fb;
            color: #0f172a;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            padding: 0;
        }

        .landing-shell {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.25rem;
        }

        .landing-hero {
            position: relative;
            overflow: hidden;
        }

        .landing-hero .halo {
            width: 340px;
            height: 340px;
            background: radial-gradient(circle at 30% 30%, #7cb8ff, #5b8df1);
            border-radius: 50%;
            filter: drop-shadow(0 20px 35px rgba(91, 141, 241, 0.25));
        }

        .landing-hero .profile-card {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }

        .landing-hero .stat-label {
            color: #5f6b7a;
        }

        .social-stack a {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        @media (max-width: 1023px) {
            .landing-hero .halo {
                width: 260px;
                height: 260px;
            }
        }
    </style>
</head>
<body class="antialiased">
    <main>
        {{ $slot }}
    </main>
</body>
</html>
