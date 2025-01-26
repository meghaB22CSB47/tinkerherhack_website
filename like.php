<?php
session_start();
include('connect.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to like a post.";
    exit();
}

// Handle liking a post
if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has already liked this post
    $check_like_query = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
    $check_like_stmt = $db_connection->prepare($check_like_query);
    $check_like_stmt->bind_param("is", $post_id, $user_id);
    $check_like_stmt->execute();
    $check_like_result = $check_like_stmt->get_result();

    if ($check_like_result->num_rows === 0) {
        // User has not liked the post, so insert a like
        $insert_like_query = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
        $insert_like_stmt = $db_connection->prepare($insert_like_query);
        $insert_like_stmt->bind_param("is", $post_id, $user_id);
        $insert_like_stmt->execute();
        echo "Like added!";
    } else {
        echo "You have already liked this post.";
    }
}
?>
