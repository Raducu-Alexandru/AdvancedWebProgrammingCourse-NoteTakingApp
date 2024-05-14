<?php
// Include configuration and function files
require_once '../src/config.php';
require_once '../src/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("location: login.php");
  exit;
}

// Fetch note details
$note_id = isset($_GET['note_id']) ? (int)$_GET['note_id'] : 0;
$user_id = $_SESSION['user_id'];
$note = null;
$comments = [];
$categories = [];
$tags = [];
$note_categories = [];
$note_tags = [];

try {
  $stmt = $pdo->prepare("SELECT * FROM Notes WHERE note_id = ? AND user_id = ?");
  $stmt->execute([$note_id, $user_id]);
  $note = $stmt->fetch();

  if ($note) {
    $stmt = $pdo->prepare("SELECT * FROM Comments WHERE note_id = ?");
    $stmt->execute([$note_id]);
    $comments = $stmt->fetchAll();

    // Fetch all categories and tags
    $stmt = $pdo->prepare("SELECT * FROM Categories");
    $stmt->execute();
    $categories = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM Tags");
    $stmt->execute();
    $tags = $stmt->fetchAll();

    // Fetch categories and tags associated with the note
    $stmt = $pdo->prepare("SELECT c.category_id, c.name FROM Categories c 
                               JOIN Note_Categories nc ON c.category_id = nc.category_id 
                               WHERE nc.note_id = ?");
    $stmt->execute([$note_id]);
    $note_categories = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT t.tag_id, t.name FROM Tags t 
                               JOIN Note_Tags nt ON t.tag_id = nt.tag_id 
                               WHERE nt.note_id = ?");
    $stmt->execute([$note_id]);
    $note_tags = $stmt->fetchAll();
  }
} catch (PDOException $e) {
  echo "Error fetching note: " . $e->getMessage();
  exit;
}

// Handle note update
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_note'])) {
  $new_title = sanitizeInput($_POST['title']);
  $new_content = sanitizeInput($_POST['content']);

  if (!empty($new_title) && !empty($new_content)) {
    try {
      $stmt = $pdo->prepare("UPDATE Notes SET title = ?, content = ? WHERE note_id = ? AND user_id = ?");
      $stmt->execute([$new_title, $new_content, $note_id, $user_id]);

      // Redirect to note_details.php
      header("Location: note_details.php?note_id=$note_id");
      exit;
    } catch (PDOException $e) {
      echo "Error updating note: " . $e->getMessage();
    }
  } else {
    echo "Title and content cannot be empty.";
  }
}

// Handle category and tag assignment
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if (isset($_POST['add_category']) && isset($_POST['category_id'])) {
    $category_id = (int)$_POST['category_id'];
    $stmt = $pdo->prepare("INSERT INTO Note_Categories (note_id, category_id) VALUES (?, ?)");
    $stmt->execute([$note_id, $category_id]);
  }

  if (isset($_POST['add_tag']) && isset($_POST['tag_id'])) {
    $tag_id = (int)$_POST['tag_id'];
    $stmt = $pdo->prepare("INSERT INTO Note_Tags (note_id, tag_id) VALUES (?, ?)");
    $stmt->execute([$note_id, $tag_id]);
  }

  if (isset($_POST['comment'])) {
    $comment_content = sanitizeInput($_POST['comment']);
    $stmt = $pdo->prepare("INSERT INTO Comments (note_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$note_id, $user_id, $comment_content]);
  }

  header("Location: note_details.php?note_id=$note_id");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Note Details</title>
  <link rel="stylesheet" href="css/note_details.css">
</head>

<body>
  <?php include '../templates/header.php'; ?>
  <?php include '../templates/navbar.php'; ?>

  <div class="container">
    <?php if ($note) : ?>
      <h2>Edit Note</h2>
      <form action="note_details.php?note_id=<?php echo $note_id; ?>" method="post">
        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($note['title']); ?>" required>
        </div>
        <div class="form-group">
          <label for="content">Content</label>
          <textarea name="content" class="form-control" required><?php echo htmlspecialchars($note['content']); ?></textarea>
        </div>
        <button type="submit" name="update_note" class="btn btn-primary">Save Changes</button>
      </form>

      <h3>Categories</h3>
      <ul>
        <?php foreach ($note_categories as $category) : ?>
          <li><?php echo htmlspecialchars($category['name']); ?></li>
        <?php endforeach; ?>
      </ul>
      <form action="note_details.php?note_id=<?php echo $note_id; ?>" method="post">
        <select name="category_id">
          <?php foreach ($categories as $category) : ?>
            <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" name="add_category">Add Category</button>
      </form>

      <h3>Tags</h3>
      <ul>
        <?php foreach ($note_tags as $tag) : ?>
          <li><?php echo htmlspecialchars($tag['name']); ?></li>
        <?php endforeach; ?>
      </ul>
      <form action="note_details.php?note_id=<?php echo $note_id; ?>" method="post">
        <select name="tag_id">
          <?php foreach ($tags as $tag) : ?>
            <option value="<?php echo $tag['tag_id']; ?>"><?php echo htmlspecialchars($tag['name']); ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" name="add_tag">Add Tag</button>
      </form>

      <h3>Comments</h3>
      <ul>
        <?php foreach ($comments as $comment) : ?>
          <li>
            <?php echo htmlspecialchars($comment['content']); ?>
            <a href="delete_comment.php?id=<?php echo $comment['comment_id']; ?>&note_id=<?php echo $note_id; ?>" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
          </li>
        <?php endforeach; ?>
      </ul>

      <form action="note_details.php?note_id=<?php echo $note_id; ?>" method="post">
        <textarea name="comment" placeholder="Add a comment" required></textarea>
        <button type="submit">Add Comment</button>
      </form>
    <?php else : ?>
      <p>Note not found or you do not have permission to view this note.</p>
    <?php endif; ?>
  </div>

  <?php include '../templates/footer.php'; ?>
</body>

</html>