<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_admin == '1') {
            return $next($request);
        }

        // Jika bukan admin, arahkan ke halaman lain (misalnya, halaman home atau 403)
        return redirect()->back()->withErrors(['error' => 'Anda tidak memiliki akses ke halaman ini']);    
    }
}
