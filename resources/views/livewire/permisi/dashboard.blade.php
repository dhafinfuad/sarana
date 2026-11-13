<div x-data="{
    showApplicationModal: false,
    showExportModal: false,
    showDetailModal: false,
    detail: {},
    showEditModal: false,
    kategori: @entangle('kategori'),
    jenis: @entangle('jenis'),
    keperluan: @entangle('keperluan'),
    tanggalMulai: @entangle('tanggalMulai'),
    tanggalSelesai: @entangle('tanggalSelesai'),
    
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
            $wire.deleteBooking(this.confirmId);
        }
        this.showConfirmModal = false;
    }
}" @close-application-modal.window="showApplicationModal = false"
  @open-edit-modal.window="showEditModal = true"
  @close-edit-modal.window="showEditModal = false">

    @if(!auth()->user()->canAccessAdminDashboard())
        <x-top-header title="{{ $isReviewerMode ? 'Persetujuan Izin' : 'Permohonan Izin' }}" />
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'py-6 pb-24 md:pb-8 max-w-7xl mx-auto flex-grow w-full px-4 sm:px-6 lg:px-8' }}">
        <!-- Page Header -->
        @if($isReviewerMode)
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                        {{ $isReviewerMode ? 'Persetujuan Izin Keluar' : 'Permohonan Izin Keluar' }}
                    </h1>
                    <p class="text-slate-500 text-sm mt-1.5">
                        {{ $isReviewerMode ? 'Real-time analytics dan persetujuan izin keluar kantor pegawai di bawah supervisi Anda.' : 'Form pengajuan dan riwayat permohonan perizinan keluar jam kantor.' }}
                    </p>
                </div>

                <x-persetujuan.header-actions :create="auth()->user()->canAccessPermohonanPermisi()" create-label="Permohonan" create-action="showApplicationModal = true"
                    :export="auth()->user()->hasAccess('permisi.export')" />
            </div>
        @else
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Permohonan Izin Keluar</h1>
            <p class="text-slate-500 text-sm mt-1.5">Pantau status permohonan perizinan keluar jam kantor Anda.</p>
        </div>
        @endif

        {{-- Notification Messages --}}
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

        @if($isReviewerMode)
        <!-- 4 Modern Metric KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8" wire:poll.15s>
            <x-persetujuan.kpi-total label="Total Permohonan" :value="$this->analytics['total']">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </x-persetujuan.kpi-total>
            <x-persetujuan.kpi-top label="Top Kategori" :item="$this->analytics['topKategori']" unit="permohonan">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </x-persetujuan.kpi-top>
            <x-persetujuan.kpi-top label="Top Seksi" :item="$this->analytics['topSeksi']" unit="permohonan izin" accent="bg-sky-50 text-sky-600 border-sky-100" :small="true">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </x-persetujuan.kpi-top>
            <x-persetujuan.kpi-top label="Top Pegawai" :item="$this->analytics['topPegawai']" unit="kali permohonan" accent="bg-purple-50 text-purple-600 border-purple-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </x-persetujuan.kpi-top>
        </div>

        <x-persetujuan.filter-card title="Manajemen Permohonan Izin" :pending="$this->analytics['pending']"
            :statuses="['pending' => 'Pending', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan']"
            :seksi="$this->availableSeksiList"
            placeholder="Cari Pegawai, Kategori, atau Keperluan..."
            reset="$set('search', ''); $set('status', ''); $set('filterSeksi', ''); $set('startDate', ''); $set('endDate', '');" />

        <!-- Permisi Approval Table -->
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
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="kategori" width="w-[14%]">Kategori</x-persetujuan.sort-th>
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="keperluan" width="w-[22%]">Keperluan</x-persetujuan.sort-th>
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="tanggal_mulai" width="w-[11%]">Mulai</x-persetujuan.sort-th>
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="tanggal_selesai" width="w-[11%]">Selesai</x-persetujuan.sort-th>
                            <x-persetujuan.sort-th :sort="$sortField" :dir="$sortDirection" field="status" width="w-[11%]" :center="true">Status</x-persetujuan.sort-th>
                            <th class="w-[15%] py-3 px-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->permisis as $item)
                            @php $badge = \App\Support\StatusBadge::admin($item->status); $nama = $item->user->name ?? 'Pengguna Dihapus'; $privileged = auth()->user()->hasAccess('permisi.persetujuan.kelola'); @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors" wire:key="permisi-{{ $item->id }}">
                                <td class="py-3 px-3 min-w-0">
                                    <p class="font-bold text-slate-900 leading-snug truncate" title="{{ $nama }}">{{ $nama }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium mt-0.5 leading-tight truncate" title="{{ $item->user->seksi ?? 'Seksi tidak diketahui' }}">{{ $item->user->seksi ?? 'Seksi tidak diketahui' }}</p>
                                </td>
                                <td class="py-3 px-3 min-w-0">
                                    <p class="font-bold text-slate-800 leading-snug truncate" title="{{ $item->kategori }}">{{ $item->kategori }}</p>
                                    <div class="mt-0.5">
                                        <span class="inline-flex px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono">{{ $item->jenis }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3 min-w-0" title="{{ $item->keperluan }}">
                                    <p class="text-slate-600 text-xs leading-relaxed break-words whitespace-normal">{{ $item->keperluan }}</p>
                                </td>
                                <td class="py-3 px-2 text-slate-600 whitespace-nowrap text-[11px] font-medium">{{ $item->tanggal_mulai->format('d M Y') }}<span class="block text-[10px] text-slate-400">{{ $item->tanggal_mulai->format('H:i') }}</span></td>
                                <td class="py-3 px-2 text-slate-600 whitespace-nowrap text-[11px] font-medium">{{ $item->tanggal_selesai->format('d M Y') }}<span class="block text-[10px] text-slate-400">{{ $item->tanggal_selesai->format('H:i') }}</span></td>
                                <td class="py-3 px-2 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                        {{ $badge['label'] }}
                                    </span>
                                    @if($item->processor)
                                        <div class="text-[10px] text-slate-400 mt-1 truncate" title="Diproses oleh: {{ $item->processor->name }}">Oleh: {{ \Illuminate\Support\Str::limit($item->processor->name, 12) }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-0.5">
                                        @if($item->status === 'pending')
                                            <x-persetujuan.action type="approve" @click="openConfirm('approve', {{ $item->id }}, 'Konfirmasi Persetujuan', 'Setujui permohonan izin dari: {{ addslashes($nama) }}?')" />
                                            <x-persetujuan.action type="reject" @click="openConfirm('reject', {{ $item->id }}, 'Konfirmasi Penolakan', 'Tolak permohonan izin dari: {{ addslashes($nama) }}?')" />
                                        @endif
                                        @if($privileged)
                                            <x-persetujuan.action type="edit" wire:click="editBooking({{ $item->id }})" />
                                            <x-persetujuan.action type="delete" @click="openConfirm('delete', {{ $item->id }}, 'Konfirmasi Hapus Data', 'Hapus data permohonan izin {{ addslashes($nama) }} secara permanen?')" />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-2 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p>Tidak ada data permohonan izin yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-3 p-4">
                @forelse($this->permisis as $item)
                    @php $badge = \App\Support\StatusBadge::admin($item->status); $nama = $item->user->name ?? 'Pengguna Dihapus'; $privileged = auth()->user()->hasAccess('permisi.persetujuan.kelola'); @endphp
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex flex-col gap-3" wire:key="permisi-card-{{ $item->id }}">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-slate-900 text-sm leading-tight truncate" title="{{ $nama }}">{{ $nama }}</h4>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5 truncate">{{ $item->user->seksi ?? 'Seksi tidak diketahui' }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border shrink-0 {{ $badge['bg'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                {{ $badge['label'] }}
                            </span>
                        </div>
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 text-xs">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Kategori</p>
                            <p class="font-bold text-slate-800 text-xs truncate">{{ $item->kategori }}</p>
                            <span class="inline-flex px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 font-mono mt-1">{{ $item->jenis }}</span>
                        </div>
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 flex items-center justify-between text-xs">
                            <div>
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Mulai</p>
                                <p class="font-semibold text-slate-700 text-xs mt-0.5">{{ $item->tanggal_mulai->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Selesai</p>
                                <p class="font-semibold text-slate-700 text-xs mt-0.5">{{ $item->tanggal_selesai->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100 text-xs">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Keperluan:</p>
                            <p class="text-slate-700 leading-relaxed">{{ $item->keperluan }}</p>
                        </div>
                        @if($item->status === 'pending' || $privileged)
                            <div class="flex items-center justify-end gap-0.5 pt-2 border-t border-slate-100">
                                @if($item->status === 'pending')
                                    <x-persetujuan.action type="approve" @click="openConfirm('approve', {{ $item->id }}, 'Konfirmasi Persetujuan', 'Setujui permohonan izin dari: {{ addslashes($nama) }}?')" />
                                    <x-persetujuan.action type="reject" @click="openConfirm('reject', {{ $item->id }}, 'Konfirmasi Penolakan', 'Tolak permohonan izin dari: {{ addslashes($nama) }}?')" />
                                @endif
                                @if($privileged)
                                    <x-persetujuan.action type="edit" wire:click="editBooking({{ $item->id }})" />
                                    <x-persetujuan.action type="delete" @click="openConfirm('delete', {{ $item->id }}, 'Konfirmasi Hapus Data', 'Hapus data permohonan izin {{ addslashes($nama) }} secara permanen?')" />
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 text-slate-300 mb-2 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-xs">Tidak ada data permohonan izin yang ditemukan.</p>
                    </div>
                @endforelse
            </div>

            @if($this->permisis->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $this->permisis->links() }}
                </div>
            @endif
        </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Column: CTA Card & Status Overview -->
            <div class="lg:col-span-4 space-y-6">
                <x-riwayat.cta-card title="Buat Permohonan"
                    description="Ajukan permohonan atau pemberitahuan izin keluar kantor secara cepat dan real-time."
                    @click="showApplicationModal = true" />

                <x-riwayat.overview-card title="Overview Status Permohonan" :rows="[
                    ['label' => 'Menunggu Persetujuan', 'dot' => 'bg-amber-500', 'value' => $this->overview['pending']],
                    ['label' => 'Disetujui', 'dot' => 'bg-emerald-500', 'value' => $this->overview['disetujui']],
                    ['label' => 'Ditolak / Dibatalkan', 'dot' => 'bg-rose-500', 'value' => $this->overview['gagal']],
                    ['label' => 'Selesai', 'dot' => 'bg-blue-500', 'value' => $this->overview['selesai']],
                ]" />
            </div>

            <!-- Right Column: Riwayat Terbaru -->
            <div class="lg:col-span-8">
                <x-riwayat.list-card title="Riwayat Permohonan" subtitle="5 permohonan izin keluar kantor terakhir Anda">
                    @forelse($this->recent as $item)
                        @php
                            $badge = \App\Support\StatusBadge::for($item->status);
                            $payload = \App\Support\RiwayatDetail::make(
                                title: 'Detail Permohonan',
                                code: 'REQ-' . $item->created_at->format('Y') . '-' . str_pad((string) $item->id, 4, '0', STR_PAD_LEFT),
                                status: $item->status,
                                mulai: $item->tanggal_mulai->translatedFormat('d M Y H:i'),
                                selesai: $item->tanggal_selesai->translatedFormat('d M Y H:i'),
                                pairs: array_values(array_filter([
                                    [['Kategori', $item->kategori, 'bold'], ['Jenis', $item->jenis, 'chip']],
                                    $item->processor ? [['Diproses Oleh', $item->processor->name, 'bold'], ['Waktu Diproses', $item->processed_at ? $item->processed_at->translatedFormat('d M Y H:i') : '-', 'normal']] : null,
                                ])),
                                noteLabel: 'Keperluan',
                                noteHeading: null,
                                noteText: $item->keperluan,
                            );
                        @endphp
                        <div class="p-5 flex flex-wrap items-center justify-between gap-3 hover:bg-slate-50/70 transition" wire:key="recent-{{ $item->id }}">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-9 h-9 rounded-full {{ $badge['bg'] }} flex items-center justify-center shrink-0 border">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-800">{{ $item->kategori }} &bull; {{ $item->jenis }}</h4>
                                    <p class="text-slate-400 text-[11px] mt-0.5">
                                        REQ-{{ $item->created_at->format('Y') }}-{{ str_pad((string) $item->id, 4, '0', STR_PAD_LEFT) }} &bull;
                                        Diajukan {{ $item->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    {{ $badge['label'] }}
                                </span>
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
                            Belum ada riwayat permohonan izin
                        </div>
                    @endforelse

                    <x-slot:footer>
                        <a href="{{ route('permisi.riwayat') }}" wire:navigate.hover
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
    @if(auth()->user()->canAccessPermohonanPermisi())
    <x-modal show="showApplicationModal" maxWidth="max-w-lg">
        <x-slot:header>
            <div class="flex items-center justify-between w-full">
                <h3 class="text-base font-bold text-slate-900">Form Pengajuan Permohonan Izin</h3>
                <button @click="showApplicationModal = false" type="button" class="text-slate-400 hover:text-slate-700 text-xl leading-none cursor-pointer">&times;</button>
            </div>
        </x-slot:header>

        <form wire:submit.prevent="submitApplication" class="space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
                                Kategori Izin <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model="kategori" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
                                <option value="">Pilih Kategori</option>
                                <option value="Kantor">Kantor</option>
                                <option value="Kesehatan">Kesehatan</option>
                                <option value="Keluarga">Keluarga</option>
                                <option value="Lain-lain">Lain-lain</option>
                            </select>
                            @error('kategori') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
                                Jenis Izin <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model="jenis" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
                                <option value="">Pilih Jenis</option>
                                <option value="Permohonan">Permohonan</option>
                                <option value="Pemberitahuan">Pemberitahuan</option>
                            </select>
                            @error('jenis') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
                                Mulai <span class="text-rose-500">*</span>
                            </label>
                            <input wire:model="tanggalMulai" type="datetime-local" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs" />
                            @error('tanggalMulai') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
                                Selesai <span class="text-rose-500">*</span>
                            </label>
                            <input wire:model="tanggalSelesai" type="datetime-local" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs" />
                            @error('tanggalSelesai') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
                            Keperluan <span class="text-rose-500">*</span>
                        </label>
                        <textarea wire:model="keperluan" rows="3" required
                            placeholder="Jelaskan keperluan izin Anda..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs resize-none"></textarea>
                        @error('keperluan') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
            
            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2.5">
                <button @click="showApplicationModal = false" type="button"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 transition cursor-pointer flex items-center gap-1.5">
                    <span>Ajukan Permohonan</span>
                    <span wire:loading wire:target="submitApplication" class="material-symbols-outlined animate-spin text-[14px]">refresh</span>
                </button>
            </div>
        </form>
    </x-modal>
    @endif

    <!-- Modal Form Edit Data Permisi -->
    <x-modal show="showEditModal" maxWidth="max-w-lg">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Edit Data Permisi</h2>
                <button @click="showEditModal = false" type="button"
                    class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <form id="form-edit-permisi" wire:submit.prevent="updateApplication" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori Izin <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model="kategori" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                        <option value="">Pilih Kategori</option>
                        <option value="Kantor">Kantor</option>
                        <option value="Kesehatan">Kesehatan</option>
                        <option value="Keluarga">Keluarga</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                    @error('kategori') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jenis Izin <span class="text-rose-500">*</span>
                    </label>
                    <select wire:model="jenis" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                        <option value="">Pilih Jenis</option>
                        <option value="Permohonan">Permohonan</option>
                        <option value="Pemberitahuan">Pemberitahuan</option>
                    </select>
                    @error('jenis') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input wire:model="tanggalMulai" type="datetime-local" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" />
                    @error('tanggalMulai') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input wire:model="tanggalSelesai" type="datetime-local" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" />
                    @error('tanggalSelesai') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keperluan <span class="text-rose-500">*</span>
                </label>
                <textarea wire:model="keperluan" rows="3" required
                    placeholder="Jelaskan keperluan izin..."
                    class="w-full p-3 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition resize-none"></textarea>
                @error('keperluan') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showEditModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" form="form-edit-permisi"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer">
                    <span wire:loading.remove wire:target="updateApplication">Simpan Perubahan</span>
                    <span wire:loading wire:target="updateApplication">Memproses...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    @if(!$isReviewerMode)
        <x-riwayat.detail-modal />
    @endif

    @if($isReviewerMode && auth()->user()->hasAccess('permisi.export'))
        <x-persetujuan.export-modal title="Export Laporan Permohonan Izin" :url="route('permisi.export')" />
    @endif

    <!-- Global Confirmation Modal -->
    <x-modal show="showConfirmModal" maxWidth="max-w-md">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900" x-text="confirmTitle"></h2>
                <button type="button" @click="showConfirmModal = false"
                    class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center"
                :class="{'bg-emerald-50 text-emerald-600 border border-emerald-200': confirmAction === 'approve' || confirmAction === 'complete', 'bg-rose-50 text-rose-600 border border-rose-200': confirmAction === 'reject' || confirmAction === 'cancel' || confirmAction === 'delete'}">
                <span class="material-symbols-outlined text-[24px]"
                    x-text="(confirmAction === 'approve' || confirmAction === 'complete') ? 'check_circle' : 'warning'"></span>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" x-text="confirmMessage"></p>
            </div>
        </div>

        <x-slot:footer>
            <button @click="showConfirmModal = false"
                class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                Batal
            </button>
            <button x-show="confirmAction === 'approve' || confirmAction === 'complete'" @click="executeAction()"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 transition-colors cursor-pointer">
                Ya, Lanjutkan
            </button>
            <button x-show="confirmAction === 'reject' || confirmAction === 'cancel' || confirmAction === 'delete'"
                @click="executeAction()"
                class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/20 transition-colors cursor-pointer">
                Ya, Lanjutkan
            </button>
        </x-slot:footer>
    </x-modal>
</div>