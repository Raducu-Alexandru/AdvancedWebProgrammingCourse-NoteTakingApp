<?php
require_once 'config.php';

// Function to register a new user
function registerUser($username, $email, $password)
{
  global $pdo;
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  $sql = "INSERT INTO Users (username, email, password_hash) VALUES (?, ?, ?)";

  try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email, $hashed_password]);
    return true;
  } catch (PDOException $e) {
    return false;
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
