<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingStatus: string
{
    case Pending    = 'pending';
    case Disetujui  = 'disetujui';
    case Ditolak    = 'ditolak';
    case Selesai    = 'selesai';
    case Dibatalkan = 'dibatalkan';

    /** Label ramah-baca untuk UI */
    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Proses',
            self::Disetujui  => 'Disetujui',
            self::Ditolak    => 'Ditolak',
            self::Selesai    => 'Dikembalikan',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    /** Warna badge Tailwind untuk tampilan UI */
    public function badgeColor(): string
    {
        return match($this) {
            self::Pending    => 'yellow',
            self::Disetujui  => 'blue',
            self::Ditolak    => 'red',
            self::Selesai    => 'green',
            self::Dibatalkan => 'gray',
        };
    }

    /**
     * Apakah booking ini sedang aktif mengunci kendaraan?
     * Status 'pending' dan 'disetujui' merupakan state yang menempati slot kendaraan.
     */
    public function isActive(): bool
    {
        return in_array($this, [self::Pending, self::Disetujui], strict: true);
    }

    /** Apakah status ini merupakan state terminal (tidak bisa diubah lagi)? */
    public function isTerminal(): bool
    {
        return in_array($this, [self::Ditolak, self::Selesai, self::Dibatalkan], strict: true);
    }

    /**
     * Daftar transisi status yang valid dari state ini.
     * Digunakan untuk validasi state machine di layer service/controller.
     *
     * @return array<BookingStatus>
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::Pending    => [self::Disetujui, self::Ditolak, self::Dibatalkan],
            self::Disetujui  => [self::Selesai, self::Dibatalkan],
            self::Ditolak    => [],
            self::Selesai    => [],
            self::Dibatalkan => [],
        };
    }

    /** Apakah transisi ke status $target diperbolehkan? */
    public function canTransitionTo(BookingStatus $target): bool
    {
        return in_array($target, $this->allowedTransitions(), strict: true);
    }
}
