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
    public ?int $editId = null;

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

    /** Semua ruangan (termasuk nonaktif) untuk form edit, agar ruangan lama tetap terpilih. */
    #[Computed]
    public function editRooms()
    {
        return Room::orderBy('nama')->get();
    }

    /**
     * Ringkasan jumlah pengajuan untuk kartu KPI di halaman Persetujuan.
     *
     * @return array{total: int, pending: int, disetujui: int, ditolak: int}
     */
    #[Computed]
    public function analytics(): array
    {
        $counts = RoomBooking::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total'     => (int) $counts->sum(),
            'pending'   => (int) ($counts['pending'] ?? 0),
            'disetujui' => (int) ($counts['disetujui'] ?? 0),
            'ditolak'   => (int) ($counts['ditolak'] ?? 0),
        ];
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

        $page = $query->latest('tanggal_mulai')->paginate(10);

        // Flag hak aksi per baris (atribut sementara, tidak disimpan ke database).
        // Tombol selalu tampil di semua status; flag menentukan apakah bisa diklik.
        $page->getCollection()->each(function (RoomBooking $b) {
            $b->setAttribute('can_approve', $this->isReviewerMode && in_array($b->status, ['pending', 'ditolak'], true));
            $b->setAttribute('can_reject', $this->isReviewerMode && in_array($b->status, ['pending', 'disetujui'], true));
            $b->setAttribute('can_edit', $this->userCanEdit($b));
            $b->setAttribute('can_delete', $this->userCanDelete($b));
            $b->setAttribute('can_cancel', ! $this->isReviewerMode && $b->user_id === Auth::user()->id && $b->status === 'pending');
        });

        return $page;
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

        if (! in_array($booking->status, ['pending', 'ditolak'], true)) {
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

        if (in_array($booking->status, ['pending', 'disetujui'], true)) {
            $booking->update([
                'status'       => 'ditolak',
                'processed_by' => Auth::user()->id,
                'processed_at' => now(),
            ]);
            session()->flash('success', 'Peminjaman ruangan ditolak.');
        }
    }

    // -----------------------------------------------------------------------
    // Edit & Hapus
    // -----------------------------------------------------------------------

    /** Pengelola boleh mengubah semua status; pemohon hanya miliknya yang masih pending. */
    private function userCanEdit(RoomBooking $b): bool
    {
        $u = Auth::user();

        return $u->canManageBookings() || ($b->user_id === $u->id && $b->status === 'pending');
    }

    /** Pengelola boleh menghapus semua status; pemohon hanya miliknya yang ditolak/dibatalkan. */
    private function userCanDelete(RoomBooking $b): bool
    {
        $u = Auth::user();

        return $u->canManageBookings()
            || ($b->user_id === $u->id && in_array($b->status, ['ditolak', 'dibatalkan'], true));
    }

    public function edit(int $id): void
    {
        $booking = RoomBooking::findOrFail($id);

        if (! $this->userCanEdit($booking)) {
            session()->flash('error', 'Anda tidak memiliki hak untuk mengubah peminjaman ini.');
            return;
        }

        $this->resetValidation();
        $this->editId         = $booking->id;
        $this->roomId         = (string) $booking->room_id;
        $this->keperluan      = $booking->keperluan;
        $this->jumlahPeserta  = (string) $booking->jumlah_peserta;
        $this->tanggalMulai   = $booking->tanggal_mulai->format('Y-m-d\TH:i');
        $this->tanggalSelesai = $booking->tanggal_selesai->format('Y-m-d\TH:i');

        $this->dispatch('open-booking-edit-modal');
    }

    public function update(): void
    {
        if ($this->editId === null) {
            return;
        }

        $booking = RoomBooking::findOrFail($this->editId);
        if (! $this->userCanEdit($booking)) {
            session()->flash('error', 'Anda tidak memiliki hak untuk mengubah peminjaman ini.');
            return;
        }

        $data = $this->validate([
            'roomId'         => ['required', 'exists:rooms,id'],
            'keperluan'      => ['required', 'string', 'min:5', 'max:255'],
            'jumlahPeserta'  => ['required', 'integer', 'min:1'],
            'tanggalMulai'   => ['required', 'date'],
            'tanggalSelesai' => ['required', 'date', 'after:tanggalMulai'],
        ], [
            'tanggalSelesai.after' => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        $mulai   = Carbon::parse($data['tanggalMulai'])->toDateTimeString();
        $selesai = Carbon::parse($data['tanggalSelesai'])->toDateTimeString();

        // Waktu mulai di masa lalu hanya boleh jika tidak diubah (mis. koreksi data riwayat).
        if ($mulai !== $booking->tanggal_mulai->toDateTimeString() && Carbon::parse($mulai)->isPast()) {
            throw ValidationException::withMessages(['tanggalMulai' => 'Waktu mulai tidak boleh di masa lalu.']);
        }

        $room = Room::find($data['roomId']);
        if ($room->status !== 'tersedia' && $room->id !== $booking->room_id) {
            throw ValidationException::withMessages(['roomId' => 'Ruangan tidak tersedia.']);
        }
        if ((int) $data['jumlahPeserta'] > $room->kapasitas) {
            throw ValidationException::withMessages([
                'jumlahPeserta' => "Jumlah peserta melebihi kapasitas ruangan ({$room->kapasitas} orang).",
            ]);
        }
        if (RoomBooking::overlapping($room->id, $mulai, $selesai, $booking->id)->exists()) {
            throw ValidationException::withMessages([
                'tanggalMulai' => 'Ruangan sudah dipesan pada rentang waktu tersebut.',
            ]);
        }

        $booking->update([
            'room_id'         => $room->id,
            'keperluan'       => $data['keperluan'],
            'jumlah_peserta'  => (int) $data['jumlahPeserta'],
            'tanggal_mulai'   => $mulai,
            'tanggal_selesai' => $selesai,
        ]);

        $this->resetForm();
        $this->dispatch('close-booking-edit-modal');
        session()->flash('success', 'Data peminjaman ruangan berhasil diperbarui.');
    }

    public function delete(int $id): void
    {
        $booking = RoomBooking::findOrFail($id);

        if (! $this->userCanDelete($booking)) {
            session()->flash('error', 'Anda tidak memiliki hak untuk menghapus peminjaman ini.');
            return;
        }

        $booking->delete();
        session()->flash('success', 'Data peminjaman ruangan berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['editId', 'roomId', 'keperluan', 'tanggalMulai', 'tanggalSelesai']);
        $this->jumlahPeserta = '1';
        $this->resetValidation();
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
