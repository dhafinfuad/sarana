<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\ActivityLog as LogModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class ActivityLog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $module = '';
    public string $startDate = '';
    public string $endDate = '';

    public string $sortColumn = 'created_at';
    public string $sortDirection = 'desc';

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
        // Default to current month
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedModule()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    protected function baseQuery(): Builder
    {
        return LogModel::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('nip_pendek', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->module, function ($query) {
                $query->where('page_group', $this->module);
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('activity_logs.created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('activity_logs.created_at', '<=', $this->endDate);
            });
    }

    #[Computed]
    public function analytics(): array
    {
        $query = $this->baseQuery();
        
        $total = (clone $query)->count();

        // Top Halaman
        $topHalaman = (clone $query)
            ->select('page_label', 'page_group', \DB::raw('count(*) as count'))
            ->whereNotNull('page_label')
            ->groupBy('page_label', 'page_group')
            ->orderByDesc('count')
            ->first();

        // Top Seksi
        $topSeksiQuery = (clone $query)
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('users.seksi', \DB::raw('count(*) as count'))
            ->whereNotNull('users.seksi')
            ->groupBy('users.seksi')
            ->orderByDesc('count')
            ->first();

        // Top Pegawai
        $topPegawaiQuery = (clone $query)
            ->select('user_id', \DB::raw('count(*) as count'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->with('user')
            ->first();

        return [
            'total' => $total,
            'topHalaman' => $topHalaman,
            'topSeksi' => $topSeksiQuery,
            'topPegawai' => $topPegawaiQuery,
        ];
    }

    #[Computed]
    public function logs()
    {
        $query = $this->baseQuery();

        if ($this->sortColumn === 'user.name') {
            $query->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortDirection)
                ->select('activity_logs.*');
        } else {
            $query->orderBy('activity_logs.' . $this->sortColumn, $this->sortDirection);
        }

        return $query->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.activity-log');
    }
}
