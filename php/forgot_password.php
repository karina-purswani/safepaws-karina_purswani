<?php
// php/forgot_password.php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Basic input validation
if (empty($_POST['email'])) {
    echo "<script>alert('Please provide an email address.'); history.back();</script>";
    exit;
}

$email = trim($_POST['email']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); history.back();</script>";
    exit;
}

// --- Database connection ---
$host = "localhost";
$user = "root";
$pass = "";
$db   = "safepaws";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    // In production show user-friendly message and log the real error privately
    error_log("DB connection error: " . $conn->connect_error);
    echo "<script>alert('Server error. Please try again later.'); history.back();</script>";
    exit;
}

// --- Lookup user by email ---
$stmt = $conn->prepare("SELECT pass FROM citizen_login WHERE email = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo "<script>alert('Server error.'); history.back();</script>";
    $conn->close();
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    // Do not reveal whether email exists in production; for dev we show message
    echo "<script>alert('Email not found.'); history.back();</script>";
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->bind_result($password);
$stmt->fetch();
$stmt->close();
$conn->close();

// --- Build the message (as you requested) ---
$subject = "Your Safe Paws Password";
$message .= "As requested, here is your Safe Paws account password:\n\n";
$message .= $password . "\n\n";
$message .= "Please keep it safe. If you did not request this, please contact our support immediately.\n\n";
$message .= "— Safe Paws Team\n";

// Wordwrap to 70 chars per line (recommended for mail())
$message = wordwrap($message, 70);

// Headers
$fromEmail = "noreply@safepaws.com"; // change to a valid sending address or domain-managed address
$fromName  = "Safe Paws";
$headers   = [];
$headers[] = "From: " . $fromName . " <" . $fromEmail . ">";
$headers[] = "Reply-To: " . $fromEmail;
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";
$headers   = implode("\r\n", $headers);

// Try to send the email
$sent = mail($email, $subject, $message, $headers);

if ($sent) {
    // Redirect back to login with a success message
    echo "<script>alert('Password sent to your email address.'); window.location.href='../HTML_files/citizen_login.html';</script>";
} else {
    error_log("mail() failed while sending to: $email");
    echo "<script>alert('Failed to send email. Please try again later.'); history.back();</script>";
}
?>

