<div x-data="{
    showModal: false,
    showConfirmModal: false,
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
        } else if (this.confirmAction === 'cancel') {
            $wire.cancel(this.confirmId);
        }
        this.showConfirmModal = false;
    }
}" @close-room-modal.window="showModal = false">

    @if(!auth()->user()->canAccessAdminDashboard())
        <x-top-header title="{{ $isReviewerMode ? 'Persetujuan Ruangan' : 'Peminjaman Ruangan' }}" />
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'py-6 pb-24 md:pb-8 max-w-7xl mx-auto flex-grow w-full px-4 sm:px-6 lg:px-8' }}">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                    {{ $isReviewerMode ? 'Persetujuan Peminjaman Ruangan' : 'Peminjaman Ruangan' }}
                </h1>
                <p class="text-slate-500 text-sm mt-1.5">
                    {{ $isReviewerMode ? 'Setujui atau tolak pengajuan peminjaman ruangan pegawai.' : 'Ajukan peminjaman ruangan rapat dan lihat riwayat pengajuan Anda.' }}
                </p>
            </div>

            @if(!$isReviewerMode)
                <div class="flex items-center gap-2.5 self-start sm:self-auto">
                    <button @click="showModal = true" type="button"
                        class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>Ajukan Peminjaman</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Notification Messages --}}
        @if (session()->has('success'))
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200/80 shadow-xs flex items-start gap-3">
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
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-rose-50/90 border border-rose-200/80 shadow-xs flex items-start gap-3">
                <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-bold text-rose-900">Terjadi Kesalahan</p>
                    <p class="text-xs sm:text-sm text-rose-700 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Filters Bar -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pencarian</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari Ruangan / Keperluan{{ $isReviewerMode ? ' / Pegawai' : '' }}..."
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Status</label>
                    <select wire:model.live="status"
                        class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            @if($isReviewerMode)<th class="py-3.5 px-6 whitespace-nowrap">Pemohon</th>@endif
                            <th class="py-3.5 px-4 whitespace-nowrap {{ $isReviewerMode ? '' : 'px-6' }}">Ruangan</th>
                            <th class="py-3.5 px-4">Keperluan</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Waktu</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status</th>
                            <th class="py-3.5 px-6 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" wire:poll.15s>
                        @forelse($this->bookings as $row)
                            <tr class="hover:bg-slate-50/60 transition-colors" wire:key="booking-{{ $row->id }}">
                                @if($isReviewerMode)
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-slate-900">{{ $row->user->name }}</p>
                                        @if($row->user->seksi)<p class="text-[10px] text-slate-400 mt-0.5">{{ $row->user->seksi }}</p>@endif
                                    </td>
                                @endif
                                <td class="py-4 {{ $isReviewerMode ? 'px-4' : 'px-6' }}">
                                    <p class="font-bold text-slate-900">{{ $row->room->nama }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $row->jumlah_peserta }} peserta</p>
                                </td>
                                <td class="py-4 px-4 text-slate-700">{{ $row->keperluan }}</td>
                                <td class="py-4 px-4 whitespace-nowrap text-slate-500 font-medium">
                                    {{ $row->tanggal_mulai->translatedFormat('d M Y H:i') }}
                                    <span class="block text-[10px] text-slate-400 mt-0.5">s.d. {{ $row->tanggal_selesai->translatedFormat('d M Y H:i') }}</span>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'dibatalkan' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        ];
                                        $pillClass = $statusClasses[$row->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $pillClass }}">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                    @if($row->processor)
                                        <div class="text-[10px] text-slate-400 mt-1 text-center truncate max-w-[130px] mx-auto" title="Diproses oleh: {{ $row->processor->name }} ({{ $row->processed_at ? $row->processed_at->format('d M Y H:i') : '' }})">
                                            Oleh: {{ \Illuminate\Support\Str::limit($row->processor->name, 14) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-0.5">
                                        @if($isReviewerMode && $row->status === 'pending')
                                            <button type="button"
                                                @click="openConfirm('approve', {{ $row->id }}, 'Setujui Peminjaman', 'Apakah Anda yakin ingin menyetujui peminjaman ruangan ini?')"
                                                class="p-1 text-emerald-600 hover:bg-emerald-50 rounded-lg border border-transparent hover:border-emerald-200 transition cursor-pointer shrink-0"
                                                title="Setujui Peminjaman">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                            <button type="button"
                                                @click="openConfirm('reject', {{ $row->id }}, 'Tolak Peminjaman', 'Apakah Anda yakin ingin menolak peminjaman ruangan ini?')"
                                                class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0"
                                                title="Tolak Peminjaman">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        @elseif(!$isReviewerMode && $row->status === 'pending')
                                            <button type="button"
                                                @click="openConfirm('cancel', {{ $row->id }}, 'Batalkan Peminjaman', 'Apakah Anda yakin ingin membatalkan pengajuan peminjaman ruangan ini?')"
                                                class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0"
                                                title="Batalkan Peminjaman">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isReviewerMode ? 6 : 5 }}" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-[48px] text-slate-300 mb-2 block">meeting_room</span>
                                    <p class="text-slate-500 font-semibold text-sm">Belum ada data peminjaman ruangan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->bookings->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $this->bookings->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Form Pengajuan -->
    @if(!$isReviewerMode)
        <x-modal show="showModal" maxWidth="max-w-lg">
            <x-slot:header>
                <div class="flex justify-between items-center w-full">
                    <h2 class="text-base font-bold text-slate-900">Form Peminjaman Ruangan</h2>
                    <button @click="showModal = false" type="button" aria-label="Tutup"
                        class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                        &times;
                    </button>
                </div>
            </x-slot:header>

            <form id="form-peminjaman-ruangan" wire:submit.prevent="submit" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Ruangan <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model="roomId"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                        <option value="">Pilih Ruangan</option>
                        @foreach($this->rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->nama }} (kapasitas {{ $room->kapasitas }})</option>
                        @endforeach
                    </select>
                    @error('roomId') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Keperluan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" wire:model="keperluan" placeholder="Contoh: Rapat koordinasi bulanan"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    @error('keperluan') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jumlah Peserta <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" min="1" wire:model="jumlahPeserta"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    @error('jumlahPeserta') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Mulai <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" wire:model="tanggalMulai"
                            class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                        @error('tanggalMulai') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Selesai <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" wire:model="tanggalSelesai"
                            class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                        @error('tanggalSelesai') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>
            </form>

            <x-slot:footer>
                <div class="flex justify-end gap-2.5 w-full">
                    <button type="button" @click="showModal = false"
                        class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" form="form-peminjaman-ruangan"
                        class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer">
                        <span wire:loading.remove wire:target="submit">Kirim Pengajuan</span>
                        <span wire:loading wire:target="submit">Memproses...</span>
                    </button>
                </div>
            </x-slot:footer>
        </x-modal>
    @endif

    <x-ruangan.confirm-modal />
</div>
