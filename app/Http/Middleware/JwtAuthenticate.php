<?php

namespace App\Http\Middleware;

use App\Http\Responses\ErrorResponse;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtAuthenticate
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return ErrorResponse::make(
                    ErrorResponse::UNAUTHORIZED,
                    'Неавторизован. Требуется авторизация.',
                    401
                );
            }

        } catch (TokenExpiredException $e) {
            return ErrorResponse::make(
                ErrorResponse::TOKEN_EXPIRED,
                'Токен истек',
                401
            );
        } catch (TokenInvalidException $e) {
            return ErrorResponse::make(
                ErrorResponse::UNAUTHORIZED,
                'Неавторизован. Требуется авторизация.',
                401
            );
        } catch (JWTException $e) {
            return ErrorResponse::make(
                ErrorResponse::UNAUTHORIZED,
                'Неавторизован. Требуется авторизация.',
                401
            );
        }

        return $next($request);
    }
}
