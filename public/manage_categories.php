<?php
// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("location: login.php");
  exit;
}

// Handle new category submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['new_category'])) {
  $new_category = sanitizeInput($_POST['new_category']);
  $sql = "INSERT INTO Categories (name) VALUES (?)";
  if ($stmt = $pdo->prepare($sql)) {
    $stmt->execute([$new_category]);
  }
}

// Fetch all categories
$sql = "SELECT category_id, name FROM Categories";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Categories</title>
  <link rel="stylesheet" href="css/manage_categories.css">
</head>

<body>
  <?php include '../templates/header.php'; ?>
  <?php include '../templates/navbar.php'; ?>

  <div class="container">
    <h2>Manage Categories</h2>
    <form action="manage_categories.php" method="post">
      <input type="text" name="new_category" placeholder="Add new category" required>
      <button type="submit">Add Category</button>
    </form>
    <ul>
      <?php foreach ($categories as $category) : ?>
        <li>
          <?php echo htmlspecialchars($category['name']); ?>
          <a href="edit_category.php?id=<?php echo $category['category_id']; ?>">Edit</a>
          <a href="delete_category.php?id=<?php echo $category['category_id']; ?>">Delete</a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <?php include '../templates/footer.php'; ?>
</body>

</html>