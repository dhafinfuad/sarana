{{--
    Kartu KPI "Top ..." gaya /admin/dashboard. $item: objek dengan ->name dan ->total (atau null).
    Slot = ikon (svg). $accent: kelas warna kotak ikon. $chip: teks chip mono (opsional). $unit: satuan di belakang jumlah.
--}}
@props(['label', 'item' => null, 'unit' => 'kali', 'chip' => null, 'accent' => 'bg-blue-50 text-blue-600 border-blue-100', 'small' => false])

<div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card flex items-start justify-between">
    <div class="min-w-0 flex-1 pr-2">
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
        @if($item)
            <h4 class="{{ $small ? 'text-sm line-clamp-1' : 'text-base truncate' }} font-bold text-slate-900 mt-2 leading-snug">{{ $item->name }}</h4>
            @if($chip)
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-md bg-brand-50 text-brand-700 border border-brand-100 font-mono">{{ $chip }}</span>
                    <span class="text-xs text-slate-500 font-medium">&bull; {{ $item->total }} {{ $unit }}</span>
                </div>
            @else
                <p class="text-xs text-slate-500 font-medium mt-2">{{ $item->total }} {{ $unit }}</p>
            @endif
        @else
            <p class="text-xs text-slate-400 font-medium mt-2">Belum ada data</p>
        @endif
    </div>
    <div class="w-12 h-12 rounded-2xl {{ $accent }} flex items-center justify-center shrink-0 border mt-1">
        {{ $slot }}
    </div>
</div>
