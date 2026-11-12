{{-- Tombol aksi ikon gaya /admin/dashboard. $type: approve | reject | cancel | edit | delete. Atribut (@click, wire:click) diteruskan. --}}
@props(['type', 'disabled' => false, 'title' => null])

@php
    $styles = [
        'approve' => ['text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200', 'M5 13l4 4L19 7', '2.5', 'Setujui'],
        'reject'  => ['text-rose-600 hover:bg-rose-50 hover:border-rose-200', 'M6 18L18 6M6 6l12 12', '2.5', 'Tolak'],
        'cancel'  => ['text-rose-600 hover:bg-rose-50 hover:border-rose-200', 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636', '2.5', 'Batalkan'],
        'edit'    => ['text-amber-600 hover:bg-amber-50 hover:border-amber-200', 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', '2', 'Edit Data'],
        'delete'  => ['text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200', 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16', '2', 'Hapus Data'],
    ];
    [$tone, $path, $stroke, $defaultTitle] = $styles[$type];
@endphp

<button type="button" @disabled($disabled) {{ $attributes->merge(['title' => $title ?? $defaultTitle]) }}
    @class(['p-1 rounded-lg border border-transparent transition shrink-0', $disabled ? 'text-slate-300 cursor-not-allowed' : $tone . ' cursor-pointer'])>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $stroke }}" d="{{ $path }}"></path></svg>
</button>
