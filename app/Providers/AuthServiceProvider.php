<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Article;
use App\Policies\PostPolicy;
use App\Policies\UserPolicy;
use OpenApi\Attributes\Post;
use App\Policies\ClientPolicy;
use Laravel\Passport\Passport;
use Workbench\App\Models\User;
use App\Policies\ArticlePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Article::class => ArticlePolicy::class,
        Client::class => ClientPolicy::class,

    ];
     

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Enregistrez les routes Passport
        // Passport::routes();
    }
}
