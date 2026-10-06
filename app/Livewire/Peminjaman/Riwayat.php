<?php

declare(strict_types=1);

namespace App\Livewire\Peminjaman;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Riwayat extends Component
{
    use WithPagination;

    // -----------------------------------------------------------------------
    // Filter & Sorting Properties
    // -----------------------------------------------------------------------
    public string $search = '';
    public string $status = '';
    public string $startDate = '';
    public string $endDate = '';
    
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public ?Booking $detailPeminjaman = null;
    public bool $showDetailModal = false;

    public function updated($property): void
    {
        if (in_array($property, ['search', 'status', 'startDate', 'endDate'])) {
            $this->resetPage();
        }
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

    public function showDetail(int $id): void
    {
        $this->detailPeminjaman = Booking::with('vehicle')->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailPeminjaman = null;
    }

    public function filterThisMonth(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $bookings = Booking::query()
            ->where('user_id', Auth::user()->id)
            ->with('vehicle')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('kota', 'like', "%{$this->search}%")
                      ->orWhere('provinsi', 'like', "%{$this->search}%")
                      ->orWhereHas('vehicle', function (Builder $q2) {
                          $q2->where('nama_kendaraan', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->status, function (Builder $query) {
                $query->where('status', $this->status);
            })
            ->when($this->startDate, function (Builder $query) {
                $query->whereDate('tanggal_mulai', '>=', $this->startDate);
            })
            ->when($this->endDate, function (Builder $query) {
                $query->whereDate('tanggal_selesai', '<=', $this->endDate);
            })
            ->when($this->sortField, function (Builder $query) {
                if ($this->sortField === 'vehicle.nama_kendaraan') {
                    $query->join('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')
                          ->orderBy('vehicles.nama_kendaraan', $this->sortDirection)
                          ->select('bookings.*');
                } else {
                    $query->orderBy($this->sortField, $this->sortDirection);
                }
            })
            ->paginate(10);

        $layout = Auth::user()->canAccessAdminDashboard() ? 'components.layouts.admin' : 'components.layouts.app';

        return view('livewire.peminjaman.riwayat', [
            'bookings' => $bookings
        ])->layout($layout);
    }
}
