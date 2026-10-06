<?php

namespace App\Livewire\Bona;

use App\Models\BonItem;
use App\Models\BonSatuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Exports\Bona\MasterBarangExport;
use App\Imports\Bona\MasterBarangImport;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class MasterBarang extends Component
{
    use WithFileUploads, WithPagination;

    public bool $showTambahModal = false;
    public bool $showEditModal = false;
    public bool $showTambahStokModal = false;
    public bool $showSatuanModal = false;
    public bool $showImportModal = false;

    // File Import
    public $importFile = null;

    // Satuan CRUD
    public string $namaSatuan = '';
    public ?int $editingSatuanId = null;
    public bool $showHapusSatuanConfirm = false;
    public ?int $hapusSatuanId = null;

    // Form Tambah/Edit
    public string $namaBarang = '';
    public $satuanId = '';
    public int $stokAwal = 0;
    public int $stokMinimum = 5;
    public int $stokSaatIni = 0;
    
    public $photo = null;
    public $editPhoto = null;

    // State Edit
    public ?int $editingId = null;

    // State Tambah Stok
    public ?int $tambahStokId = null;
    public int $jumlahTambah = 0;
    public string $keteranganTambah = '';

    // Filter
    public string $search = '';
    public bool $filterStokMenipis = false;

    public function updatedFilterStokMenipis()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (!Auth::user()->canAccessBonMaster()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    #[Computed]
    public function satuans()
    {
        return BonSatuan::orderBy('nama_satuan', 'asc')->get();
    }

    #[Computed]
    public function bonItems()
    {
        return BonItem::with('satuan')
            ->when($this->search, function ($query) {
                $query->where('nama_barang', 'like', '%' . $this->search . '%')
                      ->orWhere('kode_barang', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStokMenipis, function ($query) {
                $query->whereColumn('stok', '<=', 'stok_minimum');
            })
            ->orderBy('id', 'desc')
            ->paginate(15);
    }

    public function openTambah()
    {
        $this->reset(['namaBarang', 'satuanId', 'stokAwal', 'stokMinimum', 'photo']);
        $this->showTambahModal = true;
    }

    private function generateKodeBarang(): string
    {
        $maxNum = 0;
        $items = BonItem::select('kode_barang')->get();
        foreach ($items as $item) {
            if (preg_match('/(\d+)/', $item->kode_barang, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        do {
            $maxNum++;
            $candidate = 'ATK' . str_pad($maxNum, 6, '0', STR_PAD_LEFT);
        } while (BonItem::where('kode_barang', $candidate)->exists());

        return $candidate;
    }

    public function save()
    {
        $this->validate([
            'namaBarang' => 'required|string|max:100',
            'satuanId' => 'required|exists:bon_satuans,id',
            'stokAwal' => 'required|integer|min:0',
            'stokMinimum' => 'required|integer|min:0',
            'photo' => 'nullable|image|max:2048', // maks 2MB
        ]);

        $imagePath = null;
        if ($this->photo) {
            $imagePath = $this->photo->store('bon-items', 'public');
        }

        BonItem::create([
            'kode_barang' => $this->generateKodeBarang(),
            'nama_barang' => $this->namaBarang,
            'satuan_id' => $this->satuanId,
            'stok' => $this->stokAwal,
            'stok_minimum' => $this->stokMinimum,
            'image_path' => $imagePath,
            'created_by' => Auth::user()->id,
        ]);

        $this->showTambahModal = false;
        $this->dispatch('close-tambah-modal');
        session()->flash('success', 'Barang ATK berhasil ditambahkan.');
    }

    public function openEdit($id)
    {
        $item = BonItem::findOrFail($id);
        
        $this->editingId = $item->id;
        $this->namaBarang = $item->nama_barang;
        $this->satuanId = $item->satuan_id;
        $this->stokMinimum = $item->stok_minimum;
        $this->stokSaatIni = $item->stok;
        $this->reset(['editPhoto']);
        
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'namaBarang' => 'required|string|max:100',
            'satuanId' => 'required|exists:bon_satuans,id',
            'stokMinimum' => 'required|integer|min:0',
            'stokSaatIni' => 'required|integer|min:0',
            'editPhoto' => 'nullable|image|max:2048',
        ]);

        $item = BonItem::findOrFail($this->editingId);
        
        $imagePath = $item->image_path;
        
        if ($this->editPhoto) {
            // Hapus foto lama jika ada
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->editPhoto->store('bon-items', 'public');
        }

        $item->update([
            'nama_barang' => $this->namaBarang,
            'satuan_id' => $this->satuanId,
            'stok_minimum' => $this->stokMinimum,
            'stok' => $this->stokSaatIni,
            'image_path' => $imagePath,
        ]);

        $this->showEditModal = false;
        $this->dispatch('close-edit-modal');
        session()->flash('success', 'Barang ATK berhasil diperbarui.');
    }

    public function openTambahStok($id)
    {
        $item = BonItem::findOrFail($id);
        $this->tambahStokId = $item->id;
        $this->reset(['jumlahTambah', 'keteranganTambah']);
        $this->showTambahStokModal = true;
    }

    public function tambahStok()
    {
        $this->validate([
            'jumlahTambah' => 'required|integer|min:1',
            'keteranganTambah' => 'nullable|string|max:255',
        ]);

        $item = BonItem::findOrFail($this->tambahStokId);
        $item->increment('stok', $this->jumlahTambah);

        // Catatan: Jika ada tabel histori pergerakan stok, simpan $this->keteranganTambah di sini
        
        $this->showTambahStokModal = false;
        $this->dispatch('close-tambah-stok-modal');
        session()->flash('success', 'Stok barang berhasil ditambah sebanyak ' . $this->jumlahTambah);
    }

    // ── Satuan CRUD ──────────────────────────────────────────

    public function openSatuanModal()
    {
        $this->reset(['namaSatuan', 'editingSatuanId', 'showHapusSatuanConfirm', 'hapusSatuanId']);
        $this->showSatuanModal = true;
    }

    public function saveSatuan()
    {
        $this->validate([
            'namaSatuan' => 'required|string|max:50|unique:bon_satuans,nama_satuan' . ($this->editingSatuanId ? ',' . $this->editingSatuanId : ''),
        ]);

        if ($this->editingSatuanId) {
            $satuan = BonSatuan::findOrFail($this->editingSatuanId);
            $satuan->update(['nama_satuan' => strtoupper($this->namaSatuan)]);
        } else {
            BonSatuan::create(['nama_satuan' => strtoupper($this->namaSatuan)]);
        }

        $this->reset(['namaSatuan', 'editingSatuanId']);
        unset($this->satuans);
    }

    public function editSatuan($id)
    {
        $satuan = BonSatuan::findOrFail($id);
        $this->editingSatuanId = $satuan->id;
        $this->namaSatuan = $satuan->nama_satuan;
    }

    public function cancelEditSatuan()
    {
        $this->reset(['namaSatuan', 'editingSatuanId']);
    }

    public function confirmHapusSatuan($id)
    {
        $this->hapusSatuanId = $id;
        $this->showHapusSatuanConfirm = true;
    }

    public function hapusSatuan()
    {
        $satuan = BonSatuan::withCount('items')->findOrFail($this->hapusSatuanId);

        if ($satuan->items_count > 0) {
            $this->showHapusSatuanConfirm = false;
            session()->flash('satuan_error', 'Satuan "' . $satuan->nama_satuan . '" tidak bisa dihapus karena sedang digunakan.');
            return;
        }

        $satuan->delete();
        $this->showHapusSatuanConfirm = false;
        $this->reset(['hapusSatuanId']);
        unset($this->satuans);
    }

    public function downloadMasterBarang()
    {
        return Excel::download(new MasterBarangExport, 'Master_Barang_' . date('Ymd_His') . '.xlsx');
    }

    public function importMasterBarang()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'importFile.required' => 'Pilih file Excel terlebih dahulu.',
            'importFile.mimes' => 'Format file harus berupa xlsx, xls, atau csv.',
            'importFile.max' => 'Ukuran file maksimal 10MB.'
        ]);

        try {
            $userId = Auth::user()?->id ?? \App\Models\User::first()?->id ?? 1;
            Excel::import(new MasterBarangImport($userId), $this->importFile);

            $this->showImportModal = false;
            $this->reset('importFile');
            $this->dispatch('close-import-modal');
            session()->flash('success', 'Data Master Barang berhasil diimport.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.bona.master-barang')->title('Master Barang ATK');
    }
}
