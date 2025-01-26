<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['Userid'])) {
    header('Location:index.php'); // Redirect to login if not logged in
    exit();
}

if (isset($_GET['follower_id']) && isset($_GET['action'])) {
    $follower_id = $_GET['follower_id'];
    $action = $_GET['action'];
    $user_id = $_SESSION['Userid'];

    // Database connection
    $mysqli = new mysqli('localhost', 'root', '', 'user_db'); // Update with your credentials

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Handle the action (accept/reject)
    if ($action == 'accept') {
        $update_sql = "UPDATE friendships SET status = 'accepted' WHERE follower_id = ? AND followed_id = ?";
    } elseif ($action == 'reject') {
        $update_sql = "UPDATE friendships SET status = 'rejected' WHERE follower_id = ? AND followed_id = ?";
    }

    // Prepare and execute the query
    $stmt = $mysqli->prepare($update_sql);
    $stmt->bind_param("ii", $follower_id, $user_id);
    $stmt->execute();

    // Redirect back to homepage
    header('Location: homepage.php');
    exit();
}
?>
