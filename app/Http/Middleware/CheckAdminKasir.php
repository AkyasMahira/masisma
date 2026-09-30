<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminKasir
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login dan punya role admin ATAU kasir
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'kasir'])) {
            return $next($request);
        }
        
        // Jika bukan admin/kasir, tendang keluar
        abort(403, 'Akses Terbatas: Hanya untuk Admin atau Kasir.');
    }
}