{{--
    Anonymous Component: <x-layouts.app>
    Layout untuk halaman Authenticated (Dashboard, Form Peminjaman, dll).
    Menerapkan layout mobile-app container (max-w-md mx-auto) dengan PWA safe-areas.
--}}
@props(['title' => 'Beranda', 'showBottomNav' => true])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#f9f9fd">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Sarana') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/svg+xml" href="/icons/Icon Sarana.svg">

    <title>{{ $title }}</title>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Material Symbols --}}
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/material-symbols.css') }}">

    {{-- Vite: Tailwind CSS + App JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: max(640px, 100dvh);
        }
        
        /* Custom Radio Buttons */
        .radio-glass:checked + div {
            background-color: theme('colors.brand.600');
            color: #ffffff;
            border-color: theme('colors.brand.700');
        }
    </style>

    {{ $head ?? '' }}
</head>

<body class="min-h-screen bg-slate-50/70 text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-800 bg-grid-subtle flex flex-col justify-between font-sans pb-safe">

    <!-- Responsive App Container -->
    <div class="w-full min-h-screen relative flex flex-col justify-between">
        {{-- Slot Konten Utama (Header dan Main Area dimasukkan melalui view) --}}
        {{ $slot }}

    </div>

    {{-- Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .catch(e => console.error('[PWA] SW registration failed:', e));
            });
        }
    </script>

    {{ $afterBody ?? '' }}
    <x-mobile-drawer />
    <x-password-modal />
    <x-back-to-top />
    @livewireScripts
</body>
</html>
