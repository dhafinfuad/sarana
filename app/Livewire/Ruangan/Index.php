<?php

declare(strict_types=1);

namespace App\Livewire\Ruangan;

use App\Models\Room;
use App\Models\RoomBooking;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Livewire Component: Ruangan/Index
 *
 * - Mode Peminjaman  (ruangan.peminjaman): pegawai mengajukan & melihat riwayat miliknya.
 * - Mode Persetujuan (ruangan.persetujuan): Administrator / Subbag Umum menyetujui
 *   atau menolak pengajuan. Master ruangan dikelola di Ruangan\Kelola.
 */
class Index extends Component
{
    use WithPagination;

    /** Dikunci agar tidak bisa dimanipulasi dari sisi klien. */
    #[Locked]
    public bool $isReviewerMode = false;
    public string $status = '';
    public string $search = '';

    // Form peminjaman
    public string $roomId = '';
    public string $keperluan = '';
    public string $jumlahPeserta = '1';
    public string $tanggalMulai = '';
    public string $tanggalSelesai = '';

    public function mount(string $mode = 'pemohon'): void
    {
        $this->isReviewerMode = $mode === 'reviewer';
        $user = Auth::user();

        if ($this->isReviewerMode && ! $user->canManageBookings()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman Persetujuan Ruangan.');
        }
        if (! $this->isReviewerMode && ! $user->canAccessPeminjaman()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman Peminjaman Ruangan.');
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

    #[Computed]
    public function rooms()
    {
        return Room::tersedia()->orderBy('nama')->get();
    }

    #[Computed]
    public function bookings(): LengthAwarePaginator
    {
        $query = RoomBooking::with(['room', 'user', 'processor']);

        if (! $this->isReviewerMode) {
            $query->where('user_id', Auth::user()->id);
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('keperluan', 'like', $term)
                  ->orWhereHas('room', fn ($r) => $r->where('nama', 'like', $term))
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term));
            });
        }

        return $query->latest('tanggal_mulai')->paginate(10);
    }

    // -----------------------------------------------------------------------
    // Pegawai
    // -----------------------------------------------------------------------

    public function submit(): void
    {
        if ($this->isReviewerMode) {
            return;
        }

        $data = $this->validate([
            'roomId'         => ['required', 'exists:rooms,id'],
            'keperluan'      => ['required', 'string', 'min:5', 'max:255'],
            'jumlahPeserta'  => ['required', 'integer', 'min:1'],
            'tanggalMulai'   => ['required', 'date', 'after_or_equal:now'],
            'tanggalSelesai' => ['required', 'date', 'after:tanggalMulai'],
        ], [
            'tanggalMulai.after_or_equal' => 'Waktu mulai tidak boleh di masa lalu.',
            'tanggalSelesai.after'        => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        $room = Room::tersedia()->find($data['roomId']);
        if (! $room) {
            throw ValidationException::withMessages(['roomId' => 'Ruangan tidak tersedia.']);
        }
        if ((int) $data['jumlahPeserta'] > $room->kapasitas) {
            throw ValidationException::withMessages([
                'jumlahPeserta' => "Jumlah peserta melebihi kapasitas ruangan ({$room->kapasitas} orang).",
            ]);
        }
        $mulai   = Carbon::parse($data['tanggalMulai'])->toDateTimeString();
        $selesai = Carbon::parse($data['tanggalSelesai'])->toDateTimeString();

        if (RoomBooking::overlapping($room->id, $mulai, $selesai)->exists()) {
            throw ValidationException::withMessages([
                'tanggalMulai' => 'Ruangan sudah dipesan pada rentang waktu tersebut.',
            ]);
        }

        RoomBooking::create([
            'room_id'         => $room->id,
            'user_id'         => Auth::user()->id,
            'keperluan'       => $data['keperluan'],
            'jumlah_peserta'  => (int) $data['jumlahPeserta'],
            'tanggal_mulai'   => $mulai,
            'tanggal_selesai' => $selesai,
            'status'          => 'pending',
        ]);

        $this->reset(['roomId', 'keperluan', 'tanggalMulai', 'tanggalSelesai']);
        $this->jumlahPeserta = '1';
        $this->dispatch('close-room-modal');
        session()->flash('success', 'Peminjaman ruangan berhasil diajukan.');
    }

    public function cancel(int $id): void
    {
        $booking = RoomBooking::findOrFail($id);

        if ($booking->user_id === Auth::user()->id && $booking->status === 'pending') {
            $booking->update(['status' => 'dibatalkan']);
            session()->flash('success', 'Peminjaman ruangan dibatalkan.');
        }
    }

    // -----------------------------------------------------------------------
    // Reviewer (Administrator / Subbag Umum)
    // -----------------------------------------------------------------------

    public function approve(int $id): void
    {
        $this->authorizeReviewer();
        $booking = RoomBooking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return;
        }

        $bentrok = RoomBooking::overlapping(
            $booking->room_id,
            $booking->tanggal_mulai->toDateTimeString(),
            $booking->tanggal_selesai->toDateTimeString(),
            $booking->id
        )->where('status', 'disetujui')->exists();

        if ($bentrok) {
            session()->flash('error', 'Tidak dapat disetujui: bentrok dengan peminjaman lain yang sudah disetujui.');
            return;
        }

        $booking->update([
            'status'       => 'disetujui',
            'processed_by' => Auth::user()->id,
            'processed_at' => now(),
        ]);
        session()->flash('success', 'Peminjaman ruangan disetujui.');
    }

    public function reject(int $id): void
    {
        $this->authorizeReviewer();
        $booking = RoomBooking::findOrFail($id);

        if ($booking->status === 'pending') {
            $booking->update([
                'status'       => 'ditolak',
                'processed_by' => Auth::user()->id,
                'processed_at' => now(),
            ]);
            session()->flash('success', 'Peminjaman ruangan ditolak.');
        }
    }

    private function authorizeReviewer(): void
    {
        if (! $this->isReviewerMode || ! Auth::user()->canManageBookings()) {
            abort(403, 'Anda tidak memiliki hak untuk melakukan aksi ini.');
        }
    }

    public function render(): \Illuminate\View\View
    {
        $layout = Auth::user()->canAccessAdminDashboard() ? 'components.layouts.admin' : 'components.layouts.app';

        return view('livewire.ruangan.index')
            ->layout($layout)
            ->title($this->isReviewerMode ? 'Persetujuan Ruangan' : 'Peminjaman Ruangan');
    }
}
