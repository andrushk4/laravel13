<?php

declare(strict_types=1);

namespace App\Modules\Core\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseResourceCollection extends ResourceCollection
{
    public function toResponse($request): JsonResponse
    {
        $data = $this->resolve($request);

        $response = [
            'success' => true,
            'data' => $data['data'] ?? $data,
        ];

        $paginator = $this->resource;

        if ($paginator instanceof LengthAwarePaginator) {
            $response['pagination'] = [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ];
        }

        return new JsonResponse($response);
    }
}
