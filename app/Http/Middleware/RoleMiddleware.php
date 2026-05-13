<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Middleware ini memproteksi route berdasarkan role user.
     * Cara penggunaan di routes/web.php:
     *   ->middleware('role:admin')        → hanya admin
     *   ->middleware('role:admin,kasir')  → admin atau kasir
     *
     * @param  string  ...$roles  Daftar role yang diizinkan (dipisah koma)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Cek apakah role user ada dalam daftar yang diizinkan
        if (! in_array($request->user()->role, $roles)) {
            // Jika tidak punya akses, tolak dengan pesan error
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}
