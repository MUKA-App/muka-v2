<?php

namespace App\Http\Resources;

use App\Models\Profile;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /** @var Profile $profile */
        $profile = $this;
        return [
            'id' => $profile->getId(),
            'slug' => $profile->getSlug(),
            'first_name' => $profile->getFirstName(),
            'last_name' => $profile->getLastName(),
            'gender' => $profile->getGender(),
            'bio' => $profile->getBio(),
            'profile_image_url' => $profile->getProfileImageUrl(),
            'location' => $profile->getLocation(),
            'instruments' => $profile->getInstruments(),
            'institution' => $profile->getInstitution(),
            'birth_date' => $profile->getBirthDate(),
            'is_owner' => $profile->is_owner
        ];
    }
}
