<?php
/*
-------------------------------------------------------------
File: UNITTESTING/BaseTestCase.php
Description:
- A base test class that:
  1) uses DatabaseTestTrait to create a fresh DB connection,
  2) sets $GLOBALS['conn'] to that DB handle for production code,
  3) resets $_SESSION, $_GET, $_POST in tearDown()
-------------------------------------------------------------
*/

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/DatabaseTestTrait.php';

abstract class BaseTestCase extends TestCase
{
    use DatabaseTestTrait;

    /**
     * setUp() is called before each test method.
     * - We connect to the DB (setUpDatabase()) => $this->conn
     * - We set $GLOBALS['conn'] = $this->conn to let 'inc_connect.php' references use it.
     * - Clear or re-initialize superglobals if needed.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
        // Bridge our test DB connection to the global variable for production code
        $GLOBALS['conn'] = $this->conn;
        $_GET  = [];
        $_POST = [];
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    /**
     * tearDown() runs after each test method.
     * - We clear session, $_GET, $_POST
     * - Unset $GLOBALS['conn']
     * - We close the DB connection (tearDownDatabase()).
     */
    protected function tearDown(): void
    {
        unset($GLOBALS['conn']);

        $_GET  = [];
        $_POST = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            $_SESSION = [];
        }

        $this->tearDownDatabase();

        parent::tearDown();
    }
}