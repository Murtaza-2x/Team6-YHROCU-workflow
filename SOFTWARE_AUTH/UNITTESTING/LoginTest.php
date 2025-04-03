<?php

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset session and headers before each test
        $_SESSION = [];
        $_GET = [];
        if (headers_sent()) {
            $this->markTestSkipped('Headers already sent, cannot run header-dependent test.');
        }
    }

    /**
     * Test login redirect with login hint (email set in session).
     */
    public function testLoginRedirectWithLoginHint()
    {
        $_SESSION['login_email'] = 'testuser@example.com';
        $_GET['auth0_logged_out'] = 1; // Simulate returning after federated logout

        // Mock Auth0Factory::create() and login() to return a fake URL
        require_once __DIR__ . '/../INCLUDES/Auth0Factory.php';
        Auth0Factory::shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'login' => function($a, $b, $params) {
                    return 'https://example.com/authorize?' . http_build_query($params);
                }
            ]);

        // Catch header location
        $this->expectOutputRegex('/.*/'); // Avoids PHPUnit warning about no output

        // Include target
        try {
            include __DIR__ . '/../auth0_login.php';
        } catch (\Throwable $e) {
            // Prevent test from dying due to exit()
        }

        // Check that a Location header was sent and contains the login_hint
        $headers = xdebug_get_headers();
        $locationHeader = array_filter($headers, fn($h) => str_starts_with($h, 'Location:'));
        $this->assertNotEmpty($locationHeader, 'Expected Location header');

        $location = reset($locationHeader);
        $this->assertStringContainsString('login_hint=testuser%40example.com', $location);
    }

    /**
     * Test login redirect without login hint.
     */
    public function testLoginRedirectWithoutLoginHint()
    {
        $_GET['auth0_logged_out'] = 1; // Simulate returning after federated logout

        // Mock Auth0Factory::create() and login() to return a fake URL
        require_once __DIR__ . '/../INCLUDES/Auth0Factory.php';
        Auth0Factory::shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'login' => function($a, $b, $params) {
                    return 'https://example.com/authorize?' . http_build_query($params);
                }
            ]);

        // Catch header location
        $this->expectOutputRegex('/.*/'); // Avoids PHPUnit warning about no output

        // Include target
        try {
            include __DIR__ . '/../auth0_login.php';
        } catch (\Throwable $e) {
            // Prevent test from dying due to exit()
        }

        // Check that a Location header was sent and does NOT contain login_hint
        $headers = xdebug_get_headers();
        $locationHeader = array_filter($headers, fn($h) => str_starts_with($h, 'Location:'));
        $this->assertNotEmpty($locationHeader, 'Expected Location header');

        $location = reset($locationHeader);
        $this->assertStringNotContainsString('login_hint=', $location);
    }
}
