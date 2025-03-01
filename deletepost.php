<?php
session_start();
include 'db.php';

// Check if user is logged in and is an author
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "author") {
    header("Location: login.php");
    exit();
}


$author_id = $_SESSION["user_id"];

// Check if post ID is provided
if (isset($_GET["id"])) {
    $post_id = $_GET["id"];

    // Get the image path to delete it from the folder
    $sql = "SELECT image FROM posts WHERE id = ? AND author_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $author_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    if ($post) {
        // Delete the image file if it exists
        if ($post['image'] && file_exists($post['image'])) {
            unlink($post['image']);
        }

        // Delete post from database
        $sql = "DELETE FROM posts WHERE id = ? AND author_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $post_id, $author_id);
        
        if ($stmt->execute()) {
            header("Location: displaypost.php?success=Post Deleted Successfully");
            exit();
        } else {
            echo "<p style='color:red;'>Error deleting post.</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color:red;'>Post not found or you don't have permission.</p>";
    }
}

$conn->close();
?>