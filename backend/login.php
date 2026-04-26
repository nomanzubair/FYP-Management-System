<?php
session_start();

// Allow only POST request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: std_login.html");
    exit();
}

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get Form Data
$login_id = trim($_POST['sid']);
$pass = $_POST['pass'];

// Find User
$sql = "SELECT * FROM students WHERE std_id = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $login_id, $login_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $row = $result->fetch_assoc();

    // Verify Password
    if (password_verify($pass, $row['pass'])) {

        // Create Session
        $_SESSION['student_id'] = $row['std_id'];
        $_SESSION['student_name'] = $row['std_name'];

        // Remember Me
        if (isset($_POST['remember'])) {
            setcookie("student_login", $row['std_id'], time() + (86400 * 10), "/"); // Remember only for 10 days
        }

        // Redirect to Dashboard
        header("Location: student_dashboard.php");
        exit();

    } else {
        echo "Incorrect Password!";
    }

} else {
    echo "Student ID or Gmail not found!";
}

$stmt->close();
$conn->close();
?>