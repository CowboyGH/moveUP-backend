<?php

namespace App\Http\Middleware;

use App\Http\Responses\ErrorResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class TrackUserActivity
{
    const INACTIVITY_LIMIT_DAYS = 7;

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cacheKey = 'user_last_activity_' . $user->id;

            $lastActivity = Cache::get($cacheKey);
            $now = Carbon::now();

            if ($lastActivity) {
                $lastActivity = $lastActivity instanceof Carbon
                    ? $lastActivity
                    : Carbon::parse($lastActivity)->timezone($now->timezone);

                $inactiveDays = $now->diffInDays($lastActivity, false);

                if (abs($inactiveDays) >= self::INACTIVITY_LIMIT_DAYS) {
                    Auth::logout();

                    try {
                        JWTAuth::invalidate(JWTAuth::getToken());
                    } catch (\Exception $e) {
                    }

                    return ErrorResponse::make(
                        ErrorResponse::SESSION_EXPIRED_INACTIVITY,
                        'Сессия завершена после ' . self::INACTIVITY_LIMIT_DAYS . ' дней бездействия.',
                        401
                    );
                }
            }

            Cache::put($cacheKey, $now, now()->addDays(self::INACTIVITY_LIMIT_DAYS + 1));
        }

        return $next($request);
    }
}
