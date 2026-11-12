<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Data modal Detail pada daftar riwayat, mengikuti modal Detail di halaman Peminjaman Kendaraan:
 * No. Request + Status, Mulai + Selesai, pasangan info bebas, lalu blok keterangan.
 */
final class RiwayatDetail
{
    /**
     * Warna badge status pada modal Detail (sama dengan modal Detail Peminjaman Kendaraan).
     */
    public static function statusClass(string $status): string
    {
        return match ($status) {
            'pending'   => 'bg-amber-50 text-amber-700 border border-amber-200',
            'disetujui' => 'bg-brand-50 text-brand-700 border border-brand-200',
            'selesai'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            default     => 'bg-rose-50 text-rose-700 border border-rose-200',
        };
    }

    /**
     * @param  array<int, array<int, array{0: string, 1: string, 2: string}>>  $pairs  Baris berisi dua sel [label, nilai, gaya]; gaya: bold | chip | mono | normal
     * @return array<string, mixed>
     */
    public static function make(
        string $title,
        string $code,
        string $status,
        string $mulai,
        string $selesai,
        array $pairs,
        string $noteLabel,
        ?string $noteHeading,
        string $noteText,
    ): array {
        return [
            'title'  => $title,
            'code'   => $code,
            'status' => ['label' => ucfirst($status), 'class' => self::statusClass($status)],
            'mulai'  => $mulai,
            'selesai' => $selesai,
            'pairs'  => array_map(
                fn (array $row) => array_map(fn (array $c) => ['label' => $c[0], 'value' => $c[1], 'style' => $c[2]], $row),
                $pairs
            ),
            'note'   => ['label' => $noteLabel, 'heading' => $noteHeading, 'text' => $noteText],
        ];
    }
}
