<?php $title = 'Edit Task'; ?>

<?php include 'inc_connect.php'; ?>
<?php include 'inc_header.php'; ?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $subject = $_POST['subject'];
    $project = $_POST['project'];
    $assignee = $_POST['assignee'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];

    $time = date('Y-m-d H:i:s', time());
    $sql_update = "UPDATE tasks SET valid_until='" . $time . "'
    WHERE id = " . $id . " AND valid_until IS NULL";
    $sql_insert = "INSERT INTO tasks (`subject`, `project`, `assignee`, `status`, `priority`) 
        VALUES ('" . $subject . "', '" . $project . "',
        '" . $assignee . "', '" . $status . "', '" . $priority . "')";


    // echo $sql;
    if ($conn->query($sql_update) === TRUE and $conn->query($sql_insert) === TRUE) {
        $id = $conn->insert_id;
        echo "Record updated successfully";
        header('Location: detail.php?id=' . $id);
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $id = $_GET['id'];

    $sql = "SELECT * FROM tasks WHERE id = " . $id . " AND valid_until IS NULL";
    $result = $conn->query($sql);

    $row = $result->fetch_assoc();
    echo "<form action='edit.php' method='post'>"
        . "  <input type='hidden' id='id' name='id' value='" . $row['id'] . "'><br>"
        . "  <label for='subject'>Subject:</label><br>"
        . "  <input type='text' id='subject' name='subject' value='" . $row['subject'] . "'><br>"
        . "  <label for='project'>Project:</label><br>"
        . "  <input type='text' id='project' name='project' value='" . $row['project'] . "'><br>"
        . "  <label for='assignee'>Assignee:</label><br>"
        . "  <input type='text' id='assignee' name='assignee' value='" . $row['assignee'] . "'><br>"
        . "  <label for='status'>Status:</label><br>"
        . "  <input type='text' id='status' name='status' value='" . $row['status'] . "'><br>"
        . "  <label for='priority'>Priority:</label><br>"
        . "  <input type='text' id='priority' name='priority' value='" . $row['priority'] . "'><br>"
        . "  <input type='submit' value='Save'>"
        . "</form>";
}
?>
<?php include 'inc_footer.php'; ?>
<?php include 'inc_disconnect.php'; ?>
