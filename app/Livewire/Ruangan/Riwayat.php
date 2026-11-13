<?php

declare(strict_types=1);

namespace App\Livewire\Ruangan;

use App\Models\Room;
use App\Models\RoomBooking;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: Ruangan/Riwayat — semua riwayat peminjaman ruangan milik pemohon
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

    private const SORTABLE = ['created_at', 'room', 'keperluan', 'tanggal_mulai', 'tanggal_selesai', 'status'];

    public function mount(): void
    {
        if (! Auth::user()->hasAccess('ruangan.peminjaman')) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman Riwayat Peminjaman Ruangan.');
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

    /** @return LengthAwarePaginator<int, RoomBooking> */
    #[Computed]
    public function bookings(): LengthAwarePaginator
    {
        $query = RoomBooking::with('room')->where('user_id', Auth::user()->id)
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(fn ($w) => $w->where('keperluan', 'like', $term)
                    ->orWhereHas('room', fn ($r) => $r->where('nama', 'like', $term)));
            })
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->startDate !== '', fn ($q) => $q->whereDate('tanggal_mulai', '>=', $this->startDate))
            ->when($this->endDate !== '', fn ($q) => $q->whereDate('tanggal_selesai', '<=', $this->endDate));

        if ($this->sortField === 'room') {
            $query->orderBy(Room::select('nama')->whereColumn('rooms.id', 'room_bookings.room_id'), $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query->orderByDesc('id')->paginate(10);
    }

    public function render(): \Illuminate\View\View
    {
        $layout = Auth::user()->canAccessAdminDashboard() ? 'components.layouts.admin' : 'components.layouts.app';

        return view('livewire.ruangan.riwayat')->layout($layout)->title('Riwayat Peminjaman Ruangan');
    }
}
