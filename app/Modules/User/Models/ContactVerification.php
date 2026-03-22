<?php

declare(strict_types=1);

namespace App\Modules\User\Models;

use App\Modules\User\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_contact_id
 * @property string $code
 * @property VerificationStatus $status
 * @property Carbon|null $verified_at
 * @property Carbon $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read UserContact $contact
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification whereUserContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactVerification whereVerifiedAt($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['user_contact_id', 'code', 'status', 'verified_at', 'expires_at'])]
class ContactVerification extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => VerificationStatus::class,
            'verified_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<UserContact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(UserContact::class, 'user_contact_id');
    }
}
