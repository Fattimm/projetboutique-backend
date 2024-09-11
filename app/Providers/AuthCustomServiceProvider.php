<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthentificationPassport;
use App\Services\Interfaces\AuthentificationServiceInterface;

class AuthCustomServiceProvider extends ServiceProvider
{
    public function register()
    {    
        $this->app->singleton(AuthentificationServiceInterface::class, function ($app) {
            // Remplacez AuthenticationPassport par AuthenticationSanctum si vous utilisez Sanctum
            return new AuthentificationPassport();
        });
        
    }

    public function boot()
    {
        // Code de démarrage du service provider
    }
}
