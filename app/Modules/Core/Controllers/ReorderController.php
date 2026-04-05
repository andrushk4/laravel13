<?php

declare(strict_types=1);

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Requests\ReorderRequest;
use App\Modules\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Spatie\EloquentSortable\Sortable;

final class ReorderController extends Controller
{
    use ApiResponse;

    public function __invoke(ReorderRequest $request): JsonResponse
    {
        /** @var class-string<Sortable> $modelClass */
        $modelClass = $request->route()->defaults['model'];

        foreach ($request->validated('order') as $position => $id) {
            $modelClass::query()
                ->where('id', $id)
                ->update(['order' => $position + 1]);
        }

        return $this->successResponse();
    }
}
