{{-- Kartu KPI "Total" gaya /admin/dashboard. Slot = ikon (svg). --}}
@props(['label', 'value', 'hint' => 'Sesuai filter tabel'])

<div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-card flex items-center justify-between">
    <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ number_format($value) }}</p>
        <p class="text-[11px] text-slate-400 mt-1">{{ $hint }}</p>
    </div>
    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 border border-brand-100">
        {{ $slot }}
    </div>
</div>
