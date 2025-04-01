<?php
/*
-------------------------------------------------------------
File: Auth0UserManager.php
Description:
- Provides Auth0 Management API functionalities:
    > Get all users
    > Get single user
    > Create user (newer signature)
    > Update user role
    > Send password reset link
-------------------------------------------------------------
*/

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Auth0TokenManager.php';

use Auth0\SDK\API\Management;
use Auth0\SDK\Configuration\SdkConfiguration;

class Auth0UserManager
{
    public static function management(): Management
    {
        $token = Auth0TokenManager::getToken();
        $config = new SdkConfiguration([
            'domain'          => $_ENV['AUTH0_DOMAIN'],
            'clientId'        => $_ENV['AUTH0_MGMT_CLIENT_ID'],
            'clientSecret'    => $_ENV['AUTH0_MGMT_CLIENT_SECRET'],
            'cookieSecret'    => $_ENV['AUTH0_COOKIE_SECRET'],
            'managementToken' => $token,
            'usePkce'         => false,
            'useState'        => false,
        ]);

        return new Management($config);
    }

    /**
     * Fetch all users from Auth0
     */
    public static function getUsers()
    {
        $mgmt = self::management();
        $response = $mgmt->users()->getAll();
        return json_decode((string)$response->getBody(), true);
    }

    /**
     * Create a new DB user with the new users()->create() signature:
     * public function create(string $connection, string $email, string $password, ?array $additionalOptions = null): ResponseInterface
     */
    public static function createUser(string $email, string $password, string $role): void
    {
        $mgmt = self::management();

        $response = $mgmt->users()->create(
            'Username-Password-Authentication',
            [
                'email'          => $email,
                'password'       => $password,
                'email_verified' => true,
                'app_metadata'   => [
                    'role' => $role,
                    'approved' => true,
                ],
            ],
            null
        );

        if ($response->getStatusCode() !== 201) {
            $body = (string)$response->getBody();
            throw new \Exception('Failed to create user: ' . $body);
        }
    }

    /**
     * Update the user's role in app_metadata
     */
    public static function updateUserRole(string $userId, string $role): void
    {
        $mgmt = self::management();
        $resp = $mgmt->users()->update($userId, [
            'app_metadata' => ['role' => $role]
        ]);

        if ($resp->getStatusCode() !== 200) {
            throw new \Exception('Failed to update user role: ' . (string)$resp->getBody());
        }
    }

    /**
     * Retrieve a single user
     */
    public static function getUser($userId)
    {
        $mgmt = self::management();
        $resp = $mgmt->users()->get($userId);
        return json_decode((string)$resp->getBody(), true);
    }

    /**
     * Retrieve a user by email
     */
    public static function getUserByEmail(string $email): array
    {
        $mgmt = self::management();
        $params = [
            'q'             => 'email:"' . $email . '"',
            'search_engine' => 'v3'
        ];

        $response = $mgmt->users()->getAll($params);
        $body = json_decode((string) $response->getBody(), true);
        return $body;
    }


    /**
     * Generate / Send a password reset link for the user
     */
    public static function generatePasswordResetLink(string $userId)
    {
        $mgmt = self::management();
        if (!$mgmt) {
            throw new Exception('Auth0 Management API client is not available.');
        }

        $resUser = $mgmt->users()->get($userId);
        $userData = json_decode((string)$resUser->getBody(), true);
        $email = $userData['email'] ?? null;
        if (!$email) {
            throw new Exception('User does not have an email.');
        }

        $resTicket = $mgmt->tickets()->createPasswordChange([
            'user_id' => $userId,
            'result_url' => 'http://localhost/YOUR_APP/password-reset-success.php'
        ]);

        $body = json_decode((string)$resTicket->getBody(), true);

        if ($resTicket->getStatusCode() === 201 && !empty($body['ticket'])) {
            return $body['ticket'];
        } else {
            throw new Exception('Failed to create reset link: ' . (string)$resTicket->getBody());
        }
    }
}
