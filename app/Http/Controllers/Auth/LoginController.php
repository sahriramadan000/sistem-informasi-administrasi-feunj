<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk proses authentication user
 */
class LoginController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login user
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        try {
            // Validasi input
            $credentials = $request->validate([
                'login' => 'required|string',
                'password' => 'required|string',
            ]);

            // Cek apakah login menggunakan email atau username
            $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            
            $authCredentials = [
                $loginField => $credentials['login'],
                'password' => $credentials['password'],
                'is_active' => true, // Hanya user aktif yang bisa login
            ];

            // Proses authentication
            if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                
                Log::info('User logged in', [
                    'user_id' => Auth::id(),
                    'login_field' => $loginField,
                    'ip' => $request->ip()
                ]);

                return redirect()->intended(route('dashboard'));
            }

            return back()
                ->withErrors([
                    'login' => 'Email/Username atau password salah.',
                ])
                ->withInput($request->only('login'));

        } catch (\Throwable $e) {
            Log::error('Login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors([
                    'login' => 'Terjadi kesalahan saat login. Silakan coba lagi.',
                ])
                ->withInput($request->only('login'));
        }
    }

    /**
     * Proses logout user
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        try {
            $userId = Auth::id();
            
            Auth::logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            Log::info('User logged out', ['user_id' => $userId]);

            return redirect()->route('login');
            
        } catch (\Throwable $e) {
            Log::error('Logout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login');
        }
    }
}