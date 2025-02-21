<?php $title = "ROCU: Login"; ?>

<?php
$user="Tom";
$pass="123";
?>

<?php include 'inc_connect.php';?>
<?php include 'inc_header.php';?>

<?php
if (isset($_POST["username"])) {
    if ($_POST["username"] == $user and $_POST["pwd"] == $pass) {
      header('Location: list.php');
        exit;
    } else {
      echo "Incorrect username or password.";
    }
} else {
    include 'inc_login.php';
}
?>

<?php include 'inc_footer.php';?>
<?php include 'inc_disconnect.php';?>
