<?php
// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$username = $email = $current_password = $new_password = $confirm_password = "";
$username_err = $email_err = $current_password_err = $new_password_err = $confirm_password_err = $update_err = $update_success = "";

// Fetch current user details
try {
  $stmt = $pdo->prepare("SELECT username, email, password_hash FROM Users WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $user = $stmt->fetch();

  if (!$user) {
    echo "Error fetching user details: User not found.";
    exit;
  }

  $current_username = $user['username'];
  $current_email = $user['email'];
  $hashed_password = $user['password_hash'];
} catch (PDOException $e) {
  echo "Error fetching user details: " . $e->getMessage();
  exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Validate current password
  if (empty(trim($_POST['current_password']))) {
    $current_password_err = "Please enter your current password.";
  } elseif (!password_verify($_POST['current_password'], $hashed_password)) {
    $current_password_err = "The current password you entered is not valid.";
  } else {
    $current_password = trim($_POST['current_password']);
  }

  // Validate and sanitize new username
  if (empty(trim($_POST['username']))) {
    $username_err = "Please enter a username.";
  } elseif (trim($_POST['username']) != $current_username) {
    $new_username = sanitizeInput($_POST['username']);
    // Check if username is already in use
    $stmt = $pdo->prepare("SELECT user_id FROM Users WHERE username = ?");
    $stmt->execute([$new_username]);
    if ($stmt->fetch()) {
      $username_err = "This username is already taken.";
    } else {
      $username = $new_username;
    }
  } else {
    $username = $current_username;
  }

  // Validate and sanitize new email
  if (empty(trim($_POST['email']))) {
    $email_err = "Please enter an email.";
  } elseif (trim($_POST['email']) != $current_email) {
    $new_email = sanitizeInput($_POST['email']);
    // Check if email is already in use
    $stmt = $pdo->prepare("SELECT user_id FROM Users WHERE email = ?");
    $stmt->execute([$new_email]);
    if ($stmt->fetch()) {
      $email_err = "This email is already in use.";
    } else {
      $email = $new_email;
    }
  } else {
    $email = $current_email;
  }

  // Validate new password
  if (!empty(trim($_POST['new_password']))) {
    if (strlen(trim($_POST['new_password'])) < 6) {
      $new_password_err = "Password must have at least 6 characters.";
    } else {
      $new_password = trim($_POST['new_password']);
      // Validate confirm password
      if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = "Please confirm your password.";
      } else {
        $confirm_password = trim($_POST['confirm_password']);
        if ($new_password != $confirm_password) {
          $confirm_password_err = "Password did not match.";
        }
      }
    }
  }

  // Check input errors before updating in database
  if (empty($current_password_err) && empty($username_err) && empty($email_err) && empty($new_password_err) && empty($confirm_password_err)) {
    try {
      $stmt = $pdo->prepare("UPDATE Users SET username = ?, email = ?, password_hash = ? WHERE user_id = ?");
      $hashed_new_password = !empty($new_password) ? password_hash($new_password, PASSWORD_DEFAULT) : $hashed_password;
      $stmt->execute([$username, $email, $hashed_new_password, $user_id]);

      // Update session variables
      $_SESSION['username'] = $username;
      $update_success = "Profile updated successfully.";
      header("location: settings.php");
    } catch (PDOException $e) {
      $update_err = "Error updating profile: " . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Settings</title>
  <link rel="stylesheet" href="css/settings.css">
</head>

<body>
  <?php include '../templates/header.php'; ?>
  <?php include '../templates/navbar.php'; ?>

  <div class="container">
    <h2>Account Settings</h2>
    <p>Update your account details below.</p>
    <?php
    if (!empty($update_success)) {
      echo '<div class="success">' . $update_success . '</div>';
    }
    if (!empty($update_err)) {
      echo '<div class="error">' . $update_err . '</div>';
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group">
        <label>Current Password</label>
        <input type="password" name="current_password" class="form-control">
        <span class="help-block"><?php echo $current_password_err; ?></span>
      </div>
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($current_username); ?>">
        <span class="help-block"><?php echo $username_err; ?></span>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($current_email); ?>">
        <span class="help-block"><?php echo $email_err; ?></span>
      </div>
      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" class="form-control">
        <span class="help-block"><?php echo $new_password_err; ?></span>
      </div>
      <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" class="form-control">
        <span class="help-block"><?php echo $confirm_password_err; ?></span>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>

  <?php include '../templates/footer.php'; ?>
</body>

</html>