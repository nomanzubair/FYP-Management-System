<?php
session_start();

$conn = new mysqli("localhost","root","","student_portal");

$email = $_SESSION['reset_email'];
$newpass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE students SET password=? WHERE email=?");
$stmt->bind_param("ss",$newpass,$email);

if($stmt->execute()){
    echo "Password Updated Successfully!";
} else {
    echo "Error!";
}
?>