<?php

namespace App\Http\Controllers\Profiles;

use App\Exceptions\CannotCreateProfileException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Library\Genders;
use App\Library\Instruments;
use App\Models\Profile;
use App\Repositories\Profiles\ProfileRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfilesController extends Controller
{
    protected ProfileRepositoryInterface $repository;
    protected Request $request;

    protected array $rules;

    public function __construct(ProfileRepositoryInterface $repository, Request $request)
    {
        $this->repository = $repository;
        $this->request = $request;
        $this->rules = $this->getValidationRules();
    }

    private function getValidationRules(): array
    {
        return [
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'bio' => 'string|max:4000',
            'gender' => [
                'string',
                Rule::in(Genders::GENDERS)
            ],
            'country' => 'required_with:city|exists:locations,country_code',
            'city' => [
                'required_with:country',
                Rule::exists('locations', 'city')
                    ->where(function ($query) {
                        $query->where('country_code', '=', $this->request['country']);
                    }),
            ],
            'instruments' => 'array|max:5',
            'instruments.*' => [
                'string',
                Rule::in(Instruments::INSTRUMENTS)
            ],
            'institution' => 'string|max:255',
            'birth_date' => 'date|before:today',
        ];
    }

    private function requireRules(array $rules): array
    {
        foreach ($rules as $rule => $value) {
            if (is_array($value)) {
                $value[] = 'required';
            } else {
                $value = 'required|' . $value;
            }
            $rules[$rule] = $value;
        }

        return $rules;
    }

    /**
     * POST endpoint to create a profile
     * @throws CannotCreateProfileException
     * @throws ValidationException
     */
    public function create(): ProfileResource
    {
        $this->validate($this->request, $this->requireRules($this->rules));
        $user = Auth::user();

        $profile = $this->repository->getProfileByUserId($user->getId());

        if ($profile) {
            throw new CannotCreateProfileException();
        }

        $profile = Profile::create(
            $this->request['first_name'],
            $this->request['last_name'],
            $this->request['gender'],
            $this->request['institution'],
            $this->request['bio'],
            $user->getId(),
            $this->request['instruments'],
            $this->request['birth_date']
        );

        $this->repository->save($profile);

        $profile->setLocation($this->request['country'], $this->request['city']);

        return new ProfileResource($profile);
    }

    /**
     * PATCH endpoint to edit a profile
     * @return ProfileResource
     * @throws ValidationException
     */
    public function edit(): ProfileResource
    {
        $this->validate($this->request, $this->rules);
        $user = Auth::user();

        $profile = $this->repository->getProfileByUserId($user->getId());

        if (!$profile) {
            throw new ModelNotFoundException("This user does not have a profile");
        }

        $profile = $profile->edit(
            $this->request['first_name'],
            $this->request['last_name'],
            $this->request['gender'],
            $this->request['institution'],
            $this->request['bio'],
            $this->request['instruments'],
            $this->request['birth_date']
        );

        $this->repository->save($profile);

        if (isset($this->request['country']) && isset($this->request['city'])) {
            $profile->location()->dissociate();
            $profile->setLocation($this->request['country'], $this->request['city']);
        }

        return new ProfileResource($profile);
    }

    public function mine(): ProfileResource
    {
        $user = Auth::user();

        $profile = $this->repository->getProfileByUserId($user->getId());

        if (!$profile) {
            throw new ModelNotFoundException("This user does not have a profile");
        }

        return new ProfileResource($profile);
    }

    public function show(string $slug)
    {
        $profile = $this->repository->getProfileBySlug($slug);

        if (!$profile) {
            throw new ModelNotFoundException("This profile does not exist", 404);
        }

        return new ProfileResource($profile);
    }
}
