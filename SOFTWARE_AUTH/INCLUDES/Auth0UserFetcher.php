<?php
require_once __DIR__ . '/env_loader.php';
require_once __DIR__ . '/Auth0TokenManager.php';

class Auth0UserFetcher
{
    public static function getUsers(): array
    {
        $mgmtToken = Auth0TokenManager::getToken();
        if (!$mgmtToken) {
            return [];
        }

        $url = "https://" . $_ENV['AUTH0_DOMAIN'] . "/api/v2/users?fields=user_id,nickname,email&per_page=100";

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Authorization: Bearer {$mgmtToken}"
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            return [];
        }

        return json_decode($response, true) ?? [];
    }
}
?>
