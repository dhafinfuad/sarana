{{-- Kartu ajakan (CTA) kolom kiri, sama dengan halaman /peminjaman. Atribut (mis. @click) diteruskan ke tombol. --}}
@props(['title', 'description', 'label' => 'Mulai Pengajuan Baru'])

<div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-card">
    <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>
    <p class="text-slate-500 text-xs mt-2 leading-relaxed">{{ $description }}</p>
    <button type="button" {{ $attributes }}
        class="w-full mt-5 py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 flex items-center justify-center gap-2 transition cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
        <span>{{ $label }}</span>
    </button>
</div>
