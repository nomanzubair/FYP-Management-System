<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost","root","","student_portal");

if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

$sql = "SELECT * FROM supervisors";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Supervisors | UE Portal</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    background-color: #f4f7f6;
    display: flex;
    justify-content: center;
    padding: 30px 10px;
}

.container {
    width: 100%;
    max-width: 900px;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-top: 5px solid #2e6b3e;
    padding: 30px;
    text-align: center;
}

.card-logo {
    width: 80px;
    height: 80px;
    margin: 0 auto 15px;
    background: url("ue_logo.png") no-repeat center;
    background-size: contain;
    transition: transform 0.4s ease-in-out;
}

.card-logo:hover {
    transform: scale(1.15);
}

h2 {
    color: #2e6b3e;
    font-size: 24px;
    margin-bottom: 25px;
    text-transform: uppercase;
}


table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
}

table th, table td {
    padding: 12px 15px;
    border: 1px solid #eee; 
    text-align: left;
    font-size: 14px;
}

table th {
    background-color: #2e6b3e;
    color: white;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.5px;
}

table tr:nth-child(even) {
    background-color: #fcfcfc;
}

table tr:hover {
    background-color: #f1f9f4;
}


.delete-btn {
    background-color: #fff;
    color: #f44336;
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    font-weight: bold;
    border: 1px solid #f44336;
    transition: all 0.3s ease;
}

.delete-btn:hover {
    background-color: #f44336;
    color: white;
    box-shadow: 0 2px 5px rgba(244, 67, 54, 0.3);
}


.back-link {
    display: inline-block;
    padding: 10px 25px;
    background-color: #f4f7f6;
    text-decoration: none;
    color: #2e6b3e;
    font-weight: bold;
    border-radius: 50px; 
    transition: all 0.3s ease;
    border: 1px solid #2e6b3e;
}

.back-link:hover {
    background-color: #2e6b3e;
    color: white;
    transform: translateX(-5px); 
}

@media (max-width: 600px) {
    table th, table td {
        font-size: 12px;
        padding: 8px 10px;
    }
}
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-logo"></div>
        <h2>Supervisors List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th style="text-align:center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        echo "<tr>";
                        echo "<td>".$row['id']."</td>";
                        echo "<td>".$row['name']."</td>";
                        echo "<td>".$row['email']."</td>";
                        echo "<td>".$row['username']."</td>";
                        // Added JavaScript confirmation for safety
                        echo "<td style='text-align:center'><a class='delete-btn' href='delete_supervisor.php?id=".$row['id']."' onclick=\"return confirm('Are you sure you want to delete this supervisor?');\">Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center'>No supervisors found</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a class="back-link" href="admin_dashboard.php">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>