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

    <?php if (isset($_GET['error'])) {
      switch ($_GET['error']) {
        case 'login_required':
          echo '<div class="ERROR-MESSAGE">You must be logged in.</div>';
          break;
        case 'clearance_required':
          echo '<div class="ERROR-MESSAGE">You do not have permission to access this page.</div>';
          break;
        case 'invalid_role':
          echo '<div class="ERROR-MESSAGE">Invalid role detected.</div>';
          break;
        default:
          echo '<div class="ERROR-MESSAGE">Unknown error occurred.</div>';
      }
    } ?>

    <div class="INPUT-GROUP">
      <img class="INPUT-GROUP-IMG" src="ICONS/email.svg" />
      <input type="text" id="email" name="email" placeholder="Email Address" required />
    </div>

    <div class="INPUT-GROUP">
      <img class="INPUT-GROUP-IMG" src="ICONS/lock.svg" />
      <input type="password" id="password" name="password" placeholder="Password" required />
      <button type="button" id="togglePassword" class="PASSWORD-TOGGLE" onclick="togglePasswordVisibility()">
        <img id="toggleIcon" src="ICONS/eye-crossed.png" />
      </button>
    </div>

    <a href="auth0_reset.php" class="RESET-LINK">Forgot Password?</a>
    <a class="RESET-LINK-2">(contact admin for reset)</a>
    <a class="LOGIN-BUTTON" href="auth0_login.php">Login with Auth0</a>

    <div class="POWERED-BY">Powered by</div>
    <img class="AUTH-0" src="IMAGES/auth0.png" />
  </div>
</div>
<!-- LOGIN BOX END -->