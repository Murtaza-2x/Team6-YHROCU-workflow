<?php
require_once __DIR__ . '/inc_database.php';

$db = new DatabaseConnection();
$conn = $db->connect();

if (!$conn && !defined('NO_DB_REQUIRED')) {
    header("Location: /INCLUDES/inc_database_error.php");
    exit;
}
?>