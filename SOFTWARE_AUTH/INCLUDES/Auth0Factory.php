<?php

/*
-------------------------------------------------------------
File: Auth0Factory.php
Description:
- Factory class for creating an Auth0 instance.
- Uses Auth0 SDK to handle authentication and related tasks.
- Retrieves configuration values from environment variables.
-------------------------------------------------------------
*/

require_once __DIR__ . '/../vendor/autoload.php';

use Auth0\SDK\Auth0;

class Auth0Factory
{
    /*
    -------------------------------------------------------------
    Method: create
    Description:
    - Creates a new Auth0 instance using configuration from environment variables.
    - Configures the Auth0 instance with domain, client ID, client secret, redirect URI, and cookie secret.
    - Returns the created Auth0 instance.
    -------------------------------------------------------------
    */
    public static function create(): Auth0
    {
        // Return a new Auth0 instance with the necessary configuration
        return new Auth0([
            'domain' => $_ENV['AUTH0_DOMAIN'],
            'clientId' => $_ENV['AUTH0_CLIENT_ID'],
            'clientSecret' => $_ENV['AUTH0_CLIENT_SECRET'],
            'redirectUri' => $_ENV['AUTH0_REDIRECT_URI'],
            'cookieSecret' => $_ENV['AUTH0_COOKIE_SECRET'],
        ]);
    }
}
