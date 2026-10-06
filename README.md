<div align="center">

# 🏛️ SARANA
### Sistem Administrasi Terpadu Layanan & Prasarana Kantor

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-v4-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-v3-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white)](https://alpinejs.dev)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](#lisensi)

<p align="center">
  <b>SARANA</b> adalah sistem enterprise multi-modul terintegrasi yang dirancang untuk mendigitalkan seluruh administrasi layanan internal kantor, mulai dari peminjaman kendaraan dinas, perizinan keluar kantor, pengelolaan nomor surat dinas, hingga katalog pengadaan bon ATK dalam satu portal terpadu berbasis web dan PWA.
</p>

[Jelajahi Fitur](#-modul--fitur-utama) • [Tangkapan Layar](#-galeri-tangkapan-layar) • [Instalasi](#-panduan-instalasi) • [Teknologi](#-tech-stack)

---

</div>

## 📌 Daftar Isi
- [Tentang SARANA](#-tentang-sarana)
- [Modul & Fitur Utama](#-modul--fitur-utama)
- [Galeri Tangkapan Layar](#-galeri-tangkapan-layar)
  - [1. Autentikasi & Portal Masuk](#1-autentikasi--portal-masuk)
  - [2. Modul SARANA (Peminjaman Kendaraan Dinas)](#2-modul-sarana-peminjaman-kendaraan-dinas)
  - [3. Modul PERMISI (Izin Keluar Kantor)](#3-modul-permisi-izin-keluar-kantor)
  - [4. Modul ASMARA (Administrasi Persuratan)](#4-modul-asmara-administrasi-persuratan)
  - [5. Modul BONA (Bon & Katalog ATK)](#5-modul-bona-bon--katalog-atk)
  - [6. Panel Administrasi & Kontrol Sistem](#6-panel-administrasi--kontrol-sistem)
- [Tech Stack](#-tech-stack)
- [Struktur Arsitektur](#-struktur-arsitektur)
- [Panduan Instalasi](#-panduan-instalasi)
- [Kredensial Default Demo](#-kredensial-default-demo)
- [Audit & Keamanan](#-audit--keamanan)
- [Lisensi](#-lisensi)

---

## 💡 Tentang SARANA

Dalam operasional instansi modern, fragmentasi proses birokrasi manual sering kali menghambat efisiensi kerja. **SARANA** hadir untuk mengintegrasikan 4 modul layanan birokrasi harian ke dalam satu Single Page Application (SPA) yang modern, cepat, responsif, dan siap digunakan di perangkat mobile maupun desktop:

1. **🚗 SARANA**: Manajemen armada dan peminjaman kendaraan operasional dinas.
2. **🏢 PERMISI**: Pengajuan dan persetujuan digital izin keluar kantor bagi pegawai.
3. **📑 ASMARA**: Pengambilan nomor urut surat keluar dinas dan pengelolaan nomor kahar.
4. **📦 BONA**: Katalog pengadaan barang habis pakai (ATK) dengan sistem keranjang dan pemotongan stok otomatis.

Semua modul dilengkapi dengan sistem kontrol otorisasi berbasis peran (**Role-Based Access Control / RBAC**), pencatatan jejak audit (*Audit Trail*), serta kapabilitas **PWA (Progressive Web App)** untuk akses cepat di ponsel pegawai.

---

## 🚀 Modul & Fitur Utama

### 1. 🚗 Modul Sarana — Peminjaman Kendaraan Operasional
- **Status Armada Realtime**: Visualisasi ketersediaan mobil (Tersedia, Digunakan, Dalam Perawatan).
- **Formulir Peminjaman Cerdas**: Pemilihan tanggal & jam dinas, tujuan perjalanan, kapasitas penumpang, serta penugasan driver.
- **Workflow Persetujuan**: Verifikasi pengajuan oleh atasan/pengelola sarana (Setujui, Tolak, Selesaikan).
- **Export Laporan**: Ekspor rekapitulasi data peminjaman ke format Microsoft Excel (*Spreadsheet*).

### 2. 🏢 Modul Permisi — Izin Keluar Kantor
- **Permohonan Digital**: Pengajuan izin keluar kantor dengan pemilihan kategori (Dinas Luar, Urusan Pribadi, Mendesak) dan estimasi durasi.
- **Dukungan Pejabat Struktural & Plh/Plt**: Sistem secara dinamis mendukung persetujuan oleh pejabat definitif maupun Pelaksana Harian (Plh) / Pelaksana Tugas (Plt).
- **Riwayat & Monitoring Pegawai**: Pantauan langsung daftar pegawai yang sedang berada di luar kantor secara realtime.

### 3. 📑 Modul ASMARA — Administrasi Surat & Nomor Kahar
- **Pengambilan Nomor Otomatis**: Generator nomor urut surat dinas resmi berdasarkan kode jenis surat, kode seksi/PJ, dan tahun aktif.
- **Pencarian Cepat**: Mesin pencarian cerdas yang mendukung pencarian nomor parsial (misal: `S-276` atau angka `276`).
- **Penanganan Surat Kahar**: Modul darurat untuk menyisipkan nomor surat dinas dalam kondisi khusus (*force majeure*) dengan logging ketat.
- **Monitoring Seluruh Surat**: Rekap komprehensif seluruh nomor surat dinas yang telah teregistrasi oleh seluruh pegawai.

### 4. 📦 Modul BONA — Bon & Permintaan ATK
- **Katalog Visual Interaktif**: Tampilan galeri kartu barang ATK lengkap dengan foto, satuan unit, dan indikator sisa stok realtime.
- **Sistem Keranjang Belanja (Internal Cart)**: Pegawai dapat mengajukan beberapa barang sekaligus dalam satu nomor bon permohonan.
- **Kelola & Verifikasi Gudang**: Admin dapat menyesuaikan jumlah barang yang disetujui, menolak dengan alasan, memotong stok secara otomatis, serta menandai penyerahan fisik barang.
- **Cetak Bukti Pengambilan**: Cetak bukti form serah terima ATK berstandar dinas (*Printable Receipt*).
- **Master Data Barang & Satuan**: Manajemen katalog barang, batas stok minimum (*Low Stock Warning*), serta impor data massal.

### 5. 🛡️ Panel Admin & Keamanan
- **Dashboard Analitik**: Ringkasan metrik statistik operasional, grafik tren peminjaman armada, dan aktivitas pegawai.
- **Manajemen Pengguna (RBAC)**: Pengelolaan akun pegawai, NIP, seksi kantor, dan peran (Admin, Reviewer, Pegawai).
- **Audit Trail (Activity Log)**: Pencatatan otomatis setiap aksi kritikal (Login, Tambah Data, Perubahan Status, Hapus Data) beserta IP address dan user agent.
- **PWA Ready**: Dukungan manifest, offline fallback view, dan service worker cache untuk kenyamanan akses di ponsel.

---

## 📸 Galeri Tangkapan Layar

Berikut adalah dokumentasi antarmuka pengguna dari setiap modul yang berjalan pada sistem **SARANA**:

### 1. Autentikasi & Portal Masuk
Halaman login yang bersih, modern, dan aman untuk otentikasi akun pegawai berbasis NIP atau Username.

![Halaman Login](public/screenshots_portfolio2/01_login_page.png)

---

### 2. Modul SARANA (Peminjaman Kendaraan Dinas)

| Formulir Pengajuan Peminjaman | Riwayat & Status Peminjaman |
| :---: | :---: |
| ![Formulir Peminjaman](public/screenshots_portfolio2/02_sarana_form_peminjaman.png) | ![Riwayat Peminjaman](public/screenshots_portfolio2/03_sarana_riwayat_peminjaman.png) |
| *Pemilihan kendaraan, driver, tujuan, dan jadwal dinas.* | *Tracking status real-time: Menunggu, Disetujui, Berlangsung.* |

<details>
<summary>🔍 <b>Lihat Modal Interaktif Modul SARANA</b></summary>
<br>

| Modal Pengajuan Armada | Modal Detail Peminjaman |
| :---: | :---: |
| ![Modal Pengajuan](public/screenshots_portfolio2/modals/modal_01_sarana_pengajuan.png) | ![Modal Detail](public/screenshots_portfolio2/modals/modal_02_sarana_detail.png) |

</details>

---

### 3. Modul PERMISI (Izin Keluar Kantor)

| Permohonan Izin Pegawai | Dashboard Persetujuan Atasan |
| :---: | :---: |
| ![Permohonan Izin](public/screenshots_portfolio2/04_permisi_permohonan.png) | ![Persetujuan Izin](public/screenshots_portfolio2/05_permisi_persetujuan.png) |
| *Input keperluan dinas/pribadi dan estimasi kembali.* | *Aksi persetujuan/penolakan oleh atasan atau Plh/Plt.* |

<details>
<summary>🔍 <b>Lihat Modal Interaktif Modul PERMISI</b></summary>
<br>

| Modal Pengajuan Permisi |
| :---: |
| ![Modal Pengajuan Permisi](public/screenshots_portfolio2/modals/modal_03_permisi_pengajuan.png) |

</details>

---

### 4. Modul ASMARA (Administrasi Persuratan)

| Pengambilan Nomor Surat Keluar | Monitoring Seluruh Surat Dinas & Kahar |
| :---: | :---: |
| ![Ambil Nomor Surat](public/screenshots_portfolio2/06_asmara_permohonan.png) | ![Monitoring Surat](public/screenshots_portfolio2/07_asmara_admin_monitoring.png) |
| *Generate nomor surat dinas otomatis per seksi.* | *Audit pencatatan seluruh surat keluar dan surat kahar.* |

<details>
<summary>🔍 <b>Lihat Modal Interaktif Modul ASMARA</b></summary>
<br>

| Modal Ambil Nomor Surat | Modal Pengaturan Surat Kahar |
| :---: | :---: |
| ![Modal Ambil Nomor](public/screenshots_portfolio2/modals/modal_04_asmara_ambil_nomor.png) | ![Modal Surat Kahar](public/screenshots_portfolio2/modals/modal_05_asmara_surat_kahar.png) |

</details>

---

### 5. Modul BONA (Bon & Katalog ATK)

| Katalog Barang & Keranjang ATK | Kelola & Persetujuan Permintaan |
| :---: | :---: |
| ![Katalog ATK](public/screenshots_portfolio2/08_bona_katalog_atk.png) | ![Kelola Persetujuan ATK](public/screenshots_portfolio2/09_bona_kelola_persetujuan.png) |
| *Katalog visual, status stok, dan multi-item shopping cart.* | *Approval jumlah barang, potongan stok, dan serah terima.* |

| Manajemen Master Data Barang | Modal Keranjang Permintaan |
| :---: | :---: |
| ![Master Data Barang](public/screenshots_portfolio2/10_bona_master_barang.png) | ![Modal Keranjang](public/screenshots_portfolio2/modals/modal_06_bona_keranjang_permintaan.png) |
| *Kelola stok fisik, batas minimum, dan satuan barang.* | *Rincian item barang yang diajukan oleh pemohon.* |

---

### 6. Panel Administrasi & Kontrol Sistem

| Dashboard Analitik & Metrik | Manajemen Pengguna & Hak Akses |
| :---: | :---: |
| ![Dashboard Analitik](public/screenshots_portfolio2/11_admin_dashboard_analitik.png) | ![Manajemen Pengguna](public/screenshots_portfolio2/12_admin_manajemen_user.png) |
| *Statistik agregat dan grafik operasional terpadu.* | *Manajemen data pegawai, NIP, seksi, dan penugasan role.* |

| Manajemen Armada Kendaraan | Log Aktivitas Sistem (Audit Trail) |
| :---: | :---: |
| ![Manajemen Armada](public/screenshots_portfolio2/13_admin_manajemen_armada.png) | ![Log Aktivitas](public/screenshots_portfolio2/14_admin_log_aktivitas.png) |
| *Registrasi kendaraan dinas, kapasitas, dan transmisi.* | *Jejak rekam audit aktivitas setiap pengguna.* |

<details>
<summary>🔍 <b>Lihat Modal Administrasi Lainnya</b></summary>
<br>

| Tambah User Pegawai | Tambah Armada Baru | Export Laporan |
| :---: | :---: | :---: |
| ![Modal Tambah User](public/screenshots_portfolio2/modals/modal_08_admin_tambah_user.png) | ![Modal Tambah Armada](public/screenshots_portfolio2/modals/modal_09_admin_tambah_armada.png) | ![Modal Export Laporan](public/screenshots_portfolio2/modals/modal_10_admin_export_laporan.png) |

</details>

---

## 🛠️ Tech Stack

| Layer | Komponen & Versi | Deskripsi |
| :--- | :--- | :--- |
| **Backend Framework** | [Laravel 12.x](https://laravel.com) | Framework PHP modern dengan arsitektur MVC, Eloquent ORM, dan artisan tooling. |
| **Fullstack Reactive UI** | [Livewire v4](https://livewire.laravel.com) | Interaktivitas dinamis tanpa reload halaman dengan performa rendering server-side. |
| **Frontend Styling** | [Tailwind CSS v4](https://tailwindcss.com) | Utility-first CSS framework dengan kustomisasi tema enterprise & font Plus Jakarta Sans. |
| **Micro-Interactions** | [Alpine.js v3](https://alpinejs.dev) | Reaktivitas sisi klien ringan untuk Optimistic UI modal dialog dan animasi transisi. |
| **Database** | MySQL / MariaDB / SQLite | Dukungan penuh multi-driver database dengan migration dan seeder komprehensif. |
| **Asset Bundler** | [Vite v8](https://vitejs.dev) | Kompilasi aset super cepat dengan dukungan Rollup & PostCSS. |
| **Reporting / Export** | [Maatwebsite Excel 3.1](https://laravel-excel.com) | Ekspor data laporan berkala ke format spreadsheet Excel (*.xlsx). |
| **Mobile Integration** | Progressive Web App (PWA) | Service Worker, web app manifest, offline view, dan installable app. |

---

## 📂 Struktur Arsitektur

```text
sarana/
├── app/
│   ├── Enums/                     # Status enum (BookingStatus, VehicleStatus)
│   ├── Exports/                   # Class export laporan excel (Bon, Booking, User)
│   ├── Http/Controllers/          # Printable receipt controllers
│   ├── Http/Middleware/           # Role middleware & automatic activity logger
│   ├── Livewire/                  # Komponen reaktif Livewire
│   │   ├── Admin/                 # Dashboard, Fleet, ActivityLog, UserManagement
│   │   ├── Asmara/                # Manajemen nomor surat keluar & surat kahar
│   │   ├── Auth/                  # Login & autentikasi
│   │   ├── Bona/                  # Katalog, Cart, Kelola, Master Barang & Satuan
│   │   ├── Peminjaman/            # Peminjaman armada & riwayat
│   │   └── Permisi/               # Izin keluar kantor & monitoring
│   └── Models/                    # Eloquent models dengan relasi lengkap
├── database/
│   ├── factories/                 # Model factories untuk testing data
│   ├── migrations/                # Skema tabel database (15+ migration files)
│   └── seeders/                   # Seeder data awal pengguna, armada, dan katalog
├── public/
│   ├── build/                     # Hasil kompilasi Vite (CSS & JS)
│   ├── icons/                     # Ikon PWA & logo aplikasi
│   ├── screenshots_portfolio2/   # Dokumentasi screenshot sistem
│   ├── manifest.json              # Web app manifest untuk instalasi PWA
│   └── sw.js                      # Service Worker caching & offline handler
├── resources/
│   ├── css/                       # Definisi styling Tailwind CSS v4 & custom scrollbar
│   ├── js/                        # Inisialisasi frontend script
│   └── views/                     # Blade templates & komponen reusable
├── routes/
│   ├── console.php                # Perintah custom artisan
│   └── web.php                    # Routing aplikasi dengan proteksi middleware
└── tests/
    └── Feature/                   # Automated Feature Tests (PHPUnit)
```

---

## ⚙️ Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek **SARANA** di lingkungan lokal Anda:

### 1. Klon Repositori
```bash
git clone git@github.com:dhafinfuad/sarana.git
cd sarana
```

### 2. Pasang Dependensi Backend & Frontend
```bash
# Dependensi PHP
composer install

# Dependensi Node.js
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file template lingkungan dan buat kunci aplikasi:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sarana
DB_USERNAME=root
DB_PASSWORD=
```
*(Atau gunakan SQLite dengan mengatur `DB_CONNECTION=sqlite`)*

### 4. Jalankan Migrasi & Seeder Database
```bash
php artisan migrate --seed
```

### 5. Buat Tautan Simbolik Storage
```bash
php artisan storage:link
```

### 6. Kompilasi Aset & Jalankan Server
```bash
# Jalankan kompilasi aset frontend
npm run build

# Jalankan server pengembangan Laravel
php artisan serve
```

Aplikasi kini dapat diakses melalui peramban di: **`http://127.0.0.1:8000`**

---

## 🔑 Kredensial Default Demo

Setelah menjalankan seeder database (`php artisan db:seed`), Anda dapat masuk menggunakan akun uji coba berikut:

| Peran (Role) | NIP / Username | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin` | `password` | Akses penuh ke seluruh modul, master data, dan kontrol sistem. |
| **Pegawai** | `808320114` | `password` | Pengajuan peminjaman armada, izin permisi, surat keluar, & bon ATK. |

---

## 🧪 Pengujian Otomatis (Automated Testing)

Aplikasi dilengkapi dengan suite pengujian otomatis untuk memvalidasi fungsionalitas logika kritikal (Pencarian Asmara, Logika Plh/Plt, dan Aksi Admin Dashboard):

```bash
php artisan test
```

---

## 🔒 Keamanan & Praktik Terbaik

- **Zero Data Exposure**: Repositori ini telah melalui proses kurasi ketat: kredensial server, private key SSL, data dump sensitif, dan log produksi sepenuhnya diabaikan dari pelacakan Git melalui `.gitignore`.
- **Role-Based Protection**: Seluruh rute administratif dilindungi oleh `RoleMiddleware`.
- **Optimistic UI with No Dismissal**: Modal dialog dilindungi dari ketidaksengajaan penutupan di luar area dialog (*backdrop click protection*).

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah lisensi terbuka [MIT License](LICENSE). Bebas digunakan, dimodifikasi, dan dikembangkan untuk keperluan instansi maupun organisasi Anda.

<div align="center">

Dibuat dengan ❤️ untuk efisiensi dan tata kelola birokrasi yang lebih baik.

</div>
