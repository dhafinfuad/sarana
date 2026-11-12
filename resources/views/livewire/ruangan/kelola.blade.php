<div x-data="{
    showAddModal: false,
    showEditModal: false,
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
        if (this.confirmAction === 'activate' || this.confirmAction === 'deactivate') {
            $wire.toggleStatus(this.confirmId);
        }
        this.showConfirmModal = false;
    }
}" @close-room-form-modal.window="showAddModal = false"
  @open-room-edit-modal.window="showEditModal = true"
  @close-room-edit-modal.window="showEditModal = false">

    @if(!auth()->user()->canAccessAdminDashboard())
        <x-top-header title="Kelola Ruangan" />
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'py-6 pb-24 md:pb-8 max-w-7xl mx-auto flex-grow w-full px-4 sm:px-6 lg:px-8' }}">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Kelola Ruangan</h1>
                <p class="text-slate-500 text-sm mt-1.5">Tambah, ubah, dan aktifkan atau nonaktifkan ruangan yang dapat dipinjam pegawai.</p>
            </div>
            <div class="flex items-center gap-2.5 self-start sm:self-auto">
                <button type="button" @click="$wire.resetForm(); showAddModal = true"
                    class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Ruangan</span>
                </button>
            </div>
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

        <!-- Search -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card mb-8">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pencarian</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, lokasi, atau fasilitas..."
                    class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-6 whitespace-nowrap">Ruangan</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Kapasitas</th>
                            <th class="py-3.5 px-4">Fasilitas</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Pengajuan Aktif</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status</th>
                            <th class="py-3.5 px-6 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->rooms as $room)
                            <tr class="hover:bg-slate-50/60 transition-colors" wire:key="room-{{ $room->id }}">
                                <td class="py-4 px-6">
                                    <p class="font-bold text-slate-900">{{ $room->nama }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $room->lokasi ?: '-' }}</p>
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-slate-700">{{ $room->kapasitas }}</td>
                                <td class="py-4 px-4 text-slate-600">{{ $room->fasilitas ?: '-' }}</td>
                                <td class="py-4 px-4 text-center text-slate-600 font-medium">{{ $room->aktif_count }}</td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($room->status === 'tersedia')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200">Tersedia</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border bg-slate-100 text-slate-600 border-slate-200">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-0.5">
                                        <button type="button" wire:click="edit({{ $room->id }})"
                                            class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-200 transition cursor-pointer shrink-0"
                                            title="Edit Ruangan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        @if($room->status === 'tersedia')
                                            <button type="button"
                                                @click="openConfirm('deactivate', {{ $room->id }}, 'Nonaktifkan Ruangan', 'Ruangan tidak akan muncul lagi di form peminjaman. Pengajuan yang sudah ada tidak berubah. Lanjutkan?')"
                                                class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0"
                                                title="Nonaktifkan Ruangan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                @click="openConfirm('activate', {{ $room->id }}, 'Aktifkan Ruangan', 'Ruangan akan muncul kembali di form peminjaman. Lanjutkan?')"
                                                class="p-1 text-emerald-600 hover:bg-emerald-50 rounded-lg border border-transparent hover:border-emerald-200 transition cursor-pointer shrink-0"
                                                title="Aktifkan Ruangan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-[48px] text-slate-300 mb-2 block">meeting_room</span>
                                    <p class="text-slate-500 font-semibold text-sm">Belum ada ruangan. Klik "Tambah Ruangan" untuk memulai.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->rooms->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $this->rooms->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah Ruangan -->
    <x-modal show="showAddModal" maxWidth="max-w-lg">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Tambah Ruangan</h2>
                <button type="button" @click="showAddModal = false; $wire.resetForm()" aria-label="Tutup"
                    class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <form id="form-tambah-ruangan" wire:submit.prevent="save" class="space-y-4 text-xs">
            @include('livewire.ruangan.partials.room-fields')
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showAddModal = false; $wire.resetForm()"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" form="form-tambah-ruangan"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer">
                    <span wire:loading.remove wire:target="save">Simpan</span>
                    <span wire:loading wire:target="save">Memproses...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Edit Ruangan -->
    <x-modal show="showEditModal" maxWidth="max-w-lg">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Edit Ruangan</h2>
                <button type="button" @click="showEditModal = false; $wire.resetForm()" aria-label="Tutup"
                    class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <form id="form-edit-ruangan" wire:submit.prevent="update" class="space-y-4 text-xs">
            @include('livewire.ruangan.partials.room-fields')
        </form>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showEditModal = false; $wire.resetForm()"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" form="form-edit-ruangan"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer">
                    <span wire:loading.remove wire:target="update">Simpan Perubahan</span>
                    <span wire:loading wire:target="update">Memproses...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    <x-ruangan.confirm-modal />
</div>
