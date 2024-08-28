<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;


Route::post('user/register', [AuthController::class, 'register']);
Route::post('user/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('user/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('user/login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Routes requiring authentication
Route::middleware('auth:api')->group(function () {
    Route::post('user/logout', [AuthController::class, 'logout']);
    Route::post('user/update-password', [UserProfileController::class, 'updatePassword']);
    Route::post('user/update-profile', [UserProfileController::class, 'updateProfile']);
    Route::apiResource('communities', CommunityController::class);
    Route::post('communities/{community}/join-request', [CommunityController::class, 'requestToJoin']);
});