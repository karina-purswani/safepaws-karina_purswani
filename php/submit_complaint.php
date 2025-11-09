<?php
session_start();
$conn = new mysqli("localhost", "root", "", "safepaws");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure logged-in citizen
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Session expired. Please log in again.'); window.location.href='../citizen_login.html';</script>";
    exit();
}

$email = $_SESSION['email'];
$citizen_name = $_POST['citizenName'];
$description = $_POST['name'];
$area = $_POST['area'];
$vaccinated = $_POST['vaccinated'];

// Handle file upload
$targetDir = "../uploads/";
if (!file_exists($targetDir)) mkdir($targetDir);

$fileName = basename($_FILES["dogImage"]["name"]);
$targetFile = $targetDir . time() . "_" . $fileName;

if (move_uploaded_file($_FILES["dogImage"]["tmp_name"], $targetFile)) {
    $imagePath = $targetFile;
} else {
    $imagePath = NULL;
}

// Insert complaint
$stmt = $conn->prepare("INSERT INTO complaints (citizen_email, citizen_name, description, area, image_path, vaccinated) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $email, $citizen_name, $description, $area, $imagePath, $vaccinated);

if ($stmt->execute()) {
    echo "<script>alert('Complaint submitted successfully!'); window.location.href='../php/citizen_dashboard.php';</script>";
} else {
    echo "<script>alert('Error submitting complaint.'); window.location.href='../php/citizen_dashboard.php';</script>";
}

$stmt->close();
$conn->close();
?>
