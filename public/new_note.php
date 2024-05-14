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
$title = $content = "";
$title_err = $content_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate title
  if (empty(trim($_POST["title"]))) {
    $title_err = "Please enter a title.";
  } else {
    $title = sanitizeInput($_POST["title"]);
  }

  // Validate content
  if (empty(trim($_POST["content"]))) {
    $content_err = "Please enter some content.";
  } else {
    $content = sanitizeInput($_POST["content"]);
  }

  // Check input errors before inserting in database
  if (empty($title_err) && empty($content_err)) {
    $sql = "INSERT INTO Notes (user_id, title, content) VALUES (?, ?, ?)";

    if ($stmt = $pdo->prepare($sql)) {
      // Bind variables to the prepared statement as parameters
      $stmt->bindParam(1, $param_user_id, PDO::PARAM_INT);
      $stmt->bindParam(2, $param_title, PDO::PARAM_STR);
      $stmt->bindParam(3, $param_content, PDO::PARAM_STR);

      // Set parameters
      $param_user_id = $_SESSION['user_id'];
      $param_title = $title;
      $param_content = $content;

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Redirect to dashboard if note was added successfully
        header("location: dashboard.php");
        exit();
      } else {
        echo "Something went wrong. Please try again later.";
      }

      // Close statement
      unset($stmt);
    }
  }

  // Close connection
  unset($pdo);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create New Note</title>
  <link rel="stylesheet" href="css/new_note.css">
</head>

<body>
  <?php include '../templates/header.php'; ?>
  <?php include '../templates/navbar.php'; ?>

  <div class="container">
    <h2>Create New Note</h2>
    <p>Please fill in this form to create a new note.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
        <span class="help-block"><?php echo $title_err; ?></span>
      </div>
      <div class="form-group">
        <label>Content</label>
        <textarea name="content" class="form-control"><?php echo $content; ?></textarea>
        <span class="help-block"><?php echo $content_err; ?></span>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="reset" class="btn btn-secondary">Reset</button>
      </div>
    </form>
  </div>

  <?php include '../templates/footer.php'; ?>
</body>

</html>