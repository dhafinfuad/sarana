<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SuratKeluar extends Model
{
    protected $table = 'surat_keluar';
    protected $primaryKey = 'id_surat_keluar';
    public $timestamps = false; // We are handling tgl_rekam manually

    protected $fillable = [
        'vrb_id',
        'jenis_surat',
        'jenis_pj',
        'nomor_surat',
        'tahun_surat',
        'tgl_surat',
        'perihal',
        'tujuan_surat',
        'perekam',
        'tgl_rekam',
        'keterangan',
        'link_id',
        'tgl_kirim',
        'pengirim',
        'batal',
        'keterangan_pengiriman',
    ];

    protected $casts = [
        'nomor_surat' => 'integer',
        'tahun_surat' => 'integer',
        'tgl_surat' => 'date',
        'tgl_rekam' => 'date',
    ];

    /**
     * Scope a query to only include records by a specific perekam (nip_pendek).
     */
    public function scopeByPerekam($query, $nip)
    {
        return $query->where('perekam', $nip);
    }

    /**
     * Scope a query to search letters by various fields including the full letter number.
     */
    public function scopeSearch($query, $search)
    {
        $term = trim((string) $search);
        if ($term === '') {
            return $query;
        }

        $driver = DB::connection()->getDriverName();
        $concatNomor = $driver === 'sqlite'
            ? "COALESCE(jenis_surat, '') || COALESCE(nomor_surat, '') || COALESCE(jenis_pj, '') || COALESCE(tahun_surat, '')"
            : "CONCAT(COALESCE(jenis_surat, ''), COALESCE(nomor_surat, ''), COALESCE(jenis_pj, ''), COALESCE(tahun_surat, ''))";

        return $query->where(function ($sub) use ($term, $concatNomor) {
            $sub->where('perihal', 'like', '%' . $term . '%')
                ->orWhere('tujuan_surat', 'like', '%' . $term . '%')
                ->orWhere('nomor_surat', 'like', '%' . $term . '%')
                ->orWhere('jenis_surat', 'like', '%' . $term . '%')
                ->orWhere('jenis_pj', 'like', '%' . $term . '%')
                ->orWhere('keterangan', 'like', '%' . $term . '%')
                ->orWhere('perekam', 'like', '%' . $term . '%')
                ->orWhereRaw("{$concatNomor} LIKE ?", ['%' . $term . '%'])
                ->orWhereHas('user', function ($u) use ($term) {
                    $u->where('name', 'like', '%' . $term . '%')
                      ->orWhere('nip_pendek', 'like', '%' . $term . '%')
                      ->orWhere('nip', 'like', '%' . $term . '%');
                });

            $cleanTerm = str_replace(['-', ' '], '', $term);
            if ($cleanTerm !== '') {
                $sub->orWhereRaw("REPLACE(REPLACE({$concatNomor}, '-', ''), ' ', '') LIKE ?", ['%' . $cleanTerm . '%']);
            }
        });
    }

    /**
     * Get the full string of the nomor surat.
     * Example: LAP-999/WPJ.12/KP.0903/2021
     */
    public function getNomorLengkapAttribute(): string
    {
        return $this->jenis_surat . $this->nomor_surat . $this->jenis_pj . $this->tahun_surat;
    }

    /**
     * Get the user who recorded this (konseptor).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'perekam', 'nip_pendek');
    }
}
