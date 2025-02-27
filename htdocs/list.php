<?php $title = "List Tasks"; ?>

<?php include 'inc_connect.php';?>
<?php include 'inc_header.php';?>

<?php

$clearance = $_GET["clearance"];
echo "You are logged in as " . $clearance;

$sql = "SELECT * FROM tasks";
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

  while($row = $result->fetch_assoc()) {

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

} else {
  echo "0 results";
}

?>
<?php include 'inc_footer.php';?>
<?php include 'inc_disconnect.php';?>
