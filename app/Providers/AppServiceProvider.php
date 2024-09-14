<?php

namespace App\Providers;

use App\Services\UploadService;
use App\Services\UserServiceImpl;
use App\Services\DetteServiceImpl;
use App\Services\ClientServiceImpl;
use App\Services\ArticleServiceImpl;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\UserService;
use App\Services\Interfaces\DetteService;
use App\Repositories\ClientRepositoryImpl;
use App\Repositories\ArticleRepositoryImpl;
use App\Services\Interfaces\ArticleService;
use App\Repositories\Interfaces\ArticleRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ArticleRepository::class, ArticleRepositoryImpl::class);
        $this->app->bind(UserService::class, UserServiceImpl::class);
        $this->app->bind(ArticleService::class, ArticleServiceImpl::class);
        $this->app->singleton('Client_service', function ($app) {
            return new ClientServiceImpl();
        });
        $this->app->singleton('Client_repository', function ($app) {
            return new ClientRepositoryImpl();
        });
        $this->app->singleton('UploadService', function ($app) {
            return new UploadService();
        });
        $this->app->singleton('QrCodeService', function ($app) {
            return new UploadService();
        });
        $this->app->singleton(DetteService::class, DetteServiceImpl::class);
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
