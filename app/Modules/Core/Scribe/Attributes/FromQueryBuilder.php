<?php

declare(strict_types=1);

namespace App\Modules\Core\Scribe\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class FromQueryBuilder
{
    /**
     * @param  class-string  $repository  Класс репозитория с методом queryBuilderConfig()
     */
    public function __construct(
        public string $repository,
    ) {}
}
