<?php $title = "List Tasks"; ?>

<?php include 'inc_connect.php';?>
<?php include 'inc_header.php';?>

<?php

$sql = "SELECT * FROM tasks";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  echo "<table>"
  . "   <tr>"
  . "    <th>ID</th>"
  . "    <th>Subject</th>"
  . "    <th>Project</th>"
  . "  </tr>";

  while($row = $result->fetch_assoc()) {

    echo "<tr>
        <td>" . $row["id"] . "</td>
        <td>" . $row["subject"] . "</td>
        <td>" . $row["project"]. "</td>
      </tr>";
  }
  echo "</table>";

} else {
  echo "0 results";
}

?>
<?php include 'inc_footer.php';?>
<?php include 'inc_disconnect.php';?>
