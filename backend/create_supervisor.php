<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: supervisor_login.php");
    exit();
}

$conn = new mysqli("localhost","root","","student_portal");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO supervisors (name,email,username,password) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss",$name,$email,$username,$password);

    if($stmt->execute()){
        echo "Supervisor Created Successfully!";
    } else {
        echo "Error!";
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Supervisor | UE Portal</title>
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
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
      }

   
      h2 {
        color: #2e6b3e;
        font-size: 24px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 20px;
        text-align: center;
      }

      
      .form-card {
        background: white;
        padding: 35px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-top: 5px solid #2e6b3e;
        width: 100%;
        max-width: 400px;
        text-align: center;
      }

      
      .form-card input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        outline: none;
        transition: border 0.3s;
      }

      .form-card input:focus {
        border-color: #2e6b3e;
      }

      
      .form-card button {
        width: 100%;
        padding: 12px;
        background-color: #2e6b3e;
        color: white;
        font-weight: bold;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition:
          background 0.3s,
          transform 0.2s;
      }

      .form-card button:hover {
        background-color: #1f4d2c;
        transform: translateY(-2px);
      }

   
      .back-btn {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 25px;
        background-color: #f4f7f6;
        color: #2e6b3e;
        font-weight: bold;
        text-decoration: none;
        border-radius: 50px; 
        border: 1px solid #2e6b3e;
        transition: all 0.3s ease;
      }

      .back-btn:hover {
        background-color: #2e6b3e;
        color: white;
        transform: translateX(-5px);
      }

      /* Responsive */
      @media (max-width: 480px) {
        .form-card {
          padding: 25px 20px;
        }
      }
    </style>
  </head>
  <body>
    <h2>Create Supervisor</h2>

    <div class="form-card">
      <form method="post" autocomplete="off">
        <input type="text" name="name" placeholder="Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="text" name="username" placeholder="Username" required />
        <input
          type="password"
          name="password"
          placeholder="Password"
          required
        />
        <button type="submit">Create</button>
      </form>

      <a class="back-btn" href="admin_dashboard.php">← Back to Dashboard</a>
    </div>
  </body>
</html>