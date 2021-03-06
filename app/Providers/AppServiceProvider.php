<?php

namespace App\Providers;

use App\Http\Controllers\Admin\AdminController;
use App\Services\AdminService;
use Illuminate\Support\ServiceProvider;
use PhpParser\Node\Expr\Cast\Object_;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        $this->app->bind(AdminController::class, function ($app) {
            return new AdminController($app->make(AdminService::class));
        });
    }
}
