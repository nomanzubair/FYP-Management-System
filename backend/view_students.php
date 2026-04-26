<?php
session_start();

// Admin login check
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Database Connection
$conn = new mysqli("localhost","root","","student_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Data
$sql = "SELECT students.*, 
               supervisors.name AS supervisor_name,
               fyp_submissions.member1,
               fyp_submissions.member2,
               fyp_submissions.member3,
               fyp_submissions.idea,
               fyp_submissions.proposal_file,
               fyp_submissions.status
        FROM students
        LEFT JOIN fyp_submissions 
        ON students.std_id = fyp_submissions.student_id
        LEFT JOIN supervisors 
        ON fyp_submissions.supervisor_id = supervisors.id";

$result = $conn->query($sql);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Students | UE Portal</title>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Segoe UI", sans-serif;
      }
      body {
        background: #f4f7f6;
        padding: 20px;
        display: flex;
        justify-content: center;
      }

      .container {
        width: 100%;
        max-width: 1200px;
      }

      .card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-top: 5px solid #2e6b3e;
        padding: 30px;
        text-align: center;
      }


      .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        
      }


      .search-container {
        flex: 1;
        display: flex;
        justify-content: flex-start;
      }
      .search-bar {
        position: relative;
        width: 250px;
      }
      .search-bar input {
        width: 100%;
        padding: 10px 10px 10px 35px;
        border: 1.5px solid #ccc;
        border-radius: 25px;
        outline: none;
        transition: all 0.3s ease;
        font-size: 14px;
      }

      .search-bar input:focus,
      .search-bar input:hover {
        border-color: #2e6b3e;
        box-shadow: 0 0 8px rgba(46, 107, 62, 0.15);
        background: #f9fffb;
      }


      .card-logo {
        width: 100px;
        height: 100px;
        background: url("ue_logo.png") no-repeat center;
        background-size: contain;
        transition: transform 0.4s ease-in-out;
      }
      .card-logo:hover {
        transform: scale(1.1);
      }

      
      .back-container {
        flex: 1;
        display: flex;
        justify-content: flex-end;
      }

      .back-btn-top {
        position: relative;
        background: white;
        color: #2e6b3e;
        border: 2px solid #2e6b3e;
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        font-size: 13px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 160px; 
        height: 42px;
        overflow: hidden;
        transition: all 0.4s ease;
      }

 
      .back-text {
        position: absolute;
        transition: all 0.4s ease;
        opacity: 1;
      }


      .back-icon {
        position: absolute;
        transition: all 0.4s ease;
        opacity: 0;
        transform: translateY(20px);
        font-size: 18px;
      }


      .back-btn-top:hover {
        background: #2e6b3e;
        color: white;
        box-shadow: 0 4px 10px rgba(46, 107, 62, 0.2);
      }

      .back-btn-top:hover .back-text {
        opacity: 0;
        transform: translateY(-20px); 
      }

      .back-btn-top:hover .back-icon {
        opacity: 1;
        transform: translateY(0); 
      }

      h2 {
        color: #2e6b3e;
        text-transform: uppercase;
        margin-bottom: 20px;
        letter-spacing: 1px;
      }

    
      table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      }
      th,
      td {
        padding: 12px 10px;
        border: 1px solid #eee;
        font-size: 13px;
        text-align: left;
      }
      th {
        background: #2e6b3e;
        color: white;
        text-transform: uppercase;
      }
      tr:nth-child(even) {
        background: #fcfcfc;
      }
      tr:hover {
        background: #f1f9f4;
      }

      
      .delete-link {
        color: #f44336;
        font-weight: bold;
        text-decoration: none;
      }
      .delete-link:hover {
        color: red;
      }
      .clickable {
        cursor: pointer;
        transition: 0.3s;
      }
      .clickable:hover {
        color: #2e6b3e;
        font-weight: bold;
        background: #e8f5e9;
      }

     
      .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
      }
      .modal-content {
        background: white;
        padding: 25px;
        border-radius: 8px;
        width: 350px;
        text-align: center;
        position: relative;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
      }
      .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #888;
      }
      .close:hover {
        color: #333;
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
    <div class="container">
      <div class="card">
        <div class="header-top">
          <div class="search-container">
            <div class="search-bar">
              <span
                style="position: absolute; left: 12px; top: 8px; color: #888"
                >🔍</span
              >
              <input type="text" placeholder="Search student records..." />
            </div>
          </div>

          <div class="card-logo"></div>
          <div class="back-container">
            <a href="admin_dashboard.php" class="back-btn-top">
              <span class="back-text">BACK TO DASHBOARD</span>
              <span class="back-icon">🏠</span>
            </a>
          </div>
        </div>
        <h2>All Students</h2>
        <table>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Department</th>
            <th>Batch</th>
            <th>Shift</th>
            <th>Group Members</th>
            <th>Proposal</th>
            <th>Idea</th>
            <th>Email</th>
            <th>Supervisor</th>
            <th>Status</th>
            <th>Action</th>
          </tr>

          <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['std_id']; ?></td>
            <td><?php echo $row['std_name']; ?></td>
            <td><?php echo $row['contact']; ?></td>
            <td><?php echo $row['department']; ?></td>
            <td><?php echo $row['batch']; ?></td>
            <td><?php echo $row['shift']; ?></td>
            <td
              class="clickable"
              data-members="<?php echo $row['member1'].'<br>'.$row['member2'].'<br>'.$row['member3']; ?>"
            >
              Click to View
            </td>
            <td>
              <?php if($row['proposal_file']): ?>
              <a href="<?php echo $row['proposal_file']; ?>" target="_blank"
                >View</a
              >
              <?php else: ?>Not Uploaded<?php endif; ?>
            </td>
            <td class="clickable" data-idea="<?php echo $row['idea']; ?>">
              Click to View
            </td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['supervisor_name'] ?: "Not Assigned"; ?></td>
            <td><?php echo $row['status'] ?: "Pending"; ?></td>
            <td>
              <?php if($row['status']=="Pending"): ?>
                 <?php endif; ?>
              <a
                href="delete_student.php?id=<?php echo $row['std_id']; ?>"
                class="delete-link"
                onclick="return confirm('Are you sure?');"
                >Delete</a
              >
            </td>
          </tr>
          <?php endwhile; ?>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div id="infoModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="modalTitle"></h3>
        <p id="modalData"></p>
      </div>
    </div>

    <script>
      const modal = document.getElementById("infoModal");
      const modalTitle = document.getElementById("modalTitle");
      const modalData = document.getElementById("modalData");
      const closeBtn = document.querySelector(".close");

      document.querySelectorAll(".clickable").forEach((cell) => {
        cell.addEventListener("click", () => {
          if (cell.dataset.idea) {
            modalTitle.innerText = "Idea";
            modalData.innerHTML = cell.dataset.idea;
          } else if (cell.dataset.members) {
            modalTitle.innerText = "Group Members";
            modalData.innerHTML = cell.dataset.members;
          }
          modal.style.display = "flex";
        });
      });

      closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
      });
      window.addEventListener("click", (e) => {
        if (e.target == modal) modal.style.display = "none";
      });

      // Search Bar Functionality
document.querySelector('.search-bar input').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelector("table").querySelectorAll("tr");

    for (let i = 1; i < rows.length; i++) { 
        let rowText = rows[i].innerText.toUpperCase();
        if (rowText.includes(filter)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
});
    </script>
  </body>
</html>
