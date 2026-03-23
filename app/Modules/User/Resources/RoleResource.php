<?php

declare(strict_types=1);

namespace App\Modules\User\Resources;

use App\Modules\Core\Resources\BaseResource;
use App\Modules\User\Models\Role;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Request;

/**
 * @mixin Role
 */
final class RoleResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->whenHas('name'),
            'slug' => $this->whenHas('slug'),
            'description' => $this->whenHas('description'),
            'pivot' => $this->whenPivotLoaded('role_user', function () {
                /** @var Pivot|null $pivot */
                $pivot = $this->resource->getRelation('pivot');

                return [
                    'assigned_at' => $pivot?->getAttribute('assigned_at'),
                ];
            }),
            'created_at' => $this->whenHas('created_at'),
            'updated_at' => $this->whenHas('updated_at'),
        ];
    }
}
