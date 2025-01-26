<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Userid'])) {
    header('Location: index.php'); // Redirect to login if not logged in
    exit();
}

include('connect.php'); // Assuming you have a separate DB connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['Userid'];  // The logged-in user's ID
    $receiver_id = $_POST['receiver_id'];  // The user the message is being sent to
    $message_text = $_POST['message_text'];  // The message content

    // Insert the message into the database
    $query = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message_text);

    if ($stmt->execute()) {
        header('Location: message.php');  // Redirect to message page after sending
        exit();
    } else {
        echo "Error sending message.";
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
    <title>Send Message</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <div class="left">
            <a href="index.php">Home</a>
        </div>
        <div class="center">
            <h2>Send Message</h2>
        </div>
        <div class="right">
            <a href="profile_page.php">Profile</a>
        </div>
    </div>

    <div class="send-message-container">
        <form action="send_message.php" method="POST">
            <label for="receiver_id">To (Receiver's User ID):</label>
            <input type="number" name="receiver_id" required>
            <br>
            <label for="message_text">Message:</label>
            <textarea name="message_text" rows="5" required></textarea>
            <br>
            <button type="submit">Send Message</button>
        </form>
    </div>
</body>
</html>
