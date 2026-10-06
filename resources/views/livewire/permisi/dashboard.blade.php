<div x-data="{
    showApplicationModal: false,
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
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                    {{ $isReviewerMode ? 'Persetujuan Izin Keluar' : 'Permohonan Izin Keluar' }}
                </h1>
                <p class="text-slate-500 text-sm mt-1.5">
                    {{ $isReviewerMode ? 'Manajemen persetujuan izin keluar kantor pegawai di bawah supervisi Anda.' : 'Form pengajuan dan riwayat permohonan perizinan keluar jam kantor.' }}
                </p>
            </div>

            @if(!$isReviewerMode)
                <div class="flex items-center gap-2.5 self-start sm:self-auto">
                    <button @click="showApplicationModal = true"
                        class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>Pengajuan Izin</span>
                    </button>
                </div>
            @endif
        </div>

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

        <!-- 4 Metric Cards for Permisi -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" wire:poll.15s>
            <!-- Card 1: Total Permohonan -->
            <button type="button" wire:click="$set('status', '')"
                class="text-left bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 shadow-card hover:shadow-lg cursor-pointer relative overflow-hidden group flex items-center justify-between gap-2 sm:gap-3 {{ $status === '' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-slate-200/90 hover:border-slate-300' }}">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">TOTAL PERMOHONAN</p>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 {{ ($isReviewerMode && count($this->availableSeksiList) > 1) ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-3.5 items-end">
                <!-- Search Input -->
                <div class="col-span-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pencarian</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari Pegawai / Keperluan / Jenis..."
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                    </div>
                </div>

                @if($isReviewerMode && count($this->availableSeksiList) > 1)
                <div class="col-span-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Seksi</label>
                    <select wire:model.live="filterSeksi"
                        class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                        <option value="">Semua Seksi ({{ count($this->availableSeksiList) }})</option>
                        @foreach($this->availableSeksiList as $seksiItem)
                            <option value="{{ $seksiItem }}">{{ $seksiItem }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Status Filter -->
                <div class="col-span-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Status</label>
                    <select wire:model.live="status"
                        class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <!-- Start Date -->
                <div class="col-span-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                    <input type="date" wire:model.live="startDate"
                        class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                </div>

                <!-- End Date -->
                <div class="col-span-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                    <input type="date" wire:model.live="endDate"
                        class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                </div>
            </div>
        </div>

        <!-- Table (Desktop View) -->
        <div class="hidden md:block bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th wire:click="sortBy('user.name')" class="py-3.5 px-6 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Pegawai</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortField === 'user.name' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortField === 'user.name' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'user.name' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('kategori')" class="py-3.5 px-4 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Jenis & Kategori</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortField === 'kategori' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortField === 'kategori' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'kategori' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('keperluan')" class="py-3.5 px-4 cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Keperluan</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortField === 'keperluan' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortField === 'keperluan' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'keperluan' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('tanggal_mulai')" class="py-3.5 px-4 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Mulai & Selesai</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortField === 'tanggal_mulai' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortField === 'tanggal_mulai' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'tanggal_mulai' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('status')" class="py-3.5 px-4 text-center whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center justify-center gap-1">
                                    <span>Status</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortField === 'status' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortField === 'status' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortField === 'status' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            @if(auth()->user()->canAccessPersetujuanPermisi() || !$isReviewerMode)
                                <th class="py-3.5 px-6 text-center whitespace-nowrap">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" wire:poll.15s>
                        @forelse($this->permisis as $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- Pegawai -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <div class="font-bold text-slate-900 leading-snug">
                                                {{ $item->user->name }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-medium mt-1 leading-tight flex items-center gap-1.5 flex-wrap">
                                                <span>NIP: {{ $item->user->nip_pendek ?? '-' }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $item->user->jabatan ?? '-' }}</span>
                                            </div>
                                            @if(!empty($item->user->seksi))
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center text-[10px] font-semibold text-brand-700 bg-brand-50 border border-brand-200/60 px-2 py-0.5 rounded-md">
                                                        {{ $item->user->seksi }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Jenis & Kategori -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <p class="font-bold text-slate-800">{{ $item->kategori }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5 font-medium">{{ $item->jenis }}</p>
                                </td>

                                <!-- Keperluan -->
                                <td class="py-4 px-4">
                                    <p class="text-slate-700 font-medium line-clamp-2 max-w-xs" title="{{ $item->keperluan }}">
                                        {{ $item->keperluan }}
                                    </p>
                                </td>

                                <!-- Jadwal Mulai & Selesai -->
                                <td class="py-4 px-4 whitespace-nowrap text-slate-500 font-medium">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px] text-brand-600">calendar_today</span>
                                        <span>{{ $item->tanggal_mulai->format('d M Y, H:i') }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-1">
                                        <span class="material-symbols-outlined text-[15px] text-rose-500">event_busy</span>
                                        <span>{{ $item->tanggal_selesai->format('d M Y, H:i') }}</span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'selesai' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'dibatalkan' => 'bg-slate-100 text-slate-600 border-slate-200'
                                        ];
                                        $pillClass = $statusClasses[$item->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $pillClass }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                    @if($item->processor)
                                        <div class="text-[10px] text-slate-400 mt-1 text-center truncate max-w-[130px] mx-auto" title="Diproses oleh: {{ $item->processor->name }} ({{ $item->processed_at ? $item->processed_at->format('d M Y H:i') : '' }})">
                                            Oleh: {{ Str::limit($item->processor->name, 14) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                @if(auth()->user()->canAccessPersetujuanPermisi() || !$isReviewerMode)
                                    <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-0.5">
                                            @if($isReviewerMode)
                                                @if($item->status === 'pending')
                                                    <button type="button"
                                                        @click="openConfirm('approve', {{ $item->id }}, 'Setujui Permohonan', 'Apakah Anda yakin ingin menyetujui permohonan izin keluar ini?')"
                                                        class="p-1 text-emerald-600 hover:bg-emerald-50 rounded-lg border border-transparent hover:border-emerald-200 transition cursor-pointer shrink-0"
                                                        title="Setujui Permohonan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                    </button>
                                                    <button type="button"
                                                        @click="openConfirm('reject', {{ $item->id }}, 'Tolak Permohonan', 'Apakah Anda yakin ingin menolak permohonan izin keluar ini?')"
                                                        class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0"
                                                        title="Tolak Permohonan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                @endif
                                                @if(auth()->user()->isSubbagUmum() || auth()->user()->isAdministrator())
                                                    <button type="button" wire:click="editBooking({{ $item->id }})"
                                                        class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition cursor-pointer shrink-0"
                                                        title="Edit Permohonan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                    <button type="button"
                                                        @click="openConfirm('delete', {{ $item->id }}, 'Hapus Permohonan', 'Apakah Anda yakin ingin menghapus permohonan ini?')"
                                                        class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0"
                                                        title="Hapus Permohonan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                @endif
                                            @else
                                                @if($item->status === 'pending' || auth()->user()->isSubbagUmum() || auth()->user()->isAdministrator())
                                                    <button type="button" wire:click="editBooking({{ $item->id }})"
                                                        class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition cursor-pointer shrink-0"
                                                        title="Edit Permohonan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                @endif
                                                @if($item->status === 'pending')
                                                    <button type="button"
                                                        @click="openConfirm('cancel', {{ $item->id }}, 'Batalkan Permohonan', 'Apakah Anda yakin ingin membatalkan permohonan izin ini?')"
                                                        class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0"
                                                        title="Batalkan Permohonan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                    </button>
                                                @elseif(in_array($item->status, ['ditolak', 'selesai', 'dibatalkan']))
                                                    <button type="button"
                                                        @click="openConfirm('delete', {{ $item->id }}, 'Hapus Permohonan', 'Apakah Anda yakin ingin menghapus permohonan ini?')"
                                                        class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0"
                                                        title="Hapus Permohonan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ (auth()->user()->canAccessPersetujuanPermisi() || !$isReviewerMode) ? '6' : '5' }}"
                                    class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                            <span class="material-symbols-outlined text-[28px]">description</span>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-600">Tidak ada data permohonan ditemukan.</p>
                                        <p class="text-xs text-slate-400 mt-1">Gunakan filter lain atau buat pengajuan baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->permisis->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-white">
                    {{ $this->permisis->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden space-y-3" wire:poll.15s>
            @forelse($this->permisis as $item)
                @php
                    $statusClasses = [
                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                        'selesai' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'dibatalkan' => 'bg-slate-100 text-slate-600 border-slate-200'
                    ];
                    $pillClass = $statusClasses[$item->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                @endphp
                <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-card flex flex-col gap-3">
                    <!-- Card Header -->
                    <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-sm shrink-0">
                                <span class="material-symbols-outlined text-[20px]">badge</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $item->user->name }}</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">NIP: {{ $item->user->nip_pendek ?? '-' }} &bull; {{ $item->user->jabatan ?? '-' }}@if(!empty($item->user->seksi)) &bull; <span class="text-brand-600 font-medium">{{ $item->user->seksi }}</span>@endif</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border shrink-0 {{ $pillClass }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Jenis & Kategori</p>
                            <p class="font-bold text-slate-800 text-xs">{{ $item->kategori }}</p>
                            <p class="text-[11px] text-slate-400">{{ $item->jenis }}</p>
                        </div>
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Mulai - Selesai</p>
                            <p class="font-semibold text-slate-700 text-[11px] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px] text-brand-600">event</span>
                                {{ $item->tanggal_mulai->format('d M Y, H:i') }}
                            </p>
                            <p class="font-semibold text-slate-700 text-[11px] flex items-center gap-1 mt-0.5">
                                <span class="material-symbols-outlined text-[13px] text-rose-500">event_busy</span>
                                {{ $item->tanggal_selesai->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>

                    @if($item->keperluan)
                        <div class="bg-slate-50/50 p-2.5 rounded-xl border border-slate-100 text-xs">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Keperluan:</p>
                            <p class="text-slate-700 leading-snug">{{ $item->keperluan }}</p>
                        </div>
                    @endif

                    @if($item->processor)
                        <div class="text-[11px] text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                            <span class="font-medium text-slate-700">Diproses oleh:</span> {{ $item->processor->name }}
                            @if($item->processed_at)
                                <span class="text-[10px] text-slate-400">({{ $item->processed_at->format('d/m/Y H:i') }})</span>
                            @endif
                        </div>
                    @endif

                    <!-- Card Footer Actions -->
                    @if(auth()->user()->canAccessPersetujuanPermisi() || !$isReviewerMode)
                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 flex-wrap">
                            @if($isReviewerMode)
                                @if($item->status === 'pending')
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <button type="button" @click="openConfirm('approve', {{ $item->id }}, 'Setujui Permohonan', 'Apakah Anda yakin ingin menyetujui permohonan ini?')"
                                            class="px-3 py-1.5 text-xs font-bold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Setujui</span>
                                        </button>
                                        <button type="button" @click="openConfirm('reject', {{ $item->id }}, 'Tolak Permohonan', 'Apakah Anda yakin ingin menolak permohonan ini?')"
                                            class="px-3 py-1.5 text-xs font-bold rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            <span>Tolak</span>
                                        </button>
                                    </div>
                                @endif
                                @if(auth()->user()->isSubbagUmum() || auth()->user()->isAdministrator())
                                    <div class="flex items-center gap-1 ml-auto">
                                        <button type="button" wire:click="editBooking({{ $item->id }})"
                                            class="p-2 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Edit Permohonan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button type="button" @click="openConfirm('delete', {{ $item->id }}, 'Hapus Permohonan', 'Apakah Anda yakin ingin menghapus permohonan ini?')"
                                            class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Hapus Permohonan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                @endif
                            @else
                                @if($item->status === 'pending')
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="openConfirm('cancel', {{ $item->id }}, 'Batalkan Permohonan', 'Apakah Anda yakin ingin membatalkan permohonan izin ini?')"
                                            class="px-3 py-1.5 text-xs font-bold rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            <span>Batalkan</span>
                                        </button>
                                    </div>
                                @endif
                                <div class="flex items-center gap-1 ml-auto">
                                    @if($item->status === 'pending' || auth()->user()->isSubbagUmum() || auth()->user()->isAdministrator())
                                        <button type="button" wire:click="editBooking({{ $item->id }})"
                                            class="p-2 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Edit Permohonan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                    @endif
                                    @if(in_array($item->status, ['ditolak', 'selesai', 'dibatalkan']))
                                        <button type="button" @click="openConfirm('delete', {{ $item->id }}, 'Hapus Permohonan', 'Apakah Anda yakin ingin menghapus permohonan ini?')"
                                            class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Hapus Permohonan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-3xl p-8 text-center border border-slate-200/90 shadow-card">
                    <span class="material-symbols-outlined text-[48px] text-slate-300 mb-2">description</span>
                    <p class="text-slate-500 font-semibold text-sm">Tidak ada data permohonan ditemukan.</p>
                </div>
            @endforelse

            @if($this->permisis->hasPages())
                <div class="pt-2">
                    {{ $this->permisis->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Form Pengajuan -->
    @if(!$isReviewerMode)
        <x-modal show="showApplicationModal" maxWidth="max-w-lg">
            <x-slot:header>
                <div class="flex justify-between items-center w-full">
                    <h2 class="text-base font-bold text-slate-900">Form Pengajuan Permisi</h2>
                    <button @click="showApplicationModal = false" type="button"
                        class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                        &times;
                    </button>
                </div>
            </x-slot:header>

            <form id="form-pengajuan-permisi" wire:submit.prevent="submitApplication" class="space-y-4 text-xs">
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
                        placeholder="Jelaskan keperluan izin Anda..."
                        class="w-full p-3 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition resize-none"></textarea>
                    @error('keperluan') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </form>

            <x-slot:footer>
                <div class="flex justify-end gap-2.5 w-full">
                    <button type="button" @click="showApplicationModal = false"
                        class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" form="form-pengajuan-permisi"
                        class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer">
                        <span wire:loading.remove wire:target="submitApplication">Kirim Pengajuan</span>
                        <span wire:loading wire:target="submitApplication">Memproses...</span>
                    </button>
                </div>
            </x-slot:footer>
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