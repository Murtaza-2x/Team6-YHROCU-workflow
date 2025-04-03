<?php
/*
-------------------------------------------------------------
File: Auth0TokenManager.php
Description:
- Manages the process of obtaining an Auth0 Management API token.
- Requests a token using the client credentials grant type.
- The token is used for making authorized API requests to Auth0.
-------------------------------------------------------------
*/

class Auth0TokenManager {
    /*
    -------------------------------------------------------------
    Method: getToken
    Description:
    - Requests a Management API token from Auth0 using client credentials.
    - Uses `client_id`, `client_secret`, and `audience` to authenticate the request.
    - Returns the access token for further API calls.
    -------------------------------------------------------------
    */
    public static function getToken(): string {
        // Get necessary environment variables for Auth0 configuration
        $domain       = $_ENV['AUTH0_DOMAIN'];
        $clientId     = $_ENV['AUTH0_MGMT_CLIENT_ID'];
        $clientSecret = $_ENV['AUTH0_MGMT_CLIENT_SECRET'];

        // URL for the token request
        $url = "https://$domain/oauth/token";

        // Data for the token request
        $data = [
            'grant_type'    => 'client_credentials',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'audience'      => "https://$domain/api/v2/",
            'scope'         => 'read:users update:users create:user_tickets delete:users'
        ];

        // Set up the request context
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => json_encode($data)
            ]
        ]);

        // Send the request to Auth0's token endpoint
        $response = file_get_contents($url, false, $context);
        $json = json_decode($response, true);

        // If no access token is returned, terminate with an error
        if (!isset($json['access_token'])) {
            die("Failed to obtain Management API token: " . json_encode($json));
        }

        // Return the access token
        return $json['access_token'];
    }
}