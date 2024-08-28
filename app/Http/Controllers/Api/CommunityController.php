<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Community\StoreCommunityRequest;
use App\Http\Requests\Community\UpdateCommunityRequest;
use App\Http\Resources\Community\CommunityResource;
use App\Models\Community;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $communities = Community::paginate($perPage);
        return CommunityResource::collection($communities);
    }

    public function store(StoreCommunityRequest $request)
    {
        $validatedData            = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $community                = Community::create($validatedData);
        return new CommunityResource($community);
    }

    public function show($communityId)
    {
        $community = Community::find($communityId);
        if (!$community) return response()->json(['message' => 'Community not found.'], 404);

        return new CommunityResource($community);
    }

    public function update(UpdateCommunityRequest $request, $communityId)
    {
        $community = Community::find($communityId);
        if (!$community) return response()->json(['message' => 'Community not found.'], 404);

        if (auth()->id() !== $community->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $community->update($request->validated());
        return new CommunityResource($community);
    }

    public function destroy($communityId)
    {
        $community = Community::find($communityId);
        if (!$community) return response()->json(['message' => 'Community not found.'], 404);

        if (auth()->id() !== $community->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $community->delete();
        return response()->json(['message' => 'Community deleted successfully.'], 200);
    }

    public function requestToJoin(Request $request, $communityId)
    {
        $user = auth()->user();

        $community = Community::find($communityId);
        if (!$community) {
            return response()->json(['message' => 'Community not found.'], 404);
        }

        // Check if the authenticated user is the owner of the community
        if ($community->user_id === $user->id) {
            return response()->json(['message' => 'You cannot request to join your own community.'], 403);
        }

        // Check the current status of the user in the community
        $pivotRecord = $community->users()->wherePivot('user_id', $user->id)->first();

        if ($pivotRecord) {
            $status = $pivotRecord->pivot->status;

            if ($status === 'pending') {
                return response()->json(['message' => 'Your request is already pending.'], 400);
            } elseif ($status === 'approved') {
                return response()->json(['message' => 'You are already a member of this community.'], 400);
            } elseif ($status === 'rejected') {
                return response()->json(['message' => 'Your previous request was rejected. You cannot join this community.'], 403);
            }
        }

        // Attach the user to the community with a pending status if no record exists
        $community->users()->attach($user->id, ['status' => 'pending']);

        return response()->json(['message' => 'Your request to join the community has been sent.'], 200);
    }
}
