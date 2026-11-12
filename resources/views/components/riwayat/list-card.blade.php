{{-- Kartu "Riwayat" kolom kanan, sama dengan halaman /peminjaman. Slot default = baris daftar; slot footer = pagination/tautan. --}}
@props(['title', 'subtitle'])

<div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>
            <p class="text-xs text-slate-400 mt-0.5">{{ $subtitle }}</p>
        </div>
        <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>

    <div class="divide-y divide-slate-100 text-xs" wire:poll.10s>
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-center">
            {{ $footer }}
        </div>
    @endisset
</div>
