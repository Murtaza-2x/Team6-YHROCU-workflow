<?php
require_once __DIR__ . '/inc_database.php';

// Using the DatabaseConnection class
$db = new DatabaseConnection();

// Establish the connection
$conn = $db->connect();
