<?php

declare(strict_types=1);

namespace App\Modules\User\Services;

use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\DTOs\UpdateUserDTO;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->paginate($perPage);
    }

    public function findByIdOrFail(int $id): User
    {
        return $this->userRepository->findByIdOrFail($id);
    }

    public function create(CreateUserDTO $dto): User
    {
        return $this->userRepository->create($dto);
    }

    public function update(User $user, UpdateUserDTO $dto): User
    {
        return $this->userRepository->update($user, $dto);
    }

    public function delete(User $user): bool
    {
        return $this->userRepository->delete($user);
    }
}
