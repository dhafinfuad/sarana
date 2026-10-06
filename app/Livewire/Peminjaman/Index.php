<?php

declare(strict_types=1);

namespace App\Livewire\Peminjaman;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Livewire\Traits\WithJavaRegions;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Livewire Component: Peminjaman/Index
 *
 * Menangani UI form pengajuan peminjaman kendaraan (FLOW 2) untuk Pegawai.
 * Dilengkapi dengan algoritma pengecekan ketersediaan kendaraan (Date Overlapping).
 */

class Index extends Component
{
    use WithJavaRegions;

    // -----------------------------------------------------------------------
    // State Properties
    // -----------------------------------------------------------------------

    #[Validate('required|string|max:100')]
    public string $provinsi = 'Jawa Timur';

    #[Validate('required|string|max:100')]
    public string $kota = 'Kota Malang';

    #[Validate('required|string|min:10')]
    public string $keperluan = '';

    #[Validate('required|date|after_or_equal:today')]
    public string $tanggalMulai = '';

    #[Validate('required|date|after_or_equal:tanggalMulai')]
    public string $tanggalSelesai = '';

    #[Validate('required|exists:vehicles,plat_nomor')]
    public string $platMobil = '';

    // Koleksi kendaraan yang tersedia (dipopulasi via updated hook)
    public array $armadaTersedia = [];

    // Untuk modal detail
    public ?Booking $detailPeminjaman = null;
    public bool $showDetailModal = false;

    // -----------------------------------------------------------------------
    // Lifecycle Hooks
    // -----------------------------------------------------------------------

    public function mount(): void
    {
        if (!auth()->user()->canAccessPeminjaman()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman Peminjaman.');
        }
    }

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

    /**
     * Algoritma Cek Ketersediaan Kendaraan (CRITICAL)
     * Filter kendaraan yang tersedia dan TIDAK tumpang tindih dengan booking aktif.
     */
    private function refreshAvailableVehicles(): void
    {
        $this->platMobil = ''; // Reset pilihan kendaraan saat tanggal berubah
        $this->armadaTersedia = [];

        if (empty($this->tanggalMulai) || empty($this->tanggalSelesai)) {
            return;
        }

        // Ambil semua kendaraan yang statusnya tersedia
        $availableVehicles = Vehicle::where('status', 'tersedia')
            ->whereDoesntHave('bookings', function ($query) {
                // Jangan tampilkan jika kendaraan ini memiliki booking yang OVERLAPPING
                // dengan rentang tanggal yang diminta.
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

    // -----------------------------------------------------------------------
    // Actions
    // -----------------------------------------------------------------------

    public function submitForm(): void
    {
        // 1. Validasi Input (menggunakan #[Validate])
        $this->validate();

        // 2. Double check (race condition protection)
        $vehicle = Vehicle::where('plat_nomor', $this->platMobil)->firstOrFail();
        
        $isOverlapping = Booking::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'disetujui'])
            ->where('tanggal_mulai', '<=', $this->tanggalSelesai)
            ->where('tanggal_selesai', '>=', $this->tanggalMulai)
            ->exists();

        if ($isOverlapping) {
            $this->addError('platMobil', 'Mohon maaf, armada ini baru saja dipesan oleh orang lain pada tanggal tersebut.');
            $this->refreshAvailableVehicles();
            return;
        }

        // 3. Simpan ke Database
        Booking::create([
            'user_id'         => Auth::user()->id,
            'vehicle_id'      => $vehicle->id,
            'provinsi'        => $this->provinsi,
            'kota'            => $this->kota,
            'keperluan'       => $this->keperluan,
            'tanggal_mulai'   => $this->tanggalMulai,
            'tanggal_selesai' => $this->tanggalSelesai,
            'status'          => 'pending',
        ]);

        // 4. Reset form fields
        session()->flash('success', 'Booking berhasil dibuat');
        $this->dispatch('booking-created');
        $this->reset(['provinsi', 'kota', 'keperluan', 'tanggalMulai', 'tanggalSelesai', 'platMobil']);
        $this->armadaTersedia = [];
        
        // (Reset select option di Alpine juga via binding karena wire:model tersinkron)

        // 5. Kirim pesan sukses ke UI
        $this->dispatch('booking-created');
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

    public function render(): \Illuminate\View\View
    {
        $userId = Auth::user()->id;
        
        $riwayat = Booking::with('vehicle')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'aktif' => Booking::where('user_id', $userId)->where('status', 'disetujui')->count(),
            'pending' => Booking::where('user_id', $userId)->where('status', 'pending')->count(),
            'gagal' => Booking::where('user_id', $userId)->whereIn('status', ['ditolak', 'dibatalkan'])->count(),
            'selesai' => Booking::where('user_id', $userId)->where('status', 'selesai')->count(),
        ];

        $layout = Auth::user()->canAccessAdminDashboard() ? 'components.layouts.admin' : 'components.layouts.app';

        return view('livewire.peminjaman.index', [
            'riwayat' => $riwayat,
            'stats' => $stats,
        ])->layout($layout);
    }
}
