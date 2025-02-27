<?php $title = "Detailed Task View"; ?>

<?php include 'inc_connect.php';?>
<?php include 'inc_header.php';?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    # echo "We are posting.";
    $id = $_POST['new_id'];
    $subject = $_POST['subject'];
    $project = $_POST['project'];
    $assignee = $_POST['assignee'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];

    $sql = "UPDATE tasks SET
    subject=\"" . $subject . "\",
    project=\"" . $project . "\",
    assignee=" . $assignee . ",
    status=\"" . $status . "\",
    priority=\"" . $priority . "\"
    WHERE id = " . $id;
    # echo $sql;
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $sql = "SELECT * FROM tasks WHERE id = " . $id;
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    # echo "We are getting.";
    $id = $_GET['id'];
    $sql = "SELECT * FROM tasks WHERE id = " . $_GET['id'];
}
# echo $id;
# echo $sql;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  echo "<table>"
  . "   <tr>"
  . "    <th>ID</th>"
  . "    <th>Subject</th>"
  . "    <th>Project</th>"
  . "    <th>Assignee</th>"
  . "    <th>Status</th>"
  . "    <th>Priority</th>"
  . "  </tr>";

  $row = $result->fetch_assoc();

  echo "<tr>
      <td>" . $row["id"] . "</td>
      <td>" . $row["subject"] . "</td>
      <td>" . $row["project"]. "</td>
      <td>" . $row["assignee"]. "</td>
      <td>" . $row["status"]. "</td>
      <td>" . $row["priority"]. "</td>
    </tr>";

  echo "</table>";
  echo "<button onclick=\"document.location='edit.php?id=" . $row["id"] . "'\">Edit</button><br>";
  echo "<button onclick=\"document.location='list.php'\">Back</button>";
} else {
  echo "0 results";
}

?>

<?php include 'inc_footer.php';?>
<?php include 'inc_disconnect.php';?>
