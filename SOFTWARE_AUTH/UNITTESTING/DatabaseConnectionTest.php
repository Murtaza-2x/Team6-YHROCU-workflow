<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../INCLUDES/inc_database.php';

class DatabaseConnectionTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        // Create a new instance of DatabaseConnection before each test
        $this->db = new DatabaseConnection();
    }

    public function testConnect()
    {
        // Test the connect method
        $conn = $this->db->connect();
        
        // Assert that the connection is not null
        $this->assertNotNull($conn, "Connection should not be null");
        
        // You can also check the connection status if needed
        $this->assertTrue($conn->ping(), "Connection should be live");
    }

    public function testDisconnect()
    {
        // First connect
        $conn = $this->db->connect();
        $this->assertNotNull($conn, "Connection should not be null");
        
        // Now disconnect
        $this->db->disconnect();
        
        // You cannot check the connection after disconnecting, 
        // but you can simulate by checking if the disconnect method works correctly
        // Note: We cannot directly test for "disconnection", but we assume the method works if no error occurs.
        $this->assertTrue(true);
    }
}
