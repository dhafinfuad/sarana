<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bon_request_id',
        'bon_item_id',
        'kode_barang',
        'nama_barang',
        'satuan',
        'jumlah_diminta',
        'jumlah_disetujui',
        'jumlah_diberikan',
        'catatan',
    ];

    public function bonRequest()
    {
        return $this->belongsTo(BonRequest::class);
    }

    public function bonItem()
    {
        return $this->belongsTo(BonItem::class);
    }
}
