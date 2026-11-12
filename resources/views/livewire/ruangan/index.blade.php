<div x-data="{ showModal: false }" @close-room-modal.window="showModal = false">

    @if(!auth()->user()->canAccessAdminDashboard())
        <x-top-header title="{{ $isReviewerMode ? 'Persetujuan Ruangan' : 'Peminjaman Ruangan' }}" />
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'py-6 pb-24 md:pb-8 max-w-7xl mx-auto flex-grow w-full px-4 sm:px-6 lg:px-8' }}">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                    {{ $isReviewerMode ? 'Persetujuan Peminjaman Ruangan' : 'Peminjaman Ruangan' }}
                </h1>
                <p class="text-slate-500 text-sm mt-1.5">
                    {{ $isReviewerMode ? 'Kelola ruangan serta setujui atau tolak pengajuan peminjaman pegawai.' : 'Ajukan peminjaman ruangan rapat dan lihat riwayat pengajuan Anda.' }}
                </p>
            </div>

            @if(!$isReviewerMode)
                <div class="flex flex-wrap items-center gap-2.5 self-start sm:self-auto">
                    @if(auth()->user()->canManageBookings())
                        <a wire:navigate.hover href="{{ route('ruangan.persetujuan') }}"
                            class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition">
                            Persetujuan &amp; Kelola Ruangan
                        </a>
                    @endif
                    <button @click="showModal = true" type="button"
                        class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-sm shadow-brand-600/30 transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>Ajukan Peminjaman</span>
                    </button>
                </div>
            @else
                <a wire:navigate.hover href="{{ route('ruangan.peminjaman') }}"
                    class="self-start sm:self-auto px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition">
                    Ajukan Peminjaman Saya
                </a>
            @endif
        </div>

        @if (session()->has('success'))
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-xs sm:text-sm text-emerald-800 font-medium">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-xs sm:text-sm text-rose-800 font-medium">{{ session('error') }}</div>
        @endif

        {{-- Master Ruangan (reviewer) --}}
        @if($isReviewerMode)
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card mb-8">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Daftar Ruangan</h2>
                <form wire:submit.prevent="addRoom" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-start mb-5">
                    <div>
                        <input type="text" wire:model="roomNama" placeholder="Nama ruangan"
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none">
                        @error('roomNama') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="roomLokasi" placeholder="Lokasi (opsional)"
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none">
                        @error('roomLokasi') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <input type="number" min="1" wire:model="roomKapasitas" placeholder="Kapasitas"
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none">
                        @error('roomKapasitas') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="roomFasilitas" placeholder="Fasilitas (opsional)"
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none">
                        @error('roomFasilitas') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition cursor-pointer">Tambah Ruangan</button>
                </form>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($this->allRooms as $room)
                        <div class="rounded-2xl border border-slate-200 p-4 flex items-start justify-between gap-3" wire:key="room-{{ $room->id }}">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $room->nama }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $room->lokasi ?: '-' }} · Kapasitas {{ $room->kapasitas }}</p>
                                @if($room->fasilitas)<p class="text-[11px] text-slate-400 mt-1">{{ $room->fasilitas }}</p>@endif
                            </div>
                            <button type="button" wire:click="toggleRoom({{ $room->id }})"
                                class="shrink-0 px-2.5 py-1 rounded-lg text-[11px] font-semibold border cursor-pointer {{ $room->status === 'tersedia' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-rose-50 text-rose-700 border-rose-200/60' }}">
                                {{ $room->status === 'tersedia' ? 'Tersedia' : 'Nonaktif' }}
                            </button>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada ruangan.</p>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- Filter --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card mb-6 grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pencarian</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari ruangan / keperluan{{ $isReviewerMode ? ' / pegawai' : '' }}..."
                    class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Status</label>
                <select wire:model.live="status"
                    class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider">
                        <tr>
                            @if($isReviewerMode)<th class="px-4 py-3 font-semibold">Pemohon</th>@endif
                            <th class="px-4 py-3 font-semibold">Ruangan</th>
                            <th class="px-4 py-3 font-semibold">Keperluan</th>
                            <th class="px-4 py-3 font-semibold">Waktu</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->bookings as $row)
                            <tr wire:key="booking-{{ $row->id }}">
                                @if($isReviewerMode)<td class="px-4 py-3 font-semibold text-slate-800">{{ $row->user->name }}</td>@endif
                                <td class="px-4 py-3 text-slate-700">{{ $row->room->nama }}<span class="block text-[11px] text-slate-400">{{ $row->jumlah_peserta }} peserta</span></td>
                                <td class="px-4 py-3 text-slate-700">{{ $row->keperluan }}</td>
                                <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                                    {{ $row->tanggal_mulai->translatedFormat('d M Y H:i') }}<br>
                                    <span class="text-slate-400">s.d. {{ $row->tanggal_selesai->translatedFormat('d M Y H:i') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'inline-flex px-2.5 py-1 rounded-lg border font-medium capitalize',
                                        'bg-amber-50 text-amber-700 border-amber-200/60' => $row->status === 'pending',
                                        'bg-emerald-50 text-emerald-700 border-emerald-200/60' => $row->status === 'disetujui',
                                        'bg-rose-50 text-rose-700 border-rose-200/60' => in_array($row->status, ['ditolak', 'dibatalkan']),
                                    ])>{{ $row->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if($isReviewerMode && $row->status === 'pending')
                                        <button type="button" wire:click="approve({{ $row->id }})" wire:confirm="Setujui peminjaman ini?"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold cursor-pointer">Setujui</button>
                                        <button type="button" wire:click="reject({{ $row->id }})" wire:confirm="Tolak peminjaman ini?"
                                            class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-rose-700 hover:bg-slate-50 font-semibold cursor-pointer">Tolak</button>
                                    @elseif(!$isReviewerMode && $row->status === 'pending')
                                        <button type="button" wire:click="cancel({{ $row->id }})" wire:confirm="Batalkan pengajuan ini?"
                                            class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold cursor-pointer">Batalkan</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $isReviewerMode ? 6 : 5 }}" class="px-4 py-10 text-center text-slate-400">Belum ada data peminjaman ruangan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-100">{{ $this->bookings->links() }}</div>
        </div>
    </div>

    {{-- Modal pengajuan (tidak menutup saat backdrop diklik) --}}
    @if(!$isReviewerMode)
    <div x-show="showModal" x-cloak style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-900">Ajukan Peminjaman Ruangan</h3>
                <button type="button" @click="showModal = false" aria-label="Tutup" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form wire:submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Ruangan</label>
                    <select wire:model="roomId" class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                        <option value="">Pilih ruangan</option>
                        @foreach($this->rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->nama }} (kapasitas {{ $room->kapasitas }})</option>
                        @endforeach
                    </select>
                    @error('roomId') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Keperluan</label>
                    <input type="text" wire:model="keperluan" class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                    @error('keperluan') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Jumlah Peserta</label>
                    <input type="number" min="1" wire:model="jumlahPeserta" class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                    @error('jumlahPeserta') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Mulai</label>
                        <input type="datetime-local" wire:model="tanggalMulai" class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                        @error('tanggalMulai') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Selesai</label>
                        <input type="datetime-local" wire:model="tanggalSelesai" class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                        @error('tanggalSelesai') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-2.5 pt-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-sm shadow-brand-600/30 cursor-pointer">Ajukan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
