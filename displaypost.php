<?php
session_start();
include 'db.php'; // Include database connection

// Check if the user is logged in and is an author
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "author") {
    header("Location: login.php");
    exit();
}

$author_id = $_SESSION["user_id"];
?>

<!DOCTYPE html>
<html lang="en">
<head>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        img {
            width: 100px;
            height: auto;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
        }
        .update-btn {
            background-color: #28a745;
        }
        .delete-btn {
            background-color: #dc3545;
        }
    </style>
</head>
<body>

<h2>My Posts</h2>

<table>
    <tr>
        <th>Title</th>
        <th>Content</th>
        <th>Category</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>
    <?php
    $sql = "SELECT posts.id, posts.title, posts.content, posts.image, categories.name AS category 
            FROM posts 
            JOIN categories ON posts.category_id = categories.id 
            WHERE posts.author_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $author_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . substr(htmlspecialchars($row['content']), 0, 50) . "...</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td><img src='" . htmlspecialchars($row['image']) . "' alt='Post Image'></td>";
        echo "<td class='action-buttons'>
                <a href='updatepost.php?id=" . $row['id'] . "' class='btn update-btn'>Update</a> 
                <a href='deletepost.php?id=" . $row['id'] . "' class='btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this post?\")'>Delete</a>
              </td>";
        echo "</tr>";
    }

    $stmt->close();
    $conn->close();
    ?>
</table>

</body>
</html>