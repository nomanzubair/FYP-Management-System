<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard | UE Portal</title>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Segoe UI", sans-serif;
      }

      body {
        background-color: #f4f7f6;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
      }

      .dashboard-container {
        width: 90%;
        max-width: 700px;
      }

      .glass-card {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-top: 5px solid #2e6b3e;
        text-align: center;
      }

      .glass-card::before {
        content: "";
        display: block;
        width: 80px;
        height: 80px;
        margin: 0 auto 10px;
        background: url("ue_logo.png") no-repeat center;
        background-size: contain;
        transition: transform 0.4s ease-in-out;
      }

      .glass-card:hover::before {
        transform: scale(1.15);
      }

      .header-section h2 {
        font-size: 24px;
        color: #2e6b3e;
        margin-bottom: 5px;
        text-transform: uppercase;
      }

      .header-section p {
        color: #666;
        margin-bottom: 25px;
        font-size: 14px;
      }

      .action-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
      }

      .dash-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        color: #2e6b3e;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s ease;
      }

      .dash-btn .icon {
        font-size: 24px;
        margin-bottom: 10px;
      }

      .dash-btn:hover {
        background: #2e6b3e;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }

      .dash-btn.logout {
        grid-column: span 2;
        border-color: #f44336;
        color: #f44336;
        padding: 12px;
        font-size: 16px;
        flex-direction: row;
        gap: 10px;
      }

      .dash-btn.logout .icon {
        font-size: 18px;
        margin-bottom: 0;
      }

      .dash-btn.logout:hover {
        background: #f44336;
        color: white;
        border-color: #f44336;
      }

      /* Responsive */
      @media (max-width: 480px) {
        .action-grid {
          grid-template-columns: 1fr;
        }

        .dash-btn.logout {
          grid-column: span 1;
        }
      }
    </style>
  </head>
  <body>
    <div class="dashboard-container">
      <div class="glass-card">
        <div class="header-section">
          <h2>Admin Dashboard</h2>
          <p>Welcome back! Manage your system efficiently.</p>
        </div>

        <div class="action-grid">
          <a href="create_supervisor.php" class="dash-btn">
            <span class="icon">➕</span> Create Supervisor
          </a>
          <a href="view_supervisors.php" class="dash-btn">
            <span class="icon">👥</span> View Supervisors
          </a>
          <a href="view_students.php" class="dash-btn">
            <span class="icon">🎓</span> View Students
          </a>
          <a href="allowed_students.php" class="dash-btn">
            <span class="icon">✅</span> Allowed Students
          </a>
          <a href="admin_logout.php" class="dash-btn logout">
            <span class="icon">🚪</span> Logout
          </a>
        </div>
      </div>
    </div>
  </body>
</html>