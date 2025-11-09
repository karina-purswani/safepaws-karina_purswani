<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "safepaws");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Check if all required fields are present
if (isset($_POST['dog_id'], $_POST['vaccination_status'], $_POST['vaccination_date'])) {
    $dog_id = intval($_POST['dog_id']);
    $status = trim($_POST['vaccination_status']);
    $date = trim($_POST['vaccination_date']);

    // ✅ Step 1: Verify if the dog exists in main_dogs
    $check_sql = "SELECT dog_id FROM main_dogs WHERE dog_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $dog_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo "error: Dog not found.";
        $check_stmt->close();
        $conn->close();
        exit;
    }
    $check_stmt->close();

    // ✅ Step 2: Always insert a new vaccination record (keep full history)
    $insert_sql = "INSERT INTO vaccination_records (dog_id, vaccination_status, vaccination_date)
                   VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iss", $dog_id, $status, $date);

    if (!$insert_stmt->execute()) {
        echo "error: " . $insert_stmt->error;
        $insert_stmt->close();
        $conn->close();
        exit;
    }
    $insert_stmt->close();

    // ✅ Step 3: Update main_dogs with the latest vaccination status and date
    $update_sql = "UPDATE main_dogs 
                   SET vaccination_status = ?, date = ? 
                   WHERE dog_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $status, $date, $dog_id);

    if ($update_stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $update_stmt->error;
    }

    $update_stmt->close();

} else {
    echo "invalid";
}

$conn->close();
?>
