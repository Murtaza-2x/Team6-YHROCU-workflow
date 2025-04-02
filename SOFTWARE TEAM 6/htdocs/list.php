<?php $title = "List Tasks"; ?>

<?php include 'inc_connect.php'; ?>
<?php include 'inc_header.php'; ?>

<?php

$clearance = $_SESSION["clearance"];
$id = $_SESSION["id"];
$username = $_SESSION["username"];
echo "Welcome, " . $clearance . " " . $username . ". Your id is " . $id . ".<br>";

if ($clearance == 'user') {
  $sql = "SELECT * FROM tasks WHERE assignee = " . $id . " AND valid_until IS NULL";
} else {
  $sql = "SELECT * FROM tasks WHERE valid_until IS NULL";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  echo "<table>"
    . "  <tr>"
    . "    <th>ID</th>"
    . "    <th>Subject</th>"
    . "    <th>Project</th>"
    . "    <th>Assignee</th>"
    . "    <th>Status</th>"
    . "    <th>Priority</th>"
    . "  </tr>";

  while ($row = $result->fetch_assoc()) {

    echo "<tr>
        <td>" . $row["id"] . "</td>
        <td><a href=\"detail.php?id=" . $row["id"] . "\" title=\"Detailed view\">" . $row["subject"] . "<a></td>
        <td>" . $row["project"] . "</td>
        <td>" . $row["assignee"] . "</td>
        <td>" . $row["status"] . "</td>
        <td>" . $row["priority"] . "</td>
      </tr>";
  }
  echo "</table>";
  if ($_SESSION["clearance"] != 'user') {
    echo "<button onclick=\"document.location='create.php'\">Add Task</button><br>";
  }
} else {
  echo "0 results";
}

?>
<?php include 'inc_footer.php'; ?>
<?php include 'inc_disconnect.php'; ?>
