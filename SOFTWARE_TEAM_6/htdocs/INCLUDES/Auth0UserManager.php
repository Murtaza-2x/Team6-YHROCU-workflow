<?php
require_once __DIR__ . '/Auth0TokenManager.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Auth0\SDK\API\Management;
use Auth0\SDK\Configuration\SdkConfiguration;

class Auth0UserManager
{
    public static function management(): Management
    {
        $token  = Auth0TokenManager::getToken();
        $domain = $_ENV['AUTH0_DOMAIN'];

        // M2M credentials from .env
        $clientId     = $_ENV['AUTH0_MGMT_CLIENT_ID'];
        $clientSecret = $_ENV['AUTH0_MGMT_CLIENT_SECRET'];
        $cookieSecret = $_ENV['AUTH0_COOKIE_SECRET'];

        // Provide ALL required fields, disable PKCE & State if not needed
        $config = new SdkConfiguration([
            'domain'          => $domain,
            'clientId'        => $clientId,
            'clientSecret'    => $clientSecret,
            'cookieSecret'    => $cookieSecret,
            'managementToken' => $token,
            'usePkce'         => false,
            'useState'        => false,
            // 'redirectUri'   => 'http://localhost/dummy', // if needed
        ]);

        return new Management($config);
    }

    public static function getUsers()
    {
        $mgmt = self::management();
        $response = $mgmt->users()->getAll();
        return json_decode((string) $response->getBody(), true);
    }

    public static function getUser($userId)
    {
        $mgmt = self::management();
        $response = $mgmt->users()->get($userId);
        return json_decode((string) $response->getBody(), true);
    }

    public static function updateUserRole($userId, $role)
    {
        $mgmt = self::management();
        $response = $mgmt->users()->update($userId, [
            'app_metadata' => ['role' => $role]
        ]);
        return json_decode((string) $response->getBody(), true);
    }
}
