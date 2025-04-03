<?php
// -------------------------------------------------------------
// File: INCLUDES/DatabaseFactory.php
// Description: Returns a mysqli instance configured by environment variables
// -------------------------------------------------------------

class DatabaseFactory
{
    public static function create(): mysqli
    {
        $mysqli = new mysqli(
            $_ENV['DB_HOST'] ?? 'localhost',
            $_ENV['DB_USER'] ?? 'root',
            $_ENV['DB_PASS'] ?? '',
            $_ENV['DB_NAME'] ?? 'rocu'
        );

        if ($mysqli->connect_error) {
            throw new Exception('Database connection failed: ' . $mysqli->connect_error);
        }

        return $mysqli;
    }
}
