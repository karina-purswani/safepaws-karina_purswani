<?php
session_start();
$conn = new mysqli("localhost", "root", "", "safepaws");

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if citizen_email exists in session
if (isset($_SESSION['citizen_email'])) {
    $email = $_SESSION['citizen_email'];
    // Fetch complaints for logged-in citizen
    $query = "SELECT * FROM complaints WHERE citizen_email='$email' ORDER BY created_at DESC";
} else {
    // Admin or guest — fetch all complaints
    $query = "SELECT * FROM complaints ORDER BY created_at DESC";
}

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "
        <div class='border rounded-lg p-4 bg-slate-50'>
            <p><b>Name:</b> {$row['citizen_name']}</p>
            <p><b>Area:</b> {$row['area']}</p>
            <p><b>Status:</b> {$row['status']}</p>
            <p><b>Complaint:</b> {$row['description']}</p>
            <small class='text-slate-500'>Filed on: {$row['created_at']}</small>
        </div>";
    }
} else {
    echo "<p class='text-slate-500'>No complaints filed yet.</p>";
}

$conn->close();
?>
