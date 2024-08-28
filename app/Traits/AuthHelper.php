<?php

namespace App\Traits;

use App\Models\VerificationCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Jobs\AccountVerificationJob;

trait AuthHelper
{
    /**
     * Generate OTP and store it in the database.
     *
     * @param int $userId
     * @return string
     */
    private function generateOtp(int $userId): string
    {
        $otp      = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expireAt = Carbon::now()->addMinutes(5);

        DB::transaction(function () use ($otp, $expireAt, $userId) {
            VerificationCode::updateOrCreate(
                ['user_id' => $userId],
                ['otp' => $otp, 'expire_at' => $expireAt]
            );
        });

        return $otp;
    }

    /**
     * Send OTP to the user's email.
     *
     * @param object $user
     * @param string $otp
     * @return void
     */
    private function sendOtpEmail(object $user, string $otp): void
    {
        AccountVerificationJob::dispatch($user, $otp);
    }
}
