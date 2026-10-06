<?php

declare(strict_types=1);

namespace App\Livewire\Permisi;

use App\Models\Permisi;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: Permisi/Dashboard
 *
 * Dasbor untuk Permohonan dan Persetujuan Permisi.
 */
class Dashboard extends Component
{
    use WithPagination;

    // -----------------------------------------------------------------------
    // State Properties
    // -----------------------------------------------------------------------

    public string $search = '';
    public string $status = '';
    public string $filterSeksi = '';
    public string $startDate = '';
    public string $endDate = '';

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public bool $isReviewerMode = false;
    public ?int $editId = null;

    // -----------------------------------------------------------------------
    // Form Pengajuan Properties (Digunakan hanya di mode Permohonan)
    // -----------------------------------------------------------------------

    #[Validate('required|in:Kantor,Kesehatan,Keluarga,Lain-lain')]
    public string $kategori = '';

    #[Validate('required|in:Permohonan,Pemberitahuan')]
    public string $jenis = '';

    #[Validate('required|string|min:5')]
    public string $keperluan = '';

    #[Validate('required|date')]
    public string $tanggalMulai = '';

    #[Validate('required|date|after_or_equal:tanggalMulai')]
    public string $tanggalSelesai = '';

    // -----------------------------------------------------------------------
    // Lifecycle Hooks
    // -----------------------------------------------------------------------

    public function mount(): void
    {
        $this->isReviewerMode = request()->routeIs('permisi.persetujuan');

        if ($this->isReviewerMode) {
            if (!Auth::user()->canAccessPersetujuanPermisi()) {
                abort(403, 'Anda tidak memiliki hak akses ke halaman Persetujuan.');
            }
        } else {
            if (!Auth::user()->canAccessPermohonanPermisi()) {
                abort(403, 'Anda tidak memiliki hak akses ke halaman Permohonan.');
            }
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSeksi(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
            $this->sortField = $field;
        }
    }

    // -----------------------------------------------------------------------
    // Computed Properties
    // -----------------------------------------------------------------------

    #[Computed]
    public function availableSeksiList(): array
    {
        $user = Auth::user();
        if (! $this->isReviewerMode) {
            return [];
        }

        if ($user->isSubbagUmum() || $user->isAdministrator() || $user->isKepalaKantor()) {
            return User::whereNotNull('seksi')->where('seksi', '!=', '')->distinct()->pluck('seksi')->toArray();
        }

        if ($user->isKepalaSeksi()) {
            return $user->getManagedSeksiList();
        }

        return [];
    }

    #[Computed]
    public function permisis(): LengthAwarePaginator
    {
        $user = Auth::user();
        
        $query = Permisi::with(['user', 'processor']);

        if ($this->isReviewerMode) {
            // Mode Persetujuan
            if ($user->isSubbagUmum() || $user->isAdministrator()) {
                // Subbag Umum & Admin melihat semua data permohonan tanpa filter
            } elseif ($user->isKepalaKantor()) {
                // Kepala Kantor (definitif maupun PLH/PLT):
                // Membawahi Kepala Seksi dan Supervisor
                // Jika PLT Kepala Kantor juga Kepala Seksi definitif, ia juga membawahi staf seksinya
                $query->whereHas('user', function (Builder $q) use ($user) {
                    $q->where(function (Builder $sub) use ($user) {
                        $sub->whereIn('jabatan', ['Kepala Seksi', 'Supervisor']);
                        if ($user->jabatan === 'Kepala Seksi') {
                            $managedSeksi = $user->getManagedSeksiList();
                            $sub->orWhere(function (Builder $sub2) use ($managedSeksi) {
                                $sub2->whereIn('seksi', $managedSeksi)
                                     ->where('jabatan', '!=', 'Kepala Seksi');
                            });
                        }
                    })->where('id', '!=', $user->id);
                });
            } elseif ($user->isSupervisor()) {
                // Supervisor: Bawahan dengan jabatan FPP di tim yang sama
                $query->whereHas('user', function (Builder $q) use ($user) {
                    $q->where('tim', $user->tim)
                      ->where('jabatan', 'FPP')
                      ->where('id', '!=', $user->id);
                });
            } elseif ($user->isKepalaSeksi()) {
                // Kepala Seksi (definitif dan PLH/PLT): Pegawai di seluruh seksi yang dipimpin
                $managedSeksi = $user->getManagedSeksiList();
                $query->whereHas('user', function (Builder $q) use ($managedSeksi, $user) {
                    $q->whereIn('seksi', $managedSeksi)
                      ->where('jabatan', '!=', 'Kepala Seksi')
                      ->where('id', '!=', $user->id);
                });
            }

            // Filter Seksi (jika dipilih)
            if ($this->filterSeksi) {
                $query->whereHas('user', fn(Builder $q) => $q->where('seksi', $this->filterSeksi));
            }
        } else {
            // Mode Permohonan (Milik sendiri)
            $query->where('user_id', $user->id);
        }

        // Filter: Search (Keperluan atau Nama Pegawai)
        if ($this->search) {
            $query->where(function (Builder $q) {
                $q->where('keperluan', 'like', '%' . $this->search . '%')
                  ->orWhere('jenis', 'like', '%' . $this->search . '%')
                  ->orWhere('kategori', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function (Builder $uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('nip_pendek', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filter: Status
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Filter: Rentang Tanggal
        if ($this->startDate) {
            $query->whereDate('tanggal_mulai', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('tanggal_selesai', '<=', $this->endDate);
        }

        // Sorting
        if ($this->sortField === 'user.name') {
            $query->join('users', 'permisis.user_id', '=', 'users.id')
                  ->orderBy('users.name', $this->sortDirection)
                  ->select('permisis.*');
        } else {
            $query->orderBy('permisis.' . $this->sortField, $this->sortDirection);
        }

        return $query->paginate(10);
    }

    #[Computed]
    public function analytics(): array
    {
        $user = Auth::user();
        $query = Permisi::query();
        
        if ($this->isReviewerMode) {
            if ($user->isSubbagUmum() || $user->isAdministrator()) {
                // No filter
            } elseif ($user->isKepalaKantor()) {
                $query->whereHas('user', function (Builder $q) use ($user) {
                    $q->where(function (Builder $sub) use ($user) {
                        $sub->whereIn('jabatan', ['Kepala Seksi', 'Supervisor']);
                        if ($user->jabatan === 'Kepala Seksi') {
                            $managedSeksi = $user->getManagedSeksiList();
                            $sub->orWhere(function (Builder $sub2) use ($managedSeksi) {
                                $sub2->whereIn('seksi', $managedSeksi)
                                     ->where('jabatan', '!=', 'Kepala Seksi');
                            });
                        }
                    })->where('id', '!=', $user->id);
                });
            } elseif ($user->isSupervisor()) {
                $query->whereHas('user', fn($q) => $q->where('tim', $user->tim)->where('jabatan', 'FPP')->where('id', '!=', $user->id));
            } elseif ($user->isKepalaSeksi()) {
                $managedSeksi = $user->getManagedSeksiList();
                $query->whereHas('user', fn($q) => $q->whereIn('seksi', $managedSeksi)->where('jabatan', '!=', 'Kepala Seksi')->where('id', '!=', $user->id));
            }

            if ($this->filterSeksi) {
                $query->whereHas('user', fn(Builder $q) => $q->where('seksi', $this->filterSeksi));
            }
        } else {
            $query->where('user_id', $user->id);
        }

        $baseQuery = clone $query;

        return [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'disetujui' => (clone $baseQuery)->where('status', 'disetujui')->count(),
            'ditolak' => (clone $baseQuery)->where('status', 'ditolak')->count(),
        ];
    }

    // -----------------------------------------------------------------------
    // Actions (Permohonan)
    // -----------------------------------------------------------------------

    public function submitApplication(): void
    {
        if ($this->isReviewerMode) {
            return;
        }

        $this->validate();

        Permisi::create([
            'user_id' => Auth::user()->id,
            'kategori' => $this->kategori,
            'jenis' => $this->jenis,
            'keperluan' => $this->keperluan,
            'tanggal_mulai' => $this->tanggalMulai,
            'tanggal_selesai' => $this->tanggalSelesai,
            'status' => 'pending',
        ]);

        $this->reset(['kategori', 'jenis', 'keperluan', 'tanggalMulai', 'tanggalSelesai']);
        
        $this->dispatch('close-application-modal');
        $this->dispatch('booking-created');
        session()->flash('success', 'Permohonan perizinan berhasil diajukan.');
    }

    // -----------------------------------------------------------------------
    // Actions (Persetujuan / Pembatalan)
    // -----------------------------------------------------------------------

    public function approve(int $id): void
    {
        if (!$this->isReviewerMode) return;

        $permisi = Permisi::with('user')->findOrFail($id);
        $user = Auth::user();

        // Validasi otorisasi reviewer
        if (!$user->isAdministrator() && !$user->isSubbagUmum()) {
            if ($user->isKepalaKantor()) {
                $isAllowed = in_array($permisi->user->jabatan, ['Kepala Seksi', 'Supervisor'], true);
                if (!$isAllowed && $user->jabatan === 'Kepala Seksi') {
                    $managedSeksi = $user->getManagedSeksiList();
                    $isAllowed = in_array($permisi->user->seksi, $managedSeksi, true) && $permisi->user->jabatan !== 'Kepala Seksi';
                }
                if (!$isAllowed || $permisi->user_id === $user->id) {
                    abort(403, 'Anda tidak memiliki hak untuk memproses permohonan ini.');
                }
            } elseif ($user->isKepalaSeksi()) {
                $managedSeksi = $user->getManagedSeksiList();
                if (!in_array($permisi->user->seksi, $managedSeksi, true) || $permisi->user->jabatan === 'Kepala Seksi' || $permisi->user_id === $user->id) {
                    abort(403, 'Anda tidak memiliki hak untuk memproses permohonan dari seksi ini.');
                }
            } elseif ($user->isSupervisor()) {
                if ($permisi->user->tim !== $user->tim || $permisi->user->jabatan !== 'FPP' || $permisi->user_id === $user->id) {
                    abort(403, 'Anda tidak memiliki hak untuk memproses permohonan ini.');
                }
            }
        }

        if ($permisi->status === 'pending') {
            $permisi->update([
                'status'       => 'disetujui',
                'processed_by' => $user->id,
                'processed_at' => now(),
            ]);
            session()->flash('success', 'Permohonan berhasil disetujui.');
        }
    }

    public function reject(int $id): void
    {
        if (!$this->isReviewerMode) return;

        $permisi = Permisi::with('user')->findOrFail($id);
        $user = Auth::user();

        if (!$user->isAdministrator() && !$user->isSubbagUmum()) {
            if ($user->isKepalaKantor()) {
                $isAllowed = in_array($permisi->user->jabatan, ['Kepala Seksi', 'Supervisor'], true);
                if (!$isAllowed && $user->jabatan === 'Kepala Seksi') {
                    $managedSeksi = $user->getManagedSeksiList();
                    $isAllowed = in_array($permisi->user->seksi, $managedSeksi, true) && $permisi->user->jabatan !== 'Kepala Seksi';
                }
                if (!$isAllowed || $permisi->user_id === $user->id) {
                    abort(403, 'Anda tidak memiliki hak untuk memproses permohonan ini.');
                }
            } elseif ($user->isKepalaSeksi()) {
                $managedSeksi = $user->getManagedSeksiList();
                if (!in_array($permisi->user->seksi, $managedSeksi, true) || $permisi->user->jabatan === 'Kepala Seksi' || $permisi->user_id === $user->id) {
                    abort(403, 'Anda tidak memiliki hak untuk memproses permohonan dari seksi ini.');
                }
            } elseif ($user->isSupervisor()) {
                if ($permisi->user->tim !== $user->tim || $permisi->user->jabatan !== 'FPP' || $permisi->user_id === $user->id) {
                    abort(403, 'Anda tidak memiliki hak untuk memproses permohonan ini.');
                }
            }
        }

        if ($permisi->status === 'pending') {
            $permisi->update([
                'status'       => 'ditolak',
                'processed_by' => $user->id,
                'processed_at' => now(),
            ]);
            session()->flash('success', 'Permohonan telah ditolak.');
        }
    }

    public function cancel(int $id): void
    {
        if ($this->isReviewerMode) return;

        $permisi = Permisi::findOrFail($id);
        // Pastikan milik sendiri
        if ($permisi->user_id === Auth::user()->id && $permisi->status === 'pending') {
            $permisi->update(['status' => 'dibatalkan']);
            session()->flash('success', 'Permohonan berhasil dibatalkan.');
        }
    }

    public function editBooking(int $id): void
    {
        $permisi = Permisi::findOrFail($id);
        $user = Auth::user();

        // Otorisasi: Boleh edit jika Subbag Umum / Admin, ATAU jika pemilik sendiri & status masih pending
        $isOwnerPending = ($permisi->user_id === $user->id && $permisi->status === 'pending');
        $isPrivileged = ($user->isSubbagUmum() || $user->isAdministrator());

        if (!$isOwnerPending && !$isPrivileged) {
            session()->flash('error', 'Anda tidak memiliki hak untuk mengubah permohonan ini.');
            return;
        }

        $this->editId = $permisi->id;
        $this->kategori = $permisi->kategori;
        $this->jenis = $permisi->jenis;
        $this->keperluan = $permisi->keperluan;
        // Gunakan format yang sesuai dengan input type="datetime-local" (YYYY-MM-DDThh:mm)
        $this->tanggalMulai = Carbon::parse($permisi->tanggal_mulai)->format('Y-m-d\TH:i');
        $this->tanggalSelesai = Carbon::parse($permisi->tanggal_selesai)->format('Y-m-d\TH:i');

        $this->dispatch('open-edit-modal');
    }

    public function updateApplication(): void
    {
        if (!$this->editId) return;

        $permisi = Permisi::findOrFail($this->editId);
        $user = Auth::user();

        // Otorisasi: Boleh update jika Subbag Umum / Admin, ATAU jika pemilik sendiri & status masih pending
        $isOwnerPending = ($permisi->user_id === $user->id && $permisi->status === 'pending');
        $isPrivileged = ($user->isSubbagUmum() || $user->isAdministrator());

        if (!$isOwnerPending && !$isPrivileged) {
            session()->flash('error', 'Anda tidak memiliki hak untuk mengubah permohonan ini.');
            return;
        }

        $this->validate();

        $permisi->update([
            'kategori' => $this->kategori,
            'jenis' => $this->jenis,
            'keperluan' => $this->keperluan,
            'tanggal_mulai' => $this->tanggalMulai,
            'tanggal_selesai' => $this->tanggalSelesai,
        ]);

        $this->reset(['editId', 'kategori', 'jenis', 'keperluan', 'tanggalMulai', 'tanggalSelesai']);
        $this->dispatch('close-edit-modal');
        session()->flash('success', 'Data permohonan berhasil diperbarui.');
    }

    public function deleteBooking(int $id): void
    {
        $permisi = Permisi::findOrFail($id);

        if ($this->isReviewerMode) {
            if (Auth::user()->isSubbagUmum() || Auth::user()->isAdministrator()) {
                $permisi->delete();
                session()->flash('success', 'Data permohonan berhasil dihapus.');
            }
            return;
        }

        if ($permisi->user_id === Auth::user()->id && in_array($permisi->status, ['ditolak', 'selesai', 'dibatalkan'])) {
            $permisi->delete();
            session()->flash('success', 'Data permohonan berhasil dihapus.');
        }
    }

    // -----------------------------------------------------------------------
    // Render
    // -----------------------------------------------------------------------

    public function render(): \Illuminate\View\View
    {
        $layout = Auth::user()->canAccessAdminDashboard() ? 'components.layouts.admin' : 'components.layouts.app';

        return view('livewire.permisi.dashboard')->layout($layout)->title($this->isReviewerMode ? 'Persetujuan Permisi' : 'Permohonan Permisi');
    }
}
