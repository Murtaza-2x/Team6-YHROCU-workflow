<?php
require_once __DIR__ . '/inc_database.php';

try {
    $db = new DatabaseConnection();
    $conn = $db->connect();
} catch (mysqli_sql_exception $e) {
    error_log("DB Connection Error: " . $e->getMessage());

    // Show user-friendly message and stop execution
    echo "<p class='ERROR-MESSAGE'>MySQLi Database Connection Failed. Please contact Administrator or try again later.</p>";
    include __DIR__ . '/inc_footer.php';
    exit;
}
?>
