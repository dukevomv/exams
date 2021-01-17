<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Util\UserIs;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('navigate', function($user) {
            return UserIs::approved($user);
        });

        Gate::define('accessUsers', function($user) {
            return UserIs::admin($user);
        });

        Gate::define('customizeLessons', function($user) {
            return UserIs::admin($user);
        });

        Gate::define('customizeTests', function($user) {
            return UserIs::adminOrProfessor($user);
        });

        Gate::define('takeTests', function($user) {
            return UserIs::student($user);
        });

        Gate::define('accessSegments', function($user) {
            return UserIs::adminOrProfessor($user);
        });
    }
}
