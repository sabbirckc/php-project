<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is logged in and is an author
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "author") {
    header("Location: login.php");  // Redirect to login if not logged in as author
    exit();
}

$message = "";  // To display success or error messages

// Handle form submission for adding a post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $category_id = $_POST["category"];
    $author_id = $_SESSION["user_id"];
    
    // Handle image file upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $image_name = $_FILES["image"]["name"];
        $image_tmp_name = $_FILES["image"]["tmp_name"];
        $image_size = $_FILES["image"]["size"];
        $image_type = pathinfo($image_name, PATHINFO_EXTENSION);
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        // Validate the file type
        if (!in_array($image_type, $allowed_types)) {
            $message = "<p style='color:red;'>Only image files (jpg, jpeg, png, gif) are allowed.</p>";
        } else {
            // Set the target directory for uploading
            $image_target = "uploads/" . uniqid() . "." . $image_type;
            if (move_uploaded_file($image_tmp_name, $image_target)) {
                // Insert the post into the database
                $sql = "INSERT INTO posts (title, content, author_id, category_id, image) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssiss", $title, $content, $author_id, $category_id, $image_target);

                if ($stmt->execute()) {
                    $message = "<p style='color:green;'>Post added successfully!</p>";
                } else {
                    $message = "<p style='color:red;'>Error: " . $stmt->error . "</p>";
                }

                $stmt->close();
            } else {
                $message = "<p style='color:red;'>Error uploading the image.</p>";
            }
        }
    } else {
        $message = "<p style='color:red;'>Please select an image file.</p>";
    }
}

// Fetch categories for the dropdown menu
$sql = "SELECT id, name FROM categories";
$result = $conn->query($sql);
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        input, textarea, select, button {
            margin-bottom: 10px;
            display: block;
            width: 100%;
            padding: 8px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .message {
            font-size: 16px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create New Post</h2>
    <?php echo $message; // Display success or error message ?>

    <!-- Form to add post -->
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Post Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Post Content:</label>
        <textarea id="content" name="content" rows="6" required></textarea>

        <label for="category">Select Category:</label>
        <select name="category" id="category" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <button type="submit">Add Post</button>
    </form>

    <hr>
    <a href="dashboard.php">Go to Dashboard</a> | <a href="logout.php">Logout</a>
</div>

</body>
</html>

<?php
// Close the database connection after all operations
$conn->close();
?>
