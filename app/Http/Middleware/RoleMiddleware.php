<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        try {
            // Cek apakah user sudah login
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            // Support dua cara penulisan:
            // 1. 'role:admin,operator' → Laravel kirim sebagai satu string "admin,operator"
            // 2. 'role:admin:operator' → Laravel kirim sebagai array ["admin", "operator"]
            $allowedRoles = [];
            foreach ($roles as $role) {
                foreach (explode(',', $role) as $r) {
                    $allowedRoles[] = trim($r);
                }
            }

            $userRole = Auth::user()->role;

            // Cek apakah role user ada dalam daftar yang diizinkan
            if (!in_array($userRole, $allowedRoles)) {
                Log::warning('Unauthorized access attempt', [
                    'user_id'        => Auth::id(),
                    'user_role'      => $userRole,
                    'required_roles' => $allowedRoles,
                    'route'          => $request->route()->getName(),
                    'ip'             => $request->ip()
                ]);

                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            return $next($request);

        } catch (\Throwable $e) {
            Log::error('Role middleware error', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? null
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Terjadi kesalahan saat memeriksa hak akses.');
        }
    }
}