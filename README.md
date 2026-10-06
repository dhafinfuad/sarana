# 🏛️ SARANA — Sistem Administrasi Terpadu Layanan & Prasarana Kantor

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-v4-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](#lisensi)

Sistem enterprise multi-modul terintegrasi yang mendigitalkan seluruh administrasi layanan internal kantor. Mengintegrasikan **peminjaman kendaraan dinas**, **perizinan keluar kantor**, **administrasi surat**, dan **katalog ATK** dalam satu platform.

**[📱 Mulai Setup](#-panduan-setup) • [✨ Fitur](#-modul--fitur-utama) • [🛠️ Tech Stack](#️-tech-stack) • [📸 Screenshots](#-screenshots) • [🔑 Demo](#-kredensial-default)**

---

## 📋 Daftar Isi

- [📌 Tentang Proyek](#-tentang-proyek)
- [📸 Screenshots](#-screenshots)
- [✨ Modul & Fitur Utama](#-modul--fitur-utama)
- [🛠️ Tech Stack](#️-tech-stack)
- [📁 Struktur Proyek](#-struktur-proyek)
- [🚀 Panduan Setup](#-panduan-setup)
- [🧪 Testing](#-testing)
- [🔐 Keamanan](#-keamanan)
- [📦 Deployment](#-deployment)
- [🤝 Kontribusi](#-kontribusi)
- [📄 Lisensi](#-lisensi)

---

## 📌 Tentang Proyek

**SARANA** adalah solusi manajemen administratif modern yang mengintegrasikan empat modul layanan birokrasi harian instansi/kantor:

1. **🚗 SARANA**: Peminjaman kendaraan operasional dinas dengan tracking realtime
2. **🏢 PERMISI**: Izin keluar kantor digital dengan workflow approval
3. **📑 ASMARA**: Administrasi nomor surat keluar dinas & surat kahar
4. **📦 BONA**: Katalog pengadaan ATK dengan sistem keranjang & potongan stok

Dengan **Role-Based Access Control (RBAC)**, **Audit Trail** lengkap, dan **PWA capability** untuk akses mobile seamless, SARANA membantu organisasi meningkatkan efisiensi operasional dan transparansi proses administrasi.

---

## 📸 Screenshots

### 🔐 Autentikasi & Portal Masuk
Halaman login yang bersih dan aman untuk autentikasi pegawai berbasis NIP/Username.

![Login Page](public/screenshots_portfolio2/01_login_page.png)

### 🚗 Modul SARANA (Peminjaman Kendaraan)

| Formulir Pengajuan | Riwayat & Status |
| :---: | :---: |
| ![Formulir Peminjaman](public/screenshots_portfolio2/02_sarana_form_peminjaman.png) | ![Riwayat Peminjaman](public/screenshots_portfolio2/03_sarana_riwayat_peminjaman.png) |
| *Pemilihan kendaraan, driver & jadwal dinas* | *Tracking status realtime: Menunggu, Setujui, Berlangsung* |

### 🏢 Modul PERMISI (Izin Keluar Kantor)

| Permohonan Izin | Dashboard Persetujuan |
| :---: | :---: |
| ![Permohonan Izin](public/screenshots_portfolio2/04_permisi_permohonan.png) | ![Persetujuan Izin](public/screenshots_portfolio2/05_permisi_persetujuan.png) |
| *Input keperluan dinas/pribadi & estimasi* | *Workflow approval oleh atasan/Plh* |

### 📑 Modul ASMARA (Administrasi Surat)

| Pengambilan Nomor | Monitoring Surat |
| :---: | :---: |
| ![Ambil Nomor](public/screenshots_portfolio2/06_asmara_permohonan.png) | ![Monitoring](public/screenshots_portfolio2/07_asmara_admin_monitoring.png) |
| *Generator nomor surat otomatis per seksi* | *Audit surat keluar & surat kahar* |

### 📦 Modul BONA (Bon & Katalog ATK)

| Katalog Barang | Kelola Persetujuan |
| :---: | :---: |
| ![Katalog ATK](public/screenshots_portfolio2/08_bona_katalog_atk.png) | ![Kelola Persetujuan](public/screenshots_portfolio2/09_bona_kelola_persetujuan.png) |
| *Katalog visual, status stok realtime* | *Approval jumlah & potongan stok otomatis* |

| Master Data Barang |
| :---: |
| ![Master Data](public/screenshots_portfolio2/10_bona_master_barang.png) |
| *Kelola stok fisik & batas minimum* |

### 🛡️ Panel Administrasi

| Dashboard Analitik | Manajemen Pengguna |
| :---: | :---: |
| ![Dashboard](public/screenshots_portfolio2/11_admin_dashboard_analitik.png) | ![Manajemen User](public/screenshots_portfolio2/12_admin_manajemen_user.png) |
| *Statistik & grafik operasional* | *Kontrol akun, NIP, seksi & role* |

| Manajemen Armada | Audit Trail |
| :---: | :---: |
| ![Armada](public/screenshots_portfolio2/13_admin_manajemen_armada.png) | ![Audit Log](public/screenshots_portfolio2/14_admin_log_aktivitas.png) |
| *Registrasi kendaraan & kapasitas* | *Jejak rekam audit setiap pengguna* |

---

## ✨ Modul & Fitur Utama

### 🚗 Modul SARANA — Peminjaman Kendaraan Operasional
- **Status Armada Realtime**: Visualisasi ketersediaan mobil (Tersedia, Digunakan, Perawatan)
- **Formulir Peminjaman Cerdas**: Pemilihan tanggal, jam, tujuan, kapasitas, penugasan driver
- **Workflow Persetujuan**: Verifikasi oleh atasan/pengelola sarana
- **Export Laporan**: Rekapitulasi data ke format Excel

### 🏢 Modul PERMISI — Izin Keluar Kantor
- **Permohonan Digital**: Pengajuan kategori (Dinas Luar, Pribadi, Mendesak) dengan durasi
- **Dukungan Pejabat Dinamis**: Persetujuan oleh definitif atau Plh/Plt
- **Monitoring Realtime**: Pantau pegawai di luar kantor

### 📑 Modul ASMARA — Administrasi Surat & Nomor Kahar
- **Pengambilan Nomor Otomatis**: Generator format `[Kode]-[Nomor]-[Seksi]-[Tahun]`
- **Pencarian Fuzzy Matching**: Cari `S-276` atau hanya `276`
- **Penanganan Surat Kahar**: Modul darurat dengan logging audit ketat
- **Monitoring Terpadu**: Rekap semua surat keluar & kahar

### 📦 Modul BONA — Bon & Katalog ATK
- **Katalog Visual**: Galeri barang ATK dengan foto & indikator stok
- **Keranjang Belanja**: Multi-item dalam satu nomor bon
- **Verifikasi Admin**: Approval jumlah, penolakan, potongan stok otomatis
- **Cetak Bukti**: Form serah terima & master data katalog

### 🛡️ Panel Admin & Keamanan
- **Dashboard Analitik**: Metrik statistik & grafik tren
- **Manajemen Pengguna (RBAC)**: Kontrol akun, NIP, seksi, role
- **Audit Trail**: Pencatatan aksi kritikal dengan IP & user agent
- **PWA Ready**: Offline fallback & installable app

---

## 🛠️ Tech Stack

| Layer | Teknologi | Versi | Deskripsi |
| :--- | :--- | :--- | :--- |
| **Backend Framework** | [Laravel](https://laravel.com) | 13.x | Framework PHP modern, Eloquent ORM, artisan tooling |
| **Fullstack Reactivity** | [Livewire](https://livewire.laravel.com) | v4 | Interaktivitas dinamis tanpa reload halaman |
| **Frontend Styling** | [Tailwind CSS](https://tailwindcss.com) | v4 | Utility-first CSS dengan kustomisasi enterprise |
| **Micro-Interactions** | [Alpine.js](https://alpinejs.dev) | v3 | Reaktivitas sisi klien untuk modal & animasi |
| **Database** | MySQL / MariaDB / SQLite | Latest | Multi-driver dengan migration & seeder |
| **Asset Bundler** | [Vite](https://vitejs.dev) | v8 | Kompilasi super cepat dengan HMR |
| **Excel Export** | [Maatwebsite Excel](https://laravel-excel.com) | 3.1 | Ekspor laporan ke spreadsheet Excel |
| **PWA & Offline** | Service Worker | Standard | Offline view & installable app |

---

## 📁 Struktur Proyek

```text
sarana/
├── app/
│   ├── Enums/                       # Status enum (BookingStatus, VehicleStatus)
│   ├── Exports/                     # Class export laporan Excel
│   ├── Http/
│   │   ├── Controllers/             # Printable receipt controllers
│   │   └── Middleware/              # Role middleware & activity logger
│   ├── Livewire/                    # Komponen reaktif
│   │   ├── Admin/                   # Dashboard, Fleet, Audit, Users
│   │   ├── Asmara/                  # Nomor surat & kahar
│   │   ├── Auth/                    # Login & autentikasi
│   │   ├── Bona/                    # Katalog, Cart, Master
│   │   ├── Peminjaman/              # Armada & riwayat
│   │   └── Permisi/                 # Izin keluar & monitoring
│   └── Models/                      # Eloquent models dengan relasi
├── database/
│   ├── factories/                   # Model factories
│   ├── migrations/                  # Skema database
│   └── seeders/                     # Data awal
├── public/
│   ├── build/                       # Hasil kompilasi Vite
│   ├── icons/                       # Ikon PWA & logo
│   ├── screenshots_portfolio2/      # 📸 Screenshot sistem
│   ├── manifest.json                # Web app manifest
│   └── sw.js                        # Service Worker
├── resources/
│   ├── css/                         # Tailwind CSS v4
│   ├── js/                          # Frontend script
│   └── views/                       # Blade templates & komponen
├── routes/
│   ├── console.php                  # Custom artisan commands
│   └── web.php                      # Web routes & middleware
├── tests/
│   └── Feature/                     # Automated Feature Tests
├── .env.example
├── composer.json
├── package.json
└── README.md
```

---

## 🚀 Panduan Setup

### 📋 Prasyarat

- **PHP** 8.3+
- **Composer** (PHP dependency manager)
- **Node.js** 20+ & **NPM**
- **MySQL** 8.0+ / **MariaDB** / **SQLite**
- **Git** untuk version control

---

### ⚡ Langkah Instalasi

#### 1️⃣ Clone Repository
```bash
git clone https://github.com/dhafinfuad/sarana.git
cd sarana
```

#### 2️⃣ Instalasi Dependencies
```bash
composer install
npm install
```

#### 3️⃣ Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sarana
DB_USERNAME=root
DB_PASSWORD=
```

#### 4️⃣ Setup Database
```bash
php artisan migrate --seed
php artisan storage:link
```

#### 5️⃣ Compile & Jalankan
```bash
npm run build
php artisan serve
```

Akses aplikasi di: **`http://127.0.0.1:8000`**

---

### 🔑 Kredensial Default

| Peran | Username | Password |
| :--- | :--- | :--- |
| **Super Admin** | `admin` | `password` |
| **Pegawai** | `808320114` | `password` |

⚠️ **Ubah password untuk production!**

---

## 🧪 Testing

```bash
php artisan test
```

Suite testing mencakup:
- ✅ Pencarian Asmara
- ✅ Logika Plh/Plt
- ✅ Aksi Admin Dashboard

---

## 🔐 Keamanan

### Best Practices yang Diterapkan
- ✅ **Zero Data Exposure**: Kredensial diabaikan dari Git
- ✅ **Authentication**: Laravel BCrypt hashing (12 rounds)
- ✅ **Authorization**: RBAC granular per modul
- ✅ **Audit Trail**: Pencatatan aksi dengan timestamp, IP, user agent
- ✅ **SQL Injection Protection**: Query builder & parameterized queries
- ✅ **CSRF Protection**: CSRF tokens di setiap form
- ✅ **Rate Limiting**: Throttling untuk login & endpoints
- ✅ **Modal Protection**: Dialog dilindungi dari backdrop click

---

## 📦 Deployment

### Production Build
```bash
npm run build
php artisan optimize
php artisan config:cache
php artisan route:cache
```

### Upload ke Server
1. Gunakan FTP, SSH, atau CI/CD pipeline
2. Konfigurasi `.env` production
3. Jalankan: `composer install --no-dev && php artisan migrate --force`

---

## 🤝 Kontribusi

Kami menerima kontribusi dari komunitas!

1. **Fork** repositori
2. **Buat branch** feature: `git checkout -b feature/nama-fitur`
3. **Commit** perubahan: `git commit -m 'Add: nama-fitur'`
4. **Push**: `git push origin feature/nama-fitur`
5. **Buat Pull Request**

Pastikan:
- ✅ Code mengikuti coding standards
- ✅ Test sudah dibuat/diupdate
- ✅ Dokumentasi sudah diupdate

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE). Bebas digunakan, dimodifikasi, dan dikembangkan untuk keperluan instansi maupun organisasi Anda.

---

**Dibuat dengan ❤️ untuk efisiensi dan tata kelola birokrasi yang lebih baik**
