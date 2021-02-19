<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\DuplicateUserRegistrationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    /**
     * Validate fields and register new user if successful
     * @param Request $request
     * @param RegisterService $service
     * @return UserResource
     * @throws DuplicateUserRegistrationException
     * @throws ValidationException
     */
    public function register(Request $request, RegisterService $service): UserResource
    {
        $this->validate($request, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
        $user = $service->register($request['email'], $request['password']);

        return new UserResource($user);
    }
}
