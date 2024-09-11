<?php
session_start();

// Check if form is submitted
if(isset($_POST['submit'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "studentattends_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form parameters
    $staffNumber = $_POST['staffNumber'];
    $password = $_POST['password'];

    // Check credentials
    $stmt = $conn->prepare("SELECT * FROM administrator WHERE staffNumber = ? AND password = ?");
    $stmt->bind_param("ss", $staffNumber, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Successful login
        $_SESSION['loggedin'] = true;
        $_SESSION['staffNumber'] = $staffNumber;
        header("Location: AdminHome.php");
        exit();
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid staff number or password');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body{
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:#f0f2f5;
            flex-direction: column; /* To center the logo and the form */
            overflow: hidden; /* To prevent scrollbars from appearing */
            animation: slide-in 0.7s ease-out;
        }

        @keyframes slide-in {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }     

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            animation: bounce-in 1.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        .floating-shapes div {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(120deg, #122B40, #446CB3);
            animation: float 6s ease-in-out infinite;
            opacity: 0.8; /* Adjust opacity value here */
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        .shape1 {
            width: 80px;
            height: 80px;
            top: 5%;
            left: 23%;
            animation-duration: 8s;
           
        }
        .shape2 {
            width: 90px;
            height: 90px;
            top: 35%;
            left: 3%;
            animation-duration: 10s;
        }
        .shape3 {
            width: 100px;
            height: 100px;
            top: 65%;
            left: 17%;
            animation-duration: 12s;
        }
        .shape4 {
            width: 80px;
            height: 80px;
            top: 5%;
            left: 70%;
            animation-duration: 8s;
        }
        .shape5 {
            width: 90px;
            height: 90px;
            top: 35%;
            left: 90%;
            animation-duration: 10s;
        }
        .shape6 {
            width: 100px;
            height: 100px;
            top: 65%;
            left: 75%;
            animation-duration: 12s;
        }
        .shape7 {
          width: 70px;
            height: 70px;
            top: 85%;
            left: 5%;
            animation-duration: 8s;
        }
        .shape8 {
          width: 70px;
            height: 70px;
            top: 85%;
            left: 90%;
            animation-duration: 8s;
        }
  
        .logo {
    position: absolute;
    top: 15px; /* Adjust the top position */
    right: 10px; /* Adjust the right position */
    max-width: 110px; /* Reduce size of the logo */
    height: auto;
    margin: 0; /* Remove margin to avoid unnecessary spacing */
}
        .wrapper{
            position: relative;
            max-width: 430px;
            width: 100%;
            background: white;
            padding: 34px;
            border-radius: 6px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
            animation: fadeInSlideUp 0.5s ease-out; /* Adjust duration and easing as needed */
        }
        .wrapper h2{
            position: relative;
            font-size: 22px;
            font-weight: 600;
            color:#122B40;
        }
        .wrapper h2::before{
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 28px;
            border-radius: 12px;
            background:#122B40;
        }
        .input-box{
            height: 52px;
            margin: 18px 0;
        }
        .input-box input{
            height: 100%;
            width: 100%;
            outline: none;
            padding: 0 15px;
            font-size: 17px;
            font-weight: 400;
            color: #333;
            border: 1.5px solid #C7BEBE;
            border-bottom-width: 2.5px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .input-box input:focus,
        .input-box input:valid{
            border-color:#90B2D8;
        }
        .input-box.button input{
            color: #fff;
            letter-spacing: 1px;
            border: none;
            background: linear-gradient(120deg, #122B40, #446CB3);
            cursor: pointer;
        }
        .input-box.button input:hover{
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
        }
        .text{
            color: #333;
            text-align: center;
        }
        .text a{
            color:#122B40;
            text-decoration: none;
        }
        .text a:hover{
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="floating-shapes">
        <div class="shape1"></div>
        <div class="shape2"></div>
        <div class="shape3"></div>
        <div class="shape4"></div>
        <div class="shape5"></div>
        <div class="shape6"></div>
        <div class="shape7"></div>
        <div class="shape8"></div>
    </div>

    <div class="wrapper">
    <img src="LogoUMP.png" alt="Logo" class="logo">
        <h2>ADMIN LOGIN</h2>
        <form action="Adminlogin.php" method="POST">
            <div class="input-box">
                <input type="text" name="staffNumber" placeholder="Username" required>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-box button">
                <input type="submit" name="submit" value="Login">
            </div>
            <div class="text">
                <a href="forgotPassword.php">Forgot Password<br></a>
            </div>
        </form>
    </div>
    </body>
</html>