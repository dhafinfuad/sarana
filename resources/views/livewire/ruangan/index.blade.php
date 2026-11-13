<div x-data="{
    showModal: false,
    showExportModal: false,
    showEditModal: false,
    showDetailModal: false,
    detail: {},
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
                        {{ $isReviewerMode ? 'Real-time analytics dan persetujuan peminjaman ruangan kantor.' : 'Ajukan peminjaman ruangan rapat dan lihat riwayat pengajuan Anda.' }}
                    </p>
                </div>

                <x-persetujuan.header-actions :create="auth()->user()->hasAccess('ruangan.peminjaman')" create-label="Peminjaman" create-action="showModal = true"
                    :export="auth()->user()->hasAccess('ruangan.export')" />
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
        <!-- 4 Modern Metric KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8" wire:poll.15s>
            <x-persetujuan.kpi-total label="Total Peminjaman" :value="$this->analytics['total']">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </x-persetujuan.kpi-total>
            <x-persetujuan.kpi-top label="Top Ruangan" :item="$this->analytics['topRoom']" unit="peminjaman" :chip="$this->analytics['topRoom'] ? 'Kap. ' . $this->analytics['topRoom']->kapasitas : null">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </x-persetujuan.kpi-top>
            <x-persetujuan.kpi-top label="Top Seksi" :item="$this->analytics['topSeksi']" unit="peminjaman ruangan" accent="bg-sky-50 text-sky-600 border-sky-100" :small="true">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </x-persetujuan.kpi-top>
            <x-persetujuan.kpi-top label="Top Pegawai" :item="$this->analytics['topPegawai']" unit="kali peminjaman" accent="bg-purple-50 text-purple-600 border-purple-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </x-persetujuan.kpi-top>
        </div>

        <x-persetujuan.filter-card title="Manajemen Peminjaman Ruangan" :pending="$this->analytics['pending']"
            :statuses="['pending' => 'Pending', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'dibatalkan' => 'Dibatalkan']"
            placeholder="Cari Pegawai, Ruangan, atau Keperluan..."
            reset="$set('search', ''); $set('status', ''); $set('startDate', ''); $set('endDate', '');" />

        <!-- Room Booking Approval Table -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden relative">
            <div wire:loading.flex class="absolute inset-0 bg-white/70 backdrop-blur-xs z-10 items-center justify-center">
                <span class="material-symbols-outlined animate-spin text-brand-600 text-3xl">sync</span>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs table-fixed">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="user.name" width="w-[16%]">Pegawai</x-persetujuan.sort-th>
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="room.nama" width="w-[15%]">Ruangan</x-persetujuan.sort-th>
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="keperluan" width="w-[21%]">Keperluan</x-persetujuan.sort-th>
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="tanggal_mulai" width="w-[11%]">Mulai</x-persetujuan.sort-th>
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="tanggal_selesai" width="w-[11%]">Selesai</x-persetujuan.sort-th>
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="status" width="w-[11%]" :center="true">Status</x-persetujuan.sort-th>
                            <th class="w-[15%] py-3 px-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->bookings as $row)
                            @php $badge = \App\Support\StatusBadge::admin($row->status); $nama = $row->user->name ?? 'Pengguna Dihapus'; @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors" wire:key="booking-{{ $row->id }}">
                                <td class="py-3 px-3 min-w-0">
                                    <p class="font-bold text-slate-900 leading-snug truncate" title="{{ $nama }}">{{ $nama }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight truncate" title="{{ $row->user->seksi ?? 'Seksi tidak diketahui' }}">{{ $row->user->seksi ?? 'Seksi tidak diketahui' }}</p>
                                </td>
                                <td class="py-3 px-3 min-w-0">
                                    <p class="font-bold text-slate-800 leading-snug truncate" title="{{ $row->room->nama }}">{{ $row->room->nama }}</p>
                                    <div class="mt-0.5">
                                        <span class="inline-flex px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono">{{ $row->jumlah_peserta }} orang</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3 min-w-0" title="{{ $row->keperluan }}">
                                    <p class="text-slate-600 text-xs leading-relaxed break-words whitespace-normal">{{ $row->keperluan }}</p>
                                </td>
                                <td class="py-3 px-2 text-slate-600 whitespace-nowrap text-[11px] font-medium">{{ $row->tanggal_mulai->format('d M Y') }}<span class="block text-[10px] text-slate-400">{{ $row->tanggal_mulai->format('H:i') }}</span></td>
                                <td class="py-3 px-2 text-slate-600 whitespace-nowrap text-[11px] font-medium">{{ $row->tanggal_selesai->format('d M Y') }}<span class="block text-[10px] text-slate-400">{{ $row->tanggal_selesai->format('H:i') }}</span></td>
                                <td class="py-3 px-2 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                        {{ $badge['label'] }}
                                    </span>
                                    @if($row->processor)
                                        <div class="text-[10px] text-slate-400 mt-1 truncate" title="Diproses oleh: {{ $row->processor->name }}">Oleh: {{ \Illuminate\Support\Str::limit($row->processor->name, 12) }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-0.5">
                                        <x-persetujuan.action type="approve" :disabled="!$row->can_approve" @click="openConfirm('approve', {{ $row->id }}, 'Konfirmasi Persetujuan', 'Setujui peminjaman ruangan untuk: {{ addslashes($nama) }}?')" />
                                        <x-persetujuan.action type="reject" :disabled="!$row->can_reject" @click="openConfirm('reject', {{ $row->id }}, 'Konfirmasi Penolakan', 'Tolak peminjaman ruangan untuk: {{ addslashes($nama) }}?')" />
                                        <x-persetujuan.action type="edit" :disabled="!$row->can_edit" wire:click="edit({{ $row->id }})" />
                                        <x-persetujuan.action type="delete" :disabled="!$row->can_delete" @click="openConfirm('delete', {{ $row->id }}, 'Konfirmasi Hapus Data', 'Hapus data peminjaman ruangan {{ addslashes($nama) }} secara permanen?')" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-2 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p>Tidak ada data peminjaman ruangan yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-3 p-4">
                @forelse($this->bookings as $row)
                    @php $badge = \App\Support\StatusBadge::admin($row->status); $nama = $row->user->name ?? 'Pengguna Dihapus'; @endphp
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex flex-col gap-3" wire:key="booking-card-{{ $row->id }}">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-slate-900 text-sm leading-tight truncate" title="{{ $nama }}">{{ $nama }}</h4>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5 truncate">{{ $row->user->seksi ?? 'Seksi tidak diketahui' }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border shrink-0 {{ $badge['bg'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                {{ $badge['label'] }}
                            </span>
                        </div>
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 text-xs">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Ruangan</p>
                            <p class="font-bold text-slate-800 text-xs truncate">{{ $row->room->nama }}</p>
                            <span class="inline-flex px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono mt-1">{{ $row->jumlah_peserta }} orang</span>
                        </div>
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 flex items-center justify-between text-xs">
                            <div>
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Mulai</p>
                                <p class="font-semibold text-slate-700 text-xs mt-0.5">{{ $row->tanggal_mulai->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Selesai</p>
                                <p class="font-semibold text-slate-700 text-xs mt-0.5">{{ $row->tanggal_selesai->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 text-xs">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Keperluan:</p>
                            <p class="text-slate-700 leading-relaxed">{{ $row->keperluan }}</p>
                        </div>
                        <div class="flex items-center justify-end gap-0.5 pt-2 border-t border-slate-100">
                            <x-persetujuan.action type="approve" :disabled="!$row->can_approve" @click="openConfirm('approve', {{ $row->id }}, 'Konfirmasi Persetujuan', 'Setujui peminjaman ruangan untuk: {{ addslashes($nama) }}?')" />
                            <x-persetujuan.action type="reject" :disabled="!$row->can_reject" @click="openConfirm('reject', {{ $row->id }}, 'Konfirmasi Penolakan', 'Tolak peminjaman ruangan untuk: {{ addslashes($nama) }}?')" />
                            <x-persetujuan.action type="edit" :disabled="!$row->can_edit" wire:click="edit({{ $row->id }})" />
                            <x-persetujuan.action type="delete" :disabled="!$row->can_delete" @click="openConfirm('delete', {{ $row->id }}, 'Konfirmasi Hapus Data', 'Hapus data peminjaman ruangan {{ addslashes($nama) }} secara permanen?')" />
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 text-slate-300 mb-2 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-xs">Tidak ada data peminjaman ruangan yang ditemukan.</p>
                    </div>
                @endforelse
            </div>

            @if($this->bookings->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $this->bookings->links() }}
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
                            $payload = \App\Support\RiwayatDetail::make(
                                title: 'Detail Peminjaman',
                                code: 'REQ-' . $row->created_at->format('Y') . '-' . str_pad((string) $row->id, 4, '0', STR_PAD_LEFT),
                                status: $row->status,
                                mulai: $row->tanggal_mulai->translatedFormat('d M Y H:i'),
                                selesai: $row->tanggal_selesai->translatedFormat('d M Y H:i'),
                                pairs: [[['Ruangan', $row->room->nama, 'bold'], ['Jumlah Peserta', $row->jumlah_peserta . ' orang', 'chip']]],
                                noteLabel: 'Keperluan',
                                noteHeading: $row->room->lokasi,
                                noteText: $row->keperluan,
                            );
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

    <!-- Modal Pengajuan (pemilih jadwal) -->
    @if(auth()->user()->hasAccess('ruangan.peminjaman'))
        @include('livewire.ruangan.partials.picker-modal')
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

    @if($isReviewerMode && auth()->user()->hasAccess('ruangan.export'))
        <x-persetujuan.export-modal title="Export Laporan Peminjaman Ruangan" :url="route('ruangan.export')" />
    @endif
</div>
