<?php
require_once "db_connect.php";

$sql = "SELECT status, COUNT(*) AS count FROM complaints GROUP BY status";
$result = $conn->query($sql);

$stats = [
    "Pending" => 0,
    "Approved" => 0,
    "In Progress" => 0,
    "Resolved" => 0,
    "Rejected" => 0
];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $status = $row['status'];
        if (isset($stats[$status])) {
            $stats[$status] = (int)$row['count'];
        }
    }
}

$total = array_sum($stats);

echo json_encode([
    "stats" => $stats,
    "total" => $total
]);

$conn->close();
?>
