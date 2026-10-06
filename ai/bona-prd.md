# PRD — Fitur BONA (Bon ATK)
## Aplikasi Permintaan Stok Barang ATK — Modul Terintegrasi SARANA

**Versi:** 1.1 (revisi berdasarkan review)
**Tanggal:** 2026-07-08  
**Referensi Asli:** Aplikasi 651 BONA (`permintaan/`)

---

## 1. Latar Belakang & Tujuan

Aplikasi **651 BONA** adalah sistem permintaan stok barang ATK (Alat Tulis Kantor) yang sudah berjalan di kantor secara terpisah menggunakan PHP murni dan database MySQL tersendiri. Sistem ini mencatat:

- **Pengajuan Form Bon** oleh pegawai (barang apa, berapa jumlah).
- **Proses Pengeluaran Stok** oleh Petugas Subbag Umum yang mengonfirmasi pengeluaran dan memotong stok.

Fitur **BONA** akan diintegrasikan ke dalam aplikasi **SARANA** yang sudah ada (Laravel + Livewire + Alpine.js + Tailwind CSS), sehingga menjadi modul ketiga bersama Sarana (Peminjaman Kendaraan) dan Permisi (Permohonan Izin).

---

## 2. Ruang Lingkup Fitur

### 2.1 Halaman Utama BONA
Terdiri dari **2 sub-halaman** yang dapat diakses dari menu navigasi:

| Sub-halaman | Akses |
|---|---|
| **Bon Saya** (Pengajuan) | Seluruh pegawai kecuali Kepala Kantor |
| **Kelola Bon** (Manajemen/Proses) | Seluruh Pegawai Subbagian Umum dan Kepatuhan Internal + Administrator |

### 2.2 Halaman Master Data (Admin)
Halaman pengelolaan data referensi:

| Halaman | Akses |
|---|---|
| **Master Barang ATK** | Subbagian Umum & Kepatuhan Internal + Administrator |
| **Master Satuan** | Subbagian Umum & Kepatuhan Internal + Administrator |

---

## 3. Hak Akses Detail

| Role / Jabatan | Bon Saya | Kelola Bon | Master Barang | Master Satuan |
|---|:---:|:---:|:---:|:---:|
| **Administrator** | ✅ | ✅ | ✅ | ✅ |
| **Subbag Umum & Kepatuhan Internal** | ✅ | ✅ | ✅ | ✅ |
| **Kepala Kantor** | ❌ | ❌ | ❌ | ❌ |
| **Kepala Seksi** | ✅ | ❌ | ❌ | ❌ |
| **Supervisor** | ✅ | ❌ | ❌ | ❌ |
| **FPP** | ✅ | ❌ | ❌ | ❌ |
| **Pegawai lain** | ✅ | ❌ | ❌ | ❌ |

> **Catatan:** Kepala Kantor tidak dapat mengajukan bon maupun mengelola bon.

---

## 4. Alur Proses (Business Flow)

```
Pegawai                       Petugas Subbag Umum / Admin
    │                                      │
    ├─ Buka halaman "Bon Saya"             │
    ├─ Klik [+ Ajukan Bon ATK]             │
    ├─ Isi Form Modal:                     │
    │   - Keperluan (teks bebas)           │
    │   - Pilih barang dari list bergambar │
    │     (stok 0 tidak ditampilkan)       │
    │   - Masukkan jumlah                  │
    │   - Ulangi item hingga cukup         │
    ├─ Klik [Kirim Pengajuan]              │
    │   → Status: MENUNGGU                 │
    │                                      │
    │          [Tabel auto-refresh berkala]│
    │                    ┌─────────────────┤
    │                    │ Halaman "Kelola Bon"
    │                    │ muncul bon baru  │
    │                    │ Status: MENUNGGU │
    │                    │ [Auto-refresh]   │
    │                    │                  │
    │                    ├─ Klik [Proses]   │
    │                    │  (konfirmasi     │
    │                    │   modal)         │
    │                    │  → Stok berkurang│
    │                    │  → Status:       │
    │                    │    DIPROSES      │
    │                    │                  │
    │                    ├─ Klik [Tolak]    │
    │                    │  → Status:       │
    │                    │    DITOLAK       │
    │◄───────────────────┘                  │
    │ (Pegawai lihat status di "Bon Saya") │
    │                                      │
    ├─ Klik [Batalkan] (jika MENUNGGU)     │
    │   → Status: DIBATALKAN               │
```

---

## 5. Struktur Tabel Database

### 5.1 `bon_items` (Master Barang ATK)
Menggantikan `data_barang` dari aplikasi asli.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint unsigned | PK, auto-increment |
| `kode_barang` | varchar(30) | Unik, format: `ATK000001` |
| `nama_barang` | varchar(100) | Nama barang ATK |
| `satuan_id` | bigint unsigned | FK → `bon_satuans.id` |
| `stok` | int | Jumlah stok saat ini |
| `stok_minimum` | int | Batas stok minimum (alert) |
| `image_path` | varchar(255)\|null | Path foto barang (tersimpan di `storage/public/bon-items/`) |
| `created_by` | bigint unsigned | FK → `users.id` |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### 5.2 `bon_satuans` (Master Satuan)
Menggantikan `satuan` dari aplikasi asli.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint unsigned | PK, auto-increment |
| `nama_satuan` | varchar(50) | Contoh: PCS, BOX, RIM, LUSIN |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### 5.3 `bon_requests` (Header Form Bon)
Menggantikan `pembelian` dari aplikasi asli (di konteks ini: Form Pengambilan).

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint unsigned | PK, auto-increment |
| `no_bon` | varchar(20) | Nomor unik bon, format: `BON-YYYYMM-XXXXX` |
| `user_id` | bigint unsigned | FK → `users.id` (pengaju) |
| `keperluan` | text | Deskripsi keperluan pengambilan |
| `status` | enum | `menunggu`, `diproses`, `ditolak`, `dibatalkan` |
| `catatan_petugas` | text\|null | Catatan dari petugas saat menolak |
| `processed_by` | bigint unsigned\|null | FK → `users.id` (petugas yang memproses) |
| `processed_at` | timestamp\|null | Waktu diproses |
| `created_at` | timestamp | Waktu pengajuan |
| `updated_at` | timestamp | |

### 5.4 `bon_request_items` (Detail Item Bon)
Menggantikan `pembelian_detail` dari aplikasi asli.

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint unsigned | PK, auto-increment |
| `bon_request_id` | bigint unsigned | FK → `bon_requests.id` |
| `bon_item_id` | bigint unsigned | FK → `bon_items.id` |
| `nama_barang` | varchar(100) | Snapshot nama barang saat pengajuan |
| `satuan` | varchar(50) | Snapshot satuan saat pengajuan |
| `jumlah_diminta` | int | Jumlah yang diminta pegawai |
| `jumlah_diberikan` | int\|null | Jumlah yang benar-benar diberikan (diisi petugas saat memproses) |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## 6. Spesifikasi Halaman & Komponen UI

### 6.1 Halaman "Bon Saya" (`bona.permohonan`)

**Deskripsi:** Halaman untuk pegawai melihat riwayat bon mereka sendiri dan mengajukan bon baru.

#### 6.1.1 Kartu Analitik (Bento Grid)
4 kartu statistik di bagian atas:
- Total Bon Bulan Ini
- Menunggu Proses
- Diproses / Selesai
- Ditolak

#### 6.1.2 Tabel Riwayat Bon

> ⚡ **Auto-refresh:** Tabel ini harus di-refresh secara otomatis secara berkala menggunakan `wire:poll.30s` agar status bon terbaru selalu tampil tanpa perlu reload halaman.

| Kolom | Keterangan |
|---|---|
| No. Bon | `BON-YYYYMM-XXXXX` |
| Keperluan | Deskripsi singkat |
| Jumlah Item | Total jenis barang yang diminta |
| Tanggal Pengajuan | Format: `DD MMM YYYY` |
| Status | Badge berwarna |
| Aksi | Tombol-tombol aksi |

**Status Badge:**
- `menunggu` → kuning/amber
- `diproses` → hijau/emerald
- `ditolak` → merah/red
- `dibatalkan` → abu-abu

**Tombol Aksi per Baris:**

| Tombol | Icon | Warna | Kondisi Tampil |
|---|---|---|---|
| **Detail** | `visibility` | biru | Selalu tampil |
| **Batalkan** | `cancel` | amber | Hanya jika status `menunggu` |

#### 6.1.3 Tombol Utama
- **[+ Ajukan Bon ATK]** — di bagian atas tabel, membuka modal Pengajuan Bon.

---

### 6.2 Modal: Ajukan Bon ATK

**Trigger:** Klik tombol `+ Ajukan Bon ATK`  
**Komponen:** `<x-modal>` standar proyek  
**Ukuran:** `max-w-2xl`  

**Isi Form:**

| Field | Tipe | Validasi |
|---|---|---|
| Keperluan | `<textarea>` | Wajib, min 5 karakter |

**Tabel Daftar Barang yang Diminta (dinamis):**

| Kolom | Keterangan |
|---|---|
| Barang | Pilih dari master barang ATK — **lihat detail di bawah** |
| Jumlah | `<input type="number">` min 1, max ≤ stok barang |
| Aksi | Tombol `[Hapus]` untuk hapus baris |

**Spesifikasi Select Option Barang:**
- ⚠️ **Barang dengan stok = 0 tidak ditampilkan sama sekali** di daftar pilihan.
- Setiap opsi menampilkan **kartu bergambar** bergaya sama dengan list select kendaraan di halaman Sarana > Peminjaman, berisi:
  - Foto barang (jika ada, jika tidak tampilkan placeholder abu-abu)
  - Nama barang
  - Satuan
  - Sisa stok (misal: "Stok: 12 Rim")
- Style kartu: grid/list bergambar yang dapat diklik, bukan dropdown `<select>` biasa.

- **Tombol `[+ Tambah Barang]`** — menambah baris item baru
- Minimal 1 item sebelum bisa submit
- Stok yang ditampilkan harus stok real-time

**Footer Modal:**
- `[Batal]` — tutup modal, form di-reset
- `[Kirim Pengajuan]` — submit form dengan loading state

---

### 6.3 Modal: Detail Bon

**Trigger:** Klik tombol `Detail` pada baris tabel  
**Komponen:** `<x-modal>` standar, ukuran `max-w-xl`  

**Isi:**
- No. Bon, Tanggal Pengajuan, Status, Keperluan
- Tabel detail item: Nama Barang, Satuan, Jumlah Diminta, Jumlah Diberikan (jika sudah diproses)

> ❌ **Catatan Petugas tidak ditampilkan** di modal detail (baik dari sisi pegawai maupun dari sisi petugas).

**Footer Modal:**
- `[Tutup]`

---

### 6.4 Modal: Konfirmasi Pembatalan

**Trigger:** Klik tombol `Batalkan` pada baris  
**Komponen:** `<x-modal>` standar, ukuran `max-w-sm`  
**Isi:** "Apakah Anda yakin ingin membatalkan bon ini?"  
**Footer:** `[Batal]` | `[Ya, Batalkan]` (merah)

---

### 6.5 Halaman "Kelola Bon" (`bona.kelola`)

**Deskripsi:** Halaman untuk Petugas Subbag Umum dan Administrator memproses bon yang masuk dari seluruh pegawai.

#### 6.5.1 Kartu Analitik
4 kartu statistik:
- Total Bon Masuk Bulan Ini
- Menunggu Proses
- Sudah Diproses
- Ditolak

#### 6.5.2 Filter & Pencarian
- **Search:** Cari berdasarkan nama pegawai, no. bon, atau keperluan
- **Filter Status:** Dropdown (Semua, Menunggu, Diproses, Ditolak, Dibatalkan)
- **Filter Tanggal:** Rentang tanggal pengajuan

#### 6.5.3 Tabel Kelola Bon

> ⚡ **Auto-refresh:** Tabel ini harus di-refresh secara otomatis berkala menggunakan `wire:poll.30s` agar bon baru dari pegawai langsung muncul tanpa reload halaman.

| Kolom | Keterangan |
|---|---|
| No. Bon | `BON-YYYYMM-XXXXX` |
| Pegawai | Nama + Seksi/Jabatan |
| Keperluan | Deskripsi singkat |
| Jumlah Item | Total jenis barang |
| Tanggal Pengajuan | Format: `DD MMM YYYY` |
| Status | Badge berwarna |
| Aksi | Tombol-tombol aksi |

**Tombol Aksi per Baris:**

| Tombol | Icon | Warna | Kondisi Tampil |
|---|---|---|---|
| **Detail** | `visibility` | biru | Selalu |
| **Proses** | `check_circle` | hijau/emerald | Hanya jika status `menunggu` |
| **Tolak** | `block` | merah | Hanya jika status `menunggu` |

---

### 6.6 Modal: Proses Bon (Konfirmasi + Input Jumlah Diberikan)

**Trigger:** Klik tombol `Proses` pada baris kelola bon  
**Komponen:** `<x-modal>` standar, ukuran `max-w-xl`  

**Isi:**
- Info bon: No. Bon, Nama Pegawai, Keperluan
- Tabel item dengan **input jumlah diberikan** per item:

| Kolom | Keterangan |
|---|---|
| Nama Barang | Readonly |
| Satuan | Readonly |
| Jumlah Diminta | Readonly |
| Stok Tersedia | Readonly, real-time dari master |
| Jumlah Diberikan | `<input number>` — boleh diubah petugas (misal stok tidak cukup) |

- Sistem mengurangi stok otomatis sesuai `jumlah_diberikan`
- Validasi: `jumlah_diberikan` tidak boleh melebihi stok tersedia

**Footer:**
- `[Batal]`
- `[Konfirmasi & Proses]` (emerald) — ubah status ke `diproses`, kurangi stok

---

### 6.7 Modal: Tolak Bon

**Trigger:** Klik tombol `Tolak`  
**Komponen:** `<x-modal>` standar, ukuran `max-w-sm`  

**Isi:**
- "Apakah Anda yakin ingin menolak bon ini?"
- Field `<textarea>` — Catatan/alasan penolakan (wajib diisi, disimpan di kolom `catatan_petugas` untuk keperluan internal/log, namun tidak ditampilkan ke pegawai)

**Footer:**
- `[Batal]`
- `[Tolak Bon]` (merah)

---

### 6.8 Halaman Master Barang ATK (`bona.master-barang`)

**Akses:** Subbag Umum & Administrator

#### Tabel Master Barang

> ⚡ **Auto-refresh:** Tabel ini harus di-refresh secara otomatis berkala menggunakan `wire:poll.30s`.

| Kolom | Keterangan |
|---|---|
| Foto | Thumbnail foto barang (atau ikon placeholder jika belum ada foto) |
| Kode Barang | `ATK000001` |
| Nama Barang | Nama lengkap item |
| Satuan | Dari master satuan |
| Stok | Jumlah stok saat ini + badge warna (merah jika ≤ stok minimum) |
| Stok Minimum | Batas peringatan |
| Aksi | Edit, Tambah Stok |

**Tombol Utama:**
- `[+ Tambah Barang]` — buka modal tambah barang

**Tombol Aksi per Baris:**

| Tombol | Icon | Warna |
|---|---|---|
| **Edit** | `edit` | amber |
| **Tambah Stok** | `add_box` | biru |

---

### 6.9 Modal: Tambah / Edit Barang ATK

**Isi Form:**

| Field | Tipe | Validasi |
|---|---|---|
| Nama Barang | `<input text>` | Wajib, maks 100 karakter |
| Satuan | `<select>` dari master satuan | Wajib |
| Stok Awal (hanya Tambah) | `<input number>` | Wajib, min 0 |
| Stok Minimum | `<input number>` | Wajib, min 0 |
| Foto Barang | `<input file>` — upload gambar (JPG/PNG/WEBP, maks 2MB) | Opsional saat tambah; pada Edit tersedia tombol **[Upload Ulang Gambar]** untuk mengganti foto lama |

**Ketentuan Upload Foto:**
- File tersimpan di `storage/app/public/bon-items/`
- Dapat diakses via `public/storage/bon-items/`
- Saat Edit: foto lama dihapus dari storage jika diganti dengan foto baru
- Preview foto ditampilkan di atas input file (jika sudah ada foto)

---

### 6.10 Modal: Tambah Stok Barang

**Trigger:** Klik tombol `Tambah Stok`  
**Isi:**
- Nama Barang (readonly)
- Foto barang (tampil kecil sebagai identifikasi visual)
- Stok saat ini (readonly)
- Field: **Jumlah Penambahan** (`input number`, min 1)
- Keterangan / sumber stok (`input text`, opsional)

---

### 6.11 Halaman Master Satuan (`bona.master-satuan`)

**Akses:** Subbag Umum & Administrator

#### Tabel Satuan

| Kolom | Keterangan |
|---|---|
| No | Urutan |
| Nama Satuan | PCS, BOX, RIM, dll |
| Aksi | Edit, Hapus |

**Tombol:** `[+ Tambah Satuan]`  
**Modal Tambah/Edit:** Field tunggal: Nama Satuan  
**Hapus:** Konfirmasi modal, tidak bisa hapus jika satuan masih dipakai oleh barang

---

## 7. Fitur Export Laporan

### 7.1 Export Excel Laporan Bon ATK

Tersedia di halaman **Kelola Bon**, berupa tombol `[Export Excel]` dengan filter:
- Periode Awal (bulan & tahun)
- Periode Akhir (bulan & tahun)
- Status (opsional, bisa semua)

**Kolom Excel:**

| No | No. Bon | Nama Pegawai | Seksi | Keperluan | Tanggal Pengajuan | Status | Diproses Oleh | Tanggal Diproses |
|---|---|---|---|---|---|---|---|---|

**Sheet kedua (Detail Item):** Daftar seluruh item dari bon yang terfilter.

---

## 8. Navigasi & Integrasi Menu

### Menu di `admin-topbar.blade.php` & `admin-sidebar.blade.php`

Tambahkan menu **BONA** sebagai dropdown ketiga setelah Sarana dan Permisi:

```
[Sarana ▼]   [Permisi ▼]   [Bona ▼]   [Pegawai]
                                 │
                          ┌──────┴──────────┐
                          │  Bon Saya        │  ← Seluruh pegawai (kecuali Kakan)
                          │  Kelola Bon      │  ← Subbag Umum + Admin
                          │  Master Barang   │  ← Subbag Umum + Admin
                          │  Master Satuan   │  ← Subbag Umum + Admin
                          └─────────────────┘
```

> Setiap sub-menu hanya ditampilkan jika user memiliki hak akses yang sesuai.

---

## 9. Aturan Bisnis Penting

1. **Stok tidak boleh negatif:** Validasi di backend, jumlah yang diberikan tidak boleh melebihi stok saat ini.
2. **Stok terpotong saat diproses:** Pengurangan stok hanya terjadi saat status berubah menjadi `diproses`, bukan saat pengajuan.
3. **Bon yang sudah diproses tidak bisa diubah atau dibatalkan** oleh siapapun.
4. **Bon yang ditolak/dibatalkan tidak memotong stok.**
5. **Pegawai hanya bisa membatalkan bon milik sendiri yang masih berstatus `menunggu`.**
6. **Alert stok minimum:** Tampilkan indikator visual (badge merah) di tabel master barang jika stok ≤ stok minimum. Tidak ada notifikasi email.
7. **Snapshot barang:** Nama barang dan satuan disimpan di `bon_request_items` sehingga perubahan master tidak mengubah histori bon lama.
8. **Nomor bon otomatis:** Format `BON-YYYYMM-XXXXX` di-generate oleh sistem, bukan manual.
9. **Barang stok 0 tersembunyi:** Pada form pengajuan bon, barang dengan stok = 0 tidak muncul di pilihan barang sama sekali.
10. **Foto barang:** Foto disimpan di `storage/public/bon-items/`. Jika tidak ada foto, tampilkan placeholder bergaya abu-abu dengan ikon gambar.

---

## 10. Keterkaitan dengan Sistem Existing

| Aspek | Penanganan |
|---|---|
| **Autentikasi** | Menggunakan sistem login SARANA yang sudah ada (tabel `users`) |
| **Hak Akses** | Menambahkan method baru di `User.php`: `canAccessBonPermohonan()`, `canAccessBonKelola()`, `canAccessBonMaster()` |
| **Komponen Modal** | Menggunakan `<x-modal>` standar proyek |
| **Navigasi** | Menambah entry menu di `admin-topbar.blade.php`, `admin-sidebar.blade.php`, `layouts/admin.blade.php` |
| **Layout** | Menggunakan `#[Layout('components.layouts.admin')]` |
| **Export Excel** | Menggunakan package `maatwebsite/excel` yang sudah terinstall |
| **File Upload** | Menggunakan `Livewire\WithFileUploads` — pola sama seperti upload foto kendaraan di Fleet |
| **Data Awal** | Data barang ATK diimpor dari database `651 BONA` asli sebagai seeder |
| **Auto-refresh** | Menggunakan `wire:poll.30s` pada tabel Bon Saya, Kelola Bon, dan Master Barang |
