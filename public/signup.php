<?php
require_once '../src/config.php';
require_once '../src/functions.php';
require_once '../src/auth.php';

$message = ''; // Message to display to the user

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Validate username
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter a username.";
  } else {
    $username = sanitizeInput($_POST["username"]);
  }

  // Validate email
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter an email.";
  } else {
    $email = sanitizeInput($_POST["email"]);
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } else {
    $password = sanitizeInput($_POST["password"]);
  }

  // Check input errors before inserting in database
  if (empty($username_err) && empty($email_err) && empty($password_err)) {
    $register_result = registerUser($username, $email, $password);

    if ($register_result == "User registered successfully.") {
      header("location: login.php");
      exit;
    } else {
      $register_err = $register_result;
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="css/signup.css">
</head>

<body>
  <h2>Sign Up</h2>
  <?php if ($message !== '') : ?>
    <p><?= $message ?></p>
  <?php endif; ?>
  <form method="post" action="signup.php">
    <div>
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
    </div>
    <div>
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Sign Up</button>
  </form>
  <p>Already have an account? <a href="login.php">Log in here</a>.</p>
</body>

</html>