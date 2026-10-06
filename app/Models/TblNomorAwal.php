<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblNomorAwal extends Model
{
    protected $table = 'tbl_nomor_awal';
    public $timestamps = false;

    protected $fillable = [
        'jenis_surat',
        'jenis_pj',
        'tahun',
        'nomor_awal',
    ];
}
