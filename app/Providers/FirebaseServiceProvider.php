<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use GuzzleHttp\Client;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Firebase::class, function () {
            $factory = (new Factory)
                ->withServiceAccount(base_path('firebase.json'))
                ->withProjectId(env('FIREBASE_PROJECT_ID'))
                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'))
                ->withHttpClient(new \GuzzleHttp\Client(['verify' => false]));

            $firebase = $factory->create();
            $messaging = $firebase->getMessaging();
            return $firebase;
        });

        $this->app->singleton(Messaging::class, function () {
            return app(Firebase::class)->getMessaging();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
