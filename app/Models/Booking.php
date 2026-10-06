<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingStatus;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use RuntimeException;

#[Fillable([
    'user_id',
    'vehicle_id',
    'provinsi',
    'kota',
    'keperluan',
    'tanggal_mulai',
    'tanggal_selesai',
    'status',
    'catatan_admin',
    'processed_by',
    'processed_at',
])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    // -----------------------------------------------------------------------
    // Casts
    // -----------------------------------------------------------------------

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status'         => BookingStatus::class,
            'tanggal_mulai'  => 'date',
            'tanggal_selesai'=> 'date',
            'processed_at'   => 'datetime',
        ];
    }

    // -----------------------------------------------------------------------
    // Scopes (Query Lokal)
    // -----------------------------------------------------------------------

    /**
     * Scope: hanya booking yang masih aktif (mengunci kendaraan).
     * Status: pending atau disetujui.
     *
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereIn('status', [BookingStatus::Pending, BookingStatus::Disetujui]);
    }

    /**
     * Scope: booking berdasarkan status tertentu.
     *
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopeByStatus(Builder $query, BookingStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: booking dalam rentang bulan dan tahun tertentu (untuk laporan Excel).
     *
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopeByPeriod(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('tanggal_mulai', $year)
                     ->whereMonth('tanggal_mulai', $month);
    }

    /**
     * Scope: eager-load relasi standar agar bebas dari N+1 Query Problem.
     *
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['user', 'vehicle', 'processor']);
    }

    // -----------------------------------------------------------------------
    // State Machine — Transisi Status
    // -----------------------------------------------------------------------

    /**
     * Setujui pengajuan peminjaman.
     *
     * @throws RuntimeException jika transisi tidak valid.
     */
    public function approve(User $admin, ?string $catatan = null): bool
    {
        $this->assertCanTransition(BookingStatus::Disetujui);

        $this->status       = BookingStatus::Disetujui;
        $this->processed_by = $admin->id;
        $this->processed_at = Carbon::now();
        $this->catatan_admin = $catatan;

        return $this->save();
    }

    /**
     * Tolak pengajuan peminjaman.
     *
     * @throws RuntimeException jika transisi tidak valid.
     */
    public function reject(User $admin, ?string $catatan = null): bool
    {
        $this->assertCanTransition(BookingStatus::Ditolak);

        $this->status        = BookingStatus::Ditolak;
        $this->processed_by  = $admin->id;
        $this->processed_at  = Carbon::now();
        $this->catatan_admin = $catatan;

        return $this->save();
    }

    /**
     * Batalkan peminjaman (dapat dilakukan kapan saja oleh Admin, sesuai PRD).
     *
     * @throws RuntimeException jika transisi tidak valid.
     */
    public function cancel(User $admin, ?string $catatan = null): bool
    {
        $this->assertCanTransition(BookingStatus::Dibatalkan);

        $this->status        = BookingStatus::Dibatalkan;
        $this->processed_by  = $admin->id;
        $this->processed_at  = Carbon::now();
        $this->catatan_admin = $catatan;

        return $this->save();
    }

    /**
     * Tandai kendaraan sebagai sudah dikembalikan.
     * Dapat dipanggil oleh Pegawai (atas peminjaman miliknya) atau Admin.
     *
     * @throws RuntimeException jika transisi tidak valid.
     */
    public function markAsCompleted(User $actor): bool
    {
        $this->assertCanTransition(BookingStatus::Selesai);

        $this->status        = BookingStatus::Selesai;
        $this->processed_by  = $actor->id;
        $this->processed_at  = Carbon::now();

        return $this->save();
    }

    // -----------------------------------------------------------------------
    // Helper Methods (Strict Typing)
    // -----------------------------------------------------------------------

    /** Apakah booking ini masih menunggu persetujuan? */
    public function isPending(): bool
    {
        return $this->status === BookingStatus::Pending;
    }

    /** Apakah booking ini sudah disetujui? */
    public function isApproved(): bool
    {
        return $this->status === BookingStatus::Disetujui;
    }

    /** Apakah booking ini ditolak? */
    public function isRejected(): bool
    {
        return $this->status === BookingStatus::Ditolak;
    }

    /** Apakah booking ini sudah selesai? */
    public function isCompleted(): bool
    {
        return $this->status === BookingStatus::Selesai;
    }

    /** Apakah booking ini dibatalkan? */
    public function isCancelled(): bool
    {
        return $this->status === BookingStatus::Dibatalkan;
    }

    /** Apakah booking ini sedang aktif (mengunci slot kendaraan)? */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /** Apakah booking ini sudah berada di state terminal (tidak bisa diubah)? */
    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    /**
     * Hitung durasi peminjaman dalam hari (inklusif).
     */
    public function durationInDays(): int
    {
        return (int) $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    // -----------------------------------------------------------------------
    // Internal Guard
    // -----------------------------------------------------------------------

    /**
     * Pastikan transisi ke status $target diperbolehkan dari status saat ini.
     *
     * @throws RuntimeException
     */
    private function assertCanTransition(BookingStatus $target): void
    {
        if (! $this->status->canTransitionTo($target)) {
            throw new RuntimeException(
                "Transisi status tidak valid: dari [{$this->status->value}] ke [{$target->value}]."
            );
        }
    }

    // -----------------------------------------------------------------------
    // Relasi Eloquent
    // -----------------------------------------------------------------------

    /**
     * Pegawai yang mengajukan peminjaman ini.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Kendaraan yang dipinjam.
     *
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Administrator yang memproses booking ini (approval/rejection/cancellation).
     *
     * @return BelongsTo<User, $this>
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
