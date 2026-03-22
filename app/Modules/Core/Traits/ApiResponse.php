<?php

declare(strict_types=1);

namespace App\Modules\Core\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    protected function successResponse(mixed $data = null, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(
            data: ['success' => true, 'data' => $data],
            status: $status,
        );
    }

    protected function deletedResponse(): JsonResponse
    {
        return new JsonResponse(
            data: ['success' => true, 'data' => null],
            status: Response::HTTP_NO_CONTENT,
        );
    }

    protected function errorResponse(string $message, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse(
            data: ['success' => false, 'message' => $message],
            status: $status,
        );
    }
}
