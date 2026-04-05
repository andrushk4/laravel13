<?php

declare(strict_types=1);

namespace App\Modules\User\Models;

use App\Modules\User\Enums\ContactType;
use Database\Factories\UserContactFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @property int $id
 * @property int $user_id
 * @property ContactType $type
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserContact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserContact whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserContact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserContact whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserContact whereValue($value)
 *
 * @property-read Collection<int, ContactVerification> $verifications
 * @property-read int|null $verifications_count
 *
 * @mixin \Eloquent
 */
#[Fillable(['user_id', 'type', 'value', 'order'])]
class UserContact extends Model implements Sortable
{
    /** @use HasFactory<UserContactFactory> */
    use HasFactory;

    use SortableTrait;

    /** @var array<string, mixed> */
    public array $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    /**
     * @return Builder<static>
     */
    public function buildSortQuery(): Builder
    {
        return static::query()->where('user_id', $this->user_id);
    }

    protected static function newFactory(): UserContactFactory
    {
        return UserContactFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ContactType::class,
        ];
    }

    /**
     * @return HasMany<ContactVerification, $this>
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(ContactVerification::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
