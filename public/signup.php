<?php
require_once '../src/config.php';
require_once '../src/functions.php';
require_once '../src/auth.php';

$message = ''; // Message to display to the user

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Sanitize and validate user input
  $username = sanitizeInput($_POST['username']);
  $email = sanitizeInput($_POST['email']);
  $password = sanitizeInput($_POST['password']);

  // Simple validation
  if (empty($username) || empty($email) || empty($password)) {
    $message = 'All fields are required!';
  } else {
    // Attempt to register the user
    $registration_success = registerUser($username, $email, $password);
    if ($registration_success) {
      $message = 'Registration successful!';
      header('Location: login.php'); // Redirect to login page after successful registration
      exit;
    } else {
      $message = 'Registration failed! User might already exist.';
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