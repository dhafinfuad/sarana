<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Gaya badge status yang dipakai bersama pada daftar riwayat (Permisi, Ruangan).
 */
final class StatusBadge
{
    /**
     * @return array{bg: string, dot: string, label: string}
     */
    public static function for(string $status): array
    {
        return match ($status) {
            'pending'    => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500', 'label' => 'Pending'],
            'disetujui'  => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500', 'label' => 'Disetujui'],
            'ditolak'    => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500', 'label' => 'Ditolak'],
            'selesai'    => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'dot' => 'bg-blue-500', 'label' => 'Selesai'],
            'dibatalkan' => ['bg' => 'bg-slate-100 text-slate-600 border-slate-200', 'dot' => 'bg-slate-400', 'label' => 'Dibatalkan'],
            default      => ['bg' => 'bg-slate-50 text-slate-700 border-slate-200', 'dot' => 'bg-slate-500', 'label' => ucfirst($status)],
        };
    }

    /**
     * Badge status pada tabel persetujuan, sama dengan /admin/dashboard:
     * Pending amber, Disetujui biru, Selesai hijau, selainnya (Ditolak, Dibatalkan) merah.
     *
     * @return array{bg: string, dot: string, label: string}
     */
    public static function admin(string $status): array
    {
        $style = match ($status) {
            'pending'   => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'],
            'disetujui' => ['bg' => 'bg-brand-50 text-brand-700 border-brand-200', 'dot' => 'bg-brand-500'],
            'selesai'   => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'],
            default     => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-500'],
        };

        return $style + ['label' => ucfirst($status)];
    }
}
