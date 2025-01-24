<?php
namespace App\Providers;


use App\Services\UserService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Service\UserServiceContract;
use App\Contracts\Service\LeaveRequestServiceContract;
use App\Services\LeaveRequestService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceContract::class, UserService::class);
        $this->app->bind(LeaveRequestServiceContract::class, LeaveRequestService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}