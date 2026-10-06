{{--
Livewire Component View: Admin/Fleet
Redesain Modern sesuai sarana_redesain.html
--}}
<div x-data="{
    showAddModal: @entangle('showAddModal'),
    showEditModal: @entangle('showEditModal'),
    showDeleteModal: @entangle('showDeleteModal'),
    addPreview: null,
    editPreview: null,

    handleAddImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => { this.addPreview = e.target.result; };
            reader.readAsDataURL(file);
        }
    },
    handleEditImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => { this.editPreview = e.target.result; };
            reader.readAsDataURL(file);
        }
    }
}"
    x-init="$watch('showAddModal', value => { if(!value) addPreview = null; }); $watch('showEditModal', value => { if(!value) editPreview = null; });">

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Manajemen Armada</h1>
            <p class="text-slate-500 text-sm mt-1.5">Kelola status ketersediaan, perbaikan, dan inventaris kendaraan dinas operasional.</p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            {{-- Search Bar --}}
            <div class="relative flex-1 sm:w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari mobil atau plat nomor..."
                    class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-xs font-medium rounded-xl border border-slate-200 focus:border-brand-600 outline-none transition" />
            </div>

            {{-- Tambah Mobil Button --}}
            @if(auth()->user()->canManageArmada())
            <button x-on:click="showAddModal = true; $wire.openAdd()"
                class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 flex items-center gap-2 shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Mobil</span>
            </button>
            @endif
        </div>
    </div>

    {{-- Notification Messages --}}
    @if (session()->has('success'))
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
    @if (session()->has('error'))
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

    {{-- 4 Stat Cards for Fleet --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Armada</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $stats['total'] }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Kendaraan terdaftar</p>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tersedia</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $stats['tersedia'] }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Siap operasional</p>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Maintenance</p>
            <p class="text-3xl font-extrabold text-amber-600 mt-2">{{ $stats['maintenance'] }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Dalam perbaikan</p>
        </div>
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Utilisasi</p>
            <p class="text-3xl font-extrabold text-brand-600 mt-2">{{ $stats['pct'] }}%</p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-brand-600 h-full rounded-full transition-all duration-500" style="width: {{ $stats['pct'] }}%"></div>
            </div>
        </div>
    </div>

    {{-- Fleet Grid Cards --}}
    @if(count($vehicles) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($vehicles as $vehicle)
                @php $isActive = $vehicle->isTersedia(); @endphp
                <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card flex flex-col justify-between hover:shadow-lg transition group">
                    <div>
                        {{-- Image / Preview Container --}}
                        <div class="w-full h-36 bg-gradient-to-tr from-slate-100 to-slate-50 rounded-2xl flex items-center justify-center p-3 mb-4 relative overflow-hidden border border-slate-100">
                            {{-- Status badge top-left --}}
                            @if($isActive)
                                <span class="absolute top-2.5 left-2.5 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs z-10">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                </span>
                            @else
                                <span class="absolute top-2.5 left-2.5 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs z-10">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Maintenance
                                </span>
                            @endif

                            {{-- Context Menu (Edit/Delete) top-right --}}
                            @if(auth()->user()->canManageArmada())
                            <div class="absolute top-2.5 right-2.5 z-10" x-data="{ open: false }">
                                <button @click="open = !open" @click.outside="open = false"
                                    class="text-slate-600 hover:text-slate-900 bg-white/90 hover:bg-white backdrop-blur-xs border border-slate-200 shadow-2xs transition w-7 h-7 flex items-center justify-center rounded-xl cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>
                                <div x-show="open" style="display:none;" x-transition.opacity
                                    class="absolute right-0 top-8 z-20 w-32 bg-white border border-slate-100 rounded-2xl shadow-xl py-1 text-xs font-semibold">
                                    <button @click="showEditModal = true; open = false; $wire.openEdit({{ $vehicle->id }})"
                                        class="w-full flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-600 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span>Edit</span>
                                    </button>
                                    <button @click="showDeleteModal = true; open = false; $wire.confirmDelete({{ $vehicle->id }})"
                                        class="w-full flex items-center gap-2 px-3 py-2 text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                            @endif

                            @if($vehicle->imageUrl())
                                <img src="{{ $vehicle->imageUrl() }}" alt="{{ $vehicle->nama_kendaraan }}"
                                    class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-300">
                            @else
                                <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                </svg>
                            @endif
                        </div>

                        {{-- Vehicle Title & Plat --}}
                        <h4 class="font-bold text-slate-800 text-sm leading-snug">{{ $vehicle->nama_kendaraan }}</h4>
                        <div class="mt-2">
                            <span class="inline-flex px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                {{ $vehicle->plat_nomor }}
                            </span>
                        </div>
                    </div>

                    {{-- Footer with Status & Toggle Switch --}}
                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span @class([
                            'font-bold',
                            $isActive ? 'text-emerald-600' : 'text-amber-600'
                        ])>{{ $isActive ? 'Aktif (Tersedia)' : 'Maintenance' }}</span>

                        @if(auth()->user()->canManageArmada())
                        <button type="button" wire:click="toggleStatus({{ $vehicle->id }})"
                            @class([
                                'w-9 h-5 rounded-full p-0.5 transition flex items-center cursor-pointer',
                                $isActive ? 'bg-emerald-500' : 'bg-slate-300'
                            ])
                            title="Klik untuk mengubah status">
                            <div @class([
                                'w-4 h-4 bg-white rounded-full shadow-md transform transition',
                                $isActive ? 'translate-x-4' : 'translate-x-0'
                            ])></div>
                        </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($vehicles->hasPages())
            <div class="mt-6 p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
                {{ $vehicles->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-3xl p-16 flex flex-col items-center justify-center text-center border border-slate-200/90 shadow-card">
            <svg class="w-16 h-16 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="font-bold text-slate-800 text-base mb-1">Tidak ada kendaraan ditemukan</p>
            <p class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau daftarkan armada baru.</p>
        </div>
    @endif

    {{-- MODAL: Tambah Kendaraan --}}
    <x-modal show="showAddModal" maxWidth="max-w-lg">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h3 class="text-base font-bold text-slate-900">Tambah Kendaraan</h3>
                <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none cursor-pointer">&times;</button>
            </div>
        </x-slot:header>

        <form wire:submit.prevent="save" id="form-add-vehicle" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Nama Kendaraan <span class="text-rose-500">*</span></label>
                <input wire:model="nama_kendaraan" type="text" required placeholder="Contoh: Toyota Kijang Innova Zenix"
                    class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 focus:bg-white text-xs transition" />
                @error('nama_kendaraan') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Plat Nomor <span class="text-rose-500">*</span></label>
                <input wire:model="plat_nomor" type="text" required placeholder="Contoh: N 1234 XY"
                    class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 focus:bg-white text-xs transition font-mono uppercase" />
                @error('plat_nomor') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Status Awal</label>
                <select wire:model="status" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
                    <option value="tersedia">Aktif (Tersedia)</option>
                    <option value="tidak_tersedia">Dalam Perbaikan (Maintenance)</option>
                </select>
                @error('status') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Foto Kendaraan (Opsional)</label>
                <div class="border-2 border-dashed border-slate-200 hover:border-brand-500 rounded-2xl p-5 text-center bg-slate-50/50 cursor-pointer relative overflow-hidden"
                    @click="$refs.addFileInput.click()">
                    <img x-show="addPreview" :src="addPreview" style="display:none;" class="w-full h-32 object-cover rounded-xl mb-2" />
                    <div x-show="!addPreview">
                        <svg class="w-8 h-8 mx-auto text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-[11px] text-slate-500 font-medium">Klik untuk upload foto armada (JPG, PNG maks 2MB)</span>
                    </div>
                </div>
                <input type="file" wire:model="photo" accept="image/*" x-ref="addFileInput" class="hidden" @change="handleAddImage($event)">
                @error('photo') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2.5">
                <button type="button" @click="showAddModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 transition cursor-pointer flex items-center gap-1.5">
                    <span>Simpan Kendaraan</span>
                    <span wire:loading wire:target="save" class="material-symbols-outlined animate-spin text-[14px]">refresh</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL: Edit Kendaraan --}}
    <x-modal show="showEditModal" maxWidth="max-w-lg">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Edit Kendaraan</h3>
                    <span class="text-[11px] text-brand-600 font-mono font-bold">{{ $plat_nomor }}</span>
                </div>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none cursor-pointer">&times;</button>
            </div>
        </x-slot:header>

        <form wire:submit.prevent="update" id="form-edit-vehicle" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Nama Kendaraan <span class="text-rose-500">*</span></label>
                <input wire:model="nama_kendaraan" type="text" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs" />
                @error('nama_kendaraan') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Plat Nomor <span class="text-rose-500">*</span></label>
                <input wire:model="plat_nomor" type="text" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs font-mono uppercase" />
                @error('plat_nomor') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Status Operasional</label>
                <select wire:model="status" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
                    <option value="tersedia">Aktif (Tersedia)</option>
                    <option value="tidak_tersedia">Dalam Perbaikan (Maintenance)</option>
                </select>
                @error('status') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">Foto Kendaraan</label>
                <div class="border-2 border-dashed border-slate-200 hover:border-brand-500 rounded-2xl p-5 text-center bg-slate-50/50 cursor-pointer relative overflow-hidden"
                    @click="$refs.editFileInput.click()">
                    @php
                        $existingVehicle = $editingId ? collect($vehicles->items())->firstWhere('id', $editingId) : null;
                    @endphp
                    <img x-show="editPreview" :src="editPreview" style="display:none;" class="w-full h-32 object-cover rounded-xl mb-2" />
                    @if($existingVehicle && $existingVehicle->imageUrl())
                        <img x-show="!editPreview" src="{{ $existingVehicle->imageUrl() }}" class="w-full h-32 object-cover rounded-xl mb-2" />
                    @endif
                    <p class="text-[11px] text-slate-500 font-medium">Klik untuk mengubah foto armada</p>
                </div>
                <input type="file" wire:model="editPhoto" accept="image/*" x-ref="editFileInput" class="hidden" @change="handleEditImage($event)">
                @error('editPhoto') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2.5">
                <button type="button" @click="showEditModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" wire:loading.attr="disabled" wire:target="update, editPhoto"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 transition cursor-pointer flex items-center gap-1.5">
                    <span>Simpan Perubahan</span>
                    <span wire:loading wire:target="update" class="material-symbols-outlined animate-spin text-[14px]">refresh</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL: Konfirmasi Hapus Kendaraan --}}
    <x-modal show="showDeleteModal" maxWidth="max-w-sm">
        <div class="text-center p-2">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-3 border border-rose-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Konfirmasi Hapus Kendaraan</h3>
            <p class="text-xs text-slate-500 mt-1 mb-5">Apakah Anda yakin ingin menghapus data kendaraan ini secara permanen?</p>
            <div class="flex justify-center gap-2.5">
                <button @click="showDeleteModal = false" type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button wire:click="delete" type="button"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/25 transition cursor-pointer">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </x-modal>

</div>