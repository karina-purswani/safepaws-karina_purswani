<?php
$conn = new mysqli("localhost", "root", "", "safepaws");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT DISTINCT dog_area FROM main_dogs ORDER BY dog_area ASC");
$areas = [];

if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $areas[] = htmlspecialchars($row['dog_area']);
    }
}

header('Content-Type: application/json');
echo json_encode($areas);
$conn->close();
?>
