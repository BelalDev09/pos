<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\apiresponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    use apiresponse;

    /**
     * Register a new user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $validated = $request->only([
                'name',
                'email',
                'password',
            ]);

            $validated['password'] = bcrypt($validated['password']);

            $user = User::create($validated);

            // $otp =  $this->generateOtp($user);
            DB::commit();

            return $this->success([
                'user' => $user->only('id', 'name', 'email'),
                'token' => $this->respondWithToken(JWTAuth::fromUser($user)),
            ], 'User registered successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error([], $e->getMessage(), 400);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Attempt to log the user in
        if (! $token = JWTAuth::attempt($credentials)) {
            return $this->error([], 'Invalid credentials', 401);
        }

        $user = Auth::user();

        // Check if user is inactive
        if ($user->status !== 'active') { // or use $user->status != 1 for boolean
            return $this->error([], 'Your account is inactive. Please contact the administrator.', 403);
        }

        return $this->success([
            'token' => $this->respondWithToken($token),
            'user' => $user,
        ], 'User logged in successfully.', 200);
    }

    /**
     * Google Login
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'provider' => 'required|in:google,apple',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $provider = $request->provider;
            $token = $request->token;

            $socialiteUser = Socialite::driver($provider)
                ->stateless()
                ->userFromToken($token);

            if (! $socialiteUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid social token.',
                ], 422);
            }

            $user = User::where('provider', $provider)
                ->where('provider_id', $socialiteUser->getId())
                ->first();

            $isNewUser = false;

            if (! $user && $socialiteUser->getEmail()) {
                $user = User::where('email', $socialiteUser->getEmail())->first();
            }

            if (! $user) {
                $user = User::create([
                    'name' => $socialiteUser->getName() ?? ucfirst($provider) . ' User',
                    'email' => $socialiteUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialiteUser->getId(),
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'User',
                ]);

                $isNewUser = true;
            } else {
                if (! $user->provider_id) {
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialiteUser->getId(),
                    ]);
                }
            }

            $jwt = auth('api')->login($user);

            return response()->json([
                'success' => true,
                'message' => $isNewUser
                    ? 'User registered successfully.'
                    : 'User logged in successfully.',
                'data' => [
                    'user' => $user,
                    'token' => $jwt,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Forget Password Controller
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return $this->error([], 'User not found', 404);
        }

        $this->generateOtp($user);

        return $this->success([], 'Check Your Email for Password Reset Otp', 200);
    }

    /**
     * Reset Password Controller
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user->otp || ! Hash::check($request->otp, $user->otp)) {
            return response()->json([
                'message' => 'Invalid OTP!',
            ], 400);
        }

        if ($user->otp_created_at && now()->gt(Carbon::parse($user->otp_created_at)->addMinutes(15))) {
            return response()->json([
                'message' => 'OTP has expired.',
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_created_at = null;
        $user->save();

        return response()->json(['message' => 'Password reset successfully.'], 200);
    }

    // Resend Otp
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return $this->error([], 'User not found', 404);
        }
        $this->generateOtp($user);

        return $this->success([], 'Check Your Email for Password Reset Otp', 200);
    }

    /**
     * Varify User Otp
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function varifyOtpWithOutAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:4',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = User::where('email', $request->email)->first();

        // Check already verified
        if ($user->email_verified_at) {
            return $this->error([], 'User already verified', 400);
        }

        // Check if OTP matches
        if ($user->otp != $request->otp) {
            return $this->error([], 'Invalid OTP.', 400);
        }

        $user->otp = null;
        $user->email_verified_at = now();
        $user->save();

        $token = JWTAuth::fromUser($user);

        return $this->success([
            'token' => $this->respondWithToken($token),
            'user' => $user,
        ], 'OTP verified successfully', 200);
    }

    /**
     * Log out the user (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->success([
                'message' => 'Successfully logged out',
            ], 'User logged out successfully.', 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->error([], [$e->getMessage()], 500);
        }
    }

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->success([
            'user' => Auth::user()->load('images'),
        ], 'User retrieved successfully', 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            // Refresh the token
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return $this->success([
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ], 'Token refreshed successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 400);
        }
    }

    /**
     * Get Token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
