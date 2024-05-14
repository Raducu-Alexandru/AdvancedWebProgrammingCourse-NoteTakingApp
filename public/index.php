<?php
require_once '../src/config.php';

// Check if the user is already logged in, if yes then redirect them to the dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
  header("location: dashboard.php");
  // Optionally, use output buffering to delay sending output to the browser
  ob_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="0;url=dashboard.php"> <!-- Redirection fallback -->
  <title>Redirecting...</title>
</head>

<body>
  <p>Redirecting to your dashboard... If you are not redirected, <a href="dashboard.php">click here</a>.</p>
</body>

</html>
<?php
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
  ob_end_flush(); // Send output buffer and turn off output buffering
  exit;
}
?>