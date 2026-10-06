<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VehicleStatus;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['plat_nomor', 'nama_kendaraan', 'image_path', 'status'])]
class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    // -----------------------------------------------------------------------
    // Casts
    // -----------------------------------------------------------------------

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => VehicleStatus::class,
        ];
    }

    // -----------------------------------------------------------------------
    // Scopes (Query Lokal)
    // -----------------------------------------------------------------------

    /**
     * Scope: hanya kendaraan berstatus 'tersedia'.
     * Digunakan pada dropdown form pengajuan pegawai agar tidak menampilkan
     * kendaraan yang sedang maintenance / tidak tersedia.
     *
     * @param  Builder<Vehicle>  $query
     * @return Builder<Vehicle>
     */
    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('status', VehicleStatus::Tersedia);
    }

    // -----------------------------------------------------------------------
    // Helper Methods (Strict Typing)
    // -----------------------------------------------------------------------

    /** Apakah kendaraan ini tersedia untuk dipinjam? */
    public function isTersedia(): bool
    {
        return $this->status === VehicleStatus::Tersedia;
    }

    /**
     * Toggle status kendaraan antara 'tersedia' dan 'tidak_tersedia'.
     * Digunakan Admin untuk mengaktifkan / menonaktifkan kendaraan (maintenance toggle).
     */
    public function toggleStatus(): bool
    {
        $this->status = $this->isTersedia()
            ? VehicleStatus::TidakTersedia
            : VehicleStatus::Tersedia;

        return $this->save();
    }

    /**
     * URL publik foto kendaraan, fallback ke null jika tidak ada foto.
     */
    public function imageUrl(): ?string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : null;
    }

    // -----------------------------------------------------------------------
    // Relasi Eloquent
    // -----------------------------------------------------------------------

    /**
     * Semua histori peminjaman kendaraan ini.
     *
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'vehicle_id');
    }

    /**
     * Peminjaman aktif (pending/disetujui) yang saat ini mengunci kendaraan ini.
     *
     * @return HasMany<Booking, $this>
     */
    public function activeBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'vehicle_id')
                    ->whereIn('status', ['pending', 'disetujui']);
    }
}
