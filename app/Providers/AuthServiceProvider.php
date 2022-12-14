<?php

namespace App\Providers;

use App\Util\UserIs;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider {

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
    public function boot() {
        $this->registerPolicies();

        Gate::define('navigate', function ($user) {
            return UserIs::approved($user);
        });

        Gate::define('switchOffOTP', function ($user) {
            return !UserIs::withPendingOTP($user);
        });

        Gate::define('accessUsers', function ($user) {
            return UserIs::admin($user);
        });

        Gate::define('customizeLessons', function ($user) {
            return UserIs::admin($user);
        });

        Gate::define('customizeTests', function ($user) {
            return UserIs::adminOrProfessor($user);
        });

        Gate::define('takeTests', function ($user) {
            return UserIs::student($user);
        });

        Gate::define('accessSegments', function ($user) {
            return UserIs::adminOrProfessor($user);
        });

        Gate::define('viewStatistics', function ($user) {
            return UserIs::adminOrProfessor($user);
        });
    }
}
