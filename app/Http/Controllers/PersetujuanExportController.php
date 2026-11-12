<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\PermisiExport;
use App\Exports\RuanganBookingsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Unduh laporan .xlsx dari halaman Persetujuan (Permisi dan Ruangan), sama dengan export di /admin/dashboard:
 * periode dipilih per bulan dan tahun (mulai sampai selesai).
 */
class PersetujuanExportController extends Controller
{
    public function permisi(Request $request): BinaryFileResponse
    {
        $user = Auth::user();
        abort_unless($user->isAdministrator() || $user->isSubbagUmum(), 403, 'Anda tidak memiliki hak untuk mengekspor laporan.');

        [$sm, $sy, $em, $ey] = $this->period($request);

        return Excel::download(new PermisiExport($sm, $sy, $em, $ey), 'laporan-permohonan-izin-' . $this->label($sm, $sy, $em, $ey) . '.xlsx');
    }

    public function ruangan(Request $request): BinaryFileResponse
    {
        abort_unless(Auth::user()->canManageBookings(), 403, 'Anda tidak memiliki hak untuk mengekspor laporan.');

        [$sm, $sy, $em, $ey] = $this->period($request);

        return Excel::download(new RuanganBookingsExport($sm, $sy, $em, $ey), 'laporan-peminjaman-ruangan-' . $this->label($sm, $sy, $em, $ey) . '.xlsx');
    }

    /**
     * Periode dari query string (dibatasi ke rentang wajar; terbalik otomatis ditukar).
     *
     * @return array{0: int, 1: int, 2: int, 3: int} startMonth, startYear, endMonth, endYear
     */
    private function period(Request $request): array
    {
        $sm = max(1, min(12, (int) $request->get('start_month', now()->month)));
        $sy = max(2000, min(now()->year + 1, (int) $request->get('start_year', now()->year)));
        $em = max(1, min(12, (int) $request->get('end_month', now()->month)));
        $ey = max(2000, min(now()->year + 1, (int) $request->get('end_year', now()->year)));

        if (Carbon::create($ey, $em, 1)->lt(Carbon::create($sy, $sm, 1))) {
            [$sm, $sy, $em, $ey] = [$em, $ey, $sm, $sy];
        }

        return [$sm, $sy, $em, $ey];
    }

    private function label(int $sm, int $sy, int $em, int $ey): string
    {
        return str_pad((string) $sm, 2, '0', STR_PAD_LEFT) . '-' . $sy . '_sd_' . str_pad((string) $em, 2, '0', STR_PAD_LEFT) . '-' . $ey;
    }
}
