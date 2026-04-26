<?php
session_start();

$conn = new mysqli("localhost","root","","student_portal");

$student_id = $_SESSION['student_id'];
$supervisor_id = $_POST['supervisor_id'];
// $supervisor = $_POST['supervisor'];
$idea = $_POST['idea'];
$member1 = $_POST['member1'];
$member2 = $_POST['member2'];
$member3 = $_POST['member3'];

//Check duplicate submission
$check = $conn->prepare("SELECT * FROM fyp_submissions WHERE student_id=?");
$check->bind_param("s", $student_id);
$check->execute();
$res = $check->get_result();

if($res->num_rows > 0){
    die("You already submitted proposal!");
}

//Upload
$file = $_FILES['proposal'];

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
if(strtolower($ext) != "pdf"){
    die("Only PDF allowed!");
}

$newName = $student_id."_".time().".pdf";
$path = "uploads/".$newName;
$proposal_file = $path;

if(!move_uploaded_file($file['tmp_name'], $path)){
    die("File upload failed!");
}

//Insert
$stmt = $conn->prepare("INSERT INTO fyp_submissions 
(student_id, supervisor_id, proposal_file, idea, member1, member2, member3) 
VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sisssss", 
    $student_id, 
    $supervisor_id, 
    $proposal_file, 
    $idea, 
    $member1, 
    $member2, 
    $member3
);

if($stmt->execute()){
    echo "Submitted Successfully!";
}else{
    echo "Error!";
}
?>