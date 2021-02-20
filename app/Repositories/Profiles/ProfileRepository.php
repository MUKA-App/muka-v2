<?php

namespace App\Repositories\Profiles;

use App\Models\Profile;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function save(Profile $profile): void
    {
        $profile->save();
    }

    public function getProfileById(string $id): ?Profile
    {
        return Profile::from('profiles')
            ->where('id', '=', $id)
            ->first();
    }

    public function getProfileBySlug(string $slug): ?Profile
    {
        return Profile::from('profiles')
            ->where('slug', '=', $slug)
            ->first();
    }

    public function getProfileByUserId(string $userId): ?Profile
    {
        return Profile::from('profiles')
            ->where('user_id', '=', $userId)
            ->first();
    }
}
