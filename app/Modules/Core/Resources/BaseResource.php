<?php

declare(strict_types=1);

namespace App\Modules\Core\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseResource extends JsonResource
{
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse(
            data: [
                'success' => true,
                'data' => $this->resolve($request),
            ],
            status: $this->calculateStatus(),
        );
    }

    private function calculateStatus(): int
    {
        if ($this->resource instanceof Model && $this->resource->wasRecentlyCreated) {
            return Response::HTTP_CREATED;
        }

        return Response::HTTP_OK;
    }
}
