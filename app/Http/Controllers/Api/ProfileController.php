<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $service)
    {
    }

    public function show(Request $request)
    {
        $user = $this->service->getProfile($request->user()->id);
        return response()->json(format_success('Profile retrieved successfully', (new UserProfileResource($user))->toArray($request)));
    }
}