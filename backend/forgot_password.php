<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// DB Connection
$conn = new mysqli("localhost","root","","student_portal");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = $_POST['forgot_id'];

    // Check email exists
    $stmt = $conn->prepare("SELECT * FROM students WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){

        // Generate OTP
        $otp = rand(100000,999999);

        $_SESSION['reset_email'] = $email;
        $_SESSION['otp'] = $otp;

        // Send Email
        $mail = new PHPMailer(true);

        try{
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            $mail->Username = 'your@gmail.com';
            $mail->Password = 'your gmail app password';

            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your@gmail.com', 'Student Portal');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "<h3>Your OTP is: $otp</h3>";

            $mail->send();

            header("Location: verify_otp.html");
            exit();

        } catch (Exception $e){
            echo "Mailer Error: ".$mail->ErrorInfo;
        }

    } else {
        echo "Email not found!";
    }
}
?>