# Flow Brief: Sistem Peminjaman Kendaraan Dinas (SARANA)

Dokumen ini memuat instruksi teknis absolut untuk 5 (lima) Alur Kerja Utama. Eksekusi setiap unit kerja harus mematuhi standar desain `@design-system.md` dan panduan struktur PWA di `@guidelines.md`.

---

## FLOW 1: Otentikasi & Otorisasi Multi-Role
**Fokus:** Keamanan sesi, validasi *real-time*, dan pemisahan *routing* absolut berbasis peran.

**1. Spesifikasi Teknis & Logika:**
* **Kredensial Login:** Menggunakan `nip_pendek` (String) dan `password` (Hashed).
* **Validasi UI:** Pengecekan *field* kosong dan otentikasi gagal harus ditangani tanpa *page reload* (SPA-feel).
* **State Management Role:** Setelah login divalidasi, baca kolom `role` pada tabel `users`.
* **Redirection Matrix:**
    * `role == 'Administrator'` ➔ Arahkan ke `/admin/dashboard`.
    * `role == 'Kepala Kantor'` ➔ Arahkan ke `/admin/dashboard`.
    * `role == 'Pegawai'` ➔ Arahkan ke `/peminjaman`.
* **Middleware Protection:**
    * Buat *middleware* `RoleMiddleware`. 
    * Route Group `/admin/*` di-lock hanya untuk array role `['Administrator', 'Kepala Kantor']`.
    * Jika `Pegawai` memaksa akses `/admin/*`, lemparkan `abort(403)` atau paksa *redirect* ke `/peminjaman`.

**2. Kebutuhan Unit & AI Dependencies:**
* **Wajib panggil:** `@skills-livewire.md` (untuk form state, pengikatan variabel `wire:model`, dan eksekusi login `wire:submit.prevent`).
* **Wajib panggil:** `@skills-alpine.md` (untuk manipulasi DOM lokal seperti fitur *toggle Show/Hide Password*).

---

## FLOW 2: Pengajuan Peminjaman Kendaraan oleh Pegawai
**Fokus:** *Mobile-first approach*, algoritma presisi untuk validasi irisan tanggal (Date Overlapping), dan dependensi *dropdown*.

**1. Spesifikasi Teknis & Logika:**
* **Layout Constraint:** Bungkus dalam `max-w-md mx-auto min-h-screen shadow-xl pb-safe`.
* **Field Wajib:** `tujuan` (Radio: Dalam Kota / Luar Kota), `keperluan` (Textarea), `mulai` (Date), `selesai` (Date), `plat_mobil` (Dropdown).
* **Aturan Validasi Tanggal:** `mulai` tidak boleh kurang dari `today()`. `selesai` tidak boleh kurang dari `mulai`.
* **Algoritma Cek Ketersediaan (CRITICAL):**
    * *Dropdown* `plat_mobil` dalam kondisi *disabled* hingga pengguna memilih tanggal `mulai` dan `selesai`.
    * Saat tanggal terisi, trigger *Lifecycle Hook* Livewire untuk memuat mobil.
    * **Logika Filter:** Ambil seluruh mobil dari tabel `vehicles` di mana `status_aktif = 'Active'`, KECUALI mobil yang ID/Plat-nya ada di tabel `bookings` dengan kondisi:
        1. `status_persetujuan` IN ('Pending', 'Approved') 
        2. DAN `status_pengembalian` != 'Completed'
        3. DAN terjadi irisan tanggal: `(mulai <= $requested_end AND selesai >= $requested_start)`.
* **Sumbit:** Insert ke `bookings` dengan `status_persetujuan = 'Pending'` dan `status_pengembalian = 'On Going'`.

**2. Kebutuhan Unit & AI Dependencies:**
* **Wajib panggil:** `@skills-livewire.md` (untuk form reaktif, *updatedHooks* pada properti tanggal untuk *fetch* mobil tersedia).
* Dilarang menggunakan jQuery atau library Datepicker eksternal, gunakan `<input type="date">` bawaan HTML5 yang di-*styling* dengan Tailwind.

---

## FLOW 3: Dasbor Analitik & Manajemen Approval Admin
**Fokus:** *Desktop-first layout*, mutasi *state* persetujuan/pembatalan, dan *aggregate query* untuk *dashboarding*.

**1. Spesifikasi Teknis & Logika:**
* **Layout Constraint:** Sidebar di sisi kiri (fixed), konten utama di sisi kanan (w-full).
* **Card Analitik (Eager Loading):**
    * Hitung *Total Peminjaman* (where Month & Year = current).
    * Hitung *Top Vehicle*, *Top Seksi*, dan *Top Pegawai* menggunakan metode `groupBy()` dan `orderByRaw('COUNT(*) DESC')` dengan `limit(5)`.
* **Tabel Manajemen:** * Tampilkan data urut berdasarkan `created_at DESC`.
    * Paginasi dinamis (Livewire `WithPagination`).
* **Mutasi State Peminjaman (Action Buttons):**
    * **Setuju:** Mengubah `status_persetujuan` menjadi 'Approved'.
    * **Tolak:** Mengubah `status_persetujuan` menjadi 'Rejected'.
    * **Batalkan (Darurat):** Hanya muncul jika status saat ini 'Approved'. Mengubah `status_persetujuan` menjadi 'Cancelled'.
    * **Selesai Dikembalikan:** Hanya muncul jika status 'Approved' dan pengembalian 'On Going'. Mengubah `status_pengembalian` menjadi 'Completed'.
* **Proteksi Role:** Sembunyikan semua *Action Buttons* secara kondisional di Blade (`@if`) jika `auth()->user()->role == 'Kepala Kantor'`.

**2. Kebutuhan Unit & AI Dependencies:**
* **Wajib panggil:** `@skills-livewire.md` (untuk *action methods* perubahan status tanpa *reload*, dan fitur *polling* `wire:poll` lambat misal 10s untuk memperbarui tabel secara *background*).

---

## FLOW 4: Manajemen Armada Kendaraan
**Fokus:** Operasi CRUD reaktif dan integrasi *Soft Delete* logikal (*Status Toggling*).

**1. Spesifikasi Teknis & Logika:**
* **Entitas Target:** Tabel `vehicles`.
* **Field Data:** `plat_mobil` (String, Unique, Primary Key/Index), `nama_mobil` (String), `status_aktif` (Enum: 'Active', 'Maintenance').
* **Form Modals:**
    * Gunakan Alpine.js untuk *show/hide* modal (Create/Edit).
    * Pengecekan validasi `unique:vehicles,plat_mobil` wajib ada di sisi *backend* (Livewire).
* **Status Toggling:** * Gunakan *toggle switch UI*. Saat diklik, langsung memanggil fungsi `toggleStatus($plat_mobil)` di Livewire untuk membalikkan nilai ('Active' <=> 'Maintenance').
    * Mobil yang berstatus 'Maintenance' dipastikan 100% tidak akan lolos/tampil pada Algoritma Ketersediaan di **Flow 2**.

**2. Kebutuhan Unit & AI Dependencies:**
* **Wajib panggil:** `@skills-livewire.md` (pengikatan data modal dan *trigger save*).
* **Wajib panggil:** `@skills-alpine.md` (mengelola variabel boolean lokal `x-data="{ open: false }"` untuk Modal/Dialog).

---

## FLOW 5: Sistem Pelaporan & Ekspor Data
**Fokus:** Ekstraksi data tabular, manipulasi *response header*, dan *Cell Formatting* absolut.

**1. Spesifikasi Teknis & Logika:**
* **Komponen Filter:** Dua *dropdown* untuk memilih `Bulan` dan `Tahun`.
* **Query Engine:** *Fetch* tabel `bookings` beserta relasi `users` dan `vehicles` (untuk mencegah *N+1 query problem*) di mana `MONTH(mulai) = $selectedMonth` dan `YEAR(mulai) = $selectedYear`.
* **Ekspor `.xlsx` (CRITICAL FORMATTING):**
    * Jika menggunakan `Maatwebsite/Laravel-Excel`:
        * Wajib mengimplementasikan *interface* `WithCustomValueBinder`.
        * Terapkan format teks absolut (`NumberFormat::FORMAT_TEXT` atau tanda `@`) pada kolom `nip_pendek` dan `plat_mobil`.
    * Jika menggunakan pendekatan CSV/Spout native: Pastikan nilai diawali dengan petik tunggal (`'`) atau diformat sedemikian rupa agar aplikasi *spreadsheet* (Excel/Google Sheets) membaca cel sebagai "Plain Text", sehingga NIP/Plat Nomor tidak berubah format (misal terpotong 0 di depan atau menjadi notasi eksponensial `E+`).
* **Trigger Download:** Eksekusi pengunduhan harus dikembalikan sebagai `Response::download()` yang reaktif melalui Livewire.

**2. Kebutuhan Unit & AI Dependencies:**
* **Wajib panggil:** `@skills-livewire.md` (mengikat nilai filter dan eksekusi fungsi `export()`).
* **Catatan AI:** Dilarang merender export menggunakan DOM HTML (*parsing table* ke *xls*). Wajib menggunakan library generator Excel server-side yang aman.