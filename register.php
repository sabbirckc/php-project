<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blogpostdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));  // Sanitize name
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL); // Sanitize email
    $password = $_POST["password"]; // Store password as entered
    $role = $_POST["role"];

    // Validate role input to prevent SQL injection
    $allowed_roles = ['admin', 'author', 'subscriber'];
    if (!in_array($role, $allowed_roles)) {
        die("Invalid role selection.");
    }

    // Insert user into database
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>The registration done successfully</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        input, select, button {
            margin-bottom: 10px;
            display: block;
        }
    </style>
</head>
<body>

<h2>User Registration</h2>
<form method="POST">
    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Role:</label>
    <select name="role">
        <option value="subscriber">Subscriber</option>
        <option value="author">Author</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit">Register</button>
</form>

</body>
</html>