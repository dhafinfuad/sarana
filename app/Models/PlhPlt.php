<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlhPlt extends Model
{
    use HasFactory;

    protected $table = 'plh_plt';

    protected $fillable = [
        'posisi',
        'jenis',
        'pegawai_id',
        'pejabat_definitif_id',
        'no_sp',
        'tanggal_sp',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sp'      => 'date',
            'tanggal_mulai'   => 'date',
            'tanggal_selesai' => 'date',
            'status_aktif'    => 'boolean',
        ];
    }

    // -----------------------------------------------------------------------
    // Relasi Eloquent
    // -----------------------------------------------------------------------

    /**
     * Pegawai yang ditugaskan sebagai PLH / PLT.
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }

    /**
     * Pejabat definitif yang digantikan (jika ada).
     */
    public function pejabatDefinitif(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pejabat_definitif_id');
    }

    // -----------------------------------------------------------------------
    // Local Scopes
    // -----------------------------------------------------------------------

    /**
     * Scope query hanya untuk penugasan yang aktif pada tanggal tertentu (default: hari ini).
     */
    public function scopeAktif(Builder $query, ?string $date = null): Builder
    {
        $checkDate = $date ?? Carbon::now('Asia/Jakarta')->toDateString();

        return $query->where('status_aktif', 1)
            ->whereDate('tanggal_mulai', '<=', $checkDate)
            ->where(function (Builder $q) use ($checkDate) {
                $q->whereNull('tanggal_selesai')
                  ->orWhereDate('tanggal_selesai', '>=', $checkDate);
            });
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * Mengembalikan nama seksi yang bersih (jika posisi tertulis 'Kepala Seksi Pelayanan' -> 'Seksi Pelayanan').
     */
    public function getCleanSeksiName(): string
    {
        $pos = trim($this->posisi);
        if (str_starts_with($pos, 'Kepala Seksi ')) {
            return 'Seksi ' . substr($pos, strlen('Kepala Seksi '));
        }
        return $pos;
    }

    /**
     * Mengembalikan label jabatan tampilan (contoh: 'PLT Kepala Seksi Pelayanan').
     */
    public function getDisplayTitle(): string
    {
        $jenis = strtoupper($this->jenis ?: 'PLT');
        $pos = trim($this->posisi);

        if (str_starts_with($pos, 'Seksi ')) {
            return "{$jenis} Kepala {$pos}";
        }
        if (str_starts_with($pos, 'Kepala ')) {
            return "{$jenis} {$pos}";
        }
        if (str_starts_with($pos, 'Subbagian ')) {
            return "{$jenis} Kepala {$pos}";
        }

        return "{$jenis} {$pos}";
    }
}
