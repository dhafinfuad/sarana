<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// Halaman utama — redirect ke login jika belum auth, ke dashboard jika sudah
// ---------------------------------------------------------------------------
Route::get('/', function () {
    if (Auth::check()) {
        return redirect(Auth::user()->homeRoute());
    }
    return redirect()->route('login');
});

// ---------------------------------------------------------------------------
// FLOW 1: Otentikasi — hanya untuk guest (belum login)
// Menggunakan Livewire component sebagai handler (skills-livewire.md §2)
// ---------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    /**
     * GET /login  — Render Livewire Login component
     * POST /login — Tidak ada; Livewire menangani via wire:submit.prevent
     */
    Route::get('/login', Login::class)->name('login');
});

// ---------------------------------------------------------------------------
// Logout — hanya untuk user yang sudah login
// ---------------------------------------------------------------------------
Route::middleware('auth')->post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// ---------------------------------------------------------------------------
// Dummy Route: Cek & Ubah Password
// ---------------------------------------------------------------------------
Route::middleware('auth')->post('/check-current-password', function (\Illuminate\Http\Request $request) {
    $isValid = \Illuminate\Support\Facades\Hash::check($request->password, Auth::user()->password);
    return response()->json(['valid' => $isValid]);
})->name('password.check');

Route::middleware('auth')->put('/change-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'min:6', 'confirmed'],
    ], [
        'current_password.current_password' => 'Password lama yang Anda masukkan salah.',
        'password.min' => 'Password baru Anda kurang dari 6 karakter.',
        'password.confirmed' => 'Password baru yang Anda masukkan tidak sesuai/sama.',
    ]);

    // Dummy success (in real app, update the user's password here)
    $user = Auth::user();
    $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
    $user->save();

    return back()->with('success', 'Password berhasil diubah.');
})->name('password.update');

// ---------------------------------------------------------------------------
// Route Admin — dilindungi auth + role
// FLOW 3: Dasbor Analitik & Approval (flow-brief.md)
// ---------------------------------------------------------------------------
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        /**
         * GET /admin/dashboard — Dasbor analitik
         * Menggunakan Livewire component Admin/Dashboard
         */
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->middleware('access:kendaraan.dashboard')->name('dashboard');

        /**
         * GET /admin/user — Manajemen User
         */
        Route::get('/user', \App\Livewire\Admin\UserManagement::class)->middleware('access:admin.user')->name('user');

        /**
         * GET /admin/fleet — Manajemen Armada Kendaraan (Flow 4)
         */
        Route::get('/fleet', \App\Livewire\Admin\Fleet::class)->middleware('access:kendaraan.armada')->name('fleet');

        /**
         * GET /admin/export-bookings?month=&year= — Unduh laporan peminjaman .xlsx
         */
        Route::get('/export-bookings', function (\Illuminate\Http\Request $request) {
            $startMonth = max(1, min(12, (int) $request->get('start_month', now()->month)));
            $startYear  = max(2000, min(now()->year + 1, (int) $request->get('start_year', now()->year)));
            $endMonth   = max(1, min(12, (int) $request->get('end_month', now()->month)));
            $endYear    = max(2000, min(now()->year + 1, (int) $request->get('end_year', now()->year)));

            // Pastikan end tidak lebih awal dari start
            $start = \Carbon\Carbon::create($startYear, $startMonth, 1);
            $end   = \Carbon\Carbon::create($endYear, $endMonth, 1);
            if ($end->lt($start)) {
                [$startMonth, $startYear, $endMonth, $endYear] = [$endMonth, $endYear, $startMonth, $startYear];
            }

            $label    = str_pad((string) $startMonth, 2, '0', STR_PAD_LEFT) . '-' . $startYear
                      . '_sd_'
                      . str_pad((string) $endMonth, 2, '0', STR_PAD_LEFT) . '-' . $endYear;
            $filename = 'laporan-peminjaman-' . $label . '.xlsx';

            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\BookingsExport($startMonth, $startYear, $endMonth, $endYear),
                $filename
            );
        })->middleware('access:kendaraan.pengajuan_export')->name('export-bookings');
    });

// Route khusus Administrator
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/log', \App\Livewire\Admin\ActivityLog::class)->middleware('access:admin.log')->name('log');
        Route::get('/hak-akses', \App\Livewire\Admin\HakAkses::class)->middleware('access:admin.hak_akses')->name('hak-akses');
    });

// ---------------------------------------------------------------------------
// Route Pegawai — dilindungi auth + role:Pegawai
// FLOW 2: Pengajuan Peminjaman (flow-brief.md)
// ---------------------------------------------------------------------------
Route::middleware(['auth'])
    ->group(function () {
        /**
         * GET /peminjaman — Form & riwayat peminjaman pegawai (UI dummy FLOW 2)
         * - Menampilkan form input dan riwayat.
         * - Di-handle oleh komponen Livewire `Peminjaman\Index`.
         */
        Route::get('/peminjaman', App\Livewire\Peminjaman\Index::class)->name('peminjaman.index');

        /**
         * GET /permisi/permohonan — Form & riwayat permohonan izin keluar (Permohonan)
         */
        Route::get('/permisi/permohonan', App\Livewire\Permisi\Dashboard::class)->name('permisi.permohonan');
        Route::get('/permisi/riwayat', App\Livewire\Permisi\Riwayat::class)->name('permisi.riwayat');
        Route::get('/permisi/export', [\App\Http\Controllers\PersetujuanExportController::class, 'permisi'])->name('permisi.export');

        /**
         * GET /permisi/persetujuan — Halaman Persetujuan Permisi (Reviewer)
         */
        Route::get('/permisi/persetujuan', App\Livewire\Permisi\Dashboard::class)->name('permisi.persetujuan');

        /**
         * GET /peminjaman/riwayat — Riwayat lengkap peminjaman pegawai
         */
        Route::get('/peminjaman/riwayat', \App\Livewire\Peminjaman\Riwayat::class)->name('peminjaman.riwayat');

        /**
         * Peminjaman Ruangan — pengajuan pegawai & persetujuan (Administrator / Subbag Umum)
         */
        Route::get('/ruangan/peminjaman', \App\Livewire\Ruangan\Index::class)->name('ruangan.peminjaman');
        Route::get('/ruangan/persetujuan', \App\Livewire\Ruangan\Index::class)->defaults('mode', 'reviewer')->name('ruangan.persetujuan');
        Route::get('/ruangan/kelola', \App\Livewire\Ruangan\Kelola::class)->name('ruangan.kelola');
        Route::get('/ruangan/riwayat', \App\Livewire\Ruangan\Riwayat::class)->name('ruangan.riwayat');
        Route::get('/ruangan/export', [\App\Http\Controllers\PersetujuanExportController::class, 'ruangan'])->name('ruangan.export');

        /**
         * BONA — Bon ATK (Permintaan)
         */
        Route::get('/bona/permohonan', \App\Livewire\Bona\Permohonan::class)->name('bona.permohonan');
        Route::get('/bona/permohonan/{id}/print', [\App\Http\Controllers\BonaPrintController::class, 'print'])->name('bona.print');
        Route::get('/bona/kelola', \App\Livewire\Bona\Kelola::class)->name('bona.kelola');
        Route::get('/bona/master-barang', \App\Livewire\Bona\MasterBarang::class)->name('bona.master-barang');

        /**
         * Asmara — Pengambilan Nomor Surat Keluar
         */
        Route::get('/asmara/permohonan', \App\Livewire\Asmara\Dashboard::class)->name('asmara.permohonan');
        Route::get('/asmara/admin', \App\Livewire\Asmara\Dashboard::class)->name('asmara.admin');
    });

// ---------------------------------------------------------------------------
// PWA Offline fallback — dicache oleh Service Worker
// ---------------------------------------------------------------------------
Route::get('/offline', function () {
    return view('offline');
})->name('offline');
