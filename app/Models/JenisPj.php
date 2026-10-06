<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPj extends Model
{
    protected $table = 'jenis_pj';
    protected $primaryKey = 'jenis_pj';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['jenis_pj'];
}
