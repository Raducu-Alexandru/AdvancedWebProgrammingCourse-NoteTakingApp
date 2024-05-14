<?php
require_once 'config.php';

// Function to register a new user
function registerUser($username, $email, $password)
{
  global $pdo;

  // Check if username or email is already in use
  try {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $user = $stmt->fetch();

    if ($user) {
      if ($user['username'] == $username) {
        return "Username is already in use.";
      }
      if ($user['email'] == $email) {
        return "Email is already in use.";
      }
    }

    // If username and email are not in use, create the new user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hashed_password]);

    return "User registered successfully.";
  } catch (PDOException $e) {
    return "Error: " . $e->getMessage();
  }
}


// Function to verify user login
function verifyUser($username, $password)
{
  global $pdo;
  $sql = "SELECT user_id, password_hash FROM Users WHERE username = ?";

  try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
      session_start();
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['logged_in'] = true;
      return true;
    } else {
      return false;
    }
  } catch (PDOException $e) {
    return false;
  }
}
