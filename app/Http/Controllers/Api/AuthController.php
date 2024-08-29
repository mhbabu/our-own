<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\User\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

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

        $response = $this->authService->registerUser($request->name, $request->email, $request->password);

        return response()->json($response['data'], $response['status']);
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

        $response = $this->authService->verifyOtp($request->otp);

        return response()->json($response['data'], $response['status']);
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

        $response = $this->authService->resendOtp($request->email);

        return response()->json($response['data'], $response['status']);
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

        $response = $this->authService->loginUser($request->email, $request->password);

        return response()->json($response['data'], $response['status']);
    }

    /**
     * Request a password reset and send OTP to the user's email.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function passwordResetRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $response = $this->authService->requestPasswordReset($request->email);

        return response()->json($response['data'], $response['status']);
    }

    /**
     * Reset password using OTP.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp'              => 'required|string',
            'new_password'     => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $response = $this->authService->resetPassword($request->otp, $request->new_password);

        return response()->json($response['data'], $response['status']);
    }

    /**
     * Log out the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $response = $this->authService->logoutUser();
        return response()->json($response['data'], $response['status']);
    }
}
