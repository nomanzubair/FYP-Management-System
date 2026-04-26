<?php
session_start();

if($_POST['code'] == $_SESSION['reset_code']){
    header("Location: new_password.html");
} else {
    echo "Invalid Code!";
}
?>