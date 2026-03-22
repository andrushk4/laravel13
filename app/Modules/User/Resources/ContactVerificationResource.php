<?php

declare(strict_types=1);

namespace App\Modules\User\Resources;

use App\Modules\Core\Resources\BaseResource;
use App\Modules\User\Models\ContactVerification;
use Illuminate\Http\Request;

/**
 * @mixin ContactVerification
 */
final class ContactVerificationResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->whenHas('code'),
            'status' => $this->whenHas('status'),
            'verified_at' => $this->whenHas('verified_at'),
            'expires_at' => $this->whenHas('expires_at'),
            'created_at' => $this->whenHas('created_at'),
            'updated_at' => $this->whenHas('updated_at'),
        ];
    }
}
