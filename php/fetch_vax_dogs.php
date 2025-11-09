<?php
$conn = new mysqli("localhost", "root", "", "safepaws");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$area = isset($_GET['area']) ? $_GET['area'] : '';
$area = $conn->real_escape_string($area);

$sql = "SELECT * FROM main_dogs WHERE dog_area='$area' AND vaccination_status='Fully Vaccinated' ORDER BY dog_id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dog_image = !empty($row['dog_image']) ? htmlspecialchars($row['dog_image']) : 'default_dog.jpg';
        $imagePath = "../uploads/" . $dog_image;
        echo "
        <div class='flex gap-4 bg-slate-50 p-4 rounded shadow'>
            <div class='w-32 h-32 overflow-hidden rounded border'>
                <img src='$imagePath' class='w-full h-full object-cover'>
            </div>
            <div>
                <p><b>Area:</b> ".htmlspecialchars($row['dog_area'])."</p>
                <p><b>Status:</b> ".htmlspecialchars($row['vaccination_status'])."</p>
                <p><b>Date Registered:</b> ".htmlspecialchars($row['date'])."</p>
            </div>
        </div>";
    }
} else {
    echo "<p class='text-slate-500'>No vaccinated dogs found in this area.</p>";
}

$conn->close();
?>
