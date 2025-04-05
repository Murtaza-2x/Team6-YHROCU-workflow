<?php

class DatabaseConnection
{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "rocu";
    private $conn = null;

    /*
    -------------------------------------------------------------
    Method: connect
    Description:
    - Creates a connection to the database using mysqli.
    - Implements error handling for connection failure.
    - Returns the mysqli connection object.
    -------------------------------------------------------------
    */
    public function connect()
    {
        try {
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

            // Check connection (redundant in PHP 8.1+, but good for logs)
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            return $this->conn;
        } catch (mysqli_sql_exception $e) {
            // Handle connection errors gracefully
            error_log("MySQLi Connection Error: " . $e->getMessage());
            return null; // Signal failure
        } catch (Exception $e) {
            error_log("General DB Error: " . $e->getMessage());
            return null;
        }
    }

    /*
    -------------------------------------------------------------
    Method: disconnect
    Description:
    - Closes the active database connection.
    -------------------------------------------------------------
    */
    public function disconnect()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

?>
