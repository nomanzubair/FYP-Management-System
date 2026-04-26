<?php
session_start();
$conn = new mysqli("localhost", "root", "", "student_portal");

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM supervisors WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        $_SESSION['supervisor_id'] = $row['id'];
        $_SESSION['supervisor_name'] = $row['name'];
        header("Location: supervisor_dashboard.php");
        exit();
    } else {
        echo "Incorrect password!";
    }
} else {
    echo "Supervisor not found!";
}
?>