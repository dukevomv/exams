<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Kreait\Firebase\Firebase;
use Kreait\Firebase\Configuration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Kreait\Firebase\Firebase',function($app){
            $config = new Configuration();
            $config->setAuthConfigFile(resource_path(env('FIREBASE_AUTH_FILE')));
            return new Firebase(env('FIREBASE_DB_URL'), $config);            
        });
    }
}
