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
    /*
    -------------------------------------------------------------
    Method: management
    Description:
    - Returns an instance of the Management API client with the proper configuration and authentication token.
    -------------------------------------------------------------
    */
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

    /*
    -------------------------------------------------------------
    Method: getUsers
    Description:
    - Fetches all users from the Auth0 Management API.
    -------------------------------------------------------------
    */
    public static function getUsers()
    {
        $mgmt = self::management();
        $response = $mgmt->users()->getAll();
        return json_decode((string)$response->getBody(), true);
    }

    /*
    -------------------------------------------------------------
    Method: createUser
    Description:
    - Creates a new user in Auth0 with the specified email, password, and role.
    -------------------------------------------------------------
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
                    'role'     => $role,
                    'approved' => true,
                    'status'   => 'active',
                ],
            ],
            null
        );

        if ($response->getStatusCode() !== 201) {
            $body = (string)$response->getBody();
            throw new \Exception('Failed to create user: ' . $body);
        }
    }

    /*
    -------------------------------------------------------------
    Method: updateUserRole
    Description:
    - Updates the role of a user in their app_metadata.
    -------------------------------------------------------------
    */
    public static function updateUserRole(string $userId, string $role, string $status = 'active'): void
    {
        $mgmt = self::management();
        
        // Update both role and status
        $resp = $mgmt->users()->update($userId, [
            'app_metadata' => [
                'role'   => $role,
                'status' => $status  // Set the status to "active" or "inactive"
            ]
        ]);
    
        if ($resp->getStatusCode() !== 200) {
            throw new \Exception('Failed to update user role and status: ' . (string)$resp->getBody());
        }
    }
    

    /*
    -------------------------------------------------------------
    Method: getUser
    Description:
    - Fetches a single user by their user ID.
    -------------------------------------------------------------
    */
    public static function getUser($userId)
    {
        $mgmt = self::management();
        $resp = $mgmt->users()->get($userId);
        return json_decode((string)$resp->getBody(), true);
    }

    /*
    -------------------------------------------------------------
    Method: getUserByEmail
    Description:
    - Fetches a user by their email address from Auth0.
    -------------------------------------------------------------
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

    /*
    -------------------------------------------------------------
    Method: generatePasswordResetLink
    Description:
    - Generates and sends a password reset link for a user.
    -------------------------------------------------------------
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

    /*
    -------------------------------------------------------------
    Method: disableUser
    Description:
    - Disables a user by updating their app_metadata (or status) to 'inactive'.
    -------------------------------------------------------------
    */
    public static function disableUser(string $userId): void
    {
        $mgmt = self::management();
        $resp = $mgmt->users()->update($userId, [
            'app_metadata' => ['status' => 'inactive']
        ]);
    
        if ($resp->getStatusCode() !== 200) {
            throw new \Exception('Failed to disable user: ' . (string)$resp->getBody());
        }
    }

    /*
    -------------------------------------------------------------
    Method: reenableUser
    Description:
    - Re-enables a user by updating their app_metadata (or status) to 'inactive'.
    -------------------------------------------------------------
    */

    public static function reenableUser(string $userId): void
    {
        $mgmt = self::management();
        $resp = $mgmt->users()->update($userId, [
            'app_metadata' => ['status' => 'active']
        ]);

        if ($resp->getStatusCode() !== 200) {
            throw new \Exception('Failed to re-enable user: ' . (string)$resp->getBody());
        }
    }

    /*
    -------------------------------------------------------------
    Method: deleteUser
    Description:
    - Deletes a user from Auth0.
    -------------------------------------------------------------
    */
    public static function deleteUser(string $userId)
    {
        $mgmt = self::management();
        try {
            $resp = $mgmt->users()->delete($userId);

            if ($resp->getStatusCode() !== 204) {
                throw new \Exception('Failed to delete user: ' . (string)$resp->getBody());
            }
        } catch (Exception $e) {
            throw new \Exception('Error deleting user: ' . $e->getMessage());
        }
    }
}