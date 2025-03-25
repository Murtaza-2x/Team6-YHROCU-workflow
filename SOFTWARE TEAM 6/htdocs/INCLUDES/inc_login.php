<?php
session_start();


// Validate the user's credentials
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    include('INCLUDES/inc_connect.php');  // Make sure the database connection is included

    // SQL query to check if the user exists based on email
    $sql = "SELECT id, username, clearance, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['clearance'] = $user['clearance'];

            // Redirect to the dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Incorrect password
            $errorMsg = "Incorrect password. Please try again.";
        }
    } else {
        // No user found
        $errorMsg = "No account found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>

<head>
  <title><?php echo $title; ?></title>
  <link href="CSS/default_styles.css" rel="stylesheet">
  <link href="CSS/root_colors.css" rel="stylesheet">
  <link href="CSS/login_styles.css" rel="stylesheet">

  <script src="JS/SHOW-HIDE-PASSWORD.js"></script>
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- LOGIN BOX -->
<div class="LOGIN-CONTAINER">
  <div class="LOGIN-BOX">
    <h1>Welcome</h1>
    <p>Login to continue...</p>

    <form id="LOGIN-FORM" action="" method="post">

      <?php if (!empty($errorMsg)): ?>
        <div class="ERROR-MESSAGE"><?php echo htmlspecialchars($errorMsg); ?></div>
      <?php endif; ?>

      <div class="INPUT-GROUP">
        <img class="INPUT-GROUP-IMG" src="ICONS/email.svg" />
        <input type="text" id="email" name="email" placeholder="Email Address" required />
      </div>

      <div class="INPUT-GROUP">
        <img class="INPUT-GROUP-IMG" src="ICONS/lock.svg" />
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Password"
          required />

        <button type="button" id="togglePassword" class="PASSWORD-TOGGLE" onclick="togglePasswordVisibility()">
          <img id="toggleIcon" src="ICONS/eye-crossed.png" />
        </button>
      </div>

      <a class="RESET-LINK" href="#">Reset Password?</a>
      <a class="RESET-LINK-2">(contact admin for reset)</a>
      <button class="LOGIN-BUTTON" type="submit">Continue</button>
    </form>

    <div class="POWERED-BY">
      Powered by
    </div>
    <img class="AUTH-0" src="IMAGES/auth0.png" />
  </div>
</div>
<!-- LOGIN BOX END -->
