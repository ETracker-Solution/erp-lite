<?php

namespace App\Providers;

use App\Repository\Repos\ProductRepo;
use App\Repository\Repos\RoleRepo;
use App\Repository\Repos\UserRepo;
use App\Repository\Repos\AdminRepo;
use App\Repository\Repos\BrandRepo;
use App\Repository\Repos\BusinessRepo;
use App\Repository\Repos\CategoryRepo;
use Illuminate\Support\ServiceProvider;
use App\Repository\Repos\PermissionRepo;
use App\Repository\Interfaces\ProductInterface;
use App\Repository\Interfaces\RoleInterface;
use App\Repository\Interfaces\UserInterface;
use App\Repository\Interfaces\AdminInterface;
use App\Repository\Interfaces\BrandInterface;
use App\Repository\Interfaces\BusinessInterface;
use App\Repository\Interfaces\CategoryInterface;
use App\Repository\Interfaces\PermissionInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            AdminInterface::class,
            AdminRepo::class
        );
        $this->app->bind(
            PermissionInterface::class,
            PermissionRepo::class
        );
        $this->app->bind(
            RoleInterface::class,
            RoleRepo::class
        );
        $this->app->bind(
            CategoryInterface::class,
            CategoryRepo::class
        );
        $this->app->bind(
            BrandInterface::class,
            BrandRepo::class
        );
        $this->app->bind(
            ProductInterface::class,
            ProductRepo::class
        );
        $this->app->bind(
            BusinessInterface::class,
            BusinessRepo::class
        );
        $this->app->bind(
            UserInterface::class,
            UserRepo::class
        );
    }
}
