<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use App\Support\HakAkses as Matrix;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Livewire Component: Admin/HakAkses — pengaturan matriks hak akses per peran.
 * Hanya Administrator (kunci admin.hak_akses dikunci di config/hak_akses.php).
 */
class HakAkses extends Component
{
    /** @var array<string, array<string, bool>> [slot kunci][peran] — slot = kunci dengan '.' diganti '__' */
    public array $matrix = [];

    public string $userSearch = '';
    public ?int $checkUserId = null;

    public function mount(): void
    {
        $this->authorizeAdmin();
        $this->loadMatrix();
    }

    public static function slot(string $key): string
    {
        return str_replace('.', '__', $key);
    }

    private function loadMatrix(): void
    {
        $this->matrix = [];
        foreach (Matrix::matrix() as $key => $roles) {
            $this->matrix[self::slot($key)] = $roles;
        }
    }

    /** @return array<string, array<string, bool>> matriks dengan kunci asli */
    private function edited(): array
    {
        $out = [];
        foreach (array_keys(Matrix::catalog()) as $key) {
            $out[$key] = array_map('boolval', $this->matrix[self::slot($key)] ?? []);
        }

        return $out;
    }

    /** Jumlah sel yang diubah tetapi belum disimpan. */
    #[Computed]
    public function dirtyCount(): int
    {
        $saved = Matrix::matrix();
        $count = 0;
        foreach ($this->edited() as $key => $roles) {
            foreach ($roles as $role => $value) {
                if ($value !== ($saved[$key][$role] ?? false)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function save(): void
    {
        $this->authorizeAdmin();
        $changes = Matrix::save($this->edited(), Auth::user());
        $this->loadMatrix();
        $this->dispatch('close-hak-akses-confirm');

        session()->flash('success', $changes
            ? 'Hak akses disimpan (' . count($changes) . ' perubahan). Perubahan langsung berlaku untuk semua pengguna.'
            : 'Tidak ada perubahan yang perlu disimpan.');
    }

    public function resetDefaults(): void
    {
        $this->authorizeAdmin();
        Matrix::reset(Auth::user());
        $this->loadMatrix();
        $this->dispatch('close-hak-akses-confirm');
        session()->flash('success', 'Semua hak akses dikembalikan ke pengaturan bawaan.');
    }

    public function discard(): void
    {
        $this->loadMatrix();
    }

    // ---- Cek Pengguna ----

    /** @return Collection<int, User> */
    #[Computed]
    public function userOptions(): Collection
    {
        if (mb_strlen(trim($this->userSearch)) < 2) {
            return collect();
        }

        $term = '%' . trim($this->userSearch) . '%';

        return User::where('name', 'like', $term)->orWhere('nip_pendek', 'like', $term)
            ->orderBy('name')->limit(8)->get(['id', 'name', 'nip_pendek', 'jabatan', 'seksi']);
    }

    public function selectUser(int $id): void
    {
        $this->checkUserId = $id;
        $this->userSearch = '';
    }

    public function clearUser(): void
    {
        $this->checkUserId = null;
    }

    /** @return array{user: User, roles: array<int, string>, access: array<string, array<int, string>>}|null */
    #[Computed]
    public function checked(): ?array
    {
        $user = $this->checkUserId ? User::find($this->checkUserId) : null;
        if (! $user) {
            return null;
        }

        $labels = Matrix::roles();
        $access = [];
        foreach (Matrix::modules() as $module => $items) {
            foreach ($items as $key => $meta) {
                if ($user->hasAccess($key)) {
                    $access[$module][] = $meta['label'];
                }
            }
        }

        return [
            'user'   => $user,
            'roles'  => array_map(fn ($r) => $labels[$r] ?? $r, $user->roles()),
            'access' => $access,
        ];
    }

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user()->hasAccess('admin.hak_akses'), 403, 'Halaman ini hanya untuk Administrator.');
    }

    public function render(): \Illuminate\View\View
    {
        $layout = Auth::user()->canAccessAdminDashboard() ? 'components.layouts.admin' : 'components.layouts.app';

        return view('livewire.admin.hak-akses', [
            'modules' => Matrix::modules(),
            'roles'   => Matrix::roles(),
        ])->layout($layout)->title('Hak Akses');
    }
}
