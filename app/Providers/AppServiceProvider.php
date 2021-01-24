<?php

namespace App\Providers;

use App\Services\TestService;
use App\Services\TestServiceInterface;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Configuration;
use Kreait\Firebase\Firebase;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        if (config('app.use_https')) {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {

        $this->app->bind(TestServiceInterface::class, TestService::class);

        if (config('services.firebase.enabled')) {
            $this->app->singleton(Firebase::class, function ($app) {
                $config = new Configuration();
                $config->setAuthConfigFile(config('services.firebase.auth_file'));
                return new Firebase(config('services.firebase.db_url'), $config);
            });
            $this->app->alias(Firebase::class, 'firebase');
        }
    }
}
