<?php
// -------------------------------------------------------------
// File: UNITTESTING/traits/DatabaseTestTrait.php
// Purpose: Handles database connection setup and teardown for tests
// -------------------------------------------------------------

trait DatabaseTestTrait
{
    protected mysqli $conn;

    protected function setUpDatabase(): void
    {
        require_once __DIR__ . '/../../INCLUDES/inc_database.php';
        
        // Create a fresh database connection.
        $db = new DatabaseConnection();
        $this->conn = $db->connect();

        if (!$this->conn instanceof mysqli) {
            throw new Exception("Database connection not available in test.");
        }
    }

    protected function tearDownDatabase(): void
    {
        if (isset($this->conn) && $this->conn instanceof mysqli) {
            $this->conn->close();
        }
    }
    
    // Helper method to insert a dummy row.
    protected function insertDummy(string $query, array $params, string $types): bool
    {
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare query: " . $this->conn->error);
        }
        $stmt->bind_param($types, ...$params);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    // Helper method to fetch a single row.
    protected function fetchSingle(string $query): ?array
    {
        $result = $this->conn->query($query);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
}
