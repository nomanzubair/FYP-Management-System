<?php
session_start();
session_destroy();
header("Location: std_login.html");
exit();
?>