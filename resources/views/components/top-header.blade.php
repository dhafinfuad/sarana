@props(['title' => 'Page Title', 'backRoute' => null])

<!-- Modern Sleek Header Bar (Sticky Top-0, Blur-md) -->
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-all duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            
            <!-- Left: Wordmark Logo and Primary Modules Navigation -->
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <!-- Mobile Hamburger Drawer Trigger -->
                    <button type="button"
                        onclick="openMobileDrawer()"
                        @click="$dispatch('open-mobile-drawer')"
                        class="md:hidden p-1.5 -ml-1 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-slate-100 active:bg-slate-200 transition focus:outline-none focus:ring-2 focus:ring-brand-500/20 active:scale-95 cursor-pointer"
                        aria-label="Buka Menu Navigasi">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    @if($backRoute)
                        <a href="{{ $backRoute }}"
                            class="md:hidden w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-700 transition active:scale-95"
                            title="Kembali">
                            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                        </a>
                    @endif
                    <a href="{{ route('peminjaman.index') }}" class="flex items-center group py-1">
                        <img src="{{ asset('icons/Logo Sarana.svg') }}" alt="Sarana" class="h-4 sm:h-[18px] w-auto object-contain">
                    </a>
                </div>

                <!-- Main Module Navigation Pills (Desktop) -->
                <nav class="hidden md:flex items-center gap-1.5 pl-4 border-l border-slate-200">
                    
                    <!-- Sarana Dropdown -->
                    @if(auth()->user()->canAccessPeminjaman() || auth()->user()->canAccessAdminDashboard())
                    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                        <button @click="open = !open" @class([
                            'nav-item flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 cursor-pointer',
                            'bg-brand-50 text-brand-700 shadow-2xs' => request()->routeIs('peminjaman.*') || request()->routeIs('admin.dashboard') || request()->routeIs('admin.fleet'),
                            'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' => !request()->routeIs('peminjaman.*') && !request()->routeIs('admin.dashboard') && !request()->routeIs('admin.fleet'),
                        ])>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                            </svg>
                            <span>Sarana</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition.opacity style="display: none;"
                            class="absolute left-0 mt-1.5 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50">
                            @if(auth()->user()->canAccessPeminjaman())
                            <a wire:navigate.hover href="{{ route('peminjaman.index') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Peminjaman</span>
                            </a>
                            @endif
                            @if(auth()->user()->canAccessAdminDashboard())
                            <a wire:navigate.hover href="{{ route('admin.dashboard') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Persetujuan</span>
                            </a>
                            <a wire:navigate.hover href="{{ route('admin.fleet') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 17a2 2 0 11-4 0 2 2 0 014 0zM9 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10"></path></svg>
                                <span>Armada</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Permisi Dropdown -->
                    @if(auth()->user()->canAccessPermohonanPermisi() || auth()->user()->canAccessPersetujuanPermisi())
                    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                        <button @click="open = !open" @class([
                            'nav-item flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 cursor-pointer',
                            'bg-brand-50 text-brand-700 shadow-2xs' => request()->routeIs('permisi.*'),
                            'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' => !request()->routeIs('permisi.*'),
                        ])>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span>Permisi</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition.opacity style="display: none;"
                            class="absolute left-0 mt-1.5 w-44 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50">
                            @if(auth()->user()->canAccessPermohonanPermisi())
                            <a wire:navigate.hover href="{{ route('permisi.permohonan') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Permohonan</span>
                            </a>
                            @endif
                            @if(auth()->user()->canAccessPersetujuanPermisi())
                            <a wire:navigate.hover href="{{ route('permisi.persetujuan') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Persetujuan</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Asmara Dropdown -->
                    @if(auth()->user()->canAccessAsmaraPermohonan() || auth()->user()->canAccessAsmaraAdmin())
                    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                        <button @click="open = !open" @class([
                            'nav-item flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 cursor-pointer',
                            'bg-brand-50 text-brand-700 shadow-2xs' => request()->routeIs('asmara.*'),
                            'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' => !request()->routeIs('asmara.*'),
                        ])>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>Asmara</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition.opacity style="display: none;"
                            class="absolute left-0 mt-1.5 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50">
                            @if(auth()->user()->canAccessAsmaraPermohonan())
                            <a wire:navigate.hover href="{{ route('asmara.permohonan') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                                <span>Riwayat Surat Saya</span>
                            </a>
                            @endif
                            @if(auth()->user()->canAccessAsmaraAdmin())
                            <a wire:navigate.hover href="{{ route('asmara.admin') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <span>Semua Surat Keluar</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Bon ATK Dropdown -->
                    @if(auth()->user()->canAccessBonPermohonan() || auth()->user()->canAccessBonKelola() || auth()->user()->canAccessBonMaster())
                    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                        <button @click="open = !open" @class([
                            'nav-item flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 cursor-pointer',
                            'bg-brand-50 text-brand-700 shadow-2xs' => request()->routeIs('bona.*'),
                            'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' => !request()->routeIs('bona.*'),
                        ])>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span>Bon ATK</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition.opacity style="display: none;"
                            class="absolute left-0 mt-1.5 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50">
                            @if(auth()->user()->canAccessBonPermohonan())
                            <a wire:navigate.hover href="{{ route('bona.permohonan') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>Bon Saya</span>
                            </a>
                            @endif
                            @if(auth()->user()->canAccessBonKelola())
                            <a wire:navigate.hover href="{{ route('bona.kelola') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Persetujuan Admin</span>
                            </a>
                            @endif
                            @if(auth()->user()->canAccessBonMaster())
                            <a wire:navigate.hover href="{{ route('bona.master-barang') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7M4 7h16M4 7l2-4h12l2 4M9 11h6"></path></svg>
                                <span>Master Barang</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Admin Dropdown (Administrator Only) -->
                    @if(auth()->user()->isAdministrator())
                    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                        <button @click="open = !open" @class([
                            'nav-item flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-150 cursor-pointer',
                            'bg-brand-50 text-brand-700 shadow-2xs' => request()->routeIs('admin.user') || request()->routeIs('admin.log'),
                            'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' => !request()->routeIs('admin.user') && !request()->routeIs('admin.log'),
                        ])>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Admin</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition.opacity style="display: none;"
                            class="absolute left-0 mt-1.5 w-44 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50">
                            <a wire:navigate.hover href="{{ route('admin.user') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <span>Pegawai</span>
                            </a>
                            <a wire:navigate.hover href="{{ route('admin.log') }}" class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 flex items-center gap-2.5 transition">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <span>Log Aplikasi</span>
                            </a>
                        </div>
                    </div>
                    @endif

                </nav>
            </div>

            <!-- Right: User Trailing Actions -->
            <div class="flex items-center gap-3">
                <!-- Notifications Badge -->
                <livewire:admin.notification-badge />

                <!-- Settings Button (Change Password) -->
                <button type="button" onclick="toggleModalPasswordGlobal('modal-ubah-password-global')"
                    class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition cursor-pointer hidden sm:inline-flex"
                    title="Ubah Password Akun">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>

                <!-- User Info Pill (Desktop Only: Mobile displays full user info in Mobile Drawer) -->
                <div class="hidden sm:flex items-center gap-2.5 pl-2 sm:border-l sm:border-slate-200">
                    <div class="h-9 w-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-xs border border-slate-700 select-none">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div class="hidden lg:flex flex-col text-left">
                        <div class="flex items-center gap-1.5">
                            @foreach(auth()->user()->getPlhPltBadges() as $badgeLabel)
                                <span class="text-[10px] font-bold text-amber-800 bg-amber-100 border border-amber-300 px-1.5 py-0.2 rounded-md shadow-2xs" title="{{ $badgeLabel }}">
                                    {{ $badgeLabel }}
                                </span>
                            @endforeach
                            <span class="text-xs font-bold text-slate-800 leading-tight truncate max-w-[140px]">
                                {{ str(auth()->user()->name ?? 'Pegawai')->lower()->title()->limit(16) }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium">NIP: {{ auth()->user()->nip_pendek ?? '-' }}</span>
                    </div>
                </div>

                <!-- Modern Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                    @csrf
                    <button type="submit" class="group flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50/60 hover:bg-rose-500 text-rose-700 hover:text-white text-xs font-semibold transition-all duration-200 shadow-2xs ml-1 cursor-pointer">
                        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>