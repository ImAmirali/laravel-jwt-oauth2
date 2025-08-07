<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * validates incoming registration data and create a new user.
     *
     * @param Request  $request
     * @response 201 {
     *      "success": true,
     *      "message": "ثبت نام کاربر با موفقیت انجام شد.",
     *      "user": {
     *          "id": 1,
     *          "name": "amir",
     *          "email": "something@gmail.com",
     *          "email_verified_at": null,
     *          "created_at": "2025-08-05T17:08:28.000000Z",
     *          "updated_at": "2025-08-05T17:08:28.000000Z"
     *      }
     * }
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
           'name' => ['required', 'string', 'max:255'],
           'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
           'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ثبت نام کاربر با موفقیت انجام شد.',
            'user' => $user,
        ], 201);
    }

    /**
     * Authenticate user and return access and refresh tokens.
     *
     * - Validates login credentials.
     * - Issues a JWT access token and a refresh token (in HTTP-only cookie).
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        if (!$accessToken = auth()->attempt($credentials)) {
            return response()->json(['status' => 'error', 'message' => 'کاربری با این مشخصات ثبت نشده است!'], 401);
        }

        $refreshToken = JWTAuth::claims(['refresh_token' => true])->fromUser(auth()->user());
        $cookie = Cookie::make(
            'refresh_token',
            $refreshToken,
            config('jwt.refresh_ttl'),
            null,
            null,
            true,
            true,
            false,
            'Strict'
        );

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL()* 60,
            'user' => auth()->user()    ,
        ])->withCookie($cookie);
    }

    /**
     * Refresh the access token using the refresh token from cookie.
     *
     * - Validates the refresh token.
     * - Invalidates the old one.
     * - Issues a new access token and new refresh token.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $token = $request->cookie('refresh_token');
            if (!$token) {
                return response()->json(['error' => 'در کوکی وجود ندارد! refresh_token '], 401);
            }

            JWTAuth::setToken($token);

            $payload = JWTAuth::getPayload();
            if (!$payload->get('refresh_token')) {
                return response()->json(['error' => 'refresh_token نامعتبر است!'], 401);
            }

            $user = JWTAuth::toUser();
            JWTAuth::invalidate($token);

            $accessToken = JWTAuth::fromUser($user);
            $refreshToken = JWTAuth::claims(['refresh_token' => true])->fromUser($user);
            $cookie = Cookie::make(
                'refresh_token',
                $refreshToken,
                config('jwt.refresh_ttl'),
                null,
                null,
                true,
                true,
                false,
                'Strict'
            );

            return response()->json([
                'access_token' => $accessToken,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => $user,
            ])->withCookie($cookie);
        }
        catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'refresh_token منقضی شده است!'], 401);
        }
    }

    /**
     * Get the authenticated user's information.
     *
     * Requires a valid access token.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Logout the authenticated user.
     *
     * - Invalidates the access token.
     * - Deletes the refresh token cookie.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();
        Cookie::queue(Cookie::forget('refresh_token'));
        return response()->json([
            'status' => 'success',
            'message' => 'با موفقیت از حساب خارج شدید.'
        ]);
    }
}
