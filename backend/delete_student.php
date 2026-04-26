<?php
$conn = new mysqli("localhost","root","","student_portal");

$id = $_GET['id'];

// first submissions delete  (foreign key issue avoid)
$conn->query("DELETE FROM fyp_submissions WHERE student_id='$id'");

$conn->query("DELETE FROM students WHERE std_id='$id'");

header("Location: view_students.php");
?>