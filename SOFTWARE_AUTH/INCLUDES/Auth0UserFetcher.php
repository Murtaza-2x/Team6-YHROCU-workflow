<?php
/*
-------------------------------------------------------------
File: Auth0UserFetcher.php
Description:
- Fetches users from the Auth0 Management API.
- Retrieves users with specific fields (user_id, nickname, email).
- Uses a token from Auth0TokenManager to authorize the API request.
-------------------------------------------------------------
*/

require_once __DIR__ . '/env_loader.php';
require_once __DIR__ . '/Auth0TokenManager.php';

class Auth0UserFetcher
{
    /*
    -------------------------------------------------------------
    Method: getUsers
    Description:
    - Retrieves users from Auth0 API.
    - Fetches up to 100 users with user_id, nickname, and email fields.
    - Uses an API token to authenticate the request.
    - Returns an array of users or an empty array if the request fails.
    -------------------------------------------------------------
    */
    public static function getUsers(): array
    {
        // Get the management API token
        $mgmtToken = Auth0TokenManager::getToken();

        // If the token is not available, return an empty array
        if (!$mgmtToken) {
            return [];
        }

        // Build the URL to fetch users
        $url = "https://" . $_ENV['AUTH0_DOMAIN'] . "/api/v2/users?fields=user_id,nickname,email&per_page=100";

        // Set up the request options with the authorization header
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Authorization: Bearer {$mgmtToken}"
            ]
        ];

        // Create the context for the request
        $context = stream_context_create($opts);

        // Send the request and capture the response
        $response = file_get_contents($url, false, $context);

        // If the request fails, return an empty array
        if ($response === false) {
            return [];
        }

        // Decode the response and return the result
        return json_decode($response, true) ?? [];
    }
}