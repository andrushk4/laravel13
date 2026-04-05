<?php

declare(strict_types=1);

namespace App\Modules\User\Repositories;

use App\Modules\Core\Repositories\BaseRepository;
use App\Modules\User\Enums\ContactType;
use App\Modules\User\Enums\UserStatus;
use App\Modules\User\Enums\VerificationStatus;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final readonly class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected function model(): string
    {
        return User::class;
    }

    /**
     * @return array{
     *     includes: array<string, string>,
     *     filters: array<string, array{type: string, enum?: class-string}>,
     *     sorts: array<int, string>,
     *     defaultSort: string,
     *     fields: array<string, array<int, string>>,
     * }
     */
    protected static function queryBuilderConfig(): array
    {
        return [
            'includes' => [
                'contacts' => 'Контакты пользователя (телефон, адрес, telegram)',
                'contacts.verifications' => 'Верификации контактов',
                'roles' => 'Роли пользователя (с pivot `assigned_at`)',
            ],
            'filters' => [
                'status' => ['type' => 'exact', 'enum' => UserStatus::class],
                'name' => ['type' => 'partial'],
                'surname' => ['type' => 'partial'],
                'email' => ['type' => 'partial'],
                'contacts.type' => ['type' => 'exact', 'enum' => ContactType::class],
                'contacts.value' => ['type' => 'partial'],
                'contacts.verifications.status' => ['type' => 'exact', 'enum' => VerificationStatus::class],
                'contacts.verifications.code' => ['type' => 'exact'],
                'roles.slug' => ['type' => 'exact'],
                'roles.name' => ['type' => 'partial'],
            ],
            'sorts' => ['name', 'surname', 'email', 'status', 'created_at'],
            'defaultSort' => '-created_at',
            'fields' => [
                'users' => ['id', 'name', 'surname', 'patronymic', 'email', 'status', 'email_verified_at', 'created_at', 'updated_at'],
                'contacts' => ['id', 'user_id', 'type', 'value', 'created_at', 'updated_at'],
                'roles' => ['id', 'name', 'slug', 'description', 'created_at', 'updated_at'],
            ],
        ];
    }

    public function findById(int $id): ?User
    {
        return User::query()->with('contacts')->find($id);
    }

    /**
     * @throws ModelNotFoundException<User>
     */
    public function findByIdOrFail(int $id): User
    {
        return User::query()->with('contacts')->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            /** @var array<int, array{type: string, value: string, order?: int}> $contacts */
            $contacts = Arr::pull($data, 'contacts', []);

            $data['status'] ??= UserStatus::Created;

            $user = User::query()->create($data);

            $this->syncContacts($user, $contacts);

            return $user->load('contacts');
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            /** @var array<int, array{type: string, value: string, order?: int}>|null $contacts */
            $contacts = Arr::pull($data, 'contacts');

            $user->update($data);

            if ($contacts !== null) {
                $this->syncContacts($user, $contacts);
            }

            return $user->refresh()->load('contacts');
        });
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * @param  array<int, array{type: string, value: string, order?: int}>  $contacts
     */
    private function syncContacts(User $user, array $contacts): void
    {
        $user->contacts()->delete();

        foreach ($contacts as $contact) {
            $payload = [
                'type' => $contact['type'],
                'value' => $contact['value'],
            ];

            if (isset($contact['order'])) {
                $payload['order'] = $contact['order'];
            }

            $user->contacts()->create($payload);
        }
    }
}
