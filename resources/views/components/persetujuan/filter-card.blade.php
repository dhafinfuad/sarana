{{--
    Kartu filter "Manajemen ..." gaya /admin/dashboard. Terikat ke komponen Livewire induk:
    search, status, startDate, endDate (+ opsional filterSeksi). $reset = ekspresi wire:click untuk Reset Filter.
--}}
@props(['title', 'pending' => 0, 'statuses' => [], 'placeholder' => 'Cari...', 'reset', 'seksi' => []])

<div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card mb-6">
    <div class="flex items-center justify-between mb-3.5 pb-2 border-b border-slate-100">
        <div class="flex items-center gap-2.5">
            <h3 class="text-sm font-bold text-slate-900">{{ $title }}</h3>
            @if($pending > 0)
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    {{ $pending }} Menunggu
                </span>
            @endif
        </div>
        <button type="button" wire:click="{{ $reset }}"
            class="text-xs font-semibold text-brand-600 hover:text-brand-700 hover:underline cursor-pointer">
            Reset Filter
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
        <div class="{{ count($seksi) > 1 ? 'sm:col-span-3' : 'sm:col-span-4' }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ $placeholder }}"
                    class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-xs font-medium rounded-xl border border-slate-200 focus:border-brand-600 outline-none transition" />
            </div>
        </div>
        @if(count($seksi) > 1)
            <div class="sm:col-span-2">
                <select wire:model.live="filterSeksi" class="w-full px-3.5 py-2.5 bg-slate-50 text-xs font-medium rounded-xl border border-slate-200 focus:border-brand-600 outline-none transition">
                    <option value="">Semua Seksi ({{ count($seksi) }})</option>
                    @foreach($seksi as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="sm:col-span-3">
            <select wire:model.live="status" class="w-full px-3.5 py-2.5 bg-slate-50 text-xs font-medium rounded-xl border border-slate-200 focus:border-brand-600 outline-none transition">
                <option value="">Semua Status</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="{{ count($seksi) > 1 ? 'sm:col-span-4' : 'sm:col-span-5' }} grid grid-cols-2 gap-2.5">
            <input type="date" wire:model.live="startDate" class="w-full px-3 py-2 bg-slate-50 text-xs font-medium rounded-xl border border-slate-200 outline-none text-slate-600" />
            <input type="date" wire:model.live="endDate" class="w-full px-3 py-2 bg-slate-50 text-xs font-medium rounded-xl border border-slate-200 outline-none text-slate-600" />
        </div>
    </div>
</div>
