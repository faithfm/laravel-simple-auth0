<?php

namespace FaithFM\SimpleAuth0\Http\Controllers;

use FaithFM\SimpleAuth0\SimpleAuth0ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    public function __invoke(Request $request): Response 
    {
        // Initialize the Auth0 SDK
        $auth0sdk = SimpleAuth0ServiceProvider::getAuth0SDK();

        // Perform a Laravel-style logout
        $guard = auth()->guard();       // Assumes the default guard in 'config/auth.php' uses the 'session' (SessionGuard) driver
        $guard->logout();

        // Clear Auth0 session  (although it should have already been cleared in the 'callback' route)
        $auth0sdk->clear();

        // Redirect to the home page
        return redirect()->to('/');
    }

}
