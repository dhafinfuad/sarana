<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan_id',
        'stok',
        'stok_minimum',
        'image_path',
        'created_by'
    ];

    public function satuan()
    {
        return $this->belongsTo(BonSatuan::class, 'satuan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requestItems()
    {
        return $this->hasMany(BonRequestItem::class, 'bon_item_id');
    }
}
