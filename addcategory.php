<?php
session_start();
include 'db.php';  // Include the database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");  // Redirect to login if not logged in as admin
    exit();
}

$message = "";  // To show success or error messages when adding a category

// Handle form submission for adding category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST["category_name"];

    // Check if category name is not empty
    if (!empty($category_name)) {
        // Insert the new category into the database
        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            $message = "<p style='color:green;'>Category added successfully!</p>";
        } else {
            $message = "<p style='color:red;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        $message = "<p style='color:red;'>Category name cannot be empty.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        input, button {
            margin-bottom: 10px;
            display: block;
        }
        .container {
            max-width: 500px;
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
    <h2>Add New Category</h2>

    <?php echo $message; // Display success or error message ?>

    <!-- Form to add category -->
    <form method="POST">
        <label for="category_name">Category Name:</label>
        <input type="text" id="category_name" name="category_name" required>

        <button type="submit">Add Category</button>
    </form>

    <hr>
    <a href="dashboard.php">Go to Dashboard</a> | <a href="logout.php">Logout</a>
</div>

</body>
</html>