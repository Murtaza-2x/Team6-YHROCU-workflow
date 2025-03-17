<head>
  <title><?php echo $title; ?></title>
  <link href="CSS/default_styles.css" rel="stylesheet">
  <link href="CSS/root_colors.css" rel="stylesheet">
  <link href="CSS/login_styles.css" rel="stylesheet">

  <script src="JS/SHOW-HIDE-PASSWORD.js"></script>
</head>

<body>

  <!-- MIDDLE SECTION -->
  <div class="MIDDLE-SECTION">
    <p class="MIDDLE-HERO-IMAGE"></p>

    <!-- LOGIN BOX -->
    <div class="LOGIN-CONTAINER">
      <div class="LOGIN-BOX">
        <h1>Welcome</h1>
        <p>Login to continue...</p>

        <form id="LOGIN-FORM" action="index.php" method="post">
          <div class="INPUT-GROUP">
            <img class="INPUT-GROUP-IMG" src="ICONS/email.svg" /></img>
            <input type="text" id="username" name="username" placeholder="Username" required />
          </div>

          <div class="INPUT-GROUP">
            <img class="INPUT-GROUP-IMG" src="ICONS/lock.svg" /></img>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Password"
              required />

            <button type="button" id="togglePassword" class="PASSWORD-TOGGLE" onclick="togglePasswordVisibility()">
              <img id="toggleIcon" src="ICONS/eye-crossed.png"/>
            </button>
          </div>

          <a class="RESET-LINK" href="#" onclick="">Reset Password?</a>
          <a class="RESET-LINK-2">(contact admin for reset)</a>
          <button class="LOGIN-BUTTON" type="submit">Continue</button>
        </form>

        <div class="POWERED-BY">
          Powered by
        </div>
        <img class="AUTH-0" src="IMAGES/auth0.png" /></img>
      </div>
    </div>
    <!-- LOGIN BOX END -->

  </div>
  <!-- MIDDLE SECTION END -->