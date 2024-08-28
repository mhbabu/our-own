<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Models\VerificationCode;
use App\Traits\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use AuthHelper;

    /**
     * Register a user and send OTP.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => strtolower($request->email),
            'password' => Hash::make($request->password),
        ]);

        // Generate OTP and send email
        $otp = $this->generateOtp($user->id);
        // $this->sendOtpEmail($user, $otp);

        return response()->json(['otp' => $otp], 200);
        // return response()->json(['message' => 'OTP sent to email'], 200);
    }

    /**
     * Verify OTP and activate user account.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find the verification code based on the OTP
        $verificationCode = VerificationCode::where('otp', $request->otp)
            ->where('expire_at', '>', now())
            ->first();

        if (!$verificationCode) {
            $expiredCode = VerificationCode::where('otp', $request->otp)
                ->where('expire_at', '<=', now())
                ->first();

            if ($expiredCode) {
                return response()->json(['message' => 'OTP expired'], 401);
            }

            return response()->json(['message' => 'Invalid OTP'], 401);
        }

        $user = User::find($verificationCode->user_id);

        if ($user) {
            $user->is_verified       = true;
            $user->email_verified_at = now();
            $user->save();

            // Invalidate the OTP
            $verificationCode->update(['expire_at' => now()]);

            $token = $user->createToken('authToken')->accessToken;

            return response()->json([
                'data'  => new UserResource($user),
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'User not found'], 404);
    }


    /**
     * Resend OTP to the user's email.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Generate new OTP and send email
        $otp = $this->generateOtp($user->id);
        // $this->sendOtpEmail($user, $otp);

        return response()->json(['otp' => $otp], 200);
        return response()->json(['message' => 'New OTP sent to email'], 200);
    }

    /**
     * Log in the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 400);
        }

        $user = Auth::user();

        if (!$user->is_verified) {
            return response()->json(['message' => 'Email not verified'], 403);
        }

        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'data'  => new UserResource($user),
            'token' => $token
        ]);
    }

    /**
     * Log out the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
