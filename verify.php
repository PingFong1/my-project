<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
include "db_conn.php";
require 'vendor/autoload.php'; // Include PHPMailer autoloader

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function sendVerificationEmail($toEmail, $verificationLink) {
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your_gmail_username@gmail.com'; // Your Gmail username
            $mail->Password   = 'your_gmail_password'; // Your Gmail password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('your_gmail_username@gmail.com', 'Your Name'); // Your Gmail username and your name
            $mail->addAddress($toEmail); // Recipient email

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body    = 'Click the following link to verify your email address: <a href="' . $verificationLink . '">Verify Email</a>';

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    // Validate and sanitize input
    $username = validate($_POST['username']);
    $password = validate($_POST['password']);
    $email = strtolower(validate($_POST['email']));
    $firstName = validate($_POST['first_name']);
    $middleName = validate($_POST['middle_name']);
    $lastName = validate($_POST['last_name']);

    // Check if email already exists
    $checkEmailQuery = "SELECT email FROM user WHERE LOWER(email) = '$email' LIMIT 1";
    $checkEmailQueryRun = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($checkEmailQueryRun) > 0) {
        $_SESSION['status'] = "Email ID already exists. Please use another email address.";
        header("Location: signup.php");
        exit();
    }

    // Generate verification token
    $verificationToken = bin2hex(random_bytes(32)); // Generate a random token

    // Insert user into database with verification token
    if (empty($username) || empty($password) || empty($email) || empty($firstName) || empty($lastName)) {
        header("Location: signup.php?error=All fields are required");
        exit();
    } else {
        $sql = "INSERT INTO user (username, password, email, first_name, middle_name, last_name, verification_token, is_verified) VALUES ('$username', '$password' , '$email', '$firstName', '$middleName', '$lastName', '$verificationToken', 0)";

        if (mysqli_query($conn, $sql)) {
            // Send verification email
            $verificationLink = "http://example.com/verify_email.php?token=$verificationToken"; // Change example.com to your domain
            if (sendVerificationEmail($email, $verificationLink)) {
                header("Location: loginform.php?success=Registration successful. Please verify your email.");
                exit();
            } else {
                header("Location: signup.php?error=Error occurred while sending verification email. Please try again later.");
                exit();
            }
        } else {
            header("Location: signup.php?error=Error occurred while registering user");
            exit();
        }
    }
}
?>