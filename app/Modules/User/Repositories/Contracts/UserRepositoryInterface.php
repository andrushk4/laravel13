<?php

declare(strict_types=1);

namespace App\Modules\User\Repositories\Contracts;

use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\DTOs\UpdateUserDTO;
use App\Modules\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?User;

    public function findByIdOrFail(int $id): User;

    public function create(CreateUserDTO $dto): User;

    public function update(User $user, UpdateUserDTO $dto): User;

    public function delete(User $user): bool;
}
