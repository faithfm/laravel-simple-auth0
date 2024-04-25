<?php

namespace FaithFM\SimpleAuth0\Http\Controllers;

use FaithFM\SimpleAuth0\SimpleAuth0ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function __invoke(Request $request): Response 
    {
        // Initialize the Auth0 SDK
        $auth0sdk = SimpleAuth0ServiceProvider::getAuth0SDK();

        // Capture 'previous' URL as the 'intended' URL for the callback to redirect to after a successful login
        $this->captureIntendedUrl();

        // Regenerate the session ID and clear the Auth0 session
        $request->session()->regenerate(true);          // Regenerate the session ID to prevent session fixation attacks
        $auth0sdk->clear();                             // Auth0 recommends to clear the Auth0 session as well

        // Redirect to Auth0 login page
        $auth0LoginUrl = $auth0sdk->login(route('callback'));   // The Auth0 SDK will generate the correct Auth0 login URL (including our callback URL)
        return redirect()->away($auth0LoginUrl);
    }

    /**
     * Try to capture the 'previous' URL as the 'intended' URL for the callback to redirect to after a successful login.
     */
    protected function captureIntendedUrl(): void
    {
        // Parse 'current' and 'previous' URLs into their components, to determine whether the 'previous' URL should be captured
        $current = parse_url(url()->current());
        $previous = parse_url(url()->previous());

        // Ignore badly-formed URLs
        if ($current === false || $previous === false) {
            return;
        }

        // Ignore 'previous' URLs outside the app's domain
        if ($previous['host'] !== $current['host']) {
            return;
        }

        // Ignore special routes such as login/logout/callback
        if (Str::startsWith($previous['path'] ?? null, ['/login', '/logout', '/callback'])) {
            return;
        }

        // Set the 'intended' URL to the 'previous' URL (so the '/callback' route will redirect us back there after a successful login)
        redirect()->setIntendedUrl(url()->previous());
    }

}
