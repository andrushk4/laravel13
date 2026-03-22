<?php

declare(strict_types=1);

namespace App\Modules\User\Repositories;

use App\Modules\User\DTOs\ContactDTO;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\DTOs\UpdateUserDTO;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

final readonly class UserRepository implements UserRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(User::class)
            ->allowedFields(
                'id', 'name', 'surname', 'patronymic', 'email',
                'status', 'email_verified_at', 'created_at', 'updated_at',
                'contacts.id', 'contacts.user_id', 'contacts.type', 'contacts.value',
                'contacts.created_at', 'contacts.updated_at',
                'verifications.id', 'verifications.user_contact_id',
                'verifications.code', 'verifications.status',
                'verifications.verified_at', 'verifications.expires_at',
                'verifications.created_at', 'verifications.updated_at',
                'roles.id', 'roles.name', 'roles.slug', 'roles.description',
                'roles.created_at', 'roles.updated_at',
            )
            ->allowedIncludes(
                AllowedInclude::relationship('contacts'),
                AllowedInclude::relationship('contacts.verifications'),
                AllowedInclude::relationship('roles'),
            )
            ->allowedFilters(
                AllowedFilter::exact('status'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('surname'),
                AllowedFilter::partial('email'),
                AllowedFilter::exact('contacts.type'),
                AllowedFilter::partial('contacts.value'),
                AllowedFilter::exact('contacts.verifications.status'),
                AllowedFilter::exact('contacts.verifications.code'),
                AllowedFilter::exact('roles.slug'),
                AllowedFilter::partial('roles.name'),
            )
            ->allowedSorts('name', 'surname', 'email', 'status', 'created_at')
            ->defaultSort('-created_at')
            ->paginate($perPage);
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

    public function create(CreateUserDTO $dto): User
    {
        return DB::transaction(function () use ($dto): User {
            $user = User::query()->create([
                'name' => $dto->name,
                'surname' => $dto->surname,
                'patronymic' => $dto->patronymic,
                'email' => $dto->email,
                'password' => $dto->password,
                'status' => $dto->status,
            ]);

            $this->syncContacts($user, $dto->contacts);

            return $user->load('contacts');
        });
    }

    public function update(User $user, UpdateUserDTO $dto): User
    {
        return DB::transaction(function () use ($user, $dto): User {
            $data = array_filter(
                [
                    'name' => $dto->name,
                    'surname' => $dto->surname,
                    'patronymic' => $dto->patronymic,
                    'email' => $dto->email,
                    'password' => $dto->password,
                    'status' => $dto->status,
                ],
                static fn (mixed $value): bool => $value !== null,
            );

            $user->update($data);

            if ($dto->contacts !== null) {
                $this->syncContacts($user, $dto->contacts);
            }

            return $user->refresh()->load('contacts');
        });
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * @param  array<int, ContactDTO>  $contacts
     */
    private function syncContacts(User $user, array $contacts): void
    {
        $user->contacts()->delete();

        foreach ($contacts as $contact) {
            $user->contacts()->create([
                'type' => $contact->type,
                'value' => $contact->value,
            ]);
        }
    }
}
