<?php

declare(strict_types=1);

namespace App\Modules\Core\Scribe\Strategies;

use App\Modules\Core\Scribe\Attributes\FromQueryBuilder;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\Strategies\Strategy;

final class GetMetadataFromQueryBuilder extends Strategy
{
    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, string>|null
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

        $existingDescription = $endpointData->metadata->description ?? '';
        $generatedDocs = $this->generateMarkdown($config);

        $description = $existingDescription !== ''
            ? $existingDescription . "\n\n" . $generatedDocs
            : $generatedDocs;

        return ['description' => $description];
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function generateMarkdown(array $config): string
    {
        $md = '';

        $md .= $this->generateIncludesSection($config['includes']);
        $md .= $this->generateFiltersSection($config['filters']);
        $md .= $this->generateSortsSection($config['sorts'], $config['defaultSort']);
        $md .= $this->generateFieldsSection($config['fields']);

        return $md;
    }

    /**
     * @param  array<string, string>  $includes
     */
    private function generateIncludesSection(array $includes): string
    {
        if ($includes === []) {
            return '';
        }

        $md = "## Подключение связей (include)\n\n";
        $md .= "| Значение | Описание |\n";
        $md .= "|----------|----------|\n";

        foreach ($includes as $name => $description) {
            $md .= "| `{$name}` | {$description} |\n";
        }

        $md .= "\nПример: `?include=" . implode(',', array_keys($includes)) . "`\n\n";

        return $md;
    }

    /**
     * @param  array<string, array{type: string, enum?: class-string}>  $filters
     */
    private function generateFiltersSection(array $filters): string
    {
        if ($filters === []) {
            return '';
        }

        $md = "## Фильтрация (filter)\n\n";
        $md .= "| Параметр | Тип | Описание |\n";
        $md .= "|----------|-----|----------|\n";

        foreach ($filters as $field => $opts) {
            $type = $opts['type'];
            $description = $type === 'partial' ? 'LIKE %value%' : '';

            if (isset($opts['enum']) && enum_exists($opts['enum'])) {
                $values = array_map(
                    static fn (\UnitEnum $case): string => '`' . ($case instanceof \BackedEnum ? $case->value : $case->name) . '`',
                    $opts['enum']::cases(),
                );
                $description = implode(', ', $values);
            }

            $md .= "| `filter[{$field}]` | {$type} | {$description} |\n";
        }

        $md .= "\n";

        return $md;
    }

    /**
     * @param  array<int, string>  $sorts
     */
    private function generateSortsSection(array $sorts, string $defaultSort): string
    {
        if ($sorts === []) {
            return '';
        }

        $md = "## Сортировка (sort)\n\n";
        $md .= 'Доступные поля: `' . implode('`, `', $sorts) . '`. ';
        $md .= "По умолчанию: `{$defaultSort}`. Префикс `-` — по убыванию.\n\n";
        $md .= 'Пример: `?sort=' . ($sorts[0] ?? 'name') . '` или `?sort=' . $defaultSort . ',' . ($sorts[0] ?? 'name') . "`\n\n";

        return $md;
    }

    /**
     * @param  array<string, array<int, string>>  $fields
     */
    private function generateFieldsSection(array $fields): string
    {
        if ($fields === []) {
            return '';
        }

        $md = "## Выборка полей (fields)\n\n";
        $md .= "| Ресурс | Доступные поля |\n";
        $md .= "|--------|---------------|\n";

        foreach ($fields as $resource => $fieldList) {
            $formatted = '`' . implode('`, `', $fieldList) . '`';
            $md .= "| `{$resource}` | {$formatted} |\n";
        }

        $firstResource = array_key_first($fields);
        $md .= "\nПример: `?fields[{$firstResource}]=id,name,email`\n\n";

        return $md;
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
