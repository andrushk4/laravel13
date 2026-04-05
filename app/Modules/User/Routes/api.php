<?php

declare(strict_types=1);

use App\Modules\Core\Controllers\ReorderController;
use App\Modules\User\Controllers\UserController;
use App\Modules\User\Models\UserContact;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class);

Route::patch('users/{user}/contacts/reorder', ReorderController::class)
    ->defaults('model', UserContact::class)
    ->name('users.contacts.reorder');
