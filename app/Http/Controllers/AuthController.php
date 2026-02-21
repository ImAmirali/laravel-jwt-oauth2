<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\User;
//use Illuminate\Http\Request;
//use Illuminate\Http\JsonResponse;
//use Tymon\JWTAuth\Facades\JWTAuth;
//use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\Facades\Cookie;
//
//class AuthController extends Controller
//{
//    /**
//     * Register a new user.
//     *
//     * validates incoming registration data and create a new user.
//     *
//     * @param Request  $request
//     * @response 201 {
//     *      "success": true,
//     *      "message": "ثبت نام کاربر با موفقیت انجام شد.",
//     *      "user": {
//     *          "id": 1,
//     *          "name": "amir",
//     *          "email": "something@gmail.com",
//     *          "email_verified_at": null,
//     *          "created_at": "2025-08-05T17:08:28.000000Z",
//     *          "updated_at": "2025-08-05T17:08:28.000000Z"
//     *      }
//     * }
//     * @return JsonResponse
//     */
//    public function register(Request $request): JsonResponse
//    {
//        $validator = Validator::make($request->all(), [
//            'name' => ['required', 'string', 'max:255'],
//            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
//            'password' => ['required', 'string', 'min:8', 'confirmed'],
//        ]);
//        if ($validator->fails()) {
//            return response()->json($validator->errors(), 422);
//        }
//
//        $user = User::create([
//            'name' => $request->name,
//            'email' => $request->email,
//            'password' => Hash::make($request->password),
//        ]);
//
//        return response()->json([
//            'success' => true,
//            'message' => 'ثبت نام کاربر با موفقیت انجام شد.',
//            'user' => $user,
//        ], 201);
//    }
//
//    /**
//     * Authenticate user and return access and refresh tokens.
//     *
//     * - Validates login credentials.
//     * - Issues a JWT access token and a refresh token (in HTTP-only cookie).
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function login(Request $request): JsonResponse
//    {
//        $request->validate([
//            'email' => 'required|string|email',
//            'password' => 'required|string',
//        ]);
//        $credentials = $request->only('email', 'password');
//
//        if (!$accessToken = auth()->claims(['token_type' => 'access'])->attempt($credentials)) {
//            return response()->json(['status' => 'error', 'message' => 'کاربری با این مشخصات ثبت نشده است!'], 401);
//        }
//
//        $refreshToken = JWTAuth::claims(['token_type' => 'refresh'])->fromUser(auth()->user());
//        $cookie = Cookie::make(
//            'refresh_token',
//            $refreshToken,
//            config('jwt.refresh_ttl'),
//            null,
//            null,
//            true,
//            true,
//            false,
//            'Strict'
//        );
//
//        return response()->json([
//            'access_token' => $accessToken,
//            'token_type' => 'bearer',
//            'expires_in' => auth()->factory()->getTTL()* 60,
//            'user' => auth()->user()    ,
//        ])->withCookie($cookie);
//    }
//
//    /**
//     * Refresh the access token using the refresh token from cookie.
//     *
//     * - Validates the refresh token.
//     * - Invalidates the old one.
//     * - Issues a new access token and new refresh token.
//     *
//     * @param  Request  $request
//     * @return JsonResponse
//     */
//    public function refresh(Request $request): JsonResponse
//    {
//        $token = $request->cookie('refresh_token');
//        $user = JWTAuth::toUser();
//        JWTAuth::invalidate($token);
//
//        $accessToken = JWTAuth::claims(['token_type' => 'access'])->fromUser($user);
//        $refreshToken = JWTAuth::claims(['token_type' => 'refresh'])->fromUser($user);
//        $cookie = Cookie::make(
//            'refresh_token',
//            $refreshToken,
//            config('jwt.refresh_ttl'),
//            null,
//            null,
//            true,
//            true,
//            false,
//            'Strict'
//        );
//
//        return response()->json([
//            'access_token' => $accessToken,
//            'token_type' => 'bearer',
//            'expires_in' => auth()->factory()->getTTL() * 60,
//            'user' => $user,
//        ])->withCookie($cookie);
//
//    }
//
//    /**
//     * Get the authenticated user's information.
//     *
//     * Requires a valid access token.
//     *
//     * @return JsonResponse
//     */
//    public function user(): JsonResponse
//    {
//        return response()->json(auth()->user());
//    }
//
//    /**
//     * Logout the authenticated user.
//     *
//     * - Invalidates the access token.
//     * - Deletes the refresh token cookie.
//     *
//     * @return JsonResponse
//     */
//    public function logout(): JsonResponse
//    {
//        auth()->logout();
//        Cookie::queue(Cookie::forget('refresh_token'));
//        return response()->json([
//            'status' => 'success',
//            'message' => 'با موفقیت از حساب خارج شدید.'
//        ]);
//    }
//}


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="JWT Authorization header using the Bearer scheme"
 * )
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="amir"),
 *     @OA\Property(property="email", type="string", format="email", example="something@gmail.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-05T17:08:28.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-05T17:08:28.000000Z")
 * )
 */
class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * validates incoming registration data and create a new user.
     *
     * @param Request $request
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
     *
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register a new user",
     *     description="Validates incoming registration data and creates a new user.",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="amir"),
     *             @OA\Property(property="email", type="string", format="email", example="something@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="ثبت نام کاربر با موفقیت انجام شد."),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
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
     *
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Authenticate user",
     *     description="Validates login credentials and issues a JWT access token and a refresh token (in HTTP-only cookie).",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="something@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         ),
     *         @OA\Header(
     *             header="Set-Cookie",
     *             description="Refresh token stored in HTTP-only cookie",
     *             @OA\Schema(type="string", example="refresh_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...; HttpOnly; Secure; SameSite=Strict")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="کاربری با این مشخصات ثبت نشده است!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');

        if (!$accessToken = auth()->claims(['token_type' => 'access'])->attempt($credentials)) {
            return response()->json(['status' => 'error', 'message' => 'کاربری با این مشخصات ثبت نشده است!'], 401);
        }

        $refreshToken = JWTAuth::claims(['token_type' => 'refresh'])->fromUser(auth()->user());
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
            'user' => auth()->user(),
        ])->withCookie($cookie);
    }

    /**
     * Refresh the access token using the refresh token from cookie.
     *
     * - Validates the refresh token.
     * - Invalidates the old one.
     * - Issues a new access token and new refresh token.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/auth/refresh",
     *     summary="Refresh access token",
     *     description="Validates the refresh token from the cookie, invalidates it, and issues a new access token and refresh token.",
     *     operationId="refreshToken",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent()
     *     ),
     *     @OA\Header(
     *         header="Cookie",
     *         description="Refresh token cookie",
     *         @OA\Schema(type="string", example="refresh_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         ),
     *         @OA\Header(
     *             header="Set-Cookie",
     *             description="New refresh token stored in HTTP-only cookie",
     *             @OA\Schema(type="string", example="refresh_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...; HttpOnly; Secure; SameSite=Strict")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized (invalid refresh token)",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function refresh(Request $request): JsonResponse
    {
        $token = $request->cookie('refresh_token');
        $user = JWTAuth::toUser();
        JWTAuth::invalidate($token);

        $accessToken = JWTAuth::claims(['token_type' => 'access'])->fromUser($user);
        $refreshToken = JWTAuth::claims(['token_type' => 'refresh'])->fromUser($user);
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

    /**
     * Get the authenticated user's information.
     *
     * Requires a valid access token.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/auth/user",
     *     summary="Get authenticated user",
     *     description="Returns the authenticated user's information. Requires a valid access token.",
     *     operationId="getUser",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function user(): JsonResponse
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
     *
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Logout the authenticated user",
     *     description="Invalidates the access token and deletes the refresh token cookie.",
     *     operationId="logoutUser",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="با موفقیت از حساب خارج شدید.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
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
