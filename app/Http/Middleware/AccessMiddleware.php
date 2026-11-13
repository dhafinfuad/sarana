<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware 'access:kunci' — mengizinkan request bila pengguna punya hak akses kunci tersebut
 * menurut matriks Admin → Hak Akses (lihat App\Support\HakAkses).
 */
class AccessMiddleware
{
    public function handle(Request $request, Closure $next, string $key): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        abort_unless($user->hasAccess($key), 403, 'Anda tidak memiliki akses ke halaman ini.');

        return $next($request);
    }
}
