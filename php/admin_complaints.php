<?php
$conn = new mysqli("localhost", "root", "", "safepaws");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM complaints ORDER BY created_at DESC");

echo "<h2 class='text-xl font-bold mb-4'>Manage Complaints</h2>";

if ($result->num_rows > 0) {
    echo "<div class='grid gap-4'>";
    while ($row = $result->fetch_assoc()) {
        $id = $row['complaint_id'];
        $status = $row['status'];
        $imagePath = $row['image_path']; // <-- get image path

        echo "
    <div class='border p-4 rounded-lg bg-slate-50'>
    <p><b>Citizen Name:</b> {$row['citizen_name']}</p>
    <p><b>Citizen Email:</b> {$row['citizen_email']}</p>
    <p><b>Area:</b> {$row['area']}</p>
    <p><b>Description:</b> {$row['description']}</p>
    ";


        // Show image if uploaded
        if (!empty($imagePath)) {
            echo "<img src='{$imagePath}' alt='Complaint Image' class='mt-2 w-48 rounded'>";
        }

        echo "
            <p><b>Status:</b> <span id='status-$id' class='font-semibold text-blue-700'>$status</span></p>
            <div class='mt-3 space-x-2'>
        ";

        // Show buttons based on current status
        if ($status == 'Pending') {
            echo "
                <button class='bg-green-600 text-white px-3 py-1 rounded' onclick='updateStatus($id, \"Approved\")'>Approve</button>
                <button class='bg-red-500 text-white px-3 py-1 rounded' onclick='updateStatus($id, \"Rejected\")'>Reject</button>
            ";
        } elseif ($status == 'Approved') {
            echo "
                <button class='bg-yellow-500 text-white px-3 py-1 rounded' onclick='updateStatus($id, \"In Progress\")'>In Progress</button>
                <button class='bg-blue-600 text-white px-3 py-1 rounded' onclick='updateStatus($id, \"Resolved\")'>Resolved</button>
            ";
        } elseif ($status == 'In Progress') {
            echo "
                <button class='bg-blue-600 text-white px-3 py-1 rounded' onclick='updateStatus($id, \"Resolved\")'>Resolved</button>
            ";
        }

        echo "</div></div>";
    }
    echo "</div>";
} else {
    echo "<p>No complaints found.</p>";
}
$conn->close();
?>

<script>
function updateStatus(id, newStatus) {
    fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'complaint_id=' + id + '&status=' + encodeURIComponent(newStatus)
    })
    .then(res => res.text())
    .then(response => {
        if(response === 'success') {
            // Reload complaints dynamically
            fetch("admin_complaints.php")
                .then(resp => resp.text())
                .then(html => document.getElementById("panel").innerHTML = html);
        } else {
            alert('Failed to update status.');
        }
    })
    .catch(err => console.error(err));
}
</script>
