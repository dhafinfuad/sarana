<?php

declare(strict_types=1);

namespace App\Livewire\Ruangan;

use App\Models\Room;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: Ruangan/Kelola
 *
 * Master data ruangan (tambah, ubah, aktif/nonaktifkan) untuk
 * Administrator dan Subbag Umum. Ruangan tidak dihapus fisik: gunakan toggle status
 * agar riwayat peminjaman tetap utuh.
 */
class Kelola extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editId = null;
    public string $nama = '';
    public string $lokasi = '';
    public string $kapasitas = '';
    public string $fasilitas = '';

    public function mount(): void
    {
        if (! Auth::user()->hasAccess('ruangan.kelola')) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman Kelola Ruangan.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Room>
     */
    #[Computed]
    public function rooms(): LengthAwarePaginator
    {
        return Room::query()
            ->withCount(['bookings as aktif_count' => fn ($q) => $q->whereIn('status', ['pending', 'disetujui'])])
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(fn ($w) => $w->where('nama', 'like', $term)
                    ->orWhere('lokasi', 'like', $term)
                    ->orWhere('fasilitas', 'like', $term));
            })
            ->orderBy('nama')
            ->paginate(10);
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'nama'      => ['required', 'string', 'max:100', Rule::unique('rooms', 'nama')->ignore($this->editId)],
            'lokasi'    => ['nullable', 'string', 'max:150'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:1000'],
            'fasilitas' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'nama.required'   => 'Nama ruangan wajib diisi.',
            'nama.unique'     => 'Nama ruangan sudah digunakan.',
            'kapasitas.required' => 'Kapasitas wajib diisi.',
            'kapasitas.integer'  => 'Kapasitas harus berupa angka.',
            'kapasitas.min'      => 'Kapasitas minimal 1 orang.',
        ];
    }

    public function save(): void
    {
        $this->authorizeManager();
        $data = $this->validate();

        Room::create([
            'nama'      => $data['nama'],
            'lokasi'    => $data['lokasi'] ?: null,
            'kapasitas' => (int) $data['kapasitas'],
            'fasilitas' => $data['fasilitas'] ?: null,
        ]);

        $this->resetForm();
        $this->dispatch('close-room-form-modal');
        session()->flash('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(int $id): void
    {
        $this->authorizeManager();
        $room = Room::findOrFail($id);

        $this->resetValidation();
        $this->editId    = $room->id;
        $this->nama      = $room->nama;
        $this->lokasi    = (string) $room->lokasi;
        $this->kapasitas = (string) $room->kapasitas;
        $this->fasilitas = (string) $room->fasilitas;

        $this->dispatch('open-room-edit-modal');
    }

    public function update(): void
    {
        $this->authorizeManager();
        if ($this->editId === null) {
            return;
        }

        $data = $this->validate();
        Room::findOrFail($this->editId)->update([
            'nama'      => $data['nama'],
            'lokasi'    => $data['lokasi'] ?: null,
            'kapasitas' => (int) $data['kapasitas'],
            'fasilitas' => $data['fasilitas'] ?: null,
        ]);

        $this->resetForm();
        $this->dispatch('close-room-edit-modal');
        session()->flash('success', 'Data ruangan berhasil diperbarui.');
    }

    public function toggleStatus(int $id): void
    {
        $this->authorizeManager();
        $room = Room::findOrFail($id);
        $room->update(['status' => $room->status === 'tersedia' ? 'tidak_tersedia' : 'tersedia']);

        session()->flash('success', $room->status === 'tersedia'
            ? 'Ruangan diaktifkan kembali.'
            : 'Ruangan dinonaktifkan. Pengajuan yang sudah ada tidak berubah.');
    }

    public function resetForm(): void
    {
        $this->reset(['editId', 'nama', 'lokasi', 'kapasitas', 'fasilitas']);
        $this->resetValidation();
    }

    private function authorizeManager(): void
    {
        if (! Auth::user()->hasAccess('ruangan.kelola')) {
            abort(403, 'Anda tidak memiliki hak untuk melakukan aksi ini.');
        }
    }

    public function render(): \Illuminate\View\View
    {
        $layout = Auth::user()->canAccessAdminDashboard() ? 'components.layouts.admin' : 'components.layouts.app';

        return view('livewire.ruangan.kelola')->layout($layout)->title('Kelola Ruangan');
    }
}
