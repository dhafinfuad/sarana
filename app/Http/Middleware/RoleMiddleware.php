<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware — Proteksi route berdasarkan peran pengguna.
 *
 * Penggunaan di routes/web.php:
 *   middleware('role:administrator')
 *   middleware('role:administrator,kepala_kantor')
 *
 * @see flow-brief.md §FLOW 1 — Middleware Protection
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     * @param  string                      ...$roles  Role yang diizinkan (dari route parameter)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Special case: jika route membutuhkan role 'Pegawai'
        if (in_array('Pegawai', $roles, strict: true)) {
            if ($user->isPegawai()) {
                return $next($request);
            }
            // Jika Admin (Seksi Umum/PKD) memaksa masuk route pegawai -> kembalikan ke Dasbor Admin
            return redirect()->route('admin.dashboard');
        }

        // Cek apakah jabatan atau seksi user ada di daftar yang diizinkan (termasuk PLH/PLT aktif)
        $managedSeksi = $user->getManagedSeksiList();
        $isRoleAllowed = in_array($user->jabatan, $roles, strict: true)
            || in_array($user->seksi, $roles, strict: true)
            || !empty(array_intersect($roles, $managedSeksi))
            || (in_array('Kepala Kantor', $roles, strict: true) && $user->isKepalaKantor())
            || (in_array('Subbagian Umum dan Kepatuhan Internal', $roles, strict: true) && $user->isSubbagUmum());

        if (! $isRoleAllowed) {
            // Pegawai yang memaksa akses /admin/* → redirect ke halaman peminjaman
            if (! $user->canAccessAdminDashboard()) {
                return redirect()->route('peminjaman.index');
            }

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
