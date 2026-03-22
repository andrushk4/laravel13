<?php

declare(strict_types=1);

namespace App\Modules\User\Resources;

use App\Modules\Core\Resources\BaseResource;
use App\Modules\User\Models\User;
use Illuminate\Http\Request;

/**
 * @mixin User
 */
final class UserResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->whenHas('name'),
            'surname' => $this->whenHas('surname'),
            'patronymic' => $this->whenHas('patronymic'),
            'email' => $this->whenHas('email'),
            'status' => $this->whenHas('status'),
            'email_verified_at' => $this->whenHas('email_verified_at'),
            'contacts' => UserContactResource::collection($this->whenLoaded('contacts')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'created_at' => $this->whenHas('created_at'),
            'updated_at' => $this->whenHas('updated_at'),
        ];
    }
}
