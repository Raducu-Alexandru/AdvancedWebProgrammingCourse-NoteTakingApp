<?php
// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("location: login.php");
  exit;
}

// Check if the category ID is provided
if (isset($_GET['id'])) {
  $category_id = (int)$_GET['id'];

  // Validate category ID
  if ($category_id > 0) {
    try {
      // Prepare the SQL statement to delete the category
      $stmt = $pdo->prepare("DELETE FROM Categories WHERE category_id = ?");
      $stmt->execute([$category_id]);

      // Delete the category from Note_Categories as well to maintain integrity
      $stmt = $pdo->prepare("DELETE FROM Note_Categories WHERE category_id = ?");
      $stmt->execute([$category_id]);

      // Redirect to manage_categories.php
      header("Location: manage_categories.php");
      exit;
    } catch (PDOException $e) {
      echo "Error deleting category: " . $e->getMessage();
    }
  } else {
    echo "Invalid category ID.";
  }
} else {
  echo "Category ID not provided.";
}
