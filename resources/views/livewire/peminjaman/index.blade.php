<div x-data="{
    showApplicationModal: false,
    tanggalMulai: @entangle('tanggalMulai'),
    tanggalSelesai: @entangle('tanggalSelesai'),
    platMobil: @entangle('platMobil'),
    armadaTersedia: @entangle('armadaTersedia'),
    get isJadwalValid() { return this.tanggalMulai !== '' && this.tanggalSelesai !== ''; },
    get selectedArmada() {
        if (!this.platMobil || !this.armadaTersedia) return null;
        return this.armadaTersedia.find(a => a.plat === this.platMobil) || null;
    },
    selectArmada(plat) { this.platMobil = plat; }
}" x-init="
    $watch('showApplicationModal', value => {
        if (!value) {
            // reset if needed
        }
    })
" @booking-created.window="showApplicationModal = false">

    @if(!auth()->user()->canAccessAdminDashboard())
        <x-top-header title="Peminjaman Kendaraan" />
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'py-6 pb-24 md:pb-8 max-w-7xl mx-auto flex-grow w-full px-4 sm:px-6 lg:px-8' }}">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Peminjaman Kendaraan</h1>
            <p class="text-slate-500 text-sm mt-1.5">Pantau status pengajuan peminjaman kendaraan armada dinas Anda.</p>
        </div>

        {{-- Session Flash Notifications --}}
        @if (session()->has('success'))
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200/80 shadow-xs flex items-start gap-3 transition-all">
                <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-bold text-emerald-900">Berhasil</p>
                    <p class="text-xs sm:text-sm text-emerald-700 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if (session()->has('error'))
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-rose-50/90 border border-rose-200/80 shadow-xs flex items-start gap-3 transition-all">
                <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-bold text-rose-900">Terjadi Kesalahan</p>
                    <p class="text-xs sm:text-sm text-rose-700 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column: CTA Card & Status Overview -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Create Request Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-card">
                    <h3 class="text-base font-bold text-slate-900">Buat Peminjaman</h3>
                    <p class="text-slate-500 text-xs mt-2 leading-relaxed">Ajukan peminjaman armada dinas untuk keperluan dinas luar kantor secara cepat dan real-time.</p>
                    <button type="button" @click="showApplicationModal = true"
                        class="w-full mt-5 py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 flex items-center justify-center gap-2 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>Mulai Pengajuan Baru</span>
                    </button>
                </div>

                <!-- Quick Stats Summary -->
                <div wire:poll.10s class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-card">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Overview Status Peminjaman</h3>
                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 font-medium text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Menunggu Persetujuan
                            </span>
                            <strong class="font-bold text-slate-800">{{ $stats['pending'] }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 font-medium text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-brand-500"></span> Disetujui (Aktif)
                            </span>
                            <strong class="font-bold text-slate-800">{{ $stats['aktif'] }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 font-medium text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Ditolak / Dibatalkan
                            </span>
                            <strong class="font-bold text-slate-800">{{ $stats['gagal'] }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 font-medium text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Selesai
                            </span>
                            <strong class="font-bold text-slate-800">{{ $stats['selesai'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: My Recent Requests -->
            <div class="lg:col-span-8">
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Riwayat Peminjaman</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Daftar penggunaan armada dinas Anda sebelumnya</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs" wire:poll.10s>
                        @forelse($riwayat as $item)
                            @php
                                $statusBadge = match ($item->status->value) {
                                    'pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500', 'label' => 'Pending'],
                                    'disetujui' => ['bg' => 'bg-brand-50 text-brand-700 border-brand-200', 'dot' => 'bg-brand-500', 'label' => 'Disetujui'],
                                    'ditolak' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500', 'label' => 'Ditolak'],
                                    'selesai' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500', 'label' => 'Selesai'],
                                    'dibatalkan' => ['bg' => 'bg-slate-100 text-slate-600 border-slate-200', 'dot' => 'bg-slate-400', 'label' => 'Dibatalkan'],
                                    default => ['bg' => 'bg-slate-50 text-slate-700 border-slate-200', 'dot' => 'bg-slate-500', 'label' => ucfirst($item->status->value)]
                                };
                            @endphp
                            <div class="p-5 flex items-center justify-between hover:bg-slate-50/70 transition">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-9 h-9 rounded-full {{ $statusBadge['bg'] }} flex items-center justify-center shrink-0 border">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800">{{ $item->vehicle->nama_kendaraan ?? 'Kendaraan Dihapus' }}</h4>
                                        <p class="text-slate-400 text-[11px] mt-0.5">
                                            REQ-{{ $item->created_at->format('Y') }}-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }} &bull;
                                            {{ $item->status->value === 'selesai' ? 'Selesai' : 'Diajukan' }} {{ $item->created_at->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statusBadge['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusBadge['dot'] }}"></span>
                                        {{ $statusBadge['label'] }}
                                    </span>
                                    <button type="button" wire:click="showDetail({{ $item->id }})"
                                        class="px-3 py-1 rounded-xl text-[11px] font-bold border border-brand-200 text-brand-700 bg-brand-50/50 hover:bg-brand-100 transition flex items-center gap-1.5 cursor-pointer"
                                        title="Klik untuk melihat detail">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>Detail</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-xs">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Belum ada riwayat pengajuan peminjaman
                            </div>
                        @endforelse
                    </div>

                    <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-center">
                        <a href="{{ route('peminjaman.riwayat') }}" wire:navigate.hover
                            class="text-xs font-bold text-brand-600 hover:text-brand-700 transition flex items-center gap-1">
                            Lihat Semua Riwayat
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Pengajuan Peminjaman -->
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
                    <select wire:model.live="provinsi" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 focus:bg-white transition text-xs">
                        <option value="">Pilih Provinsi</option>
                        @foreach($this->getJavaRegions() as $provName => $cities)
                            <option value="{{ $provName }}">{{ $provName }}</option>
                        @endforeach
                    </select>
                    @error('provinsi') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Kota/Kab Tujuan <span class="text-rose-500">*</span></label>
                    <select wire:model="kota" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 focus:bg-white transition text-xs">
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
                <textarea wire:model="keperluan" rows="3" required placeholder="Jelaskan keperluan peminjaman dinas Anda..."
                    class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 focus:bg-white transition resize-none text-xs"></textarea>
                @error('keperluan') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input wire:model.live="tanggalMulai" type="date" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 focus:bg-white transition text-xs" />
                    @error('tanggalMulai') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input wire:model.live="tanggalSelesai" type="date" required class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 focus:bg-white transition text-xs" />
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

                    <button @click="if(isJadwalValid && armadaTersedia.length > 0) open = !open"
                        :disabled="!isJadwalValid"
                        :class="isJadwalValid ? 'bg-slate-50 text-slate-800 border-slate-200' : 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed'"
                        class="w-full flex items-center justify-between border rounded-xl px-3.5 py-2.5 text-left transition focus:border-brand-600 text-xs"
                        type="button">
                        <div class="flex items-center gap-2">
                            <template x-if="!selectedArmada">
                                <span class="text-xs font-medium"
                                    x-text="isJadwalValid ? (armadaTersedia.length > 0 ? 'Pilih Armada Tersedia...' : 'Tidak ada armada tersedia') : 'Pilih tanggal terlebih dahulu'"></span>
                            </template>
                            <template x-if="selectedArmada">
                                <div class="flex items-center gap-2">
                                    <img :src="selectedArmada.img" class="w-6 h-4 object-cover rounded border border-slate-200" />
                                    <span class="text-xs font-bold text-slate-800" x-text="selectedArmada.nama + ' (' + selectedArmada.plat + ')'"></span>
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

                    <!-- Dropdown Menu -->
                    <div @click.away="open = false"
                        class="absolute z-[70] w-full mt-1.5 bg-white border border-slate-100 rounded-2xl shadow-xl overflow-hidden py-1"
                        x-show="open" x-transition.opacity style="display: none;">
                        <div class="max-h-48 overflow-y-auto">
                            <template x-for="armada in armadaTersedia" :key="armada.plat">
                                <div @click="selectArmada(armada.plat); open = false"
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
                            {{ \Carbon\Carbon::parse($detailPeminjaman->tanggal_mulai)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Selesai</span>
                        <span class="block font-medium text-slate-800 mt-0.5">
                            {{ \Carbon\Carbon::parse($detailPeminjaman->tanggal_selesai)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-200/80">
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kendaraan</span>
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