<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware untuk memeriksa role user
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        try {
            // Cek apakah user sudah login
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            // Parse roles dari parameter (dipisahkan dengan koma)
            $allowedRoles = explode(',', $roles);
            $userRole = Auth::user()->role;

            // Cek apakah role user ada dalam daftar yang diizinkan
            if (!in_array($userRole, $allowedRoles)) {
                // Log unauthorized access attempt
                \Log::warning('Unauthorized access attempt', [
                    'user_id' => Auth::id(),
                    'user_role' => $userRole,
                    'required_roles' => $allowedRoles,
                    'route' => $request->route()->getName(),
                    'ip' => $request->ip()
                ]);

                // Redirect dengan pesan error
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            return $next($request);

        } catch (\Throwable $e) {
            \Log::error('Role middleware error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? null
            ]);

            // Redirect ke dashboard dengan pesan error generik
            return redirect()->route('dashboard')
                ->with('error', 'Terjadi kesalahan saat memeriksa hak akses.');
        }
    }
}