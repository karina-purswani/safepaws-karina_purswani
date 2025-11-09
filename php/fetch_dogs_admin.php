<?php
$conn = new mysqli("localhost", "root", "", "safepaws");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "SELECT * FROM main_dogs ORDER BY dog_id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dog_id = $row['dog_id'];
        $dog_area = htmlspecialchars($row['dog_area']);
        $vaccination_status = htmlspecialchars($row['vaccination_status']);
        $dog_image = !empty($row['dog_image']) ? htmlspecialchars($row['dog_image']) : 'default_dog.jpg';
        $date = htmlspecialchars($row['date']);
        $imagePath = "../uploads/" . $dog_image;

        echo "
        <div class='flex bg-slate-50 rounded-xl shadow p-4 gap-4 hover:shadow-md transition mb-4'>
            <div class='w-40 h-40 flex-shrink-0 overflow-hidden rounded-lg border'>
                <img src='$imagePath' alt='Dog Image' class='w-full h-full object-cover'>
            </div>

            <div class='flex flex-col justify-between w-full'>
                <div>
                    <p><b>Area:</b> $dog_area</p>
                    <p><b>Vaccination Status:</b> $vaccination_status</p>
                    <p><b>Date Registered:</b> $date</p>
                </div>

                <div class='mt-3'>
                    <b>Vaccination Phases:</b><br>";

        // Fetch all vaccination records for this dog
        $vax_sql = "SELECT vaccination_status, vaccination_date 
                    FROM vaccination_records 
                    WHERE dog_id = ? 
                    ORDER BY vaccination_date ASC";
        $vax_stmt = $conn->prepare($vax_sql);
        $vax_stmt->bind_param("i", $dog_id);
        $vax_stmt->execute();
        $vax_result = $vax_stmt->get_result();

        if ($vax_result && $vax_result->num_rows > 0) {
            while ($vax = $vax_result->fetch_assoc()) {
                $phase = htmlspecialchars($vax['vaccination_status']);
                $vax_date = htmlspecialchars($vax['vaccination_date']);
                echo "<p>• $phase on $vax_date</p>";
            }
        } else {
            echo "<p class='text-slate-500'>No vaccination records found.</p>";
        }

        $vax_stmt->close();

        // Update Vaccination button with theme-preserving disabled style
        $disabled = ($vaccination_status === "Fully Vaccinated") 
            ? "style='pointer-events: none; opacity: 0.6;' class='bg-blue-600 text-white px-4 py-2 rounded update-btn'" 
            : "class='bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded update-btn' onclick='openVaccinationForm($dog_id)'";

        echo "
                </div>

                <div class='mt-4'>
                    <button $disabled>
                        Update Vaccination
                    </button>
                </div>
            </div>
        </div>
        ";
    }
} else {
    echo "<p class='text-center text-slate-500'>No registered dogs found yet.</p>";
}

$conn->close();
?>
