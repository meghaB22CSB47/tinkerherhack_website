<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['Userid'])) {
    header('Location: index.php');
    exit();
}

$current_user = $_SESSION['Userid'];

// Fetch notifications
$query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $current_user);
$stmt->execute();
$result = $stmt->get_result();

// Mark notifications as read
$update_query = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param('s', $current_user);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="notifications.css">
</head>
<body>
    <h2>Notifications</h2>
    <ul>
        <?php while ($notification = $result->fetch_assoc()): ?>
            <li class="<?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                <?php echo htmlspecialchars($notification['message']); ?>
                <small><?php echo date('F j, Y, g:i a', strtotime($notification['created_at'])); ?></small>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
