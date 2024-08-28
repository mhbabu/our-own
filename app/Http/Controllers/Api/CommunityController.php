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
        return response()->json([ 'message' => 'Community deleted successfully.'], 200);
    }
}
