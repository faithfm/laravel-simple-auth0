# laravel-simple-auth0

![laravel-simple-auth0-logo](docs/laravel-simple-auth0-logo.png)

A simple/lightweight way to integrate [Auth0](https://auth0.com/) into your Laravel Application:

* Minimal configuration.
* Based on Laravel's default authentication guard ('session' / SessionGuard) - ie: no special authentication drivers required.
* Provides a genuine User model.  (A lot of Laravel libraries (including Nova) break if a user-provider provides something else.)
* The only dependency is Auth0's [PHP SDK](https://github.com/auth0/auth0-PHP/tree/8.0.0).

This library was developed after spending a many hours re-integrating our Laravel apps with each major update of Auth0's official [Laravel SDK](https://github.com/auth0/laravel-auth0) (`auth0/login` package).  Our applications are stateful "PHP Web Applications" (rather than stateless "PHP Backend APIs" interfacing to an SPA with JWTs), and we did not need a lot of the advanced features included in the Laravel SDK, so we decided to develop a simple package based around the [Auth0 QuickStart](https://auth0.com/docs/quickstart/webapp/php) for a simple PHP Web Application.

If you would like a simple way to integrate Auth0 with Laravel but would prefer not to use this library, you can simply clone our three controllers, register these routes manually, and customise them to your hearts content.  You'll soon see that 



### Installation:

Assuming you have a standard Laravel application (with the default 'session' driver in `config/auth.php`), you can add this package using composer and run the database migration to prepare the `users` table for Auth0 (vs password-based) logins.

```bash
composer require faithfm/laravel-simple-auth0
composer require doctrine/dbal                   ## ONLY required if you are using the SQLite DB driver
php artisan vendor:publish --tag=laravel-simple-auth0-migrations
php artisan migrate
```

> Note: in modifying the `users` table, the published migration adds the `sub` field, drops the *unique* constraint on the `email` field, and drops the `password` and `email_verified` fields.   if your `users` field contains existing user/password entries you with to retain, you should modify the default migration to retain your existing fields.

Modify `Models\User.php` to reflect these changes:

```diff
    protected $fillable = [
        'name',
        'email',
        'password',
+       'sub',
    ];

    protected $hidden = [
-       'password',
        'remember_token',
    ];

    protected $casts = [
-       'email_verified_at' => 'datetime',
-       'password' => 'hashed',
    ];
```



### Configuration:

1. Create a "Regular Web Application" in your [Auth0 Dashboard](https://manage.auth0.com/), and configure the allowed Callback + Logout URLs as required.    (See Laravel Auth0 SDK docs for [more details](https://github.com/auth0/laravel-auth0/blob/main/docs/Configuration.md#creating-applications-manually))
2. Use these details to configure your `.env` file:

```bash
AUTH0_DOMAIN=XXXX.xx.auth0.com
AUTH0_CLIENT_ID=XXXXXXXXXXX
AUTH0_CLIENT_SECRET=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

3. Add the following lines to your `web.php` file:

```php
use FaithFM\SimpleAuth0\SimpleAuth0ServiceProvider;

// Register login/logout/callback routes (for Auth0)
SimpleAuth0ServiceProvider::registerLoginLogoutCallbackRoutes();
```



### Basic Usage:

You can now use any of [Laravel's normal authentication](https://laravel.com/docs/master/authentication) methods to check if logged in, protect routes, retrieve a user, etc:

```php
$loggedIn = Auth::check();            // check if logged in
Route::get(...)->middleware('auth')   // protect a route using 'auth' middleware
$user = auth()->user();               // get logged-in current User model (using helper function)
$user = Auth::user();                 // ditto (using Facades)
// etc...
```

Don't forget, Authentication (AuthN) is about knowing **who** is using a system.  Whether or not a user has **permission** to use the system is a separate topic referred to as Authorization (AuthZ) - see [Laravel Authorization](https://laravel.com/docs/master/authorization) documentation.

For a simple table/model-based approach to user permissions / Authorization you might like to try our [Laravel Simple Permissions](https://github.com/faithfm/laravel-simple-permissions) package.  

Note: These packages are both part of our overall AuthN/AuthZ pattern that we deploy for our apps.  (Our [Faith FM Laravel Auth0 Pattern](https://github.com/faithfm/laravel-auth0-pattern) package is more opinionated than the underlying packages, and includes a number of published template files that may be less helpful for a wider audience, but you're welcome to use them if they are helpful.)



### How it works:

Three **routes** are registered:  /login, /logout, /callback

* The `/login` route redirects to the Auth0 login page, which redirects back to the `/callback` route on success.
  * Note: this route seeks to capture the 'previous' URL as the 'intended' URL for the callback to redirect to after a successful login

* The `/callback` route:
  * Validate callback request parameters and retrieves an **Auth0 user** (using [Auth0 PHP SDK](https://github.com/auth0/auth0-PHP/tree/8.0.0))
  * Load (or creates) a matching **User model** 
    * Auth0's `sub` property is used for model retrieval.
    * Auth0's `email` + `name` properties are additionally used for model creation.
  * Initialise Laravel's default authentication guard ('session' / SessionGuard) to "login" this retrieved User model.
  * Laravel's SessionGuard stores model's `id` in the session, and uses it to retrieve the User model for all future requests.

