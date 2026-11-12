{{-- Field form peminjaman ruangan (modal Ajukan dan Edit). Param: $roomOptions --}}
<div>
    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
        Ruangan <span class="text-rose-500">*</span>
    </label>
    <select wire:model="roomId"
        class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
        <option value="">Pilih Ruangan</option>
        @foreach($roomOptions as $room)
            <option value="{{ $room->id }}">{{ $room->nama }} (kapasitas {{ $room->kapasitas }}){{ $room->status === 'tersedia' ? '' : ' - nonaktif' }}</option>
        @endforeach
    </select>
    @error('roomId') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
</div>

<div>
    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
        Keperluan <span class="text-rose-500">*</span>
    </label>
    <input type="text" wire:model="keperluan" placeholder="Contoh: Rapat koordinasi bulanan"
        class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
    @error('keperluan') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
</div>

<div>
    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
        Jumlah Peserta <span class="text-rose-500">*</span>
    </label>
    <input type="number" min="1" wire:model="jumlahPeserta"
        class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
    @error('jumlahPeserta') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
    <div>
        <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
            Mulai <span class="text-rose-500">*</span>
        </label>
        <input type="datetime-local" wire:model="tanggalMulai"
            class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
        @error('tanggalMulai') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
    </div>
    <div>
        <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5">
            Selesai <span class="text-rose-500">*</span>
        </label>
        <input type="datetime-local" wire:model="tanggalSelesai"
            class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs">
        @error('tanggalSelesai') <span class="text-rose-600 text-[11px] mt-1 block font-medium">{{ $message }}</span> @enderror
    </div>
</div>
