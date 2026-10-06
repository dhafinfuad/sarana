<div x-data="{ showTambahModal: false, showEditModal: false, showHapusModal: false }"
     x-on:close-tambah-modal.window="showTambahModal = false"
     x-on:close-edit-modal.window="showEditModal = false"
     x-on:close-hapus-modal.window="showHapusModal = false">
    
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Master Satuan</h1>
            <p class="text-slate-500 text-sm mt-1.5">Kelola data satuan barang ATK (PCS, RIM, BOX, dsb).</p>
        </div>
        <button type="button" @click="showTambahModal = true; $wire.openTambah()"
            class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Satuan</span>
        </button>
    </div>

    {{-- Notification Messages --}}
    @if (session('success'))
        <div role="alert" class="mb-6 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200/80 shadow-xs flex items-start gap-3 transition-all">
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
    @endif
    @if (session('error'))
        <div role="alert" class="mb-6 p-4 rounded-2xl bg-rose-50/90 border border-rose-200/80 shadow-xs flex items-start gap-3 transition-all">
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
    @endif

    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-6 w-16">No</th>
                        <th wire:click="sortBy('nama_satuan')" class="py-3.5 px-4 cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                <span>Nama Satuan</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'nama_satuan' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'nama_satuan' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'nama_satuan' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th class="py-3.5 px-6 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($this->satuans as $index => $satuan)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6 text-slate-500 font-medium">{{ $index + 1 }}</td>
                            <td class="py-4 px-4 font-bold text-slate-900">{{ $satuan->nama_satuan }}</td>
                            <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-0.5">
                                    <button type="button" @click="showEditModal = true; $wire.openEdit({{ $satuan->id }})"
                                        class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition cursor-pointer shrink-0" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" @click="showHapusModal = true; $wire.openHapus({{ $satuan->id }})"
                                        class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-1.5 opacity-40">straighten</span>
                                <p class="font-semibold text-slate-600">Tidak ada data satuan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden space-y-3 p-4">
            @forelse($this->satuans as $index => $satuan)
                <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs flex items-center justify-between gap-3" wire:key="satuan-card-{{ $satuan->id }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 font-bold text-xs flex items-center justify-center border border-brand-100 shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm leading-tight">{{ $satuan->nama_satuan }}</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Satuan Barang</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" @click="showEditModal = true; $wire.openEdit({{ $satuan->id }})"
                            class="p-2 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-xl border border-amber-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Edit Satuan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <button type="button" @click="showHapusModal = true; $wire.openHapus({{ $satuan->id }})"
                            class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition cursor-pointer flex items-center justify-center shadow-2xs" title="Hapus Satuan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-slate-400">
                    <span class="material-symbols-outlined text-4xl mb-1.5 opacity-40">straighten</span>
                    <p class="font-semibold text-slate-600 text-xs">Tidak ada data satuan.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Tambah Satuan -->
    <x-modal show="showTambahModal" maxWidth="max-w-sm" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Tambah Satuan Baru</h2>
                <button type="button" @click="showTambahModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>
        
        <form wire:submit.prevent="save" id="formTambah" class="space-y-4 text-xs">
            <div>
                <label for="namaSatuan" class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Satuan <span class="text-rose-500">*</span></label>
                <input type="text" id="namaSatuan" wire:model="namaSatuan"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                    placeholder="Contoh: PCS, RIM, BOX">
                @error('namaSatuan') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showTambahModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="save"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer"
                    wire:loading.attr="disabled">
                    Simpan
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Edit Satuan -->
    <x-modal show="showEditModal" maxWidth="max-w-sm" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Edit Data Satuan</h2>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>
        
        <form wire:submit.prevent="update" id="formEdit" class="space-y-4 text-xs">
            <div>
                <label for="editNamaSatuan" class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Satuan <span class="text-rose-500">*</span></label>
                <input type="text" id="editNamaSatuan" wire:model="namaSatuan"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                @error('namaSatuan') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showEditModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="update"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer"
                    wire:loading.attr="disabled">
                    Simpan Perubahan
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Hapus Satuan -->
    <x-modal show="showHapusModal" maxWidth="max-w-sm" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Hapus Satuan</h2>
                <button type="button" @click="showHapusModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>
        
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">delete</span>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Apakah Anda yakin ingin menghapus satuan ini? Pastikan satuan tidak sedang digunakan oleh data barang.
                </p>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showHapusModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="hapus"
                    class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md shadow-rose-600/20 transition text-xs cursor-pointer"
                    wire:loading.attr="disabled">
                    Ya, Hapus
                </button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
