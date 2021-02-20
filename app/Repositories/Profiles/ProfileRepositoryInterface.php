<?php

namespace App\Repositories\Profiles;

use App\Models\Profile;

interface ProfileRepositoryInterface
{
    public function save(Profile $profile): void;

    public function getProfileById(string $id): ?Profile;

    public function getProfileBySlug(string $slug): ?Profile;

    public function getProfileByUserId(string $userId): ?Profile;
}
