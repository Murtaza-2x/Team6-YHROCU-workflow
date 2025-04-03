<?php

class indexUnitTest extends PHPUnit\Framework\TestCase
{
    /**@test */
    protected $connMock;
    protected $sessionMock;

    protected function setUp(): void
    {
        // Mocking the database connection
        $this->connMock = $this->createMock(mysqli::class);
        // Mocking $_SESSION superglobal
        $this->sessionMock = [];
        $_SESSION = &$this->sessionMock;
    }

    public function testSuccessfulLogin()
    {
        $_POST["email"] = "test@example.com";
        $_POST["password"] = "correct_password";
        
        // Simulating a successful database query
        $userRow = [
            'id' => 1,
            'email' => 'test@example.com',
            'password' => password_hash('correct_password', PASSWORD_BCRYPT), // Correct password hash
            'Status' => 'active',
            'clearance' => 'admin'
        ];
        
        // This mocks the database
        $resultMock = $this->createMock(mysqli_result::class);
        $resultMock->method('num_rows')->willReturn(1);
        $resultMock->method('fetch_assoc')->willReturn($userRow);
        $this->connMock->method('query')->willReturn($resultMock);

        // Start the logic test
        include 'your-login-script.php'; // This would include your login script
        
        // Assert that session variables are set correctly
        $this->assertEquals($_SESSION["id"], 1);
        $this->assertEquals($_SESSION["email"], 'test@example.com');
        $this->assertEquals($_SESSION["clearance"], 'admin');
    }

    public function testInvalidPassword()
    {
        $_POST["email"] = "test@example.com";
        $_POST["password"] = "wrong_password";
        
        $userRow = [
            'id' => 1,
            'email' => 'test@example.com',
            'password' => password_hash('correct_password', PASSWORD_BCRYPT),
            'Status' => 'active'
        ];

        $resultMock = $this->createMock(mysqli_result::class);
        $resultMock->method('num_rows')->willReturn(1);
        $resultMock->method('fetch_assoc')->willReturn($userRow);
        $this->connMock->method('query')->willReturn($resultMock);

        // Start the login script (this will also run the logic)
        include 'your-login-script.php';

        // Assert that the error message is set correctly
        $this->assertEquals($GLOBALS['errorMsg'], 'Incorrect Email Address or Password');
    }

    public function testInactiveUser()
    {
        $_POST["email"] = "test@example.com";
        $_POST["password"] = "correct_password";

        $userRow = [
            'id' => 1,
            'email' => 'test@example.com',
            'password' => password_hash('correct_password', PASSWORD_BCRYPT),
            'Status' => 'inactive'
        ];

        $resultMock = $this->createMock(mysqli_result::class);
        $resultMock->method('num_rows')->willReturn(1);
        $resultMock->method('fetch_assoc')->willReturn($userRow);
        $this->connMock->method('query')->willReturn($resultMock);

        // Start the login script
        include 'your-login-script.php';

        // Assert that the error message is correct for an inactive user
        $this->assertEquals($GLOBALS['errorMsg'], 'Your account has been disabled. Please contact an administrator.');
    }

    public function testEmailNotFound()
    {
        $_POST["email"] = "nonexistent@example.com";
        $_POST["password"] = "any_password";
        
        // Simulating no rows returned for the query
        $resultMock = $this->createMock(mysqli_result::class);
        $resultMock->method('num_rows')->willReturn(0);
        $this->connMock->method('query')->willReturn($resultMock);

        // Start the login script
        include 'your-login-script.php';

        // Assert that the error message for incorrect email or password is returned
        $this->assertEquals($GLOBALS['errorMsg'], 'Incorrect Email Address or Password');
    }
}
?>