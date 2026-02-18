<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class TrackUserActivity
{
    /**
     * Время неактивности в часах до завершения сессии
     */
    const INACTIVITY_LIMIT_HOURS = 24;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cacheKey = 'user_last_activity_' . $user->id;

            $lastActivity = Cache::get($cacheKey);
            $now = Carbon::now();

            if ($lastActivity) {
                if (!$lastActivity instanceof Carbon) {
                    if (is_string($lastActivity)) {
                        $lastActivity = Carbon::parse($lastActivity);
                    } else {
                        $lastActivity = Carbon::create($lastActivity);
                    }
                }

                $lastActivity = $lastActivity->timezone($now->timezone);
                $inactiveHours = $now->diffInHours($lastActivity, false);

                if (abs($inactiveHours) >= self::INACTIVITY_LIMIT_HOURS) {
                    Auth::logout();

                    try {
                        JWTAuth::invalidate(JWTAuth::getToken());
                    } catch (\Exception $e) {
                        // Логирование ошибки при необходимости
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'Сессия завершена из-за длительного бездействия.',
                        'code' => 'session_expired_inactivity'
                    ], 401);
                }
            }

            Cache::put($cacheKey, $now, now()->addHours(self::INACTIVITY_LIMIT_HOURS + 1));
        }

        return $next($request);
    }
}
