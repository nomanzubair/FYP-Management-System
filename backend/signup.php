<?php
// DB Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get Form Data
$std_name = $_POST['stdn'];
$std_id = $_POST['stdi'];
$contact = $_POST['cnt'];
$email = $_POST['stdg'];
$batch = $_POST['bth'];
$department = $_POST['dp'];
$shift = $_POST['sft'];
$pass = $_POST['pass'];
$confirm_pass = $_POST['confirm_pass'];

// 1. Check if ID is allowed by Admin
$stmt = $conn->prepare("SELECT * FROM allowed_students WHERE student_id=?");
$stmt->bind_param("s", $std_id);
$stmt->execute();
$allowed_result = $stmt->get_result();

if($allowed_result->num_rows == 0){
    die("<script>alert('❌ You are not assigned by Admin'); window.history.back();</script>");
}
$stmt->close();

// 2. Check if already registered
$check_query = "SELECT * FROM students WHERE std_id = ? OR email = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ss", $std_id, $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    die("<script>alert('⚠️ You are already registered with this ID or Email!'); window.history.back();</script>");
}
$stmt->close();

// --- NEW FEATURE START: Member Check Logic ---

$member_check = $conn->prepare("
    SELECT s.std_name as leader_name 
    FROM fyp_submissions f 
    JOIN students s ON f.student_id = s.std_id 
    WHERE f.member1 LIKE ? OR f.member2 LIKE ? OR f.member3 LIKE ?
");
$search_term = "%" . $std_id . "%";
$member_check->bind_param("sss", $search_term, $search_term, $search_term);
$member_check->execute();
$m_result = $member_check->get_result();

if ($m_result->num_rows > 0) {
    $row = $m_result->fetch_assoc();
    $leader = $row['leader_name'];
    // Sirf alert dikhayenge, signup rokna hai ya nahi ye aapki marzi hai (yahan main alert de kar rok raha hoon)
    die("<script>alert('ℹ️ You are already a member of $leader\'s group. Please login with your credentials.'); window.location.href='std_login.html';</script>");
}
$member_check->close();
// --- NEW FEATURE END ---

if ($pass !== $confirm_pass) {
    die("<script>alert('Passwords do not match!'); window.history.back();</script>");
}

$hashed_password = password_hash($pass, PASSWORD_DEFAULT);

$insert_query = "INSERT INTO students (std_name, std_id, contact, email, batch, shift, department, pass) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_query);
$stmt->bind_param("ssssssss", $std_name, $std_id, $contact, $email, $batch, $shift, $department, $hashed_password);

if ($stmt->execute()):
    $update = $conn->prepare("UPDATE allowed_students SET status='used' WHERE student_id=?");
    $update->bind_param("s", $std_id);
    $update->execute();
    $update->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful | UE Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f4f7f6; height: 100vh; display: flex; justify-content: center; align-items: center; }
        .success-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; max-width: 450px; width: 90%; border-top: 6px solid #2e6b3e; }
        .icon-box { width: 80px; height: 80px; background: #e8f5e9; color: #2e6b3e; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 40px; margin: 0 auto 20px; }
        h1 { color: #2e6b3e; margin-bottom: 10px; font-size: 24px; }
        p { color: #666; margin-bottom: 25px; line-height: 1.5; }
        .login-btn { display: inline-block; background: #2e6b3e; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; transition: 0.3s; }
        .login-btn:hover { background: #1f4d2c; transform: translateY(-2px); }
        .redirect-text { margin-top: 20px; font-size: 13px; color: #999; }
        #seconds { font-weight: bold; color: #2e6b3e; }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon-box">✓</div>
        <h1>Registration Successful!</h1>
        <p>Your account has been created! You can now access your portal by logging in.</p>
        <a href="std_login.html" class="login-btn">LOGIN NOW</a>
        <p class="redirect-text">Automatically redirecting in <span id="seconds">10</span>s...</p>
    </div>

    <script>
        let timeLeft = 10;
        let downloadTimer = setInterval(function(){
            if(timeLeft <= 0){
                clearInterval(downloadTimer);
                window.location.href = "std_login.html";
            }
            document.getElementById("seconds").innerHTML = timeLeft;
            timeLeft -= 1;
        }, 1000);
    </script>
</body>
</html>
<?php
else:
    echo "Error: " . $stmt->error;
endif;
$stmt->close();
$conn->close();
?>