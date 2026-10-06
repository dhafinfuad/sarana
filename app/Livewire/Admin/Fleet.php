<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Livewire Component: Admin/Fleet
 * FLOW 4: Manajemen Armada Kendaraan
 *
 * Fitur: CRUD Vehicle, Status Toggling, Upload Foto, Soft-Delete Guard.
 *
 * @see flow-brief.md §FLOW 4
 * @see skills-livewire.md
 */
#[Layout('components.layouts.admin')]
class Fleet extends Component
{
    use WithFileUploads;
    use WithPagination;

    // -----------------------------------------------------------------------
    // Filter Properties
    // -----------------------------------------------------------------------

    public string $search = '';

    // -----------------------------------------------------------------------
    // Modal State (di-entangle dengan Alpine.js di view)
    // -----------------------------------------------------------------------

    public bool $showAddModal    = false;
    public bool $showEditModal   = false;
    public bool $showDeleteModal = false;

    // -----------------------------------------------------------------------
    // Form Properties
    // -----------------------------------------------------------------------

    /** ID kendaraan yang sedang diedit (null = mode tambah) */
    public ?int $editingId = null;

    /** ID kendaraan yang akan dihapus */
    public ?int $deletingId = null;

    public string $nama_kendaraan = '';
    public string $plat_nomor = '';
    public string $status = 'tersedia';

    /** Temporary uploaded file (Livewire WithFileUploads) */
    public $photo = null;
    public $editPhoto = null;

    // -----------------------------------------------------------------------
    // Validation Rules (skills-livewire.md §1 — backend validation ketat)
    // -----------------------------------------------------------------------

    protected function rules(): array
    {
        $uniqueRule = $this->editingId
            ? 'unique:vehicles,plat_nomor,' . $this->editingId
            : 'unique:vehicles,plat_nomor';

        return [
            'nama_kendaraan' => ['required', 'string', 'min:3', 'max:100'],
            'plat_nomor'     => ['required', 'string', 'max:20', $uniqueRule],
            'status'         => ['required', 'in:tersedia,tidak_tersedia'],
            'photo'          => ['nullable', 'image', 'max:2048'],
            'editPhoto'      => ['nullable', 'image', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nama_kendaraan.required' => 'Nama kendaraan wajib diisi.',
            'nama_kendaraan.min'      => 'Nama kendaraan minimal 3 karakter.',
            'nama_kendaraan.max'      => 'Nama kendaraan maksimal 100 karakter.',
            'plat_nomor.required'     => 'Plat nomor wajib diisi.',
            'plat_nomor.max'          => 'Plat nomor maksimal 20 karakter.',
            'plat_nomor.unique'       => 'Plat nomor ini sudah terdaftar di sistem.',
            'status.required'         => 'Status kendaraan wajib dipilih.',
            'status.in'               => 'Nilai status tidak valid.',
            'photo.image'             => 'File harus berupa gambar (JPG, PNG, WEBP).',
            'photo.max'               => 'Ukuran foto maksimal 2MB.',
            'editPhoto.image'         => 'File harus berupa gambar (JPG, PNG, WEBP).',
            'editPhoto.max'           => 'Ukuran foto maksimal 2MB.',
        ];
    }

    // -----------------------------------------------------------------------
    // Lifecycle Hooks
    // -----------------------------------------------------------------------

    public function updated(string $property): void
    {
        if (in_array($property, ['search'])) {
            $this->resetPage();
        }
    }

    // -----------------------------------------------------------------------
    // Modal Actions
    // -----------------------------------------------------------------------

    public function openAdd(): void
    {
        $this->resetForm();
        $this->showAddModal = true;
    }

    public function openEdit(int $id): void
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->editingId      = $vehicle->id;
        $this->nama_kendaraan = $vehicle->nama_kendaraan;
        $this->plat_nomor     = $vehicle->plat_nomor;
        $this->status         = $vehicle->status->value;
        $this->photo          = null;
        $this->editPhoto      = null;
        $this->showEditModal  = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    // -----------------------------------------------------------------------
    // CRUD Actions
    // -----------------------------------------------------------------------

    /**
     * Simpan kendaraan baru ke database.
     * Constraint: plat_nomor UNIQUE, foto opsional maks 2MB.
     */
    public function save(): void
    {
        $this->validate();

        $imagePath = null;
        if ($this->photo) {
            $imagePath = $this->photo->store('vehicles', 'public');
        }

        Vehicle::create([
            'nama_kendaraan' => $this->nama_kendaraan,
            'plat_nomor'     => strtoupper(trim($this->plat_nomor)),
            'status'         => $this->status,
            'image_path'     => $imagePath,
        ]);

        $this->showAddModal = false;
        $this->resetForm();
        session()->flash('success', 'Kendaraan berhasil ditambahkan.');
    }

    /**
     * Update data kendaraan yang sedang diedit.
     * Foto lama hanya dihapus & diganti jika foto baru diupload.
     */
    public function update(): void
    {
        \Log::info('Update called', ['editingId' => $this->editingId, 'editPhoto' => $this->editPhoto ? 'Yes' : 'No']);
        $this->validate();

        $vehicle = Vehicle::findOrFail($this->editingId);

        $imagePath = $vehicle->image_path; // Pertahankan foto lama by default
        if ($this->editPhoto) {
            \Log::info('Edit photo exists, updating image path');
            // Hapus foto lama jika ada
            if ($vehicle->image_path) {
                Storage::disk('public')->delete($vehicle->image_path);
            }
            $imagePath = $this->editPhoto->store('vehicles', 'public');
            \Log::info('New image path: ' . $imagePath);
        }

        $vehicle->update([
            'nama_kendaraan' => $this->nama_kendaraan,
            'plat_nomor'     => strtoupper(trim($this->plat_nomor)),
            'status'         => $this->status,
            'image_path'     => $imagePath,
        ]);

        $this->showEditModal = false;
        $this->resetForm();
        session()->flash('success', 'Data kendaraan berhasil diperbarui.');
    }

    /**
     * Toggle status kendaraan: tersedia ↔ tidak_tersedia.
     * Memanggil toggleStatus() di Vehicle model (flow-brief §FLOW 4).
     */
    public function toggleStatus(int $id): void
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->toggleStatus();
    }

    /**
     * Hapus kendaraan dari database.
     * Guard: kendaraan TIDAK BOLEH dihapus jika masih ada booking aktif
     *        (status pending atau disetujui / on-going).
     */
    public function delete(): void
    {
        $vehicle = Vehicle::findOrFail($this->deletingId);

        // Guard: cek booking aktif (flow-brief §FLOW 4 — integritas referensial)
        $hasActiveBooking = $vehicle->bookings()
            ->whereIn('status', ['pending', 'disetujui'])
            ->exists();

        if ($hasActiveBooking) {
            session()->flash('error', 'Kendaraan tidak dapat dihapus karena masih memiliki peminjaman aktif.');
            $this->showDeleteModal = false;
            return;
        }

        // Hapus foto dari storage jika ada
        if ($vehicle->image_path) {
            Storage::disk('public')->delete($vehicle->image_path);
        }

        // Hapus data peminjaman/booking terkait agar tidak terkena error foreign key constraint
        $vehicle->bookings()->delete();

        $vehicle->delete();

        $this->showDeleteModal = false;
        $this->deletingId      = null;
        session()->flash('success', 'Kendaraan berhasil dihapus.');
    }

    // -----------------------------------------------------------------------
    // Computed Properties
    // -----------------------------------------------------------------------

    public function getStatsProperty(): array
    {
        $total       = Vehicle::count();
        $tersedia    = Vehicle::where('status', VehicleStatus::Tersedia)->count();
        $maintenance = Vehicle::where('status', VehicleStatus::TidakTersedia)->count();
        $pct         = $total > 0 ? round($tersedia / $total * 100) : 0;

        return compact('total', 'tersedia', 'maintenance', 'pct');
    }

    // -----------------------------------------------------------------------
    // Render
    // -----------------------------------------------------------------------

    public function render(): \Illuminate\View\View
    {
        $vehicles = Vehicle::query()
            ->when($this->search, fn ($q) =>
                $q->where('nama_kendaraan', 'like', '%' . $this->search . '%')
                  ->orWhere('plat_nomor', 'like', '%' . $this->search . '%')
            )
            ->orderBy('nama_kendaraan')
            ->paginate(12);

        return view('livewire.admin.fleet', [
            'vehicles' => $vehicles,
            'stats'    => $this->stats,
        ]);
    }

    // -----------------------------------------------------------------------
    // Helper
    // -----------------------------------------------------------------------

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'deletingId',
            'nama_kendaraan', 'plat_nomor', 'photo', 'editPhoto'
        ]);
        $this->status = 'tersedia';
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
