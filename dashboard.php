<?php
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

<h2>Welcome, <?php echo $_SESSION["name"]; ?>!</h2>
<p>Your role is: <?php echo $_SESSION["role"]; ?></p>

<!-- Logout Button -->
<form action="logout.php" method="POST">
    <button type="submit" class="button">Logout</button>
</form>

</body>
</html>