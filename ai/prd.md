# Product Requirements Document (PRD)
**Proyek:** Sistem Peminjaman Kendaraan Dinas
**Stack:** Laravel, Tailwind CSS, Livewire, Alpine.js
**Platform:** Progressive Web App (PWA)

## 1. Visi & Tujuan Proyek
Membangun aplikasi manajemen peminjaman kendaraan dinas berbasis web yang efisien, terpusat, dan minim hambatan administratif. Fokus utama sistem ini adalah menghadirkan alur kerja peminjaman yang serba cepat, mempertegas batas wewenang pengguna, serta menyajikan pengalaman UI/UX yang modern (Mobile-first untuk Pegawai, Desktop-first untuk Admin) dengan dukungan penuh kapabilitas Progressive Web App (PWA).

## 2. Pemetaan Peran & Hak Akses (Roles & Permissions)
Sistem ini menggunakan 3 peran (Role) pengguna secara definitif:

* **Administrator:** Memiliki hak akses penuh. Mengakses Dasbor Admin, mengelola seluruh data peminjaman, mengeksekusi persetujuan/pembatalan/pengembalian seluruh pegawai, mengekspor laporan, serta mengelola status armada kendaraan.
* **Kepala Kantor:** Memiliki akses baca (Read-Only) ke Dasbor Admin. Dapat melihat seluruh data peminjaman dan analitik, namun **tidak memiliki** tombol/aksi untuk melakukan persetujuan, pembatalan, atau manipulasi data apa pun.
* **Pegawai (User):** Hanya dapat melihat riwayat peminjamannya sendiri. Hanya memiliki akses untuk membuat form pengajuan baru dan menekan tombol konfirmasi "Selesai Dikembalikan" atas kendaraannya sendiri. Tidak memiliki akses ke Dasbor Admin.

## 3. Aturan Bisnis Inti (Core Business Logic)
* **Ketersediaan (Availability) Berbasis Hari Penuh:** Resolusi peminjaman didasarkan pada Tanggal (Date), bukan Jam. Jika kendaraan dipinjam dari tanggal X hingga Y, maka kendaraan tersebut di-lock penuh (Unavailable) untuk pengguna lain pada rentang hari tersebut secara utuh.
* **Pengemudi Mandiri:** Seluruh kendaraan dikemudikan sendiri oleh Pegawai peminjam (tidak ada manajemen *driver*).
* **Validasi Form Pengajuan:** Field **Tujuan** (Dalam Kota / Luar Kota) dan **Keperluan** bersifat *mandatory* (wajib diisi penuh) sebagai syarat utama pengajuan peminjaman.
* **Intervensi & Pembatalan:**
    * Pegawai **tidak bisa** membatalkan pengajuan mereka sendiri setelah disubmit.
    * Administrator **dapat** membatalkan peminjaman kapan saja (termasuk yang sudah berstatus 'Setuju') jika terjadi keadaan darurat/kebutuhan mendesak.
* **Prosedur Pengembalian:** Ringkas. Pegawai atau Admin cukup menekan tombol aksi "Selesai Dikembalikan" di dalam sistem. Keterlambatan pengembalian ditangani secara manual oleh Administrator di luar sistem (tidak ada pemblokiran akun otomatis).

## 4. Spesifikasi Fitur Utama
### A. PWA & Notifikasi Web
* Aplikasi harus memenuhi standar PWA (`manifest.json` dengan `display: standalone`).
* Menggunakan *safe-area* (`pt-safe`, `pb-safe`) pada layout untuk kompatibilitas layar ponsel.
* Terdapat fitur Web Notification terintegrasi pada aplikasi untuk memberitahu Pegawai mengenai perubahan status peminjaman (Disetujui/Ditolak/Dibatalkan).

### B. Dasbor Admin (Desktop-First)
Antarmuka dasbor harus memuat elemen berikut:
1.  **Card Analitik:** * Total peminjaman pada rentang bulan dan tahun berjalan.
    * Metrik frekuensi peminjaman terbanyak berdasarkan: Mobil, Seksi, dan Pegawai.
2.  **Tabel Approval & Manajemen:** Tabel yang memuat seluruh data peminjaman dengan aksi "Setuju", "Tolak", "Batalkan", dan "Selesai Dikembalikan".
3.  **Manajemen Kendaraan:** Modul pengelolaan daftar armada (Plat & Nama Mobil). Admin dapat mengatur status mobil menjadi "Tersedia" atau "Tidak Tersedia" (karena rusak/servis rutin). Mobil berstatus "Tidak Tersedia" otomatis disembunyikan dari pilihan *dropdown* form pengajuan Pegawai.

### C. Ekspor Laporan (Export to Excel)
* Fitur *generate* file pelaporan berformat `.xlsx`.
* Memiliki filter rentang periode (Tahun dan Bulan).
* Isi laporan memuat *seluruh* field data dari tabel peminjaman.
* **Catatan Kritis Format:** Seluruh *cell* di dalam file Excel yang dihasilkan **wajib** di-render sebagai tipe data `Text` untuk mencegah malformat karakter otomatis pada angka (seperti NIP atau Plat nomor).

## 5. Struktur Entitas Database
* **Users/Pegawai:** Menyimpan kredensial otentikasi, nama lengkap, penempatan seksi, dan penetapan Role.
* **Vehicles/Mobil:** Menyimpan master data armada (Plat nomor, Nama Kendaraan, dan Status Aktif/Maintenance).
* **Bookings/Peminjaman:** Menyimpan histori dan transaksi pengajuan. Menggunakan *state management* status yang jelas (misalnya: *Pending, Approved, Rejected, Completed, Cancelled*).