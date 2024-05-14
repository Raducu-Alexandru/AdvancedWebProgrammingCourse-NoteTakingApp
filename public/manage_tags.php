<?php
// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("location: login.php");
  exit;
}

// Handle new tag submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['new_tag'])) {
  $new_tag = sanitizeInput($_POST['new_tag']);
  $sql = "INSERT INTO Tags (name) VALUES (?)";
  if ($stmt = $pdo->prepare($sql)) {
    $stmt->execute([$new_tag]);
  }
}

// Fetch all tags
$sql = "SELECT tag_id, name FROM Tags";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$tags = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Tags</title>
  <link rel="stylesheet" href="css/manage_tags.css">
</head>

<body>
  <?php include '../templates/header.php'; ?>
  <?php include '../templates/navbar.php'; ?>

  <div class="container">
    <h2>Manage Tags</h2>
    <form action="manage_tags.php" method="post">
      <input type="text" name="new_tag" placeholder="Add new tag" required>
      <button type="submit">Add Tag</button>
    </form>
    <ul>
      <?php foreach ($tags as $tag) : ?>
        <li>
          <?php echo htmlspecialchars($tag['name']); ?>
          <a href="delete_tag.php?id=<?php echo $tag['tag_id']; ?>">Delete</a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <?php include '../templates/footer.php'; ?>
</body>

</html>