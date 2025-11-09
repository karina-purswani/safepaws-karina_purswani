<?php
$conn = new mysqli("localhost", "root", "", "safepaws");
if($conn->connect_error) die("DB Connection failed: ".$conn->connect_error);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['complaint_id']) && isset($_POST['status'])){
        $id = $_POST['complaint_id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE complaints SET status=? WHERE complaint_id=?");
        $stmt->bind_param("si",$status,$id);

        echo $stmt->execute() ? 'success' : 'error';
        $stmt->close();
    } else {
        echo 'error';
    }
}
$conn->close();
?>
