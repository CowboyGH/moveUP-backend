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
    const INACTIVITY_LIMIT_DAYS = 7;

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
                $inactiveDays = $now->diffInDays($lastActivity, false);

                if (abs($inactiveDays) >= self::INACTIVITY_LIMIT_DAYS) {
                    Auth::logout();

                    try {
                        JWTAuth::invalidate(JWTAuth::getToken());
                    } catch (\Exception $e) {
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'Сессия завершена после 7 дней бездействия.',
                        'code' => 'session_expired_inactivity'
                    ], 401);
                }
            }

            Cache::put($cacheKey, $now, now()->addDays(self::INACTIVITY_LIMIT_DAYS + 1));
        }

        return $next($request);
    }
}
