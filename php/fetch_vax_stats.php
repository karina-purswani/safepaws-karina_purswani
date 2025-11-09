<?php
include("db_connect.php");
header('Content-Type: application/json');

// --- Vaccination progress counts ---
$progressQuery = "
    SELECT 
        SUM(vaccination_status = 'Vaccination Phase 1 Completed') AS phase1,
        SUM(vaccination_status = 'Vaccination Phase 2 Completed') AS phase2,
        SUM(vaccination_status = 'Fully Vaccinated') AS fully,
        SUM(vaccination_status NOT IN (
            'Vaccination Phase 1 Completed',
            'Vaccination Phase 2 Completed',
            'Fully Vaccinated'
        )) AS not_started
    FROM main_dogs
";
$progressResult = mysqli_query($conn, $progressQuery);
$progressData = mysqli_fetch_assoc($progressResult);

// --- Helper function for area data ---
function getAreaData($conn, $status) {
    $query = "SELECT dog_area, COUNT(*) AS total FROM main_dogs WHERE vaccination_status = '$status' GROUP BY dog_area";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['dog_area']] = (int)$row['total'];
    }
    return $data;
}

$areaPhase1 = getAreaData($conn, 'Vaccination Phase 1 Completed');
$areaPhase2 = getAreaData($conn, 'Vaccination Phase 2 Completed');
$areaFully = getAreaData($conn, 'Fully Vaccinated');

echo json_encode([
    'phase1' => (int)$progressData['phase1'],
    'phase2' => (int)$progressData['phase2'],
    'fully' => (int)$progressData['fully'],
    'area_phase1' => $areaPhase1,
    'area_phase2' => $areaPhase2,
    'area_fully' => $areaFully
]);
?>
