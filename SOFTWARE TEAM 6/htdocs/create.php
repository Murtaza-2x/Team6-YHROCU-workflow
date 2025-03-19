<?php $title = 'Create New Task'; ?>

<?php include 'inc_connect.php'; ?>
<?php include 'inc_header.php'; ?>

<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $project = $_POST['project'];
    $assignee = $_POST['assignee'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $sql = "INSERT INTO tasks (`subject`, `project`, `assignee`, `status`, `priority`) 
        VALUES ('" . $subject . "', '" . $project . "',
        '" . $assignee . "', '" . $status . "', '" . $priority . "')";
    echo $sql;
    if ($conn->query($sql) === true) {
        $id = $conn->insert_id;
        echo "New record created successfully. Last inserted ID is: " . $id;
        header('Location: detail.php?id=' . $id);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo "<form action='create.php' method='post'>"
        . "  <label for='subject'>Subject:</label><br>"
        . "  <input type='text' id='subject' name='subject'><br>"
        . "  <label for='project'>Project:</label><br>"
        . "  <input type='text' id='project' name='project'><br>"
        . "  <label for='assignee'>Assignee:</label><br>"
        . "  <input type='text' id='assignee' name='assignee'><br>"
        . "  <label for='status'>Status:</label><br>"
        . "  <input type='text' id='status' name='status'><br>"
        . "  <label for='priority'>Priority:</label><br>"
        . "  <input type='text' id='priority' name='priority'><br>"
        . "  <input type='submit' value='Save'>"
        . "</form>";
}
?>
<?php include 'inc_footer.php'; ?>
<?php include 'inc_disconnect.php'; ?>
