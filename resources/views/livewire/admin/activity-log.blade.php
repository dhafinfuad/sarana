<div>
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 ring-1 ring-brand-600/20">
                    Audit Trail & Telemetri
                </span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Log Aktivitas Sistem</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Memantau riwayat akses, modul, dan penggunaan fitur aplikasi oleh pegawai secara transparan.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-card mb-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <!-- Search -->
            <div class="relative w-full sm:w-72 shrink-0">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari Pegawai / NIP..." 
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition"
                />
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <!-- Modul Selector -->
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider shrink-0">Modul:</label>
                    <select 
                        wire:model.live="module" 
                        class="px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 text-xs sm:text-sm font-medium text-slate-700 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition"
                    >
                        <option value="">Semua Modul</option>
                        <option value="Sarana">Sarana</option>
                        <option value="Permisi">Permisi</option>
                        <option value="Asmara">Asmara</option>
                        <option value="Bon ATK">Bon ATK</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                
                <!-- Start Date -->
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider shrink-0">Mulai:</label>
                    <input 
                        type="date" 
                        wire:model.live="startDate" 
                        class="px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 text-xs sm:text-sm font-medium text-slate-700 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition"
                    />
                </div>
                
                <!-- End Date -->
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider shrink-0">Sampai:</label>
                    <input 
                        type="date" 
                        wire:model.live="endDate" 
                        class="px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 text-xs sm:text-sm font-medium text-slate-700 focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Bento Grid Analytics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8" wire:poll.15s>
        <!-- Card 1: Total Aktivitas -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-lg transition duration-200 relative overflow-hidden flex flex-col justify-between h-36 group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand-500/10 rounded-full blur-2xl group-hover:bg-brand-500/20 transition-colors"></div>
            <div class="flex justify-between items-start relative z-10">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Aktivitas</h3>
                <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">analytics</span>
                </div>
            </div>
            <div class="relative z-10">
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-baseline gap-1.5">
                    {{ number_format($this->analytics['total'] ?? 0) }}
                </div>
                <p class="text-[11px] font-medium text-slate-400 mt-1">Sesuai rentang filter</p>
            </div>
        </div>

        <!-- Card 2: Top Halaman -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-lg transition duration-200 relative overflow-hidden flex flex-col justify-between h-36 group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-colors"></div>
            <div class="flex justify-between items-start relative z-10">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Top Halaman</h3>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">web</span>
                </div>
            </div>
            <div class="relative z-10">
                @if($this->analytics['topHalaman'])
                    <div class="text-base font-bold text-slate-800 tracking-tight truncate" title="{{ $this->analytics['topHalaman']->page_label }}">
                        {{ $this->analytics['topHalaman']->page_label }}
                    </div>
                    <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">
                        <span class="font-bold text-indigo-600">{{ $this->analytics['topHalaman']->page_group }}</span> &bull;
                        {{ number_format($this->analytics['topHalaman']->count) }} kunjungan
                    </p>
                @else
                    <div class="text-xs font-medium text-slate-400 mt-2">Belum ada data kunjungan</div>
                @endif
            </div>
        </div>

        <!-- Card 3: Top Seksi -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-lg transition duration-200 relative overflow-hidden flex flex-col justify-between h-36 group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-colors"></div>
            <div class="flex justify-between items-start relative z-10">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Top Seksi</h3>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">account_tree</span>
                </div>
            </div>
            <div class="relative z-10">
                @if($this->analytics['topSeksi'])
                    <div class="text-base font-bold text-slate-800 tracking-tight truncate" title="{{ $this->analytics['topSeksi']->seksi }}">
                        {{ $this->analytics['topSeksi']->seksi }}
                    </div>
                    <p class="text-[11px] font-medium text-slate-500 mt-0.5">
                        <span class="font-bold text-emerald-600">{{ number_format($this->analytics['topSeksi']->count) }}</span> aktivitas dicatat
                    </p>
                @else
                    <div class="text-xs font-medium text-slate-400 mt-2">Belum ada data seksi</div>
                @endif
            </div>
        </div>

        <!-- Card 4: Top Pegawai -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-card hover:shadow-lg transition duration-200 relative overflow-hidden flex flex-col justify-between h-36 group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-colors"></div>
            <div class="flex justify-between items-start relative z-10">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Top Pegawai</h3>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">badge</span>
                </div>
            </div>
            <div class="relative z-10">
                @if($this->analytics['topPegawai'] && $this->analytics['topPegawai']->user)
                    <div class="flex items-center gap-3">
                        <img 
                            src="{{ $this->analytics['topPegawai']->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($this->analytics['topPegawai']->user->name).'&color=2563eb&background=eff6ff' }}" 
                            alt="{{ $this->analytics['topPegawai']->user->name }}" 
                            class="w-10 h-10 rounded-xl border border-slate-200 object-cover shrink-0 shadow-2xs"
                        />
                        <div class="overflow-hidden min-w-0">
                            <div class="text-xs sm:text-sm font-bold text-slate-800 truncate" title="{{ $this->analytics['topPegawai']->user->name }}">
                                {{ $this->analytics['topPegawai']->user->name }}
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">
                                <span class="font-bold text-amber-600">{{ number_format($this->analytics['topPegawai']->count) }}</span> kunjungan
                            </p>
                        </div>
                    </div>
                @else
                    <div class="text-xs font-medium text-slate-400 mt-2">Belum ada data pegawai</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden flex flex-col">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                        <th wire:click="sortBy('created_at')"
                            class="px-6 py-4 cursor-pointer hover:bg-slate-100 transition-colors group whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <span>Waktu</span>
                                <span class="material-symbols-outlined text-[16px] {{ $sortColumn === 'created_at' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'created_at' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'created_at' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('user.name')"
                            class="px-6 py-4 cursor-pointer hover:bg-slate-100 transition-colors group whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <span>Pegawai</span>
                                <span class="material-symbols-outlined text-[16px] {{ $sortColumn === 'user.name' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'user.name' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'user.name' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('page_group')"
                            class="px-6 py-4 cursor-pointer hover:bg-slate-100 transition-colors group whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <span>Modul</span>
                                <span class="material-symbols-outlined text-[16px] {{ $sortColumn === 'page_group' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'page_group' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'page_group' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('route_name')"
                            class="px-6 py-4 cursor-pointer hover:bg-slate-100 transition-colors group whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <span>Halaman / Rute</span>
                                <span class="material-symbols-outlined text-[16px] {{ $sortColumn === 'route_name' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'route_name' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'route_name' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('ip_address')"
                            class="px-6 py-4 cursor-pointer hover:bg-slate-100 transition-colors group whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <span>IP Address</span>
                                <span class="material-symbols-outlined text-[16px] {{ $sortColumn === 'ip_address' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'ip_address' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'ip_address' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($this->logs as $log)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <!-- Waktu -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-bold text-slate-800">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-[11px] font-mono text-slate-400">{{ $log->created_at->format('H:i:s') }} WIB</div>
                            </td>

                            <!-- Pegawai -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="flex items-center gap-3">
                                        <img 
                                            src="{{ $log->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($log->user->name).'&color=2563eb&background=eff6ff' }}" 
                                            alt="{{ $log->user->name }}" 
                                            class="w-8 h-8 rounded-full border border-slate-200 object-cover shrink-0"
                                        />
                                        <div>
                                            <div class="text-xs sm:text-sm font-bold text-slate-800">{{ $log->user->name }}</div>
                                            <div class="text-[11px] font-medium text-slate-400">{{ $log->user->seksi ?? '-' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs">
                                            <span class="material-symbols-outlined text-[16px]">person_off</span>
                                        </div>
                                        <span class="text-xs text-slate-400 italic">Guest / Sistem</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Modul -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->page_group == 'Sarana')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-blue-600/20">
                                        {{ $log->page_group }}
                                    </span>
                                @elseif($log->page_group == 'Permisi')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-amber-600/20">
                                        {{ $log->page_group }}
                                    </span>
                                @elseif($log->page_group == 'Asmara')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20">
                                        {{ $log->page_group }}
                                    </span>
                                @elseif($log->page_group == 'Bon ATK')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 ring-1 ring-purple-600/20">
                                        {{ $log->page_group }}
                                    </span>
                                @elseif($log->page_group == 'Admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/20">
                                        {{ $log->page_group }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 ring-1 ring-slate-600/20">
                                        {{ $log->page_group ?? 'Sistem' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Halaman -->
                            <td class="px-6 py-4">
                                <div class="text-xs sm:text-sm font-semibold text-slate-800">{{ $log->page_label ?? $log->route_name }}</div>
                                <div class="text-[11px] font-mono text-slate-400 truncate max-w-sm" title="{{ $log->url }}">{{ $log->url }}</div>
                            </td>

                            <!-- IP Address -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-mono font-medium text-slate-600 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200/70 inline-block">
                                    {{ $log->ip_address }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <span class="material-symbols-outlined text-[26px]">search_off</span>
                                </div>
                                <p class="text-sm font-bold text-slate-700">Tidak ada log aktivitas ditemukan</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau rentang tanggal filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden space-y-3 p-4 bg-slate-50/50">
            @forelse($this->logs as $log)
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex flex-col gap-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex items-center gap-1.5 text-slate-500">
                            <span class="material-symbols-outlined text-[16px] text-brand-600">schedule</span>
                            <span class="font-bold text-xs text-slate-800">{{ $log->created_at->format('d M Y, H:i:s') }}</span>
                        </div>
                        @if($log->page_group == 'Sarana')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 ring-1 ring-blue-600/20">Sarana</span>
                        @elseif($log->page_group == 'Permisi')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 ring-1 ring-amber-600/20">Permisi</span>
                        @elseif($log->page_group == 'Asmara')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20">Asmara</span>
                        @elseif($log->page_group == 'Bon ATK')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 ring-1 ring-purple-600/20">Bon ATK</span>
                        @elseif($log->page_group == 'Admin')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/20">Admin</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 ring-1 ring-slate-600/20">{{ $log->page_group ?? 'Sistem' }}</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        @if($log->user)
                            <img 
                                src="{{ $log->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($log->user->name).'&color=2563eb&background=eff6ff' }}" 
                                alt="{{ $log->user->name }}" 
                                class="w-9 h-9 rounded-full border border-slate-200 object-cover shrink-0"
                            />
                            <div class="overflow-hidden min-w-0">
                                <p class="font-bold text-xs text-slate-800 leading-tight truncate">{{ $log->user->name }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $log->user->seksi ?? '-' }}</p>
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">Guest / Sistem</p>
                        @endif
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl text-xs border border-slate-200/60">
                        <p class="font-bold text-slate-800 text-xs">{{ $log->page_label ?? $log->route_name }}</p>
                        <p class="text-[11px] text-slate-400 truncate font-mono mt-0.5">{{ $log->url }}</p>
                        <div class="mt-2 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400">IP Address:</span>
                            <span class="font-mono font-medium text-slate-700">{{ $log->ip_address }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <span class="material-symbols-outlined text-[36px] text-slate-300 mb-1">search_off</span>
                    <p class="text-xs font-bold text-slate-700">Tidak ada log aktivitas.</p>
                </div>
            @endforelse
        </div>
        
        @if($this->logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $this->logs->links() }}
            </div>
        @endif
    </div>
</div>
