<?php

namespace App\Providers;

use App\Models\Client;
use App\Policies\PostPolicy;
use App\Policies\UserPolicy;
use OpenApi\Attributes\Post;
use App\Policies\ClientPolicy;
use Laravel\Passport\Passport;
use Workbench\App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        User::class => UserPolicy::class,
        Client::class => ClientPolicy::class,

    ];
     

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('create', [ClientPolicy::class, 'create']);

        // Enregistrez les routes Passport
        // Passport::routes();
    }
}
