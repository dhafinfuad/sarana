{{--
    Component: <x-mobile-drawer>
    Slide-in Navigation Drawer untuk mode Mobile.
    Selaras dengan desain sarana_redesain.html & guideline Zero-Breakage.
--}}
<div x-data="{
        open: false,
        openMobileDrawer() {
            this.open = true;
            document.body.classList.add('overflow-hidden');
        },
        closeMobileDrawer() {
            this.open = false;
            document.body.classList.remove('overflow-hidden');
        }
    }"
    @toggle-mobile-drawer.window="open ? closeMobileDrawer() : openMobileDrawer()"
    @open-mobile-drawer.window="openMobileDrawer()"
    @close-mobile-drawer.window="closeMobileDrawer()"
    @keydown.escape.window="closeMobileDrawer()"
    id="mobile-drawer"
    x-show="open"
    x-cloak
    style="display: none;"
    data-modal-container
    class="app-modal-container fixed inset-0 z-50 md:hidden"
    aria-modal="true"
    role="dialog">

    <!-- Backdrop -->
    <div id="mobile-drawer-backdrop"
         @click="closeMobileDrawer()"
         onclick="closeMobileDrawer()"
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="mobile-drawer-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

    <!-- Slide-in Drawer Content -->
    <div id="mobile-drawer-panel"
         x-show="open"
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-250"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="mobile-drawer-panel fixed inset-y-0 left-0 max-w-xs w-full bg-white shadow-2xl z-10 flex flex-col justify-between overflow-hidden">
        
        <!-- Pinned Drawer Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-900 bg-gradient-to-r from-slate-900 to-slate-950 text-white shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="h-10 w-10 rounded-2xl bg-white/10 text-white flex items-center justify-center font-extrabold text-sm border border-white/20 shrink-0 select-none">
                    {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <p class="font-bold text-sm leading-tight text-white truncate max-w-[160px]" title="{{ auth()->user()->name ?? 'Pegawai' }}">
                            {{ auth()->user()->name ?? 'Pegawai' }}
                        </p>
                    </div>
                    <p class="text-[11px] text-slate-300 mt-0.5 font-medium">NIP: {{ auth()->user()->nip_pendek ?? '-' }}</p>
                    @if(!empty(auth()->user()->getPlhPltBadges()))
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach(auth()->user()->getPlhPltBadges() as $badgeLabel)
                                <span class="text-[9px] font-bold text-amber-300 bg-amber-900/60 border border-amber-500/40 px-1.5 py-0.5 rounded-md">
                                    {{ $badgeLabel }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <button @click="closeMobileDrawer()" onclick="closeMobileDrawer()" type="button"
                class="p-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white transition leading-none cursor-pointer shrink-0 ml-2"
                aria-label="Tutup Drawer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Scrollable Drawer Navigation Body -->
        <div class="p-4 space-y-3 flex-1 overflow-y-auto">
                
                <!-- Category 1: Sarana -->
                @if(auth()->user()->canAccessPeminjaman() || auth()->user()->canAccessAdminDashboard())
                <div>
                    <p class="px-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Manajemen Sarana</p>
                    <div class="space-y-1">
                        @if(auth()->user()->canAccessPeminjaman())
                        <a wire:navigate.hover href="{{ route('peminjaman.index') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('peminjaman.index'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('peminjaman.index'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('peminjaman.index') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Peminjaman Kendaraan</span>
                        </a>
                        @endif

                        @if(auth()->user()->canAccessAdminDashboard())
                        <a wire:navigate.hover href="{{ route('admin.dashboard') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('admin.dashboard'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('admin.dashboard'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Dashboard Persetujuan</span>
                        </a>
                        <a wire:navigate.hover href="{{ route('admin.fleet') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('admin.fleet'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('admin.fleet'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.fleet') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 17a2 2 0 11-4 0 2 2 0 014 0zM9 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10"></path>
                            </svg>
                            <span>Manajemen Armada</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Category 2: Permisi -->
                @if(auth()->user()->canAccessPermohonanPermisi() || auth()->user()->canAccessPersetujuanPermisi())
                <div class="pt-2 border-t border-slate-100">
                    <p class="px-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Perizinan Keluar (Permisi)</p>
                    <div class="space-y-1">
                        @if(auth()->user()->canAccessPermohonanPermisi())
                        <a wire:navigate.hover href="{{ route('permisi.permohonan') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('permisi.permohonan'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('permisi.permohonan'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('permisi.permohonan') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Permohonan Izin Pegawai</span>
                        </a>
                        @endif

                        @if(auth()->user()->canAccessPersetujuanPermisi())
                        <a wire:navigate.hover href="{{ route('permisi.persetujuan') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('permisi.persetujuan'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('permisi.persetujuan'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('permisi.persetujuan') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Persetujuan Izin (Atasan)</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Category 3: Asmara -->
                @if(auth()->user()->canAccessAsmaraPermohonan() || auth()->user()->canAccessAsmaraAdmin())
                <div class="pt-2 border-t border-slate-100">
                    <p class="px-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Persuratan Dinas (Asmara)</p>
                    <div class="space-y-1">
                        @if(auth()->user()->canAccessAsmaraPermohonan())
                        <a wire:navigate.hover href="{{ route('asmara.permohonan') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('asmara.permohonan'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('asmara.permohonan'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('asmara.permohonan') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                            <span>Riwayat Surat Saya</span>
                        </a>
                        @endif

                        @if(auth()->user()->canAccessAsmaraAdmin())
                        <a wire:navigate.hover href="{{ route('asmara.admin') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('asmara.admin'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('asmara.admin'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('asmara.admin') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>Semua Surat Keluar</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Category 4: Bon ATK -->
                @if(auth()->user()->canAccessBonPermohonan() || auth()->user()->canAccessBonKelola() || auth()->user()->canAccessBonMaster())
                <div class="pt-2 border-t border-slate-100">
                    <p class="px-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Logistik (Bon ATK)</p>
                    <div class="space-y-1">
                        @if(auth()->user()->canAccessBonPermohonan())
                        <a wire:navigate.hover href="{{ route('bona.permohonan') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('bona.permohonan'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('bona.permohonan'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('bona.permohonan') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>Bon Saya &amp; Katalog</span>
                        </a>
                        @endif

                        @if(auth()->user()->canAccessBonKelola())
                        <a wire:navigate.hover href="{{ route('bona.kelola') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('bona.kelola'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('bona.kelola'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('bona.kelola') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Persetujuan Admin</span>
                        </a>
                        @endif

                        @if(auth()->user()->canAccessBonMaster())
                        <a wire:navigate.hover href="{{ route('bona.master-barang') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('bona.master-barang'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('bona.master-barang'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('bona.master-barang') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7M4 7h16M4 7l2-4h12l2 4M9 11h6"></path>
                            </svg>
                            <span>Master Barang ATK</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Category 5: Admin (Administrator Only) -->
                @if(auth()->user()->isAdministrator())
                <div class="pt-2 border-t border-slate-100">
                    <p class="px-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Administrasi Sistem (Admin)</p>
                    <div class="space-y-1">
                        <a wire:navigate.hover href="{{ route('admin.user') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('admin.user'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('admin.user'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.user') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>Manajemen Pegawai</span>
                        </a>
                        <a wire:navigate.hover href="{{ route('admin.log') }}" @click="closeMobileDrawer()"
                            @class([
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-xs transition text-left group cursor-pointer',
                                'bg-brand-50 text-brand-700 font-bold border-l-4 border-brand-600' => request()->routeIs('admin.log'),
                                'font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-700' => !request()->routeIs('admin.log'),
                            ])>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.log') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span>Log Aktivitas Aplikasi</span>
                        </a>
                    </div>
                </div>
                @endif

            </div>

        <!-- Drawer Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex flex-col gap-2 shrink-0">
            <!-- Ubah Password Button -->
            <button type="button" onclick="closeMobileDrawer(); toggleModalPasswordGlobal('modal-ubah-password-global');"
                class="w-full py-2 px-3 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold transition flex items-center justify-center gap-2 cursor-pointer shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                <span>Ubah Password Akun</span>
            </button>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold hover:bg-rose-500 hover:text-white transition flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Keluar Sesi (Logout)</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Global helper functions untuk pembukaan & penutupan Mobile Drawer
    window.openMobileDrawer = function() {
        window.dispatchEvent(new CustomEvent('open-mobile-drawer'));
    };
    window.closeMobileDrawer = function() {
        window.dispatchEvent(new CustomEvent('close-mobile-drawer'));
    };
    window.toggleMobileDrawer = function() {
        window.dispatchEvent(new CustomEvent('toggle-mobile-drawer'));
    };
</script>
