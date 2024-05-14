<?php
// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("location: login.php");
  exit;
}

// Check if the comment ID is provided
if (isset($_GET['id']) && isset($_GET['note_id'])) {
  $comment_id = (int)$_GET['id'];
  $note_id = (int)$_GET['note_id'];

  // Validate comment ID
  if ($comment_id > 0) {
    try {
      // Prepare the SQL statement to delete the comment
      $stmt = $pdo->prepare("DELETE FROM Comments WHERE comment_id = ?");
      $stmt->execute([$comment_id]);

      // Redirect to note_details.php
      header("Location: note_details.php?note_id=$note_id");
      exit;
    } catch (PDOException $e) {
      echo "Error deleting comment: " . $e->getMessage();
    }
  } else {
    echo "Invalid comment ID.";
  }
} else {
  echo "Comment ID or Note ID not provided.";
}
