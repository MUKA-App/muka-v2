<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Repositories\Profiles\ProfileRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileAvatarController extends Controller
{
    /**
     * PUT avatar endpoint creating / replacing profile avatar
     * @param Request $request
     * @param ProfileRepositoryInterface $repository
     * @return JsonResponse
     */
    public function putAvatar(Request $request, ProfileRepositoryInterface $repository): JsonResponse
    {
        $user = Auth::user();
        // Aborts with 403 if user has no GP

        $profile = $repository->getProfileByUserId($user->getId());

        if (!$profile) {
            throw new ModelNotFoundException("This user does not have a profile");
        }

        // Validate image data type
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $url = $request['avatar']->store('profile_pictures');

        // Remove the existing image
        if ($profile->getProfileImageUrl() !== null) {
            Storage::delete($profile->getProfileImageUrl());
        }

        $profile->setProfileImageUrl($url);
        $repository->save($profile);

        return $this->returnJsonResponse(204, 'Avatar created or updated');
    }

    /**
     * Returns a JSON response with status and message
     * @param int $status
     * @param string $message
     * @return JsonResponse
     */
    private function returnJsonResponse(int $status, string $message): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ], $status);
    }
}
