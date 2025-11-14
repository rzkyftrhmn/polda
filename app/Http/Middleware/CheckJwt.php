<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckJwt
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(format_error('Token not provided'), 401);
        }

        try {
            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();
            if (!$user) {
                return response()->json(format_error('User not found'), 401);
            }
            auth()->shouldUse('api');
            auth()->setUser($user);
        } catch (TokenExpiredException $e) {
            return response()->json(format_error('Token expired'), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(format_error('Token invalid'), 401);
        } catch (JWTException $e) {
            return response()->json(format_error('Token error'), 400);
        }

        return $next($request);
    }
}