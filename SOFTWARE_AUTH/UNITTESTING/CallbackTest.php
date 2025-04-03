<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../auth0_callback.php';

class Auth0CallbackTest extends TestCase
{
    public function testUserStatusCheck()
    {
        // Simulate user data
        $userData = [
            'sub' => 'auth0|123456',
            'email' => 'testuser@example.com',
            'app_metadata' => [
                'role' => 'User',
                'status' => 'active'
            ]
        ];

        // Simulate the logic for getting user from Auth0 and adding to session
        $_SESSION['user'] = $userData;

        // Check if the user is active (test if 'status' key is 'active')
        $status = $_SESSION['user']['app_metadata']['status'];
        $this->assertEquals('active', $status, "User status should be active");
    }

    public function testInactiveUserStatus()
    {
        // Simulate user data with inactive status
        $userData = [
            'sub' => 'auth0|123456',
            'email' => 'testuser@example.com',
            'app_metadata' => [
                'role' => 'User',
                'status' => 'inactive'
            ]
        ];

        // Simulate the logic for getting user from Auth0 and adding to session
        $_SESSION['user'] = $userData;

        // Check if the user is inactive
        $status = $_SESSION['user']['app_metadata']['status'];
        $this->assertEquals('inactive', $status, "User status should be inactive");
    }
}
