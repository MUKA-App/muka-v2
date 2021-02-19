<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UserVerificationException;
use App\Http\Controllers\Controller;
use App\Services\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VerificationController extends Controller
{
    /**
     * @param Request $request
     * @param RegisterService $service
     * @return JsonResponse
     * @throws UserVerificationException
     * @throws ValidationException
     */
    public function verify(Request $request, RegisterService $service): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required|string',
        ]);

        $service->verify($request['token']);

        return response()->json([
            'message' => 'Email successfully validated',
            'redirect' => '/profiles/create'
        ], 200);
    }

    /**
     * @param Request $request
     * @param RegisterService $service
     * @return JsonResponse
     * @throws UserVerificationException
     * @throws ValidationException
     */
    public function resendEmail(Request $request, RegisterService $service): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|string|email|max:255',
        ]);

        $service->sendVerificationEmail($request['email']);

        return response()->json([
            'message' => 'Verification email sent'
        ], 200);
    }
}
