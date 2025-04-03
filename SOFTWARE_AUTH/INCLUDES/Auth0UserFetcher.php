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
            error_log('Auth0 token not available.');
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

        try {
            // Send the request and capture the response
            $response = file_get_contents($url, false, $context);

            // If the response is false, throw an exception
            if ($response === false) {
                throw new Exception('Failed to retrieve users from Auth0.');
            }

            // Decode the response and return the result
            $users = json_decode($response, true);

            // Check if the response is valid
            if (!is_array($users)) {
                throw new Exception('Invalid response format from Auth0 API.');
            }

            return $users;

        } catch (Exception $e) {
            // Log the error message
            error_log('Error fetching users from Auth0: ' . $e->getMessage());
            return [];
        }
    }
}
