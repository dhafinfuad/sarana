{{-- Field form ruangan (dipakai modal Tambah dan Edit di Ruangan\Kelola) --}}
<div>
    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
        Nama Ruangan <span class="text-rose-500">*</span>
    </label>
    <input type="text" wire:model="nama" placeholder="Contoh: Ruang Rapat Utama"
        class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
    @error('nama') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
    <div>
        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Lokasi</label>
        <input type="text" wire:model="lokasi" placeholder="Contoh: Lantai 2"
            class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
        @error('lokasi') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Kapasitas <span class="text-rose-500">*</span>
        </label>
        <input type="number" min="1" wire:model="kapasitas" placeholder="Jumlah orang"
            class="w-full px-3.5 py-2.5 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition">
        @error('kapasitas') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
    </div>
</div>

<div>
    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fasilitas</label>
    <textarea wire:model="fasilitas" rows="3" placeholder="Contoh: Proyektor, AC, Whiteboard"
        class="w-full p-3 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition resize-none"></textarea>
    @error('fasilitas') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
</div>
