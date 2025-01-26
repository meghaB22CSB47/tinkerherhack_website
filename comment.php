<?php
session_start();
include('connect.php');

// Check if the user is logged in
if (!isset($_SESSION['Userid'])) {
    echo "You must be logged in to comment.";
    exit();
}

// Handle commenting on a post
if (isset($_POST['post_id']) && isset($_POST['comment'])) {
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    // Insert the comment into the comments table
    $query = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = $db_connection->prepare($query);
    $stmt->bind_param("iss", $post_id, $user_id, $comment);
    $stmt->execute();
    $stmt->close();

    header("Location: timeline.php"); // Redirect back to timeline
    exit();
}
?>
