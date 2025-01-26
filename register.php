<?php 
include 'connect.php';
session_start();

// SignUp Logic
if (isset($_POST['signUp'])) {
    // Get form data
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $Userid = $_POST['Userid'];
    $password = md5($_POST['password']);
    $phone = $_POST['phone'];
    $bio = $_POST['bio'];
    $job_description = $_POST['job_description'];

    // Check if User ID exists
    $checkEmail = "SELECT * FROM users WHERE Userid='$Userid'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        echo "Username Already Exists!";
    } else {
        // Insert new user
        $insertQuery = "INSERT INTO users (firstName, lastName, email, Userid, password, phone, bio, job_description)
                        VALUES ('$firstName', '$lastName', '$email', '$Userid', '$password', '$phone', '$bio', '$job_description')";
        if ($conn->query($insertQuery) === TRUE) {
            header("location:index.php?status=registered");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// SignIn Logic
if (isset($_POST['signIn'])) {
    $password = md5($_POST['password']);
    $Userid = $_POST['Userid'];
    $select = "SELECT * FROM users WHERE Userid = '$Userid' AND password = '$password'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $_SESSION['Userid'] = $Userid;
        $_SESSION['user_name'] = $row['firstName'] . ' ' . $row['lastName'];
        header('location:homepage.php');
    } else {
        header('location:error.php');
    }
}
?>
