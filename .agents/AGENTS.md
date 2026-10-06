# PANDUAN WAJIB PENGEMBANGAN & REDESAIN (ZERO-BREAKAGE GUIDELINE)

Dokumen ini adalah pedoman dan regulasi operasional **MUTLAK** yang wajib dipatuhi dalam setiap pengerjaan task, modifikasi kode, dan penataan ulang gaya visual (redesain) pada seluruh modul sistem aplikasi **1panel-sarana**.

---

## 1. Zero-Breakage Guarantee (Preservasi Logika & Fungsionalitas)
1. **Dilarang Menghapus atau Mengubah Directives Livewire**:
   - Atribut seperti `wire:click`, `wire:model`, `wire:model.live`, `wire:target`, `wire:loading`, `wire:submit`, `@click`, `@change`, dan pemanggilan `$wire` adalah pondasi fungsional aplikasi. TIDAK BOLEH dihilangkan atau diubah nama method-nya tanpa instruksi khusus.
   - Parameter pemanggilan (misal `wire:click="editBooking({{ $row->id }})"`, `wire:click="openConfirm('approve', {{ $row->id }})"`) harus dipreservasi dengan presisi 100%.
2. **Preservasi Logika Blade & Otorisasi**:
   - Seluruh blok kondisional otorisasi (`@if($isReviewerMode)`, `@if(auth()->user()->role === 'admin')`, `@can`, dll.) wajib dipertahankan.
   - Penanganan validasi error (`@error('fieldName') ... @enderror`) wajib ada pada setiap input form.
   - Perulangan data (`@foreach`, `@forelse ... @empty ... @endforelse`) dan penomoran iterasi harus tetap berfungsi normal.
3. **Modal & Interaktivitas (No Background Dismissal)**:
   - Pembukaan modal harus responsif (Optimistic UI dengan Alpine.js).
   - **Dilarang Menutup Modal Saat Backdrop Diklik**: Atribut `@click.outside` pada modal dialog container dilarang. Modal hanya boleh ditutup melalui tombol aksi eksplisit (ikon X di pojok kanan atas, tombol Batal, atau setelah submit sukses).
4. **Navigasi Single Page Application (SPA)**:
   - Seluruh tautan navigasi internal menu wajib menggunakan `wire:navigate.hover`.

---

## 2. Standar Desain Visual (Berdasarkan `sarana_redesain.html`)
1. **Tipografi Modern**:
   - Seluruh elemen antarmuka menggunakan font **Plus Jakarta Sans** (`font-sans`).
   - Ukuran teks proporsional dan jelas: judul card `text-lg font-bold text-slate-900`, label form `text-xs font-semibold uppercase tracking-wider text-slate-500`.
2. **Palet Warna Harmonis & Elegan**:
   - **Primary Brand (Blue)**:
     - Utama: `bg-brand-600` (`#2563eb`), hover: `hover:bg-brand-700` (`#1d4ed8`), text: `text-white font-medium`.
     - Shadow tombol primary: `shadow-sm shadow-brand-600/30`.
     - Permukaan aktif / soft: `bg-brand-50` (`#eff6ff`), border: `border-brand-200`, text: `text-brand-700`.
   - **Secondary / Action Netral**:
     - Tombol Batal / Secondary: `bg-white border border-slate-200 text-slate-700 hover:bg-slate-50`.
     - Dark Slate (Kontras): `bg-slate-800 hover:bg-slate-900 text-white`.
   - **Status Indicators (Pills & Badges)**:
     - Menunggu / Pending: `bg-amber-50 text-amber-700 border border-amber-200/60 font-medium`.
     - Disetujui / Selesai: `bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-medium`.
     - Ditolak / Batal: `bg-rose-50 text-rose-700 border border-rose-200/60 font-medium`.
     - Proses / Aktif: `bg-blue-50 text-blue-700 border border-blue-200/60 font-medium`.
3. **Sudut Kelengkungan (Border Radius)**:
   - Kontainer Utama (Card Wrapper, Table Container, Modal): **`rounded-3xl` (24px)**.
   - Inner Cards, Grid Items, Kartu Armada/Katalog: **`rounded-2xl` (16px)**.
   - Input Fields, Select, Dropdown, Tombol Standar: **`rounded-xl` (12px)**.
   - Badge Status: **`rounded-full`** atau **`rounded-lg`**.
4. **Elevasi & Latar Belakang**:
   - Kontainer utama wajib menggunakan bayangan halus: `shadow-card` (atau `shadow-sm border border-slate-200/80 bg-white`).
   - Latar belakang halaman aplikasi menggunakan `bg-slate-50/70` dipadukan dengan aksen dot halus `bg-grid-subtle`.
   - Scrollbar custom minimalis 6px tipis (`#cbd5e1`).

---

## 3. Protokol Eksekusi Bertahap (Step-by-Step Delivery)
1. **Pengerjaan Terisolasi per Modul**:
   - Redesain dikerjakan per modul sesuai urutan fase:
     - Fase 1: Fondasi Theme, Font & Sleek Navbar
     - Fase 2: Modul Sarana (Peminjaman & Persetujuan Armada)
     - Fase 3: Modul Permisi (Izin Keluar Kantor)
     - Fase 4: Modul Asmara (Persuratan & Nomor Kahar)
     - Fase 5: Modul Bon ATK (Katalog, Keranjang & Master Data)
     - Fase 6: Modul Admin & Login
     - Fase 7: Final Verification & Audit
2. **Uji Validasi Wajib pada Setiap Akhir Fase**:
   - Jalankan build aset: `npm run build`.
   - Bersihkan cache view: `php artisan view:clear`.
   - Jalankan verifikasi visual / browser inspeksi untuk memastikan tidak ada layout broken atau class syntax error.
3. **Integritas Backup**:
   - Salinan cadangan view asli di `resources/views_backup_redesain` tidak boleh dihapus atau ditimpa sampai seluruh proyek terverifikasi tuntas.
