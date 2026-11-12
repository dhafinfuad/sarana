{{-- Bilah filter halaman "Semua Riwayat": pencarian, status, rentang tanggal. Terikat ke komponen Livewire induk. --}}
@props(['placeholder' => 'Cari...', 'statuses' => []])

<div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pencarian</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ $placeholder }}"
                    class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Status</label>
            <select wire:model.live="status"
                class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
                <option value="">Semua Status</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
            <input type="date" wire:model.live="startDate"
                class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
            <input type="date" wire:model.live="endDate"
                class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all">
        </div>
    </div>
</div>
