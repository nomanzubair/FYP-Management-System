<?php
session_start();

// if not login
if (!isset($_SESSION['student_id'])) {
    header("Location: std_login.html");
    exit();
}

// database connection
$conn = new mysqli("localhost", "root", "", "student_portal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// fetch student data
$std_id = $_SESSION['student_id'];

$sql = "SELECT std_name, std_id, email, batch, department FROM students WHERE std_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $std_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// to fetch status
$status_sql = "SELECT status FROM fyp_submissions WHERE student_id = ?";
$stmt_status = $conn->prepare($status_sql);
$stmt_status->bind_param("s", $std_id);
$stmt_status->execute();
$status_result = $stmt_status->get_result();
$status_data = $status_result->fetch_assoc();
$current_status = $status_data ? $status_data['status'] : "No Submission Yet";

$status_class = "status-default"; 
if ($current_status == "Approved") {
    $status_class = "status-green";
} elseif ($current_status == "Pending") {
    $status_class = "status-yellow";
} elseif ($current_status == "Rejected") {
    $status_class = "status-red";
}

// Check if the user is a Group Leader
$leader_check_sql = "SELECT id FROM fyp_submissions WHERE student_id = ?";
$stmt_leader = $conn->prepare($leader_check_sql);
$stmt_leader->bind_param("s", $std_id);
$stmt_leader->execute();
$is_leader = $stmt_leader->get_result()->num_rows > 0;


$stmt->close();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Dashboard | UE Portal</title>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Segoe UI", sans-serif;
      }

      body {
        background: linear-gradient(135deg, #f4f7f6 0%, #d1d9d7 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
      }

      .dashboard-card {
        background: white;
        width: 100%;
        max-width: 600px;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border-top: 8px solid #2e6b3e;
        position: relative;
      }

      .header {
        text-align: center;
        margin-bottom: 30px;
      }
      .header h2 {
        color: #2e6b3e;
        font-size: 28px;
      }

      .info-section {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
      }

      .info-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
      }

      .info-item:last-child {
        border-bottom: none;
      }
      .info-item strong {
        color: #555;
      }
      .info-item span {
        color: #222;
        font-weight: 600;
      }

      .actions {
        display: grid;
        /* Fixed grid to accommodate 2 or 3 buttons smoothly */
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
      }

      .btn {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 14px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        border: 2px solid #2e6b3e;
        background: #e8f5e9;
        color: #2e6b3e;
        text-align: center;
      }

      .btn:hover {
        background: #2e6b3e;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(46, 107, 62, 0.3);
      }
      
      /* Specific style for Manage Group if needed, using same variables */
      .btn-manage {
        border-color: #ffa000;
        background: #fff8e1;
        color: #b7791f;
      }
      .btn-manage:hover {
        background: #ffa000;
        border-color: #ffa000;
      }

      .logout-container {
        margin-top: 40px;
        border-top: 1px solid #eee;
        padding-top: 20px;
      }

      .btn-logout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px;
        background: #fff;
        color: #f44336;
        border: 2px solid #f44336;
        border-radius: 10px;
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.3s ease;
      }

      .btn-logout:hover {
        background: #f44336;
        color: white;
        box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
      }

      .logout-icon {
        font-size: 18px;
      }


      .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        justify-content: center;
        align-items: center;
        z-index: 1000;
      }

      .modal-content {
        background: white;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 400px;
        text-align: center;
        animation: slideIn 0.3s ease-out;
      }

      @keyframes slideIn {
        from {
          transform: translateY(-50px);
          opacity: 0;
        }
        to {
          transform: translateY(0);
          opacity: 1;
        }
      }


      .status-badge {
        display: inline-block;
        padding: 10px 25px;
        border-radius: 30px;
        color: white;
        font-weight: bold;
        margin-top: 15px;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 1px;
      }

      .status-green {
        background-color: #28a745;
        box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
      }
      .status-yellow {
        background-color: #ffc107;
        color: #333;
        box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3);
      }
      .status-red {
        background-color: #dc3545;
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
      }
      .status-default {
        background-color: #6c757d;
      }
      .close-modal {
        margin-top: 20px;
        background: #eee;
        color: #333;
        border: none;
        padding: 8px 20px;
        border-radius: 5px;
        cursor: pointer;
      }

      /* Responsive */
      @media (max-width: 480px) {
        .actions {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>
  <body>
    <div class="dashboard-card">
      <div class="header">
        <h2>Welcome, <?php echo $student['std_name']; ?> 👋</h2>
      </div>

      <div class="info-section">
        <div class="info-item">
          <strong>Student ID:</strong>
          <span><?php echo $student['std_id']; ?></span>
        </div>
        <div class="info-item">
          <strong>Email:</strong> <span><?php echo $student['email']; ?></span>
        </div>
        <div class="info-item">
          <strong>Batch:</strong> <span><?php echo $student['batch']; ?></span>
        </div>
        <div class="info-item">
          <strong>Department:</strong>
          <span><?php echo $student['department']; ?></span>
        </div>
      </div>

      <div class="actions">
        <a href="FYP_Proposal.php" class="btn btn-proposal">Submit FYP Proposal</a>
        
        <?php if($is_leader): ?>
        <a href="manage_group.php" class="btn btn-manage">Manage Group ⚙️</a>
        <?php endif; ?>

        <button onclick="openModal()" class="btn btn-status">Check Status</button>
      </div>

      <div class="logout-container">
        <a href="logout.php" class="btn-logout">
          <span>Logout 🔒</span>
        </a>
      </div>
    </div>

    <div id="statusModal" class="modal">
      <div class="modal-content">
        <h3 style="color: #2e6b3e; margin-bottom: 10px">
          FYP Submission Status
        </h3>
        <p style="color: #666">Current status of your project proposal:</p>

        <div class="status-badge <?php echo $status_class; ?>">
          <?php echo $current_status; ?>
        </div>

        <br />
        <button class="close-modal" onclick="closeModal()">Close</button>
      </div>
    </div>

    <script>
      const modal = document.getElementById("statusModal");

      function openModal() {
        modal.style.display = "flex";
      }

      function closeModal() {
        modal.style.display = "none";
      }

      window.onclick = function (event) {
        if (event.target == modal) {
          closeModal();
        }
      };
    </script>
  </body>
</html>
<?php $conn->close(); ?>