<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Livewire\Traits\WithJavaRegions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: Admin/Dashboard
 *
 * Dasbor analitik dan manajemen persetujuan untuk Administrator (Flow 3).
 */
#[Layout('components.layouts.admin')]
class Dashboard extends Component
{
    use WithPagination;
    use WithJavaRegions;

    // -----------------------------------------------------------------------
    // Filter & Sorting Properties
    // -----------------------------------------------------------------------

    public string $search = '';
    public string $status = '';
    public string $startDate = '';
    public string $endDate = '';

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // -----------------------------------------------------------------------
    // Form Pengajuan Properties
    // -----------------------------------------------------------------------
    #[Validate('required|string', message: 'Provinsi tujuan wajib dipilih')]
    public string $provinsi = '';

    #[Validate('required|string', message: 'Kota tujuan wajib dipilih')]
    public string $kota = '';

    #[Validate('required|string|min:5', message: 'Keperluan wajib diisi (minimal 5 karakter)')]
    public string $keperluan = '';

    #[Validate('required|date|after_or_equal:today', message: 'Tanggal mulai tidak valid')]
    public string $tanggalMulai = '';

    #[Validate('required|date|after_or_equal:tanggalMulai', message: 'Tanggal selesai tidak valid')]
    public string $tanggalSelesai = '';

    #[Validate('required|string|exists:vehicles,plat_nomor', message: 'Kendaraan tidak valid')]
    public string $platMobil = '';

    public array $armadaTersedia = [];
    
    public string $exportStartMonth = '';
    public string $exportStartYear  = '';
    public string $exportEndMonth   = '';
    public string $exportEndYear    = '';

    // -----------------------------------------------------------------------
    // Edit Form Properties
    // -----------------------------------------------------------------------

    public ?int $editBookingId = null;
    
    #[Validate('required|string|max:100')]
    public string $editProvinsi = '';

    #[Validate('required|string|max:100')]
    public string $editKota = '';

    #[Validate('required|string|min:10')]
    public string $editKeperluan = '';

    #[Validate('required|date')]
    public string $editTanggalMulai = '';

    #[Validate('required|date|after_or_equal:editTanggalMulai')]
    public string $editTanggalSelesai = '';

    #[Validate('required|exists:vehicles,id')]
    public ?int $editVehicleId = null;

    public function mount(): void
    {
        $this->exportStartMonth = (string) Carbon::now()->month;
        $this->exportStartYear  = (string) Carbon::now()->year;
        $this->exportEndMonth   = (string) Carbon::now()->month;
        $this->exportEndYear    = (string) Carbon::now()->year;
    }

    /**
     * Reset pagination setiap kali ada update filter/pencarian.
     */
    public function updated($property): void
    {
        if (in_array($property, ['search', 'status', 'startDate', 'endDate'])) {
            $this->resetPage();
        }
    }

    public function updatedEditProvinsi($value): void
    {
        $this->editKota = '';
    }

    /**
     * Tangani pengurutan (sorting)
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
            $this->sortField = $field;
        }
    }

    /**
     * Terapkan filter tanggal ke bulan ini secara otomatis
     */
    public function filterThisMonth(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    // -----------------------------------------------------------------------
    // Computed Properties
    // -----------------------------------------------------------------------

    private function getBaseBookingQuery(): Builder
    {
        return Booking::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->whereHas('user', function (Builder $q2) {
                        $q2->where('name', 'like', "%{$this->search}%");
                    })->orWhere('bookings.kota', 'like', "%{$this->search}%")
                      ->orWhereHas('vehicle', function (Builder $q2) {
                          $q2->where('nama_kendaraan', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->status, function (Builder $query) {
                $query->where('bookings.status', $this->status);
            })
            ->when($this->startDate, function (Builder $query) {
                $query->whereDate('bookings.tanggal_mulai', '>=', $this->startDate);
            })
            ->when($this->endDate, function (Builder $query) {
                $query->whereDate('bookings.tanggal_selesai', '<=', $this->endDate);
            });
    }

    #[Computed]
    public function analytics(): array
    {
        // Total Peminjaman
        $totalPeminjaman = $this->getBaseBookingQuery()->count();

        // Top Vehicle
        $topVehicle = $this->getBaseBookingQuery()
            ->selectRaw('bookings.vehicle_id, COUNT(*) as count')
            ->groupBy('bookings.vehicle_id')
            ->orderByRaw('COUNT(*) DESC')
            ->with('vehicle')
            ->first();

        // Top Seksi
        $topSeksi = $this->getBaseBookingQuery()
            ->selectRaw('users.seksi, COUNT(*) as count')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->groupBy('users.seksi')
            ->orderByRaw('COUNT(*) DESC')
            ->first();

        // Top Pegawai
        $topPegawai = $this->getBaseBookingQuery()
            ->selectRaw('bookings.user_id, COUNT(*) as count')
            ->groupBy('bookings.user_id')
            ->orderByRaw('COUNT(*) DESC')
            ->with('user')
            ->first();

        return [
            'total' => $totalPeminjaman,
            'topVehicle' => $topVehicle,
            'topSeksi' => $topSeksi,
            'topPegawai' => $topPegawai,
        ];
    }

    // -----------------------------------------------------------------------
    // Form Pengajuan Methods
    // -----------------------------------------------------------------------

    public function updatedProvinsi($value): void
    {
        $this->kota = '';
    }

    public function updatedTanggalMulai(): void
    {
        $this->refreshAvailableVehicles();
    }

    public function updatedTanggalSelesai(): void
    {
        $this->refreshAvailableVehicles();
    }

    private function refreshAvailableVehicles(): void
    {
        $this->platMobil = ''; 
        $this->armadaTersedia = [];

        if (empty($this->tanggalMulai) || empty($this->tanggalSelesai)) {
            return;
        }

        $availableVehicles = Vehicle::where('status', 'tersedia')
            ->whereDoesntHave('bookings', function ($query) {
                $query->whereIn('status', ['pending', 'disetujui'])
                      ->where(function ($q) {
                          $q->where('tanggal_mulai', '<=', $this->tanggalSelesai)
                            ->where('tanggal_selesai', '>=', $this->tanggalMulai);
                      });
            })
            ->get();

        foreach ($availableVehicles as $vehicle) {
            $this->armadaTersedia[] = [
                'plat' => $vehicle->plat_nomor,
                'nama' => $vehicle->nama_kendaraan,
                'img'  => $vehicle->imageUrl() ?? 'https://placehold.co/100x80/e1e2e6/727780?text=Mobil',
            ];
        }
    }

    public function submitForm(): void
    {
        \Log::info('submitForm dipanggil', [
            'provinsi' => $this->provinsi,
            'kota' => $this->kota,
            'tanggalMulai' => $this->tanggalMulai,
            'tanggalSelesai' => $this->tanggalSelesai,
            'plat' => $this->platMobil
        ]);
        
        \Log::info('Sebelum validate');
        try {
            $this->validate([
                'provinsi'       => 'required|string',
                'kota'           => 'required|string',
                'keperluan'      => 'required|string|min:5',
                'tanggalMulai'   => 'required|date|after_or_equal:today',
                'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
                'platMobil'      => 'required|string|exists:vehicles,plat_nomor',
            ], [
                'provinsi.required' => 'Provinsi tujuan wajib dipilih',
                'kota.required' => 'Kota tujuan wajib dipilih',
                'keperluan.required' => 'Keperluan wajib diisi',
                'keperluan.min' => 'Keperluan minimal 5 karakter',
                'tanggalMulai.required' => 'Tanggal mulai wajib diisi',
                'tanggalMulai.after_or_equal' => 'Tanggal mulai tidak valid',
                'tanggalSelesai.required' => 'Tanggal selesai wajib diisi',
                'tanggalSelesai.after_or_equal' => 'Tanggal selesai tidak valid',
                'platMobil.required' => 'Kendaraan wajib dipilih',
                'platMobil.exists' => 'Kendaraan tidak valid',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validasi Gagal!', $e->errors());
            throw $e;
        }
        \Log::info('Sesudah validate');

        $vehicle = Vehicle::where('plat_nomor', $this->platMobil)->firstOrFail();
        \Log::info('Vehicle found', ['id' => $vehicle->id]);
        
        $isOverlapping = Booking::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'disetujui'])
            ->where('tanggal_mulai', '<=', $this->tanggalSelesai)
            ->where('tanggal_selesai', '>=', $this->tanggalMulai)
            ->exists();
            
        \Log::info('Is overlapping?', ['overlap' => $isOverlapping]);

        if ($isOverlapping) {
            $this->addError('platMobil', 'Mohon maaf, armada ini baru saja dipesan oleh orang lain pada tanggal tersebut.');
            $this->refreshAvailableVehicles();
            return;
        }

        \Log::info('Creating booking...');
        $booking = Booking::create([
            'user_id'         => Auth::user()->id,
            'vehicle_id'      => $vehicle->id,
            'provinsi'        => $this->provinsi,
            'kota'            => $this->kota,
            'keperluan'       => $this->keperluan,
            'tanggal_mulai'   => $this->tanggalMulai,
            'tanggal_selesai' => $this->tanggalSelesai,
            'status'          => 'pending',
        ]);
        \Log::info('Booking created', ['id' => $booking->id]);

        $this->reset(['keperluan', 'tanggalMulai', 'tanggalSelesai', 'platMobil']);
        $this->armadaTersedia = [];
        
        session()->flash('success', 'Pengajuan berhasil dikirim! Menunggu persetujuan.');
        
        \Log::info('Dispatching close modal');
        $this->dispatch('close-application-modal');
        $this->dispatch('booking-created');
    }

    #[Computed]
    public function availableVehicles()
    {
        // If edit dates are not set, return all available vehicles
        if (empty($this->editTanggalMulai) || empty($this->editTanggalSelesai)) {
            return Vehicle::where('status', 'tersedia')->get();
        }

        return Vehicle::where('status', 'tersedia')
            ->whereDoesntHave('bookings', function ($query) {
                $query->whereIn('status', ['pending', 'disetujui'])
                      ->where('tanggal_mulai', '<=', $this->editTanggalSelesai)
                      ->where('tanggal_selesai', '>=', $this->editTanggalMulai)
                      // Exclude the booking currently being edited from overlap check
                      ->when($this->editBookingId, function ($q) {
                          $q->where('id', '!=', $this->editBookingId);
                      });
            })
            ->get();
    }

    #[Computed]
    public function bookings(): LengthAwarePaginator
    {
        return $this->getBaseBookingQuery()
            ->withRelations()
            ->when($this->sortField, function (Builder $query) {
                if ($this->sortField === 'user.name') {
                    $query->join('users', 'bookings.user_id', '=', 'users.id')
                          ->orderBy('users.name', $this->sortDirection)
                          ->select('bookings.*');
                } elseif ($this->sortField === 'vehicle.nama_kendaraan') {
                    $query->join('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')
                          ->orderBy('vehicles.nama_kendaraan', $this->sortDirection)
                          ->select('bookings.*');
                } else {
                    $query->orderBy('bookings.' . $this->sortField, $this->sortDirection);
                }
            })
            ->paginate(10);
    }

    // -----------------------------------------------------------------------
    // Actions (Hanya Administrator)
    // -----------------------------------------------------------------------

    private function authorizeAction(): void
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (! $user->canManageBookings()) {
            abort(403, 'Anda tidak memiliki otorisasi untuk melakukan aksi ini.');
        }
    }

    public function approve(int $id): void
    {
        $this->authorizeAction();
        
        try {
            $booking = Booking::with(['user', 'vehicle'])->findOrFail($id);
            $userName = $booking->user->name ?? 'Pegawai';
            $vehicleName = $booking->vehicle->nama_kendaraan ?? 'Kendaraan';
            $booking->approve(Auth::user());

            session()->flash('success', "Peminjaman armada {$vehicleName} untuk {$userName} berhasil disetujui.");
            $this->dispatch('booking-updated');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menyetujui peminjaman: ' . $e->getMessage());
        }
    }

    public function reject(int $id): void
    {
        $this->authorizeAction();
        
        try {
            $booking = Booking::with('user')->findOrFail($id);
            $userName = $booking->user->name ?? 'Pegawai';
            $booking->reject(Auth::user());

            session()->flash('success', "Peminjaman armada untuk {$userName} berhasil ditolak.");
            $this->dispatch('booking-updated');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menolak peminjaman: ' . $e->getMessage());
        }
    }

    public function cancel(int $id): void
    {
        $this->authorizeAction();
        
        try {
            $booking = Booking::with('user')->findOrFail($id);
            $userName = $booking->user->name ?? 'Pegawai';
            $booking->cancel(Auth::user());

            session()->flash('success', "Peminjaman armada untuk {$userName} berhasil dibatalkan.");
            $this->dispatch('booking-updated');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal membatalkan peminjaman: ' . $e->getMessage());
        }
    }
    
    public function complete(int $id): void
    {
        $this->authorizeAction();
        
        try {
            $booking = Booking::with('user')->findOrFail($id);
            $userName = $booking->user->name ?? 'Pegawai';
            $booking->markAsCompleted(Auth::user());

            session()->flash('success', "Peminjaman armada untuk {$userName} telah ditandai selesai.");
            $this->dispatch('booking-updated');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menyelesaikan peminjaman: ' . $e->getMessage());
        }
    }

    public function deleteBooking(int $id): void
    {
        $this->authorizeAction();
        
        try {
            $booking = Booking::with('user')->findOrFail($id);
            $userName = $booking->user->name ?? 'Pegawai';
            $booking->delete();
            
            session()->flash('success', "Data peminjaman untuk {$userName} berhasil dihapus.");
            $this->dispatch('booking-updated');
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menghapus data peminjaman: ' . $e->getMessage());
        }
    }



    public function openEdit(int $id): void
    {
        $this->authorizeAction();
        $booking = Booking::findOrFail($id);
        
        $this->editBookingId = $booking->id;
        $this->editProvinsi = $booking->provinsi;
        $this->editKota = $booking->kota;
        $this->editKeperluan = $booking->keperluan;
        $this->editTanggalMulai = $booking->tanggal_mulai->format('Y-m-d');
        $this->editTanggalSelesai = $booking->tanggal_selesai->format('Y-m-d');
        $this->editVehicleId = $booking->vehicle_id;
        
        $this->resetValidation();
        $this->dispatch('open-edit-modal');
    }

    public function saveEdit(): void
    {
        $this->authorizeAction();
        
        $this->validate([
            'editProvinsi'       => 'required|string|max:100',
            'editKota'           => 'required|string|max:100',
            'editKeperluan'      => 'required|string|min:10',
            'editTanggalMulai'   => 'required|date',
            'editTanggalSelesai' => 'required|date|after_or_equal:editTanggalMulai',
            'editVehicleId'      => 'required|exists:vehicles,id',
        ]);

        if ($this->editBookingId) {
            $booking = Booking::findOrFail($this->editBookingId);
            $booking->update([
                'provinsi' => $this->editProvinsi,
                'kota' => $this->editKota,
                'keperluan' => $this->editKeperluan,
                'tanggal_mulai' => $this->editTanggalMulai,
                'tanggal_selesai' => $this->editTanggalSelesai,
                'vehicle_id' => $this->editVehicleId,
            ]);
            
            $this->dispatch('close-edit-modal');
            session()->flash('success', 'Data peminjaman berhasil diperbarui.');
        }
    }

    public function exportReport()
    {
        $this->authorizeAction();

        $month = (int) $this->exportMonth;
        $year = (int) $this->exportYear;

        if (!$month || !$year) {
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;
        }

        $filename = 'laporan-peminjaman-' . $year . '-' . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BookingsExport($month, $year),
            $filename
        );
    }

    // -----------------------------------------------------------------------
    // Render
    // -----------------------------------------------------------------------

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.dashboard')->title('Peminjaman');
    }
}
