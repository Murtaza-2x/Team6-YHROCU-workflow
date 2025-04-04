<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../INCLUDES/Auth0Factory.php';

class Auth0LoginSystemTest extends TestCase
{
    /**
     * setUp() is called before each test method.
     * Here, we ensure the session is started and cleared.
     */
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Reset the session for each test to ensure a clean state.
        $_SESSION = [];
    }

    /**
     * Test that the Auth0Factory returns a valid Auth0 instance.
     */
    public function testAuth0FactoryReturnsAuth0Instance()
    {
        try {
            // Create an Auth0 instance using the factory.
            $auth0 = Auth0Factory::create();
            // Echo success message if the instance is created.
            echo "testAuth0FactoryReturnsAuth0Instance => SUCCESS\n";
            // Assert that the instance is of type Auth0.
            $this->assertInstanceOf(\Auth0\SDK\Auth0::class, $auth0);
        } catch (Throwable $e) {
            // If any exception occurs, echo the error message and mark the test as failed.
            echo "testAuth0FactoryReturnsAuth0Instance => ERROR: " . $e->getMessage() . "\n";
            $this->fail($e->getMessage());
        }
    }

    /**
     * Test that calling the login() method on the Auth0 instance does not crash.
     */
    public function testLoginDoesNotCrash()
    {
        try {
            // Create an Auth0 instance.
            $auth0 = Auth0Factory::create();
            // Call the login method.
            $auth0->login();
            // Echo a success message.
            echo "testLoginDoesNotCrash => SUCCESS\n";
            // Dummy assertion, as we're just checking for crashes.
            $this->assertTrue(true);
        } catch (Throwable $e) {
            // On error, echo the error message and mark the test as failed.
            echo "testLoginDoesNotCrash => ERROR: " . $e->getMessage() . "\n";
            $this->fail($e->getMessage());
        }
    }

    /**
     * Test that the logout() method generates a valid logout URL.
     */
    public function testLogoutUrlGenerated()
    {
        try {
            // Create an Auth0 instance.
            $auth0 = Auth0Factory::create();
            // Call the logout method and store the returned URL.
            $url = $auth0->logout();
            // Echo the generated URL.
            echo "testLogoutUrlGenerated => URL: $url\n";
            // Assert that the URL is a string.
            $this->assertIsString($url);
            // Assert that the URL contains the word 'logout'.
            $this->assertStringContainsString('logout', $url);
        } catch (Throwable $e) {
            // On error, echo the error message and mark the test as failed.
            echo "testLogoutUrlGenerated => ERROR: " . $e->getMessage() . "\n";
            $this->fail($e->getMessage());
        }
    }

    /**
     * Test that the auth0_logout.php file clears the user session.
     */
    public function testLogoutClearsSession()
    {
        try {
            // Pre-set the session with a fake user.
            $_SESSION['user'] = ['sub' => 'auth0|testuser'];
            // Include the logout script (which should clear the session).
            include __DIR__ . '/../auth0_logout.php';
            // Echo a success message if the session is cleared.
            echo "testLogoutClearsSession => Session cleared\n";
            // Assert that the 'user' key no longer exists in the session.
            $this->assertArrayNotHasKey('user', $_SESSION);
        } catch (Throwable $e) {
            // On error, echo the error message and mark the test as failed.
            echo "testLogoutClearsSession => ERROR: " . $e->getMessage() . "\n";
            $this->fail($e->getMessage());
        }
    }
}