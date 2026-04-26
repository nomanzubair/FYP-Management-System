<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost","root","","student_portal");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = strtoupper(trim($_POST['student_id']));


    if (!preg_match('/^BSF\d{7}$/', $student_id)) {
        $_SESSION['msg'] = "❌ Error: ID must start with BSF followed by exactly 7 digits!";
        $_SESSION['msg_type'] = "error";
        header("Location: allowed_students.php");
        exit();
    }

    // 2. Duplicate Check
    $check = $conn->prepare("SELECT * FROM allowed_students WHERE student_id = ?");
    $check->bind_param("s", $student_id);
    $check->execute();
    $res = $check->get_result();

    if($res->num_rows > 0) {
        $_SESSION['msg'] = "⚠️ This ID is already in the list!";
        $_SESSION['msg_type'] = "warning";
        header("Location: allowed_students.php");
        exit();
    }
    $check->close();

    // 3. Insert
    $stmt = $conn->prepare("INSERT INTO allowed_students (student_id) VALUES (?)");
    $stmt->bind_param("s", $student_id);

    if($stmt->execute()){
        $_SESSION['msg'] = "✅ Student added successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "❌ Database Error!";
        $_SESSION['msg_type'] = "error";
    }
    header("Location: allowed_students.php");
    exit();
}
?>