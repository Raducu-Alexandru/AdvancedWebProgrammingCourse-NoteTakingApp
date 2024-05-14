<?php
// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';

// Redirect if not logged in

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
  header("location: login.php");
  exit;
}

// Fetch user's notes from the database
try {
  $user_id = $_SESSION['user_id']; // Assuming your session stores user_id
  $stmt = $pdo->prepare("SELECT note_id, title, content FROM Notes WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $notes = $stmt->fetchAll();
} catch (PDOException $e) {
  $error_message = "Error loading notes: " . $e->getMessage();
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
  <?php include '../templates/header.php'; ?>
  <?php include '../templates/navbar.php'; ?>

  <div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <h2>Your Notes</h2>
    <?php if (!empty($notes)) : ?>
      <ul>
        <?php foreach ($notes as $note) : ?>
          <li>
            <strong><?php echo htmlspecialchars($note['title']); ?></strong>
            <p><?php echo htmlspecialchars($note['content']); ?></p>
            <a href="note_details.php?note_id=<?php echo $note['note_id']; ?>">View Details</a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else : ?>
      <p>You have no notes yet. <a href="new_note.php">Create one now</a>.</p>
    <?php endif; ?>
  </div>

  <?php include '../templates/footer.php'; ?>
</body>

</html>