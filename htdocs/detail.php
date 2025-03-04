<?php $title = "Detailed Task View"; ?>

<?php include 'inc_connect.php'; ?>
<?php include 'inc_header.php'; ?>

<?php

// echo "We are getting.";
$id = $_GET['id'];
$sql = "SELECT * FROM tasks WHERE id = " . $id;
// echo $id;
// echo $sql;
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
      <td>" . $row["project"] . "</td>
      <td>" . $row["assignee"] . "</td>
      <td>" . $row["status"] . "</td>
      <td>" . $row["priority"] . "</td>
    </tr>";

  echo "</table>";

  if ($_SESSION["clearance"] != 'user') {
    echo "<button onclick=\"document.location='edit.php?id=" . $row["id"] . "'\">Edit</button><br>";
  }
  echo "<button onclick=\"document.location='list.php'\">Back</button>";
} else {
  echo "0 results";
}

?>

<?php include 'inc_footer.php'; ?>
<?php include 'inc_disconnect.php'; ?>
