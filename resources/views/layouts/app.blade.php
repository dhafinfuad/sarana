<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ================================================================ --}}
    {{-- PWA: Meta Tag Wajib (sesuai guidelines.md §3 & design-system.md) --}}
    {{-- ================================================================ --}}

    {{-- Theme Color: warna primary brand #004875 (sesuai design-system.md §2) --}}
    <meta name="theme-color" content="#004875">

    {{-- Apple / iOS PWA support --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Sarana') }}">

    {{-- Mobile Web App (Android legacy) --}}
    <meta name="mobile-web-app-capable" content="yes">

    {{-- Tile color untuk Windows / Edge --}}
    <meta name="msapplication-TileColor" content="#004875">
    <meta name="msapplication-TileImage" content="/icons/icon-144x144.png">

    {{-- PWA Manifest --}}
    <link rel="manifest" href="/manifest.json">

    <link rel="icon" type="image/svg+xml" href="/icons/Icon Sarana.svg">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">

    {{-- ================================================================ --}}
    {{-- SEO --}}
    {{-- ================================================================ --}}
    <title>{{ $title ?? config('app.name', 'Sarana') }} — Peminjaman Kendaraan Dinas</title>
    <meta name="description" content="{{ $description ?? 'Sistem peminjaman kendaraan dinas yang efisien, terpusat, dan modern.' }}">
    <meta name="robots" content="noindex, nofollow">{{-- Aplikasi internal, jangan diindex --}}

    {{-- ================================================================ --}}
    {{-- Font: Inter — wajib per design-system.md §4 --}}
    {{-- ================================================================ --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preload" as="style"
          href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap">
    <link rel="stylesheet"
          href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap">

    {{-- ================================================================ --}}
    {{-- Vite Assets (Tailwind CSS + App JS) --}}
    {{-- ================================================================ --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Slot untuk head tambahan per-halaman (inline style, meta OG, dsb.) --}}
    {{ $head ?? '' }}
</head>

{{--
    body: font Inter, bg #f7f9ff (background-main design-system),
    teks on-surface #151c23, antialiased untuk keterbacaan optimal.
    h-full agar layout full-viewport bekerja dengan benar.
--}}
<body class="h-full bg-[#f7f9ff] text-[#151c23] font-['Inter',ui-sans-serif,system-ui,sans-serif] antialiased">

    {{-- ============================================================== --}}
    {{-- Top Navigation Bar                                             --}}
    {{-- pt-safe: melindungi area notch/status bar di layar ponsel      --}}
    {{-- (sesuai design-system.md §6 PWA aksesibilitas)                 --}}
    {{-- ============================================================== --}}
    @hasSection('nav')
        @yield('nav')
    @else
        {{-- Slot Livewire / Blade component untuk TopNavBar --}}
        {{ $nav ?? '' }}
    @endif

    {{-- ============================================================== --}}
    {{-- Main Content Area                                              --}}
    {{-- ============================================================== --}}
    <main id="main-content" class="flex-1">
        {{ $slot }}
    </main>

    {{-- ============================================================== --}}
    {{-- Bottom Safe Area                                               --}}
    {{-- pb-safe: melindungi area home indicator iPhone                 --}}
    {{-- ============================================================== --}}
    <div class="pb-safe" aria-hidden="true"></div>

    {{-- Slot untuk konten sebelum </body> (modal, portal, dsb.) --}}
    {{ $afterBody ?? '' }}
    <x-back-to-top />

    {{-- ================================================================ --}}
    {{-- Service Worker Registration                                      --}}
    {{-- Harus di-register di sini (sebelum </body>), bukan di <head>.   --}}
    {{-- Sesuai guidelines.md §3 — Fondasi PWA.                         --}}
    {{-- ================================================================ --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker
                    .register('/sw.js', { scope: '/' })
                    .then(function (registration) {
                        console.log('[PWA] Service Worker registered successfully. Scope:', registration.scope);

                        // Cek apakah ada SW baru yang menunggu
                        registration.addEventListener('updatefound', function () {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', function () {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // SW baru tersedia — tampilkan notifikasi update (opsional)
                                    console.log('[PWA] New Service Worker available. Refresh to update.');
                                    // Dispatch custom event agar Livewire / Alpine bisa menampilkan toast
                                    window.dispatchEvent(new CustomEvent('sw-update-available'));
                                }
                            });
                        });
                    })
                    .catch(function (error) {
                        console.error('[PWA] Service Worker registration failed:', error);
                    });
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
