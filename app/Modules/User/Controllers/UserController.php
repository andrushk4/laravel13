<?php

declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Scribe\Attributes\FromQueryBuilder;
use App\Modules\Core\Traits\ApiResponse;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Resources\UserResourceCollection;
use App\Modules\User\Services\UserService;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;

#[Group('Users', 'API для управления пользователями.')]
final class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserService $userService,
    ) {}

    #[Endpoint('Список пользователей', 'Возвращает пагинированный список пользователей.')]
    #[FromQueryBuilder(repository: UserRepository::class)]
    public function index(): UserResourceCollection
    {
        $perPage = (int) request()->query('per_page', '15');

        return new UserResourceCollection(
            $this->userService->paginate($perPage),
        );
    }

    #[Endpoint('Создать пользователя', <<<'DESC'
    Создаёт нового пользователя с возможностью указать контакты.
    Пароль хэшируется автоматически. Если статус не указан, устанавливается `created`.
    DESC)]
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return (new UserResource($user))->toResponse($request);
    }

    #[Endpoint('Получить пользователя', 'Возвращает данные пользователя с загруженными контактами.')]
    public function show(User $user): JsonResponse
    {
        $user->load('contacts');

        return (new UserResource($user))->toResponse(request());
    }

    #[Endpoint('Обновить пользователя', <<<'DESC'
    Все поля опциональны — отправляйте только те, что нужно изменить.
    Если передан массив `contacts`, существующие контакты **полностью заменяются** (sync).
    Если `contacts` не передан — контакты остаются без изменений.
    DESC)]
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        return (new UserResource($user))->toResponse($request);
    }

    #[Endpoint('Удалить пользователя', 'Удаляет пользователя и все связанные данные (контакты, верификации) каскадно.')]
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return $this->deletedResponse();
    }
}
