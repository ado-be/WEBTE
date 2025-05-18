<?php

namespace App\Providers;

use App\Models\UserActivity;
use App\Policies\UserActivityPolicy;
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
        UserActivity::class => UserActivityPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definujte dodatočné Gates, ak je to potrebné
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });
    }
}