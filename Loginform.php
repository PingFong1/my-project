<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Your custom CSS -->
    <style>
        body {
            background-image: url("Sakura.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <h2 class="text-center">LOGIN</h2>
                    <form action="index.php" method="post">
                        <?php if (isset($_GET['error'])) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $_GET['error']; ?>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <label for="username">User Name</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="User Name">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                    <!-- Add a link to signup.php -->
                    <div class="text-center mt-3">
                        <a href="signup.php" class="btn btn-secondary btn-sm">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
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

    // Validate and sanitize input
    $username = validate($_POST['username']);
    $password = validate($_POST['password']);
    $Email = strtolower(validate($_POST['Email']));
    $First_name = validate($_POST['First_name']);
    $Middle_name = validate($_POST['Middle_name']);
    $Lastname = validate($_POST['Lastname']);

    // Check if email already exists
    $check_email_query = "SELECT email FROM user WHERE LOWER(email) = '$Email' LIMIT 1";
    $check_email_query_run = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($check_email_query_run) > 0) {
        $_SESSION['status'] = "Email ID already exists PLEASE INPUT ANOTHER";
        header("Location: signup.php");
        exit();
    }

    // Generate verification code
    $verification_code = mt_rand(100000, 999999); // Generate a random 6-digit code

    // Send verification email
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
        $mail->addAddress($Email); // Recipient email

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Verification Code';
        $mail->Body    = 'Your verification code is: ' . $verification_code;

        $mail->send();
        echo 'Verification code sent successfully!';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    // Insert user into database
    if (empty($username) || empty($password) || empty($Email) || empty($First_name) || empty($Lastname)) {
        header("Location: signup.php?error=All fields are required");
        exit();
    } else {
        $sql = "INSERT INTO user (username, password, email, First_name, Middle_name, Lastname) VALUES ('$username', '$password' , '$Email', '$First_name', '$Middle_name', '$Lastname')";

        if (mysqli_query($conn, $sql)) {
            header("Location: loginform.php?success=User registered successfully");
            exit();
        } else {
            header("Location: signup.php?error=Error occurred while registering user");
            exit();
        }
    }
}
?>