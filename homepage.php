<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Userid'])) {
    header('Location:index.php'); // Redirect to login if not logged in
    exit();
}

include('connect.php'); // Assuming you have a separate DB connection file

// Fetch the user name from session
$user_id = $_SESSION['Userid'];
$user_name = $_SESSION['user_name'];

// Query to fetch the latest 5 messages
$query = "SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ? ORDER BY sent_at DESC LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$messages_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="left">
            <a href="index.php">Home</a>
        </div>
        <div class="center">
            <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
        </div>
        <div class="right">
            <a href="profile_page.php"><i class="fas fa-user-circle"></i> Profile</a>
            <a href="messages.php"><i class="fas fa-comment"></i> Messages</a> <!-- Link to the messages page -->
        </div>
    </div>

    <!-- Recent Messages Section -->
    <div class="recent-messages-container">
        <h3>Recent Messages</h3>
        <ul class="messages-list">
            <?php while ($message = $messages_result->fetch_assoc()) : ?>
                <li class="message-item">
                    <strong>
                        <?php
                        // Fetch sender's name
                        $sender_id = $message['sender_id'];
                        $sender_query = "SELECT username FROM users WHERE Userid = ?";
                        $sender_stmt = $conn->prepare($sender_query);
                        $sender_stmt->bind_param("i", $sender_id);
                        $sender_stmt->execute();
                        $sender_result = $sender_stmt->get_result();
                        $sender_name = $sender_result->fetch_assoc()['firstName'];
                        echo htmlspecialchars($sender_name);
                        ?>
                    </strong>: <?php echo htmlspecialchars($message['message_text']); ?>
                    <br><small><?php echo $message['sent_at']; ?></small>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- Timeline Section -->
    <div class="container">
        <h3>Timeline</h3>
        <p>Share your moments, photos, and videos here.</p>

        <!-- Button to go to the timeline page -->
        <a href="timeline.php">
            <button class="btn">Go to Timeline</button>
        </a>
    </div>

    <!-- Search Bar Section -->
    <div class="search-bar-container">
        <form method="GET" action="search.php">
            <input type="text" name="search" placeholder="Search for friends..." required>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</body>
</html>

<?php
// Close the statement and connection
$stmt->close();
$conn->close();
?>
