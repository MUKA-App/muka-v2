<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Exceptions\InvalidResetTokenException;
use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{

    /**
     * Sends a password reset email
     * @param Request $request
     * @param PasswordResetService $resetService
     * @return JsonResponse
     * @throws ValidationException
     */
    public function sendEmail(Request $request, PasswordResetService $resetService): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|string|email|max:191',
        ]);

        $resetService->sendResetEmail($request['email']);

        return response()->json([
            'message' => 'Reset password email sent.'
        ], 200);
    }

    /**
     * Resets a password
     * @param Request $request
     * @param PasswordResetService $resetService
     * @return JsonResponse
     * @throws InvalidResetTokenException
     * @throws ValidationException
     */
    public function resetPassword(Request $request, PasswordResetService $resetService): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required|string|uuid',
            'password' => 'required|string|min:8',
        ]);

        $resetService->resetPasswordByToken($request['token'], $request['password']);

        return response()->json([
            'message' => 'Password reset successfully.',
            'redirect' => env('APP_URL') . '/'
        ], 200);
    }
}
