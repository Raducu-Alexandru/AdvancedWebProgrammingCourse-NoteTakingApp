<?php
// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';
require_once '../src/auth.php';

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Check if username is empty
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter username.";
  } else {
    $username = sanitizeInput($_POST["username"]);
  }

  // Check if password is empty
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter your password.";
  } else {
    $password = sanitizeInput($_POST["password"]);
  }

  // Validate credentials
  if (empty($username_err) && empty($password_err)) {
    if (verifyUser($username, $password)) {
      // Password is correct, so start a new session and
      // Redirect the user to the dashboard page
      session_start();
      $_SESSION["logged_in"] = true;
      $_SESSION["username"] = $username; // Store username in session variable
      $_SESSION["user_id"] = getUserId($username); // Store user_id in session variable
      header("location: dashboard.php");
      exit;
    } else {
      // Display an error message if password is not valid
      $login_err = "Invalid username or password.";
    }
  }
}

function getUserId($username)
{
  global $pdo;
  $sql = "SELECT user_id FROM Users WHERE username = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$username]);
  $user = $stmt->fetch();
  return $user['user_id'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="css/login.css">
</head>

<body>
  <div>
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>

    <?php
    if (!empty($login_err)) {
      echo '<div class="error">' . $login_err . '</div>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div>
        <label>Username</label>
        <input type="text" name="username" <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?> value="<?php echo $username; ?>">
        <span class="help-block"><?php echo $username_err; ?></span>
      </div>
      <div>
        <label>Password</label>
        <input type="password" name="password" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>>
        <span class="help-block"><?php echo $password_err; ?></span>
      </div>
      <div>
        <button type="submit">Login</button>
      </div>
      <p>Don't have an account? <a href="signup.php">Sign up now</a>.</p>
    </form>

  </div>
</body>

</html>