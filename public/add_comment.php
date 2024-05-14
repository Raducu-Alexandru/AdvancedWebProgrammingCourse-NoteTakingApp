<?php
require_once '../src/config.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("location: login.php");
  exit;
}

// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['comment'])) {
  $note_id = isset($_POST['note_id']) ? (int)$_POST['note_id'] : 0;
  $user_id = $_SESSION['user_id'];
  $comment_content = sanitizeInput($_POST['comment']);

  // Insert the comment into the database
  $sql = "INSERT INTO Comments (note_id, user_id, content) VALUES (?, ?, ?)";
  if ($stmt = $pdo->prepare($sql)) {
    $stmt->execute([$note_id, $user_id, $comment_content]);
  }

  // Redirect back to the note details page
  header("Location: note_details.php?note_id=$note_id");
  exit;
}

// If the request method is not POST, redirect to the dashboard
header("Location: dashboard.php");
exit;
