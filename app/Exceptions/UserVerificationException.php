<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserVerificationException extends Exception
{

    public const MESSAGE = "Error verifying user";

    public function __construct(
        string $message = self::MESSAGE,
        int $code = 400,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'code' => $this->getCode()
        ], $this->getCode());
    }
}
