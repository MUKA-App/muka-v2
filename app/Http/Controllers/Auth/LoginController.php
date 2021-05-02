<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Exceptions\IncorrectPasswordException;
use App\Exceptions\UserVerificationException;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{

    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {

        $this->repository = $repository;
    }

    /**
     * Creates or updates user based on existence, logs them in and redirects
     * @param Request $request
     * @return JsonResponse
     * @throws IncorrectPasswordException
     * @throws UserVerificationException
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'remember_token' => 'required|bool'
        ]);

        $route = $this->handleEmailAndPasswordLogin(
            $request['email'],
            $request['password'],
            $request['remember_token']
        );

        return response()->json([
            'message' => 'Successful authentication.',
            'redirect' => $route
        ], 200);
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $rememberToken
     * @return string
     * @throws IncorrectPasswordException
     * @throws UserVerificationException
     */
    private function handleEmailAndPasswordLogin(string $email, string $password, bool $rememberToken): string
    {
        $user = $this->repository->getUserByEmail($email);

        if (!$user) {
            throw new ModelNotFoundException("User with this email cannot be found");
        }

        if (!$user->isVerified()) {
            throw new UserVerificationException('User is not verified');
        }

        if (!Hash::check($password, $user->password)) {
            throw new IncorrectPasswordException();
        }

        Auth::login($user, $rememberToken);
        return env('APP_URL') . '/home';
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();

        return redirect(route('home'));
    }
}
