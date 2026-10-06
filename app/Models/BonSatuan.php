<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonSatuan extends Model
{
    use HasFactory;

    protected $fillable = ['nama_satuan'];

    public function items()
    {
        return $this->hasMany(BonItem::class, 'satuan_id');
    }
}
