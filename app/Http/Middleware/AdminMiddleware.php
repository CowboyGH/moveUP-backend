<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        $user = $request->user();

        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Упс! Доступ к странице, которую Вы ищите, запрещен :('
            ], 403);
        }

        return $next($request);
    }
}
