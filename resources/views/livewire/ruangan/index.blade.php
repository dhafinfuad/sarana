<div x-data="{
    showModal: false,
    showEditModal: false,
    showDetailModal: false,
    detail: { title: '', badge: {}, rows: [] },
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
        } else if (this.confirmAction === 'delete') {
            $wire.delete(this.confirmId);
        }
        this.showConfirmModal = false;
    }
}" @close-room-modal.window="showModal = false"
  @open-booking-edit-modal.window="showEditModal = true"
  @close-booking-edit-modal.window="showEditModal = false">

    @if(!auth()->user()->canAccessAdminDashboard())
        <x-top-header title="{{ $isReviewerMode ? 'Persetujuan Ruangan' : 'Peminjaman Ruangan' }}" />
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'py-6 pb-24 md:pb-8 max-w-7xl mx-auto flex-grow w-full px-4 sm:px-6 lg:px-8' }}">
        <!-- Page Header -->
        @if($isReviewerMode)
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
        @else
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Peminjaman Ruangan</h1>
            <p class="text-slate-500 text-sm mt-1.5">Pantau status pengajuan peminjaman ruangan rapat Anda.</p>
        </div>
        @endif

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

        @if($isReviewerMode)
        <!-- 4 Metric Cards untuk Persetujuan Ruangan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" wire:poll.15s>
            <!-- Card 1: Total Pengajuan -->
            <button type="button" wire:click="$set('status', '')"
                class="text-left bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 shadow-card hover:shadow-lg cursor-pointer relative overflow-hidden group flex items-center justify-between gap-2 sm:gap-3 {{ $status === '' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-slate-200/90 hover:border-slate-300' }}">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">TOTAL PENGAJUAN</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                        {{ number_format($this->analytics['total'] ?? 0) }}
                    </p>
                </div>
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 border border-brand-100 transition-transform duration-200 group-hover:scale-105">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </button>

            <!-- Card 2: Pending -->
            <button type="button" wire:click="$set('status', 'pending')"
                class="text-left bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 shadow-card hover:shadow-lg cursor-pointer relative overflow-hidden group flex items-center justify-between gap-2 sm:gap-3 {{ $status === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200/90 hover:border-slate-300' }}">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">PENDING</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-2 tracking-tight">
                        {{ number_format($this->analytics['pending'] ?? 0) }}
                    </p>
                </div>
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 transition-transform duration-200 group-hover:scale-105">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </button>

            <!-- Card 3: Disetujui -->
            <button type="button" wire:click="$set('status', 'disetujui')"
                class="text-left bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 shadow-card hover:shadow-lg cursor-pointer relative overflow-hidden group flex items-center justify-between gap-2 sm:gap-3 {{ $status === 'disetujui' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/90 hover:border-slate-300' }}">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">DISETUJUI</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-2 tracking-tight">
                        {{ number_format($this->analytics['disetujui'] ?? 0) }}
                    </p>
                </div>
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 transition-transform duration-200 group-hover:scale-105">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </button>

            <!-- Card 4: Ditolak -->
            <button type="button" wire:click="$set('status', 'ditolak')"
                class="text-left bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 shadow-card hover:shadow-lg cursor-pointer relative overflow-hidden group flex items-center justify-between gap-2 sm:gap-3 {{ $status === 'ditolak' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-200/90 hover:border-slate-300' }}">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">DITOLAK</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-rose-600 mt-2 tracking-tight">
                        {{ number_format($this->analytics['ditolak'] ?? 0) }}
                    </p>
                </div>
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 transition-transform duration-200 group-hover:scale-105">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </button>
        </div>

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
                                        @if($isReviewerMode)
                                            {{-- Setuju --}}
                                            <button type="button" @if($row->can_approve) @click="openConfirm('approve', {{ $row->id }}, 'Setujui Peminjaman', 'Apakah Anda yakin ingin menyetujui peminjaman ruangan ini?')" @else disabled @endif
                                                @class(['p-1 rounded-lg border border-transparent transition shrink-0', 'text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200 cursor-pointer' => $row->can_approve, 'text-slate-300 cursor-not-allowed' => !$row->can_approve])
                                                title="{{ $row->can_approve ? 'Setujui Peminjaman' : 'Tidak dapat disetujui pada status ini' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                            {{-- Tolak --}}
                                            <button type="button" @if($row->can_reject) @click="openConfirm('reject', {{ $row->id }}, 'Tolak Peminjaman', 'Apakah Anda yakin ingin menolak peminjaman ruangan ini?')" @else disabled @endif
                                                @class(['p-1 rounded-lg border border-transparent transition shrink-0', 'text-rose-600 hover:bg-rose-50 hover:border-rose-200 cursor-pointer' => $row->can_reject, 'text-slate-300 cursor-not-allowed' => !$row->can_reject])
                                                title="{{ $row->can_reject ? 'Tolak Peminjaman' : 'Tidak dapat ditolak pada status ini' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        @else
                                            {{-- Batalkan (pemohon) --}}
                                            <button type="button" @if($row->can_cancel) @click="openConfirm('cancel', {{ $row->id }}, 'Batalkan Peminjaman', 'Apakah Anda yakin ingin membatalkan pengajuan peminjaman ruangan ini?')" @else disabled @endif
                                                @class(['p-1 rounded-lg border border-transparent transition shrink-0', 'text-rose-600 hover:bg-rose-50 hover:border-rose-200 cursor-pointer' => $row->can_cancel, 'text-slate-300 cursor-not-allowed' => !$row->can_cancel])
                                                title="{{ $row->can_cancel ? 'Batalkan Peminjaman' : 'Hanya pengajuan Pending yang dapat dibatalkan' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            </button>
                                        @endif
                                        {{-- Edit --}}
                                        <button type="button" @if($row->can_edit) wire:click="edit({{ $row->id }})" @else disabled @endif
                                            @class(['p-1 rounded-lg border border-transparent transition shrink-0', 'text-amber-600 hover:bg-amber-50 hover:border-amber-200 cursor-pointer' => $row->can_edit, 'text-slate-300 cursor-not-allowed' => !$row->can_edit])
                                            title="{{ $row->can_edit ? 'Edit Peminjaman' : 'Hanya pengajuan Pending yang dapat diedit' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        {{-- Hapus --}}
                                        <button type="button" @if($row->can_delete) @click="openConfirm('delete', {{ $row->id }}, 'Hapus Peminjaman', 'Apakah Anda yakin ingin menghapus data peminjaman ini? Tindakan ini tidak dapat dibatalkan.')" @else disabled @endif
                                            @class(['p-1 rounded-lg border border-transparent transition shrink-0', 'text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 cursor-pointer' => $row->can_delete, 'text-slate-300 cursor-not-allowed' => !$row->can_delete])
                                            title="{{ $row->can_delete ? 'Hapus Peminjaman' : 'Hanya pengajuan Ditolak/Dibatalkan yang dapat dihapus' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
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
        @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column: CTA Card & Status Overview -->
            <div class="lg:col-span-4 space-y-6">
                <x-riwayat.cta-card title="Buat Peminjaman"
                    description="Ajukan peminjaman ruangan rapat untuk keperluan kantor secara cepat dan real-time."
                    @click="showModal = true" />

                <x-riwayat.overview-card title="Overview Status Peminjaman" :rows="[
                    ['label' => 'Menunggu Persetujuan', 'dot' => 'bg-amber-500', 'value' => $this->overview['pending']],
                    ['label' => 'Disetujui', 'dot' => 'bg-emerald-500', 'value' => $this->overview['disetujui']],
                    ['label' => 'Ditolak / Dibatalkan', 'dot' => 'bg-rose-500', 'value' => $this->overview['gagal']],
                ]" />
            </div>

            <!-- Right Column: Riwayat Terbaru -->
            <div class="lg:col-span-8">
                <x-riwayat.list-card title="Riwayat Peminjaman" subtitle="5 pengajuan peminjaman ruangan terakhir Anda">
                    @forelse($this->recent as $row)
                        @php
                            $badge = \App\Support\StatusBadge::for($row->status);
                            $payload = [
                                'title' => $row->room->nama,
                                'badge' => $badge,
                                'rows' => [
                                    ['Keperluan', $row->keperluan],
                                    ['Jumlah Peserta', $row->jumlah_peserta . ' orang'],
                                    ['Waktu Mulai', $row->tanggal_mulai->translatedFormat('d M Y H:i')],
                                    ['Waktu Selesai', $row->tanggal_selesai->translatedFormat('d M Y H:i')],
                                    ['Diajukan', $row->created_at->translatedFormat('d M Y H:i')],
                                ],
                            ];
                        @endphp
                        <div class="p-5 flex flex-wrap items-center justify-between gap-3 hover:bg-slate-50/70 transition" wire:key="recent-{{ $row->id }}">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-9 h-9 rounded-full {{ $badge['bg'] }} flex items-center justify-center shrink-0 border">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-800">{{ $row->room->nama }}</h4>
                                    <p class="text-slate-400 text-[11px] mt-0.5">
                                        REQ-{{ $row->created_at->format('Y') }}-{{ str_pad((string) $row->id, 4, '0', STR_PAD_LEFT) }} &bull;
                                        Diajukan {{ $row->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    {{ $badge['label'] }}
                                </span>
                                @if($row->show_actions)
                                    <div class="inline-flex items-center gap-0.5">
                                        {{-- Batalkan --}}
                                        <button type="button" @if($row->can_cancel) @click="openConfirm('cancel', {{ $row->id }}, 'Batalkan Peminjaman', 'Apakah Anda yakin ingin membatalkan pengajuan peminjaman ruangan ini?')" @else disabled @endif
                                            @class(['p-1 rounded-lg border border-transparent transition shrink-0', 'text-rose-600 hover:bg-rose-50 hover:border-rose-200 cursor-pointer' => $row->can_cancel, 'text-slate-300 cursor-not-allowed' => !$row->can_cancel])
                                            title="{{ $row->can_cancel ? 'Batalkan Peminjaman' : 'Hanya pengajuan Pending yang dapat dibatalkan' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        </button>
                                        {{-- Edit --}}
                                        <button type="button" @if($row->can_edit) wire:click="edit({{ $row->id }})" @else disabled @endif
                                            @class(['p-1 rounded-lg border border-transparent transition shrink-0', 'text-amber-600 hover:bg-amber-50 hover:border-amber-200 cursor-pointer' => $row->can_edit, 'text-slate-300 cursor-not-allowed' => !$row->can_edit])
                                            title="{{ $row->can_edit ? 'Edit Peminjaman' : 'Tidak dapat diedit' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        {{-- Hapus --}}
                                        <button type="button" @if($row->can_delete) @click="openConfirm('delete', {{ $row->id }}, 'Hapus Peminjaman', 'Apakah Anda yakin ingin menghapus data peminjaman ini? Tindakan ini tidak dapat dibatalkan.')" @else disabled @endif
                                            @class(['p-1 rounded-lg border border-transparent transition shrink-0', 'text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 cursor-pointer' => $row->can_delete, 'text-slate-300 cursor-not-allowed' => !$row->can_delete])
                                            title="{{ $row->can_delete ? 'Hapus Peminjaman' : 'Tidak dapat dihapus' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                @endif
                                <button type="button" @click="detail = @js($payload); showDetailModal = true"
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
                            Belum ada riwayat pengajuan peminjaman ruangan
                        </div>
                    @endforelse

                    <x-slot:footer>
                        <a href="{{ route('ruangan.riwayat') }}" wire:navigate.hover
                            class="text-xs font-bold text-brand-600 hover:text-brand-700 transition flex items-center gap-1">
                            Lihat Semua Riwayat
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </x-slot:footer>
                </x-riwayat.list-card>
            </div>
        </div>
        @endif
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
                @include('livewire.ruangan.partials.booking-fields', ['roomOptions' => $this->rooms])
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

    <!-- Modal Edit Peminjaman -->
    <x-modal show="showEditModal" maxWidth="max-w-lg">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Edit Peminjaman Ruangan</h2>
                <button @click="showEditModal = false; $wire.resetForm()" type="button" aria-label="Tutup"
                    class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <form id="form-edit-peminjaman-ruangan" wire:submit.prevent="update" class="space-y-4 text-xs">
            @include('livewire.ruangan.partials.booking-fields', ['roomOptions' => $this->editRooms])
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showEditModal = false; $wire.resetForm()"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" form="form-edit-peminjaman-ruangan"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer">
                    <span wire:loading.remove wire:target="update">Simpan Perubahan</span>
                    <span wire:loading wire:target="update">Memproses...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    @if(!$isReviewerMode)
        <x-riwayat.detail-modal />
    @endif

    <x-ruangan.confirm-modal />
</div>
