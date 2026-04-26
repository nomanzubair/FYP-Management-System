<?php
session_start();
$conn = new mysqli("localhost","root","","student_portal");

$id = $_GET['id'];

$conn->query("DELETE FROM allowed_students WHERE id=$id");

header("Location: allowed_students.php");
?>