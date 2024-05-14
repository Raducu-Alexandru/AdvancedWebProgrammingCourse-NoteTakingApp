<?php
// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("location: login.php");
  exit;
}

// Initialize variables
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category_name = $category_description = "";
$name_err = $description_err = "";

// Fetch category details
if ($category_id > 0) {
  $stmt = $pdo->prepare("SELECT * FROM Categories WHERE category_id = ?");
  $stmt->execute([$category_id]);
  $category = $stmt->fetch();

  if ($category) {
    $category_name = $category['name'];
    $category_description = $category['description'];
  } else {
    echo "Category not found.";
    exit;
  }
} else {
  echo "Invalid category ID.";
  exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Validate and sanitize inputs
  $input_name = sanitizeInput($_POST['name']);
  $input_description = sanitizeInput($_POST['description']);

  if (empty($input_name)) {
    $name_err = "Please enter a category name.";
  } else {
    $category_name = $input_name;
  }

  if (empty($input_description)) {
    $category_description = NULL;
  } else {
    $category_description = $input_description;
  }

  // Check for errors before updating
  if (empty($name_err) && empty($description_err)) {
    try {
      $stmt = $pdo->prepare("UPDATE Categories SET name = ?, description = ? WHERE category_id = ?");
      $stmt->execute([$category_name, $category_description, $category_id]);

      // Redirect to manage_categories.php
      header("Location: manage_categories.php");
      exit;
    } catch (PDOException $e) {
      echo "Error updating category: " . $e->getMessage();
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Category</title>
  <link rel="stylesheet" href="css/edit_categories.css">
</head>

<body>
  <?php include '../templates/header.php'; ?>
  <?php include '../templates/navbar.php'; ?>

  <div class="container">
    <h2>Edit Category</h2>
    <form action="edit_category.php?id=<?php echo $category_id; ?>" method="post">
      <div class="form-group">
        <label>Category Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($category_name); ?>">
        <span class="help-block"><?php echo $name_err; ?></span>
      </div>
      <div class="form-group">
        <label>Category Description</label>
        <textarea name="description" class="form-control"><?php echo htmlspecialchars($category_description); ?></textarea>
        <span class="help-block"><?php echo $description_err; ?></span>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary">Update Category</button>
        <a href="manage_categories.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>

  <?php include '../templates/footer.php'; ?>
</body>

</html>