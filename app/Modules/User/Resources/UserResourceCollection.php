<?php

declare(strict_types=1);

namespace App\Modules\User\Resources;

use App\Modules\Core\Resources\BaseResourceCollection;

final class UserResourceCollection extends BaseResourceCollection
{
    /**
     * @var string
     */
    public $collects = UserResource::class;
}
