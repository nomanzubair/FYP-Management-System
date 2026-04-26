<?php
session_start();

if($_POST['otp'] == $_SESSION['otp']){
    header("Location: new_password.html");
} else {
    echo "Invalid OTP!";
}
?>