<?php
session_start();
include "db_conn.php";

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container-box {
            margin-top: 50px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        body {
            background-image: url('Sakura.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="container-box">
                <h2 class="text-center">Signup</h2>
                <?php if (isset($_SESSION['status'])) { ?>
                    <p class="error"><?php echo $_SESSION['status']; ?></p>
                    <?php unset($_SESSION['status']); ?>
                <?php } ?>
                <form action="signup.php" method="post">
                    <?php if (isset($_GET['error'])) { ?>
                        <p class="error"><?php echo $_GET['error']; ?></p>
                    <?php } ?>
                    <div class="form-group">
                        <label for="username">User Name</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="User Name">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="Email">Email Address</label>  
                        <input type="email" class="form-control" id="Email" name="Email" placeholder="Email Address" required="required">
                    </div>
                    <div class="form-group">
                        <label for="First_name">First Name</label>
                        <input type="text" class="form-control" id="First_name" name="First_name" placeholder="First Name" required="required">
                    </div>
                    <div class="form-group">
                        <label for="Middle_name">Middle Name</label>
                        <input type="text" class="form-control" id="Middle_name" name="Middle_name" placeholder="Middle Name">
                    </div>
                    <div class="form-group">
                        <label for="Lastname">Last Name</label>
                        <input type="text" class="form-control" id="Lastname" name="Lastname" placeholder="Last Name" required="required">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Signup</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
