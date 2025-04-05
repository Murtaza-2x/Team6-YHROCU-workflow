<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/DatabaseTestTrait.php';

class DatabaseConnectionTest extends TestCase
{
    use DatabaseTestTrait;

    /**
     * setUp() is called before each test method.
     * Here, we initialize the database connection.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();

        // Create test_table if it doesn't exist
        $this->conn->query("
        CREATE TABLE IF NOT EXISTS test_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        )
    ");
    }

    /**
     * tearDown() is called after each test method.
     * We clean up the database connection here.
     */
    protected function tearDown(): void
    {
        // Tear down the database connection and clean up any test data.
        $this->tearDownDatabase();
    }

    /**
     * Test that the database connection is valid.
     */
    public function testConnectionIsValid()
    {
        // Assert that the connection is an instance of the mysqli class.
        $this->assertInstanceOf(mysqli::class, $this->conn, "The database connection is not valid.");
    }

    /**
     * Test inserting a dummy row into the test_table and then fetching it.
     */
    public function testInsertAndFetch()
    {
        // Define the insert query using a prepared statement for security.
        $query = "INSERT INTO test_table (name) VALUES (?)";
        $dummyName = "PHPUnitTest";

        // Prepare the insert statement.
        $stmt = $this->conn->prepare($query);
        // Bind the dummy name parameter to the statement.
        $stmt->bind_param("s", $dummyName);
        // Execute the insert statement.
        $result = $stmt->execute();

        // Assert that the row was inserted successfully.
        $this->assertTrue($result, "Failed to insert dummy row.");

        // Define the fetch query using a prepared statement.
        $fetchQuery = "SELECT * FROM test_table WHERE name = ? LIMIT 1";
        // Prepare the fetch statement.
        $stmtFetch = $this->conn->prepare($fetchQuery);
        // Bind the dummy name parameter to the fetch statement.
        $stmtFetch->bind_param("s", $dummyName);
        // Execute the fetch statement.
        $stmtFetch->execute();
        // Get the result set.
        $resultFetch = $stmtFetch->get_result();
        // Fetch the row as an associative array.
        $row = $resultFetch->fetch_assoc();

        // Assert that a row was successfully fetched.
        $this->assertNotNull($row, "No row was fetched.");
        // Assert that the fetched row's 'name' matches the dummy name.
        $this->assertEquals($dummyName, $row['name'], "Fetched row does not match inserted value.");
    }
}