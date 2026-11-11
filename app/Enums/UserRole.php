<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Peran utama user, disimpan pada kolom `jabatan`.
 *
 * Nilai enum mengikuti string jabatan yang dicek oleh App\Models\User
 * (isAdministrator(), isKepalaKantor(), dst.).
 */
enum UserRole: string
{
    case Administrator = 'Administrator';
    case KepalaKantor  = 'Kepala Kantor';
    case Pegawai       = 'Pelaksana';

    /** Label ramah-baca untuk UI */
    public function label(): string
    {
        return match($this) {
            self::Administrator => 'Administrator',
            self::KepalaKantor  => 'Kepala Kantor',
            self::Pegawai       => 'Pegawai',
        };
    }
}
