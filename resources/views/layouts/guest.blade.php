{{--
    Layout untuk halaman guest (login, dll.)
    Props:
      - $title : judul tab browser (opsional)
--}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Sarana') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-TileColor" content="#2563eb">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icons/Icon Sarana.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">

    {{-- Title & Meta --}}
    <title>{{ $title ?? 'Sarana - Masuk ke Akun' }}</title>
    <meta name="description" content="Portal Terpadu Sarana - Masuk ke Akun Pegawai">
    <meta name="robots" content="noindex, nofollow">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#2d7dbe',
                            600: '#2d7dbe',
                            700: '#08629c',
                            800: '#004875',
                            900: '#003152',
                            950: '#0f172a'
                        },
                        secondary: '#08629c',
                        'on-primary': '#ffffff'
                    }
                }
            }
        }
    </script>

    {{-- Vite: Tailwind CSS + App JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        /* Subtle background grid pattern */
        .bg-grid-pattern {
            background-image: radial-gradient(rgba(37, 99, 235, 0.08) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Smooth transition for layout switches */
        .fade-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    @livewireStyles
    {{ $head ?? '' }}
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased selection:bg-blue-100 selection:text-blue-700 bg-grid-pattern flex flex-col justify-between">

    {{ $slot }}

    {{-- Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(r => console.log('[PWA] SW registered. Scope:', r.scope))
                    .catch(e => console.error('[PWA] SW registration failed:', e));
            });
        }
    </script>

    @livewireScripts
    {{ $afterBody ?? '' }}
</body>
</html>
