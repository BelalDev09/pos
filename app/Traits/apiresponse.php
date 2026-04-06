<?php
namespace App\Traits;

use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

trait apiresponse
{
    public function success($data, $message = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $code);
    }

    public function error($data, $message = null, $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $code);
    }

    public function generateOtp(User $user)
    {
        $otp = rand(1000, 9999);

        $user->otp = $otp;
        $user->otp_created_at = now();
        $user->otp_expires_at = now()->addMinutes(10);

        $user->save();

        $user->notify(new OtpNotification($otp));

        return;
    }

    protected function generateSetToken($user, $action)
    {
        $customClaims = [
            'sub' => $user->id,
            'email' => $user->email,
            'action' => $action,
            'exp' => now()->addDays(365)->timestamp, // Token valid for 1 year
        ];

        return JWTAuth::customClaims($customClaims)->fromUser($user);
    }
}
