<div x-data="{ showTambahModal: false, showEditModal: false, showTambahStokModal: false, showSatuanModal: false, showImportModal: false }"
     x-on:close-tambah-modal.window="showTambahModal = false"
     x-on:close-edit-modal.window="showEditModal = false"
     x-on:close-tambah-stok-modal.window="showTambahStokModal = false"
     x-on:close-import-modal.window="showImportModal = false">
    
    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Master Barang</h1>
            <p class="text-slate-500 text-sm mt-1.5">Kelola data barang ATK, foto produk, dan stok batas minimum.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap self-start sm:self-auto">
            <button type="button" wire:click="downloadMasterBarang" wire:loading.attr="disabled"
                class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-2xs hover:border-slate-300 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Download</span>
            </button>
            <button type="button" @click="showImportModal = true"
                class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-2xs hover:border-slate-300 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"></path></svg>
                <span>Import</span>
            </button>
            <button type="button" @click="showSatuanModal = true; $wire.openSatuanModal()"
                class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-2xs hover:border-slate-300 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                <span>Edit Satuan</span>
            </button>
            <button type="button" @click="showTambahModal = true; $wire.openTambah()"
                class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Barang</span>
            </button>
        </div>
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

    {{-- Main Inventory Cards Grid Container --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden" wire:poll.30s>
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
            <div class="relative flex-1 min-w-0 sm:max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all"
                    placeholder="Cari nama atau kode barang...">
            </div>
            
            <label class="w-full sm:w-auto flex items-center justify-center sm:justify-start gap-2 cursor-pointer px-3.5 py-2 border rounded-xl shadow-2xs select-none transition-colors {{ $filterStokMenipis ? 'border-rose-300 bg-rose-50 text-rose-700' : 'border-slate-200 bg-slate-50/60 text-slate-700 hover:bg-slate-100' }}">
                <input type="checkbox" wire:model.live="filterStokMenipis" class="rounded text-rose-600 focus:ring-rose-500">
                <span class="text-xs font-semibold whitespace-nowrap">Stok Menipis</span>
            </label>

            <div class="text-xs text-slate-400 font-medium hidden sm:block" wire:loading wire:target="search, filterStokMenipis">
                Memuat data...
            </div>
        </div>
        
        <div class="p-6 bg-slate-50/50">
            @if($this->bonItems->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <span class="material-symbols-outlined text-4xl mb-2 opacity-50">inventory_2</span>
                    <p class="font-semibold text-slate-600">Tidak ada data barang ditemukan.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @foreach($this->bonItems as $item)
                        <div class="bg-white rounded-3xl p-3.5 border border-slate-200/90 shadow-card hover:shadow-lg transition flex flex-col justify-between group" wire:key="item-{{ $item->id }}">
                            <div>
                                <div class="relative w-full h-28 bg-slate-50 rounded-2xl flex items-center justify-center p-2 mb-3 overflow-hidden border border-slate-100">
                                    <span class="absolute top-2 right-2 z-10 px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/95 backdrop-blur-xs {{ $item->stok <= $item->stok_minimum ? 'text-rose-600 border-rose-200' : 'text-brand-700 border-slate-200' }} border shadow-2xs pointer-events-none">
                                        Stok: {{ $item->stok }} {{ $item->satuan->nama_satuan ?? 'PCS' }}
                                    </span>
                                    @if($item->image_path)
                                        <img class="w-full h-full object-contain group-hover:scale-105 transition duration-300" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->nama_barang }}">
                                    @else
                                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    @endif
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2" title="{{ $item->nama_barang }}">
                                    {{ $item->nama_barang }}
                                </h4>
                            </div>

                            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center gap-1.5">
                                <button type="button" @click="showEditModal = true; $wire.openEdit({{ $item->id }})"
                                    class="flex-1 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer">
                                    <span>Edit</span>
                                </button>
                                <button type="button" @click="showTambahStokModal = true; $wire.openTambahStok({{ $item->id }})"
                                    class="py-1.5 px-3 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 cursor-pointer">
                                    <span>+ Stok</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100 bg-white">
            {{ $this->bonItems->links() }}
        </div>
    </div>

    <!-- Modal Tambah Barang -->
    <x-modal show="showTambahModal" maxWidth="max-w-md" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Tambah Barang Baru</h2>
                <button type="button" @click="showTambahModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>
        
        <form wire:submit.prevent="save" id="formTambah" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Barang <span class="text-rose-500">*</span></label>
                <input type="text" wire:model="namaBarang"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                    placeholder="Contoh: Kertas HVS A4 80gr">
                @error('namaBarang') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Satuan <span class="text-rose-500">*</span></label>
                <select wire:model="satuanId"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    <option value="">Pilih Satuan</option>
                    @foreach($this->satuans as $satuan)
                        <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                    @endforeach
                </select>
                @error('satuanId') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
            </div>
            
            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stok Awal</label>
                    <input type="number" wire:model="stokAwal" min="0"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                    @error('stokAwal') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stok Minimum</label>
                    <input type="number" wire:model="stokMinimum" min="0"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                    @error('stokMinimum') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Foto Barang (Opsional)</label>
                <input type="file" wire:model="photo" accept="image/*"
                    class="w-full px-3 py-2 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                <div wire:loading wire:target="photo" class="text-[11px] text-brand-600 mt-1 font-medium">Mengunggah foto...</div>
                @error('photo') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                
                @if ($photo)
                    <div class="mt-2.5 relative">
                        <img src="{{ $photo->temporaryUrl() }}" class="h-20 w-20 object-cover rounded-xl border border-slate-200">
                    </div>
                @endif
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
                    wire:loading.attr="disabled" wire:target="photo, save">
                    Simpan Barang
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Edit Barang -->
    <x-modal show="showEditModal" maxWidth="max-w-md" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Edit Data Barang</h2>
                <button @click="showEditModal = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>
        
        <form wire:submit.prevent="update" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Barang</label>
                <input type="text" wire:model="namaBarang"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                @error('namaBarang') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Satuan</label>
                <select wire:model="satuanId"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
                    <option value="">Pilih Satuan</option>
                    @foreach($this->satuans as $satuan)
                        <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                    @endforeach
                </select>
                @error('satuanId') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
            </div>
            
            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stok Saat Ini</label>
                    <input type="number" wire:model="stokSaatIni" min="0"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                    @error('stokSaatIni') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Stok Minimum</label>
                    <input type="number" wire:model="stokMinimum" min="0"
                        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                    @error('stokMinimum') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Foto Barang (Opsional)</label>
                @php
                    $currentItem = $editingId ? \App\Models\BonItem::find($editingId) : null;
                    $existingPhoto = $currentItem && $currentItem->image_path ? asset('storage/' . $currentItem->image_path) : null;
                    $hasPhoto = $editPhoto || $existingPhoto;
                    $photoSrc = $editPhoto ? $editPhoto->temporaryUrl() : $existingPhoto;
                @endphp

                <div class="relative w-full aspect-video rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 overflow-hidden cursor-pointer hover:border-brand-400 transition-all group"
                    onclick="document.getElementById('editFileInput').click()">
                    @if($hasPhoto)
                        <img src="{{ $photoSrc }}" class="w-full h-full object-contain absolute inset-0">
                    @endif
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-1.5 {{ $hasPhoto ? 'opacity-0 hover:opacity-100 bg-slate-900/50 text-white transition-opacity' : 'text-slate-400' }}">
                        <span class="material-symbols-outlined text-[28px]">add_photo_alternate</span>
                        <span class="text-xs font-bold">{{ $hasPhoto ? 'Ganti Foto' : 'Klik untuk upload foto' }}</span>
                    </div>
                </div>
                <input type="file" wire:model="editPhoto" id="editFileInput" accept="image/*" class="hidden">
                <div wire:loading wire:target="editPhoto" class="text-[11px] text-brand-600 mt-1 font-medium">Mengunggah foto...</div>
                @error('editPhoto') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button @click="showEditModal = false" type="button"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="update"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Tambah Stok -->
    <x-modal show="showTambahStokModal" maxWidth="max-w-sm" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Tambah Stok Masuk</h2>
                <button @click="showTambahStokModal = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>
        
        <form wire:submit.prevent="tambahStok" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jumlah Masuk <span class="text-rose-500">*</span></label>
                <input type="number" wire:model="jumlahTambah" min="1"
                    class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                    placeholder="Masukkan kuantitas">
                @error('jumlahTambah') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button @click="showTambahStokModal = false" type="button"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="tambahStok"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer">
                    Tambah Stok
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Kelola Satuan -->
    <x-modal show="showSatuanModal" maxWidth="max-w-md" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Kelola Master Satuan</h2>
                <button @click="showSatuanModal = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>
        
        <div class="space-y-4 text-xs">
            @if(session('satuan_error'))
                <div role="alert" class="rounded-xl bg-rose-50/90 p-3 border border-rose-200/80 text-rose-800 text-xs flex items-center gap-2.5 font-medium shadow-xs">
                    <div class="w-5 h-5 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <span>{{ session('satuan_error') }}</span>
                </div>
            @endif

            {{-- Form Tambah / Edit --}}
            <div class="bg-slate-50/70 border border-slate-200 rounded-2xl p-3.5">
                <form wire:submit.prevent="saveSatuan" class="flex items-end gap-2.5">
                    <div class="flex-1">
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            {{ $editingSatuanId ? 'Edit Nama Satuan' : 'Tambah Satuan Baru' }}
                        </label>
                        <input type="text" wire:model="namaSatuan" placeholder="Contoh: PCS, BOX, RIM..."
                            class="w-full px-3 py-2 bg-white rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:border-brand-500 outline-none transition">
                        @error('namaSatuan') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-1.5">
                        @if($editingSatuanId)
                            <button type="button" wire:click="cancelEditSatuan"
                                class="px-3 py-2 border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 rounded-xl font-bold text-xs transition cursor-pointer">
                                Batal
                            </button>
                        @endif
                        <button type="submit"
                            class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold text-xs shadow-2xs transition cursor-pointer">
                            {{ $editingSatuanId ? 'Simpan' : 'Tambah' }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Daftar Satuan Table --}}
            <div class="border border-slate-200 rounded-2xl overflow-hidden max-h-56 overflow-y-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider sticky top-0">
                        <tr>
                            <th class="px-4 py-2.5">Nama Satuan</th>
                            <th class="px-4 py-2.5 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($this->satuans as $satuan)
                            <tr class="hover:bg-slate-50/60 transition-colors" wire:key="satuan-{{ $satuan->id }}">
                                <td class="px-4 py-2.5 font-bold text-slate-800">
                                    {{ $satuan->nama_satuan }}
                                </td>
                                <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-0.5">
                                        <button type="button" wire:click="editSatuan({{ $satuan->id }})"
                                            class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition cursor-pointer shrink-0" title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button type="button" wire:click="confirmHapusSatuan({{ $satuan->id }})"
                                            class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-slate-400">
                                    Belum ada satuan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Konfirmasi Hapus Satuan --}}
            @if($showHapusSatuanConfirm)
                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-3.5 text-xs">
                    <p class="text-rose-800 font-medium mb-2.5">Yakin ingin menghapus satuan ini? Satuan yang sedang digunakan oleh barang tidak dapat dihapus.</p>
                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="$set('showHapusSatuanConfirm', false)"
                            class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold">
                            Batal
                        </button>
                        <button type="button" wire:click="hapusSatuan"
                            class="px-3.5 py-1.5 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 transition">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <x-slot:footer>
            <div class="flex justify-end w-full">
                <button type="button" @click="showSatuanModal = false"
                    class="px-5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Import Data Barang -->
    <x-modal show="showImportModal" maxWidth="max-w-md" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Import Data Barang</h2>
                <button @click="showImportModal = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>
        
        <form wire:submit.prevent="importMasterBarang" class="space-y-4 text-xs">
            <div class="p-3.5 bg-blue-50/80 rounded-2xl border border-blue-200 text-blue-800 leading-relaxed">
                <p class="font-bold mb-1">Format Kolom Excel / CSV:</p>
                <ul class="list-disc ml-4 space-y-0.5 opacity-90 text-[11px]">
                    <li><strong>Kode Barang</strong> (Opsional: auto-generate jika kosong)</li>
                    <li><strong>Nama Barang</strong> (Wajib)</li>
                    <li><strong>Satuan</strong> (Opsional: default PCS)</li>
                    <li><strong>Stok Saat Ini</strong> & <strong>Stok Minimum</strong></li>
                </ul>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">File Excel / CSV <span class="text-rose-500">*</span></label>
                <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv"
                    class="w-full px-3 py-2 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                <div wire:loading wire:target="importFile" class="text-[11px] text-brand-600 mt-1 font-medium">Mengunggah file...</div>
                @error('importFile') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button @click="showImportModal = false" type="button"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="importMasterBarang"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-600/20 transition text-xs cursor-pointer"
                    wire:loading.attr="disabled" wire:target="importFile, importMasterBarang">
                    <span wire:loading.remove wire:target="importFile, importMasterBarang">Mulai Import</span>
                    <span wire:loading wire:target="importFile, importMasterBarang">Memproses...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
