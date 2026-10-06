<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Livewire Component: Admin/UserManagement
 */
#[Layout('components.layouts.admin')]
class UserManagement extends Component
{
    use WithPagination, WithFileUploads;

    // -----------------------------------------------------------------------
    // Table Options
    // -----------------------------------------------------------------------
    public $search = '';
    public $perPage = 50;
    public $sortColumn = 'name';
    public $sortDirection = 'asc';

    // -----------------------------------------------------------------------
    // CSV Upload
    // -----------------------------------------------------------------------
    public $csvFile;

    // -----------------------------------------------------------------------
    // Form Edit Properties
    // -----------------------------------------------------------------------
    public $editUserId = null;
    public $editName = '';
    public $editNipPendek = '';
    public $editNipPanjang = '';
    public $editSeksi = '';
    public $editJabatan = '';
    public $newPassword = '';

    // -----------------------------------------------------------------------
    // Form Add Properties
    // -----------------------------------------------------------------------
    public $addName = '';
    public $addNipPendek = '';
    public $addNipPanjang = '';
    public $addSeksi = '';
    public $addJabatan = '';
    public $addPassword = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('nip_pendek', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editUserId = $user->id;
        $this->editName = $user->name;
        $this->editNipPendek = $user->nip_pendek;
        $this->editNipPanjang = $user->nip ?? '';
        $this->editSeksi = $user->seksi ?? '';
        $this->editJabatan = $user->jabatan ?? '';
        $this->newPassword = '';

        $this->dispatch('open-edit-modal');
    }

    public function updateUser(): void
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editNipPendek' => 'required|string|max:20|unique:users,nip_pendek,' . $this->editUserId,
            'editNipPanjang' => 'nullable|string|max:25|unique:users,nip,' . $this->editUserId,
            'editSeksi' => 'nullable|string|max:100',
            'editJabatan' => 'nullable|string|max:100',
            'newPassword' => 'nullable|string|min:6',
        ]);

        $user = User::findOrFail($this->editUserId);
        $user->name = $this->editName;
        $user->nip_pendek = $this->editNipPendek;
        $user->nip = $this->editNipPanjang;
        $user->seksi = $this->editSeksi;
        $user->jabatan = $this->editJabatan;

        if (!empty($this->newPassword)) {
            $user->password = Hash::make($this->newPassword);
        }

        $user->save();

        session()->flash('success', 'Data pegawai berhasil diperbarui.');
        $this->dispatch('close-edit-modal');
    }

    public function createUser(): void
    {
        $this->validate([
            'addName' => 'required|string|max:255',
            'addNipPendek' => 'required|string|max:20|unique:users,nip_pendek',
            'addNipPanjang' => 'nullable|string|max:25|unique:users,nip',
            'addSeksi' => 'nullable|string|max:100',
            'addJabatan' => 'nullable|string|max:100',
            'addPassword' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $this->addName,
            'nip_pendek' => $this->addNipPendek,
            'nip' => $this->addNipPanjang,
            'seksi' => $this->addSeksi,
            'jabatan' => $this->addJabatan,
            'password' => Hash::make($this->addPassword),
        ]);

        $this->reset([
            'addName', 'addNipPendek', 'addNipPanjang', 'addSeksi', 'addJabatan', 'addPassword'
        ]);

        session()->flash('success', 'Pegawai baru berhasil ditambahkan.');
        $this->dispatch('close-add-modal');
    }

    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id !== auth()->id()) {
            $user->delete();
            session()->flash('success', 'Pegawai berhasil dihapus.');
        } else {
            session()->flash('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }
    }

    public function uploadCsv(): void
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        try {
            Excel::import(new UsersImport, $this->csvFile);
            session()->flash('success', 'Data pegawai berhasil diimpor.');
            $this->dispatch('close-upload-modal');
            $this->reset('csvFile');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsgs = [];
            foreach ($failures as $failure) {
                $errorMsgs[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            session()->flash('error', 'Gagal validasi: ' . implode(' | ', $errorMsgs));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\UsersTemplateExport, 'template_users.xlsx');
    }
}
