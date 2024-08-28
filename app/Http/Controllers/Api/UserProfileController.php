<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\User\UserProfileResource;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    /**
     * Update user profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $userProfile = UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'birth_date' => $request->birth_date,
                'gender'     => $request->gender,
                'language'   => $request->language,
                'location'   => $request->location,
            ]
        );

        // Handle image upload with Spatie Media Library
        if ($request->hasFile('image')) {
            $userProfile->clearMediaCollection('profile_images');
            $userProfile->addMediaFromRequest('image')->toMediaCollection('profile_images');
        }

        // Ensure media is processed
        $userProfile->load('media');

        return response()->json([
            'message' => 'Profile updated successfully',
            'data'    => new UserProfileResource($userProfile)
        ], 200);
    }


    /**
     * Update user password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'The current password you provided is incorrect.'], 400);
        }

        if ($request->current_password === $request->new_password) {
            return response()->json(['message' => 'The new password cannot be the same as the current password.'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Your password has been updated successfully.'], 200);
    }
}
