<div x-data="{
    showApplicationModal: false,
    tanggalMulai: @entangle('tanggalMulai'),
    tanggalSelesai: @entangle('tanggalSelesai'),
    platMobil: @entangle('platMobil'),
    armadaTersedia: @entangle('armadaTersedia'),
    showExportModal: false,
    showConfirmModal: false,
    showEditModal: false,
    confirmAction: '',
    confirmId: null,
    confirmTitle: '',
    confirmMessage: '',
    
    openConfirm(action, id, title, message) {
        this.confirmAction = action;
        this.confirmId = id;
        this.confirmTitle = title;
        this.confirmMessage = message;
        this.showConfirmModal = true;
    },
    
    executeAction() {
        if (this.confirmAction === 'approve') {
            $wire.approve(this.confirmId);
        } else if (this.confirmAction === 'reject') {
            $wire.reject(this.confirmId);
        } else if (this.confirmAction === 'complete') {
            $wire.complete(this.confirmId);
        } else if (this.confirmAction === 'cancel') {
            $wire.cancel(this.confirmId);
        } else if (this.confirmAction === 'delete') {
            $wire.deleteBooking(this.confirmId);
        }
        this.showConfirmModal = false;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}" @close-edit-modal.window="showEditModal = false" @close-application-modal.window="showApplicationModal = false">
    <!-- Header Area: Redesain Modern -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-normal">
                Dashboard Persetujuan Peminjaman
            </h1>
            <p class="text-slate-500 text-sm mt-1 leading-normal">Real-time analytics and lending approvals manajemen armada dinas.</p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            @if(!auth()->user()->isKepalaKantor())
            <button @click="showApplicationModal = true" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Pengajuan</span>
            </button>
            <button @click="showExportModal = true" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-2xs hover:border-slate-300 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Export</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Session Flash Notifications -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-transition role="alert"
            class="mb-6 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200/80 shadow-xs flex items-start justify-between gap-3 transition-all">
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-bold text-emerald-900">Berhasil</p>
                    <p class="text-xs sm:text-sm text-emerald-700 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-800 transition p-1 rounded-lg hover:bg-emerald-100/60 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition role="alert"
            class="mb-6 p-4 rounded-2xl bg-rose-50/90 border border-rose-200/80 shadow-xs flex items-start justify-between gap-3 transition-all">
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs sm:text-sm font-bold text-rose-900">Terjadi Kesalahan</p>
                    <p class="text-xs sm:text-sm text-rose-700 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-800 transition p-1 rounded-lg hover:bg-rose-100/60 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- 4 Modern Metric KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8" wire:poll.15s>
        <!-- Card 1: Total Peminjaman -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Peminjaman</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ number_format($this->analytics['total'] ?? 0) }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Sesuai filter tabel</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 border border-brand-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                </svg>
            </div>
        </div>

        <!-- Card 2: Top Vehicle -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card flex items-start justify-between">
            <div class="min-w-0 flex-1 pr-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Top Vehicle</p>
                @if($this->analytics['topVehicle'])
                    <h4 class="text-base font-bold text-slate-900 mt-2 truncate leading-snug">
                        {{ $this->analytics['topVehicle']->vehicle->nama_kendaraan }}
                    </h4>
                    <div class="flex items-center gap-1.5 mt-2">
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-md bg-brand-50 text-brand-700 border border-brand-100 font-mono">
                            {{ $this->analytics['topVehicle']->vehicle->plat_nomor }}
                        </span>
                        <span class="text-xs text-slate-500 font-medium">&bull; {{ $this->analytics['topVehicle']->count }} trips</span>
                    </div>
                @else
                    <p class="text-xs text-slate-400 font-medium mt-2">Belum ada data</p>
                @endif
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100 mt-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>

        <!-- Card 3: Top Seksi -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card flex items-start justify-between">
            <div class="min-w-0 flex-1 pr-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Top Seksi</p>
                @if($this->analytics['topSeksi'])
                    <h4 class="text-sm font-bold text-slate-900 mt-2 line-clamp-1 leading-snug">
                        {{ $this->analytics['topSeksi']->seksi }}
                    </h4>
                    <p class="text-xs text-slate-500 font-medium mt-2">{{ $this->analytics['topSeksi']->count }} peminjaman armada</p>
                @else
                    <p class="text-xs text-slate-400 font-medium mt-2">Belum ada data</p>
                @endif
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100 mt-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>

        <!-- Card 4: Top Pegawai -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card flex items-start justify-between">
            <div class="min-w-0 flex-1 pr-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Top Pegawai</p>
                @if($this->analytics['topPegawai'])
                    <h4 class="text-base font-bold text-slate-900 mt-2 truncate leading-snug">
                        {{ $this->analytics['topPegawai']->user->name ?? 'Pengguna Dihapus' }}
                    </h4>
                    <p class="text-xs text-slate-500 font-medium mt-2">{{ $this->analytics['topPegawai']->count }} kali peminjaman</p>
                @else
                    <p class="text-xs text-slate-400 font-medium mt-2">Belum ada data</p>
                @endif
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100 mt-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filter Bar for Lending Management -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card mb-6">
        <div class="flex items-center justify-between mb-3.5 pb-2 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <h3 class="text-sm font-bold text-slate-900">Manajemen Peminjaman</h3>
                @php
                    $pendingCount = \App\Models\Booking::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        {{ $pendingCount }} Menunggu
                    </span>
                @endif
            </div>
            <button type="button" wire:click="$set('search', ''); $set('status', ''); $set('startDate', ''); $set('endDate', '');"
                class="text-xs font-semibold text-brand-600 hover:text-brand-700 hover:underline cursor-pointer">
                Reset Filter
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            <div class="sm:col-span-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Pegawai, Mobil, atau Kota..."
                        class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-xs font-medium rounded-xl border border-slate-200 focus:border-brand-600 outline-none transition" />
                </div>
            </div>
            <div class="sm:col-span-3">
                <select wire:model.live="status" class="w-full px-3.5 py-2.5 bg-slate-50 text-xs font-medium rounded-xl border border-slate-200 focus:border-brand-600 outline-none transition">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\BookingStatus::cases() as $case)
                        <option value="{{ $case->value }}">{{ $case->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-5 grid grid-cols-2 gap-2.5">
                <input type="date" wire:model.live="startDate" class="w-full px-3 py-2 bg-slate-50 text-xs font-medium rounded-xl border border-slate-200 outline-none text-slate-600" />
                <input type="date" wire:model.live="endDate" class="w-full px-3 py-2 bg-slate-50 text-xs font-medium rounded-xl border border-slate-200 outline-none text-slate-600" />
            </div>
        </div>
    </div>

    <!-- Vehicle Lending Approval Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden relative">
        <!-- Loading overlay -->
        <div wire:loading.flex class="absolute inset-0 bg-white/70 backdrop-blur-xs z-10 items-center justify-center">
            <span class="material-symbols-outlined animate-spin text-brand-600 text-3xl">sync</span>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs table-fixed">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                        <th wire:click="sortBy('user.name')" class="w-[15%] py-3 px-3 cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                <span>Pegawai</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortField === 'user.name' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortField === 'user.name' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'user.name' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('vehicle.nama_kendaraan')" class="w-[13%] py-3 px-3 cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                <span>Mobil</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortField === 'vehicle.nama_kendaraan' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortField === 'vehicle.nama_kendaraan' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'vehicle.nama_kendaraan' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('kota')" class="w-[9%] py-3 px-2 cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                <span>Kota Tujuan</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortField === 'kota' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortField === 'kota' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'kota' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('keperluan')" class="w-[21%] py-3 px-3 cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                <span>Keperluan</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortField === 'keperluan' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortField === 'keperluan' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'keperluan' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('tanggal_mulai')" class="w-[9%] py-3 px-2 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                <span>Mulai</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortField === 'tanggal_mulai' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortField === 'tanggal_mulai' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'tanggal_mulai' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('tanggal_selesai')" class="w-[9%] py-3 px-2 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                <span>Selesai</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortField === 'tanggal_selesai' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortField === 'tanggal_selesai' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'tanggal_selesai' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('status')" class="w-[9%] py-3 px-2 text-center cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center justify-center gap-1">
                                <span>Status</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortField === 'status' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortField === 'status' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'status' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        @if(auth()->user()->canManageBookings())
                            <th class="w-[15%] py-3 px-2 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($this->bookings as $booking)
                        @php
                            $statusBadge = match ($booking->status->value) {
                                'pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'],
                                'disetujui' => ['bg' => 'bg-brand-50 text-brand-700 border-brand-200', 'dot' => 'bg-brand-500'],
                                'selesai' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
                                default => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500']
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-3 min-w-0">
                                <p class="font-bold text-slate-900 leading-snug truncate" title="{{ $booking->user->name ?? 'Pengguna Dihapus' }}">{{ $booking->user->name ?? 'Pengguna Dihapus' }}</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight truncate" title="{{ $booking->user->seksi ?? 'Seksi tidak diketahui' }}">{{ $booking->user->seksi ?? 'Seksi tidak diketahui' }}</p>
                            </td>
                            <td class="py-3 px-3 min-w-0">
                                <p class="font-bold text-slate-800 leading-snug truncate" title="{{ $booking->vehicle->nama_kendaraan }}">{{ $booking->vehicle->nama_kendaraan }}</p>
                                <div class="mt-0.5">
                                    <span class="inline-flex px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono">
                                        {{ $booking->vehicle->plat_nomor }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-2 min-w-0" title="{{ $booking->kota }}">
                                <p class="text-slate-700 font-semibold text-xs leading-snug break-words whitespace-normal">{{ $booking->kota }}</p>
                            </td>
                            <td class="py-3 px-3 min-w-0" title="{{ $booking->keperluan }}">
                                <p class="text-slate-600 text-xs leading-relaxed break-words whitespace-normal">{{ $booking->keperluan }}</p>
                            </td>
                            <td class="py-3 px-2 text-slate-600 whitespace-nowrap text-[11px] font-medium">{{ $booking->tanggal_mulai->format('d M Y') }}</td>
                            <td class="py-3 px-2 text-slate-600 whitespace-nowrap text-[11px] font-medium">{{ $booking->tanggal_selesai->format('d M Y') }}</td>
                            <td class="py-3 px-2 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                    {{ $booking->status->label() }}
                                </span>
                            </td>
                            @if(auth()->user()->canManageBookings())
                                <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-0.5">
                                        @if($booking->isPending())
                                            <button @click="openConfirm('approve', {{ $booking->id }}, 'Konfirmasi Persetujuan', 'Setujui peminjaman untuk: {{ addslashes($booking->user->name ?? '') }} ({{ addslashes($booking->vehicle->nama_kendaraan ?? '') }})?')"
                                                class="p-1 text-emerald-600 hover:bg-emerald-50 rounded-lg border border-transparent hover:border-emerald-200 transition cursor-pointer shrink-0" title="Setujui Peminjaman">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                            <button @click="openConfirm('reject', {{ $booking->id }}, 'Konfirmasi Penolakan', 'Tolak peminjaman untuk: {{ addslashes($booking->user->name ?? '') }}?')"
                                                class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0" title="Tolak Peminjaman">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        @endif
                                        @if($booking->isApproved())
                                            <button @click="openConfirm('complete', {{ $booking->id }}, 'Konfirmasi Penyelesaian', 'Tandai peminjaman armada oleh {{ addslashes($booking->user->name ?? '') }} telah selesai?')"
                                                class="p-1 text-emerald-600 hover:bg-emerald-50 rounded-lg border border-transparent hover:border-emerald-200 transition cursor-pointer shrink-0" title="Selesaikan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </button>
                                            <button @click="openConfirm('cancel', {{ $booking->id }}, 'Konfirmasi Pembatalan', 'Batalkan peminjaman untuk {{ addslashes($booking->user->name ?? '') }}?')"
                                                class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0" title="Batalkan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            </button>
                                        @endif
                                        <button @click="showEditModal = true; $wire.openEdit({{ $booking->id }})"
                                            class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition cursor-pointer shrink-0" title="Edit Data">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="openConfirm('delete', {{ $booking->id }}, 'Konfirmasi Hapus Data', 'Hapus data peminjaman {{ addslashes($booking->user->name ?? '') }} secara permanen?')"
                                            class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0" title="Hapus Data">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canManageBookings() ? '8' : '7' }}" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p>Tidak ada data peminjaman yang ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden space-y-3 p-4">
            @forelse($this->bookings as $booking)
                @php
                    $statusBadge = match ($booking->status->value) {
                        'pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'],
                        'disetujui' => ['bg' => 'bg-brand-50 text-brand-700 border-brand-200', 'dot' => 'bg-brand-500'],
                        'selesai' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
                        default => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500']
                    };
                @endphp
                <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex flex-col gap-3" wire:key="booking-card-{{ $booking->id }}">
                    <!-- Card Header: Pegawai & Status -->
                    <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3">
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-slate-900 text-sm leading-tight truncate" title="{{ $booking->user->name ?? 'Pengguna Dihapus' }}">
                                {{ $booking->user->name ?? 'Pengguna Dihapus' }}
                            </h4>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5 truncate" title="{{ $booking->user->seksi ?? 'Seksi tidak diketahui' }}">
                                {{ $booking->user->seksi ?? 'Seksi tidak diketahui' }}
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border shrink-0 {{ $statusBadge['bg'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                            {{ $booking->status->label() }}
                        </span>
                    </div>

                    <!-- Card Body Details -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Mobil</p>
                            <p class="font-bold text-slate-800 text-xs truncate" title="{{ $booking->vehicle->nama_kendaraan }}">
                                {{ $booking->vehicle->nama_kendaraan }}
                            </p>
                            <span class="inline-flex px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono mt-1">
                                {{ $booking->vehicle->plat_nomor }}
                            </span>
                        </div>
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Tujuan</p>
                            <p class="font-bold text-slate-800 text-xs truncate" title="{{ $booking->kota }}">
                                {{ $booking->kota }}
                            </p>
                            @if($booking->provinsi)
                                <p class="text-[11px] text-slate-400 truncate">{{ $booking->provinsi }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Jadwal Pelaksanaan -->
                    <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Mulai</p>
                            <p class="font-semibold text-slate-700 text-xs mt-0.5">{{ $booking->tanggal_mulai->format('d M Y') }}</p>
                        </div>
                        <div class="text-slate-300 px-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                        <div class="text-right">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Selesai</p>
                            <p class="font-semibold text-slate-700 text-xs mt-0.5">{{ $booking->tanggal_selesai->format('d M Y') }}</p>
                        </div>
                    </div>

                    <!-- Keperluan -->
                    @if($booking->keperluan)
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 text-xs">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Keperluan:</p>
                            <p class="text-slate-700 leading-relaxed">{{ $booking->keperluan }}</p>
                        </div>
                    @endif

                    <!-- Card Actions -->
                    @if(auth()->user()->canManageBookings())
                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 flex-wrap">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if($booking->isPending())
                                    <button type="button" @click="openConfirm('approve', {{ $booking->id }}, 'Konfirmasi Persetujuan', 'Setujui peminjaman untuk: {{ addslashes($booking->user->name ?? '') }} ({{ addslashes($booking->vehicle->nama_kendaraan ?? '') }})?')"
                                        class="px-3 py-1.5 text-xs font-bold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Setujui</span>
                                    </button>
                                    <button type="button" @click="openConfirm('reject', {{ $booking->id }}, 'Konfirmasi Penolakan', 'Tolak peminjaman untuk: {{ addslashes($booking->user->name ?? '') }}?')"
                                        class="px-3 py-1.5 text-xs font-bold rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        <span>Tolak</span>
                                    </button>
                                @endif
                                @if($booking->isApproved())
                                    <button type="button" @click="openConfirm('complete', {{ $booking->id }}, 'Konfirmasi Penyelesaian', 'Tandai peminjaman armada oleh {{ addslashes($booking->user->name ?? '') }} telah selesai?')"
                                        class="px-3 py-1.5 text-xs font-bold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Selesai</span>
                                    </button>
                                    <button type="button" @click="openConfirm('cancel', {{ $booking->id }}, 'Konfirmasi Pembatalan', 'Batalkan peminjaman untuk {{ addslashes($booking->user->name ?? '') }}?')"
                                        class="px-3 py-1.5 text-xs font-bold rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        <span>Batal</span>
                                    </button>
                                @endif
                            </div>

                            <div class="flex items-center gap-1 ml-auto">
                                <button type="button" @click="showEditModal = true; $wire.openEdit({{ $booking->id }})"
                                    class="p-2 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Edit Data">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" @click="openConfirm('delete', {{ $booking->id }}, 'Konfirmasi Hapus Data', 'Hapus data peminjaman {{ addslashes($booking->user->name ?? '') }} secara permanen?')"
                                    class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Hapus Data">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="py-12 text-center text-slate-400">
                    <svg class="w-12 h-12 text-slate-300 mb-2 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-xs">Tidak ada data peminjaman yang ditemukan.</p>
                </div>
            @endforelse
        </div>

        @if($this->bookings->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $this->bookings->links() }}
            </div>
        @endif
    </div>

    <!-- Global Confirmation Modal -->
    <x-modal show="showConfirmModal" maxWidth="max-w-sm">
        <div class="text-center p-2">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3 border"
                :class="{'bg-emerald-50 text-emerald-600 border-emerald-100': confirmAction === 'approve' || confirmAction === 'complete', 'bg-rose-50 text-rose-600 border-rose-100': confirmAction === 'reject' || confirmAction === 'cancel' || confirmAction === 'delete'}">
                <template x-if="confirmAction === 'approve' || confirmAction === 'complete'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </template>
                <template x-if="confirmAction === 'reject' || confirmAction === 'cancel' || confirmAction === 'delete'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </template>
            </div>
            <h3 class="text-base font-bold text-slate-900" x-text="confirmTitle"></h3>
            <p class="text-xs text-slate-500 mt-1 mb-5" x-text="confirmMessage"></p>
            <div class="flex justify-center gap-2.5">
                <button @click="showConfirmModal = false" type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button x-show="confirmAction === 'approve' || confirmAction === 'complete'" @click="executeAction()" type="button"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/25 transition cursor-pointer">
                    Ya, Lanjutkan
                </button>
                <button x-show="confirmAction === 'reject' || confirmAction === 'cancel' || confirmAction === 'delete'" @click="executeAction()" type="button"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/25 transition cursor-pointer">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Export Modal -->
    <x-modal show="showExportModal" maxWidth="max-w-md">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h3 class="text-base font-bold text-slate-900">Export Laporan Peminjaman</h3>
                <button type="button" @click="showExportModal = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none cursor-pointer">&times;</button>
            </div>
        </x-slot:header>

        <p class="text-xs text-slate-500 mb-4">Pilih rentang periode laporan yang ingin diunduh (Format: Excel .xlsx).</p>

        <div class="space-y-4 text-xs">
            <div>
                <p class="font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-2">Periode Mulai</p>
                <div class="grid grid-cols-2 gap-3">
                    <select wire:model="exportStartMonth" class="w-full px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 outline-none text-xs">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    <select wire:model="exportStartYear" class="w-full px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 outline-none text-xs">
                        @foreach(range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <p class="font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-2">Periode Selesai</p>
                <div class="grid grid-cols-2 gap-3">
                    <select wire:model="exportEndMonth" class="w-full px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 outline-none text-xs">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    <select wire:model="exportEndYear" class="w-full px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 outline-none text-xs">
                        @foreach(range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <button @click="showExportModal = false" type="button"
                class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                Batal
            </button>
            <a :href="'{{ route('admin.export-bookings') }}?start_month=' + $wire.exportStartMonth + '&start_year=' + $wire.exportStartYear + '&end_month=' + $wire.exportEndMonth + '&end_year=' + $wire.exportEndYear"
                @click="showExportModal = false"
                class="px-5 py-2.5 rounded-xl text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white shadow-md shadow-brand-600/25 flex items-center gap-1.5 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Unduh Laporan</span>
            </a>
        </x-slot:footer>
    </x-modal>

    <!-- Edit Modal -->
    <x-modal show="showEditModal" maxWidth="max-w-xl">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h3 class="text-base font-bold text-slate-900">Edit Peminjaman</h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none cursor-pointer">&times;</button>
            </div>
        </x-slot:header>

        <form wire:submit.prevent="saveEdit" class="space-y-4 text-xs">
            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Provinsi</label>
                    <select wire:model.live="editProvinsi" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
                        <option value="">Pilih Provinsi</option>
                        @foreach($this->getJavaRegions() as $provName => $cities)
                            <option value="{{ $provName }}">{{ $provName }}</option>
                        @endforeach
                    </select>
                    @error('editProvinsi') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Kota Tujuan</label>
                    <select wire:model="editKota" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
                        <option value="">Pilih Kota/Kabupaten</option>
                        @if($editProvinsi && array_key_exists($editProvinsi, $this->getJavaRegions()))
                            @foreach($this->getJavaRegions()[$editProvinsi] as $cityName)
                                <option value="{{ $cityName }}">{{ $cityName }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('editKota') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Keperluan</label>
                <textarea wire:model="editKeperluan" rows="3" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 resize-none text-xs"></textarea>
                @error('editKeperluan') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Tanggal Mulai</label>
                    <input type="date" wire:model="editTanggalMulai" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs" />
                    @error('editTanggalMulai') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Tanggal Selesai</label>
                    <input type="date" wire:model="editTanggalSelesai" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs" />
                    @error('editTanggalSelesai') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Armada (Kendaraan)</label>
                <select wire:model="editVehicleId" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
                    <option value="">-- Pilih Kendaraan --</option>
                    @foreach($this->availableVehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->nama_kendaraan }} ({{ $v->plat_nomor }})</option>
                    @endforeach
                </select>
                @error('editVehicleId') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2.5">
                <button type="button" @click="showEditModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer flex items-center gap-1.5">
                    <span>Simpan Perubahan</span>
                    <span wire:loading wire:target="saveEdit" class="material-symbols-outlined animate-spin text-[14px]">refresh</span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Form Pengajuan Modal (Admin) -->
    <x-modal show="showApplicationModal" maxWidth="max-w-lg">
        <x-slot:header>
            <div class="flex items-center justify-between w-full">
                <h3 class="text-base font-bold text-slate-900">Form Pengajuan Peminjaman</h3>
                <button @click="showApplicationModal = false" type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none cursor-pointer">&times;</button>
            </div>
        </x-slot:header>

        <form wire:submit.prevent="submitForm" class="space-y-4 text-xs">
            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Provinsi Tujuan <span class="text-rose-500">*</span></label>
                    <select wire:model.live="provinsi" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
                        <option value="">Pilih Provinsi</option>
                        @foreach($this->getJavaRegions() as $provName => $cities)
                            <option value="{{ $provName }}">{{ $provName }}</option>
                        @endforeach
                    </select>
                    @error('provinsi') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Kota/Kab Tujuan <span class="text-rose-500">*</span></label>
                    <select wire:model="kota" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
                        <option value="">Pilih Kota/Kabupaten</option>
                        @if($provinsi && array_key_exists($provinsi, $this->getJavaRegions()))
                            @foreach($this->getJavaRegions()[$provinsi] as $cityName)
                                <option value="{{ $cityName }}">{{ $cityName }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('kota') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Keperluan <span class="text-rose-500">*</span></label>
                <textarea wire:model="keperluan" rows="3" required placeholder="Jelaskan keperluan peminjaman..."
                    class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 resize-none text-xs"></textarea>
                @error('keperluan') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input wire:model.live="tanggalMulai" type="date" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs" />
                    @error('tanggalMulai') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input wire:model.live="tanggalSelesai" type="date" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs" />
                    @error('tanggalSelesai') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Pilihan Armada <span class="text-rose-500">*</span></label>
                <div class="relative" x-data="{ open: false }">
                    <select wire:model="platMobil" class="hidden">
                        <option value=""></option>
                        <template x-for="armada in armadaTersedia" :key="armada.plat">
                            <option :value="armada.plat"></option>
                        </template>
                    </select>

                    <button @click="if(tanggalMulai && tanggalSelesai && armadaTersedia.length > 0) open = !open"
                        :disabled="!(tanggalMulai && tanggalSelesai)"
                        :class="(tanggalMulai && tanggalSelesai) ? 'bg-slate-50 text-slate-800 border-slate-200' : 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed'"
                        class="w-full flex items-center justify-between border rounded-xl px-3.5 py-2.5 text-left transition focus:border-brand-600 text-xs cursor-pointer"
                        type="button">
                        <div class="flex items-center gap-2">
                            <template x-if="!platMobil">
                                <span class="text-xs" x-text="(tanggalMulai && tanggalSelesai) ? (armadaTersedia.length > 0 ? 'Pilih Armada Tersedia...' : 'Tidak ada armada tersedia') : 'Pilih tanggal terlebih dahulu'"></span>
                            </template>
                            <template x-if="platMobil">
                                <div class="flex items-center gap-2">
                                    <template x-for="a in armadaTersedia">
                                        <template x-if="a.plat === platMobil">
                                            <div class="flex items-center gap-2">
                                                <img :src="a.img" class="w-6 h-4 object-cover rounded border border-slate-200" />
                                                <span class="text-xs font-bold text-slate-800" x-text="a.nama + ' (' + a.plat + ')'"></span>
                                            </div>
                                        </template>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center gap-2">
                            <span wire:loading wire:target="tanggalMulai, tanggalSelesai" class="text-brand-600 text-[10px] font-bold">Memuat...</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>

                    <div @click.away="open = false"
                        class="absolute z-[70] w-full mt-1.5 bg-white border border-slate-100 rounded-2xl shadow-xl overflow-hidden py-1"
                        x-show="open" x-transition.opacity style="display: none;">
                        <div class="max-h-48 overflow-y-auto">
                            <template x-for="armada in armadaTersedia" :key="armada.plat">
                                <div @click="platMobil = armada.plat; open = false"
                                    class="px-3.5 py-2.5 hover:bg-brand-50/60 cursor-pointer transition flex items-center justify-between border-b border-slate-50 last:border-0"
                                    :class="platMobil === armada.plat ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-700'">
                                    <div class="flex items-center gap-3">
                                        <img :src="armada.img" class="w-8 h-5 object-cover rounded border border-slate-200 bg-slate-100" />
                                        <span class="text-xs" x-text="armada.nama + ' (' + armada.plat + ')'"></span>
                                    </div>
                                    <svg x-show="platMobil === armada.plat" class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                @error('platMobil') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2.5">
                <button @click="showApplicationModal = false" type="button"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 transition cursor-pointer flex items-center gap-1.5">
                    <span>Ajukan Peminjaman</span>
                    <span wire:loading wire:target="submitForm" class="material-symbols-outlined animate-spin text-[14px]">refresh</span>
                </button>
            </div>
        </form>
    </x-modal>
</div>