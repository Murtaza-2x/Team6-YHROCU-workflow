<?php
/*
-------------------------------------------------------------
File: Auth0UserManager.php
Description:
- Provides Auth0 Management API functionalities using instance methods:
    > Get all users
    > Get single user
    > Create user
    > Update user role
    > Send password reset link
    > Disable / re-enable / delete users
-------------------------------------------------------------
*/

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Auth0TokenManager.php';

use Auth0\SDK\API\Management;
use Auth0\SDK\Configuration\SdkConfiguration;

class Auth0UserManager
{
    private Management $mgmt;

    /*
    -------------------------------------------------------------
    Constructor
    Description:
    - Initializes the Auth0 Management API client.
    -------------------------------------------------------------
    */
    public function __construct()
    {
        $token = Auth0TokenManager::getToken();
        $config = new SdkConfiguration(
            [
            'domain'          => $_ENV['AUTH0_DOMAIN'],
            'clientId'        => $_ENV['AUTH0_MGMT_CLIENT_ID'],
            'clientSecret'    => $_ENV['AUTH0_MGMT_CLIENT_SECRET'],
            'cookieSecret'    => $_ENV['AUTH0_COOKIE_SECRET'],
            'managementToken' => $token,
            'usePkce'         => false,
            'useState'        => false,
            ]
        );

        $this->mgmt = new Management($config);
    }

    /*
    -------------------------------------------------------------
    Method: getUsers
    Description:
    - Fetches all users from the Auth0 Management API.
    -------------------------------------------------------------
    */
    public function getUsers(): array
    {
        $response = $this->mgmt->users()->getAll();
        return json_decode((string)$response->getBody(), true);
    }

    /*
    -------------------------------------------------------------
    Method: createUser
    Description:
    - Creates a new user in Auth0 with the specified email, password, and role.
    -------------------------------------------------------------
    */
    public function createUser(string $email, string $password, string $role): void
    {
        $response = $this->mgmt->users()->create(
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
    - Updates the role and status of a user in app_metadata.
    -------------------------------------------------------------
    */
    public function updateUserRole(string $userId, string $role, string $status = 'active'): void
    {
        $resp = $this->mgmt->users()->update(
            $userId, [
            'app_metadata' => [
                'role'   => $role,
                'status' => $status
            ]
            ]
        );

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
    public function getUser(string $userId): array
    {
        $resp = $this->mgmt->users()->get($userId);
        return json_decode((string)$resp->getBody(), true);
    }

    /*
    -------------------------------------------------------------
    Method: getUserByEmail
    Description:
    - Fetches a user by their email address from Auth0.
    -------------------------------------------------------------
    */
    public function getUserByEmail(string $email): array
    {
        $params = [
            'q'             => 'email:"' . $email . '"',
            'search_engine' => 'v3'
        ];

        $response = $this->mgmt->users()->getAll($params);
        return json_decode((string) $response->getBody(), true);
    }

    /*
    -------------------------------------------------------------
    Method: generatePasswordResetLink
    Description:
    - Generates and returns a password reset link for a user.
    -------------------------------------------------------------
    */
    public function generatePasswordResetLink(string $userId): string
    {
        $resUser = $this->mgmt->users()->get($userId);
        $userData = json_decode((string)$resUser->getBody(), true);
        $email = $userData['email'] ?? null;

        if (!$email) {
            throw new Exception('User does not have an email.');
        }

        $resTicket = $this->mgmt->tickets()->createPasswordChange(
            [
            'user_id' => $userId,
            'result_url' => 'http://localhost/YOUR_APP/password-reset-success.php'
            ]
        );

        $body = json_decode((string)$resTicket->getBody(), true);

        if ($resTicket->getStatusCode() === 201 && !empty($body['ticket'])) {
            return $body['ticket'];
        }

        throw new Exception('Failed to create reset link: ' . (string)$resTicket->getBody());
    }

    /*
    -------------------------------------------------------------
    Method: disableUser
    Description:
    - Disables a user by setting app_metadata > status to 'inactive'.
    -------------------------------------------------------------
    */
    public function disableUser(string $userId): void
    {
        $resp = $this->mgmt->users()->update(
            $userId, [
            'app_metadata' => ['status' => 'inactive']
            ]
        );

        if ($resp->getStatusCode() !== 200) {
            throw new \Exception('Failed to disable user: ' . (string)$resp->getBody());
        }
    }

    /*
    -------------------------------------------------------------
    Method: reenableUser
    Description:
    - Re-enables a user by setting app_metadata > status to 'active'.
    -------------------------------------------------------------
    */
    public function reenableUser(string $userId): void
    {
        $resp = $this->mgmt->users()->update(
            $userId, [
            'app_metadata' => ['status' => 'active']
            ]
        );

        if ($resp->getStatusCode() !== 200) {
            throw new \Exception('Failed to re-enable user: ' . (string)$resp->getBody());
        }
    }

    /*
    -------------------------------------------------------------
    Method: deleteUser
    Description:
    - Deletes a user from Auth0 by user ID.
    -------------------------------------------------------------
    */
    public function deleteUser(string $userId): void
    {
        try {
            $resp = $this->mgmt->users()->delete($userId);
            if ($resp->getStatusCode() !== 204) {
                throw new \Exception('Failed to delete user: ' . (string)$resp->getBody());
            }
        } catch (Exception $e) {
            throw new \Exception('Error deleting user: ' . $e->getMessage());
        }
    }
}
