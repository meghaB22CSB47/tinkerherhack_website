<?php
session_start();
include('connect.php');

// Check if the user is logged in
if (!isset($_SESSION['Userid'])) {
    // Redirect to login or registration page if not logged in
    header("Location: register.php");
    exit();
}

// Fetch Userid from session
$Userid = $_SESSION['Userid'];

// Verify the Userid exists in the database
$user_check_query = "SELECT * FROM users WHERE Userid = ?";
$stmt = $conn->prepare($user_check_query);
$stmt->bind_param("s", $Userid); // Assuming Userid is varchar(50)
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Error: User does not exist in the database.");
}
$stmt->close();

// Handle Photo/Video Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $target_dir = "uploads/";

    // Ensure the uploads directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["media"]["name"]);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file type (allowing images and videos only)
    if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi'])) {
        if (move_uploaded_file($_FILES["media"]["tmp_name"], $target_file)) {
            // Insert post into the database
            $query = "INSERT INTO posts (user_id, title, content, media_url) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $Userid, $title, $content, $target_file);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error: Unable to upload your file.";
        }
    } else {
        echo "Error: Only images and videos are allowed.";
    }
}

// Fetch posts from the database
$query = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeline</title>
    <link rel="stylesheet" href="timeline.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="navbar">
        <div class="left">
            <a href="index.php">Home</a>
        </div>
        <div class="center">
            <h2>Timeline</h2>
        </div>
        <div class="right">
            <a href="profile_page.php">Profile</a>
        </div>
    </div>

    <div class="container">
        <h3>Share Your Moments</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" required><br>
            <textarea name="content" placeholder="Share your moment..." required></textarea><br>
            <input type="file" name="media" accept="image/*,video/*" required><br>
            <button type="submit">Post</button>
        </form>

        <h3>Posts</h3>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                    <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    <?php if ($row['media_url']): ?>
                        <?php if (in_array(pathinfo($row['media_url'], PATHINFO_EXTENSION), ['mp4', 'avi'])): ?>
                            <video controls style="max-width: 100%;">
                                <source src="<?php echo htmlspecialchars($row['media_url']); ?>" type="video/<?php echo pathinfo($row['media_url'], PATHINFO_EXTENSION); ?>">
                                Your browser does not support the video tag.
                            </video>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($row['media_url']); ?>" alt="Post Media" style="max-width: 100%;"><br>
                        <?php endif; ?>
                    <?php endif; ?>
                    <span class="post-date"><?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts found. Share your first post!</p>
        <?php endif; ?>
    </div>
</body>
</html>
s