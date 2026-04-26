<?php
session_start();
if (!isset($_SESSION['supervisor_id'])) {
    header("Location: supervisor_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "student_portal");
$id = $_GET['id'];

// Fetch Student info
$info_sql = "SELECT s.std_name, f.idea FROM fyp_submissions f JOIN students s ON f.student_id = s.std_id WHERE f.id = ?";
$stmt_info = $conn->prepare($info_sql);
$stmt_info->bind_param("i", $id);
$stmt_info->execute();
$info_result = $stmt_info->get_result();
$student_data = $info_result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    $comments = $_POST['comments'];

    $stmt = $conn->prepare("UPDATE fyp_submissions SET status = ?, comments = ?, response_time = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $status, $comments, $id);
    $stmt->execute();

    header("Location: supervisor_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Response | Supervisor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        
        body {
            background: linear-gradient(135deg, #f4f7f6 0%, #d1d9d7 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .response-container {
            background: white;
            width: 100%;
            max-width: 500px;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-top: 8px solid #2e6b3e;
        }

        h2 { color: #2e6b3e; text-align: center; margin-bottom: 25px; font-size: 24px; }

        .student-info {
            background: #f0f4f1;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 5px solid #2e6b3e;
        }

        .view-idea-btn {
            color: #2e6b3e;
            text-decoration: underline;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }

        label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px; }

        select, textarea {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            outline: none;
            transition: 0.3s;
        }

        select:focus, textarea:focus { border-color: #2e6b3e; }

        textarea { height: 100px; resize: none; }

        .btn-row { display: flex; gap: 10px; }

        input[type="submit"] {
            flex: 2;
            padding: 12px;
            background: #2e6b3e;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        input[type="submit"]:hover { background: #214f2e; }

        
        .btn-back {
            flex: 1;
            padding: 12px;
            background: #f8f9fa;
            color: #333;
            text-decoration: none;
            text-align: center;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: #dc3545; 
            color: white; 
            border-color: #bd2130;
        }


        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 12px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .close-btn {
            margin-top: 20px;
            padding: 8px 20px;
            background: #2e6b3e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="response-container">
    <h2>Project Response</h2>

    <div class="student-info">
        <p><strong>Student:</strong> <?php echo $student_data['std_name']; ?></p>
        <p><strong>Idea:</strong> <span class="view-idea-btn" onclick="openIdeaModal()">Click to View Idea</span></p>
    </div>

    <form method="post">
        <label>Decision</label>
        <select name="status" required>
            <option value="" disabled selected>Choose Status...</option>
            <option value="Approved">Approve</option>
            <option value="Rejected">Reject</option>
        </select>

        <label>Comments</label>
        <textarea name="comments" placeholder="Write feedback..." required></textarea>

        <div class="btn-row">
            <input type="submit" value="Submit Feedback">
            <a href="supervisor_dashboard.php" class="btn-back">Cancel</a>
        </div>
    </form>
</div>

<div id="ideaModal" class="modal">
    <div class="modal-content">
        <h3 style="color: #2e6b3e; margin-bottom: 15px;">Proposed Project Idea</h3>
        <p style="color: #555; text-align: left; font-size: 14px; line-height: 1.6;">
            <?php echo nl2br($student_data['idea']); ?>
        </p>
        <button class="close-btn" onclick="closeIdeaModal()">Close View</button>
    </div>
</div>

<script>
    function openIdeaModal() {
        document.getElementById('ideaModal').style.display = 'flex';
    }
    function closeIdeaModal() {
        document.getElementById('ideaModal').style.display = 'none';
    }

    window.onclick = function(event) {
        let modal = document.getElementById('ideaModal');
        if (event.target == modal) {
            closeIdeaModal();
        }
    }
</script>

</body>
</html>