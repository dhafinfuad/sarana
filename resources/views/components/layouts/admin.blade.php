{{--
    Anonymous Component: <x-layouts.admin>
    Layout untuk halaman Dasbor Admin (Desktop-first approach).
--}}
@props(['title' => 'Admin'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('icons/Icon Sarana.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">

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
    </style>

    {{ $head ?? '' }}
</head>

<body class="min-h-screen bg-slate-50/70 text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-800 bg-grid-subtle flex flex-col justify-between font-sans pb-safe">

    {{-- Responsive App Container --}}
    <div class="w-full min-h-screen relative flex flex-col justify-between">

        {{-- Top Navbar --}}
        <x-admin-topbar />

        {{-- Main Content --}}
        <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
            {{ $slot }}
        </main>
    </div>

    {{ $afterBody ?? '' }}
    <x-mobile-drawer />
    <x-password-modal />
    <x-back-to-top />
    @livewireScripts
</body>
</html>
