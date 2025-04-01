<?php
class Auth0TokenManager {
    public static function getToken(): string {
        $domain       = $_ENV['AUTH0_DOMAIN'];
        $clientId     = $_ENV['AUTH0_MGMT_CLIENT_ID'];
        $clientSecret = $_ENV['AUTH0_MGMT_CLIENT_SECRET'];

        $url = "https://$domain/oauth/token";
        $data = [
            'grant_type'    => 'client_credentials',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'audience'      => "https://$domain/api/v2/",
            'scope'         => 'read:users update:users create:user_tickets'
        ];

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => json_encode($data)
            ]
        ]);
        $response = file_get_contents($url, false, $context);
        $json = json_decode($response, true);

        if (!isset($json['access_token'])) {
            die("Failed to obtain Management API token: " . json_encode($json));
        }
        return $json['access_token'];
    }
}
