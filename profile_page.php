<?php
session_start();
include('connect.php');

// Check if the user is logged in
if (!isset($_SESSION['Userid'])) {
    header("Location:index.php");
    exit();
}

$user_id = $_SESSION['Userid'];

// Fetch user details from the database
$query = "SELECT * FROM users WHERE Userid = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $bio = $_POST['bio'];
    $job_description = $_POST['job_description'];

    $update_query = "UPDATE users SET firstName='$first_name', lastName='$last_name', email='$email', phone='$phone', bio='$bio', job_description='$job_description' WHERE Userid='$user_id'";

    if (mysqli_query($conn, $update_query)) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="navbar">
        <div class="left">
            <a href="index.php">Home</a>
        </div>
        <div class="center">
            <h2>Your Profile</h2>
        </div>
    </div>

    <div class="container">
        <form method="POST">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" value="<?php echo $user['firstName']; ?>" required><br>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" value="<?php echo $user['lastName']; ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br>

            <label for="phone">Phone:</label>
            <input type="text" name="phone" value="<?php echo $user['phone']; ?>" placeholder="Enter your phone number" required><br>

            <label for="bio">Bio:</label>
            <textarea name="bio" placeholder="Tell us about yourself" required><?php echo $user['bio']; ?></textarea><br>

            <label for="job_description">Job Description:</label>
            <textarea name="job_description" placeholder="Enter your job description" required><?php echo $user['job_description']; ?></textarea><br>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
