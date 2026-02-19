<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return ApiResponse::error(
                ErrorResponse::FORBIDDEN,
                'Доступ запрещен. Только для администраторов.',
                403
            );
        }

        return $next($request);
    }
}
