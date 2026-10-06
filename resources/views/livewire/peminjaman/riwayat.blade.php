<div>
    @if(!auth()->user()->canAccessAdminDashboard())
        <x-top-header title="Riwayat Peminjaman" />
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 md:pb-8 flex-1' }}">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('peminjaman.index') }}" wire:navigate.hover class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-700 transition mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Dasbor</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Riwayat Lengkap Peminjaman</h1>
                <p class="text-slate-500 text-sm mt-1.5">Daftar seluruh pengajuan peminjaman kendaraan armada dinas Anda.</p>
            </div>
            <div class="flex items-center gap-2.5 self-start sm:self-auto">
                <button wire:click="filterThisMonth"
                    class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-2xs hover:border-slate-300 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Bulan Ini</span>
                </button>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
                <!-- Search Bar -->
                <div class="sm:col-span-4">
                    <div class="relative w-full">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search"
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-xs font-medium rounded-xl border border-slate-200 focus:border-brand-600 outline-none transition"
                            placeholder="Cari Mobil, Provinsi, atau Kota..." type="text" />
                    </div>
                </div>
                <!-- Filter Status -->
                <div class="sm:col-span-3">
                    <select wire:model.live="status"
                        class="w-full px-3.5 py-2.5 bg-slate-50 text-xs font-medium rounded-xl border border-slate-200 focus:border-brand-600 outline-none transition">
                        <option value="">Semua Status</option>
                        @foreach(\App\Enums\BookingStatus::cases() as $case)
                            <option value="{{ $case->value }}">{{ $case->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Filter Tanggal Range -->
                <div class="sm:col-span-5 grid grid-cols-2 gap-2.5">
                    <input wire:model.live="startDate" type="date"
                        class="w-full px-3 py-2 bg-slate-50 text-xs font-medium rounded-xl border border-slate-200 outline-none text-slate-600" />
                    <input wire:model.live="endDate" type="date"
                        class="w-full px-3 py-2 bg-slate-50 text-xs font-medium rounded-xl border border-slate-200 outline-none text-slate-600" />
                </div>
            </div>
        </div>

        <!-- Data Table Container -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden relative">
            <!-- Loading overlay -->
            <div wire:loading.flex class="absolute inset-0 bg-white/70 backdrop-blur-xs z-10 items-center justify-center">
                <span class="material-symbols-outlined animate-spin text-brand-600 text-3xl">sync</span>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th wire:click="sortBy('vehicle.nama_kendaraan')"
                                class="py-3.5 px-5 font-bold cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Mobil</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortField === 'vehicle.nama_kendaraan' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortField === 'vehicle.nama_kendaraan' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'vehicle.nama_kendaraan' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('kota')"
                                class="py-3.5 px-4 font-bold cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Tujuan</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortField === 'kota' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortField === 'kota' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'kota' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('keperluan')"
                                class="py-3.5 px-4 font-bold cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Keperluan</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortField === 'keperluan' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortField === 'keperluan' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'keperluan' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('tanggal_mulai')"
                                class="py-3.5 px-4 font-bold cursor-pointer hover:bg-slate-100 transition-colors group whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <span>Mulai</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortField === 'tanggal_mulai' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortField === 'tanggal_mulai' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'tanggal_mulai' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('tanggal_selesai')"
                                class="py-3.5 px-4 font-bold cursor-pointer hover:bg-slate-100 transition-colors group whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <span>Selesai</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortField === 'tanggal_selesai' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortField === 'tanggal_selesai' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'tanggal_selesai' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th class="py-3.5 px-5 text-center font-bold">Status & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bookings as $booking)
                            @php
                                $statusBadge = match ($booking->status->value) {
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'disetujui' => 'bg-brand-50 text-brand-700 border-brand-200',
                                    'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    default => 'bg-rose-50 text-rose-700 border-rose-200'
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-5">
                                    <p class="font-bold text-slate-900 leading-snug">{{ $booking->vehicle->nama_kendaraan ?? 'Kendaraan Dihapus' }}</p>
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono mt-1">
                                        {{ $booking->vehicle->plat_nomor ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-700 font-semibold whitespace-nowrap">
                                    <p>{{ $booking->kota }}</p>
                                    <p class="text-[11px] text-slate-400 font-normal">{{ $booking->provinsi }}</p>
                                </td>
                                <td class="py-4 px-4 text-slate-600 max-w-xs truncate" title="{{ $booking->keperluan }}">
                                    {{ $booking->keperluan }}
                                </td>
                                <td class="py-4 px-4 text-slate-500 whitespace-nowrap">
                                    {{ $booking->tanggal_mulai->format('d M Y') }}
                                </td>
                                <td class="py-4 px-4 text-slate-500 whitespace-nowrap">
                                    {{ $booking->tanggal_selesai->format('d M Y') }}
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    <button type="button" wire:click="showDetail({{ $booking->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold border {{ $statusBadge }} hover:shadow-xs transition cursor-pointer"
                                        title="Klik untuk melihat detail">
                                        {{ $booking->status->label() }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p>Belum ada riwayat peminjaman.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden divide-y divide-slate-100 p-4 space-y-3">
                @forelse($bookings as $booking)
                    @php
                        $statusBadge = match ($booking->status->value) {
                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'disetujui' => 'bg-brand-50 text-brand-700 border-brand-200',
                            'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            default => 'bg-rose-50 text-rose-700 border-rose-200'
                        };
                    @endphp
                    <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-200/80 flex flex-col gap-3 text-xs">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-200/80 pb-2.5">
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm leading-tight">{{ $booking->vehicle->nama_kendaraan ?? 'Kendaraan Dihapus' }}</h4>
                                <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono mt-1">
                                    {{ $booking->vehicle->plat_nomor ?? '-' }}
                                </span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                {{ $booking->status->label() }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <p class="text-slate-400 font-bold text-[10px] uppercase">Tujuan</p>
                                <p class="font-bold text-slate-800">{{ $booking->kota }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 font-bold text-[10px] uppercase">Jadwal</p>
                                <p class="text-slate-600">{{ $booking->tanggal_mulai->format('d M Y') }} - {{ $booking->tanggal_selesai->format('d M Y') }}</p>
                            </div>
                        </div>

                        @if($booking->keperluan)
                            <div class="bg-white p-2.5 rounded-xl border border-slate-200 text-slate-700">
                                <span class="font-bold text-slate-900">Keperluan:</span> {{ $booking->keperluan }}
                            </div>
                        @endif

                        <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-slate-100">
                            <button type="button" wire:click="showDetail({{ $booking->id }})"
                                class="px-3 py-1.5 text-xs font-bold rounded-xl text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition flex items-center gap-1.5 shadow-2xs cursor-pointer ml-auto"
                                title="Lihat Detail">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span>Detail</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        Belum ada riwayat peminjaman.
                    </div>
                @endforelse
            </div>

            @if($bookings->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Detail Peminjaman -->
    @if($detailPeminjaman)
        <x-modal show="$wire.showDetailModal" maxWidth="max-w-lg">
            <x-slot:header>
                <div class="flex justify-between items-center w-full">
                    <h3 class="text-base font-bold text-slate-900">Detail Peminjaman</h3>
                    <button wire:click="closeDetail" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer hover:bg-slate-100 rounded-lg" title="Tutup">&times;</button>
                </div>
            </x-slot:header>

            <div class="bg-slate-50 rounded-2xl border border-slate-200/80 p-5 text-xs space-y-3.5">
                <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-200/80">
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">No. Request</span>
                        <span class="block text-xs font-bold text-slate-800 mt-1">
                            REQ-{{ $detailPeminjaman->created_at->format('Y') }}-{{ str_pad($detailPeminjaman->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold mt-1
                            @if($detailPeminjaman->status->value === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                            @elseif($detailPeminjaman->status->value === 'disetujui') bg-brand-50 text-brand-700 border border-brand-200
                            @elseif($detailPeminjaman->status->value === 'selesai') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @else bg-rose-50 text-rose-700 border border-rose-200 @endif">
                            {{ ucfirst($detailPeminjaman->status->value) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-200/80">
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Mulai</span>
                        <span class="block font-medium text-slate-800 mt-0.5">
                            {{ \Carbon\Carbon::parse($detailPeminjaman->tanggal_mulai)->translatedFormat('l, d M Y H:i') }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Selesai</span>
                        <span class="block font-medium text-slate-800 mt-0.5">
                            {{ \Carbon\Carbon::parse($detailPeminjaman->tanggal_selesai)->translatedFormat('l, d M Y H:i') }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-200/80">
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Mobil</span>
                        <span class="block font-bold text-slate-800 mt-0.5">{{ $detailPeminjaman->vehicle->nama_kendaraan ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Plat Nomor</span>
                        <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono mt-0.5">
                            {{ $detailPeminjaman->vehicle->plat_nomor ?? '-' }}
                        </span>
                    </div>
                </div>

                <div>
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tujuan & Keperluan</span>
                    <p class="font-semibold text-slate-800 mt-0.5">{{ $detailPeminjaman->kota }}, {{ $detailPeminjaman->provinsi }}</p>
                    <p class="text-slate-600 mt-1 leading-relaxed">{{ $detailPeminjaman->keperluan }}</p>
                </div>
            </div>

            <x-slot:footer>
                <button wire:click="closeDetail" type="button"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Tutup
                </button>
            </x-slot:footer>
        </x-modal>
    @endif
</div>
