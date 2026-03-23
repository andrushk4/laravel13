<?php

declare(strict_types=1);

namespace App\Modules\Core\Scribe\Strategies;

use App\Modules\Core\Scribe\Attributes\FromQueryBuilder;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\Strategies\Strategy;

final class GetQueryParamsFromQueryBuilder extends Strategy
{
    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, array<string, mixed>>|null
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $settings = []): ?array
    {
        $attribute = $this->findAttribute($endpointData);

        if (! $attribute) {
            return null;
        }

        /** @var array{
         *     includes: array<string, string>,
         *     filters: array<string, array{type: string, enum?: class-string}>,
         *     sorts: array<int, string>,
         *     defaultSort: string,
         *     fields: array<string, array<int, string>>,
         * } $config
         */
        $config = $attribute->repository::queryBuilderConfig();

        return $this->buildQueryParams($config);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, array<string, mixed>>
     */
    private function buildQueryParams(array $config): array
    {
        $params = [];

        $params['per_page'] = [
            'name' => 'per_page',
            'type' => 'integer',
            'description' => 'Количество записей на странице. По умолчанию `15`.',
            'required' => false,
            'example' => 10,
        ];

        if ($config['includes'] !== []) {
            $params['include'] = [
                'name' => 'include',
                'type' => 'string',
                'description' => 'Связи для подключения (через запятую): `' . implode('`, `', array_keys($config['includes'])) . '`.',
                'required' => false,
                'example' => implode(',', array_slice(array_keys($config['includes']), 0, 2)),
            ];
        }

        foreach ($config['filters'] as $field => $opts) {
            $enumValues = [];
            $description = ($opts['type'] === 'exact' ? 'Точное совпадение' : 'Частичное совпадение') . '.';

            if (isset($opts['enum']) && enum_exists($opts['enum'])) {
                $enumValues = array_map(
                    static fn (\UnitEnum $case): string => $case instanceof \BackedEnum ? (string) $case->value : $case->name,
                    $opts['enum']::cases(),
                );
                $description .= ' Значения: `' . implode('`, `', $enumValues) . '`.';
            }

            $params["filter[$field]"] = [
                'name' => "filter[$field]",
                'type' => 'string',
                'description' => $description,
                'required' => false,
                'example' => $enumValues !== [] ? $enumValues[0] : null,
                'enumValues' => $enumValues,
            ];
        }

        $params['sort'] = [
            'name' => 'sort',
            'type' => 'string',
            'description' => 'Сортировка. Префикс `-` для DESC. Поля: `' . implode('`, `', $config['sorts']) . '`. По умолчанию: `' . $config['defaultSort'] . '`.',
            'required' => false,
            'example' => $config['defaultSort'],
        ];

        $fieldKeys = array_keys($config['fields']);

        if ($fieldKeys !== []) {
            $params["fields[$fieldKeys[0]]"] = [
                'name' => "fields[$fieldKeys[0]]",
                'type' => 'string',
                'description' => 'Выборка полей (через запятую). Доступные ресурсы: `' . implode('`, `', $fieldKeys) . '`.',
                'required' => false,
                'example' => null,
            ];
        }

        return $params;
    }

    private function findAttribute(ExtractedEndpointData $endpointData): ?FromQueryBuilder
    {
        $attributes = $endpointData->method?->getAttributes(FromQueryBuilder::class);

        if ($attributes === null || $attributes === []) {
            return null;
        }

        return $attributes[0]->newInstance();
    }
}
