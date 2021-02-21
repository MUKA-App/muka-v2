<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\User\PasswordResetRepository;
use App\Repositories\User\PasswordResetRepositoryInterface;
use App\Repositories\Profiles\ProfileRepository;
use App\Repositories\Profiles\ProfileRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            PasswordResetRepositoryInterface::class,
            PasswordResetRepository::class
        );
        $this->app->bind(
            ProfileRepositoryInterface::class,
            ProfileRepository::class
        );
    }
}
