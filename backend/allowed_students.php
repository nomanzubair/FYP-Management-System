<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost","root","","student_portal");

// Pop-up Alert Check
if(isset($_SESSION['msg'])) {
    $message = $_SESSION['msg'];
    echo "<script>alert('$message');</script>";
    unset($_SESSION['msg']);
    unset($_SESSION['msg_type']);
}

// FETCH DATA
$result = $conn->query("SELECT * FROM allowed_students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Allowed Students</title>
    <style>
        /* --- General Reset --- */
        * {margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', sans-serif;}
        body {background:#f4f7f6; padding:30px 10px; display:flex; justify-content:center;}

        .container {width:100%; max-width:1100px;}

        .card {
            background:white;
            border-radius:12px;
            box-shadow:0 8px 25px rgba(0,0,0,0.1);
            border-top:6px solid #2e6b3e;
            padding:30px;
        }

        /* --- HEADER SECTION (Search, Logo, Back Button) --- */
        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 15px;
        }

        /* Search Bar (Left) */
        .search-box { flex: 1; display: flex; justify-content: flex-start; }
        .search-input {
            width: 280px;
            padding: 10px 18px;
            border-radius: 50px;
            border: 1px solid #dfe1e5;
            outline: none;
            transition: all 0.3s;
        }
        .search-input:focus { box-shadow: 0 1px 6px rgba(32,33,36,0.2); border-color: #2e6b3e; }

        /* Logo (Center) */
        .logo-box { flex: 1; display: flex; justify-content: center; }
        .card-logo {
            width: 85px; height: 85px;
            background: url("ue_logo.png") no-repeat center;
            background-size: contain;
            transition: transform 0.4s;
        }
        .card-logo:hover { transform: scale(1.1); }

        /* Back Button (Right) */
        .nav-actions { flex: 1; display: flex; justify-content: flex-end; }
        .back-btn {
            padding: 10px 22px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            color: #2e6b3e;
            background: #fff;
            border: 1px solid #2e6b3e;
            transition: 0.3s;
        }
        .back-btn:hover { background: #2e6b3e; color: #fff; transform: translateX(-3px); }

        /* --- ADD STUDENT FORM --- */
        h2 { color:#2e6b3e; text-align:center; text-transform:uppercase; margin-bottom:20px; }
        
        .add-form-container {
            background: #f9fdfa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            justify-content: center;
            border: 1px dashed #2e6b3e;
        }
        .add-form-container input {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
            width: 220px;
            outline: none;
        }
        .add-form-container button {
            background: #2e6b3e;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        .add-form-container button:hover { background: #1f4d2c; }

        /* --- TABLE & STICKY HEADER --- */
        .table-scroll {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        table { width: 100%; border-collapse: collapse; }
        
        thead th {
            position: sticky;
            top: 0;
            background: #2e6b3e;
            color: white;
            z-index: 10;
            padding: 14px;
            text-transform: uppercase;
            font-size: 13px;
            text-align: left;
        }

        td { padding: 12px; border: 1px solid #eee; text-align: left; font-size: 14px; }
        tr:hover { background: #f1f9f4; }

        /* Status Styling */
        .status-unused { color: #2e6b3e; font-weight: bold; background: #e8f5e9; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .status-used { color: #f44336; font-weight: bold; background: #ffebee; padding: 4px 12px; border-radius: 20px; font-size: 12px; }

        /* Delete Button */
        .delete-btn {
            color: #f44336;
            border: 1px solid #f44336;
            padding: 6px 14px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            transition: 0.2s;
        }
        .delete-btn:hover { background: #f44336; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        
        <div class="header-row">
            <div class="search-box">
                <input type="text" id="liveSearch" class="search-input" placeholder="Search Student ID...">
            </div>

            <div class="logo-box">
                <div class="card-logo"></div>
            </div>

            <div class="nav-actions">
                <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
            </div>
        </div>

        <h2>Allowed Students Management</h2>

        <div class="add-form-container">
            <form action="add_student.php" method="post" style="display:flex;">
                <input type="text" 
                       name="student_id" 
                       placeholder="e.g. BSF1234567" 
                       style="text-transform: uppercase;" 
                       maxlength="10" 
                       required>
                <button type="submit">Add Student</button>
            </form>
        </div>

        <div class="table-scroll">
            <table id="studentTable">
                <thead>
                    <tr>
                        <th>Serial No.</th>
                        <th>Student ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $serial = 1;
                    if($result->num_rows > 0):
                        while($row = $result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?php echo $serial++; ?></td>
                        <td><strong><?php echo $row['student_id']; ?></strong></td>
                        <td>
                            <span class="<?php echo ($row['status'] == 'unused') ? 'status-unused' : 'status-used'; ?>">
                                <?php echo strtoupper($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="delete_allow_student.php?id=<?php echo $row['id']; ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Are you sure you want to remove this ID?')">Delete</a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 25px;">No students assigned yet.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    // Live Search Functionality
    document.getElementById('liveSearch').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let rows = document.querySelector("#studentTable tbody").rows;
        
        for (let i = 0; i < rows.length; i++) {
            let studentID = rows[i].cells[1].textContent.toUpperCase();
            if (studentID.indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    });
</script>

</body>
</html>