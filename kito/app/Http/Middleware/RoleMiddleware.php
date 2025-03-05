<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // // Memeriksa apakah pengguna memiliki salah satu role yang diizinkan
        // if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
        //     // Jika tidak, arahkan ke halaman yang diinginkan (misalnya ke halaman home)
        //     return redirect()->back()->withErrors(['error' => 'Anda tidak memiliki akses ke halaman ini']);
        // }
        if (Auth::check()) {
            // Cek jika pengguna memiliki role admin atau leader
            if (Auth::user()->is_admin == '1' || Auth::user()->is_leader == '1') {
                return $next($request);
            }
        }
                // dd('true');
        return redirect('dashboard')->withErrors(['error' => 'Anda tidak memiliki akses ke halaman ini']);
    }
}
