<head>
  <title><?php echo $title; ?></title>
  <link href="CSS/login_styles.css" rel="stylesheet">

  <script src="JS/SHOW-HIDE-PASSWORD.js"></script>
</head>

<p class="MIDDLE-HERO-IMAGE"></p>

<!-- LOGIN BOX SECTION -->
<div class="LOGIN-CONTAINER">
  <div class="LOGIN-BOX">
    <h1>Welcome</h1>
    <p>Login to continue...</p>

    <!-- ERROR MESSAGE -->
    <?php if ($errorMsg): ?>
      <p class="LOGIN-ERROR-MESSAGE"><?php echo $errorMsg; ?></p>
    <?php endif; ?>
    <!-- ERROR MESSAGE END -->

    <!-- LOGIN FORM -->
    <form method="post" action="login_check.php">
      <!-- EMAIL INPUT -->
      <div class="INPUT-GROUP">
        <img class="INPUT-GROUP-IMG" src="ICONS/email.svg" />
        <input type="text" id="email" name="email" placeholder="Email Address" required />
      </div>
      <!-- EMAIL INPUT END -->

      <!-- PASSWORD INPUT -->
      <div class="INPUT-GROUP">
        <img class="INPUT-GROUP-IMG" src="ICONS/lock.svg" />
        <input type="password" id="password" name="password" placeholder="Password" required />
        <button type="button" id="togglePassword" class="PASSWORD-TOGGLE" onclick="togglePasswordVisibility()">
          <img id="toggleIcon" src="ICONS/eye-crossed.png" />
        </button>
      </div>
      <!-- PASSWORD INPUT END -->

      <!-- PASSWORD RESET LINK -->
      <a href="auth0_reset.php" class="RESET-LINK">Forgot Password?</a>
      <a class="RESET-LINK-2">(contact admin for reset)</a>
      <!-- PASSWORD RESET LINK END -->

      <!-- LOGIN BUTTON -->
      <button class="LOGIN-BUTTON" href="auth0_login.php">Login with Auth0</button>
      <!-- LOGIN BUTTON END -->
    </form>

    <div class="POWERED-BY">Powered by</div>
    <img class="AUTH-0" src="IMAGES/auth0.png" />
  </div>
</div>
<!-- LOGIN BOX SECTION END -->