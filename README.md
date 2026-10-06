<div align="center">

# 🏛️ SARANA
### Sistem Administrasi Terpadu Layanan & Prasarana Kantor

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-v4-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-v3-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white)](https://alpinejs.dev)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](#lisensi)

<p align="center">
  <b>SARANA</b> adalah sistem enterprise multi-modul terintegrasi yang dirancang untuk mendigitalkan seluruh administrasi layanan internal kantor, mulai dari peminjaman kendaraan dinas, perizinan keluar kantor, administrasi surat, serta pengelolaan kebutuhan ATK dan inventori.
</p>

[Mulai](#-panduan-instalasi) • [Fitur](#-modul--fitur-utama) • [Demo](#-kredensial-default-demo) • [Teknologi](#-tech-stack)

---

</div>

## 💡 Tentang SARANA

Dalam operasional instansi modern, fragmentasi proses birokrasi manual sering kali menghambat efisiensi kerja. **SARANA** hadir untuk mengintegrasikan 4 modul layanan birokrasi harian ke dalam satu sistem digital terpadu:

1. **🚗 SARANA**: Manajemen armada dan peminjaman kendaraan operasional dinas
2. **🏢 PERMISI**: Pengajuan dan persetujuan digital izin keluar kantor bagi pegawai
3. **📑 ASMARA**: Pengambilan nomor urut surat keluar dinas dan pengelolaan nomor kahar
4. **📦 BONA**: Katalog pengadaan barang habis pakai (ATK) dengan sistem keranjang dan pemotongan stok otomatis

Semua modul dilengkapi dengan sistem kontrol otorisasi berbasis peran (RBAC), pencatatan jejak audit (*Audit Trail*), serta kapabilitas PWA untuk akses mobile yang seamless.

---

## 🚀 Modul & Fitur Utama

### 1. 🚗 Modul Sarana — Peminjaman Kendaraan Operasional
- **Status Armada Realtime**: Visualisasi ketersediaan mobil (Tersedia, Digunakan, Dalam Perawatan)
- **Formulir Peminjaman Cerdas**: Pemilihan tanggal, jam dinas, tujuan perjalanan, kapasitas penumpang, penugasan driver
- **Workflow Persetujuan**: Verifikasi pengajuan oleh atasan/pengelola sarana dengan status (Setujui, Tolak, Selesaikan)
- **Export Laporan**: Rekapitulasi data peminjaman ke format Excel

### 2. 🏢 Modul Permisi — Izin Keluar Kantor
- **Permohonan Digital**: Pengajuan izin dengan kategori (Dinas Luar, Urusan Pribadi, Mendesak) dan estimasi durasi
- **Dukungan Pejabat Dinamis**: Persetujuan oleh pejabat definitif atau Plh/Plt (Pelaksana Harian/Tugas)
- **Monitoring Real-time**: Pantauan langsung pegawai yang sedang berada di luar kantor

### 3. 📑 Modul ASMARA — Administrasi Surat & Nomor Kahar
- **Pengambilan Nomor Otomatis**: Generator nomor surat berstandar dengan format `[Kode]-[Nomor]-[Seksi]-[Tahun]`
- **Pencarian Cepat**: Mesin pencarian fuzzy matching (cari `S-276` atau hanya `276`)
- **Penanganan Surat Kahar**: Modul darurat untuk kondisi khusus dengan logging audit ketat
- **Monitoring Terpadu**: Rekap komprehensif semua surat keluar dan surat kahar

### 4. 📦 Modul BONA — Bon & Katalog ATK
- **Katalog Visual**: Tampilan galeri barang ATK dengan foto, satuan, dan indikator stok real-time
- **Keranjang Belanja**: Pegawai dapat mengajukan multi-item dalam satu nomor bon
- **Verifikasi Admin**: Approval jumlah barang, penolakan dengan alasan, potongan stok otomatis
- **Cetak Bukti**: Form serah terima barang berstandar dan Master data katalog

### 5. 🛡️ Panel Admin & Keamanan
- **Dashboard Analitik**: Metrik statistik operasional, grafik tren, dan aktivitas pegawai
- **Manajemen Pengguna (RBAC)**: Kontrol akun, NIP, seksi kantor, dan penugasan peran
- **Audit Trail**: Pencatatan otomatis aksi kritikal (login, perubahan data, approval, hapus) dengan IP & user agent
- **PWA Ready**: Offline fallback, service worker caching, dan installable app

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
| ![Modal Tambah User](public/screenshots_portfolio2/modals/modal_08_admin_tambah_user.png) | ![Modal Tambah Armada](public/screenshots_portfolio2/modals/modal_09_admin_tambah_armada.png) | ![Modal Export](public/screenshots_portfolio2/modals/modal_10_admin_export_laporan.png) |

</details>

---

## 🛠️ Tech Stack

| Layer | Komponen & Versi | Deskripsi |
| :--- | :--- | :--- |
| **Backend Framework** | [Laravel 13.x](https://laravel.com) | Framework PHP modern dengan arsitektur MVC, Eloquent ORM, dan artisan tooling |
| **Fullstack Reactive UI** | [Livewire v4](https://livewire.laravel.com) | Interaktivitas dinamis tanpa reload halaman dengan rendering server-side |
| **Frontend Styling** | [Tailwind CSS v4](https://tailwindcss.com) | Utility-first CSS framework dengan kustomisasi tema enterprise |
| **Micro-Interactions** | [Alpine.js v3](https://alpinejs.dev) | Reaktivitas sisi klien untuk modal dialog dan animasi transisi |
| **Database** | MySQL / MariaDB / SQLite | Multi-driver support dengan migration dan seeder komprehensif |
| **Asset Bundler** | [Vite v8](https://vitejs.dev) | Kompilasi aset super cepat dengan HMR support |
| **Reporting / Export** | [Maatwebsite Excel 3.1](https://laravel-excel.com) | Ekspor data laporan ke format spreadsheet Excel |
| **Mobile Integration** | Progressive Web App (PWA) | Service Worker, web app manifest, offline view, dan installable app |

---

## 📂 Struktur Arsitektur

```text
sarana/
├── app/
│   ├── Enums/                     # Status enum (BookingStatus, VehicleStatus)
│   ├── Exports/                   # Class export laporan excel
│   ├── Http/Controllers/          # Printable receipt controllers
│   ├── Http/Middleware/           # Role middleware & activity logger
│   ├── Livewire/                  # Komponen reaktif
│   │   ├── Admin/                 # Dashboard, Fleet, ActivityLog, UserManagement
│   │   ├── Asmara/                # Manajemen nomor surat keluar & kahar
│   │   ├── Auth/                  # Login & autentikasi
│   │   ├── Bona/                  # Katalog, Cart, Kelola, Master Barang
│   │   ├── Peminjaman/            # Peminjaman armada & riwayat
│   │   └── Permisi/               # Izin keluar kantor & monitoring
│   └── Models/                    # Eloquent models dengan relasi
├── database/
│   ├── factories/                 # Model factories
│   ├── migrations/                # Skema tabel database
│   └── seeders/                   # Data awal pengguna, armada, katalog
├── public/
│   ├── build/                     # Hasil kompilasi Vite
│   ├── icons/                     # Ikon PWA & logo
│   ├── screenshots_portfolio2/    # Screenshot sistem
│   ├── manifest.json              # Web app manifest
│   └── sw.js                      # Service Worker
├── resources/
│   ├── css/                       # Tailwind CSS v4 & custom styles
│   ├── js/                        # Frontend script
│   └── views/                     # Blade templates & komponen
├── routes/
│   ├── console.php                # Perintah custom artisan
│   └── web.php                    # Routing dengan middleware
└── tests/
    └── Feature/                   # Automated Feature Tests (PHPUnit)
```

---

## ⚙️ Panduan Instalasi

### Prasyarat
- PHP 8.3+
- Composer
- Node.js 20+
- MySQL 8.0+ / MariaDB / SQLite

### Langkah-Langkah

#### 1. Clone Repositori
```bash
git clone git@github.com:dhafinfuad/sarana.git
cd sarana
```

#### 2. Install Dependencies
```bash
composer install
npm install
```

#### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sarana
DB_USERNAME=root
DB_PASSWORD=
```

#### 4. Setup Database
```bash
php artisan migrate --seed
php artisan storage:link
```

#### 5. Compile & Run
```bash
npm run build
php artisan serve
```

Akses aplikasi di: **`http://127.0.0.1:8000`**

---

## 🔑 Kredensial Default Demo

| Peran | Username | Password | Akses |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin` | `password` | Seluruh modul, master data, sistem |
| **Pegawai** | `808320114` | `password` | Pengajuan layanan (SARANA, PERMISI, ASMARA, BONA) |

> ⚠️ Segera ubah password default untuk production environment.

---

## 🧪 Pengujian Otomatis

```bash
php artisan test
```

Aplikasi dilengkapi dengan suite testing untuk memvalidasi fungsionalitas kritikal (Pencarian Asmara, Logika Plh/Plt, Aksi Admin Dashboard).

---

## 🔒 Keamanan & Praktik Terbaik

- ✅ **Zero Data Exposure**: Kredensial, private key, dan data sensitif diabaikan dari Git via `.gitignore`
- ✅ **Authentication**: Laravel built-in dengan BCrypt hashing (12 rounds)
- ✅ **Authorization**: Role-Based Access Control (RBAC) granular per modul
- ✅ **Audit Trail**: Pencatatan otomatis setiap aksi dengan timestamp, user ID, IP address
- ✅ **SQL Injection Protection**: Query builder & parameterized queries
- ✅ **CSRF Protection**: CSRF tokens di setiap form
- ✅ **Rate Limiting**: Built-in throttling untuk login & endpoints
- ✅ **Role-Based Protection**: Rute administratif dilindungi oleh middleware
- ✅ **Modal Protection**: Dialog dilindungi dari backdrop click

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah lisensi [MIT License](LICENSE). Bebas digunakan, dimodifikasi, dan dikembangkan untuk keperluan instansi maupun organisasi Anda.

---

<div align="center">

Dibuat dengan ❤️ untuk efisiensi dan tata kelola birokrasi yang lebih baik.

</div>
