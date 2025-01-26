<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Userid'])) {
    header('Location: index.php'); // Redirect to login if not logged in
    exit();
}

include('connect.php'); // Assuming you have a separate DB connection file

$userid = $_SESSION['Userid']; // Logged-in user's ID

// Fetch all messages sent and received by the logged-in user
$query = "SELECT * FROM messages 
          WHERE sender_id = ? OR receiver_id = ?
          ORDER BY sent_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $userid, $userid);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <div class="left">
            <a href="index.php">Home</a>
        </div>
        <div class="center">
            <h2>Messages</h2>
        </div>
        <div class="right">
            <a href="profile_page.php">Profile</a>
        </div>
    </div>

    <div class="message-container">
        <h3>Your Messages</h3>
        <ul class="messages-list">
            <?php while ($message = $result->fetch_assoc()) : ?>
                <li class="message-item">
                    <strong>
                        <?php
                        // Fetch sender's name
                        $sender_id = $message['sender_id'];
                        $sender_query = "SELECT user_name FROM users WHERE Userid = ?";
                        $sender_stmt = $conn->prepare($sender_query);
                        $sender_stmt->bind_param("i", $sender_id);
                        $sender_stmt->execute();
                        $sender_result = $sender_stmt->get_result();
                        $sender_name = $sender_result->fetch_assoc()['user_name'];
                        echo $sender_name;
                        ?>
                    </strong>: <?php echo htmlspecialchars($message['message_text']); ?>
                    <br><small><?php echo $message['sent_at']; ?></small>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
