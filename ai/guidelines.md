# Panduan Koding (Coding Guidelines) & Arsitektur
**Aturan Absolut untuk Agent AI Fullstack:** Ini adalah aturan hukum (*ground rules*) dalam menyusun arsitektur proyek Laravel ini. Jangan melanggar arsitektur yang sudah ditetapkan.

## 1. Arsitektur Komponen (Blade & Livewire)
* **Modularitas:** Pecah UI yang berulang menjadi **Blade Components** anonim (contoh: `<x-button>`, `<x-input>`, `<x-card>`).
* **Pemisahan Logika (Separation of Concerns):** * Gunakan **Livewire** (`@skills-livewire.md`) HANYA untuk manipulasi data yang membutuhkan komunikasi ke *database* (Backend).
    * Gunakan **Alpine.js** (`@skills-alpine.md`) HANYA untuk manipulasi DOM lokal yang tidak membutuhkan server (contoh: buka-tutup *dropdown*, *modal*, atau *tabs*).

## 2. Struktur Database & Eloquent Model
* **Penamaan Relasi:** Selalu definisikan relasi Eloquent secara eksplisit dan gunakan standar penamaan CamelCase untuk metode relasinya (misal: `public function user()`).
* **Tipe Data Kuat (Strict Typing):** Deklarasikan tipe data pada parameter fungsi dan nilai kembalian (*return type*). Contoh: `public function calculateTotal(int $id): bool`.
* **Eager Loading:** Saat menarik data untuk tabel atau daftar, WAJIB menggunakan `with()` untuk mencegah *N+1 Query Problem*.

## 3. Fondasi PWA (Progressive Web App)
Saat menginisiasi kerangka aplikasi, pastikan 3 komponen utama PWA ini langsung terintegrasi di dalam *layout* utama (`app.blade.php`):
1.  **Manifest:** `<link rel="manifest" href="/manifest.json">` (Pastikan property `display: "standalone"` sudah diatur).
2.  **Meta Theme Color:** `<meta name="theme-color" content="#f8fafc">` (Warna menyesuaikan desain sistem).
3.  **Service Worker Registration:** Sisipkan script JS sederhana di sebelum tag penutup `</body>` untuk meregistrasi `/sw.js` agar aplikasi mendukung *caching* dasar dan dapat diinstal.

## 4. Keamanan & Performa
* **Kredensial Lingkungan:** Jangan pernah melakukan *hardcode* untuk konfigurasi, *password*, atau *API Key* di dalam file PHP. Selalu panggil melalui `config()` atau `env()`.
* **Proteksi Form:** Formulir Livewire sudah mengamankan CSRF, namun pastikan validasi sisi *backend* (`$rules`) selalu diterapkan seketat mungkin sebelum melakukan `Model::create()` atau `Model::update()`.
* **Soft Deletes:** Untuk data krusial, hindari penghapusan fisik. Gunakan *state toggling* (contoh: status 'Active'/'Maintenance') atau `SoftDeletes` bawaan Laravel.