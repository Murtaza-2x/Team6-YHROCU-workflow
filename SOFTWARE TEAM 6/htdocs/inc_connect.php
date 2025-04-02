<?php
    $servername = "localhost";
    $username = "pma";
    $password = "pmapma9999";
    $dbname = "rocu";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
?>
