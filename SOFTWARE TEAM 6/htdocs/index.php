<?php $title = "ROCU: Login"; ?>
<?php include 'inc_connect.php'; ?>
<?php include 'inc_header.php'; ?>

<?php
if (isset($_POST["username"])) {
    $sql = "SELECT * FROM users WHERE username = '" . $_POST["username"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $correct_pass = $row["password"];
        if ($_POST["password"] == $correct_pass) {
            $_SESSION["id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["clearance"] = $row["clearance"];
            header('Location: list.php?clearance=' . $clearance . '&id=' . $id);
        } else {
            echo "Incorrect username or password";
        }
    } else {
        echo "Incorrect username or password";
    }
}
include 'inc_login.php';
?>

<?php include 'inc_footer.php'; ?>
<?php include 'inc_disconnect.php'; ?>
