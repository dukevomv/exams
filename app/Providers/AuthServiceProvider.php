<?php

namespace App\Providers;

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

        Gate::define('accessUsers', function($user) {
            return $user->role('admin');
        });

        Gate::define('customizeLessons', function($user) {
            return $user->role('admin');
        });

        Gate::define('customizeTests', function($user) {
            return $user->role(['admin','professor']);
        });

        Gate::define('takeTests', function($user) {
            return $user->role('student');
        });

        Gate::define('accessSegments', function($user) {
            return $user->role(['admin','professor']);
        });
    }
}
