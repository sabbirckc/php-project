<?php
session_start();
include 'db.php';

// Check if user is logged in and is an author
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "author") {
    header("Location: login.php");
    exit();
}

$author_id = $_SESSION["user_id"];
$message = "";

// Fetch post data
if (isset($_GET["id"])) {
    $post_id = $_GET["id"];
    $sql = "SELECT * FROM posts WHERE id = ? AND author_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $author_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST["post_id"];
    $title = $_POST["title"];
    $content = $_POST["content"];
    $category_id = $_POST["category"];
    
    // Update post in database
    $sql = "UPDATE posts SET title = ?, content = ?, category_id = ? WHERE id = ? AND author_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $title, $content, $category_id, $post_id, $author_id);

    if ($stmt->execute()) {
        $message = "<p style='color:green;'>Post updated successfully!</p>";
    } else {
        $message = "<p style='color:red;'>Error updating post.</p>";
    }

    $stmt->close();
}

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
</head>
<body>

<h2>Edit Post</h2>
<?php echo $message; ?>

<form method="POST">
    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">

    <label>Title:</label>
    <input type="text" name="title" value="<?php echo $post['title']; ?>" required>

    <label>Content:</label>
    <textarea name="content" rows="5" required><?php echo $post['content']; ?></textarea>

    <label>Category:</label>
    <select name="category">
        <?php while ($row = $categories->fetch_assoc()): ?>
            <option value="<?= $row['id']; ?>" <?= ($row['id'] == $post['category_id']) ? "selected" : "" ?>>
                <?= $row['name']; ?>
            </option>
        <?php endwhile; ?>
    </select>

    <button type="submit">Update Post</button>
</form>
<h1>fdfd</h1>
</body>
</html>