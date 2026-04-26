<?php
session_start();
if (!isset($_SESSION['student_id'])) { header("Location: std_login.html"); exit(); }

$conn = new mysqli("localhost", "root", "", "student_portal");
$std_id = $_SESSION['student_id'];

// --- ADD MEMBER LOGIC ---
if (isset($_POST['add_member'])) {
    $member_slot = $_POST['member_slot'];
    $name = ucwords(strtolower(trim($_POST['member_name'])));
    $roll = strtoupper(trim($_POST['member_roll']));

    // 1. First check Format (BSF + 7 Digits)
    if(preg_match('/^BSF[0-9]{7}$/', $roll)) {
        
        // 2. Then check Admin allow this ID?
        $check_allowed = $conn->prepare("SELECT * FROM allowed_students WHERE student_id = ?");
        $check_allowed->bind_param("s", $roll);
        $check_allowed->execute();
        $allowed_res = $check_allowed->get_result();

        if($allowed_res->num_rows > 0) {
            // Member is allow, now update
            $stmt = $conn->prepare("UPDATE fyp_submissions SET $member_slot = ? WHERE student_id = ?");
            $full_entry = $name . " (" . $roll . ")";
            $stmt->bind_param("ss", $full_entry, $std_id);
            
            if($stmt->execute()) {
                header("Location: manage_group.php");
                exit();
            }
            $stmt->close();
        } else {
            // Member is not allow 
            echo "<script>alert('❌ Access Denied: This student ID is not allowed by Admin!'); window.history.back();</script>";
        }
        $check_allowed->close();
        
    } else {
        echo "<script>alert('Invalid Format! Use BSF followed by 7 digits.'); window.history.back();</script>";
    }
}

// --- REMOVE MEMBER LOGIC ---
if (isset($_GET['remove'])) {
    $col = $_GET['remove'];
    // Security check: Only allow specific column names
    $allowed_cols = ['member1', 'member2', 'member3'];
    if(in_array($col, $allowed_cols)) {
        $conn->query("UPDATE fyp_submissions SET $col = '' WHERE student_id = '$std_id'");
    }
    header("Location: manage_group.php");
    exit();
}

$res = $conn->query("SELECT * FROM fyp_submissions WHERE student_id = '$std_id'");
$data = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Group</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #f4f7f6 0%, #d1d9d7 100%); min-height: 100vh; padding: 20px; display: flex; flex-direction: column; align-items: center; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 500px; border-top: 6px solid #2e6b3e; }
        h2 { color: #2e6b3e; margin-bottom: 20px; text-align: center; }
        .member-row { background: #f9f9f9; display: flex; justify-content: space-between; align-items: center; padding: 15px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #eee; }
        .member-info { font-weight: 600; color: #333; }
        .remove-btn { color: #dc3545; text-decoration: none; font-weight: bold; font-size: 14px; padding: 5px 10px; border: 1px solid #dc3545; border-radius: 5px; transition: 0.3s; }
        .remove-btn:hover { background: #dc3545; color: white; }
        .add-form { margin-top: 20px; padding-top: 20px; border-top: 2px dashed #eee; }
        input, select { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        .add-btn { width: 100%; background: #2e6b3e; color: white; border: none; padding: 14px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .add-btn:hover { background: #23522f; transform: translateY(-2px); }
        .back-btn { display: inline-block; margin-top: 25px; text-decoration: none; color: #666; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Manage Group Members</h2>
        
        <?php for($i=1; $i<=3; $i++): $m = "member$i"; ?>
            <div class="member-row">
                <span class="member-info">
                    M<?php echo $i; ?>: <?php echo $data[$m] ?: '<span style="color:#999; font-weight:normal;">Empty Slot</span>'; ?>
                </span>
                <?php if($data[$m]): ?>
                    <a href="?remove=<?php echo $m; ?>" class="remove-btn" onclick="return confirm('Are you sure?')">Remove</a>
                <?php endif; ?>
            </div>
        <?php endfor; ?>

        <?php if(!$data['member1'] || !$data['member2'] || !$data['member3']): ?>
        <div class="add-form">
            <h4 style="color:#555; margin-bottom:10px;">Add Member (Admin Allowed Only)</h4>
            <form method="POST">
                <select name="member_slot" required>
                    <option value="">Select Available Slot</option>
                    <?php if(!$data['member1']) echo '<option value="member1">Member 1</option>'; ?>
                    <?php if(!$data['member2']) echo '<option value="member2">Member 2</option>'; ?>
                    <?php if(!$data['member3']) echo '<option value="member3">Member 3</option>'; ?>
                </select>

                <input type="text" id="member_name" name="member_name" placeholder="Full Name" required onblur="formatName(this)">
                <input type="text" id="member_roll" name="member_roll" placeholder="Roll No (BSF...)" required maxlength="10" onblur="validateRoll(this)">

                <button type="submit" name="add_member" class="add-btn">Verify & Save Member</button>
            </form>
        </div>
        <?php else: ?>
            <p style="color: #2e6b3e; text-align: center; margin-top: 20px; font-weight: bold;">✅ Group is Full</p>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="student_dashboard.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>

    <script>
        function formatName(input) {
            let val = input.value.toLowerCase().trim();
            if(val == "") return;
            let formatted = val.split(' ').map(word => {
                return word.charAt(0).toUpperCase() + word.slice(1);
            }).join(' ');
            input.value = formatted;
        }

        function validateRoll(input) {
            let val = input.value.toUpperCase().trim();
            input.value = val;
            if(val.length > 0) {
                let pattern = /^BSF[0-9]{7}$/;
                if(!pattern.test(val)) {
                    alert("❌ Invalid Format!\nMust be BSF + 7 digits (e.g. BSF1234567)");
                    input.value = "";
                    setTimeout(() => input.focus(), 10);
                }
            }
        }
    </script>
</body>
</html>