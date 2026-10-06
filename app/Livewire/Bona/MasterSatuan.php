<?php

namespace App\Livewire\Bona;

use App\Models\BonSatuan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class MasterSatuan extends Component
{
    public bool $showTambahModal = false;
    public bool $showEditModal = false;
    public bool $showHapusModal = false;

    public string $namaSatuan = '';
    public ?int $editingId = null;
    public ?int $hapusId = null;

    public string $sortColumn = 'nama_satuan';
    public string $sortDirection = 'asc';

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
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
        return BonSatuan::orderBy($this->sortColumn, $this->sortDirection)->get();
    }

    public function openTambah()
    {
        $this->reset(['namaSatuan']);
        $this->showTambahModal = true;
    }

    public function save()
    {
        $this->validate([
            'namaSatuan' => 'required|string|max:50|unique:bon_satuans,nama_satuan',
        ]);

        BonSatuan::create([
            'nama_satuan' => strtoupper($this->namaSatuan),
        ]);

        $this->showTambahModal = false;
        $this->dispatch('close-tambah-modal');
        session()->flash('success', 'Satuan berhasil ditambahkan.');
    }

    public function openEdit($id)
    {
        $satuan = BonSatuan::findOrFail($id);
        $this->editingId = $satuan->id;
        $this->namaSatuan = $satuan->nama_satuan;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'namaSatuan' => 'required|string|max:50|unique:bon_satuans,nama_satuan,' . $this->editingId,
        ]);

        $satuan = BonSatuan::findOrFail($this->editingId);
        $satuan->update([
            'nama_satuan' => strtoupper($this->namaSatuan),
        ]);

        $this->showEditModal = false;
        $this->dispatch('close-edit-modal');
        session()->flash('success', 'Satuan berhasil diperbarui.');
    }

    public function openHapus($id)
    {
        $this->hapusId = $id;
        $this->showHapusModal = true;
    }

    public function hapus()
    {
        $satuan = BonSatuan::withCount('items')->findOrFail($this->hapusId);

        if ($satuan->items_count > 0) {
            $this->showHapusModal = false;
            session()->flash('error', 'Satuan tidak bisa dihapus karena sedang digunakan oleh master barang.');
            return;
        }

        $satuan->delete();
        $this->showHapusModal = false;
        session()->flash('success', 'Satuan berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.bona.master-satuan')->title('Master Satuan ATK');
    }
}
