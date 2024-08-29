<?php

namespace App\Services;

use App\Models\VerificationCode;
use Carbon\Carbon;

class OtpService
{
    /**
     * Generate a new OTP and save it in the database.
     */
    public function generateOtp(int $userId): string
    {
        $otp = mt_rand(100000, 999999);

        VerificationCode::create([
            'user_id' => $userId,
            'otp'     => $otp,
            'expire_at' => Carbon::now()->addMinutes(5),
        ]);

        return $otp;
    }

    /**
     * Get a valid OTP.
     */
    public function getValidOtp(string $otp): ?VerificationCode
    {
        return VerificationCode::where('otp', $otp)
            ->where('expire_at', '>', Carbon::now())
            ->first();
    }

    /**
     * Get expired OTP.
     */
    public function getExpiredOtp(string $otp): ?VerificationCode
    {
        return VerificationCode::where('otp', $otp)
            ->where('expire_at', '<=', Carbon::now())
            ->first();
    }

    /**
     * Delete OTP from the database.
     */
    public function deleteOtp(int $userId): void
    {
        VerificationCode::where('user_id', $userId)->delete();
    }
}
