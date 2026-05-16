<?php

namespace App\Http\Controllers;

use App\Services\ErrorTrackingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Throwable;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Handle database/system errors with error tracking
     * 
     * @param Throwable $exception
     * @param string $context - context for logging
     * @param string $userMessage - user-friendly message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleError(Throwable $exception, string $context, string $userMessage = 'Terjadi kesalahan. Silakan coba lagi.'): \Illuminate\Http\RedirectResponse
    {
        $errorId = ErrorTrackingService::logError($exception, $context);
        
        return back()
            ->withInput()
            ->withErrors(['error' => $userMessage]);
    }
}
