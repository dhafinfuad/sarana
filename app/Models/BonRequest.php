<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_bon',
        'nomor_tiket',
        'user_id',
        'keperluan',
        'status',
        'catatan_petugas',
        'processed_by',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(BonRequestItem::class);
    }

    public static function generateNoBon(): string
    {
        $yearMonth = date('Ym');
        $lastBon = self::where('no_bon', 'like', "BON-{$yearMonth}-%")
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastBon) {
            $parts = explode('-', $lastBon->no_bon);
            $lastNumber = intval(end($parts));
        }

        return 'BON-' . $yearMonth . '-' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }

    public function isMenunggu(): bool
    {
        return $this->status === 'menunggu';
    }
}
