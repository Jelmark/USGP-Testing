<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <link rel="stylesheet" type="text/css" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Archivo:ital,wght@0,100..900;1,100..900&display=swap');
</style>
<div class="bg-color">
    <div class="logo-container">
        <img src="../CLSU-Logo-2.png" class="logo" alt="CLSU-Logo-2 Logo">
    </div>
</div>       
<div class="container">
    <div class="screen">
        <div class="screen__content">
            <p class="title">Student Login</p>
            <form class="login" action="" method="post">
                <div class="login__field">
                    <i class="login__icon fas fa-user"></i>
                    <input type="text" class="login__input" name="email" placeholder="CLSU Email" required>
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-lock"></i>
                    <input type="password" class="login__input" name="password" placeholder="Password" required>
                </div>
                <a href="" class="forgot">Forgot Password</a>
                <button class="button login__submit" type="submit">
                    <span class="button__text">Continue</span>
                </button>
                <button type="button" class="button login__submit" onclick="location.href='../index.html';" style="background-color: #FFF; color:#FFB22C; border: .20rem solid #FFB22C;">
                    <span class="button__text">Back</span>
                </button>					
            </form>
        </div> 
    </div>
</div>
<?php
session_start(); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usgp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Query to check email and password
    $sql = "SELECT stud_password, id FROM user_student WHERE stud_email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $stored_password = $row['stud_password'];
        $id = $row['id'];

        // Check if password matches
        if ($password === $stored_password) {
            $_SESSION['id'] = $id;
            // Debugging output
            echo "<p>Login successful! Redirecting...</p>";
            header("Location: ../student-dashboard.php");
            exit();
        } else {
            echo "<p>Invalid email or password.</p>";
        }
    } else {
        echo "<p>Invalid email or password.</p>";
    }
}

// Close the connection
$conn->close();
?>
</body>
</html>
