<?php

namespace FaithFM\SimpleAuth0\Http\Controllers;

use App\Models\User;
use FaithFM\SimpleAuth0\SimpleAuth0ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class CallbackController extends Controller
{
    public function __invoke(Request $request): Response 
    {
        // Initialize the Auth0 SDK
        $auth0sdk = SimpleAuth0ServiceProvider::getAuth0SDK();

        // Validate the 'code' and 'state' GET parameters (passed to the callback route), and exchange them for an Auth0 session
        $success = $auth0sdk->exchange(route('callback'));

        // Throw an exception if the exchange/validation was not successful
        if (! $success) {
            throw new \Exception('Auth0 login failed');
            // return redirect()->intended(route('login'));     // Note: Auth0 docs recommend redirecting to the login page, but we are currently throwing an exception
        }

        // Retrieve the Auth0 user profile (from the Auth0 session)
        $auth0user = $auth0sdk->getUser();

        // Find or create the matching Eloquent User model  (using 'sub')
        $userModel = User::firstOrCreate(['sub' => $auth0user['sub']], [
            // Auth0 'email' and 'name' properties are used to initialise a new User model
            'email' => $auth0user['email'] ?? '',
            'name' => $auth0user['name'] ?? '',
        ]);

        // 'login' the retrieved Eloquent user.  (SessionGuard stores User model 'id' in the session, and uses this to retrieve the User model in future requests)
        $guard = auth()->guard();       // Assumes the default guard in 'config/auth.php' uses the 'session' (SessionGuard) driver
        $guard->login($userModel);      // Assumes the default guard in 'config/auth.php' references something compatible with the 'eloquent' user-provider driver

        // Discard the Auth0 session - since we are only using Auth0 for initial login, and are relying on Laravel's SessionGuard for subsequent requests
        $auth0sdk->clear();

        // Redirect to the 'intended' URL (or the default URL if 'intended' is not set)
        return redirect()->intended();
    }

}
