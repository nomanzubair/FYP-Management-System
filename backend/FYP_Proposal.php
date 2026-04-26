<?php
session_start();

$conn = new mysqli("localhost","root","","student_portal");

$sql = "SELECT * FROM supervisors";
$result = $conn->query($sql);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FYP Proposal Submission | UE Portal</title>

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
  min-height:100vh;
  padding:20px;
}

.fyp_container{
  width:100%;
  max-width:500px;
  background:white;
  padding:30px;
  border-radius:10px;
  box-shadow:0 4px 20px rgba(0,0,0,0.1);
  border-top:5px solid #2e6b3e;
}

h2{
  text-align:center;
  color:#2e6b3e;
  margin-bottom:25px;
  text-transform:uppercase;
}


label{
  font-weight:600;
  margin-bottom:5px;
  display:block;
}

select,
input[type="text"],
textarea{
  width:100%;
  padding:12px;
  margin-bottom:15px;
  border:1px solid #ccc;
  border-radius:6px;
  font-size:14px;
  outline:none;
  transition:0.3s;
}

textarea{
  min-height:100px;
  resize:vertical;
}

select:focus,
input:focus,
textarea:focus{
  border-color:#2e6b3e;
  box-shadow:0 0 5px rgba(46,107,62,0.2);
}

.upload-box{
  border:2px dashed #2e6b3e;
  padding:20px;
  text-align:center;
  border-radius:8px;
  margin-bottom:15px;
  cursor:pointer;
  transition:0.3s;
}

.upload-box:hover{
  background:#f1f9f4;
}

.upload-box p{
  color:#555;
}

.upload-box input{
  display:none;
}

.char-count{
  font-size:12px;
  text-align:right;
  color:#888;
  margin-top:-10px;
  margin-bottom:10px;
}

input[type="submit"]{
  width:100%;
  padding:12px;
  background:#2e6b3e;
  color:white;
  font-weight:bold;
  border:none;
  border-radius:6px;
  cursor:pointer;
  transition:0.3s;
}

input[type="submit"]:hover{
  background:#1f4d2c;
  transform:translateY(-2px);
}

p{
  text-align:center;
  margin-top:15px;
}

a{
  text-decoration:none;
  color:#2e6b3e;
  font-weight:600;
}

a:hover{
  text-decoration:underline;
}

/* Responsive */
@media(max-width:480px){
  .fyp_container{
    padding:20px;
  }
}
</style>
</head>

<body>

<div class="fyp_container">

<h2>FYP Proposal Submission</h2>

<form action="submit_fyp.php" method="post" enctype="multipart/form-data" onsubmit="return validateFYP();">


<label>Select Supervisor *</label>
<select name="supervisor_id" required>
<option value="">Select Supervisor</option>

<?php while($row = $result->fetch_assoc()): ?>
<option value="<?php echo $row['id']; ?>">
<?php echo $row['name']; ?>
</option>
<?php endwhile; ?>

</select>


<label>Upload Proposal (PDF) *</label>
<div class="upload-box" onclick="document.getElementById('proposal').click()">
  <p id="fileText">📂 Click or Drag & Drop PDF here</p>
  <input type="file" id="proposal" name="proposal" required>
</div>


<label>Explain Your Idea *</label>
<textarea name="idea" id="Idea" maxlength="300" placeholder="Write your project idea..." required></textarea>
<div class="char-count" id="charCount">0 / 300</div>


<label>Group Members</label>
<input type="text" name="member1" placeholder="Member 1">
<input type="text" name="member2" placeholder="Member 2">
<input type="text" name="member3" placeholder="Member 3">

<input type="submit" value="Submit Proposal">

</form>

</div>

<script>


const box = document.querySelector(".upload-box");
const input = document.getElementById("proposal");
const fileText = document.getElementById("fileText");


box.addEventListener("dragover", (e)=>{
  e.preventDefault();
  box.style.background="#e8f5e9";
});

box.addEventListener("dragleave", ()=>{
  box.style.background="";
});

box.addEventListener("drop", (e)=>{
  e.preventDefault();
  input.files = e.dataTransfer.files;
  showFileName();
  box.style.background="";
});


input.addEventListener("change", showFileName);

function showFileName(){
  if(input.files.length > 0){
    fileText.innerText = "📄 " + input.files[0].name;
  }
}


const idea = document.getElementById("Idea");
const counter = document.getElementById("charCount");

idea.addEventListener("input", ()=>{
  counter.innerText = idea.value.length + " / 300";
});


function validateFYP(){
  const file = input.files[0];

  if(!file){
    alert("Please upload file!");
    return false;
  }

  if(file.type !== "application/pdf"){
    alert("Only PDF allowed!");
    return false;
  }

  alert("✅ Proposal Submitted Successfully!");
  return true;
}

</script>

</body>
</html>