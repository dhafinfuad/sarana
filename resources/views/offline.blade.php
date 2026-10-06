<x-layouts.app title="Tidak Ada Koneksi">
    {{-- Halaman offline: ditampilkan Service Worker saat user mengakses halaman yang belum di-cache --}}
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="text-center max-w-sm mx-auto">

            {{-- Ilustrasi SVG offline --}}
            <div class="mx-auto mb-8 w-24 h-24 rounded-full bg-[#e8eef9] flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#004875]"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                     aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3l18 18M8.111 8.111A6.003 6.003 0 0012 7c3.314 0 6 2.686 6 6a5.97 5.97 0 01-.72 2.853M6.343 6.343A8 8 0 004 12c0 4.418 3.582 8 8 8a7.969 7.969 0 004.9-1.676M12 3v1m0 17v1M3 12h1m17 0h1" />
                </svg>
            </div>

            {{-- Teks --}}
            <h1 class="text-2xl font-bold text-[#151c23] tracking-tight mb-3">
                Tidak Ada Koneksi
            </h1>
            <p class="text-[#414750] text-sm leading-relaxed mb-8">
                Anda sedang offline. Periksa koneksi internet Anda, lalu coba lagi.
            </p>

            {{-- Tombol coba lagi --}}
            <button
                id="btn-retry"
                onclick="window.location.reload()"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#004875] text-white font-semibold text-sm
                       hover:bg-[#00609a] active:scale-95 transition-all duration-150"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 4v5h.582M20 20v-5h-.581M5.635 15A9 9 0 1018.364 9" />
                </svg>
                Coba Lagi
            </button>

        </div>
    </div>
</x-layouts.app>
