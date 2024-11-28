<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class AlreadyAuthenticatedException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(['message' => __('Already Authenticated')], 409);
    }
}
