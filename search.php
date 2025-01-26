<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Userid'])) {
    header('Location:index.php'); // Redirect to login if not logged in
    exit();
}

include('connect.php'); // Include your DB connection file

// Get search query from the form submission
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Check if there's a search query
if ($search_query) {
    // Search for users matching the search query
    $query = "SELECT * FROM users WHERE user_name LIKE ? LIMIT 10";
    $stmt = $conn->prepare($query);
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Default case when no search query is given
    $result = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="left">
            <a href="index.php">Home</a>
        </div>
        <div class="center">
            <h2>Search Results</h2>
        </div>
        <div class="right">
            <a href="profile_page.php"><i class="fas fa-user-circle"></i> Profile</a>
        </div>
    </div>

    <!-- Search Results Section -->
    <div class="search-results-container">
        <h3>Users Found</h3>
        <?php if ($result->num_rows > 0) : ?>
            <ul class="user-list">
                <?php while ($user = $result->fetch_assoc()) : ?>
                    <li class="user-item">
                        <a href="profile_page.php?user_id=<?php echo $user['Userid']; ?>">
                            <strong><?php echo htmlspecialchars($user['user_name']); ?></strong>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p>No users found matching your search.</p>
        <?php endif; ?>
    </div>

    <!-- Search Bar Section -->
    <div class="search-bar-container">
        <form method="GET" action="search.php">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search for friends..." required>
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
