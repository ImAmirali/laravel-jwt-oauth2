<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @param string $expectedType (access or refresh)
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $expectedType='access'): Response
    {
        try {
            if ($expectedType === 'refresh' && $request->hasCookie('refresh_token')) {
                JWTAuth::setToken($request->cookie('refresh_token'));
            } else {
                JWTAuth::parseToken();
            }

            $payload = JWTAuth::getPayload();

            $tokenType = $payload->get('token_type');
            if ($tokenType !== $expectedType) {
                return response()->json([
                    'error' => 'Invalid token type. Expected: ' . $expectedType,
                ], 401);
            }

            if ($expectedType === 'access') {
                $user = JWTAuth::authenticate();
                if (!$user) {
                    return response()->json(['erroe' => 'کاربر پیدا نشد!']);
                }
                auth()->setUser($user);
            }
        }
        catch (TokenInvalidException $e) {
            return response()->json(['error' => 'access token نامعتبر است.'], 401);
        }
        catch (TokenExpiredException $e) {
            return response()->json(['error' => 'توکن منقضی شده است!'], 401);
        }
        catch (JWTException $e) {
            return response()->json(['error' => 'توکن وجود ندارد!'], 401);
        }
        return $next($request);
    }
}
