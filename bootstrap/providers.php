<?php

use App\Modules\User\Providers\UserServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\TelescopeServiceProvider;

return [
    AppServiceProvider::class,
    UserServiceProvider::class,
    TelescopeServiceProvider::class,
];
