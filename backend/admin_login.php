<?php
session_start();
$conn = new mysqli("localhost","root","","student_portal");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username=?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $admin = $result->fetch_assoc();

        if(password_verify($password,$admin['password'])){
            $_SESSION['admin'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Wrong Password!";
        }
    } else {
        $error = "Admin not found!";
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Admin Login | UE Portal</title>
  </head>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f4f7f6;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .admin-login-wrapper {
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .admin-card {
      background: white;
      width: 100%;
      max-width: 420px;
      padding: 35px;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      border-top: 5px solid #2e6b3e;
      text-align: center;
    }

    .admin-card::before {
      content: "";
      display: block;
      width: 70px;
      height: 70px;
      margin: 0 auto 10px;
      background: url("ue_logo.png") no-repeat center;
      background-size: contain;
      transition: transform 0.3s ease-in-out;
    }

    .admin-card:hover::before {
      transform: scale(1.1);
    }

    .admin-card h2 {
      color: #2e6b3e;
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 5px;
      text-transform: uppercase;
    }

    .admin-card p {
      font-size: 13px;
      color: #666;
      margin-bottom: 20px;
    }

    .input-field {
      margin-bottom: 15px;
    }

    .input-field input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
      outline: none;
      transition: border 0.3s;
    }

    .input-field input:focus {
      border-color: #2e6b3e;
    }

    .admin-btn {
      width: 100%;
      background-color: #2e6b3e;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
    }

    .error-box {
      /* background: #ffebee; */
      color: #c62828;
      padding: 8px;
      border-radius: 4px;
      margin-bottom: 10px;
      font-size: 13px;
    }

    .back-home {
      margin-top: 15px;
      font-size: 13px;
    }

    .back-home a {
      text-decoration: none;
      color: #2e6b3e;
    }
  </style>
  <body>
    <div class="admin-login-wrapper">
      <div class="admin-card">
        <h2>Admin Portal</h2>
        <p>Access the system control panel</p>

        <?php if(isset($error)): ?>
        <div class="error-box"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
          <div class="input-field">
            <input
              type="text"
              name="username"
              placeholder="Admin Username"
              required
            />
          </div>
          <div class="input-field">
            <input
              type="password"
              name="password"
              placeholder="Password"
              required
            />
          </div>
          <button type="submit" class="admin-btn">Login to Dashboard</button>
        </form>

        <div class="back-home">
          <a href="home.html">← Back to Home</a>
        </div>
      </div>
    </div>
  </body>
</html>