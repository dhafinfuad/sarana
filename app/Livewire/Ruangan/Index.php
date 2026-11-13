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
    public string $startDate = '';
    public string $endDate = '';
    public string $sortField = 'tanggal_mulai';
    public string $sortDirection = 'desc';

    // Form peminjaman
    public ?int $editId = null;

    public string $roomId = '';
    public string $keperluan = '';
    public string $jumlahPeserta = '';

    // Pemilih jadwal (modal pengajuan): tanggal + waktu dalam menit sejak 00.00
    public string $tanggal = '';
    public ?int $mulaiMenit = null;
    public ?int $selesaiMenit = null;

    public function mount(string $mode = 'pemohon'): void
    {
        $this->isReviewerMode = $mode === 'reviewer';
        $this->tanggal = now()->toDateString();
        $user = Auth::user();

        if ($this->isReviewerMode && ! $user->hasAccess('ruangan.persetujuan')) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman Persetujuan Ruangan.');
        }
        if (! $this->isReviewerMode && ! $user->hasAccess('ruangan.peminjaman')) {
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

    public function updatingStartDate(): void
    {
        $this->resetPage();
    }

    public function updatingEndDate(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['user.name', 'room.nama', 'keperluan', 'tanggal_mulai', 'tanggal_selesai', 'status'], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function rooms()
    {
        return Room::tersedia()->berurutan()->get();
    }

    /**
     * Ringkasan untuk kartu KPI halaman Persetujuan, mengikuti filter tabel (seperti /admin/dashboard).
     *
     * @return array{total: int, topRoom: ?object, topSeksi: ?object, topPegawai: ?object, pending: int}
     */
    #[Computed]
    public function analytics(): array
    {
        $base = fn () => $this->filteredQuery()->setEagerLoads([]);
        $users = fn () => $base()->join('users as u', 'u.id', '=', 'room_bookings.user_id')->orderByDesc('total');

        return [
            'total'      => $base()->count(),
            'topRoom'    => $base()->join('rooms as r', 'r.id', '=', 'room_bookings.room_id')
                ->selectRaw('r.nama as name, r.kapasitas as kapasitas, COUNT(*) as total')
                ->groupBy('r.id', 'r.nama', 'r.kapasitas')->orderByDesc('total')->first(),
            'topSeksi'   => $users()->selectRaw('u.seksi as name, COUNT(*) as total')
                ->whereNotNull('u.seksi')->where('u.seksi', '!=', '')->groupBy('u.seksi')->first(),
            'topPegawai' => $users()->selectRaw('u.name as name, COUNT(*) as total')
                ->groupBy('u.id', 'u.name')->first(),
            'pending'    => RoomBooking::where('status', 'pending')->count(),
        ];
    }

    /**
     * Ringkasan pengajuan milik pemohon untuk kartu Overview Status.
     *
     * @return array{pending: int, disetujui: int, gagal: int}
     */
    #[Computed]
    public function overview(): array
    {
        $c = RoomBooking::where('user_id', Auth::user()->id)
            ->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return [
            'pending'   => (int) ($c['pending'] ?? 0),
            'disetujui' => (int) ($c['disetujui'] ?? 0),
            'gagal'     => (int) (($c['ditolak'] ?? 0) + ($c['dibatalkan'] ?? 0)),
        ];
    }

    /**
     * Query daftar: mode Peminjaman hanya milik sendiri; mode Persetujuan semua, dengan filter
     * pencarian, status, dan rentang tanggal (sama dengan yang dipakai kartu KPI).
     *
     * @return \Illuminate\Database\Eloquent\Builder<RoomBooking>
     */
    private function filteredQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = RoomBooking::with(['room', 'user', 'processor']);

        if (! $this->isReviewerMode) {
            $query->where('room_bookings.user_id', Auth::user()->id);
        }

        if ($this->status !== '') {
            $query->where('room_bookings.status', $this->status);
        }

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('room_bookings.keperluan', 'like', $term)
                  ->orWhereHas('room', fn ($r) => $r->where('nama', 'like', $term))
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)->orWhere('seksi', 'like', $term));
            });
        }

        if ($this->startDate !== '') {
            $query->whereDate('room_bookings.tanggal_mulai', '>=', $this->startDate);
        }
        if ($this->endDate !== '') {
            $query->whereDate('room_bookings.tanggal_selesai', '<=', $this->endDate);
        }

        return $query;
    }

    #[Computed]
    public function bookings(): LengthAwarePaginator
    {
        $query = $this->filteredQuery();

        if ($this->sortField === 'user.name') {
            $query->orderBy(\App\Models\User::select('name')->whereColumn('users.id', 'room_bookings.user_id'), $this->sortDirection);
        } elseif ($this->sortField === 'room.nama') {
            $query->orderBy(Room::select('nama')->whereColumn('rooms.id', 'room_bookings.room_id'), $this->sortDirection);
        } else {
            $query->orderBy('room_bookings.' . $this->sortField, $this->sortDirection);
        }

        $page = $query->orderByDesc('room_bookings.id')->paginate(10);

        $this->decorate($page->getCollection());

        return $page;
    }

    /**
     * 5 pengajuan terbaru milik pemohon untuk kartu Riwayat (mode Peminjaman).
     *
     * @return \Illuminate\Support\Collection<int, RoomBooking>
     */
    #[Computed]
    public function recent(): \Illuminate\Support\Collection
    {
        $rows = RoomBooking::with('room')
            ->where('user_id', Auth::user()->id)
            ->latest()->latest('id')
            ->limit(5)
            ->get();

        $this->decorate($rows);

        return $rows;
    }

    /**
     * Flag hak aksi per baris (atribut sementara, tidak disimpan ke database).
     * Pada mode Peminjaman tombol aksi hanya ditampilkan pada status Pending (semua peran).
     *
     * @param \Illuminate\Support\Collection<int, RoomBooking> $rows
     */
    private function decorate(\Illuminate\Support\Collection $rows): void
    {
        $user = Auth::user();

        $rows->each(function (RoomBooking $b) use ($user) {
            $b->setAttribute('can_approve', $this->isReviewerMode && in_array($b->status, ['pending', 'ditolak'], true));
            $b->setAttribute('can_reject', $this->isReviewerMode && in_array($b->status, ['pending', 'disetujui'], true));
            $b->setAttribute('can_edit', $this->userCanEdit($b));
            $b->setAttribute('can_delete', $this->userCanDelete($b));
            $b->setAttribute('can_cancel', ! $this->isReviewerMode && $b->user_id === $user->id && $b->status === 'pending');
        });
    }

    // -----------------------------------------------------------------------
    // Pegawai
    // -----------------------------------------------------------------------

    /**
     * Data jadwal satu tanggal untuk pemilih jadwal: ruangan yang tersedia, pemakaian
     * (Terpakai = disetujui, Ditahan = menunggu persetujuan), dan batas waktu yang sudah lewat.
     * $kecuali = id peminjaman yang sedang diedit (tidak dihitung sebagai pemakaian).
     *
     * @return array<string, mixed>
     */
    public function jadwal(string $tanggal, ?int $kecuali = null): array
    {
        abort_unless(Auth::user()->hasAccess('ruangan.peminjaman'), 403);

        $today = now()->startOfDay();
        try {
            $day = Carbon::createFromFormat('Y-m-d', $tanggal)->startOfDay();
        } catch (\Throwable) {
            $day = $today->copy();
        }
        if ($day->lt($today)) {
            $day = $today->copy();
        }

        $langkah = (int) config('ruangan.langkah');
        $bookings = RoomBooking::whereIn('status', ['pending', 'disetujui'])
            ->when($kecuali, fn ($q) => $q->where('id', '!=', $kecuali))
            ->where('tanggal_mulai', '<', $day->copy()->addDay())
            ->where('tanggal_selesai', '>', $day)
            ->get(['room_id', 'tanggal_mulai', 'tanggal_selesai', 'status'])
            ->groupBy('room_id');

        $rooms = Room::tersedia()->berurutan()->get()->map(function (Room $r) use ($bookings, $day) {
            return [
                'id'        => $r->id,
                'nama'      => $r->nama,
                'lokasi'    => $r->lokasi,
                'kapasitas' => $r->kapasitas,
                'fasilitas' => $r->fasilitas,
                'busy'      => ($bookings[$r->id] ?? collect())->map(fn (RoomBooking $b) => [
                    's'  => max(0, (int) $day->diffInMinutes($b->tanggal_mulai, false)),
                    'e'  => min(1440, (int) $day->diffInMinutes($b->tanggal_selesai, false)),
                    'st' => $b->status === 'disetujui' ? 'ok' : 'hold',
                ])->sortBy('s')->values()->all(),
            ];
        })->values()->all();

        return [
            'tanggal' => $day->toDateString(),
            'hariIni' => $day->equalTo($today),
            'now'     => $day->equalTo($today) ? intdiv(now()->hour * 60 + now()->minute, $langkah) * $langkah : null,
            'buka'    => (int) config('ruangan.jam_buka') * 60,
            'tutup'   => (int) config('ruangan.jam_tutup') * 60,
            'langkah' => $langkah,
            'rooms'   => $rooms,
        ];
    }

    public function submit(): void
    {
        // Pengajuan atas nama sendiri, dari halaman Peminjaman maupun Persetujuan.
        if (! Auth::user()->hasAccess('ruangan.peminjaman')) {
            abort(403, 'Anda tidak memiliki hak untuk mengajukan peminjaman ruangan.');
        }

        [$room, $mulai, $selesai, $data] = $this->validasiPemilihJadwal();

        RoomBooking::create([
            'room_id'         => $room->id,
            'user_id'         => Auth::user()->id,
            'keperluan'       => $data['keperluan'],
            'jumlah_peserta'  => (int) $data['jumlahPeserta'],
            'tanggal_mulai'   => $mulai,
            'tanggal_selesai' => $selesai,
            'status'          => 'pending',
        ]);

        $this->resetForm();
        $this->dispatch('close-room-modal');
        session()->flash('success', 'Peminjaman ruangan berhasil diajukan.');
    }

    /**
     * Validasi isian pemilih jadwal (pengajuan dan edit): ruangan, jumlah orang, tanggal, dan waktu
     * dalam menit. Pada edit ($kecuali), waktu yang tidak diubah tidak diperiksa ulang aturan
     * langkah, jam operasional, dan masa lalu (data lama boleh tetap diedit keperluannya).
     *
     * @return array{0: Room, 1: string, 2: string, 3: array<string, mixed>}
     */
    private function validasiPemilihJadwal(?RoomBooking $kecuali = null): array
    {
        $buka    = (int) config('ruangan.jam_buka') * 60;
        $tutup   = (int) config('ruangan.jam_tutup') * 60;
        $langkah = (int) config('ruangan.langkah');

        $data = $this->validate([
            'roomId'        => ['required', 'exists:rooms,id'],
            'keperluan'     => ['required', 'string', 'min:5', 'max:255'],
            'jumlahPeserta' => ['required', 'integer', 'min:1'],
            'tanggal'       => ['required', 'date_format:Y-m-d'],
            'mulaiMenit'    => ['required', 'integer'],
            'selesaiMenit'  => ['required', 'integer'],
        ], [
            'roomId.required'        => 'Pilih ruangan dengan mengetuk jadwalnya.',
            'jumlahPeserta.required' => 'Isi jumlah orang.',
            'jumlahPeserta.integer'  => 'Jumlah orang harus berupa angka.',
            'jumlahPeserta.min'      => 'Jumlah orang minimal 1.',
            'mulaiMenit.required'    => 'Pilih waktu mulai.',
            'selesaiMenit.required'  => 'Pilih waktu selesai.',
            'keperluan.required'     => 'Keperluan wajib diisi.',
            'keperluan.min'          => 'Keperluan minimal 5 karakter.',
        ]);

        $mulaiM = (int) $data['mulaiMenit'];
        $akhirM = (int) $data['selesaiMenit'];
        $day    = Carbon::createFromFormat('Y-m-d', $data['tanggal'])->startOfDay();
        $mulaiAt = $day->copy()->addMinutes($mulaiM);
        $akhirAt = $day->copy()->addMinutes($akhirM);

        $tidakBerubah = $kecuali
            && $kecuali->tanggal_mulai->equalTo($mulaiAt)
            && $kecuali->tanggal_selesai->equalTo($akhirAt);

        if (! $tidakBerubah) {
            if ($day->lt(now()->startOfDay())) {
                throw ValidationException::withMessages(['tanggal' => 'Tanggal tidak boleh di masa lalu.']);
            }
            if ($mulaiM % $langkah !== 0 || $akhirM % $langkah !== 0) {
                throw ValidationException::withMessages(['mulaiMenit' => "Waktu harus kelipatan {$langkah} menit."]);
            }
            if ($mulaiM < $buka || $akhirM > $tutup) {
                throw ValidationException::withMessages(['mulaiMenit' => 'Peminjaman hanya pada jam operasional ' . sprintf('%02d.00', $buka / 60) . ' sampai ' . sprintf('%02d.00', $tutup / 60) . '.']);
            }
            if ($akhirM - $mulaiM < $langkah) {
                throw ValidationException::withMessages(['selesaiMenit' => "Durasi minimal {$langkah} menit."]);
            }
            // Boleh memulai di kuartal yang sedang berjalan, tetapi tidak sebelumnya.
            $kuartal = now()->startOfHour()->addMinutes(intdiv(now()->minute, $langkah) * $langkah);
            if ($mulaiAt->lt($kuartal)) {
                throw ValidationException::withMessages(['mulaiMenit' => 'Waktu mulai tidak boleh di masa lalu.']);
            }
        } elseif ($akhirM <= $mulaiM) {
            throw ValidationException::withMessages(['selesaiMenit' => 'Waktu selesai harus setelah waktu mulai.']);
        }

        $room = Room::find($data['roomId']);
        if (! $room || ($room->status !== 'tersedia' && ! ($kecuali && $kecuali->room_id === $room->id))) {
            throw ValidationException::withMessages(['roomId' => 'Ruangan tidak tersedia.']);
        }
        if ((int) $data['jumlahPeserta'] > $room->kapasitas) {
            throw ValidationException::withMessages([
                'jumlahPeserta' => "Jumlah orang melebihi kapasitas ruangan ({$room->kapasitas} orang).",
            ]);
        }

        $mulai   = $mulaiAt->toDateTimeString();
        $selesai = $akhirAt->toDateTimeString();
        if (RoomBooking::overlapping($room->id, $mulai, $selesai, $kecuali?->id)->exists()) {
            throw ValidationException::withMessages([
                'mulaiMenit' => 'Ruangan sudah dipesan pada rentang waktu tersebut. Pilih waktu lain.',
            ]);
        }

        return [$room, $mulai, $selesai, $data];
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

    /**
     * Mode Persetujuan: pengelola (Administrator / Subbag Umum) boleh mengubah semua status.
     * Mode Peminjaman: siapa pun (semua peran) hanya boleh mengubah miliknya yang masih Pending.
     */
    private function userCanEdit(RoomBooking $b): bool
    {
        $u = Auth::user();

        return $this->isReviewerMode
            ? $u->hasAccess('ruangan.persetujuan')
            : ($b->user_id === $u->id && $b->status === 'pending');
    }

    /**
     * Mode Persetujuan: pengelola boleh menghapus semua status.
     * Mode Peminjaman: siapa pun (semua peran) hanya boleh menghapus miliknya yang masih Pending.
     */
    private function userCanDelete(RoomBooking $b): bool
    {
        $u = Auth::user();

        return $this->isReviewerMode
            ? $u->hasAccess('ruangan.persetujuan')
            : ($b->user_id === $u->id && $b->status === 'pending');
    }

    public function edit(int $id): void
    {
        $booking = RoomBooking::findOrFail($id);

        if (! $this->userCanEdit($booking)) {
            session()->flash('error', 'Anda tidak memiliki hak untuk mengubah peminjaman ini.');
            return;
        }

        $this->resetValidation();
        $day = $booking->tanggal_mulai->copy()->startOfDay();

        $this->editId        = $booking->id;
        $this->roomId        = (string) $booking->room_id;
        $this->keperluan     = $booking->keperluan;
        $this->jumlahPeserta = (string) $booking->jumlah_peserta;
        $this->tanggal       = $day->toDateString();
        $this->mulaiMenit    = (int) $day->diffInMinutes($booking->tanggal_mulai);
        $this->selesaiMenit  = (int) $day->diffInMinutes($booking->tanggal_selesai);

        // Pemilih jadwal (modal pengajuan) dibuka dalam mode edit dan diisi dari data ini.
        $this->dispatch('open-room-edit',
            id: $booking->id, roomId: $booking->room_id, tanggal: $this->tanggal,
            mulai: $this->mulaiMenit, selesai: $this->selesaiMenit,
            jumlah: $booking->jumlah_peserta, keperluan: $booking->keperluan,
        );
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

        [$room, $mulai, $selesai, $data] = $this->validasiPemilihJadwal($booking);

        $booking->update([
            'room_id'         => $room->id,
            'keperluan'       => $data['keperluan'],
            'jumlah_peserta'  => (int) $data['jumlahPeserta'],
            'tanggal_mulai'   => $mulai,
            'tanggal_selesai' => $selesai,
        ]);

        $this->resetForm();
        $this->dispatch('close-room-modal');
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
        $this->reset(['editId', 'roomId', 'keperluan', 'jumlahPeserta', 'mulaiMenit', 'selesaiMenit']);
        $this->tanggal = now()->toDateString();
        $this->resetValidation();
    }

    private function authorizeReviewer(): void
    {
        if (! $this->isReviewerMode || ! Auth::user()->hasAccess('ruangan.persetujuan')) {
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
