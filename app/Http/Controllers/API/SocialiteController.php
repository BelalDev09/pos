<?php

namespace App\Http\Controllers\API;

use App\Traits\apiresponse;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;

class SocialiteController extends Controller
{
    use apiresponse;

    public function callback(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'provider' => 'required|in:google,apple',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->all(), 422);
        }

        try {
            $provider = $request->provider;
            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($request->token);

            if (!$socialUser) {
                return $this->error('Invalid credentials', ['Unable to authenticate with provided token'], 401);
            }

            // Find or create user
            $user = User::where('email', $socialUser->email)
                ->orWhere('provider_id', $socialUser->getId())
                ->first();

            $isNewUser = false;

            if (!$user) {
                $avatar = null;
                if ($provider === 'google') {
                    $avatar = $socialUser->getAvatar();
                }

                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Unknown',
                    'email' => $socialUser->getEmail(),
                    'avatar' => $avatar,
                    'password' => bcrypt(Str::random(16)),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'provider_refresh_token' => $socialUser->refreshToken,
                    'email_verified_at' => now(),
                ]);
                $isNewUser = true;
            } else {
                // Update provider refresh token and avatar if Google
                $updateData = [
                    'provider_refresh_token' => $socialUser->refreshToken,
                ];

                if ($provider === 'google' && $socialUser->getAvatar()) {
                    $updateData['avatar'] = $socialUser->getAvatar();
                }

                $user->update($updateData);
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            // Prepare success response
            $success = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar, // Include avatar in response if desired
            ];

            $message = $isNewUser ? 'User registered and logged in successfully' : 'User logged in successfully';

            return $this->success([
                'token' => $token,
                'data' => $success,
            ], $message);
        } catch (Exception $e) {
            return $this->error('Authentication failed', [$e->getMessage()], 401);
        }
    }
}
