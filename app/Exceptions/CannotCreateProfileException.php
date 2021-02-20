<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class CannotCreateProfileException extends Exception
{
    public const MESSAGE = "This user already has a profile";

    public function __construct(
        string $message = self::MESSAGE,
        int $code = 409,
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
