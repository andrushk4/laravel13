<?php

declare(strict_types=1);

namespace App\Modules\Core\Requests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\EloquentSortable\Sortable;

final class ReorderRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        /** @var class-string<Model&Sortable> $modelClass */
        $modelClass = $this->route()->defaults['model'];
        $table = (new $modelClass)->getTable();

        return [
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['required', 'integer', 'distinct', 'exists:' . $table . ',id'],
        ];
    }
}
