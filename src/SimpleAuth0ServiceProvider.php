<?php

namespace FaithFM\SimpleAuth0;

use Auth0\SDK\Auth0;
use FaithFM\SimpleAuth0\Http\Controllers\CallbackController;
use FaithFM\SimpleAuth0\Http\Controllers\LoginController;
use FaithFM\SimpleAuth0\Http\Controllers\LogoutController;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SimpleAuth0ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Publish the migration file to the 'database/migrations' directory
        //   > php artisan vendor:publish --tag=laravel-simple-auth0-migrations
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/database/migrations/2024_04_23_000000_edit_users_table_auth0_changes.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_edit_users_table_auth0_changes.php'),
            ], 'laravel-simple-auth0-migrations');
        }
    }
    
    /** 
     * Call this function from your web.php to register the Auth0 login, logout and callback routes.
     */
    public static function registerLoginLogoutCallbackRoutes(): void
    {
        // Register the login, logout and callback routes
        Route::get('/login', LoginController::class)->name('login');
        Route::match(['get', 'post'], '/logout', LogoutController::class)->name('logout');
        Route::get('/callback', CallbackController::class)->name('callback');
    }

    /**
     * Return an instance of the Auth0 SDK, configured with the Auth0 environment variables.
     */
    public static function getAuth0SDK(): Auth0
    {
        return new Auth0([
            'domain' => env('AUTH0_DOMAIN'),
            'clientId' => env('AUTH0_CLIENT_ID'),
            'clientSecret' => env('AUTH0_CLIENT_SECRET'),
            'cookieSecret' => env('APP_KEY'),
        ]);
    }
}
