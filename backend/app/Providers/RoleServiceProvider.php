<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * Class RoleServiceProvider
 *
 * This service provider is responsible for registering and defining custom
 * authorization gates. In this case, it defines a gate for checking if a
 * user has an "admin" role.
 *
 * @package App\Providers
 */
class RoleServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('admin_role', function ($user) {
            return $user->role->title === 'admin';
        });
    }
}
