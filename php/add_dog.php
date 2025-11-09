<?php
$conn = new mysqli("localhost", "root", "", "safepaws");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Directory for uploads
$uploadDir = "../uploads/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dog_area = trim($_POST['dog_area']);
    $vaccination_status = trim($_POST['vaccination_status']);
    $date = trim($_POST['date']);

    // Handle image upload
    if (!empty($_FILES['dog_image']['name'])) {
        $fileName = time() . "_" . basename($_FILES["dog_image"]["name"]);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["dog_image"]["tmp_name"], $targetFile)) {
            $dog_image = $fileName;
        } else {
            echo "error_upload";
            exit;
        }
    } else {
        echo "no_image";
        exit;
    }

    // ✅ Step 1: Insert into main_dogs
    $stmt = $conn->prepare("INSERT INTO main_dogs (dog_area, vaccination_status, dog_image, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $dog_area, $vaccination_status, $dog_image, $date);

    if ($stmt->execute()) {
        // Get the inserted dog's ID
        $dog_id = $stmt->insert_id;

        // ✅ Step 2: Also insert into vaccination_records
        $vax_stmt = $conn->prepare("INSERT INTO vaccination_records (dog_id, vaccination_status, vaccination_date) VALUES (?, ?, ?)");
        $vax_stmt->bind_param("iss", $dog_id, $vaccination_status, $date);
        $vax_stmt->execute();
        $vax_stmt->close();

        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
