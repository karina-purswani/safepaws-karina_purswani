<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: citizen_login.html');
    exit();
}

$email = $_SESSION['email'];

// Database connection parameters (adjust if needed)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "safepaws";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare delete statement to avoid SQL injection
$stmt = $conn->prepare("DELETE FROM citizen_login WHERE email = ?");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    // Account deleted successfully, log user out
    $stmt->close();
    $conn->close();

    session_unset();
    session_destroy();

    // Redirect to login or home page after removal
    header("Location:../citizen_login.html");
    exit();
} else {
    $stmt->close();
    $conn->close();
    // Something went wrong
    echo "Error removing account. Please try again later.";
}
