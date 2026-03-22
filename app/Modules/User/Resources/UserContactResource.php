<?php

declare(strict_types=1);

namespace App\Modules\User\Resources;

use App\Modules\Core\Resources\BaseResource;
use App\Modules\User\Models\UserContact;
use Illuminate\Http\Request;

/**
 * @mixin UserContact
 */
final class UserContactResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->whenHas('type'),
            'value' => $this->whenHas('value'),
            'verifications' => ContactVerificationResource::collection($this->whenLoaded('verifications')),
            'created_at' => $this->whenHas('created_at'),
            'updated_at' => $this->whenHas('updated_at'),
        ];
    }
}
