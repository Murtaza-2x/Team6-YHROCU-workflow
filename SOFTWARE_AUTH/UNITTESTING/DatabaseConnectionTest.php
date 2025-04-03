<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/traits/DatabaseTestTrait.php';

class DatabaseConnectionTest extends TestCase
{
    use DatabaseTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    protected function tearDown(): void
    {
        $this->tearDownDatabase();
    }

    public function testConnectionIsValid()
    {
        // Asserts that the connection is a valid mysqli instance.
        $this->assertInstanceOf(mysqli::class, $this->conn, "The database connection is not valid.");
    }

    public function testInsertAndFetch()
    {
        // Insert a dummy row into a test table.
        $query = "INSERT INTO test_table (name) VALUES (?)";
        $dummyName = "PHPUnitTest";
        $result = $this->insertDummy($query, [$dummyName], "s");
        $this->assertTrue($result, "Failed to insert dummy row.");

        // Fetch the inserted row.
        $fetchQuery = "SELECT * FROM test_table WHERE name = '$dummyName' LIMIT 1";
        $row = $this->fetchSingle($fetchQuery);
        $this->assertNotNull($row, "No row was fetched.");
        $this->assertEquals($dummyName, $row['name'], "Fetched row does not match inserted value.");
    }
}