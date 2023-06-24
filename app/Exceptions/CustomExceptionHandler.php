<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class CustomExceptionHandler extends ExceptionHandler
{
  
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return response()->json(['error' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof ValidationException) {
            return response()->json(['errors' => $exception->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return parent::render($request, $exception);
    }
}