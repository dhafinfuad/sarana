<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomBooking extends Model
{
    protected $fillable = [
        'room_id', 'user_id', 'keperluan', 'jumlah_peserta',
        'tanggal_mulai', 'tanggal_selesai', 'status', 'processed_by', 'processed_at',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'datetime',
        'tanggal_selesai' => 'datetime',
        'processed_at'    => 'datetime',
    ];

    /**
     * Peminjaman yang bentrok dengan rentang waktu tertentu
     * (hanya yang masih pending/disetujui yang mengunci ruangan).
     *
     * @param Builder<RoomBooking> $query
     */
    public function scopeOverlapping(Builder $query, int $roomId, string $mulai, string $selesai, ?int $exceptId = null): Builder
    {
        return $query->where('room_id', $roomId)
            ->whereIn('status', ['pending', 'disetujui'])
            ->where('tanggal_mulai', '<', $selesai)
            ->where('tanggal_selesai', '>', $mulai)
            ->when($exceptId, fn (Builder $q) => $q->where('id', '!=', $exceptId));
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
