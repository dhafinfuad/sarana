<div x-data="{
    showAddModal: false,
    showEditModal: false,
    showConfirmModal: false,
    showUploadModal: false,
    confirmId: null,
    
    openConfirm(id) {
        this.confirmId = id;
        this.showConfirmModal = true;
    },
    
    executeDelete() {
        $wire.deleteUser(this.confirmId);
        this.showConfirmModal = false;
    }
}" @close-edit-modal.window="showEditModal = false" @close-upload-modal.window="showUploadModal = false"
    @close-add-modal.window="showAddModal = false" @open-edit-modal.window="showEditModal = true">

    {{-- Notification Messages --}}
    @if(session()->has('success'))
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
    @if(session()->has('error'))
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

    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Manajemen User</h1>
            <p class="text-slate-500 text-sm mt-1.5">Kelola data pegawai, edit profil unit seksi, dan atur kata sandi akun.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap self-start sm:self-auto">
            <button wire:click="downloadTemplate"
                class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-2xs hover:border-slate-300 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Template .xlsx</span>
            </button>
            <button @click="showUploadModal = true"
                class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-2xs hover:border-slate-300 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"></path></svg>
                <span>Upload CSV</span>
            </button>
            <button @click="showAddModal = true"
                class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Pegawai</span>
            </button>
        </div>
    </div>

    <!-- User Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
            <!-- Search Bar -->
            <div class="relative flex-1 min-w-0 sm:max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </div>
                <input wire:model.live.debounce.300ms="search"
                    class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all"
                    placeholder="Cari Nama atau NIP Pegawai..." type="text" />
            </div>

            <!-- Per Page -->
            <div class="flex items-center gap-2">
                <select wire:model.live="perPage"
                    class="bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 outline-none transition">
                    <option value="50">50 Baris</option>
                    <option value="100">100 Baris</option>
                </select>
            </div>
        </div>

        <div class="relative">
            <!-- Loading overlay -->
            <div wire:loading.flex
                class="absolute inset-0 bg-white/70 backdrop-blur-xs z-10 flex items-center justify-center">
                <div class="flex flex-col items-center gap-2 bg-white px-6 py-4 rounded-2xl shadow-xl border border-slate-100">
                    <div class="animate-spin rounded-full h-7 w-7 border-2 border-brand-600 border-t-transparent"></div>
                    <span class="text-xs font-bold text-slate-700">Memuat data...</span>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                            <th wire:click="sortBy('name')"
                                class="min-w-[220px] py-3.5 px-6 cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Nama Pegawai</span>
                                    <span class="material-symbols-outlined text-[16px] {{ $sortColumn === 'name' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'name' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'name' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('nip_pendek')"
                                class="min-w-[140px] py-3.5 px-4 cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>NIP Pendek</span>
                                    <span class="material-symbols-outlined text-[16px] {{ $sortColumn === 'nip_pendek' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'nip_pendek' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'nip_pendek' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('seksi')"
                                class="min-w-[200px] py-3.5 px-4 cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Seksi</span>
                                    <span class="material-symbols-outlined text-[16px] {{ $sortColumn === 'seksi' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'seksi' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'seksi' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('jabatan')"
                                class="min-w-[150px] py-3.5 px-4 cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Jabatan</span>
                                    <span class="material-symbols-outlined text-[16px] {{ $sortColumn === 'jabatan' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'jabatan' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'jabatan' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th class="py-3.5 px-6 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->users as $user)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $user->name }}
                                </td>
                                <td class="py-4 px-4 font-mono font-medium text-slate-600">
                                    {{ $user->nip_pendek }}
                                </td>
                                <td class="py-4 px-4 text-slate-600 truncate max-w-[200px]">
                                    @if($user->seksi)
                                        <span class="inline-flex items-center text-[11px] font-semibold text-brand-700 bg-brand-50 border border-brand-200/60 px-2 py-0.5 rounded-md">
                                            {{ $user->seksi }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-slate-600 font-medium">
                                    {{ $user->jabatan ?? '-' }}
                                </td>
                                <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-0.5">
                                        <button @click="showEditModal = true; $wire.openEdit({{ $user->id }})" title="Edit Pegawai"
                                            class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition cursor-pointer shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <button @click="openConfirm({{ $user->id }})" title="Hapus Pegawai"
                                                class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-1.5">
                                        <span class="material-symbols-outlined text-[40px] text-slate-300">group_off</span>
                                        <p class="font-semibold text-slate-600">Tidak ada data pegawai yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-3 p-4">
                @forelse($this->users as $user)
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-card flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-2.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-bold shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">person</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $user->name }}</h3>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">NIP: {{ $user->nip_pendek }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Seksi</p>
                                <p class="font-semibold text-slate-800 truncate">{{ $user->seksi ?? '-' }}</p>
                            </div>
                            <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Jabatan</p>
                                <p class="font-semibold text-slate-800 truncate">{{ $user->jabatan ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-slate-100">
                            <button type="button" @click="showEditModal = true; $wire.openEdit({{ $user->id }})"
                                class="p-2 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Edit Pegawai">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            @if($user->id !== auth()->id())
                                <button type="button" @click="openConfirm({{ $user->id }})"
                                    class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Hapus Pegawai">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <p>Tidak ada data pegawai yang ditemukan.</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 border-t border-slate-100 bg-white flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan {{ $this->users->firstItem() ?? 0 }} hingga {{ $this->users->lastItem() ?? 0 }} dari
                    {{ $this->users->total() }} pegawai
                </div>
                <div class="w-full md:w-auto overflow-x-auto">
                    {{ $this->users->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <x-modal show="showEditModal" maxWidth="max-w-md">
        <x-slot:header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-base font-bold text-slate-900">Edit Data Pegawai</h2>
                <button @click="showEditModal = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <form wire:submit.prevent="updateUser" id="editForm" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Pegawai <span class="text-rose-500">*</span></label>
                <input wire:model="editName" type="text"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                    required>
                @error('editName') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIP Pendek <span class="text-rose-500">*</span></label>
                <input wire:model="editNipPendek" type="text"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                    required>
                @error('editNipPendek') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIP Panjang (Opsional)</label>
                <input wire:model="editNipPanjang" type="text"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                @error('editNipPanjang') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Seksi (Opsional)</label>
                <select wire:model="editSeksi"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                    <option value="">Pilih Seksi...</option>
                    <option value="Fungsional Pemeriksa">Fungsional Pemeriksa</option>
                    <option value="Seksi Pelayanan">Seksi Pelayanan</option>
                    <option value="Subbagian Umum dan Kepatuhan Internal">Subbagian Umum dan Kepatuhan Internal</option>
                    <option value="Seksi Pengawasan I">Seksi Pengawasan I</option>
                    <option value="Seksi Pengawasan II">Seksi Pengawasan II</option>
                    <option value="Seksi Pengawasan V">Seksi Pengawasan V</option>
                    <option value="Seksi Pemeriksaan, Penilaian, dan Penagihan">Seksi Pemeriksaan, Penilaian, dan Penagihan</option>
                    <option value="Seksi Pengawasan IV">Seksi Pengawasan IV</option>
                    <option value="Seksi Pengawasan VI">Seksi Pengawasan VI</option>
                    <option value="Seksi Pengawasan III">Seksi Pengawasan III</option>
                    <option value="Seksi Penjaminan Kualitas Data">Seksi Penjaminan Kualitas Data</option>
                </select>
                @error('editSeksi') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jabatan (Opsional)</label>
                <select wire:model="editJabatan"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                    <option value="">Pilih Jabatan...</option>
                    <option value="Kepala Kantor">Kepala Kantor</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Kepala Seksi">Kepala Seksi</option>
                    <option value="AR">AR</option>
                    <option value="FPP">FPP</option>
                    <option value="Penyuluh">Penyuluh</option>
                    <option value="Juru Sita">Juru Sita</option>
                    <option value="Penilai">Penilai</option>
                    <option value="Pelaksana">Pelaksana</option>
                </select>
                @error('editJabatan') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div class="pt-3 border-t border-slate-100">
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Reset Password (Opsional)</label>
                <div class="relative" x-data="{ show: false }">
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 left-0 pl-3 text-slate-400 hover:text-slate-700 transition flex items-center justify-center z-10 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                    </button>
                    <input :type="show ? 'text' : 'password'" wire:model="newPassword"
                        placeholder="Kosongkan jika tidak ingin mengubah password"
                        class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 outline-none transition">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Hanya isi jika ingin mengganti kata sandi pengguna ini.</p>
                @error('newPassword') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showEditModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" form="editForm"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer">
                    <span wire:loading.remove wire:target="updateUser">Simpan Perubahan</span>
                    <span wire:loading wire:target="updateUser">Menyimpan...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Upload CSV Modal -->
    <x-modal show="showUploadModal" maxWidth="max-w-md">
        <x-slot:header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-base font-bold text-slate-900">Import Data Pegawai via CSV</h2>
                <button @click="showUploadModal = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <div class="space-y-4 text-xs">
            <div class="bg-amber-50/80 border border-amber-200 p-3.5 rounded-2xl text-amber-800 leading-relaxed">
                <span class="font-bold block mb-1">Panduan Alur Import:</span>
                <ol class="list-decimal pl-4 space-y-0.5 text-[11px]">
                    <li>Gunakan <strong>Format .xlsx</strong> dari tombol Template agar digit nol NIP tidak hilang.</li>
                    <li>Simpan sebagai <strong>CSV (Comma delimited) (*.csv)</strong> jika diperlukan.</li>
                    <li>Pilih file CSV tersebut di form berikut.</li>
                </ol>
            </div>

            <form wire:submit.prevent="uploadCsv" id="uploadForm">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih File CSV / Excel <span class="text-rose-500">*</span></label>
                    <input type="file" wire:model="csvFile" accept=".csv,.xlsx" required
                        class="w-full px-3 py-2 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    @error('csvFile') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </form>
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showUploadModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" form="uploadForm"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-600/20 transition text-xs cursor-pointer"
                    wire:loading.attr="disabled" wire:target="uploadCsv">
                    <span wire:loading.remove wire:target="uploadCsv">Mulai Import</span>
                    <span wire:loading wire:target="uploadCsv">Memproses...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Add User Modal -->
    <x-modal show="showAddModal" maxWidth="max-w-md">
        <x-slot:header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-base font-bold text-slate-900">Tambah Pegawai Baru</h2>
                <button @click="showAddModal = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <form wire:submit.prevent="createUser" id="addForm" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Pegawai <span class="text-rose-500">*</span></label>
                <input wire:model="addName" type="text"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                    required>
                @error('addName') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIP Pendek <span class="text-rose-500">*</span></label>
                <input wire:model="addNipPendek" type="text"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                    required>
                @error('addNipPendek') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIP Panjang (Opsional)</label>
                <input wire:model="addNipPanjang" type="text"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                @error('addNipPanjang') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Seksi (Opsional)</label>
                <select wire:model="addSeksi"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                    <option value="">Pilih Seksi...</option>
                    <option value="Fungsional Pemeriksa">Fungsional Pemeriksa</option>
                    <option value="Seksi Pelayanan">Seksi Pelayanan</option>
                    <option value="Subbagian Umum dan Kepatuhan Internal">Subbagian Umum dan Kepatuhan Internal</option>
                    <option value="Seksi Pengawasan I">Seksi Pengawasan I</option>
                    <option value="Seksi Pengawasan II">Seksi Pengawasan II</option>
                    <option value="Seksi Pengawasan V">Seksi Pengawasan V</option>
                    <option value="Seksi Pemeriksaan, Penilaian, dan Penagihan">Seksi Pemeriksaan, Penilaian, dan Penagihan</option>
                    <option value="Seksi Pengawasan IV">Seksi Pengawasan IV</option>
                    <option value="Seksi Pengawasan VI">Seksi Pengawasan VI</option>
                    <option value="Seksi Pengawasan III">Seksi Pengawasan III</option>
                    <option value="Seksi Penjaminan Kualitas Data">Seksi Penjaminan Kualitas Data</option>
                </select>
                @error('addSeksi') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jabatan (Opsional)</label>
                <select wire:model="addJabatan"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                    <option value="">Pilih Jabatan...</option>
                    <option value="Kepala Kantor">Kepala Kantor</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Kepala Seksi">Kepala Seksi</option>
                    <option value="AR">AR</option>
                    <option value="FPP">FPP</option>
                    <option value="Penyuluh">Penyuluh</option>
                    <option value="Juru Sita">Juru Sita</option>
                    <option value="Penilai">Penilai</option>
                    <option value="Pelaksana">Pelaksana</option>
                </select>
                @error('addJabatan') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
            <div class="pt-3 border-t border-slate-100">
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password <span class="text-rose-500">*</span></label>
                <div class="relative" x-data="{ show: false }">
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 left-0 pl-3 text-slate-400 hover:text-slate-700 transition flex items-center justify-center z-10 cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                    </button>
                    <input :type="show ? 'text' : 'password'" wire:model="addPassword" placeholder="Minimal 6 karakter"
                        class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 outline-none transition"
                        required>
                </div>
                @error('addPassword') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showAddModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" form="addForm"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer"
                    wire:loading.attr="disabled" wire:target="createUser">
                    <span wire:loading.remove wire:target="createUser">Simpan Pegawai</span>
                    <span wire:loading wire:target="createUser">Menyimpan...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Confirm Delete Modal -->
    <x-modal show="showConfirmModal" maxWidth="max-w-md">
        <x-slot:header>
            <div class="flex items-center gap-2.5 text-rose-600 w-full">
                <span class="material-symbols-outlined text-[22px]">warning</span>
                <h2 class="text-base font-bold text-slate-900">Hapus Data Pegawai</h2>
            </div>
        </x-slot:header>

        <div class="text-xs text-slate-600 leading-relaxed">
            Apakah Anda yakin ingin menghapus data pegawai ini? Seluruh riwayat dan akun pengguna terkait akan dinonaktifkan / dihapus secara permanen.
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showConfirmModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="executeDelete"
                    class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md shadow-rose-600/20 transition text-xs cursor-pointer"
                    wire:loading.attr="disabled" wire:target="deleteUser">
                    <span wire:loading.remove wire:target="deleteUser">Ya, Hapus</span>
                    <span wire:loading wire:target="deleteUser">Menghapus...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>