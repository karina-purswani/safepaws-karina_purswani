<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "safepaws");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get input
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!empty($username) && !empty($password)) {
    $stmt = $conn->prepare("SELECT password FROM admin_login WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();

        // If password matches
       if ($password === $db_password) {
            // Store session variables
            $_SESSION['admin_loggedin'] = true;
            $_SESSION['admin_username'] = $username;

            header("Location: admin_dashboard.php");
            exit();
        }
        else {
            echo "<script>alert('Incorrect Password!'); window.location.href='../admin_login.html';</script>";
            }
        } else {
        echo "<script>alert('Admin not found!'); window.location.href='../admin_login.html';</script>";
        }

    $stmt->close();
} else {
    echo "<script>alert('Please enter username and password!'); window.location.href='../admin_login.html';</script>";
}

$conn->close();
?>
