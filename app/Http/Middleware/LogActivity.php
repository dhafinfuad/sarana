<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Map of route names to their label and group
     */
    protected array $routeMap = [
        'peminjaman.index' => ['label' => 'Peminjaman Kendaraan', 'group' => 'Sarana'],
        'peminjaman.riwayat' => ['label' => 'Riwayat Peminjaman', 'group' => 'Sarana'],
        'permisi.permohonan' => ['label' => 'Permohonan Permisi', 'group' => 'Permisi'],
        'permisi.persetujuan' => ['label' => 'Persetujuan Permisi', 'group' => 'Permisi'],
        'asmara.permohonan' => ['label' => 'Riwayat Surat', 'group' => 'Asmara'],
        'asmara.admin' => ['label' => 'Semua Surat Keluar', 'group' => 'Asmara'],
        'bona.permohonan' => ['label' => 'Bon Saya', 'group' => 'Bon ATK'],
        'bona.kelola' => ['label' => 'Persetujuan Bon', 'group' => 'Bon ATK'],
        'bona.master-barang' => ['label' => 'Master Barang', 'group' => 'Bon ATK'],
        'admin.dashboard' => ['label' => 'Dashboard Peminjaman', 'group' => 'Sarana'],
        'admin.user' => ['label' => 'Manajemen Pegawai', 'group' => 'Admin'],
        'admin.fleet' => ['label' => 'Manajemen Armada', 'group' => 'Sarana'],
        'admin.log' => ['label' => 'Log Aplikasi', 'group' => 'Admin'],
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log GET requests for authenticated users
        if (Auth::check() && $request->isMethod('GET')) {
            $routeName = $request->route() ? $request->route()->getName() : null;
            
            // Log only mapped routes to prevent spam
            if ($routeName && array_key_exists($routeName, $this->routeMap)) {
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'route_name' => $routeName,
                    'page_label' => $this->routeMap[$routeName]['label'],
                    'page_group' => $this->routeMap[$routeName]['group'],
                    'url' => $request->fullUrl(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return $response;
    }
}
