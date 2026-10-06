<div x-data="{
    showFormModal: @entangle('showFormModal'),
    showNomorAwalModal: @entangle('showNomorAwalModal'),
    
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
        if (this.confirmAction === 'delete') {
            $wire.deleteSurat(this.confirmId);
        } else if (this.confirmAction === 'deleteNomorAwal') {
            $wire.deleteNomorAwal(this.confirmId);
        }
        this.showConfirmModal = false;
    }
}">

    @if(!auth()->user()->canAccessAdminDashboard())
        @if(!$isAdminMode)
            <x-top-header title="Pengambilan Nomor Surat Keluar" />
        @else
            <x-top-header title="Daftar Semua Surat Keluar" />
        @endif
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'py-6 pb-24 md:pb-8 max-w-7xl mx-auto flex-grow w-full px-4 sm:px-6 lg:px-8' }}">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                    {{ $isAdminMode ? 'Semua Surat Keluar' : 'Riwayat Surat Saya' }}
                </h1>
                <p class="text-slate-500 text-sm mt-1.5">
                    {{ $isAdminMode ? 'Manajemen dan daftar seluruh surat dinas keluar kantor.' : 'Form pengambilan nomor dinas dan riwayat surat keluar pribadi.' }}
                </p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap self-start sm:self-auto">
                @if(!$isAdminMode)
                    <button @click="showFormModal = true; $wire.createSurat()"
                        class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>Pengambilan Nomor Surat</span>
                    </button>
                @endif
                @if(auth()->user()->canAccessAsmaraAdmin())
                    <button @click="showNomorAwalModal = true; $wire.openNomorAwalModal()"
                        class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-2xs hover:border-slate-300 transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ $isAdminMode ? 'Pengaturan Surat Kahar' : 'Nomor Surat Kahar' }}</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Notification Messages --}}
        @if (session()->has('success_message') || session()->has('success'))
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200/80 shadow-xs flex items-start gap-3 transition-all">
                <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-bold text-emerald-900">Berhasil</p>
                    <p class="text-xs sm:text-sm text-emerald-700 mt-0.5 leading-relaxed">{{ session('success_message') ?? session('success') }}</p>
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

        <!-- Filters Bar -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 items-end">
                <div class="col-span-1 sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pencarian</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari Perihal / Tujuan / Nomor Surat..."
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                    </div>
                </div>

                <div class="col-span-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tahun</label>
                    <input type="number" wire:model.live="filterTahun"
                        class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                </div>

                <div class="col-span-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Surat</label>
                    <select wire:model.live="filterJenis"
                        class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                        <option value="">Semua Jenis</option>
                        @foreach($this->daftarJenisSurat as $js)
                            <option value="{{ $js->jenis_surat }}">{{ $js->jenis_surat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden relative min-h-[300px]">
            <div wire:loading.delay wire:target="search, filterTahun, filterJenis" class="absolute inset-0 bg-white/70 backdrop-blur-xs z-10 flex items-center justify-center">
                <div class="flex flex-col items-center gap-2.5 bg-white px-6 py-4 rounded-2xl shadow-xl border border-slate-100">
                    <div class="animate-spin rounded-full h-7 w-7 border-2 border-brand-600 border-t-transparent"></div>
                    <span class="text-xs font-bold text-slate-700">Memuat data...</span>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th wire:click="sortBy('nomor_surat')" class="py-3.5 px-6 whitespace-nowrap w-64 cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Nomor Surat</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'nomor_surat' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'nomor_surat' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'nomor_surat' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('perihal')" class="py-3.5 px-4 min-w-[250px] cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Perihal</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'perihal' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'perihal' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'perihal' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('tujuan_surat')" class="py-3.5 px-4 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Tujuan</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'tujuan_surat' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'tujuan_surat' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'tujuan_surat' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('tgl_surat')" class="py-3.5 px-4 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Tanggal</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'tgl_surat' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'tgl_surat' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'tgl_surat' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('perekam')" class="py-3.5 px-4 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Konseptor</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'perekam' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'perekam' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'perekam' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th class="py-3.5 px-6 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->listSurat as $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- Nomor Surat -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <code class="font-mono font-bold text-brand-700 bg-brand-50 px-2.5 py-1 rounded-md border border-brand-100 text-[11px] inline-block">
                                            {{ $item->nomorLengkap }}
                                        </code>
                                        @if($item->batal == '1')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                Batal
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Perihal -->
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-slate-800 line-clamp-2" title="{{ $item->perihal }}">
                                        {{ $item->perihal ?? '-' }}
                                    </div>
                                </td>

                                <!-- Tujuan -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="text-slate-600 truncate max-w-[200px]" title="{{ $item->tujuan_surat }}">
                                        {{ $item->tujuan_surat ?? '-' }}
                                    </div>
                                </td>

                                <!-- Tanggal -->
                                <td class="py-4 px-4 whitespace-nowrap text-slate-500 font-medium">
                                    {{ $item->tgl_surat ? $item->tgl_surat->format('d M Y') : '-' }}
                                </td>

                                <!-- Konseptor -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <p class="font-bold text-slate-800 leading-snug">
                                        {{ $item->user->name ?? $item->perekam }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $item->perekam }}
                                    </p>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-0.5">
                                        <button type="button" @click="showFormModal = true; $wire.editSurat({{ $item->id_surat_keluar }})"
                                            class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition cursor-pointer shrink-0"
                                            title="Edit Surat">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button type="button" @click="openConfirm('delete', {{ $item->id_surat_keluar }}, 'Hapus Surat', 'Apakah Anda yakin ingin menghapus surat ini?')"
                                            class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0"
                                            title="Hapus Surat">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-1">
                                            <span class="material-symbols-outlined text-[28px]">drafts</span>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-600">Tidak ada data surat keluar.</p>
                                        <p class="text-xs text-slate-400">Silakan sesuaikan kata kunci pencarian atau ambil nomor surat baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-3 p-4">
                @forelse($this->listSurat as $item)
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-card flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3">
                            <div>
                                <code class="font-mono font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-100 text-xs inline-block">
                                    {{ $item->nomorLengkap }}
                                </code>
                                <p class="text-[11px] text-slate-400 mt-1 font-medium">
                                    {{ $item->tgl_surat ? $item->tgl_surat->format('d M Y') : '-' }}
                                </p>
                            </div>
                            @if($item->batal == '1')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                                    Batal
                                </span>
                            @endif
                        </div>

                        <div class="text-xs space-y-2">
                            <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Perihal:</p>
                                <p class="font-semibold text-slate-800 leading-snug">{{ $item->perihal ?? '-' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                                    <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Tujuan</p>
                                    <p class="font-semibold text-slate-700 truncate">{{ $item->tujuan_surat ?? '-' }}</p>
                                </div>
                                <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                                    <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Konseptor</p>
                                    <p class="font-semibold text-slate-700 truncate">{{ $item->user->name ?? $item->perekam }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-slate-100">
                            <button type="button" @click="showFormModal = true; $wire.editSurat({{ $item->id_surat_keluar }})"
                                class="p-2 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Edit Surat">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button type="button" @click="openConfirm('delete', {{ $item->id_surat_keluar }}, 'Hapus Surat', 'Apakah Anda yakin ingin menghapus surat ini?')"
                                class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Hapus Surat">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-3xl p-8 text-center border border-slate-200/90 shadow-card">
                        <span class="material-symbols-outlined text-[48px] text-slate-300 mb-2">drafts</span>
                        <p class="text-slate-500 font-semibold text-sm">Tidak ada data surat keluar.</p>
                    </div>
                @endforelse
            </div>
            
            @if($this->listSurat->hasPages())
                <div class="border-t border-slate-100 bg-white px-6 py-4">
                    {{ $this->listSurat->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Form Surat -->
    <x-modal show="showFormModal" maxWidth="max-w-xl">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    {{ $editSuratId ? 'Edit Surat Keluar' : 'Pengambilan Nomor Surat' }}
                </h2>
                <button @click="showFormModal = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <form wire:submit.prevent="simpanSurat" id="form-surat" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Jenis Surat -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Surat <span class="text-rose-500">*</span></label>
                    <select wire:model.live="jenisSurat"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                        <option value="">Pilih Jenis Surat</option>
                        @foreach($this->daftarJenisSurat as $js)
                            <option value="{{ $js->jenis_surat }}">{{ $js->jenis_surat }}</option>
                        @endforeach
                    </select>
                    @error('jenisSurat') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Jenis PJ -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis PJ <span class="text-rose-500">*</span></label>
                    <select wire:model.live="jenisPj"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                        <option value="">Pilih Jenis PJ</option>
                        @foreach($this->daftarJenisPj as $jpj)
                            <option value="{{ $jpj->jenis_pj }}">{{ $jpj->jenis_pj }}</option>
                        @endforeach
                    </select>
                    @error('jenisPj') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Tahun Surat -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tahun Surat <span class="text-rose-500">*</span></label>
                    <input type="number" wire:model.live="tahunSurat"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    @error('tahunSurat') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Preview Nomor Otomatis -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Preview Nomor Surat</label>
                    <div class="w-full px-3.5 py-2.5 bg-brand-50 text-brand-700 font-mono font-bold rounded-xl border border-brand-200 text-xs truncate" title="{{ $this->previewNomor }}">
                        <div wire:loading wire:target="jenisSurat, jenisPj, tahunSurat" class="inline-block">
                            <span class="animate-pulse">Menghitung...</span>
                        </div>
                        <div wire:loading.remove wire:target="jenisSurat, jenisPj, tahunSurat">
                            {{ $this->previewNomor }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Tanggal Surat -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Surat <span class="text-rose-500">*</span></label>
                    <input type="date" wire:model="tglSurat"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    @error('tglSurat') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Konseptor (Readonly) -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konseptor</label>
                    <input type="text" value="{{ auth()->user()->name }}" disabled
                        class="w-full px-3.5 py-2.5 bg-slate-100 text-slate-600 rounded-xl border border-slate-200 text-xs font-medium cursor-not-allowed">
                </div>
            </div>

            <!-- Perihal -->
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Perihal <span class="text-rose-500">*</span></label>
                <textarea wire:model="perihal" rows="2" placeholder="Masukkan perihal surat..."
                    class="w-full p-3 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition resize-none"></textarea>
                @error('perihal') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Tujuan Surat -->
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tujuan Surat <span class="text-rose-500">*</span></label>
                <input type="text" wire:model="tujuanSurat" placeholder="Masukkan tujuan surat..."
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                @error('tujuanSurat') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Keterangan / Catatan</label>
                <textarea wire:model="keterangan" rows="2" placeholder="Masukkan keterangan tambahan jika ada..."
                    class="w-full p-3 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition resize-none"></textarea>
                @error('keterangan') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Duplikasi -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Duplikasi sampai dengan <span class="text-rose-500">*</span></label>
                    <input type="number" wire:model="duplikasi" min="1" max="50" {{ $editSuratId ? 'disabled' : '' }}
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition {{ $editSuratId ? 'bg-slate-100 cursor-not-allowed' : '' }}">
                    <span class="text-[11px] text-slate-400 mt-1 block">Berapa banyak nomor berurutan yang ingin diambil sekaligus.</span>
                    @error('duplikasi') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Status Batal -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Surat</label>
                    <select wire:model="batal"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                        <option value="">Aktif (Tidak Batal)</option>
                        <option value="1">Batal (Surat dibatalkan)</option>
                    </select>
                    @error('batal') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showFormModal = false" class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" form="form-surat" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer">
                    <span wire:loading.remove wire:target="simpanSurat">Simpan Data</span>
                    <span wire:loading wire:target="simpanSurat">Menyimpan...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Modal TblNomorAwal (Kahar) -->
    @if(auth()->user()->canAccessAsmaraAdmin())
    <x-modal show="showNomorAwalModal" maxWidth="max-w-2xl">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    Form Penginputan Nomor Surat Kahar
                </h2>
                <button @click="showNomorAwalModal = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <div class="space-y-6 text-xs">
            <!-- Top Form: Tambah / Edit -->
            <div class="bg-slate-50/70 border border-slate-200/90 rounded-2xl p-4 sm:p-5">
                <h4 class="font-bold text-slate-800 text-sm mb-3">{{ $editNomorAwalId ? 'Edit Pengaturan Nomor Awal' : 'Tambah Pengaturan Nomor Awal' }}</h4>
                <form wire:submit.prevent="simpanNomorAwal" id="form-nomor-awal">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Surat <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="nomorAwalJenisSurat" list="daftarJenisSuratList"
                                placeholder="Ketik jenis surat (cth: KEP-)"
                                class="w-full px-3.5 py-2 bg-white rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                            <datalist id="daftarJenisSuratList">
                                @foreach($this->daftarJenisSurat as $js)
                                    <option value="{{ $js->jenis_surat }}"></option>
                                @endforeach
                            </datalist>
                            @error('nomorAwalJenisSurat') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis PJ</label>
                            <select wire:model="nomorAwalJenisPj"
                                class="w-full px-3.5 py-2 bg-white rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                                <option value="">(Kosong)</option>
                                @foreach($this->daftarJenisPj as $jpj)
                                    <option value="{{ $jpj->jenis_pj }}">{{ $jpj->jenis_pj }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tahun <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="nomorAwalTahun"
                                class="w-full px-3.5 py-2 bg-white rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                            @error('nomorAwalTahun') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Awal <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model="nomorAwalAngka" placeholder="Contoh: 91000"
                                class="w-full px-3.5 py-2 bg-white rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                            @error('nomorAwalAngka') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-200/80">
                        @if($editNomorAwalId)
                            <button type="button" wire:click="openNomorAwalModal"
                                class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200/60 transition cursor-pointer">
                                Batal Edit
                            </button>
                        @endif
                        <button type="submit"
                            class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold text-xs shadow-sm shadow-brand-600/25 transition cursor-pointer">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Bottom Table: List of Settings -->
            <div>
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2.5 mb-3">
                    <h4 class="font-bold text-slate-800 text-sm">Daftar Pengaturan Nomor Awal</h4>
                    <div class="relative w-full sm:w-64">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <span class="material-symbols-outlined text-[16px]">search</span>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="searchNomorAwal" placeholder="Cari jenis / tahun..."
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-9 pr-3 py-1.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 outline-none transition">
                    </div>
                </div>

                <div class="border border-slate-200/90 rounded-2xl overflow-hidden max-h-[300px] overflow-y-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-500 uppercase tracking-wider sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Jenis Surat</th>
                                <th class="px-4 py-3">Jenis PJ</th>
                                <th class="px-4 py-3">Tahun</th>
                                <th class="px-4 py-3 text-center">Nomor Awal</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($this->daftarNomorAwal as $na)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-4 py-2.5 font-bold text-slate-800">{{ $na->jenis_surat }}</td>
                                    <td class="px-4 py-2.5 text-slate-500">{{ $na->jenis_pj ?? '-' }}</td>
                                    <td class="px-4 py-2.5 text-slate-600">{{ $na->tahun }}</td>
                                    <td class="px-4 py-2.5 font-mono font-bold text-brand-600 text-center">{{ $na->nomor_awal }}</td>
                                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-0.5">
                                            <button type="button" @click="showNomorAwalModal = true; $wire.editNomorAwal({{ $na->id }})"
                                                class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition cursor-pointer shrink-0" title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button type="button" @click="openConfirm('deleteNomorAwal', {{ $na->id }}, 'Hapus Nomor Awal', 'Yakin hapus setting ini?')"
                                                class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0" title="Hapus">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                        Belum ada pengaturan nomor awal.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end w-full">
                <button type="button" @click="showNomorAwalModal = false" class="px-5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </x-slot:footer>
    </x-modal>
    @endif

    <!-- Shared Confirmation Modal -->
    <x-modal show="showConfirmModal" maxWidth="max-w-md">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900" x-text="confirmTitle">Hapus Data</h2>
                <button type="button" @click="showConfirmModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center"
                :class="{'bg-emerald-50 text-emerald-600 border border-emerald-200': confirmAction === 'approve' || confirmAction === 'complete', 'bg-rose-50 text-rose-600 border border-rose-200': confirmAction === 'reject' || confirmAction === 'cancel' || confirmAction === 'delete' || confirmAction === 'deleteNomorAwal'}">
                <span class="material-symbols-outlined text-[24px]"
                    x-text="(confirmAction === 'approve' || confirmAction === 'complete') ? 'check_circle' : 'warning'">warning</span>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" x-text="confirmMessage"></p>
            </div>
        </div>

        <x-slot:footer>
            <button type="button" @click="showConfirmModal = false"
                class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                Batal
            </button>
            <button type="button" x-show="confirmAction === 'approve' || confirmAction === 'complete'" @click="executeAction()"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 transition-colors cursor-pointer">
                Ya, Lanjutkan
            </button>
            <button type="button" x-show="confirmAction === 'reject' || confirmAction === 'cancel' || confirmAction === 'delete' || confirmAction === 'deleteNomorAwal'" @click="executeAction()"
                class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/20 transition-colors cursor-pointer">
                Ya, Lanjutkan
            </button>
        </x-slot:footer>
    </x-modal>
</div>
