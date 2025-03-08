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

    $sql = "UPDATE tasks SET
    subject='" . $subject . "',
    project='" . $project . "',
    assignee=" . $assignee . ",
    status='" . $status . "',
    priority='" . $priority . "'
    WHERE id = " . $id;
    // echo $sql;
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
        header('Location: detail.php?id=' . $id);
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $id = $_GET['id'];

    $sql = "SELECT * FROM tasks WHERE id = " . $id;
    $result = $conn->query($sql);

    $row = $result->fetch_assoc();
    echo "<form action='edit.php' method='post'>"
        . "  <input type='hidden' id='id' name='id' value=" . $row['id'] . "><br>"
        . "  <label for='subject'>Subject:</label><br>"
        . "  <input type='text' id='subject' name='subject' value=" . $row['subject'] . "><br>"
        . "  <label for='project'>Project:</label><br>"
        . "  <input type='text' id='project' name='project' value=" . $row['project'] . "><br>"
        . "  <label for='assignee'>Assignee:</label><br>"
        . "  <input type='text' id='assignee' name='assignee' value=" . $row['assignee'] . "><br>"
        . "  <label for='status'>Status:</label><br>"
        . "  <input type='text' id='status' name='status' value=" . $row['status'] . "><br>"
        . "  <label for='priority'>Priority:</label><br>"
        . "  <input type='text' id='priority' name='priority' value=" . $row['priority'] . "><br>"
        . "  <input type='submit' value='Save'>"
        . "</form>";
}
?>
<?php include 'inc_footer.php'; ?>
<?php include 'inc_disconnect.php'; ?>
