<?php
session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "safepaws");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get login form values
$email = $_POST['email'] ?? '';
$pass = $_POST['password'] ?? '';

// Check if email and password are provided
if (!empty($email) && !empty($pass)) {
    $stmt = $conn->prepare("SELECT pass FROM citizen_login WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_pass);
        $stmt->fetch();

       
            $_SESSION['email'] = $email;
        
            // Redirect to the protected dashboard
            header("Location: citizen_dashboard.php");
           //$_SESSION['email'] = $email;
            //echo "Session set for: " . $_SESSION['email'];
            exit();
       
    } else {
        echo "<script>alert('Email not found! Please sign up.'); window.location.href='../citizen_signup.html';</script>";
    }
    $stmt->close();
} else {
    echo "<script>alert('Both fields are required.'); window.location.href='../citizen_login.html';</script>";
}

$conn->close();
?>
