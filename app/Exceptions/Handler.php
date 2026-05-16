<?php

namespace App\Exceptions;

use App\Services\ErrorTrackingService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
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
            // Log all exceptions using ErrorTrackingService
            if ($this->shouldReport($e)) {
                $context = class_basename($e);
                $additionalData = [
                    'route' => request()->route()?->getName(),
                    'method' => request()->method(),
                ];
                
                $errorId = ErrorTrackingService::logError($e, $context, $additionalData);
                
                // Store error ID in session for display
                if (request()->hasSession()) {
                    session(['last_error_id' => $errorId]);
                }
            }
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

        // Handle Validation Exception
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        });

        // Handle generic exceptions
        $this->renderable(function (Throwable $e, $request) {
            // Only render if not already handled
            if ($this->shouldReport($e) && !($e instanceof TokenMismatchException) && !($e instanceof ValidationException)) {
                $errorId = session('last_error_id', 'Unknown');
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'An error occurred',
                        'error_id' => $errorId,
                    ], 500);
                }

                // For regular requests, show error page
                return response()->view('errors.500', ['error_id' => $errorId], 500);
            }
        });
    }
}
