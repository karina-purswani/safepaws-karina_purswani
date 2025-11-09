<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "safepaws");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get values from signup form
$name = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$pass = $_POST['password'] ?? '';

// Validate input
if (!empty($name) && !empty($email) && !empty($pass)) {
    // Hash the password before saving
  

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO citizen_login (email, pass) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $pass);

    if ($stmt->execute()) {
        echo "<script>alert('Sign Up Successful! You can now log in.'); window.location.href='C:/xampp/htdocs/safepaws/citizen_login.html';</script>";
    } else {
        echo "<script>alert('Error: Email already exists or invalid data.'); window.location.href='../citizen_signup.html';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('All fields are required.'); window.location.href='../citizen_signup.html';</script>";
}

$conn->close();
?>
