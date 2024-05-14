<?php
// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("location: login.php");
  exit;
}

// Check if the tag ID is provided
if (isset($_GET['id'])) {
  $tag_id = (int)$_GET['id'];

  // Validate tag ID
  if ($tag_id > 0) {
    try {
      // Prepare the SQL statement to delete the tag
      $stmt = $pdo->prepare("DELETE FROM Tags WHERE tag_id = ?");
      $stmt->execute([$tag_id]);

      // Delete the tag from Note_Tags as well to maintain integrity
      $stmt = $pdo->prepare("DELETE FROM Note_Tags WHERE tag_id = ?");
      $stmt->execute([$tag_id]);

      // Redirect to manage_tags.php
      header("Location: manage_tags.php");
      exit;
    } catch (PDOException $e) {
      echo "Error deleting tag: " . $e->getMessage();
    }
  } else {
    echo "Invalid tag ID.";
  }
} else {
  echo "Tag ID not provided.";
}
