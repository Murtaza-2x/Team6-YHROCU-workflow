<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Auth0\SDK\Auth0;

class Auth0Factory
{
    public static function create(): Auth0
    {
        return new Auth0([
            'domain' => $_ENV['AUTH0_DOMAIN'],
            'clientId' => $_ENV['AUTH0_CLIENT_ID'],
            'clientSecret' => $_ENV['AUTH0_CLIENT_SECRET'],
            'redirectUri' => $_ENV['AUTH0_REDIRECT_URI'],
            'cookieSecret' => $_ENV['AUTH0_COOKIE_SECRET'],
        ]);
    }
}
