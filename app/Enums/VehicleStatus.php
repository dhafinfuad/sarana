<?php

declare(strict_types=1);

namespace App\Enums;

enum VehicleStatus: string
{
    case Tersedia      = 'tersedia';
    case TidakTersedia = 'tidak_tersedia';

    /** Label ramah-baca untuk UI */
    public function label(): string
    {
        return match($this) {
            self::Tersedia      => 'Tersedia',
            self::TidakTersedia => 'Tidak Tersedia (Maintenance)',
        };
    }

    /** Apakah kendaraan bisa dipilih pada form pengajuan pegawai? */
    public function isAvailableForBooking(): bool
    {
        return $this === self::Tersedia;
    }

    /** Warna badge untuk tampilan UI */
    public function badgeColor(): string
    {
        return match($this) {
            self::Tersedia      => 'green',
            self::TidakTersedia => 'red',
        };
    }
}
