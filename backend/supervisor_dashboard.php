<?php
session_start();
if (!isset($_SESSION['supervisor_id'])) {
    header("Location: supervisor_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "student_portal");

$supervisor_id = $_SESSION['supervisor_id'];

$sql = "SELECT 
        f.*, 
        s.std_name,
        s.std_id,
        s.department,
        s.shift
        FROM fyp_submissions f
        JOIN students s ON f.student_id = s.std_id
        WHERE f.supervisor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Supervisor Dashboard | UE Portal</title>

<style>
* {margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', sans-serif;}
body {background:#f4f7f6; display:flex; justify-content:center; padding:20px;}

.container {width:100%; max-width:1200px;}

.card {
    background:white;
    padding:30px;
    border-radius:8px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
    border-top:5px solid #2e6b3e;
    text-align:center;
}


.card-logo {
    width:100px; height:100px;
    margin:0 auto 15px;
    background:url("ue_logo.png") no-repeat center;
    background-size:contain;
    transition: transform 0.4s ease-in-out;
}
.card-logo:hover {
    transform: scale(1.15);
}


h2 {
    color:#2e6b3e;
    margin-bottom:20px;
    text-transform:uppercase;
}


.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
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
    padding: 8px 10px 8px 35px;
    border: 1px solid #ccc;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
    transition:all 0.3s ease;
    background: white;
}

.search-bar input:focus, .search-bar input:hover {
    border-color: #2e6b3e; 
    box-shadow: 0 0 8px rgba(46, 107, 62, 0.2); 
    background: #f9fffb; 
}

.search-bar i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
}

.logout-container {
    flex: 1;
    display: flex;
    justify-content: flex-end;
}

.logout-btn {
    background: white; 
    color: #2e6b3e; 
    border: 2px solid #2e6b3e; 
    padding: 8px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    font-size: 14px;
}

.logout-btn:hover {
    background: #d93025; 
    color: white; 
    border-color: #b3261e; 
    box-shadow: 0 4px 10px rgba(217, 48, 37, 0.3); 
    text-decoration: none;
}


table {
    width:100%;
    border-collapse:collapse;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    border-radius:8px;
    overflow:hidden;
}
th, td {
    padding:12px;
    border:1px solid #eee;
    font-size:14px;
}
th {
    background:#2e6b3e;
    color:white;
}
tr:nth-child(even) {background:#fcfcfc;}
tr:hover {background:#f1f9f4;}


.clickable {
    cursor:pointer;
    transition: transform 0.3s ease-in-out;
}
.clickable:hover {
    transform: scale(1.1);
    color:#2e6b3e;
    font-weight:bold;
}


a {
    color:#2e6b3e;
    text-decoration:none;
    font-weight:600;
}
a:hover {text-decoration:underline;}


.action-btn {
    padding:6px 12px;
    background:#2e6b3e;
    color:white;
    border-radius:5px;
    font-size:13px;
    transition:0.3s;
}
.action-btn:hover {
    background:#1f4d2c;
}


.modal {
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
}
.modal-content {
    background:white;
    padding:20px;
    border-radius:8px;
    width:350px;
    text-align:center;
    position:relative;
}
.close {
    position:absolute;
    top:10px; right:10px;
    cursor:pointer;
    font-size:20px;
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
                <span style="position:absolute; left: 12px; top: 8px; color:#888">🔍</span>
                <input type="text" placeholder="Search student records...">
            </div>
        </div>
        
        <div class="card-logo"></div>

        <div class="logout-container">
            <a href="supervisor_login.html" class="logout-btn">LOGOUT ⏻</a>
        </div>
    </div>
    
    <h2>Welcome <?php echo $_SESSION['supervisor_name']; ?></h2>

<table>
<tr>
  <th>Student ID</th>
  <th>Student Name</th>
  <th>Department</th>
  <th>Shift</th>
  <th>Group Members</th>
  <th>Idea</th>
  <th>Proposal</th>
  <th>Status</th>
  <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>

<td><?php echo $row['std_id']; ?></td>
<td><?php echo $row['std_name']; ?></td>
<td><?php echo $row['department']; ?></td>
<td><?php echo $row['shift']; ?></td>

<td class="clickable" data-members="<?php echo $row['member1'].'<br>'.$row['member2'].'<br>'.$row['member3']; ?>">
    Click to View
</td>

<td class="clickable" data-idea="<?php echo $row['idea']; ?>">
    Click to View
</td>

<td>
<?php if($row['proposal_file']): ?>
<a href="<?php echo $row['proposal_file']; ?>" target="_blank">View</a>
<?php else: ?>Not Uploaded<?php endif; ?>
</td>

<td><?php echo $row['status']; ?></td>

<td>
<a class="action-btn" href="respond.php?id=<?php echo $row['id']; ?>">Respond</a>
</td>

</tr>
<?php endwhile; ?>

</table>

</div>
</div>


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

document.querySelectorAll(".clickable").forEach(cell=>{
    cell.addEventListener("click",()=>{
        if(cell.dataset.idea){
            modalTitle.innerText="Idea";
            modalData.innerHTML=cell.dataset.idea;
        } else {
            modalTitle.innerText="Group Members";
            modalData.innerHTML=cell.dataset.members;
        }
        modal.style.display="flex";
    });
});

document.querySelector(".close").onclick = ()=> modal.style.display="none";
window.onclick = (e)=> { if(e.target==modal) modal.style.display="none"; };
</script>

</body>
</html>