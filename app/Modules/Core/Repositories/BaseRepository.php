<?php

declare(strict_types=1);

namespace App\Modules\Core\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

abstract readonly class BaseRepository
{
    /**
     * @return class-string<Model>
     */
    abstract protected function model(): string;

    /**
     * @return array{
     *     includes?: array<string, string>,
     *     filters?: array<string, array{type: string, enum?: class-string}>,
     *     sorts?: array<int, string>,
     *     defaultSort?: string,
     *     fields?: array<string, array<int, string>>,
     * }
     */
    protected static function queryBuilderConfig(): array
    {
        return [];
    }

    /**
     * @return LengthAwarePaginator<int, mixed>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $config = static::queryBuilderConfig();

        $filters = array_map(
            static fn (string $field, array $opts): AllowedFilter => $opts['type'] === 'exact'
                ? AllowedFilter::exact($field)
                : AllowedFilter::partial($field),
            array_keys($config['filters'] ?? []),
            $config['filters'] ?? [],
        );

        $includes = array_map(
            static fn (string $name): AllowedInclude => AllowedInclude::relationship($name),
            array_keys($config['includes'] ?? []),
        );

        $fields = array_merge(...array_map(
            static fn (string $resource, array $fieldList): array => array_map(
                static fn (string $field): string => "{$resource}.{$field}",
                $fieldList,
            ),
            array_keys($config['fields'] ?? []),
            $config['fields'] ?? [],
        ));

        return QueryBuilder::for($this->model())
            ->allowedFields(...$fields)
            ->allowedIncludes(...$includes)
            ->allowedFilters(...$filters)
            ->allowedSorts(...($config['sorts'] ?? []))
            ->defaultSort($config['defaultSort'] ?? '-id')
            ->paginate($perPage);
    }
}
