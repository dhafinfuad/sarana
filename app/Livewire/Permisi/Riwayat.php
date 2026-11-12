<?php

declare(strict_types=1);

namespace App\Livewire\Permisi;

use App\Models\Permisi;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: Permisi/Riwayat — semua riwayat permohonan izin keluar milik pegawai
 * (pencarian, filter status & tanggal, urutan, pagination). Hanya baca.
 */
class Riwayat extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $startDate = '';
    public string $endDate = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    private const SORTABLE = ['created_at', 'kategori', 'keperluan', 'tanggal_mulai', 'tanggal_selesai', 'status'];

    public function mount(): void
    {
        if (! Auth::user()->canAccessPermohonanPermisi()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman Riwayat Permohonan.');
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'startDate', 'endDate'], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::SORTABLE, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function filterThisMonth(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
        $this->resetPage();
    }

    /** @return LengthAwarePaginator<int, Permisi> */
    #[Computed]
    public function permisis(): LengthAwarePaginator
    {
        return Permisi::with('processor')->where('user_id', Auth::user()->id)
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(fn ($w) => $w->where('keperluan', 'like', $term)
                    ->orWhere('kategori', 'like', $term)
                    ->orWhere('jenis', 'like', $term));
            })
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->startDate !== '', fn ($q) => $q->whereDate('tanggal_mulai', '>=', $this->startDate))
            ->when($this->endDate !== '', fn ($q) => $q->whereDate('tanggal_selesai', '<=', $this->endDate))
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function render(): \Illuminate\View\View
    {
        $layout = Auth::user()->canAccessAdminDashboard() ? 'components.layouts.admin' : 'components.layouts.app';

        return view('livewire.permisi.riwayat')->layout($layout)->title('Riwayat Permohonan Izin');
    }
}
