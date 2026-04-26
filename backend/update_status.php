<?php
$conn = new mysqli("localhost","root","","student_portal");

$id = $_GET['id'];
$status = $_GET['status'];

// Update status
$stmt = $conn->prepare("UPDATE fyp_submissions SET status=? WHERE student_id=?");
$stmt->bind_param("ss", $status, $id);
$stmt->execute();

header("Location: view_students.php");
?>