<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = ['nama', 'lokasi', 'kapasitas', 'fasilitas', 'status', 'urutan'];

    /** @param Builder<Room> $query */
    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('status', 'tersedia');
    }

    /** @param Builder<Room> $query */
    public function scopeBerurutan(Builder $query): Builder
    {
        return $query->orderBy('urutan')->orderBy('nama')->orderBy('id');
    }

    /** @return HasMany<RoomBooking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(RoomBooking::class);
    }
}
