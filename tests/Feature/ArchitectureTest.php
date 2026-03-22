<?php

arch('user module models extend correct base classes')
    ->expect('App\Modules\User\Models\User')
    ->toExtend('Illuminate\Foundation\Auth\User');

arch('user module controllers extend base controller')
    ->expect('App\Modules\User\Controllers')
    ->toExtend('App\Http\Controllers\Controller')
    ->toHaveSuffix('Controller');

arch('user module requests extend form request')
    ->expect('App\Modules\User\Requests')
    ->toExtend('Illuminate\Foundation\Http\FormRequest')
    ->toHaveSuffix('Request');

arch('user module DTOs are readonly')
    ->expect('App\Modules\User\DTOs')
    ->toBeReadonly();

arch('user module enums are string backed')
    ->expect('App\Modules\User\Enums')
    ->toBeEnums();

arch('user module repository implements interface')
    ->expect('App\Modules\User\Repositories\UserRepository')
    ->toImplement('App\Modules\User\Repositories\Contracts\UserRepositoryInterface');

arch('user module service is readonly')
    ->expect('App\Modules\User\Services\UserService')
    ->toBeReadonly();

arch('models are not used in wrong places')
    ->expect('App\Modules\User\Models')
    ->toBeUsedIn([
        'App\Modules\User\Controllers',
        'App\Modules\User\Services',
        'App\Modules\User\Repositories',
        'App\Modules\User\Resources',
        'App\Modules\User\Requests',
        'App\Models',
        'Database\Factories',
        'Database\Seeders',
    ]);
