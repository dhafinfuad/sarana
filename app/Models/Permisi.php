<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kategori',
        'jenis',
        'keperluan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'datetime',
        'tanggal_selesai' => 'datetime',
        'processed_at'    => 'datetime',
    ];

    /**
     * Relasi ke tabel users (pemohon).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke tabel users (pejabat yang menyetujui / memproses).
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Alias untuk relasi processor.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->processor();
    }
}
