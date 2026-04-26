<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost","root","","student_portal");

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM supervisors WHERE id=?");
$stmt->bind_param("i",$id);

if($stmt->execute()){
    header("Location: view_supervisors.php");
}else{
    echo "Error deleting supervisor.";
}

?>