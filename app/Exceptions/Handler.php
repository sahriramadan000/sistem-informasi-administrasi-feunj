<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle Token Mismatch Exception (419 Error)
        $this->renderable(function (TokenMismatchException $e, $request) {
            // Jika request adalah logout, langsung redirect ke login
            if ($request->is('logout')) {
                // Clear session dan redirect ke login
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
            }
            
            // Untuk request lain, redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'Sesi Anda telah berakhir. Silakan refresh halaman dan coba lagi.');
        });
    }
}
