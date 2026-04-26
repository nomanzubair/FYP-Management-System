<?php
session_start();

$conn = new mysqli("localhost","root","","student_portal");

$student_id = $_SESSION['student_id'];

$sql = "SELECT status FROM fyp_submissions WHERE student_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FYP Status | UE Portal</title>

<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:"Segoe UI", sans-serif;
}

body{
  background:#f4f7f6;
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh;
}


.modal{
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.5);
  display:flex;
  justify-content:center;
  align-items:center;

  opacity:0;
  visibility:hidden;
  transition:0.3s;
}


.modal.show{
  opacity:1;
  visibility:visible;
}


.modal-content{
  background:white;
  padding:30px;
  border-radius:10px;
  text-align:center;
  width:300px;
  transform:scale(0.7);
  transition:0.3s;
}


.modal.show .modal-content{
  transform:scale(1);
}


.status{
  font-size:20px;
  font-weight:bold;
  margin-top:10px;
}


.close-btn{
  margin-top:20px;
  padding:10px 20px;
  border:none;
  background:#2e6b3e;
  color:white;
  border-radius:5px;
  cursor:pointer;
  transition:0.3s;
}

.close-btn:hover{
  background:#1f4d2c;
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

<div id="statusModal" class="modal">
  <div class="modal-content">

    <h2>Your FYP Status</h2>

    <?php if($row): ?>

    <p class="status" style="
      color:
      <?php 
      if($row['status']=='Approved') echo 'green';
      elseif($row['status']=='Rejected') echo 'red';
      else echo 'orange';
      ?>
    ">
      <?php echo $row['status']; ?>
    </p>

    <?php else: ?>
      <p>No proposal submitted yet</p>
    <?php endif; ?>

    <button class="close-btn" onclick="closeModal()">OK</button>

  </div>
</div>

<script>

// Auto show popup
window.onload = function(){
  document.getElementById("statusModal").classList.add("show");
};

// Close modal
function closeModal(){
  document.getElementById("statusModal").classList.remove("show");
}

</script>

</body>
</html>