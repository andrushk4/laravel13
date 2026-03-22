<?php

declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Traits\ApiResponse;
use App\Modules\User\Models\User;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Resources\UserResourceCollection;
use App\Modules\User\Services\UserService;
use Illuminate\Http\JsonResponse;

final class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(): UserResourceCollection
    {
        $perPage = (int) request()->query('per_page', '15');

        return new UserResourceCollection(
            $this->userService->paginate($perPage),
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->toDTO());

        return (new UserResource($user))->toResponse($request);
    }

    public function show(User $user): JsonResponse
    {
        $user->load('contacts');

        return (new UserResource($user))->toResponse(request());
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->toDTO());

        return (new UserResource($user))->toResponse($request);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return $this->deletedResponse();
    }
}
