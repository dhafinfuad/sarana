# flow-brief — BONA (Bon ATK)
## Technical Flow Brief untuk Implementasi di SARANA (Laravel + Livewire + Alpine.js)

**Referensi PRD:** `BONA_PRD.md`  
**Versi:** 1.0

---

## Arsitektur Umum

```
routes/web.php
    bona.permohonan   → Livewire\Bona\Permohonan
    bona.kelola       → Livewire\Bona\Kelola
    bona.master-barang → Livewire\Bona\MasterBarang
    bona.master-satuan → Livewire\Bona\MasterSatuan

app/Livewire/Bona/
    Permohonan.php
    Kelola.php
    MasterBarang.php
    MasterSatuan.php

app/Models/
    BonItem.php          (master barang ATK)
    BonSatuan.php        (master satuan)
    BonRequest.php       (header bon)
    BonRequestItem.php   (detail item bon)

app/Exports/
    BonExport.php        (export Excel laporan)

resources/views/livewire/bona/
    permohonan.blade.php
    kelola.blade.php
    master-barang.blade.php
    master-satuan.blade.php
```

---

## Database Migrations

### Migration 1: `create_bon_satuans_table`
```php
Schema::create('bon_satuans', function (Blueprint $table) {
    $table->id();
    $table->string('nama_satuan', 50)->unique();
    $table->timestamps();
});
```

### Migration 2: `create_bon_items_table`
```php
Schema::create('bon_items', function (Blueprint $table) {
    $table->id();
    $table->string('kode_barang', 30)->unique();
    $table->string('nama_barang', 100);
    $table->foreignId('satuan_id')->constrained('bon_satuans');
    $table->integer('stok')->default(0);
    $table->integer('stok_minimum')->default(5);
    $table->string('image_path')->nullable(); // Foto barang, disimpan di storage/public/bon-items/
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
});
```

### Migration 3: `create_bon_requests_table`
```php
Schema::create('bon_requests', function (Blueprint $table) {
    $table->id();
    $table->string('no_bon', 25)->unique();
    $table->foreignId('user_id')->constrained('users');
    $table->text('keperluan');
    $table->enum('status', ['menunggu', 'diproses', 'ditolak', 'dibatalkan'])->default('menunggu');
    $table->text('catatan_petugas')->nullable();
    $table->foreignId('processed_by')->nullable()->constrained('users');
    $table->timestamp('processed_at')->nullable();
    $table->timestamps();
});
```

### Migration 4: `create_bon_request_items_table`
```php
Schema::create('bon_request_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('bon_request_id')->constrained('bon_requests')->cascadeOnDelete();
    $table->foreignId('bon_item_id')->constrained('bon_items');
    $table->string('nama_barang', 100);  // snapshot
    $table->string('satuan', 50);        // snapshot
    $table->integer('jumlah_diminta');
    $table->integer('jumlah_diberikan')->nullable();
    $table->timestamps();
});
```

### Seeder: `BonSatuanSeeder`
Isi dengan data dari `satuan` di 651 BONA: PCS, BOX, RIM, LUSIN, dll.

### Seeder: `BonItemSeeder`
Import data barang ATK dari database asli 651 BONA (`data_barang`).

---

## Models

### BonRequest — Enum Status & Relations
```php
class BonRequest extends Model {
    protected $fillable = [
        'no_bon', 'user_id', 'keperluan', 'status',
        'catatan_petugas', 'processed_by', 'processed_at'
    ];
    protected $casts = ['processed_at' => 'datetime'];

    public function user(): BelongsTo { ... }
    public function processedBy(): BelongsTo { ... }
    public function items(): HasMany { return $this->hasMany(BonRequestItem::class); }

    public static function generateNoBon(): string {
        // Format: BON-YYYYMM-XXXXX
    }
    public function isMenunggu(): bool { return $this->status === 'menunggu'; }
}
```

---

## FLOW 1 — Halaman "Bon Saya" (Permohonan)

**Livewire Component:** `App\Livewire\Bona\Permohonan`  
**Route:** `bona.permohonan`  
**View:** `livewire/bona/permohonan.blade.php`  
**Layout:** `components.layouts.admin`

### Properties
```php
// State modal
public bool $showAjukanModal = false;
public bool $showDetailModal = false;
public bool $showBatalkanModal = false;

// Form Pengajuan
public string $keperluan = '';
public array $daftarItem = []; // [{bon_item_id, jumlah_diminta}]

// State Detail & Batalkan
public ?int $viewingId = null;
public ?int $batalkanId = null;

// Filter & Search
public string $search = '';
public string $status = '';
public string $sortField = 'created_at';
public string $sortDirection = 'desc';
```

### Methods

| Method | Deskripsi |
|---|---|
| `mount()` | Cek hak akses `canAccessBonPermohonan()`, abort(403) jika tidak |
| `analytics()` [Computed] | Hitung 4 kartu statistik (milik sendiri) |
| `bonRequests()` [Computed] | Query paginated bon milik user yang login, dengan filter |
| `availableItems()` [Computed] | Daftar barang ATK untuk select option (stok > 0) |
| `openAjukan()` | Reset form, `showAjukanModal = true` |
| `addItem()` | Tambah baris item ke `daftarItem` |
| `removeItem(int $index)` | Hapus baris item dari `daftarItem` |
| `submitAjukan()` | Validasi, simpan `BonRequest` + `BonRequestItem[]`, dispatch `close-ajukan-modal` |
| `openDetail(int $id)` | Load data bon, `showDetailModal = true` |
| `openBatalkan(int $id)` | Set `batalkanId`, `showBatalkanModal = true` |
| `batalkan()` | Cek ownership & status `menunggu`, update status ke `dibatalkan` |
| `sortBy(string $field)` | Toggle sorting |

### Validasi `submitAjukan()`
```php
$this->validate([
    'keperluan'                         => 'required|string|min:5',
    'daftarItem'                        => 'required|array|min:1',
    'daftarItem.*.bon_item_id'          => 'required|exists:bon_items,id',
    'daftarItem.*.jumlah_diminta'       => 'required|integer|min:1',
]);
// Tambahan: cek stok per item
foreach ($this->daftarItem as $item) {
    $bonItem = BonItem::find($item['bon_item_id']);
    if ($item['jumlah_diminta'] > $bonItem->stok) {
        $this->addError("stok_{$item['bon_item_id']}", "Stok {$bonItem->nama_barang} tidak mencukupi.");
    }
}
```

### Logika `submitAjukan()`
```php
// Generate nomor bon
$noBon = BonRequest::generateNoBon();

// Simpan header
$bon = BonRequest::create([...]);

// Simpan item (dengan snapshot nama & satuan)
foreach ($this->daftarItem as $item) {
    $bonItem = BonItem::find($item['bon_item_id']);
    $bon->items()->create([
        'bon_item_id'    => $item['bon_item_id'],
        'nama_barang'    => $bonItem->nama_barang, // snapshot
        'satuan'         => $bonItem->satuan->nama_satuan, // snapshot
        'jumlah_diminta' => $item['jumlah_diminta'],
    ]);
}

$this->dispatch('close-ajukan-modal');
session()->flash('success', 'Bon ATK berhasil diajukan.');
```

### View Highlights
- Alpine state: `showAjukanModal`, `showDetailModal`, `showBatalkanModal`
- Setiap state modal harus dibuka via `@click` Alpine, bukan langsung Livewire
- Tabel item di modal pengajuan: dirender dari `$daftarItem` dengan `wire:model`
- Select barang ditampilkan sebagai **kartu bergambar** (bukan `<select>` biasa), gaya sama dengan list kendaraan di Sarana. Barang dengan stok = 0 **tidak ditampilkan**.
- **Tabel Riwayat Bon:** gunakan `wire:poll.30s` pada wrapper tabel agar auto-refresh setiap 30 detik

---

## FLOW 2 — Halaman "Kelola Bon" (Manajemen)

**Livewire Component:** `App\Livewire\Bona\Kelola`  
**Route:** `bona.kelola`  
**View:** `livewire/bona/kelola.blade.php`

### Properties
```php
public bool $showDetailModal = false;
public bool $showProsesModal = false;
public bool $showTolakModal = false;

// State
public ?int $viewingId = null;
public ?int $prosesId = null;
public ?int $tolakId = null;

// Form Tolak
public string $catatanPetugas = '';

// Form Proses (jumlah diberikan per item)
public array $jumlahDiberikan = []; // [bon_request_item_id => jumlah]

// Filter
public string $search = '';
public string $status = '';
public string $startDate = '';
public string $endDate = '';
public string $sortField = 'created_at';
public string $sortDirection = 'desc';

// Export
public string $exportStartMonth = '';
public string $exportStartYear = '';
public string $exportEndMonth = '';
public string $exportEndYear = '';
```

### Methods

| Method | Deskripsi |
|---|---|
| `mount()` | Cek `canAccessBonKelola()`, abort(403) jika tidak |
| `analytics()` [Computed] | Statistik seluruh bon (semua pegawai) |
| `bonRequests()` [Computed] | Query paginated seluruh bon dengan filter & sort |
| `openDetail(int $id)` | Load bon, `showDetailModal = true` |
| `openProses(int $id)` | Load bon + items, isi `jumlahDiberikan[]` dengan nilai default dari `jumlah_diminta`, `showProsesModal = true` |
| `konfirmasiProses()` | Validasi, update status bon ke `diproses`, kurangi stok, catat `processed_by` & `processed_at` |
| `openTolak(int $id)` | Set `tolakId`, reset `catatanPetugas`, `showTolakModal = true` |
| `tolakBon()` | Validasi catatan wajib, update status ke `ditolak` |
| `exportExcel()` | Return `Excel::download(new BonExport(...))` |
| `sortBy(string $field)` | Toggle sorting |

### Logika `konfirmasiProses()`
```php
$bon = BonRequest::with('items.bonItem')->findOrFail($this->prosesId);

// Validasi stok mencukupi
foreach ($bon->items as $item) {
    $diberikan = $this->jumlahDiberikan[$item->id] ?? 0;
    if ($diberikan > $item->bonItem->stok) {
        $this->addError("jumlah_{$item->id}", "Stok tidak mencukupi.");
        return;
    }
}

// Update dalam transaksi database
DB::transaction(function () use ($bon) {
    foreach ($bon->items as $item) {
        $diberikan = $this->jumlahDiberikan[$item->id] ?? 0;
        $item->update(['jumlah_diberikan' => $diberikan]);
        $item->bonItem->decrement('stok', $diberikan);
    }
    $bon->update([
        'status'       => 'diproses',
        'processed_by' => Auth::id(),
        'processed_at' => now(),
    ]);
});

$this->dispatch('close-proses-modal');
session()->flash('success', 'Bon berhasil diproses dan stok telah dikurangi.');
```

---

## FLOW 3 — Halaman Master Barang ATK

**Livewire Component:** `App\Livewire\Bona\MasterBarang`  
**Route:** `bona.master-barang`

### Properties
```php
use Livewire\WithFileUploads; // wajib di-use

public bool $showTambahModal = false;
public bool $showEditModal = false;
public bool $showTambahStokModal = false;

// Form
public string $namaBarang = '';
public int $satuanId = 0;
public int $stokAwal = 0;
public int $stokMinimum = 5;
public $photo = null;       // upload saat Tambah
public $editPhoto = null;   // upload saat Edit (opsional)

// Edit
public ?int $editingId = null;
// (namaBarang, satuanId, stokMinimum dipakai ulang)

// Tambah Stok
public ?int $tambahStokId = null;
public int $jumlahTambah = 0;
public string $keteranganTambah = '';

// Filter & Search
public string $search = '';
```

### Methods

| Method | Deskripsi |
|---|---|
| `mount()` | Cek `canAccessBonMaster()` |
| `satuans()` [Computed] | Daftar satuan untuk select option |
| `bonItems()` [Computed] | Query paginated barang dengan search |
| `openTambah()` | Reset form + photo, `showTambahModal = true` |
| `save()` | Validasi, generate kode barang otomatis, simpan + upload foto ke `bon-items/` jika ada |
| `openEdit(int $id)` | Load data, reset `editPhoto = null`, `showEditModal = true` |
| `update()` | Validasi, update barang; jika `editPhoto` ada: hapus foto lama dari storage, simpan foto baru |
| `openTambahStok(int $id)` | Set `tambahStokId`, reset field, `showTambahStokModal = true` |
| `tambahStok()` | Validasi, increment stok barang |

### Generate Kode Barang
```php
private function generateKodeBarang(): string {
    $last = BonItem::orderBy('kode_barang', 'desc')->first();
    $lastNum = $last ? (int) substr($last->kode_barang, 3) : 0;
    return 'ATK' . str_pad($lastNum + 1, 6, '0', STR_PAD_LEFT);
}

// Validasi upload foto (digunakan di save() dan update())
private function validatePhoto(string $field): void {
    $this->validateOnly($field, [
        $field => ['nullable', 'image', 'max:2048'],
    ]);
}

// Upload foto ke storage dan kembalikan path-nya
private function uploadPhoto($photo): string {
    return $photo->store('bon-items', 'public');
    // Hasil: 'bon-items/namafile.jpg'
    // Akses via: Storage::url($path) → /storage/bon-items/namafile.jpg
}
```

---

## FLOW 4 — Halaman Master Satuan

**Livewire Component:** `App\Livewire\Bona\MasterSatuan`  
**Route:** `bona.master-satuan`

### Properties
```php
public bool $showTambahModal = false;
public bool $showEditModal = false;
public bool $showHapusModal = false;

public string $namaSatuan = '';
public ?int $editingId = null;
public ?int $hapusId = null;
```

### Methods

| Method | Deskripsi |
|---|---|
| `mount()` | Cek `canAccessBonMaster()` |
| `satuans()` [Computed] | Semua satuan |
| `save()` | Validasi unique, simpan |
| `openEdit(int $id)` | Load satuan |
| `update()` | Validasi unique kecuali diri sendiri |
| `openHapus(int $id)` | Set `hapusId` |
| `hapus()` | Cek tidak dipakai oleh `bon_items`, hapus jika aman |

---

## User Model — Method Baru di `User.php`

```php
public function canAccessBonPermohonan(): bool
{
    return !$this->isKepalaKantor();
}

public function canAccessBonKelola(): bool
{
    return $this->isSubbagUmum() || $this->isAdministrator();
}

public function canAccessBonMaster(): bool
{
    return $this->isSubbagUmum() || $this->isAdministrator();
}
```

---

## Route Definitions (`routes/web.php`)

```php
// BONA — Bon ATK
Route::middleware(['auth'])->group(function () {
    Route::get('/bona/permohonan', \App\Livewire\Bona\Permohonan::class)
        ->name('bona.permohonan');

    Route::get('/bona/kelola', \App\Livewire\Bona\Kelola::class)
        ->name('bona.kelola');

    Route::get('/bona/master-barang', \App\Livewire\Bona\MasterBarang::class)
        ->name('bona.master-barang');

    Route::get('/bona/master-satuan', \App\Livewire\Bona\MasterSatuan::class)
        ->name('bona.master-satuan');
});
```

---

## Export Excel — `BonExport.php`

```php
class BonExport implements FromCollection, WithHeadings, WithMapping, WithMultipleSheets {
    public function __construct(
        private int $startMonth, private int $startYear,
        private int $endMonth,   private int $endYear,
        private string $status = ''
    ) {}

    // Sheet 1: Ringkasan bon
    // Sheet 2: Detail item per bon
}
```

---

## Integrasi Menu Navigasi

### `admin-topbar.blade.php`
Tambahkan dropdown **Bona** setelah dropdown Permisi:
```blade
<div x-data="{ open: false }" class="relative" @click.outside="open = false">
    <button @click="open = !open" ...>Bona ▼</button>
    <div x-show="open" ...>
        @if(auth()->user()->canAccessBonPermohonan())
        <a wire:navigate.hover href="{{ route('bona.permohonan') }}">Bon Saya</a>
        @endif
        @if(auth()->user()->canAccessBonKelola())
        <a wire:navigate.hover href="{{ route('bona.kelola') }}">Kelola Bon</a>
        @endif
        @if(auth()->user()->canAccessBonMaster())
        <a wire:navigate.hover href="{{ route('bona.master-barang') }}">Master Barang</a>
        <a wire:navigate.hover href="{{ route('bona.master-satuan') }}">Master Satuan</a>
        @endif
    </div>
</div>
```

### `admin-sidebar.blade.php`
Tambahkan section BONA dengan icon `inventory_2` (Material Symbols).

### `layouts/admin.blade.php` (Bottom Nav Mobile)
Tambahkan tab BONA dengan icon `inventory_2`.

---

## Urutan Implementasi (Step-by-step)

1. **Migration** — Buat 4 migration baru: `bon_satuans`, `bon_items`, `bon_requests`, `bon_request_items`
2. **Seeder** — Seed data satuan dan barang ATK dari database asli 651 BONA
3. **Models** — Buat 4 model Eloquent dengan relasi lengkap
4. **User.php** — Tambahkan 3 method baru hak akses
5. **Routes** — Tambahkan 4 route BONA
6. **Master Satuan** — Livewire + View (paling sederhana, dikerjakan pertama)
7. **Master Barang** — Livewire + View
8. **Halaman Bon Saya** — Livewire + View (paling kompleks karena ada multi-item form)
9. **Halaman Kelola Bon** — Livewire + View (termasuk logika proses stok)
10. **Export Excel** — BonExport class + tombol di Kelola Bon
11. **Navigasi** — Update ketiga komponen navigasi
12. **Hak Akses** — Pastikan setiap halaman dan tombol terlindungi
