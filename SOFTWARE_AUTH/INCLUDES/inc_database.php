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
        // Create a new mysqli connection
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Check the connection
        if ($this->conn->connect_error) {
            // Log error and terminate the script in case of connection failure
            error_log("Connection failed: " . $this->conn->connect_error);
            die("Connection failed: " . $this->conn->connect_error);
        }

        return $this->conn;
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
